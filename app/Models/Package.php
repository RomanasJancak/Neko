<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    public function job()
    {
        return $this->belongsTo(Job::class, 'sender_id');
    }
    public function packageType()
    {
        return $this->belongsTo(PackageType::class);
    }
}
