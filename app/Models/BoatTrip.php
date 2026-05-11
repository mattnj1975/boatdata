<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoatTrip extends Model
{
    protected $fillable = [
        'mac',
        'detected_start_boatdata_id',
        'detected_end_boatdata_id',
        'start_boatdata_id',
        'end_boatdata_id',
        'detected_start_time',
        'detected_end_time',
        'start_time',
        'end_time',
        'start_lat',
        'start_lon',
        'end_lat',
        'end_lon',
        'duration_minutes',
        'distance_nm',
        'max_sog',
        'avg_sog',
        'max_spd',
        'avg_spd',
        'status',
        'notes',
    ];

    protected $casts = [
        'detected_start_time' => 'datetime',
        'detected_end_time' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
}