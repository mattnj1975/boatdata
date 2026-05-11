@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
    #fleetMap {
        width: 100%;
        height: calc(100vh - 86px);
    }

    .fleet-map-header {
        padding: 10px 16px;
        background: #0b2538;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .fleet-map-header h4 {
        margin: 0;
        font-size: 1.1rem;
    }

    .fleet-map-status {
        font-size: .9rem;
        color: #b8d4e8;
    }

    .fleet-map-buttons button {
        border: 1px solid rgba(255,255,255,.35);
        background: transparent;
        color: #fff;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: .85rem;
        cursor: pointer;
    }

    .fleet-map-buttons button.active {
        background: #12a7c9;
        border-color: #12a7c9;
    }
</style>
@endsection

@section('content')

<div class="fleet-map-header">
    <h4>Fleet Map</h4>

    <div class="fleet-map-buttons">
        <button data-days="7">7 days</button>
        <button data-days="30">30 days</button>
        <button data-days="90"class="active">90 days</button>
        <button data-days="365">1 year</button>
    </div>

    <div class="fleet-map-status" id="fleetMapStatus">
        Loading boats...
    </div>
</div>

<div id="fleetMap"></div>

@endsection

@section('js')

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('fleetMap').setView([50.8, -1.1], 7);
    const statusEl = document.getElementById('fleetMapStatus');
    const bounds = L.latLngBounds();

    let activeDays = 90;
    let trackLayers = [];
    let markerLayers = [];
    let loadToken = 0;

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        minZoom: 4,
        maxZoom: 20,
        attribution: 'Map data © OpenStreetMap contributors'
    }).addTo(map);

    L.tileLayer('https://tiles.openseamap.org/seamark/{z}/{x}/{y}.png', {
        minZoom: 4,
        maxZoom: 20
    }).addTo(map);

    function colourForIndex(index) {
        const hue = (index * 47) % 360;
        return `hsl(${hue}, 85%, 50%)`;
    }

    function clearFleetMap() {
        trackLayers.forEach(layer => map.removeLayer(layer));
        markerLayers.forEach(layer => map.removeLayer(layer));
        trackLayers = [];
        markerLayers = [];
    }

    function drawTrack(feature, index) {
        const boatName = feature.properties.boatname ?? 'Unnamed boat';
        const mac = feature.properties.mac;
        const color = colourForIndex(index);

        const trackLayer = L.geoJSON(feature, {
            style: {
                color: color,
                weight: activeDays >= 365 ? 2 : 3,
                opacity: activeDays >= 365 ? 0.75 : 0.9
            },
            onEachFeature: function (feature, layer) {
                layer.bindPopup(`
                    <strong>${boatName}</strong><br>
                    MAC: ${mac}<br>
                    Last seen: ${feature.properties.last_seen}<br>
                    <a href="{{ url('/boat-map') }}/${encodeURIComponent(mac)}">
                        View individual boat map
                    </a>
                `);
            }
        }).addTo(map);

        trackLayers.push(trackLayer);

        trackLayer.eachLayer(function (subLayer) {
            if (subLayer.getBounds) {
                bounds.extend(subLayer.getBounds());
            }
        });

        const marker = L.marker([
            feature.properties.last_lat,
            feature.properties.last_lon
        ]).addTo(map);

        marker.bindTooltip(boatName, {
            direction: 'top',
            sticky: true,
            opacity: 0.9
        });

        marker.bindPopup(`
            <strong>${boatName}</strong><br>
            MAC: ${mac}<br>
            Last seen: ${feature.properties.last_seen}<br>
            <a href="{{ url('/boat-map') }}/${encodeURIComponent(mac)}">
                View individual boat map
            </a>
        `);

        markerLayers.push(marker);
    }

    async function loadFleet(days) {
        activeDays = days;
        loadToken++;
        const thisLoad = loadToken;

        clearFleetMap();

        statusEl.innerText = 'Loading boats...';

        const boatsResponse = await fetch("{{ route('fleet.map.boats') }}");
        const boats = await boatsResponse.json();

        let drawn = 0;

        for (let i = 0; i < boats.length; i++) {
            if (thisLoad !== loadToken) {
                return;
            }

            const boat = boats[i];

            statusEl.innerText =
                'Loading ' + (i + 1) + ' of ' + boats.length + ': ' + (boat.boatname ?? boat.mac);

            try {
                const response = await fetch(
                    "{{ url('/fleet-map/data') }}/" +
                    encodeURIComponent(boat.mac) +
                    "/" +
                    days
                );

const feature = await response.json();

console.log('feature', boat.mac, feature);

if (feature && feature.geometry && feature.geometry.coordinates.length > 1) {
                    drawTrack(feature, i);
                    drawn++;

                    if (bounds.isValid()) {
                        map.fitBounds(bounds, {
                            padding: [2, 2],
                            maxZoom: activeDays <= 30 ? 13 : 11
                        });
                    }
                }
            } catch (error) {
                console.error('Failed loading boat', boat.mac, error);
            }

            await new Promise(resolve => setTimeout(resolve, 10));
        }

        statusEl.innerText =
            'Loaded ' + drawn + ' boat tracks, last ' + days + ' days';
    }

    document.querySelectorAll('.fleet-map-buttons button').forEach(button => {
        button.addEventListener('click', function () {
            document.querySelectorAll('.fleet-map-buttons button').forEach(btn => {
                btn.classList.remove('active');
            });

            this.classList.add('active');

            loadFleet(parseInt(this.dataset.days));
        });
    });

    loadFleet(activeDays);
});
</script>

@endsection