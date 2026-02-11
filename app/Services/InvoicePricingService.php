<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;

class InvoicePricingService
{
    public function recalculateInvoiceItem(InvoiceItem $item): void
    {
        $total = $item->jobs()->sum('price');
        $item->price = $total / 100;
        $item->save();
    }

    public function recalculateInvoice(Invoice $invoice): void
    {
        $total = $invoice->invoiceItems()->sum('price');
        $invoice->total = $total;
        $invoice->save();
    }

    public function recalculateItemAndInvoice(?InvoiceItem $item): void
    {
        if (!$item) {
            return;
        }

        $this->recalculateInvoiceItem($item);

        if ($item->invoice) {
            $this->recalculateInvoice($item->invoice);
        }
    }
}
