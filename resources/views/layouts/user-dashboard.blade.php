@extends('layouts.app')
@section('title', 'My Dashboard')

@push('head')
    {{-- Token guard: login nahi hai to login page --}}
    <script>
        (function () {
            var t = null;
            try { t = localStorage.getItem('lx_token') || sessionStorage.getItem('lx_token'); } catch (e) {}
            if (!t) { location.replace(@json(route('login')) + '?next=' + encodeURIComponent(location.pathname)); return; }
            document.documentElement.classList.add('ud-checking');
        })();
    </script>
@endpush


@push('styles')
<style>
/* ===== USER DASHBOARD CSS (isi file me) ===== */
.role-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 16px; border-radius: 50px; font-size: 13px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; }
.role-badge.sm { padding: 3px 10px; font-size: 11px; }
.role-badge.gold  { color: #111; background: var(--gold-grad); box-shadow: 0 6px 18px rgba(212,175,55,.35); }
.role-badge.blue  { color: #fff; background: linear-gradient(135deg, #7aa5ff, #3f6fd8); box-shadow: 0 6px 18px rgba(91,141,239,.35); }
.role-badge.green { color: #062b1a; background: linear-gradient(135deg, #7ff0b8, #2fbf7d); box-shadow: 0 6px 18px rgba(62,207,142,.3); }
.role-badge.grey  { color: #ddd; background: rgba(255,255,255,.1); }

/* =========================================================
   USER DASHBOARD (/dashboard)
   ========================================================= */
html.ud-checking #userDashboard .container { visibility: hidden; }
html.ud-checking #userDashboard::after { content: ''; position: absolute; left: 50%; top: 160px; width: 46px; height: 46px; margin-left: -23px; border-radius: 50%;
    border: 3px solid rgba(212,175,55,.2); border-top-color: var(--gold); animation: spin .8s linear infinite; }
.ud-wrap { position: relative; min-height: calc(100vh - var(--header-h)); padding: 60px 0 90px; overflow: hidden; }
.ud-bg { position: absolute; inset: 0; background: radial-gradient(ellipse 60% 50% at 80% 0%, rgba(212,175,55,.14), transparent 60%), radial-gradient(ellipse 50% 40% at 0% 100%, rgba(62,207,142,.06), transparent 60%); }
.ud-wrap .container { position: relative; z-index: 1; }

.ud-hero { position: relative; display: flex; align-items: center; gap: 26px; padding: 34px 36px; border-radius: 28px; overflow: hidden; margin-bottom: 24px;
    background: linear-gradient(130deg, rgba(212,175,55,.2), rgba(255,255,255,.03) 55%, rgba(62,207,142,.06));
    border: 1px solid rgba(212,175,55,.3); box-shadow: 0 30px 80px rgba(0,0,0,.6), inset 0 1px 0 rgba(255,255,255,.12); backdrop-filter: blur(14px); }
.ud-hero > *:not(.auth-card-shine) { position: relative; z-index: 1; }
.ud-avatar { width: 100px; height: 100px; flex-shrink: 0; border-radius: 28px; overflow: hidden; display: grid; place-items: center; font-size: 40px; color: #111; background: var(--gold-grad);
    box-shadow: 0 0 0 6px rgba(212,175,55,.15), 0 16px 40px rgba(212,175,55,.35); animation: float 5s ease-in-out infinite; }
.ud-avatar img { width: 100%; height: 100%; object-fit: cover; }
.ud-avatar b { font-family: var(--serif); font-weight: 700; }
.ud-info { flex: 1; min-width: 0; }
.ud-hello { color: var(--muted); font-size: 14px; }
.ud-info h1 { font-family: var(--serif); font-size: clamp(30px, 4vw, 46px); line-height: 1.1; color: #fff; margin: 2px 0 12px; overflow-wrap: anywhere; }
.ud-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 12px 22px; color: var(--muted); font-size: 14px; }
.ud-meta i { color: var(--gold); margin-right: 4px; }
.ud-meta b { color: #ddd; font-weight: 500; }
.ud-logout { flex-shrink: 0; }

.ud-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 24px; }
.ud-stat { display: flex; align-items: center; gap: 14px; padding: 20px; border-radius: 20px; background: linear-gradient(150deg, rgba(255,255,255,.06), rgba(255,255,255,.015)); border: 1px solid var(--border); transition: .35s; }
.ud-stat:hover { transform: translateY(-6px); border-color: rgba(212,175,55,.4); }
.ud-stat > i { width: 48px; height: 48px; flex-shrink: 0; border-radius: 14px; display: grid; place-items: center; font-size: 19px; color: var(--gold); background: rgba(212,175,55,.1); }
.ud-stat small { display: block; color: var(--muted); font-size: 12.5px; }
.ud-stat b { color: #fff; font-size: 16px; font-weight: 600; }

.ud-grid { display: grid; grid-template-columns: 1fr 1.2fr; gap: 24px; }
.ud-card { padding: 28px; border-radius: 24px; background: linear-gradient(150deg, rgba(255,255,255,.06), rgba(255,255,255,.015)); border: 1px solid var(--border); box-shadow: 0 20px 60px rgba(0,0,0,.5); }
.ud-card h3 { font-size: 18px; color: #fff; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
.ud-card h3 i { color: var(--gold); }
.ud-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.ud-actions a { display: flex; flex-direction: column; align-items: flex-start; gap: 12px; padding: 20px; border-radius: 18px; border: 1px solid var(--border); background: rgba(255,255,255,.02); color: #ddd; font-weight: 500; transition: .35s; }
.ud-actions a i { width: 46px; height: 46px; border-radius: 14px; display: grid; place-items: center; font-size: 19px; color: #111; background: var(--gold-grad); transition: .4s; }
.ud-actions a:hover { border-color: rgba(212,175,55,.45); transform: translateY(-4px); color: #fff; background: rgba(212,175,55,.06); }
.ud-actions a:hover i { transform: rotate(-10deg) scale(1.1); }

.ud-history li { display: flex; align-items: center; gap: 14px; padding: 13px 0; border-bottom: 1px solid rgba(255,255,255,.05); animation: rrUp .5s ease both; }
.ud-history li:last-child { border-bottom: 0; }
.ud-history li > div { flex: 1; min-width: 0; }
.ud-history b { display: block; color: #fff; font-size: 14px; font-weight: 500; }
.ud-history small { color: var(--muted); font-size: 12.5px; }
.uh-icon { width: 42px; height: 42px; flex-shrink: 0; border-radius: 12px; display: grid; place-items: center; color: var(--gold); background: rgba(212,175,55,.1); }
.uh-icon.failed, .uh-icon.blocked { color: #ff6b6b; background: rgba(255,107,107,.1); }
.uh-status { padding: 4px 10px; border-radius: 20px; font-size: 11.5px; text-transform: capitalize; color: #7fe0a6; background: rgba(62,207,142,.1); white-space: nowrap; }
.uh-status.failed, .uh-status.blocked { color: #ff8c8c; background: rgba(255,107,107,.1); }
.ud-empty { color: var(--muted); justify-content: center; }
.ud-loading { flex-direction: column; align-items: stretch !important; gap: 10px !important; border: 0 !important; }
.ud-loading span { height: 46px; border-radius: 12px; background: linear-gradient(90deg, rgba(255,255,255,.04) 25%, rgba(255,255,255,.09) 50%, rgba(255,255,255,.04) 75%); background-size: 200% 100%; animation: udShimmer 1.3s linear infinite; }
@keyframes udShimmer { to { background-position: -200% 0; } }
@keyframes rrUp { from { opacity: 0; transform: translateY(14px); } }
.ud-hero .auth-card-shine { position: absolute; top: 0; left: -60%; width: 40%; height: 100%; pointer-events: none; transform: skewX(-20deg); background: linear-gradient(90deg, transparent, rgba(255,255,255,.12), transparent); animation: udSweep 6s ease-in-out infinite 1s; }
@keyframes udSweep { 0% { left: -60%; } 35%, 100% { left: 130%; } }
.ud-history { list-style: none; padding: 0; margin: 0; }

@media (max-width: 991px) {
    .ud-stats { grid-template-columns: 1fr 1fr; }
    .ud-grid { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
    .auth-two { grid-template-columns: 1fr; }
    .ud-wrap { padding: 36px 0 70px; }
    .ud-hero { flex-direction: column; text-align: center; padding: 30px 20px; }
    .ud-meta { justify-content: center; }
    .ud-logout { width: 100%; }
    .ud-stats { gap: 12px; }
    .ud-stat { flex-direction: column; align-items: flex-start; padding: 16px; }
    .ud-card { padding: 22px 18px; }
    .ud-actions a { padding: 16px; }
    .rr-box { padding: 38px 22px 26px; }
    .rr-box h3 { font-size: 27px; }
}
</style>
@endpush

@section('content')

<section class="ud-wrap" id="userDashboard" data-inline="1">
    <div class="ud-bg"></div>
    <div class="auth-orb-1"></div>

    <div class="container">

        {{-- Profile hero --}}
        <div class="ud-hero" data-aos="fade-up">
            <span class="auth-card-shine"></span>
            <div class="ud-avatar" data-u="avatar"><i class="fa-solid fa-user"></i></div>
            <div class="ud-info">
                <small class="ud-hello">Welcome back,</small>
                <h1 data-u="name">…</h1>
                <div class="ud-meta">
                    <span class="role-badge green" data-u="role">User</span>
                    <span><i class="fa-regular fa-envelope"></i> <b data-u="email">—</b></span>
                    <span><i class="fa-solid fa-mobile-screen"></i> <b data-u="phone">—</b></span>
                </div>
            </div>
            <button class="btn btn-outline ud-logout" data-site-logout><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
        </div>

        {{-- Stats --}}
        <div class="ud-stats">
            <div class="ud-stat" data-aos="fade-up"><i class="fa-solid fa-id-badge"></i><div><small>Account Role</small><b data-u="role_label">User</b></div></div>
            <div class="ud-stat" data-aos="fade-up" data-aos-delay="100"><i class="fa-solid fa-circle-check"></i><div><small>Status</small><b data-u="status">Active</b></div></div>
            <div class="ud-stat" data-aos="fade-up" data-aos-delay="200"><i class="fa-regular fa-calendar"></i><div><small>Member Since</small><b data-u="created_at">—</b></div></div>
            <div class="ud-stat" data-aos="fade-up" data-aos-delay="300"><i class="fa-solid fa-right-to-bracket"></i><div><small>Last Login</small><b data-u="last_login_at">—</b></div></div>
        </div>

        <div class="ud-grid">
            {{-- Quick actions --}}
            <div class="ud-card" data-aos="fade-right">
                <h3><i class="fa-solid fa-bolt"></i> Quick Actions</h3>
                <div class="ud-actions">
                    <a href="{{ route('contact') }}"><i class="fa-solid fa-calendar-check"></i><span>Book Consultation</span></a>
                    <a href="{{ route('practice.index') }}"><i class="fa-solid fa-scale-balanced"></i><span>Practice Areas</span></a>
                    <a href="tel:+919999999999"><i class="fa-solid fa-phone"></i><span>Call an Advocate</span></a>
                    <a href="https://wa.me/919999999999" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i><span>WhatsApp Us</span></a>
                </div>
            </div>

            {{-- Login history (API) --}}
            <div class="ud-card" data-aos="fade-left">
                <h3><i class="fa-solid fa-clock-rotate-left"></i> Recent Login Activity</h3>
                <ul class="ud-history" id="loginHistory">
                    <li class="ud-loading"><span></span><span></span><span></span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
/* ===== USER DASHBOARD – API + JS isi file me (GET /api/auth/me, /api/auth/login-history) ===== */
(function () {
    'use strict';
    const API_BASE = @json(url('/api'));
    const LOGIN    = @json(route('login'));
    const HOME     = { superadmin: @json(route('superadmin.dashboard')), admin: @json(route('admin.dashboard')), user: @json(route('user.dashboard')) };
    const KEY = 'lx_token', UKEY = 'lx_user';
    const token = () => { try { return localStorage.getItem(KEY) || sessionStorage.getItem(KEY); } catch (e) { return null; } };
    const clear = () => { try { [localStorage, sessionStorage].forEach(s => { s.removeItem(KEY); s.removeItem(UKEY); }); } catch (e) {} };
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    async function api(method, path) {
        const res = await fetch(API_BASE + path, { method, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Authorization': 'Bearer ' + token() } });
        let json = {}; try { json = await res.json(); } catch (e) {}
        if (!res.ok) throw { status: res.status, message: json.message || ('Error ' + res.status) };
        return json;
    }

    const ud = document.getElementById('userDashboard');
    const done = () => document.documentElement.classList.remove('ud-checking');

    api('GET', '/auth/me').then(r => {
        const u = r.user;
        if (u.role !== 'user') { location.replace(HOME[u.role] || LOGIN); return; }   // superadmin/admin -> apna panel

        const vals = {
            name: u.name, email: u.email, phone: u.phone || '—', role: u.role_label, role_label: u.role_label,
            status: (u.status || '').charAt(0).toUpperCase() + (u.status || '').slice(1),
            created_at: u.created_at || '—', last_login_at: u.last_login_at || 'Just now',
        };
        ud.querySelectorAll('[data-u]').forEach(el => {
            if (el.dataset.u === 'avatar') el.innerHTML = u.photo_url ? '<img src="' + esc(u.photo_url) + '" alt="">' : '<b>' + esc((u.name || '?').charAt(0).toUpperCase()) + '</b>';
            else if (vals[el.dataset.u] !== undefined) el.textContent = vals[el.dataset.u];
        });
        done();

        const list = document.getElementById('loginHistory');
        api('GET', '/auth/login-history').then(h => {
            list.innerHTML = h.data.length ? h.data.map(l =>
                '<li><span class="uh-icon ' + esc(l.status) + '"><i class="fa-solid ' + (l.device === 'Mobile' ? 'fa-mobile-screen' : 'fa-desktop') + '"></i></span>' +
                '<div><b>' + esc(l.browser || 'Browser') + ' on ' + esc(l.device || 'Device') + '</b><small>' + esc(l.logged_in_at) + ' · IP ' + esc(l.ip_address || '—') + '</small></div>' +
                '<span class="uh-status ' + esc(l.status) + '">' + (l.logged_out_at ? 'Logged out' : esc(l.status)) + '</span></li>'
            ).join('') : '<li class="ud-empty">No login activity yet.</li>';
        }).catch(err => { list.innerHTML = '<li class="ud-empty">' + esc(err.message) + '</li>'; });
    }).catch(err => {
        if (err.status === 401 || err.status === 403) { clear(); location.replace(LOGIN + '?next=' + encodeURIComponent(location.pathname)); return; }
        done();
        document.getElementById('loginHistory').innerHTML = '<li class="ud-empty">Could not load your account: ' + esc(err.message) + '</li>';
    });

    // Logout
    document.addEventListener('click', e => {
        const b = e.target.closest('[data-site-logout]');
        if (!b) return;
        e.preventDefault();
        b.style.opacity = '.6';
        const go = () => { clear(); location.replace(LOGIN); };
        fetch(API_BASE + '/auth/logout', { method: 'POST', headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token() } }).then(go, go);
    });
})();
</script>
@endpush