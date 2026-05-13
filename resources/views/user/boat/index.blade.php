@extends('layouts.admin')

@section('title', 'Boats')

@section('css')
<style>
    .bd-page-header { margin-bottom:20px; }
    .bd-title { color:#f8fafc; font-size:24px; font-weight:800; margin:0; }
    .bd-subtitle { color:#94a3b8; font-size:13px; margin-top:4px; }

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

    #datatable { margin-bottom:0 !important; }

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

    .modal-content {
        background:#111827;
        color:#e5e7eb;
        border:1px solid rgba(148,163,184,.18);
        border-radius:18px;
    }

    .modal-header,
    .modal-footer {
        border-color:rgba(148,163,184,.14);
    }

    .modal-title {
        color:#f8fafc;
        font-weight:800;
    }

    .bd-label {
        color:#cbd5e1;
        font-size:13px;
        font-weight:700;
        margin-bottom:7px;
    }

    .bd-input {
        background:#0f172a !important;
        border:1px solid rgba(148,163,184,.22) !important;
        color:#f8fafc !important;
        border-radius:12px !important;
    }

    .bd-btn {
        background:#2563eb;
        color:#fff;
        border:0;
        border-radius:12px;
        padding:9px 16px;
        font-weight:800;
    }

    .bd-btn:hover {
        background:#3b82f6;
        color:#fff;
    }

    .error {
        color:#f87171;
        font-size:12px;
        margin-top:5px;
        display:block;
    }
</style>
@endsection

@section('content')

<div class="bd-page-header">
    <h1 class="bd-title">My Boats</h1>
    <div class="bd-subtitle">Assigned boats, last seen status and boat file/note tools.</div>
</div>

<div class="bd-card table-responsive">
    <table id="datatable" class="table table-hover dt-responsive display nowrap w-100">
        <thead>
            <tr>
                <th>Boat Name</th>
                <th>MAC</th>
                <th>Last Seen</th>
                <th>Assigned By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div style="top: 10%;" class="modal fade" id="noteModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Note</h4>
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="noteBoatForm" name="noteBoatForm" class="form-horizontal">
                    <input type="hidden" name="boat_id" id="note_boat_id">

                    <div class="form-group">
                        <label class="bd-label">Note</label>
                        <textarea class="form-control bd-input" name="note" id="boat-note" cols="30" rows="8"></textarea>
                        <span id="noteError" class="error"></span>
                    </div>

                    <div class="modal-footer px-0 pb-0">
                        <button id="closeModalBtn" type="button" class="btn btn-outline-primary">Close</button>
                        <button type="submit" class="btn bd-btn button-spinner" id="notesaveBtn" value="create">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div style="top: 10%;" class="modal fade" id="fileModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add File</h4>
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="fileBoatForm" name="fileBoatForm" class="form-horizontal" enctype="multipart/form-data">
                    <input type="hidden" name="boat_id" id="file_boat_id">

                    <div class="form-group">
                        <label class="bd-label">File</label>
                        <input class="form-control bd-input" type="file" name="file" id="boat-file">
                        <span id="fileError" class="error"></span>
                    </div>

                    <div class="modal-footer px-0 pb-0">
                        <button id="closeModalBtn" type="button" class="btn btn-outline-primary">Close</button>
                        <button type="submit" class="btn bd-btn button-spinner" id="filesaveBtn" value="create">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
        ajax: {
            url: '{{ route('user.userboats') }}',
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
            { data: 'boatname', name: 'boatname' },
            { data: 'mac', name: 'mac' },
            { data: 'lastseen', name: 'lastseen' },
            { data: 'assignee_user_name', name: 'assignee_user_name' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });

    $('body').on('click', '#closeModalBtn', function() {
        $('#ajaxModel').modal('hide');
        $('#noteModel').modal('hide');
        $('#fileModel').modal('hide');
        $('#userAjaxModel').modal('hide');
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
            title: "Delete boat?",
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
                    url: "/user/delete_boat/" + record_id,
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

    $('body').on('click', '.addNoteInBoat', function() {
        $('#noteError').text('');
        var boat_id = $(this).data('id');
        $('#note_boat_id').val(boat_id);
        $('#noteModel').modal('show');
    });

    $('body').on('click', '.addFileInBoat', function() {
        $('#fileError').text('');
        var boat_id = $(this).data('id');
        $('#file_boat_id').val(boat_id);
        $('#fileModel').modal('show');
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        var saveBtn = document.getElementById('notesaveBtn');

        saveBtn.addEventListener('click', function(e) {
            e.preventDefault();

            if (noteValidateForm()) {
                $.ajax({
                    data: $('#noteBoatForm').serialize(),
                    url: "{{ route('add_boat_note') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        $('#noteBoatForm').trigger("reset");
                        $('#noteModel').modal('hide');
                        Toast.fire({
                            icon: 'success',
                            title: data.success
                        });
                        table.draw();
                    }
                });
            }
        });

        function noteValidateForm() {
            var noteInput = document.getElementById('boat-note');
            var noteError = document.getElementById('noteError');

            noteError.textContent = '';

            if (noteInput.value.trim() === '') {
                noteError.textContent = 'Please write a note.';
                return false;
            }

            return true;
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('fileBoatForm');
        var saveBtn = document.getElementById('filesaveBtn');

        saveBtn.addEventListener('click', function(e) {
            e.preventDefault();

            if (fileValidateForm()) {
                var formData = new FormData(form);

                $.ajax({
                    data: formData,
                    url: "{{ route('add_boat_file') }}",
                    type: "POST",
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $('#fileBoatForm').trigger("reset");
                        $('#fileModel').modal('hide');
                        Toast.fire({
                            icon: 'success',
                            title: data.success
                        });
                        table.draw();
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }
        });

        function fileValidateForm() {
            var fileInput = document.getElementById('boat-file');
            var fileError = document.getElementById('fileError');

            fileError.textContent = '';

            if (fileInput.value.trim() === '') {
                fileError.textContent = 'Please select file.';
                return false;
            }

            return true;
        }
    });
</script>

@endsection