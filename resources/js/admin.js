/* =========================================================
   LEXORA SUPERADMIN – sidebar, popper, modal, table, toast
   ========================================================= */
document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const isMobileNav = () => window.innerWidth < 992;
    const isSheet = () => window.innerWidth < 576;

    /* ---------------- Auth (Sanctum token) ---------------- */
    const finishAuth = () => {
        document.documentElement.classList.remove('auth-checking');
        document.getElementById('authLoader')?.classList.add('done');
    };

    if (window.API) {
        API.me().then(user => {
            window.CURRENT_USER = user;

            // role "user" ka panel nahi hai -> user dashboard
            if (user.role === 'user') { location.replace(API.homeFor('user')); return; }

            body.classList.add('role-' + user.role);

            const need = body.dataset.requireRole;
            if (need && user.role !== need) { location.replace(API.url('/superadmin')); return; }

            const vals = {
                name: user.name,
                first_name: (user.name || '').replace(/^adv\.?\s+/i, '').split(' ')[0],
                email: user.email,
                role_label: user.role_label,
                panel_label: user.role === 'superadmin' ? 'SUPERADMIN' : 'ADVOCATE',
            };
            document.querySelectorAll('[data-user]').forEach(el => {
                if (vals[el.dataset.user] !== undefined) el.textContent = vals[el.dataset.user];
            });
            document.querySelectorAll('[data-user-avatar]').forEach(el => {
                el.innerHTML = user.photo_url
                    ? `<img src="${API.esc(user.photo_url)}" alt="">`
                    : `<b>${API.esc((user.name || '?').replace(/^adv\.?\s+/i, '').charAt(0).toUpperCase())}</b>`;
            });

            // superadmin: pending approvals badge
            if (user.role === 'superadmin') {
                API.get('/dashboard/stats').then(s => {
                    const p = s.admins.pending;
                    const b = document.getElementById('pendingBadge');
                    if (b) { b.textContent = p; b.hidden = !p; }
                    document.querySelectorAll('[data-stat="pending"]').forEach(el => { if (!el.closest('.mini')) el.textContent = p; });
                }).catch(() => {});
            }

            finishAuth();
            document.dispatchEvent(new CustomEvent('auth:ready', { detail: user }));
        }).catch(err => {
            API.clear();
            location.replace(API.url('/login') + '?next=' + encodeURIComponent(location.pathname)
                + (err && err.status === 403 ? '&reason=inactive' : ''));
        });
    } else {
        finishAuth();
    }

    // Logout (token delete on server)
    document.addEventListener('click', e => {
        const lo = e.target.closest('[data-logout]');
        if (!lo) return;
        e.preventDefault();
        lo.classList.add('loading');
        const done = () => { API.clear(); location.replace(API.url('/login')); };
        API.post('/auth/logout').then(done, done);
    });

    /* ---------------- Sidebar ---------------- */
    // desktop collapsed state yaad rakho
    try { if (localStorage.getItem('adm_sb_mini') === '1' && !isMobileNav()) body.classList.add('sb-mini'); } catch (e) {}

    const openSidebar  = () => { body.classList.add('sb-open'); body.classList.add('lock'); };
    const closeSidebar = () => { body.classList.remove('sb-open'); body.classList.remove('lock'); };

    document.getElementById('sbToggle')?.addEventListener('click', () => {
        if (isMobileNav()) {
            body.classList.contains('sb-open') ? closeSidebar() : openSidebar();
        } else {
            body.classList.toggle('sb-mini');
            try { localStorage.setItem('adm_sb_mini', body.classList.contains('sb-mini') ? '1' : '0'); } catch (e) {}
            setTimeout(() => window.dispatchEvent(new Event('resize')), 420); // charts resize
        }
    });
    document.getElementById('sbClose')?.addEventListener('click', closeSidebar);
    document.getElementById('sbOverlay')?.addEventListener('click', closeSidebar);

    // mobile: swipe left to close
    const sb = document.getElementById('sidebar');
    let sx = null;
    sb?.addEventListener('touchstart', e => { sx = e.touches[0].clientX; }, { passive: true });
    sb?.addEventListener('touchend', e => {
        if (sx !== null && sx - e.changedTouches[0].clientX > 60) closeSidebar();
        sx = null;
    });

    // submenus
    document.querySelectorAll('.sb-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            if (body.classList.contains('sb-mini') && !isMobileNav()) {   // mini me pehle sidebar kholo
                body.classList.remove('sb-mini');
                try { localStorage.setItem('adm_sb_mini', '0'); } catch (e) {}
            }
            const li = btn.closest('.has-sub');
            document.querySelectorAll('.has-sub.open').forEach(o => { if (o !== li) o.classList.remove('open'); });
            li.classList.toggle('open');
        });
    });

    window.addEventListener('resize', () => {
        if (!isMobileNav()) closeSidebar();
    });

    /* ---------------- Topbar shadow on scroll ---------------- */
    const tb = document.getElementById('admTopbar');
    window.addEventListener('scroll', () => tb?.classList.toggle('scrolled', window.scrollY > 10), { passive: true });

    /* ---------------- Popper (dropdowns) ---------------- */
    const backdrop = document.getElementById('popBackdrop');
    let openPop = null, openBtn = null;

    const placePopper = (btn, pop) => {
        // body me move karo taaki overflow / glass card me cut na ho
        if (pop.parentElement !== body) body.appendChild(pop);
        pop.classList.add('portal');
        pop.classList.remove('up');
        pop.style.width = '';

        if (pop.classList.contains('popper-search')) {
            const tbRect = tb.getBoundingClientRect();
            pop.style.top = (tbRect.bottom + 8) + 'px';
            pop.style.left = '12px';
            pop.style.width = (window.innerWidth - 24) + 'px';
            return;
        }
        if (isSheet()) { pop.style.top = ''; pop.style.left = ''; return; } // CSS bottom-sheet

        const r = btn.getBoundingClientRect();
        const w = pop.offsetWidth, h = pop.offsetHeight;
        let left = pop.classList.contains('popper-left') ? r.left : r.right - w;
        left = Math.max(8, Math.min(left, window.innerWidth - w - 8));
        let top = r.bottom + 10;
        if (top + h > window.innerHeight - 8 && r.top - h - 10 > 8) {   // neeche jagah nahi to upar kholo
            top = r.top - h - 10;
            pop.classList.add('up');
        }
        pop.style.left = left + 'px';
        pop.style.top = top + 'px';
        pop.style.setProperty('--ax', (r.left + r.width / 2 - left) + 'px');
    };

    const closePopper = () => {
        if (!openPop) return;
        openPop.classList.remove('show');
        openBtn?.classList.remove('on');
        body.classList.remove('pop-open');
        openPop = openBtn = null;
    };

    const togglePopper = btn => {
        const pop = document.getElementById(btn.dataset.popper);
        if (!pop) return;
        if (openPop === pop) { closePopper(); return; }
        closePopper();
        placePopper(btn, pop);
        requestAnimationFrame(() => pop.classList.add('show'));
        btn.classList.add('on');
        if (isSheet() && !pop.classList.contains('popper-search')) body.classList.add('pop-open');
        openPop = pop; openBtn = btn;
        pop.querySelector('input')?.focus();
    };

    document.addEventListener('click', e => {
        const trigger = e.target.closest('[data-popper]');
        if (trigger) { e.preventDefault(); e.stopPropagation(); togglePopper(trigger); return; }
        if (openPop && !openPop.contains(e.target)) closePopper();
    });
    backdrop?.addEventListener('click', closePopper);
    window.addEventListener('resize', closePopper);
    window.addEventListener('scroll', () => { if (openPop && !isSheet()) closePopper(); }, { passive: true });

    // popper me koi menu link click ho to band karo (range picker text bhi update)
    document.addEventListener('click', e => {
        const a = e.target.closest('.popper .pop-menu a');
        if (!a) return;
        const range = a.closest('[data-range]');
        if (range) {
            e.preventDefault();
            range.querySelectorAll('a').forEach(x => x.classList.remove('active'));
            a.classList.add('active');
            const t = document.getElementById('rangeText');
            if (t) t.textContent = a.textContent.trim();
        }
        if (a.getAttribute('href') === '#') e.preventDefault();
        closePopper();
    });

    /* ---------------- Modal ---------------- */
    const openModal = id => {
        const m = document.getElementById(id);
        if (!m) return;
        closePopper();
        m.classList.add('show');
        m.setAttribute('aria-hidden', 'false');
        body.classList.add('lock');
        setTimeout(() => m.querySelector('input, select, textarea')?.focus(), 300);
    };
    const closeModal = m => {
        m.classList.remove('show');
        m.setAttribute('aria-hidden', 'true');
        if (!body.classList.contains('sb-open')) body.classList.remove('lock');
    };
    document.addEventListener('click', e => {
        const t = e.target.closest('[data-modal]');
        if (t) { e.preventDefault(); openModal(t.dataset.modal); return; }
        if (e.target.closest('[data-close-modal]')) { closeModal(e.target.closest('.adm-modal')); return; }
        if (e.target.classList?.contains('adm-modal')) closeModal(e.target);   // backdrop click
    });

    /* ---------------- Escape key ---------------- */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closePopper();
            document.querySelectorAll('.adm-modal.show').forEach(closeModal);
            closeSidebar();
        }
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {   // Ctrl+K search
            e.preventDefault();
            const s = document.querySelector('.tb-search input');
            if (s && s.offsetParent) s.focus();
            else document.querySelector('.tb-search-mobile')?.click();
        }
    });

    /* ---------------- Toast ---------------- */
    const toast = document.getElementById('admToast');
    let toastT;
    const showToast = (msg = 'Saved successfully', isError = false) => {
        if (!toast) return;
        toast.querySelector('span').textContent = msg;
        toast.classList.toggle('error', !!isError);
        toast.querySelector('i').className = isError ? 'fa-solid fa-circle-exclamation' : 'fa-solid fa-circle-check';
        toast.classList.add('show');
        clearTimeout(toastT);
        toastT = setTimeout(() => toast.classList.remove('show'), 2600);
    };

    /* ---------------- Static forms ---------------- */
    document.querySelectorAll('form[data-static-form]').forEach(f => {
        f.addEventListener('submit', e => {
            e.preventDefault();
            const m = f.closest('.adm-modal');
            if (m) { closeModal(m); f.reset(); }
            showToast('Saved successfully');
        });
    });

    /* ---------------- Table: search, status filter, delete ---------------- */
    document.querySelectorAll('[data-table-search]').forEach(inp => {
        inp.addEventListener('input', () => {
            const q = inp.value.toLowerCase();
            inp.closest('.card').querySelectorAll('[data-table] tbody tr').forEach(tr => {
                tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    });

    document.querySelectorAll('[data-status-filter] button').forEach(btn => {
        btn.addEventListener('click', () => {
            const wrap = btn.parentElement;
            wrap.querySelectorAll('button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const f = btn.dataset.f;
            wrap.closest('.card').querySelectorAll('[data-table] tbody tr').forEach(tr => {
                tr.style.display = (f === 'all' || tr.dataset.status === f) ? '' : 'none';
            });
        });
    });

    document.addEventListener('click', e => {
        const del = e.target.closest('[data-delete-row]');
        if (!del) return;
        e.preventDefault();
        // popper body me hai, isliye button se row dhoondo
        const pop = del.closest('.popper');
        const btn = pop && document.querySelector(`[data-popper="${pop.id}"]`);
        const row = btn?.closest('tr');
        closePopper();
        if (row && confirm('Delete this record?')) {
            row.classList.add('removing');
            setTimeout(() => row.remove(), 400);
            showToast('Record deleted');
        }
    });

    /* ---------------- Misc: segmented buttons, settings tabs, notif read ---------------- */
    document.querySelectorAll('[data-seg]').forEach(seg => {
        seg.querySelectorAll('button').forEach(b => b.addEventListener('click', () => {
            seg.querySelectorAll('button').forEach(x => x.classList.remove('active'));
            b.classList.add('active');
        }));
    });

    document.querySelectorAll('.set-tab').forEach(t => {
        t.addEventListener('click', () => {
            document.querySelectorAll('.set-tab').forEach(x => x.classList.remove('active'));
            document.querySelectorAll('.set-panel').forEach(p => p.classList.remove('active'));
            t.classList.add('active');
            document.getElementById(t.dataset.set)?.classList.add('active');
        });
    });

    document.addEventListener('click', e => {
        const mark = e.target.closest('.pop-link');
        if (!mark || mark.textContent.trim() !== 'Mark all read') return;
        e.preventDefault();
        mark.closest('.popper').querySelectorAll('li.unread').forEach(li => li.classList.remove('unread'));
        const badge = document.querySelector('[data-popper="popNotif"] .dot-badge');
        if (badge) badge.remove();
    });

    document.querySelectorAll('.switch input').forEach(s => s.addEventListener('change', () => showToast('Preference updated')));

    /* page scripts (admins.js) ke liye */
    window.admToast = showToast;
    window.admOpenModal = openModal;
    window.admCloseModal = closeModal;
});