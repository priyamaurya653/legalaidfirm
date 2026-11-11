@extends('frontlayout.app')
@section('title', 'My Payments')
@section('page_title', 'My Payments')
{{-- superadmin + admin – har koi SIRF apne payments dekhta hai (API: /api/payments) --}}

@section('page_actions')
    <button class="adm-btn adm-btn-ghost" id="pyExport"><i class="fa-solid fa-file-export"></i> <span class="hide-xs">Export</span></button>
    <button class="adm-btn adm-btn-gold" id="pyAdd"><i class="fa-solid fa-plus"></i> Add Payment</button>
@endsection

@push('styles')
<style>
/* ===== MY PAYMENTS (isi file me) ===== */
.py-private { display: flex; align-items: center; gap: 10px; padding: 10px 14px; margin-bottom: 18px; border-radius: 12px; font-size: 13px; color: #ccc; background: rgba(62,207,142,.06); border: 1px solid rgba(62,207,142,.25); }
.py-private i { color: #5fe0a5; }
.py-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 20px; }
.py-stat { position: relative; overflow: hidden; display: flex; align-items: center; gap: 14px; padding: 18px; animation: fadeUp .5s ease both; }
.py-stat b { display: block; font-size: 22px; color: #fff; line-height: 1.15; }
.py-stat small { color: var(--muted); font-size: 12.5px; }
.py-stat.main { background: linear-gradient(135deg, rgba(212,175,55,.18), rgba(255,255,255,.02)); border-color: rgba(212,175,55,.4); }
.py-stat.main b { color: var(--gold-light); }
.py-toolbar { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.py-toolbar .right { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
.py-select, .py-date { height: 40px; padding: 0 12px; border-radius: 12px; color: #ddd; font: inherit; font-size: 13px; background: rgba(255,255,255,.04); border: 1px solid var(--border); color-scheme: dark; }
.py-select { padding-right: 32px; appearance: none; cursor: pointer; background: rgba(255,255,255,.04) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath fill='%23d4af37' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E") no-repeat right 12px center; }
.py-select option { background: #111; }
.py-sum { font-size: 13px; color: var(--muted); margin-bottom: 10px; }
.py-sum b { color: var(--gold-light); }
.py-amt { font-weight: 600; color: #fff; white-space: nowrap; }
.py-amt.pending { color: #ffb347; }
.py-mode { display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; color: #ccc; white-space: nowrap; }
.py-mode i { color: var(--gold); }
.py-rc { font-family: ui-monospace, Consolas, monospace; font-size: 11.5px; color: var(--muted); }
.py-purpose { max-width: 240px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #ddd; }
.adm-table tr.removing { opacity: 0; transform: translateX(30px); transition: .35s; }

/* form */
.req { color: #ff7a7a; font-weight: 600; }
.py-amount-in { position: relative; }
.py-amount-in span { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--gold); font-weight: 600; }
.py-amount-in input { padding-left: 32px !important; font-size: 17px !important; font-weight: 600; }
.py-modes { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
.f-group .py-modes label { margin: 0; cursor: pointer; }
.py-modes input { position: absolute; opacity: 0; width: 0 !important; height: 0; padding: 0 !important; border: 0 !important; }
.py-modes span { display: flex; align-items: center; justify-content: center; gap: 6px; height: 40px; border-radius: 10px; font-size: 13px; color: #bbb; border: 1px solid var(--border); background: rgba(255,255,255,.02); transition: .2s; }
.py-modes input:checked + span { color: #111; font-weight: 600; background: var(--gold-grad); border-color: transparent; }
.py-st { display: flex; gap: 8px; }
.f-group .py-st label { flex: 1; margin: 0; cursor: pointer; }
.py-st input { position: absolute; opacity: 0; width: 0 !important; height: 0; padding: 0 !important; border: 0 !important; }
.py-st span { display: flex; align-items: center; justify-content: center; gap: 6px; height: 40px; border-radius: 10px; font-size: 13px; color: #bbb; border: 1px solid var(--border); }
.py-st input[value="received"]:checked + span { color: #062b1a; font-weight: 600; background: linear-gradient(135deg, #7ff0b8, #2fbf7d); border-color: transparent; }
.py-st input[value="pending"]:checked + span { color: #2b1a00; font-weight: 600; background: linear-gradient(135deg, #ffd08a, #ffb347); border-color: transparent; }
.f-group label.py-file { display: flex; align-items: center; gap: 12px; padding: 12px 14px; margin: 0; border-radius: 12px; cursor: pointer; font-size: inherit; color: inherit; border: 1px dashed rgba(212,175,55,.35); background: rgba(212,175,55,.03); }
.py-file i { color: var(--gold); font-size: 18px; }
.py-file small { color: var(--muted); font-size: 12px; display: block; word-break: break-all; }
.py-file.has { border-style: solid; border-color: rgba(62,207,142,.5); }
.py-file input { display: none; }
.py-now { font-size: 12px; color: var(--muted); margin-top: 6px; }
.py-now button { background: none; border: 0; padding: 0; font: inherit; font-size: 12px; color: var(--gold-light); cursor: pointer; }
.py-now button.rm { color: #ff8c8c; }

/* view */
.pv-amount { text-align: center; padding: 22px; margin-bottom: 18px; border-radius: 18px; background: linear-gradient(135deg, rgba(212,175,55,.15), rgba(255,255,255,.02)); border: 1px solid rgba(212,175,55,.3); }
.pv-amount b { display: block; font-family: var(--serif, serif); font-size: 42px; color: var(--gold-light); line-height: 1.1; }
.pv-amount small { color: var(--muted); }
.pv-kv { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px 18px; margin-bottom: 16px; }
.pv-kv small { display: block; color: var(--muted); font-size: 12px; margin-bottom: 2px; }
.pv-kv b { color: #eee; font-size: 13.5px; font-weight: 500; overflow-wrap: anywhere; }
.pv-kv .full { grid-column: 1 / -1; }
.adm-btn-danger { color: #fff; background: linear-gradient(135deg, #ff7a7a, #d63c3c); border: 0; }
@media (max-width: 991px) { .py-stats { grid-template-columns: 1fr 1fr; } }
@media (max-width: 575px) { .py-stats { gap: 10px; } .py-stat { padding: 14px; } .py-stat b { font-size: 18px; } .py-toolbar, .py-toolbar .right { flex-direction: column; align-items: stretch; } .py-toolbar .tb-search { width: 100%; } .py-modes { grid-template-columns: 1fr 1fr; } .pv-kv { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

<div class="py-private"><i class="fa-solid fa-user-lock"></i> These payments are private – only you can see them. No one else, including the superadmin, can view your payments.</div>

<div class="py-stats">
    <div class="glass-card py-stat main"><span class="sc-icon gold"><i class="fa-solid fa-indian-rupee-sign"></i></span><div><b data-ps="total_received">–</b><small>Total Received</small></div></div>
    <div class="glass-card py-stat"><span class="sc-icon green"><i class="fa-regular fa-calendar"></i></span><div><b data-ps="this_month">–</b><small>This Month</small></div></div>
    <div class="glass-card py-stat"><span class="sc-icon blue"><i class="fa-solid fa-sun"></i></span><div><b data-ps="today">–</b><small>Today</small></div></div>
    <div class="glass-card py-stat"><span class="sc-icon purple"><i class="fa-solid fa-hourglass-half"></i></span><div><b data-ps="pending">–</b><small>Pending</small></div></div>
</div>

<div class="glass-card card">
    <div class="py-toolbar">
        <div class="tb-search sm"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="Client, purpose, receipt no, txn id…" id="pySearch" autocomplete="off"></div>
        <div class="right">
            <div class="filter-chips" id="pyStatus"><button class="active" data-f="">All</button><button data-f="received">Received</button><button data-f="pending">Pending</button></div>
            <select class="py-select" id="pyMode"><option value="">All Modes</option></select>
            <input type="date" class="py-date" id="pyFrom" title="From">
            <input type="date" class="py-date" id="pyTo" title="To">
        </div>
    </div>
    <p class="py-sum" id="pySum"></p>
    <div class="table-wrap">
        <table class="adm-table">
            <thead><tr><th>Date</th><th>Client</th><th class="hide-sm">For</th><th>Amount</th><th class="hide-md">Mode</th><th class="hide-sm">Status</th><th class="t-right">Action</th></tr></thead>
            <tbody id="pyRows"></tbody>
        </table>
    </div>
    <div class="pager" id="pyPager"></div>
</div>

@endsection

@push('modals')
{{-- ADD / EDIT --}}
<div class="adm-modal" id="pyModal" aria-hidden="true">
    <div class="adm-modal-box glass-card wide">
        <div class="modal-head">
            <h4><i class="fa-solid fa-indian-rupee-sign"></i> <span id="pyModalTitle">Add Payment</span></h4>
            <button class="icon-btn" data-close-modal aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form class="adm-form" id="pyForm" novalidate enctype="multipart/form-data">
            <div class="f-grid">
                <div class="f-group" data-field="client_name"><label>Client Name <b class="req">*</b></label><input type="text" name="client_name" required maxlength="120" list="pyClients" placeholder="Who paid?" autocomplete="off"><datalist id="pyClients"></datalist></div>
                <div class="f-group" data-field="client_phone"><label>Client Mobile</label><input type="tel" name="client_phone" inputmode="numeric" maxlength="10" placeholder="10-digit mobile"></div>
                <div class="f-group full" data-field="purpose"><label>Payment For <b class="req">*</b></label><input type="text" name="purpose" required maxlength="200" placeholder="e.g. Bail application fee – State vs Rohit"></div>
                <div class="f-group" data-field="category"><label>Category</label><input type="text" name="category" maxlength="60" list="pyCats" placeholder="Select or type"><datalist id="pyCats"></datalist></div>
                <div class="f-group" data-field="amount"><label>Amount <b class="req">*</b></label><div class="py-amount-in"><span>₹</span><input type="number" name="amount" required min="1" step="0.01" placeholder="0.00"></div></div>
                <div class="f-group full" data-field="payment_mode"><label>Payment Mode <b class="req">*</b></label><div class="py-modes" id="pyModes"></div></div>
                <div class="f-group" data-field="payment_date"><label>Payment Date <b class="req">*</b></label><input type="date" name="payment_date" required></div>
                <div class="f-group" data-field="reference_no"><label>Reference / Txn ID</label><input type="text" name="reference_no" maxlength="100" placeholder="UPI txn / cheque no."></div>
                <div class="f-group" data-field="status"><label>Status <b class="req">*</b></label>
                    <div class="py-st"><label><input type="radio" name="status" value="received" checked><span><i class="fa-solid fa-circle-check"></i> Received</span></label><label><input type="radio" name="status" value="pending"><span><i class="fa-solid fa-hourglass-half"></i> Pending</span></label></div></div>
                <div class="f-group" data-field="client_email"><label>Client Email</label><input type="email" name="client_email" maxlength="150" placeholder="Optional"></div>
                <div class="f-group full" data-field="notes"><label>Notes</label><textarea name="notes" rows="2" maxlength="2000" placeholder="Any remarks…"></textarea></div>
                <div class="f-group full" data-field="attachment">
                    <label class="py-file" id="pyFileBox"><i class="fa-solid fa-paperclip"></i><span><b style="color:#fff;font-weight:500">Attach receipt / screenshot</b><small id="pyFileLabel">Optional · PDF / JPG / PNG · max 4MB</small></span><input type="file" name="attachment" accept=".pdf,image/png,image/jpeg,image/webp"></label>
                    <div class="py-now" id="pyFileNow" hidden></div>
                    <input type="hidden" name="remove_attachment" value="0">
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="adm-btn adm-btn-ghost" data-close-modal>Cancel</button>
                <button type="submit" class="adm-btn adm-btn-gold" id="pySave"><i class="fa-solid fa-check"></i> Save Payment</button>
            </div>
        </form>
    </div>
</div>

{{-- VIEW --}}
<div class="adm-modal" id="pyViewModal" aria-hidden="true">
    <div class="adm-modal-box glass-card">
        <div class="modal-head">
            <h4><i class="fa-solid fa-receipt"></i> Payment Receipt</h4>
            <button class="icon-btn" data-close-modal aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="pyViewBody"></div>
    </div>
</div>
@endpush

@push('scripts')
<script>
/* ===== MY PAYMENTS – API (Bearer token) ===== */
document.addEventListener('auth:ready', function (ev) {
    if (!window.API || !['superadmin', 'admin'].includes(ev.detail.role)) return;
    const me = ev.detail;
    const esc = API.esc, $ = i => document.getElementById(i);
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const form = $('pyForm'), el = n => form.elements.namedItem(n);
    const inr = n => '₹' + Number(n || 0).toLocaleString('en-IN', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    const MODE_IC = { Cash: 'fa-money-bill-wave', UPI: 'fa-mobile-screen', 'Bank Transfer': 'fa-building-columns', Cheque: 'fa-money-check', Card: 'fa-credit-card', Other: 'fa-circle-dot' };
    const today = () => new Date(Date.now() - new Date().getTimezoneOffset() * 6e4).toISOString().slice(0, 10);
    const state = { page: 1, search: '', status: '', mode: '', from: '', to: '' };
    const cache = {};
    let clients = [], editId = null, editData = null;
    const debounce = (fn, ms = 350) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

    async function getFile(url) {
        const w = window.open('', '_blank');
        try {
            const r = await fetch(url, { headers: { Authorization: 'Bearer ' + API.token(), Accept: '*/*' } });
            if (!r.ok) throw new Error('Could not open file (' + r.status + ')');
            const u = URL.createObjectURL(await r.blob()); if (w) w.location = u; else location.href = u;
        } catch (e) { if (w) w.close(); toast(e.message, true); }
    }

    /* ---------- options ---------- */
    API.get('/payments/options').then(o => {
        clients = o.clients || [];
        $('pyClients').innerHTML = clients.map(c => `<option value="${esc(c.name)}">${esc(c.phone || '')}</option>`).join('');
        $('pyCats').innerHTML = (o.categories || []).map(c => `<option value="${esc(c)}">`).join('');
        $('pyModes').innerHTML = (o.modes || []).map((m, i) => `<label><input type="radio" name="payment_mode" value="${esc(m)}" ${i === 0 ? 'checked' : ''}><span><i class="fa-solid ${MODE_IC[m] || 'fa-circle-dot'}"></i> ${esc(m)}</span></label>`).join('');
        $('pyMode').insertAdjacentHTML('beforeend', (o.modes || []).map(m => `<option>${esc(m)}</option>`).join(''));
    }).catch(() => {});

    /* ---------- list ---------- */
    const row = p => `<tr data-id="${p.id}">
        <td><b style="color:#fff;font-weight:500">${esc(p.payment_date_label)}</b><br><span class="py-rc">${esc(p.receipt_no)}</span></td>
        <td><div class="t-title">${esc(p.client_name)}</div><small class="t-sub">${esc(p.client_phone ? '+91 ' + p.client_phone : '')}</small></td>
        <td class="hide-sm"><div class="py-purpose" title="${esc(p.purpose)}">${esc(p.purpose)}</div>${p.category ? `<span class="tag">${esc(p.category)}</span>` : ''}</td>
        <td><span class="py-amt ${p.status}">${inr(p.amount)}</span></td>
        <td class="hide-md"><span class="py-mode"><i class="fa-solid ${MODE_IC[p.payment_mode] || 'fa-circle-dot'}"></i>${esc(p.payment_mode)}</span></td>
        <td class="hide-sm"><span class="status ${p.status === 'received' ? 'green' : 'gold'}">${p.status === 'received' ? 'Received' : 'Pending'}</span></td>
        <td class="t-right"><div class="popper-wrap">
            <button class="icon-btn" data-popper="pyPop${p.id}" aria-label="Actions"><i class="fa-solid fa-ellipsis"></i></button>
            <div class="popper popper-right" id="pyPop${p.id}" data-dyn><ul class="pop-menu">
                <li><a href="#" data-py="view" data-id="${p.id}"><i class="fa-regular fa-eye"></i> View</a></li>
                <li><a href="#" data-py="print" data-id="${p.id}"><i class="fa-solid fa-print"></i> Print Receipt</a></li>
                <li><a href="#" data-py="edit" data-id="${p.id}"><i class="fa-regular fa-pen-to-square"></i> Edit</a></li>
                ${p.status === 'pending' ? `<li><a href="#" data-py="received" data-id="${p.id}"><i class="fa-solid fa-circle-check"></i> Mark Received</a></li>` : ''}
                <li class="sep"></li>
                <li><a href="#" class="danger" data-py="delete" data-id="${p.id}"><i class="fa-regular fa-trash-can"></i> Delete</a></li>
            </ul></div></div></td>
    </tr>`;
    const empty = (m, err) => { $('pyRows').innerHTML = `<tr><td colspan="7"><div class="t-empty ${err ? 'error' : ''}"><i class="fa-solid ${err ? 'fa-triangle-exclamation' : 'fa-indian-rupee-sign'}"></i><p>${esc(m)}</p>${err ? '' : '<button class="adm-btn adm-btn-gold" onclick="document.getElementById(\'pyAdd\').click()"><i class="fa-solid fa-plus"></i> Add Payment</button>'}</div></td></tr>`; };
    const renderPager = meta => {
        const pager = $('pyPager');
        if (!meta || !meta.total) { pager.innerHTML = ''; return; }
        const { current_page: c, last_page: l, from, to, total } = meta, pg = [];
        for (let i = Math.max(1, c - 2); i <= Math.min(l, c + 2); i++) pg.push(i);
        pager.innerHTML = `<small>Showing ${from}–${to} of ${total}</small><div class="pages">
            <button class="icon-btn" data-page="${c - 1}" ${c <= 1 ? 'disabled' : ''}><i class="fa-solid fa-chevron-left"></i></button>
            ${pg.map(n => `<button class="icon-btn ${n === c ? 'active' : ''}" data-page="${n}">${n}</button>`).join('')}
            <button class="icon-btn" data-page="${c + 1}" ${c >= l ? 'disabled' : ''}><i class="fa-solid fa-chevron-right"></i></button></div>`;
    };
    const query = extra => {
        const qs = new URLSearchParams({ page: state.page, ...extra });
        ['search', 'status', 'mode', 'from', 'to'].forEach(k => { if (state[k]) qs.set(k, state[k]); });
        return qs;
    };
    async function load() {
        document.querySelectorAll('.popper[data-dyn]').forEach(p => p.remove());
        $('pyRows').innerHTML = Array.from({ length: 5 }, () => `<tr class="sk-row">${'<td><span class="sk sk-line w80"></span></td>'.repeat(7)}</tr>`).join('');
        try {
            const r = await API.get('/payments?' + query());
            r.data.forEach(p => { cache[p.id] = p; });
            Object.entries(r.stats || {}).forEach(([k, v]) => document.querySelectorAll(`[data-ps="${k}"]`).forEach(x => { x.textContent = inr(v); }));
            const filtered = state.search || state.status || state.mode || state.from || state.to;
            $('pySum').innerHTML = filtered && r.meta?.total ? `${r.meta.total} payment${r.meta.total > 1 ? 's' : ''} · received in this view: <b>${inr(r.stats.filtered_total)}</b>` : '';
            r.data.length ? ($('pyRows').innerHTML = r.data.map(row).join('')) : empty(filtered ? 'No payment matches your filters.' : 'No payments yet. Record your first client payment.');
            renderPager(r.meta);
        } catch (err) { empty(err.message, true); $('pyPager').innerHTML = ''; }
    }
    $('pySearch').addEventListener('input', debounce(e => { state.search = e.target.value.trim(); state.page = 1; load(); }));
    $('pyStatus').addEventListener('click', e => { const b = e.target.closest('button'); if (!b) return; $('pyStatus').querySelectorAll('button').forEach(x => x.classList.toggle('active', x === b)); state.status = b.dataset.f; state.page = 1; load(); });
    $('pyMode').addEventListener('change', e => { state.mode = e.target.value; state.page = 1; load(); });
    $('pyFrom').addEventListener('change', e => { state.from = e.target.value; state.page = 1; load(); });
    $('pyTo').addEventListener('change', e => { state.to = e.target.value; state.page = 1; load(); });
    $('pyPager').addEventListener('click', e => { const b = e.target.closest('[data-page]'); if (!b || b.disabled) return; state.page = +b.dataset.page; load(); });

    /* ---------- form ---------- */
    el('client_phone').addEventListener('input', e => { e.target.value = e.target.value.replace(/\D/g, '').slice(0, 10); });
    el('client_name').addEventListener('change', () => {
        const c = clients.find(x => x.name.toLowerCase() === el('client_name').value.trim().toLowerCase());
        if (c) { if (!el('client_phone').value && c.phone) el('client_phone').value = c.phone; if (!el('client_email').value && c.email) el('client_email').value = c.email; }
    });
    form.addEventListener('input', e => { const g = e.target.closest('.has-error'); if (g) { g.classList.remove('has-error'); g.querySelector('.f-error')?.remove(); } });
    el('attachment').addEventListener('change', e => {
        const f = e.target.files[0];
        if (f && f.size > 4 * 1048576) { toast('File must be smaller than 4MB.', true); e.target.value = ''; }
        const ok = e.target.files[0];
        $('pyFileLabel').textContent = ok ? ok.name : 'Optional · PDF / JPG / PNG · max 4MB';
        $('pyFileBox').classList.toggle('has', !!ok);
    });
    const fileNow = url => {
        $('pyFileNow').hidden = !url;
        $('pyFileNow').innerHTML = url ? `<i class="fa-solid fa-circle-check" style="color:#5fe0a5"></i> ${esc(editData.attachment_name || 'Receipt')} · <button type="button" data-open="${esc(url)}">View</button> · <button type="button" class="rm" id="pyFileRm">Remove</button>` : '';
    };
    $('pyFileNow').addEventListener('click', e => {
        const o = e.target.closest('[data-open]'); if (o) return getFile(o.dataset.open);
        if (e.target.closest('#pyFileRm')) { el('remove_attachment').value = '1'; fileNow(null); toast('Receipt will be removed when you save.'); }
    });

    function openForm(p) {
        form.reset(); API.clearErrors(form); editId = p ? p.id : null; editData = p || null;
        el('remove_attachment').value = '0';
        $('pyFileLabel').textContent = 'Optional · PDF / JPG / PNG · max 4MB'; $('pyFileBox').classList.remove('has');
        $('pyModalTitle').textContent = p ? 'Edit Payment · ' + p.receipt_no : 'Add Payment';
        if (p) {
            ['client_name', 'client_phone', 'client_email', 'purpose', 'category', 'amount', 'payment_date', 'reference_no', 'notes'].forEach(k => { el(k).value = p[k] ?? ''; });
            const m = form.querySelector(`[name="payment_mode"][value="${CSS.escape(p.payment_mode)}"]`); if (m) m.checked = true;
            form.querySelector(`[name="status"][value="${p.status}"]`).checked = true;
            fileNow(p.attachment_url);
        } else { el('payment_date').value = today(); fileNow(null); }
        window.admOpenModal('pyModal');
    }
    $('pyAdd').addEventListener('click', () => openForm(null));

    form.addEventListener('submit', async e => {
        e.preventDefault();
        API.clearErrors(form);
        const errs = {};
        if (el('client_name').value.trim().length < 2) errs.client_name = ['Please enter the client name.'];
        if (el('client_phone').value && !/^[6-9]\d{9}$/.test(el('client_phone').value)) errs.client_phone = ['Enter a valid 10-digit mobile number.'];
        if (el('purpose').value.trim().length < 2) errs.purpose = ['Please enter what this payment is for.'];
        if (!(+el('amount').value >= 1)) errs.amount = ['Please enter a valid amount.'];
        if (!form.querySelector('[name="payment_mode"]:checked')) errs.payment_mode = ['Please select the payment mode.'];
        if (!el('payment_date').value) errs.payment_date = ['Please select the payment date.'];
        if (el('client_email').value && !el('client_email').checkValidity()) errs.client_email = ['Enter a valid email.'];
        if (Object.keys(errs).length) { API.showErrors(form, errs)?.scrollIntoView({ behavior: 'smooth', block: 'center' }); return; }

        const btn = $('pySave'); btn.disabled = true; btn.classList.add('loading');
        try {
            const r = await API.post(editId ? '/payments/' + editId : '/payments', API.formData(form));
            toast(r.message);
            document.querySelector('#pyModal [data-close-modal]').click();
            if (!editId) state.page = 1;
            load();
        } catch (err) { API.showErrors(form, err.errors); toast(err.message, true); }
        finally { btn.disabled = false; btn.classList.remove('loading'); }
    });

    /* ---------- view + print ---------- */
    const v = x => x ? esc(x) : '<span style="color:var(--muted)">—</span>';
    function openView(p) {
        $('pyViewBody').innerHTML = `
            <div class="pv-amount"><b>${inr(p.amount)}</b><small>${esc(p.receipt_no)} · <span class="status ${p.status === 'received' ? 'green' : 'gold'}">${p.status === 'received' ? 'Received' : 'Pending'}</span></small></div>
            <div class="pv-kv">
                <div><small>Client</small><b>${esc(p.client_name)}</b></div>
                <div><small>Mobile</small><b>${p.client_phone ? `<a class="gold" href="tel:+91${esc(p.client_phone)}">+91 ${esc(p.client_phone)}</a>` : v()}</b></div>
                <div class="full"><small>Payment For</small><b>${esc(p.purpose)}</b></div>
                <div><small>Category</small><b>${v(p.category)}</b></div>
                <div><small>Date</small><b>${esc(p.payment_date_label)}</b></div>
                <div><small>Mode</small><b>${esc(p.payment_mode)}</b></div>
                <div><small>Reference / Txn</small><b>${v(p.reference_no)}</b></div>
                <div class="full"><small>Email</small><b>${v(p.client_email)}</b></div>
                ${p.notes ? `<div class="full"><small>Notes</small><b style="white-space:pre-wrap">${esc(p.notes)}</b></div>` : ''}
                <div class="full"><small>Recorded</small><b>${esc(p.created_at)}</b></div>
            </div>
            <div class="modal-foot">
                ${p.attachment_url ? `<button class="adm-btn adm-btn-ghost" data-open-file="${esc(p.attachment_url)}"><i class="fa-solid fa-paperclip"></i> Receipt File</button>` : ''}
                <button class="adm-btn adm-btn-ghost" data-py="edit" data-id="${p.id}"><i class="fa-regular fa-pen-to-square"></i> Edit</button>
                <button class="adm-btn adm-btn-gold" data-py="print" data-id="${p.id}"><i class="fa-solid fa-print"></i> Print</button>
            </div>`;
        window.admOpenModal('pyViewModal');
    }
    function printReceipt(p) {
        const w = window.open('', '_blank', 'width=720,height=860');
        if (!w) return toast('Please allow pop-ups to print the receipt.', true);
        w.document.write(`<!doctype html><html><head><meta charset="utf-8"><title>${esc(p.receipt_no)}</title>
            <style>body{font-family:Arial,Helvetica,sans-serif;color:#222;margin:0;padding:40px}.box{max-width:620px;margin:auto;border:2px solid #c9a227;border-radius:14px;padding:32px}
            h1{margin:0;font-family:Georgia,serif;letter-spacing:4px;color:#8a6d14}.sub{color:#777;font-size:12px;letter-spacing:3px}.hd{display:flex;justify-content:space-between;align-items:flex-start;border-bottom:1px solid #eee;padding-bottom:16px;margin-bottom:20px}
            .amt{text-align:center;background:#faf6e8;border-radius:10px;padding:18px;margin:18px 0}.amt b{font-size:34px;color:#8a6d14}table{width:100%;border-collapse:collapse}td{padding:9px 4px;border-bottom:1px solid #f0f0f0;font-size:14px}td:first-child{color:#777;width:40%}
            .ft{display:flex;justify-content:space-between;margin-top:46px;font-size:12px;color:#777}.sig{border-top:1px solid #999;padding-top:6px;width:200px;text-align:center}.st{display:inline-block;padding:3px 10px;border-radius:12px;font-size:12px;background:${p.status === 'received' ? '#e3f8ee;color:#1b8a55' : '#fff3df;color:#b26b00'}}
            @media print{body{padding:0}.np{display:none}}</style></head><body><div class="box">
            <div class="hd"><div><h1>LEXORA</h1><div class="sub">LEGAL</div></div><div style="text-align:right"><b>PAYMENT RECEIPT</b><br><small>${esc(p.receipt_no)}</small><br><small>${esc(p.payment_date_label)}</small></div></div>
            <div class="amt"><small>Amount</small><br><b>${inr(p.amount)}</b><br><span class="st">${p.status === 'received' ? 'RECEIVED' : 'PENDING'}</span></div>
            <table><tr><td>Received from</td><td><b>${esc(p.client_name)}</b></td></tr>${p.client_phone ? `<tr><td>Mobile</td><td>+91 ${esc(p.client_phone)}</td></tr>` : ''}
            <tr><td>Towards</td><td>${esc(p.purpose)}</td></tr>${p.category ? `<tr><td>Category</td><td>${esc(p.category)}</td></tr>` : ''}
            <tr><td>Payment mode</td><td>${esc(p.payment_mode)}</td></tr>${p.reference_no ? `<tr><td>Reference</td><td>${esc(p.reference_no)}</td></tr>` : ''}${p.notes ? `<tr><td>Notes</td><td>${esc(p.notes)}</td></tr>` : ''}</table>
            <div class="ft"><span>Generated on ${new Date().toLocaleString('en-IN')}</span><span class="sig">${esc(me.name)}<br>Authorised Signatory</span></div>
            <p class="np" style="text-align:center;margin-top:26px"><button onclick="window.print()" style="padding:10px 22px;border:0;border-radius:8px;background:#c9a227;color:#111;font-weight:bold;cursor:pointer">Print</button></p>
            </div><script>setTimeout(function(){window.print()},400)<\/script></body></html>`);
        w.document.close();
    }

    /* ---------- actions ---------- */
    document.addEventListener('click', async e => {
        const f = e.target.closest('[data-open-file]'); if (f) return getFile(f.dataset.openFile);
        const a = e.target.closest('[data-py]'); if (!a) return;
        e.preventDefault();
        const p = cache[a.dataset.id]; if (!p) return;
        const act = a.dataset.py;
        if (act === 'view') return openView(p);
        if (act === 'print') return printReceipt(p);
        if (act === 'edit') { document.querySelector('#pyViewModal.show [data-close-modal]')?.click(); return setTimeout(() => openForm(p), 120); }
        if (act === 'received') {
            const fd = new FormData();
            ['client_name', 'client_phone', 'client_email', 'purpose', 'category', 'amount', 'payment_mode', 'payment_date', 'reference_no', 'notes'].forEach(k => { if (p[k] != null) fd.append(k, p[k]); });
            fd.append('status', 'received');
            try { const r = await API.post('/payments/' + p.id, fd); toast('Marked as received.'); load(); } catch (err) { toast(err.message, true); }
        }
        if (act === 'delete') {
            if (!confirm(`Delete payment ${p.receipt_no} (${inr(p.amount)} from ${p.client_name})?`)) return;
            try {
                const r = await API.del('/payments/' + p.id);
                $('pyRows').querySelector(`tr[data-id="${p.id}"]`)?.classList.add('removing');
                toast(r.message); setTimeout(load, 350);
            } catch (err) { toast(err.message, true); }
        }
    });

    /* ---------- export CSV (current filters) ---------- */
    $('pyExport').addEventListener('click', async e => {
        const btn = e.currentTarget; btn.disabled = true; btn.classList.add('loading');
        try {
            let rows = [], page = 1, last = 1;
            do { const qs = query({ per_page: 200 }); qs.set('page', page); const r = await API.get('/payments?' + qs); rows = rows.concat(r.data); last = r.meta.last_page; page++; } while (page <= last && page <= 20);
            if (!rows.length) return toast('Nothing to export.', true);
            const q = x => `"${String(x ?? '').replace(/"/g, '""')}"`;
            const csv = [['Receipt No', 'Date', 'Client', 'Mobile', 'Email', 'Payment For', 'Category', 'Amount', 'Mode', 'Reference', 'Status', 'Notes'],
                ...rows.map(p => [p.receipt_no, p.payment_date, p.client_name, p.client_phone, p.client_email, p.purpose, p.category, p.amount, p.payment_mode, p.reference_no, p.status, p.notes])]
                .map(r => r.map(q).join(',')).join('\r\n');
            const a = document.createElement('a');
            a.href = URL.createObjectURL(new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8' }));
            a.download = 'my-payments-' + today() + '.csv'; a.click();
            setTimeout(() => URL.revokeObjectURL(a.href), 2000);
        } catch (err) { toast(err.message, true); }
        finally { btn.disabled = false; btn.classList.remove('loading'); }
    });

    load();
});
</script>
@endpush