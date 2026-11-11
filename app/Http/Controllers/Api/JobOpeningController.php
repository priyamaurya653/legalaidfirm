<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobOpeningResource;
use App\Models\JobOpening;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

/** Career – Job Openings (superadmin CRUD + website public list) */
class JobOpeningController extends Controller
{
    /** GET /api/public/jobs – website Career page (bina token, sirf live) */
    public function publicList(): AnonymousResourceCollection
    {
        return JobOpeningResource::collection(JobOpening::live()->ordered()->get());
    }

    /** GET /api/jobs?search=&status=&page= */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'search'   => ['nullable', 'string', 'max:100'],
            'status'   => ['nullable', Rule::in(array_keys(JobOpening::STATUSES))],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
        ]);

        $jobs = JobOpening::query()
            ->withCount(['applications', 'applications as new_applications_count' => fn ($q) => $q->whereNull('read_at')])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(fn ($w) => $w->where('title', 'like', $s)->orWhere('location', 'like', $s)->orWhere('job_type', 'like', $s));
            })
            ->ordered()
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return JobOpeningResource::collection($jobs)->additional([
            'stats' => [
                'total'        => JobOpening::count(),
                'live'         => JobOpening::live()->count(),
                'positions'    => (int) JobOpening::live()->sum('openings'),
                'applications' => \App\Models\CareerApplication::count(),
            ],
            'types' => JobOpening::TYPES,
        ]);
    }

    /** POST /api/jobs */
    public function store(Request $request): JsonResponse
    {
        $job = new JobOpening($this->validated($request));
        $job->created_by = $job->updated_by = $request->user()->id;
        $job->save();

        return response()->json(['message' => 'Job opening "' . $job->title . '" posted successfully.', 'data' => new JobOpeningResource($job)], 201);
    }

    /** GET /api/jobs/{id} */
    public function show(JobOpening $job): JobOpeningResource
    {
        return new JobOpeningResource($job->loadCount('applications'));
    }

    /** POST /api/jobs/{id} */
    public function update(Request $request, JobOpening $job): JsonResponse
    {
        $job->fill($this->validated($request, $job));
        $job->updated_by = $request->user()->id;
        $job->save();

        return response()->json(['message' => 'Job opening updated successfully.', 'data' => new JobOpeningResource($job)]);
    }

    /** PATCH /api/jobs/{id}/status  { status: active|closed } */
    public function updateStatus(Request $request, JobOpening $job): JsonResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(array_keys(JobOpening::STATUSES))]]);
        $job->update(['status' => $data['status'], 'updated_by' => $request->user()->id]);

        return response()->json(['message' => $data['status'] === 'active' ? 'Job opening is now live on the website.' : 'Job opening closed.']);
    }

    /** DELETE /api/jobs/{id} – applications rehti hain (position naam copy hai) */
    public function destroy(JobOpening $job): JsonResponse
    {
        $job->delete();

        return response()->json(['message' => 'Job opening deleted. Its applications are kept.']);
    }

    private function validated(Request $request, ?JobOpening $job = null): array
    {
        $data = $request->validate([
            'title'        => ['required', 'string', 'min:3', 'max:150'],
            'job_type'     => ['required', Rule::in(JobOpening::TYPES)],
            'experience'   => ['nullable', 'string', 'max:60'],
            'location'     => ['required', 'string', 'max:100'],
            'openings'     => ['required', 'integer', 'min:1', 'max:999'],
            'salary'       => ['nullable', 'string', 'max:80'],
            'description'  => ['nullable', 'string', 'max:5000'],
            'requirements' => ['nullable', 'string', 'max:5000'],
            'last_date'    => ['nullable', 'date', $job ? 'date' : 'after_or_equal:today'],
            'sort_order'   => ['nullable', 'integer', 'min:0', 'max:9999'],
            'status'       => ['nullable', Rule::in(array_keys(JobOpening::STATUSES))],
        ], [
            'title.required'           => 'Please enter the job title.',
            'openings.required'        => 'Please enter the number of openings.',
            'openings.min'             => 'There must be at least 1 opening.',
            'last_date.after_or_equal' => 'Last date cannot be in the past.',
        ]);

        $data['status']     = $data['status'] ?? ($job->status ?? 'active');
        $data['sort_order'] = $data['sort_order'] ?? ($job->sort_order ?? 0);

        return $data;
    }
}