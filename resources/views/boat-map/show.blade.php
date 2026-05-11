@extends('layouts.app')

@section('css')

<link rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
    #boatMap {
        width: 100%;
        height: calc(100vh - 70px);
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

    .boat-map-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .boat-map-buttons .btn {
        border-radius: 999px;
    }
</style>

@endsection

@section('content')

<div class="boat-map-header">

    <div style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:15px;
        flex-wrap:wrap;
    ">

        <h4>
            Tracks for vessel: {{ $mac }}
        </h4>

        <div class="boat-map-buttons">

<a href="{{ url('/boat-map/'.$mac.'/0') }}"
   class="btn btn-sm {{ $days == 0 ? 'btn-info' : 'btn-outline-light' }}">
    All time
</a>

            <a href="{{ url('/boat-map/'.$mac.'/7') }}"
               class="btn btn-sm {{ $days == 7 ? 'btn-info' : 'btn-outline-light' }}">
                7 days
            </a>

            <a href="{{ url('/boat-map/'.$mac.'/30') }}"
               class="btn btn-sm {{ $days == 30 ? 'btn-info' : 'btn-outline-light' }}">
                30 days
            </a>

            <a href="{{ url('/boat-map/'.$mac.'/90') }}"
               class="btn btn-sm {{ $days == 90 ? 'btn-info' : 'btn-outline-light' }}">
                90 days
            </a>

            <a href="{{ url('/boat-map/'.$mac.'/365') }}"
               class="btn btn-sm {{ $days == 365 ? 'btn-info' : 'btn-outline-light' }}">
                1 year
            </a>

        </div>
    </div>
</div>

<div id="boatMap"></div>

@endsection

@section('js')

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const geojsonData = @json($geojson);

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

    const features = L.geoJSON(geojsonData, {

        style: function (feature) {

            return {
                color: feature.properties.color || '#ff007b',
                weight: {{ $days >= 365 ? 2 : 3 }},
                opacity: {{ $days >= 365 ? 0.75 : 1 }}
            };
        },

        onEachFeature: function (feature, layer) {

            const popupContent = `
                <strong>Vessel MAC:</strong>
                ${feature.properties.mac}<br>

                <strong>Date:</strong>
                ${feature.properties.date}
            `;

            layer.bindPopup(popupContent);
        }

    }).addTo(map);

    if (features.getLayers().length > 0) {

        map.fitBounds(features.getBounds(), {
            padding: [2, 2],
            maxZoom: 16
        });

    } else {

        map.setView([50.8, -1.1], 7);
    }
});
</script>

@endsection