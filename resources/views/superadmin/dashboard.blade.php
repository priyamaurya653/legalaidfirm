@extends('frontlayout.app')
@section('title', 'Dashboard')
@section('require_role', 'superadmin')
@section('page_title', 'Dashboard')

@section('page_actions')
    <div class="popper-wrap">
        <button class="adm-btn adm-btn-ghost" data-popper="popRange"><i class="fa-regular fa-calendar"></i> <span id="rangeText">This Month</span> <i class="fa-solid fa-chevron-down sm"></i></button>
        <div class="popper popper-right" id="popRange">
            <ul class="pop-menu" data-range>
                <li><a href="#">Today</a></li>
                <li><a href="#">This Week</a></li>
                <li><a href="#" class="active">This Month</a></li>
                <li><a href="#">This Year</a></li>
            </ul>
        </div>
    </div>
    <button class="adm-btn adm-btn-gold" data-modal="caseModal"><i class="fa-solid fa-plus"></i> New Case</button>
@endsection

@section('content')

{{-- Welcome banner --}}
<div class="welcome glass-card">
    <div class="welcome-text">
        <span class="chip"><i class="fa-solid fa-sun"></i> Good Morning</span>
        <h2>Welcome back, <span class="gold-text" data-user="first_name">…</span> 👋</h2>
        <p>You have <b>3 hearings</b> today and <b>5 new enquiries</b> waiting for a reply.</p>
        <div class="welcome-actions">
            <a href="{{ route('superadmin.cases') }}" class="adm-btn adm-btn-gold">View Today's Hearings</a>
            <a href="{{ route('superadmin.enquiries') }}" class="adm-btn adm-btn-ghost">Reply Enquiries</a>
            <a href="{{ route('superadmin.admins') }}?status=pending" class="adm-btn adm-btn-ghost only-superadmin">
                <i class="fa-solid fa-user-clock"></i> Pending Approvals <span class="mini-badge" data-stat="pending">0</span>
            </a>
        </div>
    </div>
    <div class="welcome-art"><i class="fa-solid fa-scale-balanced"></i></div>
</div>

{{-- Stat cards --}}
<div class="stat-grid">
    @foreach([
        ['Active Cases',    '248',   'fa-solid fa-gavel',          '+12%', 'up',   'gold'],
        ['Total Clients',   '1,862', 'fa-solid fa-users',          '+8%',  'up',   'blue'],
        ['New Enquiries',   '57',    'fa-solid fa-envelope-open',  '+23%', 'up',   'green'],
        ['Revenue (₹)',     '18.4L', 'fa-solid fa-indian-rupee-sign','-3%','down', 'purple'],
    ] as $i => $s)
        <div class="stat-card glass-card" style="--i:{{ $i }}">
            <div class="sc-top">
                <span class="sc-icon {{ $s[5] }}"><i class="{{ $s[2] }}"></i></span>
                <span class="sc-trend {{ $s[4] }}"><i class="fa-solid fa-arrow-trend-{{ $s[4] }}"></i> {{ $s[3] }}</span>
            </div>
            <h3 class="sc-value">{{ $s[1] }}</h3>
            <p class="sc-label">{{ $s[0] }}</p>
            <div class="sc-bar"><i style="width: {{ [72, 64, 85, 48][$i] }}%"></i></div>
        </div>
    @endforeach
</div>

{{-- Charts --}}
<div class="grid-2-1">
    <div class="glass-card card">
        <div class="card-head">
            <div><h4>Revenue & Cases</h4><small>Last 12 months</small></div>
            <div class="seg" data-seg>
                <button class="active">Revenue</button>
                <button>Cases</button>
            </div>
        </div>
        <div class="chart-box"><canvas id="revenueChart"></canvas></div>
    </div>

    <div class="glass-card card">
        <div class="card-head">
            <div><h4>Cases by Practice</h4><small>Active matters</small></div>
            <div class="popper-wrap">
                <button class="icon-btn" data-popper="popPie"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                <div class="popper popper-right" id="popPie">
                    <ul class="pop-menu">
                        <li><a href="#"><i class="fa-solid fa-download"></i> Export PNG</a></li>
                        <li><a href="#"><i class="fa-solid fa-file-csv"></i> Export CSV</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="chart-box sm"><canvas id="practiceChart"></canvas></div>
        <ul class="legend" id="practiceLegend"></ul>
    </div>
</div>

{{-- Table + hearings --}}
<div class="grid-2-1">
    <div class="glass-card card">
        <div class="card-head">
            <div><h4>Recent Cases</h4><small>Latest 6 matters</small></div>
            <a href="{{ route('superadmin.cases') }}" class="adm-btn adm-btn-ghost sm">View All</a>
        </div>
        @include('frontlayout.cases-table', ['limit' => 6])
    </div>

    <div class="glass-card card">
        <div class="card-head">
            <div><h4>Upcoming Hearings</h4><small>Next 7 days</small></div>
        </div>
        <ul class="hearings">
            @foreach([
                ['17', 'Oct', 'State vs Rohit S.', 'Saket Court · Room 12', '10:30 AM', 'gold'],
                ['17', 'Oct', 'Verma vs Builder Ltd.', 'Delhi High Court', '02:00 PM', 'blue'],
                ['18', 'Oct', 'Meera J. – Custody', 'Family Court, Dwarka', '11:00 AM', 'green'],
                ['20', 'Oct', 'Gupta – GST Appeal', 'GST Tribunal', '12:15 PM', 'purple'],
                ['22', 'Oct', 'Kapoor Industries', 'NCLT, New Delhi', '03:30 PM', 'gold'],
            ] as $h)
                <li>
                    <div class="h-date {{ $h[5] }}"><b>{{ $h[0] }}</b><span>{{ $h[1] }}</span></div>
                    <div class="h-info"><h6>{{ $h[2] }}</h6><small><i class="fa-solid fa-location-dot"></i> {{ $h[3] }}</small></div>
                    <span class="h-time">{{ $h[4] }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</div>

{{-- Enquiries + advocates + activity --}}
<div class="grid-3">
    <div class="glass-card card">
        <div class="card-head"><div><h4>New Enquiries</h4><small>From website</small></div></div>
        <ul class="people">
            @foreach([
                ['Amit Kumar', 'Rent & Sale Agreement', '5m'],
                ['Sunita Rao', 'Family Cases', '22m'],
                ['Vikas Jain', 'Legal Notice', '1h'],
                ['Pooja Singh', 'Challan', '3h'],
            ] as $e)
                <li>
                    <span class="avatar-txt">{{ substr($e[0], 0, 1) }}</span>
                    <div><h6>{{ $e[0] }}</h6><small>{{ $e[1] }}</small></div>
                    <span class="muted-sm">{{ $e[2] }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="glass-card card">
        <div class="card-head"><div><h4>Top Advocates</h4><small>Win rate this year</small></div></div>
        <ul class="people">
            @foreach([
                ['Arjun Mehta', 'Criminal', 96, 'photo-1507003211169-0a1dd7228f2d'],
                ['Neha Kapoor', 'Corporate', 93, 'photo-1573496359142-b8d87734a5a2'],
                ['Priya Sharma', 'Family & Civil', 90, 'photo-1580489944761-15a19d654956'],
            ] as $a)
                <li class="col">
                    <div class="row">
                        <img src="https://images.unsplash.com/{{ $a[3] }}?w=80&q=80" alt="">
                        <div><h6>{{ $a[0] }}</h6><small>{{ $a[1] }}</small></div>
                        <b class="gold">{{ $a[2] }}%</b>
                    </div>
                    <div class="sc-bar"><i style="width: {{ $a[2] }}%"></i></div>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="glass-card card">
        <div class="card-head"><div><h4>Activity</h4><small>Today</small></div></div>
        <ul class="activity">
            <li><span class="act-dot gold"></span><p><b>Bail granted</b> in State vs Rohit S.</p><small>09:40 AM</small></li>
            <li><span class="act-dot green"></span><p>New client <b>Ankita Verma</b> registered</p><small>09:10 AM</small></li>
            <li><span class="act-dot blue"></span><p>Invoice <b>#INV-1042</b> paid</p><small>08:55 AM</small></li>
            <li><span class="act-dot purple"></span><p>Will registered for <b>Mr. Sharma</b></p><small>Yesterday</small></li>
        </ul>
    </div>
</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (!window.Chart) return;
    Chart.defaults.color = '#9a9aa2';
    Chart.defaults.font.family = 'Poppins';
    Chart.defaults.borderColor = 'rgba(255,255,255,.06)';

    // ---- Revenue / Cases line chart
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, 300);
    grad.addColorStop(0, 'rgba(212,175,55,.45)');
    grad.addColorStop(1, 'rgba(212,175,55,0)');

    const months  = ['Nov','Dec','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct'];
    const revenue = [9.2, 11.5, 10.1, 12.8, 14.2, 13.1, 15.6, 14.9, 16.8, 17.2, 19.0, 18.4];
    const cases   = [140, 152, 160, 171, 178, 186, 195, 204, 215, 226, 239, 248];

    const lineChart = new Chart(ctx, {
        type: 'line',
        data: { labels: months, datasets: [{ label: 'Revenue (₹ Lakh)', data: revenue, borderColor: '#d4af37', backgroundColor: grad, fill: true, tension: .4, borderWidth: 2.5, pointRadius: 0, pointHoverRadius: 6, pointBackgroundColor: '#f5d77a' }] },
        options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#17171b', borderColor: 'rgba(212,175,55,.4)', borderWidth: 1, padding: 12 } },
            scales: { x: { grid: { display: false } }, y: { grid: { color: 'rgba(255,255,255,.05)' } } } }
    });

    document.querySelectorAll('[data-seg] button').forEach((b, i) => b.addEventListener('click', () => {
        lineChart.data.datasets[0].data  = i === 0 ? revenue : cases;
        lineChart.data.datasets[0].label = i === 0 ? 'Revenue (₹ Lakh)' : 'Active Cases';
        lineChart.update();
    }));

    // ---- Practice doughnut
    const labels = ['Criminal', 'Civil', 'Corporate', 'Family', 'Tax', 'Documentation'];
    const values = [68, 54, 39, 42, 21, 24];
    const colors = ['#d4af37', '#5b8def', '#3ecf8e', '#b07cff', '#ff6b6b', '#f5a623'];
    new Chart(document.getElementById('practiceChart'), {
        type: 'doughnut',
        data: { labels, datasets: [{ data: values, backgroundColor: colors, borderColor: '#0d0d10', borderWidth: 4, hoverOffset: 8 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '72%', plugins: { legend: { display: false } } }
    });
    document.getElementById('practiceLegend').innerHTML =
        labels.map((l, i) => `<li><i style="background:${colors[i]}"></i>${l}<b>${values[i]}</b></li>`).join('');
});
</script>
@endpush