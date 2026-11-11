<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoginLogResource;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

/**
 * Superadmin only – sabhi login attempts (role wise)
 */
class LoginLogController extends Controller
{
    /** GET /api/login-logs?role=&status=&search=&date=&page= */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'role'     => ['nullable', Rule::in(User::ROLES)],
            'status'   => ['nullable', Rule::in(['success', 'failed', 'blocked'])],
            'search'   => ['nullable', 'string', 'max:100'],
            'date'     => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
        ]);

        $logs = LoginLog::query()
            ->with('user:id,name,email,photo')
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->role))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('date'), fn ($q) => $q->whereDate('logged_in_at', $request->date))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(fn ($w) => $w
                    ->where('login_input', 'like', $s)
                    ->orWhere('ip_address', 'like', $s)
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $s)->orWhere('email', 'like', $s)));
            })
            ->latest('logged_in_at')
            ->latest('id')
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return LoginLogResource::collection($logs);
    }
}