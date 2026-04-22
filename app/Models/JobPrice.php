<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPrice extends Model
{
    use HasFactory;

    public const TYPE_DISTANCE = 'distance';
    public const TYPE_OUTSIDE_ZONE = 'outside_zone';
    public const TYPE_WEIGHT = 'weight';
    public const TYPE_TIMING = 'timing';
    public const TYPE_PACKAGES = 'packages';
    public const TYPE_SUNDAY = 'sunday';
    public const TYPE_BANK_HOLIDAY = 'bank_holiday';
    public const TYPE_SAME_DAY_RETURN = 'same_day_return';
    public const TYPE_OVERSIZE = 'oversize';
    public const TYPE_FOOD = 'food';
    public const TYPE_PRICE_ADJUSTMENT = 'price_adjustment';
    public const TYPE_TOTAL = 'total';

    protected $fillable = ['job_id', 'type', 'value', 'description', 'calculated_at'];

    protected $casts = [
        'description' => 'array',
        'calculated_at' => 'datetime',
    ];

    public static function supportedTypes(): array
    {
        return [
            self::TYPE_DISTANCE,
            self::TYPE_OUTSIDE_ZONE,
            self::TYPE_WEIGHT,
            self::TYPE_TIMING,
            self::TYPE_PACKAGES,
            self::TYPE_SUNDAY,
            self::TYPE_BANK_HOLIDAY,
            self::TYPE_SAME_DAY_RETURN,
            self::TYPE_OVERSIZE,
            self::TYPE_FOOD,
            self::TYPE_PRICE_ADJUSTMENT,
            self::TYPE_TOTAL,
        ];
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
