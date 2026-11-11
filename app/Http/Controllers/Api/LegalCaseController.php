<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LegalCaseRequest;
use App\Http\Resources\LegalCaseResource;
use App\Models\CaseDocument;
use App\Models\LegalCase;
use App\Models\PracticeArea;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Cases – Sanctum Bearer token
 *  superadmin : sab cases – list / add / view / edit / delete
 *  admin      : sirf apne assigned cases – list / view
 * Aadhaar / PAN / documents "local" (private) disk par – sirf token wale API se khulte hain
 */
class LegalCaseController extends Controller
{
    private const DISK = 'local';

    /** GET /api/cases/options – form dropdowns (dynamic) */
    public function options(): JsonResponse
    {
        $advocates = User::query()
            ->where('role', User::ROLE_ADMIN)->where('status', 'active')
            ->with('userProfile:id,user_id,city,interested_in')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone'])
            ->map(fn ($u) => [
                'id'    => $u->id,
                'name'  => $u->name,
                'email' => $u->email,
                'phone' => $u->phone,
                'city'  => $u->userProfile?->city,
                'practice' => $u->userProfile?->interested_in,
            ]);

        $areas = PracticeArea::active()->ordered()->get(['id', 'name', 'type']);

        return response()->json([
            'advocates'      => $advocates,
            'practice_areas' => $areas,
            'statuses'       => LegalCase::STATUSES,
            'priorities'     => LegalCase::PRIORITIES,
        ]);
    }

    /** GET /api/cases?search=&status=&practice_area_id=&advocate_id=&page=&per_page= */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'search'           => ['nullable', 'string', 'max:100'],
            'status'           => ['nullable', Rule::in(array_keys(LegalCase::STATUSES))],
            'practice_area_id' => ['nullable', 'integer'],
            'advocate_id'      => ['nullable', 'integer'],
            'per_page'         => ['nullable', 'integer', 'min:5', 'max:100'],
        ]);

        $user = $request->user();
        $base = LegalCase::query()->visibleTo($user);

        $cases = (clone $base)
            ->with(['practiceArea:id,name,type', 'advocate:id,name,email,phone'])
            ->withCount('documents')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('practice_area_id'), fn ($q) => $q->where('practice_area_id', $request->practice_area_id))
            ->when($request->filled('advocate_id'), fn ($q) => $request->advocate_id === '0'
                ? $q->whereNull('advocate_id')
                : $q->where('advocate_id', $request->advocate_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(fn ($w) => $w->where('case_no', 'like', $s)
                    ->orWhere('title', 'like', $s)
                    ->orWhere('client_name', 'like', $s)
                    ->orWhere('client_phone', 'like', $s)
                    ->orWhere('court_name', 'like', $s)
                    ->orWhere('aadhaar_last4', 'like', $s));
            })
            ->latest()
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        $counts = (clone $base)->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return LegalCaseResource::collection($cases)->additional([
            'stats' => [
                'total'   => (int) $counts->sum(),
                'active'  => (int) ($counts['active'] ?? 0),
                'hearing' => (int) ($counts['hearing'] ?? 0),
                'pending' => (int) ($counts['pending'] ?? 0),
                'closed'  => (int) ($counts['closed'] ?? 0),
            ],
        ]);
    }

    /** POST /api/cases  (multipart) */
    public function store(LegalCaseRequest $request): JsonResponse
    {
        $case = $this->save($request, new LegalCase());

        return response()->json([
            'message' => 'Case ' . $case->case_no . ' created successfully.',
            'data'    => new LegalCaseResource($case),
        ], 201);
    }

    /** GET /api/cases/{id} */
    public function show(Request $request, LegalCase $legalCase): LegalCaseResource
    {
        abort_unless($legalCase->isVisibleTo($request->user()), 404, 'Case not found.');

        $legalCase->load(['practiceArea:id,name,type', 'advocate:id,name,email,phone', 'documents', 'creator:id,name', 'editor:id,name']);

        return (new LegalCaseResource($legalCase))->withFull($request->boolean('full'));
    }

    /** POST /api/cases/{id}  (multipart – file ke liye POST) */
    public function update(LegalCaseRequest $request, LegalCase $legalCase): JsonResponse
    {
        $case = $this->save($request, $legalCase);

        return response()->json([
            'message' => 'Case ' . $case->case_no . ' updated successfully.',
            'data'    => new LegalCaseResource($case),
        ]);
    }

    /** DELETE /api/cases/{id} */
    public function destroy(LegalCase $legalCase): JsonResponse
    {
        $no = $legalCase->case_no;
        Storage::disk(self::DISK)->deleteDirectory($legalCase->storageDir());
        $legalCase->delete();   // case_documents cascade

        return response()->json(['message' => 'Case ' . $no . ' deleted successfully.']);
    }

    /** GET /api/cases/{id}/files/{aadhaar|pan} – private file (token ke saath) */
    public function file(Request $request, LegalCase $legalCase, string $type): StreamedResponse
    {
        abort_unless($legalCase->isVisibleTo($request->user()), 404, 'Case not found.');

        $path = $type === 'pan' ? $legalCase->pan_file : $legalCase->aadhaar_file;
        abort_unless($path && Storage::disk(self::DISK)->exists($path), 404, 'File not found.');

        $name = $legalCase->case_no . '-' . $type . '.' . pathinfo($path, PATHINFO_EXTENSION);

        return Storage::disk(self::DISK)->response($path, $name);
    }

    /** GET /api/cases/{id}/documents/{doc} */
    public function document(Request $request, LegalCase $legalCase, CaseDocument $document): StreamedResponse
    {
        abort_unless($legalCase->isVisibleTo($request->user()), 404, 'Case not found.');
        abort_unless(Storage::disk(self::DISK)->exists($document->path), 404, 'File not found.');

        return Storage::disk(self::DISK)->response($document->path, $document->original_name);
    }

    /** DELETE /api/cases/{id}/documents/{doc} */
    public function destroyDocument(LegalCase $legalCase, CaseDocument $document): JsonResponse
    {
        Storage::disk(self::DISK)->delete($document->path);
        $document->delete();
        $legalCase->touch();

        return response()->json(['message' => 'Document deleted.']);
    }

    /* ---------------------------------------------------------- */

    private function save(LegalCaseRequest $request, LegalCase $case): LegalCase
    {
        $data   = $request->validated();
        $userId = $request->user()->id;
        $disk   = Storage::disk(self::DISK);

        return DB::transaction(function () use ($request, $data, $case, $userId, $disk) {
            $case->fill(Arr::only($data, [
                'title', 'practice_area_id', 'advocate_id', 'status', 'next_hearing_date',
                'client_name', 'client_phone', 'client_email',
                'court_name', 'opponent_name', 'filing_date', 'description',
            ]));
            $case->priority       = $data['priority'] ?? ($case->priority ?: 'normal');
            $case->aadhaar_number = $data['aadhaar_number'];
            $case->aadhaar_last4  = substr($data['aadhaar_number'], -4);
            $case->pan_number     = $data['pan_number'] ?? null;
            $case->updated_by     = $userId;
            if (! $case->exists) {
                $case->created_by   = $userId;
                $case->aadhaar_file = '';            // id milne ke baad file save hogi
            }
            $case->save();                           // created event -> case_no

            $dir = $case->storageDir();

            if ($request->hasFile('aadhaar_file')) {
                if ($case->aadhaar_file) $disk->delete($case->aadhaar_file);
                $case->aadhaar_file = $request->file('aadhaar_file')->store($dir . '/kyc', self::DISK);
            }

            if ($request->boolean('remove_pan_file') && $case->pan_file) {
                $disk->delete($case->pan_file);
                $case->pan_file = null;
            }
            if ($request->hasFile('pan_file')) {
                if ($case->pan_file) $disk->delete($case->pan_file);
                $case->pan_file = $request->file('pan_file')->store($dir . '/kyc', self::DISK);
            }
            $case->saveQuietly();

            $titles = $request->input('document_titles', []);
            foreach ($request->file('documents', []) as $i => $file) {
                $case->documents()->create([
                    'title'         => trim($titles[$i] ?? '') ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'path'          => $file->store($dir . '/documents', self::DISK),
                    'original_name' => $file->getClientOriginalName(),
                    'mime'          => $file->getClientMimeType(),
                    'size'          => $file->getSize(),
                    'uploaded_by'   => $userId,
                ]);
            }

            return $case->load(['practiceArea:id,name,type', 'advocate:id,name,email,phone', 'documents']);
        });
    }
}