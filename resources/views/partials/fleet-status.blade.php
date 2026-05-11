@php
    $stats = $fleetStatus['stats'] ?? null;
    $boats = $fleetStatus['boats'] ?? collect();
@endphp

<div class="container-fluid px-4 mb-5">
    <div class="card marine-card">

        <div class="marine-card-header">
            <span>Fleet Status</span>
            <small>{{ $stats->boats ?? 0 }} public boats seen in the last 365 days</small>
        </div>

        <div class="marine-card-body">
            <div
    id="fleetMap"
    data-loaded="0"
    style="height: 620px; width: 100%; border-radius: 10px;"
></div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mapEl = document.getElementById('fleetMap');

    if (!mapEl) {
        return;
    }

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting || mapEl.dataset.loaded === '1') {
                return;
            }

            mapEl.dataset.loaded = '1';
            loadFleetMap();
            observer.disconnect();
        });
    }, {
        rootMargin: '150px'
    });

    observer.observe(mapEl);

    function loadFleetMap() {
        const boats = @json($boats);

        const map = L.map('fleetMap');

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const bounds = [];

        boats.forEach(function (boat) {
            const lat = parseFloat(boat.lat);
            const lon = parseFloat(boat.lon);

            if (!lat || !lon) {
                return;
            }

            const label = `
                <strong>${boat.boatname ?? 'Unnamed boat'}</strong><br>
                MAC: ${boat.mac}<br>
                Last seen: ${boat.last_seen}
            `;

            const marker = L.marker([lat, lon]).addTo(map);

            marker.bindTooltip(label, {
                direction: 'top',
                sticky: true,
                opacity: 0.95
            });

            marker.bindPopup(label);

            marker.on('click', function () {
                window.location.href = `/view/?mac=${encodeURIComponent(boat.mac)}`;
            });

            bounds.push([lat, lon]);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, {
                padding: [40, 40],
                maxZoom: 13
            });
        } else {
            map.setView([50.8, -1.1], 8);
        }

        setTimeout(function () {
            map.invalidateSize();
        }, 250);
    }
});
</script>