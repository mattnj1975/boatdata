<!-- JAVASCRIPT -->


<!-- <script src="{{ URL::asset('assets/libs/jquery/jquery.min.js')}}"></script> -->
<script src="{{ URL::asset('assets/libs/bootstrap/bootstrap.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/metismenu/metismenu.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/node-waves/node-waves.min.js')}}"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{env('MAP_API_KEY')}}&libraries=places"></script>
<script>
    $(window).on('load', function() {
        $('#exampleModal').modal('show');
    });
    $(document).ready(function(){
        var autocomplete;
        var id = 'location';
        autocomplete = new google.maps.places.Autocomplete((document.getElementById(id)),{
            types:['geocode'],
        })

        var base_id = 'base_location';
        autocomplete = new google.maps.places.Autocomplete((document.getElementById(base_id)),{
            types:['geocode'],
        })
    });
    $('#change-password').on('submit',function(event){
        event.preventDefault();
        var Id = $('#data_id').val();
        var current_password = $('#current-password').val();
        var password = $('#password').val();
        var password_confirm = $('#password-confirm').val();
        $('#current_passwordError').text('');
        $('#passwordError').text('');
        $('#password_confirmError').text('');
        $.ajax({
            url: "{{ url('update-password') }}" + "/" + Id,
            type:"POST",
            data:{
                "current_password": current_password,
                "password": password,
                "password_confirmation": password_confirm,
                "_token": "{{ csrf_token() }}",
            },
            success:function(response){
                $('#current_passwordError').text('');
                $('#passwordError').text('');
                $('#password_confirmError').text('');
                if(response.isSuccess == false){
                    $('#current_passwordError').text(response.Message);
                }else if(response.isSuccess == true){
                    setTimeout(function () {
                        window.location.href = "{{ url('/') }}"
                    }, 1000);
                }
            },
            error: function(response) {
                $('#current_passwordError').text(response.responseJSON.errors.current_password);
                $('#passwordError').text(response.responseJSON.errors.password);
                $('#password_confirmError').text(response.responseJSON.errors.password_confirmation);
            }
        });
    });
</script>

@yield('script')
<script>
$(document).ready(function () {
if ($('header').width() <= 990 ){
    $('.top-nav').hide();
}
else{
    $('.top-nav').show();
}
if ($('header').width() <= 990 ){
    $('.nav-top-menu').show();
}
else{
    $('.nav-top-menu').hide();
}
$(window).resize(function(){     
    if ($('header').width() <= 990 ){
        $('.top-nav').hide();
    }
    else{
        $('.top-nav').show();
    }
}); 
$(window).resize(function(){     
    if ($('header').width() <= 990 ){
        $('.nav-top-menu').show();
    }
    else{
        $('.nav-top-menu').hide();
    }
});
});

</script>
<!-- App js -->
<script src="{{ URL::asset('assets/js/app.min.js')}}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>   
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>

<script>
$(document).ready(function(){
    $('.datetimepicker').datetimepicker({
    // use24hours: true,
    format: 'HH:mm'
});
});

</script>
@yield('script-bottom')
