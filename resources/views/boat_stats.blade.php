<!doctype html>
<html>
<head>
    <title>Boat Dashboard</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        body {
            background: radial-gradient(circle at top left, #12395a, #061521 45%, #02070c);
            color: #eaf6ff;
            min-height: 100vh;
        }

        .glass {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            backdrop-filter: blur(12px);
        }

        .muted {
            color: rgba(234,246,255,0.65);
        }

        .hero-value {
            font-size: 3.3rem;
            font-weight: 800;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
        }

        #boatMap {
            height: 470px;
            border-radius: 22px;
            overflow: hidden;
        }

        .table-dark {
            --bs-table-bg: transparent;
        }

        canvas {
            max-width: 100%;
        }

        .form-select,
        .form-control {
            background: rgba(255,255,255,0.08);
            color: white;
            border: 1px solid rgba(255,255,255,0.15);
        }

        .form-select option {
            color: black;
        }
    </style>
</head>

<body>

<div class="container-fluid py-4 px-4">
@include('partials.boat_nav', ['mac' => $mac ?? null])

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">

        <div>
            <h1 class="fw-bold mb-1">
                {{ $deviceSettings->boatname ?? 'Boat Dashboard' }}
            </h1>

            <div class="muted">
                {{ $mac }}
            </div>
        </div>

        <form method="GET" class="d-flex gap-2">



            <input
                type="number"
                name="year"
                value="{{ $year }}"
                class="form-control"
                style="width:120px"
            >

            <button class="btn btn-info fw-bold">
                Go
            </button>
			

        </form>

    </div>

    <div class="glass p-4 mb-4">

        <div class="row g-4">

            <div class="col-lg-4">

                <div class="mb-3">

@php
    $statusClass = match($status) {
        'Online' => 'bg-success',
        'Idle' => 'bg-info text-dark',
        'Stale' => 'bg-warning text-dark',
        default => 'bg-danger'
    };
@endphp

<span class="badge {{ $statusClass }}">
    {{ $status }}
</span>

                    <span class="muted ms-2">
                        {{ $lastSeenAge }}
                    </span>

                </div>

                <div class="hero-value">
                    {{ number_format($latest->sog ?? 0,1) }}
                    <span style="font-size:1.4rem">kn</span>
                </div>

<div class="{{ $status === 'Online' ? 'text-success' : 'text-warning' }} fw-bold">
    Device Last Seen
</div>

<div class="muted small">
    {{ $deviceSettings->lastseen ?? '-' }}
</div>

<div class="muted small mt-1">
    {{ $lastSeenAge }}
</div>


                <hr style="border-color:rgba(255,255,255,0.12)">

                <div class="row">

                    <div class="col-6">
                        <div class="muted small">COG</div>
                        <div class="h4 fw-bold">
                            {{ number_format($latest->cog ?? 0,0) }}°
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="muted small">AWS</div>
                        <div class="h4 fw-bold">
                            {{ number_format($latest->aws ?? 0,1) }} kn
                        </div>
                    </div>

                </div>

                <hr style="border-color:rgba(255,255,255,0.12)">

                <div class="row g-3">

                    <div class="col-6">
                        <div class="muted small">Serial</div>
                        <div class="fw-bold">
                            {{ $deviceSettings->serial ?? '-' }}
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="muted small">MMSI</div>
                        <div class="fw-bold">
                            {{ $deviceSettings->gprsuser ?? '-' }}
                        </div>
                    </div>

<div class="col-6">
    <div class="muted small">Firmware</div>
    <div class="fw-bold">
        {{ $deviceSettings->version ?? 0 }}
    </div>
</div>

@if(($deviceSettings->update_to ?? 0) > ($deviceSettings->version ?? 0))
    <div class="col-6">
        <div class="muted small">Pending Upgrade</div>
        <div class="fw-bold text-warning">
            {{ $deviceSettings->update_to }}
        </div>
        <div class="muted small">
            Device will upgrade at next reboot
        </div>
    </div>
@endif

                    <div class="col-12">
                        <div class="muted small">Device Last Online</div>
                        <div class="fw-bold">
                            {{ $deviceSettings->lastseen ?? '-' }}
                        </div>
                    </div>
					
					

                </div>

            </div>

            <div class="col-lg-8">

                @if($latest)
                    <div id="boatMap"></div>
                @endif

            </div>

        </div>

    </div>

    <div class="row g-3 mb-4">

        <div class="col-md-2">
            <div class="glass p-3">
                <div class="muted small">Top Speed</div>
                <div class="stat-value">
                    {{ number_format($summary->top_speed ?? 0,1) }}
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="glass p-3">
                <div class="muted small">Average Speed</div>
                <div class="stat-value">
                    {{ number_format($summary->avg_speed ?? 0,1) }}
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="glass p-3">
                <div class="muted small">Top Wind</div>
                <div class="stat-value">
                    {{ number_format($summary->top_wind ?? 0,1) }}
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="glass p-3">
                <div class="muted small">Average Wind</div>
                <div class="stat-value">
                    {{ number_format($summary->avg_wind ?? 0,1) }}
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="glass p-3">
                <div class="muted small">Miles</div>
                <div class="stat-value">
                    {{ number_format($totalMiles ?? 0,0) }}
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="glass p-3">
                <div class="muted small">Days Used</div>
                <div class="stat-value">
                    {{ $summary->days_used ?? 0 }}
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-xl-8">
            <div class="glass p-4">
                <h5 class="fw-bold mb-3">Speed</h5>
                <canvas id="speedChart"></canvas>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="glass p-4">
                <h5 class="fw-bold mb-3">Wind</h5>
                <canvas id="windChart"></canvas>
            </div>
        </div>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-xl-6">
            <div class="glass p-4">
                <h5 class="fw-bold mb-3">Usage</h5>
                <canvas id="usageChart"></canvas>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="glass p-4">
                <h5 class="fw-bold mb-3">Monthly</h5>
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-12">

            <div class="glass p-4">

                <h5 class="fw-bold mb-3">
                    Recent Upload Log
                </h5>

                <div class="table-responsive">

                    <table class="table table-dark table-hover align-middle">

                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Status</th>
                                <th>IP</th>
								<th>SD Card</th>
								<th>DB Import</th>
                            </tr>
                        </thead>

                        <tbody>

                        @foreach($uploadLogs as $log)

@php

    $statusCode = (int) $log->upload_status;

    $statusText = $uploadStatusCodes[$statusCode] ?? 'Unknown';

    $statusClasses = [
        0  => 'bg-success',
        1  => 'bg-danger',
        2  => 'bg-warning text-dark',
        3  => 'bg-danger',
        4  => 'bg-info text-dark',
        5  => 'bg-danger',
        6  => 'bg-warning text-dark',
        7  => 'bg-warning text-dark',
        8  => 'bg-secondary',
        9  => 'bg-primary',
        10 => 'bg-info text-dark',
        11 => 'bg-warning text-dark',
        12 => 'bg-danger',
        13 => 'bg-danger',
        14 => 'bg-warning text-dark',
        15 => 'bg-secondary',
        16 => 'bg-dark'
    ];

    $badgeClass = $statusClasses[$statusCode] ?? 'bg-secondary';

@endphp

                            <tr>

                                <td>{{ $log->uload_time }}</td>

                                <td>
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $statusCode }}:{{ $statusText }}
                                    </span>
                                </td>

                                <td>{{ $log->ip_address }}</td>

<td>
    @php
        $sdSpace = (float) ($log->sd_space ?? 0);
        $sdUsed = (float) ($log->sd_used ?? 0);
        $sdPercent = $sdSpace > 0 ? ($sdUsed / $sdSpace) * 100 : 0;

        $spaceText = $sdSpace >= 1024
            ? number_format($sdSpace / 1024, 1) . ' GB'
            : number_format($sdSpace, 0) . ' MB';

        $usedText = $sdUsed >= 1024
            ? number_format($sdUsed / 1024, 1) . ' GB'
            : number_format($sdUsed, 0) . ' MB';
    @endphp

    <div class="fw-bold">
        {{ $usedText }} / {{ $spaceText }}
    </div>

    <div class="progress mt-1" style="height:7px; background:rgba(255,255,255,0.12);">
        <div
            class="progress-bar {{ $sdPercent > 90 ? 'bg-danger' : ($sdPercent > 75 ? 'bg-warning' : 'bg-info') }}"
            style="width: {{ min($sdPercent, 100) }}%;"
        ></div>
    </div>

    <div class="muted small mt-1">
        {{ number_format($sdPercent, 1) }}% used
    </div>
</td>

<td>

    <div class="d-flex gap-2 align-items-center">

@php
    $dbOk = (int) ($log->db_ok ?? 0);
@endphp

@if($dbOk >= 0)

    <span class="badge bg-success px-3 py-2">
        +{{ number_format($dbOk) }}
    </span>

@endif

        @php
    $dbErr = (int) ($log->db_err ?? 0);
@endphp

@if($dbErr > 0)

    <span class="badge bg-danger px-3 py-2">
        -{{ number_format($dbErr) }}
    </span>

@endif

    </div>

</td>

                            </tr>

                        @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>

Chart.defaults.color = '#dff6ff';

const daily = @json($daily);
const monthly = @json($monthly);
const latest = @json($latest);

const labels = daily.map(r => r.date);

new Chart(document.getElementById('speedChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: 'Max SOG',
            data: daily.map(r => Number(r.max_sog || 0)),
            borderColor: '#00e5ff',
            backgroundColor: 'rgba(0,229,255,0.15)',
            borderWidth: 3,
            tension: 0.35,
            fill: true,
            pointRadius: 0
        }]
    }
});

new Chart(document.getElementById('windChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: 'Max AWS',
            data: daily.map(r => Number(r.max_aws || 0)),
            borderColor: '#ffb347',
            backgroundColor: 'rgba(255,179,71,0.15)',
            borderWidth: 3,
            tension: 0.35,
            fill: true,
            pointRadius: 0
        }]
    }
});

new Chart(document.getElementById('usageChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Records',
            data: daily.map(r => Number(r.records || 0)),
            backgroundColor: '#00c2ff'
        }]
    }
});

new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: monthly.map(r => r.month_name),
        datasets: [{
            label: 'Days Used',
            data: monthly.map(r => Number(r.days_used || 0)),
            backgroundColor: '#7cffcb'
        }]
    }
});

@if($latest)

const lat = Number(latest.latdec);
const lon = Number(latest.londec);
const cog = Number(latest.cog || 0);
const sog = Number(latest.sog || 0);

const map = L.map('boatMap').setView([lat, lon], 13);

// Base map
L.tileLayer(
    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }
).addTo(map);

// OpenSeaMap overlay
L.tileLayer(
    'https://tiles.openseamap.org/seamark/{z}/{x}/{y}.png',
    {
        maxZoom: 18,
        opacity: 0.9,
        attribution: '&copy; OpenSeaMap'
    }
).addTo(map);

function destinationPoint(lat, lon, bearingDeg, distanceNm) {
    const R = 6371;
    const distanceKm = distanceNm * 1.852;
    const brng = bearingDeg * Math.PI / 180;
    const d = distanceKm / R;

    const lat1 = lat * Math.PI / 180;
    const lon1 = lon * Math.PI / 180;

    const lat2 = Math.asin(
        Math.sin(lat1) * Math.cos(d) +
        Math.cos(lat1) * Math.sin(d) * Math.cos(brng)
    );

    const lon2 = lon1 + Math.atan2(
        Math.sin(brng) * Math.sin(d) * Math.cos(lat1),
        Math.cos(d) - Math.sin(lat1) * Math.sin(lat2)
    );

    return [
        lat2 * 180 / Math.PI,
        lon2 * 180 / Math.PI
    ];
}

const latestTime = new Date(
    `${latest.date} ${latest.utc}`.replace(' ', 'T')
);
const ageMinutes = (new Date() - latestTime) / 1000 / 60;
const isRecentPosition = ageMinutes <= 10;

const boatColour = isRecentPosition ? '#00e5ff' : '#ffb347';

const boatIcon = L.divIcon({
    className: '',
    iconSize: [34, 34],
    iconAnchor: [17, 17],
    popupAnchor: [0, -18],
    html: `
        <div style="
            width:34px;
            height:34px;
            transform: rotate(${cog}deg);
            display:flex;
            align-items:center;
            justify-content:center;
            filter: drop-shadow(0 2px 5px rgba(0,0,0,0.65));
        ">
            <div style="
                width:0;
                height:0;
                border-left:10px solid transparent;
                border-right:10px solid transparent;
                border-bottom:28px solid ${boatColour};
            "></div>
        </div>
    `
});

function formatAge(minutes) {
    minutes = Math.max(0, Math.floor(minutes));

    if (minutes < 1) return 'just now';
    if (minutes < 60) return `${minutes} min${minutes === 1 ? '' : 's'}`;

    const hours = Math.floor(minutes / 60);
    if (hours < 48) return `${hours} hour${hours === 1 ? '' : 's'}`;

    const days = Math.floor(hours / 24);
    if (days < 30) return `${days} day${days === 1 ? '' : 's'}`;

    const months = Math.floor(days / 30);
    return `${months} month${months === 1 ? '' : 's'}`;
}

L.marker([lat, lon], { icon: boatIcon })
    .addTo(map)
    .bindPopup(`
        <strong>{{ $deviceSettings->boatname ?? $mac }}</strong><br>
Device last seen: {{ $deviceSettings->lastseen ?? '-' }}<br>
GPS position age: ${formatAge(ageMinutes)} ago<br>
        SOG ${sog.toFixed(1)} kn<br>
        COG ${cog.toFixed(0)}°
    `)
    .openPopup();

if (isRecentPosition && sog > 0.2) {

    const projected30 = destinationPoint(lat, lon, cog, sog * 0.5);

    L.polyline(
        [
            [lat, lon],
            projected30
        ],
        {
            color: '#00e5ff',
            weight: 4,
            opacity: 0.9,
            dashArray: '8,8'
        }
    ).addTo(map);

    L.circleMarker(projected30, {
        radius: 6,
        color: '#00e5ff',
        fillColor: '#00e5ff',
        fillOpacity: 0.9
    })
    .addTo(map)
    .bindPopup(`Projected position in 30 mins at ${sog.toFixed(1)} kn`);
}

@endif

</script>

</body>
</html>