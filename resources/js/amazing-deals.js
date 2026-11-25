const $ = (selector, root = document) => root.querySelector(selector);
const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];

const cardStep = (track) => {
    const first = track.firstElementChild;
    if (! first) {
        return track.clientWidth;
    }

    const gap = Number.parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap || '0') || 0;

    return first.offsetWidth + gap;
};

const initAmazingDeals = () => {
    $$('.lebasino-page [data-amazing-deals]').forEach((section) => {
        if (section._amazingDealsInit) {
            return;
        }

        section._amazingDealsInit = true;

        const track = $('[data-amazing-deals-track]', section);
        if (! track) {
            return;
        }

        const prev = $('[data-amazing-deals-prev]', section);
        const next = $('[data-amazing-deals-next]', section);
        const rtl = getComputedStyle(track).direction === 'rtl';

        const maxScroll = () => Math.max(0, track.scrollWidth - track.clientWidth);
        const position = () => Math.max(0, Math.min(maxScroll(), (rtl ? -1 : 1) * track.scrollLeft));

        const scrollTo = (left) => track.scrollTo({ left: rtl ? -left : left, behavior: 'smooth' });

        const move = (direction) => {
            const unit = cardStep(track);
            const end = maxScroll();
            if (unit <= 0 || end <= 1) {
                return;
            }

            const current = position();
            const target = direction > 0
                ? Math.min(end, current + unit)
                : Math.max(0, current - unit);

            scrollTo(target);
        };

        prev?.addEventListener('click', () => move(-1));
        next?.addEventListener('click', () => move(1));

        const sync = () => {
            const max = maxScroll();
            const current = position();
            const canScroll = max > 1;

            prev?.toggleAttribute('disabled', current <= 1);
            next?.toggleAttribute('disabled', current >= max - 1);
            section.classList.toggle('amazing-section--scrollable', canScroll);
            section.classList.toggle('amazing-section--fade-left', canScroll && current < max - 8);
        };

        track.addEventListener('scroll', sync, { passive: true });

        let drag = null;
        let moved = false;

        track.addEventListener('pointerdown', (event) => {
            if (event.pointerType !== 'mouse' || event.button !== 0 || track.scrollWidth <= track.clientWidth + 1) {
                return;
            }

            drag = { x: event.clientX, left: track.scrollLeft, id: event.pointerId };
            moved = false;
        });

        window.addEventListener('pointermove', (event) => {
            if (! drag || event.pointerId !== drag.id) {
                return;
            }

            const dx = event.clientX - drag.x;
            if (! moved && Math.abs(dx) < 6) {
                return;
            }

            if (! moved) {
                moved = true;
                track.style.scrollBehavior = 'auto';
                track.style.cursor = 'grabbing';
                track.style.userSelect = 'none';
            }

            track.scrollLeft = drag.left - dx;
        });

        const endDrag = () => {
            if (! drag) {
                return;
            }

            drag = null;
            track.style.scrollBehavior = '';
            track.style.cursor = '';
            track.style.userSelect = '';
            setTimeout(() => {
                moved = false;
            }, 0);
        };

        window.addEventListener('pointerup', endDrag);
        window.addEventListener('pointercancel', endDrag);

        track.addEventListener('click', (event) => {
            if (moved) {
                event.preventDefault();
                event.stopPropagation();
            }
        }, true);

        track.addEventListener('dragstart', (event) => event.preventDefault());

        sync();

        if (window.ResizeObserver) {
            new ResizeObserver(sync).observe(track);
        }

        window.addEventListener('load', sync);
    });
};

document.addEventListener('DOMContentLoaded', initAmazingDeals);