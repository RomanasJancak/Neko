<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobPrice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class JobPriceSnapshotService
{
    private ?bool $jobPriceValueSupportsNegative = null;

    public function persistLatestSnapshot(Job $job, array $pricePayload): void
    {
        $rows = $this->buildSnapshotRows($pricePayload);
        $now = Carbon::now();
        $supportsNegative = $this->jobPriceValueSupportsNegative();

        foreach ($rows as $row) {
            $rawValue = (int) ($row['value'] ?? 0);
            $description = is_array($row['description'] ?? null) ? $row['description'] : [];
            $description['signed_value'] = $rawValue;

            JobPrice::query()->updateOrCreate(
                [
                    'job_id' => $job->id,
                    'type' => $row['type'],
                ],
                [
                    'value' => $supportsNegative ? $rawValue : max(0, $rawValue),
                    'description' => $description,
                    'calculated_at' => $now,
                ]
            );
        }

        $types = array_map(static fn (array $row): string => $row['type'], $rows);

        JobPrice::query()
            ->where('job_id', $job->id)
            ->whereNotIn('type', $types)
            ->delete();
    }

    /**
     * Reconstruct the Job::price() response shape from stored snapshot rows.
     * Returns null when no snapshot exists yet (caller should fall back to live calc).
     */
    public function snapshotToPayload(Job $job): ?array
    {
        $rows = JobPrice::query()
            ->where('job_id', $job->id)
            ->get()
            ->keyBy('type');

        if ($rows->isEmpty()) {
            return null;
        }

        $desc = static fn (string $type): array => $rows->get($type)?->description ?? [];
        $val = static fn (string $type): int => (int) ($desc($type)['signed_value'] ?? ($rows->get($type)?->value ?? 0));

        $totalDesc           = $desc(JobPrice::TYPE_TOTAL);
        $isFixed             = !($totalDesc['is_fixed_price'] ?? false);
        $breakdownPrice      = $totalDesc['breakdown_price'] ?? $val(JobPrice::TYPE_TOTAL);

        $sameDayDesc         = $desc(JobPrice::TYPE_SAME_DAY_RETURN);
        $sameDayReturnPayload = $sameDayDesc['payload'] ?? $val(JobPrice::TYPE_SAME_DAY_RETURN);

        $oversizeDesc        = $desc(JobPrice::TYPE_OVERSIZE);
        $oversizePayload     = $oversizeDesc['payload'] ?? [];

        return [
            'breakdownOfPrice' => [
                'price_distance'              => $val(JobPrice::TYPE_DISTANCE),
                'price_outsidePostalCodeZone' => $val(JobPrice::TYPE_OUTSIDE_ZONE),
                'price_weight'                => $val(JobPrice::TYPE_WEIGHT),
                'price_timing'                => $val(JobPrice::TYPE_TIMING),
                'price_packages'              => $val(JobPrice::TYPE_PACKAGES),
                'price_sunday'                => $val(JobPrice::TYPE_SUNDAY),
                'price_bankHoliday'           => $val(JobPrice::TYPE_BANK_HOLIDAY),
                'price_sameDayReturn'         => $sameDayReturnPayload,
                'oversizePrice'               => $val(JobPrice::TYPE_OVERSIZE),
                'price_food'                  => $val(JobPrice::TYPE_FOOD),
                'price_adjustment_number'     => $val(JobPrice::TYPE_PRICE_ADJUSTMENT),
                'price'                       => $breakdownPrice,
                'fixed_price'                 => $isFixed,
            ],
            'totalPrice'              => $val(JobPrice::TYPE_TOTAL),
            'price_Distance'          => $desc(JobPrice::TYPE_DISTANCE)['payload']          ?? $val(JobPrice::TYPE_DISTANCE),
            'price_OutOfZone'         => $desc(JobPrice::TYPE_OUTSIDE_ZONE)['payload']      ?? $val(JobPrice::TYPE_OUTSIDE_ZONE),
            'weight_price'            => $desc(JobPrice::TYPE_WEIGHT)['payload']            ?? $val(JobPrice::TYPE_WEIGHT),
            'price-packages'          => $desc(JobPrice::TYPE_PACKAGES)['payload']          ?? $val(JobPrice::TYPE_PACKAGES),
            'price_oversize_added'    => $oversizePayload['price_oversize_added']    ?? null,
            'price_oversize_value'    => $oversizePayload['price_oversize_value']    ?? null,
            'price_package_oversize'  => $oversizePayload['price_package_oversize']  ?? null,
            'timing_price'            => $desc(JobPrice::TYPE_TIMING)['payload']            ?? $val(JobPrice::TYPE_TIMING),
            'price_time_sunday'       => $desc(JobPrice::TYPE_SUNDAY)['payload']            ?? $val(JobPrice::TYPE_SUNDAY),
            'price_time_bankholiday'  => $desc(JobPrice::TYPE_BANK_HOLIDAY)['payload']      ?? $val(JobPrice::TYPE_BANK_HOLIDAY),
        ];
    }

    private function jobPriceValueSupportsNegative(): bool
    {
        if ($this->jobPriceValueSupportsNegative !== null) {
            return $this->jobPriceValueSupportsNegative;
        }

        try {
            $column = DB::selectOne("SHOW COLUMNS FROM `job_prices` LIKE 'value'");
            $type = strtolower((string) ($column->Type ?? ''));
            $this->jobPriceValueSupportsNegative = $type === '' || !str_contains($type, 'unsigned');
        } catch (\Throwable $e) {
            // Fail open: if schema lookup is unavailable, preserve previous behavior.
            $this->jobPriceValueSupportsNegative = true;
        }

        return $this->jobPriceValueSupportsNegative;
    }

    /**
     * Build deterministic rows from current Job::price() response contract.
     */
    public function buildSnapshotRows(array $pricePayload): array
    {
        $breakdown = $pricePayload['breakdownOfPrice'] ?? [];

        $sameDayReturn = $breakdown['price_sameDayReturn'] ?? 0;
        $sameDayReturnValue = is_array($sameDayReturn)
            ? (int) ($sameDayReturn['price'] ?? 0)
            : (int) $sameDayReturn;

        return [
            [
                'type' => JobPrice::TYPE_DISTANCE,
                'value' => (int) ($breakdown['price_distance'] ?? 0),
                'description' => [
                    'source' => 'price_Distance',
                    'payload' => $pricePayload['price_Distance'] ?? null,
                ],
            ],
            [
                'type' => JobPrice::TYPE_OUTSIDE_ZONE,
                'value' => (int) ($breakdown['price_outsidePostalCodeZone'] ?? 0),
                'description' => [
                    'source' => 'price_OutOfZone',
                    'payload' => $pricePayload['price_OutOfZone'] ?? null,
                ],
            ],
            [
                'type' => JobPrice::TYPE_WEIGHT,
                'value' => (int) ($breakdown['price_weight'] ?? 0),
                'description' => [
                    'source' => 'weight_price',
                    'payload' => $pricePayload['weight_price'] ?? null,
                ],
            ],
            [
                'type' => JobPrice::TYPE_TIMING,
                'value' => (int) ($breakdown['price_timing'] ?? 0),
                'description' => [
                    'source' => 'timing_price',
                    'payload' => $pricePayload['timing_price'] ?? null,
                ],
            ],
            [
                'type' => JobPrice::TYPE_PACKAGES,
                'value' => (int) ($breakdown['price_packages'] ?? 0),
                'description' => [
                    'source' => 'price-packages',
                    'payload' => $pricePayload['price-packages'] ?? null,
                ],
            ],
            [
                'type' => JobPrice::TYPE_SUNDAY,
                'value' => (int) ($breakdown['price_sunday'] ?? 0),
                'description' => [
                    'source' => 'price_time_sunday',
                    'payload' => $pricePayload['price_time_sunday'] ?? null,
                ],
            ],
            [
                'type' => JobPrice::TYPE_BANK_HOLIDAY,
                'value' => (int) ($breakdown['price_bankHoliday'] ?? 0),
                'description' => [
                    'source' => 'price_time_bankholiday',
                    'payload' => $pricePayload['price_time_bankholiday'] ?? null,
                ],
            ],
            [
                'type' => JobPrice::TYPE_SAME_DAY_RETURN,
                'value' => $sameDayReturnValue,
                'description' => [
                    'source' => 'breakdownOfPrice.price_sameDayReturn',
                    'payload' => $sameDayReturn,
                ],
            ],
            [
                'type' => JobPrice::TYPE_OVERSIZE,
                'value' => (int) ($breakdown['oversizePrice'] ?? 0),
                'description' => [
                    'source' => 'price_package_oversize',
                    'payload' => [
                        'price_oversize_added' => $pricePayload['price_oversize_added'] ?? null,
                        'price_oversize_value' => $pricePayload['price_oversize_value'] ?? null,
                        'price_package_oversize' => $pricePayload['price_package_oversize'] ?? null,
                    ],
                ],
            ],
            [
                'type' => JobPrice::TYPE_FOOD,
                'value' => (int) ($breakdown['price_food'] ?? 0),
                'description' => [
                    'source' => 'breakdownOfPrice.price_food',
                ],
            ],
            [
                'type' => JobPrice::TYPE_PRICE_ADJUSTMENT,
                'value' => (int) ($breakdown['price_adjustment_number'] ?? 0),
                'description' => [
                    'source' => 'breakdownOfPrice.price_adjustment_number',
                ],
            ],
            [
                'type' => JobPrice::TYPE_TOTAL,
                'value' => (int) ($pricePayload['totalPrice'] ?? 0),
                'description' => [
                    'source' => 'totalPrice',
                    'is_fixed_price' => !($breakdown['fixed_price'] ?? false),
                    'breakdown_price' => $breakdown['price'] ?? null,
                ],
            ],
        ];
    }
}
