<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EmailAddress extends Model
{
    protected $fillable = ['email', 'type'];

    public function emailable(): MorphTo
    {
        return $this->morphTo();
    }
}
