@extends('frontlayout.app')
@section('title', 'All Admins')
@section('page_title', 'All Admins')
@section('require_role', 'superadmin')

@section('page_actions')
    <a href="{{ route('superadmin.admins.create') }}" class="adm-btn adm-btn-gold"><i class="fa-solid fa-user-plus"></i> Create Admin</a>
@endsection

@section('content')

{{-- Stats (API) --}}
<div class="mini-stats">
    <button class="glass-card mini mini-btn" data-quick-status=""><span class="sc-icon gold"><i class="fa-solid fa-user-tie"></i></span><div><b data-stat="total">–</b><small>Total Admins</small></div></button>
    <button class="glass-card mini mini-btn" data-quick-status="active"><span class="sc-icon green"><i class="fa-solid fa-user-check"></i></span><div><b data-stat="active">–</b><small>Active</small></div></button>
    <button class="glass-card mini mini-btn" data-quick-status="pending"><span class="sc-icon blue"><i class="fa-solid fa-user-clock"></i></span><div><b data-stat="pending">–</b><small>Pending Approval</small></div></button>
    <button class="glass-card mini mini-btn" data-quick-status="inactive"><span class="sc-icon purple"><i class="fa-solid fa-user-slash"></i></span><div><b data-stat="inactive">–</b><small>Inactive</small></div></button>
</div>

<div class="glass-card card">
    <div class="toolbar">
        <div class="tb-search sm">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Search name, email, phone, enrollment…" id="adminSearch">
        </div>
        <div class="filter-chips" id="adminFilter">
            <button class="active" data-f="">All</button>
            <button data-f="active">Active</button>
            <button data-f="pending">Pending</button>
            <button data-f="inactive">Inactive</button>
        </div>
    </div>

    <div class="table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Admin</th>
                    <th class="hide-sm">City / State</th>
                    <th class="hide-sm">Phone</th>
                    <th class="hide-md">Practice Area</th>
                    <th>Clients</th>
                    <th>Status</th>
                    <th class="hide-md">Joined</th>
                    <th class="t-right">Action</th>
                </tr>
            </thead>
            <tbody id="adminRows">
                {{-- JS se API data aata hai --}}
            </tbody>
        </table>
    </div>

    <div class="pager" id="adminPager"></div>
</div>

@endsection

@push('modals')
{{-- View admin details --}}
<div class="adm-modal" id="viewAdminModal" aria-hidden="true">
    <div class="adm-modal-box glass-card wide">
        <div class="modal-head">
            <h4><i class="fa-solid fa-id-badge"></i> Advocate Profile</h4>
            <button class="icon-btn" data-close-modal aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="viewAdminBody" class="view-body"></div>
    </div>
</div>
@endpush

@push('scripts')
    <script src="{{ asset('js/admins.js') }}"></script>
@endpush