@extends('frontlayout.app')
@section('title', 'My Dashboard')
@section('page_title', 'My Dashboard')
@section('require_role', 'user')

{{-- =====================================================================
     USER DASHBOARD  –  resources/views/user/dashboard.blade.php   (/dashboard)
     Layout: frontlayout.app (same sidebar + topbar, user wala menu)
     APIs  : GET /api/auth/me · GET /api/auth/login-history   (Bearer token)
     ===================================================================== --}}

@section('page_actions')
    <a href="{{ route('contact') }}" class="adm-btn adm-btn-gold"><i class="fa-solid fa-calendar-check"></i> Book Consultation</a>
@endsection

@push('styles')
<style>
    .ud-hero { position: relative; display: flex; align-items: center; gap: 24px; padding: 28px 30px; margin-bottom: 22px; overflow: hidden;
        background: linear-gradient(120deg, rgba(62,207,142,.18), rgba(212,175,55,.08) 55%, rgba(255,255,255,.02)); border-color: rgba(62,207,142,.35); animation: fadeUp .6s ease both; }
    .ud-hero::after { content: ''; position: absolute; top: 0; left: -60%; width: 40%; height: 100%; transform: skewX(-20deg); background: linear-gradient(90deg, transparent, rgba(255,255,255,.1), transparent); animation: udSweep 6s ease-in-out infinite 1s; pointer-events: none; }
    @keyframes udSweep { 0% { left: -60%; } 40%, 100% { left: 140%; } }
    .ud-hero > * { position: relative; z-index: 1; }
    .ud-avatar { width: 92px; height: 92px; flex-shrink: 0; border-radius: 24px; overflow: hidden; display: grid; place-items: center; font-size: 36px; font-weight: 700; color: #062b1a;
        background: linear-gradient(135deg, #7ff0b8, #2fbf7d); box-shadow: 0 0 0 6px rgba(62,207,142,.15), 0 14px 34px rgba(62,207,142,.35); }
    .ud-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .ud-info { flex: 1; min-width: 0; }
    .ud-info small { color: #9a9aa2; }
    .ud-info h2 { font-family: var(--serif); font-size: 32px; color: #fff; line-height: 1.1; margin: 2px 0 10px; overflow-wrap: anywhere; }
    .ud-tags { display: flex; flex-wrap: wrap; gap: 8px 16px; align-items: center; font-size: 13px; color: #bbb; }
    .ud-tags i { color: var(--gold); margin-right: 4px; }
    .ud-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: #062b1a; background: linear-gradient(135deg, #7ff0b8, #2fbf7d); }
    .ud-badge i { color: #062b1a !important; margin: 0 !important; }

    .ud-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 22px; }
    .ud-stat { display: flex; align-items: center; gap: 14px; padding: 18px; animation: fadeUp .6s ease both; }
    .ud-stat > * { position: relative; z-index: 1; }
    .ud-stat > i { width: 46px; height: 46px; flex-shrink: 0; border-radius: 13px; display: grid; place-items: center; font-size: 18px; color: var(--green); background: rgba(62,207,142,.12); }
    .ud-stat b { display: block; font-size: 17px; color: #fff; line-height: 1.2; overflow-wrap: anywhere; }
    .ud-stat small { color: var(--muted); font-size: 12.5px; }

    .ud-h { margin: 16px 0 10px; font-size: 11.5px; letter-spacing: 2px; text-transform: uppercase; color: var(--gold); position: relative; z-index: 1; }
    .ud-h:first-child { margin-top: 0; }
    .ud-grid-info { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; position: relative; z-index: 1; }
    .ud-grid-info div { padding: 10px 12px; border-radius: 12px; background: rgba(255,255,255,.03); border: 1px solid var(--border); min-width: 0; }
    .ud-grid-info small { display: block; font-size: 11.5px; color: var(--muted); }
    .ud-grid-info b { display: block; font-size: 13.5px; color: #fff; font-weight: 500; overflow-wrap: anywhere; }

    .ud-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; position: relative; z-index: 1; }
    .ud-actions a { display: flex; flex-direction: column; align-items: flex-start; gap: 10px; padding: 18px; border-radius: 16px; border: 1px solid var(--border); background: rgba(255,255,255,.02); color: #ddd; font-weight: 500; font-size: 13.5px; transition: .35s; }
    .ud-actions a i { width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; font-size: 17px; color: #111; background: var(--gold-grad); transition: .4s; }
    .ud-actions a:hover { border-color: rgba(212,175,55,.45); transform: translateY(-4px); color: #fff; }
    .ud-actions a:hover i { transform: rotate(-10deg) scale(1.1); }

    .ud-hist li { display: flex; align-items: center; gap: 12px; padding: 11px 0; border-bottom: 1px solid rgba(255,255,255,.04); position: relative; z-index: 1; }
    .ud-hist li:last-child { border-bottom: 0; }
    .ud-hist li > div { flex: 1; min-width: 0; }
    .ud-hist b { display: block; color: #fff; font-size: 13.5px; font-weight: 500; }
    .ud-hist small { color: var(--muted); font-size: 12px; }
    .ud-hist .ic { width: 38px; height: 38px; flex-shrink: 0; border-radius: 11px; display: grid; place-items: center; color: var(--green); background: rgba(62,207,142,.1); }
    .ud-empty { color: var(--muted); font-size: 13px; padding: 14px 0; text-align: center; }
    .ud-sk { display: block; height: 11px; border-radius: 6px; margin: 6px 0; background: linear-gradient(90deg, rgba(255,255,255,.04) 25%, rgba(255,255,255,.1) 50%, rgba(255,255,255,.04) 75%); background-size: 200% 100%; animation: udSk 1.3s linear infinite; }
    @keyframes udSk { to { background-position: -200% 0; } }

    @media (max-width: 1100px) { .ud-stats { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 575px) {
        .ud-hero { flex-direction: column; text-align: center; padding: 24px 18px; }
        .ud-tags { justify-content: center; }
        .ud-grid-info { grid-template-columns: 1fr; }
        .ud-stats { gap: 10px; }
        .ud-stat { flex-direction: column; align-items: flex-start; padding: 14px; }
    }
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="ud-hero glass-card">
    <div class="ud-avatar" id="udAvatar">U</div>
    <div class="ud-info">
        <small>Welcome back,</small>
        <h2 id="udName">…</h2>
        <div class="ud-tags">
            <span class="ud-badge"><i class="fa-solid fa-user"></i> User</span>
            <span><i class="fa-regular fa-envelope"></i> <span id="udEmail">—</span></span>
            <span><i class="fa-solid fa-mobile-screen"></i> <span id="udPhone">—</span></span>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="ud-stats">
    <div class="glass-card ud-stat"><i class="fa-solid fa-id-badge"></i><div><b id="udRole">User</b><small>Account Role</small></div></div>
    <div class="glass-card ud-stat"><i class="fa-solid fa-circle-check"></i><div><b id="udStatus">Active</b><small>Status</small></div></div>
    <div class="glass-card ud-stat"><i class="fa-regular fa-calendar"></i><div><b id="udSince">—</b><small>Member Since</small></div></div>
    <div class="glass-card ud-stat"><i class="fa-solid fa-right-to-bracket"></i><div><b id="udLast">—</b><small>Last Login</small></div></div>
</div>

<div class="grid-2-1">
    {{-- Profile --}}
    <div class="glass-card card" id="profile">
        <div class="card-head"><div><h4>My Profile</h4><small>Details you registered with</small></div></div>
        <div id="udProfile">
            <span class="ud-sk" style="width:40%"></span><span class="ud-sk"></span><span class="ud-sk" style="width:70%"></span>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="glass-card card">
        <div class="card-head"><div><h4>Quick Actions</h4></div></div>
        <div class="ud-actions">
            <a href="{{ route('contact') }}"><i class="fa-solid fa-calendar-check"></i> Book Consultation</a>
            <a href="{{ route('practice.index') }}"><i class="fa-solid fa-scale-balanced"></i> Practice Areas</a>
            <a href="tel:+919999999999" id="udCallAdv"><i class="fa-solid fa-phone"></i> Call an Advocate</a>
            <a href="https://wa.me/919999999999" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> WhatsApp Us</a>
        </div>
    </div>
</div>

{{-- Login history --}}
<div class="glass-card card" id="history">
    <div class="card-head"><div><h4>Recent Login Activity</h4><small>Your last 10 sessions</small></div></div>
    <ul class="ud-hist" id="udHistory"><li><span class="ud-sk"></span></li></ul>
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';
    const $ = id => document.getElementById(id);
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const cap = s => s ? s.charAt(0).toUpperCase() + s.slice(1) : '';

    // admin.js /api/auth/me se token verify karke 'auth:ready' bhejta hai
    document.addEventListener('auth:ready', e => {
        const u = e.detail;
        if (u.role !== 'user') return;
        const p = u.user_profile || {};

        $('udName').textContent = u.name;
        $('udEmail').textContent = u.email;
        $('udPhone').textContent = u.phone || '—';
        $('udRole').textContent = u.role_label || 'User';
        $('udStatus').textContent = cap(u.status || 'active');
        $('udSince').textContent = u.created_at || '—';
        $('udLast').textContent = u.last_login_at || 'Just now';
        $('udAvatar').innerHTML = u.photo_url ? '<img src="' + esc(u.photo_url) + '" alt="">' : esc((u.name || 'U').charAt(0).toUpperCase());

        const item = (k, v) => '<div><small>' + k + '</small><b>' + esc(v || '—') + '</b></div>';
        $('udProfile').innerHTML =
            '<h6 class="ud-h">Personal</h6><div class="ud-grid-info">' + item('Full Name', u.name) + item('Gender', p.gender) + item('Date of Birth', p.dob) + item('Occupation', p.occupation) + '</div>' +
            '<h6 class="ud-h">Contact</h6><div class="ud-grid-info">' + item('Email', u.email) + item('Mobile', u.phone) + item('Alternate', p.alt_phone) + item('Need Help With', p.interested_in) + '</div>' +
            (u.creator ? '<h6 class="ud-h">My Advocate</h6><div class="ud-grid-info">' + item('Advocate', u.creator.name) + item('Email', u.creator.email) + item('Mobile', u.creator.phone) + '</div>' : '') +
            '<h6 class="ud-h">Address</h6><div class="ud-grid-info">' + item('Address', p.address) + item('City / State', [p.city, p.state].filter(Boolean).join(', ')) + item('Pincode', p.pincode) + item('Heard From', p.heard_from) + '</div>';

        // apne advocate ko call
        if (u.creator && u.creator.phone) { $('udCallAdv').href = 'tel:+91' + u.creator.phone; $('udCallAdv').innerHTML = '<i class="fa-solid fa-phone"></i> Call ' + esc(u.creator.name); }

        API.get('/auth/login-history').then(r => {
            $('udHistory').innerHTML = r.data.length ? r.data.map(l =>
                '<li><span class="ic"><i class="fa-solid ' + (l.device === 'Mobile' ? 'fa-mobile-screen' : 'fa-desktop') + '"></i></span>' +
                '<div><b>' + esc(l.browser || 'Browser') + ' on ' + esc(l.device || 'Device') + '</b><small>' + esc(l.logged_in_at) + ' · IP ' + esc(l.ip_address || '—') + '</small></div>' +
                '<span class="status ' + ({ success: 'green', failed: 'red', blocked: 'gold' }[l.status] || 'grey') + '">' + esc(l.logged_out_at ? 'logged out' : l.status) + '</span></li>'
            ).join('') : '<li class="ud-empty">No login activity yet.</li>';
        }).catch(err => { $('udHistory').innerHTML = '<li class="ud-empty">' + esc(err.message) + '</li>'; });
    });
})();
</script>
@endpush