const $ = (selector, root = document) => root.querySelector(selector);
const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];

const COMPARE_KEY = 'baharfc_compare_ids';
const COMPARE_MAX = 4;

const optionAliases = {
    color: ['color', 'رنگ'],
    storage: ['storage', 'حافظه', 'حافظه داخلی'],
    size: ['size', 'سایز'],
    volume: ['volume', 'حجم'],
};

const readCompare = () => {
    try {
        const raw = localStorage.getItem(COMPARE_KEY);
        const parsed = raw ? JSON.parse(raw) : [];

        return Array.isArray(parsed) ? parsed.map(String) : [];
    } catch {
        return [];
    }
};

const writeCompare = (ids) => {
    localStorage.setItem(COMPARE_KEY, JSON.stringify(ids.slice(0, COMPARE_MAX)));
};

const parseVariants = (root) => {
    const raw = root.getAttribute('data-product-variants');
    if (! raw) {
        return [];
    }

    try {
        const parsed = JSON.parse(raw);

        return Array.isArray(parsed) ? parsed : [];
    } catch {
        return [];
    }
};

const optionMatches = (options, key, value) => {
    const keys = optionAliases[key] || [key];
    const target = String(value);

    return keys.some((candidate) => String(options?.[candidate] ?? '') === target);
};

const matchVariantId = (variants, selected) => {
    if (variants.length === 0) {
        return null;
    }

    const entries = Object.entries(selected).filter(([, value]) => value !== undefined && value !== null && value !== '');

    if (entries.length === 0) {
        return variants[0].id ?? null;
    }

    const match = variants.find((variant) =>
        entries.every(([key, value]) => optionMatches(variant.options, key, value)),
    );

    return match?.id ?? variants[0].id ?? null;
};

const dispatchVariant = (variantId, root) => {
    if (root && variantId != null) {
        root.dataset.selectedVariantId = String(variantId);
    }

    if (typeof Livewire === 'undefined') {
        return;
    }

    Livewire.dispatch('product-variant-selected', { variantId });
};

const syncSelectedVariantFromPage = (root) => {
    const id = root?.dataset.selectedVariantId;
    if (! id || typeof Livewire === 'undefined') {
        return;
    }

    Livewire.dispatch('product-variant-selected', { variantId: Number(id) });
};

const syncProductStickyTop = () => {
    const header = document.querySelector('.lebasino-page .site-header');
    const top = header ? Math.ceil(header.getBoundingClientRect().height) : 0;
    document.documentElement.style.setProperty('--fs-product-sticky-top', `${top}px`);
};

const galleryUrls = (root) => {
    const raw = root.querySelector('[data-product-gallery]')?.getAttribute('data-gallery-urls');
    if (raw) {
        try {
            const parsed = JSON.parse(raw);

            return Array.isArray(parsed) ? parsed.filter(Boolean) : [];
        } catch {
            return [];
        }
    }

    return $$('[data-product-thumb]', root)
        .map((thumb) => thumb.getAttribute('data-product-thumb'))
        .filter(Boolean);
};

const initGallery = (root) => {
    const galleryRoot = $('[data-product-gallery]', root);
    const mainImg = $('[data-product-main-img]', root);
    const zoomBtn = $('[data-product-zoom]', root);
    const urls = galleryUrls(root);
    let index = Math.max(
        0,
        urls.findIndex((url) => url === mainImg?.getAttribute('src') || url === mainImg?.src),
    );

    const showIndex = (nextIndex) => {
        if (urls.length === 0 || ! mainImg) {
            return;
        }

        index = (nextIndex + urls.length) % urls.length;
        mainImg.src = urls[index];

        $$('[data-product-thumb]', root).forEach((thumb, thumbIndex) => {
            const match = urls[thumbIndex] === urls[index];
            thumb.toggleAttribute('data-active', match);
            thumb.setAttribute('aria-current', match ? 'true' : 'false');
        });
    };

    const step = (delta) => {
        if (urls.length < 2) {
            return;
        }

        showIndex(index + delta);
    };

    $$('[data-product-thumb]', root).forEach((thumb, thumbIndex) => {
        thumb.addEventListener('click', () => showIndex(thumbIndex));
    });

    $('[data-gallery-prev]', root)?.addEventListener('click', (event) => {
        event.preventDefault();
        step(-1);
    });
    $('[data-gallery-next]', root)?.addEventListener('click', (event) => {
        event.preventDefault();
        step(1);
    });

    const openZoom = () => {
        if (! mainImg?.src || urls.length === 0) {
            return;
        }

        let zoomIndex = index;

        const overlay = document.createElement('div');
        overlay.className = 'fs-product-zoom-overlay';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-label', 'بزرگنمایی تصویر');

        const render = () => {
            const hasMany = urls.length > 1;
            overlay.innerHTML = `
                <button type="button" class="fs-product-zoom-close" aria-label="بستن">×</button>
                ${hasMany ? '<button type="button" class="fs-product-zoom-nav fs-product-zoom-nav--prev" aria-label="تصویر قبلی">‹</button>' : ''}
                <img src="${urls[zoomIndex]}" alt="${mainImg.alt || ''}" />
                ${hasMany ? '<button type="button" class="fs-product-zoom-nav fs-product-zoom-nav--next" aria-label="تصویر بعدی">›</button>' : ''}
            `;

            overlay.querySelector('.fs-product-zoom-close')?.addEventListener('click', close);
            overlay.querySelector('.fs-product-zoom-nav--prev')?.addEventListener('click', (event) => {
                event.stopPropagation();
                zoomIndex = (zoomIndex - 1 + urls.length) % urls.length;
                render();
            });
            overlay.querySelector('.fs-product-zoom-nav--next')?.addEventListener('click', (event) => {
                event.stopPropagation();
                zoomIndex = (zoomIndex + 1) % urls.length;
                render();
            });
        };

        const close = () => {
            showIndex(zoomIndex);
            overlay.remove();
        };

        const onKey = (event) => {
            if (event.key === 'Escape') {
                close();
                document.removeEventListener('keydown', onKey);
            }
            if (urls.length > 1 && event.key === 'ArrowRight') {
                zoomIndex = (zoomIndex + 1) % urls.length;
                render();
            }
            if (urls.length > 1 && event.key === 'ArrowLeft') {
                zoomIndex = (zoomIndex - 1 + urls.length) % urls.length;
                render();
            }
        };

        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) {
                close();
            }
        });

        document.addEventListener('keydown', onKey);
        render();
        document.body.appendChild(overlay);
    };

    galleryRoot?.addEventListener('keydown', (event) => {
        if (urls.length < 2) {
            return;
        }

        if (event.key === 'ArrowRight') {
            event.preventDefault();
            step(1);
        }
        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            step(-1);
        }
    });

    zoomBtn?.addEventListener('click', openZoom);
    mainImg?.addEventListener('click', openZoom);
};

const initVariantOptions = (root) => {
    if (! root.hasAttribute('data-variant-options')) {
        return;
    }

    const variants = parseVariants(root);
    const selected = {};

    const apply = () => {
        dispatchVariant(matchVariantId(variants, selected), root);
    };

    const register = (element) => {
        element.addEventListener('click', () => {
            const key = element.getAttribute('data-option-key');
            const value = element.getAttribute('data-option-value');
            if (! key || value == null) {
                return;
            }

            selected[key] = value;

            if (element.hasAttribute('data-swatch')) {
                $$('[data-swatch]', root).forEach((item) => item.toggleAttribute('data-active', item === element));
                const label = $('[data-color-label]', root);
                if (label) {
                    label.textContent = element.getAttribute('data-name') || '';
                }
            } else {
                const container = element.closest('[data-options]');
                if (container) {
                    $$('.fs-product-option', container).forEach((item) => item.toggleAttribute('data-active', item === element));
                }
            }

            apply();
        });
    };

    $$('[data-swatch][data-option-key]', root).forEach(register);
    $$('.fs-product-option[data-option-key]', root).forEach(register);

    $$('[data-swatch][data-active]', root).forEach((element) => {
        selected[element.getAttribute('data-option-key')] = element.getAttribute('data-option-value');
    });
    $$('.fs-product-option[data-active]', root).forEach((element) => {
        selected[element.getAttribute('data-option-key')] = element.getAttribute('data-option-value');
    });

    apply();
};

const initCompare = (root) => {
    const button = $('[data-product-compare]', root);
    if (! button) {
        return;
    }

    const id = String(button.getAttribute('data-product-compare') || '');
    const sync = () => {
        const active = readCompare().includes(id);
        button.toggleAttribute('data-active', active);
        button.setAttribute('aria-pressed', active ? 'true' : 'false');
    };

    sync();

    button.addEventListener('click', () => {
        let ids = readCompare();
        if (ids.includes(id)) {
            ids = ids.filter((item) => item !== id);
        } else {
            ids = [...ids, id];
            if (ids.length > COMPARE_MAX) {
                ids = ids.slice(-COMPARE_MAX);
            }
        }
        writeCompare(ids);
        sync();
    });
};

const initStickyTabNav = (root) => {
    const shell = $('[data-product-tabnav-shell]', root);
    const nav = $('[data-product-tabnav]', root);
    const sentinel = $('[data-product-tabnav-sentinel]', root);
    const placeholder = $('[data-product-tabnav-placeholder]', root);
    if (! shell || ! nav || ! sentinel || ! placeholder) {
        return;
    }

    const syncHeight = () => {
        placeholder.style.height = `${nav.offsetHeight}px`;
    };

    syncHeight();
    window.addEventListener('resize', syncHeight, { passive: true });

    const observePin = () => {
        syncProductStickyTop();
        const top = getComputedStyle(document.documentElement).getPropertyValue('--fs-product-sticky-top').trim() || '0px';

        return new IntersectionObserver(
            ([entry]) => {
                const pinned = ! entry.isIntersecting;
                shell.classList.toggle('is-pinned', pinned);
                nav.classList.toggle('is-pinned', pinned);
                if (pinned) {
                    syncHeight();
                }
            },
            {
                threshold: [0, 1],
                rootMargin: `-${top} 0px 0px 0px`,
            },
        );
    };

    let pinObserver = observePin();
    pinObserver.observe(sentinel);

    window.addEventListener('resize', () => {
        pinObserver.disconnect();
        pinObserver = observePin();
        pinObserver.observe(sentinel);
    }, { passive: true });
};

const initTabNav = (root) => {
    const nav = $('[data-product-tabnav]', root);
    if (! nav) {
        return;
    }

    initStickyTabNav(root);

    const tabs = $$('.fs-product-tab', nav);
    const sections = tabs
        .map((tab) => {
            const href = tab.getAttribute('href');
            if (! href?.startsWith('#')) {
                return null;
            }

            return $(href, document);
        })
        .filter(Boolean);

    const setActive = (id) => {
        tabs.forEach((tab) => {
            const match = tab.getAttribute('href') === `#${id}`;
            tab.toggleAttribute('data-active', match);
        });
    };

    tabs.forEach((tab) => {
        tab.addEventListener('click', (event) => {
            event.preventDefault();
            const href = tab.getAttribute('href');
            const target = href ? $(href, document) : null;
            const stickyTop = parseInt(
                getComputedStyle(document.documentElement).getPropertyValue('--fs-product-sticky-top'),
                10,
            ) || 0;
            const offset = nav.offsetHeight + stickyTop + 12;
            if (! target) {
                return;
            }

            const top = target.getBoundingClientRect().top + window.scrollY - offset;
            window.scrollTo({ top, behavior: 'smooth' });
        });
    });

    if (sections.length === 0) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            const visible = entries
                .filter((entry) => entry.isIntersecting)
                .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];

            if (visible?.target?.id) {
                setActive(visible.target.id);
            }
        },
        { rootMargin: '-120px 0px -55% 0px', threshold: [0, 0.12, 0.35] },
    );

    sections.forEach((section) => observer.observe(section));
};

const initMobileBar = (root) => {
    const buyAnchor = $('.fs-product-buybox-actions button[type="button"]', root);

    $('[href="#product-intro"].fs-product-mobile-buy', root)?.addEventListener('click', (event) => {
        event.preventDefault();
        $('.fs-product-buyaside', root)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        window.setTimeout(() => buyAnchor?.focus(), 400);
    });
};

const initProductPage = () => {
    const page = $('.lebasino-page .fs-product-page');
    if (! page || page._productPageInit) {
        return;
    }

    page._productPageInit = true;
    syncProductStickyTop();
    initGallery(page);
    initVariantOptions(page);
    syncSelectedVariantFromPage(page);
    initCompare(page);
    initTabNav(page);
    initMobileBar(page);
};

const bootProductPage = () => {
    const page = $('.lebasino-page .fs-product-page');
    if (page) {
        page._productPageInit = false;
    }

    initProductPage();
};

document.addEventListener('DOMContentLoaded', bootProductPage);
document.addEventListener('livewire:navigated', bootProductPage);
document.addEventListener('livewire:init', () => {
    const page = $('.lebasino-page .fs-product-page');
    if (page) {
        syncSelectedVariantFromPage(page);
    }
});
window.addEventListener('resize', syncProductStickyTop, { passive: true });