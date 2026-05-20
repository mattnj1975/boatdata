@extends('layouts.admin')

@section('title', 'Boats')

@section('content')

<div class="bd-page-header">
    <h1 class="bd-title">Boats</h1>
    <div class="bd-subtitle">Search, assign users, add notes and manage boat files.</div>
</div>

<div class="bd-card mb-3">
    <div class="row align-items-end g-3">
        <div class="col-md-6">
            <label class="bd-label">Search by MAC</label>
            <input type="text" name="filter_mac" class="form-control bd-input" id="filter_mac" placeholder="Search by MAC">
        </div>

        <div class="col-md-6 text-md-end">
            <button type="button" class="btn bd-btn search_btn">
                <i class="fa fa-search me-1"></i> Search
            </button>
        </div>
    </div>
</div>

<div class="bd-card table-responsive">
    <table id="datatable" class="table table-hover dt-responsive display nowrap w-100">
        <thead>
            <tr>
                <th>Boat Name</th>
                <th>MAC</th>
                <th>Public</th>
                <th>ID</th>
                <th>Last Seen</th>
                <th>Ver</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div style="top: 20%;" class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Assign User</h4>
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="boatForm" name="boatForm" class="form-horizontal">
                    <input type="hidden" name="boat_id" id="boat_id">

                    <div class="form-group">
                        <label class="bd-label">Users</label>
                        <select id="select-users" class="form-select" multiple="multiple" name="users[]" style="width:100%"></select>
                        <span id="groupsError" class="error"></span>
                    </div>

                    <div class="modal-footer px-0 pb-0">
                        <button id="closeModalBtn" type="button" class="btn btn-outline-primary">Close</button>
                        <button type="submit" class="btn bd-btn button-spinner" id="saveBtn" value="create">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
    $('.search_btn').on('click', function() {
        table.draw();
    });

    $('#filter_mac').on('keypress', function(e) {
        if (e.which === 13) {
            table.draw();
        }
    });

    var table = $('#datatable').DataTable({
        responsive: true,
        serverSide: true,
        searching: false,
        pageLength: 10,
        lengthChange: false,
        ajax: {
            url: '{{ route('master_boats') }}',
            type: 'GET',
            dataType: 'JSON',
            accepts: 'JSON',
            data: function(d) {
                d.filter_mac = $('input[name=filter_mac]').val();
                d.page = (d.start / d.length) + 1;
            },
            dataSrc: function(response) {
                return response.data;
            }
        },
        columns: [
            { data: 'boatname', name: 'boatname' },
            { data: 'mac', name: 'mac' },
            { data: 'is_public', name: 'is_public' },
            { data: 'device_id', name: 'device_id' },
            {
                data: 'lastseen',
                name: 'lastseen',
                render: function(data) {
                    return data ? data : 'N/A';
                }
            },
            {
                data: 'version',
                name: 'version',
                render: function(data) {
                    return data ? data : 'N/A';
                }
            },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });

    $('body').on('click', '#closeModalBtn', function() {
        $('#ajaxModel').modal('hide');
        $('#noteModel').modal('hide');
        $('#fileModel').modal('hide');
    });

    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('body').on('click', '.assignBoat', function() {
            $('#groupsError').text('');
            $('#select-users').empty();

            $.get("{{ route('get_users') }}", function(data) {
                var users = data.map(function(item) {
                    return {
                        id: item.id,
                        text: item.name
                    };
                });

                $('#select-users').select2({
                    dropdownParent: $("#ajaxModel"),
                    data: users,
                    placeholder: "Select Users",
                    allowClear: false,
                    maximumSelectionLength: 20,
                    minimumResultsForSearch: 5
                });
            });

            var boat_id = $(this).data('id');

            $.get("get_assigned_user" + '/' + boat_id, function(data) {
                var selectedUserIds = data.map(function(user) {
                    return user.user_id;
                });

                $('#select-users').val(selectedUserIds).trigger('change');
            });

            $('#boat_id').val(boat_id);
            $('#ajaxModel').modal('show');
        });
    });

    $('body').on('click', '.addNoteInBoat', function() {
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

    document.addEventListener('DOMContentLoaded', function() {
        var saveBtn = document.getElementById('saveBtn');

        saveBtn.addEventListener('click', function(e) {
            e.preventDefault();

            if (validateForm()) {
                $.ajax({
                    data: $('#boatForm').serialize(),
                    url: "{{ route('assign_boat_to_user') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        $('#boatForm').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        Toast.fire({ icon: 'success', title: data.success });
                        table.draw();
                    }
                });
            }
        });

        function validateForm() {
            var groupsInput = document.getElementById('select-users');
            var groupsError = document.getElementById('groupsError');

            groupsError.textContent = '';

            if (groupsInput.value.trim() === '') {
                groupsError.textContent = 'Please select a user.';
                return false;
            }

            return true;
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
                        Toast.fire({ icon: 'success', title: data.success });
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
                        Toast.fire({ icon: 'success', title: data.success });
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
                fileError.textContent = 'Please select a file.';
                return false;
            }

            return true;
        }
    });
</script>

@endsection