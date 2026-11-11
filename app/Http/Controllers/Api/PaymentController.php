<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Client;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Payments – superadmin + admin
 * PRIVACY: har koi SIRF apne (owner_id = khud) payments dekh / bana / badal / delete kar sakta hai.
 * Superadmin bhi kisi admin ka payment nahi dekh sakta – dusre ka id dene par 404.
 */
class PaymentController extends Controller
{
    private const DISK = 'local';

    /** GET /api/payments/options – dropdowns + client suggestions (sirf apne clients) */
    public function options(Request $request): JsonResponse
    {
        $me = $request->user();
        $clients = collect();

        // pehle jinse payment liya hai
        Payment::ownedBy($me)->select('client_name', 'client_phone', 'client_email')->latest()->limit(200)->get()
            ->each(fn ($p) => $clients->push(['name' => $p->client_name, 'phone' => $p->client_phone, 'email' => $p->client_email]));

        // admin ke banaye users
        User::where('created_by', $me->id)->get(['name', 'phone', 'email'])
            ->each(fn ($u) => $clients->push(['name' => $u->name, 'phone' => $u->phone, 'email' => $u->email]));

        // Clients module (superadmin = apne banaye, admin = jo assign hain)
        if (Schema::hasTable('clients')) {
            Client::query()->when($me->role === User::ROLE_SUPERADMIN, fn ($q) => $q->where('created_by', $me->id), fn ($q) => $q->where('advocate_id', $me->id))
                ->get(['name', 'phone', 'email'])
                ->each(fn ($c) => $clients->push(['name' => $c->name, 'phone' => $c->phone, 'email' => $c->email]));
        }

        return response()->json([
            'modes'      => Payment::MODES,
            'categories' => Payment::CATEGORIES,
            'clients'    => $clients->filter(fn ($c) => $c['name'])->unique(fn ($c) => strtolower($c['name']) . '|' . $c['phone'])->values()->take(300),
        ]);
    }

    /** GET /api/payments?search=&status=&mode=&category=&from=&to=&page= */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'search'   => ['nullable', 'string', 'max:100'],
            'status'   => ['nullable', Rule::in(array_keys(Payment::STATUSES))],
            'mode'     => ['nullable', 'string', 'max:30'],
            'category' => ['nullable', 'string', 'max:60'],
            'from'     => ['nullable', 'date'],
            'to'       => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:200'],
        ]);

        $me   = $request->user();
        $base = Payment::ownedBy($me);

        $filtered = (clone $base)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('mode'), fn ($q) => $q->where('payment_mode', $request->mode))
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('payment_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('payment_date', '<=', $request->to))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(fn ($w) => $w->where('client_name', 'like', $s)->orWhere('client_phone', 'like', $s)
                    ->orWhere('purpose', 'like', $s)->orWhere('receipt_no', 'like', $s)->orWhere('reference_no', 'like', $s));
            });

        $payments = (clone $filtered)->orderByDesc('payment_date')->orderByDesc('id')
            ->paginate($request->integer('per_page', 10))->withQueryString();

        $received = fn ($q) => $q->where('status', 'received');

        return PaymentResource::collection($payments)->additional([
            'stats' => [
                'total_received' => (float) $received(clone $base)->sum('amount'),
                'this_month'     => (float) $received(clone $base)->whereBetween('payment_date', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount'),
                'today'          => (float) $received(clone $base)->whereDate('payment_date', today())->sum('amount'),
                'pending'        => (float) (clone $base)->where('status', 'pending')->sum('amount'),
                'count'          => (clone $base)->count(),
                'filtered_total' => (float) (clone $filtered)->where('status', 'received')->sum('amount'),
            ],
            'by_mode' => $received(clone $base)->selectRaw('payment_mode, SUM(amount) as total')->groupBy('payment_mode')->pluck('total', 'payment_mode'),
        ]);
    }

    /** POST /api/payments (multipart) */
    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $payment = new Payment($data);
        $payment->owner_id = $request->user()->id;
        $payment->save();
        $this->saveFile($request, $payment);

        return response()->json([
            'message' => 'Payment of ₹' . number_format($payment->amount, 2) . ' from ' . $payment->client_name . ' recorded (' . $payment->fresh()->receipt_no . ').',
            'data'    => new PaymentResource($payment->fresh()),
        ], 201);
    }

    /** GET /api/payments/{payment} */
    public function show(Request $request, Payment $payment): PaymentResource
    {
        $this->own($request, $payment);

        return new PaymentResource($payment);
    }

    /** POST /api/payments/{payment} (multipart) */
    public function update(Request $request, Payment $payment): JsonResponse
    {
        $this->own($request, $payment);
        $payment->update($this->validated($request));

        if ($request->boolean('remove_attachment') && $payment->attachment) {
            Storage::disk(self::DISK)->delete($payment->attachment);
            $payment->update(['attachment' => null, 'attachment_name' => null]);
        }
        $this->saveFile($request, $payment);

        return response()->json(['message' => 'Payment updated.', 'data' => new PaymentResource($payment->fresh())]);
    }

    /** DELETE /api/payments/{payment} */
    public function destroy(Request $request, Payment $payment): JsonResponse
    {
        $this->own($request, $payment);
        if ($payment->attachment) {
            Storage::disk(self::DISK)->delete($payment->attachment);
        }
        $payment->delete();

        return response()->json(['message' => 'Payment deleted.']);
    }

    /** GET /api/payments/{payment}/attachment */
    public function attachment(Request $request, Payment $payment): StreamedResponse
    {
        $this->own($request, $payment);
        abort_unless($payment->attachment && Storage::disk(self::DISK)->exists($payment->attachment), 404, 'File not found.');

        return Storage::disk(self::DISK)->response($payment->attachment, $payment->attachment_name ?: 'receipt');
    }

    /* ---------------------------------------------------------- */

    /** dusre ka payment = 404 (exist karta hai ye bhi pata na chale) */
    private function own(Request $request, Payment $payment): void
    {
        abort_unless((int) $payment->owner_id === (int) $request->user()->id, 404, 'Payment not found.');
    }

    private function validated(Request $request): array
    {
        $request->merge([
            'client_phone' => preg_replace('/\D/', '', (string) $request->client_phone) ?: null,
            'client_email' => $request->filled('client_email') ? strtolower(trim($request->client_email)) : null,
        ]);

        return $request->validate([
            'client_name'  => ['required', 'string', 'min:2', 'max:120'],
            'client_phone' => ['nullable', 'regex:/^[6-9]\d{9}$/'],
            'client_email' => ['nullable', 'email', 'max:150'],
            'purpose'      => ['required', 'string', 'min:2', 'max:200'],
            'category'     => ['nullable', 'string', 'max:60'],
            'amount'       => ['required', 'numeric', 'min:1', 'max:999999999'],
            'payment_mode' => ['required', 'string', 'max:30'],
            'payment_date' => ['required', 'date', 'before_or_equal:' . now()->addDays(365)->toDateString()],
            'reference_no' => ['nullable', 'string', 'max:100'],
            'status'       => ['required', Rule::in(array_keys(Payment::STATUSES))],
            'notes'        => ['nullable', 'string', 'max:2000'],
            'attachment'   => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'client_name.required'  => 'Please enter the client name.',
            'client_phone.regex'    => 'Enter a valid 10-digit mobile number.',
            'purpose.required'      => 'Please enter what this payment is for.',
            'amount.required'       => 'Please enter the amount.',
            'amount.min'            => 'Amount must be at least ₹1.',
            'payment_mode.required' => 'Please select the payment mode.',
            'payment_date.required' => 'Please select the payment date.',
            'attachment.max'        => 'Receipt file must be smaller than 4MB.',
        ]);
    }

    private function saveFile(Request $request, Payment $payment): void
    {
        if (! $request->hasFile('attachment')) {
            return;
        }
        if ($payment->attachment) {
            Storage::disk(self::DISK)->delete($payment->attachment);
        }
        $file = $request->file('attachment');
        $payment->update([
            'attachment'      => $file->store('payments/' . $payment->owner_id, self::DISK),
            'attachment_name' => $file->getClientOriginalName(),
        ]);
    }
}