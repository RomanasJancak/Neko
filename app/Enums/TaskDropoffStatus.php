<?php

namespace App\Enums;

enum TaskDropoffStatus: int
{
    case WAITING = 24;
    case ATDROP = 2;
    case UNABLE = 3;
    case POD = 4;
    case POD_OP = 5;

    /**
     * Returns the single automatic next status, when one exists.
     */
    public function next(): ?self
    {
        return match($this) {
            self::WAITING => self::ATDROP,
            self::ATDROP => null,
            self::UNABLE, self::POD, self::POD_OP => null,
        };
    }

    /**
     * Returns all allowed next statuses for explicit transition validation.
     *
     * @return array<self>
     */
    public function allowedNextStatuses(): array
    {
        return match($this) {
            self::WAITING => [self::ATDROP],
            self::ATDROP => [self::UNABLE, self::POD, self::POD_OP],
            self::UNABLE, self::POD, self::POD_OP => [],
        };
    }

    /**
     * Normalizes DB status names into this enum.
     */
    public static function fromStatusName(?string $name): ?self
    {
        if (! $name) {
            return null;
        }

        $normalized = strtoupper(trim(str_replace(['-', '_'], ' ', $name)));
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        if (str_contains($normalized, 'POD OP') || str_contains($normalized, 'PODOP')) {
            return self::POD_OP;
        }

        if ($normalized === 'POD' || str_contains($normalized, 'PROOF OF DELIVERY')) {
            return self::POD;
        }

        if (str_contains($normalized, 'UNABLE') || str_contains($normalized, 'UNDELIVERED')) {
            return self::UNABLE;
        }

        if (str_contains($normalized, 'ATDROP') || str_contains($normalized, 'AT DROP') || str_contains($normalized, 'AT DROPOFF') || str_contains($normalized, 'AT DROP OFF') || str_contains($normalized, 'COURIER AT DROPOFF')) {
            return self::ATDROP;
        }

        if (str_contains($normalized, 'WAITING')) {
            return self::WAITING;
        }

        return null;
    }

    /**
     * Candidate DB names used to resolve this enum back to Status rows.
     *
     * @return array<string>
     */
    public function aliases(): array
    {
        return match($this) {
            self::WAITING => ['DROPOFF PENDING', 'WAITING'],
            self::ATDROP => ['ATDROP', 'AT DROP', 'AT DROPOFF', 'COURIER AT DROPOFF'],
            self::UNABLE => ['UNABLE', 'UNDELIVERED'],
            self::POD => ['POD', 'PROOF OF DELIVERY'],
            self::POD_OP => ['POD OP', 'POD_OP', 'PODOP'],
        };
    }
}