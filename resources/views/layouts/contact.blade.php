@extends('layouts.app')
@section('title', 'Contact Us')
@php
    // Practice areas – database se (superadmin panel me jo add hote hain), sirf active
    try {
        $areas = \App\Models\PracticeArea::active()->ordered()->get(['id', 'name']);
    } catch (\Throwable $e) {
        $areas = collect();   // table abhi nahi bani (migrate nahi chala)
    }
@endphp

@section('content')

@include('layouts.page-banner', [
    'title' => 'Contact <span class="gold-text">Us</span>',
    'image' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=1800&q=80',
])

<section class="section">
    <div class="container">
        <div class="contact-cards">
            <div class="c-card glass tilt" data-aos="fade-up">
                <div class="pc-icon"><i class="fa-solid fa-location-dot"></i></div>
                <h4>Visit Our Office</h4>
                <p>Chamber 214, Supreme Tower,<br>Connaught Place, New Delhi – 110001</p>
            </div>
            <div class="c-card glass tilt" data-aos="fade-up" data-aos-delay="100">
                <div class="pc-icon"><i class="fa-solid fa-phone"></i></div>
                <h4>Call Us</h4>
                <p><a href="tel:+919999999999">+91 99999 99999</a><br><a href="tel:+911140000000">+91 11 4000 0000</a></p>
            </div>
            <div class="c-card glass tilt" data-aos="fade-up" data-aos-delay="200">
                <div class="pc-icon"><i class="fa-solid fa-envelope"></i></div>
                <h4>Email Us</h4>
                <p><a href="mailto:info@lexoralegal.com">info@lexoralegal.com</a><br><a href="mailto:cases@lexoralegal.com">cases@lexoralegal.com</a></p>
            </div>
            <div class="c-card glass tilt" data-aos="fade-up" data-aos-delay="300">
                <div class="pc-icon"><i class="fa-regular fa-clock"></i></div>
                <h4>Working Hours</h4>
                <p>Mon – Sat : 10 AM – 7 PM<br>Sunday : By Appointment</p>
            </div>
        </div>

        <div class="contact-split">
            <div class="contact-form-wrap" data-aos="fade-right">
                <span class="section-tag">Get In Touch</span>
                <h2 class="section-title">Book a <span class="gold-text">Consultation</span></h2>
                <p class="muted">Fill the form and a senior advocate will contact you within 24 hours.</p>

                <div id="enqAlert"></div>

                <form id="enquiryForm" class="lux-form glass" novalidate>
                    {{-- honeypot: bots ke liye, user ko nahi dikhta --}}
                    <input type="text" name="website" tabindex="-1" autocomplete="off" class="enq-hp" aria-hidden="true">
                    <div class="form-grid">
                        <div class="field" data-field="name">
                            <input type="text" name="name" placeholder=" " required minlength="3" maxlength="100">
                            <label>Full Name</label>
                        </div>
                        <div class="field" data-field="email">
                            <input type="email" name="email" placeholder=" " required maxlength="150">
                            <label>Email Address</label>
                        </div>
                        <div class="field" data-field="phone">
                            <input type="tel" name="phone" placeholder=" " required inputmode="numeric" maxlength="10">
                            <label>Phone Number</label>
                        </div>
                        <div class="field" data-field="subject">
                            <select name="subject">
                                <option value="">Select Practice Area</option>
                                @foreach($areas as $a)
                                    <option value="{{ $a->id }}" @selected(request('area') == $a->id)>{{ $a->name }}</option>
                                @endforeach
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="field full" data-field="message">
                            <textarea name="message" rows="5" placeholder=" " required minlength="10" maxlength="3000"></textarea>
                            <label>Describe your legal matter</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-gold btn-lg w-100" id="enqBtn"><span>Send Message</span> <i class="fa-solid fa-paper-plane"></i></button>
                </form>
            </div>

            <div class="map-wrap" data-aos="fade-left">
                <iframe
                    src="https://www.google.com/maps?q=Connaught+Place,+New+Delhi&output=embed"
                    loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</section>

@endsection

@push('styles')
<style>
/* ===== Contact form (isi file me) ===== */
.enq-hp { position: absolute !important; left: -9999px !important; width: 1px; height: 1px; opacity: 0; }
.lux-form .field.has-error input, .lux-form .field.has-error select, .lux-form .field.has-error textarea { border-color: rgba(231,76,60,.7) !important; box-shadow: 0 0 0 4px rgba(231,76,60,.08); }
.lux-form .f-err { display: block; margin-top: 6px; font-size: 12.5px; color: #f19a90; }
#enqBtn.loading { pointer-events: none; opacity: .8; }
#enqBtn.loading i { animation: enqFly .9s ease-in-out infinite; }
@keyframes enqFly { 50% { transform: translate(4px, -4px); opacity: .5; } }
#enqAlert .alert { animation: enqIn .4s ease both; }
@keyframes enqIn { from { opacity: 0; transform: translateY(-8px); } }
</style>
@endpush

@push('scripts')
<script>
/* ===== Contact form -> POST /api/enquiries (superadmin ke Enquiries me dikhta hai) ===== */
(function () {
    const form  = document.getElementById('enquiryForm');
    if (!form) return;
    const btn   = document.getElementById('enqBtn');
    const box   = document.getElementById('enqAlert');
    const BASE  = (document.querySelector('meta[name="api-base"]')?.content || '/api').replace(/\/$/, '');
    const fld   = n => form.elements.namedItem(n);   // form.name = form ka attribute hota hai, isliye ye helper
    const esc   = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    const alertBox = (type, html) => {
        box.innerHTML = `<div class="alert alert-${type}"><i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation'}"></i> <div>${html}</div></div>`;
        box.scrollIntoView({ behavior: 'smooth', block: 'center' });
    };
    const clearErrors = () => {
        form.querySelectorAll('.has-error').forEach(f => f.classList.remove('has-error'));
        form.querySelectorAll('.f-err').forEach(e => e.remove());
    };
    const showErrors = errors => {
        Object.entries(errors || {}).forEach(([k, msgs]) => {
            const f = form.querySelector(`[data-field="${k}"]`);
            if (!f) return;
            f.classList.add('has-error');
            f.insertAdjacentHTML('beforeend', `<small class="f-err">${esc(Array.isArray(msgs) ? msgs[0] : msgs)}</small>`);
        });
        form.querySelector('.has-error input, .has-error select, .has-error textarea')?.focus();
    };

    fld('phone').addEventListener('input', () => { fld('phone').value = fld('phone').value.replace(/\D/g, '').slice(0, 10); });
    form.addEventListener('input', e => {
        const f = e.target.closest('.has-error');
        if (f) { f.classList.remove('has-error'); f.querySelector('.f-err')?.remove(); }
    });

    form.addEventListener('submit', async e => {
        e.preventDefault();
        clearErrors(); box.innerHTML = '';

        // client side check
        const errs = {};
        if (fld('name').value.trim().length < 3) errs.name = 'Please enter your full name.';
        if (!fld('email').checkValidity() || !fld('email').value) errs.email = 'Please enter a valid email address.';
        if (!/^[6-9]\d{9}$/.test(fld('phone').value)) errs.phone = 'Please enter a valid 10-digit mobile number.';
        if (fld('message').value.trim().length < 10) errs.message = 'Please describe your matter in at least 10 characters.';
        if (Object.keys(errs).length) { showErrors(errs); return; }

        btn.classList.add('loading'); btn.disabled = true;
        btn.querySelector('span').textContent = 'Sending…';
        try {
            const res = await fetch(BASE + '/enquiries', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(Object.fromEntries(new FormData(form))),
            });
            const json = await res.json().catch(() => ({}));
            if (!res.ok) {
                if (res.status === 422) { showErrors(json.errors); alertBox('error', esc(json.message || 'Please correct the highlighted fields.')); }
                else if (res.status === 429) alertBox('error', 'Too many requests. Please wait a minute and try again.');
                else alertBox('error', esc(json.message || 'Something went wrong. Please try again.'));
                return;
            }
            alertBox('success', esc(json.message));
            form.reset();
        } catch (err) {
            alertBox('error', 'Network error – please check your internet connection.');
        } finally {
            btn.classList.remove('loading'); btn.disabled = false;
            btn.querySelector('span').textContent = 'Send Message';
        }
    });
})();
</script>
@endpush