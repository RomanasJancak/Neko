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
        return $this->belongsToMany(PackageType::class,'client_package_types')->withTimestamps();
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
    public function getAllAddresses(){
        return $this->hasMany(Address::class,'model_id')->where('model','App\Models\Client')->get();
    }
    public function getPickupAddress(){
        return $this->hasOne(Address::class,'model_id')->where('model','App\Models\Client')->where('type','pickup')->get();
    }
    public function getDropoffAddress(){
        return $this->hasOne(Address::class,'model_id')->where('model','App\Models\Client')->where('type','dropoff')->get();
    }
    public function getBillingAddress(){
        return $this->hasOne(Address::class,'model_id')->where('model','App\Models\Client')->where('type','billing')->get();
    }
    public function getInvoiceAddress(){
        return $this->hasOne(Address::class,'model_id')->where('model','App\Models\Client')->where('type','invoice')->get();
    }
    public function getAddressesByType($type){
        return $this->hasMany(Address::class,'model_id')->where('model','App\Models\Client')->where('type',$type)->get();
    }
    public function addNewAddress($address){
        $address->model = 'App\Models\Client';
        $address->model_id = $this->id;
        $address->save();
        return $address;
    }
    public function createAndAddNewAddress($id = false,$name,$type,$address_line_1,$address_line_2,$postalCode,$city,$country,){
        if($id){
            $address = Address::find($id);
            if (!$address) {
            $address = new Address();
            }
        }else{    
            $address = new Address();
        }
        
        $address->name = $name;
        //$address->type = $type;
        $address->type = 'all';
        $address->address_line_1 = $address_line_1;
        $address->address_line_2 = $address_line_2;
        $address->city = $city;
        $address->country = $country;
        if(!isset($address->postalCode)){
            $address->addNewPostalCode($postalCode);
        }elseif($address->postalCode->postal_code != $postalCode){
            $address->postalCode->delete();
            $address->addNewPostalCode($postalCode);
        }
        return $this->addNewAddress($address);
    }
}