<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function jobs(){
        return $this->hasMany(Job::class,'courrier_id')->orderBy('eilesNumeris');
    }
    public function jobsWithDate($date)
    {
        $date = \Carbon\Carbon::parse($date);
    return $this->hasMany(Job::class, 'courrier_id')
        ->where(function ($query) use ($date) {
            $query->where('pickup_time_begin', '>', $date->startOfDay())
                ->where('pickup_time_end', '<', $date->endOfDay());
        })
        ->orWhere(function ($query) use ($date) {
            $query->where('dropoff_time_begin', '>', $date->startOfDay())
                ->where('dropoff_time_end', '<', $date->endOfDay());
        }) ->orderBy('eilesNumeris');
    }

}
