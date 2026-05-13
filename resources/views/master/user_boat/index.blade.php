@extends('layouts.admin')

@section('title', 'Boats')

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

    .bd-table-card {
        background: #111827;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 18px;
        padding: 12px;
        box-shadow: 0 16px 40px rgba(0,0,0,.22);
    }

    #datatable,
    #datatable tbody td,
    #datatable tbody td a,
    #datatable_wrapper,
    #datatable_wrapper label,
    #datatable_wrapper .dataTables_info,
    #datatable_wrapper .dataTables_paginate {
        color: #e5e7eb !important;
    }

    #datatable {
        margin-bottom: 0 !important;
    }

    #datatable thead th {
        background: #0f172a;
        color: #94a3b8 !important;
        border-color: rgba(148, 163, 184, 0.18);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: 9px 10px;
    }

    #datatable tbody td {
        border-color: rgba(148, 163, 184, 0.12);
        padding: 7px 10px;
        font-size: 13px;
        vertical-align: middle;
    }

    table.dataTable.display tbody tr.odd,
    table.dataTable.stripe tbody tr.odd {
        background-color: #111827 !important;
    }

    table.dataTable.display tbody tr.even,
    #datatable tbody tr {
        background-color: #0f172a !important;
    }

    #datatable tbody tr:hover {
        background: rgba(59,130,246,.12) !important;
    }

    .dataTables_wrapper .form-select,
    .dataTables_wrapper .form-control {
        background-color: #0f172a;
        color: #e5e7eb;
        border-color: rgba(148, 163, 184, .25);
    }

    .page-link {
        background-color: #0f172a;
        border-color: rgba(148, 163, 184, .2);
        color: #cbd5e1;
    }

    .page-item.active .page-link {
        background-color: #2563eb;
        border-color: #2563eb;
    }

    .page-link:hover {
        background-color: #1e293b;
        color: #fff;
    }

    .bd-table-card .btn {
        border-radius: 10px;
        font-size: 12px;
        padding: 5px 9px;
    }
</style>

<div class="bd-page-header">
    <h1 class="bd-title">Boats</h1>
    <div class="bd-subtitle">
        Manage boats assigned to users.
    </div>
</div>

<div class="bd-table-card table-responsive">
    <table id="datatable" class="table table-hover dt-responsive display nowrap w-100">
        <thead>
            <tr>
                <th>User Name</th>
                <th>Boat Name</th>
                <th>MAC</th>
                <th>Assigned By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" defer></script>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    var table = $('#datatable').DataTable({
        responsive: true,
        serverSide: true,
        searching: false,
        pageLength: 10,
        lengthChange: false,
        order: [[1, 'asc']],
        ajax: {
            url: '{{ route('master.userboats') }}',
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
            { data: 'user_name', name: 'user_name' },
            { data: 'boatname', name: 'boatname' },
            { data: 'mac', name: 'mac' },
            { data: 'assignee_user_name', name: 'assignee_user_name' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });

    $('body').on('click', '.deleteUserBoat', function() {
        var record_id = $(this).data("id");

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success m-2",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            title: "Delete boat assignment?",
            text: "This cannot be undone.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it",
            cancelButtonText: "Cancel",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "GET",
                    url: "/view/master/delete_user_boat/" + record_id,
                    success: function(data) {
                        Toast.fire({
                            icon: 'success',
                            title: data.success
                        });
                        table.draw();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            }
        });
    });
</script>

@endsection