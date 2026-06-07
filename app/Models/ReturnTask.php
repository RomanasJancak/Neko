<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class ReturnTask extends Model
{
    protected $table = 'returntasks';
    use HasFactory;
    protected $fillable = [
        'name',
        'country',
        'city',
        'postal_code',
        'adress_line',
        'time_begin',
        'time_end',
        'task_id',
        'status_id',
    ];
    public function task()
    {
        return $this->morphOne(Task::class, 'taskable');
    }
    public function addressShort(){
        return $this->adress_line.' '.$this->postal_code;
    }
    public function addressFull(){
        return $this->country.' '.$this->city.' '.$this->postal_code.' '.$this->adress_line;
    }
    public function nameOfAddress(){
        return $this->name;
    }
    public function country(){
        return $this->country;
    }
    public function city(){
        return $this->city;
    }
    public function postalCode(){
        return $this->postal_code;
    }
    public function addressLine(){
        return $this->adress_line;
    }
    public function timeWindow(){
        return $this->time_begin.'/'.$this->time_end;
    }
    public function timeWindowBegin(){
        return $this->time_begin;
    }
    public function timeWindowEnd(){
        return $this->time_end;
    }
    public function timeWindowBeginFormatted(): string
    {
        if (!$this->time_begin) return 'N/A';
        
        return Carbon::parse($this->time_begin)
            //->locale(config('app.locale'))        
            ->isoFormat('h:mm A');               
    }
    public function timeWindowEndFormatted(): string
    {
        if (!$this->time_end) return 'N/A';

        return Carbon::parse($this->time_end)
            //->locale(config('app.locale'))
            ->isoFormat('h:mm A');
    }
    public function setAddress($name,$country,$city,$postalCode,$addressLine){
        $this->name         =   $name;
        $this->country      =   $country;
        $this->city         =   $city;
        $this->postal_code   =   $postalCode;
        $this->adress_line  =   $addressLine;     
    }
    public function setTimeWindow($begin,$end){
        $this->time_begin    =   $begin;
        $this->time_end      =   $end;
    }
}
