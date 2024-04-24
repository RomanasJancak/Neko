<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{

    use HasFactory;

    protected $fillable = [ 'name',
                            'number',
                            'client_user',
                            'balance',
                            'paid_to_date',
                            'client_currency',
                            'website',
                            'private_notes',
                            'client_phone',
                            'address_line',
                            'postal_code',
                            'city',
                            'country',
                            'pickup_adress_line',
                            'pickup_postal_code',
                            'pickup_city',
                            'pickup_country',
                            'email',
                            'vat','regNumber','address','note'];
    public function packageTypes()
    {
        return $this->belongsToMany(PackageType::class,'client__package_types')->withTimestamps();
    }
    public function addOnRules()
    {
        return $this->belongsToMany(AddOnRule::class,'client_add_on_rules')->withTimestamps();
    }
    public function jobs(){
        return $this->hasmany(Job::class,'clientToBill_id');
    }
    public function shortenedNameWithoutterPostalCode(){
        return $this->shortenedName;
    }
}