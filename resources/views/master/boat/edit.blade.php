@extends('layouts.admin')
@section('title', 'Edit Boat')
@section('css')

<style>

</style>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Edit Boat</h4>
            {{-- {{ $errors }}--}}
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Edit Boat</li>
                </ol>
                
            </div>
        </div>
    </div>
</div>

<div class="w-100">
    <div class="row justify-content-center">
        <div class="col-md-12 mt-4">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Device ID<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input readonly type="text" class="form-control" name="device_id" @isset($boat)value="{{$boat->device_id}}" @endisset>
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Mac<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input readonly id="mac" type="text" class="form-control" name="mac" @isset($boat)value="{{$boat->mac}}" @endisset>
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Serial<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input readonly id="serial" type="text" class="form-control" name="serial" @isset($boat)value="{{$boat->serial}}" @endisset>
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Rev<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input readonly id="rev" type="number" class="form-control" name="rev" @isset($boat)value="{{$boat->rev}}" @endisset>
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Public<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="public" name="public" id="" class="form-control">
                            <option value="1" {{$boat->public == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->public == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('public');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Plan<span class="text-danger">*</span></label>
                    <div class="input-group">
                    <input type="number" readonly  name="plan" id="plan"  value="{{$boat->plan }}" class="form-control">
                        <!-- <select readonly id="plan" name="plan" id="" class="form-control">
                            <option disabled value="1" {{$boat->plan == 1 ? 'selected' : ''}}>Yes</option>
                            <option disabled value="0" {{$boat->plan == 0 ? 'selected' : ''}}>No</option>
                        </select> -->
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Boat Name<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="boatname" type="text" class="form-control" name="boatname" @isset($boat)value="{{$boat->boatname}}" @endisset>
                        <input onclick="updateBoat('boatname');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Upload Enabled<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select readonly id="upload_enabled" name="upload_enabled" id="" class="form-control">
                            <option disabled value="1" {{$boat->upload_enabled == 1 ? 'selected' : ''}}>Yes</option>
                            <option disabled value="0" {{$boat->upload_enabled == 0 ? 'selected' : ''}}>No</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Upload Underway<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="upload_underway" name="upload_underway" id="" class="form-control">
                            <option value="1" {{$boat->upload_underway == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->upload_underway == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('upload_underway');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Sog Average<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="sog_average" type="number" class="form-control" name="sog_average" @isset($boat)value="{{$boat->sog_average}}" @endisset>
                        <input onclick="updateBoat('sog_average');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                
               
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Buzz on<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="buzz_on" name="buzz_on" id="" class="form-control">
                            <option value="1" {{$boat->buzz_on == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->buzz_on == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('buzz_on');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Int GPS<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="int_gps" name="int_gps" id="" class="form-control">
                            <option value="1" {{$boat->int_gps == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->int_gps == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('int_gps');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">GPS Filter<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="gps_filter" name="gps_filter" id="" class="form-control">
                            <option value="1" {{$boat->gps_filter == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->gps_filter == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('gps_filter');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">GPS Filter HDOP<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="gps_filter_hdop" type="number" class="form-control" name="gps_filter_hdop" @isset($boat)value="{{$boat->gps_filter_hdop}}" @endisset>
                        <input onclick="updateBoat('gps_filter_hdop');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">GPS Filter Sats<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="gps_filter_sats" type="number" class="form-control" name="gps_filter_sats" @isset($boat)value="{{$boat->gps_filter_sats}}" @endisset>
                        <input onclick="updateBoat('gps_filter_sats');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Telnet Client Enable<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="telnet_client_enable" name="telnet_client_enable" id="" class="form-control">
                            <option value="1" {{$boat->telnet_client_enable == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->telnet_client_enable == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('telnet_client_enable');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Telnet Client<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="telnet_client" type="text" class="form-control" name="telnet_client" @isset($boat)value="{{$boat->telnet_client}}" @endisset>
                        <input onclick="updateBoat('telnet_client');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Telnet Client Port<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="telnet_client_port" type="number" class="form-control" name="telnet_client_port" @isset($boat)value="{{$boat->telnet_client_port}}" @endisset>
                        <input onclick="updateBoat('telnet_client_port');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                
                <div class="form-group col-sm-4 mb-2">
                    <label for="">APN<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="apn" type="text" class="form-control" name="apn" @isset($boat)value="{{$boat->apn}}" @endisset>
                        <input onclick="updateBoat('apn');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Gprs User</label>
                    <div class="input-group">
                        <input id="gprsuser" type="text" class="form-control" name="gprsuser" @isset($boat)value="{{$boat->gprsuser}}" @endisset>
                        <input onclick="updateBoat('gprsuser');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Gprs Pass</label>
                    <div class="input-group">
                        <input id="gprspass" type="text" class="form-control" name="gprspass" @isset($boat)value="{{$boat->gprspass}}" @endisset>
                        <input onclick="updateBoat('gprspass');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Fast GPS<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="fast_gps" name="fast_gps" id="" class="form-control">
                            <option value="1" {{$boat->fast_gps == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->fast_gps == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('fast_gps');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Reset Wifi<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="reset_wifi" name="reset_wifi" id="" class="form-control">
                            <option value="1" {{$boat->reset_wifi == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->reset_wifi == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('reset_wifi');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Power Save<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="power_save" name="power_save" id="" class="form-control">
                            <option value="1" {{$boat->power_save == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->power_save == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('power_save');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Beep Log<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="beep_log" name="beep_log" id="" class="form-control">
                            <option value="1" {{$boat->beep_log == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->beep_log == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('beep_log');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Last Seen<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input readonly type="text" class="form-control" name="lastseen" @isset($boat)value="{{$boat->lastseen}}" @endisset>
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Version<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input readonly type="text" class="form-control" name="version" @isset($boat)value="{{$boat->version}}" @endisset>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script>
$(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
})
function updateBoat(boat_key)
{
    boat_id = {{ $boat->id }}
    boat_value = document.getElementById(boat_key).value;
    if (boat_value || boat_key == 'gprsuser' || boat_key == 'gprspass') {
        $.ajax({
            data: {boat_id: boat_id,boat_key: boat_key, boat_value: boat_value},
            url: "{{route('master_update_boat')}}",
            type: "POST",
            dataType: 'json',
            success: function(data) {
                Toast.fire({
                    icon: 'success',
                    title: data.success
                });
            },
            error: function(xhr, status, error) {
            var errorMessage = xhr.responseJSON ? xhr.responseJSON.error : 'An error occurred';
            Toast.fire({
                icon: 'error',
                title: errorMessage
            });
        }
        });
    } else {
        Toast.fire({
            icon: 'warning',
            title: boat_key + ' value can not be empty!'
        });
    }
    
}
</script>

@endsection