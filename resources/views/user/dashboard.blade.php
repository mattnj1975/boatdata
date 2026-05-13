@extends('layouts.admin')

@section('title')
{{ 'Dashboard' }}
@endsection

@section('content')

<style>
    .bd-page-header {
        margin-bottom: 20px;
    }

    .bd-title {
        color: #f8fafc;
        font-size: 24px;
        font-weight: 800;
        margin: 0;
    }

    .bd-subtitle {
        color: #94a3b8;
        font-size: 13px;
        margin-top: 4px;
    }

    .bd-card {
        background: #111827;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 18px;
        box-shadow: 0 16px 40px rgba(0,0,0,.22);
        overflow: hidden;
    }

    .bd-card-body {
        padding: 24px;
    }

    .bd-welcome {
        min-height: 220px;
        background:
            radial-gradient(circle at top right, rgba(59,130,246,.32), transparent 36%),
            linear-gradient(135deg, #111827, #0f172a);
        position: relative;
    }

    .bd-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(59,130,246,.16);
        color: #93c5fd;
        font-size: 12px;
        font-weight: 700;
    }

    .bd-name {
        color: #ffffff;
        font-size: 32px;
        font-weight: 800;
        line-height: 1.1;
        margin-top: 18px;
        margin-bottom: 10px;
    }

    .bd-desc {
        color: #94a3b8;
        font-size: 15px;
        max-width: 500px;
    }

    .bd-icon {
        position: absolute;
        right: 25px;
        bottom: 20px;
        font-size: 90px;
        color: rgba(59,130,246,.12);
    }

    @media (max-width: 768px) {
        .bd-name {
            font-size: 26px;
        }

        .bd-icon {
            font-size: 70px;
        }
    }
</style>

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