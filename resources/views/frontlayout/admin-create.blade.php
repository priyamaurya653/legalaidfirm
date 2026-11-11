@extends('frontlayout.app')
@php $editId = $id ?? null; @endphp
@section('title', $editId ? 'Edit Admin' : 'Create Admin')
@section('page_title', $editId ? 'Edit Admin' : 'Create Admin')
@section('require_role', 'superadmin')

@section('page_actions')
    <a href="{{ route('superadmin.admins') }}" class="adm-btn adm-btn-ghost"><i class="fa-solid fa-arrow-left"></i> All Admins</a>
@endsection

@section('content')

<form id="adminForm" class="adv-form" data-edit-id="{{ $editId }}" novalidate enctype="multipart/form-data">
    <div class="adv-form-grid">

        <div class="adv-form-main">
            {{-- Sign Up jaise fields --}}
            @include('frontlayout.admin-fields', ['mode' => $editId ? 'edit' : 'create'])
        </div>

        <aside class="adv-form-side">
            <div class="glass-card card side-sticky">
                <div class="side-icon"><i class="fa-solid {{ $editId ? 'fa-user-pen' : 'fa-user-plus' }}"></i></div>
                <h4>{{ $editId ? 'Update Admin' : 'New Admin' }}</h4>
                <p class="muted-sm">Same fields as the website <b class="gold">Sign Up</b>. The admin logs in with this <b class="gold">email & password</b> and lands on the Admin Dashboard.</p>

                <ul class="side-checks">
                    <li><i class="fa-solid fa-circle-check"></i> Fields marked <b class="gold">*</b> are required</li>
                    <li><i class="fa-solid fa-circle-check"></i> Photo optional, max 2MB</li>
                    <li><i class="fa-solid fa-circle-check"></i> Password: 8+ chars, Aa + number</li>
                    <li><i class="fa-solid fa-circle-check"></i> Inactive / pending admins are logged out</li>
                </ul>

                <button type="submit" class="adm-btn adm-btn-gold w-100 save-btn">
                    <i class="fa-solid fa-check"></i> <span>{{ $editId ? 'Save Changes' : 'Create Admin' }}</span>
                </button>
                <a href="{{ route('superadmin.admins') }}" class="adm-btn adm-btn-ghost w-100">Cancel</a>
            </div>
        </aside>
    </div>
</form>

@endsection

@push('scripts')
    <script src="{{ asset('js/admins.js') }}"></script>
@endpush