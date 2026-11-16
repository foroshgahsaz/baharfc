document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.getElementById('menuToggle');
    const mainNav = document.getElementById('mainNav');
    const backdrop = document.getElementById('mobileNavBackdrop');
    const closeBtn = document.getElementById('mobileNavClose');

    const setMobileNavOpen = (open) => {
        mainNav?.classList.toggle('open', open);
        backdrop?.classList.toggle('is-open', open);
        backdrop?.setAttribute('aria-hidden', open ? 'false' : 'true');
        menuToggle?.setAttribute('aria-expanded', open ? 'true' : 'false');
        document.body.classList.toggle('mobile-nav-open', open);
    };

    menuToggle?.addEventListener('click', () => {
        const willOpen = ! mainNav?.classList.contains('open');
        setMobileNavOpen(willOpen);
    });

    closeBtn?.addEventListener('click', () => setMobileNavOpen(false));

    backdrop?.addEventListener('click', () => setMobileNavOpen(false));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && mainNav?.classList.contains('open')) {
            setMobileNavOpen(false);
        }
    });

    mainNav?.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.matchMedia('(max-width: 700px)').matches) {
                setMobileNavOpen(false);
            }
        });
    });

    const hero = document.getElementById('homeHero');
    if (! hero) {
        return;
    }

    const slides = [...hero.querySelectorAll('[data-slide-index]')];
    const dots = [...hero.querySelectorAll('.dot')];
    const prev = hero.querySelector('.slider-arrow.prev');
    const next = hero.querySelector('.slider-arrow.next');

    if (slides.length <= 1) {
        return;
    }

    let active = 0;

    const setSlide = (index) => {
        active = (index + slides.length) % slides.length;
        slides.forEach((slide, i) => {
            slide.hidden = i !== active;
        });
        dots.forEach((dot, i) => dot.classList.toggle('active', i === active));
    };

    prev?.addEventListener('click', () => setSlide(active - 1));
    next?.addEventListener('click', () => setSlide(active + 1));
    dots.forEach((dot, i) => dot.addEventListener('click', () => setSlide(i)));

    setSlide(0);
});