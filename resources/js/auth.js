/* =========================================================
   LEXORA – Website auth via API (Sanctum token)
   Login (role auto-detect) · User Sign Up · Advocate Register · User Dashboard
   Roles: superadmin | admin | user
   ========================================================= */
document.addEventListener('DOMContentLoaded', () => {
    if (!window.API) return;

    /* ---------- Header: logged in ho to role ke hisaab se "Dashboard" ---------- */
    if (API.token()) {
        const saved = API.user();
        document.querySelectorAll('[data-auth-link]').forEach(a => {
            a.href = API.homeFor(saved && saved.role);
            const t = a.querySelector('[data-auth-text]');
            if (t) t.textContent = 'Dashboard';
            a.querySelector('i')?.setAttribute('class', 'fa-solid fa-gauge-high');
        });
    }

    const showAlert = (box, type, msg) => {
        if (!box) return;
        const icon = { error: 'fa-circle-exclamation', success: 'fa-circle-check', info: 'fa-circle-info' }[type];
        box.className = `auth-alert ${type}`;
        box.innerHTML = `<i class="fa-solid ${icon}"></i><span>${API.esc(msg)}</span>`;
        box.hidden = false;
    };
    const setLoading = (btn, on) => {
        if (!btn) return;
        btn.disabled = on;
        btn.classList.toggle('loading', on);
    };
    const clearOnInput = form => form.addEventListener('input', e => {
        const g = e.target.closest('.has-error');
        if (g) { g.classList.remove('has-error'); g.querySelector('.f-error')?.remove(); }
    });

    /** Login / signup ke baad role popup dikhao, phir role wale dashboard par bhejo */
    const showRoleAndGo = (res, url) => {
        const m = API.roleMeta(res.role);
        const box = document.getElementById('roleResult');
        if (!box) { location.href = url; return; }
        box.querySelector('.rr-icon i').className = `fa-solid ${m.icon}`;
        box.querySelector('.rr-icon').className = `rr-icon ${m.cls}`;
        const badge = box.querySelector('[data-rr-role]');
        badge.textContent = res.role_label || m.label;
        badge.className = `role-badge ${m.cls}`;
        box.querySelector('[data-rr-name]').textContent = res.user.name;
        box.querySelector('[data-rr-email]').textContent = res.user.email;
        box.hidden = false;
        requestAnimationFrame(() => box.classList.add('show'));
        setTimeout(() => { location.href = url; }, 1900);
    };

    /* =====================================================
       LOGIN – role server detect karta hai
       ===================================================== */
    const lf = document.getElementById('loginForm');
    if (lf) {
        const alertBox = document.getElementById('loginAlert');
        const btn = lf.querySelector('.auth-submit');
        const card = lf.closest('.auth-card');
        clearOnInput(lf);

        // pehle se login hai to apne dashboard par
        if (API.token()) API.me().then(u => location.replace(API.homeFor(u.role))).catch(() => API.clear());

        const qp = new URLSearchParams(location.search);
        if (qp.get('reason') === 'inactive') showAlert(alertBox, 'error', 'Your account is not active. Please contact the superadmin.');
        else if (qp.get('next')) showAlert(alertBox, 'info', 'Please log in to continue.');

        lf.querySelector('.pw-toggle')?.addEventListener('click', e => {
            const b = e.currentTarget, inp = b.parentElement.querySelector('input');
            const show = inp.type === 'password';
            inp.type = show ? 'text' : 'password';
            b.innerHTML = `<i class="fa-regular fa-eye${show ? '-slash' : ''}"></i>`;
        });

        document.getElementById('forgotLink')?.addEventListener('click', e => {
            e.preventDefault();
            showAlert(alertBox, 'info', 'Please contact the superadmin at admin@lexoralegal.com to reset your password.');
        });

        lf.addEventListener('submit', async e => {
            e.preventDefault();
            API.clearErrors(lf);
            alertBox.hidden = true;
            if (!lf.checkValidity()) { lf.reportValidity(); return; }

            const fd = new FormData(lf);
            setLoading(btn, true);
            try {
                const res = await API.post('/auth/login', {
                    login: String(fd.get('login')).trim(),
                    password: fd.get('password'),
                });
                API.setAuth(res.token, res.user, !!fd.get('remember'));
                card?.classList.add('success');

                // ?next= sirf tab jab woh page us role ke liye ho
                const next = qp.get('next');
                const area = res.role === 'user' ? '/dashboard' : '/superadmin';
                const safe = next && next.startsWith('/') && !next.startsWith('//') && next.includes(area);
                showRoleAndGo(res, safe ? next : res.redirect);
            } catch (err) {
                API.showErrors(lf, err.errors);
                showAlert(alertBox, 'error', err.message);
                card?.classList.remove('shake'); void card?.offsetWidth; card?.classList.add('shake');
                setLoading(btn, false);
            }
        });
    }

    /* =====================================================
       USER SIGN UP – role "user", turant login
       ===================================================== */
    const sf = document.getElementById('signupForm');
    if (sf) {
        const alertBox = document.getElementById('signupAlert');
        const btn = sf.querySelector('.auth-submit');
        const card = sf.closest('.auth-card');
        API.bindAdvocateForm(sf);   // password toggle + meter + error clear

        sf.addEventListener('submit', async e => {
            e.preventDefault();
            API.clearErrors(sf);
            alertBox.hidden = true;
            if (!sf.checkValidity()) { sf.reportValidity(); return; }
            if (API.extraChecks(sf, sf)) return;

            setLoading(btn, true);
            try {
                const fd = new FormData(sf);
                const res = await API.post('/auth/user-register', Object.fromEntries(fd.entries()));
                API.setAuth(res.token, res.user, true);
                card?.classList.add('success');
                const rr = document.querySelector('#roleResult .rr-small');
                if (rr) rr.textContent = 'Account created! You are logged in as';
                showRoleAndGo(res, res.redirect);
            } catch (err) {
                API.showErrors(sf, err.errors);
                showAlert(alertBox, 'error', err.message);
                card?.classList.remove('shake'); void card?.offsetWidth; card?.classList.add('shake');
                setLoading(btn, false);
            }
        });
    }

    /* =====================================================
       USER DASHBOARD (/dashboard) – sirf role "user"
       ===================================================== */
    const ud = document.getElementById('userDashboard');
    if (ud) {
        const done = () => document.documentElement.classList.remove('ud-checking');

        API.me().then(u => {
            if (u.role !== 'user') { location.replace(API.homeFor(u.role)); return; }   // superadmin/admin -> panel

            const m = API.roleMeta(u.role);
            const vals = {
                name: u.name, email: u.email, phone: u.phone || '—',
                role: u.role_label, role_label: u.role_label,
                status: (u.status || '').charAt(0).toUpperCase() + (u.status || '').slice(1),
                created_at: u.created_at || '—', last_login_at: u.last_login_at || 'Just now',
            };
            ud.querySelectorAll('[data-u]').forEach(el => {
                if (el.dataset.u === 'avatar') {
                    el.innerHTML = u.photo_url ? `<img src="${API.esc(u.photo_url)}" alt="">` : `<b>${API.esc((u.name || '?').charAt(0).toUpperCase())}</b>`;
                } else if (vals[el.dataset.u] !== undefined) {
                    el.textContent = vals[el.dataset.u];
                }
            });
            ud.querySelector('[data-u="role"]').className = `role-badge ${m.cls}`;
            done();

            // login history
            const list = document.getElementById('loginHistory');
            API.get('/auth/login-history').then(r => {
                list.innerHTML = r.data.length ? r.data.map(l => `
                    <li>
                        <span class="uh-icon ${l.status}"><i class="fa-solid ${l.device === 'Mobile' ? 'fa-mobile-screen' : (l.device === 'Tablet' ? 'fa-tablet-screen-button' : 'fa-desktop')}"></i></span>
                        <div>
                            <b>${API.esc(l.browser || 'Browser')} on ${API.esc(l.device || 'Device')}</b>
                            <small>${API.esc(l.logged_in_at)} · IP ${API.esc(l.ip_address || '—')}</small>
                        </div>
                        <span class="uh-status ${l.status}">${l.logged_out_at ? 'Logged out' : API.esc(l.status)}</span>
                    </li>`).join('') : '<li class="ud-empty">No login activity yet.</li>';
            }).catch(err => { list.innerHTML = `<li class="ud-empty">${API.esc(err.message)}</li>`; });
        }).catch(() => {
            API.clear();
            location.replace(API.url('/login') + '?next=' + encodeURIComponent(location.pathname));
        });
    }

    /* ---------- Website logout ---------- */
    document.addEventListener('click', e => {
        const b = e.target.closest('[data-site-logout]');
        if (!b) return;
        e.preventDefault();
        b.classList.add('loading');
        const go = () => { API.clear(); location.replace(API.url('/login')); };
        API.post('/auth/logout').then(go, go);
    });

    /* =====================================================
       ADVOCATE REGISTER (4 steps) – role "admin", status pending
       ===================================================== */
    const rf = document.getElementById('registerForm');
    if (rf) {
        API.bindAdvocateForm(rf);

        const steps   = [...rf.querySelectorAll('.adv-section[data-step]')];
        const dots    = [...document.querySelectorAll('#regSteps .rs-step')];
        const bar     = document.querySelector('#regSteps .rs-progress i');
        const prevBtn = rf.querySelector('[data-prev]');
        const nextBtn = rf.querySelector('[data-next]');
        const subBtn  = rf.querySelector('[data-submit]');
        const counter = document.getElementById('regCount');
        const alertBox = document.getElementById('regAlert');
        let cur = 0;

        const show = (i, scroll = true) => {
            cur = i;
            steps.forEach((s, n) => s.classList.toggle('active', n === i));
            dots.forEach((d, n) => { d.classList.toggle('active', n === i); d.classList.toggle('done', n < i); });
            if (bar) bar.style.width = (i / (steps.length - 1)) * 100 + '%';
            prevBtn.hidden = i === 0;
            nextBtn.hidden = i === steps.length - 1;
            subBtn.hidden  = i !== steps.length - 1;
            if (counter) counter.textContent = `Step ${i + 1} of ${steps.length}`;
            if (scroll) document.getElementById('regSteps').scrollIntoView({ behavior: 'smooth', block: 'start' });
        };

        const validStep = i => {
            const s = steps[i];
            for (const el of s.querySelectorAll('input, select, textarea')) {
                if (el.type === 'checkbox' && el.name === 'specializations[]') continue;
                if (!el.checkValidity()) { el.reportValidity(); return false; }
            }
            const bad = API.extraChecks(s, rf);
            if (bad) { bad.scrollIntoView({ behavior: 'smooth', block: 'center' }); return false; }
            return true;
        };

        nextBtn.addEventListener('click', () => { if (validStep(cur)) show(cur + 1); });
        prevBtn.addEventListener('click', () => show(cur - 1));
        dots.forEach((d, n) => d.addEventListener('click', () => {
            if (n < cur) return show(n);
            for (let k = cur; k < n; k++) { if (!validStep(k)) return show(k); }
            show(n);
        }));
        show(0, false);

        rf.addEventListener('submit', async e => {
            e.preventDefault();
            alertBox.hidden = true;
            API.clearErrors(rf);
            if (!validStep(cur)) return;

            setLoading(subBtn, true);
            try {
                const res = await API.post('/auth/register', API.formData(rf));
                rf.hidden = true;
                document.getElementById('regSteps').hidden = true;
                document.getElementById('regSuccessMsg').textContent = res.message;
                const ok = document.getElementById('regSuccess');
                ok.hidden = false;
                ok.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } catch (err) {
                const first = API.showErrors(rf, err.errors);
                showAlert(alertBox, 'error', err.message);
                if (first) {
                    const idx = steps.indexOf(first.closest('.adv-section'));
                    if (idx > -1) show(idx);
                    setTimeout(() => first.scrollIntoView({ behavior: 'smooth', block: 'center' }), 350);
                } else {
                    alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            } finally {
                setLoading(subBtn, false);
            }
        });
    }
});