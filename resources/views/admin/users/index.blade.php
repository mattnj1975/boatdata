@extends('layouts.admin')

@section('title', 'Users')

@section('content')

<style>
    .bd-page-header {
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        margin-bottom:20px;
    }

    .bd-title {
        color:#f8fafc;
        font-size:24px;
        font-weight:800;
        margin:0;
    }

    .bd-subtitle {
        color:#94a3b8;
        font-size:13px;
        margin-top:4px;
    }

    .bd-add-btn {
        background:linear-gradient(135deg,#2563eb,#3b82f6);
        border:0;
        border-radius:12px;
        color:#fff;
        padding:11px 18px;
        font-weight:800;
        text-decoration:none;
    }

    .bd-add-btn:hover {
        color:#fff;
        transform:translateY(-1px);
        box-shadow:0 10px 22px rgba(37,99,235,.25);
    }

    .bd-card {
        background:#111827;
        border:1px solid rgba(148,163,184,.18);
        border-radius:18px;
        box-shadow:0 16px 40px rgba(0,0,0,.22);
        padding:12px;
        color:#e5e7eb;
    }

    #datatable,
    #datatable tbody td,
    #datatable tbody td a,
    #datatable_wrapper,
    #datatable_wrapper label,
    #datatable_wrapper .dataTables_info,
    #datatable_wrapper .dataTables_paginate {
        color:#e5e7eb !important;
    }

    #datatable {
        margin-bottom:0 !important;
    }

    #datatable thead th {
        background:#0f172a !important;
        color:#94a3b8 !important;
        border-color:rgba(148,163,184,.18) !important;
        font-size:12px;
        text-transform:uppercase;
        letter-spacing:.04em;
        padding:9px 10px;
    }

    #datatable tbody td {
        border-color:rgba(148,163,184,.12) !important;
        padding:7px 10px !important;
        font-size:13px;
        vertical-align:middle;
    }

    table.dataTable.display tbody tr.odd,
    table.dataTable.stripe tbody tr.odd {
        background-color:#111827 !important;
    }

    table.dataTable.display tbody tr.even,
    #datatable tbody tr {
        background-color:#0f172a !important;
    }

    #datatable tbody tr:hover {
        background:rgba(59,130,246,.12) !important;
    }

    .page-link {
        background:#0f172a;
        border-color:rgba(148,163,184,.2);
        color:#cbd5e1;
    }

    .page-item.active .page-link {
        background:#2563eb;
        border-color:#2563eb;
    }

    .page-link:hover {
        background:#1e293b;
        color:#fff;
    }

    .bd-card .btn {
        border-radius:10px;
        font-size:12px;
        padding:5px 9px;
    }

    @media (max-width: 768px) {
        .bd-page-header {
            align-items:flex-start;
            flex-direction:column;
        }
    }
</style>

<div class="bd-page-header">
    <div>
        <h1 class="bd-title">Users</h1>
        <div class="bd-subtitle">Manage platform user accounts and access status.</div>
    </div>

    <a href="{{ route('users.create') }}" class="btn bd-add-btn">
        <i class="fas fa-plus me-1"></i>
        Add New
    </a>
</div>

<div class="bd-card table-responsive">
    <table id="datatable" class="table table-hover dt-responsive display nowrap w-100">
        <thead>
            <tr>
                <th>User Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    var table = $('#datatable').DataTable({
        serverSide: true,
        searching: false,
        pageLength: 10,
        lengthChange: false,
        ajax: '{{ route('users.index') }}',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'status', name: 'status' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });

    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('body').on('click', '.deleteUser', function() {
            var user_id = $(this).data("id");

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success m-2",
                    cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "Delete user?",
                text: "This cannot be undone.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it",
                cancelButtonText: "Cancel",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('users.store') }}" + '/' + user_id,
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
    });
</script>

@endsection