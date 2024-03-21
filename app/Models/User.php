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
        return $this->hasMany(Job::class,'courrier_id');
    }
    public function tasksForTHeDay($date){
        $date = \Carbon\Carbon::parse($date);
    }
    public function jobsWithDate($date)
    {
        
        $date = \Carbon\Carbon::parse($date);
        return $this->hasMany(Job::class,'courrier_id')
        ->whereBetween('pickup_time_begin', [$date->toDateString(), $date->addDay()->toDateString()])
        ->orderBy('eilesNumeris')
        ->get();
    }
    public function workloads()
    {
        return $this->hasMany(Workload::class,'user_id');
    }
    public function workload(Day $day)
    {      
        return $this->hasOne(Workload::class)
        ->where('day_id', $day->id)->first();
    }
    
    public function getAllRoles(){
        return Role::all();
    }
    public function currentrole()
    {
        // Assuming a user has only one role at a time
        return $this->roles()->first();
    }
    public function tasks()
    {
        return $this->hasManyThrough(Task::class, Job::class, 'courrier_id', 'job_id')->orderBy('order_number');;
    }
}
