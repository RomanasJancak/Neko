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
                                ?   $this->package->dropoff_name
                                :   (isset($this->return)
                                    ?   $this->return->name
                                    :   (isset($this->customTask)
                                        ?   $this->customTask->name
                                        :   null)));
        return $return_value;
    }
    public function country(){
        $return_value   =   isset($this->pickup)
                            ?   $this->pickup->country() 
                            :   (isset($this->package)
                                ?   $this->package->dropoff_postal_code
                                :   (isset($this->return)
                                    ?   $this->return->postal_code
                                    :   (isset($this->customTask)
                                        ?   $this->customTask->postal_code
                                        :   null)));
        return $return_value;
    }
    public function city(){
        $return_value   =   isset($this->pickup)
                            ?   $this->pickup->city() 
                            :   (isset($this->package)
                                ?   $this->package->dropoff_postal_code
                                :   (isset($this->return)
                                    ?   $this->return->postal_code
                                    :   (isset($this->customTask)
                                        ?   $this->customTask->postal_code
                                        :   null)));
        return $return_value;
    }
    public function postalCode()
    {
        $return_value   =   isset($this->pickup)
                            ?   $this->pickup->postalCode() 
                            :   (isset($this->package)
                                ?   $this->package->dropoff_postal_code
                                :   (isset($this->return)
                                    ?   $this->return->postal_code
                                    :   (isset($this->customTask)
                                        ?   $this->customTask->postal_code
                                        :   null)));
        return $return_value;
    }
    public function addressLine()
    {
        $return_value   =   isset($this->pickup)
                            ?   $this->pickup->addressLine() 
                            :   (isset($this->package)
                                ?   $this->package->dropoff_postal_code
                                :   (isset($this->return)
                                    ?   $this->return->postal_code
                                    :   (isset($this->customTask)
                                        ?   $this->customTask->postal_code
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
