<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobPrice;
use Illuminate\Support\Carbon;

class JobPriceSnapshotService
{
    public function persistLatestSnapshot(Job $job, array $pricePayload): void
    {
        $rows = $this->buildSnapshotRows($pricePayload);
        $now = Carbon::now();

        foreach ($rows as $row) {
            JobPrice::query()->updateOrCreate(
                [
                    'job_id' => $job->id,
                    'type' => $row['type'],
                ],
                [
                    'value' => $row['value'],
                    'description' => $row['description'],
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
