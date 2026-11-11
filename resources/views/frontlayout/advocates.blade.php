@extends('frontlayout.app')
@section('title', 'Advocates')
@section('page_title', 'Advocates')

@section('page_actions')
    <button class="adm-btn adm-btn-gold" data-modal="advModal"><i class="fa-solid fa-user-plus"></i> Add Advocate</button>
@endsection

@section('content')

<div class="adv-grid">
    @foreach([
        ['Adv. Rajiv Malhotra', 'Founder & Senior Advocate', 'photo-1560250097-0b93528c311a', 24, 98, 'Supreme Court'],
        ['Adv. Neha Kapoor',    'Partner – Corporate Law',   'photo-1573496359142-b8d87734a5a2', 38, 93, 'Corporate / Tax'],
        ['Adv. Arjun Mehta',    'Partner – Criminal Law',    'photo-1507003211169-0a1dd7228f2d', 52, 96, 'Criminal'],
        ['Adv. Priya Sharma',   'Family & Civil Law',        'photo-1580489944761-15a19d654956', 41, 90, 'Family / Civil'],
    ] as $i => $a)
        <div class="glass-card adv-card" style="--i:{{ $i }}">
            <div class="adv-cover"></div>
            <img src="https://images.unsplash.com/{{ $a[2] }}?w=200&q=80" alt="{{ $a[0] }}">
            <h5>{{ $a[0] }}</h5>
            <small>{{ $a[1] }}</small>
            <span class="tag">{{ $a[5] }}</span>
            <div class="adv-stats">
                <div><b>{{ $a[3] }}</b><small>Active Cases</small></div>
                <div><b class="gold">{{ $a[4] }}%</b><small>Win Rate</small></div>
            </div>
            <div class="adv-actions">
                <a href="#" class="adm-btn adm-btn-ghost sm w-100">Profile</a>
                <a href="#" class="adm-btn adm-btn-gold sm w-100">Assign Case</a>
            </div>
        </div>
    @endforeach
</div>

@endsection

@push('modals')
<div class="adm-modal" id="advModal" aria-hidden="true">
    <div class="adm-modal-box glass-card">
        <div class="modal-head">
            <h4><i class="fa-solid fa-user-tie"></i> Add Advocate</h4>
            <button class="icon-btn" data-close-modal aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form class="adm-form" data-static-form>
            <div class="f-grid">
                <div class="f-group"><label>Full Name</label><input type="text" required></div>
                <div class="f-group"><label>Designation</label><input type="text"></div>
                <div class="f-group"><label>Enrollment No.</label><input type="text"></div>
                <div class="f-group"><label>Specialisation</label><input type="text"></div>
                <div class="f-group"><label>Email</label><input type="email"></div>
                <div class="f-group"><label>Phone</label><input type="tel"></div>
            </div>
            <div class="modal-foot">
                <button type="button" class="adm-btn adm-btn-ghost" data-close-modal>Cancel</button>
                <button class="adm-btn adm-btn-gold"><i class="fa-solid fa-check"></i> Save</button>
            </div>
        </form>
    </div>
</div>
@endpush