<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'status_id',
        'job_id',
        'order_number',
    ];
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
    public function type(){
        $return_value   =   isset($this->pickup)
                            ?   'pickup'
                            :   (isset($this->package)
                                ?   'dropOff'
                                :   (isset($this->return)
                                    ?   'return'
                                    :   (isset($this->customTask)
                                        ?   'custom'
                                        :   null)));
        return $return_value;
    }
    public function typeOfTask(){
        $return_value   =   isset($this->pickup)
                            ?   $this->hasOne(Pickuptask::class)
                            :   (isset($this->package)
                                ?   $this->hasOne(Package::class)
                                :   (isset($this->return)
                                    ?   $this->hasOne(ReturnTask::class)
                                    :   (isset($this->customTask)
                                        ?   $this->hasOne(CustomTask::class)
                                        :   null)));
        return $return_value;
    }
    public function job(){
        return $this->belongsTo(Job::class);
    }
    public function status(){
        return $this->belongsTo(Status::class,'status_id');
    }
    public function nameOfAddress()
    {
        $return_value   =   isset($this->pickup)
                            ?   $this->pickup->nameOfAddress() 
                            :   (isset($this->package)
                                ?   $this->package->nameOfAddress()
                                :   (isset($this->return)
                                    ?   $this->return->nameOfAddress()
                                    :   (isset($this->customTask)
                                        ?   $this->customTask->nameOfAddress()
                                        :   null)));
        return $return_value;
    }
    public function country()
    {
        $return_value   =   isset($this->pickup)
                            ?   $this->pickup->country() 
                            :   (isset($this->package)
                                ?   $this->package->country()
                                :   (isset($this->return)
                                    ?   $this->return->country()
                                    :   (isset($this->customTask)
                                        ?   $this->customTask->country()
                                        :   null)));
        return $return_value;
    }
    public function city()
    {
        $return_value   =   isset($this->pickup)
                            ?   $this->pickup->city() 
                            :   (isset($this->package)
                                ?   $this->package->city() 
                                :   (isset($this->return)
                                    ?   $this->return->city() 
                                    :   (isset($this->customTask)
                                        ?   $this->customTask->city() 
                                        :   null)));
        return $return_value;
    }
    public function addressShort(){
        $return_value   =   isset($this->pickup)
                            ?   $this->pickup->addressShort() 
                            :   (isset($this->package)
                                ?   $this->package->addressShort()
                                :   (isset($this->return)
                                    ?   $this->return->addressShort()
                                    :   (isset($this->customTask)
                                        ?   $this->customTask->name
                                        :   null)));
        return $return_value;
    }
    public function postalCode()
    {
        $return_value   =   isset($this->pickup)
                            ?   $this->pickup->postalCode() 
                            :   (isset($this->package)
                                ?   $this->package->postalCode()
                                :   (isset($this->return)
                                    ?   $this->return->postalCode()
                                    :   (isset($this->customTask)
                                        ?   $this->customTask->postalCode()
                                        :   null)));
        return $return_value;
    }
    public function addressLine()
    {
        $return_value   =   isset($this->pickup)
                            ?   $this->pickup->addressLine() 
                            :   (isset($this->package)
                                ?   $this->package->addressLine()
                                :   (isset($this->return)
                                    ?   $this->return->addressLine()
                                    :   (isset($this->customTask)
                                        ?   $this->customTask->addressLine()
                                        :   null)));
        return $return_value;
    }
    public function fullAddress()//not finished
    {
        $return_value   =   isset($this->pickup)
                ?   $this->pickup->pickupAddressFull() 
                :   (isset($this->package)
                    ?   $this->package->dropoff_country.' '.$this->package->dropoff_city.' '.$this->package->dropoff_postal_code.' '.$this->package->dropoff_adress_line
                    :   (isset($this->return)
                        ?   $this->return->addressFull()
                        :   (isset($this->customTask)
                            ?   $this->customTask->name
                            :   null)));
        return $return_value;
    }

    public function timeWindow()
    {
        $return_value   =   isset($this->pickup)
            ?   $this->pickup->timeWindow() 
            :   (isset($this->package)
                ?   $this->package->timeWindow() 
                :   (isset($this->return)
                    ?   $this->return->timeWindow()
                    :   (isset($this->customTask)
                        ?   $this->customTask->name
                        :   null)));
        return $return_value;
    }
    public function timeWindowBegin(){
        $return_value   =   isset($this->pickup)
        ?   $this->pickup->timeWindowBegin() 
        :   (isset($this->package)
            ?   $this->package->timeWindowBegin() 
            :   (isset($this->return)
                ?   $this->return->timeWindowBegin()
                :   (isset($this->customTask)
                    ?   $this->customTask->timeWindowBegin()
                    :   null)));
        return $return_value;
    }
    public function timeWindowEnd(){
        $return_value   =   isset($this->pickup)
        ?   $this->pickup->timeWindowEnd() 
        :   (isset($this->package)
            ?   $this->package->timeWindowEnd() 
            :   (isset($this->return)
                ?   $this->return->timeWindowEnd()
                :   (isset($this->customTask)
                    ?   $this->customTask->timeWindowEnd()
                    :   null)));
        return $return_value;
    }
}
