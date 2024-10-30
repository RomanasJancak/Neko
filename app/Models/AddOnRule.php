<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Schema;

class AddOnRule extends Model
{
    use HasFactory;
    protected $fillable = [];
    public $timestamps = true;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->fillable = array_diff(
            Schema::getColumnListing($this->getTable()),
            ['id', 'created_at', 'updated_at']
        );
    }
    
    public static function getAllThatAreApplicableToThisDate($date)
    {
        
        $formattedDatetime = Carbon::parse($date);
        
        $rules = AddOnRule::where('begin_date', '<=', $formattedDatetime)
            ->where('end_date', '>=', $formattedDatetime)->get();
        return $rules;
    }
    public static function getAllThatAreApplicableToThisDateForSpecificClient($date,$clientId)
    {
        
        $formattedDatetime = Carbon::parse($date);
        
        // $rules = AddOnRule::where('begin_date', '<=', $formattedDatetime)
        //     ->where('end_date', '>=', $formattedDatetime)
        //     ->where('client_id','=',$clientId)
        //     ->get();
        // return $rules;
        $rules = AddOnRule::where('begin_date', '<=', $formattedDatetime)
        ->where('end_date', '>=', $formattedDatetime)
        ->whereHas('clients', function ($query) use ($clientId) {
            $query->where('client_id', $clientId);
        })
        ->get();
        return $rules;
    }
    public static function getAllThatAreApplicableToThisDateForSpecificClientByPatern($date,$clientId,$prefix)
    {
        
        $formattedDatetime = Carbon::parse($date);
        
        $rules = AddOnRule::where('begin_date', '<=', $formattedDatetime)
            ->where('end_date', '>=', $formattedDatetime)
            ->whereHas('clients', function ($query) use ($clientId) {
                $query->where('client_id', $clientId);
            })
            ->where('name', 'like', $prefix . '%')
            ->get();
        return $rules;
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_add_on_rules')->withTimestamps();
    }
    
}
