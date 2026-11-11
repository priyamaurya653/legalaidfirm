@extends('frontlayout.app')
@section('title', 'Settings')
@section('page_title', 'Settings')
{{-- superadmin + admin dono apni profile yahan dekh / badal sakte hain (API: /api/profile) --}}

@push('styles')
<style>
/* ===== SETTINGS / MY PROFILE (isi file me) ===== */
.sp-hero { position: relative; overflow: hidden; display: flex; flex-wrap: wrap; align-items: center; gap: 20px; padding: 22px; margin-bottom: 24px; border-radius: 20px;
    background: linear-gradient(130deg, rgba(212,175,55,.16), rgba(255,255,255,.02) 60%); border: 1px solid rgba(212,175,55,.28); }
.sp-hero::after { content: ''; position: absolute; top: 0; left: -60%; width: 40%; height: 100%; transform: skewX(-20deg); pointer-events: none;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.09), transparent); animation: spSweep 6s ease-in-out infinite 1s; }
@keyframes spSweep { 0% { left: -60%; } 35%, 100% { left: 130%; } }
.sp-avatar { position: relative; width: 96px; height: 96px; flex-shrink: 0; border-radius: 26px; overflow: hidden; display: grid; place-items: center;
    font-family: var(--serif, serif); font-size: 40px; font-weight: 700; color: #111; background: var(--gold-grad); box-shadow: 0 0 0 5px rgba(212,175,55,.15), 0 14px 34px rgba(212,175,55,.3); }
.sp-avatar img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
.sp-avatar label { position: absolute; inset: auto 0 0 0; height: 30px; display: grid; place-items: center; color: #fff; font-size: 13px; cursor: pointer; background: rgba(0,0,0,.6); opacity: 0; transition: .3s; margin: 0 !important; }
.sp-avatar:hover label { opacity: 1; }
.sp-info { flex: 1; min-width: 200px; position: relative; z-index: 1; }
.sp-info h3 { color: #fff; font-size: 22px; margin: 0 0 6px; overflow-wrap: anywhere; }
.sp-info .meta { display: flex; flex-wrap: wrap; gap: 8px 18px; color: var(--muted); font-size: 13px; margin-top: 8px; }
.sp-info .meta i { color: var(--gold); margin-right: 4px; }
.sp-photo-acts { display: flex; gap: 8px; flex-wrap: wrap; position: relative; z-index: 1; }
.role-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; letter-spacing: .5px; }
.role-pill.gold { color: #111; background: var(--gold-grad); }
.role-pill.blue { color: #fff; background: linear-gradient(135deg, #7aa5ff, #3f6fd8); }
.role-pill.green { color: #062b1a; background: linear-gradient(135deg, #7ff0b8, #2fbf7d); }
.sp-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 24px; }
.sp-stat { padding: 14px 16px; border-radius: 14px; border: 1px solid var(--border); background: rgba(255,255,255,.02); }
.sp-stat small { display: block; color: var(--muted); font-size: 12px; }
.sp-stat b { color: #fff; font-size: 15px; font-weight: 500; }
.sp-sub { font-size: 12px; letter-spacing: 1.5px; text-transform: uppercase; color: var(--gold-light); margin: 6px 0 14px; }
.sp-actions { display: flex; gap: 10px; flex-wrap: wrap; }
.req { color: #ff7a7a; font-weight: 600; }
.pw-wrap { position: relative; }
.pw-wrap input { padding-right: 44px !important; }
.pw-eye { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: 0; color: var(--muted); cursor: pointer; }
.pw-meter { height: 5px; border-radius: 5px; background: rgba(255,255,255,.06); overflow: hidden; margin-top: 8px; }
.pw-meter i { display: block; height: 100%; width: 0; transition: .3s; background: #ff6b6b; }
.pw-meter i[data-l="2"] { background: #ffb347; } .pw-meter i[data-l="3"] { background: #d4af37; } .pw-meter i[data-l="4"] { background: #3ecf8e; }
.sp-check { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #ccc; margin: 4px 0 18px; cursor: pointer; }
.sp-check input { width: 16px; height: 16px; accent-color: #d4af37; }
.sp-logins { list-style: none; padding: 0; margin: 26px 0 0; }
.sp-logins li { display: flex; align-items: center; gap: 12px; padding: 11px 0; border-bottom: 1px solid rgba(255,255,255,.05); }
.sp-logins li:last-child { border-bottom: 0; }
.sp-logins .ic { width: 38px; height: 38px; flex-shrink: 0; border-radius: 11px; display: grid; place-items: center; color: var(--gold); background: rgba(212,175,55,.1); }
.sp-logins .ic.bad { color: #ff8c8c; background: rgba(255,107,107,.1); }
.sp-logins div { flex: 1; min-width: 0; }
.sp-logins b { display: block; color: #fff; font-size: 13.5px; font-weight: 500; }
.sp-logins small { color: var(--muted); font-size: 12px; }
.set-body.is-loading .set-panel { opacity: .5; pointer-events: none; }
@media (max-width: 575px) { .sp-hero { flex-direction: column; text-align: center; } .sp-info .meta { justify-content: center; } .sp-stats { grid-template-columns: 1fr; } .sp-photo-acts { justify-content: center; } }
</style>
@endpush

@section('content')

<div class="settings">
    <div class="glass-card set-nav">
        <button class="set-tab active" data-set="setProfile" data-tab="profile"><i class="fa-regular fa-user"></i> My Profile</button>
        <button class="set-tab" data-set="setSecurity" data-tab="security"><i class="fa-solid fa-lock"></i> Security</button>

    </div>

    <div class="glass-card card set-body is-loading" id="setBody">
        {{-- ================= MY PROFILE (API) ================= --}}
        <div class="set-panel active" id="setProfile">
            <div class="sp-hero">
                <div class="sp-avatar" id="spAvatar">
                    <b id="spInitial">?</b>
                    <label title="Change photo"><i class="fa-solid fa-camera"></i><input type="file" id="spPhoto" accept="image/png,image/jpeg,image/webp" hidden></label>
                </div>
                <div class="sp-info">
                    <h3 id="spName">Loading…</h3>
                    <span class="role-pill gold" id="spRole">—</span>
                    <div class="meta">
                        <span><i class="fa-regular fa-envelope"></i><span id="spEmail">—</span></span>
                        <span><i class="fa-solid fa-phone"></i><span id="spPhone">—</span></span>
                    </div>
                </div>
                <div class="sp-photo-acts">
                    <label class="adm-btn adm-btn-gold sm" for="spPhoto" style="cursor:pointer"><i class="fa-solid fa-camera"></i> Change Photo</label>
                    <button type="button" class="adm-btn adm-btn-ghost sm" id="spPhotoRemove" hidden><i class="fa-regular fa-trash-can"></i> Remove</button>
                </div>
            </div>

            <div class="sp-stats">
                <div class="sp-stat"><small>Member since</small><b id="spSince">—</b></div>
                <div class="sp-stat"><small>Last login</small><b id="spLast">—</b></div>
                <div class="sp-stat"><small>Total logins</small><b id="spLogins">—</b></div>
            </div>

            <form class="adm-form" id="profileForm" novalidate>
                <p class="sp-sub">Account</p>
                <div class="f-grid">
                    <div class="f-group" data-field="name"><label>Full Name <b class="req">*</b></label><input type="text" name="name" required minlength="3" maxlength="100"></div>
                    <div class="f-group" data-field="email"><label>Email <b class="req">*</b> <small class="f-hint">(used for login)</small></label><input type="email" name="email" required maxlength="150"></div>
                    <div class="f-group" data-field="phone"><label>Phone</label><input type="tel" name="phone" inputmode="numeric" maxlength="10" placeholder="10-digit mobile"></div>
                    <div class="f-group"><label>Role</label><input type="text" id="spRoleInput" disabled></div>
                </div>
                <p class="sp-sub">Personal</p>
                <div class="f-grid">
                    <div class="f-group" data-field="gender"><label>Gender</label><select name="gender"><option value="">Select</option></select></div>
                    <div class="f-group" data-field="dob"><label>Date of Birth</label><input type="date" name="dob" max="{{ now()->subDay()->format('Y-m-d') }}"></div>
                    <div class="f-group" data-field="occupation"><label>Designation</label><input type="text" name="occupation" maxlength="100" placeholder="e.g. Managing Partner"></div>
                    <div class="f-group" data-field="city"><label>City</label><input type="text" name="city" maxlength="80"></div>
                    <div class="f-group" data-field="state"><label>State</label><select name="state"><option value="">Select state</option></select></div>
                    <div class="f-group" data-field="pincode"><label>Pincode</label><input type="text" name="pincode" inputmode="numeric" maxlength="6"></div>
                    <div class="f-group full" data-field="address"><label>Address</label><textarea name="address" rows="2" maxlength="500"></textarea></div>
                </div>
                <div class="sp-actions">
                    <button type="submit" class="adm-btn adm-btn-gold" id="spSave"><i class="fa-solid fa-check"></i> Save Changes</button>
                    <button type="button" class="adm-btn adm-btn-ghost" id="spReset">Reset</button>
                </div>
            </form>
        </div>

        {{-- ================= SECURITY (API) ================= --}}
        <div class="set-panel" id="setSecurity">
            <h4>Change Password</h4>
            <form class="adm-form" id="passwordForm" novalidate>
                <div class="f-grid">
                    <div class="f-group full" data-field="current_password"><label>Current Password <b class="req">*</b></label>
                        <div class="pw-wrap"><input type="password" name="current_password" required autocomplete="current-password"><button type="button" class="pw-eye" aria-label="Show"><i class="fa-regular fa-eye"></i></button></div></div>
                    <div class="f-group" data-field="password"><label>New Password <b class="req">*</b></label>
                        <div class="pw-wrap"><input type="password" name="password" required autocomplete="new-password"><button type="button" class="pw-eye" aria-label="Show"><i class="fa-regular fa-eye"></i></button></div>
                        <div class="pw-meter"><i id="pwMeter"></i></div>
                        <small class="f-hint">8+ characters with uppercase, lowercase and a number</small></div>
                    <div class="f-group" data-field="password_confirmation"><label>Confirm Password <b class="req">*</b></label>
                        <div class="pw-wrap"><input type="password" name="password_confirmation" required autocomplete="new-password"><button type="button" class="pw-eye" aria-label="Show"><i class="fa-regular fa-eye"></i></button></div></div>
                </div>
                <label class="sp-check"><input type="checkbox" name="logout_others" value="1" checked> Log out from all other devices</label>
                <button type="submit" class="adm-btn adm-btn-gold" id="pwSave"><i class="fa-solid fa-key"></i> Update Password</button>
            </form>

            <h4 style="margin-top:30px">Recent Login Activity</h4>
            <ul class="sp-logins" id="spLoginList"><li><small>Loading…</small></li></ul>
        </div>

      
     
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ===== MY PROFILE + PASSWORD – API (Bearer token) ===== */
document.addEventListener('auth:ready', function (ev) {
    if (!window.API) return;
    const me    = ev.detail;
    const esc   = API.esc;
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const $     = id => document.getElementById(id);
    const pf    = $('profileForm'), pw = $('passwordForm');
    const pel   = n => pf.elements.namedItem(n);
    let data = null, removePhoto = false;

    /* ---- tab URL se: ?tab=security ---- */
    const openTab = t => {
        const btn = document.querySelector(`.set-tab[data-tab="${t}"]`);
        if (!btn || getComputedStyle(btn).display === 'none') return;
        btn.click();
    };
    openTab(new URLSearchParams(location.search).get('tab') || (location.hash || '').slice(1));
    document.querySelectorAll('.set-tab').forEach(b => b.addEventListener('click', () => {
        const u = new URL(location.href); u.searchParams.set('tab', b.dataset.tab); history.replaceState(null, '', u);
    }));

    /* ---- render ---- */
    function setAvatar(url, name) {
        const box = $('spAvatar');
        box.querySelector('img')?.remove();
        $('spInitial').textContent = (name || '?').replace(/^adv\.?\s+/i, '').charAt(0).toUpperCase();
        if (url) box.insertAdjacentHTML('afterbegin', `<img src="${esc(url)}" alt="">`);
        $('spPhotoRemove').hidden = !url;
    }
    function render(u) {
        const meta = API.roleMeta(u.role);
        $('spName').textContent = u.name;
        $('spRole').className = 'role-pill ' + meta.cls;
        $('spRole').innerHTML = `<i class="fa-solid ${meta.icon}"></i> ${esc(u.role_label || meta.label)}`;
        $('spEmail').textContent = u.email;
        $('spPhone').textContent = u.phone ? '+91 ' + u.phone : '—';
        $('spSince').textContent = u.created_at || '—';
        $('spLast').textContent = u.last_login_at || 'First login';
        $('spLogins').textContent = u.logins_count ?? 0;
        $('spRoleInput').value = u.role_label || meta.label;
        setAvatar(u.photo_url, u.name);

        const p = u.user_profile || {};
        ['name', 'email', 'phone'].forEach(k => { pel(k).value = u[k] || ''; });
        ['gender', 'dob', 'occupation', 'city', 'state', 'pincode', 'address'].forEach(k => {
            const el = pel(k), val = p[k] || '';
            if (el.tagName === 'SELECT' && val && ![...el.options].some(o => o.value === val)) el.insertAdjacentHTML('beforeend', `<option>${esc(val)}</option>`);
            el.value = val;
        });
        removePhoto = false; $('spPhoto').value = '';
    }
    function renderLogins(list) {
        $('spLoginList').innerHTML = (list || []).length ? list.map(l => `<li>
            <span class="ic ${l.status === 'success' ? '' : 'bad'}"><i class="fa-solid ${l.status === 'success' ? 'fa-right-to-bracket' : 'fa-triangle-exclamation'}"></i></span>
            <div><b>${l.status === 'success' ? 'Logged in' : 'Failed / blocked attempt'}</b><small>${esc(l.device || 'Unknown device')} · ${esc(l.ip || '')}</small></div>
            <small title="${esc(l.at)}">${esc(l.ago)}</small></li>`).join('') : '<li><small>No login activity yet.</small></li>';
    }
    // topbar / sidebar ka naam + photo turant update
    function syncHeader(u) {
        document.querySelectorAll('[data-user="name"]').forEach(el => { el.textContent = u.name; });
        document.querySelectorAll('[data-user="email"]').forEach(el => { el.textContent = u.email; });
        document.querySelectorAll('[data-user-avatar]').forEach(el => {
            el.innerHTML = u.photo_url ? `<img src="${esc(u.photo_url)}" alt="">` : `<b>${esc(u.name.charAt(0).toUpperCase())}</b>`;
        });
        try {
            [localStorage, sessionStorage].forEach(s => { if (s.getItem('lx_user')) s.setItem('lx_user', JSON.stringify(Object.assign(JSON.parse(s.getItem('lx_user')), u))); });
        } catch (e) {}
    }

    API.get('/profile').then(r => {
        pel('gender').insertAdjacentHTML('beforeend', (r.genders || []).map(g => `<option>${esc(g)}</option>`).join(''));
        pel('state').insertAdjacentHTML('beforeend', (r.states || []).map(s => `<option>${esc(s)}</option>`).join(''));
        data = r.data; render(data); renderLogins(r.recent_logins);
    }).catch(err => toast(err.message, true)).finally(() => $('setBody').classList.remove('is-loading'));

    /* ---- photo ---- */
    $('spPhoto').addEventListener('change', e => {
        const f = e.target.files[0]; if (!f) return;
        if (f.size > 2 * 1048576) { toast('Photo must be smaller than 2MB.', true); e.target.value = ''; return; }
        removePhoto = false;
        setAvatar(URL.createObjectURL(f), pel('name').value);
        toast('Click "Save Changes" to upload the new photo.');
    });
    $('spPhotoRemove').addEventListener('click', () => { removePhoto = true; $('spPhoto').value = ''; setAvatar(null, pel('name').value); toast('Photo will be removed when you save.'); });

    ['phone', 'pincode'].forEach(n => pel(n).addEventListener('input', e => { e.target.value = e.target.value.replace(/\D/g, '').slice(0, n === 'phone' ? 10 : 6); }));
    [pf, pw].forEach(f => f.addEventListener('input', e => { const g = e.target.closest('.has-error'); if (g) { g.classList.remove('has-error'); g.querySelector('.f-error')?.remove(); } }));
    $('spReset').addEventListener('click', () => { if (data) { API.clearErrors(pf); render(data); } });

    /* ---- save profile ---- */
    pf.addEventListener('submit', async e => {
        e.preventDefault();
        API.clearErrors(pf);
        const errs = {};
        if (pel('name').value.trim().length < 3) errs.name = ['Please enter your full name.'];
        if (!pel('email').value || !pel('email').checkValidity()) errs.email = ['Please enter a valid email.'];
        if (pel('phone').value && pel('phone').value.length !== 10) errs.phone = ['Phone must be 10 digits.'];
        if (pel('pincode').value && pel('pincode').value.length !== 6) errs.pincode = ['Pincode must be 6 digits.'];
        if (Object.keys(errs).length) { API.showErrors(pf, errs)?.scrollIntoView({ behavior: 'smooth', block: 'center' }); return; }

        const fd = new FormData(pf);
        if ($('spPhoto').files[0]) fd.append('photo', $('spPhoto').files[0]);
        if (removePhoto) fd.append('remove_photo', '1');
        const btn = $('spSave'); btn.disabled = true; btn.classList.add('loading');
        try {
            const r = await API.post('/profile', fd);
            data = Object.assign({}, data, r.data);
            render(data); syncHeader(data);
            toast(r.message);
        } catch (err) {
            if (err.errors?.photo) toast(err.errors.photo[0], true);
            API.showErrors(pf, err.errors)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            toast(err.message, true);
        } finally { btn.disabled = false; btn.classList.remove('loading'); }
    });

    /* ---- password ---- */
    pw.querySelectorAll('.pw-eye').forEach(b => b.addEventListener('click', () => {
        const i = b.parentElement.querySelector('input'), show = i.type === 'password';
        i.type = show ? 'text' : 'password';
        b.innerHTML = `<i class="fa-regular fa-eye${show ? '-slash' : ''}"></i>`;
    }));
    pw.elements.namedItem('password').addEventListener('input', e => {
        const v = e.target.value; let s = 0;
        if (v.length >= 8) s++; if (/[a-z]/.test(v) && /[A-Z]/.test(v)) s++; if (/\d/.test(v)) s++; if (/[^a-z0-9]/i.test(v)) s++;
        $('pwMeter').style.width = (v ? Math.max(s, 1) * 25 : 0) + '%'; $('pwMeter').dataset.l = s;
    });
    pw.addEventListener('submit', async e => {
        e.preventDefault();
        API.clearErrors(pw);
        const g = n => pw.elements.namedItem(n).value;
        const errs = {};
        if (!g('current_password')) errs.current_password = ['Please enter your current password.'];
        if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(g('password'))) errs.password = ['8+ characters with uppercase, lowercase and a number.'];
        if (g('password') !== g('password_confirmation')) errs.password_confirmation = ['Passwords do not match.'];
        if (Object.keys(errs).length) { API.showErrors(pw, errs); return; }

        const btn = $('pwSave'); btn.disabled = true; btn.classList.add('loading');
        try {
            const r = await API.post('/profile/password', {
                current_password: g('current_password'), password: g('password'), password_confirmation: g('password_confirmation'),
                logout_others: pw.elements.namedItem('logout_others').checked,
            });
            toast(r.message);
            pw.reset(); $('pwMeter').style.width = 0;
        } catch (err) { API.showErrors(pw, err.errors); toast(err.message, true); }
        finally { btn.disabled = false; btn.classList.remove('loading'); }
    });
});
</script>
@endpush