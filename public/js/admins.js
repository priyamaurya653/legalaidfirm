/* =========================================================
   LEXORA SUPERADMIN – Admins (Advocates) : list + create/edit
   Sab data /api/admins se (Sanctum token)
   ========================================================= */
document.addEventListener('DOMContentLoaded', () => {
    if (!window.API) return;
    const esc   = API.esc;
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const cap   = s => s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
    const statusCls = { active: 'green', pending: 'gold', inactive: 'grey' };

    /* =====================================================
       LIST PAGE
       ===================================================== */
    const tbody = document.getElementById('adminRows');
    if (tbody) {
        const params = new URLSearchParams(location.search);
        const state  = { page: 1, search: '', status: params.get('status') || '' };
        const cache  = {};
        const pager  = document.getElementById('adminPager');
        const chips  = document.querySelectorAll('#adminFilter button');

        const syncChips = () => chips.forEach(c => c.classList.toggle('active', c.dataset.f === state.status));
        syncChips();

        const loadStats = () => API.get('/dashboard/stats').then(s => {
            Object.entries(s.admins).forEach(([k, v]) => {
                document.querySelectorAll(`.mini [data-stat="${k}"]`).forEach(el => { el.textContent = v; });
            });
            const b = document.getElementById('pendingBadge');
            if (b) { b.textContent = s.admins.pending; b.hidden = !s.admins.pending; }
        }).catch(() => {});

        const skeleton = () => {
            tbody.innerHTML = Array.from({ length: 5 }, () =>
                `<tr class="sk-row"><td><div class="t-user"><span class="sk sk-av"></span><div><span class="sk sk-line w140"></span><span class="sk sk-line w100"></span></div></div></td>
                 <td class="hide-sm"><span class="sk sk-line w80"></span></td><td class="hide-sm"><span class="sk sk-line w80"></span></td>
                 <td class="hide-md"><span class="sk sk-line w120"></span></td><td><span class="sk sk-pill"></span></td><td><span class="sk sk-pill"></span></td>
                 <td class="hide-md"><span class="sk sk-line w80"></span></td><td></td></tr>`).join('');
        };

        const row = u => {
            const p  = u.profile || {};
            const up = u.user_profile || {};
            const place = [up.city || p.city, up.state || p.state].filter(Boolean).join(', ');
            const area  = up.interested_in || (p.specializations || [])[0] || '';
            const pid = 'admPop' + u.id;
            const avatar = u.photo_url
                ? `<img class="t-avatar" src="${esc(u.photo_url)}" alt="">`
                : `<span class="avatar-txt">${esc((u.name || '?').replace(/^adv\.?\s+/i, '').charAt(0).toUpperCase())}</span>`;

            return `<tr data-id="${u.id}">
                <td><div class="t-user">${avatar}<div><div class="t-title">${esc(u.name)}</div><small class="t-sub">${esc(u.email)}</small></div></div></td>
                <td class="hide-sm">${esc(place || '—')}</td>
                <td class="hide-sm">${esc(u.phone || '—')}</td>
                <td class="hide-md">${area ? `<span class="tag">${esc(area)}</span>` : '—'}</td>
                <td><a href="${API.url('/superadmin/clients?advocate=' + u.id)}" class="tag" title="View this admin's clients"><i class="fa-solid fa-users" style="color:var(--gold);margin-right:5px"></i>${u.clients_count ?? 0} ${(u.clients_count ?? 0) === 1 ? 'client' : 'clients'}</a></td>
                <td><span class="status ${statusCls[u.status] || 'grey'}">${esc(cap(u.status))}</span></td>
                <td class="hide-md">${esc(u.created_at || '')}</td>
                <td class="t-right">
                    <div class="popper-wrap">
                        <button class="icon-btn" data-popper="${pid}" aria-label="Actions"><i class="fa-solid fa-ellipsis"></i></button>
                        <div class="popper popper-right" id="${pid}" data-dyn>
                            <ul class="pop-menu">
                                <li><a href="#" data-act="view" data-id="${u.id}"><i class="fa-regular fa-eye"></i> View Profile</a></li>
                                <li><a href="${API.url('/superadmin/admins/' + u.id + '/edit')}"><i class="fa-regular fa-pen-to-square"></i> Edit</a></li>
                                <li><a href="${API.url('/superadmin/clients?advocate=' + u.id)}"><i class="fa-solid fa-users"></i> View Clients (${u.clients_count ?? 0})</a></li>
                                ${u.status !== 'active' ? `<li><a href="#" data-act="status" data-status="active" data-id="${u.id}"><i class="fa-solid fa-circle-check"></i> ${u.status === 'pending' ? 'Approve' : 'Activate'}</a></li>` : ''}
                                ${u.status === 'active' ? `<li><a href="#" data-act="status" data-status="inactive" data-id="${u.id}"><i class="fa-solid fa-ban"></i> Deactivate</a></li>` : ''}
                                <li class="sep"></li>
                                <li><a href="#" class="danger" data-act="delete" data-id="${u.id}"><i class="fa-regular fa-trash-can"></i> Delete</a></li>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>`;
        };

        const renderPager = meta => {
            if (!meta || !meta.total) { pager.innerHTML = ''; return; }
            const { current_page: cur, last_page: last, from, to, total } = meta;
            let pages = [];
            for (let i = Math.max(1, cur - 2); i <= Math.min(last, cur + 2); i++) pages.push(i);
            pager.innerHTML = `<small>Showing ${from}–${to} of ${total}</small>
                <div class="pages">
                    <button class="icon-btn" data-page="${cur - 1}" ${cur <= 1 ? 'disabled' : ''} aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
                    ${pages.map(n => `<button class="icon-btn ${n === cur ? 'active' : ''}" data-page="${n}">${n}</button>`).join('')}
                    <button class="icon-btn" data-page="${cur + 1}" ${cur >= last ? 'disabled' : ''} aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
                </div>`;
        };

        const load = async () => {
            document.querySelectorAll('.popper[data-dyn]').forEach(p => p.remove());
            skeleton();
            const qs = new URLSearchParams({ page: state.page });
            if (state.search) qs.set('search', state.search);
            if (state.status) qs.set('status', state.status);
            try {
                const res = await API.get('/admins?' + qs.toString());
                res.data.forEach(u => { cache[u.id] = u; });
                tbody.innerHTML = res.data.length ? res.data.map(row).join('') : `
                    <tr><td colspan="8"><div class="t-empty">
                        <i class="fa-solid fa-user-slash"></i>
                        <p>No admins found${state.search || state.status ? ' for this filter' : ''}.</p>
                        <a href="${API.url('/superadmin/admins/create')}" class="adm-btn adm-btn-gold sm"><i class="fa-solid fa-user-plus"></i> Create Admin</a>
                    </div></td></tr>`;
                renderPager(res.meta);
            } catch (err) {
                tbody.innerHTML = `<tr><td colspan="8"><div class="t-empty error"><i class="fa-solid fa-triangle-exclamation"></i><p>${esc(err.message)}</p></div></td></tr>`;
                pager.innerHTML = '';
            }
        };

        // search (debounce)
        let t;
        document.getElementById('adminSearch')?.addEventListener('input', e => {
            clearTimeout(t);
            t = setTimeout(() => { state.search = e.target.value.trim(); state.page = 1; load(); }, 350);
        });

        // status filter chips + stat cards
        const setStatus = s => {
            state.status = s; state.page = 1; syncChips(); load();
            const u = new URL(location.href);
            s ? u.searchParams.set('status', s) : u.searchParams.delete('status');
            history.replaceState(null, '', u);
        };
        chips.forEach(c => c.addEventListener('click', () => setStatus(c.dataset.f)));
        document.querySelectorAll('[data-quick-status]').forEach(b => b.addEventListener('click', () => setStatus(b.dataset.quickStatus)));

        pager.addEventListener('click', e => {
            const b = e.target.closest('[data-page]');
            if (!b || b.disabled) return;
            state.page = +b.dataset.page;
            load();
            tbody.closest('.card').scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        // row actions
        document.addEventListener('click', async e => {
            const a = e.target.closest('[data-act]');
            if (!a) return;
            e.preventDefault();
            const id = a.dataset.id, u = cache[id];

            if (a.dataset.act === 'view') return openView(u);

            if (a.dataset.act === 'status') {
                try {
                    const r = await API.patch(`/admins/${id}/status`, { status: a.dataset.status });
                    toast(r.message);
                    load(); loadStats();
                } catch (err) { toast(err.message, true); }
            }

            if (a.dataset.act === 'delete') {
                if (!confirm(`Delete "${u ? u.name : 'this admin'}"?\nAll documents will be removed. This cannot be undone.`)) return;
                try {
                    const r = await API.del(`/admins/${id}`);
                    tbody.querySelector(`tr[data-id="${id}"]`)?.classList.add('removing');
                    toast(r.message);
                    setTimeout(() => { load(); loadStats(); }, 400);
                } catch (err) { toast(err.message, true); }
            }
        });

        // View modal
        const openView = u => {
            if (!u) return;
            const up = u.user_profile || {};   // website signup wale admin ki details
            const p = Object.assign({ gender: up.gender, dob: up.dob, alt_phone: up.alt_phone, office_address: up.address, city: up.city, state: up.state, pincode: up.pincode }, u.profile || {});
            const item = (label, val) => `<div class="vd-item"><small>${label}</small><b>${esc(val || '—')}</b></div>`;

            document.getElementById('viewAdminBody').innerHTML = `
                <div class="vd-top">
                    ${u.photo_url ? `<img src="${esc(u.photo_url)}" alt="">` : `<span class="avatar-txt xl">${esc((u.name || '?').charAt(0))}</span>`}
                    <div>
                        <h3>${esc(u.name)}</h3>
                        <p>Admin${up.occupation ? ' · ' + esc(up.occupation) : ''}${u.created_at ? ' · Joined ' + esc(u.created_at) : ''}</p>
                        <span class="status ${statusCls[u.status] || 'grey'}">${esc(cap(u.status))}</span>
                        ${u.last_login_at ? `<small class="muted-sm d-block">Last login ${esc(u.last_login_at)}</small>` : ''}
                    </div>
                </div>
                <h6 class="vd-h">Personal</h6>
                <div class="vd-grid">${item('Full Name', u.name)}${item('Gender', p.gender)}${item('Date of Birth', p.dob)}${item('Occupation', up.occupation)}</div>
                <h6 class="vd-h">Contact</h6>
                <div class="vd-grid">${item('Email (login)', u.email)}${item('Mobile', u.phone)}${item('Alternate', p.alt_phone)}${item('Practice Area', up.interested_in)}${item('Heard From', up.heard_from)}</div>
                <h6 class="vd-h">Address</h6>
                <div class="vd-grid">${item('Address', p.office_address)}${item('City', p.city)}${item('State', p.state)}${item('Pincode', p.pincode)}</div>
                <div class="modal-foot">
                    ${u.status !== 'active' ? `<a href="#" class="adm-btn adm-btn-gold" data-act="status" data-status="active" data-id="${u.id}" data-close-modal><i class="fa-solid fa-circle-check"></i> ${u.status === 'pending' ? 'Approve' : 'Activate'}</a>` : ''}
                    <a href="${API.url('/superadmin/admins/' + u.id + '/edit')}" class="adm-btn adm-btn-ghost"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                </div>`;
            window.admOpenModal && window.admOpenModal('viewAdminModal');
        };

        load();
        loadStats();
    }

    /* =====================================================
       CREATE / EDIT PAGE
       ===================================================== */
    const form = document.getElementById('adminForm');
    if (form) {
        API.bindAdvocateForm(form);
        const id  = form.dataset.editId;
        const btn = form.querySelector('.save-btn');

        if (id) {
            form.classList.add('is-loading');
            API.get('/admins/' + id)
                .then(r => API.fillAdvocateForm(form, r.data))
                .catch(err => { toast(err.message, true); if (err.status === 404) setTimeout(() => location.replace(API.url('/superadmin/admins')), 1200); })
                .finally(() => form.classList.remove('is-loading'));
        }

        form.addEventListener('submit', async e => {
            e.preventDefault();
            API.clearErrors(form);

            // HTML5 + custom checks
            const invalid = [...form.querySelectorAll('input, select, textarea')]
                .find(el => !(el.name === 'specializations[]') && !el.checkValidity());
            if (invalid) {
                invalid.closest('.adv-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                setTimeout(() => invalid.reportValidity(), 300);
                return;
            }
            const bad = API.extraChecks(form, form);
            if (bad) { bad.scrollIntoView({ behavior: 'smooth', block: 'center' }); return; }

            const fd = API.formData(form);
            if (id && !fd.get('password')) { fd.delete('password'); fd.delete('password_confirmation'); }

            btn.disabled = true; btn.classList.add('loading');
            try {
                const r = await API.post(id ? '/admins/' + id : '/admins', fd);
                toast(r.message);
                setTimeout(() => { location.href = API.url('/superadmin/admins'); }, 900);
            } catch (err) {
                const first = API.showErrors(form, err.errors);
                first?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                toast(err.message, true);
                btn.disabled = false; btn.classList.remove('loading');
            }
        });
    }
});