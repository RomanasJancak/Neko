<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;
    protected $fillable = ['content', 'user_id'];

    public function notable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function previous()
    {
        return $this->notable
            ->notes()
            ->where('id', '<', $this->id)
            ->orderBy('id', 'desc')
            ->first();
    }
    public function next()
    {
        return $this->notable
            ->notes()
            ->where('id', '>', $this->id)
            ->orderBy('id', 'asc')
            ->first();
    }
}
