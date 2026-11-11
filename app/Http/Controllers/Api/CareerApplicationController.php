<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CareerApplicationResource;
use App\Models\CareerApplication;
use App\Models\JobOpening;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Career applications – website form (public) + superadmin list / view / status / delete */
class CareerApplicationController extends Controller
{
    private const DISK = 'local';   // resume private

    /** POST /api/career/apply  (multipart, public) */
    public function apply(Request $request): JsonResponse
    {
        $request->merge(['phone' => substr(preg_replace('/\D/', '', (string) $request->phone), -10)]);

        $data = $request->validate([
            'name'     => ['required', 'string', 'min:3', 'max:100'],
            'email'    => ['required', 'email', 'max:150'],
            'phone'    => ['required', 'regex:/^[6-9]\d{9}$/'],
            'position' => ['required', 'integer', Rule::exists('job_openings', 'id')],
            'resume'   => ['required', 'file', 'mimes:pdf,doc,docx', 'max:4096'],
            'cover'    => ['nullable', 'string', 'max:3000'],
            'website'  => ['nullable', 'max:0'],   // honeypot
        ], [
            'name.required'     => 'Please enter your full name.',
            'email.required'    => 'Please enter your email address.',
            'phone.required'    => 'Please enter your phone number.',
            'phone.regex'       => 'Please enter a valid 10-digit mobile number.',
            'position.required' => 'Please select a position.',
            'position.exists'   => 'This position is no longer available.',
            'resume.required'   => 'Please upload your resume.',
            'resume.mimes'      => 'Resume must be a PDF, DOC or DOCX file.',
            'resume.max'        => 'Resume must be smaller than 4MB.',
            'resume.uploaded'   => 'Resume could not be uploaded – the file is larger than the server limit (check upload_max_filesize in php.ini).',
        ]);

        $job = JobOpening::find($data['position']);
        if (! $job || ! $job->isLive()) {
            return response()->json(['message' => 'This position is no longer accepting applications.', 'errors' => ['position' => ['This position is closed.']]], 422);
        }

        $app = CareerApplication::create([
            'job_opening_id' => $job->id,
            'position'       => $job->title,
            'name'           => trim($data['name']),
            'email'          => strtolower(trim($data['email'])),
            'phone'          => $data['phone'],
            'cover'          => $data['cover'] ?? null,
            'status'         => 'new',
            'ip'             => $request->ip(),
        ]);

        $file = $request->file('resume');
        $app->update([
            'resume'      => $file->store('career/' . $app->id, self::DISK),
            'resume_name' => $file->getClientOriginalName(),
        ]);

        return response()->json(['message' => 'Thank you! Your application for "' . $job->title . '" has been received. Our HR team will contact you soon.'], 201);
    }

    /** GET /api/applications?search=&status=&job_opening_id=&page= */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'search'         => ['nullable', 'string', 'max:100'],
            'status'         => ['nullable', Rule::in(array_keys(CareerApplication::STATUSES))],
            'job_opening_id' => ['nullable', 'integer'],
            'per_page'       => ['nullable', 'integer', 'min:5', 'max:100'],
        ]);

        $apps = CareerApplication::query()
            ->with('job:id,title,job_type')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('job_opening_id'), fn ($q) => $q->where('job_opening_id', $request->job_opening_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(fn ($w) => $w->where('name', 'like', $s)->orWhere('email', 'like', $s)->orWhere('phone', 'like', $s)->orWhere('position', 'like', $s));
            })
            ->latest()
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        $counts = CareerApplication::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return CareerApplicationResource::collection($apps)->additional([
            'stats' => [
                'total'       => (int) $counts->sum(),
                'new'         => (int) ($counts['new'] ?? 0),
                'shortlisted' => (int) ($counts['shortlisted'] ?? 0),
                'rejected'    => (int) ($counts['rejected'] ?? 0),
                'hired'       => (int) ($counts['hired'] ?? 0),
                'unread'      => CareerApplication::whereNull('read_at')->count(),
            ],
            'jobs' => JobOpening::orderBy('title')->get(['id', 'title']),
        ]);
    }

    /** GET /api/applications/{id} – view (read mark) */
    public function show(CareerApplication $application): CareerApplicationResource
    {
        if (! $application->read_at) {
            $application->forceFill(['read_at' => now()])->save();
        }

        return new CareerApplicationResource($application->load('job:id,title,job_type'));
    }

    /** PATCH /api/applications/{id}  { status?, notes? } */
    public function update(Request $request, CareerApplication $application): JsonResponse
    {
        $data = $request->validate([
            'status' => ['sometimes', 'required', Rule::in(array_keys(CareerApplication::STATUSES))],
            'notes'  => ['sometimes', 'nullable', 'string', 'max:3000'],
        ]);
        $application->update($data);

        return response()->json([
            'message' => isset($data['status']) ? 'Application marked as ' . $application->statusLabel() . '.' : 'Notes saved.',
            'data'    => new CareerApplicationResource($application->load('job:id,title,job_type')),
        ]);
    }

    /** GET /api/applications/{id}/resume */
    public function resume(CareerApplication $application): StreamedResponse
    {
        abort_unless($application->resume && Storage::disk(self::DISK)->exists($application->resume), 404, 'Resume not found.');

        return Storage::disk(self::DISK)->response($application->resume, $application->resume_name ?: 'resume');
    }

    /** DELETE /api/applications/{id} */
    public function destroy(CareerApplication $application): JsonResponse
    {
        Storage::disk(self::DISK)->deleteDirectory('career/' . $application->id);
        $application->delete();

        return response()->json(['message' => 'Application deleted.']);
    }
}