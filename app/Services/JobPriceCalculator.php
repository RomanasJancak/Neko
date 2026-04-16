<?php

namespace App\Services;

use App\Models\Job;
use App\Models\AddOn;
use App\Models\AddOnRule;
use App\Models\JobPrice;

class JobPriceCalculator
{
    protected Job $job;
    protected float $price = 0;
    protected array $breakdown = [];
    protected $rules ;

    public function __construct(Job $job)
    {
        $this->job = $job;
        $this->job->populateVariables();
        $this->rules = json_decode(AddOnRule::getAllThatAreApplicableToThisDateForSpecificClient($this->job->date,$this->job->clientToBill->id),true);
    }

    public function calculate(): array
    {
        $this->add('price_food', $this->price_food()['price']);
        $this->add('price_distance', $this->job->price_distance()['price']);
        $this->add('price_outsidePostalCodeZone', $this->job->price_outsidePostalCodeZone());
        $this->add('price_weight', $this->job->price_weight()['price']);
        $this->add('price_timing', $this->job->new_price_timing()['price']);
        $this->add('price_packages', $this->job->price_packages()['price']);
        $this->add('price_sunday', $this->job->price_sunday()['price']);
        $this->add('price_bankHoliday', $this->job->price_bankHoliday()['price']);
        $this->add('oversizePrice', $this->job->oversizePrice());
        $this->add('price_sameDayReturn', $this->job->price_sameDayReturn()['price']);
        $this->add('price_adjustment_number', $this->job->price_adjustment_number);
        
        // Save breakdown to job_prices table
        foreach ($this->breakdown as $type => $value) {
            JobPrice::updateOrCreate(
                ['job_id' => $this->job->id, 'type' => $type],
                ['value' => $value]
            );
        }
        
        // Update total price on Job
        $this->job->forceFill(['price' => $this->price])->saveQuietly();
        return [
            'total' => $this->price,
            'breakdown' => $this->breakdown,
        ];
    }
    public function finalizePricing(Job $job): void
    {
      $job->rules()->delete();
      
      foreach($this->rules as $rule){
        AddOn::create([
          'notable_type' => Job::class,
          'notable_id' => $this->job->id,
          'name' => $rule['name'],
          'display_name' => $rule['display_name'],
          'price' => $rule['price'],
          'begin_date' => $rule['begin_date'],
          'end_date' => $rule['end_date'],
        ]);
      }
    }
    protected function add(string $type, float $amount): void
    {
        $this->price += $amount;
        $this->breakdown[$type] = $amount;
    }
    protected function populateJobVariables(): void
    {
        $this->job->populateVariables();
    }
    protected function price_packages(){
      $price = 0;
      $packages = [];
      foreach($this->job->getDropOffTasks() as $task){
        $packageTypeId = $task->package->package_type_id;
        if(!isset($packages[$packageTypeId])){
          $packages[$packageTypeId]['quantity'] += $task->package->quantity;
          $packages[$packageTypeId]['total_price'] += $task->package->packageType->price;
        }else{
          $packages[$packageTypeId] = [
              'id'    => $task->package->packageType->id,
              'price' => $task->package->packageType->price,
              'quantity' => $task->package->quantity,
              'baseQuantityThreshold' => $task->package->packageType->baseQuantityThreshold,
              'total_price' => $task->package->packageType->price, // Keep track of the total price for this package type
          ];
        } 
      }
      $oversize = false;
      $tempPrice = 0;
      foreach($packages as $package){
        if($package['quantity'] > $package['baseQuantityThreshold']){
          $oversize = true;
        }
        $tempPrice += $package['total_price'];
      }
      $price += $tempPrice;
      return [
        'price' => $price,
        'isOversize' => $oversize,
      ];
    }
    protected function price_food(){
      foreach($this->job->getDropOffTasks() as $task){
        if($task->package->packageType->extras->contains('name', 'food')){
          return [
            'price' => collect($this->rules)->firstWhere('name','package-food')['price'] ?? 0,
            'isApplicable' => true,
          ];
        }
      }
      return [
        'price' => 0,
        'isApplicable' => false,
      ];
    }
    public function getPrices(): array
    {
      $prices = JobPrice::where('job_id', $this->job->id)->get();
      foreach ($prices as $price) {
        $this->breakdown[$price->type] = $price->value;
        $this->price += $price->value;
      }
        return [
            'total' => $this->price,
            'breakdown' => $this->breakdown,
        ];
    }
}
