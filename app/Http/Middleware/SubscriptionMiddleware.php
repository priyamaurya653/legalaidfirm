<?php

namespace App\Http\Middleware;

use App\Models\AdminSubscription;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin (Advocate) ke paas active subscription nahi hai to block (403)
 * Superadmin hamesha allowed.
 * Usage: ->middleware(SubscriptionMiddleware::class)
 */
class SubscriptionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === User::ROLE_ADMIN && ! AdminSubscription::currentFor($user->id)) {
            return response()->json([
                'message' => 'Your subscription is not active. Please accept a subscription or contact the superadmin to continue.',
                'code'    => 'subscription_required',
            ], 403);
        }

        return $next($request);
    }
}