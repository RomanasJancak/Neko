<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\ApprovedPostalCodeArea;
use App\Services\FieldLockService;
use App\Services\JobPriceSnapshotService;
use App\Services\SettingsService;
use App\Services\JobPriceCalculator;

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
    /*
    private $bankHolidays = [
      '2024-01-01' => 'New Year’s Day',
      '2024-03-29' => 'Good Friday',
      '2024-04-01' => 'Easter Monday',
      '2024-05-06' => 'Early May Bank Holiday',
      '2024-05-27' => 'Spring Bank Holiday',
      '2024-08-26' => 'Summer Bank Holiday',
      '2024-12-25' => 'Christmas Day',
      '2024-12-26' => 'Boxing Day',
      '2025-01-01' => 'New Year’s Day',
      '2025-04-18' => 'Good Friday',
      '2025-04-21' => 'Easter Monday',
      '2025-05-05' => 'Early May Bank Holiday',
      '2025-05-26' => 'Spring Bank Holiday',
      '2025-08-25' => 'Summer Bank Holiday',
      '2025-12-25' => 'Christmas Day',
      '2025-12-26' => 'Boxing Day',
      '2026-01-01' => 'New Year’s Day',
      '2026-04-03' => 'Good Friday',
      '2026-04-06' => 'Easter Monday',
      '2026-05-04' => 'Early May Bank Holiday',
      '2026-05-25' => 'Spring Bank Holiday',
      '2026-08-31' => 'Summer Bank Holiday',
      '2026-12-25' => 'Christmas Day',
      '2026-12-26' => 'Boxing Day',
      '2027-01-01' => 'New Year’s Day',
      '2027-03-26' => 'Good Friday',
      '2027-03-29' => 'Easter Monday',
      '2027-05-03' => 'Early May Bank Holiday',
      '2027-05-31' => 'Spring Bank Holiday',
      '2027-08-30' => 'Summer Bank Holiday',
      '2027-12-25' => 'Christmas Day',
      '2027-12-26' => 'Boxing Day',
      '2028-01-01' => 'New Year’s Day',
      '2028-04-14' => 'Good Friday',
      '2028-04-17' => 'Easter Monday',
      '2028-05-01' => 'Early May Bank Holiday',
    ];
    */
    private $workShift_Begin    = "08:00:00";
    private $workShift_End      = "16:00:00";
    private $workingHours_minimum = "07:00:00";
    private $workingHours_maximum = "17:00:00";
    private $timeWindowSize_minimumJourneyTime = "00:15:00";
    protected $casts = [
        'date' => 'date',
    ];

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
        //'notes',
        'price',
        'date',
        'distance',
        'invoice_id',
        'price_adjustment_number',
        'invoice_item_id',
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
        return $this->invoiceItem ? $this->invoiceItem->invoice : null;
    }
    public function invoiceItem(){
        return $this->belongsTo(InvoiceItem::class, 'invoice_item_id');
    }
    public function tasks(){
        return $this->hasMany(Task::class)->orderBy('order_number');
    }
    public function jobPrices()
    {
        return $this->hasMany(JobPrice::class);
    }
    public function jobTemplate()
    {
        return $this->belongsTo(JobTemplate::class);
    }
    public function notes()
    {
        return $this->morphMany(Note::class, 'notable');
    }
    public function latestNote()
    {
        return $this->morphOne(Note::class, 'notable')->latestOfMany();
    }
    public function isNoteDifferentThanTemplateNote(){
        $latestNote = $this->latestNote;
        if($latestNote && $this->jobTemplate && $this->jobTemplate->notes){
            return $latestNote->content !== $this->jobTemplate->notes;
        }
        return false;
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
    public function getDropOffs(){
        $returnValue = [];
        foreach($this->tasks as $task){
            if($task->type() === 'dropOff'){
                $returnValue[] = $task->package;
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
    public function getPickupTask()
    {
        // Filter the already-loaded tasks collection in memory
        $pickupTask = $this->tasks->first(function ($task) {
            return $task->taskable_type === Pickuptask::class;
        });

        // Return the child model data directly
        return $pickupTask ? $pickupTask->taskable : null;
    }
    public function urlToLogo(){
      return asset("files/logos/{$this->clientToBill_id}.png");
    }
    public function getDate(){
        $returnValue = '';
        $task = $this->tasks->first();
        if ($task) {
            $returnValue = Carbon::parse($task->date)->format('Y-m-d');
        } else {
            $returnValue = '0000-00-00';
        }
        return $returnValue;
    }
    public function getBankHolidaysAttribute(){
        return collect(app(HolidayService::class)->getBankHolidays())->map(function ($name, $date) {
            return [
                'date' => $date,
                'name' => $name,
            ];
        })->values()->toArray();
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
    public function lockedFields()
    {
        return app(FieldLockService::class)->getLockedFields('job', (int) $this->id);
    }
    public function isLocked($fieldName)
    {
        return app(FieldLockService::class)->isLocked('job', (int) $this->id, (string) $fieldName);
    }
    public function changeLockedField($fieldName, $isLocked)
    {
        app(FieldLockService::class)->setLock('job', (int) $this->id, (string) $fieldName, (bool) $isLocked);
    }
    /* Unused function - kept for reference */  
    public function calculateShortestRoute($start, $points, $end = null)
    {
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
        return $this->calculateShortestRoute($pickup, $dropOffs, $return);
    }
    private function calculateDistanceBasedOnTasksOrder()
    {
        $tasks = $this->tasks()->orderBy('order_number')->get();
        $totalDistance = 0;
        //dd($tasks[0]);
        for ($i = 0; $i < $tasks->count() - 1; $i++) {
            $totalDistance += Distance::getDistance(
                $tasks[$i]->fullAddress(),
                $tasks[$i + 1]->fullAddress()
            );
        }

        return $totalDistance;
    }
    public function price_distance(){
        //$distance = $this->findShortestDistance()['distance']*0.0006213712;
        $distance = $this->calculateDistanceBasedOnTasksOrder()*0.0006213712;
        // Round distance down to 2 decimals
        $distance = floor($distance * 100) / 100;
        //dd($distance);
        $thresholds = [];
        
        foreach($this->addOns_distance as $addOn){
            if(preg_match('/threshold-([0-9.]+)-step-([0-9.]+)/', $addOn['name'], $matches)){
                $threshold = (float) $matches[1];
                $chargingStep = (float) $matches[2];
                if ($chargingStep <= 0) {
                    continue;
                }
                $thresholds[] = [
                    'threshold' => $threshold,
                    'price'     => $addOn['price'],
                    'charginStep'   =>  $chargingStep,
                ];
            }
        }
        //dd($thresholds);
        $returnDistance = $distance;
        if (empty($thresholds)) {
            return [
                'value' => $returnDistance,
                'price' => 0,
                '$freeMile' => 0,
            ];
        }

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
        //dd($returnDistance, $price_distance, $freeMile,$distance);
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
        if (empty($this->addOns_postalCode) || !isset($this->addOns_postalCode[0]['price'])) {
            return 0;
        }

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
                $threshold = (float) $matches[1];
                $chargingStep = (float) $matches[2];
                if ($chargingStep <= 0) {
                    continue;
                }
                $thresholds[] = [
                    'threshold' => $threshold,
                    'price'     => $addOn['price'],
                    'charginStep'   =>  $chargingStep,
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
        if (empty($thresholds)) {
            return [
                'value' => $weight,
                'price' => 0,
                'freeWeight' => 0,
                'thresholds' => [],
                'information' => [],
            ];
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
        //dd($timeArray_pickup);
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
    public function price_bankHoliday(): array 
    {
        $jobDate = $this->getDate();        
        if (!app(\App\Services\HolidayService::class)->isBankHoliday($jobDate)) {
            return [
                'price' => 0,
                'isApplicable' => false,
            ];
        }
        foreach ($this->addOns_time as $addOn) {
            if (str_starts_with($addOn['name'], 'time-bankHoliday')) {
                return [
                    'price' => $addOn['price'],
                    'isApplicable' => true,
                ];
            }
        }
        return [
            'price' => 0,
            'isApplicable' => true,
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
    public function isInvoiced(): bool
    {
        return !is_null($this->invoice_item_id);
    }

    public function isCompletedAndPastInvoiceLockDate(): bool
    {
        $statusName = strtolower((string) ($this->status->name ?? ''));
        if ($statusName !== 'completed') {
            return false;
        }

        $invoiceDate = $this->invoiceItem?->invoice?->invoice_date;
        if (empty($invoiceDate)) {
            return false;
        }

        $lockDays = (int) (app(SettingsService::class)->get('global.invoiceLockDays') ?? 1);

        return Carbon::today()->gt(Carbon::parse($invoiceDate)->addDays($lockDays)->startOfDay());
    }

    public function isLockedForUser(?User $user = null): bool
    {
        if (!$this->isCompletedAndPastInvoiceLockDate()) {
            return false;
        }

        if ($user && $user->isAdminOrSuperAdmin()) {
            return false;
        }

        return true;
    }

    private function guardAgainstInvoicedPriceMutation(): void
    {
        if ($this->isInvoiced()) {
            throw new \DomainException('Cannot recalculate price for invoiced job.');
        }
    }

    public function recalculatePrice(){
        $this->guardAgainstInvoicedPriceMutation();
        return $this->price();
    }
    public function price(){
        if($this->isInvoiced()){
            return [
                'breakdownOfPrice' => json_decode($this->price_snapshot, true),
                'totalPrice' => $this->price,
            ];
        }
        $this->populateVariables();
        $priceCalculator = new JobPriceCalculator($this);
        $calculatorPriceArray = $priceCalculator->calculate();
        $price = 0;
        //dd($this->price_packages());
        $price+=$this->price_food()['price'];
        $price+=$this->price_distance()['price'];
        $price+=$this->price_outsidePostalCodeZone();
        $price+=$this->price_weight()['price'];
        $price+=$this->new_price_timing()['price'];
        //$price+=$this->price_packages()['price'];
        $price+=$calculatorPriceArray['breakdown']['price_packages'];
        $price+=$this->price_sunday()['price'];
        $price+=$this->price_bankHoliday()['price'];
        //$price+=$this->oversizePrice();
        $price+=$calculatorPriceArray['breakdown']['oversizePrice'];
        $price+=$this->price_sameDayReturn()['price'];
        $price+=$this->price_adjustment_number;
        $this->price = $price;
        $this->save();
        $returnArray = [
            'breakdownOfPrice' => [
                'price_distance'        =>  $this->fixed_price === 0?$this->price_distance()['price']:0,
                'price_outsidePostalCodeZone'   =>  $this->fixed_price === 0?$this->price_outsidePostalCodeZone():0,
                'price_weight'          =>  $this->fixed_price === 0?$this->price_weight()['price']:0,
                'price_timing'          =>  $this->fixed_price === 0?$this->new_price_timing()['price']:0,
                'price_packages'        =>  $this->fixed_price === 0?$this->price_packages()['price']:0,
                'price_sunday'          =>  $this->fixed_price === 0?$this->price_sunday()['price']:0,
                'price_bankHoliday'     =>  $this->fixed_price === 0?$this->price_bankHoliday()['price']:0,
                'price_sameDayReturn'   =>  $this->fixed_price === 0?$this->price_sameDayReturn():0,
                'oversizePrice'         =>  $this->fixed_price === 0?$calculatorPriceArray['breakdown']['oversizePrice']:0,
                'price_food'            =>  $this->fixed_price === 0?$this->price_food()['price']:0,
                'price_adjustment_number' => $this->fixed_price === 0?$this->price_adjustment_number:0,
                'price'                 =>  $this->fixed_price === 0?$this->price:$this->fixed_price,
                'fixed_price'           =>  $this->fixed_price === 0,
            ],
            'totalPrice'            =>  $this->fixed_price === 0?$price:0,
            'price_Distance'        =>  $this->fixed_price === 0?$this->price_distance():0,
            'price_OutOfZone'       =>  $this->fixed_price === 0?$this->price_outsidePostalCodeZone():0,
            'weight_price'          =>  $this->fixed_price === 0?$this->price_weight():0,
            'price-packages' =>  $this->fixed_price === 0?[
                                                            'price' => $calculatorPriceArray['breakdown']['price_packages'],
                                                            'packages' => $this->price_packages()['packages'],
                                                            'oversize' => $calculatorPriceArray['breakdown']['oversizePrice'],
                                                            ]:0,
            'price_oversize_added'  =>  $this->fixed_price === 0?$calculatorPriceArray['breakdown']['oversizePrice']:0,
            'price_oversize_value'  =>  $this->fixed_price === 0?$this->price_oversize:0,
            'price_package_oversize'        =>  $this->fixed_price === 0?$this->oversizePrice():0,
            'timing_price'          =>  $this->fixed_price === 0?$this->new_price_timing():0,
            'price_time_sunday'     =>  $this->fixed_price === 0?$this->price_sunday():0,
            'price_time_bankholiday'     =>  $this->fixed_price === 0?$this->price_bankHoliday():0,
            
        ];
        if (!$this->invoice_item_id) {
            app(JobPriceSnapshotService::class)->persistLatestSnapshot($this, $returnArray);
        }else{
            //app(JobPriceSnapshotService::class)->deleteSnapshot($this);
        }
        //dd($returnArray,$calculatorPriceArray);
        return $returnArray;
    }
}