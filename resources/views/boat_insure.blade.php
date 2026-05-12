@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<style>
    body {
        background: #f3f6fb;
    }

    .dash-wrap {
        max-width: 1300px;
        margin: 18px auto;
        padding: 0 16px 30px;
    }

    .dash-header {
        background: linear-gradient(135deg, #062b49, #0d4f7a);
        color: white;
        border-radius: 16px;
        padding: 18px 22px;
        margin-bottom: 16px;
        box-shadow: 0 8px 22px rgba(0,0,0,0.12);
        display: flex;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        align-items: center;
    }

    .dash-header h1 {
        font-size: 24px;
        margin: 0;
        font-weight: 700;
    }

    .dash-header .sub {
        opacity: 0.82;
        margin-top: 4px;
        font-size: 13px;
    }

    .dash-links {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .dash-links a {
        color: white;
        text-decoration: none;
        background: rgba(255,255,255,0.14);
        padding: 8px 11px;
        border-radius: 999px;
        font-size: 13px;
    }

    .panel {
        background: white;
        border-radius: 16px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.07);
        padding: 16px;
        margin-bottom: 16px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(120px, 1fr));
        gap: 12px;
        align-items: end;
    }

    .field label {
        font-size: 12px;
        color: #536171;
        font-weight: 700;
        display: block;
        margin-bottom: 5px;
    }

    .field input,
    .field select {
        width: 100%;
        border: 1px solid #d8e0ea;
        border-radius: 10px;
        padding: 9px 10px;
        background: #fbfdff;
    }

    .btn-main {
        border: none;
        border-radius: 10px;
        padding: 10px 14px;
        background: #0d4f7a;
        color: white;
        font-weight: 700;
        cursor: pointer;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(9, 1fr);
        gap: 10px;
    }

    .stat-card {
        background: #f7fafd;
        border: 1px solid #e4ebf3;
        border-radius: 14px;
        padding: 12px;
    }

    .stat-label {
        font-size: 11px;
        color: #687789;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .stat-value {
        margin-top: 5px;
        font-size: 21px;
        font-weight: 800;
        color: #102a43;
    }

    .legend {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .legend-pill {
        display: flex;
        align-items: center;
        gap: 7px;
        background: #f7fafd;
        border: 1px solid #e4ebf3;
        border-radius: 999px;
        padding: 8px 11px;
        font-size: 13px;
        color: #344456;
    }

    .dot {
        width: 12px;
        height: 12px;
        border-radius: 999px;
        display: inline-block;
    }

    .dot-red { background: red; }
    .dot-orange { background: orange; }
    .dot-black { background: black; }

    #map {
        height: 620px;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #d8e0ea;
    }

    @media (max-width: 1000px) {
        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .filter-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
        .stats-grid,
        .filter-grid {
            grid-template-columns: 1fr;
        }

        #map {
            height: 520px;
        }
    }
</style>

<div class="dash-wrap">
    <div class="dash-header">
        <div>
            <h1>Boat Activity / Insurance Summary</h1>
            <div class="sub">MAC: {{ $mac }} · Showing {{ $rangeOptions[$range] ?? $range }}</div>
        </div>

        <div class="dash-links">
            <a href="{{ route('boat.stats', $mac) }}">Stats</a>
            <a href="{{ route('boat.raw', [$mac, '7d']) }}">Raw Data</a>
            <a href="{{ route('boat.map', $mac) }}">Map</a>
        </div>
    </div>

    <div class="panel">
        <form method="get" class="filter-grid">
            <div class="field">
                <label for="range">Range</label>
                <select name="range" id="range">
                    @foreach ($rangeOptions as $val => $label)
                        <option value="{{ $val }}" @selected($range === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="sogThreshold">Speed greater than</label>
                <input type="number" step="0.1" name="sogThreshold" id="sogThreshold" value="{{ $sogThreshold }}" min="0">
            </div>

            <div class="field">
                <label for="depThreshold">Depth less than</label>
                <input type="number" step="0.1" name="depThreshold" id="depThreshold" value="{{ $depThreshold }}" min="0">
            </div>

            <div class="field">
                <label for="awsThreshold">Wind greater than</label>
                <input type="number" step="0.1" name="awsThreshold" id="awsThreshold" value="{{ $awsThreshold }}" min="0">
            </div>

            <button type="submit" class="btn-main">Refresh</button>
        </form>
    </div>

    <div class="panel">
        <div class="stats-grid">
            @foreach ([
                'sog' => 'Max SOG',
                'aws' => 'Max AWS',
                'spd' => 'Max SPD',
                'pitch' => 'Pitch',
                'roll' => 'Roll',
                'yaw' => 'Yaw',
                'xacc' => 'X Acc',
                'yacc' => 'Y Acc',
                'zacc' => 'Z Acc',
            ] as $key => $label)
                <div class="stat-card">
                    <div class="stat-label">{{ $label }}</div>
                    <div class="stat-value">
                        {{ isset($result["max_$key"]) ? round($result["max_$key"], 2) : '-' }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="panel">
        <div class="legend">
            <div class="legend-pill">
                <span class="dot dot-red"></span>
                SOG &gt; {{ $sogThreshold }} and DEP &lt; {{ $depThreshold }}
            </div>
            <div class="legend-pill">
                <span class="dot dot-orange"></span>
                Night-time movement
            </div>
            <div class="legend-pill">
                <span class="dot dot-black"></span>
                AWS &gt; {{ $awsThreshold }}
            </div>
        </div>

        <div id="map"></div>
    </div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
const boundsData = @json($bounds);
const maxPoints = @json($maxPoints);
const nightTracks = @json($nightTracks);
const sogDepTracks = @json($sogDepTracks);
const awsTracks = @json($awsTracks);

const map = L.map('map');

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

L.tileLayer('https://tiles.openseamap.org/seamark/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenSeaMap contributors',
    transparent: true
}).addTo(map);

if (
    boundsData &&
    boundsData.minLat !== null &&
    boundsData.maxLat !== null &&
    boundsData.minLon !== null &&
    boundsData.maxLon !== null
) {
    const bounds = L.latLngBounds([
        [boundsData.minLat, boundsData.minLon],
        [boundsData.maxLat, boundsData.maxLon]
    ]);

    map.fitBounds(bounds, { padding: [30, 30] });

    L.rectangle(bounds, {
        color: "#1f77b4",
        weight: 2,
        fillOpacity: 0.03
    }).addTo(map);
} else {
    map.setView([50.8, -1.1], 8);
}

Object.entries(maxPoints).forEach(([key, pt]) => {
    L.circleMarker([pt.latdec, pt.londec], {
        radius: 6,
        weight: 2,
        fillOpacity: 0.8
    })
    .bindTooltip(`${key.toUpperCase()}: ${pt.value} @ ${pt.datetime}`)
    .addTo(map);
});

function drawLineWithPopup(points, color) {
    if (!points || points.length < 2) return;

    const latlngs = points.map(p => [p.lat, p.lon]);

    let tipContent = "";
    for (let i = 0; i < Math.min(3, points.length); i++) {
        tipContent += points[i].tip + "<br>";
    }

    if (points.length > 3) {
        tipContent += `... and ${points.length - 3} more points`;
    }

    L.polyline(latlngs, {
        color,
        weight: 4,
        opacity: 0.85
    })
    .bindPopup(tipContent)
    .addTo(map);
}

nightTracks.forEach(segment => drawLineWithPopup(segment, "orange"));
sogDepTracks.forEach(segment => drawLineWithPopup(segment, "red"));
awsTracks.forEach(segment => drawLineWithPopup(segment, "black"));
</script>
@endsection