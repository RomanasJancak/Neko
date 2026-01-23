<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bike extends Model
{
    use HasFactory;
    public function workloads()
    {
        return $this->hasMany(Workload::class,'bike_id'); 
    }
    public function status(){
        return $this->belongsTo(Status::class,'status_id');
    }
}
