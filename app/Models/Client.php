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
                            'phone',
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
    public function isSameAsPickupAdress($address){
        $pickupAdressString = $this->pickup_adress_line.' '.$this->pickup_postal_code.' '.$this->pickup_city.' '.$this->pickup_country;
        $normalize = function($str) {
            $str = strtolower($str); // Convert to lowercase
            $str = preg_replace('/\s+/', ' ', $str); // Replace multiple spaces with a single space
            $str = preg_replace('/[^a-z0-9\s]/', '', $str); // Remove punctuation
            $words = explode(' ', $str); // Split into words
            sort($words); // Sort the words alphabetically
            return $words;
        };
        $normalizedStr1 = $normalize($pickupAdressString);
        $normalizedStr2 = $normalize($address);
        return $normalizedStr1 === $normalizedStr2;
    }
}