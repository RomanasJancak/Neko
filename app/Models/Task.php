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
    public function postalCode()
    {
        $return_value   =   isset($this->pickup)
                            ?   $this->pickup->pickupclientpostalcode 
                            :   (isset($this->package)
                                ?   $this->package->dropoff_postal_code
                                :   (isset($this->return)
                                    ?   $this->return->postal_code
                                    :   (isset($this->customTask)
                                        ?   $this->customTask->postal_code
                                        :   null)));
        return $return_value;
    }
    public function nameOfAddress()
    {
        $return_value   =   isset($this->pickup)
                            ?   $this->pickup->pickupclientname 
                            :   (isset($this->package)
                                ?   $this->package->dropoff_name
                                :   (isset($this->return)
                                    ?   $this->return->name
                                    :   (isset($this->customTask)
                                        ?   $this->customTask->name
                                        :   null)));
        return $return_value;
    }
    public function fullAddress(){
        $return_value   =   isset($this->pickup)
                ?   $this->pickup->pickupclientname 
                :   (isset($this->package)
                    ?   $this->package->dropoff_country.' '.$this->package->dropoff_city.' '.$this->package->dropoff_postal_code.' '.$this->package->dropoff_adress_line
                    :   (isset($this->return)
                        ?   $this->return->name
                        :   (isset($this->customTask)
                            ?   $this->customTask->name
                            :   null)));
        return $return_value;
    }
}
