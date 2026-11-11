@extends('frontlayout.app')
@section('title', 'My Dashboard')
@section('page_title', 'My Dashboard')
@section('require_role', 'admin')

{{-- =====================================================================
     ADMIN (ADVOCATE) DASHBOARD  –  resources/views/admin/dashboard.blade.php   (/admin)
     Layout: frontlayout.app (same sidebar + topbar as superadmin, role wise menu)
     APIs  : GET /api/auth/me · GET /api/auth/login-history   (Bearer token)
     ===================================================================== --}}

@section('page_actions')
    <a href="{{ route('superadmin.cases') }}" class="adm-btn adm-btn-ghost"><i class="fa-solid fa-gavel"></i> <span class="hide-xs">My Cases</span></a>
    <button class="adm-btn adm-btn-gold" data-modal="caseModal"><i class="fa-solid fa-plus"></i> New Case</button>
@endsection

@push('styles')
<style>
    .ad-hero { position: relative; display: flex; align-items: center; gap: 24px; padding: 28px 30px; margin-bottom: 22px; overflow: hidden;
        background: linear-gradient(120deg, rgba(91,141,239,.22), rgba(212,175,55,.08) 55%, rgba(255,255,255,.02)); border-color: rgba(91,141,239,.35); animation: fadeUp .6s ease both; }
    .ad-hero::after { content: ''; position: absolute; top: 0; left: -60%; width: 40%; height: 100%; transform: skewX(-20deg); background: linear-gradient(90deg, transparent, rgba(255,255,255,.1), transparent); animation: adSweep 6s ease-in-out infinite 1s; pointer-events: none; }
    @keyframes adSweep { 0% { left: -60%; } 40%, 100% { left: 140%; } }
    .ad-hero > * { position: relative; z-index: 1; }
    .ad-avatar { width: 92px; height: 92px; flex-shrink: 0; border-radius: 24px; overflow: hidden; display: grid; place-items: center; font-size: 36px; font-weight: 700; color: #fff;
        background: linear-gradient(135deg, #7aa5ff, #3f6fd8); box-shadow: 0 0 0 6px rgba(91,141,239,.15), 0 14px 34px rgba(91,141,239,.4); }
    .ad-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .ad-info { flex: 1; min-width: 0; }
    .ad-info small { color: #9a9aa2; }
    .ad-info h2 { font-family: var(--serif); font-size: 32px; color: #fff; line-height: 1.1; margin: 2px 0 10px; overflow-wrap: anywhere; }
    .ad-tags { display: flex; flex-wrap: wrap; gap: 8px 16px; align-items: center; font-size: 13px; color: #bbb; }
    .ad-tags i { color: var(--gold); margin-right: 4px; }
    .ad-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: #fff; background: linear-gradient(135deg, #7aa5ff, #3f6fd8); }
    .ad-badge i { color: #fff !important; margin: 0 !important; }

    .ad-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 22px; }
    .ad-stat { display: flex; align-items: center; gap: 14px; padding: 18px; animation: fadeUp .6s ease both; }
    .ad-stat > * { position: relative; z-index: 1; }
    .ad-stat > i { width: 46px; height: 46px; flex-shrink: 0; border-radius: 13px; display: grid; place-items: center; font-size: 18px; color: var(--gold-light); background: rgba(212,175,55,.12); }
    .ad-stat b { display: block; font-size: 18px; color: #fff; line-height: 1.2; }
    .ad-stat small { color: var(--muted); font-size: 12.5px; }

    .ad-h { margin: 16px 0 10px; font-size: 11.5px; letter-spacing: 2px; text-transform: uppercase; color: var(--gold); position: relative; z-index: 1; }
    .ad-h:first-child { margin-top: 0; }
    .ad-grid-info { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; position: relative; z-index: 1; }
    .ad-grid-info div { padding: 10px 12px; border-radius: 12px; background: rgba(255,255,255,.03); border: 1px solid var(--border); min-width: 0; }
    .ad-grid-info small { display: block; font-size: 11.5px; color: var(--muted); }
    .ad-grid-info b { display: block; font-size: 13.5px; color: #fff; font-weight: 500; overflow-wrap: anywhere; }
    .ad-chips { display: flex; flex-wrap: wrap; gap: 6px; position: relative; z-index: 1; }
    .ad-quick { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; position: relative; z-index: 1; }
    .ad-quick a { display: flex; align-items: center; gap: 10px; padding: 14px; border-radius: 14px; border: 1px solid var(--border); background: rgba(255,255,255,.02); font-size: 13.5px; color: #ddd; transition: .3s; }
    .ad-quick a i { width: 34px; height: 34px; border-radius: 10px; display: grid; place-items: center; color: #fff; background: linear-gradient(135deg, #7aa5ff, #3f6fd8); flex-shrink: 0; }
    .ad-quick a:hover { border-color: rgba(91,141,239,.45); transform: translateY(-3px); color: #fff; }
    .ad-hist li { display: flex; align-items: center; gap: 12px; padding: 11px 0; border-bottom: 1px solid rgba(255,255,255,.04); position: relative; z-index: 1; }
    .ad-hist li:last-child { border-bottom: 0; }
    .ad-hist li > div { flex: 1; min-width: 0; }
    .ad-hist b { display: block; color: #fff; font-size: 13.5px; font-weight: 500; }
    .ad-hist small { color: var(--muted); font-size: 12px; }
    .ad-hist .ic { width: 38px; height: 38px; flex-shrink: 0; border-radius: 11px; display: grid; place-items: center; color: var(--gold); background: rgba(212,175,55,.1); }
    .ad-empty { color: var(--muted); font-size: 13px; padding: 14px 0; text-align: center; }
    .ad-sk { display: block; height: 11px; border-radius: 6px; margin: 6px 0; background: linear-gradient(90deg, rgba(255,255,255,.04) 25%, rgba(255,255,255,.1) 50%, rgba(255,255,255,.04) 75%); background-size: 200% 100%; animation: adSk 1.3s linear infinite; }
    @keyframes adSk { to { background-position: -200% 0; } }

    @media (max-width: 1100px) { .ad-stats { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 575px) {
        .ad-hero { flex-direction: column; text-align: center; padding: 24px 18px; }
        .ad-tags { justify-content: center; }
        .ad-grid-info, .ad-quick { grid-template-columns: 1fr; }
        .ad-stats { gap: 10px; }
        .ad-stat { flex-direction: column; align-items: flex-start; padding: 14px; }
    }
</style>
@endpush

@section('content')

{{-- Hero --}}
<div class="ad-hero glass-card">
    <div class="ad-avatar" id="adAvatar">A</div>
    <div class="ad-info">
        <small>Welcome back,</small>
        <h2 id="adName">…</h2>
        <div class="ad-tags">
            <span class="ad-badge"><i class="fa-solid fa-user-tie"></i> Admin</span>
            <span><i class="fa-regular fa-envelope"></i> <span id="adEmail">—</span></span>
            <span><i class="fa-solid fa-mobile-screen"></i> <span id="adEnroll">—</span></span>
            <span class="status green" id="adStatus">active</span>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="ad-stats">
    <div class="glass-card ad-stat"><i class="fa-solid fa-location-dot"></i><div><b id="adExp">—</b><small>City</small></div></div>
    <div class="glass-card ad-stat"><i class="fa-solid fa-scale-balanced"></i><div><b id="adAreas">—</b><small>Practice Area</small></div></div>
    <div class="glass-card ad-stat"><i class="fa-regular fa-calendar"></i><div><b id="adSince">—</b><small>Member Since</small></div></div>
    <div class="glass-card ad-stat"><i class="fa-solid fa-right-to-bracket"></i><div><b id="adLast">—</b><small>Last Login</small></div></div>
</div>

<div class="grid-2-1">
    {{-- Profile --}}
    <div class="glass-card card" id="profile">
        <div class="card-head"><div><h4>My Profile</h4><small>As registered with the firm</small></div></div>
        <div id="adProfile">
            <span class="ad-sk" style="width:40%"></span><span class="ad-sk"></span><span class="ad-sk" style="width:70%"></span>
        </div>
    </div>

    {{-- Quick links --}}
    <div class="glass-card card">
        <div class="card-head"><div><h4>Quick Links</h4></div></div>
        <div class="ad-quick">
            <a href="{{ route('superadmin.cases') }}"><i class="fa-solid fa-gavel"></i> Cases</a>
            <a href="{{ route('superadmin.clients') }}"><i class="fa-solid fa-users"></i> Clients</a>
            <a href="{{ route('superadmin.enquiries') }}"><i class="fa-solid fa-envelope-open-text"></i> Enquiries</a>
            <a href="{{ route('home') }}" target="_blank"><i class="fa-solid fa-globe"></i> Website</a>
        </div>
    </div>
</div>

{{-- Login history --}}
<div class="glass-card card" id="history">
    <div class="card-head"><div><h4>Recent Logins</h4><small>Your last 10 sessions</small></div></div>
    <ul class="ad-hist" id="adHistory"><li><span class="ad-sk"></span></li></ul>
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';
    const $ = id => document.getElementById(id);
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    // admin.js /api/auth/me se token verify karke 'auth:ready' bhejta hai
    document.addEventListener('auth:ready', e => {
        const u = e.detail;
        if (u.role !== 'admin') return;
        const p  = u.profile || {};          // superadmin ne create kiya (advocate profile)
        const up = u.user_profile || {};     // website signup se bana admin
        const pick = (a, b) => (a !== undefined && a !== null && a !== '') ? a : b;

        $('adName').textContent = u.name;
        $('adEmail').textContent = u.email;
        $('adEnroll').textContent = u.phone || '—';
        $('adStatus').textContent = u.status || 'active';
        $('adStatus').className = 'status ' + ({ active: 'green', pending: 'gold', inactive: 'grey' }[u.status] || 'grey');
        $('adAvatar').innerHTML = u.photo_url ? '<img src="' + esc(u.photo_url) + '" alt="">' : esc((u.name || 'A').replace(/^adv\.?\s+/i, '').charAt(0).toUpperCase());
        $('adExp').textContent = pick(up.city, p.city) || '—';
        $('adAreas').textContent = pick(up.interested_in, (p.specializations || [])[0]) || '—';
        $('adSince').textContent = u.created_at || '—';
        $('adLast').textContent = u.last_login_at || 'Just now';

        const item = (k, v) => '<div><small>' + k + '</small><b>' + esc(v || '—') + '</b></div>';
        $('adProfile').innerHTML =
            '<h6 class="ad-h">Personal</h6><div class="ad-grid-info">' + item('Full Name', u.name) + item('Gender', pick(up.gender, p.gender)) + item('Date of Birth', pick(up.dob, p.dob)) + item('Occupation', up.occupation) + '</div>' +
            '<h6 class="ad-h">Contact</h6><div class="ad-grid-info">' + item('Email (login)', u.email) + item('Mobile', u.phone) + item('Alternate', pick(up.alt_phone, p.alt_phone)) + item('Practice Area', pick(up.interested_in, (p.specializations || []).join(', '))) + '</div>' +
            '<h6 class="ad-h">Address</h6><div class="ad-grid-info">' + item('Address', pick(up.address, p.office_address)) + item('City / State', [pick(up.city, p.city), pick(up.state, p.state)].filter(Boolean).join(', ')) + item('Pincode', pick(up.pincode, p.pincode)) + item('Heard From', up.heard_from) + '</div>';

        API.get('/auth/login-history').then(r => {
            $('adHistory').innerHTML = r.data.length ? r.data.map(l =>
                '<li><span class="ic"><i class="fa-solid ' + (l.device === 'Mobile' ? 'fa-mobile-screen' : 'fa-desktop') + '"></i></span>' +
                '<div><b>' + esc(l.browser || 'Browser') + ' · ' + esc(l.device || 'Device') + '</b><small>' + esc(l.logged_in_at) + ' · IP ' + esc(l.ip_address || '—') + '</small></div>' +
                '<span class="status ' + ({ success: 'green', failed: 'red', blocked: 'gold' }[l.status] || 'grey') + '">' + esc(l.logged_out_at ? 'logged out' : l.status) + '</span></li>'
            ).join('') : '<li class="ad-empty">No login activity yet.</li>';
        }).catch(err => { $('adHistory').innerHTML = '<li class="ad-empty">' + esc(err.message) + '</li>'; });
    });
})();
</script>
@endpush