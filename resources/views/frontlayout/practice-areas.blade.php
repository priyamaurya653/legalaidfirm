@extends('frontlayout.app')
@section('title', 'Practice Areas')
@section('page_title', 'Practice Areas')
@section('require_role', 'superadmin')

@section('page_actions')
    <a href="{{ route('superadmin.practice-areas.create') }}" class="adm-btn adm-btn-gold"><i class="fa-solid fa-plus"></i> Add Practice Area</a>
@endsection

@push('styles')
<style>
/* ===== PRACTICE AREAS LIST (isi file me) ===== */
.pa-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 22px; }
.pa-stat { position: relative; display: flex; align-items: center; gap: 14px; padding: 20px; overflow: hidden; cursor: pointer; transition: .35s; }
.pa-stat:hover, .pa-stat.on { transform: translateY(-4px); border-color: rgba(212,175,55,.45); }
.pa-stat.on::after { content: ''; position: absolute; left: 0; bottom: 0; height: 3px; width: 100%; background: var(--gold-grad); }
.pa-stat > i { width: 48px; height: 48px; flex-shrink: 0; border-radius: 14px; display: grid; place-items: center; font-size: 19px; }
.pa-stat b { display: block; font-size: 24px; color: #fff; line-height: 1.1; }
.pa-stat small { color: var(--muted); font-size: 12.5px; }
.pa-stat .i-gold  { color: var(--gold); background: rgba(212,175,55,.12); }
.pa-stat .i-blue  { color: #7aa5ff; background: rgba(91,141,239,.12); }
.pa-stat .i-purple{ color: #b493ff; background: rgba(160,120,255,.12); }
.pa-stat .i-green { color: #5fe0a5; background: rgba(62,207,142,.12); }

.pa-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 12px; justify-content: space-between; margin-bottom: 16px; }
.pa-toolbar .right { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
.pa-select { height: 40px; padding: 0 34px 0 14px; border-radius: 12px; color: #ddd; font: inherit; font-size: 13px; cursor: pointer;
    background: rgba(255,255,255,.04) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath fill='%23d4af37' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E") no-repeat right 14px center;
    border: 1px solid var(--border); appearance: none; }
.pa-select option { background: #111; }

.pa-name { display: flex; align-items: center; gap: 12px; min-width: 0; }
.pa-ico { width: 44px; height: 44px; flex-shrink: 0; border-radius: 13px; display: grid; place-items: center; font-size: 18px; color: #111; background: var(--gold-grad);
    box-shadow: 0 8px 20px rgba(212,175,55,.25); overflow: hidden; }
.pa-ico img { width: 100%; height: 100%; object-fit: cover; }
.pa-name .t-title { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 260px; }
.pa-slug { font-family: ui-monospace, Consolas, monospace; font-size: 11.5px; color: var(--muted); }
.pa-desc { max-width: 300px; color: var(--muted); font-size: 13px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.pa-type { display: inline-flex; align-items: center; gap: 6px; padding: 4px 11px; border-radius: 20px; font-size: 12px; white-space: nowrap; }
.pa-type { color: var(--gold-light); background: rgba(212,175,55,.12); max-width: 200px; overflow: hidden; text-overflow: ellipsis; }
.pa-order { display: inline-grid; place-items: center; min-width: 32px; height: 28px; padding: 0 8px; border-radius: 8px; font-size: 12.5px; color: #ddd; background: rgba(255,255,255,.06); }
.pa-switch-wrap { display: inline-flex; align-items: center; gap: 8px; font-size: 12px; color: var(--muted); }
.pa-switch-wrap.busy { opacity: .5; pointer-events: none; }
.adm-table tr.removing { opacity: 0; transform: translateX(30px); transition: .35s; }

/* delete modal */
.pa-del { text-align: center; padding: 10px 6px 4px; }
.pa-del .ring { width: 76px; height: 76px; margin: 0 auto 16px; border-radius: 50%; display: grid; place-items: center; font-size: 30px; color: #ff6b6b;
    background: rgba(255,107,107,.1); box-shadow: 0 0 0 10px rgba(255,107,107,.05); animation: paPulse 1.6s ease-in-out infinite; }
@keyframes paPulse { 50% { box-shadow: 0 0 0 16px rgba(255,107,107,.02); } }
.pa-del h4 { color: #fff; font-size: 20px; margin-bottom: 8px; }
.pa-del p { color: var(--muted); font-size: 14px; margin-bottom: 22px; }
.pa-del p b { color: var(--gold-light); }
.pa-del .btns { display: flex; gap: 10px; justify-content: center; }
.adm-btn-danger { color: #fff; background: linear-gradient(135deg, #ff7a7a, #d63c3c); border: 0; box-shadow: 0 8px 22px rgba(214,60,60,.35); }

@media (max-width: 991px) { .pa-stats { grid-template-columns: 1fr 1fr; } }
@media (max-width: 575px) {
    .pa-stats { gap: 12px; }
    .pa-stat { padding: 14px; gap: 10px; }
    .pa-stat > i { width: 40px; height: 40px; font-size: 16px; }
    .pa-stat b { font-size: 20px; }
    .pa-toolbar, .pa-toolbar .right { flex-direction: column; align-items: stretch; }
    .pa-toolbar .tb-search { width: 100%; }
    .pa-name .t-title { max-width: 150px; }
}
</style>
@endpush

@section('content')

{{-- ===== Stats (click = filter) ===== --}}
<div class="pa-stats">
    <div class="glass-card pa-stat on" data-quick="all"><i class="fa-solid fa-briefcase i-gold"></i><div><b data-pstat="total">–</b><small>Total Practice Areas</small></div></div>
    <div class="glass-card pa-stat" data-quick="types"><i class="fa-solid fa-layer-group i-purple"></i><div><b data-pstat="types">–</b><small>Types</small></div></div>
    <div class="glass-card pa-stat" data-quick="active"><i class="fa-solid fa-circle-check i-green"></i><div><b data-pstat="active">–</b><small>Active</small></div></div>
    <div class="glass-card pa-stat" data-quick="inactive"><i class="fa-solid fa-circle-pause i-blue"></i><div><b data-pstat="inactive">–</b><small>Inactive</small></div></div>
</div>

<div class="glass-card card">
    <div class="pa-toolbar">
        <div class="tb-search sm">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Search practice area…" id="paSearch" autocomplete="off">
        </div>
        <div class="right">
            {{-- types DB se aate hain (dynamic) --}}
            <select class="pa-select" id="paType" aria-label="Type filter">
                <option value="">All Types</option>
            </select>
            <select class="pa-select" id="paStatus" aria-label="Status filter">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <div class="table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Practice Area</th>
                    <th>Type</th>
                    <th class="hide-md">Short Description</th>
                    <th class="hide-sm">Order</th>
                    <th>Status</th>
                    <th class="hide-md">Updated</th>
                    <th class="t-right">Action</th>
                </tr>
            </thead>
            <tbody id="paRows"></tbody>
        </table>
    </div>
    <div class="pager" id="paPager"></div>
</div>

@endsection

@push('modals')
<div class="adm-modal" id="paDeleteModal" aria-hidden="true">
    <div class="adm-modal-box glass-card">
        <div class="pa-del">
            <div class="ring"><i class="fa-regular fa-trash-can"></i></div>
            <h4>Delete Practice Area?</h4>
            <p>"<b id="paDelName"></b>" will be permanently removed. This cannot be undone.</p>
            <div class="btns">
                <button class="adm-btn adm-btn-ghost" data-close-modal>Cancel</button>
                <button class="adm-btn adm-btn-danger" id="paDelConfirm"><i class="fa-regular fa-trash-can"></i> Yes, Delete</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
/* ===== PRACTICE AREAS LIST – API (Bearer token) ===== */
document.addEventListener('auth:ready', function (ev) {
    if (!window.API || ev.detail.role !== 'superadmin') return;

    const esc   = API.esc;
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const body  = document.getElementById('paRows');
    const pager = document.getElementById('paPager');
    const EDIT  = @json(url('/superadmin/practice-areas')) + '/';
    const state = { page: 1, search: '', type: '', status: '' };
    const cache = {};
    let delId = null;

    const debounce = (fn, ms = 350) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

    const skeleton = () => {
        body.innerHTML = Array.from({ length: 6 }, () =>
            `<tr class="sk-row"><td><div class="t-user"><span class="sk sk-av"></span><div><span class="sk sk-line w140"></span><span class="sk sk-line w100"></span></div></div></td>${'<td><span class="sk sk-line w80"></span></td>'.repeat(6)}</tr>`).join('');
    };
    const empty = (msg, isErr) => {
        body.innerHTML = `<tr><td colspan="7"><div class="t-empty ${isErr ? 'error' : ''}">
            <i class="fa-solid ${isErr ? 'fa-triangle-exclamation' : 'fa-briefcase'}"></i><p>${esc(msg)}</p>
            ${isErr ? '' : `<a href="${EDIT}create" class="adm-btn adm-btn-gold"><i class="fa-solid fa-plus"></i> Add Practice Area</a>`}
        </div></td></tr>`;
    };

    const icon = p => p.image_url
        ? `<span class="pa-ico"><img src="${esc(p.image_url)}" alt="" loading="lazy"></span>`
        : `<span class="pa-ico"><i class="${esc(p.icon || 'fa-solid fa-scale-balanced')}"></i></span>`;

    const row = p => {
        const pid = 'paPop' + p.id;
        const on  = p.status === 'active';
        return `<tr data-id="${p.id}">
            <td><div class="pa-name">${icon(p)}<div style="min-width:0"><div class="t-title" title="${esc(p.name)}">${esc(p.name)}</div><span class="pa-slug">/${esc(p.slug)}</span></div></div></td>
            <td><span class="pa-type" title="${esc(p.type)}"><i class="fa-solid fa-layer-group"></i> ${esc(p.type)}</span></td>
            <td class="hide-md"><div class="pa-desc">${esc(p.short_description || '—')}</div></td>
            <td class="hide-sm"><span class="pa-order">${esc(p.sort_order)}</span></td>
            <td>
                <label class="pa-switch-wrap" title="${on ? 'Click to deactivate' : 'Click to activate'}">
                    <span class="switch"><input type="checkbox" data-toggle="${p.id}" ${on ? 'checked' : ''}><span></span></span>
                    <span class="status ${on ? 'green' : 'grey'}">${on ? 'Active' : 'Inactive'}</span>
                </label>
            </td>
            <td class="hide-md"><small>${esc(p.updated_at || '')}</small>${p.updated_by ? `<br><small class="t-sub">by ${esc(p.updated_by)}</small>` : ''}</td>
            <td class="t-right">
                <div class="popper-wrap">
                    <button class="icon-btn" data-popper="${pid}" aria-label="Actions"><i class="fa-solid fa-ellipsis"></i></button>
                    <div class="popper popper-right" id="${pid}" data-dyn>
                        <ul class="pop-menu">
                            <li><a href="${EDIT}${p.id}/edit"><i class="fa-regular fa-pen-to-square"></i> Edit</a></li>
                            ${p.url ? `<li><a href="${esc(p.url)}" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> View on Website</a></li>` : ''}
                            <li class="sep"></li>
                            <li><a href="#" class="danger" data-pdel="${p.id}"><i class="fa-regular fa-trash-can"></i> Delete</a></li>
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

    const renderStats = s => {
        if (!s) return;
        Object.entries(s).forEach(([k, v]) => document.querySelectorAll(`[data-pstat="${k}"]`).forEach(el => { el.textContent = v; }));
    };

    async function load() {
        document.querySelectorAll('.popper[data-dyn]').forEach(p => p.remove());
        skeleton();
        const qs = new URLSearchParams({ page: state.page });
        if (state.search) qs.set('search', state.search);
        if (state.type)   qs.set('type', state.type);
        if (state.status) qs.set('status', state.status);
        try {
            const res = await API.get('/practice-areas?' + qs);
            res.data.forEach(p => { cache[p.id] = p; });
            renderStats(res.stats);
            renderTypeFilter(res.types);
            if (res.data.length) body.innerHTML = res.data.map(row).join('');
            else empty(state.search || state.type || state.status ? 'No practice area matches your filters.' : 'No practice areas yet. Add your first one!');
            renderPager(res.meta);
        } catch (err) { empty(err.message, true); pager.innerHTML = ''; }
    }

    /* ---- filters ---- */
    document.getElementById('paSearch').addEventListener('input', debounce(e => { state.search = e.target.value.trim(); state.page = 1; load(); }));

    const typeSel   = document.getElementById('paType');
    const statusSel = document.getElementById('paStatus');
    // DB ke types se dropdown banao (selected value bachi rahe)
    function renderTypeFilter(types) {
        if (!Array.isArray(types)) return;
        const cur = state.type;
        typeSel.innerHTML = '<option value="">All Types</option>' +
            types.map(t => `<option value="${esc(t)}" ${t === cur ? 'selected' : ''}>${esc(t)}</option>`).join('');
        if (cur && !types.includes(cur)) { state.type = ''; }
    }
    const syncQuick = () => {
        const q = state.type ? '' : (state.status || 'all');
        document.querySelectorAll('.pa-stat').forEach(c => c.classList.toggle('on', c.dataset.quick === q));
    };
    typeSel.addEventListener('change', () => { state.type = typeSel.value; state.page = 1; syncQuick(); load(); });
    statusSel.addEventListener('change', () => { state.status = statusSel.value; state.page = 1; syncQuick(); load(); });
    document.querySelectorAll('.pa-stat').forEach(c => c.addEventListener('click', () => {
        const q = c.dataset.quick;
        state.type   = '';
        state.status = (q === 'active' || q === 'inactive') ? q : '';
        state.page = 1; typeSel.value = ''; statusSel.value = state.status; syncQuick(); load();
    }));
    pager.addEventListener('click', e => { const b = e.target.closest('[data-page]'); if (!b || b.disabled) return; state.page = +b.dataset.page; load(); });

    /* ---- status toggle (PATCH) ---- */
    body.addEventListener('change', async e => {
        const inp = e.target.closest('[data-toggle]'); if (!inp) return;
        const id = inp.dataset.toggle, wrap = inp.closest('.pa-switch-wrap'), pill = wrap.querySelector('.status');
        const status = inp.checked ? 'active' : 'inactive';
        wrap.classList.add('busy');
        try {
            const r = await API.patch(`/practice-areas/${id}/status`, { status });
            pill.className = 'status ' + (inp.checked ? 'green' : 'grey');
            pill.textContent = inp.checked ? 'Active' : 'Inactive';
            toast(r.message);
            if (state.status) load(); else API.get('/practice-areas?per_page=5').then(x => renderStats(x.stats)).catch(() => {});
        } catch (err) { inp.checked = !inp.checked; toast(err.message, true); }
        finally { wrap.classList.remove('busy'); }
    });

    /* ---- delete (modal) ---- */
    document.addEventListener('click', e => {
        const a = e.target.closest('[data-pdel]'); if (!a) return;
        e.preventDefault();
        delId = a.dataset.pdel;
        document.getElementById('paDelName').textContent = cache[delId] ? cache[delId].name : '';
        window.admOpenModal ? window.admOpenModal('paDeleteModal') : (confirm('Delete this practice area?') && doDelete());
    });
    document.getElementById('paDelConfirm').addEventListener('click', doDelete);

    async function doDelete() {
        if (!delId) return;
        const btn = document.getElementById('paDelConfirm');
        btn.disabled = true; btn.classList.add('loading');
        try {
            const r = await API.del(`/practice-areas/${delId}`);
            document.querySelector('#paDeleteModal [data-close-modal]')?.click();
            body.querySelector(`tr[data-id="${delId}"]`)?.classList.add('removing');
            toast(r.message);
            if (body.querySelectorAll('tr[data-id]').length === 1 && state.page > 1) state.page--;
            setTimeout(load, 350);
        } catch (err) { toast(err.message, true); }
        finally { btn.disabled = false; btn.classList.remove('loading'); delId = null; }
    }

    // create/edit page se aaya flash message
    try {
        const f = sessionStorage.getItem('pa_flash');
        if (f) { sessionStorage.removeItem('pa_flash'); setTimeout(() => toast(f), 300); }
    } catch (e) {}

    load();
});
</script>
@endpush