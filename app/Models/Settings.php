<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;
    protected $table = "settings";
    protected $fillable = [
        'device_id',
        'mac',
        'serial',
        'rev',
        'public',
        'plan',
        'boatname',
        'default_interval',
        'gsm_enabled',
        'upload_enabled',
        'upload_underway',
        'default_upload_interval',
        'default_upload_timeout',
        'sog_average',
        'portal_timeout',
        'nmea_in1_enable',
        'nmea_in1_baud',
        'nmea_in2_enable',
        'nmea_in2_baud',
        'debug',
        'nmea_debug',
        'telnet_debug',
        'enable_n2k',
        'nmea2k_debug',
        'http_debug',
        'gsm_debug',
        'mpu_debug',
        'buzz_on',
        'fix_req',
        'int_gps',
        'gps_filter',
        'gps_filter_hdop',
        'gps_filter_sats',
        'telnet_client_enable',
        'telnet_client',
        'telnet_client_port',
        'telnet_server_port',
        'upload_server',
        'upload_port',
        'upload_path',
        'api_key',
        'settings_server',
        'settings_api_key',
        'apn',
        'gprsuser',
        'gprspass',
        'fast_gps',
        'test_data',
        'reset_wifi',
        'power_save',
        'beep_log',
        'update_to',
        'lastseen',
        'version',
    ];

    public function boatData()
    {
        return $this->hasMany(BoatData::class, 'mac', 'mac');
    }
}
