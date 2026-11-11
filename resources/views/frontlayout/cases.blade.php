@extends('frontlayout.app')
@section('title', 'Cases')
@section('page_title', 'Cases')

@section('page_actions')
    <button class="adm-btn adm-btn-ghost"><i class="fa-solid fa-file-export"></i> <span class="hide-xs">Export</span></button>
    <button class="adm-btn adm-btn-gold" data-modal="caseModal"><i class="fa-solid fa-plus"></i> New Case</button>
@endsection

@section('content')

<div class="mini-stats">
    <div class="glass-card mini"><span class="sc-icon gold"><i class="fa-solid fa-gavel"></i></span><div><b>248</b><small>Total</small></div></div>
    <div class="glass-card mini"><span class="sc-icon green"><i class="fa-solid fa-circle-play"></i></span><div><b>171</b><small>Active</small></div></div>
    <div class="glass-card mini"><span class="sc-icon blue"><i class="fa-solid fa-hourglass-half"></i></span><div><b>42</b><small>Pending</small></div></div>
    <div class="glass-card mini"><span class="sc-icon purple"><i class="fa-solid fa-circle-check"></i></span><div><b>35</b><small>Closed</small></div></div>
</div>

<div class="glass-card card">
    <div class="toolbar">
        <div class="tb-search sm">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Search cases…" data-table-search>
        </div>
        <div class="filter-chips" data-status-filter>
            <button class="active" data-f="all">All</button>
            <button data-f="Active">Active</button>
            <button data-f="Hearing">Hearing</button>
            <button data-f="Pending">Pending</button>
            <button data-f="Closed">Closed</button>
        </div>
    </div>

    @include('frontlayout.cases-table')

    <div class="pager">
        <small>Showing 1–10 of 248</small>
        <div class="pages">
            <button class="icon-btn"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="icon-btn active">1</button>
            <button class="icon-btn">2</button>
            <button class="icon-btn">3</button>
            <button class="icon-btn"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
    </div>
</div>

@endsection