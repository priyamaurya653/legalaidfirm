@extends('frontlayout.app')
@section('title', 'Career Applications')
@section('page_title', 'Career Applications')
@section('require_role', 'superadmin')

@section('page_actions')
    <a href="{{ route('superadmin.career.jobs') }}" class="adm-btn adm-btn-ghost"><i class="fa-solid fa-briefcase"></i> <span class="hide-xs">Job Openings</span></a>
@endsection

@push('styles')
<style>
/* ===== CAREER APPLICATIONS (isi file me) ===== */
.ap-stats { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 20px; }
.ap-stat { display: flex; align-items: center; gap: 14px; padding: 16px 18px; cursor: pointer; text-align: left; font: inherit; color: inherit; transition: .35s; position: relative; overflow: hidden; }
.ap-stat:hover, .ap-stat.on { transform: translateY(-4px); border-color: rgba(212,175,55,.45); }
.ap-stat.on::after { content: ''; position: absolute; left: 0; bottom: 0; width: 100%; height: 3px; background: var(--gold-grad); }
.ap-stat b { display: block; font-size: 22px; color: #fff; line-height: 1.1; }
.ap-stat small { color: var(--muted); font-size: 12.5px; }
.ap-toolbar { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.ap-select { height: 40px; max-width: 260px; padding: 0 34px 0 14px; border-radius: 12px; color: #ddd; font: inherit; font-size: 13px; cursor: pointer; appearance: none;
    background: rgba(255,255,255,.04) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath fill='%23d4af37' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E") no-repeat right 14px center; border: 1px solid var(--border); }
.ap-select option { background: #111; }
.adm-table tr.unread { background: rgba(212,175,55,.04); }
.adm-table tr.unread .t-title::after { content: ''; display: inline-block; width: 7px; height: 7px; margin-left: 7px; border-radius: 50%; background: var(--gold); box-shadow: 0 0 8px var(--gold); vertical-align: middle; }
.adm-table tr { cursor: default; }
.status.red { color: #ff8c8c; background: rgba(255,107,107,.1); }
.adm-table tr.removing { opacity: 0; transform: translateX(30px); transition: .35s; }

.av-head { display: flex; align-items: center; gap: 14px; margin-bottom: 18px; }
.av-head .avatar-txt { width: 56px; height: 56px; font-size: 22px; }
.av-head h4 { color: #fff; font-size: 20px; margin: 0 0 4px; }
.av-kv { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px 18px; margin-bottom: 16px; }
.av-kv small { display: block; color: var(--muted); font-size: 12px; margin-bottom: 2px; }
.av-kv b { color: #eee; font-size: 13.5px; font-weight: 500; overflow-wrap: anywhere; }
.av-box { padding: 14px; border-radius: 12px; background: rgba(255,255,255,.03); border: 1px solid var(--border); color: #ddd; font-size: 13.5px; line-height: 1.7; white-space: pre-wrap; overflow-wrap: anywhere; margin-bottom: 16px; max-height: 200px; overflow-y: auto; }
.av-resume { display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 14px; border: 1px solid rgba(212,175,55,.3); background: rgba(212,175,55,.05); margin-bottom: 16px; }
.av-resume > i { width: 42px; height: 42px; border-radius: 11px; display: grid; place-items: center; font-size: 18px; color: var(--gold); background: rgba(212,175,55,.12); }
.av-resume div { flex: 1; min-width: 0; }
.av-resume b { display: block; color: #fff; font-size: 13.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.av-resume small { color: var(--muted); font-size: 12px; }
.av-status { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin-bottom: 14px; }
.av-status > small { color: var(--muted); font-size: 12px; margin-right: 4px; }
.av-status button.active { color: #111; background: var(--gold-grad); border-color: transparent; }
.av-notes textarea { width: 100%; min-height: 70px; padding: 10px 12px; border-radius: 12px; border: 1px solid var(--border-2); background: rgba(0,0,0,.35); color: #fff; font: inherit; font-size: 13.5px; resize: vertical; }
.av-notes .row { display: flex; justify-content: flex-end; margin: 8px 0 16px; }

.ap-del { text-align: center; padding: 10px 6px 4px; }
.ap-del .ring { width: 76px; height: 76px; margin: 0 auto 16px; border-radius: 50%; display: grid; place-items: center; font-size: 30px; color: #ff6b6b; background: rgba(255,107,107,.1); }
.ap-del h4 { color: #fff; font-size: 20px; margin-bottom: 8px; }
.ap-del p { color: var(--muted); font-size: 14px; margin-bottom: 22px; }
.ap-del .btns { display: flex; gap: 10px; justify-content: center; }
.adm-btn-danger { color: #fff; background: linear-gradient(135deg, #ff7a7a, #d63c3c); border: 0; }

@media (max-width: 1199px) { .ap-stats { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 767px) { .ap-stats { grid-template-columns: repeat(2, 1fr); } .ap-toolbar { flex-direction: column; align-items: stretch; } .ap-select { max-width: none; } .ap-toolbar .tb-search { width: 100%; } .av-kv { grid-template-columns: 1fr 1fr; } }
@media (max-width: 575px) { .av-kv { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

<div class="ap-stats">
    <button class="glass-card ap-stat on" data-q=""><span class="sc-icon gold"><i class="fa-solid fa-inbox"></i></span><div><b data-as="total">–</b><small>All Applications</small></div></button>
    <button class="glass-card ap-stat" data-q="new"><span class="sc-icon blue"><i class="fa-solid fa-star"></i></span><div><b data-as="new">–</b><small>New</small></div></button>
    <button class="glass-card ap-stat" data-q="shortlisted"><span class="sc-icon purple"><i class="fa-solid fa-list-check"></i></span><div><b data-as="shortlisted">–</b><small>Shortlisted</small></div></button>
    <button class="glass-card ap-stat" data-q="hired"><span class="sc-icon green"><i class="fa-solid fa-user-check"></i></span><div><b data-as="hired">–</b><small>Hired</small></div></button>
    <button class="glass-card ap-stat" data-q="rejected"><span class="sc-icon" style="background:rgba(255,107,107,.12);color:#ff8c8c"><i class="fa-solid fa-user-xmark"></i></span><div><b data-as="rejected">–</b><small>Rejected</small></div></button>
</div>

<div class="glass-card card">
    <div class="ap-toolbar">
        <div class="tb-search sm">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Search name, email, phone, position…" id="apSearch" autocomplete="off">
        </div>
        <select class="ap-select" id="apJob" aria-label="Position"><option value="">All Positions</option></select>
    </div>

    <div class="table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Applicant</th>
                    <th>Position</th>
                    <th class="hide-sm">Phone</th>
                    <th class="hide-md">Applied</th>
                    <th>Status</th>
                    <th class="t-right">Action</th>
                </tr>
            </thead>
            <tbody id="apRows"></tbody>
        </table>
    </div>
    <div class="pager" id="apPager"></div>
</div>

@endsection

@push('modals')
<div class="adm-modal" id="apViewModal" aria-hidden="true">
    <div class="adm-modal-box glass-card wide">
        <div class="modal-head">
            <h4><i class="fa-solid fa-file-lines"></i> Application</h4>
            <button class="icon-btn" data-close-modal aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="apViewBody"></div>
    </div>
</div>
<div class="adm-modal" id="apDeleteModal" aria-hidden="true">
    <div class="adm-modal-box glass-card">
        <div class="ap-del">
            <div class="ring"><i class="fa-regular fa-trash-can"></i></div>
            <h4>Delete Application?</h4>
            <p>The application and resume will be permanently deleted.</p>
            <div class="btns">
                <button class="adm-btn adm-btn-ghost" data-close-modal>Cancel</button>
                <button class="adm-btn adm-btn-danger" id="apDelConfirm"><i class="fa-regular fa-trash-can"></i> Yes, Delete</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
/* ===== CAREER APPLICATIONS – API (Bearer token) ===== */
document.addEventListener('auth:ready', function (ev) {
    if (!window.API || ev.detail.role !== 'superadmin') return;
    const esc = API.esc, $ = i => document.getElementById(i);
    const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
    const body = $('apRows'), pager = $('apPager'), vbody = $('apViewBody');
    const CLS = { new: 'gold', shortlisted: 'blue', rejected: 'red', hired: 'green' };
    const LABEL = { new: 'New', shortlisted: 'Shortlisted', rejected: 'Rejected', hired: 'Hired' };
    const state = { page: 1, search: '', status: '', job_opening_id: new URLSearchParams(location.search).get('job') || '' };
    const cache = {};
    let delId = null, jobsLoaded = false;
    const debounce = (fn, ms = 350) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };
    const wa = p => 'https://wa.me/91' + String(p || '').replace(/\D/g, '').slice(-10);

    async function getFile(url, download, name) {
        const w = download ? null : window.open('', '_blank');
        try {
            const r = await fetch(url, { headers: { Authorization: 'Bearer ' + API.token(), Accept: '*/*' } });
            if (!r.ok) throw new Error('Could not open resume (' + r.status + ')');
            const u = URL.createObjectURL(await r.blob());
            if (download) { const a = document.createElement('a'); a.href = u; a.download = name || 'resume'; a.click(); setTimeout(() => URL.revokeObjectURL(u), 3000); }
            else if (w) w.location = u; else location.href = u;
        } catch (e) { if (w) w.close(); toast(e.message, true); }
    }

    const row = a => {
        const pid = 'apPop' + a.id;
        return `<tr data-id="${a.id}" class="${a.is_read ? '' : 'unread'}">
            <td><div class="t-user"><span class="avatar-txt">${esc(a.name.charAt(0).toUpperCase())}</span><div><div class="t-title">${esc(a.name)}</div><small class="t-sub">${esc(a.email)}</small></div></div></td>
            <td><span class="tag">${esc(a.position)}</span></td>
            <td class="hide-sm">+91 ${esc(a.phone)}</td>
            <td class="hide-md"><small title="${esc(a.created_at)}">${esc(a.time_ago)}</small></td>
            <td><span class="status ${CLS[a.status] || 'grey'}">${esc(a.status_label)}</span></td>
            <td class="t-right"><div class="popper-wrap">
                <button class="icon-btn" data-popper="${pid}" aria-label="Actions"><i class="fa-solid fa-ellipsis"></i></button>
                <div class="popper popper-right" id="${pid}" data-dyn><ul class="pop-menu">
                    <li><a href="#" data-ap="view" data-id="${a.id}"><i class="fa-regular fa-eye"></i> View</a></li>
                    ${a.resume_url ? `<li><a href="#" data-resume="${esc(a.resume_url)}"><i class="fa-regular fa-file-pdf"></i> Open Resume</a></li>` : ''}
                    <li><a href="tel:+91${esc(a.phone)}"><i class="fa-solid fa-phone"></i> Call</a></li>
                    <li><a href="mailto:${esc(a.email)}"><i class="fa-regular fa-envelope"></i> Email</a></li>
                    <li class="sep"></li>
                    <li><a href="#" class="danger" data-ap="delete" data-id="${a.id}"><i class="fa-regular fa-trash-can"></i> Delete</a></li>
                </ul></div></div></td>
        </tr>`;
    };
    const empty = (m, err) => { body.innerHTML = `<tr><td colspan="6"><div class="t-empty ${err ? 'error' : ''}"><i class="fa-solid ${err ? 'fa-triangle-exclamation' : 'fa-inbox'}"></i><p>${esc(m)}</p></div></td></tr>`; };
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
        body.innerHTML = Array.from({ length: 6 }, () => `<tr class="sk-row">${'<td><span class="sk sk-line w80"></span></td>'.repeat(6)}</tr>`).join('');
        const qs = new URLSearchParams({ page: state.page });
        ['search', 'status', 'job_opening_id'].forEach(k => { if (state[k]) qs.set(k, state[k]); });
        try {
            const r = await API.get('/applications?' + qs);
            r.data.forEach(a => { cache[a.id] = a; });
            Object.entries(r.stats || {}).forEach(([k, v]) => document.querySelectorAll(`[data-as="${k}"]`).forEach(x => { x.textContent = v; }));
            if (!jobsLoaded) {
                $('apJob').insertAdjacentHTML('beforeend', (r.jobs || []).map(j => `<option value="${j.id}">${esc(j.title)}</option>`).join(''));
                $('apJob').value = state.job_opening_id; jobsLoaded = true;
            }
            r.data.length ? (body.innerHTML = r.data.map(row).join('')) : empty(state.search || state.status || state.job_opening_id ? 'No application matches your filters.' : 'No applications yet. Website career form submissions will appear here.');
            renderPager(r.meta);
        } catch (err) { empty(err.message, true); pager.innerHTML = ''; }
    }

    $('apSearch').addEventListener('input', debounce(e => { state.search = e.target.value.trim(); state.page = 1; load(); }));
    $('apJob').addEventListener('change', e => { state.job_opening_id = e.target.value; state.page = 1; load(); });
    document.querySelectorAll('.ap-stat').forEach(c => c.addEventListener('click', () => {
        state.status = c.dataset.q; state.page = 1;
        document.querySelectorAll('.ap-stat').forEach(x => x.classList.toggle('on', x === c)); load();
    }));
    pager.addEventListener('click', e => { const b = e.target.closest('[data-page]'); if (!b || b.disabled) return; state.page = +b.dataset.page; load(); });

    /* ---------- view ---------- */
    function renderView(a) {
        vbody.dataset.id = a.id;
        vbody.innerHTML = `
            <div class="av-head"><span class="avatar-txt">${esc(a.name.charAt(0).toUpperCase())}</span>
                <div><h4>${esc(a.name)}</h4><span class="tag">${esc(a.position)}</span> <span class="status ${CLS[a.status] || 'grey'}">${esc(a.status_label)}</span></div></div>
            <div class="av-kv">
                <div><small>Email</small><b><a class="gold" href="mailto:${esc(a.email)}">${esc(a.email)}</a></b></div>
                <div><small>Phone</small><b><a class="gold" href="tel:+91${esc(a.phone)}">+91 ${esc(a.phone)}</a></b></div>
                <div><small>Applied</small><b>${esc(a.created_at)}</b></div>
            </div>
            ${a.resume_url ? `<div class="av-resume"><i class="fa-regular fa-file-pdf"></i><div><b>${esc(a.resume_name || 'Resume')}</b><small>Uploaded resume</small></div>
                <button class="icon-btn" data-resume="${esc(a.resume_url)}" aria-label="View"><i class="fa-regular fa-eye"></i></button>
                <button class="icon-btn" data-resume-dl="${esc(a.resume_url)}" data-name="${esc(a.resume_name || 'resume')}" aria-label="Download"><i class="fa-solid fa-download"></i></button></div>` : ''}
            ${a.cover ? `<small class="muted-sm">About the candidate</small><div class="av-box">${esc(a.cover)}</div>` : ''}
            <div class="av-status filter-chips"><small>Status:</small>
                ${Object.keys(LABEL).map(s => `<button data-st="${s}" class="${a.status === s ? 'active' : ''}">${LABEL[s]}</button>`).join('')}</div>
            <div class="av-notes"><small class="muted-sm">Internal notes (not visible to candidate)</small>
                <textarea id="apNotes" maxlength="3000" placeholder="Interview date, feedback…">${esc(a.notes || '')}</textarea>
                <div class="row"><button class="adm-btn adm-btn-ghost sm" id="apNotesSave"><i class="fa-regular fa-floppy-disk"></i> Save Notes</button></div></div>
            <div class="modal-foot">
                <a href="tel:+91${esc(a.phone)}" class="adm-btn adm-btn-ghost"><i class="fa-solid fa-phone"></i> Call</a>
                <a href="${wa(a.phone)}" target="_blank" class="adm-btn adm-btn-ghost"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
                <a href="mailto:${esc(a.email)}?subject=${encodeURIComponent('Your application – ' + a.position)}" class="adm-btn adm-btn-gold"><i class="fa-regular fa-envelope"></i> Email</a>
            </div>`;
    }
    async function openView(id) {
        cache[id] ? renderView(cache[id]) : (vbody.innerHTML = '<div class="t-empty"><i class="fa-solid fa-spinner fa-spin"></i></div>');
        window.admOpenModal('apViewModal');
        try {
            const r = await API.get('/applications/' + id);
            cache[id] = r.data; renderView(r.data);
            body.querySelector(`tr[data-id="${id}"]`)?.classList.remove('unread');
        } catch (err) { toast(err.message, true); }
    }
    vbody.addEventListener('click', async e => {
        const id = vbody.dataset.id;
        const st = e.target.closest('[data-st]');
        if (st && !st.classList.contains('active')) {
            try { const r = await API.patch('/applications/' + id, { status: st.dataset.st }); cache[id] = r.data; renderView(r.data); toast(r.message); load(); }
            catch (err) { toast(err.message, true); }
        }
        if (e.target.closest('#apNotesSave')) {
            try { const r = await API.patch('/applications/' + id, { notes: $('apNotes').value }); cache[id] = r.data; toast(r.message); }
            catch (err) { toast(err.message, true); }
        }
        const dl = e.target.closest('[data-resume-dl]'); if (dl) getFile(dl.dataset.resumeDl, true, dl.dataset.name);
    });

    document.addEventListener('click', e => {
        const rs = e.target.closest('[data-resume]'); if (rs) { e.preventDefault(); return getFile(rs.dataset.resume); }
        const a = e.target.closest('[data-ap]'); if (!a) return;
        e.preventDefault();
        if (a.dataset.ap === 'view') openView(a.dataset.id);
        if (a.dataset.ap === 'delete') { delId = a.dataset.id; window.admOpenModal('apDeleteModal'); }
    });
    $('apDelConfirm').addEventListener('click', async () => {
        const b = $('apDelConfirm'); b.disabled = true; b.classList.add('loading');
        try {
            const r = await API.del('/applications/' + delId);
            document.querySelector('#apDeleteModal [data-close-modal]').click();
            body.querySelector(`tr[data-id="${delId}"]`)?.classList.add('removing');
            toast(r.message); setTimeout(load, 350);
        } catch (err) { toast(err.message, true); }
        finally { b.disabled = false; b.classList.remove('loading'); }
    });

    load();
});
</script>
@endpush