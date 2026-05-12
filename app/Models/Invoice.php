<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\InvoicePricingService;
use App\Models\InvoiceSnapshot;
use App\Services\SettingsService;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory;

        public const STATUS_SENT_ID = 24;
        public const STATUS_NOT_SENT_ID = 25;

    protected $fillable = [
            'client_id','status_id','issue_date','due_date','paid_date','total_amount','notes','invoice_number','total','sent_at','sent_by'
    ];

        protected $casts = [
            'sent_at' => 'datetime',
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

    public function sentByUser()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
    public function recalculatePrice(): void
    {
        app(InvoicePricingService::class)->recalculateInvoice($this);
    }
    public function canBeSent(): bool
    {
        return $this->client?->hasInvoiceEmail() ?? false;
    }
    public function getInvoiceEmail(): ?string
    {
        return $this->client?->getInvoiceEmail();
    }

    public function isSentStatus(): bool
    {
        $statusValue = strtolower((string) ($this->status ?? ''));

        return ((int) ($this->status_id ?? 0) === self::STATUS_SENT_ID) || $statusValue === 'sent';
    }

    public function isCompletedAndPastInvoiceLockDate(): bool
    {
        if (!$this->isSentStatus()) {
            return false;
        }

        if (empty($this->invoice_date)) {
            return false;
        }

        $lockDays = (int) (app(SettingsService::class)->get('global.invoiceLockDays') ?? 1);

        return Carbon::today()->gt(Carbon::parse($this->invoice_date)->addDays($lockDays)->startOfDay());
    }

    public function isLockedForUser(?User $user = null): bool
    {
        if (!$this->isCompletedAndPastInvoiceLockDate()) {
            return false;
        }

        if ($user && $user->isAdminOrSuperAdmin()) {
            return false;
        }

        return true;
    }
}
