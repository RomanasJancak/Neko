<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\InvoicePricingService;

class InvoiceItem extends Model
{
    use HasFactory;
    protected $fillable = ['invoice_id','job_id','description','price'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'invoice_item_id');
    }
    public function recalculatePrice(): void
    {
        app(InvoicePricingService::class)->recalculateInvoiceItem($this);
    }
}
