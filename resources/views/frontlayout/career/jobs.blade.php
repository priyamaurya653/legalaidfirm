@extends('frontlayout.app')
@section('title', 'Job Openings')
@section('page_title', 'Job Openings')
@section('require_role', 'superadmin')

@section('page_actions')
    <a href="{{ route('superadmin.career.applications') }}" class="adm-btn adm-btn-ghost"><i class="fa-solid fa-inbox"></i> <span class="hide-xs">Applications</span></a>
    <a href="{{ route('superadmin.career.jobs.create') }}" class="adm-btn adm-btn-gold"><i class="fa-solid fa-plus"></i> Add Job Opening</a>
@endsection

@push('styles')
<style>
/* ===== JOB OPENINGS (isi file me) ===== */
.jb-name .t-title { max-width: 280px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.jb-meta { display: flex; flex-wrap: wrap; gap: 4px 12px; font-size: 12px; color: var(--muted); margin-top: 2px; }
.jb-meta i { color: var(--gold); margin-right: 3px; }
.jb-open { display: inline-flex; align-items: center; gap: 6px; padding: 4px 11px; border-radius: 20px; font-size: 12.5px; font-weight: 600; color: #111; background: var(--gold-grad); white-space: nowrap; }
.jb-apps { display: inline-flex; align-items: center; gap: 6px; color: #ddd; font-size: 13px; }
.jb-apps .new { font-size: 10.5px; padding: 2px 7px; border-radius: 10px; color: #fff; background: #e05252; }
.jb-sw { display: inline-flex; align-items: center; gap: 8px; }
.jb-sw.busy { opacity: .5; pointer-events: none; }
.jb-exp { color: #ff8c8c; font-size: 11.5px; display: block; }
.adm-table tr.removing { opacity: 0; transform: translateX(30px); transition: .35s; }
.jb-del { text-align: center; padding: 10px 6px 4px; }
.jb-del .ring { width: 76px; height: 76px; margin: 0 auto 16px; border-radius: 50%; display: grid; place-items: center; font-size: 30px; color: #ff6b6b; background: rgba(255,107,107,.1); }
.jb-del h4 { color: #fff; font-size: 20px; margin-bottom: 8px; }
.jb-del p { color: var(--muted); font-size: 14px; margin-bottom: 22px; }
.jb-del p b { color: var(--gold-light); }
.jb-del .btns { display: flex; gap: 10px; justify-content: center; }
.adm-btn-danger { color: #fff; background: linear-gradient(135deg, #ff7a7a, #d63c3c); border: 0; }
</style>
@endpush

@section('content')

<div class="mini-stats">
    <div class="glass-card mini"><span class="sc-icon gold"><i class="fa-solid fa-briefcase"></i></span><div><b data-js="total">–</b><small>Total Job Posts</small></div></div>
    <div class="glass-card mini"><span class="sc-icon green"><i class="fa-solid fa-signal"></i></span><div><b data-js="live">–</b><small>Live on Website</small></div></div>
    <div class="glass-card mini"><span class="sc-icon blue"><i class="fa-solid fa-chair"></i></span><div><b data-js="positions">–</b><small>Open Positions</small></div></div>
    <a href="{{ route('superadmin.career.applications') }}" class="glass-card mini"><span class="sc-icon purple"><i class="fa-solid fa-file-lines"></i></span><div><b data-js="applications">–</b><small>Applications</small></div></a>
</div>

<div class="glass-card card">
    <div class="toolbar">
        <div class="tb-search sm">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Search title, location, type…" id="jbSearch" autocomplete="off">
        </div>
        <div class="filter-chips" id="jbStatus">
            <button class="active" data-f="">All</button>
            <button data-f="active">Active</button>
            <button data-f="closed">Closed</button>
        </div>
    </div>

    <div class="table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Job</th>
                    <th>Openings</th>
                    <th class="hide-sm">Applications</th>
                    <th class="hide-md">Last Date</th>
                    <th>Status</th>
                    <th class="t-right">Action</th>
                </tr>
            </thead>
            <tbody id="jbRows"></tbody>
        </table>
    </div>
    <div class="pager" id="jbPager"></div>
</div>

@endsection

@push('modals')
<div class="adm-modal" id="jbDeleteModal" aria-hidden="true">
    <div class="adm-modal-box glass-card">
        <div class="jb-del">
            <div class="ring"><i class="fa-regular fa-trash-can"></i></div>
            <h4>Delete Job Opening?</h4>
            <p>"<b id="jbDelName"></b>" will be removed from the website. Received applications are kept.</p>
            <div class="btns">
                <button class="adm-btn adm-btn-ghost" data-close-modal>Cancel</button>
                <button class="adm-btn adm-btn-danger" id="jbDelConfirm"><i class="fa-regular fa-trash-can"></i> Yes, Delete</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
/* ===== JOB OPENINGS – API (Bearer token) ===== */
document.addEventListener('auth:ready', function (ev) {
    if (!window.API || ev.detail.role !== 'superadmin') return;
    const esc = API.esc, $ = id => document.getElementById(id);
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const BASE = @json(url('/superadmin/career')), body = $('jbRows'), pager = $('jbPager');
    const state = { page: 1, search: '', status: '' }, cache = {};
    let delId = null;
    const debounce = (fn, ms = 350) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

    const row = j => {
        const pid = 'jbPop' + j.id, on = j.status === 'active';
        return `<tr data-id="${j.id}">
            <td class="jb-name"><div class="t-title" title="${esc(j.title)}">${esc(j.title)}</div>
                <div class="jb-meta"><span><i class="fa-solid fa-briefcase"></i>${esc(j.job_type)}</span>${j.experience ? `<span><i class="fa-solid fa-award"></i>${esc(j.experience)}</span>` : ''}<span><i class="fa-solid fa-location-dot"></i>${esc(j.location)}</span></div></td>
            <td><span class="jb-open"><i class="fa-solid fa-chair"></i> ${j.openings}</span></td>
            <td class="hide-sm"><a class="jb-apps" href="${BASE}/applications?job=${j.id}"><i class="fa-regular fa-file-lines"></i> ${j.applications_count ?? 0}${j.new_applications ? ` <span class="new">${j.new_applications} new</span>` : ''}</a></td>
            <td class="hide-md">${esc(j.last_date_label || 'No deadline')}${on && !j.is_live ? '<span class="jb-exp">Expired – hidden on website</span>' : ''}</td>
            <td><label class="jb-sw" title="${on ? 'Click to close' : 'Click to make live'}"><span class="switch"><input type="checkbox" data-toggle="${j.id}" ${on ? 'checked' : ''}><span></span></span>
                <span class="status ${on ? 'green' : 'grey'}">${on ? 'Active' : 'Closed'}</span></label></td>
            <td class="t-right"><div class="popper-wrap">
                <button class="icon-btn" data-popper="${pid}" aria-label="Actions"><i class="fa-solid fa-ellipsis"></i></button>
                <div class="popper popper-right" id="${pid}" data-dyn><ul class="pop-menu">
                    <li><a href="${BASE}/jobs/${j.id}/edit"><i class="fa-regular fa-pen-to-square"></i> Edit</a></li>
                    <li><a href="${BASE}/applications?job=${j.id}"><i class="fa-regular fa-file-lines"></i> View Applications</a></li>
                    <li><a href="{{ route('career') }}#apply" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> View on Website</a></li>
                    <li class="sep"></li>
                    <li><a href="#" class="danger" data-jdel="${j.id}"><i class="fa-regular fa-trash-can"></i> Delete</a></li>
                </ul></div></div></td>
        </tr>`;
    };
    const empty = (m, err) => { body.innerHTML = `<tr><td colspan="6"><div class="t-empty ${err ? 'error' : ''}"><i class="fa-solid ${err ? 'fa-triangle-exclamation' : 'fa-briefcase'}"></i><p>${esc(m)}</p>${err ? '' : `<a href="${BASE}/jobs/create" class="adm-btn adm-btn-gold"><i class="fa-solid fa-plus"></i> Add Job Opening</a>`}</div></td></tr>`; };
    const renderPager = meta => {
        if (!meta || !meta.total) { pager.innerHTML = ''; return; }
        const { current_page: c, last_page: l, from, to, total } = meta, p = [];
        for (let i = Math.max(1, c - 2); i <= Math.min(l, c + 2); i++) p.push(i);
        pager.innerHTML = `<small>Showing ${from}–${to} of ${total}</small><div class="pages">
            <button class="icon-btn" data-page="${c - 1}" ${c <= 1 ? 'disabled' : ''}><i class="fa-solid fa-chevron-left"></i></button>
            ${p.map(n => `<button class="icon-btn ${n === c ? 'active' : ''}" data-page="${n}">${n}</button>`).join('')}
            <button class="icon-btn" data-page="${c + 1}" ${c >= l ? 'disabled' : ''}><i class="fa-solid fa-chevron-right"></i></button></div>`;
    };

    async function load() {
        document.querySelectorAll('.popper[data-dyn]').forEach(p => p.remove());
        body.innerHTML = Array.from({ length: 5 }, () => `<tr class="sk-row">${'<td><span class="sk sk-line w80"></span></td>'.repeat(6)}</tr>`).join('');
        const qs = new URLSearchParams({ page: state.page });
        if (state.search) qs.set('search', state.search);
        if (state.status) qs.set('status', state.status);
        try {
            const r = await API.get('/jobs?' + qs);
            r.data.forEach(j => { cache[j.id] = j; });
            Object.entries(r.stats || {}).forEach(([k, v]) => document.querySelectorAll(`[data-js="${k}"]`).forEach(x => { x.textContent = v; }));
            r.data.length ? (body.innerHTML = r.data.map(row).join('')) : empty(state.search || state.status ? 'No job opening matches your filters.' : 'No job openings yet. Post your first opening!');
            renderPager(r.meta);
        } catch (err) { empty(err.message, true); pager.innerHTML = ''; }
    }

    $('jbSearch').addEventListener('input', debounce(e => { state.search = e.target.value.trim(); state.page = 1; load(); }));
    $('jbStatus').addEventListener('click', e => { const b = e.target.closest('button'); if (!b) return; $('jbStatus').querySelectorAll('button').forEach(x => x.classList.toggle('active', x === b)); state.status = b.dataset.f; state.page = 1; load(); });
    pager.addEventListener('click', e => { const b = e.target.closest('[data-page]'); if (!b || b.disabled) return; state.page = +b.dataset.page; load(); });

    body.addEventListener('change', async e => {
        const inp = e.target.closest('[data-toggle]'); if (!inp) return;
        const wrap = inp.closest('.jb-sw'); wrap.classList.add('busy');
        try { const r = await API.patch(`/jobs/${inp.dataset.toggle}/status`, { status: inp.checked ? 'active' : 'closed' }); toast(r.message); load(); }
        catch (err) { inp.checked = !inp.checked; toast(err.message, true); }
        finally { wrap.classList.remove('busy'); }
    });

    document.addEventListener('click', e => {
        const a = e.target.closest('[data-jdel]'); if (!a) return;
        e.preventDefault(); delId = a.dataset.jdel; $('jbDelName').textContent = cache[delId]?.title || ''; window.admOpenModal('jbDeleteModal');
    });
    $('jbDelConfirm').addEventListener('click', async () => {
        const b = $('jbDelConfirm'); b.disabled = true; b.classList.add('loading');
        try {
            const r = await API.del('/jobs/' + delId);
            document.querySelector('#jbDeleteModal [data-close-modal]').click();
            body.querySelector(`tr[data-id="${delId}"]`)?.classList.add('removing');
            toast(r.message); setTimeout(load, 350);
        } catch (err) { toast(err.message, true); }
        finally { b.disabled = false; b.classList.remove('loading'); }
    });

    try { const f = sessionStorage.getItem('jb_flash'); if (f) { sessionStorage.removeItem('jb_flash'); setTimeout(() => toast(f), 300); } } catch (e) {}
    load();
});
</script>
@endpush