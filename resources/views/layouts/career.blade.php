@extends('layouts.app')
@section('title', 'Career')
@php
    // Job openings – superadmin panel (Career > Job Openings) se, sirf live
    try {
        $jobs = \App\Models\JobOpening::live()->ordered()->get();
    } catch (\Throwable $e) {
        $jobs = collect();   // migrate nahi chala
    }
@endphp

@section('content')

@include('layouts.page-banner', [
    'title' => 'Build Your <span class="gold-text">Career</span>',
    'image' => 'https://images.unsplash.com/photo-1521791055366-0d553872125f?w=1800&q=80',
])

{{-- Why join --}}
<section class="section">
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <span class="section-tag">Life at Lexora</span>
            <h2 class="section-title">Why Work <span class="gold-text">With Us</span></h2>
            <p>We hire sharp minds and give them real courtroom exposure from day one.</p>
        </div>
        <div class="why-grid">
            @foreach([
                ['fa-solid fa-gavel', 'Real Court Exposure', 'Argue matters and assist senior counsel in live cases.'],
                ['fa-solid fa-graduation-cap', 'Mentorship', 'Weekly learning sessions with partners and senior advocates.'],
                ['fa-solid fa-chart-line', 'Fast Growth', 'Clear performance-based growth path to associate and partner.'],
                ['fa-solid fa-heart', 'Healthy Culture', 'Respectful workplace, fair hours and team outings.'],
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

{{-- Openings --}}
<section class="section section-dark">
    <div class="container">
        <div class="section-head" data-aos="fade-up">
            <span class="section-tag">We're Hiring</span>
            <h2 class="section-title">Current <span class="gold-text">Openings</span></h2>
            @if($jobs->count())
                <p>{{ $jobs->sum('openings') }} {{ $jobs->sum('openings') > 1 ? 'positions' : 'position' }} open across {{ $jobs->count() }} {{ $jobs->count() > 1 ? 'roles' : 'role' }}</p>
            @endif
        </div>
        <div class="jobs">
            @forelse($jobs as $j)
                <div class="job-card glass" data-aos="fade-up" data-aos-delay="{{ min($loop->index, 5) * 100 }}">
                    <div class="job-info">
                        <h4>{{ $j->title }} <span class="job-open">{{ $j->openings }} {{ $j->openings > 1 ? 'Openings' : 'Opening' }}</span></h4>
                        <div class="job-meta">
                            <span><i class="fa-solid fa-briefcase"></i> {{ $j->job_type }}</span>
                            @if($j->experience)<span><i class="fa-solid fa-award"></i> {{ $j->experience }}</span>@endif
                            <span><i class="fa-solid fa-location-dot"></i> {{ $j->location }}</span>
                            @if($j->salary)<span><i class="fa-solid fa-indian-rupee-sign"></i> {{ $j->salary }}</span>@endif
                            @if($j->last_date)<span><i class="fa-regular fa-calendar"></i> Apply by {{ $j->last_date->format('d M Y') }}</span>@endif
                        </div>
                        @if($j->description || $j->requirements)
                            <details class="job-more">
                                <summary>View details</summary>
                                @if($j->description)<p>{{ $j->description }}</p>@endif
                                @if($j->requirements)
                                    <ul>@foreach(preg_split('/\r\n|\n/', $j->requirements) as $req)@if(trim($req))<li>{{ preg_replace('/^[\s\-•*]+/u', '', $req) }}</li>@endif @endforeach</ul>
                                @endif
                            </details>
                        @endif
                    </div>
                    <a href="#apply" class="btn btn-outline apply-btn" data-job="{{ $j->id }}">Apply Now</a>
                </div>
            @empty
                <div class="job-card glass job-empty" data-aos="fade-up">
                    <div class="job-info">
                        <h4>No openings right now</h4>
                        <div class="job-meta"><span><i class="fa-regular fa-envelope"></i> We're always happy to meet great talent – check back soon.</span></div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- Apply form --}}
<section class="section" id="apply">
    <div class="container narrow">
        <div class="section-head" data-aos="fade-up">
            <span class="section-tag">Apply Online</span>
            <h2 class="section-title">Send Your <span class="gold-text">Application</span></h2>
        </div>

        <div id="careerAlert"></div>

        @if($jobs->isEmpty())
            <p class="muted" style="text-align:center">Applications will open when a new position is posted.</p>
        @else
        <form id="careerForm" enctype="multipart/form-data" class="lux-form glass" data-aos="fade-up" novalidate>
            <input type="text" name="website" tabindex="-1" autocomplete="off" class="cr-hp" aria-hidden="true">
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
                <div class="field" data-field="position">
                    <select name="position" id="positionSelect" required>
                        <option value="">Select Position</option>
                        @foreach($jobs as $j)
                            <option value="{{ $j->id }}">{{ $j->title }} ({{ $j->job_type }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="field full file-field" data-field="resume">
                    <label class="file-label">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span id="fileName">Upload Resume * (PDF / DOC, max 4MB)</span>
                        <input type="file" name="resume" accept=".pdf,.doc,.docx" id="resumeInput">
                    </label>
                </div>
                <div class="field full" data-field="cover">
                    <textarea name="cover" rows="4" placeholder=" " maxlength="3000"></textarea>
                    <label>Tell us about yourself</label>
                </div>
            </div>
            <button type="submit" class="btn btn-gold btn-lg w-100" id="careerBtn"><span>Submit Application</span> <i class="fa-solid fa-paper-plane"></i></button>
        </form>
        @endif
    </div>
</section>

@endsection

@push('styles')
<style>
/* ===== Career page (isi file me) ===== */
.job-open { display: inline-flex; align-items: center; vertical-align: middle; margin-left: 10px; padding: 4px 12px; border-radius: 20px; font-family: var(--sans, inherit); font-size: 12px; font-weight: 600; letter-spacing: .3px; color: #111; background: var(--gold-grad, linear-gradient(135deg, #f3d77a, #c9a227)); }
.job-more { margin-top: 12px; color: var(--muted); font-size: 14px; }
.job-more summary { cursor: pointer; color: var(--gold-light, #f3d77a); font-size: 13px; list-style: none; }
.job-more summary::before { content: '+ '; }
.job-more[open] summary::before { content: '– '; }
.job-more p { margin: 10px 0 6px; line-height: 1.7; white-space: pre-line; }
.job-more ul { margin: 6px 0 0 18px; padding: 0; line-height: 1.8; }
.job-empty { justify-content: center; text-align: center; }
.cr-hp { position: absolute !important; left: -9999px !important; width: 1px; height: 1px; opacity: 0; }
.lux-form .field.has-error input, .lux-form .field.has-error select, .lux-form .field.has-error textarea, .lux-form .field.has-error .file-label { border-color: rgba(231,76,60,.7) !important; }
.lux-form .f-err { display: block; margin-top: 6px; font-size: 12.5px; color: #f19a90; }
.file-label.has-file { border-style: solid; color: #7fe0a6; }
/* FIX: ".field label" (floating label) resume box par bhi lag raha tha -> absolute + pointer-events:none
   isliye click nahi hota tha aur box textarea ke upar chadh jata tha */
.lux-form .field label.file-label { position: relative; left: auto; top: auto; pointer-events: auto; font-size: 15px; letter-spacing: 0; color: var(--muted); width: 100%; }
.lux-form .field label.file-label:hover { color: var(--gold-light); }
.lux-form .field label.file-label.has-file { color: #7fe0a6; }
.lux-form .field .file-label input[type=file] { position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; padding: 0; z-index: 2; }
.lux-form .file-field .f-err { margin-top: 8px; }
#careerBtn.loading { pointer-events: none; opacity: .8; }
@media (max-width: 575px) { .job-open { display: table; margin: 8px 0 0; } }
</style>
@endpush

@push('scripts')
<script>
/* ===== Career form -> POST /api/career/apply (superadmin > Career > Applications) ===== */
(function () {
    const form = document.getElementById('careerForm');
    if (!form) return;
    const btn  = document.getElementById('careerBtn');
    const box  = document.getElementById('careerAlert');
    const BASE = (document.querySelector('meta[name="api-base"]')?.content || '/api').replace(/\/$/, '');
    const fld  = n => form.elements.namedItem(n);     // form.name = form ka attribute, isliye helper
    const esc  = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const DEF  = 'Upload Resume * (PDF / DOC, max 4MB)';

    const alertBox = (type, html) => {
        box.innerHTML = `<div class="alert alert-${type}"><i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation'}"></i> <div>${html}</div></div>`;
        box.scrollIntoView({ behavior: 'smooth', block: 'center' });
    };
    const clearErrors = () => { form.querySelectorAll('.has-error').forEach(f => f.classList.remove('has-error')); form.querySelectorAll('.f-err').forEach(e => e.remove()); };
    const showErrors = errs => {
        Object.entries(errs || {}).forEach(([k, m]) => {
            const f = form.querySelector(`[data-field="${k}"]`); if (!f) return;
            f.classList.add('has-error');
            f.insertAdjacentHTML('beforeend', `<small class="f-err">${esc(Array.isArray(m) ? m[0] : m)}</small>`);
        });
    };

    fld('phone').addEventListener('input', () => { fld('phone').value = fld('phone').value.replace(/\D/g, '').slice(0, 10); });
    form.addEventListener('input', e => { const f = e.target.closest('.has-error'); if (f) { f.classList.remove('has-error'); f.querySelector('.f-err')?.remove(); } });
    form.addEventListener('change', e => { const f = e.target.closest('.has-error'); if (f) { f.classList.remove('has-error'); f.querySelector('.f-err')?.remove(); } });
    const resume = fld('resume');
    const drop = resume.closest('.file-label');
    ['dragover', 'dragenter'].forEach(t => drop.addEventListener(t, e => { e.preventDefault(); drop.classList.add('has-file'); }));
    ['dragleave', 'drop'].forEach(t => drop.addEventListener(t, () => drop.classList.remove('has-file')));
    drop.addEventListener('drop', e => {
        e.preventDefault();
        const f = e.dataTransfer.files[0]; if (!f) return;
        const dt = new DataTransfer(); dt.items.add(f); resume.files = dt.files;
        resume.dispatchEvent(new Event('change', { bubbles: true }));
    });
    resume.addEventListener('change', () => {
        const f = resume.files[0];
        if (f && f.size > 4 * 1048576) { showErrors({ resume: 'Resume must be smaller than 4MB.' }); resume.value = ''; }
        document.getElementById('fileName').textContent = resume.files[0] ? resume.files[0].name : DEF;
        resume.closest('.file-label').classList.toggle('has-file', !!resume.files[0]);
    });

    form.addEventListener('submit', async e => {
        e.preventDefault();
        clearErrors(); box.innerHTML = '';
        const errs = {};
        if (fld('name').value.trim().length < 3) errs.name = 'Please enter your full name.';
        if (!fld('email').value || !fld('email').checkValidity()) errs.email = 'Please enter a valid email address.';
        if (!/^[6-9]\d{9}$/.test(fld('phone').value)) errs.phone = 'Please enter a valid 10-digit mobile number.';
        if (!fld('position').value) errs.position = 'Please select a position.';
        if (!resume.files[0]) errs.resume = 'Please upload your resume.';
        else if (!/\.(pdf|docx?)$/i.test(resume.files[0].name)) errs.resume = 'Resume must be a PDF, DOC or DOCX file.';
        if (Object.keys(errs).length) { showErrors(errs); form.querySelector('.has-error')?.scrollIntoView({ behavior: 'smooth', block: 'center' }); return; }

        btn.classList.add('loading'); btn.disabled = true; btn.querySelector('span').textContent = 'Submitting…';
        try {
            const res = await fetch(BASE + '/career/apply', { method: 'POST', headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: new FormData(form) });
            const json = await res.json().catch(() => ({}));
            if (!res.ok) {
                if (res.status === 422) { showErrors(json.errors); alertBox('error', esc(json.message || 'Please correct the highlighted fields.')); }
                else if (res.status === 429) alertBox('error', 'Too many attempts. Please wait a minute and try again.');
                else if (res.status === 413) alertBox('error', 'The resume file is too large.');
                else alertBox('error', esc(json.message || 'Something went wrong. Please try again.'));
                return;
            }
            alertBox('success', esc(json.message));
            form.reset();
            document.getElementById('fileName').textContent = DEF;
            resume.closest('.file-label').classList.remove('has-file');
        } catch (err) {
            alertBox('error', 'Network error – please check your internet connection.');
        } finally {
            btn.classList.remove('loading'); btn.disabled = false; btn.querySelector('span').textContent = 'Submit Application';
        }
    });
})();
</script>
@endpush