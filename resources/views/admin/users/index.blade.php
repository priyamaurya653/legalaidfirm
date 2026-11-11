@extends('frontlayout.app')
@section('title', 'My Users')
@section('page_title', 'My Users')
@section('require_role', 'admin')

@section('page_actions')
    <a href="{{ route('admin.users.create') }}" class="adm-btn adm-btn-gold"><i class="fa-solid fa-user-plus"></i> Create User</a>
@endsection

@push('styles')
<style>
/* ===== ADMIN – MY USERS (isi file me) ===== */
.mu-case { font-size: 12px; color: var(--muted); }
.adm-table tr.removing { opacity: 0; transform: translateX(30px); transition: .35s; }
.mv-head { display: flex; align-items: center; gap: 14px; margin-bottom: 18px; }
.mv-av { width: 62px; height: 62px; flex-shrink: 0; border-radius: 18px; overflow: hidden; display: grid; place-items: center; font-size: 24px; font-weight: 700; color: #062b1a; background: linear-gradient(135deg, #7ff0b8, #2fbf7d); }
.mv-av img { width: 100%; height: 100%; object-fit: cover; }
.mv-head h4 { color: #fff; font-size: 19px; margin: 0 0 4px; }
.mv-kv { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px 18px; margin-bottom: 18px; }
.mv-kv small { display: block; color: var(--muted); font-size: 12px; margin-bottom: 2px; }
.mv-kv b { color: #eee; font-size: 13.5px; font-weight: 500; overflow-wrap: anywhere; }
.mv-kv .full { grid-column: 1 / -1; }
.mv-logs { list-style: none; padding: 0; margin: 0 0 16px; }
.mv-logs li { display: flex; gap: 10px; align-items: center; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,.05); font-size: 13px; color: #ccc; }
.mv-logs li i { color: #5fe0a5; width: 16px; } .mv-logs li.bad i { color: #ff8c8c; }
.mv-logs li span { flex: 1; } .mv-logs li small { color: var(--muted); }
.mu-del { text-align: center; padding: 10px 6px 4px; }
.mu-del .ring { width: 76px; height: 76px; margin: 0 auto 16px; border-radius: 50%; display: grid; place-items: center; font-size: 30px; color: #ff6b6b; background: rgba(255,107,107,.1); }
.mu-del h4 { color: #fff; font-size: 20px; margin-bottom: 8px; }
.mu-del p { color: var(--muted); font-size: 14px; margin-bottom: 22px; }
.mu-del p b { color: var(--gold-light); }
.mu-del .btns { display: flex; gap: 10px; justify-content: center; }
.adm-btn-danger { color: #fff; background: linear-gradient(135deg, #ff7a7a, #d63c3c); border: 0; }
@media (max-width: 767px) { .mv-kv { grid-template-columns: 1fr 1fr; } }
@media (max-width: 575px) { .mv-kv { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

<div class="mini-stats">
    <div class="glass-card mini"><span class="sc-icon green"><i class="fa-solid fa-users"></i></span><div><b data-mu="total">–</b><small>My Users</small></div></div>
    <div class="glass-card mini"><span class="sc-icon blue"><i class="fa-solid fa-user-check"></i></span><div><b data-mu="active">–</b><small>Active</small></div></div>
    <div class="glass-card mini"><span class="sc-icon purple"><i class="fa-solid fa-user-slash"></i></span><div><b data-mu="inactive">–</b><small>Inactive</small></div></div>
    <div class="glass-card mini"><span class="sc-icon gold"><i class="fa-solid fa-calendar-plus"></i></span><div><b data-mu="new_week">–</b><small>Added this week</small></div></div>
</div>

<div class="glass-card card">
    <div class="toolbar">
        <div class="tb-search sm">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Search name, email, phone, city, case…" id="muSearch" autocomplete="off">
        </div>
        <div class="filter-chips" id="muStatus">
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
                    <th class="hide-sm">Mobile</th>
                    <th class="hide-md">Case</th>
                    <th class="hide-md">City</th>
                    <th>Status</th>
                    <th class="hide-md">Last Login</th>
                    <th class="t-right">Action</th>
                </tr>
            </thead>
            <tbody id="muRows"></tbody>
        </table>
    </div>
    <div class="pager" id="muPager"></div>
</div>

@endsection

@push('modals')
<div class="adm-modal" id="muViewModal" aria-hidden="true">
    <div class="adm-modal-box glass-card wide">
        <div class="modal-head">
            <h4><i class="fa-regular fa-id-badge"></i> User Details</h4>
            <button class="icon-btn" data-close-modal aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="muViewBody"></div>
    </div>
</div>
<div class="adm-modal" id="muDeleteModal" aria-hidden="true">
    <div class="adm-modal-box glass-card">
        <div class="mu-del">
            <div class="ring"><i class="fa-regular fa-trash-can"></i></div>
            <h4>Delete User?</h4>
            <p>"<b id="muDelName"></b>" will be deleted and can no longer log in.</p>
            <div class="btns">
                <button class="adm-btn adm-btn-ghost" data-close-modal>Cancel</button>
                <button class="adm-btn adm-btn-danger" id="muDelConfirm"><i class="fa-regular fa-trash-can"></i> Yes, Delete</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
/* ===== ADMIN – MY USERS – API (Bearer token) ===== */
document.addEventListener('auth:ready', function (ev) {
    if (!window.API || ev.detail.role !== 'admin') return;
    const esc = API.esc, $ = i => document.getElementById(i);
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const EDIT = @json(url('/admin/users')) + '/';
    const body = $('muRows'), pager = $('muPager');
    const state = { page: 1, search: '', status: '' }, cache = {};
    let delId = null;
    const debounce = (fn, ms = 350) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };
    const ini = n => esc((n || '?').charAt(0).toUpperCase());
    const avatar = u => u.photo_url ? `<img class="t-avatar" src="${esc(u.photo_url)}" alt="">` : `<span class="avatar-txt">${ini(u.name)}</span>`;

    const row = u => {
        const p = u.user_profile || {}, pid = 'muPop' + u.id, on = u.status === 'active';
        return `<tr data-id="${u.id}">
            <td><div class="t-user">${avatar(u)}<div><div class="t-title">${esc(u.name)}</div><small class="t-sub">${esc(u.email)}</small></div></div></td>
            <td class="hide-sm">+91 ${esc(u.phone || '')}</td>
            <td class="hide-md">${p.interested_in ? `<span class="tag">${esc(p.interested_in)}</span>` : '—'}</td>
            <td class="hide-md">${esc(p.city || '—')}</td>
            <td><span class="status ${on ? 'green' : 'grey'}">${on ? 'Active' : 'Inactive'}</span></td>
            <td class="hide-md">${esc(u.last_login_at || 'Never')}</td>
            <td class="t-right"><div class="popper-wrap">
                <button class="icon-btn" data-popper="${pid}" aria-label="Actions"><i class="fa-solid fa-ellipsis"></i></button>
                <div class="popper popper-right" id="${pid}" data-dyn><ul class="pop-menu">
                    <li><a href="#" data-mu-act="view" data-id="${u.id}"><i class="fa-regular fa-eye"></i> View</a></li>
                    <li><a href="${EDIT}${u.id}/edit"><i class="fa-regular fa-pen-to-square"></i> Edit</a></li>
                    <li><a href="tel:+91${esc(u.phone || '')}"><i class="fa-solid fa-phone"></i> Call</a></li>
                    <li><a href="#" data-mu-act="status" data-status="${on ? 'inactive' : 'active'}" data-id="${u.id}"><i class="fa-solid ${on ? 'fa-ban' : 'fa-circle-check'}"></i> ${on ? 'Deactivate' : 'Activate'}</a></li>
                    <li class="sep"></li>
                    <li><a href="#" class="danger" data-mu-act="delete" data-id="${u.id}"><i class="fa-regular fa-trash-can"></i> Delete</a></li>
                </ul></div></div></td>
        </tr>`;
    };
    const empty = (m, err) => { body.innerHTML = `<tr><td colspan="7"><div class="t-empty ${err ? 'error' : ''}"><i class="fa-solid ${err ? 'fa-triangle-exclamation' : 'fa-users'}"></i><p>${esc(m)}</p>${err ? '' : `<a href="${EDIT}create" class="adm-btn adm-btn-gold"><i class="fa-solid fa-user-plus"></i> Create User</a>`}</div></td></tr>`; };
    const renderPager = meta => {
        if (!meta || !meta.total) { pager.innerHTML = ''; return; }
        const { current_page: c, last_page: l, from, to, total } = meta, p = [];
        for (let i = Math.max(1, c - 2); i <= Math.min(l, c + 2); i++) p.push(i);
        pager.innerHTML = `<small>Showing ${from}–${to} of ${total}</small><div class="pages">
            <button class="icon-btn" data-page="${c - 1}" ${c <= 1 ? 'disabled' : ''}><i class="fa-solid fa-chevron-left"></i></button>
            ${p.map(n => `<button class="icon-btn ${n === c ? 'active' : ''}" data-page="${n}">${n}</button>`).join('')}
            <button class="icon-btn" data-page="${c + 1}" ${c >= l ? 'disabled' : ''}><i class="fa-solid fa-chevron-right"></i></button></div>`;
    };

    async function load() {
        document.querySelectorAll('.popper[data-dyn]').forEach(p => p.remove());
        body.innerHTML = Array.from({ length: 5 }, () => `<tr class="sk-row">${'<td><span class="sk sk-line w80"></span></td>'.repeat(7)}</tr>`).join('');
        const qs = new URLSearchParams({ page: state.page });
        if (state.search) qs.set('search', state.search);
        if (state.status) qs.set('status', state.status);
        try {
            const r = await API.get('/my-users?' + qs);
            r.data.forEach(u => { cache[u.id] = u; });
            Object.entries(r.stats || {}).forEach(([k, v]) => document.querySelectorAll(`[data-mu="${k}"]`).forEach(x => { x.textContent = v; }));
            r.data.length ? (body.innerHTML = r.data.map(row).join('')) : empty(state.search || state.status ? 'No user matches your filters.' : "You haven't created any users yet.");
            renderPager(r.meta);
        } catch (err) { empty(err.message, true); pager.innerHTML = ''; }
    }

    $('muSearch').addEventListener('input', debounce(e => { state.search = e.target.value.trim(); state.page = 1; load(); }));
    $('muStatus').addEventListener('click', e => { const b = e.target.closest('button'); if (!b) return; $('muStatus').querySelectorAll('button').forEach(x => x.classList.toggle('active', x === b)); state.status = b.dataset.f; state.page = 1; load(); });
    pager.addEventListener('click', e => { const b = e.target.closest('[data-page]'); if (!b || b.disabled) return; state.page = +b.dataset.page; load(); });

    async function openView(id) {
        const vb = $('muViewBody');
        vb.innerHTML = '<div class="t-empty"><i class="fa-solid fa-spinner fa-spin"></i></div>';
        window.admOpenModal('muViewModal');
        try {
            const r = await API.get('/my-users/' + id), u = r.data, p = u.user_profile || {};
            const v = x => x ? esc(x) : '<span style="color:var(--muted)">—</span>';
            vb.innerHTML = `
                <div class="mv-head"><span class="mv-av">${u.photo_url ? `<img src="${esc(u.photo_url)}" alt="">` : ini(u.name)}</span>
                    <div><h4>${esc(u.name)}</h4><span class="status ${u.status === 'active' ? 'green' : 'grey'}">${u.status === 'active' ? 'Active' : 'Inactive'}</span> <small class="t-sub">· added ${esc(u.created_at)}</small></div></div>
                <div class="mv-kv">
                    <div><small>Email (login)</small><b>${esc(u.email)}</b></div>
                    <div><small>Mobile</small><b>+91 ${esc(u.phone || '')}</b></div>
                    <div><small>Alternate</small><b>${v(p.alt_phone)}</b></div>
                    <div><small>Gender</small><b>${v(p.gender)}</b></div>
                    <div><small>Date of Birth</small><b>${v(p.dob)}</b></div>
                    <div><small>Occupation</small><b>${v(p.occupation)}</b></div>
                    <div class="full"><small>Address</small><b>${v([p.address, p.city, p.state, p.pincode].filter(Boolean).join(', '))}</b></div>
                    <div><small>Case / Practice Area</small><b>${v(p.interested_in)}</b></div>
                    <div><small>Client Source</small><b>${v(p.heard_from)}</b></div>
                    <div><small>Total Logins</small><b>${esc(u.logins_count ?? 0)}</b></div>
                </div>
                <small class="muted-sm">Recent logins</small>
                <ul class="mv-logs">${(r.logins || []).map(l => `<li class="${l.status === 'success' ? '' : 'bad'}"><i class="fa-solid ${l.status === 'success' ? 'fa-right-to-bracket' : 'fa-triangle-exclamation'}"></i><span>${esc(l.device || 'Unknown device')} · ${esc(l.ip || '')}</span><small>${esc(l.ago || '')}</small></li>`).join('') || '<li><span>Not logged in yet.</span></li>'}</ul>
                <div class="modal-foot"><a href="tel:+91${esc(u.phone || '')}" class="adm-btn adm-btn-ghost"><i class="fa-solid fa-phone"></i> Call</a><a href="${EDIT}${u.id}/edit" class="adm-btn adm-btn-gold"><i class="fa-regular fa-pen-to-square"></i> Edit</a></div>`;
        } catch (err) { vb.innerHTML = `<div class="t-empty error"><i class="fa-solid fa-triangle-exclamation"></i><p>${esc(err.message)}</p></div>`; }
    }

    document.addEventListener('click', async e => {
        const a = e.target.closest('[data-mu-act]'); if (!a) return;
        e.preventDefault();
        const id = a.dataset.id;
        if (a.dataset.muAct === 'view') return openView(id);
        if (a.dataset.muAct === 'delete') { delId = id; $('muDelName').textContent = cache[id]?.name || ''; return window.admOpenModal('muDeleteModal'); }
        if (a.dataset.muAct === 'status') {
            try { const r = await API.patch(`/my-users/${id}/status`, { status: a.dataset.status }); toast(r.message); load(); }
            catch (err) { toast(err.message, true); }
        }
    });
    $('muDelConfirm').addEventListener('click', async () => {
        const b = $('muDelConfirm'); b.disabled = true; b.classList.add('loading');
        try {
            const r = await API.del('/my-users/' + delId);
            document.querySelector('#muDeleteModal [data-close-modal]').click();
            body.querySelector(`tr[data-id="${delId}"]`)?.classList.add('removing');
            toast(r.message); setTimeout(load, 350);
        } catch (err) { toast(err.message, true); }
        finally { b.disabled = false; b.classList.remove('loading'); }
    });

    load();
});
</script>
@endpush