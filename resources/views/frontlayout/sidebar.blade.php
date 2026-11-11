@php
    $menu = [
        ['label' => 'Main'],
        ['route' => 'superadmin.dashboard', 'icon' => 'fa-solid fa-gauge-high',    'text' => 'Dashboard', 'only' => 'superadmin'],
        ['route' => 'admin.dashboard',      'icon' => 'fa-solid fa-gauge-high',    'text' => 'My Dashboard', 'only' => 'admin'],
        ['route' => 'user.dashboard',       'icon' => 'fa-solid fa-gauge-high',    'text' => 'My Dashboard', 'only' => 'user'],
        // Cases – superadmin: Add + All, admin (advocate): sirf apne assigned cases
        ['icon' => 'fa-solid fa-gavel', 'text' => 'Cases', 'id' => 'sub-cases', 'only' => 'superadmin', 'children' => [
            ['route' => 'superadmin.cases.create', 'text' => 'Add Case'],
            ['route' => 'superadmin.cases', 'active' => ['superadmin.cases', 'superadmin.cases.show', 'superadmin.cases.edit'], 'text' => 'All Cases'],
        ]],
        ['route' => 'superadmin.cases', 'active' => ['superadmin.cases', 'superadmin.cases.show'], 'icon' => 'fa-solid fa-gavel', 'text' => 'My Cases', 'only' => 'admin'],
        ['route' => 'superadmin.clients',   'icon' => 'fa-solid fa-users',         'text' => 'Clients', 'hide' => 'user'],
        ['route' => 'payments', 'icon' => 'fa-solid fa-indian-rupee-sign', 'text' => 'My Payments', 'hide' => 'user'],
        ['route' => 'superadmin.enquiries', 'icon' => 'fa-solid fa-envelope-open-text', 'text' => 'Enquiries', 'only' => 'superadmin', 'badge_id' => 'enqBadge', 'badge_title' => 'Unread enquiries'],

        // ---- User (client) menu ----
        ['label' => 'My Account', 'only' => 'user'],
        ['url' => route('user.dashboard') . '#profile', 'icon' => 'fa-regular fa-id-card', 'text' => 'My Profile', 'only' => 'user'],
        ['url' => route('user.dashboard') . '#history', 'icon' => 'fa-solid fa-clock-rotate-left', 'text' => 'Login History', 'only' => 'user'],
        ['url' => route('contact'), 'icon' => 'fa-solid fa-calendar-check', 'text' => 'Book Consultation', 'only' => 'user'],
        ['url' => route('practice.index'), 'icon' => 'fa-solid fa-scale-balanced', 'text' => 'Practice Areas', 'only' => 'user'],

        // ---- Admin (advocate) menu ----
        ['label' => 'My Account', 'only' => 'admin'],
        ['url' => route('admin.dashboard') . '#profile', 'icon' => 'fa-regular fa-id-card', 'text' => 'My Profile', 'only' => 'admin'],
        ['url' => route('admin.dashboard') . '#history', 'icon' => 'fa-solid fa-clock-rotate-left', 'text' => 'Login History', 'only' => 'admin'],
        // ---- Admin: subscription ----
        ['route' => 'admin.subscription', 'icon' => 'fa-solid fa-crown', 'text' => 'My Subscription', 'only' => 'admin', 'badge_id' => 'subBadge', 'badge_title' => 'Subscription needs attention'],
        // (Create User hata diya – ab CLIENT hi USER hai: Clients me add karo, wahi login karega)
        ['label' => 'Admin Management', 'only' => 'superadmin'],
        ['route' => 'superadmin.admins.create', 'icon' => 'fa-solid fa-user-plus', 'text' => 'Create Admin', 'only' => 'superadmin'],
        ['route' => 'superadmin.admins', 'active' => ['superadmin.admins', 'superadmin.admins.edit'], 'icon' => 'fa-solid fa-user-tie', 'text' => 'All Admins', 'only' => 'superadmin', 'badge_id' => 'pendingBadge'],
        // Users – abhi band (Client hi User hai). Wapas chahiye to neeche ki line uncomment karo
        // ['route' => 'superadmin.users', 'icon' => 'fa-solid fa-users-line', 'text' => 'Users', 'only' => 'superadmin'],
        ['icon' => 'fa-solid fa-crown', 'text' => 'Subscriptions', 'id' => 'sub-subs', 'only' => 'superadmin', 'children' => [
            ['route' => 'superadmin.subscriptions.plans', 'text' => 'Plans'],
            ['route' => 'superadmin.subscriptions', 'text' => 'Admin Subscriptions'],
        ]],
        ['route' => 'superadmin.login-activity', 'icon' => 'fa-solid fa-clock-rotate-left', 'text' => 'Login Activity', 'only' => 'superadmin'],
        ['label' => 'Firm', 'hide' => 'user'],
        // Practice Areas – superadmin: dynamic (API), admin: sirf website link
        ['icon' => 'fa-solid fa-briefcase', 'text' => 'Practice Areas', 'id' => 'sub-practice', 'only' => 'superadmin', 'children' => [
            ['route' => 'superadmin.practice-areas.create', 'text' => 'Add Practice Area'],
            ['route' => 'superadmin.practice-areas', 'active' => ['superadmin.practice-areas', 'superadmin.practice-areas.edit'], 'text' => 'All Practice Areas'],
            ['url' => route('practice.index'), 'text' => 'View on Website', 'blank' => true],
        ]],
        ['url' => route('practice.index'), 'icon' => 'fa-solid fa-briefcase', 'text' => 'Practice Areas', 'only' => 'admin', 'blank' => true],
        // Career – job openings + website applications
        ['icon' => 'fa-solid fa-user-graduate', 'text' => 'Career', 'id' => 'sub-career', 'only' => 'superadmin', 'children' => [
            ['route' => 'superadmin.career.jobs.create', 'text' => 'Add Job Opening'],
            ['route' => 'superadmin.career.jobs', 'active' => ['superadmin.career.jobs', 'superadmin.career.jobs.edit'], 'text' => 'Job Openings'],
            ['route' => 'superadmin.career.applications', 'text' => 'Applications'],
            ['url' => route('career'), 'text' => 'View on Website', 'blank' => true],
        ]],
        // Reports – superadmin: poore system ki | admin: sirf apni
        ['route' => 'reports', 'icon' => 'fa-solid fa-chart-pie', 'text' => 'Reports', 'hide' => 'user'],
        ['label' => 'System', 'only' => 'superadmin'],
        ['route' => 'superadmin.settings',  'icon' => 'fa-solid fa-gear',          'text' => 'Settings', 'only' => 'superadmin'],
        ['url' => route('home'), 'icon' => 'fa-solid fa-globe', 'text' => 'View Website', 'blank' => true],
    ];
@endphp

<style>
    /* submenu active link (Practice Areas etc.) */
    .sb-sub a.active { color: var(--gold-light, #f3d77a); padding-left: 18px; }
    .sb-sub a.active::before { background: var(--gold, #d4af37); box-shadow: 0 0 8px var(--gold, #d4af37); }
</style>

<aside class="sidebar" id="sidebar">
    <div class="sb-head">
    <a href="{{ route('superadmin.dashboard') }}" class="sb-logo" data-home-link>
    <span class="sb-logo-icon">
        <img src="{{ asset('images/logo.png') }}" alt="LEXORA Logo">
    </span>
    <span class="sb-logo-text">LEXORA<small data-user="panel_label">DASHBOARD</small></span>
</a>
        <button class="sb-close" id="sbClose" aria-label="Close sidebar"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="sb-user">
        <span class="user-avatar" data-user-avatar><i class="fa-solid fa-user"></i></span>
        <div class="sb-user-info">
            <h6 data-user="name">Loading…</h6>
            <span><i class="fa-solid fa-circle"></i> <span data-user="role_label">—</span></span>
        </div>
    </div>

    <nav class="sb-nav">
        <ul>
            @foreach($menu as $m)
                @if(isset($m['label']))
                    <li class="sb-label {{ isset($m['only']) ? 'only-'.$m['only'] : '' }} {{ isset($m['hide']) ? 'hide-'.$m['hide'] : '' }}"><span>{{ $m['label'] }}</span></li>
                @elseif(isset($m['children']))
                    @php
                        $subOpen = collect($m['children'])->contains(fn ($c) => isset($c['route']) && request()->routeIs(...($c['active'] ?? [$c['route']])));
                    @endphp
                    <li class="sb-item has-sub {{ $subOpen ? 'open' : '' }} {{ isset($m['only']) ? 'only-'.$m['only'] : '' }} {{ isset($m['hide']) ? 'hide-'.$m['hide'] : '' }}">
                        <button class="sb-link sb-toggle" data-target="{{ $m['id'] }}" data-tip="{{ $m['text'] }}">
                            <i class="{{ $m['icon'] }}"></i>
                            <span class="sb-text">{{ $m['text'] }}</span>
                            <i class="fa-solid fa-chevron-down sb-caret"></i>
                        </button>
                        <ul class="sb-sub" id="{{ $m['id'] }}">
                            @foreach($m['children'] as $c)
                                @php
                                    $cHref   = isset($c['route']) ? route($c['route']) : ($c['url'] ?? '#');
                                    $cActive = isset($c['route']) && request()->routeIs(...($c['active'] ?? [$c['route']]));
                                @endphp
                                <li>
                                    <a href="{{ $cHref }}" class="{{ $cActive ? 'active' : '' }}" @if(!empty($c['blank'])) target="_blank" @endif>
                                        {{ $c['text'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @else
                    @php
                        $href   = isset($m['route']) ? route($m['route']) : ($m['url'] ?? '#');
                        $active = isset($m['route']) && request()->routeIs(...($m['active'] ?? [$m['route']]));
                    @endphp
                    <li class="sb-item {{ isset($m['only']) ? 'only-'.$m['only'] : '' }} {{ isset($m['hide']) ? 'hide-'.$m['hide'] : '' }}">
                        <a href="{{ $href }}" @if(!empty($m['blank'])) target="_blank" @endif class="sb-link {{ $active ? 'active' : '' }}" data-tip="{{ $m['text'] }}">
                            <i class="{{ $m['icon'] }}"></i>
                            <span class="sb-text">{{ $m['text'] }}</span>
                            @isset($m['badge'])
                                <span class="sb-badge {{ !empty($m['hot']) ? 'hot' : '' }}">{{ $m['badge'] }}</span>
                            @endisset
                            @isset($m['badge_id'])
                                <span class="sb-badge hot" id="{{ $m['badge_id'] }}" title="{{ $m['badge_title'] ?? 'Pending approvals' }}" hidden>0</span>
                            @endisset
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </nav>

    <div class="sb-foot">

        <a href="#" class="sb-link sb-logout" data-logout data-tip="Logout">
            <i class="fa-solid fa-right-from-bracket"></i><span class="sb-text">Logout</span>
        </a>
    </div>
</aside>

{{-- Enquiries unread badge (superadmin) – API se --}}
<script>
document.addEventListener('auth:ready', function (e) {
    if (!window.API || e.detail.role !== 'superadmin' || /\/superadmin\/enquiries/.test(location.pathname)) return;
    API.get('/enquiries/unread-count').then(function (r) {
        var b = document.getElementById('enqBadge');
        if (b) { b.textContent = r.unread; b.hidden = !r.unread; }
    }).catch(function () {});
});
</script>

{{-- Admin: subscription banner + badge (har page par) --}}
<script>
document.addEventListener('auth:ready', function (e) {
    if (!window.API || e.detail.role !== 'admin') return;
    API.get('/my-subscription/status').then(function (s) {
        window.LX_SUB = s;
        var b = document.getElementById('subBadge');
        if (b) { b.textContent = '!'; b.hidden = s.active && s.days_left > 5; }
        if (/\/admin\/subscription/.test(location.pathname)) return;
        var msg = null, cls = 'warn';
        if (!s.active && !s.ever) { msg = '<i class="fa-solid fa-hourglass-half"></i> <b>Your account is waiting for superadmin acceptance.</b> Adding clients will unlock once the superadmin accepts you and activates your subscription.'; cls = 'gold'; }
        else if (!s.active) { msg = '<i class="fa-solid fa-lock"></i> <b>Your subscription has expired.</b> Adding and managing clients is locked. Please contact the superadmin to renew.'; cls = 'red'; }
        else if (s.days_left <= 5) { msg = '<i class="fa-regular fa-clock"></i> Your <b>' + API.esc(s.plan_name) + '</b> plan ends in <b>' + s.days_left + ' day' + (s.days_left === 1 ? '' : 's') + '</b> (' + API.esc(s.ends_at) + '). Contact the superadmin to renew.'; }
        if (!msg) return;
        var main = document.querySelector('.adm-content');
        if (!main) return;
        var head = main.querySelector('.page-head');
        var bar = document.createElement('div');
        bar.className = 'sub-banner ' + cls;
        bar.innerHTML = '<span>' + msg + '</span><a href="' + API.url('/admin/subscription') + '" class="adm-btn adm-btn-gold sm">View Subscription</a>';
        head ? head.after(bar) : main.insertBefore(bar, main.firstChild);
    }).catch(function () {});
});
</script>
<style>
.sub-banner { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; padding: 12px 16px; margin-bottom: 18px; border-radius: 14px; font-size: 13.5px; color: #eee; animation: fadeUp .45s ease both;
    background: rgba(255,179,71,.1); border: 1px solid rgba(255,179,71,.4); }
.sub-banner.red { background: rgba(255,107,107,.09); border-color: rgba(255,107,107,.45); }
.sub-banner.gold { background: rgba(212,175,55,.12); border-color: rgba(212,175,55,.5); }
.sub-banner i { margin-right: 6px; color: var(--gold); }
.sub-banner.red i { color: #ff8c8c; }
.sb-logo-icon img {
    width: 45px;
    /* height: 38px; */
    object-fit: contain;
    display: block;
}
</style>