@extends('frontlayout.app')
@section('title', 'Login Activity')
@section('page_title', 'Login Activity')
@section('require_role', 'superadmin')

@section('page_actions')
    <button class="adm-btn adm-btn-ghost" id="logRefresh"><i class="fa-solid fa-rotate"></i> Refresh</button>
@endsection

@section('content')

{{-- Today --}}
<div class="role-stats">
    <div class="glass-card rs-card gold"><i class="fa-solid fa-crown"></i><div><b data-lstat="superadmin">–</b><small>Super Admin logins today</small></div></div>
    <div class="glass-card rs-card blue"><i class="fa-solid fa-user-tie"></i><div><b data-lstat="admin">–</b><small>Admin logins today</small></div></div>
    <div class="glass-card rs-card green"><i class="fa-solid fa-user"></i><div><b data-lstat="user">–</b><small>User logins today</small></div></div>
    <div class="glass-card rs-card red"><i class="fa-solid fa-shield-halved"></i><div><b data-lstat="failed">–</b><small>Failed / blocked today</small></div></div>
</div>

<div class="glass-card card">
    <div class="toolbar">
        <div class="tb-search sm">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Search name, email, IP…" id="logSearch">
        </div>
        <div class="log-filters">
            <div class="filter-chips" id="logRole">
                <button class="active" data-f="">All Roles</button>
                <button data-f="superadmin"><i class="fa-solid fa-crown"></i> Super Admin</button>
                <button data-f="admin"><i class="fa-solid fa-user-tie"></i> Admin</button>
                <button data-f="user"><i class="fa-solid fa-user"></i> User</button>
            </div>
            <select id="logStatus" class="adm-select">
                <option value="">All Status</option>
                <option value="success">Success</option>
                <option value="failed">Failed</option>
                <option value="blocked">Blocked</option>
            </select>
            <input type="date" id="logDate" class="adm-select" max="{{ now()->format('Y-m-d') }}">
        </div>
    </div>

    <div class="table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="hide-sm">Device</th>
                    <th class="hide-md">IP Address</th>
                    <th>Login Time</th>
                    <th class="hide-md">Logout</th>
                </tr>
            </thead>
            <tbody id="logRows"></tbody>
        </table>
    </div>
    <div class="pager" id="logPager"></div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/users.js') }}"></script>
@endpush