/**
 * main.js — PT. Hastra Karya Persada
 * Premium interactive behaviours
 */

document.addEventListener('DOMContentLoaded', () => {

    /* ================================================================
       1. PAGE LOADER
    ================================================================ */
    const loader = document.getElementById('loader');
    if (loader) {
        window.addEventListener('load', () => {
            setTimeout(() => loader.classList.add('loaded'), 600);
        });
    }

    /* ================================================================
       2. NAVBAR SCROLL SHRINK
    ================================================================ */
    const navbarWrap = document.getElementById('navbarWrap');
    if (navbarWrap) {
        window.addEventListener('scroll', () => {
            navbarWrap.classList.toggle('scrolled', window.scrollY > 60);
        }, { passive: true });
    }

    /* ================================================================
       3. BACK TO TOP
    ================================================================ */
    const backToTop = document.getElementById('backToTop');
    if (backToTop) {
        window.addEventListener('scroll', () => {
            backToTop.classList.toggle('show', window.scrollY > 400);
        }, { passive: true });

        backToTop.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ================================================================
       4. SCROLL REVEAL (IntersectionObserver)
    ================================================================ */
    const revealEls = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
    if (revealEls.length) {
        const revealObs = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

        revealEls.forEach(el => revealObs.observe(el));
    }

    /* ================================================================
       5. COUNTER ANIMATION
    ================================================================ */
    const counterEls = document.querySelectorAll('.counter-value');
    if (counterEls.length) {
        const counterObs = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const el     = entry.target;
                const target = parseInt(el.dataset.target, 10);
                const dur    = 1800;
                const start  = performance.now();

                const tick = (now) => {
                    const elapsed  = now - start;
                    const progress = Math.min(elapsed / dur, 1);
                    // Ease out expo
                    const eased = 1 - Math.pow(2, -10 * progress);
                    el.textContent = Math.floor(eased * target);
                    if (progress < 1) requestAnimationFrame(tick);
                    else el.textContent = target;
                };
                requestAnimationFrame(tick);
                counterObs.unobserve(el);
            });
        }, { threshold: 0.5 });

        counterEls.forEach(el => counterObs.observe(el));
    }

    /* ================================================================
       6. LIGHTBOX
    ================================================================ */
    const lightbox     = document.getElementById('lightbox');
    const lightboxImg  = document.getElementById('lightbox-img');
    const lightboxClose = document.getElementById('lightbox-close');

    if (lightbox) {
        document.querySelectorAll('[data-lightbox]').forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                lightboxImg.src = trigger.dataset.lightbox || trigger.src || trigger.href;
                lightbox.classList.add('show');
                document.body.style.overflow = 'hidden';
            });
        });

        const closeLB = () => {
            lightbox.classList.remove('show');
            document.body.style.overflow = '';
        };
        lightboxClose?.addEventListener('click', closeLB);
        lightbox.addEventListener('click', (e) => { if (e.target === lightbox) closeLB(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeLB(); });
    }

    /* ================================================================
       7. GALLERY FILTER
    ================================================================ */
    const filterBtns = document.querySelectorAll('.filter-btn');
    const filterItems = document.querySelectorAll('[data-category]');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const cat = btn.dataset.filter;
            filterItems.forEach(item => {
                const match = cat === 'all' || item.dataset.category === cat;
                item.style.transition = 'opacity .4s, transform .4s';
                if (match) {
                    item.style.opacity  = '1';
                    item.style.transform = 'scale(1)';
                    item.style.display = '';
                } else {
                    item.style.opacity  = '0';
                    item.style.transform = 'scale(.96)';
                    setTimeout(() => { item.style.display = 'none'; }, 350);
                }
            });
        });
    });

    /* ================================================================
       8. CONTACT FORM (AJAX)
    ================================================================ */
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = contactForm.querySelector('[type="submit"]');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="bi-arrow-repeat spin me-2"></i>Mengirim…';
            btn.disabled = true;

            try {
                const res  = await fetch('handlers/contact_handler.php', {
                    method: 'POST',
                    body: new FormData(contactForm),
                });
                const data = await res.json();
                showNotif(data.success ? 'success' : 'error', data.message || 'Terjadi kesalahan.');
                if (data.success) contactForm.reset();
            } catch {
                showNotif('error', 'Gagal mengirim pesan. Periksa koneksi Anda.');
            } finally {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        });
    }

    /* ================================================================
       9. TOAST NOTIFICATION
    ================================================================ */
    function showNotif(type, msg) {
        const el = document.createElement('div');
        el.className = `hastra-toast hastra-toast-${type}`;
        el.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill'}"></i> ${msg}`;
        Object.assign(el.style, {
            position: 'fixed', bottom: '2rem', left: '50%',
            transform: 'translateX(-50%) translateY(20px)',
            background: type === 'success' ? 'var(--navy)' : '#b91c1c',
            color: '#fff',
            padding: '.85rem 1.75rem',
            borderRadius: '100px',
            boxShadow: '0 8px 30px rgba(0,0,0,.25)',
            fontSize: '.9rem',
            fontFamily: 'var(--font-body)',
            border: `1px solid ${type === 'success' ? 'rgba(201,162,39,.4)' : 'rgba(239,68,68,.4)'}`,
            zIndex: 99999,
            opacity: '0',
            transition: 'all .4s cubic-bezier(.16,1,.3,1)',
            display: 'flex', gap: '.6rem', alignItems: 'center',
        });
        document.body.appendChild(el);
        requestAnimationFrame(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateX(-50%) translateY(0)';
        });
        setTimeout(() => {
            el.style.opacity = '0';
            el.style.transform = 'translateX(-50%) translateY(10px)';
            setTimeout(() => el.remove(), 400);
        }, 5000);
    }

    /* ================================================================
       10. SMOOTH ANCHOR LINKS
    ================================================================ */
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const target = document.querySelector(a.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

}); // DOMContentLoaded
