<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovedPostalCodeArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
    ];
    public function parent(){
        return $this->belongsTo(ApprovedPostalCodeArea::class, 'parent_id');
    }
    public function children(){
        return $this->hasMany(ApprovedPostalCodeArea::class, 'parent_id');
    }
    public function area(){
        if ($this->type === 'area') {
            return $this->name;
        } else{
        return preg_replace('/[^a-zA-Z].*$/', '', $this->name);
        }
    }
    public function district (){
        if ($this->type === 'area') {
            return preg_replace('/[^a-zA-Z]/', '', $this->name);
        } elseif ($this->type === 'district') {
            return $this->name;
        } else{
            return preg_replace('/[^a-zA-Z0-9]/', '', $this->name);
        }
    }
    public function subdistrict(){
        if ($this->type === 'area') {
            return preg_replace('/[^a-zA-Z]/', '', $this->name);
        } elseif ($this->type === 'district') {
            return $this->name;
        } else{
            return preg_replace('/[^a-zA-Z0-9]/', '', $this->name);
        }
    }
}
