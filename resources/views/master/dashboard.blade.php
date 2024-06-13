@extends('layouts.admin')
@section('title')
{{ 'Dashboard' }}
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Dashboard</h4>



        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">

    </div>
</div>
<!-- end row -->

<div class="row">
    <div class="col-xl-4">
        <div class="card bg-primary bg-soft">
            <div>
                <div class="row">
                    <div class="col-7">
                        <div class="text-primary p-3">
                            <h5 class="text-primary">Welcome Back<br>

                                <strong>
                                    {{ Auth::user()->name}}
                                </strong>
                            </h5>
                            <p>Admin User Dashboard</p>


                        </div>
                    </div>
                    <div class="col-5 align-self-end">
                        <img src="assets/images/profile-img.png" alt="" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-8">
        <div class="row">
            <div class="col-sm-6">
                <a href="{{ route('users.index') }}">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-xs me-3">
                                    <span class="avatar-title rounded-circle bg-primary bg-soft text-primary font-size-18 adminCard">
                                        <i class="fas fa-users fa-2x"></i>
                                    </span>
                                </div>
                                <h5 class="font-size-14 mb-0">Users</h5>
                            </div>
                            <div class="text-muted mt-4">
                                <h4>{{ $users }}</h4>
                            </div>
                        </div>
                    </div>
                </a>

            </div>
            <div class="col-sm-6">
                <a href="{{ route('master_boats') }}">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-xs me-3">
                                    <span class="avatar-title rounded-circle bg-primary bg-soft text-primary font-size-18 adminCard">
                                        <i class="fas fa-ship fa-2x"></i>
                                    </span>
                                </div>
                                <h5 class="font-size-14 mb-0">Boats</h5>
                            </div>
                            <div class="text-muted mt-4">
                                <h4>{{ $boats }}</h4>
                            </div>
                        </div>
                    </div>
                </a>

            </div>
        </div>
        
    </div> 

</div>
<div class="w-100 pt-2">
    <h4 class="mb-sm-0 font-size-16">Recent Connections</h4>
    <div class="row justify-content-center">
        <div class="col-md-12 mt-2">
            <div class="card p-2 rounded cShadow table-responsive">
                <table id="masteruploadlog" class="table table-bordered  table-hover dt-responsive display nowrap">
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
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>

    
        var table = $('#masteruploadlog').DataTable({

        responsive: true,
        serverSide: true,
        searching: false,
        ajax: {
                url: '{{ route('master.conn') }}',
                type: 'GET',
                dataType: 'JSON',
                accepts: 'JSON',
                dom: 'frtip',
                data: function(d) {
                    d.page = (d.start / d.length) + 1;
                    
                },
                beforeSend: function() {},
                dataSrc: function(response) {
                return response.data;
                }
            },

        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'device_id',
                name: 'device_id',
            },
            {
                data: 'ip_address',
                name: 'ip_address',
            },
            {
                data: 'uload_time',
                name: 'uload_time',
            },   
{
                data: 'upload_status',
                name: 'upload_status',
            },        
{
                data: 'db_ok',
                name: 'db_ok',
            },        			

        ]
    });
    

</script>

@endsection