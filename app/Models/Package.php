<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Package extends Model
{
    use HasFactory;
    protected $fillable = [
        'dropoff_name',
        'dropoff_country',
        'dropoff_city',
        'dropoff_postal_code',
        'dropoff_adress_line',
        'packagedropofftimebegin',
        'packagedropofftimeend',
        'quantity',
        'status_id',
        'packageType_id',
        'job_id',
        'task_id',
        'status_id',
        'weight',
        'dimensions',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    public function status(){
        return $this->belongsTo(Status::class,'status_id');
    }
    public function packageType()
    {
        return $this->belongsTo(PackageType::class,'packageType_id');
    }
    public function changeTypeWithId($id){
        $this->packageType_id = $id;
    }
    public function addOns()
    {
        return $this->hasMany(AddOn::class, 'model_id')
                    ->where('model_type', '=', 'app/models/Package');
    }
    public function nameOfAddress(){
        return $this->dropoff_name;
    }
    public function country(){
        return $this->dropoff_country;
    }
    public function city(){
        return $this->dropoff_city;
    }
    public function postalCode(){
        return $this->dropoff_postal_code;
    }
    public function addressLine(){
        return $this->dropoff_adress_line;
    }
    public function addressShort(){
        return $this->dropoff_adress_line.' '.$this->dropoff_postal_code;
    }
    public function fullAddress(){
        return $this->dropoff_adress_line.' '.$this->dropoff_postal_code.' '.$this->dropoff_city .' '.$this->dropoff_country;
    }
    public function timeWindow(){
        return $this->packagedropofftimebegin.'/'.$this->packagedropofftimeend;
    }
    public function timeWindowBegin(){
        return $this->packagedropofftimebegin;
    }
    public function timeWindowEnd(){
        return $this->packagedropofftimeend;
    }
    public function timeWindowBeginFormatted(): string
    {
        if (!$this->packagedropofftimebegin) return 'N/A';

        return Carbon::parse($this->packagedropofftimebegin)
            //->locale(config('app.locale'))
            ->isoFormat('h:mm A');
    }
    public function timeWindowEndFormatted(): string
    {
        if (!$this->packagedropofftimeend) return 'N/A';

        return Carbon::parse($this->packagedropofftimeend)
            //->locale(config('app.locale'))
            ->isoFormat('h:mm A');
    }
    public function setAddress($name,$country,$city,$postalCode,$addressLine){
        $this->dropoff_name         =   $name;
        $this->dropoff_country      =   $country;
        $this->dropoff_city         =   $city;
        $this->dropoff_postal_code   =   $postalCode;
        $this->dropoff_adress_line  =   $addressLine;     
    }
    public function setTimeWindow($begin,$end){
        $this->packagedropofftimebegin    =   $begin;
        $this->packagedropofftimeend      =   $end;
    }
    public function setQuantity($quantity){
         $this->quantity = $quantity;
    }
}
