<?php

namespace App\Observers;

use App\Models\Package;

class PackageObserver
{
  public function created(Package $package): void
  {
    $this->recalculateJobInvoiceItem($package);
  }

  public function updated(Package $package): void
  {
    $this->recalculateJobInvoiceItem($package);
  }

  public function deleted(Package $package): void
  {
    $this->recalculateJobInvoiceItem($package);
  }

  protected function recalculateJobInvoiceItem(Package $package): void
  {
    $task = $package->task;
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
