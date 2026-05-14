<?php

namespace App\Observers;

use App\Models\Task;

use Carbon\Carbon;

class TaskObserver
{
  public function created(Task $task): void
  {
    //
  }
}