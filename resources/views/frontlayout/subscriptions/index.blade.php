@extends('frontlayout.app')
@section('title', 'Admin Subscriptions')
@section('page_title', 'Admin Subscriptions')
@section('require_role', 'superadmin')

@section('page_actions')
    <a href="{{ route('superadmin.subscriptions.plans') }}" class="adm-btn adm-btn-ghost"><i class="fa-solid fa-crown"></i> <span class="hide-xs">Plans</span></a>
    <button class="adm-btn adm-btn-gold" id="sbAssignBtn"><i class="fa-solid fa-user-check"></i> Give Subscription</button>
@endsection

@push('styles')
<style>
/* ===== ADMIN SUBSCRIPTIONS (isi file me) ===== */
.sb-stats { display: grid; grid-template-columns: repeat(6, 1fr); gap: 14px; margin-bottom: 20px; }
.sb-stat { display: flex; align-items: center; gap: 12px; padding: 15px 16px; }
.sb-stat b { display: block; font-size: 21px; color: #fff; line-height: 1.1; }
.sb-stat small { color: var(--muted); font-size: 12px; }
.sc-icon.red { background: rgba(255,107,107,.12); color: #ff8c8c; }
.sb-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 18px; }
.sb-tabs button { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 12px; font: inherit; font-size: 13.5px; color: #bbb; cursor: pointer; border: 1px solid var(--border); background: rgba(255,255,255,.03); transition: .25s; }
.sb-tabs button b { font-size: 11px; padding: 2px 8px; border-radius: 10px; background: rgba(255,255,255,.08); color: #ddd; }
.sb-tabs button.active { color: #111; font-weight: 600; background: var(--gold-grad); border-color: transparent; }
.sb-tabs button.active b { background: rgba(0,0,0,.15); color: #111; }
.sb-tabs button.hot b { background: #e05252; color: #fff; }
.sb-panel { display: none; } .sb-panel.active { display: block; animation: fadeUp .4s ease both; }

/* awaiting cards */
.aw-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: 16px; }
.aw-card { padding: 20px; display: flex; flex-direction: column; gap: 12px; animation: fadeUp .5s ease both; animation-delay: calc(var(--i) * .05s); }
.aw-card:hover { border-color: rgba(212,175,55,.4); }
.aw-top { display: flex; align-items: center; gap: 12px; }
.aw-top > div { min-width: 0; flex: 1; }
.aw-top h5 { color: #fff; font-size: 15px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.aw-top small { color: var(--muted); font-size: 12px; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.aw-meta { display: flex; flex-wrap: wrap; gap: 6px 14px; font-size: 12.5px; color: var(--muted); }
.aw-meta i { color: var(--gold); margin-right: 4px; }
.aw-card .adm-btn { justify-content: center; }
.aw-new { font-size: 10.5px; padding: 3px 8px; border-radius: 10px; color: #111; background: var(--gold-grad); font-weight: 600; }

.sb-period { font-size: 12.5px; color: #ccc; white-space: nowrap; }
.sb-left { display: block; font-size: 11.5px; color: var(--muted); }
.sb-left.soon { color: #ffb347; }
.sb-bar { height: 4px; border-radius: 4px; background: rgba(255,255,255,.07); margin-top: 5px; width: 110px; overflow: hidden; }
.sb-bar i { display: block; height: 100%; background: var(--gold-grad); }
.status.red { color: #ff8c8c; background: rgba(255,107,107,.1); }
.rq-item { display: flex; gap: 14px; align-items: flex-start; padding: 16px; border-radius: 14px; border: 1px solid var(--border); background: rgba(255,255,255,.02); margin-bottom: 10px; }
.rq-item > div { flex: 1; min-width: 0; }
.rq-item h6 { color: #fff; font-size: 14.5px; margin: 0 0 3px; }
.rq-item p { color: #ccc; font-size: 13px; margin: 6px 0 0; white-space: pre-wrap; overflow-wrap: anywhere; }
.rq-item .acts { display: flex; gap: 6px; flex-wrap: wrap; justify-content: flex-end; }

/* assign modal */
.req { color: #ff7a7a; font-weight: 600; }
.as-plans { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 10px; }
.f-group .as-plans label { margin: 0; cursor: pointer; }
.as-plans input { position: absolute; opacity: 0; width: 0 !important; height: 0; padding: 0 !important; border: 0 !important; }
.as-plans .box { display: block; padding: 14px; border-radius: 14px; border: 1px solid var(--border); background: rgba(255,255,255,.02); transition: .25s; }
.as-plans .box b { display: block; color: #fff; font-size: 14px; }
.as-plans .box em { font-style: normal; display: block; font-family: var(--serif, serif); font-size: 22px; color: var(--gold-light); margin: 4px 0 2px; }
.as-plans .box small { color: var(--muted); font-size: 11.5px; }
.as-plans input:checked + .box { border-color: var(--gold); background: linear-gradient(140deg, rgba(212,175,55,.16), rgba(255,255,255,.02)); box-shadow: 0 8px 22px rgba(212,175,55,.15); }
.as-info { display: flex; gap: 10px; align-items: center; padding: 12px 14px; border-radius: 12px; font-size: 13px; color: #ddd; background: rgba(62,207,142,.07); border: 1px solid rgba(62,207,142,.3); margin-bottom: 16px; }
.as-info i { color: #5fe0a5; }
.as-cur { font-size: 12px; color: #ffb347; margin-top: 6px; display: block; }
@media (max-width: 1199px) { .sb-stats { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 575px) { .sb-stats { grid-template-columns: repeat(2, 1fr); } .rq-item { flex-direction: column; } }
</style>
@endpush

@section('content')

<div class="sb-stats">
    <div class="glass-card sb-stat"><span class="sc-icon green"><i class="fa-solid fa-circle-check"></i></span><div><b data-sb="active">–</b><small>Active Admins</small></div></div>
    <div class="glass-card sb-stat"><span class="sc-icon gold"><i class="fa-solid fa-hourglass-half"></i></span><div><b data-sb="awaiting">–</b><small>Awaiting Acceptance</small></div></div>
    <div class="glass-card sb-stat"><span class="sc-icon blue"><i class="fa-regular fa-clock"></i></span><div><b data-sb="expiring_soon">–</b><small>Expiring ≤ 5 days</small></div></div>
    <div class="glass-card sb-stat"><span class="sc-icon red"><i class="fa-solid fa-ban"></i></span><div><b data-sb="expired">–</b><small>Expired</small></div></div>
    <div class="glass-card sb-stat"><span class="sc-icon purple"><i class="fa-solid fa-envelope-open-text"></i></span><div><b data-sb="open_requests">–</b><small>Renewal Requests</small></div></div>
    <div class="glass-card sb-stat"><span class="sc-icon gold"><i class="fa-solid fa-indian-rupee-sign"></i></span><div><b data-sb="revenue_month">–</b><small>This Month</small></div></div>
</div>

<div class="sb-tabs" id="sbTabs">
    <button class="active" data-tab="awaiting"><i class="fa-solid fa-user-clock"></i> Awaiting Acceptance <b data-sb="awaiting">0</b></button>
    <button data-tab="expired"><i class="fa-solid fa-rotate"></i> Expired / Renew <b data-sb="expired">0</b></button>
    <button data-tab="requests"><i class="fa-solid fa-envelope"></i> Requests <b data-sb="open_requests">0</b></button>
    <button data-tab="all"><i class="fa-solid fa-list"></i> All Subscriptions</button>
</div>

{{-- Awaiting acceptance --}}
<div class="sb-panel active" id="tab-awaiting">
    <div class="aw-grid" id="awGrid"></div>
</div>

{{-- Expired --}}
<div class="sb-panel" id="tab-expired">
    <div class="glass-card card">
        <div class="table-wrap"><table class="adm-table">
            <thead><tr><th>Admin</th><th class="hide-sm">Last Plan</th><th>Ended</th><th class="t-right">Action</th></tr></thead>
            <tbody id="exRows"></tbody>
        </table></div>
    </div>
</div>

{{-- Requests --}}
<div class="sb-panel" id="tab-requests">
    <div class="glass-card card">
        <div class="toolbar"><div class="filter-chips" id="rqFilter"><button class="active" data-f="open">Open</button><button data-f="resolved">Resolved</button><button data-f="dismissed">Dismissed</button><button data-f="">All</button></div></div>
        <div id="rqList"></div>
    </div>
</div>

{{-- All --}}
<div class="sb-panel" id="tab-all">
    <div class="glass-card card">
        <div class="toolbar">
            <div class="tb-search sm"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Search admin or plan…" id="sbSearch" autocomplete="off"></div>
            <div class="filter-chips" id="sbStatus"><button class="active" data-f="">All</button><button data-f="active">Active</button><button data-f="expired">Expired</button><button data-f="cancelled">Cancelled</button></div>
        </div>
        <div class="table-wrap"><table class="adm-table">
            <thead><tr><th>Admin</th><th>Plan</th><th class="hide-sm">Period</th><th>Status</th><th class="hide-md">Given By</th><th class="t-right">Action</th></tr></thead>
            <tbody id="sbRows"></tbody>
        </table></div>
        <div class="pager" id="sbPager"></div>
    </div>
</div>

@endsection

@push('modals')
<div class="adm-modal" id="asModal" aria-hidden="true">
    <div class="adm-modal-box glass-card wide">
        <div class="modal-head">
            <h4><i class="fa-solid fa-user-check"></i> Accept & Give Subscription</h4>
            <button class="icon-btn" data-close-modal aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form class="adm-form" id="asForm" novalidate>
            <div class="as-info"><i class="fa-solid fa-circle-info"></i><span>The subscription starts <b>immediately</b> (or right after the admin's current plan ends) and the admin's account is activated.</span></div>
            <div class="f-grid">
                <div class="f-group full" data-field="admin_id"><label>Admin <b class="req">*</b></label><select name="admin_id" required><option value="">Select admin</option></select><small class="as-cur" id="asCur" hidden></small></div>
                <div class="f-group full" data-field="plan_id"><label>Plan <b class="req">*</b></label><div class="as-plans" id="asPlans"></div></div>
                <div class="f-group full" data-field="note"><label>Note for admin</label><textarea name="note" rows="2" maxlength="1000" placeholder="e.g. Payment received via UPI on 18 Oct"></textarea></div>
            </div>
            <div class="modal-foot">
                <button type="button" class="adm-btn adm-btn-ghost" data-close-modal>Cancel</button>
                <button type="submit" class="adm-btn adm-btn-gold" id="asSave"><i class="fa-solid fa-check"></i> Accept & Activate</button>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
/* ===== ADMIN SUBSCRIPTIONS – API (Bearer token) ===== */
document.addEventListener('auth:ready', function (ev) {
    if (!window.API || ev.detail.role !== 'superadmin') return;
    const esc = API.esc, $ = i => document.getElementById(i);
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const form = $('asForm'), el = n => form.elements.namedItem(n);
    const inr = n => '₹' + Number(n || 0).toLocaleString('en-IN', { maximumFractionDigits: 2 });
    const ini = n => esc((n || '?').charAt(0).toUpperCase());
    const PLANS_URL = @json(route('superadmin.subscriptions.plans'));
    const qp = new URLSearchParams(location.search);
    const state = { page: 1, search: '', status: '' };
    let admins = [], plans = [], rqStatus = 'open';
    const SCLS = { active: 'green', expired: 'red', cancelled: 'grey' };
    const debounce = (fn, ms = 350) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

    /* ---------- tabs ---------- */
    $('sbTabs').addEventListener('click', e => {
        const b = e.target.closest('[data-tab]'); if (!b) return;
        $('sbTabs').querySelectorAll('button').forEach(x => x.classList.toggle('active', x === b));
        document.querySelectorAll('.sb-panel').forEach(p => p.classList.toggle('active', p.id === 'tab-' + b.dataset.tab));
        if (b.dataset.tab === 'requests') loadRequests();
    });

    /* ---------- main load ---------- */
    const row = s => {
        const soon = s.status === 'active' && s.days_left <= 5;
        return `<tr data-id="${s.id}">
            <td><div class="t-user"><span class="avatar-txt">${ini(s.admin?.name)}</span><div><div class="t-title">${esc(s.admin?.name || 'Deleted admin')}</div><small class="t-sub">${esc(s.admin?.email || '')}</small></div></div></td>
            <td><b style="color:#fff;font-weight:500">${esc(s.plan_name)}</b><br><small class="t-sub">${inr(s.price)} · ${s.duration_days} days${s.max_users ? ' · ' + s.max_users + ' users' : ''}</small></td>
            <td class="hide-sm"><span class="sb-period">${esc(s.starts_at || '—')} → ${esc(s.ends_at || '—')}</span>
                ${s.status === 'active' ? `<span class="sb-left ${soon ? 'soon' : ''}">${s.days_left} days left</span><div class="sb-bar"><i style="width:${s.progress || 0}%"></i></div>` : ''}</td>
            <td><span class="status ${s.status_label === 'Scheduled' ? 'blue' : (SCLS[s.status] || 'grey')}">${esc(s.status_label)}</span></td>
            <td class="hide-md"><small>${esc(s.assigned_by || '—')}</small><br><small class="t-sub">${esc(s.accepted_at || '')}</small></td>
            <td class="t-right"><div class="popper-wrap">
                <button class="icon-btn" data-popper="sbPop${s.id}" aria-label="Actions"><i class="fa-solid fa-ellipsis"></i></button>
                <div class="popper popper-right" id="sbPop${s.id}" data-dyn><ul class="pop-menu">
                    ${s.admin ? `<li><a href="#" data-assign="${s.admin.id}"><i class="fa-solid fa-rotate"></i> Renew / Extend</a></li>` : ''}
                    ${s.status === 'active' ? `<li><a href="#" class="danger" data-cancel="${s.id}"><i class="fa-solid fa-ban"></i> Cancel</a></li>` : `<li><a href="#" class="danger" data-del="${s.id}"><i class="fa-regular fa-trash-can"></i> Delete Record</a></li>`}
                </ul></div></div></td>
        </tr>`;
    };
    const renderPager = meta => {
        const pager = $('sbPager');
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
        const qs = new URLSearchParams({ page: state.page });
        if (state.search) qs.set('search', state.search);
        if (state.status) qs.set('status', state.status);
        try {
            const r = await API.get('/subscriptions?' + qs);
            admins = r.admins || []; plans = r.plans || [];
            Object.entries(r.stats || {}).forEach(([k, v]) => document.querySelectorAll(`[data-sb="${k}"]`).forEach(x => { x.textContent = k === 'revenue_month' ? inr(v) : v; }));
            document.querySelector('[data-tab="requests"]').classList.toggle('hot', (r.stats?.open_requests || 0) > 0);

            // awaiting acceptance
            const aw = r.awaiting || [];
            $('awGrid').innerHTML = aw.length ? aw.map((a, i) => `
                <div class="glass-card aw-card" style="--i:${i}">
                    <div class="aw-top"><span class="avatar-txt lg">${ini(a.name)}</span><div><h5>${esc(a.name)}</h5><small>${esc(a.email)}</small></div><span class="aw-new">NEW</span></div>
                    <div class="aw-meta">
                        ${a.phone ? `<span><i class="fa-solid fa-phone"></i>+91 ${esc(a.phone)}</span>` : ''}
                        ${a.city ? `<span><i class="fa-solid fa-location-dot"></i>${esc(a.city)}</span>` : ''}
                        ${a.practice ? `<span><i class="fa-solid fa-scale-balanced"></i>${esc(a.practice)}</span>` : ''}
                        <span title="${esc(a.joined)}"><i class="fa-regular fa-clock"></i>Joined ${esc(a.joined_ago)}</span>
                        <span><i class="fa-solid fa-circle-dot"></i>Account: ${esc(a.status)}</span>
                    </div>
                    <button class="adm-btn adm-btn-gold" data-assign="${a.id}"><i class="fa-solid fa-user-check"></i> Accept & Give Subscription</button>
                </div>`).join('')
                : `<div class="glass-card card" style="grid-column:1/-1"><div class="t-empty"><i class="fa-solid fa-circle-check" style="color:#5fe0a5"></i><p>No admins are waiting – everyone has been accepted.</p></div></div>`;

            // expired
            const ex = r.expired_admins || [];
            $('exRows').innerHTML = ex.length ? ex.map(a => `<tr>
                <td><div class="t-user"><span class="avatar-txt">${ini(a.name)}</span><div><div class="t-title">${esc(a.name)}</div><small class="t-sub">${esc(a.email)}</small></div></div></td>
                <td class="hide-sm">${esc(a.last_plan || '—')}</td>
                <td><span class="status ${a.last_status === 'cancelled' ? 'grey' : 'red'}">${esc(a.last_status === 'cancelled' ? 'Cancelled' : 'Expired')}</span><br><small class="t-sub">${esc(a.ended || '')}</small></td>
                <td class="t-right">${a.phone ? `<a href="tel:+91${esc(a.phone)}" class="icon-btn" title="Call"><i class="fa-solid fa-phone"></i></a> ` : ''}<button class="adm-btn adm-btn-gold sm" data-assign="${a.id}"><i class="fa-solid fa-rotate"></i> Renew</button></td>
            </tr>`).join('') : `<tr><td colspan="4"><div class="t-empty"><i class="fa-solid fa-circle-check" style="color:#5fe0a5"></i><p>No expired subscriptions.</p></div></td></tr>`;

            // all
            $('sbRows').innerHTML = r.data.length ? r.data.map(row).join('') : `<tr><td colspan="6"><div class="t-empty"><i class="fa-solid fa-crown"></i><p>No subscriptions yet.</p></div></td></tr>`;
            renderPager(r.meta);
            renderPlans();
            if (qp.get('plan') && !load.done) { load.done = true; openAssign(null, qp.get('plan')); }
        } catch (err) { toast(err.message, true); }
    }

    $('sbSearch').addEventListener('input', debounce(e => { state.search = e.target.value.trim(); state.page = 1; load(); }));
    $('sbStatus').addEventListener('click', e => { const b = e.target.closest('button'); if (!b) return; $('sbStatus').querySelectorAll('button').forEach(x => x.classList.toggle('active', x === b)); state.status = b.dataset.f; state.page = 1; load(); });
    $('sbPager').addEventListener('click', e => { const b = e.target.closest('[data-page]'); if (!b || b.disabled) return; state.page = +b.dataset.page; load(); });

    /* ---------- requests ---------- */
    async function loadRequests() {
        $('rqList').innerHTML = '<div class="t-empty"><i class="fa-solid fa-spinner fa-spin"></i></div>';
        try {
            const r = await API.get('/subscription-requests' + (rqStatus ? '?status=' + rqStatus : ''));
            $('rqList').innerHTML = r.data.length ? r.data.map(q => `
                <div class="rq-item">
                    <span class="avatar-txt">${ini(q.admin?.name)}</span>
                    <div><h6>${esc(q.admin?.name || 'Deleted admin')} <span class="status ${q.status === 'open' ? 'gold' : 'grey'}">${esc(q.status)}</span></h6>
                        <small class="t-sub">${esc(q.admin?.email || '')}${q.admin?.phone ? ' · +91 ' + esc(q.admin.phone) : ''} · ${esc(q.time_ago)}</small>
                        ${q.plan ? `<div style="margin-top:6px"><span class="tag">Wants: ${esc(q.plan.name)} · ${inr(q.plan.price)}</span></div>` : ''}
                        ${q.message ? `<p>${esc(q.message)}</p>` : ''}</div>
                    <div class="acts">
                        ${q.status === 'open' && q.admin ? `<button class="adm-btn adm-btn-gold sm" data-assign="${q.admin.id}" data-plan="${q.plan?.id || ''}"><i class="fa-solid fa-user-check"></i> Give Subscription</button>
                        <button class="adm-btn adm-btn-ghost sm" data-rq="${q.id}" data-st="dismissed">Dismiss</button>` : ''}
                    </div>
                </div>`).join('') : '<div class="t-empty"><i class="fa-regular fa-envelope"></i><p>No requests.</p></div>';
        } catch (err) { $('rqList').innerHTML = `<div class="t-empty error"><i class="fa-solid fa-triangle-exclamation"></i><p>${esc(err.message)}</p></div>`; }
    }
    $('rqFilter').addEventListener('click', e => { const b = e.target.closest('button'); if (!b) return; $('rqFilter').querySelectorAll('button').forEach(x => x.classList.toggle('active', x === b)); rqStatus = b.dataset.f; loadRequests(); });

    /* ---------- assign / accept modal ---------- */
    function renderPlans(selected) {
        $('asPlans').innerHTML = plans.length ? plans.map(p => `<label><input type="radio" name="plan_id" value="${p.id}" ${String(selected) === String(p.id) ? 'checked' : ''}>
            <span class="box"><b>${esc(p.name)}</b><em>${inr(p.price)}</em><small>${p.duration_days} days · ${p.max_users ? p.max_users + ' users' : 'Unlimited users'}</small></span></label>`).join('')
            : `<div class="t-empty"><p>No active plans. <a class="gold" href="${PLANS_URL}">Create a plan first</a>.</p></div>`;
    }
    function showCurrent() {
        const a = admins.find(x => String(x.id) === el('admin_id').value);
        $('asCur').hidden = !(a && a.subscription);
        if (a && a.subscription) $('asCur').innerHTML = `<i class="fa-solid fa-circle-info"></i> Current: ${esc(a.subscription)} – the new plan will start after it ends.`;
    }
    function openAssign(adminId, planId) {
        form.reset(); API.clearErrors(form);
        el('admin_id').innerHTML = '<option value="">Select admin</option>' + admins.map(a => `<option value="${a.id}">${esc(a.name)} – ${esc(a.email)}${a.subscription ? ' (active)' : ''}</option>`).join('');
        if (adminId) el('admin_id').value = adminId;
        renderPlans(planId || (plans[0] && plans[0].id));
        showCurrent();
        window.admOpenModal('asModal');
    }
    el('admin_id').addEventListener('change', showCurrent);
    $('sbAssignBtn').addEventListener('click', () => openAssign(null));

    form.addEventListener('submit', async e => {
        e.preventDefault();
        API.clearErrors(form);
        const plan = form.querySelector('[name="plan_id"]:checked');
        const errs = {};
        if (!el('admin_id').value) errs.admin_id = ['Please select an admin.'];
        if (!plan) errs.plan_id = ['Please select a plan.'];
        if (Object.keys(errs).length) { API.showErrors(form, errs); return; }
        const btn = $('asSave'); btn.disabled = true; btn.classList.add('loading');
        try {
            const r = await API.post('/subscriptions', { admin_id: +el('admin_id').value, plan_id: +plan.value, note: el('note').value.trim() || null });
            toast(r.message);
            document.querySelector('#asModal [data-close-modal]').click();
            load(); if ($('tab-requests').classList.contains('active')) loadRequests();
        } catch (err) { API.showErrors(form, err.errors); toast(err.message, true); }
        finally { btn.disabled = false; btn.classList.remove('loading'); }
    });

    /* ---------- actions ---------- */
    document.addEventListener('click', async e => {
        const as = e.target.closest('[data-assign]');
        if (as) { e.preventDefault(); return openAssign(as.dataset.assign, as.dataset.plan || null); }
        const c = e.target.closest('[data-cancel]');
        if (c) {
            e.preventDefault();
            if (!confirm('Cancel this subscription? The admin will be locked from creating users immediately.')) return;
            try { const r = await API.post(`/subscriptions/${c.dataset.cancel}/cancel`, {}); toast(r.message); load(); } catch (err) { toast(err.message, true); }
        }
        const d = e.target.closest('[data-del]');
        if (d) {
            e.preventDefault();
            if (!confirm('Delete this subscription record?')) return;
            try { const r = await API.del('/subscriptions/' + d.dataset.del); toast(r.message); load(); } catch (err) { toast(err.message, true); }
        }
        const rq = e.target.closest('[data-rq]');
        if (rq) {
            try { const r = await API.patch('/subscription-requests/' + rq.dataset.rq, { status: rq.dataset.st }); toast(r.message); loadRequests(); load(); } catch (err) { toast(err.message, true); }
        }
    });

    if (qp.get('tab')) document.querySelector(`[data-tab="${qp.get('tab')}"]`)?.click();
    load();
});
</script>
@endpush