@extends('layouts.app')
@section('title', 'Practice Areas')
@php $areas = config('practice', []); @endphp

@section('content')

@include('layouts.page-banner', [
    'title' => 'Practice <span class="gold-text">Areas</span>',
    'image' => 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=1800&q=80',
])

<section class="section explore-section">
    <div class="ex-bg-lines"></div>
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <span class="section-tag">Our Expertise</span>
            <h2 class="section-title">Explore Our <span class="gold-text">Practice Areas</span></h2>
            <p>Every practice is led by advocates who focus only on that field — so you get depth, not guesswork.</p>
        </div>

        <div class="ex-filter" data-aos="fade-up">
            <button class="active" data-filter="all">All Services</button>
            <button data-filter="litigation"><i class="fa-solid fa-gavel"></i> Litigation</button>
            <button data-filter="services"><i class="fa-solid fa-file-signature"></i> Legal Services</button>
        </div>

        <div class="ex-grid" id="exGrid">
            @foreach($areas as $slug => $a)
                <div class="ex-item" data-group="{{ $a['group'] }}">
                    @include('layouts.explore-card', ['slug' => $slug, 'a' => $a, 'i' => $loop->index])
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Why our practice teams --}}
<section class="section section-dark">
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <span class="section-tag">Our Approach</span>
            <h2 class="section-title">One Firm. <span class="gold-text">Every Legal Need.</span></h2>
        </div>
        <div class="why-grid">
            @foreach([
                ['fa-solid fa-gavel', 'Litigation Team', 'Senior advocates for trial, appeal and High Court matters.'],
                ['fa-solid fa-file-signature', 'Documentation Desk', 'Agreements, wills, affidavits and notary — same-day service.'],
                ['fa-solid fa-headset', 'Client Support', 'A dedicated person tracks your matter and keeps you updated.'],
                ['fa-solid fa-laptop', 'Online Service', 'Share documents on WhatsApp / email — no need to visit.'],
            ] as $w)
                <div class="why-card" data-aos="flip-left" data-aos-delay="{{ $loop->index * 120 }}">
                    <div class="why-icon"><i class="{{ $w[0] }}"></i></div>
                    <h4>{{ $w[1] }}</h4>
                    <p>{{ $w[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection