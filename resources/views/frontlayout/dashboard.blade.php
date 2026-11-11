@extends('frontlayout.app')
@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('require_role', 'superadmin')



@section('page_actions')
<button class="adm-btn adm-btn-ghost" id="dbRefresh"><i class="fa-solid fa-rotate"></i> <span class="hide-xs">Refresh</span></button>
<a href="{{ route('superadmin.admins.create') }}" class="adm-btn adm-btn-gold"><i class="fa-solid fa-user-plus"></i> Create Admin</a>
@endsection

@push('styles')
<style>
    .db-sub {
        display: block;
        margin-top: 2px;
        font-size: 12px;
        color: var(--muted);
        position: relative;
        z-index: 1;
    }

    .db-sub b {
        color: #ddd;
        font-weight: 500;
    }

    .db-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 500;
        white-space: nowrap;
    }

    .db-pill i {
        font-size: 10px;
    }

    .db-pill.superadmin {
        color: var(--gold-light);
        background: rgba(212, 175, 55, .13);
    }

    .db-pill.admin {
        color: #8fb4ff;
        background: rgba(91, 141, 239, .13);
    }

    .db-pill.user {
        color: #7fe0a6;
        background: rgba(62, 207, 142, .12);
    }

    .db-pill.none {
        color: #999;
        background: rgba(255, 255, 255, .06);
    }

    .db-empty {
        text-align: center;
        padding: 34px 10px;
        color: var(--muted);
    }

    .db-empty i {
        display: block;
        font-size: 30px;
        color: rgba(212, 175, 55, .5);
        margin-bottom: 8px;
    }

    .db-sk {
        display: block;
        height: 11px;
        border-radius: 6px;
        background: linear-gradient(90deg, rgba(255, 255, 255, .04) 25%, rgba(255, 255, 255, .1) 50%, rgba(255, 255, 255, .04) 75%);
        background-size: 200% 100%;
        animation: dbSk 1.3s linear infinite;
    }

    @keyframes dbSk {
        to {
            background-position: -200% 0;
        }
    }

    .db-chart-empty {
        position: absolute;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        color: var(--muted);
        font-size: 13px;
        z-index: 2;
    }

    .db-chart-empty.show {
        display: flex;
    }

    .db-ava {
        width: 36px;
        height: 36px;
        flex-shrink: 0;
        border-radius: 10px;
        overflow: hidden;
        display: grid;
        place-items: center;
        font-weight: 600;
        color: #111;
        background: var(--gold-grad);
    }

    .db-ava img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .db-ava.green {
        background: linear-gradient(135deg, #7ff0b8, #2fbf7d);
        color: #062b1a;
    }

    .db-ava.grey {
        background: rgba(255, 255, 255, .08);
        color: #999;
    }

    .db-users li {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 0;
        border-bottom: 1px solid rgba(255, 255, 255, .04);
        position: relative;
        z-index: 1;
    }

    .db-users li:last-child {
        border-bottom: 0;
    }

    .db-users li>div {
        flex: 1;
        min-width: 0;
    }

    .db-users h6 {
        font-size: 13.5px;
        color: #fff;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .db-users small {
        font-size: 12px;
        color: var(--muted);
    }

    .db-quick {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        position: relative;
        z-index: 1;
    }

    .db-quick a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px;
        border-radius: 14px;
        border: 1px solid var(--border);
        background: rgba(255, 255, 255, .02);
        font-size: 13.5px;
        color: #ddd;
        transition: .3s;
    }

    .db-quick a i {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        color: #111;
        background: var(--gold-grad);
        font-size: 14px;
        flex-shrink: 0;
    }

    .db-quick a:hover {
        border-color: rgba(212, 175, 55, .4);
        transform: translateY(-3px);
        color: #fff;
    }

    @media (max-width: 420px) {
        .db-quick {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

{{-- ================= WELCOME ================= --}}
<div class="welcome glass-card">
    <div class="welcome-text">
        <span class="chip"><i class="fa-solid fa-crown"></i> <span id="dbGreet">Welcome</span></span>
        <h2>Welcome back, <span class="gold-text" data-user="first_name">…</span> 👋</h2>
        <p>You have <b id="dbPendingTxt">0</b> advocate registration(s) pending and <b id="dbLoginTxt">0</b> successful login(s) today.</p>
        <div class="welcome-actions">
            <a href="{{ route('superadmin.admins') }}?status=pending" class="adm-btn adm-btn-gold"><i class="fa-solid fa-user-clock"></i> Pending Approvals</a>
            <a href="{{ route('superadmin.login-activity') }}" class="adm-btn adm-btn-ghost"><i class="fa-solid fa-clock-rotate-left"></i> Login Activity</a>
        </div>
    </div>
    <div class="welcome-art"><i class="fa-solid fa-scale-balanced"></i></div>
</div>

{{-- ================= STATS (API) ================= --}}
<div class="stat-grid">
    <div class="stat-card glass-card" style="--i:0">
        <div class="sc-top"><span class="sc-icon gold"><i class="fa-solid fa-user-tie"></i></span></div>
        <h3 class="sc-value" data-s="admins.total">–</h3>
        <p class="sc-label">Total Admins (Advocates)</p>
        <span class="db-sub"><b data-s="admins.active">0</b> active · <b data-s="admins.inactive">0</b> inactive</span>
    </div>
    <div class="stat-card glass-card" style="--i:1">
        <div class="sc-top"><span class="sc-icon blue"><i class="fa-solid fa-user-clock"></i></span></div>
        <h3 class="sc-value" data-s="admins.pending">–</h3>
        <p class="sc-label">Pending Approvals</p>
        <span class="db-sub"><a href="{{ route('superadmin.admins') }}?status=pending" style="color:var(--gold-light)">Review now →</a></span>
    </div>
    <div class="stat-card glass-card" style="--i:2">
        <div class="sc-top"><span class="sc-icon green"><i class="fa-solid fa-users"></i></span></div>
        <h3 class="sc-value" data-s="users.total">–</h3>
        <p class="sc-label">Total Users</p>
        <span class="db-sub"><b data-s="users.active">0</b> active · <b data-s="users.inactive">0</b> inactive</span>
    </div>
    <div class="stat-card glass-card" style="--i:3">
        <div class="sc-top"><span class="sc-icon purple"><i class="fa-solid fa-right-to-bracket"></i></span></div>
        <h3 class="sc-value" data-s="logins_today.success">–</h3>
        <p class="sc-label">Logins Today</p>
        <span class="db-sub"><b data-s="failed_total">0</b> failed / blocked</span>
    </div>
</div>

{{-- ================= CHARTS ================= --}}
<div class="grid-2-1">
    <div class="glass-card card">
        <div class="card-head">
            <div>
                <h4>Login Activity</h4><small>Last 7 days</small>
            </div>
        </div>
        <div class="chart-box">
            <canvas id="dbWeekChart"></canvas>
            <div class="db-chart-empty" id="dbWeekEmpty">No login data yet</div>
        </div>
    </div>

    <div class="glass-card card">
        <div class="card-head">
            <div>
                <h4>Today's Logins by Role</h4><small>Successful logins</small>
            </div>
        </div>
        <div class="chart-box sm">
            <canvas id="dbRoleChart"></canvas>
            <div class="db-chart-empty" id="dbRoleEmpty">No logins today</div>
        </div>
        <ul class="legend" id="dbRoleLegend"></ul>
    </div>
</div>

{{-- ================= RECENT ADMINS + USERS ================= --}}
<div class="grid-2-1">
    <div class="glass-card card">
        <div class="card-head">
            <div>
                <h4>Recent Admins</h4><small>Sign up + created by superadmin</small>
            </div>
            <a href="{{ route('superadmin.admins') }}" class="adm-btn adm-btn-ghost sm">View All</a>
        </div>
        <div class="table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Admin</th>
                        <th class="hide-sm">City</th>
                        <th>Status</th>
                        <th class="hide-md">Joined</th>
                        <th class="t-right">Action</th>
                    </tr>
                </thead>
                <tbody id="dbAdmins"></tbody>
            </table>
        </div>
    </div>

    <div class="glass-card card">
        <div class="card-head">
            <div>
                <h4>New Users</h4><small>Recently signed up</small>
            </div>
            <a href="{{ route('superadmin.users') }}" class="adm-btn adm-btn-ghost sm">View All</a>
        </div>
        <ul class="db-users" id="dbUsers"></ul>
    </div>
</div>

{{-- ================= LOGIN ACTIVITY + QUICK ================= --}}
<div class="grid-2-1">
    <div class="glass-card card">
        <div class="card-head">
            <div>
                <h4>Recent Login Activity</h4><small>All roles</small>
            </div>
            <a href="{{ route('superadmin.login-activity') }}" class="adm-btn adm-btn-ghost sm">View All</a>
        </div>
        <div class="table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="hide-sm">Device</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody id="dbLogs"></tbody>
            </table>
        </div>
    </div>

    <div class="glass-card card">
        <div class="card-head">
            <div>
                <h4>Quick Actions</h4>
            </div>
        </div>
        <div class="db-quick">
            <a href="{{ route('superadmin.admins.create') }}"><i class="fa-solid fa-user-plus"></i> Create Admin</a>
            <a href="{{ route('superadmin.admins') }}"><i class="fa-solid fa-user-tie"></i> All Admins</a>
            <a href="{{ route('superadmin.users') }}"><i class="fa-solid fa-users-line"></i> Users</a>
            <a href="{{ route('superadmin.login-activity') }}"><i class="fa-solid fa-clock-rotate-left"></i> Login Activity</a>
            <a href="{{ route('superadmin.enquiries') }}"><i class="fa-solid fa-envelope-open-text"></i> Enquiries</a>
            <a href="{{ route('home') }}" target="_blank"><i class="fa-solid fa-globe"></i> Website</a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    (function() {
        'use strict';

        const $ = id => document.getElementById(id);
        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        } [c]));
        const toast = (m, e) => window.admToast ? window.admToast(m, e) : console.log(m);
        const ROLE = {
            superadmin: ['Super Admin', 'fa-crown'],
            admin: ['Admin', 'fa-user-tie'],
            user: ['User', 'fa-user']
        };
        const ava = (name, photo, cls = '') => '<span class="db-ava ' + cls + '">' + (photo ? '<img src="' + esc(photo) + '" alt="">' : esc((name || '?').replace(/^adv\.?\s+/i, '').charAt(0).toUpperCase())) + '</span>';
        const skRows = (cols, n = 4) => Array.from({
            length: n
        }, () => '<tr>' + '<td><span class="db-sk"></span></td>'.repeat(cols) + '</tr>').join('');
        const emptyRow = (cols, msg, icon = 'fa-inbox') => '<tr><td colspan="' + cols + '"><div class="db-empty"><i class="fa-solid ' + icon + '"></i>' + esc(msg) + '</div></td></tr>';

        let weekChart = null,
            roleChart = null;

        /* greeting */
        const h = new Date().getHours();
        $('dbGreet').textContent = h < 12 ? 'Good Morning' : (h < 17 ? 'Good Afternoon' : 'Good Evening');

        /* ================= 1. STATS + CHARTS ================= */
        function loadStats() {
            return API.get('/dashboard/stats').then(s => {
                const pick = path => path.split('.').reduce((o, k) => (o || {})[k], s);
                s.failed_total = (s.logins_today.failed || 0) + (s.logins_today.blocked || 0);
                document.querySelectorAll('[data-s]').forEach(el => {
                    el.textContent = pick(el.dataset.s) ?? 0;
                });
                $('dbPendingTxt').textContent = s.admins.pending;
                $('dbLoginTxt').textContent = s.logins_today.success;

                const pb = $('pendingBadge');
                if (pb) {
                    pb.textContent = s.admins.pending;
                    pb.hidden = !s.admins.pending;
                }

                drawWeek(s.logins_week || []);
                drawRoles(s.logins_today);
            }).catch(err => toast(err.message, true));
        }

        function drawWeek(week) {
            if (!window.Chart) return;
            const labels = week.map(d => d.label);
            const success = week.map(d => d.success);
            const failed = week.map(d => d.failed);
            $('dbWeekEmpty').classList.toggle('show', !success.some(Boolean) && !failed.some(Boolean));

            const ctx = $('dbWeekChart').getContext('2d');
            const g = ctx.createLinearGradient(0, 0, 0, 300);
            g.addColorStop(0, 'rgba(212,175,55,.45)');
            g.addColorStop(1, 'rgba(212,175,55,0)');

            if (weekChart) weekChart.destroy();
            weekChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                            label: 'Successful',
                            data: success,
                            borderColor: '#d4af37',
                            backgroundColor: g,
                            fill: true,
                            tension: .4,
                            borderWidth: 2.5,
                            pointRadius: 3,
                            pointBackgroundColor: '#f5d77a'
                        },
                        {
                            label: 'Failed / Blocked',
                            data: failed,
                            borderColor: '#ff6b6b',
                            backgroundColor: 'transparent',
                            tension: .4,
                            borderWidth: 2,
                            borderDash: [5, 5],
                            pointRadius: 3,
                            pointBackgroundColor: '#ff6b6b'
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: '#bbb',
                                usePointStyle: true,
                                boxWidth: 8
                            }
                        },
                        tooltip: {
                            backgroundColor: '#17171b',
                            borderColor: 'rgba(212,175,55,.4)',
                            borderWidth: 1,
                            padding: 12
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#8d8d97'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: '#8d8d97'
                            },
                            grid: {
                                color: 'rgba(255,255,255,.05)'
                            }
                        }
                    }
                }
            });
        }

        function drawRoles(t) {
            if (!window.Chart) return;
            const labels = ['Super Admin', 'Admin', 'User'];
            const values = [t.superadmin || 0, t.admin || 0, t.user || 0];
            const colors = ['#d4af37', '#5b8def', '#3ecf8e'];
            $('dbRoleEmpty').classList.toggle('show', !values.some(Boolean));

            if (roleChart) roleChart.destroy();
            roleChart = new Chart($('dbRoleChart'), {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data: values.some(Boolean) ? values : [1, 1, 1],
                        backgroundColor: values.some(Boolean) ? colors : ['#222', '#222', '#222'],
                        borderColor: '#0d0d10',
                        borderWidth: 4,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: values.some(Boolean)
                        }
                    }
                }
            });
            $('dbRoleLegend').innerHTML = labels.map((l, i) => '<li><i style="background:' + colors[i] + '"></i>' + l + '<b>' + values[i] + '</b></li>').join('');
        }

        /* ================= 2. RECENT ADMINS ================= */
        function loadAdmins() {
            const tb = $('dbAdmins');
            tb.innerHTML = skRows(5);
            return API.get('/admins?per_page=6').then(r => {
                tb.innerHTML = r.data.length ? r.data.map(u => {
                    const p = u.profile || {};
                    const st = {
                        active: 'green',
                        pending: 'gold',
                        inactive: 'grey'
                    } [u.status] || 'grey';
                    const action = u.status !== 'active' ?
                        '<button class="adm-btn adm-btn-gold sm" data-approve="' + u.id + '"><i class="fa-solid fa-check"></i> ' + (u.status === 'pending' ? 'Approve' : 'Activate') + '</button>' :
                        '<a class="adm-btn adm-btn-ghost sm" href="' + API.url('/superadmin/admins/' + u.id + '/edit') + '">Edit</a>';
                    return '<tr>' +
                        '<td><div class="t-user">' + ava(u.name, u.photo_url) + '<div><div class="t-title">' + esc(u.name) + '</div><small class="t-sub">' + esc(u.email) + '</small></div></div></td>' +
                        '<td class="hide-sm">' + esc(((u.user_profile || {}).city) || p.city || '—') + '</td>' +
                        '<td><span class="status ' + st + '">' + esc(u.status) + '</span></td>' +
                        '<td class="hide-md">' + esc(u.created_at || '') + '</td>' +
                        '<td class="t-right">' + action + '</td></tr>';
                }).join('') : emptyRow(5, 'No admins yet – create the first advocate.', 'fa-user-tie');
            }).catch(err => {
                tb.innerHTML = emptyRow(5, err.message, 'fa-triangle-exclamation');
            });
        }

        $('dbAdmins').addEventListener('click', async e => {
            const b = e.target.closest('[data-approve]');
            if (!b) return;
            b.disabled = true;
            try {
                const r = await API.patch('/admins/' + b.dataset.approve + '/status', {
                    status: 'active'
                });
                toast(r.message);
                loadAdmins();
                loadStats();
            } catch (err) {
                toast(err.message, true);
                b.disabled = false;
            }
        });

        /* ================= 3. NEW USERS ================= */
        function loadUsers() {
            const ul = $('dbUsers');
            ul.innerHTML = Array.from({
                length: 4
            }, () => '<li><span class="db-ava grey"></span><div><span class="db-sk" style="width:60%;margin-bottom:6px"></span><span class="db-sk" style="width:40%"></span></div></li>').join('');
            return API.get('/users?per_page=5').then(r => {
                ul.innerHTML = r.data.length ? r.data.map(u =>
                    '<li>' + ava(u.name, u.photo_url, 'green') +
                    '<div><h6>' + esc(u.name) + '</h6><small>' + esc(u.email) + '</small></div>' +
                    '<span class="status ' + (u.status === 'active' ? 'green' : 'grey') + '">' + esc(u.status) + '</span></li>'
                ).join('') : '<li class="db-empty" style="display:block"><i class="fa-solid fa-users"></i>No users yet.</li>';
            }).catch(err => {
                ul.innerHTML = '<li class="db-empty" style="display:block">' + esc(err.message) + '</li>';
            });
        }

        /* ================= 4. RECENT LOGIN ACTIVITY ================= */
        function loadLogs() {
            const tb = $('dbLogs');
            tb.innerHTML = skRows(5);
            return API.get('/login-logs?per_page=7').then(r => {
                tb.innerHTML = r.data.length ? r.data.map(l => {
                    const who = l.user ?
                        '<div class="t-user">' + ava(l.user.name, l.user.photo_url) + '<div><div class="t-title">' + esc(l.user.name) + '</div><small class="t-sub">' + esc(l.user.email) + '</small></div></div>' :
                        '<div class="t-user">' + ava('?', null, 'grey') + '<div><div class="t-title">Unknown</div><small class="t-sub">' + esc(l.login_input || '—') + '</small></div></div>';
                    const role = ROLE[l.role];
                    const st = {
                        success: 'green',
                        failed: 'red',
                        blocked: 'gold'
                    } [l.status] || 'grey';
                    return '<tr><td>' + who + '</td>' +
                        '<td><span class="db-pill ' + (role ? l.role : 'none') + '">' + (role ? '<i class="fa-solid ' + role[1] + '"></i> ' + role[0] : 'Unknown') + '</span></td>' +
                        '<td><span class="status ' + st + '" title="' + esc(l.reason || '') + '">' + esc(l.status) + '</span></td>' +
                        '<td class="hide-sm">' + esc((l.browser || '—') + ' · ' + (l.device || '—')) + '</td>' +
                        '<td><small class="t-sub">' + esc(l.logged_in_ago || l.logged_in_at || '') + '</small></td></tr>';
                }).join('') : emptyRow(5, 'No login activity yet.', 'fa-clock-rotate-left');
            }).catch(err => {
                tb.innerHTML = emptyRow(5, err.message, 'fa-triangle-exclamation');
            });
        }

        function loadAll() {
            loadStats();
            loadAdmins();
            loadUsers();
            loadLogs();
        }

        /* admin.js token verify (/api/auth/me) karke 'auth:ready' bhejta hai – uske baad hi data load */
        document.addEventListener('auth:ready', e => {
            if (e.detail.role !== 'superadmin') return; // admin.js khud redirect kar dega
            loadAll();
        });

        $('dbRefresh').addEventListener('click', () => {
            loadAll();
            toast('Dashboard refreshed');
        });
    })();
</script>
@endpush