<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Uploadlog extends Model
{
    protected $table = "uploadlog";
    use HasFactory;
    protected $fillable = [
        'upload_id',
        'device_id',
        'uload_time',
        'upload_status',
        'connection_status',
        'ip_address',
        'sd_space',
        'sd_used',
        'db_ok',
        'db_err'
    ];
}
