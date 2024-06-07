@extends('layouts.admin')
@section('title', 'Edit Boat')
@section('css')

<style>
/* Remove the default number input arrows */
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

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
                        <input id="mac" type="text" class="form-control" name="mac" @isset($boat)value="{{$boat->mac}}" @endisset>
                        <input onclick="updateBoat('mac');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Serial<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="serial" type="text" class="form-control" name="serial" @isset($boat)value="{{$boat->serial}}" @endisset>
                        <input onclick="updateBoat('serial');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Rev<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="rev" type="number" class="form-control" name="rev" @isset($boat)value="{{$boat->rev}}" @endisset>
                        <input onclick="updateBoat('rev');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
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
                        <input type="number"  name="plan" id="plan"  value="{{$boat->plan }}" class="form-control">
                        {{-- <select id="plan" name="plan" id=""  class="form-control">
                            <option value="1" {{$boat->plan == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->plan == 0 ? 'selected' : ''}}>No</option>
                        </select> --}}
                        <input onclick="updateBoat('plan');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
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
                    <label for="">Default Interval<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="default_interval" type="number" class="form-control" name="default_interval" @isset($boat)value="{{$boat->default_interval}}" @endisset>
                        <input onclick="updateBoat('default_interval');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">GSM Enabled<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="gsm_enabled" name="gsm_enabled" id="" class="form-control">
                            <option value="1" {{$boat->gsm_enabled == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->gsm_enabled == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('gsm_enabled');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Upload Enabled<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="upload_enabled" name="upload_enabled" id="" class="form-control">
                            <option value="1" {{$boat->upload_enabled == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->upload_enabled == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('upload_enabled');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
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
                    <label for="">Default Upload Interval<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="default_upload_interval" type="number" class="form-control" name="default_upload_interval" @isset($boat)value="{{$boat->default_upload_interval}}" @endisset>
                        <input onclick="updateBoat('default_upload_interval');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Default Upload Timeout<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="default_upload_timeout" type="number" class="form-control" name="default_upload_timeout" @isset($boat)value="{{$boat->default_upload_timeout}}" @endisset>
                        <input onclick="updateBoat('default_upload_timeout');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
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
                    <label for="">Portal Timeout<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="portal_timeout" type="number" class="form-control" name="portal_timeout" @isset($boat)value="{{$boat->portal_timeout}}" @endisset>
                        <input onclick="updateBoat('portal_timeout');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Nmea in1 Enable<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="nmea_in1_enable" name="nmea_in1_enable" id="" class="form-control">
                            <option value="1" {{$boat->nmea_in1_enable == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->nmea_in1_enable == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('nmea_in1_enable');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Nmea in1 Baud<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="nmea_in1_baud" type="number" class="form-control" name="nmea_in1_baud" @isset($boat)value="{{$boat->nmea_in1_baud}}" @endisset>
                        <input onclick="updateBoat('nmea_in1_baud');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Nmea in2 Enable<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="nmea_in2_enable" name="nmea_in2_enable" id="" class="form-control">
                            <option value="1" {{$boat->nmea_in2_enable == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->nmea_in2_enable == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('nmea_in2_enable');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Nmea in2 Baud<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="nmea_in2_baud" type="number" class="form-control" name="nmea_in2_baud" @isset($boat)value="{{$boat->nmea_in2_baud}}" @endisset>
                        <input onclick="updateBoat('nmea_in2_baud');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Debug<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="debug" name="debug" id="" class="form-control">
                            <option value="1" {{$boat->debug == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->debug == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('debug');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Nmea Debug<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="nmea_debug" name="nmea_debug" id="" class="form-control">
                            <option value="1" {{$boat->nmea_debug == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->nmea_debug == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('nmea_debug');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Telnet Debug<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="telnet_debug" name="telnet_debug" id="" class="form-control">
                            <option value="1" {{$boat->telnet_debug == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->telnet_debug == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('telnet_debug');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Enable n2k<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="enable_n2k" name="enable_n2k" id="" class="form-control">
                            <option value="1" {{$boat->enable_n2k == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->enable_n2k == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('enable_n2k');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Nmea2k Debug<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="nmea2k_debug" name="nmea2k_debug" id="" class="form-control">
                            <option value="1" {{$boat->nmea2k_debug == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->nmea2k_debug == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('nmea2k_debug');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Http Debug<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="http_debug" name="http_debug" id="" class="form-control">
                            <option value="1" {{$boat->http_debug == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->http_debug == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('http_debug');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Gsm Debug<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="gsm_debug" name="gsm_debug" id="" class="form-control">
                            <option value="1" {{$boat->gsm_debug == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->gsm_debug == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('gsm_debug');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Mpu Debug<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="mpu_debug" name="mpu_debug" id="" class="form-control">
                            <option value="1" {{$boat->mpu_debug == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->mpu_debug == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('mpu_debug');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
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
                    <label for="">Telnet Server Port<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="telnet_server_port" type="number" class="form-control" name="telnet_server_port" @isset($boat)value="{{$boat->telnet_server_port}}" @endisset>
                        <input onclick="updateBoat('telnet_server_port');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Upload Server<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="upload_server" type="text" class="form-control" name="upload_server" @isset($boat)value="{{$boat->upload_server}}" @endisset>
                        <input onclick="updateBoat('upload_server');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Upload Port<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="upload_port" type="number" class="form-control" name="upload_port" @isset($boat)value="{{$boat->upload_port}}" @endisset>
                        <input onclick="updateBoat('upload_port');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Upload Path<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="upload_path" type="text" class="form-control" name="upload_path" @isset($boat)value="{{$boat->upload_path}}" @endisset>
                        <input onclick="updateBoat('upload_path');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Api Key<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="api_key" type="text" class="form-control" name="api_key" @isset($boat)value="{{$boat->api_key}}" @endisset>
                        <input onclick="updateBoat('api_key');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Settings Server<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="settings_server" type="text" class="form-control" name="settings_server" @isset($boat)value="{{$boat->settings_server}}" @endisset>
                        <input onclick="updateBoat('settings_server');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
                    </div>
                </div>
                <div class="form-group col-sm-4 mb-2">
                    <label for="">Settings Api Key<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="settings_api_key" type="text" class="form-control" name="settings_api_key" @isset($boat)value="{{$boat->settings_api_key}}" @endisset>
                        <input onclick="updateBoat('settings_api_key');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
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
                    <label for="">Test Data<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select id="test_data" name="test_data" id="" class="form-control">
                            <option value="1" {{$boat->test_data == 1 ? 'selected' : ''}}>Yes</option>
                            <option value="0" {{$boat->test_data == 0 ? 'selected' : ''}}>No</option>
                        </select>
                        <input onclick="updateBoat('test_data');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
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
                    <label for="">Update To<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="update_to" type="number" class="form-control" name="update_to" @isset($boat)value="{{$boat->update_to}}" @endisset>
                        <input onclick="updateBoat('update_to');" type="button" value="Update" class="mx-1 btn btn-primary btn-sm">
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
            url: "{{route('update_boat')}}",
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