@extends('layouts.admin')
@section('title', 'Boat Notes and Files')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Boat Notes and Files</h4>
                {{-- {{ $errors }} --}}
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Boat Notes and Files</li>
                    </ol>

                </div>
            </div>
        </div>
    </div>
    <h4 class="mb-sm-0 font-size-18">Boat Name: <label for=""
            class="badge badge-pill badge-soft-primary p-2">{{ $boat->boatname }}</label></h4>
    <div class="w-100">
        <div class="row justify-content-center">
            <div class="col-md-6 mt-4">
                <div class="card p-4 rounded cShadow table-responsive">
                    <table class="table table-bordered  table-hover dt-responsive display nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Note</th>
                                <th>Added By</th>
                                <th>Added At</th>
                                @if (Auth::user()->role_as == 3 )
                                                
                                @else
                                <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if ($notes)
                                @foreach ($notes as $note)
                                    <tr >
                                        <th scope="row">{{ $note->id }}</th>
                                        <td>{{ $note->note }}</td>
                                        <td >{{ $note->user->name }}</td>
                                        <td>{{ $note->created_at }}</td> 
                                        @if (Auth::user()->role_as == 3 )
                                                
                                        @else
                                        <td>
                                            <a class="btn btn-primary m-1 editNote" href="javascript:void(0)"
                                                data-id="{{ $note->id }}"><i class="fa fa-edit"></i> </a>
                                            <a class="btn btn-danger delNote m-1" href="javascript:void(0)"
                                                data-id="{{ $note->id }}"><i class="fa fa-trash"></i> </a>
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                No record found
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6 mt-4">
                <div class="card p-4 rounded cShadow table-responsive">
                    <table class="table table-bordered  table-hover dt-responsive display nowrap">
                        <thead>
                            <tr class="text-center">
                                <th>#</th>
                                <th>File</th>
                                <th>File Name</th>
                                <th>Added By</th>
                                <th>Added At</th>
                                @if (Auth::user()->role_as == 3 )
                                                
                                @else
                                <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if ($files)
                                @foreach ($files as $file)
                                    <tr class="text-center">
                                        <th scope="row">{{ $file->id }}</th>
                                        <td>
                                            <a href="/view/{{ $file->file }}" target="_blank">
                                                @if (in_array(pathinfo($file->file, PATHINFO_EXTENSION), ['pdf']))
                                                    <img style="border-radius: 40px" width="50" height="50" src="{{ asset('images/pdf_icon.png') }}" alt="PDF">
                                                @elseif (in_array(pathinfo($file->file, PATHINFO_EXTENSION), ['doc', 'docx']))
                                                    <img style="border-radius: 40px" width="50" height="50" src="{{ asset('images/word_icon.jpeg') }}" alt="Word">
                                                @elseif (in_array(pathinfo($file->file, PATHINFO_EXTENSION), ['xls', 'xlsx']))
                                                    <img style="border-radius: 40px" width="50" height="50" src="{{ asset('images/excel_icon.jpeg') }}" alt="Excel">
                                                @elseif (in_array(pathinfo($file->file, PATHINFO_EXTENSION), ['txt']))
                                                    <img style="border-radius: 40px" width="50" height="50" src="{{ asset('images/txt_icon.png') }}" alt="Text">
                                                @elseif (in_array(pathinfo($file->file, PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg', 'gif']))
                                                    <img style="border-radius: 40px" width="50" height="50" src="{{ asset($file->file) }}" alt="Image">
                                                @else
                                                    <img style="border-radius: 40px" width="50" height="50" src="{{ asset('images/default_icon.jpg') }}" alt="File">
                                                @endif
                                            </a>
                                        </td>
                                        <td>{{ $file->file_name }}</td>
                                        <td>{{ $file->user->name }}</td>
                                        <td>{{ $file->created_at }}</td>
                                        @if (Auth::user()->role_as == 3 )
                                                
                                        @else
                                        <td>
                                            <a class="btn btn-primary editFile m-1" href="javascript:void(0)"
                                                data-id="{{ $file->id }}"><i class="fa fa-edit"></i></a>
                                            <a class="btn btn-danger delFile m-1" href="javascript:void(0)"
                                                data-id="{{ $file->id }}"><i class="fa fa-trash"></i></a>
                                        </td>
                                        @endif

                                    </tr>
                                @endforeach
                            @else
                                No record found
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
    <!-- notes model -->
    <div style="top: 10%;" class="modal fade" id="noteModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading">Udate Note</h4>
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="noteBoatForm" name="noteBoatForm" class="form-horizontal">
                        <input type="hidden" name="note_id" id="note_id">
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Note</label>
                            <div class="col-sm-12">
                                <textarea class="form-control" name="note" id="boat-note" cols="30" rows="10"></textarea>
                                <span id="noteError" class="error"></span>
                            </div>
                        </div>
                        <div class="modal-footer py-1">
                            <button id="closeModalBtn" type="button" class="btn btn-outline-primary">Close</button>
                            <button type="submit" class="btn btn-primary button-spinner" id="notesaveBtn" value="create">Update</button>
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
                    <h4 class="modal-title" id="modelHeading">Update File</h4>
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="fileBoatForm" name="fileBoatForm" class="form-horizontal" enctype="multipart/form-data">
                        <input type="hidden" name="file_id" id="file_id">
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">File</label>
                            <div class="col-sm-12">
                                <img id="boat-file-view" src="" alt="" height="100">
                                <input class="form-control" type="file" name="file" id="boat-file">
                                <span id="fileError" class="error"></span>
                            </div>
                        </div>
                        <div class="modal-footer py-1">
                            <button id="closeModalBtn" type="button" class="btn btn-outline-primary">Close</button>
                            <button type="submit" class="btn btn-primary button-spinner" id="filesaveBtn" value="create">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>

    <script>

        $('body').on('click', '#closeModalBtn', function() {
            $('#noteModel').modal('hide');
            $('#fileModel').modal('hide');
        });
        $('.editNote').click(function(e) {
            e.preventDefault();

            var noteId = $(this).data('id');
            $('#boat-note').val('');
            $('#note_id').val('');
            $('#noteError').text('');
            $.ajax({
                url: '{{route('edit_note')}}',
                type: 'GET', 
                data: { id: noteId },
                success: function(response) {
                   
                    $('#boat-note').val(response.note);
                    $('#note_id').val(response.id);
                    $('#noteModel').modal('show');
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error('AJAX request failed');
                    console.error(status + ': ' + error);
                }
            });
        });
        $('.editFile').click(function(e) {
            e.preventDefault();

            var fileId = $(this).data('id');
            $('#boat-file-view').attr('src', '');
            $('#boat-file').val('');
            $('#file_id').val('');
            $('#fileError').text('');
            $.ajax({
                url: '{{route('edit_file')}}',
                type: 'GET', 
                data: { id: fileId },
                success: function(response) {
                    $('#boat-file-view').attr('src', '/view/'+ response.file);
                    // $('#boat-file').val(response.file);
                    $('#file_id').val(response.id);
                    $('#fileModel').modal('show');
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error('AJAX request failed');
                    console.error(status + ': ' + error);
                }
            });
        });
        $('body').on('click', '.delNote', function() {
            var note_id = $(this).data("id");
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success m-2",
                    cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "GET",
                        url: "/delete_note/" + note_id,
                        success: function(data) {
                            Toast.fire({
                                icon: 'success',
                                title: data.success
                            });
                            location.reload();
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // Handle cancellation if needed
                }
            });
        });
        $('body').on('click', '.delFile', function() {
            var note_id = $(this).data("id");
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success m-2",
                    cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "GET",
                        url: "/view/delete_file/" + note_id,
                        success: function(data) {
                            Toast.fire({
                                icon: 'success',
                                title: data.success
                            });
                            location.reload();
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // Handle cancellation if needed
                }
            });
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                        url: "{{route('edit_boat_note')}}",
                        type: "POST",
                        dataType: 'json',
                        success: function(data) {
                            $('#noteBoatForm').trigger("reset");
                            $('#noteModel').modal('hide');
                            location.reload();
                            Toast.fire({
                                icon: 'success',
                                title: data.success
                            });
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
                        url: "{{route('edit_boat_file')}}",
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
                            location.reload();
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
