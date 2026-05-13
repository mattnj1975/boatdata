@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
.trip-wrap {
    padding: 24px;
}

.trip-card {
    background: #101827;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 18px;
    color: #e5e7eb;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
}

.trip-muted {
    color: #9ca3af;
}

.trip-stat {
    font-size: 1.35rem;
    font-weight: 700;
}

.trip-map {
    height: 320px;
    border-radius: 16px;
    overflow: hidden;
}

.trip-table {
    color: #e5e7eb;
    margin-bottom: 0;
}

.trip-table th,
.trip-table td {
    border-color: rgba(255,255,255,0.08);
}

.trip-table th {
    color: #9ca3af;
    font-weight: 500;
}

.boundary-row {
    background: rgba(245, 158, 11, 0.25) !important;
}

.btn-soft {
    background: rgba(255,255,255,0.08);
    color: #e5e7eb;
    border: 1px solid rgba(255,255,255,0.12);
}

.btn-soft:hover {
    background: rgba(255,255,255,0.14);
    color: #fff;
}
</style>

<div class="container-fluid trip-wrap">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Trip #{{ $trip->id }}</h1>

            <div class="trip-muted">
                <code>{{ $trip->mac }}</code>
                ·
                {{ optional($trip->start_time)->format('d/m/Y H:i') }}
                to
                {{ optional($trip->end_time)->format('d/m/Y H:i') }}
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('trips.index') }}" class="btn btn-soft">
                Back
            </a>

            <a href="{{ route('trips.edit', $trip) }}" class="btn btn-primary">
                Manual Edit
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="trip-card p-3">
                <div class="trip-muted">Duration</div>
                <div class="trip-stat">
                    {{ $trip->duration_minutes }} mins
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="trip-card p-3">
                <div class="trip-muted">Distance</div>
                <div class="trip-stat">
                    {{ number_format((float)$trip->distance_nm, 2) }} nm
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="trip-card p-3">

                <div class="trip-muted mb-2">
                    Actions
                </div>

                <div class="d-flex gap-2 flex-wrap">

                    <form method="POST" action="{{ route('trips.confirm', $trip) }}">
                        @csrf
                        <button class="btn btn-sm btn-success">
                            Confirm
                        </button>
                    </form>

                    <form method="POST" action="{{ route('trips.ignore', $trip) }}">
                        @csrf
                        <button class="btn btn-sm btn-warning">
                            Ignore
                        </button>
                    </form>

                    <form method="POST"
                          action="{{ route('trips.merge-next', $trip) }}"
                          onsubmit="return confirm('Merge this trip with next trip?')">

                        @csrf

                        <button class="btn btn-sm btn-outline-primary">
                            Merge Next
                        </button>
                    </form>

                    <form method="POST"
                          action="{{ route('trips.destroy', $trip) }}"
                          onsubmit="return confirm('Delete this trip?')">

                        @csrf
                        @method('DELETE')

                        <button class="btn btn-sm btn-outline-danger">
                            Delete
                        </button>
                    </form>

                </div>
            </div>
        </div>

    </div>

    <div class="row g-4">

        {{-- START PANEL --}}
        <div class="col-xl-6">

            <div class="trip-card p-3">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>
                        <h4 class="mb-1">Start Point</h4>

                        <div class="trip-muted">
                            {{ optional($trip->start_time)->format('d/m/Y H:i:s') }}
                        </div>
                    </div>

                    <div class="d-flex gap-1 flex-wrap">

@foreach([-5, -1, 1, 5] as $m)

    <button type="button"
            class="btn btn-sm btn-soft"
            onclick="nudgeBoundary('start', {{ $m }})">

        {{ $m > 0 ? '+' : '' }}{{ $m }}m

    </button>

@endforeach

<button type="button"
        class="btn btn-sm btn-success"
        onclick="saveBoundary('start')">

    Save Start

</button>

                    </div>

                </div>

                <div id="startMap" class="trip-map mb-3"></div>

                <div class="table-responsive">

                    <table class="table table-sm trip-table">

                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>SOG</th>
                                <th>SPD</th>
                                <th>COG</th>
								<th>RPM1</th>
                            </tr>
                        </thead>

                        <tbody id="startTableBody">

                            @foreach($startPoints as $p)

                                <tr class="{{ $p->id == $trip->start_boatdata_id ? 'boundary-row' : '' }}">

                                    <td>
                                        {{ \Carbon\Carbon::parse($p->datetime)->format('H:i:s') }}
                                    </td>

                                    <td>
                                        {{ $p->sog !== null ? number_format($p->sog,1) : '-' }}
                                    </td>

                                    <td>
                                        {{ $p->spd !== null ? number_format($p->spd,1) : '-' }}
                                    </td>

                                    <td>
                                        {{ $p->cog ?? '-' }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        {{-- END PANEL --}}
        <div class="col-xl-6">

            <div class="trip-card p-3">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>
                        <h4 class="mb-1">End Point</h4>

                        <div class="trip-muted">
                            {{ optional($trip->end_time)->format('d/m/Y H:i:s') }}
                        </div>
                    </div>

                    <div class="d-flex gap-1 flex-wrap">

@foreach([-5, -1, 1, 5] as $m)

    <button type="button"
            class="btn btn-sm btn-soft"
            onclick="nudgeBoundary('end', {{ $m }})">

        {{ $m > 0 ? '+' : '' }}{{ $m }}m

    </button>

@endforeach

<button type="button"
        class="btn btn-sm btn-success"
        onclick="saveBoundary('end')">

    Save End

</button>

                    </div>

                </div>

                <div id="endMap" class="trip-map mb-3"></div>

                <div class="table-responsive">

                    <table class="table table-sm trip-table">

                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>SOG</th>
                                <th>SPD</th>
                                <th>COG</th>
                            </tr>
                        </thead>

                        <tbody id="endTableBody">

                            @foreach($endPoints as $p)

                                <tr class="{{ $p->id == $trip->end_boatdata_id ? 'boundary-row' : '' }}">

                                    <td>
                                        {{ \Carbon\Carbon::parse($p->datetime)->format('H:i:s') }}
                                    </td>

                                    <td>
                                        {{ $p->sog !== null ? number_format($p->sog,1) : '-' }}
                                    </td>

                                    <td>
                                        {{ $p->spd !== null ? number_format($p->spd,1) : '-' }}
                                    </td>

                                    <td>
                                        {{ $p->cog ?? '-' }}
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

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let boundaryState = {
    start: {
        recordId: {{ $trip->start_boatdata_id }},
        map: null,
        marker: null,
        line: null
    },
    end: {
        recordId: {{ $trip->end_boatdata_id }},
        map: null,
        marker: null,
        line: null
    }
};

function initMap(type, elementId) {
    const map = L.map(elementId);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    boundaryState[type].map = map;
}

function renderBoundary(type, data) {
    const state = boundaryState[type];
    const map = state.map;

    state.recordId = data.centre.id;

    if (state.line) {
        map.removeLayer(state.line);
    }

    if (state.marker) {
        map.removeLayer(state.marker);
    }

    const latLngs = data.map
        .filter(p => p.latdec && p.londec)
        .map(p => [parseFloat(p.latdec), parseFloat(p.londec)]);

    if (latLngs.length > 1) {
        state.line = L.polyline(latLngs, {
            weight: 4,
            opacity: 0.85
        }).addTo(map);

        map.fitBounds(state.line.getBounds(), {
            padding: [25, 25]
        });
    }

    if (data.centre.lat && data.centre.lon) {
        state.marker = L.marker([
            parseFloat(data.centre.lat),
            parseFloat(data.centre.lon)
        ]).addTo(map).bindPopup(data.centre.datetime).openPopup();

        if (latLngs.length <= 1) {
            map.setView([parseFloat(data.centre.lat), parseFloat(data.centre.lon)], 15);
        }
    }

    renderTable(type, data.table, data.centre.id);
}

function renderTable(type, rows, centreId) {

    const tbody = document.getElementById(type + 'TableBody');

    if (!rows.length) {

        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="trip-muted">
                    No records found.
                </td>
            </tr>
        `;

        return;
    }

    tbody.innerHTML = rows.map(row => {

        const d = new Date(String(row.datetime).replace(' ', 'T'));

        const date =
            d.getDate().toString().padStart(2, '0') + '/' +
            (d.getMonth() + 1).toString().padStart(2, '0');

        const time =
            d.toLocaleTimeString('en-GB', {
                hour12: false
            });

        const cls =
            Number(row.id) === Number(centreId)
                ? 'boundary-row'
                : '';

        return `
            <tr class="${cls}">

                <td>
                    ${date}<br>
                    <small>${time}</small>
                </td>

                <td>
                    ${row.sog !== null
                        ? Number(row.sog).toFixed(1)
                        : '-'}
                </td>

                <td>
                    ${row.spd !== null
                        ? Number(row.spd).toFixed(1)
                        : '-'}
                </td>

                <td>
                    ${row.cog !== null
                        ? row.cog
                        : '-'}
                </td>

                <td>
                    ${row.rpm1 !== null
                        ? Math.round(row.rpm1)
                        : '-'}
                </td>

                <td>
                    ${row.rpm2 !== null
                        ? Math.round(row.rpm2)
                        : '-'}
                </td>

            </tr>
        `;

    }).join('');
}

async function loadBoundary(type) {
    const recordId = boundaryState[type].recordId;

    const url = `{{ url('/trips/' . $trip->id . '/boundary-data') }}?type=${type}&record_id=${recordId}`;

    const response = await fetch(url, {
        headers: {
            'Accept': 'application/json'
        }
    });

    const data = await response.json();

    if (!response.ok) {
        alert(data.error || 'Could not load boundary data');
        return;
    }

    renderBoundary(type, data);
}

async function nudgeBoundary(type, minutes) {
    const recordId = boundaryState[type].recordId;

    const url = `{{ url('/trips/' . $trip->id . '/nudge-record') }}?record_id=${recordId}&minutes=${minutes}`;

    const response = await fetch(url, {
        headers: {
            'Accept': 'application/json'
        }
    });

    const data = await response.json();

    if (!response.ok) {
        alert(data.error || 'No more records in that direction');
        return;
    }

    boundaryState[type].recordId = data.record_id;

    await loadBoundary(type);
}

async function saveBoundary(type) {
    const response = await fetch(`{{ url('/trips/' . $trip->id . '/save-boundary') }}`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            type: type,
            record_id: boundaryState[type].recordId
        })
    });

    const data = await response.json();

    if (!response.ok) {
        alert(data.error || 'Could not save boundary');
        return;
    }

    window.location.reload();
}

initMap('start', 'startMap');
initMap('end', 'endMap');

loadBoundary('start');
loadBoundary('end');
</script>

@endsection