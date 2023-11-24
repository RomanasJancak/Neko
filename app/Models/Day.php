<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;

    public function jobs()
    {
        $date = $this->date;
    
        return Job::where(function ($query) use ($date) {
            $query->where('pickup_time_begin', '>=', \Carbon\Carbon::parse($date)->startOfDay())
                  ->where('pickup_time_end', '<=', \Carbon\Carbon::parse($date)->endOfDay());
        })
        ->orWhere(function ($query) use ($date) {
            $query->where('dropoff_time_begin', '<=', \Carbon\Carbon::parse($date)->startOfDay())
                  ->where('dropoff_time_end', '>=', \Carbon\Carbon::parse($date)->endOfDay());
        })
        ->get();
    }

}
