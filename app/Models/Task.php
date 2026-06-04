<?php

namespace App\Models;

use App\Services\TaskStatusTransitionService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'status_id',
        'job_id',
        'order_number',
        'taskable_type',
        'taskable_id',
    ];

    public function taskable()
    {
        return $this->morphTo();
    }
    public function pickup()
    {
        return $this->hasOne(Pickuptask::class);
    }
    public function package(){
        return $this->hasOne(Package::class); 
    }
    public function return(){
        return $this->hasOne(ReturnTask::class);
    }
    public function customTask(){
        return $this->hasOne(CustomTask::class);
    }
    public function type(){
        return match ($this->taskable_type) {
            Pickuptask::class => 'pickup',
            Package::class    => 'dropOff',
            ReturnTask::class => 'return',
            CustomTask::class => 'custom',
            default           => null,
        };
    }
    public function typeOfTask(){
        if (!$this->taskable_type) {
            return null;
        }

        return $this->hasOne($this->taskable_type);
    }
    public function job(){
        return $this->belongsTo(Job::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
    public function revertToFirstStatus(): void
    {
        app(TaskStatusTransitionService::class)->revertToFirstStatus($this);
    }
    public function resolvedStatus()
    {
        return $this->taskable?->status;
    }

    public function statusNextInfo(): array
    {
        return app(TaskStatusTransitionService::class)->getNextStatusInfo($this);
    }
    public function nameOfAddress()
    {
      return $this->taskable?->nameOfAddress();
    }
    public function country()
    {
        return $this->taskable?->country();
    }
    public function city()
    {
        return $this->taskable?->city();
    }
    public function addressShort(){
        return $this->taskable?->addressShort();
    }
    public function postalCode()
    {
        return $this->taskable?->postalCode();
    }
    public function addressLine()
    {
        return $this->taskable?->addressLine();
    }
    public function fullAddress()//not finished
    {
        return $this->taskable?->fullAddress();
    }

    public function timeWindow()
    {
        return $this->taskable?->timeWindow();
    }
    public function timeWindowBegin(){
        return $this->taskable?->timeWindowBegin();
    }
    public function timeWindowEnd(){
        return $this->taskable?->timeWindowEnd();
    }
}
