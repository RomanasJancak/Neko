<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Colour extends Model
{
    use HasFactory;

    public const TASKABLE_TYPES = [
        'pickup' => Pickuptask::class,
        'package' => Package::class,
        'return' => ReturnTask::class,
        'user' => User::class,
    ];

    protected $fillable = [
        'hex_code',
        'taskable_type',
        'taskable_id',
        'type',
    ];

    protected $attributes = [
        'type' => 'main',
    ];

    protected $appends = [
        'taskable_alias',
    ];
    protected function hexCode(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => '#' . $value, 
            set: fn (string $value) => ltrim($value, '#'), 
        );
    }

    public function taskable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function taskableTypeOptions(): array
    {
        return self::TASKABLE_TYPES;
    }

    public static function taskableAliases(): array
    {
        return array_keys(self::TASKABLE_TYPES);
    }

    public static function resolveTaskableClass(?string $taskableType): ?string
    {
        if ($taskableType === null) {
            return null;
        }

        if (isset(self::TASKABLE_TYPES[$taskableType])) {
            return self::TASKABLE_TYPES[$taskableType];
        }

        return in_array($taskableType, self::TASKABLE_TYPES, true) ? $taskableType : null;
    }

    public function getTaskableAliasAttribute(): ?string
    {
        $alias = array_search($this->taskable_type, self::TASKABLE_TYPES, true);

        return $alias === false ? null : $alias;
    }
}