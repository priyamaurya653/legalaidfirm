@extends('frontlayout.app')
@section('title', 'My Subscription')
@section('page_title', 'My Subscription')
@section('require_role', 'admin')

@push('styles')
<style>
/* ===== ADMIN – MY SUBSCRIPTION (isi file me) ===== */
.ms-hero { position: relative; overflow: hidden; display: grid; grid-template-columns: auto 1fr auto; gap: 24px; align-items: center; padding: 28px; margin-bottom: 22px; border-radius: 24px; animation: fadeUp .5s ease both; }
.ms-hero::after { content: ''; position: absolute; top: 0; left: -60%; width: 40%; height: 100%; transform: skewX(-20deg); pointer-events: none; background: linear-gradient(90deg, transparent, rgba(255,255,255,.09), transparent); animation: msSweep 6s ease-in-out infinite 1s; }
@keyframes msSweep { 0% { left: -60%; } 35%, 100% { left: 130%; } }
.ms-hero.on { background: linear-gradient(130deg, rgba(212,175,55,.18), rgba(255,255,255,.02) 60%); border: 1px solid rgba(212,175,55,.35); }
.ms-hero.wait { background: linear-gradient(130deg, rgba(91,141,239,.14), rgba(255,255,255,.02) 60%); border: 1px solid rgba(91,141,239,.35); }
.ms-hero.off { background: linear-gradient(130deg, rgba(255,107,107,.12), rgba(255,255,255,.02) 60%); border: 1px solid rgba(255,107,107,.35); }
.ms-hero > * { position: relative; z-index: 1; }
.ms-ring { --p: 0; width: 132px; height: 132px; border-radius: 50%; display: grid; place-items: center; background: conic-gradient(#d4af37 calc(var(--p) * 1%), rgba(255,255,255,.07) 0); }
.ms-ring > div { width: 108px; height: 108px; border-radius: 50%; display: grid; place-items: center; text-align: center; background: #111; }
.ms-ring b { display: block; font-family: var(--serif, serif); font-size: 38px; color: #fff; line-height: 1; }
.ms-ring small { color: var(--muted); font-size: 11.5px; letter-spacing: 1px; text-transform: uppercase; }
.ms-ic { width: 110px; height: 110px; border-radius: 30px; display: grid; place-items: center; font-size: 44px; }
.ms-hero.wait .ms-ic { color: #8fb4ff; background: rgba(91,141,239,.12); animation: msPulse 2s ease-in-out infinite; }
.ms-hero.off .ms-ic { color: #ff8c8c; background: rgba(255,107,107,.12); }
@keyframes msPulse { 50% { transform: scale(1.06); } }
.ms-hero h2 { font-family: var(--serif, serif); color: #fff; font-size: clamp(24px, 3vw, 32px); margin: 4px 0 8px; }
.ms-hero .tag-line { font-size: 12px; letter-spacing: 2px; text-transform: uppercase; color: var(--gold); }
.ms-hero.wait .tag-line { color: #8fb4ff; } .ms-hero.off .tag-line { color: #ff8c8c; }
.ms-hero p { color: #ccc; font-size: 14px; margin: 0; max-width: 560px; line-height: 1.7; }
.ms-meta { display: flex; flex-wrap: wrap; gap: 8px 20px; margin-top: 12px; font-size: 13px; color: var(--muted); }
.ms-meta i { color: var(--gold); margin-right: 5px; }
.ms-meta b { color: #fff; font-weight: 500; }
.ms-side { display: grid; gap: 10px; min-width: 200px; }
.ms-use small { display: flex; justify-content: space-between; color: var(--muted); font-size: 12px; margin-bottom: 6px; }
.ms-use .bar { height: 7px; border-radius: 6px; background: rgba(255,255,255,.08); overflow: hidden; }
.ms-use .bar i { display: block; height: 100%; background: var(--gold-grad); }
.ms-use .bar i.full { background: linear-gradient(90deg, #ff8c8c, #d63c3c); }
.ms-feat { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
.ms-feat span { font-size: 12px; padding: 5px 11px; border-radius: 20px; color: #ddd; background: rgba(255,255,255,.05); border: 1px solid var(--border); }
.ms-feat i { color: #5fe0a5; margin-right: 5px; }
.ms-note { margin-top: 12px; padding: 10px 14px; border-radius: 12px; font-size: 13px; color: #ddd; background: rgba(255,255,255,.04); border-left: 3px solid var(--gold); }

.ms-grid { display: grid; grid-template-columns: 1.3fr 1fr; gap: 22px; }
.ms-plans { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; }
.ms-plan { padding: 16px; border-radius: 16px; border: 1px solid var(--border); background: rgba(255,255,255,.02); display: flex; flex-direction: column; gap: 6px; transition: .3s; }
.ms-plan:hover { border-color: rgba(212,175,55,.4); transform: translateY(-3px); }
.ms-plan b { color: #fff; font-size: 14.5px; }
.ms-plan em { font-style: normal; font-family: var(--serif, serif); font-size: 26px; color: var(--gold-light); }
.ms-plan small { color: var(--muted); font-size: 12px; }
.ms-plan ul { list-style: none; padding: 0; margin: 4px 0 8px; font-size: 12px; color: #bbb; display: grid; gap: 4px; flex: 1; }
.ms-plan ul i { color: #5fe0a5; margin-right: 5px; }
.ms-plan .adm-btn { justify-content: center; }
.ms-req textarea { width: 100%; min-height: 90px; padding: 12px; border-radius: 12px; border: 1px solid var(--border-2); background: rgba(0,0,0,.35); color: #fff; font: inherit; font-size: 13.5px; resize: vertical; margin-bottom: 10px; }
.ms-req select { width: 100%; height: 42px; padding: 0 12px; border-radius: 12px; border: 1px solid var(--border-2); background: rgba(0,0,0,.35); color: #fff; font: inherit; margin-bottom: 10px; }
.ms-req select option { background: #111; }
.ms-sent { padding: 16px; border-radius: 14px; background: rgba(62,207,142,.07); border: 1px solid rgba(62,207,142,.3); color: #ccc; font-size: 13.5px; }
.ms-sent b { color: #5fe0a5; }
.ms-contact { display: grid; gap: 8px; margin-top: 14px; }
.ms-contact a { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 12px; color: #ddd; border: 1px solid var(--border); font-size: 13.5px; }
.ms-contact a i { color: var(--gold); width: 18px; }
.ms-contact a:hover { border-color: rgba(212,175,55,.4); color: #fff; }
.status.red { color: #ff8c8c; background: rgba(255,107,107,.1); }
.ms-sk { height: 200px; border-radius: 24px; margin-bottom: 22px; background: linear-gradient(90deg, rgba(255,255,255,.03) 25%, rgba(255,255,255,.07) 50%, rgba(255,255,255,.03) 75%); background-size: 200% 100%; animation: msSh 1.3s linear infinite; }
@keyframes msSh { to { background-position: -200% 0; } }
@media (max-width: 991px) { .ms-grid { grid-template-columns: 1fr; } .ms-hero { grid-template-columns: auto 1fr; } .ms-side { grid-column: 1 / -1; } }
@media (max-width: 575px) { .ms-hero { grid-template-columns: 1fr; text-align: center; justify-items: center; padding: 22px 16px; } .ms-meta, .ms-feat { justify-content: center; } .ms-side { width: 100%; } }
</style>
@endpush

@section('content')

<div id="msHero"><div class="ms-sk"></div></div>

<div class="ms-grid">
    <div class="glass-card card">
        <div class="card-head"><div><h4>Available Plans</h4><small>Ask the superadmin for any of these plans</small></div></div>
        <div class="ms-plans" id="msPlans"></div>
    </div>

    <div class="glass-card card ms-req" id="msReqBox">
        <div class="card-head"><div><h4><i class="fa-solid fa-paper-plane gold"></i> Contact Superadmin</h4><small>Request a new subscription or renewal</small></div></div>
        <div id="msReqBody"></div>
        <div class="ms-contact" id="msContact"></div>
    </div>
</div>

<div class="glass-card card">
    <div class="card-head"><div><h4>Subscription History</h4></div></div>
    <div class="table-wrap"><table class="adm-table">
        <thead><tr><th>Plan</th><th class="hide-sm">Price</th><th>Period</th><th>Status</th><th class="hide-md">Given By</th></tr></thead>
        <tbody id="msHistory"></tbody>
    </table></div>
</div>

@endsection

@push('scripts')
<script>
/* ===== ADMIN – MY SUBSCRIPTION – API (Bearer token) ===== */
document.addEventListener('auth:ready', function (ev) {
    if (!window.API || ev.detail.role !== 'admin') return;
    const esc = API.esc, $ = i => document.getElementById(i);
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const inr = n => '₹' + Number(n || 0).toLocaleString('en-IN', { maximumFractionDigits: 2 });
    const USERS_URL = @json(route('superadmin.clients'));
    const SCLS = { active: 'green', expired: 'red', cancelled: 'grey' };
    let data = null;

    function hero(d) {
        const c = d.current;
        if (c) {
            const used = d.users_count, max = c.max_users, pct = max ? Math.min(100, Math.round(used / max * 100)) : 0;
            return `<div class="glass-card ms-hero on">
                <div class="ms-ring" style="--p:${100 - (c.progress || 0)}"><div><div><b>${c.days_left}</b><small>days left</small></div></div></div>
                <div>
                    <span class="tag-line"><i class="fa-solid fa-crown"></i> Active Subscription</span>
                    <h2>${esc(c.plan_name)}</h2>
                    <div class="ms-meta">
                        <span><i class="fa-regular fa-calendar-check"></i>Started <b>${esc(c.starts_at)}</b></span>
                        <span><i class="fa-regular fa-calendar-xmark"></i>Ends <b>${esc(c.ends_at_full)}</b></span>
                        <span><i class="fa-solid fa-indian-rupee-sign"></i><b>${inr(c.price)}</b> / ${c.duration_days} days</span>
                    </div>
                    ${(c.features || []).length ? `<div class="ms-feat">${c.features.map(f => `<span><i class="fa-solid fa-check"></i>${esc(f)}</span>`).join('')}</div>` : ''}
                    ${c.note ? `<div class="ms-note"><i class="fa-regular fa-comment"></i> ${esc(c.note)}</div>` : ''}
                </div>
                <div class="ms-side">
                    <div class="ms-use"><small><span>Clients (logins)</span><span>${used} / ${max || '∞'}</span></small><div class="bar"><i class="${max && used >= max ? 'full' : ''}" style="width:${max ? pct : 12}%"></i></div></div>
                    <a href="${USERS_URL}" class="adm-btn adm-btn-gold ${max && used >= max ? 'disabled' : ''}"><i class="fa-solid fa-user-plus"></i> Add Client</a>
                    ${(d.scheduled || []).map(s => `<small class="t-sub"><i class="fa-solid fa-forward"></i> Next: ${esc(s.plan_name)} from ${esc(s.starts_at)}</small>`).join('')}
                </div>
            </div>`;
        }
        const never = !(d.history || []).length;
        return `<div class="glass-card ms-hero ${never ? 'wait' : 'off'}">
            <span class="ms-ic"><i class="fa-solid ${never ? 'fa-hourglass-half' : 'fa-lock'}"></i></span>
            <div>
                <span class="tag-line">${never ? 'Awaiting Acceptance' : 'Subscription Expired'}</span>
                <h2>${never ? 'Waiting for superadmin approval' : 'Your plan has ended'}</h2>
                <p>${never
                    ? 'Your account has been registered. The superadmin will review and accept your account and activate a subscription. Until then, adding and managing clients is locked.'
                    : 'Your subscription is no longer active, so adding and managing clients is locked. Send a renewal request or contact the superadmin to continue.'}</p>
            </div>
        </div>`;
    }

    function render(d) {
        $('msHero').innerHTML = hero(d);

        $('msPlans').innerHTML = (d.plans || []).length ? d.plans.map(p => `
            <div class="ms-plan">
                <b>${esc(p.name)}</b><em>${inr(p.price)}</em>
                <small>${p.duration_days} days · ${p.max_users ? p.max_users + ' users' : 'Unlimited users'}</small>
                ${(p.features || []).length ? `<ul>${p.features.slice(0, 4).map(f => `<li><i class="fa-solid fa-check"></i>${esc(f)}</li>`).join('')}</ul>` : '<ul></ul>'}
                <button class="adm-btn adm-btn-ghost sm" data-want="${p.id}" ${d.open_request ? 'disabled' : ''}><i class="fa-solid fa-paper-plane"></i> Request this plan</button>
            </div>`).join('') : '<div class="t-empty"><i class="fa-solid fa-crown"></i><p>No plans published yet.</p></div>';

        $('msReqBody').innerHTML = d.open_request
            ? `<div class="ms-sent"><b><i class="fa-solid fa-circle-check"></i> Request sent</b> – the superadmin has been notified and will activate your subscription soon.${d.open_request.message ? `<br><small class="t-sub">"${esc(d.open_request.message)}"</small>` : ''}</div>`
            : `<select id="msReqPlan"><option value="">Any plan (let superadmin decide)</option>${(d.plans || []).map(p => `<option value="${p.id}">${esc(p.name)} – ${inr(p.price)}</option>`).join('')}</select>
               <textarea id="msReqMsg" maxlength="1000" placeholder="${d.current ? 'e.g. Please renew my plan for next month.' : 'e.g. I have registered, please accept my account. Payment done via UPI.'}"></textarea>
               <button class="adm-btn adm-btn-gold w-100" id="msReqSend"><i class="fa-solid fa-paper-plane"></i> Send Request</button>`;

        const sp = d.support || {};
        $('msContact').innerHTML = `${sp.phone ? `<a href="tel:${esc(sp.phone.replace(/\s/g, ''))}"><i class="fa-solid fa-phone"></i>${esc(sp.phone)}</a>` : ''}
            ${sp.phone ? `<a href="https://wa.me/${esc(sp.phone.replace(/\D/g, ''))}" target="_blank"><i class="fa-brands fa-whatsapp"></i>WhatsApp Superadmin</a>` : ''}
            ${sp.email ? `<a href="mailto:${esc(sp.email)}?subject=${encodeURIComponent('Subscription request')}"><i class="fa-regular fa-envelope"></i>${esc(sp.email)}</a>` : ''}`;

        $('msHistory').innerHTML = (d.history || []).length ? d.history.map(h => `<tr>
            <td><b style="color:#fff;font-weight:500">${esc(h.plan_name)}</b><br><small class="t-sub">${h.duration_days} days${h.max_users ? ' · ' + h.max_users + ' users' : ''}</small></td>
            <td class="hide-sm">${inr(h.price)}</td>
            <td><small>${esc(h.starts_at || '—')} → ${esc(h.ends_at || '—')}</small></td>
            <td><span class="status ${h.status_label === 'Scheduled' ? 'blue' : (SCLS[h.status] || 'grey')}">${esc(h.status_label)}</span></td>
            <td class="hide-md"><small>${esc(h.assigned_by || 'Superadmin')}</small><br><small class="t-sub">${esc(h.accepted_at || '')}</small></td>
        </tr>`).join('') : '<tr><td colspan="5"><div class="t-empty"><i class="fa-regular fa-clock"></i><p>No subscriptions yet.</p></div></td></tr>';
    }

    async function load() {
        try { data = await API.get('/my-subscription'); render(data); }
        catch (err) { $('msHero').innerHTML = `<div class="glass-card card"><div class="t-empty error"><i class="fa-solid fa-triangle-exclamation"></i><p>${esc(err.message)}</p></div></div>`; }
    }

    async function sendRequest(planId, message, btn) {
        btn && (btn.disabled = true, btn.classList.add('loading'));
        try { const r = await API.post('/my-subscription/request', { plan_id: planId ? +planId : null, message: message || null }); toast(r.message); load(); }
        catch (err) { toast(err.message, true); btn && (btn.disabled = false, btn.classList.remove('loading')); }
    }

    document.addEventListener('click', e => {
        const s = e.target.closest('#msReqSend');
        if (s) return sendRequest($('msReqPlan').value, $('msReqMsg').value.trim(), s);
        const w = e.target.closest('[data-want]');
        if (w) {
            const p = data.plans.find(x => String(x.id) === w.dataset.want);
            if (confirm(`Send a request to the superadmin for "${p.name}" (${inr(p.price)})?`)) sendRequest(p.id, 'Requested plan: ' + p.name, w);
        }
    });

    load();
});
</script>
@endpush