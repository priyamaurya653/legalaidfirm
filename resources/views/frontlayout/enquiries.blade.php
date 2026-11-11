@extends('frontlayout.app')
@section('title', 'Enquiries')
@section('page_title', 'Enquiries')
@section('require_role', 'superadmin')

@push('styles')
<style>
/* ===== ENQUIRIES (layout same – data API se) ===== */
.enq-toolbar { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between; margin-bottom: 18px; }
.enq-chips button b { margin-left: 4px; font-weight: 600; opacity: .75; }
.enq-card { cursor: pointer; }
.enq-card.unread { border-color: rgba(212,175,55,.4); box-shadow: inset 3px 0 0 var(--gold); }
.enq-card.unread h5::after { content: ''; display: inline-block; width: 8px; height: 8px; margin-left: 8px; border-radius: 50%; background: var(--gold); box-shadow: 0 0 10px var(--gold); vertical-align: middle; }
.enq-card p { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; overflow-wrap: anywhere; }
.enq-top > div { min-width: 0; }
.enq-top h5 { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.enq-sk { height: 210px; border-radius: 20px; background: linear-gradient(90deg, rgba(255,255,255,.03) 25%, rgba(255,255,255,.07) 50%, rgba(255,255,255,.03) 75%); background-size: 200% 100%; animation: enqSh 1.3s linear infinite; }
@keyframes enqSh { to { background-position: -200% 0; } }
.enq-empty { grid-column: 1 / -1; }
.enq-pager { margin-top: 18px; }

/* view modal */
.ev-head { display: flex; align-items: center; gap: 14px; margin-bottom: 18px; }
.ev-head .avatar-txt { width: 54px; height: 54px; font-size: 22px; }
.ev-head h4 { color: #fff; font-size: 19px; margin: 0 0 2px; }
.ev-head small { color: var(--muted); font-size: 12.5px; }
.ev-kv { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 18px; margin-bottom: 16px; }
.ev-kv small { display: block; color: var(--muted); font-size: 12px; margin-bottom: 2px; }
.ev-kv b { color: #eee; font-size: 14px; font-weight: 500; overflow-wrap: anywhere; }
.ev-msg { padding: 16px; border-radius: 14px; background: rgba(255,255,255,.03); border: 1px solid var(--border); color: #ddd; font-size: 14px; line-height: 1.7; white-space: pre-wrap; overflow-wrap: anywhere; margin-bottom: 18px; max-height: 260px; overflow-y: auto; }
.ev-status { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-bottom: 18px; }
.ev-status > small { color: var(--muted); font-size: 12px; margin-right: 4px; }
.ev-status button.active { color: #111; background: var(--gold-grad); border-color: transparent; }
.ev-actions { display: flex; flex-wrap: wrap; gap: 10px; }
.ev-actions .adm-btn { flex: 1; justify-content: center; min-width: 120px; }
@media (max-width: 575px) { .ev-kv { grid-template-columns: 1fr; } .enq-toolbar { flex-direction: column; align-items: stretch; } .enq-toolbar .tb-search { width: 100%; } }
</style>
@endpush

@section('content')

<div class="enq-toolbar">
    <div class="tb-search sm">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" placeholder="Search name, phone, email, practice…" id="enqSearch" autocomplete="off">
    </div>
    <div class="filter-chips enq-chips" id="enqFilter">
        <button class="active" data-f="">All <b data-es="total"></b></button>
        <button data-f="new">New <b data-es="new"></b></button>
        <button data-f="contacted">Contacted <b data-es="contacted"></b></button>
        <button data-f="closed">Closed <b data-es="closed"></b></button>
    </div>
</div>

<div class="enq-grid" id="enqGrid">
    @for($i = 0; $i < 6; $i++)<div class="enq-sk"></div>@endfor
</div>
<div class="pager enq-pager" id="enqPager"></div>

@endsection

@push('modals')
<div class="adm-modal" id="enqViewModal" aria-hidden="true">
    <div class="adm-modal-box glass-card">
        <div class="modal-head">
            <h4><i class="fa-solid fa-envelope-open-text"></i> Enquiry Details</h4>
            <button class="icon-btn" data-close-modal aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="enqViewBody"></div>
    </div>
</div>
@endpush

@push('scripts')
<script>
/* ===== ENQUIRIES – API (Bearer token) ===== */
document.addEventListener('auth:ready', function (ev) {
    if (!window.API || ev.detail.role !== 'superadmin') return;

    const esc   = API.esc;
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const grid  = document.getElementById('enqGrid');
    const pager = document.getElementById('enqPager');
    const vbody = document.getElementById('enqViewBody');
    const CASE_NEW = @json(route('superadmin.cases.create'));
    const CLS   = { new: 'gold', contacted: 'blue', closed: 'grey' };
    const state = { page: 1, search: '', status: '' };
    const cache = {};

    const debounce = (fn, ms = 350) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };
    const wa = p => 'https://wa.me/91' + String(p || '').replace(/\D/g, '').slice(-10);

    const card = (e, i) => `
        <div class="glass-card enq-card ${e.is_read ? '' : 'unread'}" style="--i:${i}" data-view="${e.id}">
            <div class="enq-top">
                <span class="avatar-txt lg">${esc((e.name || '?').charAt(0).toUpperCase())}</span>
                <div>
                    <h5 title="${esc(e.name)}">${esc(e.name)}</h5>
                    <small><i class="fa-solid fa-phone"></i> +91 ${esc(e.phone)}</small>
                </div>
                <span class="status ${CLS[e.status] || 'grey'}">${esc(e.status_label)}</span>
            </div>
            <span class="tag">${esc(e.practice_area)}</span>
            <p>${esc(e.message)}</p>
            <div class="enq-foot">
                <small title="${esc(e.created_at)}"><i class="fa-regular fa-clock"></i> ${esc(e.time_ago)}</small>
                <div class="enq-actions">
                    <a href="tel:+91${esc(e.phone)}" class="icon-btn" title="Call" data-stop><i class="fa-solid fa-phone"></i></a>
                    <a href="${wa(e.phone)}" target="_blank" class="icon-btn" title="WhatsApp" data-stop><i class="fa-brands fa-whatsapp"></i></a>
                    <button class="adm-btn adm-btn-gold sm" data-view="${e.id}"><i class="fa-regular fa-eye"></i> View</button>
                </div>
            </div>
        </div>`;

    const renderPager = meta => {
        if (!meta || meta.last_page <= 1) { pager.innerHTML = meta && meta.total ? `<small>Showing ${meta.from}–${meta.to} of ${meta.total}</small>` : ''; return; }
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

    const setBadge = n => { const b = document.getElementById('enqBadge'); if (b) { b.textContent = n; b.hidden = !n; } };

    async function load() {
        grid.innerHTML = '<div class="enq-sk"></div>'.repeat(6);
        const qs = new URLSearchParams({ page: state.page });
        if (state.search) qs.set('search', state.search);
        if (state.status) qs.set('status', state.status);
        try {
            const res = await API.get('/enquiries?' + qs);
            res.data.forEach(e => { cache[e.id] = e; });
            Object.entries(res.stats || {}).forEach(([k, v]) => document.querySelectorAll(`[data-es="${k}"]`).forEach(el => { el.textContent = v; }));
            setBadge(res.stats?.unread || 0);
            grid.innerHTML = res.data.length
                ? res.data.map(card).join('')
                : `<div class="glass-card card enq-empty"><div class="t-empty"><i class="fa-regular fa-envelope"></i><p>${state.search || state.status ? 'No enquiry matches your filters.' : 'No enquiries yet. Website contact form submissions will appear here.'}</p></div></div>`;
            renderPager(res.meta);
        } catch (err) {
            grid.innerHTML = `<div class="glass-card card enq-empty"><div class="t-empty error"><i class="fa-solid fa-triangle-exclamation"></i><p>${esc(err.message)}</p></div></div>`;
            pager.innerHTML = '';
        }
    }

    /* ---------- view ---------- */
    function renderView(e) {
        vbody.innerHTML = `
            <div class="ev-head">
                <span class="avatar-txt">${esc((e.name || '?').charAt(0).toUpperCase())}</span>
                <div><h4>${esc(e.name)}</h4><small><i class="fa-regular fa-clock"></i> ${esc(e.created_at)} · ${esc(e.time_ago)}</small></div>
            </div>
            <div class="ev-kv">
                <div><small>Phone</small><b><a class="gold" href="tel:+91${esc(e.phone)}">+91 ${esc(e.phone)}</a></b></div>
                <div><small>Email</small><b><a class="gold" href="mailto:${esc(e.email)}">${esc(e.email)}</a></b></div>
                <div><small>Practice Area</small><b>${esc(e.practice_area)}</b></div>
                <div><small>First viewed</small><b>${esc(e.read_at || 'Just now')}</b></div>
            </div>
            <small class="muted-sm">Legal matter</small>
            <div class="ev-msg">${esc(e.message)}</div>
            <div class="ev-status filter-chips" id="evStatus">
                <small>Mark as:</small>
                ${['new', 'contacted', 'closed'].map(s => `<button data-status="${s}" class="${e.status === s ? 'active' : ''}">${s.charAt(0).toUpperCase() + s.slice(1)}</button>`).join('')}
            </div>
            <div class="ev-actions">
                <a href="tel:+91${esc(e.phone)}" class="adm-btn adm-btn-ghost"><i class="fa-solid fa-phone"></i> Call</a>
                <a href="${wa(e.phone)}" target="_blank" class="adm-btn adm-btn-ghost"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
                <a href="mailto:${esc(e.email)}?subject=${encodeURIComponent('Your enquiry – Lexora Legal')}" class="adm-btn adm-btn-ghost"><i class="fa-regular fa-envelope"></i> Email</a>
                <a href="${CASE_NEW}" class="adm-btn adm-btn-gold"><i class="fa-solid fa-gavel"></i> Convert to Case</a>
            </div>`;
        vbody.dataset.id = e.id;
    }

    async function openView(id) {
        const e = cache[id];
        if (e) renderView(e);
        else vbody.innerHTML = '<div class="enq-sk" style="height:240px"></div>';
        window.admOpenModal('enqViewModal');
        try {
            const r = await API.get('/enquiries/' + id);     // read mark hota hai
            const wasUnread = e && !e.is_read;
            cache[id] = r.data;
            renderView(r.data);
            if (wasUnread) {
                grid.querySelector(`.enq-card[data-view="${id}"]`)?.classList.remove('unread');
                const b = document.getElementById('enqBadge');
                if (b) setBadge(Math.max(0, (+b.textContent || 0) - 1));
            }
        } catch (err) { toast(err.message, true); }
    }

    grid.addEventListener('click', e => {
        if (e.target.closest('[data-stop]')) return;      // call / whatsapp
        const v = e.target.closest('[data-view]');
        if (v) openView(v.dataset.view);
    });

    vbody.addEventListener('click', async e => {
        const b = e.target.closest('[data-status]'); if (!b || b.classList.contains('active')) return;
        const id = vbody.dataset.id;
        b.classList.add('loading');
        try {
            const r = await API.patch(`/enquiries/${id}/status`, { status: b.dataset.status });
            cache[id] = r.data;
            renderView(r.data);
            toast(r.message);
            load();
        } catch (err) { toast(err.message, true); b.classList.remove('loading'); }
    });

    /* ---------- filters ---------- */
    document.getElementById('enqSearch').addEventListener('input', debounce(e => { state.search = e.target.value.trim(); state.page = 1; load(); }));
    document.getElementById('enqFilter').addEventListener('click', e => {
        const b = e.target.closest('button'); if (!b) return;
        document.querySelectorAll('#enqFilter button').forEach(x => x.classList.toggle('active', x === b));
        state.status = b.dataset.f; state.page = 1; load();
    });
    pager.addEventListener('click', e => { const b = e.target.closest('[data-page]'); if (!b || b.disabled) return; state.page = +b.dataset.page; load(); window.scrollTo({ top: 0, behavior: 'smooth' }); });

    load();
});
</script>
@endpush