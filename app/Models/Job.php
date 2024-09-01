<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Job extends Model
{
    use HasFactory;

    static $snakeAttributes = false;

    protected $fillable = [
        'eilesNumeris',
        'courrier_id',
        'status_id',
        'clientToBill_id',
        'pickup_time_begin',
        'pickup_time_end',
        'pickupclientname',
        'pickupclientaddressline',
        'pickupclientcity',
        'pickupclientcountry',
        'pickupclientpostalcode',
        'manager_id',
        'notes',
        'price',
        'distance',
        'invoice_id',
        'price_adjustment_number',
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
    public function getPickupTask(){
        $returnValue = null;
        foreach($this->tasks as $task){
            if($task->type() === 'pickup'){
                $returnValue = $task->pickup;
            }
        }
        return $returnValue;
    }
    public function urlToLogo(){
        return asset('files/logos/'.$this->clientToBill->id.".png");
    }
    public function getDate(){
        $returnValue = '';
        $task = $this->tasks()->first();
        if ($task) {
            $returnValue = Carbon::parse($task->date)->format('Y-m-d');
        } else {
            $returnValue = '0000-00-00';
        }
        return $returnValue;
    }
    public function hasReturn()
    {
        $tasks = $this->tasks;
        foreach($tasks as $task){
            if(isset($task->return)){
                return true;
            }
        }
        return false;
    }
    public function addOns()
    {
        return $this->hasMany(AddOn::class, 'model_id')
                    ->where('model_type', '=', 'app/models/Job');
    }
    public function findShortestDistance(){
        $pickup = '';
        $dropOffs = [];
        $return = '';
        foreach($this->tasks as $task){
            if($task->type() == 'pickup'){
                $pickup = $task;
            }else if($task->type() == 'dropOff'){
                $dropOffs[] = $task;
            }else if($task->type() == 'picreturnkup'){
                $return = $task;
            }
        }
        $distanceToNearestDropOff = PHP_INT_MAX;
        foreach($dropOffs as $dropOff){
            $distance = Distance::getDistance($pickup->fullAddress(),$dropOff->fullAddress());
            if($distance < $distanceToNearestDropOff){
                $distanceToNearestDropOff = $distance;
            }
        }
        return $distanceToNearestDropOff;
    }
    public function distancePrice(){
        $distance = $this->findShortestDistance()*0.0006213712;
        $freeMile = 1;
        $tresholds = [
            [
                'treshold'      => 1,
                'price'         => 100,
                'charginStep'   =>  0.5
            ],
            [
                'treshold'      => 5,
                'price'         => 150,
                'charginStep'   =>  0.5
            ],[
                'treshold'  => 6,
                'price'     => 300,
                'charginStep'   =>  1
            ]
        ];
        $price_distance = 0;
        if($distance < 1){
            $price_distance = 0;
        }else{
            $lastTreshold = array_pop($tresholds);
            while($lastTreshold != null){
                while($distance > $lastTreshold['treshold']){
                    if(($distance - $lastTreshold['charginStep']) < $lastTreshold['treshold']){
                        $distance = $lastTreshold['treshold'] - 0.000001;
                    }else{
                        $distance -= $lastTreshold['charginStep'];
                    }
                    $price_distance += $lastTreshold['price'];
                }
                $lastTreshold = array_pop($tresholds);
            }
        }

        return $price_distance;
    }
    public function outsidePostalCodeZone(){
        $postalCodes = [
                        'N1','N4','N5','N7','N16','N19',
                        'W1',
                        'NW','NW5',
                        'WC1','WC2',
                        'EC1','EC2','EC3','EC4',
                        'E1','E2','E5','E8','E9',];
                        
    }
    public function price(){
        $price = 0;
        $price+=$this->distancePrice();
        return $price;
    }
}