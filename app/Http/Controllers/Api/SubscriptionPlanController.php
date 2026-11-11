<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Superadmin – Subscription Plans CRUD */
class SubscriptionPlanController extends Controller
{
    /** GET /api/subscription-plans?status= */
    public function index(Request $request): JsonResponse
    {
        AdminSubscription::expireOld();

        $plans = SubscriptionPlan::query()
            ->withCount(['subscriptions as active_count' => fn ($q) => $q->where('status', 'active')->where('ends_at', '>', now())])
            ->withCount('subscriptions')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->ordered()
            ->get();

        return response()->json(['data' => $plans->map(fn ($p) => $this->format($p))]);
    }

    /** POST /api/subscription-plans */
    public function store(Request $request): JsonResponse
    {
        $plan = SubscriptionPlan::create($this->validated($request) + ['created_by' => $request->user()->id]);

        return response()->json(['message' => 'Plan "' . $plan->name . '" created.', 'data' => $this->format($plan)], 201);
    }

    /** GET /api/subscription-plans/{plan} */
    public function show(SubscriptionPlan $plan): JsonResponse
    {
        return response()->json(['data' => $this->format($plan->loadCount('subscriptions'))]);
    }

    /** POST /api/subscription-plans/{plan} */
    public function update(Request $request, SubscriptionPlan $plan): JsonResponse
    {
        $plan->update($this->validated($request));

        return response()->json(['message' => 'Plan updated. (Already assigned subscriptions keep their old terms.)', 'data' => $this->format($plan)]);
    }

    /** PATCH /api/subscription-plans/{plan}/status */
    public function updateStatus(Request $request, SubscriptionPlan $plan): JsonResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(['active', 'inactive'])]]);
        $plan->update($data);

        return response()->json(['message' => $data['status'] === 'active' ? 'Plan activated.' : 'Plan deactivated – it can no longer be assigned.']);
    }

    /** DELETE /api/subscription-plans/{plan} – purane subscriptions rehte hain (plan ki copy saved hai) */
    public function destroy(SubscriptionPlan $plan): JsonResponse
    {
        $plan->delete();

        return response()->json(['message' => 'Plan deleted. Existing admin subscriptions are not affected.']);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'min:2', 'max:100'],
            'price'         => ['required', 'numeric', 'min:0', 'max:9999999'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:366'],
            'max_users'     => ['nullable', 'integer', 'min:1', 'max:100000'],
            'description'   => ['nullable', 'string', 'max:2000'],
            'features'      => ['nullable', 'array', 'max:20'],
            'features.*'    => ['nullable', 'string', 'max:150'],
            'status'        => ['nullable', Rule::in(['active', 'inactive'])],
            'sort_order'    => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], [
            'name.required'          => 'Please enter the plan name.',
            'price.required'         => 'Please enter the price.',
            'duration_days.required' => 'Please enter the duration in days.',
        ]);

        $data['features']   = array_values(array_filter(array_map('trim', $data['features'] ?? [])));
        $data['status']     = $data['status'] ?? 'active';
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }

    private function format(SubscriptionPlan $p): array
    {
        return [
            'id'                  => $p->id,
            'name'                => $p->name,
            'price'               => (float) $p->price,
            'duration_days'       => $p->duration_days,
            'max_users'           => $p->max_users,
            'description'         => $p->description,
            'features'            => $p->features ?? [],
            'status'              => $p->status,
            'sort_order'          => $p->sort_order,
            'active_count'        => (int) ($p->active_count ?? 0),
            'subscriptions_count' => (int) ($p->subscriptions_count ?? 0),
            'created_at'          => $p->created_at?->format('d M Y'),
        ];
    }
}