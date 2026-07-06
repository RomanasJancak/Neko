<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address_line_1',
        'address_line_2',
        'city',
        'country',
        'postal_code_id',
        'model',
        'model_id',
    ];

    public function postalCode()
    {
        return $this->belongsTo(PostalCode::class);
    }
    public function addNewPostalCode($postalCode){
        $postalCode = new PostalCode(['postal_code' => $postalCode]);
        $postalCode->save();
        $this->postal_code_id = $postalCode->id;   
        $this->save();
        return $postalCode;
    }
    //
}
