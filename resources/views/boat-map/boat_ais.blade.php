@extends('layouts.app')

@section('css')

<link rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
    #boatMap {
        width: 100%;
        height: calc(100vh - 155px);
    }

    .boat-map-header {
        padding: 10px 16px;
        background: #0b2538;
        color: #fff;
    }

    .boat-map-header h4 {
        margin: 0;
        font-size: 1.1rem;
    }

    .boat-map-controls {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }
	
	.boat-map-controls {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    flex-wrap: wrap;
}

    .boat-map-controls .btn,
    .boat-map-controls .form-control,
    .boat-map-controls .form-select {
        border-radius: 999px;
    }

    .boat-map-controls label {
        font-size: 0.85rem;
        opacity: 0.8;
        margin-right: 4px;
    }
</style>

@endsection

@section('content')

<div class="boat-map-header">

    @include('partials.boat_nav', ['mac' => $mac ?? null])

    <div style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:15px;
        flex-wrap:wrap;
        margin-top:10px;
    ">

        <div>
            <h4 style="margin-bottom:2px;">
                {{ $boatName ?? ($deviceSettings->boatname ?? 'AIS Encounters Map') }}
            </h4>

            <div style="font-size:0.85rem; opacity:0.7;">
                {{ $mac }}
            </div>
        </div>

<div class="boat-map-controls">

    <div class="d-flex flex-column">
        <label for="aisDate" class="mb-1">Date</label>

        <input type="date"
               id="aisDate"
               class="form-control form-control-sm"
               style="width:150px;"
               value="{{ $defaultDate ?? now()->toDateString() }}">
    </div>

    <div class="d-flex flex-column">
        <label for="aisRange" class="mb-1">Range</label>

        <select id="aisRange"
                class="form-select form-select-sm"
                style="width:100px;">
            <option value="0">All</option>
            <option value="0.5">0.5 NM</option>
            <option value="1" selected>1 NM</option>
            <option value="2">2 NM</option>
            <option value="5">5 NM</option>
        </select>
    </div>

    <div class="d-flex flex-column justify-content-end">
        <label class="mb-1" style="opacity:0;">Load</label>

        <button id="loadAisMap"
                class="btn btn-sm btn-info px-3">
            Load AIS
        </button>
    </div>

</div>
    </div>
</div>

<div id="boatMap"></div>

@endsection

@section('js')

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const mac = @json($mac);
	const baseUrl = @json(request()->getBaseUrl());

    const map = L.map('boatMap');

    const osmLayer = L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            minZoom: 4,
            maxZoom: 20,
            attribution: 'Map data © OpenStreetMap contributors'
        }
    );

    const seaLayer = L.tileLayer(
        'https://tiles.openseamap.org/seamark/{z}/{x}/{y}.png',
        {
            minZoom: 4,
            maxZoom: 20
        }
    );

    osmLayer.addTo(map);
    seaLayer.addTo(map);

    let boatLayer = null;
    let aisLayerGroup = L.layerGroup().addTo(map);
    let closestLayerGroup = L.layerGroup().addTo(map);

    map.setView([50.8, -1.1], 7);

    document.getElementById('loadAisMap').addEventListener('click', loadAisMap);

    loadAisMap();

async function loadAisMap() {
    const date = document.getElementById('aisDate').value;
    const rangeNm = document.getElementById('aisRange').value;

    let url = `${baseUrl}/api/boat-ais/${mac}/${date}`;

    if (rangeNm && rangeNm !== '0') {
        url += `?range_nm=${rangeNm}`;
    }

    console.log('Loading AIS URL:', url);

    const response = await fetch(url);

    if (!response.ok) {
        const errorText = await response.text();
        console.error('AIS API failed:', response.status, errorText);
        alert('Could not load AIS map data');
        return;
    }

    const data = await response.json();

    window.history.replaceState(
        {},
        '',
        `${baseUrl}/boat-ais/${mac}/${date}`
    );

    drawBoatTrack(data.boatTrack || []);
    drawAisTargets(data.aisTargets || []);
}

    function drawBoatTrack(track) {
        if (boatLayer) {
            map.removeLayer(boatLayer);
            boatLayer = null;
        }

        const points = track
            .filter(p => validLatLon(p.lat, p.lon))
            .map(p => [p.lat, p.lon]);

if (!points.length) {
    aisLayerGroup.clearLayers();
    closestLayerGroup.clearLayers();
    map.setView([50.8, -1.1], 7);
    return;
}

        boatLayer = L.polyline(points, {
            color: '#000000',
            weight: 4,
            opacity: 0.95
        }).addTo(map);

        map.fitBounds(boatLayer.getBounds(), {
            padding: [10, 10],
            maxZoom: 15
        });
    }
function drawAisTargets(targets) {
    aisLayerGroup.clearLayers();
    closestLayerGroup.clearLayers();

    targets.forEach(target => {
        const points = (target.track || [])
            .filter(p => validLatLon(p.lat, p.lon))
            .map(p => [p.lat, p.lon]);

        if (points.length < 2) {
            return;
        }

        const colour = getRangeColour(target.min_range_nm);

const popup = `
    <strong>AIS Target</strong><br>
    MMSI: ${target.mmsi}<br>
    Closest: ${target.min_range_nm ?? '?'} NM<br>
    Closest time: ${target.closest?.time ?? '?'}<br>
    AIS SOG/COG: ${target.closest?.ais_sog ?? '?'} kn / ${target.closest?.ais_cog ?? '?'}°<br>
    Points: ${target.point_count ?? points.length}
`;

        const line = L.polyline(points, {
            color: colour,
            weight: 2,
            opacity: 0.55
        }).bindPopup(popup);

        line.on('mouseover', function () {
            this.setStyle({
                weight: 5,
                opacity: 0.95
            });
            this.bringToFront();
        });

        line.on('mouseout', function () {
            this.setStyle({
                weight: 2,
                opacity: 0.55
            });
        });

        aisLayerGroup.addLayer(line);

        if (target.closest && validLatLon(target.closest.lat, target.closest.lon)) {
            const closestMarker = L.circleMarker(
                [target.closest.lat, target.closest.lon],
                {
                    radius: 6,
                    color: colour,
                    weight: 2,
                    fillOpacity: 0.9
                }
            ).bindPopup(`
                <strong>Closest approach</strong><br>
                MMSI: ${target.mmsi}<br>
                Range: ${target.closest.range_nm} NM<br>
                Time: ${target.closest.time}
            `);

            closestLayerGroup.addLayer(closestMarker);
        }
		
		if (
    target.closest &&
    validLatLon(target.closest.boat_lat, target.closest.boat_lon) &&
    validLatLon(target.closest.ais_lat, target.closest.ais_lon)
) {
    const cpaLine = L.polyline(
        [
            [target.closest.boat_lat, target.closest.boat_lon],
            [target.closest.ais_lat, target.closest.ais_lon]
        ],
        {
            color: colour,
            weight: 2,
            opacity: 0.7,
            dashArray: '4,6'
        }
    ).bindPopup(popup);

    closestLayerGroup.addLayer(cpaLine);
}

        const lastPoint = target.track[target.track.length - 1];

        if (lastPoint && validLatLon(lastPoint.lat, lastPoint.lon)) {
            const arrow = makeHeadingArrow(
                lastPoint.lat,
                lastPoint.lon,
                lastPoint.cog,
                colour,
                popup
            );

            if (arrow) {
                aisLayerGroup.addLayer(arrow);
            }
        }
    });
}

function getRangeColour(rangeNm) {
    rangeNm = Number(rangeNm);

    if (isNaN(rangeNm)) return '#999999';
    if (rangeNm <= 0.25) return '#ff3333';
    if (rangeNm <= 0.5) return '#ff9900';
    if (rangeNm <= 1) return '#ffcc00';
    return '#999999';
}

function makeHeadingArrow(lat, lon, cog, colour, popup) {
    cog = Number(cog);

    if (isNaN(cog)) {
        return null;
    }

    const lengthNm = 0.08;
    const end = destinationPoint(lat, lon, cog, lengthNm);

    return L.polyline(
        [
            [lat, lon],
            [end.lat, end.lon]
        ],
        {
            color: colour,
            weight: 2,
            opacity: 0.8
        }
    ).bindPopup(popup);
}

function destinationPoint(lat, lon, bearingDeg, distanceNm) {
    const radius = 6371000;
    const distance = distanceNm * 1852;
    const bearing = bearingDeg * Math.PI / 180;

    const lat1 = lat * Math.PI / 180;
    const lon1 = lon * Math.PI / 180;

    const lat2 = Math.asin(
        Math.sin(lat1) * Math.cos(distance / radius) +
        Math.cos(lat1) * Math.sin(distance / radius) * Math.cos(bearing)
    );

    const lon2 = lon1 + Math.atan2(
        Math.sin(bearing) * Math.sin(distance / radius) * Math.cos(lat1),
        Math.cos(distance / radius) - Math.sin(lat1) * Math.sin(lat2)
    );

    return {
        lat: lat2 * 180 / Math.PI,
        lon: lon2 * 180 / Math.PI
    };
}

    function validLatLon(lat, lon) {
        lat = Number(lat);
        lon = Number(lon);

        return !isNaN(lat)
            && !isNaN(lon)
            && lat >= -90
            && lat <= 90
            && lon >= -180
            && lon <= 180
            && Math.abs(lat) > 0.001
            && Math.abs(lon) > 0.001;
    }

});
</script>

@endsection