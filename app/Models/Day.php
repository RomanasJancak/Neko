<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Day extends Model
{
    use HasFactory;

    protected $casts = [
        'date' => 'datetime',
    ];
    protected $fillable = [
        'name','date',
        // Add other attributes to the $fillable array as needed
    ];

    public function month(){
        return $this->date->format('m');
    }
    public function year(){
        return $this->date->format('Y');
    }
    public function jobs()
    {
        $date = $this->date;
    
        return Job::where(function ($query) use ($date) {
            $query->where('pickup_time_begin', '>=', \Carbon\Carbon::parse($date)->startOfDay())
                  ->where('pickup_time_end', '<=', \Carbon\Carbon::parse($date)->endOfDay());
        })
        // ->orWhere(function ($query) use ($date) {
        //     $query->where('dropoff_time_begin', '<=', \Carbon\Carbon::parse($date)->startOfDay())
        //           ->where('dropoff_time_end', '>=', \Carbon\Carbon::parse($date)->endOfDay());
        // })
        ->get();
    }
    public function workloads()
    {
        return $this->hasMany(Workload::class,'day_id'); 
    }
    public function usedBike()
    {
        return Bike::whereHas('workloads', function ($query) {
            $query->where('day_id', $this->id);
        })
        ->get();
    }
    public function freeBikes()
    {
        return Bike::whereDoesntHave('workloads', function ($query) {
            $query->where('day_id', $this->id);
        })->get();
    }
    public function freeCouriers()
    {
        return User::whereDoesntHave('workloads', function ($query) {
            $query->where('day_id', $this->id);
        })
        ->whereHas('roles', function ($query) {
            $query->where('name', 'courier');
        })
        ->get();
    }
}
