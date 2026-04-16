<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workload extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bike_id',
        'day_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function bike()
    {
        return $this->belongsTo(Bike::class);
    }
    public function day(){
        return $this->belongsTo(Day::class);
    }
}
