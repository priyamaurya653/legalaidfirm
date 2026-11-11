@extends('frontlayout.app')
@php
    $editId    = $id ?? null;
    $heardFrom = \App\Http\Requests\UserRegisterRequest::HEARD_FROM;
@endphp
@section('title', $editId ? 'Edit User' : 'Create User')
@section('page_title', $editId ? 'Edit User' : 'Create User')
@section('require_role', 'admin')

@section('page_actions')
    <a href="{{ route('admin.users') }}" class="adm-btn adm-btn-ghost"><i class="fa-solid fa-users"></i> My Users</a>
@endsection

@push('styles')
<style>
/* ===== ADMIN – CREATE / EDIT USER (isi file me) ===== */
.uf-grid { display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 24px; align-items: start; }
.uf-sec { padding: 26px; margin-bottom: 22px; animation: ufUp .5s ease both; }
.uf-sec:nth-child(2) { animation-delay: .06s; } .uf-sec:nth-child(3) { animation-delay: .12s; } .uf-sec:nth-child(4) { animation-delay: .18s; }
@keyframes ufUp { from { opacity: 0; transform: translateY(16px); } }
.uf-head { display: flex; align-items: center; gap: 14px; margin-bottom: 20px; }
.uf-num { width: 42px; height: 42px; flex-shrink: 0; border-radius: 12px; display: grid; place-items: center; font-weight: 600; color: #fff; background: linear-gradient(135deg, #7aa5ff, #3f6fd8); box-shadow: 0 8px 20px rgba(91,141,239,.3); }
.uf-head h4 { color: #fff; font-size: 17px; margin: 0; }
.uf-head small { color: var(--muted); font-size: 12.5px; }
.req { color: #ff7a7a; font-weight: 600; }
.uf-photo { display: flex; align-items: center; gap: 16px; margin-bottom: 18px; }
.uf-av { width: 76px; height: 76px; flex-shrink: 0; border-radius: 20px; overflow: hidden; display: grid; place-items: center; font-size: 28px; color: #5fe0a5; background: rgba(62,207,142,.1); border: 1px dashed rgba(62,207,142,.4); }
.uf-av img { width: 100%; height: 100%; object-fit: cover; }
.pw-wrap { position: relative; }
.pw-wrap input { padding-right: 44px !important; }
.pw-eye { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: 0; color: var(--muted); cursor: pointer; }
.pw-gen { font-size: 12px; color: var(--gold-light); background: none; border: 0; cursor: pointer; padding: 0; margin-top: 6px; }
.uf-side { position: sticky; top: 90px; padding: 22px; }
.uf-side .ic { width: 58px; height: 58px; border-radius: 16px; display: grid; place-items: center; font-size: 24px; color: #062b1a; background: linear-gradient(135deg, #7ff0b8, #2fbf7d); margin-bottom: 14px; }
.uf-side h4 { color: #fff; font-size: 18px; margin-bottom: 6px; }
.uf-side ul { list-style: none; padding: 0; margin: 14px 0 18px; display: grid; gap: 8px; }
.uf-side li { display: flex; gap: 8px; font-size: 13px; color: var(--muted); }
.uf-side li i { color: #5fe0a5; margin-top: 3px; }
.uf-side .adm-btn { margin-bottom: 10px; }
.uf-status { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 14px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 16px; }
.uf-status h6 { color: #fff; font-size: 14px; margin: 0; }
.uf-status small { color: var(--muted); font-size: 12px; }
.uf-done { display: none; text-align: center; padding: 40px 20px; }
.uf-done.show { display: block; animation: ufUp .5s ease both; }
.uf-done .ok { width: 84px; height: 84px; margin: 0 auto 18px; border-radius: 50%; display: grid; place-items: center; font-size: 36px; color: #062b1a; background: linear-gradient(135deg, #7ff0b8, #2fbf7d); box-shadow: 0 0 0 12px rgba(62,207,142,.1); }
.uf-done h3 { color: #fff; font-size: 24px; margin-bottom: 8px; }
.uf-done p { color: var(--muted); margin-bottom: 18px; }
.uf-cred { display: inline-grid; gap: 6px; text-align: left; padding: 14px 18px; border-radius: 14px; background: rgba(255,255,255,.03); border: 1px solid var(--border); margin-bottom: 22px; font-size: 14px; color: #ddd; }
.uf-cred b { color: var(--gold-light); font-family: ui-monospace, Consolas, monospace; }
.uf-done .acts { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
form.is-loading .uf-sec, form.is-loading .uf-side { opacity: .45; pointer-events: none; }
@media (max-width: 1100px) { .uf-grid { grid-template-columns: 1fr; } .uf-side { position: static; } }
@media (max-width: 575px) { .uf-sec { padding: 20px 16px; } }
</style>
@endpush

@section('content')

<form id="userForm" data-edit-id="{{ $editId }}" novalidate enctype="multipart/form-data">
    <div class="uf-grid">
        <div>
            {{-- 01 Personal --}}
            <div class="glass-card uf-sec">
                <div class="uf-head"><span class="uf-num">01</span><div><h4>Personal Details</h4><small>Client's basic information</small></div></div>
                <div class="uf-photo">
                    <span class="uf-av" id="ufAv"><i class="fa-solid fa-user"></i></span>
                    <div>
                        <label class="adm-btn adm-btn-ghost sm" style="cursor:pointer"><i class="fa-solid fa-camera"></i> Upload Photo<input type="file" name="photo" accept="image/png,image/jpeg,image/webp" hidden></label>
                        <small class="f-hint" style="display:block">Optional · max 2MB</small>
                    </div>
                </div>
                <div class="f-grid">
                    <div class="f-group full" data-field="name"><label>Full Name <b class="req">*</b></label><input type="text" name="name" required minlength="3" maxlength="100" placeholder="As on Aadhaar"></div>
                    <div class="f-group" data-field="gender"><label>Gender <b class="req">*</b></label><select name="gender" required><option value="">Select</option></select></div>
                    <div class="f-group" data-field="dob"><label>Date of Birth</label><input type="date" name="dob" max="{{ now()->subDay()->format('Y-m-d') }}"></div>
                    <div class="f-group full" data-field="occupation"><label>Occupation</label><input type="text" name="occupation" maxlength="100" placeholder="e.g. Business, Service, Student"></div>
                </div>
            </div>

            {{-- 02 Contact & Address --}}
            <div class="glass-card uf-sec">
                <div class="uf-head"><span class="uf-num">02</span><div><h4>Contact & Address</h4></div></div>
                <div class="f-grid">
                    <div class="f-group" data-field="phone"><label>Mobile <b class="req">*</b></label><input type="tel" name="phone" required inputmode="numeric" maxlength="10" placeholder="10-digit mobile"></div>
                    <div class="f-group" data-field="alt_phone"><label>Alternate Mobile</label><input type="tel" name="alt_phone" inputmode="numeric" maxlength="10"></div>
                    <div class="f-group full" data-field="address"><label>Address <b class="req">*</b></label><textarea name="address" rows="2" required maxlength="500" placeholder="House no., street, area"></textarea></div>
                    <div class="f-group" data-field="city"><label>City <b class="req">*</b></label><input type="text" name="city" required maxlength="80"></div>
                    <div class="f-group" data-field="state"><label>State <b class="req">*</b></label><select name="state" required><option value="">Select state</option></select></div>
                    <div class="f-group" data-field="pincode"><label>Pincode <b class="req">*</b></label><input type="text" name="pincode" required inputmode="numeric" maxlength="6"></div>
                </div>
            </div>

            {{-- 03 Case --}}
            <div class="glass-card uf-sec">
                <div class="uf-head"><span class="uf-num">03</span><div><h4>Legal Matter</h4><small>What the client needs help with</small></div></div>
                <div class="f-grid">
                    <div class="f-group" data-field="interested_in"><label>Case / Practice Area <b class="req">*</b></label><select name="interested_in" required><option value="">Loading…</option></select></div>
                    <div class="f-group" data-field="heard_from"><label>Client Source</label>
                        <select name="heard_from"><option value="">Select</option>@foreach($heardFrom as $h)<option>{{ $h }}</option>@endforeach<option>Walk-in</option></select></div>
                </div>
            </div>

            {{-- 04 Login --}}
            <div class="glass-card uf-sec">
                <div class="uf-head"><span class="uf-num">04</span><div><h4>Login Details</h4><small>User logs in with this email & password → User Dashboard</small></div></div>
                <div class="f-grid">
                    <div class="f-group full" data-field="email"><label>Email <b class="req">*</b> <small class="f-hint">(login ID)</small></label><input type="email" name="email" required maxlength="150" placeholder="client@example.com"></div>
                    <div class="f-group" data-field="password"><label>Password {!! $editId ? '<small class="f-hint">(leave blank to keep)</small>' : '<b class="req">*</b>' !!}</label>
                        <div class="pw-wrap"><input type="password" name="password" autocomplete="new-password" {{ $editId ? '' : 'required' }}><button type="button" class="pw-eye" aria-label="Show"><i class="fa-regular fa-eye"></i></button></div>
                        <button type="button" class="pw-gen" id="ufGen"><i class="fa-solid fa-wand-magic-sparkles"></i> Generate strong password</button></div>
                    <div class="f-group" data-field="password_confirmation"><label>Confirm Password</label>
                        <div class="pw-wrap"><input type="password" name="password_confirmation" autocomplete="new-password"><button type="button" class="pw-eye" aria-label="Show"><i class="fa-regular fa-eye"></i></button></div></div>
                </div>
            </div>
        </div>

        <aside class="glass-card uf-side">
            <div class="ic"><i class="fa-solid {{ $editId ? 'fa-user-pen' : 'fa-user-plus' }}"></i></div>
            <h4>{{ $editId ? 'Update User' : 'New User' }}</h4>
            <p class="muted-sm">This user will be linked to <b class="gold" data-user="name">you</b>. Superadmin can see which advocate each user belongs to.</p>
            <ul>
                <li><i class="fa-solid fa-circle-check"></i> Role is set to <b>User</b> automatically</li>
                <li><i class="fa-solid fa-circle-check"></i> Login with email & password</li>
                <li><i class="fa-solid fa-circle-check"></i> Lands on the User Dashboard</li>
                <li><i class="fa-solid fa-circle-check"></i> Password: 8+ chars, Aa + number</li>
            </ul>
            <div class="uf-status">
                <div><h6 id="ufStatusText">Active</h6><small>Can log in</small></div>
                <label class="switch"><input type="checkbox" id="ufStatus" checked><span></span></label>
            </div>
            <button type="submit" class="adm-btn adm-btn-gold w-100" id="ufSave"><i class="fa-solid fa-check"></i> <span>{{ $editId ? 'Save Changes' : 'Create User' }}</span></button>
            <a href="{{ route('admin.users') }}" class="adm-btn adm-btn-ghost w-100">Cancel</a>
        </aside>
    </div>
</form>

{{-- success (create ke baad) --}}
<div class="glass-card uf-done" id="ufDone">
    <div class="ok"><i class="fa-solid fa-check"></i></div>
    <h3>User Created!</h3>
    <p>Share these login details with your client.</p>
    <div class="uf-cred">
        <span>Login URL: <b>{{ route('login') }}</b></span>
        <span>Email: <b id="ufCredEmail"></b></span>
        <span>Password: <b id="ufCredPass"></b></span>
    </div>
    <div class="acts">
        <button class="adm-btn adm-btn-ghost" id="ufCopy"><i class="fa-regular fa-copy"></i> Copy Details</button>
        <a href="{{ route('admin.users.create') }}" class="adm-btn adm-btn-ghost"><i class="fa-solid fa-user-plus"></i> Create Another</a>
        <a href="{{ route('admin.users') }}" class="adm-btn adm-btn-gold"><i class="fa-solid fa-users"></i> My Users</a>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ===== ADMIN – CREATE / EDIT USER – API (Bearer token) ===== */
document.addEventListener('auth:ready', function (ev) {
    if (!window.API || ev.detail.role !== 'admin') return;
    const form = document.getElementById('userForm'), id = form.dataset.editId, $ = i => document.getElementById(i);
    const el = n => form.elements.namedItem(n), esc = API.esc;
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const LIST = @json(route('admin.users'));
    const LOGIN = @json(route('login'));
    const addOpt = (sel, val) => { if (val && ![...sel.options].some(o => o.value === val)) sel.insertAdjacentHTML('beforeend', `<option>${esc(val)}</option>`); };

    const ready = API.get('/my-users/options').then(o => {
        el('gender').insertAdjacentHTML('beforeend', (o.genders || []).map(g => `<option>${esc(g)}</option>`).join(''));
        el('state').insertAdjacentHTML('beforeend', (o.states || []).map(s => `<option>${esc(s)}</option>`).join(''));
        el('interested_in').innerHTML = '<option value="">Select practice area</option>' + (o.practice_areas || []).map(p => `<option>${esc(p)}</option>`).join('') + '<option>Other</option>';
    }).catch(err => toast(err.message, true));

    ['phone', 'alt_phone', 'pincode'].forEach(n => el(n).addEventListener('input', e => { e.target.value = e.target.value.replace(/\D/g, '').slice(0, n === 'pincode' ? 6 : 10); }));
    form.addEventListener('input', e => { const g = e.target.closest('.has-error'); if (g) { g.classList.remove('has-error'); g.querySelector('.f-error')?.remove(); } });
    form.addEventListener('change', e => { const g = e.target.closest('.has-error'); if (g) { g.classList.remove('has-error'); g.querySelector('.f-error')?.remove(); } });
    $('ufStatus').addEventListener('change', e => { $('ufStatusText').textContent = e.target.checked ? 'Active' : 'Inactive'; });
    form.querySelectorAll('.pw-eye').forEach(b => b.addEventListener('click', () => {
        const i = b.parentElement.querySelector('input'), s = i.type === 'password'; i.type = s ? 'text' : 'password';
        b.innerHTML = `<i class="fa-regular fa-eye${s ? '-slash' : ''}"></i>`;
    }));
    $('ufGen').addEventListener('click', () => {
        const pick = s => s[crypto.getRandomValues(new Uint32Array(1))[0] % s.length];
        let p = pick('ABCDEFGHJKLMNPQRSTUVWXYZ') + pick('abcdefghijkmnpqrstuvwxyz') + pick('23456789') + pick('@#$%&*');
        for (let i = 0; i < 6; i++) p += pick('ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789');
        el('password').value = el('password_confirmation').value = p;
        form.querySelectorAll('.pw-wrap input').forEach(i => { i.type = 'text'; });
        toast('Strong password generated – note it down for the user.');
    });
    el('photo').addEventListener('change', e => {
        const f = e.target.files[0]; if (!f) return;
        if (f.size > 2 * 1048576) { toast('Photo must be smaller than 2MB.', true); e.target.value = ''; return; }
        $('ufAv').innerHTML = `<img src="${URL.createObjectURL(f)}" alt="">`;
    });

    if (id) {
        form.classList.add('is-loading');
        Promise.all([API.get('/my-users/' + id), ready]).then(([r]) => {
            const u = r.data, p = u.user_profile || {};
            ['name', 'email', 'phone'].forEach(k => { el(k).value = u[k] || ''; });
            ['gender', 'dob', 'occupation', 'alt_phone', 'address', 'city', 'state', 'pincode', 'interested_in', 'heard_from'].forEach(k => {
                if (el(k).tagName === 'SELECT') addOpt(el(k), p[k]);
                el(k).value = p[k] || '';
            });
            $('ufStatus').checked = u.status === 'active';
            $('ufStatusText').textContent = u.status === 'active' ? 'Active' : 'Inactive';
            if (u.photo_url) $('ufAv').innerHTML = `<img src="${esc(u.photo_url)}" alt="">`;
        }).catch(err => { toast(err.message, true); if (err.status === 404) setTimeout(() => location.replace(LIST), 1200); })
          .finally(() => form.classList.remove('is-loading'));
    }

    form.addEventListener('submit', async e => {
        e.preventDefault();
        API.clearErrors(form);
        const v = n => el(n).value.trim(), errs = {};
        if (v('name').length < 3) errs.name = ['Please enter the full name.'];
        if (!v('gender')) errs.gender = ['Please select gender.'];
        if (!/^[6-9]\d{9}$/.test(v('phone'))) errs.phone = ['Enter a valid 10-digit mobile number.'];
        if (v('alt_phone') && !/^[6-9]\d{9}$/.test(v('alt_phone'))) errs.alt_phone = ['Enter a valid alternate number.'];
        if (!v('address')) errs.address = ['Please enter the address.'];
        if (!v('city')) errs.city = ['Please enter the city.'];
        if (!v('state')) errs.state = ['Please select the state.'];
        if (!/^\d{6}$/.test(v('pincode'))) errs.pincode = ['Pincode must be 6 digits.'];
        if (!v('interested_in')) errs.interested_in = ['Please select the case / practice area.'];
        if (!v('email') || !el('email').checkValidity()) errs.email = ['Enter a valid email address.'];
        const pw = el('password').value;
        if ((!id || pw) && !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(pw)) errs.password = ['8+ characters with uppercase, lowercase and a number.'];
        if (pw !== el('password_confirmation').value) errs.password_confirmation = ['Passwords do not match.'];
        if (Object.keys(errs).length) { API.showErrors(form, errs)?.scrollIntoView({ behavior: 'smooth', block: 'center' }); return; }

        const fd = API.formData(form);
        fd.set('status', $('ufStatus').checked ? 'active' : 'inactive');
        if (id && !pw) { fd.delete('password'); fd.delete('password_confirmation'); }

        const btn = $('ufSave'); btn.disabled = true; btn.classList.add('loading');
        try {
            const r = await API.post(id ? '/my-users/' + id : '/my-users', fd);
            toast(r.message);
            if (id) { setTimeout(() => { location.href = LIST; }, 700); return; }
            // create ke baad login details dikhao
            $('ufCredEmail').textContent = r.data.email;
            $('ufCredPass').textContent = pw;
            form.hidden = true; $('ufDone').classList.add('show');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } catch (err) {
            API.showErrors(form, err.errors)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            toast(err.message, true);
        } finally { btn.disabled = false; btn.classList.remove('loading'); }
    });

    $('ufCopy').addEventListener('click', () => {
        const t = `Lexora Legal – Login\nURL: ${LOGIN}\nEmail: ${$('ufCredEmail').textContent}\nPassword: ${$('ufCredPass').textContent}`;
        navigator.clipboard?.writeText(t).then(() => toast('Login details copied.')).catch(() => toast('Copy failed – please copy manually.', true));
    });

    if (!id) setTimeout(() => el('name').focus(), 200);
});
</script>
@endpush