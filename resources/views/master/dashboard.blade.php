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
        border: 1px solid rgba(148,163,184,.18);
        border-radius: 18px;
        box-shadow: 0 16px 40px rgba(0,0,0,.22);
        color: #e5e7eb;
        overflow: hidden;
    }

    .bd-card-body {
        padding: 20px;
    }

    .bd-welcome {
        min-height: 150px;
        background:
            radial-gradient(circle at top right, rgba(59,130,246,.32), transparent 36%),
            linear-gradient(135deg, #111827, #0f172a);
    }

    .bd-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(59,130,246,.16);
        color: #93c5fd;
        font-size: 12px;
        font-weight: 700;
    }

    .bd-welcome-name {
        color: #fff;
        font-size: 24px;
        font-weight: 800;
        margin-top: 18px;
    }

    .bd-stat-link {
        text-decoration: none;
        display: block;
        height: 100%;
    }

    .bd-stat-card {
        height: 100%;
        transition: .15s ease;
    }

    .bd-stat-card:hover {
        transform: translateY(-2px);
        border-color: rgba(59,130,246,.55);
        background: #162033;
    }

    .bd-stat-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }

    .bd-stat-label {
        color: #94a3b8;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .bd-stat-value {
        color: #fff;
        font-size: 34px;
        font-weight: 800;
        line-height: 1;
    }

    .bd-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: rgba(59,130,246,.16);
        color: #60a5fa;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .bd-section-title {
        color: #f8fafc;
        font-size: 17px;
        font-weight: 800;
        margin: 24px 0 10px;
    }

    .bd-table-card {
        background: #111827;
        border: 1px solid rgba(148,163,184,.18);
        border-radius: 18px;
        padding: 12px;
        box-shadow: 0 16px 40px rgba(0,0,0,.22);
    }

    #masteruploadlog,
    #masteruploadlog tbody td,
    #masteruploadlog tbody td a,
    #masteruploadlog_wrapper,
    #masteruploadlog_wrapper label,
    #masteruploadlog_wrapper .dataTables_info,
    #masteruploadlog_wrapper .dataTables_paginate {
        color: #e5e7eb !important;
    }

    #masteruploadlog {
        margin-bottom: 0 !important;
    }

    #masteruploadlog thead th {
        background: #0f172a !important;
        color: #94a3b8 !important;
        border-color: rgba(148,163,184,.18) !important;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: 9px 10px;
    }

    #masteruploadlog tbody td {
        border-color: rgba(148,163,184,.12) !important;
        padding: 7px 10px !important;
        font-size: 13px;
        vertical-align: middle;
    }

    table.dataTable.display tbody tr.odd,
    table.dataTable.stripe tbody tr.odd {
        background-color: #111827 !important;
    }

    table.dataTable.display tbody tr.even,
    #masteruploadlog tbody tr {
        background-color: #0f172a !important;
    }

    #masteruploadlog tbody tr:hover {
        background: rgba(59,130,246,.12) !important;
    }

    .page-link {
        background: #0f172a;
        border-color: rgba(148,163,184,.2);
        color: #cbd5e1;
    }

    .page-item.active .page-link {
        background: #2563eb;
        border-color: #2563eb;
    }

    @media (max-width: 767px) {
        .bd-page-header {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>

<div class="bd-page-header">
    <h1 class="bd-title">Master Dashboard</h1>
    <div class="bd-subtitle">Users, boats and recent device upload activity.</div>
</div>

<div class="row g-3">
    <div class="col-xl-4 col-lg-5">
        <div class="bd-card bd-welcome h-100">
            <div class="bd-card-body">
                <span class="bd-pill">
                    <i class="fas fa-user-shield"></i>
                    Master User
                </span>

                <div class="bd-welcome-name">
                    Welcome back,<br>
                    {{ Auth::user()->name }}
                </div>

                <div class="bd-subtitle">Dashboard overview</div>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-7">
        <div class="row g-3 h-100">
            <div class="col-sm-6">
                <a href="{{ route('users.index') }}" class="bd-stat-link">
                    <div class="bd-card bd-stat-card">
                        <div class="bd-card-body">
                            <div class="bd-stat-top">
                                <div class="bd-stat-label">Users</div>
                                <div class="bd-stat-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>

                            <div class="bd-stat-value">{{ $users }}</div>
                            <div class="bd-subtitle mt-2">Managed users</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-sm-6">
                <a href="{{ route('master_boats') }}" class="bd-stat-link">
                    <div class="bd-card bd-stat-card">
                        <div class="bd-card-body">
                            <div class="bd-stat-top">
                                <div class="bd-stat-label">Boats</div>
                                <div class="bd-stat-icon">
                                    <i class="fas fa-ship"></i>
                                </div>
                            </div>

                            <div class="bd-stat-value">{{ $boats }}</div>
                            <div class="bd-subtitle mt-2">Assigned boats</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<h2 class="bd-section-title">Recent Connections</h2>

<div class="bd-table-card table-responsive">
    <table id="masteruploadlog" class="table table-hover dt-responsive display nowrap w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Device ID</th>
                <th>IP</th>
                <th>Time</th>
                <th>Status</th>
                <th>Rows+</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    var table = $('#masteruploadlog').DataTable({
        responsive: true,
        serverSide: true,
        searching: false,
        pageLength: 10,
        lengthChange: false,
        ajax: {
            url: '{{ route('master.conn') }}',
            type: 'GET',
            dataType: 'JSON',
            accepts: 'JSON',
            data: function(d) {
                d.page = (d.start / d.length) + 1;
            },
            dataSrc: function(response) {
                return response.data;
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'device_id', name: 'device_id' },
            { data: 'ip_address', name: 'ip_address' },
            { data: 'uload_time', name: 'uload_time' },
            { data: 'upload_status', name: 'upload_status' },
            { data: 'db_ok', name: 'db_ok' }
        ]
    });
</script>

@endsection