/* =========================================================
   LEXORA – API helper (Laravel Sanctum Bearer token)
   Website aur Superadmin dono me load hota hai.
   ========================================================= */
(function (w) {
    const meta = n => (document.querySelector(`meta[name="${n}"]`)?.content || '').replace(/\/$/, '');
    const BASE = meta('api-base') || '/api';
    const APP  = meta('app-url') || '';
    const KEY  = 'lx_token';
    const UKEY = 'lx_user';

    const read = k => {
        try { return localStorage.getItem(k) ?? sessionStorage.getItem(k); } catch (e) { return null; }
    };
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    const API = {
        esc,
        url: (path = '') => APP + path,

        /* Roles: superadmin | admin | user */
        ROLE_META: {
            superadmin: { label: 'Super Admin',      icon: 'fa-crown',    home: '/superadmin', cls: 'gold' },
            admin:      { label: 'Admin (Advocate)', icon: 'fa-user-tie', home: '/admin',      cls: 'blue' },
            user:       { label: 'User',             icon: 'fa-user',     home: '/dashboard',  cls: 'green' },
        },
        roleMeta: role => API.ROLE_META[role] || { label: role || 'Unknown', icon: 'fa-user', home: '/', cls: 'grey' },
        homeFor: role => API.url(API.roleMeta(role).home),
        token: () => read(KEY),
        user: () => { try { return JSON.parse(read(UKEY) || 'null'); } catch (e) { return null; } },

        /** remember = true -> localStorage (browser band hone par bhi login), false -> sessionStorage */
        setAuth(token, user, remember = true) {
            API.clear();
            try {
                const s = remember ? localStorage : sessionStorage;
                s.setItem(KEY, token);
                s.setItem(UKEY, JSON.stringify(user));
            } catch (e) {}
        },
        clear() {
            try { [localStorage, sessionStorage].forEach(s => { s.removeItem(KEY); s.removeItem(UKEY); }); } catch (e) {}
        },

        async request(method, path, data) {
            const headers = { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' };
            const token = API.token();
            if (token) headers.Authorization = 'Bearer ' + token;

            let body;
            if (data instanceof FormData) body = data;               // multipart (files)
            else if (data !== undefined) { headers['Content-Type'] = 'application/json'; body = JSON.stringify(data); }

            let res;
            try {
                res = await fetch(BASE + path, { method, headers, body });
            } catch (e) {
                throw { status: 0, message: 'Network error – please check your internet connection.', errors: {} };
            }

            let json = {};
            try { json = await res.json(); } catch (e) {}

            if (!res.ok) {
                // token expire / invalid -> logout
                if (res.status === 401 && token) {
                    API.clear();
                    if (document.body && document.body.classList.contains('adm')) {
                        location.replace(API.url('/login') + '?next=' + encodeURIComponent(location.pathname));
                    }
                }
                let msg = json.message || `Something went wrong (${res.status}).`;
                if (res.status === 429) msg = 'Too many attempts. Please wait a minute and try again.';
                if (res.status === 413) msg = 'Uploaded files are too large.';
                throw { status: res.status, message: msg, errors: json.errors || {} };
            }
            return json;
        },
        get:   p      => API.request('GET', p),
        post:  (p, d) => API.request('POST', p, d),
        patch: (p, d) => API.request('PATCH', p, d),
        del:   p      => API.request('DELETE', p),
        me:    ()     => API.get('/auth/me').then(r => r.user),

        /* ---------------- Form helpers ---------------- */
        clearErrors(form) {
            form.querySelectorAll('.has-error').forEach(g => g.classList.remove('has-error'));
            form.querySelectorAll('.f-error').forEach(e => e.remove());
        },

        /** Laravel 422 errors ko field ke neeche dikhata hai, pehla error wala group return karta hai */
        showErrors(form, errors) {
            let first = null;
            Object.entries(errors || {}).forEach(([key, msgs]) => {
                const name = key.split('.')[0];
                const grp = form.querySelector(`[data-field="${name}"]`);
                if (!grp) return;
                grp.classList.add('has-error');
                let el = grp.querySelector('.f-error');
                if (!el) { el = document.createElement('small'); el.className = 'f-error'; grp.appendChild(el); }
                el.textContent = Array.isArray(msgs) ? msgs[0] : msgs;
                if (!first) first = grp;
            });
            return first;
        },

        /** Form ko FormData me badlo – khaali file inputs hata do */
        formData(form) {
            const fd = new FormData(form);
            [...fd.entries()].forEach(([k, v]) => { if (v instanceof File && !v.name) fd.delete(k); });
            return fd;
        },

        /** Advocate form: photo preview, file name, password toggle/meter, error clear */
        bindAdvocateForm(form) {
            form.addEventListener('input', e => {
                const g = e.target.closest('.has-error');
                if (g) { g.classList.remove('has-error'); g.querySelector('.f-error')?.remove(); }
            });
            form.addEventListener('change', e => {
                const g = e.target.closest('.has-error');
                if (g) { g.classList.remove('has-error'); g.querySelector('.f-error')?.remove(); }
            });

            const photo = form.querySelector('input[name="photo"]');
            photo?.addEventListener('change', () => {
                const f = photo.files[0];
                if (!f) return;
                API.setPhoto(form, URL.createObjectURL(f));
            });

            form.querySelectorAll('.file-up input[type=file]').forEach(inp => {
                const label = inp.closest('.file-up').querySelector('[data-file-label]');
                const def = label.textContent;
                inp.addEventListener('change', () => {
                    const f = inp.files[0];
                    label.textContent = f ? `${f.name} (${(f.size / 1024 / 1024).toFixed(2)} MB)` : def;
                    inp.closest('.file-up').classList.toggle('has-file', !!f);
                });
            });

            form.querySelectorAll('.pw-toggle').forEach(btn => {
                btn.addEventListener('click', () => {
                    const inp = btn.parentElement.querySelector('input');
                    const show = inp.type === 'password';
                    inp.type = show ? 'text' : 'password';
                    btn.innerHTML = `<i class="fa-regular fa-eye${show ? '-slash' : ''}"></i>`;
                });
            });

            const pw = form.querySelector('input[name="password"]');
            const meter = form.querySelector('.pw-meter i');
            pw?.addEventListener('input', () => {
                if (!meter) return;
                const v = pw.value;
                let s = 0;
                if (v.length >= 8) s++;
                if (/[a-z]/i.test(v) && /\d/.test(v)) s++;
                if (/[A-Z]/.test(v) && /[a-z]/.test(v)) s++;
                if (/[^a-z0-9]/i.test(v)) s++;
                meter.style.width = (v ? Math.max(s, 1) * 25 : 0) + '%';
                meter.dataset.level = s;
            });
        },

        setPhoto(form, src) {
            const box = form.querySelector('[data-photo-preview]');
            if (!box) return;
            const img = box.querySelector('img');
            img.src = src; img.hidden = false;
            box.classList.add('has-img');
        },

        /** Client side checks jo HTML5 se nahi hote (checkbox group, password match) */
        extraChecks(scope, form) {
            const errs = {};
            const spec = scope.querySelector('[data-field="specializations"]');
            if (spec && !spec.querySelector('input:checked')) errs.specializations = ['Select at least one practice area.'];
            const pw = scope.querySelector('[name="password"]');
            const pc = scope.querySelector('[name="password_confirmation"]');
            if (pw && pc && (pw.value || pc.value) && pw.value !== pc.value) errs.password = ['Passwords do not match.'];
            return Object.keys(errs).length ? API.showErrors(form, errs) : null;
        },

        /** Edit ke liye API data ko form me bharo */
        fillAdvocateForm(form, u) {
            const vals = Object.assign({ name: u.name, email: u.email, phone: u.phone, status: u.status }, u.user_profile || {}, u.profile || {});
            Object.entries(vals).forEach(([k, v]) => {
                if (v === null || v === undefined) return;
                if (k === 'specializations') {
                    form.querySelectorAll('[name="specializations[]"]').forEach(c => { c.checked = v.includes(c.value); });
                    return;
                }
                const el = form.elements.namedItem(k);
                if (!el || el.type === 'file' || el instanceof RadioNodeList) return;
                el.value = v;
            });
            if (u.photo_url) API.setPhoto(form, u.photo_url);
            const p = u.profile || {};
            [['id_proof', p.id_proof_url], ['enrollment_certificate', p.enrollment_certificate_url]].forEach(([f, url]) => {
                const a = form.querySelector(`[data-field="${f}"] .file-current`);
                if (a && url) { a.href = url; a.hidden = false; }
            });
        },
    };

    w.API = API;
})(window);