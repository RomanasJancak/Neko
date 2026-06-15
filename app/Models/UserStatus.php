<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserStatus extends Model
{
    use HasFactory;
    protected $fillable = ['status_id'];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function userDayStatuses(): HasMany
    {
        return $this->hasMany(UserDayStatus::class, 'user_status_id');
    }
}
