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
use Illuminate\Validation\Rule;

/**
 * Admin (Advocate) – apna subscription dekhna + superadmin se request (renewal)
 *  Superadmin admin ko accept karke plan deta hai -> turant active (1 month)
 *  Khatam hone par admin yahan se superadmin ko request bhejta hai
 */
class MySubscriptionController extends Controller
{
    /** GET /api/my-subscription/status – halka (sidebar banner / badge) */
    public function status(Request $request): JsonResponse
    {
        $me  = $request->user();
        $cur = AdminSubscription::currentFor($me->id);

        return response()->json([
            'active'        => (bool) $cur,
            'plan_name'     => $cur?->plan_name,
            'days_left'     => $cur?->daysLeft() ?? 0,
            'ends_at'       => $cur?->ends_at?->format('d M Y'),
            'max_users'     => $cur?->max_users,
            'users_count'   => User::where('created_by', $me->id)->count(),
            'ever'          => AdminSubscription::where('admin_id', $me->id)->exists(),   // false = abhi tak accept nahi hua
            'open_request'  => SubscriptionRequest::where('admin_id', $me->id)->where('status', 'open')->exists(),
        ]);
    }

    /** GET /api/my-subscription – poori jaankari */
    public function show(Request $request): JsonResponse
    {
        $me  = $request->user();
        $cur = AdminSubscription::currentFor($me->id);

        return response()->json([
            'current'   => $cur ? new AdminSubscriptionResource($cur->load('plan:id,features')) : null,
            'scheduled' => AdminSubscriptionResource::collection(
                AdminSubscription::where('admin_id', $me->id)->where('status', 'active')->where('starts_at', '>', now())->orderBy('starts_at')->get()
            ),
            'history'   => AdminSubscriptionResource::collection(
                AdminSubscription::with('assigner:id,name')->where('admin_id', $me->id)->latest()->limit(20)->get()
            ),
            'plans'     => SubscriptionPlan::active()->ordered()->get(['id', 'name', 'price', 'duration_days', 'max_users', 'description', 'features']),
            'open_request' => SubscriptionRequest::with('plan:id,name')->where('admin_id', $me->id)->where('status', 'open')->latest()->first()?->only(['id', 'message', 'created_at']),
            'users_count'  => User::where('created_by', $me->id)->count(),
            'support'      => ['email' => config('mail.from.address'), 'phone' => '+91 99999 99999'],
        ]);
    }

    /** POST /api/my-subscription/request  { plan_id?, message? } – superadmin se subscription / renewal maango */
    public function request(Request $request): JsonResponse
    {
        $me   = $request->user();
        $data = $request->validate([
            'plan_id' => ['nullable', 'integer', Rule::exists('subscription_plans', 'id')->where('status', 'active')],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        if (SubscriptionRequest::where('admin_id', $me->id)->where('status', 'open')->exists()) {
            return response()->json(['message' => 'You already have an open request. The superadmin will contact you soon.'], 422);
        }

        SubscriptionRequest::create(['admin_id' => $me->id, 'plan_id' => $data['plan_id'] ?? null, 'message' => $data['message'] ?? null, 'status' => 'open']);

        return response()->json(['message' => 'Request sent to the superadmin. You will see the subscription here once it is assigned.'], 201);
    }
}