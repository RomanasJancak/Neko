<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDayStatus extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','user_status_id', 'date'];

    public function userStatus()
    {
        return $this->belongsTo(UserStatus::class, 'user_status_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function day()
    {
        return $this->belongsTo(Day::class);
    }
    //--------Accessors & Mutators--------//
    public function statusName() : Attribute
    {
        return Attribute::make(
            get: fn () => $this->userStatus->status->name ?? 'Unknown',
        );
    }

}

