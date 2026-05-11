<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripDetectionConfig extends Model
{
    protected $fillable = [
        'mac',
        'min_sog',
        'min_spd',
        'min_moving_minutes',
        'min_stopped_minutes',
        'start_rewind_minutes',
        'end_extend_minutes',
        'max_gap_minutes',
        'use_engine_rpm',
        'min_rpm',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'use_engine_rpm' => 'boolean',
    ];
}