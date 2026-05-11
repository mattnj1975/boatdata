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
            line-height: 1;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
        }

        .badge-online {
            background: #1ee68c;
            color: #032013;
        }

        .badge-stale {
            background: #ffc857;
            color: #2c1d00;
        }

        .badge-offline {
            background: #ff5c7a;
            color: #2a0008;
        }

        #boatMap {
            height: 470px;
            border-radius: 22px;
            overflow: hidden;
        }

        .form-control,
        .form-select {
            background: rgba(255,255,255,0.1);
            color: white;
            border: 1px solid rgba(255,255,255,0.18);
        }

        .form-select option {
            color: black;
        }

        canvas {
            max-width: 100%;
        }
    </style>
</head>

<body>

<div class="container-fluid py-4 px-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">Boat Dashboard</h1>
            <div class="muted">{{ $mac }} · {{ $year }}</div>
        </div>

        <form method="GET" class="d-flex gap-2">
            <select
                class="form-select"
                onchange="location.href='{{ url('boat-stats') }}/' + this.value + '?year={{ $year }}'"
            >
                @foreach($boats as $boat)
                    <option value="{{ $boat }}" @selected($boat === $mac)>
                        {{ $boat }}
                    </option>
                @endforeach
            </select>

            <input type="number" name="year" value="{{ $year }}" class="form-control" style="width:120px">
            <button class="btn btn-info fw-bold">Go</button>
        </form>
    </div>

    <div class="glass p-4 mb-4">
        <div class="row g-4 align-items-center">

            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    @php
                        $statusClass = $status === 'Online' ? 'badge-online' : ($status === 'Stale' ? 'badge-stale' : 'badge-offline');
                    @endphp

                    <span class="badge rounded-pill {{ $statusClass }} px-3 py-2">
                        {{ $status }}
                    </span>

                    <span class="muted">
                        {{ $lastSeenAge }}
                    </span>
                </div>

                <div class="hero-value">
                    {{ number_format($latest->sog ?? 0, 1) }}
                    <span style="font-size:1.2rem">kn</span>
                </div>

                <div class="muted mt-2">
                    Current / last known speed
                </div>

                <hr style="border-color:rgba(255,255,255,0.15)">

                <div class="row">
                    <div class="col-6">
                        <div class="muted small">COG</div>
                        <div class="h4 fw-bold">
                            {{ number_format($latest->cog ?? 0, 0) }}°
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="muted small">Wind</div>
                        <div class="h4 fw-bold">
                            {{ number_format($latest->aws ?? 0, 1) }} kn
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                @if($latest)
                    <div id="boatMap"></div>
                @else
                    <div class="p-5 text-center muted">No latest position available</div>
                @endif
            </div>

        </div>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-6 col-md-4 col-xl-2">
            <div class="glass p-3">
                <div class="muted small">Top Speed</div>
                <div class="stat-value">{{ number_format($summary->top_speed ?? 0, 1) }}</div>
                <div class="muted">kn</div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="glass p-3">
                <div class="muted small">Average Speed</div>
                <div class="stat-value">{{ number_format($summary->avg_speed ?? 0, 1) }}</div>
                <div class="muted">kn</div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="glass p-3">
                <div class="muted small">Total Miles</div>
                <div class="stat-value">{{ number_format($totalMiles ?? 0, 0) }}</div>
                <div class="muted">nm</div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="glass p-3">
                <div class="muted small">Days Used</div>
                <div class="stat-value">{{ $summary->days_used ?? 0 }}</div>
                <div class="muted">days</div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="glass p-3">
                <div class="muted small">Top Wind</div>
                <div class="stat-value">{{ number_format($summary->top_wind ?? 0, 1) }}</div>
                <div class="muted">kn</div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="glass p-3">
                <div class="muted small">Average Wind</div>
                <div class="stat-value">{{ number_format($summary->avg_wind ?? 0, 1) }}</div>
                <div class="muted">kn</div>
            </div>
        </div>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-xl-8">
            <div class="glass p-4">
                <h5 class="fw-bold mb-3">Speed Over Time</h5>
                <canvas id="speedChart" height="110"></canvas>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="glass p-4">
                <h5 class="fw-bold mb-3">Wind Over Time</h5>
                <canvas id="windChart" height="230"></canvas>
            </div>
        </div>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-xl-6">
            <div class="glass p-4">
                <h5 class="fw-bold mb-3">Monthly Usage</h5>
                <canvas id="monthlyChart" height="150"></canvas>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="glass p-4">
                <h5 class="fw-bold mb-3">Days Used / Data Activity</h5>
                <canvas id="usageChart" height="150"></canvas>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
const daily = @json($daily);
const monthly = @json($monthly);
const latest = @json($latest);
const track = @json($track);

const labels = daily.map(r => r.date);

Chart.defaults.color = '#dff6ff';
Chart.defaults.borderColor = 'rgba(255,255,255,0.08)';

new Chart(document.getElementById('speedChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [
            {
                label: 'Max SOG',
                data: daily.map(r => Number(r.max_sog || 0)),
                borderColor: '#00e5ff',
                backgroundColor: 'rgba(0,229,255,0.15)',
                pointRadius: 0,
                borderWidth: 3,
                tension: 0.35,
                fill: true
            },
            {
                label: 'Average SOG',
                data: daily.map(r => Number(r.avg_sog || 0)),
                borderColor: '#7cffcb',
                backgroundColor: 'rgba(124,255,203,0.10)',
                pointRadius: 0,
                borderWidth: 2,
                tension: 0.35,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#dff6ff'
                }
            }
        },
        scales: {
            x: {
                ticks: { color: '#9ed8ff' },
                grid: { color: 'rgba(255,255,255,0.05)' }
            },
            y: {
                beginAtZero: true,
                ticks: { color: '#9ed8ff' },
                grid: { color: 'rgba(255,255,255,0.05)' }
            }
        }
    }
});

new Chart(document.getElementById('windChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [
            {
                label: 'Max AWS',
                data: daily.map(r => Number(r.max_aws || 0)),
                borderColor: '#ffb347',
                backgroundColor: 'rgba(255,179,71,0.15)',
                pointRadius: 0,
                borderWidth: 3,
                tension: 0.35,
                fill: true
            },
            {
                label: 'Average AWS',
                data: daily.map(r => Number(r.avg_aws || 0)),
                borderColor: '#ff6b6b',
                backgroundColor: 'rgba(255,107,107,0.10)',
                pointRadius: 0,
                borderWidth: 2,
                tension: 0.35,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#dff6ff'
                }
            }
        },
        scales: {
            x: {
                ticks: { color: '#9ed8ff' },
                grid: { color: 'rgba(255,255,255,0.05)' }
            },
            y: {
                beginAtZero: true,
                ticks: { color: '#9ed8ff' },
                grid: { color: 'rgba(255,255,255,0.05)' }
            }
        }
    }
});

new Chart(document.getElementById('usageChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Records per day',
            data: daily.map(r => Number(r.records || 0)),
            backgroundColor: '#00c2ff',
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                ticks: { color: '#9ed8ff' },
                grid: { color: 'rgba(255,255,255,0.05)' }
            },
            y: {
                beginAtZero: true,
                ticks: { color: '#9ed8ff' },
                grid: { color: 'rgba(255,255,255,0.05)' }
            }
        }
    }
});

new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: monthly.map(r => r.month_name),
        datasets: [
            {
                label: 'Days Used',
                data: monthly.map(r => Number(r.days_used || 0)),
                backgroundColor: '#00e5ff',
                borderRadius: 6
            },
            {
                label: 'Top Speed',
                data: monthly.map(r => Number(r.max_sog || 0)),
                backgroundColor: '#7cffcb',
                borderRadius: 6
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#dff6ff'
                }
            }
        },
        scales: {
            x: {
                ticks: { color: '#9ed8ff' },
                grid: { color: 'rgba(255,255,255,0.05)' }
            },
            y: {
                beginAtZero: true,
                ticks: { color: '#9ed8ff' },
                grid: { color: 'rgba(255,255,255,0.05)' }
            }
        }
    }
});

@if($latest)

const lat = Number(latest.latdec);
const lon = Number(latest.londec);
const cog = Number(latest.cog || 0);
const sog = Number(latest.sog || 0);

function destinationPoint(lat, lon, bearingDeg, distanceKm) {
    const R = 6371;
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

const map = L.map('boatMap').setView([lat, lon], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19
}).addTo(map);



L.marker([lat, lon])
    .addTo(map)
    .bindPopup(`
        <strong>{{ $mac }}</strong><br>
        Last seen: {{ $latest->date }} {{ $latest->utc }}<br>
        SOG: ${sog.toFixed(1)} kn<br>
        COG: ${cog.toFixed(0)}°
    `)
    .openPopup();

const projectionKm = sog * 1.852;
const projected = destinationPoint(lat, lon, cog, projectionKm);

if (projectionKm > 0) {
    L.polyline([[lat, lon], projected], {
        weight: 4,
        opacity: 0.9,
        dashArray: '10,10'
    }).addTo(map);

    L.marker(projected)
        .addTo(map)
        .bindPopup('Projected position in 1 hour');
}



@endif
</script>

</body>
</html>