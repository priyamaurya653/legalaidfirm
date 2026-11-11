/* =========================================================
   LEXORA SUPERADMIN – Users list + Login Activity (API)
   ========================================================= */
document.addEventListener('DOMContentLoaded', () => {
    if (!window.API) return;
    const esc   = API.esc;
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const cap   = s => s ? s.charAt(0).toUpperCase() + s.slice(1) : '';

    const roleBadge = role => {
        const m = API.roleMeta(role);
        return `<span class="role-pill ${m.cls}"><i class="fa-solid ${m.icon}"></i> ${esc(role ? m.label : 'Unknown')}</span>`;
    };
    const avatar = (name, photo) => photo
        ? `<img class="t-avatar" src="${esc(photo)}" alt="">`
        : `<span class="avatar-txt">${esc((name || '?').replace(/^adv\.?\s+/i, '').charAt(0).toUpperCase())}</span>`;

    const skeleton = (tbody, cols) => {
        tbody.innerHTML = Array.from({ length: 6 }, () =>
            `<tr class="sk-row"><td><div class="t-user"><span class="sk sk-av"></span><div><span class="sk sk-line w140"></span><span class="sk sk-line w100"></span></div></div></td>${'<td><span class="sk sk-line w80"></span></td>'.repeat(cols - 1)}</tr>`).join('');
    };
    const empty = (tbody, cols, msg, isErr) => {
        tbody.innerHTML = `<tr><td colspan="${cols}"><div class="t-empty ${isErr ? 'error' : ''}"><i class="fa-solid ${isErr ? 'fa-triangle-exclamation' : 'fa-inbox'}"></i><p>${esc(msg)}</p></div></td></tr>`;
    };
    const renderPager = (pager, meta) => {
        if (!meta || !meta.total) { pager.innerHTML = ''; return; }
        const { current_page: cur, last_page: last, from, to, total } = meta;
        const pages = [];
        for (let i = Math.max(1, cur - 2); i <= Math.min(last, cur + 2); i++) pages.push(i);
        pager.innerHTML = `<small>Showing ${from}–${to} of ${total}</small>
            <div class="pages">
                <button class="icon-btn" data-page="${cur - 1}" ${cur <= 1 ? 'disabled' : ''} aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
                ${pages.map(n => `<button class="icon-btn ${n === cur ? 'active' : ''}" data-page="${n}">${n}</button>`).join('')}
                <button class="icon-btn" data-page="${cur + 1}" ${cur >= last ? 'disabled' : ''} aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
            </div>`;
    };
    const bindChips = (wrap, onPick) => wrap?.querySelectorAll('button').forEach(b => b.addEventListener('click', () => {
        wrap.querySelectorAll('button').forEach(x => x.classList.remove('active'));
        b.classList.add('active');
        onPick(b.dataset.f);
    }));
    const debounce = (fn, ms = 350) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };

    const loadStats = () => API.get('/dashboard/stats').then(s => {
        const u = { total: s.users.total, active: s.users.active, inactive: s.users.inactive, logins: s.logins_today.user };
        Object.entries(u).forEach(([k, v]) => document.querySelectorAll(`[data-ustat="${k}"]`).forEach(el => { el.textContent = v; }));
        const l = { superadmin: s.logins_today.superadmin, admin: s.logins_today.admin, user: s.logins_today.user, failed: s.logins_today.failed + s.logins_today.blocked };
        Object.entries(l).forEach(([k, v]) => document.querySelectorAll(`[data-lstat="${k}"]`).forEach(el => { el.textContent = v; }));
    }).catch(() => {});

    /* =====================================================
       USERS PAGE
       ===================================================== */
    const uBody = document.getElementById('userRows');
    if (uBody) {
        const state = { page: 1, search: '', status: '' };
        const pager = document.getElementById('userPager');
        const cache = {};

        const row = u => {
            const pid = 'usrPop' + u.id;
            return `<tr data-id="${u.id}">
                <td><div class="t-user">${avatar(u.name, u.photo_url)}<div><div class="t-title">${esc(u.name)}</div><small class="t-sub">${esc(u.email)}</small></div></div></td>
                <td class="hide-sm">${esc(u.phone || '—')}</td>
                <td>${roleBadge(u.role)}</td>
                <td><span class="status ${u.status === 'active' ? 'green' : 'grey'}">${esc(cap(u.status))}</span></td>
                <td class="hide-md">${esc(u.logins_count ?? 0)}</td>
                <td class="hide-md">${esc(u.last_login_at || 'Never')}</td>
                <td class="hide-md">${esc(u.created_at || '')}</td>
                <td class="t-right">
                    <div class="popper-wrap">
                        <button class="icon-btn" data-popper="${pid}" aria-label="Actions"><i class="fa-solid fa-ellipsis"></i></button>
                        <div class="popper popper-right" id="${pid}" data-dyn>
                            <ul class="pop-menu">
                                <li><a href="${API.url('/superadmin/login-activity')}?search=${encodeURIComponent(u.email)}"><i class="fa-solid fa-clock-rotate-left"></i> Login History</a></li>
                                ${u.status === 'active'
                                    ? `<li><a href="#" data-uact="status" data-status="inactive" data-id="${u.id}"><i class="fa-solid fa-ban"></i> Deactivate</a></li>`
                                    : `<li><a href="#" data-uact="status" data-status="active" data-id="${u.id}"><i class="fa-solid fa-circle-check"></i> Activate</a></li>`}
                                <li class="sep"></li>
                                <li><a href="#" class="danger" data-uact="delete" data-id="${u.id}"><i class="fa-regular fa-trash-can"></i> Delete</a></li>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>`;
        };

        const load = async () => {
            document.querySelectorAll('.popper[data-dyn]').forEach(p => p.remove());
            skeleton(uBody, 8);
            const qs = new URLSearchParams({ page: state.page });
            if (state.search) qs.set('search', state.search);
            if (state.status) qs.set('status', state.status);
            try {
                const res = await API.get('/users?' + qs);
                res.data.forEach(u => { cache[u.id] = u; });
                res.data.length ? (uBody.innerHTML = res.data.map(row).join('')) : empty(uBody, 8, 'No users found.');
                renderPager(pager, res.meta);
            } catch (err) { empty(uBody, 8, err.message, true); pager.innerHTML = ''; }
        };

        document.getElementById('userSearch')?.addEventListener('input', debounce(e => { state.search = e.target.value.trim(); state.page = 1; load(); }));
        bindChips(document.getElementById('userFilter'), f => { state.status = f; state.page = 1; load(); });
        pager.addEventListener('click', e => { const b = e.target.closest('[data-page]'); if (!b || b.disabled) return; state.page = +b.dataset.page; load(); });

        document.addEventListener('click', async e => {
            const a = e.target.closest('[data-uact]');
            if (!a) return;
            e.preventDefault();
            const id = a.dataset.id, u = cache[id];
            try {
                if (a.dataset.uact === 'status') {
                    const r = await API.patch(`/users/${id}/status`, { status: a.dataset.status });
                    toast(r.message);
                }
                if (a.dataset.uact === 'delete') {
                    if (!confirm(`Delete user "${u ? u.name : ''}"? This cannot be undone.`)) return;
                    const r = await API.del(`/users/${id}`);
                    uBody.querySelector(`tr[data-id="${id}"]`)?.classList.add('removing');
                    toast(r.message);
                }
                setTimeout(() => { load(); loadStats(); }, 300);
            } catch (err) { toast(err.message, true); }
        });

        load();
        loadStats();
    }

    /* =====================================================
       LOGIN ACTIVITY PAGE
       ===================================================== */
    const lBody = document.getElementById('logRows');
    if (lBody) {
        const params = new URLSearchParams(location.search);
        const state = { page: 1, search: params.get('search') || '', role: params.get('role') || '', status: '', date: '' };
        const pager = document.getElementById('logPager');
        const searchInp = document.getElementById('logSearch');
        if (searchInp) searchInp.value = state.search;
        document.querySelectorAll('#logRole button').forEach(b => b.classList.toggle('active', b.dataset.f === state.role));

        const devIcon = d => d === 'Mobile' ? 'fa-mobile-screen' : (d === 'Tablet' ? 'fa-tablet-screen-button' : 'fa-desktop');
        const stCls = { success: 'green', failed: 'red', blocked: 'gold' };

        const row = l => {
            const who = l.user
                ? `<div class="t-user">${avatar(l.user.name, l.user.photo_url)}<div><div class="t-title">${esc(l.user.name)}</div><small class="t-sub">${esc(l.user.email)}</small></div></div>`
                : `<div class="t-user"><span class="avatar-txt muted"><i class="fa-solid fa-question"></i></span><div><div class="t-title">Unknown</div><small class="t-sub">${esc(l.login_input || '—')}</small></div></div>`;
            return `<tr>
                <td>${who}</td>
                <td>${roleBadge(l.role)}</td>
                <td><span class="status ${stCls[l.status] || 'grey'}" title="${esc(l.reason || '')}">${esc(cap(l.status))}</span>${l.reason && l.status !== 'success' ? `<small class="t-sub d-block">${esc(l.reason)}</small>` : ''}</td>
                <td class="hide-sm"><span class="dev"><i class="fa-solid ${devIcon(l.device)}"></i> ${esc(l.browser || '—')} · ${esc(l.device || '—')}</span></td>
                <td class="hide-md"><code class="ip">${esc(l.ip_address || '—')}</code></td>
                <td><div class="t-title sm">${esc(l.logged_in_at || '')}</div><small class="t-sub">${esc(l.logged_in_ago || '')}</small></td>
                <td class="hide-md">${l.logged_out_at ? esc(l.logged_out_at) : (l.status === 'success' ? '<span class="live-dot">Active session</span>' : '—')}</td>
            </tr>`;
        };

        const load = async () => {
            skeleton(lBody, 7);
            const qs = new URLSearchParams({ page: state.page });
            ['search', 'role', 'status', 'date'].forEach(k => { if (state[k]) qs.set(k, state[k]); });
            try {
                const res = await API.get('/login-logs?' + qs);
                res.data.length ? (lBody.innerHTML = res.data.map(row).join('')) : empty(lBody, 7, 'No login activity for this filter.');
                renderPager(pager, res.meta);
            } catch (err) { empty(lBody, 7, err.message, true); pager.innerHTML = ''; }
        };

        searchInp?.addEventListener('input', debounce(e => { state.search = e.target.value.trim(); state.page = 1; load(); }));
        bindChips(document.getElementById('logRole'), f => { state.role = f; state.page = 1; load(); });
        document.getElementById('logStatus')?.addEventListener('change', e => { state.status = e.target.value; state.page = 1; load(); });
        document.getElementById('logDate')?.addEventListener('change', e => { state.date = e.target.value; state.page = 1; load(); });
        document.getElementById('logRefresh')?.addEventListener('click', () => { load(); loadStats(); toast('Login activity refreshed'); });
        pager.addEventListener('click', e => { const b = e.target.closest('[data-page]'); if (!b || b.disabled) return; state.page = +b.dataset.page; load(); });

        load();
        loadStats();
    }
});