<?php

namespace App\Enums;

enum TaskPickupStatus: int
{
    case WAITING = 24;
    case ATPU = 25;
    case POB = 26;
    case COMPLETED = 27;

    // Define the cycle here
    public function next(): ?self
    {
        return match($this) {
            self::WAITING => self::ATPU,
            self::ATPU => self::POB,
            self::POB => self::COMPLETED,
            self::COMPLETED => null, // No cycle back to start
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
            self::WAITING => [self::ATPU],
            self::ATPU => [self::POB],
            self::POB => [self::COMPLETED],
            self::COMPLETED => [],
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

        if (str_contains($normalized, 'PICKUP') && str_contains($normalized, 'PENDING')) {
            return self::WAITING;
        }

        if (str_contains($normalized, 'PENDING') || str_contains($normalized, 'WAITING')) {
            return self::WAITING;
        }

        if (str_contains($normalized, 'ATPU') || str_contains($normalized, 'AT PU') || str_contains($normalized, 'AT PICKUP')) {
            return self::ATPU;
        }

        if (str_contains($normalized, 'POB')) {
            return self::POB;
        }

        if ((str_contains($normalized, 'PICKUP') && str_contains($normalized, 'COM')) || str_contains($normalized, 'COMPLETE')) {
            return self::COMPLETED;
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
            self::WAITING => ['PICKUP PENDING', 'PENDING', 'WAITING'],
            self::ATPU => ['PICKUP ATPU', 'ATPU', 'AT PU', 'AT PICKUP'],
            self::POB => ['PICKUP POB', 'POB', 'PARCEL ON BOARD'],
            self::COMPLETED => ['PICKUP COM', 'PICKUP COMPLETED', 'COMPLETED', 'COMPLETE'],
        };
    }
}