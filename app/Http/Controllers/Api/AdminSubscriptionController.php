<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminSubscriptionResource;
use App\Models\AdminSubscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

/**
 * Superadmin – admin ko ACCEPT karke subscription dena (turant active), cancel, renewal requests
 * Website se register hua admin tab tak users nahi bana sakta jab tak superadmin accept karke subscription na de.
 */
class AdminSubscriptionController extends Controller
{
    /** GET /api/subscriptions?status=&admin_id=&search=&page= */
    public function index(Request $request): AnonymousResourceCollection
    {
        AdminSubscription::expireOld();

        $request->validate([
            'status'   => ['nullable', Rule::in(array_keys(AdminSubscription::STATUSES))],
            'admin_id' => ['nullable', 'integer'],
            'search'   => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
        ]);

        $subs = AdminSubscription::query()
            ->with(['admin:id,name,email,phone', 'assigner:id,name', 'plan:id,features'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('admin_id'), fn ($q) => $q->where('admin_id', $request->admin_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(fn ($w) => $w->where('plan_name', 'like', $s)
                    ->orWhereHas('admin', fn ($a) => $a->where('name', 'like', $s)->orWhere('email', 'like', $s)));
            })
            ->latest()
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        $activeAdminIds = AdminSubscription::current()->pluck('admin_id')->unique();
        $everIds        = AdminSubscription::distinct()->pluck('admin_id');
        $totalAdmins    = User::where('role', User::ROLE_ADMIN)->count();

        // naye admins (website signup / created) jinhe abhi tak kabhi subscription nahi mila -> "Awaiting Acceptance"
        $awaiting = User::where('role', User::ROLE_ADMIN)->whereNotIn('id', $everIds)
            ->with('userProfile:id,user_id,city,interested_in')->latest()->get(['id', 'name', 'email', 'phone', 'status', 'created_at'])
            ->map(fn ($a) => [
                'id' => $a->id, 'name' => $a->name, 'email' => $a->email, 'phone' => $a->phone, 'status' => $a->status,
                'city' => $a->userProfile?->city, 'practice' => $a->userProfile?->interested_in,
                'joined' => $a->created_at?->format('d M Y'), 'joined_ago' => $a->created_at?->diffForHumans(),
            ]);
        // jinka subscription khatam ho gaya (pehle tha, ab nahi)
        $expiredAdmins = User::where('role', User::ROLE_ADMIN)->whereIn('id', $everIds)->whereNotIn('id', $activeAdminIds)
            ->get(['id', 'name', 'email', 'phone'])
            ->map(function ($a) {
                $last = AdminSubscription::where('admin_id', $a->id)->latest('ends_at')->first();
                return ['id' => $a->id, 'name' => $a->name, 'email' => $a->email, 'phone' => $a->phone,
                    'last_plan' => $last?->plan_name, 'ended' => $last?->ends_at?->format('d M Y'), 'last_status' => $last?->status];
            });

        return AdminSubscriptionResource::collection($subs)->additional([
            'stats' => [
                'active'         => $activeAdminIds->count(),
                'awaiting'       => $awaiting->count(),
                'expiring_soon'  => AdminSubscription::current()->where('ends_at', '<=', now()->addDays(5))->count(),
                'expired'        => $expiredAdmins->count(),
                'without'        => max(0, $totalAdmins - $activeAdminIds->count()),
                'open_requests'  => SubscriptionRequest::where('status', 'open')->count(),
                'revenue_month'  => (float) AdminSubscription::whereNotNull('accepted_at')->where('accepted_at', '>=', now()->startOfMonth())->sum('price'),
            ],
            // Assign modal ke liye – har admin ka current status
            'awaiting'       => $awaiting,
            'expired_admins' => $expiredAdmins,
            'admins' => User::where('role', User::ROLE_ADMIN)->orderBy('name')->get(['id', 'name', 'email', 'status'])
                ->map(function ($a) {
                    $cur = AdminSubscription::where('admin_id', $a->id)->where('status', 'active')->where('ends_at', '>', now())->orderByDesc('ends_at')->first();
                    return [
                        'id' => $a->id, 'name' => $a->name, 'email' => $a->email, 'status' => $a->status,
                        'subscription' => $cur ? $cur->plan_name . ' · till ' . $cur->ends_at->format('d M Y') : null,
                    ];
                }),
            'plans' => SubscriptionPlan::active()->ordered()->get(['id', 'name', 'price', 'duration_days', 'max_users']),
        ]);
    }

    /**
     * POST /api/subscriptions  { admin_id, plan_id, note? }
     * Superadmin admin ko ACCEPT karke subscription deta hai -> TURANT active (duration_days ke liye)
     * Admin ka pehle se active plan hai to naya uske khatam hone ke baad shuru hoga (renewal).
     * Admin ka account "pending" ho to "active" bhi ho jaata hai.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'admin_id'   => ['required', 'integer', Rule::exists('users', 'id')->where('role', User::ROLE_ADMIN)],
            'plan_id'    => ['required', 'integer', Rule::exists('subscription_plans', 'id')->where('status', 'active')],
            'note'       => ['nullable', 'string', 'max:1000'],
            'request_id' => ['nullable', 'integer', Rule::exists('subscription_requests', 'id')],
        ], [
            'admin_id.required' => 'Please select an admin.',
            'plan_id.required'  => 'Please select a plan.',
            'plan_id.exists'    => 'This plan is not active.',
        ]);

        AdminSubscription::expireOld();
        $plan  = SubscriptionPlan::findOrFail($data['plan_id']);
        $admin = User::findOrFail($data['admin_id']);

        $sub = \Illuminate\Support\Facades\DB::transaction(function () use ($plan, $admin, $data, $request) {
            // pehle se chalu plan hai to uske baad se shuru
            $lastEnd = AdminSubscription::where('admin_id', $admin->id)->where('status', 'active')->where('ends_at', '>', now())->max('ends_at');
            $start   = $lastEnd ? \Illuminate\Support\Carbon::parse($lastEnd) : now();

            // account accept (pending -> active)
            if ($admin->status !== 'active') {
                $admin->update(['status' => 'active']);
            }

            return AdminSubscription::create([
                'admin_id'      => $admin->id,
                'plan_id'       => $plan->id,
                'plan_name'     => $plan->name,
                'price'         => $plan->price,
                'duration_days' => $plan->duration_days,
                'max_users'     => $plan->max_users,
                'status'        => 'active',
                'note'          => $data['note'] ?? null,
                'assigned_by'   => $request->user()->id,
                'assigned_at'   => now(),
                'accepted_at'   => now(),
                'starts_at'     => $start,
                'ends_at'       => $start->copy()->addDays($plan->duration_days),
            ]);
        });

        // admin ki open request(s) resolve
        SubscriptionRequest::where('admin_id', $data['admin_id'])->where('status', 'open')
            ->update(['status' => 'resolved', 'resolved_by' => $request->user()->id, 'resolved_at' => now()]);

        return response()->json([
            'message' => $sub->starts_at->isFuture()
                ? $admin->name . ' renewed with "' . $plan->name . '" – starts ' . $sub->starts_at->format('d M Y') . ', valid till ' . $sub->ends_at->format('d M Y') . '.'
                : $admin->name . ' accepted with "' . $plan->name . '" – active till ' . $sub->ends_at->format('d M Y') . '.',
            'data'    => new AdminSubscriptionResource($sub->load(['admin:id,name,email,phone', 'assigner:id,name'])),
        ], 201);
    }

    /** POST /api/subscriptions/{subscription}/cancel – active subscription band (admin turant lock) */
    public function cancel(Request $request, AdminSubscription $subscription): JsonResponse
    {
        abort_unless($subscription->status === 'active', 422, 'Only active subscriptions can be cancelled.');

        $subscription->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        return response()->json(['message' => 'Subscription cancelled.']);
    }

    /** DELETE /api/subscriptions/{subscription} – record hatao (active nahi) */
    public function destroy(AdminSubscription $subscription): JsonResponse
    {
        abort_if($subscription->status === 'active' && $subscription->ends_at?->isFuture(), 422, 'Cancel the active subscription before deleting it.');
        $subscription->delete();

        return response()->json(['message' => 'Subscription record deleted.']);
    }

    /** GET /api/subscription-requests?status=open */
    public function requests(Request $request): JsonResponse
    {
        $rows = SubscriptionRequest::with(['admin:id,name,email,phone', 'plan:id,name,price'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()->limit(100)->get();

        return response()->json(['data' => $rows->map(fn ($r) => [
            'id'         => $r->id,
            'status'     => $r->status,
            'message'    => $r->message,
            'plan'       => $r->plan ? ['id' => $r->plan->id, 'name' => $r->plan->name, 'price' => (float) $r->plan->price] : null,
            'admin'      => $r->admin ? ['id' => $r->admin->id, 'name' => $r->admin->name, 'email' => $r->admin->email, 'phone' => $r->admin->phone] : null,
            'created_at' => $r->created_at?->format('d M Y, h:i A'),
            'time_ago'   => $r->created_at?->diffForHumans(),
        ])]);
    }

    /** PATCH /api/subscription-requests/{subscriptionRequest}  { status: resolved|dismissed } */
    public function updateRequest(Request $request, SubscriptionRequest $subscriptionRequest): JsonResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(['open', 'resolved', 'dismissed'])]]);
        $subscriptionRequest->update($data + ['resolved_by' => $request->user()->id, 'resolved_at' => now()]);

        return response()->json(['message' => 'Request marked as ' . $data['status'] . '.']);
    }
}