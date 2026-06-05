<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Facades\Auth;

class DateFormatService
{
    public function __construct(private readonly SettingsService $settings)
    {
    }
    public function getUserFormat(?User $user = null): string
    {
        $user ??= Auth::user();

        return (string) ($this->settings->get('global.dateFormat', $user) ?? 'Y-m-d');
    }

    public function userDateFormat(?User $user = null): string
    {
        $user ??= Auth::user();

        return (string) ($this->settings->get('ui.dateFormat', $user) ?? 'Y-m-d');
    }

    public function invoicePdfDateFormat(): string
    {
        return (string) ($this->settings->get('global.invoicePdfDateFormat') ?? 'Y-m-d');
    }

    public function formatForUser(mixed $value, ?User $user = null, ?string $fallbackFormat = null): string
    {
        $user ??= Auth::user();

        return $this->format($value, $this->userDateFormat($user), $fallbackFormat ?? 'Y-m-d');
    }

    public function formatForInvoicePdf(mixed $value, ?string $fallbackFormat = null): string
    {
        return $this->format($value, $this->invoicePdfDateFormat(), $fallbackFormat ?? 'Y-m-d');
    }

    public function format(mixed $value, string $format, ?string $fallbackFormat = 'Y-m-d'): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $carbon = $this->toCarbon($value);

        if (!$carbon) {
            return (string) $value;
        }

        try {
            return $carbon->format($format);
        } catch (\Throwable) {
            return $fallbackFormat ? $carbon->format($fallbackFormat) : $carbon->toDateString();
        }
    }

    protected function toCarbon(mixed $value): ?Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if ($value instanceof DateTimeInterface) {
            return Carbon::instance($value);
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}