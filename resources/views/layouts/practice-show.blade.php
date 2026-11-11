@php $areas = config('practice', []); $area = $areas[$slug]; @endphp
@extends('layouts.app')
@section('title', $area['title'])
@section('meta', $area['short'])

@section('content')

@include('layouts.page-banner', [
    'title'  => $area['title'],
    'image'  => $area['image'],
    'parent' => ['Practice Areas', route('practice.index')],
])

<section class="section">
    <div class="container detail-layout">

        {{-- Main content --}}
        <div class="detail-main">
            <div class="detail-hero-img" data-aos="fade-up">
                <img src="{{ $area['image'] }}" alt="{{ $area['title'] }}">
            </div>

            <h2 class="section-title" data-aos="fade-up">{{ $area['title'] }} <span class="gold-text">Services</span></h2>
            <p class="lead" data-aos="fade-up">{{ $area['intro'] }}</p>

            <h3 class="sub-title" data-aos="fade-up">What We Handle</h3>
            <div class="service-grid">
                @foreach($area['services'] as $s)
                    <div class="service-item glass" data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                        <i class="fa-solid fa-scale-balanced"></i>
                        <span>{{ $s }}</span>
                    </div>
                @endforeach
            </div>

            <h3 class="sub-title" data-aos="fade-up">How We Work On Your Case</h3>
            <div class="timeline small">
                @foreach($area['process'] as $i => $p)
                    <div class="tl-step" data-aos="fade-up" data-aos-delay="{{ $i * 120 }}">
                        <div class="tl-num">0{{ $i + 1 }}</div>
                        <h4>{{ $p[0] }}</h4>
                        <p>{{ $p[1] }}</p>
                    </div>
                @endforeach
            </div>

            <h3 class="sub-title" data-aos="fade-up">Frequently Asked Questions</h3>
            <div class="faq" data-aos="fade-up">
                <div class="faq-item">
                    <button class="faq-q">How soon can I meet an advocate? <i class="fa-solid fa-plus"></i></button>
                    <div class="faq-a"><p>Usually the same or next working day. Urgent {{ strtolower($area['title']) }} matters get a call back within a few hours.</p></div>
                </div>
                <div class="faq-item">
                    <button class="faq-q">Is my consultation confidential? <i class="fa-solid fa-plus"></i></button>
                    <div class="faq-a"><p>Yes. Everything you share is protected by advocate–client privilege and our internal confidentiality policy.</p></div>
                </div>
                <div class="faq-item">
                    <button class="faq-q">How are fees decided? <i class="fa-solid fa-plus"></i></button>
                    <div class="faq-a"><p>After reviewing your matter we share a clear written fee estimate — stage-wise or fixed — before any work begins.</p></div>
                </div>
                <div class="faq-item">
                    <button class="faq-q">Do you handle cases outside Delhi? <i class="fa-solid fa-plus"></i></button>
                    <div class="faq-a"><p>Yes. Through our associate network we appear in courts across 12 states and before the Supreme Court.</p></div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <aside class="detail-side">
            <div class="side-box glass" data-aos="fade-left">
                <h4>All Practice Areas</h4>
                <ul class="side-links">
                    @foreach($areas as $s => $a)
                        <li>
                            <a href="{{ route('practice.show', $s) }}" class="{{ $s === $slug ? 'active' : '' }}">
                                <i class="{{ $a['icon'] }}"></i> {{ $a['title'] }}
                                <i class="fa-solid fa-chevron-right end"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="side-box side-cta" data-aos="fade-left" data-aos-delay="100">
                <i class="fa-solid fa-phone-volume big"></i>
                <h4>Need Urgent Help?</h4>
                <p>Speak to a {{ $area['title'] }} expert now.</p>
                <a href="tel:+919999999999" class="btn btn-dark w-100">+91 99999 99999</a>
            </div>

            <div class="side-box glass" data-aos="fade-left" data-aos-delay="200">
                <h4>Quick Enquiry</h4>
                <form action="#" data-static-form class="side-form">
                    <input type="text" name="name" placeholder="Name" required>
                    <input type="tel" name="phone" placeholder="Phone" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="hidden" name="subject" value="{{ $area['title'] }}">
                    <textarea name="message" rows="3" placeholder="Briefly describe your matter" required></textarea>
                    <button class="btn btn-gold w-100">Send</button>
                </form>
            </div>
        </aside>
    </div>
</section>

@endsection