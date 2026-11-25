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

const dispatchVariant = (variantId) => {
    if (typeof Livewire === 'undefined') {
        return;
    }

    Livewire.dispatch('product-variant-selected', { variantId });
};

const initGallery = (root) => {
    const mainImg = $('[data-product-main-img]', root);
    const zoomBtn = $('[data-product-zoom]', root);

    $$('[data-product-thumb]', root).forEach((thumb) => {
        thumb.addEventListener('click', () => {
            const url = thumb.getAttribute('data-product-thumb');
            if (! url || ! mainImg) {
                return;
            }

            mainImg.src = url;
            $$('[data-product-thumb]', root).forEach((t) => {
                t.toggleAttribute('data-active', t === thumb);
                t.setAttribute('aria-current', t === thumb ? 'true' : 'false');
            });
        });
    });

    const openZoom = () => {
        if (! mainImg?.src) {
            return;
        }

        const overlay = document.createElement('div');
        overlay.className = 'fs-product-zoom-overlay';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-label', 'بزرگنمایی تصویر');
        overlay.innerHTML = `
            <button type="button" class="fs-product-zoom-close" aria-label="بستن">×</button>
            <img src="${mainImg.src}" alt="${mainImg.alt || ''}" />
        `;

        const close = () => overlay.remove();

        overlay.addEventListener('click', (event) => {
            if (event.target === overlay || event.target.closest('.fs-product-zoom-close')) {
                close();
            }
        });

        document.addEventListener(
            'keydown',
            (event) => {
                if (event.key === 'Escape') {
                    close();
                }
            },
            { once: true },
        );

        document.body.appendChild(overlay);
    };

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
        dispatchVariant(matchVariantId(variants, selected));
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

const initTabNav = (root) => {
    const nav = $('[data-product-tabnav]', root);
    if (! nav) {
        return;
    }

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
            const offset = nav.offsetHeight + 72;
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
    initGallery(page);
    initVariantOptions(page);
    initCompare(page);
    initTabNav(page);
    initMobileBar(page);
};

document.addEventListener('DOMContentLoaded', initProductPage);
document.addEventListener('livewire:navigated', initProductPage);