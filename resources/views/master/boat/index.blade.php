@extends('layouts.admin')
@section('title', 'Boat')
@section('css')

<style>

</style>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Boats</h4>
            {{-- {{ $errors }}--}}
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Boats</li>
                </ol>
                
            </div>
        </div>
    </div>
</div>

<div class="card">
  
    <div class="card-body">
        
            <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email">Search By MAC :</label>
                    <input type="text" name="filter_mac" class="form-control" id="filter_mac"  placeholder="Search by MAC" />
                </div>
            </div>
            
            <div class="col-md-6">
                     <button type="button" class="btn btn-lg btn-primary mt-4 float-right button-spinner search_btn">
                    <i class="fa fa-search"></i> Search
                </button>
            </div>
            </div>
    
    </div>
</div>

<div class="w-100">
    <div class="row justify-content-center">
        <div class="col-md-12 mt-4">
            <div class="card p-4 rounded cShadow table-responsive">
                <table id="datatable" class="table table-bordered  table-hover dt-responsive display nowrap">
                    <thead>
                        <tr>
                            <th>Boat Name</th>
                            <th>MAC</th>
                            <th>Public</th>
                            <!-- <th>Default Interval</th> -->
                            <th>ID</th>
                            <th>Last Seen</th>
                            <th>Ver</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Model -->
<div style="top: 20%;" class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading">+User</h4>
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="boatForm" name="boatForm" class="form-horizontal">
                    <input type="hidden" name="boat_id" id="boat_id">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Users</label>
                        <div class="col-sm-12">
                            <select id="select-users" class="form-select" multiple="multiple" name="users[]" style="width: 100%"></select>
                            <span id="groupsError" class="error"></span>
                        </div>
                    </div>


                    <div class="modal-footer py-1">
                        <button id="closeModalBtn" type="button" class="btn btn-outline-primary">Close</button>
                        <button type="submit" class="btn btn-primary button-spinner" id="saveBtn" value="create">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- notes model -->
<div style="top: 10%;" class="modal fade" id="noteModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading">Add Note</h4>
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="noteBoatForm" name="noteBoatForm" class="form-horizontal">
                    <input type="hidden" name="boat_id" id="note_boat_id">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Note</label>
                        <div class="col-sm-12">
                            <textarea class="form-control" name="note" id="boat-note" cols="30" rows="10"></textarea>
                            <span id="noteError" class="error"></span>
                        </div>
                    </div>
                    <div class="modal-footer py-1">
                        <button id="closeModalBtn" type="button" class="btn btn-outline-primary">Close</button>
                        <button type="submit" class="btn btn-primary button-spinner" id="notesaveBtn" value="create">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- file model -->
<div style="top: 10%;" class="modal fade" id="fileModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading">Add File</h4>
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="fileBoatForm" name="fileBoatForm" class="form-horizontal" enctype="multipart/form-data">
                    <input type="hidden" name="boat_id" id="file_boat_id">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">File</label>
                        <div class="col-sm-12">
                            <input class="form-control" type="file" name="file" id="boat-file">
                            <span id="fileError" class="error"></span>
                        </div>
                    </div>
                    <div class="modal-footer py-1">
                        <button id="closeModalBtn" type="button" class="btn btn-outline-primary">Close</button>
                        <button type="submit" class="btn btn-primary button-spinner" id="filesaveBtn" value="create">Submit</button>
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
    var table = $('#datatable').DataTable({

        responsive: true,
        serverSide: true,
        searching: false,
        ajax: {
                url: '{{ route('master_boats') }}',
                type: 'GET',
                dataType: 'JSON',
                accepts: 'JSON',
                dom: 'frtip',
                data: function(d) {
                    d.filter_mac = $('input[name=filter_mac]').val();                    
                    d.page = (d.start / d.length) + 1;
                    
                },
                beforeSend: function() {},
                dataSrc: function(response) {
                return response.data;
                }
            },

        columns: [{
                data: 'boatname',
                name: 'boatname'
            },
            {
                data: 'mac',
                name: 'mac',
            },
            {
                data: 'is_public',
                name: 'is_public',
            },
            // {
            //     data: 'default_interval',
            //     name: 'default_interval',
            // },
            {
                data: 'device_id',
                name: 'device_id',
            },
            {
                data: 'lastseen',
                name: 'lastseen',
                render: function(data, type, row, meta) {
                    return data ? data : 'N/A';
                }
            },
            {
                data: 'version',
                name: 'version',
                render: function(data, type, row, meta) {
                    return data ? data : 'N/A';
                }
            },
            {
                data: 'actions',
                name: 'actions'
            },
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
            $.get("{{ route('get_users') }}", function(data) {
                var users = data.map(function(item) {
                    return {
                        id: item.id,
                        text: item.name
                    };
                })
                var data = users;
                var placeholder = "Select Users";
                $('#select-users').select2({
                    dropdownParent: $("#ajaxModel"),
                    data: data,
                    placeholder: placeholder,
                    allowClear: false,
                    maximumSelectionLength: 20,
                    minimumResultsForSearch: 5
                })
            })
            var boat_id = $(this).data('id');
            
            $.get("get_assigned_user" + '/' + boat_id, function(data) {
                var selectedUserIds = data.map(function(user) {
                    return user.user_id;
                });
                // Set selected values in the select2 dropdown
                $('#select-users').val(selectedUserIds).trigger('change');
            })
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
        var form = document.getElementById('boatForm');
        var saveBtn = document.getElementById('saveBtn');

        saveBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Check if the form is valid
            if (validateForm()) {

                $.ajax({
                    data: $('#boatForm').serialize(),
                    url: "{{route('assign_boat_to_user')}}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        $('#boatForm').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        Toast.fire({
                            icon: 'success',
                            title: data.success
                        });
                        table.draw();
                    },
                });
            }
        });

    
        function validateForm() {
            var groupsInput = document.getElementById('select-users');
            var groupsError = document.getElementById('groupsError');

            // Reset error messages
            groupsError.textContent = '';
            
            // Validate groups
            if (groupsInput.value.trim() === '') {
                groupsError.textContent = 'Please Select User';
                return false;
            }

            return true;
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('noteBoatForm');
        var saveBtn = document.getElementById('notesaveBtn');

        saveBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Check if the form is valid
            if (noteValidateForm()) {

                $.ajax({
                    data: $('#noteBoatForm').serialize(),
                    url: "{{route('add_boat_note')}}",
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
                    },
                });
            }
        });

    
        function noteValidateForm() {
            var noteInput = document.getElementById('boat-note');
            var noteError = document.getElementById('noteError');

            // Reset error messages
            noteError.textContent = '';
            
            if (noteInput.value.trim() === '') {
                noteError.textContent = 'Please Write a Note.';
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
                    url: "{{route('add_boat_file')}}",
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
                    error: function(xhr, status, error) {
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
                fileError.textContent = 'Please Select File.';
                return false;
            }

            return true;
        }
    });
</script>



@endsection