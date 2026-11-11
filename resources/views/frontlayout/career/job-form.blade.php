@extends('frontlayout.app')
@php
    $editId = $id ?? null;
    $types  = \App\Models\JobOpening::TYPES;
@endphp
@section('title', $editId ? 'Edit Job Opening' : 'Add Job Opening')
@section('page_title', $editId ? 'Edit Job Opening' : 'Add Job Opening')
@section('require_role', 'superadmin')

@section('page_actions')
    <a href="{{ route('superadmin.career.jobs') }}" class="adm-btn adm-btn-ghost"><i class="fa-solid fa-list"></i> Job Openings</a>
@endsection

@push('styles')
<style>
/* ===== ADD / EDIT JOB (isi file me) ===== */
.jf-grid { display: grid; grid-template-columns: minmax(0, 1fr) 340px; gap: 24px; align-items: start; }
.jf-sec { padding: 26px; margin-bottom: 22px; animation: jfUp .5s ease both; }
.jf-sec:nth-child(2) { animation-delay: .07s; }
@keyframes jfUp { from { opacity: 0; transform: translateY(16px); } }
.jf-head { display: flex; align-items: center; gap: 14px; margin-bottom: 20px; }
.jf-num { width: 42px; height: 42px; flex-shrink: 0; border-radius: 12px; display: grid; place-items: center; font-weight: 600; color: #111; background: var(--gold-grad); }
.jf-head h4 { color: #fff; font-size: 17px; margin: 0; }
.jf-head small { color: var(--muted); font-size: 12.5px; }
.req { color: #ff7a7a; font-weight: 600; }
.jf-types { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
.f-group .jf-types label { margin: 0; cursor: pointer; }
.jf-types input { position: absolute; opacity: 0; width: 0 !important; height: 0; padding: 0 !important; border: 0 !important; }
.jf-types span { display: flex; align-items: center; justify-content: center; height: 42px; border-radius: 12px; font-size: 13px; color: #bbb; border: 1px solid var(--border); background: rgba(255,255,255,.02); transition: .25s; }
.jf-types input:checked + span { color: #111; font-weight: 600; background: var(--gold-grad); border-color: transparent; }
.jf-counter { display: flex; align-items: center; gap: 0; border: 1px solid var(--border-2); border-radius: 12px; overflow: hidden; background: rgba(0,0,0,.35); }
.jf-counter button { width: 44px; height: 44px; flex-shrink: 0; border: 0; background: rgba(212,175,55,.1); color: var(--gold); font-size: 16px; cursor: pointer; }
.jf-counter button:hover { background: rgba(212,175,55,.2); }
.f-group .jf-counter input { border: 0 !important; border-radius: 0 !important; text-align: center; font-size: 16px; font-weight: 600; background: transparent !important; box-shadow: none !important; }

/* side preview – website job card jaisa */
.jf-side { position: sticky; top: 90px; padding: 22px; }
.jf-side h5 { color: var(--muted); font-size: 11.5px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 14px; }
.jf-card { padding: 20px; border-radius: 18px; margin-bottom: 18px; border: 1px solid rgba(212,175,55,.3); background: linear-gradient(150deg, rgba(212,175,55,.1), rgba(255,255,255,.02)); }
.jf-card h4 { font-family: var(--serif, serif); color: #fff; font-size: 22px; margin: 0 0 10px; overflow-wrap: anywhere; }
.jf-card .meta { display: flex; flex-wrap: wrap; gap: 6px 14px; color: var(--muted); font-size: 12.5px; margin-bottom: 14px; }
.jf-card .meta i { color: var(--gold); margin-right: 4px; }
.jf-card .open { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; color: #111; background: var(--gold-grad); }
.jf-status { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 14px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 18px; }
.jf-status h6 { color: #fff; font-size: 14px; margin: 0; }
.jf-status small { color: var(--muted); font-size: 12px; }
.jf-side .adm-btn { margin-bottom: 10px; }
form.is-loading .jf-sec, form.is-loading .jf-side { opacity: .45; pointer-events: none; }
@media (max-width: 1100px) { .jf-grid { grid-template-columns: 1fr; } .jf-side { position: static; } }
@media (max-width: 575px) { .jf-sec { padding: 20px 16px; } .jf-types { grid-template-columns: 1fr 1fr; } }
</style>
@endpush

@section('content')

<form id="jobForm" data-edit-id="{{ $editId }}" novalidate>
    <div class="jf-grid">
        <div>
            <div class="glass-card jf-sec">
                <div class="jf-head"><span class="jf-num">01</span><div><h4>Job Details</h4><small>Shown on the website "Current Openings" card</small></div></div>
                <div class="f-grid">
                    <div class="f-group full" data-field="title"><label>Job Title <b class="req">*</b></label>
                        <input type="text" name="title" required minlength="3" maxlength="150" placeholder="e.g. Associate Advocate – Litigation"></div>
                    <div class="f-group full" data-field="job_type"><label>Job Type <b class="req">*</b></label>
                        <div class="jf-types">
                            @foreach($types as $t)
                                <label><input type="radio" name="job_type" value="{{ $t }}" @checked($loop->first)><span>{{ $t }}</span></label>
                            @endforeach
                        </div></div>
                    <div class="f-group" data-field="experience"><label>Experience</label>
                        <input type="text" name="experience" maxlength="60" placeholder="e.g. 2–5 Years / Freshers" list="jfExpList">
                        <datalist id="jfExpList"><option>Freshers</option><option>0–1 Years</option><option>1–3 Years</option><option>2–5 Years</option><option>3+ Years</option><option>5+ Years</option><option>Students (3rd–5th Year)</option></datalist></div>
                    <div class="f-group" data-field="location"><label>Location <b class="req">*</b></label>
                        <input type="text" name="location" required maxlength="100" value="New Delhi"></div>
                    <div class="f-group" data-field="openings"><label>No. of Openings <b class="req">*</b></label>
                        <div class="jf-counter"><button type="button" data-step="-1" aria-label="Less">−</button><input type="number" name="openings" min="1" max="999" value="1" required><button type="button" data-step="1" aria-label="More">+</button></div></div>
                    <div class="f-group" data-field="salary"><label>Salary / Stipend</label>
                        <input type="text" name="salary" maxlength="80" placeholder="e.g. ₹4–6 LPA / ₹10,000 per month"></div>
                </div>
            </div>

            <div class="glass-card jf-sec">
                <div class="jf-head"><span class="jf-num">02</span><div><h4>Description</h4><small>Role responsibilities and requirements</small></div></div>
                <div class="f-grid">
                    <div class="f-group full" data-field="description"><label>Job Description</label>
                        <textarea name="description" rows="5" maxlength="5000" placeholder="What will this person do?"></textarea></div>
                    <div class="f-group full" data-field="requirements"><label>Requirements</label>
                        <textarea name="requirements" rows="4" maxlength="5000" placeholder="One per line – e.g. LLB from a recognised university"></textarea></div>
                    <div class="f-group" data-field="last_date"><label>Last Date to Apply</label>
                        <input type="date" name="last_date" min="{{ now()->format('Y-m-d') }}"><small class="f-hint">Hidden from website after this date</small></div>
                    <div class="f-group" data-field="sort_order"><label>Display Order</label>
                        <input type="number" name="sort_order" min="0" max="9999" placeholder="0 = top"></div>
                </div>
            </div>
        </div>

        <aside class="glass-card jf-side">
            <h5>Website Preview</h5>
            <div class="jf-card">
                <h4 id="pvTitle">Job Title</h4>
                <div class="meta"><span><i class="fa-solid fa-briefcase"></i><span id="pvType">Full Time</span></span><span id="pvExpWrap"><i class="fa-solid fa-award"></i><span id="pvExp"></span></span><span><i class="fa-solid fa-location-dot"></i><span id="pvLoc">New Delhi</span></span></div>
                <span class="open"><i class="fa-solid fa-chair"></i> <span id="pvOpen">1 Opening</span></span>
            </div>
            <div class="jf-status">
                <div><h6 id="jfStatusText">Active</h6><small>Visible on the career page</small></div>
                <label class="switch"><input type="checkbox" id="jfStatus" checked><span></span></label>
            </div>
            <button type="submit" class="adm-btn adm-btn-gold w-100" id="jfSave"><i class="fa-solid fa-check"></i> <span>{{ $editId ? 'Save Changes' : 'Post Job Opening' }}</span></button>
            <a href="{{ route('superadmin.career.jobs') }}" class="adm-btn adm-btn-ghost w-100">Cancel</a>
        </aside>
    </div>
</form>

@endsection

@push('scripts')
<script>
/* ===== ADD / EDIT JOB – API (Bearer token) ===== */
document.addEventListener('auth:ready', function (ev) {
    if (!window.API || ev.detail.role !== 'superadmin') return;
    const form = document.getElementById('jobForm'), id = form.dataset.editId, $ = i => document.getElementById(i);
    const el = n => form.elements.namedItem(n);
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const LIST = @json(route('superadmin.career.jobs'));

    function preview() {
        $('pvTitle').textContent = el('title').value.trim() || 'Job Title';
        $('pvType').textContent = form.querySelector('[name="job_type"]:checked')?.value || '';
        $('pvExp').textContent = el('experience').value.trim();
        $('pvExpWrap').hidden = !el('experience').value.trim();
        $('pvLoc').textContent = el('location').value.trim() || '—';
        const n = Math.max(1, +el('openings').value || 1);
        $('pvOpen').textContent = n + (n > 1 ? ' Openings' : ' Opening');
        $('jfStatusText').textContent = $('jfStatus').checked ? 'Active' : 'Closed';
    }
    form.addEventListener('input', e => { const g = e.target.closest('.has-error'); if (g) { g.classList.remove('has-error'); g.querySelector('.f-error')?.remove(); } preview(); });
    form.addEventListener('change', preview);
    form.querySelectorAll('[data-step]').forEach(b => b.addEventListener('click', () => {
        el('openings').value = Math.min(999, Math.max(1, (+el('openings').value || 1) + (+b.dataset.step))); preview();
    }));

    if (id) {
        form.classList.add('is-loading');
        el('last_date').removeAttribute('min');
        API.get('/jobs/' + id).then(r => {
            const j = r.data;
            ['title', 'experience', 'location', 'openings', 'salary', 'description', 'requirements', 'last_date', 'sort_order'].forEach(k => { if (j[k] != null) el(k).value = j[k]; });
            const t = form.querySelector(`[name="job_type"][value="${j.job_type}"]`); if (t) t.checked = true;
            $('jfStatus').checked = j.status === 'active';
            preview();
        }).catch(err => { toast(err.message, true); if (err.status === 404) setTimeout(() => location.replace(LIST), 1200); })
          .finally(() => form.classList.remove('is-loading'));
    }

    form.addEventListener('submit', async e => {
        e.preventDefault();
        API.clearErrors(form);
        const errs = {};
        if (el('title').value.trim().length < 3) errs.title = ['Please enter the job title.'];
        if (!el('location').value.trim()) errs.location = ['Please enter the location.'];
        if (!(+el('openings').value >= 1)) errs.openings = ['There must be at least 1 opening.'];
        if (Object.keys(errs).length) { API.showErrors(form, errs)?.scrollIntoView({ behavior: 'smooth', block: 'center' }); return; }

        const payload = Object.fromEntries(new FormData(form));
        payload.status = $('jfStatus').checked ? 'active' : 'closed';
        Object.keys(payload).forEach(k => { if (payload[k] === '') payload[k] = null; });

        const btn = $('jfSave'); btn.disabled = true; btn.classList.add('loading');
        try {
            const r = await API.post(id ? '/jobs/' + id : '/jobs', payload);
            try { sessionStorage.setItem('jb_flash', r.message); } catch (x) {}
            toast(r.message);
            setTimeout(() => { location.href = LIST; }, 700);
        } catch (err) {
            API.showErrors(form, err.errors)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            toast(err.message, true);
            btn.disabled = false; btn.classList.remove('loading');
        }
    });

    preview();
    if (!id) setTimeout(() => el('title').focus(), 200);
});
</script>
@endpush