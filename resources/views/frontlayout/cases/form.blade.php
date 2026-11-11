@extends('frontlayout.app')
@php
    $editId     = $id ?? null;
    $statuses   = \App\Models\LegalCase::STATUSES;
    $priorities = \App\Models\LegalCase::PRIORITIES;
    $req        = '<b class="req">*</b>';
@endphp
@section('title', $editId ? 'Edit Case' : 'Add Case')
@section('page_title', $editId ? 'Edit Case' : 'Add Case')
@section('require_role', 'superadmin')

@section('page_actions')
    <a href="{{ route('superadmin.cases') }}" class="adm-btn adm-btn-ghost"><i class="fa-solid fa-list"></i> All Cases</a>
@endsection

@push('styles')
<style>
/* ===== ADD / EDIT CASE (isi file me) ===== */
.cf-grid { display: grid; grid-template-columns: minmax(0, 1fr) 330px; gap: 24px; align-items: start; }
.cf-sec { padding: 26px; margin-bottom: 22px; animation: cfUp .55s ease both; }
.cf-sec:nth-child(2) { animation-delay: .06s; } .cf-sec:nth-child(3) { animation-delay: .12s; } .cf-sec:nth-child(4) { animation-delay: .18s; }
@keyframes cfUp { from { opacity: 0; transform: translateY(18px); } }
.cf-head { display: flex; align-items: center; gap: 14px; margin-bottom: 20px; }
.cf-num { width: 42px; height: 42px; flex-shrink: 0; border-radius: 12px; display: grid; place-items: center; font-weight: 600; color: #111; background: var(--gold-grad); box-shadow: 0 8px 20px rgba(212,175,55,.3); }
.cf-head h4 { color: #fff; font-size: 17px; margin: 0; }
.cf-head small { color: var(--muted); font-size: 12.5px; }
.cf-head .opt { margin-left: auto; font-size: 11px; letter-spacing: 1px; text-transform: uppercase; color: var(--muted); padding: 4px 10px; border-radius: 20px; border: 1px solid var(--border); }
.req { color: #ff7a7a; font-weight: 600; }
.cf-empty-link { font-size: 12px; color: var(--gold-light); margin-top: 6px; display: inline-block; }

/* status chips */
.cf-status { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
.cf-status label { margin: 0 !important; cursor: pointer; }
.cf-status input { position: absolute; opacity: 0; width: 0 !important; height: 0; padding: 0 !important; border: 0 !important; }
.cf-status span { display: flex; align-items: center; justify-content: center; gap: 6px; height: 42px; border-radius: 12px; font-size: 13px; color: #bbb; border: 1px solid var(--border); background: rgba(255,255,255,.02); transition: .25s; }
.cf-status label:hover span { border-color: rgba(212,175,55,.4); color: #fff; }
.cf-status input:checked + span { color: #111; font-weight: 600; background: var(--gold-grad); border-color: transparent; box-shadow: 0 8px 20px rgba(212,175,55,.25); }
.cf-status input:focus-visible + span { outline: 2px solid var(--gold); outline-offset: 2px; }

/* upload boxes (Aadhaar / PAN) */
.cf-kyc { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.cf-drop { position: relative; display: flex; align-items: center; gap: 14px; padding: 16px; border-radius: 16px; cursor: pointer; transition: .3s; margin: 0 !important;
    border: 1px dashed rgba(212,175,55,.35); background: rgba(212,175,55,.03); }
.f-group label.cf-drop { display: flex; font-size: inherit; color: inherit; }
.f-group label.cf-docs-drop { flex-direction: column; }
.cf-drop:hover, .cf-drop.drag { background: rgba(212,175,55,.08); border-color: var(--gold); }
.cf-drop > i { width: 46px; height: 46px; flex-shrink: 0; border-radius: 13px; display: grid; place-items: center; font-size: 19px; color: var(--gold); background: rgba(212,175,55,.1); }
.cf-drop b { display: block; color: #fff; font-size: 14px; font-weight: 500; }
.cf-drop small { color: var(--muted); font-size: 12px; display: block; word-break: break-all; }
.cf-drop.has-file { border-style: solid; border-color: rgba(62,207,142,.5); background: rgba(62,207,142,.05); }
.cf-drop.has-file > i { color: #5fe0a5; background: rgba(62,207,142,.12); }
.cf-drop input[type=file] { display: none; }
.f-group.has-error .cf-drop { border-color: rgba(255,107,107,.7); }
.cf-existing { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-top: 8px; font-size: 12px; color: var(--muted); }
.cf-existing a, .cf-existing button { font: inherit; font-size: 12px; color: var(--gold-light); background: none; border: 0; cursor: pointer; padding: 0; }
.cf-existing button.rm { color: #ff8c8c; }

/* documents */
.cf-docs-drop { flex-direction: column; text-align: center; padding: 28px 16px; }
.cf-docs-drop > i { width: 56px; height: 56px; font-size: 22px; }
.cf-doc-list { list-style: none; padding: 0; margin: 14px 0 0; display: grid; gap: 10px; }
.cf-doc { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 14px; border: 1px solid var(--border); background: rgba(255,255,255,.02); animation: cfUp .35s ease both; }
.cf-doc > i { width: 38px; height: 38px; flex-shrink: 0; border-radius: 10px; display: grid; place-items: center; color: var(--gold); background: rgba(212,175,55,.1); }
.cf-doc.saved > i { color: #5fe0a5; background: rgba(62,207,142,.1); }
.cf-doc .meta { flex: 1; min-width: 0; }
.cf-doc .meta input { width: 100%; padding: 6px 10px; border-radius: 8px; font-size: 13px; }
.cf-doc .meta b { display: block; color: #fff; font-size: 13.5px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cf-doc .meta small { color: var(--muted); font-size: 11.5px; }
.cf-doc .acts { display: flex; gap: 6px; }
.cf-doc .acts .icon-btn.danger { color: #ff8c8c; }
.cf-doc-label { font-size: 12px; color: var(--muted); margin: 18px 0 0; text-transform: uppercase; letter-spacing: 1.5px; }

/* more */
.cf-more-btn { width: 100%; display: flex; align-items: center; gap: 12px; padding: 16px 20px; margin-bottom: 22px; border-radius: 16px; cursor: pointer; font: inherit; color: #ddd; text-align: left;
    border: 1px dashed rgba(212,175,55,.35); background: rgba(212,175,55,.04); transition: .3s; }
.cf-more-btn:hover { background: rgba(212,175,55,.08); color: #fff; }
.cf-more-btn > i:first-child { color: var(--gold); }
.cf-more-btn .caret { margin-left: auto; transition: .35s; color: var(--gold); }
.cf-more-btn.open .caret { transform: rotate(180deg); }
.cf-more { display: none; }
.cf-more.open { display: block; }

/* side */
.cf-side { position: sticky; top: 90px; padding: 22px; }
.cf-side .cno { font-family: ui-monospace, Consolas, monospace; font-size: 13px; color: var(--gold-light); background: rgba(212,175,55,.08); padding: 4px 10px; border-radius: 8px; display: inline-block; margin-bottom: 12px; }
.cf-side h4 { color: #fff; font-size: 18px; margin-bottom: 4px; overflow-wrap: anywhere; }
.cf-checks { list-style: none; padding: 0; margin: 16px 0 18px; display: grid; gap: 9px; }
.cf-checks li { display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--muted); transition: .3s; }
.cf-checks li i { width: 20px; height: 20px; border-radius: 50%; display: grid; place-items: center; font-size: 10px; color: transparent; border: 1px solid var(--border); transition: .3s; }
.cf-checks li.ok { color: #ddd; }
.cf-checks li.ok i { color: #111; background: var(--gold-grad); border-color: transparent; }
.cf-progress { height: 6px; border-radius: 6px; background: rgba(255,255,255,.06); overflow: hidden; margin-bottom: 18px; }
.cf-progress i { display: block; height: 100%; width: 0; background: var(--gold-grad); transition: width .4s; }
.cf-side .adm-btn { margin-bottom: 10px; }
form.is-loading .cf-sec, form.is-loading .cf-side { opacity: .45; pointer-events: none; }

@media (max-width: 1100px) { .cf-grid { grid-template-columns: 1fr; } .cf-side { position: static; } }
@media (max-width: 575px) {
    .cf-sec { padding: 20px 16px; }
    .cf-status { grid-template-columns: 1fr 1fr; }
    .cf-kyc { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

<form id="caseForm" data-edit-id="{{ $editId }}" novalidate enctype="multipart/form-data">
    <div class="cf-grid">
        <div>
            {{-- ================= 01. CASE ================= --}}
            <div class="glass-card cf-sec">
                <div class="cf-head"><span class="cf-num">01</span><div><h4>Case Details</h4><small>Matter, practice area & assigned advocate</small></div></div>
                <div class="f-grid">
                    <div class="f-group full" data-field="title">
                        <label>Case Title / Matter {!! $req !!}</label>
                        <input type="text" name="title" required minlength="3" maxlength="200" placeholder="e.g. State vs Rohit Sharma – Bail Application">
                    </div>
                    <div class="f-group" data-field="practice_area_id">
                        <label>Practice Area {!! $req !!}</label>
                        <select name="practice_area_id" required><option value="">Loading…</option></select>
                        <a href="{{ route('superadmin.practice-areas.create') }}" class="cf-empty-link" id="cfAreaEmpty" hidden><i class="fa-solid fa-plus"></i> Add a practice area first</a>
                    </div>
                    <div class="f-group" data-field="advocate_id">
                        <label>Assign Advocate</label>
                        <select name="advocate_id"><option value="">Loading…</option></select>
                        <a href="{{ route('superadmin.admins.create') }}" class="cf-empty-link" id="cfAdvEmpty" hidden><i class="fa-solid fa-plus"></i> Create an admin (advocate)</a>
                    </div>
                    <div class="f-group full" data-field="status">
                        <label>Status {!! $req !!}</label>
                        <div class="cf-status">
                            @foreach($statuses as $val => $label)
                                <label><input type="radio" name="status" value="{{ $val }}" @checked($val === 'pending')><span>{{ $label }}</span></label>
                            @endforeach
                        </div>
                    </div>
                    <div class="f-group" data-field="next_hearing_date">
                        <label>Next Hearing Date</label>
                        <input type="date" name="next_hearing_date">
                    </div>
                    <div class="f-group" data-field="priority">
                        <label>Priority</label>
                        <select name="priority">
                            @foreach($priorities as $val => $label)
                                <option value="{{ $val }}" @selected($val === 'normal')>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- ================= 02. CLIENT + KYC ================= --}}
            <div class="glass-card cf-sec">
                <div class="cf-head"><span class="cf-num">02</span><div><h4>Client & KYC</h4><small>Aadhaar is mandatory · PAN optional</small></div></div>
                <div class="f-grid">
                    <div class="f-group" data-field="client_name">
                        <label>Client Name {!! $req !!}</label>
                        <input type="text" name="client_name" required minlength="3" maxlength="120" placeholder="Full name as on Aadhaar">
                    </div>
                    <div class="f-group" data-field="client_phone">
                        <label>Client Mobile {!! $req !!}</label>
                        <input type="tel" name="client_phone" required inputmode="numeric" maxlength="10" pattern="[6-9][0-9]{9}" placeholder="10-digit mobile">
                    </div>
                    <div class="f-group" data-field="aadhaar_number">
                        <label>Aadhaar Number {!! $req !!}</label>
                        <input type="text" name="aadhaar_number" required inputmode="numeric" maxlength="14" placeholder="XXXX XXXX XXXX" autocomplete="off">
                    </div>
                    <div class="f-group" data-field="pan_number">
                        <label>PAN Number</label>
                        <input type="text" name="pan_number" maxlength="10" placeholder="ABCDE1234F" style="text-transform:uppercase" autocomplete="off">
                    </div>
                </div>

                <div class="cf-kyc">
                    <div class="f-group" data-field="aadhaar_file">
                        <label>Aadhaar Card {!! $req !!}</label>
                        <label class="cf-drop" data-drop>
                            <i class="fa-solid fa-id-card"></i>
                            <span><b>Upload Aadhaar Card</b><small data-file-label>PDF / JPG / PNG · max 4MB</small></span>
                            <input type="file" name="aadhaar_file" accept=".pdf,image/png,image/jpeg,image/webp">
                        </label>
                        <div class="cf-existing" id="cfAadhaarNow" hidden></div>
                    </div>
                    <div class="f-group" data-field="pan_file">
                        <label>PAN Card</label>
                        <label class="cf-drop" data-drop>
                            <i class="fa-regular fa-address-card"></i>
                            <span><b>Upload PAN Card</b><small data-file-label>Optional · PDF / JPG / PNG · max 4MB</small></span>
                            <input type="file" name="pan_file" accept=".pdf,image/png,image/jpeg,image/webp">
                        </label>
                        <div class="cf-existing" id="cfPanNow" hidden></div>
                        <input type="hidden" name="remove_pan_file" value="0">
                    </div>
                </div>
            </div>

            {{-- ================= 03. DOCUMENTS ================= --}}
            <div class="glass-card cf-sec">
                <div class="cf-head"><span class="cf-num">03</span><div><h4>Case Documents</h4><small>FIR, notices, agreements, court orders… (up to 10 at a time)</small></div><span class="opt">Optional</span></div>
                <div class="f-group" data-field="documents">
                    <label class="cf-drop cf-docs-drop" id="cfDocsDrop">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span><b>Drop files here or click to upload</b><small>PDF, DOC, DOCX, JPG, PNG · max 10MB each</small></span>
                        <input type="file" id="cfDocsInput" multiple accept=".pdf,.doc,.docx,image/png,image/jpeg,image/webp">
                    </label>
                    <ul class="cf-doc-list" id="cfNewDocs"></ul>
                    <p class="cf-doc-label" id="cfSavedLabel" hidden>Already uploaded</p>
                    <ul class="cf-doc-list" id="cfSavedDocs"></ul>
                </div>
            </div>

            {{-- ================= 04. MORE (extra DB fields) ================= --}}
            <button type="button" class="cf-more-btn" id="cfMoreBtn" aria-expanded="false">
                <i class="fa-solid fa-sliders"></i>
                <span><b>More Details</b> <small class="muted-sm">— court, opponent, filing date, email, notes (optional)</small></span>
                <i class="fa-solid fa-chevron-down caret"></i>
            </button>
            <div class="glass-card cf-sec cf-more" id="cfMore">
                <div class="cf-head"><span class="cf-num">04</span><div><h4>Additional Details</h4><small>Court & case background</small></div><span class="opt">Optional</span></div>
                <div class="f-grid">
                    <div class="f-group" data-field="court_name">
                        <label>Court Name</label>
                        <input type="text" name="court_name" maxlength="150" placeholder="e.g. District Court, Saket">
                    </div>
                    <div class="f-group" data-field="opponent_name">
                        <label>Opponent / Other Party</label>
                        <input type="text" name="opponent_name" maxlength="150" placeholder="e.g. State of Delhi">
                    </div>
                    <div class="f-group" data-field="filing_date">
                        <label>Filing Date</label>
                        <input type="date" name="filing_date" max="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="f-group" data-field="client_email">
                        <label>Client Email</label>
                        <input type="email" name="client_email" maxlength="150" placeholder="client@example.com">
                    </div>
                    <div class="f-group full" data-field="description">
                        <label>Case Description / Notes</label>
                        <textarea name="description" rows="5" maxlength="5000" placeholder="Brief facts of the case, sections involved, next steps…"></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= SIDE ================= --}}
        <aside class="glass-card cf-side">
            <span class="cno" id="cfCaseNo">{{ $editId ? 'Loading…' : 'New Case' }}</span>
            <h4 id="cfTitle">{{ $editId ? 'Edit Case' : 'Create Case' }}</h4>
            <p class="muted-sm">Fields marked <b class="req">*</b> are required. Case ID is generated automatically.</p>

            <ul class="cf-checks" id="cfChecks">
                <li data-chk="title"><i class="fa-solid fa-check"></i> Case title</li>
                <li data-chk="practice_area_id"><i class="fa-solid fa-check"></i> Practice area</li>
                <li data-chk="client_name"><i class="fa-solid fa-check"></i> Client name</li>
                <li data-chk="client_phone"><i class="fa-solid fa-check"></i> Client mobile</li>
                <li data-chk="aadhaar_number"><i class="fa-solid fa-check"></i> Aadhaar number</li>
                <li data-chk="aadhaar_file"><i class="fa-solid fa-check"></i> Aadhaar card upload</li>
            </ul>
            <div class="cf-progress"><i id="cfProgress"></i></div>

            <button type="submit" class="adm-btn adm-btn-gold w-100 save-btn">
                <i class="fa-solid fa-check"></i> <span>{{ $editId ? 'Save Changes' : 'Create Case' }}</span>
            </button>
            <a href="{{ $editId ? route('superadmin.cases.show', $editId) : route('superadmin.cases') }}" class="adm-btn adm-btn-ghost w-100">Cancel</a>
        </aside>
    </div>
</form>

@endsection

@push('scripts')
<script>
/* ===== ADD / EDIT CASE – API (Bearer token) ===== */
document.addEventListener('auth:ready', function (ev) {
    if (!window.API || ev.detail.role !== 'superadmin') return;

    const form  = document.getElementById('caseForm');
    const id    = form.dataset.editId;
    const btn   = form.querySelector('.save-btn');
    const esc   = API.esc;
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const LIST  = @json(route('superadmin.cases'));
    const $     = s => document.getElementById(s);
    const el    = n => form.elements.namedItem(n);
    const MAX_DOC = 10 * 1024 * 1024, MAX_KYC = 4 * 1024 * 1024;
    const mb    = b => (b / 1048576).toFixed(2) + ' MB';
    const icon  = name => /\.pdf$/i.test(name) ? 'fa-regular fa-file-pdf' : /\.(docx?)$/i.test(name) ? 'fa-regular fa-file-word' : 'fa-regular fa-file-image';

    let newDocs = [];          // { file, title }
    let caseData = null;

    /* ---------- private file kholna (token ke saath) ---------- */
    async function openFile(url) {
        const w = window.open('', '_blank');
        try {
            const r = await fetch(url, { headers: { Authorization: 'Bearer ' + API.token(), Accept: '*/*' } });
            if (!r.ok) throw new Error('Could not open file (' + r.status + ')');
            const u = URL.createObjectURL(await r.blob());
            if (w) w.location = u; else location.href = u;
        } catch (e) { if (w) w.close(); toast(e.message, true); }
    }

    /* ---------- dropdowns (DB se dynamic) ---------- */
    const optionsReady = API.get('/cases/options').then(o => {
        const areas = o.practice_areas || [], advs = o.advocates || [];
        el('practice_area_id').innerHTML = '<option value="">Select practice area</option>' +
            areas.map(p => `<option value="${p.id}">${esc(p.name)}${p.type ? ' — ' + esc(p.type) : ''}</option>`).join('');
        el('advocate_id').innerHTML = '<option value="">— Unassigned —</option>' +
            advs.map(a => `<option value="${a.id}">${esc(a.name)}${a.city ? ' (' + esc(a.city) + ')' : ''}</option>`).join('');
        $('cfAreaEmpty').hidden = areas.length > 0;
        $('cfAdvEmpty').hidden = advs.length > 0;
    }).catch(err => toast(err.message, true));

    /* ---------- formatting ---------- */
    el('aadhaar_number').addEventListener('input', e => {
        const d = e.target.value.replace(/\D/g, '').slice(0, 12);
        e.target.value = d.replace(/(\d{4})(?=\d)/g, '$1 ');
    });
    el('client_phone').addEventListener('input', e => { e.target.value = e.target.value.replace(/\D/g, '').slice(0, 10); });
    el('pan_number').addEventListener('input', e => { e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 10); });

    /* ---------- Aadhaar / PAN upload boxes ---------- */
    form.querySelectorAll('[data-drop]').forEach(box => {
        const inp = box.querySelector('input[type=file]'), label = box.querySelector('[data-file-label]'), def = label.textContent;
        const show = () => {
            const f = inp.files[0];
            if (f && f.size > MAX_KYC) { toast('File must be smaller than 4MB.', true); inp.value = ''; }
            const ok = inp.files[0];
            label.textContent = ok ? `${ok.name} (${mb(ok.size)})` : def;
            box.classList.toggle('has-file', !!ok);
            if (ok && inp.name === 'pan_file') el('remove_pan_file').value = '0';
            progress();
        };
        inp.addEventListener('change', show);
        ['dragover', 'dragenter'].forEach(t => box.addEventListener(t, e => { e.preventDefault(); box.classList.add('drag'); }));
        ['dragleave', 'drop'].forEach(t => box.addEventListener(t, () => box.classList.remove('drag')));
        box.addEventListener('drop', e => { e.preventDefault(); if (e.dataTransfer.files[0]) { const dt = new DataTransfer(); dt.items.add(e.dataTransfer.files[0]); inp.files = dt.files; show(); } });
    });

    /* ---------- documents (multiple) ---------- */
    const drop = $('cfDocsDrop'), docsInput = $('cfDocsInput');
    function addDocs(files) {
        [...files].forEach(f => {
            if (!/\.(pdf|docx?|jpe?g|png|webp)$/i.test(f.name)) { toast(f.name + ': file type not allowed.', true); return; }
            if (f.size > MAX_DOC) { toast(f.name + ' is larger than 10MB.', true); return; }
            if (newDocs.length >= 10) { toast('You can upload up to 10 documents at a time.', true); return; }
            newDocs.push({ file: f, title: f.name.replace(/\.[^.]+$/, '') });
        });
        renderNewDocs();
    }
    function renderNewDocs() {
        $('cfNewDocs').innerHTML = newDocs.map((d, i) => `<li class="cf-doc">
            <i class="${icon(d.file.name)}"></i>
            <div class="meta"><input type="text" value="${esc(d.title)}" maxlength="150" data-doc-title="${i}" placeholder="Document title"><small>${esc(d.file.name)} · ${mb(d.file.size)}</small></div>
            <div class="acts"><button type="button" class="icon-btn danger" data-doc-rm="${i}" aria-label="Remove"><i class="fa-solid fa-xmark"></i></button></div>
        </li>`).join('');
    }
    docsInput.addEventListener('change', () => { addDocs(docsInput.files); docsInput.value = ''; });
    ['dragover', 'dragenter'].forEach(t => drop.addEventListener(t, e => { e.preventDefault(); drop.classList.add('drag'); }));
    ['dragleave', 'drop'].forEach(t => drop.addEventListener(t, () => drop.classList.remove('drag')));
    drop.addEventListener('drop', e => { e.preventDefault(); addDocs(e.dataTransfer.files); });
    $('cfNewDocs').addEventListener('input', e => { const i = e.target.dataset.docTitle; if (i !== undefined) newDocs[i].title = e.target.value; });
    $('cfNewDocs').addEventListener('click', e => { const b = e.target.closest('[data-doc-rm]'); if (!b) return; newDocs.splice(+b.dataset.docRm, 1); renderNewDocs(); });

    function renderSavedDocs(docs) {
        $('cfSavedLabel').hidden = !docs.length;
        $('cfSavedDocs').innerHTML = docs.map(d => `<li class="cf-doc saved" data-doc="${d.id}">
            <i class="${icon(d.original_name)}"></i>
            <div class="meta"><b title="${esc(d.title)}">${esc(d.title)}</b><small>${esc(d.original_name)} · ${esc(d.size)} · ${esc(d.uploaded_at)}</small></div>
            <div class="acts">
                <button type="button" class="icon-btn" data-open="${esc(d.url)}" aria-label="View"><i class="fa-regular fa-eye"></i></button>
                <button type="button" class="icon-btn danger" data-doc-del="${d.id}" aria-label="Delete"><i class="fa-regular fa-trash-can"></i></button>
            </div>
        </li>`).join('');
    }
    $('cfSavedDocs').addEventListener('click', async e => {
        const del = e.target.closest('[data-doc-del]');
        if (!del) return;
        if (!confirm('Delete this document permanently?')) return;
        del.classList.add('loading');
        try {
            const r = await API.del(`/cases/${id}/documents/${del.dataset.docDel}`);
            caseData.documents = caseData.documents.filter(d => String(d.id) !== del.dataset.docDel);
            renderSavedDocs(caseData.documents);
            toast(r.message);
        } catch (err) { toast(err.message, true); del.classList.remove('loading'); }
    });
    form.addEventListener('click', e => { const o = e.target.closest('[data-open]'); if (o) { e.preventDefault(); openFile(o.dataset.open); } });

    /* ---------- more details ---------- */
    const moreBtn = $('cfMoreBtn'), more = $('cfMore');
    const openMore = (open = true) => { more.classList.toggle('open', open); moreBtn.classList.toggle('open', open); moreBtn.setAttribute('aria-expanded', open); };
    moreBtn.addEventListener('click', () => openMore(!more.classList.contains('open')));

    /* ---------- side checklist ---------- */
    function progress() {
        const ok = {
            title: el('title').value.trim().length >= 3,
            practice_area_id: !!el('practice_area_id').value,
            client_name: el('client_name').value.trim().length >= 3,
            client_phone: /^[6-9]\d{9}$/.test(el('client_phone').value),
            aadhaar_number: /^[2-9]\d{11}$/.test(el('aadhaar_number').value.replace(/\D/g, '')),
            aadhaar_file: !!el('aadhaar_file').files[0] || !!(caseData && caseData.aadhaar_file_url),
        };
        let n = 0;
        Object.entries(ok).forEach(([k, v]) => { form.querySelector(`[data-chk="${k}"]`).classList.toggle('ok', v); if (v) n++; });
        $('cfProgress').style.width = (n / 6 * 100) + '%';
        $('cfTitle').textContent = el('title').value.trim() || (id ? 'Edit Case' : 'Create Case');
    }
    const clearErr = e => { const g = e.target.closest('.has-error'); if (g) { g.classList.remove('has-error'); g.querySelector('.f-error')?.remove(); } progress(); };
    form.addEventListener('input', clearErr);
    form.addEventListener('change', clearErr);

    /* ---------- edit: data bharo ---------- */
    function kycNow(box, url, label, removable) {
        box.hidden = !url;
        box.innerHTML = url ? `<i class="fa-solid fa-circle-check" style="color:#5fe0a5"></i> ${label} uploaded ·
            <button type="button" data-open="${esc(url)}"><i class="fa-regular fa-eye"></i> View</button>
            ${removable ? ' · <button type="button" class="rm" id="cfPanRm"><i class="fa-regular fa-trash-can"></i> Remove</button>' : ' · upload again to replace'}` : '';
    }

    if (id) {
        openMore(true);
        form.classList.add('is-loading');
        Promise.all([API.get(`/cases/${id}?full=1`), optionsReady]).then(([r]) => {
            const c = caseData = r.data;
            ['title', 'next_hearing_date', 'priority', 'client_name', 'client_phone', 'pan_number',
             'court_name', 'opponent_name', 'filing_date', 'client_email', 'description'].forEach(k => { if (c[k] != null) el(k).value = c[k]; });
            el('aadhaar_number').value = (c.aadhaar_number || '').replace(/(\d{4})(?=\d)/g, '$1 ');
            const st = form.querySelector(`[name="status"][value="${c.status}"]`); if (st) st.checked = true;

            // deleted / inactive area ya advocate bhi dropdown me dikhe
            const ensure = (sel, val, text) => {
                if (!val) return;
                if (![...sel.options].some(o => o.value == val)) sel.insertAdjacentHTML('beforeend', `<option value="${val}">${esc(text)} (inactive)</option>`);
                sel.value = val;
            };
            ensure(el('practice_area_id'), c.practice_area_id, c.practice_area?.name || 'Practice area #' + c.practice_area_id);
            ensure(el('advocate_id'), c.advocate_id, c.advocate?.name || 'Advocate #' + c.advocate_id);

            $('cfCaseNo').textContent = c.case_no;
            kycNow($('cfAadhaarNow'), c.aadhaar_file_url, 'Aadhaar card', false);
            kycNow($('cfPanNow'), c.pan_file_url, 'PAN card', true);
            form.querySelector('[data-field="aadhaar_file"] small[data-file-label]').textContent = 'Already uploaded · choose a file to replace';
            renderSavedDocs(c.documents || []);
            progress();
        }).catch(err => {
            toast(err.message, true);
            if (err.status === 404) setTimeout(() => location.replace(LIST), 1200);
        }).finally(() => form.classList.remove('is-loading'));

        form.addEventListener('click', e => {
            if (!e.target.closest('#cfPanRm')) return;
            el('remove_pan_file').value = '1';
            caseData.pan_file_url = null;
            kycNow($('cfPanNow'), null);
            toast('PAN card will be removed when you save.');
        });
    }

    /* ---------- submit ---------- */
    form.addEventListener('submit', async e => {
        e.preventDefault();
        API.clearErrors(form);

        const errs = {};
        if (el('title').value.trim().length < 3) errs.title = ['Please enter the case title / matter.'];
        if (!el('practice_area_id').value) errs.practice_area_id = ['Please select a practice area.'];
        if (el('client_name').value.trim().length < 3) errs.client_name = ['Please enter the client name.'];
        if (!/^[6-9]\d{9}$/.test(el('client_phone').value)) errs.client_phone = ['Enter a valid 10-digit Indian mobile number.'];
        if (!/^[2-9]\d{11}$/.test(el('aadhaar_number').value.replace(/\D/g, ''))) errs.aadhaar_number = ['Enter a valid 12-digit Aadhaar number.'];
        if (!el('aadhaar_file').files[0] && !(caseData && caseData.aadhaar_file_url)) errs.aadhaar_file = ['Please upload the Aadhaar card (mandatory).'];
        if (el('pan_number').value && !/^[A-Z]{5}[0-9]{4}[A-Z]$/.test(el('pan_number').value)) errs.pan_number = ['Enter a valid PAN number, e.g. ABCDE1234F.'];
        if (el('client_email').value && !el('client_email').checkValidity()) { errs.client_email = ['Enter a valid email address.']; openMore(true); }
        if (Object.keys(errs).length) {
            API.showErrors(form, errs)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        const fd = API.formData(form);
        fd.set('aadhaar_number', el('aadhaar_number').value.replace(/\D/g, ''));
        newDocs.forEach((d, i) => { fd.append(`documents[${i}]`, d.file); fd.append(`document_titles[${i}]`, d.title || ''); });

        btn.disabled = true; btn.classList.add('loading');
        try {
            const r = await API.post(id ? '/cases/' + id : '/cases', fd);
            try { sessionStorage.setItem('cs_flash', r.message); } catch (x) {}
            toast(r.message);
            setTimeout(() => { location.href = LIST + '/' + r.data.id; }, 700);
        } catch (err) {
            const keys = Object.keys(err.errors || {});
            if (keys.some(k => ['court_name', 'opponent_name', 'filing_date', 'client_email', 'description'].includes(k))) openMore(true);
            // documents.3 -> documents
            const mapped = {};
            keys.forEach(k => { mapped[k.split('.')[0]] = err.errors[k]; });
            API.showErrors(form, mapped)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            toast(err.message, true);
            btn.disabled = false; btn.classList.remove('loading');
        }
    });

    progress();
    if (!id) setTimeout(() => el('title').focus(), 200);
});
</script>
@endpush