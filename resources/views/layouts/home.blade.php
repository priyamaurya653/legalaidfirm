@extends('layouts.app')
@section('title', 'Home')
@php
    $areas      = config('practice', []);
    $litigation = array_filter($areas, fn($a) => $a['group'] === 'litigation');
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')

{{-- ================= 1. HERO (glossy slider banner) ================= --}}
<section class="h-hero" id="heroBanner">

    {{-- background image slider --}}
    <div class="hb-slides">
        <div class="hb-slide active" style="background-image:url('https://images.unsplash.com/photo-1589994965851-a8f479c573a9?w=1800&q=80')"></div>
        <div class="hb-slide" style="background-image:url('https://images.unsplash.com/photo-1505664194779-8beaceb93744?w=1800&q=80')"></div>
        <div class="hb-slide" style="background-image:url('https://images.unsplash.com/photo-1436450412740-6b988f486c6b?w=1800&q=80')"></div>
    </div>
    <div class="hb-overlay"></div>
    <div class="h-hero-grid-bg"></div>
    <div class="hb-beam hb-beam-1"></div>
    <div class="hb-beam hb-beam-2"></div>
    <div class="h-orb h-orb-1"></div>
    <div class="h-orb h-orb-2"></div>
    <div class="hero-particles" id="particles"></div>

    <div class="container h-hero-inner">
        <div class="h-hero-text hb-glass">
            <span class="hb-glass-shine"></span>

            <span class="eyebrow hb-reveal" style="--d:.2s"><i class="fa-solid fa-scale-balanced"></i> Advocates & Legal Consultants</span>

            <h1>
                <span class="hb-line"><span class="hb-reveal" style="--d:.35s">We Defend Your</span></span>
                <span class="hb-line"><span class="hb-reveal hb-shimmer typed" style="--d:.5s" data-words='["Rights","Freedom","Business","Family","Future"]'>Rights</span></span>
            </h1>
            <div class="h-line hb-reveal" style="--d:.65s"></div>

            <p class="hb-reveal" style="--d:.75s">
                20+ years of courtroom experience — from criminal trials and property disputes to rent agreements, wills and challans. Honest advice. Fearless representation. Real results.
            </p>

            <div class="hero-actions hb-reveal" style="--d:.9s">
                <a href="{{ route('contact') }}" class="btn btn-gold btn-lg">Book Free Consultation <i class="fa-solid fa-arrow-right"></i></a>
                <a href="tel:+919999999999" class="h-call">
                    <span class="h-call-icon"><i class="fa-solid fa-phone"></i></span>
                    <span><small>24×7 Helpline</small>+91 99999 99999</span>
                </a>
            </div>

            <div class="h-trust hb-reveal" style="--d:1.05s">
                <div class="h-avatars">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&q=80" alt="">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=100&q=80" alt="">
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=100&q=80" alt="">
                    <span>+3K</span>
                </div>
                <div>
                    <div class="stars">@for($s=0;$s<5;$s++)<i class="fa-solid fa-star"></i>@endfor <b>4.9</b></div>
                    <small>Trusted by 3,500+ happy clients</small>
                </div>
            </div>
        </div>

        <div class="h-hero-visual" id="heroVisual">
            <div class="h-arch hb-pop" data-depth="10">
                <img src="https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=900&q=80" alt="Scales of justice">
                <span class="hb-arch-shine"></span>
            </div>
            <div class="hb-arch-reflect"></div>

            <div class="h-ring hb-pop" style="--d:.6s" data-depth="25">
                <svg viewBox="0 0 200 200">
                    <defs><path id="circlePath" d="M100,100 m-78,0 a78,78 0 1,1 156,0 a78,78 0 1,1 -156,0"/></defs>
                    <text><textPath href="#circlePath">• JUSTICE • INTEGRITY • EXCELLENCE • SINCE 2005 </textPath></text>
                </svg>
                <i class="fa-solid fa-gavel"></i>
            </div>

            <div class="h-float h-float-1 glass hb-pop" style="--d:.8s" data-depth="35">
                <i class="fa-solid fa-trophy"></i>
                <div><strong>3,500+</strong><span>Cases Won</span></div>
            </div>
            <div class="h-float h-float-2 glass hb-pop" style="--d:1s" data-depth="20">
                <i class="fa-solid fa-shield-halved"></i>
                <div><strong>100%</strong><span>Confidential</span></div>
            </div>
        </div>
    </div>

    {{-- slide caption + dots --}}
    <div class="container hb-controls">
        <div class="hb-dots">
            <button class="active" aria-label="Slide 1"><i></i></button>
            <button aria-label="Slide 2"><i></i></button>
            <button aria-label="Slide 3"><i></i></button>
        </div>
        <span class="hb-caption" id="hbCaption">Supreme Court & High Courts</span>
    </div>

    <div class="hb-floor"></div>
    <a href="#marquee" class="scroll-down" aria-label="Scroll down"><span></span></a>
</section>

{{-- ================= 2. MARQUEE ================= --}}
<div class="h-marquee" id="marquee">
    <div class="h-marquee-track">
        @for($r = 0; $r < 2; $r++)
            @foreach($areas as $a)
                <span><i class="{{ $a['icon'] }}"></i> {{ $a['title'] }}</span>
            @endforeach
        @endfor
    </div>
</div>

{{-- ================= 3. STATS ================= --}}
<section class="h-stats">
    <div class="container stats-grid">
        @foreach([
            ['20', '+', 'Years Experience', 'fa-solid fa-hourglass-half'],
            ['3500', '+', 'Cases Won', 'fa-solid fa-trophy'],
            ['98', '%', 'Success Rate', 'fa-solid fa-chart-line'],
            ['25', '+', 'Expert Advocates', 'fa-solid fa-user-tie'],
        ] as $st)
            <div class="stat glass" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <i class="{{ $st[3] }} stat-icon"></i>
                <h3><span class="counter" data-target="{{ $st[0] }}">0</span>{{ $st[1] }}</h3>
                <p>{{ $st[2] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ================= 4. ABOUT ================= --}}
<section class="section">
    <div class="container split">
        <div class="h-collage" data-aos="fade-right">
            <div class="hc-main img-frame">
                <img src="https://images.unsplash.com/photo-1521791055366-0d553872125f?w=900&q=80" alt="Our team">
            </div>
            <div class="hc-small img-frame">
                <img src="https://images.unsplash.com/photo-1505664194779-8beaceb93744?w=600&q=80" alt="Law books">
            </div>
            <div class="exp-badge glass"><strong>20+</strong><span>Years of Legal<br>Excellence</span></div>
        </div>

        <div class="split-content" data-aos="fade-left">
            <span class="section-tag">About Lexora</span>
            <h2 class="section-title">A Legacy Built On <span class="gold-text">Trust & Victory</span></h2>
            <p>Founded in 2005 in New Delhi, Lexora Legal is a full-service law firm. We represent individuals, families and businesses before District Courts, High Courts, Tribunals and the Supreme Court of India.</p>
            <p>Alongside litigation, our documentation desk handles everyday legal needs — rent agreements, wills, power of attorney, affidavits, legal notices, challans and registrations — quickly and correctly.</p>

            <div class="h-about-points">
                <div class="hap glass">
                    <i class="fa-solid fa-user-shield"></i>
                    <div><h5>Senior Advocates</h5><p>Direct access to experienced counsel.</p></div>
                </div>
                <div class="hap glass">
                    <i class="fa-solid fa-indian-rupee-sign"></i>
                    <div><h5>Transparent Fees</h5><p>Clear estimate before we begin.</p></div>
                </div>
            </div>

            <div class="h-about-foot">
                <a href="{{ route('about') }}" class="btn btn-gold">Discover More <i class="fa-solid fa-arrow-right"></i></a>
                <div class="signature">
                    <h4>Adv. Rajiv Malhotra</h4>
                    <span>Founder & Senior Advocate</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= 5. EXPLORE OUR PRACTICE AREAS (new section) ================= --}}
<section class="section section-dark explore-section">
    <div class="ex-bg-lines"></div>
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <span class="section-tag">What We Handle</span>
            <h2 class="section-title">Explore Our <span class="gold-text">Practice Areas</span></h2>
            <p>From courtroom battles to everyday legal paperwork — pick a service to see exactly how we help.</p>
        </div>

        <div class="ex-grid">
            @foreach($areas as $slug => $a)
                @include('layouts.explore-card', ['slug' => $slug, 'a' => $a, 'i' => $loop->index])
            @endforeach
        </div>
    </div>
</section>

{{-- ================= 6. CORE EXPERTISE – TABS (explained) ================= --}}
<section class="section">
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <span class="section-tag">Core Expertise</span>
            <h2 class="section-title">How We Win <span class="gold-text">In Court</span></h2>
            <p>Click an area to see what we handle and how our advocates approach your case.</p>
        </div>

        <div class="xp-tabs" data-aos="fade-up">
            <div class="xp-nav">
                @foreach($litigation as $slug => $a)
                    <button class="xp-btn {{ $loop->first ? 'active' : '' }}" data-tab="xp-{{ $slug }}">
                        <i class="{{ $a['icon'] }}"></i>
                        <span>{{ $a['title'] }}</span>
                        <i class="fa-solid fa-chevron-right xp-arrow"></i>
                    </button>
                @endforeach
            </div>

            <div class="xp-panels glass">
                @foreach($litigation as $slug => $a)
                    <div class="xp-panel {{ $loop->first ? 'active' : '' }}" id="xp-{{ $slug }}">
                        <div class="xp-img">
                            <img src="{{ $a['image'] }}" alt="{{ $a['title'] }}" loading="lazy">
                            <span class="xp-badge"><i class="{{ $a['icon'] }}"></i></span>
                        </div>
                        <div class="xp-content">
                            <h3>{{ $a['title'] }}</h3>
                            <p>{{ $a['intro'] }}</p>
                            <ul class="check-list two-col">
                                @foreach(array_slice($a['services'], 0, 6) as $s)
                                    <li><i class="fa-solid fa-circle-check"></i> {{ $s }}</li>
                                @endforeach
                            </ul>
                            <a href="{{ route('practice.show', $slug) }}" class="btn btn-gold">Read More <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ================= 7. WHY US ================= --}}
<section class="section section-dark">
    <div class="container h-why">
        <div class="h-why-text" data-aos="fade-right">
            <span class="section-tag">Why Choose Us</span>
            <h2 class="section-title">Your Case Deserves <span class="gold-text">The Best Defence</span></h2>
            <p class="muted">We prepare every matter as if it's going to trial — that's why our clients win more often.</p>

            <div class="h-why-list">
                @foreach([
                    ['fa-solid fa-bolt', 'Same-Day Action', 'Urgent bail, stay and injunction matters filed the same day.'],
                    ['fa-solid fa-comments', 'Direct Communication', 'Talk to your advocate directly — updates on call & WhatsApp.'],
                    ['fa-solid fa-lock', 'Complete Confidentiality', 'Your information stays protected under advocate–client privilege.'],
                    ['fa-solid fa-map-location-dot', 'Pan-India Reach', 'Associate counsel network across 12 states and the Supreme Court.'],
                ] as $w)
                    <div class="h-why-item" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="why-icon"><i class="{{ $w[0] }}"></i></div>
                        <div>
                            <h4>{{ $w[1] }}</h4>
                            <p>{{ $w[2] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="h-why-visual" data-aos="fade-left">
            <div class="img-frame">
                <img src="https://images.unsplash.com/photo-1436450412740-6b988f486c6b?w=900&q=80" alt="Courtroom">
            </div>
            <div class="h-why-card glass">
                <div class="h-progress">
                    <div class="hp-row"><span>Criminal Defence</span><b>96%</b></div>
                    <div class="hp-bar"><i style="--w:96%"></i></div>
                    <div class="hp-row"><span>Civil Disputes</span><b>92%</b></div>
                    <div class="hp-bar"><i style="--w:92%"></i></div>
                    <div class="hp-row"><span>Corporate Matters</span><b>98%</b></div>
                    <div class="hp-bar"><i style="--w:98%"></i></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= 8. PROCESS ================= --}}
<section class="section">
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <span class="section-tag">How We Work</span>
            <h2 class="section-title">From Consultation To <span class="gold-text">Victory</span></h2>
        </div>
        <div class="timeline">
            @foreach([
                ['Free Consultation', 'Share your matter privately with a senior advocate.'],
                ['Case Evaluation', 'We study documents and explain every legal option.'],
                ['Strategy & Filing', 'A clear plan is made and filed without delay.'],
                ['Resolution', 'We fight for the best outcome — in or out of court.'],
            ] as $i => $s)
                <div class="tl-step" data-aos="fade-up" data-aos-delay="{{ $i * 150 }}">
                    <div class="tl-num">0{{ $i + 1 }}</div>
                    <h4>{{ $s[0] }}</h4>
                    <p>{{ $s[1] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= 9. TEAM ================= --}}
<section class="section section-dark">
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <span class="section-tag">Our Advocates</span>
            <h2 class="section-title">Meet The <span class="gold-text">Legal Experts</span></h2>
        </div>
        <div class="team-grid">
            @foreach([
                ['Adv. Rajiv Malhotra', 'Founder & Senior Advocate', 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=600&q=80'],
                ['Adv. Neha Kapoor', 'Partner – Corporate Law', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=600&q=80'],
                ['Adv. Arjun Mehta', 'Partner – Criminal Law', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&q=80'],
                ['Adv. Priya Sharma', 'Family & Civil Law', 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=600&q=80'],
            ] as $i => $t)
                <div class="team-card" data-aos="zoom-in" data-aos-delay="{{ $i * 100 }}">
                    <div class="team-img">
                        <img src="{{ $t[2] }}" alt="{{ $t[0] }}" loading="lazy">
                        <div class="team-social">
                            <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                            <a href="#"><i class="fa-solid fa-envelope"></i></a>
                        </div>
                    </div>
                    <h4>{{ $t[0] }}</h4>
                    <span>{{ $t[1] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= 10. TESTIMONIALS ================= --}}
<section class="section">
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <span class="section-tag">Testimonials</span>
            <h2 class="section-title">What Our <span class="gold-text">Clients Say</span></h2>
        </div>
        <div class="testi-slider" data-aos="fade-up">
            <div class="testi-track">
                @foreach([
                    ['Got anticipatory bail within two days. The team was available even late at night and kept me calm throughout.', 'Rohit S.', 'Criminal Matter'],
                    ['They handled our company registration, contracts and a tough recovery suit. Truly a one-stop legal partner.', 'Ankita Verma', 'Business Owner'],
                    ['My custody case was very sensitive. Lexora handled it with care and got a fair order for my child.', 'Meera J.', 'Family Matter'],
                    ['Rent agreement and police verification for my flat were done in one day. Very smooth and quick.', 'Sanjay Gupta', 'Landlord'],
                ] as $r)
                    <div class="testi-card glass">
                        <i class="fa-solid fa-quote-left quote"></i>
                        <div class="stars">@for($s=0;$s<5;$s++)<i class="fa-solid fa-star"></i>@endfor</div>
                        <p>{{ $r[0] }}</p>
                        <h5>{{ $r[1] }}</h5>
                        <span>{{ $r[2] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="testi-dots"></div>
        </div>
    </div>
</section>

{{-- ================= 11. FAQ + CONSULTATION ================= --}}
<section class="section section-dark">
    <div class="container h-faq-wrap">
        <div data-aos="fade-right">
            <span class="section-tag">FAQ</span>
            <h2 class="section-title">Questions? <span class="gold-text">We Have Answers</span></h2>
            <div class="faq">
                @foreach([
                    ['Is the first consultation really free?', 'Yes. Your first consultation is free and completely confidential.'],
                    ['How quickly can you act in an urgent case?', 'Urgent bail, stay and injunction matters are usually filed the same day.'],
                    ['Can you make a rent agreement or affidavit the same day?', 'Yes. Most documentation work — affidavits, rent agreements, notary — is ready the same day.'],
                    ['How are your fees decided?', 'After reviewing your matter we share a written fee estimate — stage-wise or fixed.'],
                    ['Do you handle cases outside Delhi?', 'Yes, through our associate network in 12 states and before the Supreme Court.'],
                ] as $f)
                    <div class="faq-item">
                        <button class="faq-q">{{ $f[0] }} <i class="fa-solid fa-plus"></i></button>
                        <div class="faq-a"><p>{{ $f[1] }}</p></div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="h-consult glass" data-aos="fade-left">
            <h3>Book a <span class="gold-text">Free Consultation</span></h3>
            <p>A senior advocate will call you within 24 hours.</p>
            <form action="#" data-static-form>
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="tel" name="phone" placeholder="Phone Number" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <select name="subject">
                    <option value="">Select Practice Area</option>
                    @foreach($areas as $a)
                        <option>{{ $a['title'] }}</option>
                    @endforeach
                </select>
                <textarea name="message" rows="3" placeholder="Briefly describe your matter"></textarea>
                <button class="btn btn-gold btn-lg w-100">Request Call Back <i class="fa-solid fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
</section>

@endsection