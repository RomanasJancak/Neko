<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPrice extends Model
{
    use HasFactory;

    protected $fillable = ['job_id', 'type', 'value', 'description', 'calculated_at'];

    protected $casts = [
        'description' => 'array',
        'calculated_at' => 'datetime',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
