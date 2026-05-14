<?php

namespace App\Observers;

use App\Models\Pickuptask;
use App\Services\InvoicePricingService;

class PickupTaskObserver
{
  public function created(Pickuptask $pickuptask): void
  {
    $this->recalculateJobInvoiceItem($pickuptask);
  }

  public function updated(Pickuptask $pickuptask): void
  {
    $this->recalculateJobInvoiceItem($pickuptask);
  }
  public function deleted(Pickuptask $pickuptask): void
  {
    $this->recalculateJobInvoiceItem($pickuptask);
  }
  protected function recalculateJobInvoiceItem(Pickuptask $pickuptask): void
  {
    
    $task = $pickuptask->task;
    if (!$task || !$task->job) {
      return;
    }
    
    $job = $task->job;
    if ((int) $job->status_id !== 14 || !$job->invoiceItem) {
      return;
    }
    //$pricingService = app(InvoicePricingService::class);
    //$pricingService->recalculateItemAndInvoice($job->invoiceItem);
  }
}