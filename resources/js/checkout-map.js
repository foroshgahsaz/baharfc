function loadLeaflet() {
    if (window.L) {
        return Promise.resolve(window.L);
    }

    return new Promise((resolve) => {
        const script = document.createElement('script');
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        script.crossOrigin = '';
        script.onload = () => resolve(window.L);
        document.head.appendChild(script);
    });
}

function wireFromRoot(root) {
    const host = root.closest('[wire\\:id]');

    if (! host) {
        return null;
    }

    return window.Livewire?.find(host.getAttribute('wire:id'));
}

async function bindCheckoutMap(root) {
    const mapEl = root.querySelector('[data-checkout-map]');
    if (! mapEl) {
        return;
    }

    if (mapEl.dataset.mapReady === '1') {
        return;
    }

    const wire = wireFromRoot(root);
    if (! wire) {
        return;
    }

    const L = await loadLeaflet();
    const lat = Number(wire.get('latitude')) || 35.6892;
    const lng = Number(wire.get('longitude')) || 51.389;

    const map = L.map(mapEl).setView([lat, lng], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap',
    }).addTo(map);

    let marker = null;
    if (wire.get('latitude') && wire.get('longitude')) {
        marker = L.marker([lat, lng]).addTo(map);
    }

    map.on('click', (event) => {
        const { lat: clickLat, lng: clickLng } = event.latlng;
        wire.set('latitude', clickLat);
        wire.set('longitude', clickLng);
        if (marker) {
            marker.setLatLng([clickLat, clickLng]);
        } else {
            marker = L.marker([clickLat, clickLng]).addTo(map);
        }
    });

    mapEl.dataset.mapReady = '1';
    setTimeout(() => map.invalidateSize(), 300);
}

document.addEventListener('livewire:init', () => {
    Livewire.on('checkout-map-init', () => {
        window.setTimeout(() => {
            document.querySelectorAll('[data-checkout-map-root]').forEach((root) => bindCheckoutMap(root));
        }, 150);
    });
});