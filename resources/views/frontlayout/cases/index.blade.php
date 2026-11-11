@extends('frontlayout.app')
@section('title', 'Cases')
@section('page_title', 'Cases')
{{-- require_role nahi: superadmin (sab cases) + admin/advocate (sirf apne cases) dono dekh sakte hain --}}

@section('page_actions')
    <button class="adm-btn adm-btn-ghost" id="csExport"><i class="fa-solid fa-file-export"></i> <span class="hide-xs">Export</span></button>
    <a href="{{ route('superadmin.cases.create') }}" class="adm-btn adm-btn-gold only-superadmin"><i class="fa-solid fa-plus"></i> New Case</a>
@endsection

@push('styles')
<style>
/* ===== CASES LIST (isi file me) ===== */
.cs-stats { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 20px; }
.cs-stat { display: flex; align-items: center; gap: 14px; padding: 16px 18px; cursor: pointer; text-align: left; font: inherit; color: inherit; transition: .35s; position: relative; overflow: hidden; }
.cs-stat:hover, .cs-stat.on { transform: translateY(-4px); border-color: rgba(212,175,55,.45); }
.cs-stat.on::after { content: ''; position: absolute; left: 0; bottom: 0; width: 100%; height: 3px; background: var(--gold-grad); }
.cs-stat b { display: block; font-size: 22px; color: #fff; line-height: 1.1; }
.cs-stat small { color: var(--muted); font-size: 12.5px; }
.sc-icon.red { background: rgba(255,107,107,.12); color: #ff8c8c; }

.cs-toolbar { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.cs-toolbar .right { display: flex; flex-wrap: wrap; gap: 10px; }
.cs-select { height: 40px; max-width: 220px; padding: 0 34px 0 14px; border-radius: 12px; color: #ddd; font: inherit; font-size: 13px; cursor: pointer; appearance: none;
    background: rgba(255,255,255,.04) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath fill='%23d4af37' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E") no-repeat right 14px center;
    border: 1px solid var(--border); }
.cs-select option { background: #111; }

.cs-matter .t-title { max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cs-client { display: inline-flex; align-items: center; gap: 6px; }
.cs-kyc { font-family: ui-monospace, Consolas, monospace; font-size: 11px; color: var(--muted); }
.cs-adv { display: flex; align-items: center; gap: 8px; }
.cs-adv .av { width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0; display: grid; place-items: center; font-size: 12px; font-weight: 600; color: #111; background: var(--gold-grad); }
.cs-adv.none { color: var(--muted); font-style: italic; font-size: 13px; }
.cs-date.soon { color: #ffcf6b; }
.cs-date.past { color: #ff8c8c; }
.cs-docs { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; color: var(--muted); }
.status.red { color: #ff8c8c; background: rgba(255,107,107,.1); }
.prio-dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; margin-right: 6px; vertical-align: middle; }
.prio-dot.urgent { background: #ff6b6b; box-shadow: 0 0 8px #ff6b6b; }
.prio-dot.high { background: #ffb347; }
.adm-table tr.removing { opacity: 0; transform: translateX(30px); transition: .35s; }

.cs-del { text-align: center; padding: 10px 6px 4px; }
.cs-del .ring { width: 76px; height: 76px; margin: 0 auto 16px; border-radius: 50%; display: grid; place-items: center; font-size: 30px; color: #ff6b6b; background: rgba(255,107,107,.1); box-shadow: 0 0 0 10px rgba(255,107,107,.05); }
.cs-del h4 { color: #fff; font-size: 20px; margin-bottom: 8px; }
.cs-del p { color: var(--muted); font-size: 14px; margin-bottom: 22px; }
.cs-del p b { color: var(--gold-light); }
.cs-del .btns { display: flex; gap: 10px; justify-content: center; }
.adm-btn-danger { color: #fff; background: linear-gradient(135deg, #ff7a7a, #d63c3c); border: 0; box-shadow: 0 8px 22px rgba(214,60,60,.35); }

@media (max-width: 1199px) { .cs-stats { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 767px)  { .cs-stats { grid-template-columns: repeat(2, 1fr); } .cs-toolbar, .cs-toolbar .right { flex-direction: column; align-items: stretch; } .cs-select { max-width: none; } .cs-toolbar .tb-search { width: 100%; } }
@media (max-width: 575px)  { .cs-stat { padding: 14px; gap: 10px; } .cs-stat b { font-size: 19px; } .cs-matter .t-title { max-width: 160px; } }
</style>
@endpush

@section('content')

<div class="cs-stats">
    <button class="glass-card cs-stat on" data-quick=""><span class="sc-icon gold"><i class="fa-solid fa-gavel"></i></span><div><b data-cstat="total">–</b><small>Total</small></div></button>
    <button class="glass-card cs-stat" data-quick="active"><span class="sc-icon green"><i class="fa-solid fa-circle-play"></i></span><div><b data-cstat="active">–</b><small>Active</small></div></button>
    <button class="glass-card cs-stat" data-quick="hearing"><span class="sc-icon red"><i class="fa-solid fa-landmark"></i></span><div><b data-cstat="hearing">–</b><small>Hearing</small></div></button>
    <button class="glass-card cs-stat" data-quick="pending"><span class="sc-icon blue"><i class="fa-solid fa-hourglass-half"></i></span><div><b data-cstat="pending">–</b><small>Pending</small></div></button>
    <button class="glass-card cs-stat" data-quick="closed"><span class="sc-icon purple"><i class="fa-solid fa-circle-check"></i></span><div><b data-cstat="closed">–</b><small>Closed</small></div></button>
</div>

<div class="glass-card card">
    <div class="cs-toolbar">
        <div class="tb-search sm">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Case ID, matter, client, phone…" id="csSearch" autocomplete="off">
        </div>
        <div class="right">
            <div class="filter-chips" id="csStatus">
                <button class="active" data-f="">All</button>
                <button data-f="active">Active</button>
                <button data-f="hearing">Hearing</button>
                <button data-f="pending">Pending</button>
                <button data-f="closed">Closed</button>
            </div>
            <select class="cs-select" id="csArea" aria-label="Practice area"><option value="">All Practice Areas</option></select>
            <select class="cs-select only-superadmin" id="csAdv" aria-label="Advocate"><option value="">All Advocates</option><option value="0">Unassigned</option></select>
        </div>
    </div>

    <div class="table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Case ID</th>
                    <th>Matter</th>
                    <th class="hide-md">Practice</th>
                    <th class="hide-sm">Advocate</th>
                    <th class="hide-sm">Next Date</th>
                    <th>Status</th>
                    <th class="t-right">Action</th>
                </tr>
            </thead>
            <tbody id="csRows"></tbody>
        </table>
    </div>
    <div class="pager" id="csPager"></div>
</div>

@endsection

@push('modals')
<div class="adm-modal" id="csDeleteModal" aria-hidden="true">
    <div class="adm-modal-box glass-card">
        <div class="cs-del">
            <div class="ring"><i class="fa-regular fa-trash-can"></i></div>
            <h4>Delete Case?</h4>
            <p>Case <b id="csDelName"></b> with its Aadhaar, PAN and all documents will be permanently deleted.</p>
            <div class="btns">
                <button class="adm-btn adm-btn-ghost" data-close-modal>Cancel</button>
                <button class="adm-btn adm-btn-danger" id="csDelConfirm"><i class="fa-regular fa-trash-can"></i> Yes, Delete</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
/* ===== CASES LIST – API (Bearer token) ===== */
document.addEventListener('auth:ready', function (ev) {
    if (!window.API) return;
    const me    = ev.detail;
    const isSA  = me.role === 'superadmin';
    const esc   = API.esc;
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const BASE  = @json(url('/superadmin/cases')) + '/';
    const body  = document.getElementById('csRows');
    const pager = document.getElementById('csPager');
    const params = new URLSearchParams(location.search);
    const state = { page: 1, search: '', status: params.get('status') || '', practice_area_id: '', advocate_id: '' };
    const cache = {};
    const STATUS_CLS = { active: 'green', hearing: 'gold', pending: 'blue', closed: 'grey' };
    let delId = null;

    const debounce = (fn, ms = 350) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };
    const initial  = n => (n || '?').replace(/^adv\.?\s+/i, '').charAt(0).toUpperCase();
    const dateCls  = d => {
        if (!d) return '';
        const days = Math.round((new Date(d + 'T00:00:00') - new Date(new Date().toDateString())) / 864e5);
        return days < 0 ? 'past' : (days <= 3 ? 'soon' : '');
    };

    const skeleton = () => {
        body.innerHTML = Array.from({ length: 6 }, () => `<tr class="sk-row">${'<td><span class="sk sk-line w80"></span></td>'.repeat(7)}</tr>`).join('');
    };
    const empty = (msg, isErr) => {
        body.innerHTML = `<tr><td colspan="7"><div class="t-empty ${isErr ? 'error' : ''}">
            <i class="fa-solid ${isErr ? 'fa-triangle-exclamation' : 'fa-folder-open'}"></i><p>${esc(msg)}</p>
            ${!isErr && isSA ? `<a href="${BASE}create" class="adm-btn adm-btn-gold"><i class="fa-solid fa-plus"></i> Add Case</a>` : ''}
        </div></td></tr>`;
    };

    const row = c => {
        const pid = 'csPop' + c.id;
        const adv = c.advocate
            ? `<div class="cs-adv"><span class="av">${esc(initial(c.advocate.name))}</span><span>${esc(c.advocate.name)}</span></div>`
            : `<span class="cs-adv none">Unassigned</span>`;
        const prio = ['urgent', 'high'].includes(c.priority) ? `<span class="prio-dot ${c.priority}" title="${esc(c.priority)} priority"></span>` : '';
        return `<tr data-id="${c.id}">
            <td><a href="${BASE}${c.id}" class="case-id">${esc(c.case_no)}</a></td>
            <td class="cs-matter">
                <div class="t-title" title="${esc(c.title)}">${prio}${esc(c.title)}</div>
                <small class="t-sub cs-client">${esc(c.client_name)} · <span class="cs-kyc">${esc(c.aadhaar_masked)}</span></small>
            </td>
            <td class="hide-md">${c.practice_area ? `<span class="tag">${esc(c.practice_area.name)}</span>` : '<span class="t-sub">—</span>'}</td>
            <td class="hide-sm">${adv}</td>
            <td class="hide-sm"><span class="cs-date ${dateCls(c.next_hearing_date)}">${esc(c.next_hearing || '—')}</span>
                ${c.documents_count ? `<br><span class="cs-docs"><i class="fa-solid fa-paperclip"></i> ${c.documents_count} doc${c.documents_count > 1 ? 's' : ''}</span>` : ''}</td>
            <td><span class="status ${STATUS_CLS[c.status] || 'grey'}">${esc(c.status_label)}</span></td>
            <td class="t-right">
                <div class="popper-wrap">
                    <button class="icon-btn" data-popper="${pid}" aria-label="Actions"><i class="fa-solid fa-ellipsis"></i></button>
                    <div class="popper popper-right" id="${pid}" data-dyn>
                        <ul class="pop-menu">
                            <li><a href="${BASE}${c.id}"><i class="fa-regular fa-eye"></i> View</a></li>
                            ${isSA ? `<li><a href="${BASE}${c.id}/edit"><i class="fa-regular fa-pen-to-square"></i> Edit</a></li>
                            <li class="sep"></li>
                            <li><a href="#" class="danger" data-cdel="${c.id}"><i class="fa-regular fa-trash-can"></i> Delete</a></li>` : ''}
                        </ul>
                    </div>
                </div>
            </td>
        </tr>`;
    };

    const renderPager = meta => {
        if (!meta || !meta.total) { pager.innerHTML = ''; return; }
        const { current_page: cur, last_page: last, from, to, total } = meta;
        const pages = [];
        for (let i = Math.max(1, cur - 2); i <= Math.min(last, cur + 2); i++) pages.push(i);
        pager.innerHTML = `<small>Showing ${from}–${to} of ${total}</small>
            <div class="pages">
                <button class="icon-btn" data-page="${cur - 1}" ${cur <= 1 ? 'disabled' : ''} aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
                ${pages.map(n => `<button class="icon-btn ${n === cur ? 'active' : ''}" data-page="${n}">${n}</button>`).join('')}
                <button class="icon-btn" data-page="${cur + 1}" ${cur >= last ? 'disabled' : ''} aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
            </div>`;
    };

    const query = (extra = {}) => {
        const qs = new URLSearchParams({ page: state.page, ...extra });
        ['search', 'status', 'practice_area_id', 'advocate_id'].forEach(k => { if (state[k] !== '') qs.set(k, state[k]); });
        return qs;
    };

    async function load() {
        document.querySelectorAll('.popper[data-dyn]').forEach(p => p.remove());
        skeleton();
        try {
            const res = await API.get('/cases?' + query());
            res.data.forEach(c => { cache[c.id] = c; });
            Object.entries(res.stats || {}).forEach(([k, v]) => document.querySelectorAll(`[data-cstat="${k}"]`).forEach(el => { el.textContent = v; }));
            const filtered = state.search || state.status || state.practice_area_id || state.advocate_id;
            res.data.length ? (body.innerHTML = res.data.map(row).join(''))
                : empty(filtered ? 'No case matches your filters.' : (isSA ? 'No cases yet. Add your first case!' : 'No cases are assigned to you yet.'));
            renderPager(res.meta);
        } catch (err) { empty(err.message, true); pager.innerHTML = ''; }
    }

    /* ---- dropdowns: practice areas + advocates (DB se) ---- */
    API.get('/cases/options').then(o => {
        document.getElementById('csArea').insertAdjacentHTML('beforeend', (o.practice_areas || []).map(p => `<option value="${p.id}">${esc(p.name)}</option>`).join(''));
        document.getElementById('csAdv').insertAdjacentHTML('beforeend', (o.advocates || []).map(a => `<option value="${a.id}">${esc(a.name)}</option>`).join(''));
    }).catch(() => {});

    /* ---- filters ---- */
    const chips = document.getElementById('csStatus');
    const syncStatus = () => {
        chips.querySelectorAll('button').forEach(b => b.classList.toggle('active', b.dataset.f === state.status));
        document.querySelectorAll('.cs-stat').forEach(c => c.classList.toggle('on', c.dataset.quick === state.status));
    };
    const setStatus = s => { state.status = s; state.page = 1; syncStatus(); load(); };
    chips.addEventListener('click', e => { const b = e.target.closest('button'); if (b) setStatus(b.dataset.f); });
    document.querySelectorAll('.cs-stat').forEach(c => c.addEventListener('click', () => setStatus(c.dataset.quick)));
    document.getElementById('csSearch').addEventListener('input', debounce(e => { state.search = e.target.value.trim(); state.page = 1; load(); }));
    document.getElementById('csArea').addEventListener('change', e => { state.practice_area_id = e.target.value; state.page = 1; load(); });
    document.getElementById('csAdv').addEventListener('change', e => { state.advocate_id = e.target.value; state.page = 1; load(); });
    pager.addEventListener('click', e => { const b = e.target.closest('[data-page]'); if (!b || b.disabled) return; state.page = +b.dataset.page; load(); });

    /* ---- delete ---- */
    document.addEventListener('click', e => {
        const a = e.target.closest('[data-cdel]'); if (!a) return;
        e.preventDefault();
        delId = a.dataset.cdel;
        document.getElementById('csDelName').textContent = cache[delId] ? `${cache[delId].case_no} – ${cache[delId].title}` : '';
        window.admOpenModal('csDeleteModal');
    });
    document.getElementById('csDelConfirm').addEventListener('click', async () => {
        if (!delId) return;
        const btn = document.getElementById('csDelConfirm');
        btn.disabled = true; btn.classList.add('loading');
        try {
            const r = await API.del('/cases/' + delId);
            document.querySelector('#csDeleteModal [data-close-modal]')?.click();
            body.querySelector(`tr[data-id="${delId}"]`)?.classList.add('removing');
            toast(r.message);
            if (body.querySelectorAll('tr[data-id]').length === 1 && state.page > 1) state.page--;
            setTimeout(load, 350);
        } catch (err) { toast(err.message, true); }
        finally { btn.disabled = false; btn.classList.remove('loading'); delId = null; }
    });

    /* ---- export CSV (current filters, max 1000 rows) ---- */
    document.getElementById('csExport').addEventListener('click', async e => {
        const btn = e.currentTarget;
        btn.classList.add('loading'); btn.disabled = true;
        try {
            let rows = [], page = 1, last = 1;
            do {
                const qs = query({ per_page: 100 }); qs.set('page', page);
                const r = await API.get('/cases?' + qs);
                rows = rows.concat(r.data); last = r.meta.last_page; page++;
            } while (page <= last && page <= 10);
            if (!rows.length) { toast('Nothing to export.', true); return; }
            const head = ['Case ID', 'Matter', 'Client', 'Client Phone', 'Aadhaar', 'Practice Area', 'Advocate', 'Court', 'Next Date', 'Priority', 'Status', 'Documents', 'Created'];
            const q = v => `"${String(v ?? '').replace(/"/g, '""')}"`;
            const csv = [head, ...rows.map(c => [c.case_no, c.title, c.client_name, c.client_phone, c.aadhaar_masked, c.practice_area?.name, c.advocate?.name || 'Unassigned',
                c.court_name, c.next_hearing, c.priority, c.status_label, c.documents_count, c.created_at])].map(r => r.map(q).join(',')).join('\r\n');
            const a = document.createElement('a');
            a.href = URL.createObjectURL(new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8' }));
            a.download = 'cases-' + new Date().toISOString().slice(0, 10) + '.csv';
            a.click();
            setTimeout(() => URL.revokeObjectURL(a.href), 2000);
        } catch (err) { toast(err.message, true); }
        finally { btn.classList.remove('loading'); btn.disabled = false; }
    });

    try { const f = sessionStorage.getItem('cs_flash'); if (f) { sessionStorage.removeItem('cs_flash'); setTimeout(() => toast(f), 300); } } catch (e) {}

    syncStatus();
    load();
});
</script>
@endpush