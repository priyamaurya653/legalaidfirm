@extends('layouts.app')
@section('title', 'Login')


@push('styles')
<style>
    /* ---------- page ---------- */
    .lg-page {
        position: relative;
        min-height: calc(100vh - var(--header-h, 80px));
        display: flex;
        align-items: center;
        padding: 60px 0;
        overflow: hidden;
        background: #050505;
    }

    .lg-bg {
        position: absolute;
        inset: 0;
        background: url('https://images.unsplash.com/photo-1589994965851-a8f479c573a9?w=1800&q=80') center/cover;
        transform: scale(1.1);
        animation: lgZoom 24s ease-in-out infinite alternate;
    }

    @keyframes lgZoom {
        to {
            transform: scale(1.25);
        }
    }

    .lg-shade {
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse 55% 60% at 72% 45%, rgba(212, 175, 55, .16), transparent 60%), linear-gradient(100deg, rgba(5, 5, 5, .97) 0%, rgba(5, 5, 5, .88) 50%, rgba(5, 5, 5, .78) 100%);
    }

    .lg-grid-lines {
        position: absolute;
        inset: 0;
        background-image: linear-gradient(rgba(212, 175, 55, .05) 1px, transparent 1px), linear-gradient(90deg, rgba(212, 175, 55, .05) 1px, transparent 1px);
        background-size: 56px 56px;
        -webkit-mask-image: radial-gradient(ellipse at center, #000 15%, transparent 70%);
        mask-image: radial-gradient(ellipse at center, #000 15%, transparent 70%);
        animation: lgGrid 18s linear infinite;
    }

    @keyframes lgGrid {
        to {
            background-position: 56px 56px;
        }
    }

    .lg-orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(90px);
        pointer-events: none;
    }

    .lg-orb.o1 {
        width: 420px;
        height: 420px;
        right: 6%;
        top: -120px;
        background: rgba(212, 175, 55, .22);
        animation: lgOrb 12s ease-in-out infinite alternate;
    }

    .lg-orb.o2 {
        width: 300px;
        height: 300px;
        left: -80px;
        bottom: -80px;
        background: rgba(168, 132, 31, .16);
        animation: lgOrb 15s ease-in-out infinite alternate-reverse;
    }

    @keyframes lgOrb {
        to {
            transform: translate(-60px, 60px) scale(1.2);
        }
    }

    .lg-container {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 1180px;
        margin: 0 auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 1.05fr .95fr;
        gap: 60px;
        align-items: center;
    }

    /* ---------- left info ---------- */
    .lg-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px;
        border-radius: 50px;
        border: 1px solid rgba(212, 175, 55, .4);
        background: rgba(212, 175, 55, .08);
        color: #f5d77a;
        font-size: 13px;
        letter-spacing: 1px;
        margin-bottom: 22px;
    }

    .lg-info h1 {
        font-family: 'Cormorant Garamond', serif;
        font-size: clamp(38px, 5vw, 64px);
        line-height: 1.06;
        color: #fff;
        margin-bottom: 16px;
    }

    .lg-gold {
        background: linear-gradient(110deg, #a8841f, #f5d77a 25%, #fff6d5 35%, #d4af37 45%, #a8841f 60%, #f5d77a 80%);
        background-size: 250% 100%;
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: lgShimmer 4s linear infinite;
    }

    @keyframes lgShimmer {
        to {
            background-position: -250% 0;
        }
    }

    .lg-info>p {
        color: #b5b5bc;
        font-size: 16px;
        max-width: 500px;
        margin-bottom: 30px;
    }

    .lg-roles {
        display: grid;
        gap: 14px;
        max-width: 500px;
    }

    .lg-role {
        position: relative;
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 18px;
        border-radius: 18px;
        background: rgba(255, 255, 255, .03);
        border: 1px solid rgba(255, 255, 255, .08);
        transition: .4s;
        overflow: hidden;
    }

    .lg-role:hover {
        transform: translateX(8px);
    }

    .lg-role-ic {
        width: 48px;
        height: 48px;
        flex-shrink: 0;
        border-radius: 14px;
        display: grid;
        place-items: center;
        font-size: 19px;
    }

    .lg-role b {
        display: block;
        color: #fff;
        font-weight: 600;
        font-size: 15px;
    }

    .lg-role small {
        color: #8d8d97;
        font-size: 13px;
    }

    .lg-role .lg-tick {
        margin-left: auto;
        width: 28px;
        height: 28px;
        flex-shrink: 0;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 12px;
        opacity: 0;
        transform: scale(0);
        transition: .5s cubic-bezier(.34, 1.56, .64, 1);
    }

    .lg-role.gold .lg-role-ic,
    .lg-role.gold .lg-tick {
        background: linear-gradient(135deg, #f8e08e, #d4af37 45%, #a8841f);
        color: #111;
    }

    .lg-role.blue .lg-role-ic,
    .lg-role.blue .lg-tick {
        background: linear-gradient(135deg, #7aa5ff, #3f6fd8);
        color: #fff;
    }

    .lg-role.green .lg-role-ic,
    .lg-role.green .lg-tick {
        background: linear-gradient(135deg, #7ff0b8, #2fbf7d);
        color: #062b1a;
    }

    .lg-role.gold:hover {
        border-color: rgba(212, 175, 55, .45);
    }

    .lg-role.blue:hover {
        border-color: rgba(91, 141, 239, .45);
    }

    .lg-role.green:hover {
        border-color: rgba(62, 207, 142, .45);
    }

    .lg-role.detected {
        transform: translateX(10px) scale(1.02);
        box-shadow: 0 10px 40px rgba(0, 0, 0, .5);
    }

    .lg-role.detected .lg-tick {
        opacity: 1;
        transform: scale(1);
    }

    .lg-role.gold.detected {
        border-color: #d4af37;
        background: rgba(212, 175, 55, .1);
    }

    .lg-role.blue.detected {
        border-color: #5b8def;
        background: rgba(91, 141, 239, .1);
    }

    .lg-role.green.detected {
        border-color: #3ecf8e;
        background: rgba(62, 207, 142, .1);
    }

    .lg-roles.dim .lg-role:not(.detected) {
        opacity: .35;
    }

    /* ---------- card ---------- */
    .lg-card {
        position: relative;
        padding: 44px 40px 34px;
        border-radius: 30px;
        overflow: hidden;
        background: linear-gradient(150deg, rgba(255, 255, 255, .1), rgba(255, 255, 255, .02) 50%, rgba(255, 255, 255, .05));
        border: 1px solid rgba(212, 175, 55, .3);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        box-shadow: 0 40px 110px rgba(0, 0, 0, .8), 0 0 70px rgba(212, 175, 55, .1), inset 0 1px 0 rgba(255, 255, 255, .16);
    }

    .lg-card::before {
        content: '';
        position: absolute;
        inset: 0 0 58% 0;
        pointer-events: none;
        border-radius: 30px 30px 50% 50% / 30px 30px 34px 34px;
        background: linear-gradient(180deg, rgba(255, 255, 255, .09), transparent);
    }

    .lg-card::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        padding: 1.5px;
        pointer-events: none;
        background: conic-gradient(from var(--lgAng, 0deg), transparent 0 60%, #f5d77a 75%, #d4af37 85%, transparent);
        -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
        -webkit-mask-composite: xor;
        mask: linear-gradient(#000 0 0) content-box exclude, linear-gradient(#000 0 0);
        animation: lgSpin 5s linear infinite;
    }

    @property --lgAng {
        syntax: '<angle>';
        initial-value: 0deg;
        inherits: false;
    }

    @keyframes lgSpin {
        to {
            --lgAng: 360deg;
        }
    }

    .lg-card>* {
        position: relative;
        z-index: 1;
    }

    .lg-sweep {
        position: absolute !important;
        top: 0;
        left: -60%;
        width: 40%;
        height: 100%;
        z-index: 0 !important;
        pointer-events: none;
        transform: skewX(-20deg);
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .12), transparent);
        animation: lgSweep 6s ease-in-out infinite 1s;
    }

    @keyframes lgSweep {
        0% {
            left: -60%;
        }

        35%,
        100% {
            left: 130%;
        }
    }

    .lg-card.shake {
        animation: lgShake .45s ease;
    }

    @keyframes lgShake {

        20%,
        60% {
            transform: translateX(-9px);
        }

        40%,
        80% {
            transform: translateX(9px);
        }
    }

    .lg-card.ok {
        border-color: rgba(62, 207, 142, .55);
    }

    .lg-logo {
        width: 74px;
        height: 74px;
        margin: 0 auto 16px;
        border-radius: 22px;
        display: grid;
        place-items: center;
        font-size: 30px;
        color: #111;
        background: linear-gradient(135deg, #f8e08e, #d4af37 45%, #a8841f);
        box-shadow: 0 0 0 8px rgba(212, 175, 55, .1), 0 14px 34px rgba(212, 175, 55, .45);
        animation: lgFloat 5s ease-in-out infinite;
    }

    @keyframes lgFloat {
        50% {
            transform: translateY(-8px);
        }
    }

    .lg-card h2 {
        text-align: center;
        font-family: 'Cormorant Garamond', serif;
        font-size: 40px;
        line-height: 1.1;
        color: #fff;
    }

    .lg-sub {
        text-align: center;
        color: #8d8d97;
        font-size: 14px;
        margin: 4px 0 24px;
    }

    .lg-sub i {
        color: #d4af37;
    }

    .lg-alert {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        padding: 13px 16px;
        border-radius: 12px;
        margin-bottom: 18px;
        font-size: 14px;
        animation: lgIn .35s ease;
    }

    .lg-alert[hidden] {
        display: none;
    }

    .lg-alert i {
        margin-top: 3px;
    }

    .lg-alert.error {
        background: rgba(255, 107, 107, .1);
        border: 1px solid rgba(255, 107, 107, .4);
        color: #ffa3a3;
    }

    .lg-alert.success {
        background: rgba(62, 207, 142, .1);
        border: 1px solid rgba(62, 207, 142, .4);
        color: #7fe0a6;
    }

    .lg-alert.info {
        background: rgba(212, 175, 55, .08);
        border: 1px solid rgba(212, 175, 55, .35);
        color: #f5d77a;
    }

    @keyframes lgIn {
        from {
            opacity: 0;
            transform: translateY(-6px);
        }
    }

    .lg-form {
        display: grid;
        gap: 18px;
    }

    .lg-field label {
        display: block;
        font-size: 13.5px;
        color: #c9c9cf;
        margin-bottom: 7px;
        font-weight: 500;
    }

    .lg-input {
        position: relative;
    }

    .lg-input>i {
        position: absolute;
        left: 17px;
        top: 50%;
        transform: translateY(-50%);
        color: #d4af37;
        font-size: 15px;
        pointer-events: none;
        transition: .3s;
    }

    .lg-page .lg-input input {
        width: 100%;
        height: 54px;
        padding: 0 50px 0 48px;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, .1);
        background: rgba(0, 0, 0, .45);
        color: #fff;
        font-family: inherit;
        font-size: 15px;
        outline: none;
        transition: .3s;
    }

    .lg-page .lg-input input::placeholder {
        color: #66666e;
    }

    .lg-page .lg-input input:focus {
        border-color: #d4af37;
        box-shadow: 0 0 0 4px rgba(212, 175, 55, .12);
        background: rgba(0, 0, 0, .6);
    }

    .lg-input:focus-within>i {
        transform: translateY(-50%) scale(1.15);
    }

    .lg-eye {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: 0;
        background: none;
        color: #8d8d97;
        cursor: pointer;
    }

    .lg-eye:hover {
        color: #d4af37;
        background: rgba(212, 175, 55, .08);
    }

    .lg-field.err input {
        border-color: #ff6b6b !important;
        box-shadow: 0 0 0 3px rgba(255, 107, 107, .12) !important;
    }

    .lg-err {
        display: block;
        margin-top: 6px;
        font-size: 12.5px;
        color: #ff8c8c;
        animation: lgIn .3s ease;
    }

    .lg-caps {
        display: none;
        margin-top: 6px;
        font-size: 12.5px;
        color: #f5a623;
    }

    .lg-caps.on {
        display: block;
    }

    .lg-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        font-size: 13.5px;
    }

    .lg-check {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        color: #bbb;
        user-select: none;
    }

    .lg-page .lg-check input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
        width: 1px;
        height: 1px;
        padding: 0;
    }

    .lg-box {
        width: 20px;
        height: 20px;
        border-radius: 6px;
        display: grid;
        place-items: center;
        border: 1px solid rgba(212, 175, 55, .5);
        background: rgba(0, 0, 0, .4);
        transition: .25s;
    }

    .lg-box i {
        font-size: 10px;
        color: #111;
        transform: scale(0);
        transition: .25s cubic-bezier(.34, 1.56, .64, 1);
    }

    .lg-check input:checked+.lg-box {
        background: linear-gradient(135deg, #f8e08e, #d4af37);
        border-color: transparent;
    }

    .lg-check input:checked+.lg-box i {
        transform: scale(1);
    }

    .lg-link {
        color: #f5d77a;
    }

    .lg-link:hover {
        color: #fff;
    }

    .lg-btn {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        height: 56px;
        border: 0;
        border-radius: 50px;
        cursor: pointer;
        overflow: hidden;
        font-family: inherit;
        font-size: 16px;
        font-weight: 600;
        color: #111;
        background: linear-gradient(135deg, #f8e08e 0%, #d4af37 45%, #a8841f 100%);
        box-shadow: 0 12px 30px rgba(212, 175, 55, .35), inset 0 1px 0 rgba(255, 255, 255, .6);
        transition: .35s;
    }

    .lg-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 50%;
        height: 100%;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, .7), transparent);
        transform: skewX(-25deg);
        animation: lgBtn 3.2s infinite;
    }

    @keyframes lgBtn {
        0% {
            left: -100%;
        }

        40%,
        100% {
            left: 160%;
        }
    }

    .lg-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 40px rgba(212, 175, 55, .5);
    }

    .lg-btn:disabled {
        pointer-events: none;
        opacity: .85;
    }

    .lg-btn .lg-spin {
        display: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid rgba(0, 0, 0, .25);
        border-top-color: #111;
        animation: lgRot .7s linear infinite;
    }

    .lg-btn.loading .lg-spin {
        display: block;
    }

    .lg-btn.loading .lg-arrow {
        display: none;
    }

    @keyframes lgRot {
        to {
            transform: rotate(360deg);
        }
    }

    .lg-divider {
        display: flex;
        align-items: center;
        gap: 14px;
        margin: 24px 0 16px;
        color: #8d8d97;
        font-size: 13px;
    }

    .lg-divider::before,
    .lg-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(212, 175, 55, .4), transparent);
    }

    .lg-two {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .lg-ghost {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 48px;
        border-radius: 50px;
        font-size: 14px;
        color: #f5d77a;
        border: 1px solid rgba(212, 175, 55, .5);
        background: rgba(212, 175, 55, .05);
        transition: .3s;
    }

    .lg-ghost:hover {
        background: linear-gradient(135deg, #f8e08e, #d4af37);
        color: #111;
        transform: translateY(-2px);
    }

    .lg-secure {
        text-align: center;
        margin-top: 18px;
        color: #6f6f78;
        font-size: 12px;
    }

    .lg-secure i {
        color: #3ecf8e;
    }

    /* ---------- role popup ---------- */
    .lg-pop {
        position: fixed;
        inset: 0;
        z-index: 6000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: rgba(0, 0, 0, .82);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        opacity: 0;
        transition: opacity .4s;
    }

    .lg-pop.open {
        display: flex;
    }

    .lg-pop.show {
        opacity: 1;
    }

    .lg-pop-box {
        position: relative;
        width: 100%;
        max-width: 420px;
        padding: 44px 30px 30px;
        text-align: center;
        border-radius: 28px;
        overflow: hidden;
        background: linear-gradient(160deg, rgba(34, 30, 18, .97), rgba(10, 10, 12, .98));
        border: 1px solid rgba(212, 175, 55, .45);
        box-shadow: 0 40px 120px rgba(0, 0, 0, .9), 0 0 80px rgba(212, 175, 55, .2);
        transform: translateY(40px) scale(.9);
        transition: transform .6s cubic-bezier(.34, 1.56, .64, 1);
    }

    .lg-pop.show .lg-pop-box {
        transform: none;
    }

    .lg-pop-ic {
        width: 96px;
        height: 96px;
        margin: 0 auto 18px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 40px;
    }

    .lg-pop.show .lg-pop-ic {
        animation: lgPopIc .8s cubic-bezier(.34, 1.56, .64, 1) both .15s;
    }

    @keyframes lgPopIc {
        from {
            transform: scale(0) rotate(-120deg);
        }
    }

    .lg-pop-ic.gold {
        color: #111;
        background: linear-gradient(135deg, #f8e08e, #d4af37 45%, #a8841f);
        box-shadow: 0 0 0 10px rgba(212, 175, 55, .12), 0 0 0 20px rgba(212, 175, 55, .05), 0 18px 40px rgba(212, 175, 55, .45);
    }

    .lg-pop-ic.blue {
        color: #fff;
        background: linear-gradient(135deg, #7aa5ff, #3f6fd8);
        box-shadow: 0 0 0 10px rgba(91, 141, 239, .14), 0 0 0 20px rgba(91, 141, 239, .05), 0 18px 40px rgba(91, 141, 239, .45);
    }

    .lg-pop-ic.green {
        color: #062b1a;
        background: linear-gradient(135deg, #7ff0b8, #2fbf7d);
        box-shadow: 0 0 0 10px rgba(62, 207, 142, .14), 0 0 0 20px rgba(62, 207, 142, .05), 0 18px 40px rgba(62, 207, 142, .4);
    }

    .lg-pop small {
        display: block;
        color: #8d8d97;
        font-size: 13px;
        margin-bottom: 10px;
    }

    .lg-badge {
        display: inline-block;
        padding: 6px 18px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    .lg-badge.gold {
        color: #111;
        background: linear-gradient(135deg, #f8e08e, #d4af37);
    }

    .lg-badge.blue {
        color: #fff;
        background: linear-gradient(135deg, #7aa5ff, #3f6fd8);
    }

    .lg-badge.green {
        color: #062b1a;
        background: linear-gradient(135deg, #7ff0b8, #2fbf7d);
    }

    .lg-pop h3 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 32px;
        color: #fff;
        margin: 14px 0 2px;
        overflow-wrap: anywhere;
    }

    .lg-pop p {
        color: #aaa;
        font-size: 14px;
        overflow-wrap: anywhere;
    }

    .lg-pop.show .lg-badge,
    .lg-pop.show h3,
    .lg-pop.show p {
        animation: lgUp .6s ease both .5s;
    }

    @keyframes lgUp {
        from {
            opacity: 0;
            transform: translateY(14px);
        }
    }

    .lg-bar {
        height: 4px;
        border-radius: 4px;
        margin: 24px 0 10px;
        background: rgba(255, 255, 255, .08);
        overflow: hidden;
    }

    .lg-bar i {
        display: block;
        height: 100%;
        width: 0;
        border-radius: 4px;
        background: linear-gradient(90deg, #f5d77a, #d4af37);
    }

    .lg-pop.show .lg-bar i {
        animation: lgFill 1.8s linear forwards .1s;
    }

    @keyframes lgFill {
        to {
            width: 100%;
        }
    }

    /* ---------- responsive ---------- */
    @media (max-width: 991px) {
        .lg-container {
            grid-template-columns: 1fr;
            gap: 36px;
        }

        .lg-info {
            text-align: center;
            order: 2;
        }

        .lg-info>p,
        .lg-roles {
            margin-left: auto;
            margin-right: auto;
        }

        .lg-roles {
            text-align: left;
        }

        .lg-card {
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
        }
    }

    @media (max-width: 575px) {
        .lg-page {
            padding: 30px 0 50px;
        }

        .lg-card {
            padding: 34px 20px 26px;
            border-radius: 24px;
        }

        .lg-card h2 {
            font-size: 34px;
        }

        .lg-two {
            grid-template-columns: 1fr;
        }

        .lg-role {
            padding: 13px 14px;
        }

        .lg-role-ic {
            width: 42px;
            height: 42px;
            font-size: 17px;
        }

        .lg-pop-box {
            padding: 38px 20px 24px;
        }
    }

    @media (prefers-reduced-motion: reduce) {

        .lg-page *,
        .lg-pop * {
            animation: none !important;
            transition: none !important;
        }
    }
</style>
@endpush

@section('content')

<section class="lg-page">
    <div class="lg-bg"></div>
    <div class="lg-shade"></div>
    <div class="lg-grid-lines"></div>
    <div class="lg-orb o1"></div>
    <div class="lg-orb o2"></div>

    <div class="lg-container">

        {{-- LEFT: roles --}}
        <div class="lg-info" data-aos="fade-right">
            <span class="lg-eyebrow"><i class="fa-solid fa-shield-halved"></i> Secure Login Portal</span>
            <h1>Welcome Back to <span class="lg-gold">Lexora Legal</span></h1>
            <p>One login for everyone. Enter your email and password – your role is detected automatically and you land on the right dashboard.</p>

            <div class="lg-roles" id="lgRoles">
                <div class="lg-role gold" data-role="superadmin">
                    <span class="lg-role-ic"><i class="fa-solid fa-crown"></i></span>
                    <div><b>Super Admin</b><small>Full control – advocates, users & activity</small></div>
                    <span class="lg-tick"><i class="fa-solid fa-check"></i></span>
                </div>
                <div class="lg-role blue" data-role="admin">
                    <span class="lg-role-ic"><i class="fa-solid fa-user-tie"></i></span>
                    <div><b>Admin (Advocate)</b><small>Cases, clients & hearings</small></div>
                    <span class="lg-tick"><i class="fa-solid fa-check"></i></span>
                </div>
                <div class="lg-role green" data-role="user">
                    <span class="lg-role-ic"><i class="fa-solid fa-user"></i></span>
                    <div><b>User</b><small>Consultations & your account</small></div>
                    <span class="lg-tick"><i class="fa-solid fa-check"></i></span>
                </div>
            </div>
        </div>

        {{-- RIGHT: login card --}}
        <div class="lg-card" id="lgCard" data-aos="fade-left" data-aos-delay="100">
            <a href="{{ route('home') }}" class="logo lg-logo">
                <img src="{{ asset('images/logo.png') }}" alt="LEXORA LEGAL Logo" class="logo-image" style="text-align:center;">
            </a>

            <!-- <div class="lg-logo"><i class="fa-solid fa-scale-balanced"></i></div> -->
            <h2>Sign In</h2>
            <p class="lg-sub"><i class="fa-solid fa-wand-magic-sparkles"></i> Role is detected automatically</p>

            <div class="lg-alert" id="lgAlert" hidden></div>

            <form class="lg-form" id="lgForm" novalidate autocomplete="on">
                <div class="lg-field" data-field="email">
                    <label for="lgEmail">Email Address</label>
                    <div class="lg-input">
                        <input type="email" id="lgEmail" name="email" required maxlength="150" autocomplete="email" placeholder="you@example.com">
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                </div>

                <div class="lg-field" data-field="password">
                    <label for="lgPassword">Password</label>
                    <div class="lg-input">
                        <input type="password" id="lgPassword" name="password" required autocomplete="current-password" placeholder="Enter your password">
                        <i class="fa-solid fa-key"></i>
                        <button type="button" class="lg-eye" id="lgEye" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
                    </div>
                    <small class="lg-caps" id="lgCaps"><i class="fa-solid fa-triangle-exclamation"></i> Caps Lock is ON</small>
                </div>

                <div class="lg-row">
                    <label class="lg-check">
                        <input type="checkbox" name="remember" id="lgRemember" checked>
                        <span class="lg-box"><i class="fa-solid fa-check"></i></span>
                        Keep me logged in
                    </label>
                    <a href="#" class="lg-link" id="lgForgot">Forgot password?</a>
                </div>

                <button type="submit" class="lg-btn" id="lgSubmit">
                    <span class="lg-btn-text">Login</span>
                    <i class="fa-solid fa-arrow-right lg-arrow"></i>
                    <span class="lg-spin"></span>
                </button>
            </form>

            <div class="lg-divider">New to Lexora?</div>
            <div class="lg-two">
                <a href="{{ route('signup') }}" class="lg-ghost"><i class="fa-solid fa-user-plus"></i> Create Account</a>
                <a href="{{ route('register') }}" class="lg-ghost"><i class="fa-solid fa-user-tie"></i> Join as Advocate</a>
            </div>
            <p class="lg-secure"><i class="fa-solid fa-lock"></i> Secured with encrypted token authentication</p>
        </div>
    </div>
</section>

{{-- Role popup --}}
<div class="lg-pop" id="lgPop" aria-live="polite">
    <div class="lg-pop-box">
        <div class="lg-pop-ic" id="lgPopIc"><i class="fa-solid fa-user"></i></div>
        <small>Logged in successfully as</small>
        <span class="lg-badge" id="lgPopRole">User</span>
        <h3 id="lgPopName">—</h3>
        <p id="lgPopEmail">—</p>
        <div class="lg-bar"><i></i></div>
        <small>Redirecting to your dashboard…</small>
    </div>
</div>

@endsection

@push('scripts')
<script>
    (function() {
        'use strict';

        /* ================= CONFIG ================= */
        const API_BASE = @json(url('/api'));
        const TOKEN_KEY = 'lx_token';
        const USER_KEY = 'lx_user';
        const ROLES = {
            superadmin: {
                label: 'Super Admin',
                icon: 'fa-crown',
                cls: 'gold',
                home: @json(route('superadmin.dashboard'))
            },
            admin: {
                label: 'Admin (Advocate)',
                icon: 'fa-user-tie',
                cls: 'blue',
                home: @json(route('admin.dashboard'))
            },
            user: {
                label: 'User',
                icon: 'fa-user',
                cls: 'green',
                home: @json(route('user.dashboard'))
            },
        };

        /* ================= ELEMENTS ================= */
        const form = document.getElementById('lgForm');
        const card = document.getElementById('lgCard');
        const alertB = document.getElementById('lgAlert');
        const btn = document.getElementById('lgSubmit');
        const pwd = document.getElementById('lgPassword');
        if (!form) return;

        /* ================= HELPERS ================= */
        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        } [c]));
        const getToken = () => {
            try {
                return localStorage.getItem(TOKEN_KEY) || sessionStorage.getItem(TOKEN_KEY);
            } catch (e) {
                return null;
            }
        };
        const clearAuth = () => {
            try {
                [localStorage, sessionStorage].forEach(s => {
                    s.removeItem(TOKEN_KEY);
                    s.removeItem(USER_KEY);
                });
            } catch (e) {}
        };
        const saveAuth = (token, user, remember) => {
            clearAuth();
            try {
                const s = remember ? localStorage : sessionStorage;
                s.setItem(TOKEN_KEY, token);
                s.setItem(USER_KEY, JSON.stringify(user));
            } catch (e) {}
        };

        /** API call (JSON body) */
        async function api(method, path, data) {
            const headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };
            const token = getToken();
            if (token) headers['Authorization'] = 'Bearer ' + token;
            if (data) headers['Content-Type'] = 'application/json';

            let res;
            try {
                res = await fetch(API_BASE + path, {
                    method,
                    headers,
                    body: data ? JSON.stringify(data) : undefined
                });
            } catch (e) {
                throw {
                    status: 0,
                    message: 'Network error – please check your internet connection.',
                    errors: {}
                };
            }

            let json = {};
            try {
                json = await res.json();
            } catch (e) {}
            if (!res.ok) {
                let msg = json.message || ('Something went wrong (' + res.status + ').');
                if (res.status === 429) msg = 'Too many login attempts. Please wait a minute and try again.';
                throw {
                    status: res.status,
                    message: msg,
                    errors: json.errors || {}
                };
            }
            return json;
        }

        const showAlert = (type, msg) => {
            const icon = {
                error: 'fa-circle-exclamation',
                success: 'fa-circle-check',
                info: 'fa-circle-info'
            } [type];
            alertB.className = 'lg-alert ' + type;
            alertB.innerHTML = '<i class="fa-solid ' + icon + '"></i><span>' + esc(msg) + '</span>';
            alertB.hidden = false;
        };
        const clearErrors = () => {
            alertB.hidden = true;
            form.querySelectorAll('.lg-field.err').forEach(f => f.classList.remove('err'));
            form.querySelectorAll('.lg-err').forEach(e => e.remove());
        };
        const fieldError = (name, msg) => {
            const f = form.querySelector('[data-field="' + name + '"]');
            if (!f) return;
            f.classList.add('err');
            const s = document.createElement('small');
            s.className = 'lg-err';
            s.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> ' + esc(msg);
            f.appendChild(s);
        };
        const setLoading = on => {
            btn.disabled = on;
            btn.classList.toggle('loading', on);
            btn.querySelector('.lg-btn-text').textContent = on ? 'Checking…' : 'Login';
        };
        const shake = () => {
            card.classList.remove('shake');
            void card.offsetWidth;
            card.classList.add('shake');
        };

        /* ================= ALREADY LOGGED IN? ================= */
        if (getToken()) {
            api('GET', '/auth/me')
                .then(r => {
                    const role = r.user && r.user.role;
                    location.replace((ROLES[role] || ROLES.user).home);
                })
                .catch(() => clearAuth());
        }

        const qp = new URLSearchParams(location.search);
        if (qp.get('email')) form.elements.email.value = qp.get('email');
        if (qp.get('registered')) {
            showAlert('success', 'Account created! Please log in with your email and password.');
            setTimeout(() => form.elements.password.focus(), 300);
        } else if (qp.get('reason') === 'inactive') showAlert('error', 'Your account is not active. Please contact the superadmin.');
        else if (qp.get('next')) showAlert('info', 'Please log in to continue.');

        /* ================= UI ================= */
        document.getElementById('lgEye').addEventListener('click', function() {
            const show = pwd.type === 'password';
            pwd.type = show ? 'text' : 'password';
            this.innerHTML = '<i class="fa-regular fa-eye' + (show ? '-slash' : '') + '"></i>';
        });

        const caps = document.getElementById('lgCaps');
        ['keydown', 'keyup'].forEach(ev => pwd.addEventListener(ev, e => {
            if (e.getModifierState) caps.classList.toggle('on', e.getModifierState('CapsLock'));
        }));

        form.addEventListener('input', e => {
            const f = e.target.closest('.lg-field.err');
            if (f) {
                f.classList.remove('err');
                f.querySelector('.lg-err')?.remove();
            }
        });

        document.getElementById('lgForgot').addEventListener('click', e => {
            e.preventDefault();
            showAlert('info', 'Please contact the superadmin at admin@lexoralegal.com to reset your password.');
        });

        /* ================= ROLE POPUP ================= */
        function showRole(res, url) {
            const role = res.role || (res.user && res.user.role);
            const m = ROLES[role] || ROLES.user;

            // left side: detected role highlight
            const roles = document.getElementById('lgRoles');
            roles.classList.add('dim');
            const rc = roles.querySelector('[data-role="' + role + '"]');
            if (rc) rc.classList.add('detected');

            const ic = document.getElementById('lgPopIc');
            ic.className = 'lg-pop-ic ' + m.cls;
            ic.innerHTML = '<i class="fa-solid ' + m.icon + '"></i>';
            const badge = document.getElementById('lgPopRole');
            badge.className = 'lg-badge ' + m.cls;
            badge.textContent = res.role_label || m.label;
            document.getElementById('lgPopName').textContent = res.user.name;
            document.getElementById('lgPopEmail').textContent = res.user.email;

            const pop = document.getElementById('lgPop');
            setTimeout(() => {
                pop.classList.add('open');
                requestAnimationFrame(() => requestAnimationFrame(() => pop.classList.add('show')));
            }, 500);
            setTimeout(() => {
                location.href = url;
            }, 2500);
        }

        /* ================= SUBMIT ================= */
        form.addEventListener('submit', async e => {
            e.preventDefault();
            clearErrors();

            const email = form.elements.email.value.trim().toLowerCase();
            const password = form.elements.password.value;
            let bad = false;
            if (!email) {
                fieldError('email', 'Please enter your email address.');
                bad = true;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) {
                fieldError('email', 'Please enter a valid email address.');
                bad = true;
            }
            if (!password) {
                fieldError('password', 'Please enter your password.');
                bad = true;
            }
            if (bad) {
                shake();
                return;
            }

            setLoading(true);
            try {
                const res = await api('POST', '/auth/login', {
                    email,
                    password
                });
                saveAuth(res.token, res.user, document.getElementById('lgRemember').checked);
                card.classList.add('ok');
                showAlert('success', res.message);

                // ?next= sirf usi role ke area ka ho
                const next = qp.get('next');
                const areas = {
                    superadmin: ['/superadmin'],
                    admin: ['/admin', '/superadmin/cases', '/superadmin/clients', '/superadmin/enquiries', '/superadmin/settings'],
                    user: ['/dashboard']
                } [res.role] || [];
                const go = (next && next.startsWith('/') && !next.startsWith('//') && areas.some(a => next.includes(a))) ? next : res.redirect;
                showRole(res, go);
            } catch (err) {
                const errs = err.errors || {};
                Object.entries(errs).forEach(([k, v]) => fieldError(k, Array.isArray(v) ? v[0] : v));
                if (err.status !== 422 || !Object.keys(errs).length) showAlert('error', err.message);
                shake();
                setLoading(false);
            }
        });
    })();
</script>
@endpush