<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extra extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_type',
        'model_id',
        'name',
        'extra_type_id',
    ];
    public function type()
    {
        return $this->belongsTo(ExtraTypes::class, 'extra_type_id');
    }
}
