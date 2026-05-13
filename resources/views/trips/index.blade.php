@extends('layouts.app')

@section('content')

<style>
.trip-page { padding: 24px; }

.trip-card {
    background: #101827;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 18px;
    color: #e5e7eb;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
}

.trip-muted { color: #9ca3af; }

.trip-table {
    color: #e5e7eb;
    margin-bottom: 0;
}

.trip-table th {
    color: #9ca3af;
    font-weight: 500;
    border-color: rgba(255,255,255,0.08);
}

.trip-table td {
    border-color: rgba(255,255,255,0.06);
    vertical-align: middle;
}

.trip-table tr:hover {
    background: rgba(255,255,255,0.03);
}

.trip-badge {
    font-size: 0.8rem;
    border-radius: 999px;
    padding: 5px 10px;
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

.trip-form-control {
    background: #0b1220;
    color: #e5e7eb;
    border: 1px solid rgba(255,255,255,0.12);
}

.trip-form-control:focus {
    background: #0b1220;
    color: #e5e7eb;
    border-color: #3b82f6;
    box-shadow: none;
}

.trip-form-control option {
    color: #111827;
}
</style>

<div class="container-fluid trip-page">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Trips</h1>
            <div class="trip-muted">Detected and edited journeys</div>
        </div>

        <a href="{{ route('trip-settings.edit') }}" class="btn btn-soft">
            Detection Settings
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="GET" class="trip-card p-3 mb-3">
        <div class="row g-3 align-items-end">

            <div class="col-md-4">
                <label class="form-label trip-muted">Boat / MAC</label>
                <select name="mac" class="form-select trip-form-control">
                    <option value="">All boats</option>
                    @foreach($macs as $mac)
                        <option value="{{ $mac }}" @selected(request('mac') === $mac)>
                            {{ $mac }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label trip-muted">Status</label>
                <select name="status" class="form-select trip-form-control">
                    <option value="">All statuses</option>
                    @foreach(['auto', 'confirmed', 'edited', 'ignored'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-5">
                <button class="btn btn-primary">Filter</button>
                <a href="{{ route('trips.index') }}" class="btn btn-soft">Reset</a>
            </div>

        </div>
    </form>

    <div class="trip-card p-3">
        <div class="table-responsive">
            <table class="table trip-table align-middle">
                <thead>
                    <tr>
                        <th>Boat</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Duration</th>
                        <th>Distance</th>
                        <th>Status</th>
                        <th class="text-end"></th>
                    </tr>
                </thead>

                <tbody>
                @forelse($trips as $trip)

                    @php
                        $mins = (int) $trip->duration_minutes;
                        $hours = floor($mins / 60);
                        $remaining = $mins % 60;
                        $durationText = $mins < 60
                            ? $mins . ' mins'
                            : $hours . 'h ' . $remaining . 'm';
                    @endphp

                    <tr>
                        <td>
                            <div class="fw-bold">{{ $trip->mac }}</div>
                        </td>

                        <td>{{ optional($trip->start_time)->format('d/m/y H:i') }}</td>

                        <td>{{ optional($trip->end_time)->format('d/m/y H:i') }}</td>

                        <td>{{ $durationText }}</td>

                        <td>{{ number_format((float)$trip->distance_nm, 2) }} nm</td>

                        <td>
                            <span class="
                                badge trip-badge
                                @if(in_array($trip->status, ['confirmed', 'edited']))
                                    bg-success
                                @elseif($trip->status == 'ignored')
                                    bg-secondary
                                @else
                                    bg-warning text-dark
                                @endif
                            ">
                                {{ ucfirst($trip->status) }}
                            </span>
                        </td>

                        <td class="text-end">
                            <a href="{{ route('trips.show', $trip) }}" class="btn btn-sm btn-primary">
                                Open
                            </a>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 trip-muted">
                            No trips detected yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $trips->links() }}
    </div>

</div>

@endsection