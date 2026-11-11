<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PracticeAreaRequest;
use App\Http\Resources\PracticeAreaResource;
use App\Models\PracticeArea;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Practice Areas – Superadmin CRUD (Sanctum Bearer token)
 */
class PracticeAreaController extends Controller
{
    /** GET /api/practice-areas?search=&type=&status=&page=&per_page= */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'search'   => ['nullable', 'string', 'max:100'],
            'type'     => ['nullable', 'string', 'max:60'],
            'status'   => ['nullable', Rule::in(PracticeArea::STATUSES)],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
        ]);

        $items = PracticeArea::query()
            ->with(['creator:id,name', 'editor:id,name'])
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(fn ($w) => $w->where('name', 'like', $s)
                    ->orWhere('slug', 'like', $s)
                    ->orWhere('type', 'like', $s)
                    ->orWhere('short_description', 'like', $s));
            })
            ->ordered()
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        $types = PracticeArea::typeList();

        return PracticeAreaResource::collection($items)->additional([
            'stats' => [
                'total'    => PracticeArea::count(),
                'types'    => count($types),
                'active'   => PracticeArea::where('status', 'active')->count(),
                'inactive' => PracticeArea::where('status', 'inactive')->count(),
            ],
            'types' => $types,   // filter chips ke liye (DB se dynamic)
        ]);
    }

    /** GET /api/practice-areas/types – DB me save types + har type ka count (form suggestions ke liye) */
    public function types(): JsonResponse
    {
        $rows = PracticeArea::query()
            ->selectRaw('type, COUNT(*) as total')
            ->whereNotNull('type')->where('type', '!=', '')
            ->groupBy('type')->orderBy('type')
            ->get();

        return response()->json([
            'data' => $rows->map(fn ($r) => ['name' => $r->type, 'count' => (int) $r->total])->values(),
        ]);
    }

    /** POST /api/practice-areas  (multipart) */
    public function store(PracticeAreaRequest $request): JsonResponse
    {
        $item = $this->save($request, new PracticeArea());

        return response()->json([
            'message' => 'Practice area "' . $item->name . '" added successfully.',
            'data'    => new PracticeAreaResource($item),
        ], 201);
    }

    /** GET /api/practice-areas/{id} */
    public function show(PracticeArea $practiceArea): PracticeAreaResource
    {
        return new PracticeAreaResource($practiceArea->load(['creator:id,name', 'editor:id,name']));
    }

    /** POST /api/practice-areas/{id}  (multipart – file ke liye POST) */
    public function update(PracticeAreaRequest $request, PracticeArea $practiceArea): JsonResponse
    {
        $item = $this->save($request, $practiceArea);

        return response()->json([
            'message' => 'Practice area updated successfully.',
            'data'    => new PracticeAreaResource($item),
        ]);
    }

    /** PATCH /api/practice-areas/{id}/status   body: { status: active|inactive } */
    public function updateStatus(Request $request, PracticeArea $practiceArea): JsonResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(PracticeArea::STATUSES)]]);

        $practiceArea->update(['status' => $data['status'], 'updated_by' => $request->user()->id]);

        return response()->json([
            'message' => $data['status'] === 'active' ? 'Practice area activated.' : 'Practice area deactivated.',
            'data'    => new PracticeAreaResource($practiceArea),
        ]);
    }

    /** DELETE /api/practice-areas/{id} */
    public function destroy(PracticeArea $practiceArea): JsonResponse
    {
        $this->deleteImage($practiceArea->image);
        $practiceArea->delete();

        return response()->json(['message' => 'Practice area deleted successfully.']);
    }

    /** GET /api/public/practice-areas – website ke liye (bina token, sirf active) */
    public function publicList(Request $request): AnonymousResourceCollection
    {
        $items = PracticeArea::active()
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->ordered()
            ->get();

        return PracticeAreaResource::collection($items);
    }

    /* ---------------------------------------------------------- */

    private function save(PracticeAreaRequest $request, PracticeArea $item): PracticeArea
    {
        $data   = $request->validated();
        $userId = $request->user()->id;

        // "litigation" / "LITIGATION" jaise duplicate na bane – pehle se save spelling use karo
        $existing = PracticeArea::whereRaw('LOWER(type) = ?', [mb_strtolower($data['type'])])
            ->when($item->exists, fn ($q) => $q->where('id', '!=', $item->id))->value('type');
        $data['type'] = $existing ?: $data['type'];

        $item->fill([
            'name'              => $data['name'],
            'type'              => $data['type'],
            'slug'              => PracticeArea::uniqueSlug(($data['slug'] ?? null) ?: $data['name'], $item->id),
            'icon'              => ($data['icon'] ?? null) ?: 'fa-solid fa-scale-balanced',
            'short_description' => $data['short_description'] ?? null,
            'description'       => $data['description'] ?? null,
            'sort_order'        => $data['sort_order'] ?? ($item->exists ? $item->sort_order : (int) PracticeArea::max('sort_order') + 1),
            'status'            => $data['status'] ?? ($item->status ?: 'active'),
            'updated_by'        => $userId,
        ]);

        if (! $item->exists) {
            $item->created_by = $userId;
        }

        if ($request->boolean('remove_image')) {
            $this->deleteImage($item->image);
            $item->image = null;
        }

        if ($request->hasFile('image')) {
            $this->deleteImage($item->image);
            $item->image = $request->file('image')->store('practice-areas', 'public');
        }

        $item->save();

        return $item->load(['creator:id,name', 'editor:id,name']);
    }

    private function deleteImage(?string $path): void
    {
        if ($path && ! Str::startsWith($path, ['http://', 'https://']) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}