<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{

    use HasFactory;

    protected $fillable = [ 'name',
                            'number',
                            'client_user',
                            'balance',
                            'paid_to_date',
                            'client_currency',
                            'website',
                            'private_notes',
                            'client_phone',
                            'address_line',
                            'postal_code',
                            'city',
                            'country',
                            'pickup_adress_line',
                            'pickup_postal_code',
                            'pickup_city',
                            'pickup_country',
                            'email',
                            'vat','regNumber','address','note'];
    public function packageTypes()
    {
        return $this->belongsToMany(PackageType::class,'client__package_types');
    }
    public function jobs(){
        return $this->hasmany(Job::class,'clientToBill_id');
    }
}
       
// $table->string('dropoff_adress_line')->nullable();//CSV
// $table->string('dropoff_postal_code')->nullable();//CSV
// $table->string('dropoff_city')->nullable();//CSV
// $table->string('dropoff_country')->nullable();//CSV
// $table->string('public_notes')->nullable();//CSV
// $table->string('contact_phone')->nullable();//CSV
// $table->string('first_name')->nullable();//CSV
// $table->string('last_name')->nullable();//CSV
// $table->string('email')->nullable();
// $table->string('Credit_Balance')->nullable();//CSV
// //==================================
// $table->string('vat')->nullable();
// $table->string('address')->default('Default Address');
// $table->string('note')->nullable()->default('');
// // $table->unsignedBigInteger('contactPersonForPickup')->default('0')->nullable();
// $table->string('receiverContacts')->nullable();
// $table->string('collection_details')->nullable();
// $table->string('dropoff_details')->nullable();