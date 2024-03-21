<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    public function pickup()
    {
        return $this->hasOne(Pickuptask::class);
    }
    public function package(){
        return $this->hasOne(Package::class); 
    }
    public function return(){
        return $this->hasOne(ReturnTask::class);
    }
    public function customTask(){
        return $this->hasOne(CustomTask::class);
    }
    public function job(){
        return $this->belongsTo(Job::class);
    }
    public function status(){
        return $this->belongsTo(Status::class,'status_id');
    }
}
