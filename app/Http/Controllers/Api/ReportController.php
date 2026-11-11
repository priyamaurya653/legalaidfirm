<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminSubscription;
use App\Models\CareerApplication;
use App\Models\Client;
use App\Models\Enquiry;
use App\Models\JobOpening;
use App\Models\LegalCase;
use App\Models\LoginLog;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

/**
 * REPORTS – GET /api/reports?from=YYYY-MM-DD&to=YYYY-MM-DD&admin_id=
 *
 *  superadmin : poore system ki report (cases, clients, admins, subscriptions, enquiries, career, logins)
 *               admin_id dene par sirf us admin ke cases / clients + uska subscription / logins
 *  admin      : SIRF apni report (apne cases, apne clients, apna subscription, apne logins)
 *  payments   : HAR KOI SIRF APNE payments (superadmin bhi dusre ka payment nahi dekhta)
 */
class ReportController extends Controller
{
    private Carbon $from;
    private Carbon $to;

    public function index(Request $request): JsonResponse
    {
        $me   = $request->user();
        $isSA = $me->role === User::ROLE_SUPERADMIN;

        $request->validate([
            'from'     => ['nullable', 'date'],
            'to'       => ['nullable', 'date', 'after_or_equal:from'],
            'admin_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', User::ROLE_ADMIN)],
        ]);

        $this->from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : now()->startOfYear();
        $this->to   = $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : now()->endOfDay();

        // kiski report: superadmin -> sab ya chuna hua admin, admin -> khud
        $focus = $isSA ? ($request->filled('admin_id') ? User::find($request->admin_id) : null) : $me;

        AdminSubscription::expireOld();

        $data = [
            'period' => [
                'from'  => $this->from->toDateString(),
                'to'    => $this->to->toDateString(),
                'label' => $this->from->format('d M Y') . ' – ' . $this->to->format('d M Y'),
            ],
            'scope' => [
                'role'       => $me->role,
                'is_global'  => $isSA && ! $focus,
                'focus'      => $focus ? ['id' => $focus->id, 'name' => $focus->name, 'email' => $focus->email] : null,
                'generated'  => now()->format('d M Y, h:i A'),
            ],
            'cases'    => $this->cases($me, $focus, $isSA),
            'clients'  => $this->clients($me, $focus, $isSA),
            'payments' => $this->payments($me),                         // hamesha sirf apne
        ];

        if ($isSA && ! $focus) {
            $data['admins']        = $this->admins();
            $data['subscriptions'] = $this->subscriptions();
            $data['enquiries']     = $this->enquiries();
            $data['career']        = $this->career();
            $data['logins']        = $this->logins(null);
        } else {
            $target = $focus ?? $me;
            $data['my_subscription'] = $this->subscriptionOf($target);
            $data['logins']          = $this->logins($target);
        }

        if ($isSA) {
            $data['admin_options'] = User::where('role', User::ROLE_ADMIN)->orderBy('name')->get(['id', 'name']);
        }

        return response()->json($data);
    }

    /* ======================================================== CASES */

    private function caseQuery(User $me, ?User $focus, bool $isSA): Builder
    {
        $q = LegalCase::query();
        if (! $isSA) {
            return $q->visibleTo($me);
        }

        return $focus ? $q->where('legal_cases.advocate_id', $focus->id) : $q;
    }

    private function cases(User $me, ?User $focus, bool $isSA): ?array
    {
        if (! Schema::hasTable('legal_cases')) {
            return null;
        }
        $base = fn () => $this->caseQuery($me, $focus, $isSA);

        return [
            'total'        => $base()->count(),
            'new'          => $base()->whereBetween('legal_cases.created_at', [$this->from, $this->to])->count(),
            'by_status'    => $this->labelled($base()->selectRaw('status as k, COUNT(*) as v')->groupBy('status')->pluck('v', 'k'), LegalCase::STATUSES),
            'by_priority'  => $base()->selectRaw('priority as k, COUNT(*) as v')->groupBy('priority')->pluck('v', 'k'),
            'by_practice'  => $base()->leftJoin('practice_areas', 'practice_areas.id', '=', 'legal_cases.practice_area_id')
                ->selectRaw("COALESCE(practice_areas.name, 'Not set') as k, COUNT(*) as v")->groupBy('k')->orderByDesc('v')->limit(8)->pluck('v', 'k'),
            'by_advocate'  => $isSA && ! $focus
                ? $base()->leftJoin('users', 'users.id', '=', 'legal_cases.advocate_id')
                    ->selectRaw("COALESCE(users.name, 'Unassigned') as k, COUNT(*) as v")->groupBy('k')->orderByDesc('v')->limit(10)->pluck('v', 'k')
                : null,
            'trend'        => $this->trend($base(), 'legal_cases.created_at'),
            'hearings'     => $base()->with(['advocate:id,name'])
                ->whereNotNull('next_hearing_date')->whereBetween('next_hearing_date', [today(), today()->addDays(30)])
                ->whereIn('status', ['active', 'hearing', 'pending'])
                ->orderBy('next_hearing_date')->limit(10)
                ->get(['legal_cases.id', 'case_no', 'title', 'client_name', 'next_hearing_date', 'advocate_id', 'status'])
                ->map(fn ($c) => [
                    'id' => $c->id, 'case_no' => $c->case_no, 'title' => $c->title, 'client' => $c->client_name,
                    'date' => $c->next_hearing_date?->format('d M Y'), 'advocate' => $c->advocate?->name, 'status' => $c->statusLabel(),
                ]),
        ];
    }

    /* ======================================================== CLIENTS */

    private function clientQuery(User $me, ?User $focus, bool $isSA): Builder
    {
        $q = Client::query();
        if (! $isSA) {
            return $q->visibleTo($me);
        }

        return $focus ? $q->where('clients.advocate_id', $focus->id) : $q;
    }

    private function clients(User $me, ?User $focus, bool $isSA): ?array
    {
        if (! Schema::hasTable('clients')) {
            return null;
        }
        $base     = fn () => $this->clientQuery($me, $focus, $isSA);
        $hasLogin = Schema::hasColumn('clients', 'user_id');

        return [
            'total'       => $base()->count(),
            'active'      => $base()->where('clients.status', 'active')->count(),
            'inactive'    => $base()->where('clients.status', '!=', 'active')->count(),
            'with_login'  => $hasLogin ? $base()->whereNotNull('clients.user_id')->count() : 0,
            'new'         => $base()->whereBetween('clients.created_at', [$this->from, $this->to])->count(),
            'by_practice' => $base()->leftJoin('practice_areas', 'practice_areas.id', '=', 'clients.practice_area_id')
                ->selectRaw("COALESCE(practice_areas.name, 'Not set') as k, COUNT(*) as v")->groupBy('k')->orderByDesc('v')->limit(8)->pluck('v', 'k'),
            'by_city'     => $base()->selectRaw("COALESCE(NULLIF(clients.city, ''), 'Not set') as k, COUNT(*) as v")->groupBy('k')->orderByDesc('v')->limit(8)->pluck('v', 'k'),
            'by_advocate' => $isSA && ! $focus
                ? $base()->leftJoin('users', 'users.id', '=', 'clients.advocate_id')
                    ->selectRaw("COALESCE(users.name, 'Unassigned') as k, COUNT(*) as v")->groupBy('k')->orderByDesc('v')->limit(10)->pluck('v', 'k')
                : null,
            'trend'       => $this->trend($base(), 'clients.created_at'),
        ];
    }

    /* ======================================================== PAYMENTS (sirf apne) */

    private function payments(User $me): ?array
    {
        if (! Schema::hasTable('payments')) {
            return null;
        }
        $base   = fn () => Payment::ownedBy($me);
        $period = fn () => $base()->whereBetween('payment_date', [$this->from->toDateString(), $this->to->toDateString()]);

        return [
            'private'      => true,
            'received'     => (float) $period()->where('status', 'received')->sum('amount'),
            'pending'      => (float) $base()->where('status', 'pending')->sum('amount'),
            'count'        => $period()->count(),
            'avg'          => round((float) $period()->where('status', 'received')->avg('amount'), 2),
            'all_time'     => (float) $base()->where('status', 'received')->sum('amount'),
            'by_mode'      => $period()->where('status', 'received')->selectRaw('payment_mode as k, SUM(amount) as v')->groupBy('payment_mode')->orderByDesc('v')->pluck('v', 'k')->map(fn ($v) => (float) $v),
            'by_category'  => $period()->where('status', 'received')->selectRaw("COALESCE(NULLIF(category, ''), 'Other') as k, SUM(amount) as v")->groupBy('k')->orderByDesc('v')->limit(8)->pluck('v', 'k')->map(fn ($v) => (float) $v),
            'trend'        => $this->trend($period()->where('status', 'received'), 'payment_date', 'SUM(amount)'),
            'top_clients'  => $period()->where('status', 'received')->selectRaw('client_name as name, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('client_name')->orderByDesc('total')->limit(5)->get()->map(fn ($r) => ['name' => $r->name, 'count' => (int) $r->count, 'total' => (float) $r->total]),
        ];
    }

    /* ======================================================== SUPERADMIN ONLY */

    private function admins(): array
    {
        $q = fn () => User::where('role', User::ROLE_ADMIN);

        return [
            'total'    => $q()->count(),
            'active'   => $q()->where('status', 'active')->count(),
            'pending'  => $q()->where('status', 'pending')->count(),
            'inactive' => $q()->where('status', 'inactive')->count(),
            'new'      => $q()->whereBetween('created_at', [$this->from, $this->to])->count(),
            'trend'    => $this->trend($q(), 'created_at'),
            'top'      => $q()->withCount(['assignedClients'])
                ->when(Schema::hasTable('legal_cases'), fn ($x) => $x->withCount(['assignedCases']))
                ->orderByDesc('assigned_clients_count')->limit(10)->get(['id', 'name', 'email', 'status'])
                ->map(fn ($a) => [
                    'id' => $a->id, 'name' => $a->name, 'email' => $a->email, 'status' => $a->status,
                    'clients' => (int) $a->assigned_clients_count, 'cases' => (int) ($a->assigned_cases_count ?? 0),
                    'subscription' => AdminSubscription::where('admin_id', $a->id)->current()->value('plan_name'),
                ]),
        ];
    }

    private function subscriptions(): ?array
    {
        if (! Schema::hasTable('admin_subscriptions')) {
            return null;
        }
        $period = fn () => AdminSubscription::whereBetween('accepted_at', [$this->from, $this->to]);
        $ever   = AdminSubscription::distinct()->pluck('admin_id');

        return [
            'active'        => AdminSubscription::current()->distinct()->count('admin_id'),
            'expired'       => User::where('role', User::ROLE_ADMIN)->whereIn('id', $ever)
                ->whereNotIn('id', AdminSubscription::current()->pluck('admin_id'))->count(),
            'awaiting'      => User::where('role', User::ROLE_ADMIN)->whereNotIn('id', $ever)->count(),
            'given'         => $period()->count(),
            'revenue'       => (float) $period()->sum('price'),
            'by_plan'       => $period()->selectRaw('plan_name as name, COUNT(*) as count, SUM(price) as revenue')->groupBy('plan_name')->orderByDesc('revenue')->get()
                ->map(fn ($r) => ['name' => $r->name, 'count' => (int) $r->count, 'revenue' => (float) $r->revenue]),
            'trend'         => $this->trend($period(), 'accepted_at', 'SUM(price)'),
            'expiring'      => AdminSubscription::with('admin:id,name,email')->current()->where('ends_at', '<=', now()->addDays(7))->orderBy('ends_at')->limit(10)->get()
                ->map(fn ($s) => ['admin' => $s->admin?->name, 'email' => $s->admin?->email, 'plan' => $s->plan_name, 'ends' => $s->ends_at?->format('d M Y'), 'days_left' => $s->daysLeft()]),
        ];
    }

    private function enquiries(): ?array
    {
        if (! Schema::hasTable('enquiries')) {
            return null;
        }
        $period = fn () => Enquiry::whereBetween('created_at', [$this->from, $this->to]);

        return [
            'total'       => $period()->count(),
            'unread'      => Enquiry::whereNull('read_at')->count(),
            'by_status'   => $this->labelled($period()->selectRaw('status as k, COUNT(*) as v')->groupBy('status')->pluck('v', 'k'), Enquiry::STATUSES),
            'by_practice' => $period()->selectRaw("COALESCE(NULLIF(practice_area, ''), 'General') as k, COUNT(*) as v")->groupBy('k')->orderByDesc('v')->limit(8)->pluck('v', 'k'),
            'trend'       => $this->trend($period(), 'created_at'),
        ];
    }

    private function career(): ?array
    {
        if (! Schema::hasTable('career_applications')) {
            return null;
        }
        $period = fn () => CareerApplication::whereBetween('created_at', [$this->from, $this->to]);

        return [
            'applications' => $period()->count(),
            'open_jobs'    => JobOpening::live()->count(),
            'positions'    => (int) JobOpening::live()->sum('openings'),
            'by_status'    => $this->labelled($period()->selectRaw('status as k, COUNT(*) as v')->groupBy('status')->pluck('v', 'k'), CareerApplication::STATUSES),
            'by_job'       => $period()->selectRaw("COALESCE(position, 'General') as k, COUNT(*) as v")->groupBy('k')->orderByDesc('v')->limit(8)->pluck('v', 'k'),
        ];
    }

    private function logins(?User $user): array
    {
        $q = fn () => LoginLog::whereBetween('logged_in_at', [$this->from, $this->to])->when($user, fn ($x) => $x->where('user_id', $user->id));

        // pichhle 14 din (din-wise)
        $start = today()->subDays(13);
        $rows  = LoginLog::where('logged_in_at', '>=', $start)->when($user, fn ($x) => $x->where('user_id', $user->id))
            ->get(['logged_in_at', 'status'])->groupBy(fn ($l) => $l->logged_in_at->toDateString());
        $daily = collect(range(0, 13))->map(function ($i) use ($start, $rows) {
            $d = $start->copy()->addDays($i)->toDateString();
            $g = $rows->get($d, collect());
            return ['label' => Carbon::parse($d)->format('d M'), 'success' => $g->where('status', 'success')->count(), 'failed' => $g->where('status', '!=', 'success')->count()];
        });

        return [
            'success' => $q()->where('status', 'success')->count(),
            'failed'  => $q()->where('status', '!=', 'success')->count(),
            'by_role' => $user ? null : $q()->where('status', 'success')->selectRaw("COALESCE(role, 'unknown') as k, COUNT(*) as v")->groupBy('k')->pluck('v', 'k'),
            'daily'   => $daily,
            'last'    => $user?->last_login_at?->format('d M Y, h:i A'),
        ];
    }

    private function subscriptionOf(User $admin): ?array
    {
        if (! Schema::hasTable('admin_subscriptions') || $admin->role !== User::ROLE_ADMIN) {
            return null;
        }
        $cur = AdminSubscription::currentFor($admin->id);

        return [
            'active'    => (bool) $cur,
            'plan'      => $cur?->plan_name,
            'ends'      => $cur?->ends_at?->format('d M Y'),
            'days_left' => $cur?->daysLeft() ?? 0,
            'max_users' => $cur?->max_users,
            'history'   => AdminSubscription::where('admin_id', $admin->id)->latest()->limit(6)->get()
                ->map(fn ($s) => ['plan' => $s->plan_name, 'price' => (float) $s->price, 'from' => $s->starts_at?->format('d M Y'), 'to' => $s->ends_at?->format('d M Y'), 'status' => $s->statusLabel()]),
        ];
    }

    /* ======================================================== HELPERS */

    /** status keys ko labels me badlo (aur jo 0 hain unhe bhi dikhao) */
    private function labelled($pairs, array $labels): array
    {
        $out = [];
        foreach ($labels as $key => $label) {
            $out[$label] = (int) ($pairs[$key] ?? 0);
        }
        foreach ($pairs as $key => $v) {
            if (! isset($labels[$key])) $out[ucfirst((string) $key)] = (int) $v;
        }

        return $out;
    }

    /** mahine-wise trend (period ke andar, max 24 mahine) */
    private function trend(Builder $query, string $column, string $agg = 'COUNT(*)'): array
    {
        $driver = DB::connection()->getDriverName();
        $expr   = match ($driver) {
            'sqlite' => "strftime('%Y-%m', {$column})",
            'pgsql'  => "to_char({$column}, 'YYYY-MM')",
            default  => "DATE_FORMAT({$column}, '%Y-%m')",
        };

        $rows = (clone $query)->whereBetween($column, [$this->from, $this->to])
            ->selectRaw("{$expr} as ym, {$agg} as v")->groupByRaw($expr)->pluck('v', 'ym');

        $out    = [];
        $cursor = $this->from->copy()->startOfMonth();
        $end    = $this->to->copy()->startOfMonth();
        $guard  = 0;
        while ($cursor->lte($end) && $guard++ < 24) {
            $key   = $cursor->format('Y-m');
            $out[] = ['label' => $cursor->format('M y'), 'value' => (float) ($rows[$key] ?? 0)];
            $cursor->addMonth();
        }

        return $out;
    }
}