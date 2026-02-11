<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\InvoicePricingService;
use App\Models\InvoiceSnapshot;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
      'client_id','status_id','issue_date','due_date','paid_date','total_amount','notes','invoice_number','total'
    ];
    public function client()
    {
         return $this->belongsTo(Client::class, 'customer_id');
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }
    public function snapshots()
    {
        return $this->hasMany(InvoiceSnapshot::class);
    }
    public function latestSnapshot()
    {
        return $this->hasOne(InvoiceSnapshot::class)->latestOfMany();
    }
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
    public function recalculatePrice(): void
    {
        app(InvoicePricingService::class)->recalculateInvoice($this);
    }
}
