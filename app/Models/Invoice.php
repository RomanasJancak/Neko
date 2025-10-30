<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
    public function recalculatePrice(): void
    {
        $total = $this->items()->sum('price');
        $this->total = $total;
        $this->save();
    }
}
