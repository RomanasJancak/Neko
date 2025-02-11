<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTemplate extends Job
{
    use HasFactory;

    public function tasks(){
        return json_decode(json_encode([
            'pickup' => json_decode($this->pickuptask_data),
            'dropOffs' => json_decode($this->dropOffs_data),
            'return' => json_decode($this->return_data),
        ]));
    }
}
