<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Usage:  ->middleware('role:superadmin')   or   ->middleware('role:superadmin,admin')
 * Sirf "active" users hi aage ja sakte hain.
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($user->status !== 'active') {
            $token = $user->currentAccessToken();
            if ($token && method_exists($token, 'delete')) {
                $token->delete();
            }

            return response()->json(['message' => 'Your account is not active. Please contact the superadmin.'], 403);
        }

        if ($roles && ! in_array($user->role, $roles, true)) {
            return response()->json(['message' => 'You are not allowed to perform this action.'], 403);
        }

        return $next($request);
    }
}