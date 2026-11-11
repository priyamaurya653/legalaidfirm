@extends('layouts.app')
@section('title', 'About Firm')

@section('content')

@include('layouts.page-banner', [
    'title' => 'About <span class="gold-text">Our Firm</span>',
    'image' => 'https://images.unsplash.com/photo-1505664194779-8beaceb93744?w=1800&q=80',
])

{{-- Story --}}
<section class="section">
    <div class="container split">
        <div class="split-media" data-aos="fade-right">
            <div class="img-frame">
                <img src="https://images.unsplash.com/photo-1436450412740-6b988f486c6b?w=900&q=80" alt="Our chamber">
            </div>
            <div class="exp-badge glass"><strong>2005</strong><span>Established in<br>New Delhi</span></div>
        </div>
        <div class="split-content" data-aos="fade-left">
            <span class="section-tag">Our Story</span>
            <h2 class="section-title">Two Decades of <span class="gold-text">Fearless Advocacy</span></h2>
            <p>Lexora Legal was founded in 2005 by Senior Advocate Rajiv Malhotra with a simple belief — every person deserves honest legal advice and strong representation, regardless of how big or small their matter is.</p>
            <p>What started as a single chamber in Tis Hazari Courts has today grown into a full-service firm of 25+ advocates, associates and paralegals, appearing before District Courts, High Courts, NCLT, ITAT, Consumer Forums and the Supreme Court of India.</p>
            <p>We combine deep courtroom experience with modern case management, so our clients always know where their matter stands.</p>
        </div>
    </div>
</section>

{{-- Mission / Vision / Values --}}
<section class="section section-dark">
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <span class="section-tag">What Drives Us</span>
            <h2 class="section-title">Mission, Vision & <span class="gold-text">Values</span></h2>
        </div>
        <div class="mvv-grid">
            <div class="mvv-card glass tilt" data-aos="fade-up">
                <div class="pc-icon"><i class="fa-solid fa-bullseye"></i></div>
                <h3>Our Mission</h3>
                <p>To make quality legal services accessible, transparent and result-oriented for individuals and businesses across India.</p>
            </div>
            <div class="mvv-card glass tilt" data-aos="fade-up" data-aos-delay="120">
                <div class="pc-icon"><i class="fa-solid fa-eye"></i></div>
                <h3>Our Vision</h3>
                <p>To be India's most trusted law firm, known for integrity, courtroom excellence and genuine care for every client.</p>
            </div>
            <div class="mvv-card glass tilt" data-aos="fade-up" data-aos-delay="240">
                <div class="pc-icon"><i class="fa-solid fa-gem"></i></div>
                <h3>Our Values</h3>
                <p>Integrity, confidentiality, preparation and accountability — the four pillars behind every case we accept.</p>
            </div>
        </div>
    </div>
</section>

{{-- Journey timeline --}}
<section class="section">
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <span class="section-tag">Our Journey</span>
            <h2 class="section-title">Milestones That <span class="gold-text">Define Us</span></h2>
        </div>
        <div class="v-timeline">
            @foreach([
                ['2005', 'Foundation', 'Chamber started at Tis Hazari Courts with a focus on criminal defence.'],
                ['2010', 'Civil & Family Wing', 'Dedicated teams set up for property, civil and matrimonial disputes.'],
                ['2015', 'Corporate Practice', 'Corporate & commercial law division launched for start-ups and SMEs.'],
                ['2019', 'Tax Law Division', 'Specialised income tax and GST litigation team formed.'],
                ['2024', 'Pan-India Presence', 'Associate counsel network across 12 states and the Supreme Court.'],
            ] as $i => $m)
                <div class="vt-item {{ $i % 2 ? 'right' : 'left' }}" data-aos="{{ $i % 2 ? 'fade-left' : 'fade-right' }}">
                    <div class="vt-card glass">
                        <span class="vt-year">{{ $m[0] }}</span>
                        <h4>{{ $m[1] }}</h4>
                        <p>{{ $m[2] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Counters --}}
<section class="stats stats-solid">
    <div class="container stats-grid">
        <div class="stat glass" data-aos="fade-up"><h3><span class="counter" data-target="20">0</span>+</h3><p>Years</p></div>
        <div class="stat glass" data-aos="fade-up" data-aos-delay="100"><h3><span class="counter" data-target="3500">0</span>+</h3><p>Cases Won</p></div>
        <div class="stat glass" data-aos="fade-up" data-aos-delay="200"><h3><span class="counter" data-target="12">0</span></h3><p>States Covered</p></div>
        <div class="stat glass" data-aos="fade-up" data-aos-delay="300"><h3><span class="counter" data-target="25">0</span>+</h3><p>Advocates</p></div>
    </div>
</section>

{{-- Founder message --}}
<section class="section section-dark">
    <div class="container split">
        <div class="split-content" data-aos="fade-right">
            <span class="section-tag">Founder's Message</span>
            <h2 class="section-title">"Law is Not Just a Profession, <span class="gold-text">It's a Promise.</span>"</h2>
            <p>Every client who walks into our chamber is carrying a burden. Our job is to lift it — with preparation, honesty and courage. We never promise outcomes we cannot deliver, and we never stop fighting for the ones we can.</p>
            <div class="signature">
                <h4>Adv. Rajiv Malhotra</h4>
                <span>Founder & Senior Advocate</span>
            </div>
        </div>
        <div class="split-media" data-aos="fade-left">
            <div class="img-frame">
                <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=900&q=80" alt="Founder">
            </div>
        </div>
    </div>
</section>

@endsection