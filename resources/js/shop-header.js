document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.getElementById('menuToggle');
    const mainNav = document.getElementById('mainNav');
    const backdrop = document.getElementById('mobileNavBackdrop');
    const closeBtn = document.getElementById('mobileNavClose');
    const mobileMq = window.matchMedia('(max-width: 700px)');

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

    mainNav?.querySelectorAll('a.nav-link').forEach((link) => {
        link.addEventListener('click', () => {
            if (mobileMq.matches) {
                setMobileNavOpen(false);
            }
        });
    });

    const bindMobileSubmenuToggle = (selector, panelSelector) => {
        mainNav?.querySelectorAll(selector).forEach((trigger) => {
            trigger.addEventListener('click', (event) => {
                if (! mobileMq.matches) {
                    return;
                }
                event.preventDefault();
                const item = trigger.closest('.nav-item');
                const panel = item?.querySelector(panelSelector);
                item?.classList.toggle('is-open');
            });
        });
    };

    bindMobileSubmenuToggle('.nav-item--has-children > .nav-link', ':scope > .nav-submenu');
    bindMobileSubmenuToggle('.nav-item--mega > .nav-link', ':scope > .mega-menu-panel');

    const mobileSearchOpen = document.getElementById('mobileSearchOpen');
    const mobileSearchClose = document.getElementById('mobileSearchClose');
    const mobileSearchSheet = document.getElementById('mobileSearchSheet');

    const setMobileSearchOpen = (open) => {
        document.body.classList.toggle('mobile-search-open', open);
        mobileSearchOpen?.setAttribute('aria-expanded', open ? 'true' : 'false');
        mobileSearchSheet?.toggleAttribute('hidden', ! open);
        if (open) {
            mobileSearchSheet?.querySelector('input[type="search"]')?.focus();
        }
    };

    mobileSearchOpen?.addEventListener('click', () => {
        const willOpen = ! document.body.classList.contains('mobile-search-open');
        setMobileSearchOpen(willOpen);
    });

    mobileSearchClose?.addEventListener('click', () => setMobileSearchOpen(false));

    document.querySelectorAll('[data-mobile-bottom-key="search"]').forEach((link) => {
        link.addEventListener('click', (event) => {
            if (! mobileMq.matches) {
                return;
            }
            event.preventDefault();
            setMobileSearchOpen(true);
        });
    });

    document.querySelectorAll('[data-mobile-bottom-key="categories"]').forEach((link) => {
        link.addEventListener('click', (event) => {
            if (! mobileMq.matches) {
                return;
            }
            event.preventDefault();
            setMobileNavOpen(true);
        });
    });

    const filtersOpen = document.querySelector('[data-catalog-filters-open]');
    const catalogSidebar = document.querySelector('.fs-catalog-sidebar');
    const catalogBackdrop = document.getElementById('catalogFiltersBackdrop');
    const filtersClose = document.querySelector('[data-catalog-filters-close]');

    const setCatalogFiltersOpen = (open) => {
        catalogSidebar?.classList.toggle('is-mobile-open', open);
        catalogSidebar?.setAttribute('aria-hidden', open ? 'false' : 'true');
        catalogBackdrop?.toggleAttribute('hidden', ! open);
        catalogBackdrop?.classList.toggle('is-open', open);
        filtersOpen?.setAttribute('aria-expanded', open ? 'true' : 'false');
        document.body.classList.toggle('catalog-filters-open', open);
    };

    filtersOpen?.addEventListener('click', () => {
        const willOpen = ! catalogSidebar?.classList.contains('is-mobile-open');
        setCatalogFiltersOpen(willOpen);
    });

    filtersClose?.addEventListener('click', () => setCatalogFiltersOpen(false));
    catalogBackdrop?.addEventListener('click', () => setCatalogFiltersOpen(false));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && catalogSidebar?.classList.contains('is-mobile-open')) {
            setCatalogFiltersOpen(false);
        }
    });
});