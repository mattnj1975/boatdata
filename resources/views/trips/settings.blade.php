@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Trip Detection Settings</h1>
            <p class="text-muted mb-0">Controls how automatic journeys are detected from boatdata.</p>
        </div>

        <a href="{{ route('trips.index') }}" class="btn btn-outline-secondary">Back to Trips</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('trip-settings.update') }}">
        @csrf
        @method('PUT')

        <div class="row g-3">

            @include('trips.settings-field', [
                'name' => 'min_sog',
                'label' => 'Minimum SOG',
                'value' => $config->min_sog,
                'suffix' => 'knots',
                'help' => 'GPS speed-over-ground needed before a record counts as moving. Lower catches slow sailing; higher avoids marina/GPS jitter.'
            ])

            @include('trips.settings-field', [
                'name' => 'min_spd',
                'label' => 'Minimum SPD',
                'value' => $config->min_spd,
                'suffix' => 'knots',
                'help' => 'Boat speed through water. Useful if SOG is missing or affected by tide. If no paddlewheel/log data exists, this may often be NULL.'
            ])

            @include('trips.settings-field', [
                'name' => 'min_moving_minutes',
                'label' => 'Minimum Moving Time',
                'value' => $config->min_moving_minutes,
                'suffix' => 'minutes',
                'help' => 'Boat must keep moving this long before a trip is confirmed. Prevents short false trips caused by GPS jumps or tiny movements.'
            ])

            @include('trips.settings-field', [
                'name' => 'min_stopped_minutes',
                'label' => 'Minimum Stopped Time',
                'value' => $config->min_stopped_minutes,
                'suffix' => 'minutes',
                'help' => 'Boat must remain below movement thresholds this long before the trip ends. Increase this if fuel stops or short pauses split one trip into several.'
            ])

            @include('trips.settings-field', [
                'name' => 'start_rewind_minutes',
                'label' => 'Start Rewind',
                'value' => $config->start_rewind_minutes,
                'suffix' => 'minutes',
                'help' => 'Once movement is confirmed, the stored start is moved backwards by this amount to catch marina manoeuvring and the real departure.'
            ])

            @include('trips.settings-field', [
                'name' => 'end_extend_minutes',
                'label' => 'End Extend',
                'value' => $config->end_extend_minutes,
                'suffix' => 'minutes',
                'help' => 'Extends the trip end slightly after the first stopped period to catch final docking or slow arrival movement.'
            ])

            @include('trips.settings-field', [
                'name' => 'max_gap_minutes',
                'label' => 'Maximum Data Gap',
                'value' => $config->max_gap_minutes,
                'suffix' => 'minutes',
                'help' => 'If there is no data for longer than this during a trip, the trip is split/ended. Increase for poor mobile signal areas.'
            ])

            @include('trips.settings-field', [
                'name' => 'min_rpm',
                'label' => 'Minimum RPM',
                'value' => $config->min_rpm,
                'suffix' => 'rpm',
                'help' => 'If engine RPM detection is enabled, this RPM or higher can count as movement. Useful for slow manoeuvring where speed is missing.'
            ])

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <label class="form-label fw-bold">Use Engine RPM</label>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="use_engine_rpm" value="1" class="form-check-input"
                                   @checked($config->use_engine_rpm)>
                            <label class="form-check-label">
                                Allow RPM to count as movement
                            </label>
                        </div>
                        <p class="text-muted mt-2 mb-0">
                            Useful if SOG/SPD are unreliable, but can create false trips if the engine is run while stationary.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <label class="form-label fw-bold">Enabled</label>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="enabled" value="1" class="form-check-input"
                                   @checked($config->enabled)>
                            <label class="form-check-label">
                                Enable automatic trip detection
                            </label>
                        </div>
                        <p class="text-muted mt-2 mb-0">
                            If disabled, the detector command will not use this config.
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-4">
            <button class="btn btn-primary">Save Settings</button>
        </div>
    </form>

</div>
@endsection