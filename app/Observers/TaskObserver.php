<?php

namespace App\Observers;

use App\Models\Task;

use Carbon\Carbon;

class TaskObserver
{
  public function created(Task $task): void
  {
    $this->ensurePickupBeforeDelivery($task);
    $this->recalculateJobPrice($task);
  }
  public function updated(Task $task): void
  {
    $this->ensurePickupBeforeDelivery($task);
    $this->recalculateJobPrice($task);
  }
  public function deleting(Task $task): void
  {
    $this->ensurePickupBeforeDelivery($task);
    $this->recalculateJobPrice($task);

  }
  private function ensurePickupBeforeDelivery(Task $task): void
  {
    $job = $task->job;
    $pickup = $job->getPickupTask();
    if($pickup){
      $pickupTask = $pickup->task;
      if($pickupTask->order_number !== 0){
        Task::where('job_id', $job->id)
            ->increment('order_number');
        $pickupTask->order_number = 0;
        $pickupTask->save();
      }
    }
  }
  private function recalculateJobPrice(Task $task): void
  {
    $job = $task->job;
    if ($job) {
      $job->recalculatePrice();
      $job->save();
    }
  }
}