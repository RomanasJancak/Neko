<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

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
        'phone',
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
    public static function getCouriersForDate($date)
    {
        $date = \Carbon\Carbon::parse($date)->toDateString();
        return self::whereHas('roles', function ($query) {
            $query->where('name', 'courier');
        })->whereHas('workloads.day', function ($query) use ($date) {
            $query->whereDate('date', $date);
        })->get();
    }
    public function settings()
    {
        return $this->hasMany(UserSetting::class);
    }
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

    public function isAdminOrSuperAdmin(): bool
    {
        return $this->roles()->whereIn('roles.id', [1, 2])->exists();
    }

    public function getVisibleUsers(): Builder
    {
        if ($this->isAdminOrSuperAdmin()) {
            return self::query()->orderBy('name');
        }

        $visibleRoleIds = $this->getDescendantRoleIds();
        // Include the user's own roles as well
        $visibleRoleIds = array_merge($visibleRoleIds, $this->roles()->pluck('roles.id')->map(static fn ($id) => (int) $id)->all());
        $query = self::query();

        if ((int) $this->client_id !== 1) {
            $query->where('client_id', $this->client_id);
        }

        if (empty($visibleRoleIds)) {
            return $query->where('id', $this->id)
                ->orderBy('name');
        }

        return $query->where(function ($query) use ($visibleRoleIds) {
            $query->where('id', $this->id)
                ->orWhereHas('roles', function ($rolesQuery) use ($visibleRoleIds) {
                    $rolesQuery->whereIn('roles.id', $visibleRoleIds);
                });
        })
            ->orderBy('name');
    }

    protected function getDescendantRoleIds(): array
    {
        $currentRoleIds = $this->roles()->pluck('roles.id')->map(static fn ($id) => (int) $id)->all();

        if (empty($currentRoleIds)) {
            return [];
        }

        $descendantRoleIds = [];
        $frontier = $currentRoleIds;

        while (!empty($frontier)) {
            $children = DB::table('role_hierarchy')
                ->whereIn('parent_role_id', $frontier)
                ->pluck('child_role_id')
                ->map(static fn ($id) => (int) $id)
                ->all();

            $newChildren = array_values(array_diff($children, $descendantRoleIds));

            if (empty($newChildren)) {
                break;
            }

            $descendantRoleIds = array_values(array_unique(array_merge($descendantRoleIds, $newChildren)));
            $frontier = $newChildren;
        }

        return $descendantRoleIds;
    }
    public function tasks()
    {
        return $this->hasManyThrough(Task::class, Job::class, 'courrier_id', 'job_id')->orderBy('order_number');
    }
    public function tasksByDate($date)
    {

        $query = $this->hasManyThrough(Task::class, Job::class, 'courrier_id', 'job_id')
        ->whereDate('tasks.date', $date.' 00:00:00')
        ->orderBy('order_number');
        $sql = $query->toSql();
        $return_data = $query->get();
        return $return_data;
    }
    public static function getCouriersWithWorkload(Day $day): Collection
    {
        return self::whereHas('roles', function ($query) {
            $query->where('name', 'courier');  // Assuming the role name is stored in 'name' column
        })->whereHas('workloads', function ($query) use ($day) {
            $query->where('day_id', $day->id);
        })->get();
    }

    public function scopeCouriers(Builder $query): Builder
    {
        return $query->whereHas('roles', function (Builder $roleQuery) {
            $roleQuery->where('name', 'courier');
        });
    }

    public function scopeWithWorkloadOnDate(Builder $query, string $date): Builder
    {
        return $query->whereHas('workloads.day', function (Builder $dayQuery) use ($date) {
            $dayQuery->whereDate('date', $date);
        });
    }
}
