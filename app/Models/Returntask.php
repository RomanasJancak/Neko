<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Returntask extends Model
{
    use HasFactory;
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    public function addressShort(){
        return $this->adress_line.' '.$this->postal_code;
    }
    public function addressFull(){
        return $this->country.' '.$this->city.' '.$this->postal_code.' '.$this->adress_line;
    }
    public function timeWindow(){
        return $this->time_begin.'/'.$this->time_end;
    }
}
