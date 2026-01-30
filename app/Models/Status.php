<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'color_main',
        'color_pickup',
        'color_dropoff',
        'color_return',
        'color_custom',
    ];
}
