<!DOCTYPE html>
<html>
<head>
    <title>Dangerous Boat Activity Summary</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            background: #fafafa;
        }
        h2 {
            background-color: #003366;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 10px;
        }
        form#filterForm {
            margin-bottom: 15px;
            padding: 10px 15px;
            background-color: #f2f4f8;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            max-width: 800px;
        }
        form#filterForm label {
            font-weight: bold;
            margin-right: 5px;
        }
        form#filterForm select, 
        form#filterForm input[type=number] {
            padding: 5px 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 100px;
        }
        form#filterForm button {
            padding: 6px 15px;
            background-color: #003366;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        form#filterForm button:hover {
            background-color: #002244;
        }
        table {
            border-collapse: collapse;
            margin-top: 15px;
            width: 80%;
            max-width: 800px;
            font-family: sans-serif;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        th {
            background-color: #003366;
            color: white;
            text-align: left;
            padding: 8px;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        #map { 
            height: 500px; 
            margin-top: 15px; 
            max-width: 900px;
        }
        /* Legend styling */
        .legend {
            margin-top: 15px;
            max-width: 900px;
            background: white;
            padding: 10px 15px;
            border-radius: 4px;
            box-shadow: 0 0 6px rgba(0,0,0,0.1);
            font-size: 14px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 1px solid #999;
        }
        .red { background-color: red; }
        .orange { background-color: orange; }
        .black { background-color: black; }
    </style>
</head>
<body>

<h2>Boat Activity Summary</h2>

<form id="filterForm" method="get">
    <input type="hidden" name="mac" value="{{ e($mac) }}">

    <div style="margin-bottom: 10px;">
        <label for="range">Show data for:</label><br>
        <select name="range" id="range" onchange="this.form.submit()" style="width: 150px; padding: 5px; border-radius: 4px; border: 1px solid #ccc;">
            @foreach ($rangeOptions as $val => $label)
                <option value="{{ $val }}" {{ $range === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
        <label for="sogThreshold">Speed &gt; </label>
        <input type="number" step="0.1" name="sogThreshold" id="sogThreshold" value="{{ $sogThreshold }}" min="0" style="width: 100px;">

        <label for="depThreshold">Depth &lt; </label>
        <input type="number" step="0.1" name="depThreshold" id="depThreshold" value="{{ $depThreshold }}" min="0" style="width: 100px;">

        <label for="awsThreshold">Wind Speed &gt; </label>
        <input type="number" step="0.1" name="awsThreshold" id="awsThreshold" value="{{ $awsThreshold }}" min="0" style="width: 100px;">

        <button type="submit" style="padding: 6px 15px; background-color: #003366; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Refresh
        </button>
    </div>
</form>


<table>
    <tr>
        <th>Max SOG</th><td>{{ $result['max_sog'] ?? '' }} kn</td>
        <th>Max AWS</th><td>{{ $result['max_aws'] ?? '' }} kn</td>
        <th>Max SPD</th><td>{{ $result['max_spd'] ?? '' }} kn</td>
    </tr>
    <tr>
        <th>Pitch</th><td>{{ $result['max_pitch'] ?? '' }}</td>
        <th>Roll</th><td>{{ $result['max_roll'] ?? '' }}</td>
        <th>Yaw</th><td>{{ $result['max_yaw'] ?? '' }}</td>
    </tr>
    <tr>
        <th>X Acc</th><td>{{ $result['max_xacc'] ?? '' }}</td>
        <th>Y Acc</th><td>{{ $result['max_yacc'] ?? '' }}</td>
        <th>Z Acc</th><td>{{ $result['max_zacc'] ?? '' }}</td>
    </tr>
</table>

<div class="legend">
    <div class="legend-item"><div class="legend-color red"></div> <div>Red: SOG > {{ e($sogThreshold) }} and DEP < {{ e($depThreshold) }}</div></div>
    <div class="legend-item"><div class="legend-color orange"></div> <div>Orange: Night-time movement</div></div>
    <div class="legend-item"><div class="legend-color black"></div> <div>Black: AWS > {{ e($awsThreshold) }}</div></div>
</div>

<div id="map"></div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
const map = L.map('map');

const bounds = L.latLngBounds([
    [{{ $bounds->minLat ?? 0 }}, {{ $bounds->minLon ?? 0 }}],
    [{{ $bounds->maxLat ?? 0 }}, {{ $bounds->maxLon ?? 0 }}]
]);
map.fitBounds(bounds);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

L.tileLayer('https://tiles.openseamap.org/seamark/{z}/{x}/{y}.png', {
  attribution: '&copy; OpenSeaMap contributors',
  transparent: true
}).addTo(map);

// Max value markers
@foreach ($maxPoints as $key => $pt)
    L.marker([{{ $pt['latdec'] }}, {{ $pt['londec'] }}])
     .bindTooltip("{{ ucfirst($key) }}: {{ $pt['value'] }} @ {{ $pt['datetime'] }}")
     .addTo(map);
@endforeach

L.geoJSON({
    "type": "Polygon",
    "coordinates": [[
        [{{ $bounds->minLon ?? 0 }}, {{ $bounds->minLat ?? 0 }}],
        [{{ $bounds->maxLon ?? 0 }}, {{ $bounds->minLat ?? 0 }}],
        [{{ $bounds->maxLon ?? 0 }}, {{ $bounds->maxLat ?? 0 }}],
        [{{ $bounds->minLon ?? 0 }}, {{ $bounds->maxLat ?? 0 }}],
        [{{ $bounds->minLon ?? 0 }}, {{ $bounds->minLat ?? 0 }}]
    ]]
}, {
    color: 'blue',
    weight: 2,
    fillOpacity: 0.05
}).addTo(map);

// Helper to add track segments with color
function addTrackSegments(segments, color) {
    segments.forEach(segment => {
        const latlngs = segment.map(p => [p.lat, p.lon]);
        const polyline = L.polyline(latlngs, {color: color, weight: 5});
        polyline.addTo(map);
        polyline.bindTooltip(segment[0].tip);
    });
}

// Add night time tracks (orange)
addTrackSegments(@json($nightTracks), 'orange');

// Add SOG/DEP tracks (red)
addTrackSegments(@json($sogDepTracks), 'red');

// Add AWS tracks (black)
addTrackSegments(@json($awsTracks), 'black');
</script>

</body>
</html>
