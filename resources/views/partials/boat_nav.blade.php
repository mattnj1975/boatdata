@php
    $currentMac = $mac ?? request('mac') ?? 'A0B765616044';
@endphp

<nav class="mb-4">
    <div class="glass p-3 d-flex flex-wrap gap-2 align-items-center">

        <a class="btn btn-sm btn-outline-info fw-bold" href="{{ url('/') }}">
            Home
        </a>

        <a class="btn btn-sm btn-outline-info fw-bold" href="{{ url('/boat-stats/' . $currentMac) }}">
            Dashboard
        </a>

        <a class="btn btn-sm btn-outline-info fw-bold" href="{{ url('/boat-map/' . $currentMac) }}">
            Track Map
        </a>

        <a class="btn btn-sm btn-outline-info fw-bold" href="{{ url('/boat-raw/' . $currentMac . '/7d') }}">
            Raw Data
        </a>

        <a class="btn btn-sm btn-outline-info fw-bold" href="{{ url('/boat-insure/' . $currentMac) }}">
            Insurance
        </a>

        <a class="btn btn-sm btn-outline-info fw-bold" href="{{ url('/fleet-map') }}">
            Fleet Map
        </a>

    </div>
</nav>