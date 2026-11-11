@extends('frontlayout.app')
@php
$editId = $id ?? null;
$icons = ['fa-solid fa-gavel', 'fa-solid fa-scale-balanced', 'fa-solid fa-building-columns', 'fa-solid fa-landmark', 'fa-solid fa-house-chimney',
'fa-solid fa-people-roof', 'fa-solid fa-briefcase', 'fa-solid fa-file-contract', 'fa-solid fa-file-signature', 'fa-solid fa-handshake',
'fa-solid fa-car-burst', 'fa-solid fa-shield-halved', 'fa-solid fa-coins', 'fa-solid fa-heart-crack', 'fa-solid fa-laptop-code', 'fa-solid fa-stamp'];
@endphp
@section('title', $editId ? 'Edit Practice Area' : 'Add Practice Area')
@section('page_title', $editId ? 'Edit Practice Area' : 'Add Practice Area')
@section('require_role', 'superadmin')

@section('page_actions')
<a href="{{ route('superadmin.practice-areas') }}" class="adm-btn adm-btn-ghost"><i class="fa-solid fa-list"></i> All Practice Areas</a>
@endsection

@push('styles')
<style>
    /* ===== ADD / EDIT PRACTICE AREA (isi file me) ===== */
    .pa-form-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 24px;
        align-items: start;
    }

    .pa-sec {
        padding: 26px;
        margin-bottom: 22px;
        animation: paUp .55s ease both;
    }

    .pa-sec:nth-child(2) {
        animation-delay: .08s;
    }

    @keyframes paUp {
        from {
            opacity: 0;
            transform: translateY(18px);
        }
    }

    .pa-sec-head {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 20px;
    }

    .pa-sec-num {
        width: 42px;
        height: 42px;
        flex-shrink: 0;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-weight: 600;
        color: #111;
        background: var(--gold-grad);
        box-shadow: 0 8px 20px rgba(212, 175, 55, .3);
    }

    .pa-sec-head h4 {
        color: #fff;
        font-size: 17px;
        margin: 0;
    }

    .pa-sec-head small {
        color: var(--muted);
        font-size: 12.5px;
    }

    .pa-sec-head .opt {
        margin-left: auto;
        font-size: 11px;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: var(--muted);
        padding: 4px 10px;
        border-radius: 20px;
        border: 1px solid var(--border);
    }

    .req {
        color: #ff7a7a;
        font-weight: 600;
    }

    /* type: text input + DB suggestions (dynamic) */
    .pa-type-input {
        position: relative;
    }

    .pa-type-input>i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gold);
        pointer-events: none;
    }

    .pa-type-input input {
        padding-left: 40px !important;
    }

    .pa-type-sugg {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
        min-height: 30px;
        align-items: center;
    }

    .pa-type-sugg>small {
        color: var(--muted);
        font-size: 12px;
        margin-right: 2px;
    }

    .pa-type-sugg button {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        cursor: pointer;
        font: inherit;
        font-size: 12.5px;
        color: #ccc;
        border: 1px solid var(--border);
        background: rgba(255, 255, 255, .03);
        transition: .25s;
        animation: paUp .35s ease both;
    }

    .pa-type-sugg button em {
        font-style: normal;
        font-size: 11px;
        color: var(--muted);
    }

    .pa-type-sugg button:hover {
        color: #fff;
        border-color: rgba(212, 175, 55, .45);
        transform: translateY(-2px);
    }

    .pa-type-sugg button.on {
        color: #111;
        background: var(--gold-grad);
        border-color: transparent;
    }

    .pa-type-sugg button.on em {
        color: #3a2f0a;
    }

    .pa-type-sugg .sk {
        width: 80px;
        height: 28px;
        border-radius: 20px;
    }

    .pa-type-new {
        font-size: 12px;
        color: #7fe0a6;
        display: none;
        margin-top: 6px;
    }

    .pa-type-new.show {
        display: block;
    }


    /* more-details toggle */
    .pa-more-btn {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        margin-bottom: 22px;
        border-radius: 16px;
        cursor: pointer;
        font: inherit;
        color: #ddd;
        text-align: left;
        border: 1px dashed rgba(212, 175, 55, .35);
        background: rgba(212, 175, 55, .04);
        transition: .3s;
    }

    .pa-more-btn:hover {
        background: rgba(212, 175, 55, .08);
        color: #fff;
    }

    .pa-more-btn>i:first-child {
        color: var(--gold);
    }

    .pa-more-btn .caret {
        margin-left: auto;
        transition: .35s;
        color: var(--gold);
    }

    .pa-more-btn.open .caret {
        transform: rotate(180deg);
    }

    .pa-more {
        display: none;
    }

    .pa-more.open {
        display: block;
    }

    .pa-input-addon {
        position: relative;
    }

    .pa-input-addon>span {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
        font-size: 13px;
        pointer-events: none;
        font-family: ui-monospace, Consolas, monospace;
    }

    .pa-input-addon input {
        padding-left: 128px !important;
        font-family: ui-monospace, Consolas, monospace;
    }

    .pa-icon-row {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .pa-icon-row input {
        flex: 1;
    }

    .pa-icon-now {
        width: 46px;
        height: 46px;
        flex-shrink: 0;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 18px;
        color: #111;
        background: var(--gold-grad);
    }

    .pa-icons {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 8px;
        margin-top: 10px;
    }

    .pa-icons button {
        aspect-ratio: 1;
        border-radius: 10px;
        border: 1px solid var(--border);
        background: rgba(255, 255, 255, .03);
        color: #bbb;
        cursor: pointer;
        font-size: 15px;
        transition: .25s;
    }

    .pa-icons button:hover {
        color: var(--gold);
        border-color: rgba(212, 175, 55, .4);
        transform: translateY(-2px);
    }

    .pa-icons button.on {
        color: #111;
        background: var(--gold-grad);
        border-color: transparent;
    }

    .pa-img-up {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .pa-img-prev {
        position: relative;
        width: 140px;
        height: 92px;
        flex-shrink: 0;
        border-radius: 14px;
        overflow: hidden;
        display: grid;
        place-items: center;
        color: #555;
        font-size: 26px;
        border: 1px dashed var(--border-2, rgba(255, 255, 255, .15));
        background: rgba(255, 255, 255, .02);
    }

    .pa-img-prev img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pa-img-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .pa-img-actions .adm-btn {
        height: 38px;
        padding: 0 14px;
        font-size: 13px;
    }

    .pa-count {
        float: right;
        font-size: 11.5px;
        color: var(--muted);
    }

    .pa-count.over {
        color: #ff7a7a;
    }

    .pa-status-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 16px;
        border-radius: 14px;
        border: 1px solid var(--border);
        background: rgba(255, 255, 255, .02);
    }

    .pa-status-row h6 {
        color: #fff;
        font-size: 14px;
        font-weight: 500;
        margin: 0 0 2px;
    }

    .pa-status-row small {
        color: var(--muted);
        font-size: 12px;
    }

    /* side: live preview */
    .pa-side {
        position: sticky;
        top: 90px;
        padding: 22px;
    }

    .pa-side h5 {
        color: var(--muted);
        font-size: 11.5px;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 14px;
    }

    .pa-preview {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 18px;
        border: 1px solid rgba(212, 175, 55, .25);
        background: #0c0c0c;
        transition: .4s;
    }

    .pa-preview:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 50px rgba(0, 0, 0, .6), 0 0 0 1px rgba(212, 175, 55, .35);
    }

    .pa-preview .pv-img {
        height: 130px;
        background: radial-gradient(ellipse at 30% 0%, rgba(212, 175, 55, .3), transparent 60%), linear-gradient(135deg, #1a1a1a, #0b0b0b);
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .pa-preview .pv-img::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent 30%, rgba(12, 12, 12, .95));
    }

    .pa-preview .pv-ico {
        position: absolute;
        left: 18px;
        top: 102px;
        z-index: 1;
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        font-size: 22px;
        color: #111;
        background: var(--gold-grad);
        box-shadow: 0 10px 26px rgba(212, 175, 55, .4);
    }

    .pa-preview .pv-body {
        padding: 38px 18px 20px;
    }

    .pa-preview .pv-type {
        font-size: 11px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--gold);
    }

    .pa-preview h4 {
        font-family: var(--serif, serif);
        color: #fff;
        font-size: 22px;
        margin: 4px 0 8px;
        overflow-wrap: anywhere;
    }

    .pa-preview p {
        color: var(--muted);
        font-size: 13px;
        line-height: 1.6;
        margin: 0;
        overflow-wrap: anywhere;
    }

    .pa-preview .pv-badge {
        position: absolute;
        right: 12px;
        top: 12px;
        z-index: 1;
    }

    .pa-meta {
        list-style: none;
        padding: 0;
        margin: 0 0 18px;
        font-size: 12.5px;
        color: var(--muted);
    }

    .pa-meta li {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        padding: 7px 0;
        border-bottom: 1px solid rgba(255, 255, 255, .05);
    }

    .pa-meta b {
        color: #ddd;
        font-weight: 500;
        text-align: right;
    }

    .pa-side .adm-btn {
        margin-bottom: 10px;
    }

    form.is-loading .pa-sec,
    form.is-loading .pa-side {
        opacity: .45;
        pointer-events: none;
    }

    @media (max-width: 1100px) {
        .pa-form-grid {
            grid-template-columns: 1fr;
        }

        .pa-side {
            position: static;
        }
    }

    @media (max-width: 575px) {
        .pa-sec {
            padding: 20px 16px;
        }

        .pa-types {
            grid-template-columns: 1fr;
        }

        .pa-icons {
            grid-template-columns: repeat(6, 1fr);
        }

        .pa-img-up {
            flex-direction: column;
            align-items: flex-start;
        }

        .pa-img-prev {
            width: 100%;
            height: 140px;
        }

        .pa-input-addon>span {
            display: none;
        }

        .pa-input-addon input {
            padding-left: 14px !important;
        }
    }
</style>
@endpush

@section('content')

<form id="paForm" data-edit-id="{{ $editId }}" novalidate enctype="multipart/form-data">
    <div class="pa-form-grid">

        <div>
            {{-- ================= 01. BASIC (2 main fields) ================= --}}
            <div class="glass-card pa-sec">
                <div class="pa-sec-head">
                    <span class="pa-sec-num">01</span>
                    <div>
                        <h4>Practice Area Details</h4><small>Name and type of the practice area</small>
                    </div>
                </div>

                <div class="f-grid">
                    <div class="f-group full" data-field="name">
                        <label>Practice Area Name <b class="req">*</b></label>
                        <input type="text" name="name" required minlength="3" maxlength="120" placeholder="e.g. Criminal Cases, Property Registration" autocomplete="off">
                    </div>

                    <div class="f-group full" data-field="type">
                        <label>Type <b class="req">*</b> <small class="f-hint">(type a new one or pick an existing type)</small></label>
                        <div class="pa-type-input">
                            <i class="fa-solid fa-layer-group"></i>
                            <input type="text" name="type" required minlength="2" maxlength="60" list="paTypeList" placeholder="e.g. Litigation, Corporate, Documentation" autocomplete="off">
                            <datalist id="paTypeList"></datalist>
                        </div>
                        <small class="pa-type-new" id="paTypeNew"><i class="fa-solid fa-circle-plus"></i> New type – it will be created when you save.</small>
                        <div class="pa-type-sugg" id="paTypeSugg">
                            <span class="sk"></span><span class="sk"></span><span class="sk"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= 02. MORE (optional extra DB fields) ================= --}}
            <button type="button" class="pa-more-btn" id="paMoreBtn" aria-expanded="false">
                <i class="fa-solid fa-sliders"></i>
                <span><b>More Details</b> <small class="muted-sm">— icon, image, description, URL, order, status (optional)</small></span>
                <i class="fa-solid fa-chevron-down caret"></i>
            </button>

            <div class="glass-card pa-sec pa-more" id="paMore">
                <div class="pa-sec-head">
                    <span class="pa-sec-num">02</span>
                    <div>
                        <h4>Additional Details</h4><small>Shown on the website practice area card & page</small>
                    </div>
                    <span class="opt">Optional</span>
                </div>

                <div class="f-grid">
                    <div class="f-group full" data-field="slug">
                        <label>URL Slug <small class="f-hint">(auto from name)</small></label>
                        <div class="pa-input-addon">
                            <span>/practice-areas/</span>
                            <input type="text" name="slug" maxlength="140" placeholder="criminal-cases" autocomplete="off">
                        </div>
                    </div>

                    <div class="f-group full" data-field="icon">
                        <label>Icon <small class="f-hint">(Font Awesome class)</small></label>
                        <div class="pa-icon-row">
                            <span class="pa-icon-now"><i id="paIconNow" class="fa-solid fa-scale-balanced"></i></span>
                            <input type="text" name="icon" maxlength="60" placeholder="fa-solid fa-gavel" autocomplete="off">
                        </div>
                        <div class="pa-icons" id="paIcons">
                            @foreach($icons as $ic)
                            <button type="button" data-icon="{{ $ic }}" title="{{ $ic }}"><i class="{{ $ic }}"></i></button>
                            @endforeach
                        </div>
                    </div>

                    <div class="f-group full" data-field="image">
                        <label>Cover Image</label>
                        <div class="pa-img-up">
                            <div class="pa-img-prev" id="paImgPrev"><i class="fa-regular fa-image"></i></div>
                            <div>
                                <div class="pa-img-actions">
                                    <label class="adm-btn adm-btn-ghost"><i class="fa-solid fa-upload"></i> Choose Image
                                        <input type="file" name="image" accept="image/png,image/jpeg,image/webp" hidden>
                                    </label>
                                    <button type="button" class="adm-btn adm-btn-ghost" id="paImgRemove" hidden><i class="fa-regular fa-trash-can"></i> Remove</button>
                                </div>
                                <small class="f-hint">JPG / PNG / WEBP · max 2MB</small>
                            </div>
                        </div>
                        <input type="hidden" name="remove_image" value="0">
                    </div>

                    <div class="f-group full" data-field="short_description">
                        <label>Short Description <span class="pa-count" data-count-for="short_description">0 / 255</span></label>
                        <input type="text" name="short_description" maxlength="255" placeholder="One line shown on the practice area card">
                    </div>

                    <div class="f-group full" data-field="description">
                        <label>Full Description</label>
                        <textarea name="description" rows="5" maxlength="10000" placeholder="Explain this practice area – what you handle, how you help clients…"></textarea>
                    </div>

                    <div class="f-group" data-field="sort_order">
                        <label>Display Order <small class="f-hint">(low = first)</small></label>
                        <input type="number" name="sort_order" min="0" max="9999" placeholder="Auto">
                    </div>

                    <div class="f-group" data-field="status">
                        <label>Status</label>
                        <div class="pa-status-row">
                            <div>
                                <h6 id="paStatusText">Active</h6><small>Visible on website</small>
                            </div>
                            <label class="switch"><input type="checkbox" id="paStatus" checked><span></span></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= SIDE: live preview + save ================= --}}
        <aside class="glass-card pa-side">
            <h5>Live Preview</h5>
            <div class="pa-preview">
                <span class="status green pv-badge" id="pvStatus">Active</span>
                <div class="pv-img" id="pvImg"></div>
                <span class="pv-ico"><i id="pvIcon" class="fa-solid fa-scale-balanced"></i></span>
                <div class="pv-body">
                    <span class="pv-type" id="pvType">Type</span>
                    <h4 id="pvName">Practice Area Name</h4>
                    <p id="pvShort">Short description will appear here.</p>
                </div>
            </div>

            <ul class="pa-meta">
                <li><span>URL</span><b id="pvSlug">/practice-areas/…</b></li>
                <li id="pvCreatedRow" hidden><span>Created</span><b id="pvCreated"></b></li>
                <li id="pvUpdatedRow" hidden><span>Last updated</span><b id="pvUpdated"></b></li>
            </ul>

            <button type="submit" class="adm-btn adm-btn-gold w-100 save-btn">
                <i class="fa-solid fa-check"></i> <span>{{ $editId ? 'Save Changes' : 'Add Practice Area' }}</span>
            </button>
            <a href="{{ route('superadmin.practice-areas') }}" class="adm-btn adm-btn-ghost w-100">Cancel</a>
        </aside>
    </div>
</form>

@endsection

@push('scripts')
<script>
    /* ===== ADD / EDIT PRACTICE AREA – API (Bearer token) ===== */
    document.addEventListener('auth:ready', function(ev) {
        if (!window.API || ev.detail.role !== 'superadmin') return;

        const form = document.getElementById('paForm');
        const id = form.dataset.editId;
        const btn = form.querySelector('.save-btn');
        const toast = (m, err) => window.admToast ? window.admToast(m, err) : alert(m);
        const LIST = @json(route('superadmin.practice-areas'));
        const DEF_IC = 'fa-solid fa-scale-balanced';
        const $ = s => document.getElementById(s);
        const el = n => form.elements.namedItem(n);
        const slugify = s => String(s || '').toLowerCase().trim().replace(/&/g, ' and ').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 140);

        let slugTouched = false,
            currentImg = null,
            TYPES = [];

        /* ---------- Types: DB se (dynamic) ---------- */
        const sugg = $('paTypeSugg');

        function renderTypes() {
            const cur = el('type').value.trim().toLowerCase();
            $('paTypeList').innerHTML = TYPES.map(t => `<option value="${API.esc(t.name)}">`).join('');
            sugg.innerHTML = TYPES.length ?
                '<small>Existing:</small>' + TYPES.map(t => `<button type="button" data-type="${API.esc(t.name)}" class="${t.name.toLowerCase() === cur ? 'on' : ''}">${API.esc(t.name)} <em>${t.count}</em></button>`).join('') :
                '<small><i class="fa-solid fa-circle-info"></i> No types yet – type your first one above.</small>';
            $('paTypeNew').classList.toggle('show', cur.length >= 2 && !TYPES.some(t => t.name.toLowerCase() === cur));
        }
        const typesReady = API.get('/practice-areas/types').then(r => {
            TYPES = r.data || [];
        }).catch(() => {
            TYPES = [];
        }).finally(renderTypes);
        sugg.addEventListener('click', e => {
            const b = e.target.closest('[data-type]');
            if (!b) return;
            el('type').value = b.dataset.type;
            el('type').dispatchEvent(new Event('input', {
                bubbles: true
            }));
        });

        /* ---------- More details toggle ---------- */
        const moreBtn = $('paMoreBtn'),
            more = $('paMore');
        const openMore = (open = true) => {
            more.classList.toggle('open', open);
            moreBtn.classList.toggle('open', open);
            moreBtn.setAttribute('aria-expanded', open);
        };
        moreBtn.addEventListener('click', () => openMore(!more.classList.contains('open')));

        /* ---------- Live preview ---------- */
        const validIcon = v => /^[a-z0-9\- ]+$/i.test(v || '') ? v : '';

        function preview() {
            const name = el('name').value.trim();
            const type = el('type').value.trim();
            const ic = validIcon(el('icon').value.trim()) || DEF_IC;
            const slug = el('slug').value.trim() || slugify(name);
            $('pvName').textContent = name || 'Practice Area Name';
            $('pvType').textContent = type || 'Type';
            if (TYPES.length || type) renderTypes();
            $('pvShort').textContent = el('short_description').value.trim() || 'Short description will appear here.';
            $('pvIcon').className = ic;
            $('paIconNow').className = ic;
            $('pvSlug').textContent = '/practice-areas/' + (slug || '…');
            const on = $('paStatus').checked;
            $('pvStatus').className = 'status pv-badge ' + (on ? 'green' : 'grey');
            $('pvStatus').textContent = $('paStatusText').textContent = on ? 'Active' : 'Inactive';
            document.querySelectorAll('#paIcons button').forEach(b => b.classList.toggle('on', b.dataset.icon === ic));
            const c = form.querySelector('[data-count-for="short_description"]');
            const len = el('short_description').value.length;
            c.textContent = len + ' / 255';
            c.classList.toggle('over', len > 240);
        }

        function setImage(src) {
            currentImg = src || null;
            const box = $('paImgPrev');
            box.innerHTML = src ? `<img src="${API.esc(src)}" alt="">` : '<i class="fa-regular fa-image"></i>';
            $('pvImg').style.backgroundImage = src ? `url("${src.replace(/"/g, '%22')}")` : '';
            $('paImgRemove').hidden = !src;
        }

        form.addEventListener('input', e => {
            const g = e.target.closest('.has-error');
            if (g) {
                g.classList.remove('has-error');
                g.querySelector('.f-error')?.remove();
            }
            if (e.target.name === 'slug') slugTouched = e.target.value.trim() !== '';
            if (e.target.name === 'name' && !slugTouched) el('slug').value = slugify(e.target.value);
            preview();
        });
        form.addEventListener('change', e => {
            const g = e.target.closest('.has-error');
            if (g) {
                g.classList.remove('has-error');
                g.querySelector('.f-error')?.remove();
            }
            preview();
        });
        el('slug').addEventListener('blur', () => {
            if (el('slug').value) el('slug').value = slugify(el('slug').value);
            preview();
        });

        $('paIcons').addEventListener('click', e => {
            const b = e.target.closest('[data-icon]');
            if (!b) return;
            el('icon').value = b.dataset.icon;
            preview();
        });

        el('image').addEventListener('change', () => {
            const f = el('image').files[0];
            if (!f) return;
            if (f.size > 2 * 1024 * 1024) {
                toast('Image must be smaller than 2MB.', true);
                el('image').value = '';
                return;
            }
            el('remove_image').value = '0';
            setImage(URL.createObjectURL(f));
        });
        $('paImgRemove').addEventListener('click', () => {
            el('image').value = '';
            el('remove_image').value = id ? '1' : '0';
            setImage(null);
        });

        /* ---------- Edit: API se data bharo ---------- */
        if (id) {
            openMore(true);
            form.classList.add('is-loading');
            API.get('/practice-areas/' + id).then(r => {
                const p = r.data;
                el('name').value = p.name || '';
                el('type').value = p.type || '';
                typesReady.then(renderTypes);
                el('slug').value = p.slug || '';
                slugTouched = true;
                el('icon').value = p.icon || '';
                el('short_description').value = p.short_description || '';
                el('description').value = p.description || '';
                el('sort_order').value = p.sort_order ?? '';
                $('paStatus').checked = p.status === 'active';
                setImage(p.image_url);
                if (p.created_at) {
                    $('pvCreated').textContent = p.created_at + (p.created_by ? ' · ' + p.created_by : '');
                    $('pvCreatedRow').hidden = false;
                }
                if (p.updated_at) {
                    $('pvUpdated').textContent = p.updated_at + (p.updated_by ? ' · ' + p.updated_by : '');
                    $('pvUpdatedRow').hidden = false;
                }
                preview();
            }).catch(err => {
                toast(err.message, true);
                if (err.status === 404) setTimeout(() => location.replace(LIST), 1200);
            }).finally(() => form.classList.remove('is-loading'));
        }

        /* ---------- Submit ---------- */
        form.addEventListener('submit', async e => {
            e.preventDefault();
            API.clearErrors(form);

            const nameEl = el('name');
            if (!nameEl.checkValidity()) {
                nameEl.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                setTimeout(() => nameEl.reportValidity(), 250);
                return;
            }
            el('type').value = el('type').value.trim().replace(/\s+/g, ' ');
            if (el('type').value.length < 2) {
                API.showErrors(form, {
                    type: ['Please enter the practice area type.']
                });
                el('type').focus();
                return;
            }
            if (el('icon').value && !validIcon(el('icon').value)) {
                openMore(true);
                API.showErrors(form, {
                    icon: ['Icon must be a Font Awesome class, e.g. fa-solid fa-gavel']
                });
                return;
            }

            const fd = API.formData(form);
            fd.set('status', $('paStatus').checked ? 'active' : 'inactive');
            if (!fd.get('sort_order')) fd.delete('sort_order');

            btn.disabled = true;
            btn.classList.add('loading');
            try {
                const r = await API.post(id ? '/practice-areas/' + id : '/practice-areas', fd);
                try {
                    sessionStorage.setItem('pa_flash', r.message);
                } catch (x) {}
                toast(r.message);
                setTimeout(() => {
                    location.href = LIST;
                }, 700);
            } catch (err) {
                if (Object.keys(err.errors || {}).some(k => !['name', 'type'].includes(k))) openMore(true);
                const first = API.showErrors(form, err.errors);
                first?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                toast(err.message, true);
                btn.disabled = false;
                btn.classList.remove('loading');
            }
        });

        preview();
        if (!id) setTimeout(() => el('name').focus(), 200);
    });
</script>
@endpush