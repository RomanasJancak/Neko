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
        return $this->belongsTo(Status::class);
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

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // public function status()
    // {
    //     return $this->belongsTo(Status::class, 'status_id');
    // }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}