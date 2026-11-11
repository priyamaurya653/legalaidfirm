<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | Lexora Superadmin</title>
    <meta name="app-url" content="{{ url('/') }}">
    <meta name="api-base" content="{{ url('/api') }}">

    {{-- Token guard: token nahi hai to seedha login par bhejo (page dikhne se pehle) --}}
    <script>
        (function () {
            var t = null;
            try { t = localStorage.getItem('lx_token') || sessionStorage.getItem('lx_token'); } catch (e) {}
            if (!t) { location.replace(@json(route('login')) + '?next=' + encodeURIComponent(location.pathname)); return; }
            document.documentElement.classList.add('auth-checking');
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ file_exists(public_path('css/admin.css')) ? filemtime(public_path('css/admin.css')) : time() }}">
    {{-- Preloader logo (image) – ring ghoomti hai, logo seedha rehta hai --}}
    <style>
        .lx-loader-logo { position: relative; width: 96px; height: 96px; display: grid; place-items: center; border: 0 !important; animation: none !important; }
        .lx-loader-logo::before { content: ''; position: absolute; inset: 0; border-radius: 50%; border: 2px solid rgba(212,175,55,.2); border-top-color: #d4af37; animation: spin 1.1s linear infinite; }
        .lx-loader-logo::after { content: ''; position: absolute; inset: 10px; border-radius: 50%; background: radial-gradient(circle, rgba(212,175,55,.18), transparent 70%); animation: lxPulse 1.6s ease-in-out infinite; }
        .lx-loader-logo img { position: relative; z-index: 1; width: 62px; height: 62px; object-fit: contain; animation: none !important; filter: drop-shadow(0 4px 14px rgba(212,175,55,.35)); }
        .lx-loader-logo i { position: relative; z-index: 1; animation: none !important; font-size: 30px; color: #d4af37; }
        @keyframes lxPulse { 50% { transform: scale(1.12); opacity: .6; } }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
    @stack('styles')
</head>
<body class="adm" data-require-role="@yield('require_role')">

    {{-- Auth check loader --}}
    <div class="auth-loader" id="authLoader">
        <div class="al-logo lx-loader-logo">
            <img src="{{ asset('images/logo.png') }}" alt="LEXORA Logo"
                 onerror="this.outerHTML='<i class=&quot;fa-solid fa-scale-balanced&quot;></i>'">
        </div>
        <p>Verifying session…</p>
    </div>

    <div class="adm-wrap" id="admWrap">
        @include('frontlayout.sidebar')

        {{-- mobile overlay --}}
        <div class="sb-overlay" id="sbOverlay"></div>

        <div class="adm-main">
            @include('frontlayout.topbar')

            <main class="adm-content">
                {{-- Page heading + breadcrumb --}}
                <div class="page-head">
                    <div>
                        <h1>@yield('page_title', 'Dashboard')</h1>
                        <ul class="adm-crumb">
                            <li><a href="{{ route('superadmin.dashboard') }}"><i class="fa-solid fa-house"></i></a></li>
                            <li>@yield('page_title', 'Dashboard')</li>
                        </ul>
                    </div>
                    <div class="page-actions">@yield('page_actions')</div>
                </div>

                @yield('content')
            </main>

            @include('frontlayout.footer')
        </div>
    </div>

    {{-- Toast (static forms ke liye) --}}
    <div class="adm-toast" id="admToast"><i class="fa-solid fa-circle-check"></i> <span>Saved successfully</span></div>

    @include('frontlayout.case-modal')
    @stack('modals')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script src="{{ asset('js/api.js') }}?v={{ file_exists(public_path('js/api.js')) ? filemtime(public_path('js/api.js')) : time() }}"></script>
    <script src="{{ asset('js/admin.js') }}?v={{ file_exists(public_path('js/admin.js')) ? filemtime(public_path('js/admin.js')) : time() }}"></script>
    @stack('scripts')
</body>
</html>