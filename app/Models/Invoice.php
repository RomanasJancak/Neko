<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['client_id','status_id','issue_date','due_date','paid_date','total_amount','notes'];
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }
    public function recalculatePrice(): void
    {
        $total = $this->invoiceItems()->sum('price');
        $this->total = $total;
        $this->save();
    }
}
