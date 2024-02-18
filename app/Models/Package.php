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
        return $this->dropoff_postal_code.' '.$this->dropoff_adress_line;
    }
}
