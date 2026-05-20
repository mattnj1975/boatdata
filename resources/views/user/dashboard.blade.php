@extends('layouts.admin')

@section('title')
{{ 'Dashboard' }}
@endsection

@section('content')
<div class="bd-page-header">
    <h1 class="bd-title">Dashboard</h1>
    <div class="bd-subtitle">
        Boat telemetry platform overview.
    </div>
</div>

<div class="row">
    <div class="col-xl-8">

        <div class="bd-card bd-welcome">
            <div class="bd-card-body">

                <span class="bd-pill">
                    <i class="fas fa-user"></i>
                    User Dashboard
                </span>

                <div class="bd-name">
                    Welcome back,<br>
                    {{ Auth::user()->name }}
                </div>

                <div class="bd-desc">
                    Access your boats, trips, maps and telemetry data from the navigation menu.
                </div>

                <div class="bd-icon">
                    <i class="fas fa-ship"></i>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection