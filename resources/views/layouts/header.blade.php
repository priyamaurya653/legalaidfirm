@php
$navAreas = config('practice', []);
$litigation = array_filter($navAreas, fn($a) => $a['group'] === 'litigation');
$services = array_filter($navAreas, fn($a) => $a['group'] === 'services');
@endphp

<div class="topbar">
    <div class="container topbar-inner">
        <div class="topbar-left">
            <span><i class="fa-solid fa-phone"></i> +91 99999 99999</span>
            <span><i class="fa-solid fa-envelope"></i> info@lexoralegal.com</span>
        </div>
        <div class="topbar-right">
            <span><i class="fa-regular fa-clock"></i> Mon – Sat : 10:00 AM – 7:00 PM</span>
            <a href="{{ route('register') }}" class="tb-link"><i class="fa-solid fa-user-plus"></i> Advocate Registration</a>
            <span class="tb-social">
                <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
            </span>
        </div>
    </div>
</div>

<header class="site-header" id="siteHeader">
    <div class="container header-inner">
        <a href="{{ route('home') }}" class="logo-image">
            <img src="{{ asset('images/logo.png') }}" alt="LEXORA LEGAL Logo" class="logo-image">
        </a>

        {{-- Desktop Nav --}}
        <nav class="main-nav">
            <ul>
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
                <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About Firm</a></li>
                <li class="has-dropdown">
                    <a href="{{ route('practice.index') }}" class="{{ request()->routeIs('practice.*') ? 'active' : '' }}">
                        Practice Areas <i class="fa-solid fa-chevron-down"></i>
                    </a>
                    <div class="dropdown mega">
                        <div class="mega-col">
                            <h6>Litigation</h6>
                            <ul>
                                @foreach($litigation as $slug => $a)
                                <li><a href="{{ route('practice.show', $slug) }}"><i class="{{ $a['icon'] }}"></i> {{ $a['title'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="mega-col">
                            <h6>Legal Services</h6>
                            <ul>
                                @foreach($services as $slug => $a)
                                <li><a href="{{ route('practice.show', $slug) }}"><i class="{{ $a['icon'] }}"></i> {{ $a['title'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        <a href="{{ route('practice.index') }}" class="mega-all">View All Practice Areas <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </li>
                <li><a href="{{ route('career') }}" class="{{ request()->routeIs('career') ? 'active' : '' }}">Career</a></li>
                <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a></li>
            </ul>
        </nav>

        <div class="header-actions">
            {{-- hamesha "Login" (data-auth-link hataya – warna auth.js ise "Dashboard" bana deta tha) --}}
            <a href="{{ route('login') }}" class="btn btn-outline header-login" aria-label="Login">
                <i class="fa-regular fa-user"></i> <span>Login</span>
            </a>
            <a href="{{ route('contact') }}" class="btn btn-gold header-cta">Free Consultation</a>

            {{-- Mobile toggle --}}
            <button class="menu-toggle" id="menuToggle" aria-label="Open menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

{{-- Mobile side drawer (LEFT se open hota hai) --}}
<div class="drawer-overlay" id="drawerOverlay"></div>
<aside class="mobile-drawer" id="mobileDrawer">
    <div class="drawer-head">
       <a href="{{ route('home') }}" class="logo-image">
            <img src="{{ asset('images/logo.png') }}" alt="LEXORA LEGAL Logo" class="logo-image">
        </a>
        <button class="drawer-close" id="drawerClose" aria-label="Close menu"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <ul class="drawer-nav">
        <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}"><i class="fa-solid fa-house"></i> Home</a></li>
        <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}"><i class="fa-solid fa-landmark"></i> About Firm</a></li>
        <li class="drawer-dd">
            <button class="drawer-dd-btn {{ request()->routeIs('practice.*') ? 'active' : '' }}">
                <span><i class="fa-solid fa-briefcase"></i> Practice Areas</span>
                <i class="fa-solid fa-chevron-down arrow"></i>
            </button>
            <ul class="drawer-sub">
                <li><a href="{{ route('practice.index') }}"><b>All Practice Areas</b></a></li>
                <li class="drawer-sub-head">Litigation</li>
                @foreach($litigation as $slug => $a)
                <li><a href="{{ route('practice.show', $slug) }}">{{ $a['title'] }}</a></li>
                @endforeach
                <li class="drawer-sub-head">Legal Services</li>
                @foreach($services as $slug => $a)
                <li><a href="{{ route('practice.show', $slug) }}">{{ $a['title'] }}</a></li>
                @endforeach
            </ul>
        </li>
        <li><a href="{{ route('career') }}" class="{{ request()->routeIs('career') ? 'active' : '' }}"><i class="fa-solid fa-user-tie"></i> Career</a></li>
        <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}"><i class="fa-solid fa-envelope-open-text"></i> Contact</a></li>
    </ul>

    <div class="drawer-foot">
        <div class="drawer-auth">
            <a href="{{ route('login') }}" class="btn btn-outline"><i class="fa-regular fa-user"></i> <span>Login</span></a>
            <a href="{{ route('signup') }}" class="btn btn-outline"><i class="fa-solid fa-user-plus"></i> Sign Up</a>
        </div>
        <a href="{{ route('register') }}" class="drawer-adv-link"><i class="fa-solid fa-user-tie"></i> Advocate? Register here</a>
        <a href="{{ route('contact') }}" class="btn btn-gold w-100">Free Consultation</a>
        <p><i class="fa-solid fa-phone"></i> +91 99999 99999</p>
        <div class="socials">
            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#"><i class="fa-brands fa-instagram"></i></a>
            <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
            <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
        </div>
    </div>
</aside>