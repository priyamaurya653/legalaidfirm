@extends('frontlayout.app')
@section('title', 'Users')
@section('page_title', 'Users')
@section('require_role', 'superadmin')

@section('content')

<div class="mini-stats">
    <div class="glass-card mini"><span class="sc-icon green"><i class="fa-solid fa-users"></i></span><div><b data-ustat="total">–</b><small>Total Users</small></div></div>
    <div class="glass-card mini"><span class="sc-icon blue"><i class="fa-solid fa-user-check"></i></span><div><b data-ustat="active">–</b><small>Active</small></div></div>
    <div class="glass-card mini"><span class="sc-icon purple"><i class="fa-solid fa-user-slash"></i></span><div><b data-ustat="inactive">–</b><small>Inactive</small></div></div>
    <div class="glass-card mini"><span class="sc-icon gold"><i class="fa-solid fa-right-to-bracket"></i></span><div><b data-ustat="logins">–</b><small>User Logins Today</small></div></div>
</div>

<div class="glass-card card">
    <div class="toolbar">
        <div class="tb-search sm">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Search name, email, phone…" id="userSearch">
        </div>
        <div class="filter-chips" id="userFilter">
            <button class="active" data-f="">All</button>
            <button data-f="active">Active</button>
            <button data-f="inactive">Inactive</button>
        </div>
    </div>

    <div class="table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th class="hide-sm">Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="hide-md">Logins</th>
                    <th class="hide-md">Last Login</th>
                    <th class="hide-md">Joined</th>
                    <th class="t-right">Action</th>
                </tr>
            </thead>
            <tbody id="userRows"></tbody>
        </table>
    </div>
    <div class="pager" id="userPager"></div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/users.js') }}"></script>
@endpush