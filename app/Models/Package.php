<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

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
        return $this->belongsTo(PackageType::class);
    }
    public function addOns()
    {
        return $this->hasMany(AddOn::class, 'model_id')
                    ->where('model_type', '=', 'app/models/Package');
    }
    public function pickupAddressShort(){
        return $this->dropoff_adress_line.' '.$this->dropoff_postal_code;
    }
    public function addressShort(){
        return $this->dropoff_adress_line.' '.$this->dropoff_postal_code;
    }
}
