<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageType extends Model
{
    use HasFactory;

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client__package_types')->withTimestamps();
    }
    public function packages(){
        
        return $this->hasMany(Package::class);
    }
}
