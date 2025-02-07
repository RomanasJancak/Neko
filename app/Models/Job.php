<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\ApprovedPostalCodeArea;

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
    private $sameDayReturnAddOnPrice = 0;
    private $addon_food_price         =   0;

    private $bankHolidays = [
        ['date' => '2024-01-01', 'nameOfHoliday' => 'New Year’s Day'],
        ['date' => '2024-03-29', 'nameOfHoliday' => 'Good Friday'],
        ['date' => '2024-04-01', 'nameOfHoliday' => 'Easter Monday'],
        ['date' => '2024-05-06', 'nameOfHoliday' => 'Early May Bank Holiday'],
        ['date' => '2024-05-27', 'nameOfHoliday' => 'Spring Bank Holiday'],
        ['date' => '2024-08-26', 'nameOfHoliday' => 'Summer Bank Holiday'],
        ['date' => '2024-12-25', 'nameOfHoliday' => 'Christmas Day'],
        ['date' => '2024-12-26', 'nameOfHoliday' => 'Boxing Day'],
        ['date' => '2025-01-01', 'nameOfHoliday' => 'New Year’s Day'],
        ['date' => '2025-04-18', 'nameOfHoliday' => 'Good Friday'],
        ['date' => '2025-04-21', 'nameOfHoliday' => 'Easter Monday'],
        ['date' => '2025-05-05', 'nameOfHoliday' => 'Early May Bank Holiday'],
        ['date' => '2025-05-26', 'nameOfHoliday' => 'Spring Bank Holiday'],
        ['date' => '2025-08-25', 'nameOfHoliday' => 'Summer Bank Holiday'],
        ['date' => '2025-12-25', 'nameOfHoliday' => 'Christmas Day'],
        ['date' => '2025-12-26', 'nameOfHoliday' => 'Boxing Day'],
        ['date' => '2026-01-01', 'nameOfHoliday' => 'New Year’s Day'],
        ['date' => '2026-04-03', 'nameOfHoliday' => 'Good Friday'],
        ['date' => '2026-04-06', 'nameOfHoliday' => 'Easter Monday'],
        ['date' => '2026-05-04', 'nameOfHoliday' => 'Early May Bank Holiday'],
        ['date' => '2026-05-25', 'nameOfHoliday' => 'Spring Bank Holiday'],
        ['date' => '2026-08-31', 'nameOfHoliday' => 'Summer Bank Holiday'],
        ['date' => '2026-12-25', 'nameOfHoliday' => 'Christmas Day'],
        ['date' => '2026-12-26', 'nameOfHoliday' => 'Boxing Day'],
        ['date' => '2027-01-01', 'nameOfHoliday' => 'New Year’s Day'],
        ['date' => '2027-03-26', 'nameOfHoliday' => 'Good Friday'],
        ['date' => '2027-03-29', 'nameOfHoliday' => 'Easter Monday'],
        ['date' => '2027-05-03', 'nameOfHoliday' => 'Early May Bank Holiday'],
        ['date' => '2027-05-31', 'nameOfHoliday' => 'Spring Bank Holiday'],
        ['date' => '2027-08-30', 'nameOfHoliday' => 'Summer Bank Holiday'],
        ['date' => '2027-12-25', 'nameOfHoliday' => 'Christmas Day'],
        ['date' => '2027-12-26', 'nameOfHoliday' => 'Boxing Day'],
        ['date' => '2028-01-01', 'nameOfHoliday' => 'New Year’s Day'],
        ['date' => '2028-04-14', 'nameOfHoliday' => 'Good Friday'],
        ['date' => '2028-04-17', 'nameOfHoliday' => 'Easter Monday'],
        ['date' => '2028-05-01', 'nameOfHoliday' => 'Early May Bank Holiday'],
    ];
    
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
    public function getDropOffTasks(){
        $returnValue = [];
        foreach($this->tasks as $task){
            if($task->type() === 'dropOff'){
                $returnValue[] = $task;
            }
        }
        return $returnValue;
    }
    public function getReturnTask(){
        $returnValue = null;
        foreach($this->tasks as $task){
            if($task->type() === 'return'){
                $returnValue = $task;
            }
        }
        return $returnValue;
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
    // public function addOns()
    // {
    //     return $this->hasMany(AddOn::class, 'model_id')
    //                 ->where('model_type', '=', 'app/models/Job');
    // }
    public function calculateShortestRoute($start, $points, $end = null)
    {
        //dd(!$start);
        if(!$start){
            return [
                'distance' => 0,
                'route' => [],
            ];
        }
        $points = array_values($points); // Ensure points are indexed from 0
        $permutations = self::permute($points);
        $shortestDistance = PHP_INT_MAX;
        $shortestRoute = [];

        foreach ($permutations as $permutation) {
            $route = array_merge([$start], $permutation);
            if ($end) {
                $route[] = $end;
            }

            $distance = self::calculateTotalDistance($route);
            if ($distance < $shortestDistance) {
                $shortestDistance = $distance;
                $shortestRoute = $route;
            }
        }

        return [
            'distance' => $shortestDistance,
            'route' => $shortestRoute,
        ];
    }
    public function calculateTotalDistance($route)
    {
        $totalDistance = 0;
        for ($i = 0; $i < count($route) - 1; $i++) {
            $totalDistance += Distance::getDistance($route[$i]->fullAddress(), $route[$i + 1]->fullAddress());
        }
        return $totalDistance;
    }
    public function permute($items, $perms = [], &$result = [])
    {
        if (empty($items)) {
            $result[] = $perms;
        } else {
            for ($i = count($items) - 1; $i >= 0; --$i) {
                $newItems = $items;
                $newPerms = $perms;
                list($foo) = array_splice($newItems, $i, 1);
                array_unshift($newPerms, $foo);
                self::permute($newItems, $newPerms, $result);
            }
        }
        return $result;
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
            }else if($task->type() == 'return'){
                $return = $task;
            }
        }
        //dd($return->fullAddress());
        return $this->calculateShortestRoute($pickup, $dropOffs, $return);
    }
    public function price_distance(){
        $distance = $this->findShortestDistance()['distance']*0.0006213712;
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
    public function parseUkPostalCode($postalCode) {
        // Standardize the format by converting to uppercase (keep spaces intact)
        $postalCode = strtoupper($postalCode);
    
        // Updated regex pattern to account for spaces
        $pattern = '/^([A-Z]{1,2}) ?(\d{1,2}[A-Z]?)? ?(\d)? ?([A-Z]{2})?$/';
    
        if (preg_match($pattern, $postalCode, $matches)) {
            return [
                'area' => $matches[1] ?? null,      // Area: letters at the start
                'district' => $matches[2] ?? null, // District: numbers/letters after the area
                'sector' => $matches[3] ?? null,   // Sector: first number after the space
                'unit' => $matches[4] ?? null      // Unit: last two letters
            ];
        } else {
            // Return empty components for unparseable input
            return [
                'area' => null,
                'district' => null,
                'sector' => null,
                'unit' => null
            ];
        }
    }
    public function price_outsidePostalCodeZone(){
        $price = 0;
        $outOfZonePrice = $this->addOns_postalCode[0]['price'];
        $approvedPostalCodes = ApprovedPostalCodeArea::all();
        
        foreach($this->tasks as $task){
            $postalCode = strtoupper($task->postalCode());
            $postalCodeDecoded = $this->parseUkPostalCode($postalCode);
            $isInside = false;
            
            foreach ($approvedPostalCodes as $approvedPostalCode) {
                //echo(json_encode($postalCodeDecoded));
                $nameToUpper = strtoupper($approvedPostalCode->name);
                
                $approvedPostalCodeDecoded = $this->parseUkPostalCode($nameToUpper);
                switch ($approvedPostalCode->type) {
                    case 'district':
                        if($postalCodeDecoded['district'] === $approvedPostalCodeDecoded['district']){
                            if($postalCodeDecoded['area'] === $approvedPostalCodeDecoded['area']){
                                $isInside = true;
                            }
                        }
                        break;
                    case 'area':
                        if($postalCodeDecoded['area'] === $approvedPostalCodeDecoded['area']){
                            $isInside = true;
                        }
                        break;
                    case 'sector':
                        if($postalCodeDecoded['sector'] === $approvedPostalCodeDecoded['sector']){
                            if($postalCodeDecoded['district'] === $approvedPostalCodeDecoded['district']){
                                if($postalCodeDecoded['area'] === $approvedPostalCodeDecoded['area']){
                                    $isInside = true;
                                }
                            }
                        }
                        break;
                    case 'postalcode':
                        if ($postalCode === $nameToUpper) {
                            $isInside = true;
                        }
                        break;
                }
                if ($isInside) {
                    break;
                }
            }
            
            if (!$isInside) {
                //dd($task->postalCode());
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
        $totalWeight = 0;
        //$freeWeight = 20.00;
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
        $thresholds2  = $thresholds;
        $information = [];
        $totalWeight = $weight;
        if($weight < $freeWeight){
            $price_weight = 0;
        }else{
            $lastTreshold = array_pop($thresholds);
            $step = [];
            while($lastTreshold != null){
                while($weight >= $lastTreshold['threshold']){
                    if(($weight - $lastTreshold['charginStep']) < $lastTreshold['threshold']){
                        $weight = $lastTreshold['threshold'] - 0.000001;
                    }else{
                        $weight -= $lastTreshold['charginStep'];
                    }
                    $price_weight += $lastTreshold['price'];
                }
                $step[] = [
                    'lastThreshold' => $lastTreshold,
                    'if(($weight - $lastTreshold[`charginStep`]) `<` $lastTreshold[`threshold`])' => (($weight - $lastTreshold['charginStep']) < $lastTreshold['threshold']),
                    'priceSoFar' => $price_weight,
                    'weight' => $weight,
                ];
                $lastTreshold = array_pop($thresholds);
                $information[] = $step;
                $step  = [];
            }
        }
        return  [
                    'value' =>  $totalWeight,
                    'price' =>  $price_weight,
                    'freeWeight' => $freeWeight,
                    'thresholds' => $thresholds2,
                    'information' => $information,
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
    public function new_price_timing(){
        $pickup_price = null;
        $dropOff_price_array = [];
        $timeString = 0;
        $timeArray_pickup = [];
        $timeArray_dropoff = [];
        $pickupWindow = '';
        $dropoffWindow = [];
        // ================
        foreach($this->addOns_time as $addOn){
            if(preg_match('/time-pickup-window-([0-9.]+)/', $addOn['name'], $matches)){
                $timeValue  = (int)str_pad($matches[1], 4, '0', STR_PAD_LEFT);
                $timeArray_pickup[] = [
                    'value' => $timeValue,
                    'price' => $addOn['price'],
                ];
            }
        }
        usort($timeArray_pickup, function ($a, $b) {
            return $a['value'] - $b['value'];
        });
        foreach($this->addOns_time as $addOn){
            if(preg_match('/time-dropoff-window-([0-9.]+)/', $addOn['name'], $matches)){
                $timeValue  = (int)str_pad($matches[1], 4, '0', STR_PAD_LEFT);
                $timeArray_dropoff[] = [
                    'value' => $timeValue,
                    'price' => $addOn['price'],
                ];
            }
        }
        usort($timeArray_dropoff, function ($a, $b) {
            return $a['value'] - $b['value'];
        });
        // ================
        //echo count($this->tasks);
        //dd($dropOff_price_array);
        foreach($this->tasks as $task){
            //echo $task->type();
            if($task->type() ==='pickup'){
                //echo 'yes';
                
                $dif = Carbon::parse($task->timeWindowEnd())->diffInMinutes(Carbon::parse($task->timeWindowBegin()));
                $pickupWindow = $dif;
                //echo $dif;
                //echo $task->timeWindowEnd().' ';
                //echo $task->timeWindowBegin().' ';
                foreach ($timeArray_pickup as $item) {
                    if($dif <= $item['value']){

                        $pickup_price = $item['price'];

                        break;
                    }
                }
                if ($pickup_price === null) {
                    $pickup_price = end($timeArray_pickup)['price'];
                }
            }
            else if($task->type() ==='dropOff'){
                
                $dif = Carbon::parse($task->timeWindowEnd())->diffInMinutes(Carbon::parse($task->timeWindowBegin()));
                $dropoffWindow[] = $dif;
                foreach ($timeArray_dropoff as $item) {
                    if($dif <= $item['value']){
                        $dropOff_price_array[] = $item['price'];
                        break;
                    }
                }
                if ($dropOff_price_array === []) {
                    $dropOff_price_array[] = end($timeArray_dropoff)['price'];
                }
            }
        }
        $dropOff_price = 0;
        //dd($dropOff_price_array);
        foreach($dropOff_price_array as $price){
            $dropOff_price += $price;
        }
        return [
            //'tasks' => $this->tasks,
            'price' => $pickup_price+$dropOff_price,
            'pickup_price'  => $pickup_price,
            'dropOff_price'  => $dropOff_price,
            //'alltimeAddons' =>  $this->addOns_time,
            'timeArray_pickup'  => $timeArray_pickup,
            'timeArray_dropoff'  => $timeArray_dropoff,
            'pickup_value'  => $pickupWindow,
            'dropOff_value'  => $dropoffWindow,
        ];
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
                    $packages[$packageTypeId]['total_price'] += $task->package->packageType->price;
                    // $packages[$packageTypeId]['total_weight'] += $task->package->packageType->price * $task->package->quantity;
                } else {
                    // Otherwise, add the new package type to the array
                    $packages[$packageTypeId] = [
                        'id'    => $task->package->packageType->id,
                        'price' => $task->package->packageType->price,
                        'quantity' => $task->package->quantity,
                        'baseQuantityThreshold' => $task->package->packageType->baseQuantityThreshold,
                        'total_price' => $task->package->packageType->price, // Keep track of the total price for this package type
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
            //$price += $this->price_oversize;
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
    public function price_sameDayReturn(){
        $price = 0;
        $returnTask = $this->getReturnTask();
        if ($returnTask) {
            if(!$returnTask->return->is_flexible){
                $price = $this->sameDayReturnAddOnPrice;
            }
        }
        return ['price' => $price];
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
            if (strpos($addOn['name'], 'time-sameDayReturn') === 0) {
                $this->sameDayReturnAddOnPrice = $addOn['price'];
            }
            if (strpos($addOn['name'], 'package-food') === 0) {
                $this->addon_food_price = $addOn['price'];
            }
        }
    }
    public function price_sunday(){
        $sundayAddon;
        if(Carbon::parse($this->getDate())->dayOfWeek != 0){
            return [
                'price' => 0,
                'isApplicable' => false,
            ];
        }else{
            foreach($this->addOns_time as $addOn){
                if(strpos($addOn['name'], 'time-sunday') === 0){
                    $sundayAddon = $addOn;
                }
            }
        }

        return [
            'price' => $sundayAddon['price'],
            'isApplicable' => true,
        ];
    }
    public function price_bankHoliday(){
        $bankHolidayAddon;
        foreach($this->bankHolidays as $holiday){
            if($this->getDate() == $holiday['date']){
                foreach($this->addOns_time as $addOn){
                    if(strpos($addOn['name'], 'time-bankHoliday') === 0){
                        $bankHolidayAddon = $addOn;
                    }
                }
                return [
                    'price' => $bankHolidayAddon['price'],
                    'isApplicable' => true,
                ];
            }
        }
        return [
            'price' => 0,
            'isApplicable' => false,
        ];
    }
    public function price_food(){
        foreach($this->getDropOffTasks() as $task){
            if($task->package->packageType->extras->contains('name', 'food')){
                return [
                    'price' => $this->addon_food_price,
                    'isApplicable' => true,
                ];
            }
        }
        return [
            'price' => 0,
            'isApplicable' => false,
        ];
    }
    public function price(){
        $this->populateVariables();

        $price = 0;
        $price+=$this->price_food()['price'];
        $price+=$this->price_distance()['price'];
        $price+=$this->price_outsidePostalCodeZone();
        $price+=$this->price_weight()['price'];
        //$price+=$this->price_timing()['price'];
        $price+=$this->new_price_timing()['price'];
        $price+=$this->price_packages()['price'];
        $price+=$this->price_sunday()['price'];
        $price+=$this->price_bankHoliday()['price'];
        $price+=$this->oversizePrice();
        $price+=$this->price_sameDayReturn()['price'];
        $price+=$this->price_adjustment_number;

        return [
            'breakdownOfPrice' => [
                'price_distance'        =>  $this->price_distance()['price'],
                'price_outsidePostalCodeZone'   =>  $this->price_outsidePostalCodeZone(),
                'price_weight'          =>  $this->price_weight()['price'],
                'price_timing'          =>  $this->new_price_timing()['price'],
                'price_packages'        =>  $this->price_packages()['price'],
                'price_sunday'          =>  $this->price_sunday()['price'],
                'price_bankHoliday'     =>  $this->price_bankHoliday()['price'],
                'price_sameDayReturn'   =>  $this->price_sameDayReturn(),
                'oversizePrice'         =>  $this->oversizePrice(),
                'price_food'            =>  $this->price_food()['price'],
                'price_adjustment_number' => $this->price_adjustment_number,
                'price'                 =>  $this->price,
            ],
            'totalPrice'            =>  $price,
            'price_Distance'        =>  $this->price_distance(),
            'price_OutOfZone'       =>  $this->price_outsidePostalCodeZone(),
            'weight_price'          =>  $this->price_weight(),
            //'old_timing_price'          =>  $this->price_timing(),
            'price-packages' =>  $this->price_packages(),
            'price_oversize_added'  =>  $this->oversizePrice(),
            'price_oversize_value'  =>  $this->price_oversize,
            'price_package_oversize'        =>  $this->oversizePrice(),
            'timing_price'          =>  $this->new_price_timing(),
            'price_time_sunday'     =>  $this->price_sunday(),
            'price_time_bankholiday'     =>  $this->price_bankHoliday(),
            
        ];
    }
}