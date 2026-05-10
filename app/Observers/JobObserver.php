<?php

namespace App\Observers;

use App\Models\Job;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\InvoicePricingService;

use Carbon\Carbon;

class JobObserver
{
    /**
     * Handle the Job "created" event.
     */
    public function created(Job $job): void
    {
      if((int)$job->status_id === 14) {
        $this->assignToInvoice($job);
      }
    }

    /**
     * Handle the Job "updated" event.
     */
    public function updated(Job $job): void
    {
      
      if ($job->isDirty('status_id') && ((int)$job->status_id === 14)) {
        $this->assignToInvoice($job);
      }
    }

    /**
     * Handle the Job "deleted" event.
     */
    public function deleted(Job $job): void
    {
        //
    }

    /**
     * Handle the Job "restored" event.
     */
    public function restored(Job $job): void
    {
        //
    }

    /**
     * Handle the Job "force deleted" event.
     */
    public function forceDeleted(Job $job): void
    {
        //
    }
    protected function assignToInvoice(Job $job): void
    {
      
      $jobDate = Carbon::parse($job->date);
      $nextMonday = $jobDate->copy()->next(Carbon::MONDAY)->startOfDay();
      $nextMondayOnlyYearMonthDay = $nextMonday->format('Y-m-d');
      $nextnextMonday = $nextMonday->copy()->addWeek();
      
      $invoice = Invoice::where('customer_id', $job->clientToBill_id)
                        ->where('invoice_date', '=', $nextMondayOnlyYearMonthDay)
                        //->where('invoice_date', '<=', $nextnextMonday)
                        ->first();
      //dd($jobDate, $nextMonday, $nextnextMonday, $invoice,$nextMondayOnlyYearMonthDay);
      if (!$invoice) {
          
          $invoice = new Invoice();
          $invoice->customer_id = $job->clientToBill_id;
          $invoice->invoice_date = $nextMondayOnlyYearMonthDay;
          //dd($invoice->invoice_date);
          $invoice->due_date = $nextMonday->copy()->addDays(30);
          $invoice->invoice_number = 'INV-' . Carbon::now()->format('YmdHis') . '-' . $job->clientToBill_id;
          $invoice->status = 'draft';
          $invoice->total = 0;
          /*
          $invoice = Invoice::create([
              'customer_id' => $job->clientToBill_id,
              'invoice_date' => $nextMonday,
              'due_date' => $nextMonday->copy()->addDays(30),
              'invoice_number' => 'INV-' . Carbon::now()->format('YmdHis') . '-' . $job->clientToBill_id,
              'status' => 'draft',
              'total' => 0,
          ]);
          */
          //dd($invoice);
          $invoice->save();
      }
      $invoiceItem = $invoice->invoiceItems()->first();
      if (!$invoiceItem) {
          $invoiceItem = new InvoiceItem();
          $job->invoice_item_id = $invoiceItem->id;
          $invoiceItem->price = $job->price / 100;
            $fromDate = Carbon::parse($invoice->invoice_date)->copy()->subWeeks(1)->format('Y-m-d');
            $toDate = Carbon::parse($invoice->invoice_date)->format('Y-m-d');
          $invoiceItem->description = "Deliveries from " . $fromDate . " to " . $toDate;

      }
      $invoiceItem->invoice_id = $invoice->id;
      $invoiceItem->save();
      
      $job->invoice_item_id = $invoiceItem->id;
      //dd($job);
      $job->saveQuietly();
                
      
      $pricingService = app(InvoicePricingService::class);
      $pricingService->recalculateItemAndInvoice($invoiceItem);
      //dd($job, $invoiceItem);
    }
}
