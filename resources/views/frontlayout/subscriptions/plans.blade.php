@extends('frontlayout.app')
@section('title', 'Subscription Plans')
@section('page_title', 'Subscription Plans')
@section('require_role', 'superadmin')

@section('page_actions')
    <a href="{{ route('superadmin.subscriptions') }}" class="adm-btn adm-btn-ghost"><i class="fa-solid fa-user-tie"></i> <span class="hide-xs">Admin Subscriptions</span></a>
    <button class="adm-btn adm-btn-gold" id="plAdd"><i class="fa-solid fa-plus"></i> New Plan</button>
@endsection

@push('styles')
<style>
/* ===== SUBSCRIPTION PLANS (isi file me) ===== */
.pl-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
.pl-card { position: relative; display: flex; flex-direction: column; padding: 26px 24px 22px; overflow: hidden; transition: .4s; animation: plUp .5s ease both; animation-delay: calc(var(--i) * .06s); }
@keyframes plUp { from { opacity: 0; transform: translateY(18px); } }
.pl-card:hover { transform: translateY(-6px); border-color: rgba(212,175,55,.5); box-shadow: 0 24px 60px rgba(0,0,0,.5), 0 0 0 1px rgba(212,175,55,.2); }
.pl-card::before { content: ''; position: absolute; top: -60px; right: -60px; width: 160px; height: 160px; border-radius: 50%; background: radial-gradient(circle, rgba(212,175,55,.22), transparent 70%); pointer-events: none; }
.pl-card.off { opacity: .6; }
.pl-card > * { position: relative; z-index: 1; }
.pl-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; margin-bottom: 14px; }
.pl-ic { width: 50px; height: 50px; border-radius: 15px; display: grid; place-items: center; font-size: 21px; color: #111; background: var(--gold-grad); box-shadow: 0 10px 24px rgba(212,175,55,.3); }
.pl-card h3 { color: #fff; font-size: 20px; margin: 0 0 4px; overflow-wrap: anywhere; }
.pl-price { display: flex; align-items: baseline; gap: 6px; margin: 10px 0 4px; }
.pl-price b { font-family: var(--serif, serif); font-size: 38px; color: var(--gold-light); line-height: 1; }
.pl-price small { color: var(--muted); font-size: 13px; }
.pl-desc { color: var(--muted); font-size: 13px; margin: 6px 0 12px; line-height: 1.6; }
.pl-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; }
.pl-chips span { font-size: 12px; padding: 4px 10px; border-radius: 20px; color: #ddd; background: rgba(255,255,255,.05); border: 1px solid var(--border); }
.pl-chips i { color: var(--gold); margin-right: 4px; }
.pl-feat { list-style: none; padding: 0; margin: 0 0 16px; display: grid; gap: 7px; flex: 1; }
.pl-feat li { display: flex; gap: 8px; font-size: 13px; color: #ccc; }
.pl-feat li i { color: #5fe0a5; margin-top: 3px; }
.pl-foot { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding-top: 14px; border-top: 1px solid var(--border); }
.pl-foot small { color: var(--muted); font-size: 12px; }
.pl-foot .acts { display: flex; gap: 6px; }
.pl-foot .icon-btn.danger { color: #ff8c8c; }
.pl-sk { height: 340px; border-radius: 22px; background: linear-gradient(90deg, rgba(255,255,255,.03) 25%, rgba(255,255,255,.07) 50%, rgba(255,255,255,.03) 75%); background-size: 200% 100%; animation: plSh 1.3s linear infinite; }
@keyframes plSh { to { background-position: -200% 0; } }
.pl-empty { grid-column: 1 / -1; }

/* modal form */
.req { color: #ff7a7a; font-weight: 600; }
.pl-feats { display: grid; gap: 8px; }
.pl-feats .row { display: flex; gap: 8px; }
.pl-feats .row input { flex: 1; }
.pl-feats .icon-btn.danger { color: #ff8c8c; flex-shrink: 0; }
.pl-addf { font-size: 12.5px; color: var(--gold-light); background: none; border: 0; cursor: pointer; padding: 4px 0; text-align: left; }
.pl-quick { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 6px; }
.pl-quick button { font-size: 11.5px; padding: 3px 9px; border-radius: 12px; border: 1px solid var(--border); background: rgba(255,255,255,.03); color: #bbb; cursor: pointer; }
.pl-quick button:hover { color: var(--gold-light); border-color: rgba(212,175,55,.4); }
.adm-btn-danger { color: #fff; background: linear-gradient(135deg, #ff7a7a, #d63c3c); border: 0; }
</style>
@endpush

@section('content')
<div class="pl-grid" id="plGrid">
    <div class="pl-sk"></div><div class="pl-sk"></div><div class="pl-sk"></div>
</div>
@endsection

@push('modals')
<div class="adm-modal" id="plModal" aria-hidden="true">
    <div class="adm-modal-box glass-card wide">
        <div class="modal-head">
            <h4><i class="fa-solid fa-crown"></i> <span id="plModalTitle">New Plan</span></h4>
            <button class="icon-btn" data-close-modal aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form class="adm-form" id="plForm" novalidate>
            <div class="f-grid">
                <div class="f-group full" data-field="name"><label>Plan Name <b class="req">*</b></label><input type="text" name="name" required maxlength="100" placeholder="e.g. Basic Monthly, Pro Monthly"></div>
                <div class="f-group" data-field="price"><label>Price (₹) <b class="req">*</b></label><input type="number" name="price" required min="0" step="0.01" placeholder="999"></div>
                <div class="f-group" data-field="duration_days"><label>Duration (days) <b class="req">*</b></label><input type="number" name="duration_days" required min="1" max="366" value="30">
                    <div class="pl-quick"><button type="button" data-days="30">1 Month</button><button type="button" data-days="90">3 Months</button><button type="button" data-days="180">6 Months</button><button type="button" data-days="365">1 Year</button></div></div>
                <div class="f-group" data-field="max_users"><label>Max Users <small class="f-hint">(blank = unlimited)</small></label><input type="number" name="max_users" min="1" placeholder="Unlimited"></div>
                <div class="f-group" data-field="sort_order"><label>Display Order</label><input type="number" name="sort_order" min="0" placeholder="0"></div>
                <div class="f-group full" data-field="description"><label>Description</label><textarea name="description" rows="2" maxlength="2000" placeholder="Short line about the plan"></textarea></div>
                <div class="f-group full" data-field="features"><label>Features</label>
                    <div class="pl-feats" id="plFeats"></div>
                    <button type="button" class="pl-addf" id="plAddFeat"><i class="fa-solid fa-plus"></i> Add feature</button></div>
                <div class="f-group" data-field="status"><label>Status</label><select name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            </div>
            <div class="modal-foot">
                <button type="button" class="adm-btn adm-btn-ghost" data-close-modal>Cancel</button>
                <button type="submit" class="adm-btn adm-btn-gold" id="plSave"><i class="fa-solid fa-check"></i> Save Plan</button>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
/* ===== SUBSCRIPTION PLANS – API (Bearer token) ===== */
document.addEventListener('auth:ready', function (ev) {
    if (!window.API || ev.detail.role !== 'superadmin') return;
    const esc = API.esc, $ = i => document.getElementById(i);
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const grid = $('plGrid'), form = $('plForm'), el = n => form.elements.namedItem(n);
    const ASSIGN = @json(route('superadmin.subscriptions'));
    const inr = n => '₹' + Number(n || 0).toLocaleString('en-IN', { maximumFractionDigits: 2 });
    const dur = d => d % 365 === 0 ? (d / 365) + (d === 365 ? ' Year' : ' Years') : d % 30 === 0 ? (d / 30) + (d === 30 ? ' Month' : ' Months') : d + ' days';
    let plans = [], editId = null;

    const card = (p, i) => `
        <div class="glass-card pl-card ${p.status === 'active' ? '' : 'off'}" style="--i:${i}">
            <div class="pl-top">
                <span class="pl-ic"><i class="fa-solid fa-crown"></i></span>
                <label class="switch" title="${p.status === 'active' ? 'Deactivate' : 'Activate'}"><input type="checkbox" data-pl-toggle="${p.id}" ${p.status === 'active' ? 'checked' : ''}><span></span></label>
            </div>
            <h3>${esc(p.name)}</h3>
            <div class="pl-price"><b>${inr(p.price)}</b><small>/ ${dur(p.duration_days)}</small></div>
            ${p.description ? `<p class="pl-desc">${esc(p.description)}</p>` : ''}
            <div class="pl-chips">
                <span><i class="fa-regular fa-calendar"></i>${p.duration_days} days</span>
                <span><i class="fa-solid fa-users"></i>${p.max_users ? p.max_users + ' users' : 'Unlimited users'}</span>
            </div>
            <ul class="pl-feat">${(p.features || []).map(f => `<li><i class="fa-solid fa-circle-check"></i>${esc(f)}</li>`).join('')}</ul>
            <div class="pl-foot">
                <small><b style="color:#5fe0a5">${p.active_count}</b> active · ${p.subscriptions_count} total</small>
                <div class="acts">
                    <a href="${ASSIGN}?plan=${p.id}" class="icon-btn" title="Assign to admin"><i class="fa-solid fa-user-plus"></i></a>
                    <button class="icon-btn" data-pl-edit="${p.id}" title="Edit"><i class="fa-regular fa-pen-to-square"></i></button>
                    <button class="icon-btn danger" data-pl-del="${p.id}" title="Delete"><i class="fa-regular fa-trash-can"></i></button>
                </div>
            </div>
        </div>`;

    async function load() {
        try {
            const r = await API.get('/subscription-plans');
            plans = r.data;
            grid.innerHTML = plans.length ? plans.map(card).join('')
                : `<div class="glass-card card pl-empty"><div class="t-empty"><i class="fa-solid fa-crown"></i><p>No plans yet. Create your first monthly plan.</p><button class="adm-btn adm-btn-gold" onclick="document.getElementById('plAdd').click()"><i class="fa-solid fa-plus"></i> New Plan</button></div></div>`;
        } catch (err) { grid.innerHTML = `<div class="glass-card card pl-empty"><div class="t-empty error"><i class="fa-solid fa-triangle-exclamation"></i><p>${esc(err.message)}</p></div></div>`; }
    }

    /* ---- features rows ---- */
    const featRow = v => `<div class="row"><input type="text" maxlength="150" value="${esc(v || '')}" placeholder="e.g. Unlimited cases"><button type="button" class="icon-btn danger" data-rmf aria-label="Remove"><i class="fa-solid fa-xmark"></i></button></div>`;
    const setFeats = list => { $('plFeats').innerHTML = (list && list.length ? list : ['']).map(featRow).join(''); };
    $('plAddFeat').addEventListener('click', () => { $('plFeats').insertAdjacentHTML('beforeend', featRow('')); $('plFeats').lastElementChild.querySelector('input').focus(); });
    $('plFeats').addEventListener('click', e => { if (e.target.closest('[data-rmf]')) { e.target.closest('.row').remove(); if (!$('plFeats').children.length) setFeats([]); } });
    form.querySelectorAll('[data-days]').forEach(b => b.addEventListener('click', () => { el('duration_days').value = b.dataset.days; }));
    form.addEventListener('input', e => { const g = e.target.closest('.has-error'); if (g) { g.classList.remove('has-error'); g.querySelector('.f-error')?.remove(); } });

    function openForm(p) {
        form.reset(); API.clearErrors(form); editId = p ? p.id : null;
        $('plModalTitle').textContent = p ? 'Edit Plan' : 'New Plan';
        if (p) ['name', 'price', 'duration_days', 'max_users', 'sort_order', 'description', 'status'].forEach(k => { el(k).value = p[k] ?? ''; });
        else el('duration_days').value = 30;
        setFeats(p ? p.features : ['Unlimited cases & clients', 'Create users for your clients', 'Email support']);
        window.admOpenModal('plModal');
    }
    $('plAdd').addEventListener('click', () => openForm(null));

    form.addEventListener('submit', async e => {
        e.preventDefault();
        API.clearErrors(form);
        const errs = {};
        if (el('name').value.trim().length < 2) errs.name = ['Please enter the plan name.'];
        if (el('price').value === '' || +el('price').value < 0) errs.price = ['Please enter a valid price.'];
        if (!(+el('duration_days').value >= 1)) errs.duration_days = ['Duration must be at least 1 day.'];
        if (Object.keys(errs).length) { API.showErrors(form, errs); return; }
        const payload = {
            name: el('name').value.trim(), price: +el('price').value, duration_days: +el('duration_days').value,
            max_users: el('max_users').value ? +el('max_users').value : null, sort_order: el('sort_order').value ? +el('sort_order').value : 0,
            description: el('description').value.trim() || null, status: el('status').value,
            features: [...$('plFeats').querySelectorAll('input')].map(i => i.value.trim()).filter(Boolean),
        };
        const btn = $('plSave'); btn.disabled = true; btn.classList.add('loading');
        try {
            const r = await API.post(editId ? '/subscription-plans/' + editId : '/subscription-plans', payload);
            toast(r.message);
            document.querySelector('#plModal [data-close-modal]').click();
            load();
        } catch (err) { API.showErrors(form, err.errors); toast(err.message, true); }
        finally { btn.disabled = false; btn.classList.remove('loading'); }
    });

    grid.addEventListener('click', async e => {
        const ed = e.target.closest('[data-pl-edit]'); if (ed) return openForm(plans.find(p => p.id == ed.dataset.plEdit));
        const del = e.target.closest('[data-pl-del]');
        if (del) {
            const p = plans.find(x => x.id == del.dataset.plDel);
            if (!confirm(`Delete plan "${p.name}"? Already assigned subscriptions are not affected.`)) return;
            try { const r = await API.del('/subscription-plans/' + p.id); toast(r.message); load(); } catch (err) { toast(err.message, true); }
        }
    });
    grid.addEventListener('change', async e => {
        const t = e.target.closest('[data-pl-toggle]'); if (!t) return;
        try { const r = await API.patch(`/subscription-plans/${t.dataset.plToggle}/status`, { status: t.checked ? 'active' : 'inactive' }); toast(r.message); load(); }
        catch (err) { t.checked = !t.checked; toast(err.message, true); }
    });

    load();
});
</script>
@endpush