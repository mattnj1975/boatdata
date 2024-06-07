<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoatFile extends Model
{
    use HasFactory;
    protected $fillable = [
        'boat_id',
        'file',
        'file_name',
        'user_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
