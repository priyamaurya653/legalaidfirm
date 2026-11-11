<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /** GET /api/dashboard/stats */
    public function stats(): JsonResponse
    {
        $count = fn (string $role) => User::where('role', $role)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $admins = $count(User::ROLE_ADMIN);
        $users  = $count(User::ROLE_USER);

        $today = LoginLog::whereDate('logged_in_at', today())
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $byRole = LoginLog::whereDate('logged_in_at', today())
            ->where('status', 'success')
            ->selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        // pichhle 7 din ka login trend (chart ke liye)
        $from = today()->subDays(6);
        $rows = LoginLog::where('logged_in_at', '>=', $from)
            ->selectRaw('DATE(logged_in_at) as d, status, COUNT(*) as total')
            ->groupBy('d', 'status')
            ->get();
        $week = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $from->copy()->addDays($i);
            $key  = $date->toDateString();
            $day  = $rows->filter(fn ($r) => (string) $r->d === $key);
            $week[] = [
                'date'    => $key,
                'label'   => $date->format('D d'),
                'success' => (int) $day->where('status', 'success')->sum('total'),
                'failed'  => (int) $day->whereIn('status', ['failed', 'blocked'])->sum('total'),
            ];
        }

        return response()->json([
            'logins_week' => $week,
            'admins' => [
                'total'    => (int) $admins->sum(),
                'active'   => (int) ($admins['active'] ?? 0),
                'pending'  => (int) ($admins['pending'] ?? 0),
                'inactive' => (int) ($admins['inactive'] ?? 0),
            ],
            'users' => [
                'total'    => (int) $users->sum(),
                'active'   => (int) ($users['active'] ?? 0),
                'inactive' => (int) ($users['inactive'] ?? 0),
            ],
            'logins_today' => [
                'success'    => (int) ($today['success'] ?? 0),
                'failed'     => (int) ($today['failed'] ?? 0),
                'blocked'    => (int) ($today['blocked'] ?? 0),
                'superadmin' => (int) ($byRole['superadmin'] ?? 0),
                'admin'      => (int) ($byRole['admin'] ?? 0),
                'user'       => (int) ($byRole['user'] ?? 0),
            ],
        ]);
    }
}