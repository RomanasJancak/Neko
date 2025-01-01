<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraTypes extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];
    public function extras()
    {
        return $this->hasMany(Extra::class);
    }
}
