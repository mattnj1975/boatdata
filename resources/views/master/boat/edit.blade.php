@extends('layouts.admin')

@section('title', 'Edit Boat')

@section('css')
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

    .bd-card {
        background: #111827;
        border: 1px solid rgba(148,163,184,.18);
        border-radius: 18px;
        box-shadow: 0 16px 40px rgba(0,0,0,.22);
        padding: 20px;
    }

    .bd-section-title {
        color: #f8fafc;
        font-size: 16px;
        font-weight: 800;
        margin: 18px 0 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid rgba(148,163,184,.14);
    }

    .bd-section-title:first-child {
        margin-top: 0;
    }

    .bd-label {
        color: #cbd5e1;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .bd-input,
    .bd-select {
        background: #0f172a !important;
        border: 1px solid rgba(148,163,184,.22) !important;
        color: #f8fafc !important;
        border-radius: 12px !important;
        min-height: 42px;
        font-size: 13px;
    }

    .bd-input:focus,
    .bd-select:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 .18rem rgba(59,130,246,.18) !important;
    }

    .bd-input[readonly],
    .bd-select[readonly],
    .bd-select:disabled,
    .bd-input:disabled {
        background: #020617 !important;
        color: #94a3b8 !important;
        cursor: not-allowed;
    }

    .bd-update-btn {
        background: #2563eb;
        color: #fff;
        border: 0;
        border-radius: 12px !important;
        font-size: 12px;
        font-weight: 800;
        padding: 0 12px;
        min-width: 74px;
    }

    .bd-update-btn:hover {
        background: #3b82f6;
        color: #fff;
    }

    .bd-readonly-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 9px;
        border-radius: 999px;
        background: rgba(148,163,184,.12);
        color: #94a3b8;
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .input-group {
        gap: 6px;
    }

    .input-group > .form-control,
    .input-group > .form-select {
        border-radius: 12px !important;
    }

    @media (max-width: 768px) {
        .bd-card {
            padding: 14px;
        }

        .bd-update-btn {
            min-width: 68px;
        }
    }
</style>
@endsection

@section('content')

<div class="bd-page-header">
    <h1 class="bd-title">Edit Boat</h1>
    <div class="bd-subtitle">
        {{ $boat->boatname ?: 'Unnamed Boat' }} — {{ $boat->mac }}
    </div>
</div>

<div class="bd-card">
    <form action="" method="post" enctype="multipart/form-data">
        @csrf

        <div class="bd-section-title">Device Details</div>

        <div class="row g-3">
            <div class="form-group col-sm-4">
                <label class="bd-label">Device ID</label>
                <input readonly type="text" class="form-control bd-input" name="device_id" @isset($boat)value="{{ $boat->device_id }}" @endisset>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">MAC</label>
                <input readonly id="mac" type="text" class="form-control bd-input" name="mac" @isset($boat)value="{{ $boat->mac }}" @endisset>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">Serial</label>
                <input readonly id="serial" type="text" class="form-control bd-input" name="serial" @isset($boat)value="{{ $boat->serial }}" @endisset>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">Rev</label>
                <input readonly id="rev" type="number" class="form-control bd-input" name="rev" @isset($boat)value="{{ $boat->rev }}" @endisset>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">Plan</label>
                <input type="number" readonly name="plan" id="plan" value="{{ $boat->plan }}" class="form-control bd-input">
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">Version</label>
                <input readonly type="text" class="form-control bd-input" name="version" @isset($boat)value="{{ $boat->version }}" @endisset>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">Last Seen</label>
                <input readonly type="text" class="form-control bd-input" name="lastseen" @isset($boat)value="{{ $boat->lastseen }}" @endisset>
            </div>
        </div>

        <div class="bd-section-title">Boat Settings</div>

        <div class="row g-3">
            <div class="form-group col-sm-4">
                <label class="bd-label">Boat Name</label>
                <div class="input-group">
                    <input id="boatname" type="text" class="form-control bd-input" name="boatname" @isset($boat)value="{{ $boat->boatname }}" @endisset>
                    <input onclick="updateBoat('boatname');" type="button" value="Update" class="btn bd-update-btn">
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">Public</label>
                <div class="input-group">
                    <select id="public" name="public" class="form-control bd-select">
                        <option value="1" {{ $boat->public == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $boat->public == 0 ? 'selected' : '' }}>No</option>
                    </select>
                    <input onclick="updateBoat('public');" type="button" value="Update" class="btn bd-update-btn">
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">Upload Enabled</label>
                <select readonly id="upload_enabled" name="upload_enabled" class="form-control bd-select">
                    <option disabled value="1" {{ $boat->upload_enabled == 1 ? 'selected' : '' }}>Yes</option>
                    <option disabled value="0" {{ $boat->upload_enabled == 0 ? 'selected' : '' }}>No</option>
                </select>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">Upload Underway</label>
                <div class="input-group">
                    <select id="upload_underway" name="upload_underway" class="form-control bd-select">
                        <option value="1" {{ $boat->upload_underway == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $boat->upload_underway == 0 ? 'selected' : '' }}>No</option>
                    </select>
                    <input onclick="updateBoat('upload_underway');" type="button" value="Update" class="btn bd-update-btn">
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">SOG Average</label>
                <div class="input-group">
                    <input id="sog_average" type="number" class="form-control bd-input" name="sog_average" @isset($boat)value="{{ $boat->sog_average }}" @endisset>
                    <input onclick="updateBoat('sog_average');" type="button" value="Update" class="btn bd-update-btn">
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">Buzz On</label>
                <div class="input-group">
                    <select id="buzz_on" name="buzz_on" class="form-control bd-select">
                        <option value="1" {{ $boat->buzz_on == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $boat->buzz_on == 0 ? 'selected' : '' }}>No</option>
                    </select>
                    <input onclick="updateBoat('buzz_on');" type="button" value="Update" class="btn bd-update-btn">
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">Beep Log</label>
                <div class="input-group">
                    <select id="beep_log" name="beep_log" class="form-control bd-select">
                        <option value="1" {{ $boat->beep_log == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $boat->beep_log == 0 ? 'selected' : '' }}>No</option>
                    </select>
                    <input onclick="updateBoat('beep_log');" type="button" value="Update" class="btn bd-update-btn">
                </div>
            </div>
        </div>

        <div class="bd-section-title">GPS Settings</div>

        <div class="row g-3">
            <div class="form-group col-sm-4">
                <label class="bd-label">Internal GPS</label>
                <div class="input-group">
                    <select id="int_gps" name="int_gps" class="form-control bd-select">
                        <option value="1" {{ $boat->int_gps == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $boat->int_gps == 0 ? 'selected' : '' }}>No</option>
                    </select>
                    <input onclick="updateBoat('int_gps');" type="button" value="Update" class="btn bd-update-btn">
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">GPS Filter</label>
                <div class="input-group">
                    <select id="gps_filter" name="gps_filter" class="form-control bd-select">
                        <option value="1" {{ $boat->gps_filter == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $boat->gps_filter == 0 ? 'selected' : '' }}>No</option>
                    </select>
                    <input onclick="updateBoat('gps_filter');" type="button" value="Update" class="btn bd-update-btn">
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">GPS Filter HDOP</label>
                <div class="input-group">
                    <input id="gps_filter_hdop" type="number" class="form-control bd-input" name="gps_filter_hdop" @isset($boat)value="{{ $boat->gps_filter_hdop }}" @endisset>
                    <input onclick="updateBoat('gps_filter_hdop');" type="button" value="Update" class="btn bd-update-btn">
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">GPS Filter Sats</label>
                <div class="input-group">
                    <input id="gps_filter_sats" type="number" class="form-control bd-input" name="gps_filter_sats" @isset($boat)value="{{ $boat->gps_filter_sats }}" @endisset>
                    <input onclick="updateBoat('gps_filter_sats');" type="button" value="Update" class="btn bd-update-btn">
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">Fast GPS</label>
                <div class="input-group">
                    <select id="fast_gps" name="fast_gps" class="form-control bd-select">
                        <option value="1" {{ $boat->fast_gps == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $boat->fast_gps == 0 ? 'selected' : '' }}>No</option>
                    </select>
                    <input onclick="updateBoat('fast_gps');" type="button" value="Update" class="btn bd-update-btn">
                </div>
            </div>
        </div>

        <div class="bd-section-title">Network & Power</div>

        <div class="row g-3">
            <div class="form-group col-sm-4">
                <label class="bd-label">Telnet Client Enable</label>
                <div class="input-group">
                    <select id="telnet_client_enable" name="telnet_client_enable" class="form-control bd-select">
                        <option value="1" {{ $boat->telnet_client_enable == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $boat->telnet_client_enable == 0 ? 'selected' : '' }}>No</option>
                    </select>
                    <input onclick="updateBoat('telnet_client_enable');" type="button" value="Update" class="btn bd-update-btn">
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">Telnet Client</label>
                <div class="input-group">
                    <input id="telnet_client" type="text" class="form-control bd-input" name="telnet_client" @isset($boat)value="{{ $boat->telnet_client }}" @endisset>
                    <input onclick="updateBoat('telnet_client');" type="button" value="Update" class="btn bd-update-btn">
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">Telnet Client Port</label>
                <div class="input-group">
                    <input id="telnet_client_port" type="number" class="form-control bd-input" name="telnet_client_port" @isset($boat)value="{{ $boat->telnet_client_port }}" @endisset>
                    <input onclick="updateBoat('telnet_client_port');" type="button" value="Update" class="btn bd-update-btn">
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">APN</label>
                <div class="input-group">
                    <input id="apn" type="text" class="form-control bd-input" name="apn" @isset($boat)value="{{ $boat->apn }}" @endisset>
                    <input onclick="updateBoat('apn');" type="button" value="Update" class="btn bd-update-btn">
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">GPRS User</label>
                <div class="input-group">
                    <input id="gprsuser" type="text" class="form-control bd-input" name="gprsuser" @isset($boat)value="{{ $boat->gprsuser }}" @endisset>
                    <input onclick="updateBoat('gprsuser');" type="button" value="Update" class="btn bd-update-btn">
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">GPRS Pass</label>
                <div class="input-group">
                    <input id="gprspass" type="text" class="form-control bd-input" name="gprspass" @isset($boat)value="{{ $boat->gprspass }}" @endisset>
                    <input onclick="updateBoat('gprspass');" type="button" value="Update" class="btn bd-update-btn">
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">Reset Wifi</label>
                <div class="input-group">
                    <select id="reset_wifi" name="reset_wifi" class="form-control bd-select">
                        <option value="1" {{ $boat->reset_wifi == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $boat->reset_wifi == 0 ? 'selected' : '' }}>No</option>
                    </select>
                    <input onclick="updateBoat('reset_wifi');" type="button" value="Update" class="btn bd-update-btn">
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label class="bd-label">Power Save</label>
                <div class="input-group">
                    <select id="power_save" name="power_save" class="form-control bd-select">
                        <option value="1" {{ $boat->power_save == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $boat->power_save == 0 ? 'selected' : '' }}>No</option>
                    </select>
                    <input onclick="updateBoat('power_save');" type="button" value="Update" class="btn bd-update-btn">
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script>
$(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

function updateBoat(boat_key)
{
    boat_id = {{ $boat->id }};
    boat_value = document.getElementById(boat_key).value;

    if (boat_value || boat_key == 'gprsuser' || boat_key == 'gprspass') {
        $.ajax({
            data: {
                boat_id: boat_id,
                boat_key: boat_key,
                boat_value: boat_value
            },
            url: "{{ route('master_update_boat') }}",
            type: "POST",
            dataType: 'json',
            success: function(data) {
                Toast.fire({
                    icon: 'success',
                    title: data.success
                });
            },
            error: function(xhr) {
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