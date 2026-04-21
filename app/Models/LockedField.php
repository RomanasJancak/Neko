<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LockedField extends Model
{
    use HasFactory;

    protected $table = 'locked_fields';

    protected $fillable = [
        'field_name',
        'is_locked',
        'model',
        'model_id',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
    ];
}
