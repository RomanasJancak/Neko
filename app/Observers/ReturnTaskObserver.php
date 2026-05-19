<?php

namespace App\Observers;

use App\Models\ReturnTask;

class ReturnTaskObserver
{
  public function created(ReturnTask $returnTask): void
  {
    $this->recalculateJobInvoiceItem($returnTask);
  }

  public function updated(ReturnTask $returnTask): void
  {
    $this->recalculateJobInvoiceItem($returnTask);
  }

  public function deleted(ReturnTask $returnTask): void
  {
    $this->recalculateJobInvoiceItem($returnTask);
  }

  protected function recalculateJobInvoiceItem(ReturnTask $returnTask): void
  {
    $task = $returnTask->task;
    if (!$task || !$task->job) {
      return;
    }

    $job = $task->job;
    if ((int) $job->status_id !== 14 || !$job->invoiceItem) {
      $job->recalculatePrice();
      return;
    }

    //$pricingService = app(InvoicePricingService::class);
    //$pricingService->recalculateItemAndInvoice($job->invoiceItem);
  }
}
