@extends('frontlayout.app')
@section('title', 'Case Details')
@section('page_title', 'Case Details')
{{-- superadmin + assigned advocate (admin) dono dekh sakte hain --}}

@section('page_actions')
    <a href="{{ route('superadmin.cases') }}" class="adm-btn adm-btn-ghost"><i class="fa-solid fa-arrow-left"></i> <span class="hide-xs">All Cases</span></a>
    <a href="{{ route('superadmin.cases.edit', $id) }}" class="adm-btn adm-btn-gold only-superadmin"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
    <button class="adm-btn adm-btn-ghost only-superadmin cv-del-btn" id="cvDelete"><i class="fa-regular fa-trash-can"></i> <span class="hide-xs">Delete</span></button>
@endsection

@push('styles')
<style>
/* ===== CASE DETAILS (isi file me) ===== */
.cv-hero { position: relative; overflow: hidden; display: flex; flex-wrap: wrap; align-items: center; gap: 20px; padding: 28px 30px; margin-bottom: 22px;
    background: linear-gradient(130deg, rgba(212,175,55,.16), rgba(255,255,255,.03) 55%); border-color: rgba(212,175,55,.3); animation: cvUp .5s ease both; }
.cv-hero::after { content: ''; position: absolute; top: 0; left: -60%; width: 40%; height: 100%; transform: skewX(-20deg); pointer-events: none;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.1), transparent); animation: cvSweep 6s ease-in-out infinite 1s; }
@keyframes cvSweep { 0% { left: -60%; } 35%, 100% { left: 130%; } }
@keyframes cvUp { from { opacity: 0; transform: translateY(16px); } }
.cv-ico { width: 70px; height: 70px; flex-shrink: 0; border-radius: 20px; display: grid; place-items: center; font-size: 28px; color: #111; background: var(--gold-grad); box-shadow: 0 14px 34px rgba(212,175,55,.35); }
.cv-hero .info { flex: 1; min-width: 220px; }
.cv-hero h2 { font-family: var(--serif, serif); color: #fff; font-size: clamp(24px, 3vw, 34px); line-height: 1.15; margin: 6px 0 10px; overflow-wrap: anywhere; }
.cv-tags { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
.cv-next { text-align: right; padding: 14px 18px; border-radius: 16px; background: rgba(0,0,0,.25); border: 1px solid var(--border); }
.cv-next small { display: block; color: var(--muted); font-size: 11.5px; letter-spacing: 1.5px; text-transform: uppercase; }
.cv-next b { color: #fff; font-size: 20px; }
.cv-next em { display: block; font-style: normal; font-size: 12px; color: var(--gold-light); }
.status.red { color: #ff8c8c; background: rgba(255,107,107,.1); }

.cv-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; }
.cv-card { padding: 24px; animation: cvUp .5s ease both; }
.cv-card:nth-child(2) { animation-delay: .06s; } .cv-card:nth-child(3) { animation-delay: .12s; } .cv-card:nth-child(4) { animation-delay: .18s; }
.cv-card.full { grid-column: 1 / -1; }
.cv-card h4 { display: flex; align-items: center; gap: 10px; color: #fff; font-size: 16px; margin-bottom: 16px; }
.cv-card h4 i { color: var(--gold); }
.cv-kv { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 20px; }
.cv-kv div small { display: block; color: var(--muted); font-size: 12px; margin-bottom: 3px; }
.cv-kv div b { color: #eee; font-size: 14px; font-weight: 500; overflow-wrap: anywhere; }
.cv-kv .full { grid-column: 1 / -1; }
.cv-mono { font-family: ui-monospace, Consolas, monospace; letter-spacing: 1px; }
.cv-reveal { background: none; border: 0; color: var(--gold-light); cursor: pointer; font-size: 12px; margin-left: 6px; }
.cv-desc { color: #ccc; font-size: 14px; line-height: 1.75; white-space: pre-wrap; overflow-wrap: anywhere; }

.cv-kyc { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 18px; }
.cv-file { display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 14px; border: 1px solid var(--border); background: rgba(255,255,255,.02); transition: .3s; }
.cv-file:hover { border-color: rgba(212,175,55,.4); }
.cv-file > i { width: 40px; height: 40px; flex-shrink: 0; border-radius: 11px; display: grid; place-items: center; color: var(--gold); background: rgba(212,175,55,.1); }
.cv-file.missing > i { color: #777; background: rgba(255,255,255,.04); }
.cv-file .m { flex: 1; min-width: 0; }
.cv-file b { display: block; color: #fff; font-size: 13.5px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cv-file small { color: var(--muted); font-size: 11.5px; }
.cv-file .acts { display: flex; gap: 6px; }
.cv-docs { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 12px; }
.cv-empty { color: var(--muted); font-size: 14px; text-align: center; padding: 24px 10px; }
.cv-empty i { display: block; font-size: 28px; color: rgba(212,175,55,.5); margin-bottom: 8px; }

.adm-btn.cv-del-btn { color: #ff8c8c; }
.cv-del { text-align: center; padding: 10px 6px 4px; }
.cv-del .ring { width: 76px; height: 76px; margin: 0 auto 16px; border-radius: 50%; display: grid; place-items: center; font-size: 30px; color: #ff6b6b; background: rgba(255,107,107,.1); }
.cv-del h4 { color: #fff; font-size: 20px; margin-bottom: 8px; }
.cv-del p { color: var(--muted); font-size: 14px; margin-bottom: 22px; }
.cv-del .btns { display: flex; gap: 10px; justify-content: center; }
.adm-btn-danger { color: #fff; background: linear-gradient(135deg, #ff7a7a, #d63c3c); border: 0; }

.cv-skel { height: 160px; border-radius: 20px; margin-bottom: 22px; background: linear-gradient(90deg, rgba(255,255,255,.03) 25%, rgba(255,255,255,.07) 50%, rgba(255,255,255,.03) 75%); background-size: 200% 100%; animation: cvSh 1.3s linear infinite; }
@keyframes cvSh { to { background-position: -200% 0; } }

@media (max-width: 991px) { .cv-grid { grid-template-columns: 1fr; } }
@media (max-width: 575px) {
    .cv-hero { padding: 22px 18px; }
    .cv-next { text-align: left; width: 100%; }
    .cv-card { padding: 20px 16px; }
    .cv-kv, .cv-kyc { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

<div id="cvRoot" data-id="{{ $id }}">
    <div class="cv-skel"></div>
    <div class="cv-grid"><div class="cv-skel"></div><div class="cv-skel"></div></div>
</div>

@endsection

@push('modals')
<div class="adm-modal" id="cvDeleteModal" aria-hidden="true">
    <div class="adm-modal-box glass-card">
        <div class="cv-del">
            <div class="ring"><i class="fa-regular fa-trash-can"></i></div>
            <h4>Delete this case?</h4>
            <p>The case, Aadhaar, PAN and all documents will be permanently deleted.</p>
            <div class="btns">
                <button class="adm-btn adm-btn-ghost" data-close-modal>Cancel</button>
                <button class="adm-btn adm-btn-danger" id="cvDelConfirm"><i class="fa-regular fa-trash-can"></i> Yes, Delete</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
/* ===== CASE DETAILS – API (Bearer token) ===== */
document.addEventListener('auth:ready', function (ev) {
    if (!window.API) return;
    const isSA  = ev.detail.role === 'superadmin';
    const root  = document.getElementById('cvRoot');
    const id    = root.dataset.id;
    const esc   = API.esc;
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const LIST  = @json(route('superadmin.cases'));
    const STATUS_CLS = { active: 'green', hearing: 'gold', pending: 'blue', closed: 'grey' };
    const PRIO_CLS   = { urgent: 'red', high: 'gold', normal: 'grey', low: 'grey' };
    const cap   = s => s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
    const v     = x => x ? esc(x) : '<span style="color:var(--muted)">—</span>';
    const icon  = n => /\.pdf$/i.test(n) ? 'fa-regular fa-file-pdf' : /\.(docx?)$/i.test(n) ? 'fa-regular fa-file-word' : 'fa-regular fa-file-image';
    let c = null;

    /* private file (token ke saath) – view ya download */
    async function getFile(url, download, name) {
        const w = download ? null : window.open('', '_blank');
        try {
            const r = await fetch(url, { headers: { Authorization: 'Bearer ' + API.token(), Accept: '*/*' } });
            if (!r.ok) throw new Error('Could not open file (' + r.status + ')');
            const u = URL.createObjectURL(await r.blob());
            if (download) { const a = document.createElement('a'); a.href = u; a.download = name || 'document'; a.click(); setTimeout(() => URL.revokeObjectURL(u), 3000); }
            else if (w) w.location = u; else location.href = u;
        } catch (e) { if (w) w.close(); toast(e.message, true); }
    }

    const fileCard = (title, sub, url, name, ic) => url
        ? `<div class="cv-file"><i class="${ic}"></i><div class="m"><b>${esc(title)}</b><small>${esc(sub)}</small></div>
            <div class="acts">
                <button class="icon-btn" data-view="${esc(url)}" aria-label="View"><i class="fa-regular fa-eye"></i></button>
                <button class="icon-btn" data-dl="${esc(url)}" data-name="${esc(name)}" aria-label="Download"><i class="fa-solid fa-download"></i></button>
            </div></div>`
        : `<div class="cv-file missing"><i class="${ic}"></i><div class="m"><b>${esc(title)}</b><small>Not uploaded</small></div></div>`;

    function daysTo(d) {
        if (!d) return '';
        const n = Math.round((new Date(d + 'T00:00:00') - new Date(new Date().toDateString())) / 864e5);
        return n === 0 ? 'Today' : n === 1 ? 'Tomorrow' : n > 0 ? `in ${n} days` : `${-n} days ago`;
    }

    function render() {
        document.title = c.case_no + ' | Lexora';
        root.innerHTML = `
        <div class="glass-card cv-hero">
            <span class="cv-ico"><i class="fa-solid fa-gavel"></i></span>
            <div class="info">
                <div class="cv-tags">
                    <span class="case-id">${esc(c.case_no)}</span>
                    <span class="status ${STATUS_CLS[c.status] || 'grey'}">${esc(c.status_label)}</span>
                    <span class="status ${PRIO_CLS[c.priority] || 'grey'}"><i class="fa-solid fa-flag"></i> ${esc(cap(c.priority))}</span>
                    ${c.practice_area ? `<span class="tag">${esc(c.practice_area.name)}</span>` : ''}
                </div>
                <h2>${esc(c.title)}</h2>
                <small class="t-sub">Created ${esc(c.created_at)}${c.created_by ? ' by ' + esc(c.created_by) : ''} · Updated ${esc(c.updated_at)}${c.updated_by ? ' by ' + esc(c.updated_by) : ''}</small>
            </div>
            <div class="cv-next"><small>Next Hearing</small><b>${esc(c.next_hearing || 'Not set')}</b>${c.next_hearing_date ? `<em>${daysTo(c.next_hearing_date)}</em>` : ''}</div>
        </div>

        <div class="cv-grid">
            <div class="glass-card cv-card">
                <h4><i class="fa-solid fa-briefcase"></i> Case Information</h4>
                <div class="cv-kv">
                    <div><small>Practice Area</small><b>${v(c.practice_area?.name)}</b></div>
                    <div><small>Type</small><b>${v(c.practice_area?.type)}</b></div>
                    <div><small>Assigned Advocate</small><b>${c.advocate ? esc(c.advocate.name) : '<span style="color:var(--muted)">Unassigned</span>'}</b></div>
                    <div><small>Advocate Contact</small><b>${c.advocate ? esc(c.advocate.phone || c.advocate.email) : v()}</b></div>
                    <div><small>Court</small><b>${v(c.court_name)}</b></div>
                    <div><small>Opponent / Other Party</small><b>${v(c.opponent_name)}</b></div>
                    <div><small>Filing Date</small><b>${v(c.filing_date)}</b></div>
                    <div><small>Status</small><b>${esc(c.status_label)}</b></div>
                </div>
            </div>

            <div class="glass-card cv-card">
                <h4><i class="fa-regular fa-id-card"></i> Client & KYC</h4>
                <div class="cv-kv">
                    <div><small>Client Name</small><b>${esc(c.client_name)}</b></div>
                    <div><small>Mobile</small><b><a href="tel:${esc(c.client_phone)}" class="gold">${esc(c.client_phone)}</a></b></div>
                    <div class="full"><small>Email</small><b>${c.client_email ? `<a href="mailto:${esc(c.client_email)}" class="gold">${esc(c.client_email)}</a>` : v()}</b></div>
                    <div><small>Aadhaar Number</small><b class="cv-mono" id="cvAadhaar">${esc(c.aadhaar_masked)}</b>${c.aadhaar_number ? '<button class="cv-reveal" data-reveal="aadhaar"><i class="fa-regular fa-eye"></i></button>' : ''}</div>
                    <div><small>PAN Number</small><b class="cv-mono" id="cvPan">${v(c.pan_masked)}</b>${c.pan_number ? '<button class="cv-reveal" data-reveal="pan"><i class="fa-regular fa-eye"></i></button>' : ''}</div>
                </div>
                <div class="cv-kyc">
                    ${fileCard('Aadhaar Card', 'Mandatory · KYC', c.aadhaar_file_url, c.case_no + '-aadhaar', 'fa-solid fa-id-card')}
                    ${fileCard('PAN Card', 'Optional · KYC', c.pan_file_url, c.case_no + '-pan', 'fa-regular fa-address-card')}
                </div>
            </div>

            <div class="glass-card cv-card full">
                <h4><i class="fa-solid fa-paperclip"></i> Documents <span class="tag" style="margin-left:auto">${(c.documents || []).length}</span></h4>
                ${(c.documents || []).length
                    ? `<div class="cv-docs">${c.documents.map(d => fileCard(d.title, `${d.original_name} · ${d.size} · ${d.uploaded_at}`, d.url, d.original_name, icon(d.original_name))).join('')}</div>`
                    : `<div class="cv-empty"><i class="fa-regular fa-folder-open"></i>No documents uploaded yet.${isSA ? ` <a class="gold" href="${LIST}/${c.id}/edit">Upload now</a>` : ''}</div>`}
            </div>

            ${c.description ? `<div class="glass-card cv-card full"><h4><i class="fa-regular fa-note-sticky"></i> Description / Notes</h4><div class="cv-desc">${esc(c.description)}</div></div>` : ''}
        </div>`;
    }

    root.addEventListener('click', e => {
        const vw = e.target.closest('[data-view]'); if (vw) return getFile(vw.dataset.view, false);
        const dl = e.target.closest('[data-dl]');  if (dl) return getFile(dl.dataset.dl, true, dl.dataset.name);
        const rv = e.target.closest('[data-reveal]');
        if (rv) {
            const isA = rv.dataset.reveal === 'aadhaar', box = document.getElementById(isA ? 'cvAadhaar' : 'cvPan');
            const showing = rv.classList.toggle('on');
            box.textContent = showing ? (isA ? c.aadhaar_number.replace(/(\d{4})(?=\d)/g, '$1 ') : c.pan_number) : (isA ? c.aadhaar_masked : c.pan_masked);
            rv.innerHTML = `<i class="fa-regular fa-eye${showing ? '-slash' : ''}"></i>`;
        }
    });

    /* delete */
    document.getElementById('cvDelete')?.addEventListener('click', () => window.admOpenModal('cvDeleteModal'));
    document.getElementById('cvDelConfirm').addEventListener('click', async e => {
        const b = e.currentTarget; b.disabled = true; b.classList.add('loading');
        try {
            const r = await API.del('/cases/' + id);
            try { sessionStorage.setItem('cs_flash', r.message); } catch (x) {}
            location.href = LIST;
        } catch (err) { toast(err.message, true); b.disabled = false; b.classList.remove('loading'); }
    });

    API.get(`/cases/${id}${isSA ? '?full=1' : ''}`).then(r => { c = r.data; render(); })
        .catch(err => {
            root.innerHTML = `<div class="glass-card card"><div class="t-empty error"><i class="fa-solid fa-triangle-exclamation"></i><p>${esc(err.message)}</p>
                <a href="${LIST}" class="adm-btn adm-btn-ghost">Back to Cases</a></div></div>`;
        });

    try { const f = sessionStorage.getItem('cs_flash'); if (f) { sessionStorage.removeItem('cs_flash'); setTimeout(() => toast(f), 300); } } catch (e) {}
});
</script>
@endpush