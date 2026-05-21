@php
    $currentMac = $mac ?? request('mac') ?? 'A0B765616044';
@endphp

<nav class="mb-4">
    <div class="glass p-3 d-flex flex-wrap gap-2 align-items-center">

        <a class="btn btn-sm btn-outline-info fw-bold" href="{{ url('/boat-stats/' . $currentMac) }}">
            Dashboard
        </a>
		<a class="btn btn-sm btn-outline-info fw-bold" href="{{ url('/?mac=' . $currentMac) }}">
            View Trips
        </a>

        <a class="btn btn-sm btn-outline-info fw-bold" href="{{ url('/boat-map/' . $currentMac) }}">
            Track Map
        </a>

        <a class="btn btn-sm btn-outline-info fw-bold" href="{{ url('/boat-insure/' . $currentMac) }}">
            Insurance
        </a>

        <a class="btn btn-sm btn-outline-info fw-bold" href="{{ url('/boat-ais/' . $currentMac) }}">
            AIS Traffic
        </a>
		<a class="btn btn-sm btn-outline-info fw-bold" href="{{ url('/boat-raw/' . $currentMac . '/7d') }}">
            Raw Data
        </a>

    </div>
</nav>