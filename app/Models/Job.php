<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
    protected $fillable = [
        'eilesNumeris'.
        'courrier_id',
        'sender_id',
        'receiver_id',
        'pickup_time_begin',
        'pickup_time_end',
        'dropoff_time_begin',
        'dropoff_time_end',
        'status_id',
        'collection_details',
        'dropoff_details',

    ];
    public function status(){
        return $this->belongsTo(Status::class,'status_id');
    }
    public function clientToBill()
    {
        return $this->belongsTo(Client::class, 'clientToBill_id');
    }
    public function sender()
    {
        return $this->belongsTo(Client::class, 'sender_id');
    }
    public function receiver()
    {
        
        return $this->belongsTo(Client::class, 'receiver_id');
    }

    public function courier()
    {
        return $this->belongsTo(User::class, 'courrier_id');
    }
    public function packages(){
        return $this->hasMany(Package::class); 
    }
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
    public function pickupAddressShort(){
        return $this->pickupclientaddressline.' '.$this->pickupclientpostalcode;
    }
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
    public function tasks(){
        return $this->hasMany(Task::class)->orderBy('order_number');
    }
    public function addOns()
    {
        return $this->hasMany(AddOn::class, 'model_id')
                    ->where('model_type', '=', 'app/models/Job');
    }
    public function price(){
        $price = 0;
        $distance = 0;
        $originAddress = $this->pickupclientcountry.' '.$this->pickupclientcity.' '.$this->pickupclientpostalcode.' '.$this->pickupclientaddressline;
        foreach($this->packages as $key => $package){
            foreach($package->addOns as $addOn){
                $price+=$addOn->price;
            }
            $address= $package->dropoff_country.' '.$package->dropoff_city.' '.$package->dropoff_postal_code.' '.$package->dropoff_adress_line;
            if($key === 0){
                $distance+=Distance::getDistance($originAddress,$address);
                $originAddress = $address;
            }else{
                $distance+=Distance::getDistance($originAddress,$address);
            }
        }
        foreach($this->addOns as $addon){
            $price+=$addon->price;
        }
        return $price;
    }
}