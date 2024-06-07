<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminBoats extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'boat_id'
    ];
    public function boat()
    {
        return $this->belongsTo(Settings::class,'boat_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
