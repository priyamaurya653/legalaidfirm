<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Home') | Lexora Legal – Advocates & Legal Consultants</title>
    <meta name="description" content="@yield('meta', 'Lexora Legal – trusted advocates for Criminal, Civil, Corporate, Family, Tax cases and all legal documentation services.')">
    <meta name="app-url" content="{{ url('/') }}">
    <meta name="api-base" content="{{ url('/api') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    @stack('head')
    @stack('styles')
</head>
<body>

    {{-- Preloader --}}
    <div id="preloader">
        <div class="loader-logo"><i class="fa-solid fa-scale-balanced"></i></div>
        <div class="loader-text">LEXORA</div>
    </div>

    {{-- Scroll progress bar --}}
    <div class="scroll-progress"></div>

    {{-- Cursor glow (desktop only) --}}
    <div class="cursor-glow"></div>

    @include('layouts.header')

    <main>
        @yield('content')
    </main>

    @include('layouts.footer')

    {{-- ============ DISCLAIMER POPUP (first visit) ============ --}}
    <div class="disclaimer" id="disclaimer" role="dialog" aria-modal="true" aria-labelledby="discTitle">
        <div class="disc-box glass">
            <button class="disc-close" id="discClose" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>

            <div class="disc-icon"><i class="fa-solid fa-scale-balanced"></i></div>
            <h3 id="discTitle">Disclaimer</h3>
            <div class="disc-line"></div>

            <div class="disc-text">
                <p>As per the rules of the <strong>Bar Council of India</strong>, advocates and law firms are not permitted to solicit work or advertise in any manner.</p>
                <p>By clicking <strong>"I Agree"</strong> or closing this message, you acknowledge that:</p>
                <ul>
                    <li><i class="fa-solid fa-check"></i> You are seeking information about Lexora Legal on your own accord and there has been no advertisement, solicitation or inducement from us or any of our members.</li>
                    <li><i class="fa-solid fa-check"></i> The information on this website is for general information only and should not be treated as legal advice.</li>
                    <li><i class="fa-solid fa-check"></i> Using this website or contacting us through it does not create an advocate–client relationship.</li>
                    <li><i class="fa-solid fa-check"></i> Lexora Legal is not liable for any action taken based on the content of this website.</li>
                </ul>
            </div>

            <div class="disc-actions">
                <button class="btn btn-gold" id="discAgree">I Agree <i class="fa-solid fa-check"></i></button>
            </div>
        </div>
    </div>

    {{-- Floating buttons --}}
    <a href="https://wa.me/919999999999" target="_blank" class="float-whatsapp" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
    <button class="back-to-top" aria-label="Back to top"><i class="fa-solid fa-arrow-up"></i></button>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/api.js') }}"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
    @stack('scripts')
</body>
</html>