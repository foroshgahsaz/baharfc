document.addEventListener('DOMContentLoaded', () => {
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