<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Job extends Model
{
    use HasFactory;

    static $snakeAttributes = false;

    private $addOns             =   [];
    private $addOns_distance    =   [];
    private $addOns_weight      =   [];
    private $addOns_time        =   [];
    private $addOns_postalCode  =   [];
    private $packages_zero      =   [];
    private $price_oversize     =   0;
    private $price_oversize_status     =   0;
    
    private $workShift_Begin    = "08:00:00";
    private $workShift_End      = "16:00:00";
    private $workingHours_minimum = "07:00:00";
    private $workingHours_maximum = "17:00:00";
    private $timeWindowSize_minimumJourneyTime = "00:15:00";
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
    public function group(){
        return $this->belongsTo(Group::class, 'group_id');
    }
    public function invoice(){
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
        //=============TBD FIX SHORTEST PATH ==== Dabar tiesiog is eiles sudeda
        /*
        $distanceToNearestDropOff = PHP_INT_MAX;
        
        foreach($dropOffs as $dropOff){
            $distance = Distance::getDistance($pickup->fullAddress(),$dropOff->fullAddress());
            if($distance < $distanceToNearestDropOff){
                $distanceToNearestDropOff = $distance;
            }
        }
        */
        $tempDropOffs = $dropOffs;
        $distanceToNearestDropOff = 0;
        $lastPoint = $pickup;
        foreach($dropOffs as $dropOff){
            $distance = Distance::getDistance($lastPoint->fullAddress(),$dropOff->fullAddress());
            $lastPoint = $dropOff;
            $distanceToNearestDropOff+= $distance;    
        } 
        return $distanceToNearestDropOff;
    }
    public function price_distance(){
        $distance = $this->findShortestDistance()*0.0006213712;
        $thresholds = [];
        
        foreach($this->addOns_distance as $addOn){
            if(preg_match('/threshold-([0-9.]+)-step-([0-9.]+)/', $addOn['name'], $matches)){
                $thresholds[] = [
                    'threshold' => $matches[1],
                    'price'     => $addOn['price'],
                    'charginStep'   =>  $matches[2],
                ];
            }
        }
        $returnDistance = $distance;
        usort($thresholds, function ($a, $b) {
            return $a['threshold'] <=> $b['threshold'];
        });
        $freeMile = $thresholds[0]['threshold'];
        $price_distance = 0;
        if($distance < $freeMile){
            $price_distance = 0;
        }else{
            $lastTreshold = array_pop($thresholds);
            while($lastTreshold != null){
                while($distance > $lastTreshold['threshold']){
                    if(($distance - $lastTreshold['charginStep']) < $lastTreshold['threshold']){
                        $distance = $lastTreshold['threshold'] - 0.000001;
                    }else{
                        $distance -= $lastTreshold['charginStep'];
                    }
                    $price_distance += $lastTreshold['price'];
                }
                $lastTreshold = array_pop($thresholds);
            }
        }

        return [
            'value' =>  $returnDistance,
            'price' =>  $price_distance,
            '$freeMile' => $freeMile,
        ];
    }
    private function isPostalCodeInsideList($postalCode,$list){
        $firstPart = strtoupper(explode(' ', $postalCode)[0]);
        return in_array($firstPart, $list);
    }
    public function price_outsidePostalCodeZone(){
        $price = 0;
        $outOfZonePrice = $this->addOns_postalCode[0]['price'];
        $postalCodes = [
                        'N1','N4','N5','N7','N16','N19',
                        'W1',
                        'NW','NW5',
                        'WC1','WC2',
                        'EC1','EC2','EC3','EC4',
                        'E1','E2','E5','E8','E9',];
        foreach($this->tasks as $task){
            if(!$this->isPostalCodeInsideList($task->postalCode(),$postalCodes)){
                $price = $outOfZonePrice;
                break;
            }
        }
        return $price;                
    }
    public function price_weight(){
        $thresholds = [];
        $price = 0;
        $price_weight = 0;
        $dropOffs = [];
        $weight = 0;
        $freeWeight = 20.00;
        foreach($this->addOns_weight as $addOn){
            if(preg_match('/threshold-([0-9.]+)-step-([0-9.]+)/', $addOn['name'], $matches)){
                $thresholds[] = [
                    'threshold' => $matches[1],
                    'price'     => $addOn['price'],
                    'charginStep'   =>  $matches[2],
                ];
            }
        }
        foreach($this->tasks as $task){
            if($task->type() == 'dropOff'){
                $dropOffs[] = $task;
            }
        }
        foreach($dropOffs as $dropOff){
            $weight+=$dropOff->package->weight;
        }
        usort($thresholds, function ($a, $b) {
            return $a['threshold'] <=> $b['threshold'];
        });
        $freeWeight  = $thresholds[0]['threshold'];
        if($weight < $freeWeight){
            $price_weight = 0;
        }else{
            $lastTreshold = array_pop($thresholds);
            while($lastTreshold != null){
                while($weight > $lastTreshold['threshold']){
                    if(($weight - $lastTreshold['charginStep']) < $lastTreshold['threshold']){
                        $weight = $lastTreshold['threshold'] - 0.000001;
                    }else{
                        $weight -= $lastTreshold['charginStep'];
                    }
                    $price_weight += $lastTreshold['price'];
                }
                $lastTreshold = array_pop($thresholds);
            }
        }
        return  [
                    'value' =>  $weight,
                    'price' =>  $price_weight,
                    'freeWeight' => $freeWeight,
                ];
    }
    public function convertToCarbonTime($timeString) {
        // Pad the time string to ensure it's at least 4 characters long (e.g., "900" becomes "0900")
        $timeString = str_pad($timeString, 4, '0', STR_PAD_LEFT);
        
        // Extract the hours and minutes from the string
        $hours = substr($timeString, 0, -2); // First part for hours
        $minutes = substr($timeString, -2);  // Last two characters for minutes
    
        // Create a Carbon instance for today's date with the extracted time
        return Carbon::createFromTime($hours, $minutes);
    }
    private function calculateOverlap(Carbon $shiftStart, Carbon $shiftEnd, Carbon $eventStart, Carbon $eventEnd){
        $shiftStart = $shiftStart->setDate(1970, 1, 1);
        $shiftEnd = $shiftEnd->setDate(1970, 1, 1);
        $eventStart = $eventStart->setDate(1970, 1, 1);
        $eventEnd = $eventEnd->setDate(1970, 1, 1);
        // Calculate the overlap in minutes using min and max

        if ($eventEnd <= $shiftStart || $eventStart >= $shiftEnd) {
            // No overlap
            return 0;
        }
        $overlapMinutes = max(0, min($shiftEnd, $eventEnd)->diffInMinutes(max($shiftStart, $eventStart)));
    
        // Convert the overlap to decimal hours
        $overlapHours = $overlapMinutes / 60;
    
        // Return the overlap in decimal hours
        return round($overlapHours, 2);  // Rounded to 2 decimal places
        //return max($shiftStart, $eventStart);
        //return min($shiftEnd, $eventEnd);
        //return min($shiftEnd, $eventEnd)->diffInMinutes(max($shiftStart, $eventStart));
        
    }
    public function price_timing(){
        $pickup_normal_begin    =   ''; $pickup_normal_end = '';
        $dropoff_normal_begin   =   ''; $dropoff_normal_end = '';
        $pickup_max_begin    =   ''; $pickup_max_end = '';
        $dropoff_max_begin   =   ''; $dropoff_max_end = '';
        $normalworktime_cof =   ''; $maxworktime_cof    =   '';

        
        foreach($this->addOns_time as $addOn){
            if(preg_match('/time-normalworktime-pickup-begin-([0-9.]+)/', $addOn['name'], $matches)){
                $timeString = str_pad($matches[1], 4, '0', STR_PAD_LEFT);
                $pickup_normal_begin = Carbon::createFromTime(substr($timeString, 0, 2), substr($timeString, 2, 2));
            }
            if(preg_match('/time-normalworktime-pickup-end-([0-9.]+)/', $addOn['name'], $matches)){
                $timeString = str_pad($matches[1], 4, '0', STR_PAD_LEFT);
                $pickup_normal_end = Carbon::createFromTime(substr($timeString, 0, 2), substr($timeString, 2, 2));
            }
            if(preg_match('/time-normalworktime-dropoff-begin-([0-9.]+)/', $addOn['name'], $matches)){
                $timeString = str_pad($matches[1], 4, '0', STR_PAD_LEFT);
                $dropoff_normal_begin = Carbon::createFromTime(substr($timeString, 0, 2), substr($timeString, 2, 2));
            }
            if(preg_match('/time-normalworktime-dropoff-end-([0-9.]+)/', $addOn['name'], $matches)){
                $timeString = str_pad($matches[1], 4, '0', STR_PAD_LEFT);
                $dropoff_normal_end = Carbon::createFromTime(substr($timeString, 0, 2), substr($timeString, 2, 2));
            }

            if(preg_match('/time-maxworktime-pickup-begin-([0-9.]+)/', $addOn['name'], $matches)){
                $timeString = str_pad($matches[1], 4, '0', STR_PAD_LEFT);
                $pickup_max_begin = Carbon::createFromTime(substr($timeString, 0, 2), substr($timeString, 2, 2));
            }
            if(preg_match('/time-maxworktime-pickup-end-([0-9.]+)/', $addOn['name'], $matches)){
                $timeString = str_pad($matches[1], 4, '0', STR_PAD_LEFT);
                $pickup_max_end = Carbon::createFromTime(substr($timeString, 0, 2), substr($timeString, 2, 2));
            }
            if(preg_match('/time-maxworktime-dropoff-begin-([0-9.]+)/', $addOn['name'], $matches)){
                $timeString = str_pad($matches[1], 4, '0', STR_PAD_LEFT);
                $dropoff_max_begin = Carbon::createFromTime(substr($timeString, 0, 2), substr($timeString, 2, 2));
            }
            if(preg_match('/time-maxworktime-dropoff-end-([0-9.]+)/', $addOn['name'], $matches)){
                $timeString = str_pad($matches[1], 4, '0', STR_PAD_LEFT);
                $dropoff_max_end = Carbon::createFromTime(substr($timeString, 0, 2), substr($timeString, 2, 2));
            }
            if(preg_match('/time-normalworktime-cof/', $addOn['name'], $matches)){//time-normalworktime-cof
                $normalworktime_cof = $addOn['price'];
            }
            if(preg_match('/time-maxworktime-cof/', $addOn['name'], $matches)){
                $maxworktime_cof = $addOn['price'];
            }

        }
        $pickup_price = 0;
        $dropOff_price = 0;
        $dropOff_price = 0;
        $overlap = 0;
        $outsideNormalWh = 0;
        $window = '';
        foreach($this->tasks as $task){
            if($task->type() ==='pickup'){
                $overlap =  $this->calculateOverlap($pickup_normal_begin,$pickup_normal_end,Carbon::parse($task->timeWindowBegin()),Carbon::parse($task->timeWindowEnd()));
                if($overlap < (($pickup_normal_begin->diffInMinutes($pickup_normal_end))/60)){
                    $window = (Carbon::parse($task->timeWindowEnd())->diffInMinutes(Carbon::parse($task->timeWindowBegin())))/60;
                    $outsideNormalWh = $window - $overlap;
                    $totalHours = $outsideNormalWh + $overlap;
                    $pickup_price = ($normalworktime_cof/100)/$window+$outsideNormalWh*($maxworktime_cof/100);
                }else{
                    $pickup_price = 0;
                }
            }
            else if($task->type() ==='dropOff'){
                $overlap =  $this->calculateOverlap(
                    $dropoff_normal_begin,$dropoff_normal_end,
                    Carbon::parse($task->timeWindowBegin()),Carbon::parse($task->timeWindowEnd()));
                if($overlap < (($dropoff_normal_begin->diffInMinutes($dropoff_normal_end))/60)){
                    $window = (Carbon::parse($task->timeWindowEnd())->diffInMinutes(Carbon::parse($task->timeWindowBegin())))/60;
                    $outsideNormalWh = $window - $overlap;
                    ////
                    $dropOff_price += ($normalworktime_cof/100)/$window+$outsideNormalWh*($maxworktime_cof/100);
                    ////
                }else{

                }
            }
        }
        return [
            'price' => ($pickup_price+$dropOff_price)*100,
            'pickup_price'  => $pickup_price*100,
            'dropOff_price'  => $dropOff_price*100,
            'normalworktime_cof' => $normalworktime_cof,
            '$maxworktime_cof' => $maxworktime_cof,
        ];
    }
    public function price_packages(){
        $price = 0;
        $packages = [];
    
        // Step 1: Loop through tasks and accumulate packages by type (id)
        foreach ($this->tasks as $task) {
            if ($task->type() === 'dropOff') {
                $packageTypeId = $task->package->packageType->id;
    
                // If the package type already exists in the array, accumulate its quantity
                if (isset($packages[$packageTypeId])) {
                    $packages[$packageTypeId]['quantity'] += $task->package->quantity;
                    $packages[$packageTypeId]['total_price'] += $task->package->packageType->price * $task->package->quantity;
                } else {
                    // Otherwise, add the new package type to the array
                    $packages[$packageTypeId] = [
                        'id'    => $task->package->packageType->id,
                        'price' => $task->package->packageType->price,
                        'quantity' => $task->package->quantity,
                        'baseQuantityThreshold' => $task->package->packageType->baseQuantityThreshold,
                        'total_price' => $task->package->packageType->price * $task->package->quantity, // Keep track of the total price for this package type
                    ];
                }
            }
        }
    
        $oversize = false;
        $tempPrice = 0;
    
        // Step 2: Check if any of the total quantities exceed the base threshold and calculate total price
        foreach ($packages as $package) {
            if ($package['quantity'] > $package['baseQuantityThreshold']) {
                $oversize = true;
                $this->price_oversize_status = true;
            }
            $tempPrice += $package['total_price']; // Add the total price for this package type
        }
    
        // Step 3: Add oversize price if any package exceeds baseQuantityThreshold
        if ($oversize) {
            $price += $this->price_oversize;
        }
    
        // Step 4: Add the base price of all packages
        $price += $tempPrice;
    
        // Step 5: Return the calculated price
        return [
            'price' => $price,
            'packages'  =>  $packages,
            'oversize'  => $oversize,
        ];
    }
    public function oversizePrice(){
        if($this->price_oversize_status){
            return $this->price_oversize;
        }
        return 0;
    }
    public function populateVariables(){

        $this->addOns = json_decode(AddOnRule::getAllThatAreApplicableToThisDateForSpecificClient($this->date,$this->clientToBill->id),true);
        foreach ($this->addOns as $addOn) {
            if (strpos($addOn['name'], 'distance-') === 0) {
                $this->addOns_distance[] = $addOn;
            }
            if (strpos($addOn['name'], 'weight-') === 0) {
                $this->addOns_weight[] = $addOn;
            }
            if (strpos($addOn['name'], 'time-') === 0) {
                $this->addOns_time[] = $addOn;
            }
            if (strpos($addOn['name'], 'postalcodes-') === 0) {
                $this->addOns_postalCode[] = $addOn;
            }
            if (strpos($addOn['name'], 'package-oversize') === 0) {
                $this->price_oversize = $addOn['price'];
            }
        }
    }
    public function price(){
        $this->populateVariables();

        $price = 0;
        $price+=$this->price_distance()['price'];
        $price+=$this->price_outsidePostalCodeZone();
        $price+=$this->price_weight()['price'];
        $price+=$this->price_timing()['price'];
        $price+=$this->price_packages()['price'];
        //$price+=$this->price_distance();
        //$price+=$this->price_outsidePostalCodeZone();//TBD
        return [
            //'test'              =>  (strpos($this->addOns[0]['name'], 'postalcodes-') === 0),
            //  'addOns'            =>  $this->addOns,
            // 'addOns_distance'   =>  $this->addOns_distance,
            //'addOns_weight'     =>  $this->addOns_weight,
            //'addOns_time'       =>  $this->addOns_time,
            //'addOns_postalCode' =>  $this->addOns_postalCode,
            'totalPrice'            =>  $price,
            'price_Distance'        =>  $this->price_distance(),
            'price_OutOfZone'       =>  $this->price_outsidePostalCodeZone(),
            'weight_price'          =>  $this->price_weight(),
            'timing_price'          =>  $this->price_timing(),
            'price-package_type&qt' =>  $this->price_packages(),
            'price_oversize_added'  =>  $this->oversizePrice(),
            'price_oversize_value'  =>  $this->price_oversize,

        ];
    }
}