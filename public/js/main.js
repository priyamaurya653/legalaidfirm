document.addEventListener('DOMContentLoaded', () => {

    /* ---------- Preloader ---------- */
    window.addEventListener('load', () => {
        setTimeout(() => document.getElementById('preloader')?.classList.add('hide'), 400);
    });
    setTimeout(() => document.getElementById('preloader')?.classList.add('hide'), 3000); // fallback

    /* ---------- AOS ---------- */
    if (window.AOS) AOS.init({ duration: 900, once: true, offset: 60, easing: 'ease-out-cubic' });

    /* ---------- Sticky header + back to top ---------- */
    const header = document.getElementById('siteHeader');
    const toTop = document.querySelector('.back-to-top');
    const onScroll = () => {
        const y = window.scrollY;
        header?.classList.toggle('scrolled', y > 50);
        toTop?.classList.toggle('show', y > 500);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    toTop?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    /* ---------- Mobile drawer (left side) ---------- */
    const drawer = document.getElementById('mobileDrawer');
    const overlay = document.getElementById('drawerOverlay');
    const openDrawer = () => { drawer.classList.add('open'); overlay.classList.add('open'); document.body.classList.add('no-scroll'); };
    const closeDrawer = () => { drawer.classList.remove('open'); overlay.classList.remove('open'); document.body.classList.remove('no-scroll'); };

    document.getElementById('menuToggle')?.addEventListener('click', openDrawer);
    document.getElementById('drawerClose')?.addEventListener('click', closeDrawer);
    overlay?.addEventListener('click', closeDrawer);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });

    document.querySelectorAll('.drawer-dd-btn').forEach(btn => {
        btn.addEventListener('click', () => btn.parentElement.classList.toggle('open'));
    });
    // practice page pe ho to dropdown pehle se khula rahe
    document.querySelector('.drawer-dd-btn.active')?.parentElement.classList.add('open');

    window.addEventListener('resize', () => { if (window.innerWidth > 1100) closeDrawer(); });

    /* ---------- Cursor glow ---------- */
    const glow = document.querySelector('.cursor-glow');
    if (glow && window.matchMedia('(hover: hover)').matches) {
        window.addEventListener('mousemove', e => {
            glow.style.left = e.clientX + 'px';
            glow.style.top = e.clientY + 'px';
        });
    }

    /* ---------- Hero particles ---------- */
    const pBox = document.getElementById('particles');
    if (pBox) {
        const count = window.innerWidth < 640 ? 15 : 35;
        for (let i = 0; i < count; i++) {
            const p = document.createElement('span');
            p.className = 'particle';
            const size = Math.random() * 3 + 1;
            p.style.width = p.style.height = size + 'px';
            p.style.left = Math.random() * 100 + '%';
            p.style.animationDuration = (Math.random() * 10 + 8) + 's';
            p.style.animationDelay = (Math.random() * 10) + 's';
            pBox.appendChild(p);
        }
    }

    /* ---------- Typing words ---------- */
    const typed = document.querySelector('.typed');
    if (typed) {
        const words = JSON.parse(typed.dataset.words || '[]');
        let w = 0, c = words[0]?.length || 0, deleting = true;
        const tick = () => {
            const word = words[w];
            typed.textContent = word.substring(0, c);
            if (deleting) {
                c--;
                if (c < 0) { deleting = false; w = (w + 1) % words.length; c = 0; }
                setTimeout(tick, 60);
            } else {
                c++;
                if (c > words[w].length) { deleting = true; setTimeout(tick, 1800); return; }
                setTimeout(tick, 110);
            }
        };
        setTimeout(tick, 2200);
    }

    /* ---------- Counters ---------- */
    const counters = document.querySelectorAll('.counter');
    const countIO = new IntersectionObserver(entries => {
        entries.forEach(en => {
            if (!en.isIntersecting) return;
            const el = en.target, target = +el.dataset.target, dur = 2000, start = performance.now();
            const step = now => {
                const p = Math.min((now - start) / dur, 1);
                el.textContent = Math.floor((1 - Math.pow(1 - p, 3)) * target).toLocaleString('en-IN');
                if (p < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
            countIO.unobserve(el);
        });
    }, { threshold: .5 });
    counters.forEach(c => countIO.observe(c));

    /* ---------- 3D tilt on cards (desktop) ---------- */
    if (window.matchMedia('(hover: hover)').matches) {
        document.querySelectorAll('.tilt').forEach(card => {
            card.addEventListener('mousemove', e => {
                const r = card.getBoundingClientRect();
                const x = (e.clientX - r.left) / r.width - .5;
                const y = (e.clientY - r.top) / r.height - .5;
                card.style.transform = `perspective(900px) rotateY(${x * 10}deg) rotateX(${-y * 10}deg) translateY(-6px)`;
            });
            card.addEventListener('mouseleave', () => card.style.transform = '');
        });
    }

    /* ---------- Testimonial slider ---------- */
    const track = document.querySelector('.testi-track');
    if (track) {
        const cards = track.children;
        const dotsBox = document.querySelector('.testi-dots');
        let idx = 0, timer;
        const perView = () => window.innerWidth <= 900 ? 1 : 2;
        const pages = () => cards.length - perView() + 1;

        const buildDots = () => {
            dotsBox.innerHTML = '';
            for (let i = 0; i < pages(); i++) {
                const d = document.createElement('button');
                d.setAttribute('aria-label', 'Slide ' + (i + 1));
                d.addEventListener('click', () => { go(i); restart(); });
                dotsBox.appendChild(d);
            }
        };
        const go = i => {
            idx = (i + pages()) % pages();
            const cardW = cards[0].getBoundingClientRect().width + parseFloat(getComputedStyle(cards[0]).marginRight);
            track.style.transform = `translateX(-${idx * cardW}px)`;
            [...dotsBox.children].forEach((d, n) => d.classList.toggle('active', n === idx));
        };
        const restart = () => { clearInterval(timer); timer = setInterval(() => go(idx + 1), 4500); };

        buildDots(); go(0); restart();
        window.addEventListener('resize', () => { buildDots(); go(0); });
    }

    /* ---------- FAQ ---------- */
    document.querySelectorAll('.faq-q').forEach(q => {
        q.addEventListener('click', () => {
            const item = q.parentElement, ans = item.querySelector('.faq-a');
            const isOpen = item.classList.contains('open');
            document.querySelectorAll('.faq-item').forEach(i => { i.classList.remove('open'); i.querySelector('.faq-a').style.maxHeight = null; });
            if (!isOpen) { item.classList.add('open'); ans.style.maxHeight = ans.scrollHeight + 'px'; }
        });
    });

    /* ---------- Career: apply button selects job + file name ---------- */
    document.querySelectorAll('.apply-btn').forEach(b => {
        b.addEventListener('click', () => {
            const sel = document.getElementById('positionSelect');
            if (sel) sel.value = b.dataset.job;
        });
    });
    /* ---------- Static forms: submit par success message ---------- */
    document.querySelectorAll('form[data-static-form]').forEach(form => {
        form.addEventListener('submit', e => {
            e.preventDefault();
            form.parentElement.querySelector('.alert-static')?.remove();
            const msg = document.createElement('div');
            msg.className = 'alert alert-success alert-static';
            msg.innerHTML = '<i class="fa-solid fa-circle-check"></i> Thank you! Our team will contact you within 24 hours.';
            form.before(msg);
            form.reset();
            const fn = document.getElementById('fileName');
            if (fn) fn.textContent = 'Upload Resume (PDF / DOC, max 4MB)';
            msg.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => msg.remove(), 6000);
        });
    });

    const resume = document.getElementById('resumeInput');
    resume?.addEventListener('change', () => {
        document.getElementById('fileName').textContent = resume.files[0]?.name || 'Upload Resume (PDF / DOC, max 4MB)';
    });

    /* ---------- Disclaimer popup (sirf pehli visit par) ---------- */
    const disc = document.getElementById('disclaimer');
    if (disc) {
        let seen = false;
        try { seen = localStorage.getItem('lexora_disclaimer') === '1'; } catch (e) {}

        const closeDisc = () => {
            disc.classList.remove('show');
            document.body.classList.remove('no-scroll');
            try { localStorage.setItem('lexora_disclaimer', '1'); } catch (e) {}
        };

        if (!seen) {
            setTimeout(() => { disc.classList.add('show'); document.body.classList.add('no-scroll'); }, 900);
        }
        document.getElementById('discClose')?.addEventListener('click', closeDisc);
        document.getElementById('discAgree')?.addEventListener('click', closeDisc);
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && disc.classList.contains('show')) closeDisc(); });
    }

    /* ---------- Hero banner slider + parallax ---------- */
    const hero = document.getElementById('heroBanner');
    if (hero) {
        const slides = hero.querySelectorAll('.hb-slide');
        const dots = hero.querySelectorAll('.hb-dots button');
        const caption = document.getElementById('hbCaption');
        const captions = ['Supreme Court & High Courts', 'Civil & Property Disputes', 'Trial & Appellate Advocacy'];
        let cur = 0, heroTimer;

        const showSlide = n => {
            cur = (n + slides.length) % slides.length;
            slides.forEach((s, i) => s.classList.toggle('active', i === cur));
            dots.forEach((d, i) => {
                d.classList.remove('active');
                if (i === cur) { void d.offsetWidth; d.classList.add('active'); }   // restart fill animation
            });
            if (caption) {
                caption.classList.add('swap');
                setTimeout(() => { caption.textContent = captions[cur]; caption.classList.remove('swap'); }, 400);
            }
        };
        const startHero = () => { clearInterval(heroTimer); heroTimer = setInterval(() => showSlide(cur + 1), 6000); };
        dots.forEach((d, i) => d.addEventListener('click', () => { showSlide(i); startHero(); }));
        startHero();

        // mouse parallax (desktop only)
        const visual = document.getElementById('heroVisual');
        if (visual && window.matchMedia('(hover: hover)').matches) {
            const layers = visual.querySelectorAll('[data-depth]');
            hero.addEventListener('mousemove', e => {
                const r = hero.getBoundingClientRect();
                const x = (e.clientX - r.left) / r.width - .5;
                const y = (e.clientY - r.top) / r.height - .5;
                layers.forEach(l => {
                    const d = +l.dataset.depth;
                    l.style.translate = `${-x * d}px ${-y * d}px`;
                });
            });
            hero.addEventListener('mouseleave', () => layers.forEach(l => l.style.translate = '0 0'));
        }
    }

    /* ---------- Scroll progress bar ---------- */
    const bar = document.querySelector('.scroll-progress');
    if (bar) {
        window.addEventListener('scroll', () => {
            const h = document.documentElement.scrollHeight - window.innerHeight;
            bar.style.width = (h > 0 ? (window.scrollY / h) * 100 : 0) + '%';
        }, { passive: true });
    }

    /* ---------- Core expertise tabs (home) ---------- */
    document.querySelectorAll('.xp-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.xp-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.xp-panel').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(btn.dataset.tab)?.classList.add('active');
            btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        });
    });

    /* ---------- Practice filter (practice-areas page) ---------- */
    document.querySelectorAll('.ex-filter button').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.ex-filter button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const f = btn.dataset.filter;
            document.querySelectorAll('.ex-item').forEach(item => {
                const show = f === 'all' || item.dataset.group === f;
                item.classList.toggle('hide', !show);
                item.classList.remove('pop');
                if (show) { void item.offsetWidth; item.classList.add('pop'); }
            });
        });
    });
});