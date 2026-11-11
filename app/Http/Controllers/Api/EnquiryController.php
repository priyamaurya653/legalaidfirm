<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EnquiryRequest;
use App\Http\Resources\EnquiryResource;
use App\Models\Enquiry;
use App\Models\PracticeArea;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EnquiryController extends Controller
{
    /** POST /api/enquiries – website Contact form (public) */
    public function store(EnquiryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $area = ($data['subject'] ?? null) && $data['subject'] !== 'other' ? PracticeArea::find($data['subject']) : null;

        Enquiry::create([
            'name'             => $data['name'],
            'email'            => $data['email'],
            'phone'            => $data['phone'],
            'practice_area_id' => $area?->id,
            'practice_area'    => $area?->name ?? (($data['subject'] ?? null) === 'other' ? 'Other' : null),
            'message'          => $data['message'],
            'status'           => 'new',
            'ip'               => $request->ip(),
            'user_agent'       => Str::limit((string) $request->userAgent(), 250, ''),
        ]);

        return response()->json([
            'message' => 'Thank you! Our team will contact you within 24 hours.',
        ], 201);
    }

    /** GET /api/enquiries?search=&status=&page= (superadmin) */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'search'   => ['nullable', 'string', 'max:100'],
            'status'   => ['nullable', Rule::in(array_keys(Enquiry::STATUSES))],
            'per_page' => ['nullable', 'integer', 'min:6', 'max:60'],
        ]);

        $items = Enquiry::query()
            ->with('practiceArea:id,name')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(fn ($w) => $w->where('name', 'like', $s)->orWhere('email', 'like', $s)
                    ->orWhere('phone', 'like', $s)->orWhere('practice_area', 'like', $s)->orWhere('message', 'like', $s));
            })
            ->latest()
            ->paginate($request->integer('per_page', 12))
            ->withQueryString();

        $counts = Enquiry::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return EnquiryResource::collection($items)->additional([
            'stats' => [
                'total'     => (int) $counts->sum(),
                'new'       => (int) ($counts['new'] ?? 0),
                'contacted' => (int) ($counts['contacted'] ?? 0),
                'closed'    => (int) ($counts['closed'] ?? 0),
                'unread'    => Enquiry::whereNull('read_at')->count(),
            ],
        ]);
    }

    /** GET /api/enquiries/unread-count – sidebar badge */
    public function unreadCount(): JsonResponse
    {
        return response()->json(['unread' => Enquiry::whereNull('read_at')->count()]);
    }

    /** GET /api/enquiries/{id} – view (pehli baar kholne par "read" mark) */
    public function show(Enquiry $enquiry): EnquiryResource
    {
        if (! $enquiry->read_at) {
            $enquiry->forceFill(['read_at' => now()])->save();
        }

        return new EnquiryResource($enquiry->load('practiceArea:id,name'));
    }

    /** PATCH /api/enquiries/{id}/status  body: { status: new|contacted|closed } */
    public function updateStatus(Request $request, Enquiry $enquiry): JsonResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(array_keys(Enquiry::STATUSES))]]);
        $enquiry->update(['status' => $data['status']]);

        return response()->json([
            'message' => 'Enquiry marked as ' . $enquiry->statusLabel() . '.',
            'data'    => new EnquiryResource($enquiry->load('practiceArea:id,name')),
        ]);
    }
}