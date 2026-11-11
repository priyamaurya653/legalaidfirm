@extends('frontlayout.app')
@section('title', 'Reports')
@section('page_title', 'Reports')
{{-- superadmin: poore system ki report (ya kisi ek admin ki) · admin: sirf apni · payments: sabko sirf apne --}}

@section('page_actions')
    <button class="adm-btn adm-btn-ghost" id="rpExport"><i class="fa-solid fa-file-csv"></i> <span class="hide-xs">Export CSV</span></button>
    <button class="adm-btn adm-btn-gold" id="rpPrint"><i class="fa-solid fa-print"></i> <span class="hide-xs">Print</span></button>
@endsection

@push('styles')
<style>
/* ===== REPORTS (isi file me) ===== */
.rp-bar { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; justify-content: space-between; padding: 14px 16px; margin-bottom: 18px; }
.rp-bar .left, .rp-bar .right { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
.rp-date, .rp-select { height: 40px; padding: 0 12px; border-radius: 12px; color: #ddd; font: inherit; font-size: 13px; background: rgba(255,255,255,.04); border: 1px solid var(--border); color-scheme: dark; }
.rp-select { padding-right: 32px; appearance: none; cursor: pointer; min-width: 190px; background: rgba(255,255,255,.04) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath fill='%23d4af37' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E") no-repeat right 12px center; }
.rp-select option { background: #111; }
.rp-scope { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-bottom: 18px; font-size: 13px; color: var(--muted); }
.rp-scope b { color: #fff; font-weight: 500; }
.rp-scope .pill { padding: 5px 12px; border-radius: 20px; font-size: 12px; color: #111; background: var(--gold-grad); font-weight: 600; }
.rp-scope .pill.blue { color: #fff; background: linear-gradient(135deg, #7aa5ff, #3f6fd8); }

.rp-kpis { display: grid; grid-template-columns: repeat(6, 1fr); gap: 14px; margin-bottom: 24px; }
.rp-kpi { padding: 16px; animation: fadeUp .5s ease both; animation-delay: calc(var(--i) * .05s); }
.rp-kpi i { width: 38px; height: 38px; border-radius: 11px; display: grid; place-items: center; font-size: 16px; margin-bottom: 10px; color: var(--gold); background: rgba(212,175,55,.1); }
.rp-kpi b { display: block; font-size: 22px; color: #fff; line-height: 1.15; overflow-wrap: anywhere; }
.rp-kpi small { color: var(--muted); font-size: 12px; }
.rp-kpi em { display: block; font-style: normal; font-size: 11.5px; color: #5fe0a5; margin-top: 4px; }

.rp-sec { margin-bottom: 26px; animation: fadeUp .5s ease both; }
.rp-sec-head { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
.rp-sec-head .ic { width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; font-size: 17px; color: #111; background: var(--gold-grad); box-shadow: 0 8px 20px rgba(212,175,55,.25); }
.rp-sec-head h3 { color: #fff; font-size: 19px; margin: 0; }
.rp-sec-head small { color: var(--muted); font-size: 12.5px; }
.rp-sec-head .tag-private { margin-left: auto; display: inline-flex; align-items: center; gap: 6px; font-size: 12px; padding: 5px 12px; border-radius: 20px; color: #5fe0a5; background: rgba(62,207,142,.1); border: 1px solid rgba(62,207,142,.3); }
.rp-mini { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 14px; }
.rp-mini > div { padding: 14px; border-radius: 14px; border: 1px solid var(--border); background: rgba(255,255,255,.02); }
.rp-mini small { display: block; color: var(--muted); font-size: 12px; }
.rp-mini b { color: #fff; font-size: 19px; }
.rp-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
.rp-grid.three { grid-template-columns: repeat(3, minmax(0, 1fr)); }
.rp-card { padding: 18px; min-width: 0; }
.rp-card.span2 { grid-column: span 2; }
.rp-card h5 { color: #ddd; font-size: 14px; font-weight: 500; margin: 0 0 12px; display: flex; align-items: center; gap: 8px; }
.rp-card h5 i { color: var(--gold); }
.rp-chart { position: relative; height: 240px; }
.rp-chart.sm { height: 210px; }
.rp-empty { display: grid; place-items: center; height: 100%; min-height: 120px; color: var(--muted); font-size: 13px; text-align: center; }
.rp-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.rp-table th { text-align: left; color: var(--muted); font-weight: 500; font-size: 11.5px; text-transform: uppercase; letter-spacing: 1px; padding: 8px 6px; border-bottom: 1px solid var(--border); }
.rp-table td { padding: 9px 6px; border-bottom: 1px solid rgba(255,255,255,.05); color: #ddd; }
.rp-table td.r, .rp-table th.r { text-align: right; }
.rp-table tr:last-child td { border-bottom: 0; }
.rp-table-wrap { overflow-x: auto; }
.status.red { color: #ff8c8c; background: rgba(255,107,107,.1); }
.rp-sk { height: 120px; border-radius: 18px; margin-bottom: 16px; background: linear-gradient(90deg, rgba(255,255,255,.03) 25%, rgba(255,255,255,.07) 50%, rgba(255,255,255,.03) 75%); background-size: 200% 100%; animation: rpSh 1.3s linear infinite; }
@keyframes rpSh { to { background-position: -200% 0; } }

@media (max-width: 1199px) { .rp-kpis { grid-template-columns: repeat(3, 1fr); } .rp-grid.three { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
@media (max-width: 767px) { .rp-grid, .rp-grid.three { grid-template-columns: minmax(0, 1fr); } .rp-card.span2 { grid-column: auto; } .rp-kpis { grid-template-columns: repeat(2, 1fr); } .rp-bar, .rp-bar .left, .rp-bar .right { flex-direction: column; align-items: stretch; } .rp-select { min-width: 0; } }

/* TABS */
.rp-tabs { position: sticky; top: 76px; z-index: 5; display: flex; gap: 6px; overflow-x: auto; scrollbar-width: none; padding: 6px; margin-bottom: 18px; border-radius: 16px;
    background: rgba(14,14,17,.92); border: 1px solid var(--border); backdrop-filter: blur(10px); }
.rp-tabs::-webkit-scrollbar { display: none; }
.rp-tabs button { flex-shrink: 0; display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 11px; border: 0; background: transparent; color: #aaa; font: inherit; font-size: 13.5px; cursor: pointer; white-space: nowrap; transition: .25s; }
.rp-tabs button i { color: var(--gold); transition: .25s; }
.rp-tabs button:hover { color: #fff; background: rgba(255,255,255,.04); }
.rp-tabs button.active { color: #111; font-weight: 600; background: var(--gold-grad); box-shadow: 0 6px 18px rgba(212,175,55,.3); }
.rp-tabs button.active i { color: #111; }
.rp-panel { display: none; }
.rp-panel.active { display: block; animation: fadeUp .4s ease both; }
.rp-panel.print-show { display: block; }   /* print ke waqt saare tabs */
@media (max-width: 575px) { .rp-tabs { top: 64px; } .rp-tabs button { padding: 9px 12px; font-size: 13px; } }

/* PRINT */
@media print {
    body { background: #fff !important; }
    .sidebar, .topbar, .adm-topbar, .page-actions, .rp-bar, .sb-overlay, .adm-footer, footer, .sub-banner, .adm-toast { display: none !important; }
    .adm-main, .adm-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
    .glass-card { background: #fff !important; border: 1px solid #ddd !important; box-shadow: none !important; }
    .glass-card::before, .glass-card::after { display: none !important; }
    .rp-kpi b, .rp-mini b, .rp-sec-head h3, .rp-table td, .page-head h1 { color: #111 !important; }
    .rp-sec { break-inside: avoid; }
    .rp-card { break-inside: avoid; }
    .rp-tabs { display: none !important; }
    .rp-panel.print-show { display: block !important; }
}
</style>
@endpush

@section('content')

<div class="glass-card rp-bar">
    <div class="left">
        <div class="filter-chips" id="rpPresets">
            <button data-p="month">This Month</button>
            <button data-p="30">Last 30 Days</button>
            <button data-p="quarter">Last 3 Months</button>
            <button class="active" data-p="year">This Year</button>
            <button data-p="all">All Time</button>
        </div>
    </div>
    <div class="right">
        <input type="date" class="rp-date" id="rpFrom" title="From">
        <input type="date" class="rp-date" id="rpTo" title="To">
        <select class="rp-select only-superadmin" id="rpAdmin" aria-label="Report for"><option value="">Whole firm (all admins)</option></select>
    </div>
</div>

<div class="rp-scope" id="rpScope"></div>
<div id="rpRoot">
    <div class="rp-sk"></div><div class="rp-sk" style="height:300px"></div><div class="rp-sk" style="height:300px"></div>
</div>

@endsection

@push('scripts')
<script>
/* ===== REPORTS – API (Bearer token) ===== */
document.addEventListener('auth:ready', function (ev) {
    if (!window.API || !['superadmin', 'admin'].includes(ev.detail.role)) return;
    const isSA = ev.detail.role === 'superadmin';
    const esc = API.esc, $ = i => document.getElementById(i);
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const root = $('rpRoot');
    const inr = n => '₹' + Number(n || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 });
    const num = n => Number(n || 0).toLocaleString('en-IN');
    const iso = d => new Date(d.getTime() - d.getTimezoneOffset() * 6e4).toISOString().slice(0, 10);
    const PAL = ['#d4af37', '#5b8def', '#3ecf8e', '#b07cff', '#ff8c8c', '#ffb347', '#4dd0e1', '#f06292', '#9ccc65', '#a1887f'];
    const state = { from: '', to: '', admin_id: '' };
    let charts = [], last = null, adminsLoaded = false;

    if (window.Chart) { Chart.defaults.color = '#9a9a9a'; Chart.defaults.font.family = 'Poppins, sans-serif'; Chart.defaults.borderColor = 'rgba(255,255,255,.06)'; }

    /* ---------- presets ---------- */
    function preset(p) {
        const t = new Date(), f = new Date();
        if (p === 'month') f.setDate(1);
        else if (p === '30') f.setDate(t.getDate() - 29);
        else if (p === 'quarter') f.setMonth(t.getMonth() - 2, 1);
        else if (p === 'year') f.setMonth(0, 1);
        else if (p === 'all') f.setFullYear(2020, 0, 1);
        state.from = iso(f); state.to = iso(t);
        $('rpFrom').value = state.from; $('rpTo').value = state.to;
        $('rpPresets').querySelectorAll('button').forEach(b => b.classList.toggle('active', b.dataset.p === p));
    }
    $('rpPresets').addEventListener('click', e => { const b = e.target.closest('[data-p]'); if (!b) return; preset(b.dataset.p); load(); });
    ['rpFrom', 'rpTo'].forEach(id => $(id).addEventListener('change', () => {
        state.from = $('rpFrom').value; state.to = $('rpTo').value;
        $('rpPresets').querySelectorAll('button').forEach(b => b.classList.remove('active'));
        if (state.from && state.to && state.from <= state.to) load();
    }));
    $('rpAdmin').addEventListener('change', e => { state.admin_id = e.target.value; load(); });

    /* ---------- chart helpers ---------- */
    const killCharts = () => { charts.forEach(c => c.destroy()); charts = []; };
    const entries = obj => Object.entries(obj || {}).filter(([, v]) => v !== null);
    const hasData = obj => entries(obj).some(([, v]) => +v > 0);
    function chart(id, cfg) {
        const el = document.getElementById(id); if (!el || !window.Chart) return;
        charts.push(new Chart(el, cfg));
    }
    function doughnut(id, obj, money) {
        if (!hasData(obj)) return emptyIn(id);
        const e = entries(obj);
        chart(id, { type: 'doughnut', data: { labels: e.map(x => x[0]), datasets: [{ data: e.map(x => +x[1]), backgroundColor: PAL, borderWidth: 0 }] },
            options: { maintainAspectRatio: false, cutout: '64%', plugins: { legend: { position: 'right', labels: { boxWidth: 10, padding: 10 } },
                tooltip: { callbacks: { label: c => ` ${c.label}: ${money ? inr(c.raw) : num(c.raw)}` } } } } });
    }
    function bars(id, obj, money, horizontal) {
        if (!hasData(obj)) return emptyIn(id);
        const e = entries(obj);
        chart(id, { type: 'bar', data: { labels: e.map(x => x[0]), datasets: [{ data: e.map(x => +x[1]), backgroundColor: 'rgba(212,175,55,.75)', borderRadius: 6, maxBarThickness: 34 }] },
            options: { indexAxis: horizontal ? 'y' : 'x', maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ' + (money ? inr(c.raw) : num(c.raw)) } } },
                scales: { x: { grid: { display: !horizontal } }, y: { beginAtZero: true, ticks: { precision: 0, callback: v => money && !horizontal ? inr(v) : v } } } } });
    }
    function line(id, trend, money, label) {
        if (!(trend || []).some(t => t.value > 0)) return emptyIn(id);
        chart(id, { type: 'line', data: { labels: trend.map(t => t.label), datasets: [{ label, data: trend.map(t => t.value), borderColor: '#d4af37', backgroundColor: 'rgba(212,175,55,.15)', fill: true, tension: .35, pointRadius: 3 }] },
            options: { maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ' + (money ? inr(c.raw) : num(c.raw)) } } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0, callback: v => money ? inr(v) : v } }, x: { grid: { display: false } } } } });
    }
    function emptyIn(id) { const el = document.getElementById(id); if (el) el.parentElement.innerHTML = '<div class="rp-empty"><span><i class="fa-regular fa-chart-bar"></i><br>No data for this period</span></div>'; }

    /* ---------- render pieces ---------- */
    const kpi = (i, icon, value, label, sub) => `<div class="glass-card rp-kpi" style="--i:${i}"><i class="fa-solid ${icon}"></i><b>${value}</b><small>${label}</small>${sub ? `<em>${sub}</em>` : ''}</div>`;
    const mini = list => `<div class="rp-mini">${list.map(([l, v]) => `<div><small>${l}</small><b>${v}</b></div>`).join('')}</div>`;
    const head = (icon, title, sub, extra = '') => `<div class="rp-sec-head"><span class="ic"><i class="fa-solid ${icon}"></i></span><div><h3>${title}</h3><small>${sub}</small></div>${extra}</div>`;
    const card = (title, icon, body, cls = '') => `<div class="glass-card rp-card ${cls}"><h5><i class="fa-solid ${icon}"></i>${title}</h5>${body}</div>`;
    const canvas = (id, sm) => `<div class="rp-chart ${sm ? 'sm' : ''}"><canvas id="${id}"></canvas></div>`;
    const table = (cols, rows, emptyMsg) => rows.length
        ? `<div class="rp-table-wrap"><table class="rp-table"><thead><tr>${cols.map(c => `<th class="${c.r ? 'r' : ''}">${c.t}</th>`).join('')}</tr></thead><tbody>${rows.map(r => `<tr>${r.map((v, i) => `<td class="${cols[i].r ? 'r' : ''}">${v}</td>`).join('')}</tr>`).join('')}</tbody></table></div>`
        : `<div class="rp-empty">${emptyMsg || 'Nothing to show.'}</div>`;

    function render(d) {
        killCharts();
        last = d;
        const c = d.cases, cl = d.clients, p = d.payments, global = d.scope.is_global;
        const who = d.scope.focus ? (isSA ? `<span class="pill blue"><i class="fa-solid fa-user-tie"></i> ${esc(d.scope.focus.name)}</span>` : '<span class="pill blue">My report</span>') : '<span class="pill">Whole firm</span>';
        $('rpScope').innerHTML = `${who}<span><i class="fa-regular fa-calendar"></i> <b>${esc(d.period.label)}</b></span><span>· Generated ${esc(d.scope.generated)}</span>`;

        let h = `<div class="rp-kpis">
            ${kpi(0, 'fa-gavel', num(c?.total), 'Total Cases', c ? `+${num(c.new)} in period` : '')}
            ${kpi(1, 'fa-users', num(cl?.total), 'Clients', cl ? `+${num(cl.new)} in period` : '')}
            ${kpi(2, 'fa-indian-rupee-sign', inr(p?.received), 'My Payments Received', p ? `${num(p.count)} payments` : '')}
            ${kpi(3, 'fa-hourglass-half', inr(p?.pending), 'My Pending Payments')}
            ${global ? kpi(4, 'fa-user-tie', num(d.admins?.total), 'Admins', `${num(d.admins?.active)} active`) : kpi(4, 'fa-crown', d.my_subscription ? (d.my_subscription.active ? esc(d.my_subscription.plan) : 'Inactive') : '—', 'Subscription', d.my_subscription?.active ? `${d.my_subscription.days_left} days left` : '')}
            ${global ? kpi(5, 'fa-envelope-open-text', num(d.enquiries?.total), 'Enquiries', `${num(d.enquiries?.unread)} unread`) : kpi(5, 'fa-right-to-bracket', num(d.logins?.success), 'Logins in period', d.logins?.last ? 'Last: ' + esc(d.logins.last) : '')}
        </div>`;

        /* ---------- sections -> TABS (ek tab khulega, baaki chhupe) ---------- */
        const secs = [];
        const add = (id, label, icon, html, draw) => secs.push({ id, label, icon, html, draw });

        /* CASES */
        if (c) add('cases', 'Cases', 'fa-gavel', `${head('fa-gavel', 'Cases', global ? 'All cases in the firm' : (isSA ? 'Cases assigned to this admin' : 'Cases assigned to you'))}
            ${mini([['Total', num(c.total)], ['New in period', num(c.new)], ...Object.entries(c.by_status).map(([k, v]) => [k, num(v)])])}
            <div class="rp-grid">
                ${card('New cases per month', 'fa-chart-line', canvas('chCaseTrend'))}
                ${card('By status', 'fa-chart-pie', canvas('chCaseStatus'))}
                ${card('By practice area', 'fa-scale-balanced', canvas('chCasePractice'))}
                ${c.by_advocate ? card('By advocate', 'fa-user-tie', canvas('chCaseAdv')) : card('By priority', 'fa-flag', canvas('chCasePrio'))}
                ${card('Upcoming hearings (next 30 days)', 'fa-landmark', table([{ t: 'Date' }, { t: 'Case' }, { t: 'Client' }, ...(global ? [{ t: 'Advocate' }] : []), { t: 'Status' }],
                    (c.hearings || []).map(x => [esc(x.date), `<span class="case-id">${esc(x.case_no)}</span> ${esc(x.title)}`, esc(x.client), ...(global ? [esc(x.advocate || '—')] : []), esc(x.status)]), 'No hearings in the next 30 days.'), 'span2')}
            </div>`, () => {
            line('chCaseTrend', c.trend, false, 'Cases'); doughnut('chCaseStatus', c.by_status); bars('chCasePractice', c.by_practice, false, true);
            c.by_advocate ? bars('chCaseAdv', c.by_advocate, false, true) : doughnut('chCasePrio', c.by_priority);
        });

        /* CLIENTS */
        if (cl) add('clients', 'Clients', 'fa-users', `${head('fa-users', 'Clients', global ? 'All clients' : (isSA ? "This admin's clients" : 'Your clients'))}
            ${mini([['Total', num(cl.total)], ['Active', num(cl.active)], ['Inactive', num(cl.inactive)], ['With login', num(cl.with_login)], ['New in period', num(cl.new)]])}
            <div class="rp-grid">
                ${card('New clients per month', 'fa-chart-line', canvas('chClTrend'))}
                ${card('By practice area', 'fa-scale-balanced', canvas('chClPractice'))}
                ${card('By city', 'fa-location-dot', canvas('chClCity'))}
                ${cl.by_advocate ? card('By advocate', 'fa-user-tie', canvas('chClAdv')) : card('Active vs inactive', 'fa-toggle-on', canvas('chClActive'))}
            </div>`, () => {
            line('chClTrend', cl.trend, false, 'Clients'); bars('chClPractice', cl.by_practice, false, true); bars('chClCity', cl.by_city, false, true);
            cl.by_advocate ? bars('chClAdv', cl.by_advocate, false, true) : doughnut('chClActive', { Active: cl.active, Inactive: cl.inactive });
        });

        /* MY PAYMENTS (private) */
        if (p) add('payments', 'My Payments', 'fa-indian-rupee-sign', `${head('fa-indian-rupee-sign', 'My Payments', 'Only your own payments are included', '<span class="tag-private"><i class="fa-solid fa-user-lock"></i> Private – visible only to you</span>')}
            ${mini([['Received (period)', inr(p.received)], ['Pending (all)', inr(p.pending)], ['Payments (period)', num(p.count)], ['Average payment', inr(p.avg)], ['All-time received', inr(p.all_time)]])}
            <div class="rp-grid">
                ${card('Received per month', 'fa-chart-column', canvas('chPayTrend'))}
                ${card('By payment mode', 'fa-wallet', canvas('chPayMode'))}
                ${card('By category', 'fa-tags', canvas('chPayCat'))}
                ${card('Top paying clients', 'fa-trophy', table([{ t: 'Client' }, { t: 'Payments', r: 1 }, { t: 'Total', r: 1 }],
                    (p.top_clients || []).map(x => [esc(x.name), num(x.count), `<b style="color:var(--gold-light)">${inr(x.total)}</b>`]), 'No payments in this period.'))}
            </div>`, () => {
            const t = p.trend || [];
            if (t.some(x => x.value > 0)) chart('chPayTrend', { type: 'bar', data: { labels: t.map(x => x.label), datasets: [{ data: t.map(x => x.value), backgroundColor: 'rgba(62,207,142,.7)', borderRadius: 6, maxBarThickness: 34 }] },
                options: { maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ' + inr(c.raw) } } }, scales: { y: { beginAtZero: true, ticks: { callback: v => inr(v) } }, x: { grid: { display: false } } } } });
            else emptyIn('chPayTrend');
            doughnut('chPayMode', p.by_mode, true); bars('chPayCat', p.by_category, true, true);
        });

        /* ADMINS + SUBSCRIPTIONS (superadmin, whole firm) */
        if (global && d.admins) {
            const s = d.subscriptions;
            add('admins', 'Admins (Advocates)', 'fa-user-tie', `${head('fa-user-tie', 'Admins (Advocates)', 'Team overview')}
            ${mini([['Total', num(d.admins.total)], ['Active', num(d.admins.active)], ['Pending', num(d.admins.pending)], ['Inactive', num(d.admins.inactive)], ['Joined in period', num(d.admins.new)]])}
            <div class="rp-grid">
                ${card('Top admins', 'fa-ranking-star', table([{ t: 'Admin' }, { t: 'Clients', r: 1 }, { t: 'Cases', r: 1 }, { t: 'Subscription' }],
                    (d.admins.top || []).map(a => [`${esc(a.name)}<br><small class="t-sub">${esc(a.email)}</small>`, num(a.clients), num(a.cases), a.subscription ? `<span class="status green">${esc(a.subscription)}</span>` : '<span class="status red">None</span>']), 'No admins yet.'), 'span2')}
            </div>
            ${s ? `<div style="margin-top:22px">${head('fa-crown', 'Subscriptions', 'Plans given to admins')}
            ${mini([['Active admins', num(s.active)], ['Expired', num(s.expired)], ['Awaiting acceptance', num(s.awaiting)], ['Given in period', num(s.given)], ['Revenue (period)', inr(s.revenue)]])}
            <div class="rp-grid">
                ${card('Subscription revenue per month', 'fa-chart-column', canvas('chSubTrend'))}
                ${card('By plan (period)', 'fa-layer-group', table([{ t: 'Plan' }, { t: 'Given', r: 1 }, { t: 'Revenue', r: 1 }], (s.by_plan || []).map(x => [esc(x.name), num(x.count), inr(x.revenue)]), 'No subscriptions given in this period.'))}
                ${card('Expiring in 7 days', 'fa-clock', table([{ t: 'Admin' }, { t: 'Plan' }, { t: 'Ends' }, { t: 'Left', r: 1 }], (s.expiring || []).map(x => [esc(x.admin), esc(x.plan), esc(x.ends), `${x.days_left}d`]), 'No subscription expiring soon.'), 'span2')}
            </div></div>` : ''}`, () => { if (s) line('chSubTrend', s.trend, true, 'Revenue'); });
        }

        /* ADMIN / FOCUSED ADMIN: SUBSCRIPTION */
        if (d.my_subscription) {
            const ms = d.my_subscription;
            add('subscription', isSA ? 'Subscription' : 'My Subscription', 'fa-crown', `${head('fa-crown', isSA ? 'Subscription' : 'My Subscription', ms.active ? `${esc(ms.plan)} · ends ${esc(ms.ends)} · ${ms.days_left} days left` : 'No active subscription')}
            ${card('History', 'fa-clock-rotate-left', table([{ t: 'Plan' }, { t: 'Price', r: 1 }, { t: 'Period' }, { t: 'Status' }],
                (ms.history || []).map(x => [esc(x.plan), inr(x.price), `${esc(x.from || '—')} → ${esc(x.to || '—')}`, esc(x.status)]), 'No subscriptions yet.'))}`, () => {});
        }

        /* ENQUIRIES & CAREER (superadmin, whole firm) */
        if (global && (d.enquiries || d.career)) add('enquiries', 'Enquiries & Career', 'fa-envelope-open-text', `${head('fa-envelope-open-text', 'Enquiries & Career', 'Website contact form and job applications')}
            ${mini([['Enquiries (period)', num(d.enquiries?.total)], ['Unread enquiries', num(d.enquiries?.unread)], ['Applications (period)', num(d.career?.applications)], ['Live jobs', num(d.career?.open_jobs)], ['Open positions', num(d.career?.positions)]])}
            <div class="rp-grid three">
                ${card('Enquiries per month', 'fa-chart-line', canvas('chEnqTrend', 1))}
                ${card('Enquiries by practice area', 'fa-scale-balanced', canvas('chEnqPractice', 1))}
                ${card('Enquiry status', 'fa-chart-pie', canvas('chEnqStatus', 1))}
                ${card('Applications by job', 'fa-briefcase', canvas('chCarJob', 1))}
                ${card('Application status', 'fa-list-check', canvas('chCarStatus', 1))}
            </div>`, () => {
            if (d.enquiries) { line('chEnqTrend', d.enquiries.trend, false, 'Enquiries'); bars('chEnqPractice', d.enquiries.by_practice, false, true); doughnut('chEnqStatus', d.enquiries.by_status); }
            if (d.career) { bars('chCarJob', d.career.by_job, false, true); doughnut('chCarStatus', d.career.by_status); }
        });

        /* LOGIN ACTIVITY */
        if (d.logins) add('logins', 'Login Activity', 'fa-right-to-bracket', `${head('fa-right-to-bracket', 'Login Activity', global ? 'All roles' : 'Sign-ins of this account')}
            ${mini([['Successful (period)', num(d.logins.success)], ['Failed / blocked', num(d.logins.failed)], ...(d.logins.last ? [['Last login', esc(d.logins.last)]] : [])])}
            <div class="rp-grid">
                ${card('Last 14 days', 'fa-chart-column', canvas('chLogDaily'), d.logins.by_role ? '' : 'span2')}
                ${d.logins.by_role ? card('Successful logins by role', 'fa-users-gear', canvas('chLogRole')) : ''}
            </div>`, () => {
            const dl = d.logins.daily || [];
            if (dl.some(x => x.success || x.failed)) chart('chLogDaily', { type: 'bar', data: { labels: dl.map(x => x.label), datasets: [
                    { label: 'Success', data: dl.map(x => x.success), backgroundColor: 'rgba(212,175,55,.75)', borderRadius: 4, maxBarThickness: 22 },
                    { label: 'Failed', data: dl.map(x => x.failed), backgroundColor: 'rgba(255,107,107,.7)', borderRadius: 4, maxBarThickness: 22 }] },
                options: { maintainAspectRatio: false, plugins: { legend: { labels: { boxWidth: 10 } } }, scales: { x: { stacked: true, grid: { display: false } }, y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } } } } });
            else emptyIn('chLogDaily');
            if (d.logins.by_role) doughnut('chLogRole', Object.fromEntries(Object.entries(d.logins.by_role).map(([k, v]) => [API.roleMeta(k).label, v])));
        });

        /* tab bar + panels */
        if (!secs.some(x => x.id === activeTab)) activeTab = secs[0]?.id;
        h += `<div class="rp-tabs" id="rpTabs" role="tablist">${secs.map(x => `<button role="tab" data-tab="${x.id}" class="${x.id === activeTab ? 'active' : ''}"><i class="fa-solid ${x.icon}"></i> <span>${esc(x.label)}</span></button>`).join('')}</div>
              ${secs.map(x => `<div class="rp-sec rp-panel ${x.id === activeTab ? 'active' : ''}" id="rpPanel-${x.id}" role="tabpanel">${x.html}</div>`).join('')}`;
        root.innerHTML = h;

        drawn = {};
        sections = secs;
        openTab(activeTab);
    }

    /* tab kholna – chart sirf pehli baar khulne par banta hai (hidden canvas ka size 0 hota hai) */
    let activeTab = (() => { try { return localStorage.getItem('rp_tab') || 'cases'; } catch (e) { return 'cases'; } })();
    let sections = [], drawn = {};
    function openTab(id) {
        const sec = sections.find(x => x.id === id); if (!sec) return;
        activeTab = id;
        try { localStorage.setItem('rp_tab', id); } catch (e) {}
        document.querySelectorAll('#rpTabs [data-tab]').forEach(b => b.classList.toggle('active', b.dataset.tab === id));
        document.querySelectorAll('.rp-panel').forEach(pn => pn.classList.toggle('active', pn.id === 'rpPanel-' + id));
        if (!drawn[id]) { drawn[id] = true; sec.draw(); }
    }
    root.addEventListener('click', e => { const b = e.target.closest('#rpTabs [data-tab]'); if (b) openTab(b.dataset.tab); });

    // print: saare tab dikhao aur unke charts bana do
    window.addEventListener('beforeprint', () => {
        document.querySelectorAll('.rp-panel').forEach(pn => pn.classList.add('print-show'));
        sections.forEach(x => { if (!drawn[x.id]) { drawn[x.id] = true; x.draw(); } });
    });
    window.addEventListener('afterprint', () => document.querySelectorAll('.rp-panel').forEach(pn => pn.classList.remove('print-show')));

    async function load() {
        killCharts();
        root.innerHTML = '<div class="rp-sk"></div><div class="rp-sk" style="height:300px"></div>';
        const qs = new URLSearchParams();
        Object.entries(state).forEach(([k, v]) => { if (v) qs.set(k, v); });
        try {
            const d = await API.get('/reports?' + qs);
            if (isSA && !adminsLoaded) {
                $('rpAdmin').insertAdjacentHTML('beforeend', (d.admin_options || []).map(a => `<option value="${a.id}">${esc(a.name)}</option>`).join(''));
                adminsLoaded = true;
                const qa = new URLSearchParams(location.search).get('admin');
                if (qa && !state.admin_id) { $('rpAdmin').value = qa; if ($('rpAdmin').value === qa) { state.admin_id = qa; return load(); } }
            }
            render(d);
        } catch (err) {
            root.innerHTML = `<div class="glass-card card"><div class="t-empty error"><i class="fa-solid fa-triangle-exclamation"></i><p>${esc(err.message)}</p></div></div>`;
        }
    }

    /* ---------- export CSV (summary) ---------- */
    $('rpExport').addEventListener('click', () => {
        if (!last) return;
        const d = last, rows = [['Section', 'Metric', 'Value']];
        const add = (s, obj) => Object.entries(obj || {}).forEach(([k, v]) => rows.push([s, k, v]));
        rows.push(['Report', 'Period', d.period.label], ['Report', 'Scope', d.scope.focus ? d.scope.focus.name : 'Whole firm']);
        if (d.cases) { rows.push(['Cases', 'Total', d.cases.total], ['Cases', 'New in period', d.cases.new]); add('Cases – status', d.cases.by_status); add('Cases – practice area', d.cases.by_practice); add('Cases – advocate', d.cases.by_advocate); }
        if (d.clients) { ['total', 'active', 'inactive', 'with_login', 'new'].forEach(k => rows.push(['Clients', k, d.clients[k]])); add('Clients – practice area', d.clients.by_practice); add('Clients – city', d.clients.by_city); add('Clients – advocate', d.clients.by_advocate); }
        if (d.payments) { ['received', 'pending', 'count', 'avg', 'all_time'].forEach(k => rows.push(['My payments', k, d.payments[k]])); add('My payments – mode', d.payments.by_mode); add('My payments – category', d.payments.by_category); (d.payments.top_clients || []).forEach(t => rows.push(['My payments – top clients', t.name, t.total])); }
        if (d.admins) ['total', 'active', 'pending', 'inactive', 'new'].forEach(k => rows.push(['Admins', k, d.admins[k]]));
        if (d.subscriptions) { ['active', 'expired', 'awaiting', 'given', 'revenue'].forEach(k => rows.push(['Subscriptions', k, d.subscriptions[k]])); (d.subscriptions.by_plan || []).forEach(x => rows.push(['Subscriptions – plan', x.name, x.revenue])); }
        if (d.enquiries) { rows.push(['Enquiries', 'Total', d.enquiries.total]); add('Enquiries – status', d.enquiries.by_status); add('Enquiries – practice area', d.enquiries.by_practice); }
        if (d.career) { rows.push(['Career', 'Applications', d.career.applications]); add('Career – status', d.career.by_status); add('Career – job', d.career.by_job); }
        if (d.logins) rows.push(['Logins', 'Success', d.logins.success], ['Logins', 'Failed', d.logins.failed]);
        const q = v => `"${String(v ?? '').replace(/"/g, '""')}"`;
        const a = document.createElement('a');
        a.href = URL.createObjectURL(new Blob(['﻿' + rows.map(r => r.map(q).join(',')).join('\r\n')], { type: 'text/csv;charset=utf-8' }));
        a.download = `report-${d.period.from}-to-${d.period.to}.csv`; a.click();
        setTimeout(() => URL.revokeObjectURL(a.href), 2000);
    });
    $('rpPrint').addEventListener('click', () => window.print());

    preset('year');
    load();
});
</script>
@endpush