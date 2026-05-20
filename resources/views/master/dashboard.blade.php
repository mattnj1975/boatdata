@extends('layouts.admin')

@section('title')
{{ 'Dashboard' }}
@endsection

@section('content')
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