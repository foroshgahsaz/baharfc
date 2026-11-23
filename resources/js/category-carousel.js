const $ = (selector, root = document) => root.querySelector(selector);
const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];

const step = (track) => {
    const first = track.firstElementChild;
    if (! first) {
        return track.clientWidth;
    }

    const gap = Number.parseFloat(getComputedStyle(track).columnGap || '0') || 0;

    return first.offsetWidth + gap;
};

const initCategoryCarousels = () => {
    $$('.lebasino-page [data-slider]').forEach((slider) => {
        if (slider._categoryCarouselInit) {
            return;
        }

        slider._categoryCarouselInit = true;

        const track = $('[data-slider-track]', slider);
        if (! track) {
            return;
        }

        const prev = $$('[data-slider-prev]', slider).filter((button) => button.closest('[data-slider]') === slider);
        const next = $$('[data-slider-next]', slider).filter((button) => button.closest('[data-slider]') === slider);
        const dots = $$('[data-slider-dot]', slider).filter((dot) => dot.closest('[data-slider]') === slider);

        const dotGroups = new Map();
        dots.forEach((dot) => {
            const group = dot.parentElement;
            if (! dotGroups.has(group)) {
                dotGroups.set(group, []);
            }
            dotGroups.get(group).push(dot);
        });

        const rtl = getComputedStyle(track).direction === 'rtl';

        const maxScroll = () => Math.max(0, track.scrollWidth - track.clientWidth);
        const position = () => Math.max(0, Math.min(maxScroll(), (rtl ? -1 : 1) * track.scrollLeft));

        const scrollTo = (left) => track.scrollTo({ left: rtl ? -left : left, behavior: 'smooth' });

        const move = (direction) => {
            const unit = step(track);
            const end = maxScroll();
            if (unit <= 0 || end <= 1) {
                return;
            }

            const current = position();
            const target = direction > 0
                ? (Math.floor((current + 1) / unit) + 1) * unit
                : (Math.ceil((current - 1) / unit) - 1) * unit;

            scrollTo(Math.max(0, Math.min(end, target)));
        };

        prev.forEach((button) => button.addEventListener('click', () => move(-1)));
        next.forEach((button) => button.addEventListener('click', () => move(1)));

        const sync = () => {
            const max = maxScroll();
            const current = position();

            track.toggleAttribute('data-draggable', max > 1);

            const ratio = max > 0 ? current / max : 0;

            dotGroups.forEach((group, container) => {
                if (container?.hasAttribute('data-slider-progress')) {
                    container.hidden = max <= 1;
                }

                const active = Math.round(ratio * (group.length - 1));

                group.forEach((dot, index) => {
                    const isActive = index === active;
                    dot.toggleAttribute('data-active', isActive);
                    if (isActive) {
                        dot.setAttribute('aria-current', 'true');
                    } else {
                        dot.removeAttribute('aria-current');
                    }
                });
            });

            prev.forEach((button) => button.toggleAttribute('disabled', current <= 1));
            next.forEach((button) => button.toggleAttribute('disabled', current >= max - 1));
        };

        track.addEventListener('scroll', sync, { passive: true });

        dotGroups.forEach((group) => group.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                const end = maxScroll();
                const target = (index / Math.max(group.length - 1, 1)) * end;
                scrollTo(target);
            });
        }));

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
                track.style.scrollSnapType = 'none';
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
            track.style.scrollSnapType = '';
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

document.addEventListener('DOMContentLoaded', initCategoryCarousels);