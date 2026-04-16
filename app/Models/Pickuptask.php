<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use Carbon\Carbon;


class Pickuptask extends Model
{
    use HasFactory;
    protected $fillable = [
        'pickupclientname',
        'pickupclientcountry',
        'pickupclientcity',
        'pickupclientpostalcode',
        'pickupclientaddressline',
        'pickup_time_begin',
        'pickup_time_end',
        'task_id',
        'status_id',
    ];
    public function nameOfAddress(){
        return $this->pickupclientname;
    }
    public function country(){
        return $this->pickupclientcountry;
    }
    public function city(){
        return $this->pickupclientcity;
    }
    public function postalCode(){
        return $this->pickupclientpostalcode;
    }
    public function addressLine(){
        return $this->pickupclientaddressline;
    }
    public function addressShort(){
        return $this->pickupclientaddressline.' '.$this->pickupclientpostalcode;
    }
    public function pickupAddressShort(){
        return $this->pickupclientaddressline.' '.$this->pickupclientpostalcode;
    }
    public function pickupAddressFull(){
        return $this->pickupclientaddressline.' '.$this->pickupclientpostalcode.' '.$this->pickupclientcity .' '.$this->pickupclientcountry;
    }
    public function timeWindow(){
        return $this->pickup_time_begin.'/'.$this->pickup_time_end;
    }
    public function timeWindowBegin(){
        return $this->pickup_time_begin;
    }
    public function timeWindowEnd(){
        return $this->pickup_time_end;
    }
    public function timeWindowBeginFormatted(): string
    {
        if (!$this->pickup_time_begin) return 'N/A';
        
        return Carbon::parse($this->pickup_time_begin)
            //->locale(config('app.locale'))        
            ->isoFormat('h:mm A');               
    }
    public function timeWindowEndFormatted(): string
    {
        if (!$this->pickup_time_end) return 'N/A';

        return Carbon::parse($this->pickup_time_end)
            //->locale(config('app.locale'))
            ->isoFormat('h:mm A');
    }
    public function setAddress($name,$country,$city,$postalCode,$addressLine){
        $this->pickupclientname         =   $name;
        $this->pickupclientcountry      =   $country;
        $this->pickupclientcity         =   $city;
        $this->pickupclientpostalcode   =   $postalCode;
        $this->pickupclientaddressline  =   $addressLine;     
    }
    public function setTimeWindow($begin,$end){
        $this->pickup_time_begin    =   $begin;
        $this->pickup_time_end      =   $end;
    }
}
