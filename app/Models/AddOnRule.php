<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Schema;

class AddOnRule extends Model
{
    use HasFactory;
    protected $fillable = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->fillable = array_diff(
            Schema::getColumnListing($this->getTable()),
            ['id', 'created_at', 'updated_at']
        );
    }
}
