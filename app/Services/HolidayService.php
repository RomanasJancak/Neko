<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class HolidayService
{
    private array $bankHolidays = [
        '2024-01-01' => 'New Year’s Day',
        '2024-03-29' => 'Good Friday',
        '2024-04-01' => 'Easter Monday',
        '2024-05-06' => 'Early May Bank Holiday',
        '2024-05-27' => 'Spring Bank Holiday',
        '2024-08-26' => 'Summer Bank Holiday',
        '2024-12-25' => 'Christmas Day',
        '2024-12-26' => 'Boxing Day',
        '2025-01-01' => 'New Year’s Day',
        '2025-04-18' => 'Good Friday',
        '2025-04-21' => 'Easter Monday',
        '2025-05-05' => 'Early May Bank Holiday',
        '2025-05-26' => 'Spring Bank Holiday',
        '2025-08-25' => 'Summer Bank Holiday',
        '2025-12-25' => 'Christmas Day',
        '2025-12-26' => 'Boxing Day',
        '2026-01-01' => 'New Year’s Day',
        '2026-04-03' => 'Good Friday',
        '2026-04-06' => 'Easter Monday',
        '2026-05-04' => 'Early May Bank Holiday',
        '2026-05-25' => 'Spring Bank Holiday',
        '2026-08-31' => 'Summer Bank Holiday',
        '2026-12-25' => 'Christmas Day',
        '2026-12-26' => 'Boxing Day',
        '2027-01-01' => 'New Year’s Day',
        '2027-03-26' => 'Good Friday',
        '2027-03-29' => 'Easter Monday',
        '2027-05-03' => 'Early May Bank Holiday',
        '2027-05-31' => 'Spring Bank Holiday',
        '2027-08-30' => 'Summer May Bank Holiday',
        '2027-12-25' => 'Christmas Day',
        '2027-12-26' => 'Boxing Day',
        '2028-01-01' => 'New Year’s Day',
        '2028-04-14' => 'Good Friday',
        '2028-04-17' => 'Easter Monday',
        '2028-05-01' => 'Early May Bank Holiday',
    ];

    public function isBankHoliday(string $date): bool
    {
        return isset($this->bankHolidays[$date]);
    }

    public function getBankHolidays(): array
    {
        return $this->bankHolidays;
    }
}