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
        //=============================================
        $packageResult = $this->price_packages();

        // 2. Explicitly bridge the state back to the model so $this->job->oversizePrice() doesn't fail
        if ($packageResult['isOversize']) {
            // Use reflection or a temporary public method/property if price_oversize_status is private
            // Since it is private on your model, the cleanest local fix without touching the model yet is:
            $this->add('oversizePrice', $packageResult['isOversize'] ? $this->job->price_oversize : 0);
        } else {
            $this->add('oversizePrice', 0);
        }


        //================================================
        $this->add('price_packages', $packageResult['price']);



        $this->add('price_food', $this->price_food()['price']);
        $this->add('price_distance', $this->job->price_distance()['price']);
        $this->add('price_outsidePostalCodeZone', $this->job->price_outsidePostalCodeZone());
        $this->add('price_weight', $this->job->price_weight()['price']);
        $this->add('price_timing', $this->job->new_price_timing()['price']);        
        $this->add('price_sunday', $this->job->price_sunday()['price']);
        $this->add('price_bankHoliday', $this->job->price_bankHoliday()['price']);
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
    protected function price_packages(): array
    {
        $packages = [];
        
        foreach ($this->job->getDropOffTasks() as $task) {
            $packageType = $task->package->packageType;
            $packageTypeId = $packageType->id;

            if (!isset($packages[$packageTypeId])) {
                $packages[$packageTypeId] = [
                    'id' => $packageTypeId,
                    'price' => $packageType->price,
                    'quantity' => 0,
                    'baseQuantityThreshold' => $packageType->baseQuantityThreshold,
                    'total_price' => 0,
                ];
            }

            $packages[$packageTypeId]['quantity'] += $task->package->quantity;
            $packages[$packageTypeId]['total_price'] += ($task->package->quantity * $packageType->price);
        }

        $oversize = false;
        $totalPrice = 0;

        foreach ($packages as $package) {
            if ($package['quantity'] > $package['baseQuantityThreshold']) {
                $oversize = true;
            }
            $totalPrice += $package['total_price'];
        }

        return [
            'price' => $totalPrice,
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
