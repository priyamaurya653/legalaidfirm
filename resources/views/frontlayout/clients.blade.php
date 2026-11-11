@extends('frontlayout.app')
@section('title', 'Clients')
@section('page_title', 'Clients')
{{-- superadmin: sab clients · admin: apne assigned clients – admin ka banaya client usi ko assign hota hai --}}

@section('page_actions')
    <button class="adm-btn adm-btn-gold" id="clAddBtn"><i class="fa-solid fa-user-plus"></i> Add Client</button>
@endsection

@push('styles')
<style>
/* ===== CLIENTS (isi file me) ===== */
.cl-toolbar { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between; }
.cl-toolbar .right { display: flex; flex-wrap: wrap; gap: 10px; }
.cl-select { height: 40px; max-width: 210px; padding: 0 34px 0 14px; border-radius: 12px; color: #ddd; font: inherit; font-size: 13px; cursor: pointer; appearance: none;
    background: rgba(255,255,255,.04) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath fill='%23d4af37' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E") no-repeat right 14px center; border: 1px solid var(--border); }
.cl-select option { background: #111; }
.cl-adv { font-size: 12px; color: var(--muted); }
.cl-adv b { color: #ccc; font-weight: 500; }
.adm-table tr.removing { opacity: 0; transform: translateX(30px); transition: .35s; }
.adm-table tr.inactive .t-title { opacity: .55; }

/* form modal */
.cl-sec-title { display: flex; align-items: center; gap: 10px; margin: 6px 0 14px; font-size: 13px; letter-spacing: 1.5px; text-transform: uppercase; color: var(--gold-light); }
.cl-login-note { display: flex; gap: 10px; align-items: center; padding: 10px 14px; margin: -4px 0 14px; border-radius: 12px; font-size: 12.5px; color: #ccc; background: rgba(91,141,239,.08); border: 1px solid rgba(91,141,239,.3); }
.cl-login-note i { color: #8fb4ff; }
.cl-pw { position: relative; }
.cl-pw input { padding-right: 42px !important; }
.cl-pw button { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: 0; color: var(--muted); cursor: pointer; }
.cl-gen { font-size: 12px; color: var(--gold-light); background: none; border: 0; cursor: pointer; padding: 0; margin-top: 6px; }
.cl-cred { display: grid; gap: 8px; padding: 16px; border-radius: 14px; background: rgba(255,255,255,.03); border: 1px solid var(--border); font-size: 14px; color: #ddd; margin-bottom: 18px; }
.cl-cred b { color: var(--gold-light); font-family: ui-monospace, Consolas, monospace; word-break: break-all; }
.cl-login-pill { font-size: 11px; padding: 2px 8px; border-radius: 10px; margin-left: 6px; vertical-align: middle; }
.cl-login-pill.on { color: #5fe0a5; background: rgba(62,207,142,.12); }
.cl-login-pill.off { color: #ffb347; background: rgba(255,179,71,.12); }
.cl-sec-title::after { content: ''; flex: 1; height: 1px; background: linear-gradient(90deg, rgba(212,175,55,.35), transparent); }
.req { color: #ff7a7a; font-weight: 600; }
.cl-kyc { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 20px; }
.f-group label.cl-drop { display: flex; align-items: center; gap: 12px; padding: 14px; margin: 0; border-radius: 14px; cursor: pointer; font-size: inherit; color: inherit; transition: .3s;
    border: 1px dashed rgba(212,175,55,.35); background: rgba(212,175,55,.03); }
.cl-drop:hover { background: rgba(212,175,55,.08); border-color: var(--gold); }
.cl-drop > i { width: 40px; height: 40px; flex-shrink: 0; border-radius: 11px; display: grid; place-items: center; font-size: 17px; color: var(--gold); background: rgba(212,175,55,.1); }
.cl-drop b { display: block; color: #fff; font-size: 13.5px; font-weight: 500; }
.cl-drop small { display: block; color: var(--muted); font-size: 11.5px; word-break: break-all; }
.cl-drop.has-file { border-style: solid; border-color: rgba(62,207,142,.5); background: rgba(62,207,142,.05); }
.cl-drop.has-file > i { color: #5fe0a5; background: rgba(62,207,142,.12); }
.cl-drop input[type=file] { display: none; }
.f-group.has-error .cl-drop { border-color: rgba(255,107,107,.7); }
.cl-now { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin-top: 6px; font-size: 12px; color: var(--muted); }
.cl-now button { background: none; border: 0; padding: 0; font: inherit; font-size: 12px; color: var(--gold-light); cursor: pointer; }
.cl-now button.rm { color: #ff8c8c; }
.cl-doc-list { list-style: none; padding: 0; margin: 10px 0 0; display: grid; gap: 8px; }
.cl-doc { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 12px; border: 1px solid var(--border); background: rgba(255,255,255,.02); }
.cl-doc > i { width: 34px; height: 34px; flex-shrink: 0; border-radius: 9px; display: grid; place-items: center; color: var(--gold); background: rgba(212,175,55,.1); }
.cl-doc.saved > i { color: #5fe0a5; background: rgba(62,207,142,.1); }
.cl-doc .m { flex: 1; min-width: 0; }
.cl-doc .m b { display: block; color: #fff; font-size: 13px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cl-doc .m small { color: var(--muted); font-size: 11px; }
.cl-doc .m input { width: 100%; padding: 5px 9px !important; font-size: 13px !important; border-radius: 8px !important; }
.cl-doc .icon-btn.danger { color: #ff8c8c; }
.cl-more-btn { width: 100%; display: flex; align-items: center; gap: 10px; padding: 12px 16px; margin: 4px 0 16px; border-radius: 12px; cursor: pointer; font: inherit; font-size: 13.5px; color: #ddd; border: 1px dashed rgba(212,175,55,.35); background: rgba(212,175,55,.04); }
.cl-more-btn i:first-child { color: var(--gold); }
.cl-more-btn .caret { margin-left: auto; color: var(--gold); transition: .3s; }
.cl-more-btn.open .caret { transform: rotate(180deg); }
.cl-more { display: none; }
.cl-more.open { display: block; }
#clForm.is-loading { opacity: .5; pointer-events: none; }

/* view modal */
.cv-head { display: flex; align-items: center; gap: 14px; margin-bottom: 18px; }
.cv-head .avatar-txt { width: 56px; height: 56px; font-size: 22px; }
.cv-head h4 { color: #fff; font-size: 20px; margin: 0 0 4px; }
.cv-kv { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px 18px; margin-bottom: 18px; }
.cv-kv small { display: block; color: var(--muted); font-size: 12px; margin-bottom: 2px; }
.cv-kv b { color: #eee; font-size: 13.5px; font-weight: 500; overflow-wrap: anywhere; }
.cv-kv .full { grid-column: 1 / -1; }
.cv-mono { font-family: ui-monospace, Consolas, monospace; letter-spacing: 1px; }
.cv-eye { background: none; border: 0; color: var(--gold-light); cursor: pointer; margin-left: 4px; }
.cv-box { padding: 14px; border-radius: 12px; background: rgba(255,255,255,.03); border: 1px solid var(--border); color: #ddd; font-size: 13.5px; line-height: 1.7; white-space: pre-wrap; overflow-wrap: anywhere; margin-bottom: 18px; }
.cv-files { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 10px; margin-bottom: 18px; }
.cv-file.missing > i { color: #777; background: rgba(255,255,255,.04); }

.cl-del { text-align: center; padding: 10px 6px 4px; }
.cl-del .ring { width: 76px; height: 76px; margin: 0 auto 16px; border-radius: 50%; display: grid; place-items: center; font-size: 30px; color: #ff6b6b; background: rgba(255,107,107,.1); }
.cl-del h4 { color: #fff; font-size: 20px; margin-bottom: 8px; }
.cl-del p { color: var(--muted); font-size: 14px; margin-bottom: 22px; }
.cl-del p b { color: var(--gold-light); }
.cl-del .btns { display: flex; gap: 10px; justify-content: center; }
.adm-btn-danger { color: #fff; background: linear-gradient(135deg, #ff7a7a, #d63c3c); border: 0; }

@media (max-width: 767px) { .cl-toolbar, .cl-toolbar .right { flex-direction: column; align-items: stretch; } .cl-select { max-width: none; } .cl-toolbar .tb-search { width: 100%; } .cv-kv { grid-template-columns: 1fr 1fr; } }
@media (max-width: 575px) { .cl-kyc, .cv-kv { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

<div class="glass-card card">
    <div class="toolbar cl-toolbar">
        <div class="tb-search sm">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Search name, phone, email, city…" id="clSearch" autocomplete="off">
        </div>
        <div class="right">
            <div class="filter-chips" id="clStatus">
                <button class="active" data-f="">All</button>
                <button data-f="active">Active</button>
                <button data-f="inactive">Inactive</button>
            </div>
            <select class="cl-select only-superadmin" id="clAdvFilter" aria-label="Assigned admin"><option value="">All Admins</option><option value="0">Unassigned</option></select>
        </div>
    </div>

    <div class="table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th class="hide-sm">Phone</th>
                    <th class="hide-md">City</th>
                    <th class="hide-md">Case / Assigned</th>
                    <th>Cases</th>
                    <th class="hide-sm">Since</th>
                    <th class="t-right">Action</th>
                </tr>
            </thead>
            <tbody id="clRows"></tbody>
        </table>
    </div>
    <div class="pager" id="clPager"></div>
</div>

@endsection

@push('modals')
{{-- ================= ADD / EDIT ================= --}}
<div class="adm-modal" id="clientModal" aria-hidden="true">
    <div class="adm-modal-box glass-card wide">
        <div class="modal-head">
            <h4><i class="fa-solid fa-user-plus" id="clModalIcon"></i> <span id="clModalTitle">Add Client</span></h4>
            <button class="icon-btn" data-close-modal aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form class="adm-form" id="clForm" novalidate enctype="multipart/form-data">
            <p class="cl-sec-title">Client Details</p>
            <div class="f-grid">
                <div class="f-group" data-field="name"><label>Full Name <b class="req">*</b></label><input type="text" name="name" required minlength="3" maxlength="120" placeholder="Full name as on Aadhaar"></div>
                <div class="f-group" data-field="phone"><label>Phone <b class="req">*</b></label><input type="tel" name="phone" required inputmode="numeric" maxlength="10" placeholder="10-digit mobile"></div>
                <div class="f-group" data-field="email"><label>Email <b class="req">*</b> <small class="f-hint">(login ID)</small></label><input type="email" name="email" required maxlength="150" placeholder="client@example.com"></div>
                <div class="f-group" data-field="city"><label>City</label><input type="text" name="city" maxlength="80" placeholder="e.g. New Delhi"></div>
                <div class="f-group full" data-field="address"><label>Address</label><textarea name="address" rows="2" maxlength="500" placeholder="House no., street, area"></textarea></div>
            </div>

            <p class="cl-sec-title">Login Details</p>
            <div class="cl-login-note"><i class="fa-solid fa-circle-info"></i><span id="clLoginNote">The client logs in with the <b>email above</b> and this password, and lands on their own dashboard.</span></div>
            <div class="f-grid">
                <div class="f-group" data-field="password"><label>Password <b class="req" id="clPwReq">*</b></label>
                    <div class="cl-pw"><input type="password" name="password" autocomplete="new-password" placeholder="8+ chars, Aa + number"><button type="button" data-eye aria-label="Show"><i class="fa-regular fa-eye"></i></button></div>
                    <button type="button" class="cl-gen" id="clGen"><i class="fa-solid fa-wand-magic-sparkles"></i> Generate strong password</button></div>
                <div class="f-group" data-field="password_confirmation"><label>Confirm Password</label>
                    <div class="cl-pw"><input type="password" name="password_confirmation" autocomplete="new-password"><button type="button" data-eye aria-label="Show"><i class="fa-regular fa-eye"></i></button></div></div>
            </div>

            <p class="cl-sec-title">Case & Assignment</p>
            <div class="f-grid">
                <div class="f-group" data-field="practice_area_id"><label>What is the case? <b class="req">*</b></label><select name="practice_area_id" required><option value="">Loading…</option></select></div>
                <div class="f-group" data-field="advocate_id"><label>Assign To (Admin)</label><select name="advocate_id"><option value="">Loading…</option></select><small class="f-hint" id="clSelfHint" hidden><i class="fa-solid fa-circle-info"></i> Assign to yourself or the Super Admin. You will still see clients you created.</small></div>
                <div class="f-group full" data-field="case_details"><label>Case Details</label><textarea name="case_details" rows="3" maxlength="3000" placeholder="Briefly describe the client's legal matter…"></textarea></div>
            </div>

            <p class="cl-sec-title">KYC & Documents</p>
            <div class="f-grid">
                <div class="f-group" data-field="aadhaar_number"><label>Aadhaar Number <b class="req">*</b></label><input type="text" name="aadhaar_number" required inputmode="numeric" maxlength="14" placeholder="XXXX XXXX XXXX" autocomplete="off"></div>
                <div class="f-group" data-field="pan_number"><label>PAN Number</label><input type="text" name="pan_number" maxlength="10" placeholder="ABCDE1234F" autocomplete="off" style="text-transform:uppercase"></div>
            </div>
            <div class="cl-kyc">
                <div class="f-group" data-field="aadhaar_file">
                    <label class="cl-drop" data-drop><i class="fa-solid fa-id-card"></i><span><b>Aadhaar Card <span class="req">*</span></b><small data-label>PDF / JPG / PNG · max 4MB</small></span><input type="file" name="aadhaar_file" accept=".pdf,image/png,image/jpeg,image/webp"></label>
                    <div class="cl-now" id="clAadhaarNow" hidden></div>
                </div>
                <div class="f-group" data-field="pan_file">
                    <label class="cl-drop" data-drop><i class="fa-regular fa-address-card"></i><span><b>PAN Card</b><small data-label>Optional · PDF / JPG / PNG · max 4MB</small></span><input type="file" name="pan_file" accept=".pdf,image/png,image/jpeg,image/webp"></label>
                    <div class="cl-now" id="clPanNow" hidden></div>
                    <input type="hidden" name="remove_pan_file" value="0">
                </div>
            </div>
            <div class="f-group" data-field="documents" style="margin-bottom:20px">
                <label class="cl-drop" id="clDocsDrop"><i class="fa-solid fa-cloud-arrow-up"></i><span><b>Other Documents</b><small>Click or drop · PDF, DOC, JPG, PNG · max 10MB each · up to 10</small></span><input type="file" id="clDocsInput" multiple accept=".pdf,.doc,.docx,image/png,image/jpeg,image/webp"></label>
                <ul class="cl-doc-list" id="clNewDocs"></ul>
                <ul class="cl-doc-list" id="clSavedDocs"></ul>
            </div>

            <button type="button" class="cl-more-btn" id="clMoreBtn"><i class="fa-solid fa-sliders"></i> More Details <small class="muted-sm">— alternate phone, state, pincode, status</small><i class="fa-solid fa-chevron-down caret"></i></button>
            <div class="cl-more" id="clMore">
                <div class="f-grid">
                    <div class="f-group" data-field="alt_phone"><label>Alternate Phone</label><input type="tel" name="alt_phone" inputmode="numeric" maxlength="10" placeholder="Optional"></div>
                    <div class="f-group" data-field="state"><label>State</label><select name="state"><option value="">Select state</option></select></div>
                    <div class="f-group" data-field="pincode"><label>Pincode</label><input type="text" name="pincode" inputmode="numeric" maxlength="6" placeholder="6 digits"></div>
                    <div class="f-group" data-field="status"><label>Status</label><select name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                </div>
            </div>

            <div class="modal-foot">
                <button type="button" class="adm-btn adm-btn-ghost" data-close-modal>Cancel</button>
                <button type="submit" class="adm-btn adm-btn-gold" id="clSaveBtn"><i class="fa-solid fa-check"></i> <span>Save Client</span></button>
            </div>
        </form>
    </div>
</div>

{{-- ================= VIEW ================= --}}
<div class="adm-modal" id="clViewModal" aria-hidden="true">
    <div class="adm-modal-box glass-card wide">
        <div class="modal-head">
            <h4><i class="fa-regular fa-id-badge"></i> Client Profile</h4>
            <button class="icon-btn" data-close-modal aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="clViewBody"></div>
    </div>
</div>

{{-- ================= LOGIN DETAILS ================= --}}
<div class="adm-modal" id="clCredModal" aria-hidden="true">
    <div class="adm-modal-box glass-card">
        <div class="modal-head">
            <h4><i class="fa-solid fa-key"></i> <span id="clCredTitle">Login details</span></h4>
            <button class="icon-btn" data-close-modal aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <p class="muted-sm" style="margin-bottom:12px">Share these with your client. They log in and land on their own dashboard.</p>
        <div class="cl-cred">
            <span>Login URL: <b>{{ route('login') }}</b></span>
            <span>Email: <b id="clCredEmail"></b></span>
            <span>Password: <b id="clCredPass"></b></span>
        </div>
        <div class="modal-foot">
            <button class="adm-btn adm-btn-ghost" id="clCredCopy"><i class="fa-regular fa-copy"></i> Copy</button>
            <button class="adm-btn adm-btn-gold" data-close-modal>Done</button>
        </div>
    </div>
</div>

{{-- ================= DELETE ================= --}}
<div class="adm-modal" id="clDeleteModal" aria-hidden="true">
    <div class="adm-modal-box glass-card">
        <div class="cl-del">
            <div class="ring"><i class="fa-regular fa-trash-can"></i></div>
            <h4>Delete Client?</h4>
            <p>"<b id="clDelName"></b>" with Aadhaar, PAN, documents <b>and their login account</b> will be permanently deleted.</p>
            <div class="btns">
                <button class="adm-btn adm-btn-ghost" data-close-modal>Cancel</button>
                <button class="adm-btn adm-btn-danger" id="clDelConfirm"><i class="fa-regular fa-trash-can"></i> Yes, Delete</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
/* ===== CLIENTS – API (Bearer token) ===== */
document.addEventListener('auth:ready', function (ev) {
    if (!window.API) return;
    const isSA  = ev.detail.role === 'superadmin';
    const meId  = ev.detail.id;
    const canEdit   = ['superadmin', 'admin'].includes(ev.detail.role);              // admin bhi add / edit
    const canDelete = c => isSA || (c.created_by_id && +c.created_by_id === +meId);  // admin sirf apne banaye
    const esc   = API.esc;
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const $     = id => document.getElementById(id);
    const body  = $('clRows'), pager = $('clPager');
    const form  = $('clForm'), saveBtn = $('clSaveBtn');
    const el    = n => form.elements.namedItem(n);
    const state = { page: 1, search: '', status: '', advocate_id: '' };
    const cache = {};
    const MAX_KYC = 4 * 1048576, MAX_DOC = 10 * 1048576;
    const mb    = b => (b / 1048576).toFixed(2) + ' MB';
    const ficon = n => /\.pdf$/i.test(n) ? 'fa-regular fa-file-pdf' : /\.docx?$/i.test(n) ? 'fa-regular fa-file-word' : 'fa-regular fa-file-image';
    const fmtA  = s => String(s || '').replace(/\D/g, '').slice(0, 12).replace(/(\d{4})(?=\d)/g, '$1 ');
    const debounce = (fn, ms = 350) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };
    let editId = null, editData = null, newDocs = [], delId = null, view = null;

    /* ---------- private file (token ke saath) ---------- */
    async function getFile(url, download, name) {
        const w = download ? null : window.open('', '_blank');
        try {
            const r = await fetch(url, { headers: { Authorization: 'Bearer ' + API.token(), Accept: '*/*' } });
            if (!r.ok) throw new Error('Could not open file (' + r.status + ')');
            const u = URL.createObjectURL(await r.blob());
            if (download) { const a = document.createElement('a'); a.href = u; a.download = name || 'file'; a.click(); setTimeout(() => URL.revokeObjectURL(u), 3000); }
            else if (w) w.location = u; else location.href = u;
        } catch (e) { if (w) w.close(); toast(e.message, true); }
    }

    /* ---------- options (DB se) ---------- */
    const optionsReady = API.get('/clients/options').then(o => {
        el('practice_area_id').innerHTML = '<option value="">Select practice area</option>' + (o.practice_areas || []).map(p => `<option value="${p.id}">${esc(p.name)}</option>`).join('');
        // Super Admin + Admins (admin ko: khud + superadmin)
        const advOpt = a => `<option value="${a.id}">${a.role === 'superadmin' ? '★ ' : ''}${esc(a.name)} – ${esc(a.label || 'Admin')}${+a.id === +meId ? ' (You)' : ''}${a.city ? ' · ' + esc(a.city) : ''}</option>`;
        const sup = (o.advocates || []).filter(a => a.role === 'superadmin'), adm = (o.advocates || []).filter(a => a.role !== 'superadmin');
        el('advocate_id').innerHTML = (isSA ? '<option value="">— Not assigned —</option>' : '')
            + (sup.length ? `<optgroup label="Super Admin">${sup.map(advOpt).join('')}</optgroup>` : '')
            + (adm.length ? `<optgroup label="Admins">${adm.map(advOpt).join('')}</optgroup>` : '');
        if (!isSA) { el('advocate_id').value = meId; $('clSelfHint').hidden = false; }
        el('state').insertAdjacentHTML('beforeend', (o.states || []).map(s => `<option>${esc(s)}</option>`).join(''));
        $('clAdvFilter').insertAdjacentHTML('beforeend', (o.advocates || []).map(a => `<option value="${a.id}">${esc(a.name)} (${esc(a.label || 'Admin')})</option>`).join(''));
        // All Admins page se ?advocate=ID aaya to filter laga do
        const qa = new URLSearchParams(location.search).get('advocate');
        if (qa && isSA) { $('clAdvFilter').value = qa; if ($('clAdvFilter').value === qa) { state.advocate_id = qa; state.page = 1; load(); } }
    }).catch(() => {});

    /* ---------- LIST ---------- */
    const row = c => {
        const pid = 'clPop' + c.id;
        return `<tr data-id="${c.id}" class="${c.status === 'inactive' ? 'inactive' : ''}">
            <td><div class="t-user"><span class="avatar-txt">${esc((c.name || '?').charAt(0).toUpperCase())}</span>
                <div><div class="t-title">${esc(c.name)}${c.has_login ? '' : '<span class="cl-login-pill off" title="No login yet">No login</span>'}</div><small class="t-sub">${esc(c.email || c.aadhaar_masked)}</small></div></div></td>
            <td class="hide-sm">+91 ${esc(c.phone)}</td>
            <td class="hide-md">${esc(c.city || '—')}</td>
            <td class="hide-md">${c.practice_area ? `<span class="tag">${esc(c.practice_area)}</span>` : '—'}<br><span class="cl-adv">${c.advocate ? `<i class="fa-solid fa-user-tie"></i> <b>${esc(c.advocate.name)}</b>` : 'Not assigned'}</span></td>
            <td><span class="tag">${c.cases_count} ${c.cases_count === 1 ? 'case' : 'cases'}</span></td>
            <td class="hide-sm">${esc(c.since)}</td>
            <td class="t-right">
                <div class="popper-wrap">
                    <button class="icon-btn" data-popper="${pid}" aria-label="Actions"><i class="fa-solid fa-ellipsis"></i></button>
                    <div class="popper popper-right" id="${pid}" data-dyn>
                        <ul class="pop-menu">
                            <li><a href="#" data-cl="view" data-id="${c.id}"><i class="fa-regular fa-eye"></i> Profile</a></li>
                            ${canEdit ? `<li><a href="#" data-cl="edit" data-id="${c.id}"><i class="fa-regular fa-pen-to-square"></i> Edit</a></li>` : ''}
                            <li><a href="tel:+91${esc(c.phone)}"><i class="fa-solid fa-phone"></i> Call</a></li>
                            ${c.email ? `<li><a href="mailto:${esc(c.email)}"><i class="fa-regular fa-envelope"></i> Email</a></li>` : ''}
                            ${canDelete(c) ? `<li class="sep"></li><li><a href="#" class="danger" data-cl="delete" data-id="${c.id}"><i class="fa-regular fa-trash-can"></i> Delete</a></li>` : ''}
                        </ul>
                    </div>
                </div>
            </td>
        </tr>`;
    };
    const empty = (msg, err) => {
        body.innerHTML = `<tr><td colspan="7"><div class="t-empty ${err ? 'error' : ''}"><i class="fa-solid ${err ? 'fa-triangle-exclamation' : 'fa-users'}"></i><p>${esc(msg)}</p></div></td></tr>`;
    };
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
        body.innerHTML = Array.from({ length: 6 }, () => `<tr class="sk-row">${'<td><span class="sk sk-line w80"></span></td>'.repeat(7)}</tr>`).join('');
        const qs = new URLSearchParams({ page: state.page });
        ['search', 'status', 'advocate_id'].forEach(k => { if (state[k] !== '') qs.set(k, state[k]); });
        try {
            const res = await API.get('/clients?' + qs);
            res.data.forEach(c => { cache[c.id] = c; });
            res.data.length ? (body.innerHTML = res.data.map(row).join(''))
                : empty(state.search || state.status || state.advocate_id ? 'No client matches your filters.' : 'No clients yet. Click "Add Client" to add one.');
            renderPager(res.meta);
        } catch (err) { empty(err.message, true); pager.innerHTML = ''; }
    }

    $('clSearch').addEventListener('input', debounce(e => { state.search = e.target.value.trim(); state.page = 1; load(); }));
    $('clStatus').addEventListener('click', e => {
        const b = e.target.closest('button'); if (!b) return;
        $('clStatus').querySelectorAll('button').forEach(x => x.classList.toggle('active', x === b));
        state.status = b.dataset.f; state.page = 1; load();
    });
    $('clAdvFilter').addEventListener('change', e => { state.advocate_id = e.target.value; state.page = 1; load(); });
    pager.addEventListener('click', e => { const b = e.target.closest('[data-page]'); if (!b || b.disabled) return; state.page = +b.dataset.page; load(); });

    /* ---------- FORM helpers ---------- */
    el('phone').addEventListener('input', e => { e.target.value = e.target.value.replace(/\D/g, '').slice(0, 10); });
    el('alt_phone').addEventListener('input', e => { e.target.value = e.target.value.replace(/\D/g, '').slice(0, 10); });
    el('pincode').addEventListener('input', e => { e.target.value = e.target.value.replace(/\D/g, '').slice(0, 6); });
    el('aadhaar_number').addEventListener('input', e => { e.target.value = fmtA(e.target.value); });
    el('pan_number').addEventListener('input', e => { e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 10); });
    form.addEventListener('input', e => { const g = e.target.closest('.has-error'); if (g) { g.classList.remove('has-error'); g.querySelector('.f-error')?.remove(); } });

    const moreOpen = open => { $('clMore').classList.toggle('open', open); $('clMoreBtn').classList.toggle('open', open); };
    $('clMoreBtn').addEventListener('click', () => moreOpen(!$('clMore').classList.contains('open')));

    form.querySelectorAll('[data-drop]').forEach(box => {
        const inp = box.querySelector('input'), lbl = box.querySelector('[data-label]');
        box.dataset.def = lbl.textContent;
        inp.addEventListener('change', () => {
            const f = inp.files[0];
            if (f && f.size > MAX_KYC) { toast('File must be smaller than 4MB.', true); inp.value = ''; }
            const ok = inp.files[0];
            lbl.textContent = ok ? `${ok.name} (${mb(ok.size)})` : box.dataset.def;
            box.classList.toggle('has-file', !!ok);
            const g = box.closest('.has-error'); if (g) { g.classList.remove('has-error'); g.querySelector('.f-error')?.remove(); }
        });
    });

    function addDocs(files) {
        [...files].forEach(f => {
            if (!/\.(pdf|docx?|jpe?g|png|webp)$/i.test(f.name)) return toast(f.name + ': file type not allowed.', true);
            if (f.size > MAX_DOC) return toast(f.name + ' is larger than 10MB.', true);
            if (newDocs.length >= 10) return toast('Up to 10 documents at a time.', true);
            newDocs.push({ file: f, title: f.name.replace(/\.[^.]+$/, '') });
        });
        renderNewDocs();
    }
    function renderNewDocs() {
        $('clNewDocs').innerHTML = newDocs.map((d, i) => `<li class="cl-doc"><i class="${ficon(d.file.name)}"></i>
            <div class="m"><input type="text" value="${esc(d.title)}" maxlength="150" data-dt="${i}" placeholder="Document title"><small>${esc(d.file.name)} · ${mb(d.file.size)}</small></div>
            <button type="button" class="icon-btn danger" data-drm="${i}" aria-label="Remove"><i class="fa-solid fa-xmark"></i></button></li>`).join('');
    }
    function renderSavedDocs(docs) {
        $('clSavedDocs').innerHTML = (docs || []).map(d => `<li class="cl-doc saved"><i class="${ficon(d.original_name)}"></i>
            <div class="m"><b>${esc(d.title)}</b><small>${esc(d.original_name)} · ${esc(d.size)}</small></div>
            <button type="button" class="icon-btn" data-open="${esc(d.url)}" aria-label="View"><i class="fa-regular fa-eye"></i></button>
            <button type="button" class="icon-btn danger" data-ddel="${d.id}" aria-label="Delete"><i class="fa-regular fa-trash-can"></i></button></li>`).join('');
    }
    $('clDocsInput').addEventListener('change', e => { addDocs(e.target.files); e.target.value = ''; });
    const drop = $('clDocsDrop');
    ['dragover', 'dragenter'].forEach(t => drop.addEventListener(t, e => { e.preventDefault(); drop.classList.add('has-file'); }));
    ['dragleave', 'drop'].forEach(t => drop.addEventListener(t, () => drop.classList.remove('has-file')));
    drop.addEventListener('drop', e => { e.preventDefault(); addDocs(e.dataTransfer.files); });
    $('clNewDocs').addEventListener('input', e => { if (e.target.dataset.dt !== undefined) newDocs[e.target.dataset.dt].title = e.target.value; });
    $('clNewDocs').addEventListener('click', e => { const b = e.target.closest('[data-drm]'); if (b) { newDocs.splice(+b.dataset.drm, 1); renderNewDocs(); } });

    form.addEventListener('click', async e => {
        const o = e.target.closest('[data-open]'); if (o) { e.preventDefault(); return getFile(o.dataset.open); }
        if (e.target.closest('#clPanRm')) { el('remove_pan_file').value = '1'; editData.pan_file_url = null; kycNow($('clPanNow'), null); return toast('PAN card will be removed when you save.'); }
        const d = e.target.closest('[data-ddel]');
        if (d && confirm('Delete this document permanently?')) {
            try {
                const r = await API.del(`/clients/${editId}/documents/${d.dataset.ddel}`);
                editData.documents = editData.documents.filter(x => String(x.id) !== d.dataset.ddel);
                renderSavedDocs(editData.documents); toast(r.message);
            } catch (err) { toast(err.message, true); }
        }
    });

    function kycNow(box, url, label, removable) {
        box.hidden = !url;
        box.innerHTML = url ? `<i class="fa-solid fa-circle-check" style="color:#5fe0a5"></i> ${label} uploaded · <button type="button" data-open="${esc(url)}">View</button>${removable ? ' · <button type="button" class="rm" id="clPanRm">Remove</button>' : ' · upload to replace'}` : '';
    }

    form.querySelectorAll('[data-eye]').forEach(b => b.addEventListener('click', () => {
        const i = b.parentElement.querySelector('input'), sh = i.type === 'password'; i.type = sh ? 'text' : 'password';
        b.innerHTML = `<i class="fa-regular fa-eye${sh ? '-slash' : ''}"></i>`;
    }));
    $('clGen').addEventListener('click', () => {
        const pick = s => s[crypto.getRandomValues(new Uint32Array(1))[0] % s.length];
        let p = pick('ABCDEFGHJKLMNPQRSTUVWXYZ') + pick('abcdefghijkmnpqrstuvwxyz') + pick('23456789') + pick('@#$%&*');
        for (let i = 0; i < 6; i++) p += pick('ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789');
        el('password').value = el('password_confirmation').value = p;
        form.querySelectorAll('.cl-pw input').forEach(i => { i.type = 'text'; });
        toast('Strong password generated – share it with the client.');
    });
    const setLoginMode = hasLogin => {
        $('clPwReq').hidden = hasLogin;
        el('password').placeholder = hasLogin ? 'Leave blank to keep current password' : '8+ chars, Aa + number';
        $('clLoginNote').innerHTML = hasLogin
            ? 'Login is active for this client. Enter a new password only if you want to <b>reset</b> it.'
            : 'The client logs in with the <b>email above</b> and this password, and lands on their own dashboard.';
    };

    function resetForm() {
        form.reset(); API.clearErrors(form);
        newDocs = []; renderNewDocs(); renderSavedDocs([]);
        editId = null; editData = null;
        el('remove_pan_file').value = '0';
        form.querySelectorAll('[data-drop]').forEach(b => { b.classList.remove('has-file'); b.querySelector('[data-label]').textContent = b.dataset.def; });
        kycNow($('clAadhaarNow'), null); kycNow($('clPanNow'), null);
        moreOpen(false);
        if (!isSA) el('advocate_id').value = meId;
        form.querySelectorAll('.cl-pw input').forEach(i => { i.type = 'password'; });
        setLoginMode(false);
    }

    function openAdd() {
        resetForm();
        $('clModalTitle').textContent = 'Add Client';
        $('clModalIcon').className = 'fa-solid fa-user-plus';
        saveBtn.querySelector('span').textContent = 'Save Client';
        window.admOpenModal('clientModal');
    }

    async function openEdit(id) {
        resetForm();
        editId = id;
        $('clModalTitle').textContent = 'Edit Client';
        $('clModalIcon').className = 'fa-regular fa-pen-to-square';
        saveBtn.querySelector('span').textContent = 'Save Changes';
        form.classList.add('is-loading');
        window.admOpenModal('clientModal');
        try {
            const [r] = await Promise.all([API.get(`/clients/${id}?full=1`), optionsReady]);
            const c = editData = r.data;
            ['name', 'phone', 'email', 'city', 'address', 'case_details', 'pan_number', 'alt_phone', 'pincode', 'status'].forEach(k => { if (c[k] != null) el(k).value = c[k]; });
            el('aadhaar_number').value = fmtA(c.aadhaar_number);
            const ensure = (sel, val, text) => {
                if (val == null || val === '') return;
                if (![...sel.options].some(o => o.value == val)) sel.insertAdjacentHTML('beforeend', `<option value="${esc(val)}">${esc(text)}</option>`);
                sel.value = val;
            };
            ensure(el('practice_area_id'), c.practice_area_id, (c.practice_area || 'Practice area') + ' (inactive)');
            ensure(el('advocate_id'), c.advocate_id, (c.advocate?.name || 'Admin') + ' (inactive)');
            ensure(el('state'), c.state, c.state);
            kycNow($('clAadhaarNow'), c.aadhaar_file_url, 'Aadhaar card', false);
            kycNow($('clPanNow'), c.pan_file_url, 'PAN card', true);
            setLoginMode(!!c.has_login);
            renderSavedDocs(c.documents);
            if (c.alt_phone || c.state || c.pincode || c.status === 'inactive') moreOpen(true);
        } catch (err) { toast(err.message, true); document.querySelector('#clientModal [data-close-modal]').click(); }
        finally { form.classList.remove('is-loading'); }
    }

    /* ---------- login details popup (create / password reset ke baad) ---------- */
    const LOGIN_URL = @json(route('login'));
    function showCred(email, pass, isNew) {
        $('clCredTitle').textContent = isNew ? 'Client created – login details' : 'Password updated';
        $('clCredEmail').textContent = email; $('clCredPass').textContent = pass;
        window.admOpenModal('clCredModal');
    }
    $('clCredCopy').addEventListener('click', () => {
        const t = `Lexora Legal – Client Login\nURL: ${LOGIN_URL}\nEmail: ${$('clCredEmail').textContent}\nPassword: ${$('clCredPass').textContent}`;
        navigator.clipboard?.writeText(t).then(() => toast('Login details copied.')).catch(() => toast('Copy failed – please copy manually.', true));
    });

    /* ---------- SAVE ---------- */
    form.addEventListener('submit', async e => {
        e.preventDefault();
        API.clearErrors(form);
        const errs = {};
        if (el('name').value.trim().length < 3) errs.name = ['Please enter the client name.'];
        if (!/^[6-9]\d{9}$/.test(el('phone').value)) errs.phone = ['Enter a valid 10-digit mobile number.'];
        if (!el('email').value || !el('email').checkValidity()) errs.email = ['Email is required – the client logs in with it.'];
        const pw = el('password').value, needPw = !editData || !editData.has_login;
        if ((needPw || pw) && !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(pw)) errs.password = [needPw ? 'Set a login password: 8+ characters with uppercase, lowercase and a number.' : '8+ characters with uppercase, lowercase and a number.'];
        if (pw !== el('password_confirmation').value) errs.password_confirmation = ['Passwords do not match.'];
        if (!el('practice_area_id').value) errs.practice_area_id = ['Please select what this case is about.'];
        if (!/^[2-9]\d{11}$/.test(el('aadhaar_number').value.replace(/\D/g, ''))) errs.aadhaar_number = ['Enter a valid 12-digit Aadhaar number.'];
        if (!el('aadhaar_file').files[0] && !(editData && editData.aadhaar_file_url)) errs.aadhaar_file = ['Please upload the Aadhaar card.'];
        if (el('pan_number').value && !/^[A-Z]{5}[0-9]{4}[A-Z]$/.test(el('pan_number').value)) errs.pan_number = ['Enter a valid PAN, e.g. ABCDE1234F.'];
        if (el('alt_phone').value && !/^[6-9]\d{9}$/.test(el('alt_phone').value)) { errs.alt_phone = ['Enter a valid alternate number.']; moreOpen(true); }
        if (el('pincode').value && el('pincode').value.length !== 6) { errs.pincode = ['Pincode must be 6 digits.']; moreOpen(true); }
        if (Object.keys(errs).length) { API.showErrors(form, errs)?.scrollIntoView({ behavior: 'smooth', block: 'center' }); return; }

        const fd = API.formData(form);
        fd.set('aadhaar_number', el('aadhaar_number').value.replace(/\D/g, ''));
        newDocs.forEach((d, i) => { fd.append(`documents[${i}]`, d.file); fd.append(`document_titles[${i}]`, d.title || ''); });

        saveBtn.disabled = true; saveBtn.classList.add('loading');
        try {
            const r = await API.post(editId ? '/clients/' + editId : '/clients', fd);
            toast(r.message);
            if (pw) setTimeout(() => showCred(r.data.email, pw, !editId), 350);
            document.querySelector('#clientModal [data-close-modal]').click();
            if (!editId) state.page = 1;
            load();
        } catch (err) {
            const mapped = {};
            Object.keys(err.errors || {}).forEach(k => { mapped[k.split('.')[0]] = err.errors[k]; });
            if (['alt_phone', 'state', 'pincode', 'status'].some(k => mapped[k])) moreOpen(true);
            API.showErrors(form, mapped)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            toast(err.message, true);
            if (err.status === 403 && /subscription/i.test(err.message)) setTimeout(() => { if (confirm('Your subscription is not active. Open My Subscription page?')) location.href = API.url('/admin/subscription'); }, 400);
        } finally { saveBtn.disabled = false; saveBtn.classList.remove('loading'); }
    });

    /* ---------- VIEW ---------- */
    const vbody = $('clViewBody');
    const v = x => x ? esc(x) : '<span style="color:var(--muted)">—</span>';
    const fileCard = (title, sub, url, name, ic) => url
        ? `<div class="cl-doc cv-file"><i class="${ic}"></i><div class="m"><b>${esc(title)}</b><small>${esc(sub)}</small></div>
            <button class="icon-btn" data-vopen="${esc(url)}" aria-label="View"><i class="fa-regular fa-eye"></i></button>
            <button class="icon-btn" data-vdl="${esc(url)}" data-name="${esc(name)}" aria-label="Download"><i class="fa-solid fa-download"></i></button></div>`
        : `<div class="cl-doc cv-file missing"><i class="${ic}"></i><div class="m"><b>${esc(title)}</b><small>Not uploaded</small></div></div>`;

    function renderView(c) {
        vbody.innerHTML = `
            <div class="cv-head"><span class="avatar-txt">${esc(c.name.charAt(0).toUpperCase())}</span>
                <div><h4>${esc(c.name)}</h4><span class="status ${c.status === 'active' ? 'green' : 'grey'}">${c.status === 'active' ? 'Active' : 'Inactive'}</span>
                <small class="t-sub"> · Client since ${esc(c.created_at)}${c.created_by ? ' · added by ' + esc(c.created_by) : ''}</small></div></div>
            <div class="cv-kv">
                <div><small>Phone</small><b><a class="gold" href="tel:+91${esc(c.phone)}">+91 ${esc(c.phone)}</a></b></div>
                <div><small>Alternate Phone</small><b>${v(c.alt_phone)}</b></div>
                <div><small>Email</small><b>${c.email ? `<a class="gold" href="mailto:${esc(c.email)}">${esc(c.email)}</a>` : v()}</b></div>
                <div><small>City</small><b>${v(c.city)}</b></div>
                <div><small>State</small><b>${v(c.state)}</b></div>
                <div><small>Pincode</small><b>${v(c.pincode)}</b></div>
                <div class="full"><small>Address</small><b>${v(c.address)}</b></div>
                <div><small>Case</small><b>${v(c.practice_area)}</b></div>
                <div><small>Assigned To</small><b>${c.advocate ? esc(c.advocate.name) : v()}</b></div>
                <div><small>Cases in system</small><b>${c.cases_count}</b></div>
                <div><small>Login (Email)</small><b>${c.has_login ? esc(c.email) + `<span class="cl-login-pill ${c.login_status === 'active' ? 'on' : 'off'}">${c.login_status === 'active' ? 'Active' : 'Disabled'}</span>` : '<span class="cl-login-pill off">No login – edit & set password</span>'}</b></div>
                <div><small>Last Login</small><b>${c.has_login ? esc(c.last_login_at || 'Never') : '—'}</b></div>
                <div><small>Aadhaar</small><b class="cv-mono" id="cvA">${esc(c.aadhaar_masked)}</b>${c.aadhaar_number ? '<button class="cv-eye" data-reveal="a"><i class="fa-regular fa-eye"></i></button>' : ''}</div>
                <div><small>PAN</small><b class="cv-mono" id="cvP">${v(c.pan_masked)}</b>${c.pan_number ? '<button class="cv-eye" data-reveal="p"><i class="fa-regular fa-eye"></i></button>' : ''}</div>
            </div>
            ${c.case_details ? `<small class="muted-sm">Case details</small><div class="cv-box">${esc(c.case_details)}</div>` : ''}
            <small class="muted-sm">KYC & Documents</small>
            <div class="cv-files">
                ${fileCard('Aadhaar Card', 'KYC', c.aadhaar_file_url, 'aadhaar-' + c.id, 'fa-solid fa-id-card')}
                ${fileCard('PAN Card', 'KYC', c.pan_file_url, 'pan-' + c.id, 'fa-regular fa-address-card')}
                ${(c.documents || []).map(d => fileCard(d.title, d.original_name + ' · ' + d.size, d.url, d.original_name, ficon(d.original_name))).join('')}
            </div>
            <div class="modal-foot">
                <a href="tel:+91${esc(c.phone)}" class="adm-btn adm-btn-ghost"><i class="fa-solid fa-phone"></i> Call</a>
                ${canEdit ? `<button class="adm-btn adm-btn-gold" data-cl="edit" data-id="${c.id}"><i class="fa-regular fa-pen-to-square"></i> Edit</button>` : ''}
            </div>`;
    }

    async function openView(id) {
        view = null;
        vbody.innerHTML = '<div class="t-empty"><i class="fa-solid fa-spinner fa-spin"></i><p>Loading…</p></div>';
        window.admOpenModal('clViewModal');
        try { const r = await API.get(`/clients/${id}?full=1`); view = r.data; renderView(view); }
        catch (err) { vbody.innerHTML = `<div class="t-empty error"><i class="fa-solid fa-triangle-exclamation"></i><p>${esc(err.message)}</p></div>`; }
    }
    vbody.addEventListener('click', e => {
        const o = e.target.closest('[data-vopen]'); if (o) return getFile(o.dataset.vopen);
        const d = e.target.closest('[data-vdl]'); if (d) return getFile(d.dataset.vdl, true, d.dataset.name);
        const r = e.target.closest('[data-reveal]');
        if (r && view) {
            const a = r.dataset.reveal === 'a', on = r.classList.toggle('on');
            $(a ? 'cvA' : 'cvP').textContent = on ? (a ? fmtA(view.aadhaar_number) : view.pan_number) : (a ? view.aadhaar_masked : view.pan_masked);
            r.innerHTML = `<i class="fa-regular fa-eye${on ? '-slash' : ''}"></i>`;
        }
    });

    /* ---------- actions (list + view) ---------- */
    document.addEventListener('click', e => {
        const a = e.target.closest('[data-cl]'); if (!a) return;
        e.preventDefault();
        const id = a.dataset.id;
        if (a.dataset.cl === 'view') openView(id);
        if (a.dataset.cl === 'edit') { document.querySelector('#clViewModal.show [data-close-modal]')?.click(); setTimeout(() => openEdit(id), 150); }
        if (a.dataset.cl === 'delete') { delId = id; $('clDelName').textContent = cache[id]?.name || ''; window.admOpenModal('clDeleteModal'); }
    });
    $('clAddBtn')?.addEventListener('click', openAdd);

    $('clDelConfirm').addEventListener('click', async () => {
        if (!delId) return;
        const b = $('clDelConfirm'); b.disabled = true; b.classList.add('loading');
        try {
            const r = await API.del('/clients/' + delId);
            document.querySelector('#clDeleteModal [data-close-modal]').click();
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