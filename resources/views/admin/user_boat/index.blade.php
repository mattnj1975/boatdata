@extends('layouts.admin')
@section('title', 'Boat')
@section('css')

<style>

</style>
@endsection
@section('content')
<div class="bd-page-header">
    <div>
        <h1 class="bd-title">User Boats</h1>
        <div class="bd-subtitle">
            Manage user boat assignments and access.
        </div>
    </div>
</div>

<div class="w-100">
    <div class="row justify-content-center">
        <div class="col-md-12 mt-4">
            <div class="bd-card bd-table-card table-responsive">
                <table id="datatable" class="table table-bordered  table-hover dt-responsive display nowrap">
                    <thead>
                        <tr>
                            <th>LastSeen</th>
                            <th>Boat Name</th>
                            <th>MAC</th>
                            <th>Assigned By</th>
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


<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" defer></script>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>

    var table = $('#datatable').DataTable({

        responsive: true,
        serverSide: true,
        searching: false,
        ajax: {
                url: '{{ route('admin.userboats') }}',
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

        columns: [
            {
                data: 'user_name',
                name: 'user_name'
            },
            {
                data: 'boatname',
                name: 'boatname'
            },
            {
                data: 'mac',
                name: 'mac',
            },
            {
                data: 'assignee_user_name',
                name: 'assignee_user_name',
            },         
            {
                data: 'actions',
                name: 'actions'
            },
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
                    url: "/view/delete_user_boat/" + record_id,
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
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                // Handle cancellation if needed
            }
        });
    });
   
    
</script>



@endsection