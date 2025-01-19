<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageType extends Model
{
    use HasFactory;

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_package_types')->withTimestamps();
    }
    public function packages(){
        
        return $this->hasMany(Package::class);
    }
    public function addOnRules()
    {
    return $this->belongsToMany(AddOnRule::class)->withTimestamps();
    }
    public function extras()
    {
        return $this->hasMany(Extra::class, 'model_id')
                    ->where('model_type', '=', self::class)
                    ;
    }
    public function extraTypes()
    {
        return ExtraTypes::where('model_type', '=', self::class)->get();
    }
    public function hasWeight()
    {
        return $this->extras()->where('extra_type_id', 2)->exists();
    }
}
