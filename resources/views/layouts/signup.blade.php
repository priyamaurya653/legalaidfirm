@extends('layouts.app')
@section('title', 'Admin Sign Up')

{{-- =====================================================================
     ADMIN (ADVOCATE) SIGN UP – CSS + JS + API sab isi file me
     API : POST /api/auth/user-register  (multipart) – token nahi, signup ke baad login page
     Save: users (role = admin) + user_profiles  → login ke baad /admin dashboard
     ===================================================================== --}}

@php
    $genders   = config('advocate.genders', ['Male', 'Female', 'Other']);
    $states    = config('advocate.states', []);
    $practice  = array_column(config('practice', []), 'title');
    $heardFrom = ['Google Search', 'Friend / Family', 'Social Media', 'Advertisement', 'Advocate Referral', 'Other'];
@endphp

@push('styles')
<style>
    /* ---------- page ---------- */
    .su-page { position: relative; min-height: calc(100vh - var(--header-h, 80px)); padding: 60px 0 80px; overflow: hidden; background: #050505; }
    .su-bg { position: absolute; inset: 0; background: url('https://images.unsplash.com/photo-1505664194779-8beaceb93744?w=1800&q=80') center/cover; transform: scale(1.1); animation: suZoom 26s ease-in-out infinite alternate; }
    @keyframes suZoom { to { transform: scale(1.22); } }
    .su-shade { position: absolute; inset: 0; background: radial-gradient(ellipse 60% 50% at 50% 0%, rgba(212,175,55,.16), transparent 60%), linear-gradient(180deg, rgba(5,5,5,.9), rgba(5,5,5,.97)); }
    .su-orb { position: absolute; border-radius: 50%; filter: blur(90px); pointer-events: none; }
    .su-orb.o1 { width: 380px; height: 380px; left: -100px; top: 10%; background: rgba(212,175,55,.18); animation: suOrb 13s ease-in-out infinite alternate; }
    .su-orb.o2 { width: 320px; height: 320px; right: -80px; bottom: 5%; background: rgba(62,207,142,.08); animation: suOrb 15s ease-in-out infinite alternate-reverse; }
    @keyframes suOrb { to { transform: translate(50px, -40px) scale(1.2); } }
    .su-container { position: relative; z-index: 2; width: 100%; max-width: 900px; margin: 0 auto; padding: 0 20px; }

    /* ---------- heading ---------- */
    .su-head { text-align: center; margin-bottom: 30px; }
    .su-eyebrow { display: inline-flex; align-items: center; gap: 8px; padding: 7px 16px; border-radius: 50px; border: 1px solid rgba(91,141,239,.5); background: rgba(91,141,239,.1); color: #8fb4ff; font-size: 13px; letter-spacing: 1px; margin-bottom: 16px; }
    .su-head h1 { font-family: 'Cormorant Garamond', serif; font-size: clamp(36px, 5vw, 58px); line-height: 1.08; color: #fff; }
    .su-gold { background: linear-gradient(110deg, #a8841f, #f5d77a 25%, #fff6d5 35%, #d4af37 45%, #a8841f 60%, #f5d77a 80%); background-size: 250% 100%; -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; animation: suShimmer 4s linear infinite; }
    @keyframes suShimmer { to { background-position: -250% 0; } }
    .su-head p { color: #a9a9b1; max-width: 560px; margin: 10px auto 0; }

    /* ---------- steps ---------- */
    .su-steps { position: relative; display: grid; grid-template-columns: repeat(3, 1fr); max-width: 620px; margin: 0 auto 26px; }
    .su-line { position: absolute; top: 25px; left: 16.66%; right: 16.66%; height: 2px; background: rgba(255,255,255,.08); border-radius: 2px; }
    .su-line i { display: block; height: 100%; width: 0; border-radius: 2px; background: linear-gradient(90deg, #f5d77a, #d4af37); box-shadow: 0 0 12px #d4af37; transition: width .6s cubic-bezier(.2,.8,.2,1); }
    .su-step { position: relative; z-index: 1; display: flex; flex-direction: column; align-items: center; gap: 10px; cursor: pointer; background: none; border: 0; font-family: inherit; }
    .su-step span { width: 52px; height: 52px; border-radius: 50%; display: grid; place-items: center; font-size: 18px; color: #8d8d97; background: #0e0e11; border: 1px solid rgba(255,255,255,.1); transition: .45s cubic-bezier(.34,1.56,.64,1); }
    .su-step small { font-size: 13px; color: #8d8d97; transition: .3s; }
    .su-step.active span { color: #111; background: linear-gradient(135deg, #f8e08e, #d4af37 45%, #a8841f); border-color: transparent; transform: scale(1.12); box-shadow: 0 0 0 6px rgba(212,175,55,.15), 0 10px 30px rgba(212,175,55,.4); }
    .su-step.active small { color: #f5d77a; font-weight: 500; }
    .su-step.done span { color: #3ecf8e; border-color: rgba(62,207,142,.6); background: rgba(62,207,142,.1); }

    /* ---------- card ---------- */
    .su-card { position: relative; padding: 38px 40px 32px; border-radius: 30px; overflow: hidden;
        background: linear-gradient(150deg, rgba(255,255,255,.09), rgba(255,255,255,.02) 50%, rgba(255,255,255,.04));
        border: 1px solid rgba(212,175,55,.28); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        box-shadow: 0 40px 110px rgba(0,0,0,.8), 0 0 70px rgba(212,175,55,.08), inset 0 1px 0 rgba(255,255,255,.15); }
    .su-card::before { content: ''; position: absolute; inset: 0 0 70% 0; pointer-events: none; background: linear-gradient(180deg, rgba(255,255,255,.07), transparent); }
    .su-card::after { content: ''; position: absolute; inset: 0; border-radius: inherit; padding: 1.5px; pointer-events: none;
        background: conic-gradient(from var(--suAng, 0deg), transparent 0 65%, #f5d77a 78%, #d4af37 88%, transparent);
        -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite: xor;
        mask: linear-gradient(#000 0 0) content-box exclude, linear-gradient(#000 0 0); animation: suSpin 6s linear infinite; }
    @property --suAng { syntax: '<angle>'; initial-value: 0deg; inherits: false; }
    @keyframes suSpin { to { --suAng: 360deg; } }
    .su-card > * { position: relative; z-index: 1; }
    .su-card.shake { animation: suShake .45s ease; }
    @keyframes suShake { 20%, 60% { transform: translateX(-8px); } 40%, 80% { transform: translateX(8px); } }

    .su-alert { display: flex; gap: 10px; align-items: flex-start; padding: 13px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; animation: suIn .35s ease; }
    .su-alert[hidden] { display: none; }
    .su-alert i { margin-top: 3px; }
    .su-alert.error { background: rgba(255,107,107,.1); border: 1px solid rgba(255,107,107,.4); color: #ffa3a3; }
    .su-alert.success { background: rgba(62,207,142,.1); border: 1px solid rgba(62,207,142,.4); color: #7fe0a6; }
    @keyframes suIn { from { opacity: 0; transform: translateY(-6px); } }

    /* section */
    .su-section { display: none; }
    .su-section.active { display: block; animation: suStep .55s cubic-bezier(.2,.8,.2,1) both; }
    @keyframes suStep { from { opacity: 0; transform: translateX(30px); } }
    .su-sec-head { display: flex; align-items: center; gap: 14px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid rgba(255,255,255,.08); }
    .su-sec-num { font-family: 'Cormorant Garamond', serif; font-size: 42px; font-weight: 700; line-height: 1; color: transparent; -webkit-text-stroke: 1px rgba(212,175,55,.75); }
    .su-sec-head h3 { font-family: 'Cormorant Garamond', serif; font-size: 27px; color: #fff; line-height: 1.1; }
    .su-sec-head small { color: #8d8d97; font-size: 13px; }

    /* grid + fields */
    .su-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
    .su-full { grid-column: 1 / -1; }
    .su-field { position: relative; min-width: 0; }
    .su-field > label { display: block; font-size: 13.5px; color: #c9c9cf; margin-bottom: 7px; font-weight: 500; }
    .su-field > label b { color: #d4af37; }
    .su-field > label small { color: #77777f; font-weight: 400; }
    .su-in { position: relative; }
    .su-in > i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #d4af37; font-size: 14px; pointer-events: none; transition: .3s; }
    .su-in.top > i { top: 18px; transform: none; }
    .su-page .su-in input, .su-page .su-in select, .su-page .su-in textarea {
        width: 100%; height: 50px; padding: 0 16px 0 44px; border-radius: 13px; border: 1px solid rgba(255,255,255,.1); background: rgba(0,0,0,.45);
        color: #fff; font-family: inherit; font-size: 14.5px; outline: none; transition: .3s; color-scheme: dark; appearance: none; -webkit-appearance: none; }
    .su-page .su-in textarea { height: auto; min-height: 90px; padding: 14px 16px 14px 44px; resize: vertical; }
    .su-page .su-in select { padding-right: 40px; background-image: linear-gradient(45deg, transparent 50%, #d4af37 50%), linear-gradient(135deg, #d4af37 50%, transparent 50%); background-position: calc(100% - 20px) 50%, calc(100% - 15px) 50%; background-size: 5px 5px; background-repeat: no-repeat; }
    .su-page .su-in select option { background: #111; color: #fff; }
    .su-page .su-in input::placeholder, .su-page .su-in textarea::placeholder { color: #66666e; }
    .su-page .su-in input:focus, .su-page .su-in select:focus, .su-page .su-in textarea:focus { border-color: #d4af37; box-shadow: 0 0 0 4px rgba(212,175,55,.12); background-color: rgba(0,0,0,.6); }
    .su-in:focus-within > i { color: #f5d77a; }
    .su-pw input { padding-right: 50px !important; }
    .su-eye { position: absolute; right: 7px; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; border-radius: 10px; border: 0; background: none; color: #8d8d97; cursor: pointer; }
    .su-eye:hover { color: #d4af37; background: rgba(212,175,55,.08); }

    .su-field.err input, .su-field.err select, .su-field.err textarea, .su-field.err .su-gender { border-color: #ff6b6b !important; box-shadow: 0 0 0 3px rgba(255,107,107,.12) !important; }
    .su-err { display: block; margin-top: 6px; font-size: 12.5px; color: #ff8c8c; animation: suIn .3s ease; }

    /* photo */
    .su-photo { display: flex; align-items: center; gap: 20px; padding: 16px; border-radius: 18px; border: 1px dashed rgba(212,175,55,.35); background: rgba(212,175,55,.03); margin-bottom: 22px; }
    .su-avatar { position: relative; width: 92px; height: 92px; flex-shrink: 0; border-radius: 24px; overflow: hidden; display: grid; place-items: center; font-size: 32px; color: #d4af37;
        background: radial-gradient(circle at 30% 20%, rgba(212,175,55,.22), rgba(0,0,0,.45)); border: 1px solid rgba(212,175,55,.45); }
    .su-avatar img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; animation: suPop .45s cubic-bezier(.34,1.56,.64,1); }
    @keyframes suPop { from { transform: scale(.6); opacity: 0; } }
    .su-photo b { display: block; color: #fff; font-size: 15px; }
    .su-photo small { color: #8d8d97; font-size: 12.5px; }
    .su-upbtn { display: inline-flex; align-items: center; gap: 8px; margin-top: 10px; padding: 9px 16px; border-radius: 50px; cursor: pointer; font-size: 13.5px; color: #f5d77a; border: 1px solid rgba(212,175,55,.5); background: rgba(212,175,55,.06); transition: .3s; }
    .su-upbtn:hover { background: linear-gradient(135deg, #f8e08e, #d4af37); color: #111; }
    .su-rm { margin-left: 8px; font-size: 12.5px; color: #ff8c8c; background: none; border: 0; cursor: pointer; }

    /* gender pills */
    .su-gender { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; padding: 4px; border-radius: 14px; border: 1px solid rgba(255,255,255,.1); background: rgba(0,0,0,.35); }
    .su-gender label { position: relative; cursor: pointer; }
    .su-page .su-gender input { position: absolute; opacity: 0; width: 1px; height: 1px; pointer-events: none; }
    .su-gender span { display: flex; align-items: center; justify-content: center; gap: 7px; height: 40px; border-radius: 10px; font-size: 13.5px; color: #aaa; transition: .3s; }
    .su-gender input:checked + span { color: #111; font-weight: 600; background: linear-gradient(135deg, #f8e08e, #d4af37); box-shadow: 0 6px 16px rgba(212,175,55,.3); }
    .su-gender label:hover span { color: #fff; }
    .su-gender input:checked + span:hover { color: #111; }

    /* password meter + rules */
    .su-meter { display: grid; grid-template-columns: repeat(4, 1fr); gap: 5px; margin-top: 9px; }
    .su-meter i { height: 4px; border-radius: 4px; background: rgba(255,255,255,.08); transition: .35s; }
    .su-meter[data-l="1"] i:nth-child(-n+1) { background: #ff6b6b; }
    .su-meter[data-l="2"] i:nth-child(-n+2) { background: #f5a623; }
    .su-meter[data-l="3"] i:nth-child(-n+3) { background: #d4af37; }
    .su-meter[data-l="4"] i { background: #3ecf8e; }
    .su-rules { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 14px; margin-top: 10px; }
    .su-rules li { list-style: none; font-size: 12.5px; color: #77777f; display: flex; align-items: center; gap: 7px; transition: .3s; }
    .su-rules li i { font-size: 11px; }
    .su-rules li.ok { color: #7fe0a6; }
    .su-match { display: none; margin-top: 6px; font-size: 12.5px; }
    .su-match.show { display: block; }
    .su-match.ok { color: #7fe0a6; }
    .su-match.no { color: #ff8c8c; }

    /* checkbox */
    .su-check { position: relative; display: flex; align-items: flex-start; gap: 12px; cursor: pointer; font-size: 13.5px; color: #bbb; line-height: 1.55; }
    .su-page .su-check input { position: absolute; opacity: 0; width: 1px; height: 1px; pointer-events: none; }
    .su-box { width: 22px; height: 22px; flex-shrink: 0; border-radius: 7px; display: grid; place-items: center; border: 1px solid rgba(212,175,55,.5); background: rgba(0,0,0,.4); transition: .25s; }
    .su-box i { font-size: 11px; color: #111; transform: scale(0); transition: .25s cubic-bezier(.34,1.56,.64,1); }
    .su-check input:checked + .su-box { background: linear-gradient(135deg, #f8e08e, #d4af37); border-color: transparent; box-shadow: 0 0 12px rgba(212,175,55,.4); }
    .su-check input:checked + .su-box i { transform: scale(1); }
    .su-check a { color: #f5d77a; }
    .su-field.err .su-box { border-color: #ff6b6b; }

    /* summary box (step 3) */
    .su-summary { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 22px; }
    .su-summary div { padding: 11px 13px; border-radius: 12px; background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.07); min-width: 0; }
    .su-summary small { display: block; font-size: 11.5px; color: #77777f; }
    .su-summary b { display: block; font-size: 13.5px; color: #fff; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    /* nav */
    .su-nav { display: flex; align-items: center; gap: 12px; margin-top: 28px; padding-top: 22px; border-top: 1px solid rgba(255,255,255,.08); }
    .su-count { margin: 0 auto; font-size: 13px; color: #8d8d97; }
    .su-btn { position: relative; display: inline-flex; align-items: center; justify-content: center; gap: 9px; height: 50px; padding: 0 28px; border-radius: 50px; cursor: pointer; overflow: hidden; font-family: inherit; font-size: 15px; font-weight: 600; transition: .35s; border: 0; }
    .su-btn[hidden] { display: none; }
    .su-gold-btn { color: #111; background: linear-gradient(135deg, #f8e08e 0%, #d4af37 45%, #a8841f 100%); box-shadow: 0 12px 28px rgba(212,175,55,.35), inset 0 1px 0 rgba(255,255,255,.6); }
    .su-gold-btn::before { content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%; background: linear-gradient(120deg, transparent, rgba(255,255,255,.7), transparent); transform: skewX(-25deg); animation: suBtn 3.2s infinite; }
    @keyframes suBtn { 0% { left: -100%; } 40%, 100% { left: 160%; } }
    .su-gold-btn:hover { transform: translateY(-3px); box-shadow: 0 18px 38px rgba(212,175,55,.5); }
    .su-ghost-btn { color: #f5d77a; background: rgba(212,175,55,.05); border: 1px solid rgba(212,175,55,.5); }
    .su-ghost-btn:hover { background: rgba(212,175,55,.14); }
    .su-btn:disabled { pointer-events: none; opacity: .85; }
    .su-spin { display: none; width: 18px; height: 18px; border-radius: 50%; border: 2px solid rgba(0,0,0,.25); border-top-color: #111; animation: suRot .7s linear infinite; }
    .su-btn.loading .su-spin { display: block; }
    .su-btn.loading .su-arrow { display: none; }
    @keyframes suRot { to { transform: rotate(360deg); } }

    .su-foot { text-align: center; margin-top: 22px; color: #8d8d97; font-size: 14px; }
    .su-foot a { color: #f5d77a; font-weight: 500; }

    /* success popup */
    .su-pop { position: fixed; inset: 0; z-index: 6000; display: none; align-items: center; justify-content: center; padding: 16px; background: rgba(0,0,0,.82); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); opacity: 0; transition: opacity .4s; }
    .su-pop.open { display: flex; }
    .su-pop.show { opacity: 1; }
    .su-pop-box { width: 100%; max-width: 420px; padding: 44px 28px 30px; text-align: center; border-radius: 28px; background: linear-gradient(160deg, rgba(20,34,27,.97), rgba(10,10,12,.98)); border: 1px solid rgba(62,207,142,.45);
        box-shadow: 0 40px 120px rgba(0,0,0,.9), 0 0 80px rgba(62,207,142,.15); transform: translateY(40px) scale(.9); transition: transform .6s cubic-bezier(.34,1.56,.64,1); }
    .su-pop.show .su-pop-box { transform: none; }
    .su-pop-ic { width: 96px; height: 96px; margin: 0 auto 18px; border-radius: 50%; display: grid; place-items: center; font-size: 40px; color: #fff; background: linear-gradient(135deg, #7aa5ff, #3f6fd8);
        box-shadow: 0 0 0 10px rgba(91,141,239,.14), 0 0 0 20px rgba(91,141,239,.05), 0 18px 40px rgba(91,141,239,.4); }
    .su-pop.show .su-pop-ic { animation: suPopIc .8s cubic-bezier(.34,1.56,.64,1) both .15s; }
    @keyframes suPopIc { from { transform: scale(0) rotate(-120deg); } }
    .su-pop small { display: block; color: #8d8d97; font-size: 13px; margin-bottom: 10px; }
    .su-badge { display: inline-block; padding: 6px 18px; border-radius: 50px; font-size: 13px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: #fff; background: linear-gradient(135deg, #7aa5ff, #3f6fd8); }
    .su-pop h3 { font-family: 'Cormorant Garamond', serif; font-size: 32px; color: #fff; margin: 14px 0 2px; overflow-wrap: anywhere; }
    .su-pop p { color: #aaa; font-size: 14px; overflow-wrap: anywhere; }
    .su-bar { height: 4px; border-radius: 4px; margin: 24px 0 10px; background: rgba(255,255,255,.08); overflow: hidden; }
    .su-bar i { display: block; height: 100%; width: 0; background: linear-gradient(90deg, #7ff0b8, #2fbf7d); }
    .su-pop.show .su-bar i { animation: suFill 2s linear forwards .1s; }
    @keyframes suFill { to { width: 100%; } }

    /* responsive */
    @media (max-width: 767px) {
        .su-card { padding: 28px 20px 24px; border-radius: 24px; }
        .su-grid { grid-template-columns: 1fr; gap: 16px; }
        .su-summary { grid-template-columns: 1fr 1fr; }
        .su-rules { grid-template-columns: 1fr; }
    }
    @media (max-width: 575px) {
        .su-page { padding: 36px 0 60px; }
        .su-step span { width: 44px; height: 44px; font-size: 15px; }
        .su-line { top: 21px; }
        .su-step small { font-size: 11.5px; }
        .su-photo { flex-direction: column; text-align: center; }
        .su-sec-num { font-size: 34px; }
        .su-sec-head h3 { font-size: 23px; }
        .su-nav { flex-wrap: wrap; }
        .su-count { order: -1; width: 100%; text-align: center; }
        .su-nav .su-btn { flex: 1; padding: 0 16px; }
        .su-summary { grid-template-columns: 1fr; }
    }
    @media (prefers-reduced-motion: reduce) { .su-page *, .su-pop * { animation: none !important; transition: none !important; } }
</style>
@endpush

@section('content')

<section class="su-page">
    <div class="su-bg"></div>
    <div class="su-shade"></div>
    <div class="su-orb o1"></div>
    <div class="su-orb o2"></div>

    <div class="su-container">

        <div class="su-head" data-aos="fade-up">
            <span class="su-eyebrow"><i class="fa-solid fa-user-tie"></i> Role: Admin (Advocate)</span>
            <h1>Create Your <span class="su-gold">Admin Account</span></h1>
            <p>Register in 3 quick steps. Your account is created as an <b>Admin</b>. After sign up, log in with your email & password to open your dashboard.</p>
        </div>

        {{-- steps --}}
        <div class="su-steps" id="suSteps" data-aos="fade-up">
            <div class="su-line"><i></i></div>
            <button type="button" class="su-step active" data-go="0"><span><i class="fa-solid fa-user"></i></span><small>Personal</small></button>
            <button type="button" class="su-step" data-go="1"><span><i class="fa-solid fa-location-dot"></i></span><small>Contact & Address</small></button>
            <button type="button" class="su-step" data-go="2"><span><i class="fa-solid fa-lock"></i></span><small>Account</small></button>
        </div>

        <div class="su-card" id="suCard" data-aos="fade-up" data-aos-delay="100">
            <div class="su-alert" id="suAlert" hidden></div>

            <form id="suForm" novalidate enctype="multipart/form-data" autocomplete="on">

                {{-- ================= STEP 1: PERSONAL ================= --}}
                <div class="su-section active" data-step="0">
                    <div class="su-sec-head">
                        <span class="su-sec-num">01</span>
                        <div><h3>Personal Details</h3><small>Tell us a little about yourself</small></div>
                    </div>

                    <div class="su-photo su-field" data-field="photo">
                        <div class="su-avatar" id="suAvatar"><i class="fa-solid fa-user"></i></div>
                        <div>
                            <b>Profile Photo <small style="color:#77777f;font-weight:400">(optional)</small></b>
                            <small>JPG, PNG or WEBP · max 2MB</small><br>
                            <label class="su-upbtn"><i class="fa-solid fa-camera"></i> Choose Photo
                                <input type="file" name="photo" id="suPhoto" accept="image/png,image/jpeg,image/webp" hidden>
                            </label>
                            <button type="button" class="su-rm" id="suPhotoRm" hidden><i class="fa-solid fa-xmark"></i> Remove</button>
                        </div>
                    </div>

                    <div class="su-grid">
                        <div class="su-field su-full" data-field="name">
                            <label>Full Name <b>*</b></label>
                            <div class="su-in"><input type="text" name="name" required minlength="3" maxlength="100" placeholder="e.g. Rahul Sharma" autocomplete="name"><i class="fa-regular fa-user"></i></div>
                        </div>
                        <div class="su-field su-full" data-field="gender">
                            <label>Gender <b>*</b></label>
                            <div class="su-gender">
                                @foreach($genders as $g)
                                    <label>
                                        <input type="radio" name="gender" value="{{ $g }}" required>
                                        <span><i class="fa-solid {{ ['Male' => 'fa-mars', 'Female' => 'fa-venus'][$g] ?? 'fa-genderless' }}"></i> {{ $g }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="su-field" data-field="dob">
                            <label>Date of Birth <b>*</b> <small>(18+)</small></label>
                            <div class="su-in"><input type="date" name="dob" required max="{{ now()->subYears(18)->format('Y-m-d') }}" min="1920-01-02"><i class="fa-regular fa-calendar"></i></div>
                        </div>
                        <div class="su-field" data-field="occupation">
                            <label>Occupation <small>(optional)</small></label>
                            <div class="su-in"><input type="text" name="occupation" maxlength="100" placeholder="e.g. Business, Salaried, Student"><i class="fa-solid fa-briefcase"></i></div>
                        </div>
                    </div>
                </div>

                {{-- ================= STEP 2: CONTACT & ADDRESS ================= --}}
                <div class="su-section" data-step="1">
                    <div class="su-sec-head">
                        <span class="su-sec-num">02</span>
                        <div><h3>Contact & Address</h3><small>So our advocates can reach you</small></div>
                    </div>

                    <div class="su-grid">
                        <div class="su-field su-full" data-field="email">
                            <label>Email Address <b>*</b></label>
                            <div class="su-in"><input type="email" name="email" required maxlength="150" placeholder="you@example.com" autocomplete="email"><i class="fa-regular fa-envelope"></i></div>
                        </div>
                        <div class="su-field" data-field="phone">
                            <label>Mobile Number <b>*</b></label>
                            <div class="su-in"><input type="tel" name="phone" required inputmode="numeric" maxlength="14" placeholder="10-digit mobile" autocomplete="tel"><i class="fa-solid fa-mobile-screen"></i></div>
                        </div>
                        <div class="su-field" data-field="alt_phone">
                            <label>Alternate Mobile <small>(optional)</small></label>
                            <div class="su-in"><input type="tel" name="alt_phone" inputmode="numeric" maxlength="14" placeholder="10-digit mobile"><i class="fa-solid fa-phone"></i></div>
                        </div>
                        <div class="su-field su-full" data-field="address">
                            <label>Full Address <b>*</b></label>
                            <div class="su-in top"><textarea name="address" required maxlength="255" rows="2" placeholder="House no., street, area / locality" autocomplete="street-address"></textarea><i class="fa-solid fa-house"></i></div>
                        </div>
                        <div class="su-field" data-field="city">
                            <label>City <b>*</b></label>
                            <div class="su-in"><input type="text" name="city" required maxlength="80" placeholder="e.g. New Delhi" autocomplete="address-level2"><i class="fa-solid fa-city"></i></div>
                        </div>
                        <div class="su-field" data-field="state">
                            <label>State / UT <b>*</b></label>
                            <div class="su-in">
                                <select name="state" required>
                                    <option value="">Select State</option>
                                    @foreach($states as $s)
                                        <option value="{{ $s }}">{{ $s }}</option>
                                    @endforeach
                                </select>
                                <i class="fa-solid fa-map"></i>
                            </div>
                        </div>
                        <div class="su-field" data-field="pincode">
                            <label>Pincode <b>*</b></label>
                            <div class="su-in"><input type="text" name="pincode" required inputmode="numeric" maxlength="6" placeholder="6 digits" autocomplete="postal-code"><i class="fa-solid fa-location-dot"></i></div>
                        </div>
                        <div class="su-field" data-field="interested_in">
                            <label>Need Help With <small>(optional)</small></label>
                            <div class="su-in">
                                <select name="interested_in">
                                    <option value="">Select practice area</option>
                                    @foreach($practice as $p)
                                        <option value="{{ $p }}">{{ $p }}</option>
                                    @endforeach
                                </select>
                                <i class="fa-solid fa-scale-balanced"></i>
                            </div>
                        </div>
                        <div class="su-field su-full" data-field="heard_from">
                            <label>How did you hear about us? <small>(optional)</small></label>
                            <div class="su-in">
                                <select name="heard_from">
                                    <option value="">Select</option>
                                    @foreach($heardFrom as $h)
                                        <option value="{{ $h }}">{{ $h }}</option>
                                    @endforeach
                                </select>
                                <i class="fa-solid fa-bullhorn"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= STEP 3: ACCOUNT ================= --}}
                <div class="su-section" data-step="2">
                    <div class="su-sec-head">
                        <span class="su-sec-num">03</span>
                        <div><h3>Login & Account</h3><small>Review your details and set a password</small></div>
                    </div>

                    <div class="su-summary" id="suSummary"></div>

                    <div class="su-grid">
                        <div class="su-field" data-field="password">
                            <label>Password <b>*</b></label>
                            <div class="su-in su-pw">
                                <input type="password" name="password" id="suPass" required minlength="8" placeholder="Create password" autocomplete="new-password">
                                <i class="fa-solid fa-key"></i>
                                <button type="button" class="su-eye" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
                            </div>
                            <div class="su-meter" id="suMeter"><i></i><i></i><i></i><i></i></div>
                        </div>
                        <div class="su-field" data-field="password_confirmation">
                            <label>Confirm Password <b>*</b></label>
                            <div class="su-in su-pw">
                                <input type="password" name="password_confirmation" id="suPass2" required minlength="8" placeholder="Re-enter password" autocomplete="new-password">
                                <i class="fa-solid fa-key"></i>
                                <button type="button" class="su-eye" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
                            </div>
                            <small class="su-match" id="suMatch"></small>
                        </div>
                        <ul class="su-rules su-full" id="suRules">
                            <li data-r="len"><i class="fa-solid fa-circle"></i> At least 8 characters</li>
                            <li data-r="upper"><i class="fa-solid fa-circle"></i> One uppercase letter</li>
                            <li data-r="lower"><i class="fa-solid fa-circle"></i> One lowercase letter</li>
                            <li data-r="num"><i class="fa-solid fa-circle"></i> One number</li>
                        </ul>
                        <div class="su-field su-full" data-field="terms">
                            <label class="su-check">
                                <input type="checkbox" name="terms" value="1" required>
                                <span class="su-box"><i class="fa-solid fa-check"></i></span>
                                <span>I confirm the above details are correct and I agree to the <a href="#">Terms of Use</a> & <a href="#">Privacy Policy</a>. I understand this website does not create an advocate–client relationship.</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- nav --}}
                <div class="su-nav">
                    <button type="button" class="su-btn su-ghost-btn" id="suPrev" hidden><i class="fa-solid fa-arrow-left"></i> Back</button>
                    <span class="su-count" id="suCount">Step 1 of 3</span>
                    <button type="button" class="su-btn su-gold-btn" id="suNext">Next <i class="fa-solid fa-arrow-right"></i></button>
                    <button type="submit" class="su-btn su-gold-btn" id="suSubmit" hidden>
                        <span class="su-btn-text">Create Account</span>
                        <i class="fa-solid fa-user-check su-arrow"></i>
                        <span class="su-spin"></span>
                    </button>
                </div>
            </form>
        </div>

        <p class="su-foot">Already have an account? <a href="{{ route('login') }}">Login</a> &nbsp;·&nbsp; Are you an advocate? <a href="{{ route('register') }}">Register here</a></p>
    </div>
</section>

{{-- success popup --}}
<div class="su-pop" id="suPop" aria-live="polite">
    <div class="su-pop-box">
        <div class="su-pop-ic"><i class="fa-solid fa-user-tie"></i></div>
        <small id="suPopSmall">Account created! You are logged in as</small>
        <span class="su-badge" id="suPopRole">Admin (Advocate)</span>
        <h3 id="suPopName">—</h3>
        <p id="suPopEmail">—</p>
        <div class="su-bar"><i></i></div>
        <small>Redirecting to login…</small>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /* ================= CONFIG ================= */
    const API_BASE  = @json(url('/api'));
    const TOKEN_KEY = 'lx_token';
    const USER_KEY  = 'lx_user';
    const USER_HOME = @json(route('admin.dashboard'));   // signup = admin

    /* ================= ELEMENTS ================= */
    const form    = document.getElementById('suForm');
    if (!form) return;
    const card    = document.getElementById('suCard');
    const alertB  = document.getElementById('suAlert');
    const steps   = [...form.querySelectorAll('.su-section')];
    const dots    = [...document.querySelectorAll('#suSteps .su-step')];
    const line    = document.querySelector('#suSteps .su-line i');
    const prevBtn = document.getElementById('suPrev');
    const nextBtn = document.getElementById('suNext');
    const subBtn  = document.getElementById('suSubmit');
    const counter = document.getElementById('suCount');
    let cur = 0;

    /* ================= HELPERS ================= */
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const digits = v => String(v || '').replace(/\D/g, '').slice(-10);

    const saveAuth = (token, user) => {
        try {
            [localStorage, sessionStorage].forEach(s => { s.removeItem(TOKEN_KEY); s.removeItem(USER_KEY); });
            localStorage.setItem(TOKEN_KEY, token);
            localStorage.setItem(USER_KEY, JSON.stringify(user));
        } catch (e) {}
    };

    /** API call – FormData (multipart, photo ke saath) */
    async function apiForm(path, fd) {
        let res;
        try {
            res = await fetch(API_BASE + path, { method: 'POST', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: fd });
        } catch (e) {
            throw { status: 0, message: 'Network error – please check your internet connection.', errors: {} };
        }
        let json = {};
        try { json = await res.json(); } catch (e) {}
        if (!res.ok) {
            let msg = json.message || ('Something went wrong (' + res.status + ').');
            if (res.status === 429) msg = 'Too many attempts. Please wait a minute and try again.';
            if (res.status === 413) msg = 'Photo is too large. Please upload a smaller image.';
            throw { status: res.status, message: msg, errors: json.errors || {} };
        }
        return json;
    }

    const showAlert = (type, msg) => {
        alertB.className = 'su-alert ' + type;
        alertB.innerHTML = '<i class="fa-solid ' + (type === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check') + '"></i><span>' + esc(msg) + '</span>';
        alertB.hidden = false;
    };
    const clearErrors = (scope = form) => {
        scope.querySelectorAll('.su-field.err').forEach(f => f.classList.remove('err'));
        scope.querySelectorAll('.su-err').forEach(e => e.remove());
    };
    const fieldError = (name, msg) => {
        const f = form.querySelector('[data-field="' + name + '"]');
        if (!f) return null;
        f.classList.add('err');
        f.querySelector('.su-err')?.remove();
        const s = document.createElement('small');
        s.className = 'su-err';
        s.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> ' + esc(msg);
        f.appendChild(s);
        return f;
    };
    const shake = () => { card.classList.remove('shake'); void card.offsetWidth; card.classList.add('shake'); };
    const val = n => { const el = form.elements[n]; return el ? String(el.value || '').trim() : ''; };

    /* ================= STEPS ================= */
    function show(i, scroll = true) {
        cur = i;
        steps.forEach((s, n) => s.classList.toggle('active', n === i));
        dots.forEach((d, n) => { d.classList.toggle('active', n === i); d.classList.toggle('done', n < i); });
        line.style.width = (i / (steps.length - 1)) * 100 + '%';
        prevBtn.hidden = i === 0;
        nextBtn.hidden = i === steps.length - 1;
        subBtn.hidden  = i !== steps.length - 1;
        counter.textContent = 'Step ' + (i + 1) + ' of ' + steps.length;
        if (i === steps.length - 1) buildSummary();
        if (scroll) document.getElementById('suSteps').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    /** Client side validation – har step ke liye */
    function validate(i) {
        clearErrors(steps[i]);
        const e = {};
        if (i === 0) {
            if (val('name').length < 3) e.name = 'Please enter your full name (min 3 letters).';
            if (!form.querySelector('input[name="gender"]:checked')) e.gender = 'Please select your gender.';
            const dob = val('dob');
            if (!dob) e.dob = 'Please select your date of birth.';
            else {
                const d = new Date(dob), lim = new Date(); lim.setFullYear(lim.getFullYear() - 18);
                if (d > lim) e.dob = 'You must be at least 18 years old.';
            }
            const ph = form.elements.photo.files[0];
            if (ph && ph.size > 2 * 1024 * 1024) e.photo = 'Photo must be less than 2MB.';
        }
        if (i === 1) {
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(val('email'))) e.email = 'Enter a valid email address.';
            if (!/^[6-9]\d{9}$/.test(digits(val('phone')))) e.phone = 'Enter a valid 10-digit mobile number.';
            if (val('alt_phone')) {
                if (!/^[6-9]\d{9}$/.test(digits(val('alt_phone')))) e.alt_phone = 'Enter a valid 10-digit mobile number.';
                else if (digits(val('alt_phone')) === digits(val('phone'))) e.alt_phone = 'Must be different from the mobile number.';
            }
            if (val('address').length < 5) e.address = 'Please enter your full address.';
            if (!val('city')) e.city = 'Please enter your city.';
            if (!val('state')) e.state = 'Please select your state.';
            if (!/^\d{6}$/.test(val('pincode'))) e.pincode = 'Pincode must be 6 digits.';
        }
        if (i === 2) {
            const p = form.elements.password.value, c = form.elements.password_confirmation.value;
            if (p.length < 8 || !/[a-z]/.test(p) || !/[A-Z]/.test(p) || !/\d/.test(p)) e.password = 'Password must have 8+ characters with uppercase, lowercase and a number.';
            if (!c) e.password_confirmation = 'Please confirm your password.';
            else if (p !== c) e.password_confirmation = 'Passwords do not match.';
            if (!form.elements.terms.checked) e.terms = 'Please accept the terms to continue.';
        }
        const keys = Object.keys(e);
        keys.forEach(k => fieldError(k, e[k]));
        if (keys.length) {
            shake();
            form.querySelector('[data-field="' + keys[0] + '"]')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return !keys.length;
    }

    nextBtn.addEventListener('click', () => { if (validate(cur)) show(cur + 1); });
    prevBtn.addEventListener('click', () => show(cur - 1));
    dots.forEach((d, n) => d.addEventListener('click', () => {
        if (n <= cur) return show(n);
        for (let k = cur; k < n; k++) { if (!validate(k)) return show(k); }
        show(n);
    }));

    /* ================= SUMMARY (step 3) ================= */
    function buildSummary() {
        const g = form.querySelector('input[name="gender"]:checked');
        const items = [
            ['Name', val('name')], ['Email', val('email')], ['Mobile', digits(val('phone'))],
            ['Gender', g ? g.value : '—'], ['City', val('city')], ['State', val('state')],
        ];
        document.getElementById('suSummary').innerHTML = items.map(([k, v]) => '<div><small>' + k + '</small><b>' + esc(v || '—') + '</b></div>').join('');
    }

    /* ================= PHOTO PREVIEW ================= */
    const photo = document.getElementById('suPhoto');
    const avatar = document.getElementById('suAvatar');
    const rmBtn = document.getElementById('suPhotoRm');
    photo.addEventListener('change', () => {
        const f = photo.files[0];
        const pf = form.querySelector('[data-field="photo"]');
        pf.classList.remove('err');
        pf.querySelector('.su-err')?.remove();
        if (!f) return;
        if (!/^image\/(png|jpe?g|webp)$/.test(f.type)) { fieldError('photo', 'Only JPG, PNG or WEBP images are allowed.'); photo.value = ''; return; }
        avatar.innerHTML = '<img alt="" src="' + URL.createObjectURL(f) + '">';
        rmBtn.hidden = false;
    });
    rmBtn.addEventListener('click', () => {
        photo.value = '';
        avatar.innerHTML = '<i class="fa-solid fa-user"></i>';
        rmBtn.hidden = true;
    });

    /* ================= INPUT HELPERS ================= */
    ['phone', 'alt_phone', 'pincode'].forEach(n => form.elements[n].addEventListener('input', e => {
        e.target.value = e.target.value.replace(/[^\d+\s-]/g, '');
    }));
    form.addEventListener('input', e => {
        const f = e.target.closest('.su-field.err');
        if (f) { f.classList.remove('err'); f.querySelector('.su-err')?.remove(); }
    });
    form.addEventListener('change', e => {
        const f = e.target.closest('.su-field.err');
        if (f) { f.classList.remove('err'); f.querySelector('.su-err')?.remove(); }
    });

    form.querySelectorAll('.su-eye').forEach(b => b.addEventListener('click', () => {
        const inp = b.parentElement.querySelector('input');
        const showIt = inp.type === 'password';
        inp.type = showIt ? 'text' : 'password';
        b.innerHTML = '<i class="fa-regular fa-eye' + (showIt ? '-slash' : '') + '"></i>';
    }));

    // password strength + rules + match
    const pass = document.getElementById('suPass'), pass2 = document.getElementById('suPass2');
    const meter = document.getElementById('suMeter'), match = document.getElementById('suMatch');
    const rules = { len: v => v.length >= 8, upper: v => /[A-Z]/.test(v), lower: v => /[a-z]/.test(v), num: v => /\d/.test(v) };
    const checkMatch = () => {
        if (!pass2.value) { match.className = 'su-match'; return; }
        const ok = pass.value === pass2.value;
        match.className = 'su-match show ' + (ok ? 'ok' : 'no');
        match.innerHTML = ok ? '<i class="fa-solid fa-circle-check"></i> Passwords match' : '<i class="fa-solid fa-circle-xmark"></i> Passwords do not match';
    };
    pass.addEventListener('input', () => {
        const v = pass.value;
        let score = 0;
        Object.entries(rules).forEach(([k, fn]) => {
            const ok = fn(v);
            if (ok) score++;
            const li = document.querySelector('#suRules [data-r="' + k + '"]');
            li.classList.toggle('ok', ok);
            li.querySelector('i').className = 'fa-solid ' + (ok ? 'fa-circle-check' : 'fa-circle');
        });
        if (/[^a-zA-Z0-9]/.test(v) && score === 4) score = 4;
        meter.dataset.l = v ? Math.max(score, 1) : 0;
        checkMatch();
    });
    pass2.addEventListener('input', checkMatch);

    /* ================= SUBMIT ================= */
    form.addEventListener('submit', async e => {
        e.preventDefault();
        alertB.hidden = true;
        for (let k = 0; k < steps.length; k++) { if (!validate(k)) { show(k); return; } }

        const fd = new FormData(form);
        fd.set('phone', digits(fd.get('phone')));
        if (fd.get('alt_phone')) fd.set('alt_phone', digits(fd.get('alt_phone'))); else fd.delete('alt_phone');
        const ph = fd.get('photo');
        if (!(ph instanceof File) || !ph.name) fd.delete('photo');

        subBtn.disabled = true;
        subBtn.classList.add('loading');
        subBtn.querySelector('.su-btn-text').textContent = 'Creating…';

        try {
            const res = await apiForm('/auth/user-register', fd);
            const pop = document.getElementById('suPop');

            // auto-login nahi: popup dikhao phir login page (email pehle se bhara)
            document.getElementById('suPopSmall').textContent = res.pending ? 'Account created – waiting for superadmin approval' : 'Account created successfully as';
            document.getElementById('suPopRole').textContent = res.pending ? 'Pending Approval' : 'Admin';
            document.getElementById('suPopName').textContent = form.elements.name.value;
            document.getElementById('suPopEmail').textContent = res.message;
            pop.classList.add('open');
            requestAnimationFrame(() => requestAnimationFrame(() => pop.classList.add('show')));
            setTimeout(() => { location.href = res.login_url || @json(route('login')); }, res.pending ? 3500 : 2500);
        } catch (err) {
            const errs = err.errors || {};
            let firstStep = -1;
            Object.entries(errs).forEach(([k, v]) => {
                const f = fieldError(k.split('.')[0], Array.isArray(v) ? v[0] : v);
                if (f) {
                    const idx = steps.indexOf(f.closest('.su-section'));
                    if (idx > -1 && (firstStep === -1 || idx < firstStep)) firstStep = idx;
                }
            });
            showAlert('error', err.message);
            if (firstStep > -1) show(firstStep);
            shake();
            subBtn.disabled = false;
            subBtn.classList.remove('loading');
            subBtn.querySelector('.su-btn-text').textContent = 'Create Account';
        }
    });

    show(0, false);
})();
</script>
@endpush