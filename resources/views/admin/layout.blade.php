<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | Legal Aid  Advocate Panel</title>

    {{-- ================= TOKEN GUARD (page dikhne se pehle) ================= --}}
    <script>
        (function () {
            var t = null;
            try { t = localStorage.getItem('lx_token') || sessionStorage.getItem('lx_token'); } catch (e) {}
            if (!t) { location.replace(@json(route('login')) + '?next=' + encodeURIComponent(location.pathname)); return; }
            document.documentElement.classList.add('lx-checking');
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    {{-- ================= LAYOUT CSS ================= --}}
    <style>
        :root {
            --bg: #070708; --panel: #111115; --border: rgba(255,255,255,.07); --border-2: rgba(255,255,255,.12);
            --text: #e6e6ea; --muted: #8d8d97;
            --accent: #5b8def; --gold: #d4af37; --gold-light: #f5d77a; --gold-grad: linear-gradient(135deg, #f8e08e 0%, #d4af37 45%, #a8841f 100%);
            --green: #3ecf8e; --blue: #5b8def; --purple: #b07cff; --red: #ff6b6b;
            --sb-w: 268px; --tb-h: 70px; --radius: 18px; --shadow: 0 20px 50px rgba(0,0,0,.55);
            --serif: 'Cormorant Garamond', serif; --sans: 'Poppins', sans-serif; --ease: cubic-bezier(.2,.8,.2,1);
        }
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: var(--sans); font-size: 14.5px; line-height: 1.6; color: var(--text); overflow-x: hidden;
            background: radial-gradient(ellipse 60% 50% at 100% 0%, rgba(212,175,55,.08), transparent 60%), radial-gradient(ellipse 50% 40% at 0% 100%, rgba(91,141,239,.05), transparent 60%), var(--bg); background-attachment: fixed; }
        body.lock { overflow: hidden; }
        a { color: inherit; text-decoration: none; transition: .25s; }
        ul { list-style: none; }
        img { max-width: 100%; display: block; }
        button { font-family: inherit; font-size: inherit; color: inherit; background: none; border: 0; cursor: pointer; }
        ::-webkit-scrollbar { width: 7px; height: 7px; }
        ::-webkit-scrollbar-thumb { background: rgba(212,175,55,.35); border-radius: 10px; }
        .gold-text { background: var(--gold-grad); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }

        /* loader (jab tak /api/auth/me verify na ho) */
        .lx-loader { position: fixed; inset: 0; z-index: 9999; display: none; flex-direction: column; align-items: center; justify-content: center; gap: 14px; background: var(--bg); }
        html.lx-checking .lx-loader { display: flex; }
        html.lx-checking .lx-wrap { visibility: hidden; }
        .lx-loader div { width: 70px; height: 70px; border-radius: 50%; display: grid; place-items: center; font-size: 26px; color: var(--gold); border: 2px solid rgba(212,175,55,.2); border-top-color: var(--gold); animation: lxSpin 1s linear infinite; }
        .lx-loader div i { animation: lxSpin 1s linear infinite reverse; }
        .lx-loader p { color: var(--muted); font-size: 12px; letter-spacing: 2px; text-transform: uppercase; }
        @keyframes lxSpin { to { transform: rotate(360deg); } }
        @keyframes lxUp { from { opacity: 0; transform: translateY(18px); } }

        .lx-wrap { display: flex; min-height: 100vh; }
        .lx-main { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .lx-content { flex: 1; padding: 26px 28px 10px; }

        /* ---------- sidebar ---------- */
        .lx-sb { position: sticky; top: 0; height: 100vh; width: var(--sb-w); flex-shrink: 0; z-index: 900; display: flex; flex-direction: column;
            background: linear-gradient(180deg, #121216, #09090b); border-right: 1px solid var(--border); box-shadow: 10px 0 40px rgba(0,0,0,.4); transition: transform .45s var(--ease); }
        .lx-sb::after { content: ''; position: absolute; top: 0; right: -1px; width: 1px; height: 100%; background: linear-gradient(180deg, transparent, rgba(212,175,55,.5), transparent); }
        .lx-sb-head { height: var(--tb-h); display: flex; align-items: center; justify-content: space-between; padding: 0 18px; border-bottom: 1px solid var(--border); flex-shrink: 0; }
        .lx-logo { display: flex; align-items: center; gap: 12px; }
        .lx-logo-ic { width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; background: var(--gold-grad); color: #111; font-size: 18px; box-shadow: 0 6px 18px rgba(212,175,55,.35), inset 0 1px 0 rgba(255,255,255,.6); }
        .lx-logo b { font-family: var(--serif); font-size: 22px; letter-spacing: 3px; color: #fff; line-height: 1; display: block; }
        .lx-logo small { display: block; font-size: 9px; letter-spacing: 4px; color: var(--gold); margin-top: 4px; }
        .lx-sb-close { display: none; width: 36px; height: 36px; border-radius: 50%; border: 1px solid var(--border-2); color: var(--gold); }
        .lx-sb-user { display: flex; align-items: center; gap: 12px; margin: 16px; padding: 12px; border-radius: 14px; background: rgba(255,255,255,.03); border: 1px solid var(--border); }
        .lx-ava { width: 42px; height: 42px; flex-shrink: 0; border-radius: 12px; overflow: hidden; display: grid; place-items: center; font-weight: 600; color: #111; background: var(--gold-grad); }
        .lx-ava img { width: 100%; height: 100%; object-fit: cover; }
        .lx-sb-user h6 { font-size: 14px; color: #fff; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; }
        .lx-sb-user span { font-size: 12px; color: var(--gold-light); }
        .lx-sb-user .lx-ava, .lx-prof .lx-ava { background: linear-gradient(135deg, #7aa5ff, #3f6fd8); color: #fff; }
        .lx-nav { flex: 1; overflow-y: auto; padding: 4px 14px 14px; }
        .lx-label { padding: 16px 12px 6px; font-size: 10.5px; letter-spacing: 2.5px; text-transform: uppercase; color: #5f5f68; }
        .lx-link { position: relative; display: flex; align-items: center; gap: 14px; padding: 11px 14px; margin-bottom: 4px; border-radius: 12px; color: #b3b3bb; font-size: 14.5px; transition: .3s var(--ease); }
        .lx-link > i { width: 20px; text-align: center; font-size: 16px; transition: .3s; }
        .lx-link:hover { color: #fff; background: rgba(255,255,255,.04); }
        .lx-link:hover > i { color: var(--gold); transform: scale(1.12); }
        .lx-link.active { color: #111; font-weight: 500; background: var(--gold-grad); box-shadow: 0 8px 22px rgba(212,175,55,.3), inset 0 1px 0 rgba(255,255,255,.5); }
        .lx-link.active > i { color: #111; }
        .lx-badge { margin-left: auto; min-width: 22px; height: 20px; padding: 0 6px; border-radius: 20px; display: grid; place-items: center; font-size: 11px; font-weight: 600; background: var(--red); color: #fff; }
        .lx-badge[hidden] { display: none; }
        .lx-sb-foot { padding: 14px; border-top: 1px solid var(--border); }
        .lx-logout:hover { color: var(--red) !important; background: rgba(255,107,107,.08) !important; }
        .lx-logout:hover > i { color: var(--red) !important; }
        .lx-overlay { position: fixed; inset: 0; z-index: 899; background: rgba(0,0,0,.65); backdrop-filter: blur(3px); opacity: 0; visibility: hidden; transition: .35s; }

        /* ---------- topbar ---------- */
        .lx-top { position: sticky; top: 0; z-index: 800; height: var(--tb-h); display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 0 28px;
            background: rgba(7,7,8,.75); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-bottom: 1px solid var(--border); }
        .lx-top-left { display: flex; align-items: center; gap: 14px; min-width: 0; }
        .lx-toggle { display: none; width: 42px; height: 42px; border-radius: 12px; border: 1px solid var(--border); background: rgba(255,255,255,.03); flex-direction: column; justify-content: center; align-items: center; gap: 4px; }
        .lx-toggle span { width: 18px; height: 2px; border-radius: 2px; background: var(--gold); }
        .lx-top h1 { font-family: var(--serif); font-size: 26px; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .lx-prof-wrap { position: relative; }
        .lx-prof { display: flex; align-items: center; gap: 10px; padding: 4px 12px 4px 4px; border-radius: 14px; border: 1px solid var(--border); background: rgba(255,255,255,.03); }
        .lx-prof:hover, .lx-prof.on { border-color: rgba(212,175,55,.45); }
        .lx-prof .lx-ava { width: 34px; height: 34px; border-radius: 10px; }
        .lx-prof-info { text-align: left; line-height: 1.2; }
        .lx-prof-info b { display: block; font-size: 13.5px; color: #fff; font-weight: 600; }
        .lx-prof-info small { font-size: 11.5px; color: var(--muted); }
        .lx-drop { position: absolute; right: 0; top: calc(100% + 12px); width: 250px; z-index: 1000; border-radius: 16px; overflow: hidden;
            background: linear-gradient(160deg, #19191e, #0e0e11); border: 1px solid var(--border-2); border-top-color: rgba(212,175,55,.5); box-shadow: 0 25px 60px rgba(0,0,0,.7);
            opacity: 0; visibility: hidden; transform: translateY(10px) scale(.97); transform-origin: top right; transition: .25s var(--ease); }
        .lx-drop.show { opacity: 1; visibility: visible; transform: none; }
        .lx-drop-head { padding: 16px; border-bottom: 1px solid var(--border); background: linear-gradient(145deg, rgba(212,175,55,.12), transparent); }
        .lx-drop-head h6 { color: #fff; font-size: 14px; }
        .lx-drop-head small { color: var(--muted); font-size: 12px; overflow-wrap: anywhere; }
        .lx-drop ul { padding: 8px; }
        .lx-drop a { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 10px; font-size: 13.5px; color: #ccc; }
        .lx-drop a i { width: 16px; color: var(--muted); }
        .lx-drop a:hover { background: rgba(212,175,55,.09); color: var(--gold-light); }
        .lx-drop a.danger:hover { background: rgba(255,107,107,.1); color: var(--red); }

        /* ---------- shared components ---------- */
        .lx-card { position: relative; padding: 22px; border-radius: var(--radius); background: linear-gradient(150deg, rgba(255,255,255,.06), rgba(255,255,255,.015) 45%, rgba(255,255,255,.025));
            border: 1px solid var(--border); box-shadow: var(--shadow), inset 0 1px 0 rgba(255,255,255,.07); animation: lxUp .6s var(--ease) both; }
        .lx-card::before { content: ''; position: absolute; inset: 0; border-radius: inherit; pointer-events: none; background: linear-gradient(170deg, rgba(255,255,255,.06), transparent 35%); }
        .lx-card > * { position: relative; z-index: 1; }
        .lx-card-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
        .lx-card-head h4 { font-size: 16px; color: #fff; font-weight: 600; }
        .lx-card-head small { color: var(--muted); font-size: 12.5px; }
        .lx-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 18px; border-radius: 12px; font-size: 14px; font-weight: 500; white-space: nowrap; transition: .3s var(--ease); }
        .lx-btn.sm { padding: 6px 12px; font-size: 12.5px; border-radius: 9px; }
        .lx-btn-gold { background: var(--gold-grad); color: #111; box-shadow: 0 8px 22px rgba(212,175,55,.3), inset 0 1px 0 rgba(255,255,255,.5); }
        .lx-btn-gold:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(212,175,55,.45); }
        .lx-btn-ghost { background: rgba(255,255,255,.04); border: 1px solid var(--border-2); color: #ddd; }
        .lx-btn-ghost:hover { border-color: var(--gold); color: var(--gold-light); }
        .lx-btn:disabled { opacity: .5; pointer-events: none; }
        .lx-status { display: inline-flex; align-items: center; gap: 6px; padding: 4px 11px; border-radius: 20px; font-size: 12px; font-weight: 500; white-space: nowrap; text-transform: capitalize; }
        .lx-status::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .lx-status.active, .lx-status.success { color: var(--green); background: rgba(62,207,142,.1); }
        .lx-status.pending, .lx-status.blocked { color: var(--gold-light); background: rgba(212,175,55,.12); }
        .lx-status.inactive { color: #999; background: rgba(255,255,255,.06); }
        .lx-status.failed { color: var(--red); background: rgba(255,107,107,.12); }
        .lx-table-wrap { width: 100%; overflow-x: auto; }
        .lx-table { width: 100%; border-collapse: collapse; min-width: 460px; }
        .lx-table th { text-align: left; padding: 11px 12px; font-size: 11.5px; font-weight: 500; letter-spacing: 1px; text-transform: uppercase; color: var(--muted); border-bottom: 1px solid var(--border); white-space: nowrap; }
        .lx-table td { padding: 12px; border-bottom: 1px solid rgba(255,255,255,.04); font-size: 13.5px; color: #ccc; vertical-align: middle; }
        .lx-table tr:hover td { background: rgba(212,175,55,.03); }
        .lx-user { display: flex; align-items: center; gap: 10px; }
        .lx-user .lx-ava { width: 36px; height: 36px; border-radius: 10px; font-size: 14px; }
        .lx-user b { display: block; color: #fff; font-weight: 500; }
        .lx-user small { color: var(--muted); font-size: 12px; }
        .lx-empty { text-align: center; padding: 30px 10px; color: var(--muted); }
        .lx-empty i { display: block; font-size: 28px; color: rgba(212,175,55,.5); margin-bottom: 8px; }
        .lx-sk { display: block; height: 12px; border-radius: 6px; background: linear-gradient(90deg, rgba(255,255,255,.04) 25%, rgba(255,255,255,.1) 50%, rgba(255,255,255,.04) 75%); background-size: 200% 100%; animation: lxSk 1.3s linear infinite; }
        @keyframes lxSk { to { background-position: -200% 0; } }

        .lx-footer { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px; padding: 18px 28px; border-top: 1px solid var(--border); font-size: 12.5px; color: var(--muted); }
        .lx-toast { position: fixed; left: 50%; bottom: 24px; z-index: 3000; display: flex; align-items: center; gap: 10px; padding: 13px 22px; border-radius: 14px; background: linear-gradient(160deg, #1b1b20, #101013);
            border: 1px solid rgba(62,207,142,.4); color: #fff; font-size: 14px; box-shadow: 0 20px 50px rgba(0,0,0,.6); transform: translate(-50%, 120px); opacity: 0; transition: .45s cubic-bezier(.34,1.56,.64,1); }
        .lx-toast i { color: var(--green); }
        .lx-toast.err { border-color: rgba(255,107,107,.5); }
        .lx-toast.err i { color: var(--red); }
        .lx-toast.show { transform: translate(-50%, 0); opacity: 1; }

        /* ---------- responsive: mobile me sidebar LEFT se ---------- */
        @media (max-width: 991.98px) {
            .lx-sb { position: fixed; left: 0; top: 0; width: 280px; max-width: 86vw; transform: translateX(-105%); }
            body.sb-open .lx-sb { transform: none; }
            body.sb-open .lx-overlay { opacity: 1; visibility: visible; }
            .lx-sb-close { display: grid; place-items: center; }
            .lx-toggle { display: flex; }
            .lx-top { padding: 0 16px; }
            .lx-content { padding: 22px 16px 10px; }
            .lx-footer { padding: 16px; }
        }
        @media (max-width: 575.98px) {
            .lx-prof-info { display: none; }
            .lx-prof { padding: 4px; }
            .lx-top h1 { font-size: 22px; }
            .lx-card { padding: 16px; }
            .lx-drop { position: fixed; left: 10px; right: 10px; top: auto; bottom: 10px; width: auto; border-radius: 20px; transform: translateY(110%); }
            .lx-drop.show { transform: none; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="lx-loader"><div><i class="fa-solid fa-scale-balanced"></i></div><p>Verifying session…</p></div>

<div class="lx-wrap">

    {{-- ================= SIDEBAR ================= --}}
    <aside class="lx-sb" id="lxSidebar">
        <div class="lx-sb-head">
            <a href="{{ route('admin.dashboard') }}" class="lx-logo">
                <span class="lx-logo-ic"><i class="fa-solid fa-scale-balanced"></i></span>
                <span><b>LEXORA</b><small>ADVOCATE</small></span>
            </a>
            <button class="lx-sb-close" id="lxSbClose" aria-label="Close menu"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="lx-sb-user">
            <span class="lx-ava" data-me="avatar">A</span>
            <div><h6 data-me="name">…</h6><span style="color:#8fb4ff"><i class="fa-solid fa-user-tie"></i> Admin (Advocate)</span></div>
        </div>

        <nav class="lx-nav">
            <div class="lx-label">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="lx-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-gauge-high"></i> My Dashboard</a>

            <div class="lx-label">My Work</div>
            <a href="{{ route('superadmin.cases') }}" class="lx-link"><i class="fa-solid fa-gavel"></i> Cases</a>
            <a href="{{ route('superadmin.clients') }}" class="lx-link"><i class="fa-solid fa-users"></i> Clients</a>
            <a href="{{ route('superadmin.enquiries') }}" class="lx-link"><i class="fa-solid fa-envelope-open-text"></i> Enquiries</a>

            <div class="lx-label">Account</div>
            <a href="{{ route('admin.dashboard') }}#profile" class="lx-link"><i class="fa-regular fa-id-card"></i> My Profile</a>
            <a href="{{ route('home') }}" target="_blank" class="lx-link"><i class="fa-solid fa-globe"></i> View Website</a>
        </nav>

        <div class="lx-sb-foot">
            <a href="#" class="lx-link lx-logout" data-logout><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </aside>
    <div class="lx-overlay" id="lxOverlay"></div>

    {{-- ================= MAIN ================= --}}
    <div class="lx-main">
        <header class="lx-top">
            <div class="lx-top-left">
                <button class="lx-toggle" id="lxToggle" aria-label="Open menu"><span></span><span></span><span></span></button>
                <h1>@yield('page_title', 'Dashboard')</h1>
            </div>

            <div class="lx-prof-wrap">
                <button class="lx-prof" id="lxProfBtn">
                    <span class="lx-ava" data-me="avatar">A</span>
                    <span class="lx-prof-info"><b data-me="name">…</b><small>Admin (Advocate)</small></span>
                    <i class="fa-solid fa-chevron-down" style="font-size:10px;color:var(--muted)"></i>
                </button>
                <div class="lx-drop" id="lxDrop">
                    <div class="lx-drop-head"><h6 data-me="name">…</h6><small data-me="email">—</small></div>
                    <ul>
                        <li><a href="{{ route('admin.dashboard') }}#profile"><i class="fa-regular fa-id-card"></i> My Profile</a></li>
                        <li><a href="{{ route('home') }}" target="_blank"><i class="fa-solid fa-globe"></i> View Website</a></li>
                        <li><a href="#" class="danger" data-logout><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="lx-content">
            @yield('content')
        </main>

        <footer class="lx-footer">
            <p>© {{ date('Y') }} <span class="gold-text">Lo</span> – Advocate Panel</p>
            <p>Secured with token authentication</p>
        </footer>
    </div>
</div>

<div class="lx-toast" id="lxToast"><i class="fa-solid fa-circle-check"></i> <span>Done</span></div>

{{-- ================= CORE JS: API + AUTH + SIDEBAR ================= --}}
<script>
window.LX = (function () {
    'use strict';
    const API_BASE = @json(url('/api'));
    const LOGIN    = @json(route('login'));
    const ROLE     = 'admin';                            // is layout ka role (advocate)
    const HOME     = { superadmin: @json(route('superadmin.dashboard')), admin: @json(route('admin.dashboard')), user: @json(route('user.dashboard')) };
    const KEY = 'lx_token', UKEY = 'lx_user';

    const token = () => { try { return localStorage.getItem(KEY) || sessionStorage.getItem(KEY); } catch (e) { return null; } };
    const clear = () => { try { [localStorage, sessionStorage].forEach(s => { s.removeItem(KEY); s.removeItem(UKEY); }); } catch (e) {} };
    const esc   = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const goLogin = extra => location.replace(LOGIN + '?next=' + encodeURIComponent(location.pathname) + (extra || ''));

    /** Bearer token ke saath API call */
    async function api(method, path, data) {
        const headers = { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Authorization': 'Bearer ' + token() };
        let body;
        if (data instanceof FormData) body = data;
        else if (data !== undefined) { headers['Content-Type'] = 'application/json'; body = JSON.stringify(data); }

        let res;
        try { res = await fetch(API_BASE + path, { method, headers, body }); }
        catch (e) { throw { status: 0, message: 'Network error – check your internet connection.', errors: {} }; }

        let json = {};
        try { json = await res.json(); } catch (e) {}
        if (res.status === 401) { clear(); goLogin(); throw { status: 401, message: 'Session expired.', errors: {} }; }
        if (!res.ok) throw { status: res.status, message: json.message || ('Something went wrong (' + res.status + ').'), errors: json.errors || {} };
        return json;
    }

    let tt;
    function toast(msg, isErr) {
        const t = document.getElementById('lxToast');
        t.querySelector('span').textContent = msg;
        t.querySelector('i').className = 'fa-solid ' + (isErr ? 'fa-circle-exclamation' : 'fa-circle-check');
        t.classList.toggle('err', !!isErr);
        t.classList.add('show');
        clearTimeout(tt); tt = setTimeout(() => t.classList.remove('show'), 2600);
    }

    const avatarHtml = (name, photo) => photo ? '<img src="' + esc(photo) + '" alt="">' : esc((name || '?').replace(/^adv\.?\s+/i, '').charAt(0).toUpperCase());

    /* -------- 1. AUTH: token verify + role check -------- */
    api('GET', '/auth/me').then(r => {
        const u = r.user;
        if (u.role !== ROLE) { location.replace(HOME[u.role] || LOGIN); return; }   // galat role -> apna dashboard

        document.querySelectorAll('[data-me="name"]').forEach(el => el.textContent = u.name);
        document.querySelectorAll('[data-me="email"]').forEach(el => el.textContent = u.email);
        document.querySelectorAll('[data-me="avatar"]').forEach(el => el.innerHTML = avatarHtml(u.name, u.photo_url));
        document.documentElement.classList.remove('lx-checking');
        document.dispatchEvent(new CustomEvent('lx:ready', { detail: u }));

    }).catch(err => {
        if (err.status === 403) { clear(); goLogin('&reason=inactive'); }
        else if (err.status !== 401) { clear(); goLogin(); }
    });

    /* -------- 2. LOGOUT (server par token delete) -------- */
    document.addEventListener('click', e => {
        const lo = e.target.closest('[data-logout]');
        if (!lo) return;
        e.preventDefault();
        lo.style.opacity = '.5';
        const done = () => { clear(); location.replace(LOGIN); };
        api('POST', '/auth/logout').then(done, done);
    });

    /* -------- 3. SIDEBAR (mobile: left se open) -------- */
    const openSb  = () => document.body.classList.add('sb-open', 'lock');
    const closeSb = () => document.body.classList.remove('sb-open', 'lock');
    document.getElementById('lxToggle').addEventListener('click', openSb);
    document.getElementById('lxSbClose').addEventListener('click', closeSb);
    document.getElementById('lxOverlay').addEventListener('click', closeSb);
    window.addEventListener('resize', () => { if (window.innerWidth >= 992) closeSb(); });

    /* -------- 4. PROFILE DROPDOWN -------- */
    const pBtn = document.getElementById('lxProfBtn'), drop = document.getElementById('lxDrop');
    pBtn.addEventListener('click', e => { e.stopPropagation(); drop.classList.toggle('show'); pBtn.classList.toggle('on'); });
    document.addEventListener('click', e => { if (!drop.contains(e.target)) { drop.classList.remove('show'); pBtn.classList.remove('on'); } });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeSb(); drop.classList.remove('show'); } });

    return {
        api, toast, esc, avatarHtml,
        get:   p      => api('GET', p),
        post:  (p, d) => api('POST', p, d),
        patch: (p, d) => api('PATCH', p, d),
        del:   p      => api('DELETE', p),
        url:   @json(url('/')),
    };
})();
</script>
@stack('scripts')
</body>
</html>