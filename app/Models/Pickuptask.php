<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pickuptask extends Model
{
    use HasFactory;
    public function pickupAddressShort(){
        return $this->pickupclientaddressline.' '.$this->pickupclientpostalcode;
    }
}
