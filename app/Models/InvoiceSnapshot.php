<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InvoiceSnapshot extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $snapshot): void {
            // Fallback for environments where invoice_snapshots.id is not AUTO_INCREMENT.
            if ($snapshot->getAttribute('id') === null) {
                $nextId = (int) DB::table($snapshot->getTable())->max('id') + 1;
                $snapshot->setAttribute('id', $nextId);
            }
        });
    }

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
