<header class="topbar-adm" id="admTopbar">
    <div class="tb-left">
        {{-- mobile: open sidebar | desktop: collapse sidebar --}}
        <button class="tb-icon-btn" id="sbToggle" aria-label="Toggle sidebar">
            <span class="hamb"><span></span><span></span><span></span></span>
        </button>

        <div class="tb-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Search cases, clients, enquiries…">
            <kbd>Ctrl K</kbd>
        </div>
    </div>

    <div class="tb-right">
        <button class="tb-icon-btn tb-search-mobile" data-popper="popSearch" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>

        {{-- Quick add --}}
        <div class="popper-wrap hide-user">
            <button class="tb-icon-btn" data-popper="popAdd" aria-label="Quick add"><i class="fa-solid fa-plus"></i></button>
            <div class="popper" id="popAdd">
                <div class="pop-head"><h6>Quick Add</h6></div>
                <div class="pop-grid">
                    <a href="#" data-modal="caseModal"><i class="fa-solid fa-gavel"></i> New Case</a>
                    <a href="{{ route('superadmin.clients') }}"><i class="fa-solid fa-user-plus"></i> Client</a>
                    <a href="{{ route('superadmin.admins.create') }}" class="only-superadmin"><i class="fa-solid fa-user-plus"></i> Admin</a>
                    <a href="{{ route('superadmin.enquiries') }}"><i class="fa-solid fa-envelope"></i> Enquiry</a>
                </div>
            </div>
        </div>

        {{-- Notifications --}}
        <div class="popper-wrap">
            <button class="tb-icon-btn" data-popper="popNotif" aria-label="Notifications">
                <i class="fa-regular fa-bell"></i><span class="dot-badge">4</span>
            </button>
            <div class="popper popper-lg" id="popNotif">
                <div class="pop-head">
                    <h6>Notifications</h6>
                    <a href="#" class="pop-link">Mark all read</a>
                </div>
                <ul class="pop-list">
                    <li class="unread"><span class="pl-icon gold"><i class="fa-solid fa-gavel"></i></span><div><p>Hearing tomorrow – <b>State vs Rohit S.</b></p><small>10 min ago</small></div></li>
                    <li class="unread"><span class="pl-icon green"><i class="fa-solid fa-envelope"></i></span><div><p>New enquiry for <b>Rent Agreement</b></p><small>32 min ago</small></div></li>
                    <li class="unread"><span class="pl-icon blue"><i class="fa-solid fa-file-arrow-up"></i></span><div><p>Neha Kapoor uploaded <b>3 documents</b></p><small>1 hour ago</small></div></li>
                    <li><span class="pl-icon red"><i class="fa-solid fa-triangle-exclamation"></i></span><div><p>Filing deadline for <b>GST Appeal</b> in 2 days</p><small>Yesterday</small></div></li>
                </ul>
                <a href="#" class="pop-foot">View all notifications</a>
            </div>
        </div>

        {{-- Messages --}}
        <div class="popper-wrap tb-hide-xs hide-user">
            <button class="tb-icon-btn" data-popper="popMsg" aria-label="Messages">
                <i class="fa-regular fa-comment-dots"></i><span class="dot-badge green">2</span>
            </button>
            <div class="popper popper-lg" id="popMsg">
                <div class="pop-head"><h6>Messages</h6><a href="#" class="pop-link">New</a></div>
                <ul class="pop-list">
                    <li class="unread"><img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=80&q=80" alt=""><div><p><b>Neha Kapoor</b></p><small>Draft of the shareholder agreement is ready…</small></div></li>
                    <li class="unread"><img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&q=80" alt=""><div><p><b>Arjun Mehta</b></p><small>Bail granted in the Saket matter 🎉</small></div></li>
                    <li><img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?w=80&q=80" alt=""><div><p><b>Priya Sharma</b></p><small>Client meeting moved to 4 PM.</small></div></li>
                </ul>
                <a href="#" class="pop-foot">Open inbox</a>
            </div>
        </div>

        {{-- Profile --}}
        <div class="popper-wrap">
            <button class="tb-profile" data-popper="popProfile">
                <span class="user-avatar sm" data-user-avatar><i class="fa-solid fa-user"></i></span>
                <span class="tb-profile-info"><b data-user="name">…</b><small data-user="role_label">—</small></span>
                <i class="fa-solid fa-chevron-down"></i>
            </button>
            <div class="popper" id="popProfile">
                <div class="pop-user">
                    <span class="user-avatar" data-user-avatar><i class="fa-solid fa-user"></i></span>
                    <div><h6 data-user="name">…</h6><small data-user="email">—</small></div>
                </div>
                <ul class="pop-menu">
                    <li><a href="{{ route('superadmin.settings') }}?tab=profile"><i class="fa-regular fa-user"></i> My Profile</a></li>
                    <li><a href="{{ route('superadmin.settings') }}"><i class="fa-solid fa-gear"></i> Settings</a></li>
                    <li><a href="{{ route('home') }}" target="_blank"><i class="fa-solid fa-globe"></i> View Website</a></li>
                    <li class="sep"></li>
                    <li><a href="#" class="danger" data-logout><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- mobile search popper --}}
    <div class="popper popper-search" id="popSearch">
        <div class="tb-search full">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Search…">
        </div>
    </div>
</header>

{{-- backdrop used by poppers on mobile (bottom-sheet) --}}
<div class="pop-backdrop" id="popBackdrop"></div>