@extends('frontlayout.app')
@section('title', 'Users')
@section('page_title', 'All Users')
@section('require_role', 'superadmin')

@push('styles')
<style>
/* ===== ALL USERS (isi file me) ===== */
.us-stats { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 20px; }
.us-stat { display: flex; align-items: center; gap: 14px; padding: 16px 18px; cursor: pointer; text-align: left; font: inherit; color: inherit; transition: .35s; position: relative; overflow: hidden; }
.us-stat:hover, .us-stat.on { transform: translateY(-4px); border-color: rgba(212,175,55,.45); }
.us-stat.on::after { content: ''; position: absolute; left: 0; bottom: 0; width: 100%; height: 3px; background: var(--gold-grad); }
.us-stat b { display: block; font-size: 22px; color: #fff; line-height: 1.1; }
.us-stat small { color: var(--muted); font-size: 12.5px; }
.us-toolbar { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.us-toolbar .right { display: flex; flex-wrap: wrap; gap: 10px; }
.role-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 11px; border-radius: 20px; font-size: 12px; font-weight: 500; white-space: nowrap; }
.role-pill.gold { color: var(--gold-light); background: rgba(212,175,55,.12); }
.role-pill.blue { color: #8fb4ff; background: rgba(91,141,239,.12); }
.role-pill.green { color: #5fe0a5; background: rgba(62,207,142,.12); }
.us-select { height: 40px; max-width: 220px; padding: 0 34px 0 14px; border-radius: 12px; color: #ddd; font: inherit; font-size: 13px; cursor: pointer; appearance: none;
    background: rgba(255,255,255,.04) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath fill='%23d4af37' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E") no-repeat right 14px center; border: 1px solid var(--border); }
.us-select option { background: #111; }
.us-by { display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; color: #8fb4ff; }
.us-by.none { color: var(--muted); }
.us-you { font-size: 10.5px; padding: 2px 7px; border-radius: 6px; margin-left: 6px; color: #111; background: var(--gold-grad); vertical-align: middle; }
.adm-table tr.removing { opacity: 0; transform: translateX(30px); transition: .35s; }

/* view */
.uv-head { display: flex; align-items: center; gap: 16px; margin-bottom: 18px; }
.uv-av { width: 66px; height: 66px; flex-shrink: 0; border-radius: 18px; overflow: hidden; display: grid; place-items: center; font-size: 26px; font-weight: 700; color: #111; background: var(--gold-grad); }
.uv-av img { width: 100%; height: 100%; object-fit: cover; }
.uv-head h4 { color: #fff; font-size: 20px; margin: 0 0 6px; overflow-wrap: anywhere; }
.uv-kv { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px 18px; margin-bottom: 18px; }
.uv-kv small { display: block; color: var(--muted); font-size: 12px; margin-bottom: 2px; }
.uv-kv b { color: #eee; font-size: 13.5px; font-weight: 500; overflow-wrap: anywhere; }
.uv-kv .full { grid-column: 1 / -1; }
.uv-logs { list-style: none; padding: 0; margin: 0 0 18px; max-height: 240px; overflow-y: auto; }
.uv-logs li { display: flex; align-items: center; gap: 10px; padding: 9px 0; border-bottom: 1px solid rgba(255,255,255,.05); font-size: 13px; color: #ccc; }
.uv-logs li i { color: var(--gold); width: 18px; }
.uv-logs li.bad i { color: #ff8c8c; }
.uv-logs li span { flex: 1; min-width: 0; }
.uv-logs li small { color: var(--muted); }
.uv-sub { font-size: 12px; letter-spacing: 1.5px; text-transform: uppercase; color: var(--gold-light); margin: 4px 0 10px; }

/* edit */
.req { color: #ff7a7a; font-weight: 600; }
.ue-photo { display: flex; align-items: center; gap: 14px; margin-bottom: 18px; }
.ue-photo .uv-av { width: 60px; height: 60px; }
.ue-note { font-size: 12px; color: var(--muted); margin: -6px 0 16px; }
#userForm.is-loading { opacity: .5; pointer-events: none; }

.us-del { text-align: center; padding: 10px 6px 4px; }
.us-del .ring { width: 76px; height: 76px; margin: 0 auto 16px; border-radius: 50%; display: grid; place-items: center; font-size: 30px; color: #ff6b6b; background: rgba(255,107,107,.1); }
.us-del h4 { color: #fff; font-size: 20px; margin-bottom: 8px; }
.us-del p { color: var(--muted); font-size: 14px; margin-bottom: 22px; }
.us-del p b { color: var(--gold-light); }
.us-del .btns { display: flex; gap: 10px; justify-content: center; }
.adm-btn-danger { color: #fff; background: linear-gradient(135deg, #ff7a7a, #d63c3c); border: 0; }

@media (max-width: 1199px) { .us-stats { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 767px) { .us-stats { grid-template-columns: repeat(2, 1fr); } .us-toolbar, .us-toolbar .right { flex-direction: column; align-items: stretch; } .us-toolbar .tb-search { width: 100%; } .uv-kv { grid-template-columns: 1fr 1fr; } }
@media (max-width: 575px) { .uv-kv { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

<div class="us-stats">
    <button class="glass-card us-stat on" data-q-role="" data-q-status=""><span class="sc-icon gold"><i class="fa-solid fa-users"></i></span><div><b data-us="total">–</b><small>All Users</small></div></button>
    <button class="glass-card us-stat" data-q-role="superadmin"><span class="sc-icon purple"><i class="fa-solid fa-crown"></i></span><div><b data-us="superadmin">–</b><small>Super Admins</small></div></button>
    <button class="glass-card us-stat" data-q-role="admin"><span class="sc-icon blue"><i class="fa-solid fa-user-tie"></i></span><div><b data-us="admin">–</b><small>Admins</small></div></button>
    <button class="glass-card us-stat" data-q-role="user"><span class="sc-icon green"><i class="fa-solid fa-user"></i></span><div><b data-us="user">–</b><small>Website Users</small></div></button>
    <button class="glass-card us-stat" data-q-status="inactive"><span class="sc-icon red" style="background:rgba(255,107,107,.12);color:#ff8c8c"><i class="fa-solid fa-user-slash"></i></span><div><b data-us="inactive">–</b><small>Inactive / Pending</small></div></button>
</div>

<div class="glass-card card">
    <div class="us-toolbar">
        <div class="tb-search sm">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Search name, email, phone, city…" id="usSearch" autocomplete="off">
        </div>
        <div class="right">
            <div class="filter-chips" id="usRole">
                <button class="active" data-f="">All Roles</button>
                <button data-f="superadmin">Super Admin</button>
                <button data-f="admin">Admin</button>
                <button data-f="user">User</button>
            </div>
            <select class="us-select" id="usCreator" aria-label="Created by admin"><option value="">All (any admin)</option><option value="0">Not created by an admin</option></select>
            <div class="filter-chips" id="usStatus">
                <button class="active" data-f="">Any Status</button>
                <button data-f="active">Active</button>
                <button data-f="pending">Pending</button>
                <button data-f="inactive">Inactive</button>
            </div>
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
                    <th class="hide-sm">Created By</th>
                    <th class="hide-md">Logins</th>
                    <th class="hide-md">Last Login</th>
                    <th class="hide-md">Joined</th>
                    <th class="t-right">Action</th>
                </tr>
            </thead>
            <tbody id="usRows"></tbody>
        </table>
    </div>
    <div class="pager" id="usPager"></div>
</div>

@endsection

@push('modals')
{{-- VIEW --}}
<div class="adm-modal" id="usViewModal" aria-hidden="true">
    <div class="adm-modal-box glass-card wide">
        <div class="modal-head">
            <h4><i class="fa-regular fa-id-badge"></i> User Profile</h4>
            <button class="icon-btn" data-close-modal aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="usViewBody"></div>
    </div>
</div>

{{-- EDIT --}}
<div class="adm-modal" id="usEditModal" aria-hidden="true">
    <div class="adm-modal-box glass-card wide">
        <div class="modal-head">
            <h4><i class="fa-regular fa-pen-to-square"></i> Edit User</h4>
            <button class="icon-btn" data-close-modal aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form class="adm-form" id="userForm" novalidate>
            <div class="ue-photo">
                <span class="uv-av" id="uePhotoBox">?</span>
                <div>
                    <label class="adm-btn adm-btn-ghost sm" style="cursor:pointer"><i class="fa-solid fa-camera"></i> Change Photo<input type="file" name="photo" accept="image/png,image/jpeg,image/webp" hidden></label>
                    <small class="f-hint" style="display:block">JPG / PNG / WEBP · max 2MB</small>
                </div>
            </div>
            <div class="f-grid">
                <div class="f-group" data-field="name"><label>Full Name <b class="req">*</b></label><input type="text" name="name" required minlength="3" maxlength="100"></div>
                <div class="f-group" data-field="email"><label>Email <b class="req">*</b></label><input type="email" name="email" required maxlength="150"></div>
                <div class="f-group" data-field="phone"><label>Phone</label><input type="tel" name="phone" inputmode="numeric" maxlength="10"></div>
                <div class="f-group" data-field="role"><label>Role <b class="req">*</b></label>
                    <select name="role"><option value="superadmin">Super Admin</option><option value="admin">Admin (Advocate)</option><option value="user">User</option></select></div>
                <div class="f-group" data-field="status"><label>Status <b class="req">*</b></label>
                    <select name="status"><option value="active">Active</option><option value="pending">Pending</option><option value="inactive">Inactive</option></select></div>
                <div class="f-group" data-field="created_by" id="ueCreatorWrap"><label>Belongs to Admin <small class="f-hint">(only for role User)</small></label>
                    <select name="created_by"><option value="">— None —</option></select></div>
                <div class="f-group" data-field="gender"><label>Gender</label><select name="gender"><option value="">Select</option><option>Male</option><option>Female</option><option>Other</option></select></div>
                <div class="f-group" data-field="dob"><label>Date of Birth</label><input type="date" name="dob" max="{{ now()->subDay()->format('Y-m-d') }}"></div>
                <div class="f-group" data-field="city"><label>City</label><input type="text" name="city" maxlength="80"></div>
                <div class="f-group" data-field="state"><label>State</label><select name="state"><option value="">Select state</option>@foreach(config('advocate.states', []) as $s)<option>{{ $s }}</option>@endforeach</select></div>
                <div class="f-group" data-field="pincode"><label>Pincode</label><input type="text" name="pincode" inputmode="numeric" maxlength="6"></div>
                <div class="f-group full" data-field="address"><label>Address</label><textarea name="address" rows="2" maxlength="500"></textarea></div>
                <div class="f-group" data-field="password"><label>New Password</label><input type="password" name="password" autocomplete="new-password" placeholder="Leave blank to keep current"></div>
                <div class="f-group" data-field="password_confirmation"><label>Confirm Password</label><input type="password" name="password_confirmation" autocomplete="new-password"></div>
            </div>
            <p class="ue-note"><i class="fa-solid fa-circle-info"></i> Changing the password or setting status to Inactive / Pending logs the user out from all devices.</p>
            <div class="modal-foot">
                <button type="button" class="adm-btn adm-btn-ghost" data-close-modal>Cancel</button>
                <button type="submit" class="adm-btn adm-btn-gold" id="ueSave"><i class="fa-solid fa-check"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- DELETE --}}
<div class="adm-modal" id="usDeleteModal" aria-hidden="true">
    <div class="adm-modal-box glass-card">
        <div class="us-del">
            <div class="ring"><i class="fa-regular fa-trash-can"></i></div>
            <h4>Delete User?</h4>
            <p>"<b id="usDelName"></b>" will be permanently deleted and logged out. Login history is kept.</p>
            <div class="btns">
                <button class="adm-btn adm-btn-ghost" data-close-modal>Cancel</button>
                <button class="adm-btn adm-btn-danger" id="usDelConfirm"><i class="fa-regular fa-trash-can"></i> Yes, Delete</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
/* ===== ALL USERS – API (Bearer token) ===== */
document.addEventListener('auth:ready', function (ev) {
    if (!window.API || ev.detail.role !== 'superadmin') return;

    const meId  = ev.detail.id;
    const esc   = API.esc;
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const $     = id => document.getElementById(id);
    const body  = $('usRows'), pager = $('usPager'), form = $('userForm');
    const el    = n => form.elements.namedItem(n);
    const LOGS  = @json(route('superadmin.login-activity'));
    const ADMIN_EDIT = @json(url('/superadmin/admins')) + '/';
    const SCLS  = { active: 'green', pending: 'gold', inactive: 'grey' };
    const cap   = s => s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
    const state = { page: 1, search: '', role: '', status: '', created_by: new URLSearchParams(location.search).get('admin') || '' };
    let adminsLoaded = false;
    const cache = {};
    let editId = null, delId = null;
    const debounce = (fn, ms = 350) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

    const roleBadge = r => { const m = API.roleMeta(r); return `<span class="role-pill ${m.cls}"><i class="fa-solid ${m.icon}"></i> ${esc(m.label)}</span>`; };
    const avatar = u => u.photo_url ? `<img class="t-avatar" src="${esc(u.photo_url)}" alt="">` : `<span class="avatar-txt">${esc((u.name || '?').replace(/^adv\.?\s+/i, '').charAt(0).toUpperCase())}</span>`;
    const avBig  = u => u.photo_url ? `<img src="${esc(u.photo_url)}" alt="">` : esc((u.name || '?').replace(/^adv\.?\s+/i, '').charAt(0).toUpperCase());

    /* ---------- list ---------- */
    const row = u => {
        const pid = 'usPop' + u.id, self = u.id === meId;
        return `<tr data-id="${u.id}">
            <td><div class="t-user">${avatar(u)}<div><div class="t-title">${esc(u.name)}${self ? '<span class="us-you">YOU</span>' : ''}</div><small class="t-sub">${esc(u.email)}</small></div></div></td>
            <td class="hide-sm">${u.phone ? '+91 ' + esc(u.phone) : '—'}</td>
            <td>${roleBadge(u.role)}</td>
            <td><span class="status ${SCLS[u.status] || 'grey'}">${esc(cap(u.status))}</span></td>
            <td class="hide-sm">${u.role === 'admin'
                ? `<a href="#" class="us-by" data-us-admin="${u.id}" title="Show users of this admin"><i class="fa-solid fa-users"></i> ${u.created_users_count ?? 0} users</a>`
                : (u.creator ? `<span class="us-by"><i class="fa-solid fa-user-tie"></i> ${esc(u.creator.name)}</span>` : '<span class="us-by none">—</span>')}</td>
            <td class="hide-md">${esc(u.logins_count ?? 0)}</td>
            <td class="hide-md">${esc(u.last_login_at || 'Never')}</td>
            <td class="hide-md">${esc(u.created_at || '')}</td>
            <td class="t-right">
                <div class="popper-wrap">
                    <button class="icon-btn" data-popper="${pid}" aria-label="Actions"><i class="fa-solid fa-ellipsis"></i></button>
                    <div class="popper popper-right" id="${pid}" data-dyn>
                        <ul class="pop-menu">
                            <li><a href="#" data-us-act="view" data-id="${u.id}"><i class="fa-regular fa-eye"></i> View</a></li>
                            <li><a href="#" data-us-act="edit" data-id="${u.id}"><i class="fa-regular fa-pen-to-square"></i> Edit</a></li>
                            ${u.role === 'admin' ? `<li><a href="${ADMIN_EDIT}${u.id}/edit"><i class="fa-solid fa-user-tie"></i> Full Admin Profile</a></li>` : ''}
                            <li><a href="${LOGS}?search=${encodeURIComponent(u.email)}"><i class="fa-solid fa-clock-rotate-left"></i> Login History</a></li>
                            ${self ? '' : (u.status === 'active'
                                ? `<li><a href="#" data-us-act="status" data-status="inactive" data-id="${u.id}"><i class="fa-solid fa-ban"></i> Deactivate</a></li>`
                                : `<li><a href="#" data-us-act="status" data-status="active" data-id="${u.id}"><i class="fa-solid fa-circle-check"></i> Activate</a></li>`)}
                            ${self ? '' : `<li class="sep"></li><li><a href="#" class="danger" data-us-act="delete" data-id="${u.id}"><i class="fa-regular fa-trash-can"></i> Delete</a></li>`}
                        </ul>
                    </div>
                </div>
            </td>
        </tr>`;
    };
    const empty = (msg, err) => { body.innerHTML = `<tr><td colspan="9"><div class="t-empty ${err ? 'error' : ''}"><i class="fa-solid ${err ? 'fa-triangle-exclamation' : 'fa-users'}"></i><p>${esc(msg)}</p></div></td></tr>`; };
    const renderPager = meta => {
        if (!meta || !meta.total) { pager.innerHTML = ''; return; }
        const { current_page: cur, last_page: last, from, to, total } = meta;
        const pages = [];
        for (let i = Math.max(1, cur - 2); i <= Math.min(last, cur + 2); i++) pages.push(i);
        pager.innerHTML = `<small>Showing ${from}–${to} of ${total}</small><div class="pages">
            <button class="icon-btn" data-page="${cur - 1}" ${cur <= 1 ? 'disabled' : ''} aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
            ${pages.map(n => `<button class="icon-btn ${n === cur ? 'active' : ''}" data-page="${n}">${n}</button>`).join('')}
            <button class="icon-btn" data-page="${cur + 1}" ${cur >= last ? 'disabled' : ''} aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button></div>`;
    };

    async function load() {
        document.querySelectorAll('.popper[data-dyn]').forEach(p => p.remove());
        body.innerHTML = Array.from({ length: 6 }, () => `<tr class="sk-row">${'<td><span class="sk sk-line w80"></span></td>'.repeat(9)}</tr>`).join('');
        const qs = new URLSearchParams({ page: state.page });
        ['search', 'role', 'status', 'created_by'].forEach(k => { if (state[k] !== '') qs.set(k, state[k]); });
        try {
            const res = await API.get('/users?' + qs);
            res.data.forEach(u => { cache[u.id] = u; });
            if (!adminsLoaded) {
                const opts = (res.admins || []).map(a => `<option value="${a.id}">${esc(a.name)}</option>`).join('');
                $('usCreator').insertAdjacentHTML('beforeend', opts);
                el('created_by').insertAdjacentHTML('beforeend', opts);
                $('usCreator').value = state.created_by; adminsLoaded = true;
            }
            Object.entries(res.stats || {}).forEach(([k, v]) => document.querySelectorAll(`[data-us="${k}"]`).forEach(x => { x.textContent = v; }));
            res.data.length ? (body.innerHTML = res.data.map(row).join('')) : empty('No users match your filters.');
            renderPager(res.meta);
        } catch (err) { empty(err.message, true); pager.innerHTML = ''; }
    }

    const syncFilters = () => {
        $('usRole').querySelectorAll('button').forEach(b => b.classList.toggle('active', b.dataset.f === state.role));
        $('usStatus').querySelectorAll('button').forEach(b => b.classList.toggle('active', b.dataset.f === state.status));
        document.querySelectorAll('.us-stat').forEach(c => c.classList.toggle('on',
            (c.dataset.qRole ?? '') === state.role && (c.dataset.qStatus ?? '') === state.status && !(c.dataset.qStatus === 'inactive' && state.status !== 'inactive')));
    };
    $('usSearch').addEventListener('input', debounce(e => { state.search = e.target.value.trim(); state.page = 1; load(); }));
    $('usRole').addEventListener('click', e => { const b = e.target.closest('button'); if (!b) return; state.role = b.dataset.f; state.page = 1; syncFilters(); load(); });
    $('usStatus').addEventListener('click', e => { const b = e.target.closest('button'); if (!b) return; state.status = b.dataset.f; state.page = 1; syncFilters(); load(); });
    document.querySelectorAll('.us-stat').forEach(c => c.addEventListener('click', () => {
        state.role = c.dataset.qRole || ''; state.status = c.dataset.qStatus || ''; state.page = 1; syncFilters(); load();
    }));
    pager.addEventListener('click', e => { const b = e.target.closest('[data-page]'); if (!b || b.disabled) return; state.page = +b.dataset.page; load(); });
    $('usCreator').addEventListener('change', e => { state.created_by = e.target.value; state.page = 1; load(); });
    // admin row -> "X users" click = us admin ke users
    document.addEventListener('click', e => {
        const a = e.target.closest('[data-us-admin]'); if (!a) return;
        e.preventDefault();
        state.created_by = a.dataset.usAdmin; state.role = 'user'; state.status = ''; state.page = 1;
        $('usCreator').value = state.created_by; syncFilters(); load();
    });

    /* ---------- view ---------- */
    const v = x => x ? esc(x) : '<span style="color:var(--muted)">—</span>';
    async function openView(id) {
        const vb = $('usViewBody');
        vb.innerHTML = '<div class="t-empty"><i class="fa-solid fa-spinner fa-spin"></i><p>Loading…</p></div>';
        window.admOpenModal('usViewModal');
        try {
            const r = await API.get('/users/' + id);
            const u = r.data, p = u.user_profile || {}, a = u.profile || {};
            vb.innerHTML = `
                <div class="uv-head"><span class="uv-av">${avBig(u)}</span>
                    <div><h4>${esc(u.name)}${u.id === meId ? '<span class="us-you">YOU</span>' : ''}</h4>${roleBadge(u.role)} <span class="status ${SCLS[u.status] || 'grey'}">${esc(cap(u.status))}</span></div></div>
                <div class="uv-kv">
                    <div><small>Email</small><b><a class="gold" href="mailto:${esc(u.email)}">${esc(u.email)}</a></b></div>
                    <div><small>Phone</small><b>${u.phone ? `<a class="gold" href="tel:+91${esc(u.phone)}">+91 ${esc(u.phone)}</a>` : v()}</b></div>
                    <div><small>Joined</small><b>${v(u.created_at)}</b></div>
                    ${u.role === 'user' ? `<div><small>Created By (Admin)</small><b>${u.creator ? esc(u.creator.name) + ' · ' + esc(u.creator.email) : 'Self signup / Superadmin'}</b></div>` : ''}
                    ${u.role === 'admin' ? `<div><small>Users Created</small><b>${esc(u.created_users_count ?? 0)}</b></div>` : ''}
                    <div><small>Gender</small><b>${v(p.gender || a.gender)}</b></div>
                    <div><small>Date of Birth</small><b>${v(p.dob || a.dob)}</b></div>
                    <div><small>Occupation</small><b>${v(p.occupation)}</b></div>
                    <div><small>City</small><b>${v(p.city || a.city)}</b></div>
                    <div><small>State</small><b>${v(p.state || a.state)}</b></div>
                    <div><small>Pincode</small><b>${v(p.pincode || a.pincode)}</b></div>
                    <div class="full"><small>Address</small><b>${v(p.address || a.office_address)}</b></div>
                    ${u.role === 'admin' ? `<div><small>Practice Area</small><b>${v(p.interested_in)}</b></div>` : ''}
                    <div><small>Total Logins</small><b>${esc(u.logins_count ?? 0)}</b></div>
                    <div><small>Last Login</small><b>${v(u.last_login_at)}</b></div>
                </div>
                <p class="uv-sub">Recent login activity</p>
                <ul class="uv-logs">${(r.logins?.data || r.logins || []).map(l => `<li class="${l.status === 'success' ? '' : 'bad'}">
                    <i class="fa-solid ${l.status === 'success' ? 'fa-right-to-bracket' : 'fa-triangle-exclamation'}"></i>
                    <span>${esc(cap(l.status))}${l.reason ? ' – ' + esc(l.reason) : ''} · ${esc([l.browser, l.device].filter(Boolean).join(' / ') || 'Unknown')} · ${esc(l.ip_address || '')}</span>
                    <small>${esc(l.logged_in_ago || '')}</small></li>`).join('') || '<li><span>No logins yet.</span></li>'}</ul>
                <div class="modal-foot"><button class="adm-btn adm-btn-gold" data-us-act="edit" data-id="${u.id}"><i class="fa-regular fa-pen-to-square"></i> Edit</button></div>`;
        } catch (err) { vb.innerHTML = `<div class="t-empty error"><i class="fa-solid fa-triangle-exclamation"></i><p>${esc(err.message)}</p></div>`; }
    }

    /* ---------- edit ---------- */
    el('phone').addEventListener('input', e => { e.target.value = e.target.value.replace(/\D/g, '').slice(0, 10); });
    el('pincode').addEventListener('input', e => { e.target.value = e.target.value.replace(/\D/g, '').slice(0, 6); });
    form.addEventListener('input', e => { const g = e.target.closest('.has-error'); if (g) { g.classList.remove('has-error'); g.querySelector('.f-error')?.remove(); } });
    el('photo').addEventListener('change', e => {
        const f = e.target.files[0]; if (!f) return;
        if (f.size > 2 * 1048576) { toast('Photo must be smaller than 2MB.', true); e.target.value = ''; return; }
        $('uePhotoBox').innerHTML = `<img src="${URL.createObjectURL(f)}" alt="">`;
    });

    const toggleCreator = () => {
        const isUser = el('role').value === 'user';
        $('ueCreatorWrap').style.opacity = isUser ? 1 : .45;
        el('created_by').disabled = !isUser;
        if (!isUser) el('created_by').value = '';
    };
    el('role').addEventListener('change', toggleCreator);

    async function openEdit(id) {
        document.querySelector('#usViewModal.show [data-close-modal]')?.click();
        form.reset(); API.clearErrors(form); editId = id;
        form.classList.add('is-loading');
        setTimeout(() => window.admOpenModal('usEditModal'), 120);
        try {
            const r = await API.get('/users/' + id);
            const u = r.data, p = u.user_profile || {};
            ['name', 'email', 'phone', 'role', 'status'].forEach(k => { el(k).value = u[k] || ''; });
            el('created_by').value = u.created_by_id || '';
            toggleCreator();
            ['gender', 'dob', 'city', 'state', 'pincode', 'address'].forEach(k => {
                const x = el(k), val = p[k] || '';
                if (x.tagName === 'SELECT' && val && ![...x.options].some(o => o.value === val)) x.insertAdjacentHTML('beforeend', `<option>${esc(val)}</option>`);
                x.value = val;
            });
            $('uePhotoBox').innerHTML = avBig(u);
            const self = u.id === meId;
            el('role').disabled = self; el('status').disabled = self;
        } catch (err) { toast(err.message, true); document.querySelector('#usEditModal [data-close-modal]').click(); }
        finally { form.classList.remove('is-loading'); }
    }

    form.addEventListener('submit', async e => {
        e.preventDefault();
        API.clearErrors(form);
        const errs = {};
        if (el('name').value.trim().length < 3) errs.name = ['Please enter the full name.'];
        if (!el('email').value || !el('email').checkValidity()) errs.email = ['Please enter a valid email.'];
        if (el('phone').value && el('phone').value.length !== 10) errs.phone = ['Phone must be 10 digits.'];
        if (el('password').value && !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(el('password').value)) errs.password = ['8+ characters with uppercase, lowercase and a number.'];
        if (el('password').value !== el('password_confirmation').value) errs.password_confirmation = ['Passwords do not match.'];
        if (Object.keys(errs).length) { API.showErrors(form, errs)?.scrollIntoView({ behavior: 'smooth', block: 'center' }); return; }

        // disabled select FormData me nahi aata – isliye pehle enable karke lo
        const dis = [el('role'), el('status')].filter(x => x.disabled);
        dis.forEach(x => { x.disabled = false; });
        const fd = API.formData(form);
        dis.forEach(x => { x.disabled = true; });
        if (!fd.get('password')) { fd.delete('password'); fd.delete('password_confirmation'); }
        if (!fd.has('created_by')) fd.set('created_by', '');

        const btn = $('ueSave'); btn.disabled = true; btn.classList.add('loading');
        try {
            const r = await API.post('/users/' + editId, fd);
            toast(r.message);
            document.querySelector('#usEditModal [data-close-modal]').click();
            load();
        } catch (err) { API.showErrors(form, err.errors)?.scrollIntoView({ behavior: 'smooth', block: 'center' }); toast(err.message, true); }
        finally { btn.disabled = false; btn.classList.remove('loading'); }
    });

    /* ---------- actions ---------- */
    document.addEventListener('click', async e => {
        const a = e.target.closest('[data-us-act]'); if (!a) return;
        e.preventDefault();
        const id = a.dataset.id, act = a.dataset.usAct;
        if (act === 'view') return openView(id);
        if (act === 'edit') return openEdit(id);
        if (act === 'delete') { delId = id; $('usDelName').textContent = cache[id]?.name || ''; return window.admOpenModal('usDeleteModal'); }
        if (act === 'status') {
            try { const r = await API.patch(`/users/${id}/status`, { status: a.dataset.status }); toast(r.message); load(); }
            catch (err) { toast(err.message, true); }
        }
    });

    $('usDelConfirm').addEventListener('click', async () => {
        if (!delId) return;
        const b = $('usDelConfirm'); b.disabled = true; b.classList.add('loading');
        try {
            const r = await API.del('/users/' + delId);
            document.querySelector('#usDeleteModal [data-close-modal]').click();
            body.querySelector(`tr[data-id="${delId}"]`)?.classList.add('removing');
            toast(r.message);
            if (body.querySelectorAll('tr[data-id]').length === 1 && state.page > 1) state.page--;
            setTimeout(load, 350);
        } catch (err) { toast(err.message, true); }
        finally { b.disabled = false; b.classList.remove('loading'); delId = null; }
    });

    load();
});
</script>
@endpush