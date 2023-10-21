<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'courrier_id',
        'creation_time',
        'completion_time',
        'status_id',
        'collection_details',
        'pickup_adress',
        'delivery_adress',
        'senderContacts',
        'manager_id',
        'receiverContacts',
        'group_id',
        'notes',
        'invoice_id',
    ];
    public function status(){
        return $this->belongsTo(Status::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
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