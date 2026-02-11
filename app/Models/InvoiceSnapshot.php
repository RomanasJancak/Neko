<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'version',
        'generated_at',
        'data',
        'created_by',
    ];

    protected $casts = [
        'data' => 'array',
        'generated_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
