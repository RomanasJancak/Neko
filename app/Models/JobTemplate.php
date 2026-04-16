<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * JobTemplate Model
 * 
 * Represents a template for creating batch jobs.
 * Jobs created from templates are fixed-price with locked fields.
 * Only the date field can be freely changed when creating jobs.
 */
class JobTemplate extends Model
{
    use HasFactory;

    protected $table = 'job_templates';
    
    protected $fillable = [
        'name',
        'client_id',
        'pickup_address_id',
        'pickup_time_begin',
        'pickup_time_end',
        'template_data', // JSON: stores pickup, dropoffs, return info
    ];

    protected $casts = [
        'template_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Template belongs to a Client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relationship: Template has many Jobs created from it
     */
    public function jobs()
    {
        return $this->hasMany(Job::class, 'job_template_id');
    }

    /**
     * Get all locked fields for this template
     */
    public function lockedFields()
    {
        return \Illuminate\Support\Facades\DB::table('locked_fields')
            ->where('model', 'job_template')
            ->where('model_id', $this->id)
            ->get()
            ->toArray();
    }

    /**
     * Check if a specific field is locked
     */
    public function isLocked($fieldName)
    {
        $lockedField = \Illuminate\Support\Facades\DB::table('locked_fields')
            ->where('model', 'job_template')
            ->where('model_id', $this->id)
            ->where('field_name', $fieldName)
            ->first();

        return $lockedField ? $lockedField->is_locked : false;
    }

    /**
     * Set lock status for a field
     * If parent field is locked, all children are considered locked
     */
    public function setLockedField($fieldName, $isLocked)
    {
        $lockedField = \Illuminate\Support\Facades\DB::table('locked_fields')
            ->where('model', 'job_template')
            ->where('model_id', $this->id)
            ->where('field_name', $fieldName)
            ->first();

        if ($lockedField) {
            \Illuminate\Support\Facades\DB::table('locked_fields')
                ->where('id', $lockedField->id)
                ->update(['is_locked' => $isLocked]);
        } else {
            \Illuminate\Support\Facades\DB::table('locked_fields')->insert([
                'model' => 'job_template',
                'model_id' => $this->id,
                'field_name' => $fieldName,
                'is_locked' => $isLocked,
            ]);
        }
    }

    /**
     * Get all child fields of a parent field
     * Example: if 'dropoff_1' is parent, returns 'dropoff_1.address', 'dropoff_1.time', etc.
     */
    public function getChildFields($parentField)
    {
        return \Illuminate\Support\Facades\DB::table('locked_fields')
            ->where('model', 'job_template')
            ->where('model_id', $this->id)
            ->where('field_name', 'like', $parentField . '%')
            ->get()
            ->toArray();
    }

    /**
     * Lock all child fields when parent is locked
     */
    public function lockChildFields($parentField)
    {
        \Illuminate\Support\Facades\DB::table('locked_fields')
            ->where('model', 'job_template')
            ->where('model_id', $this->id)
            ->where('field_name', 'like', $parentField . '%')
            ->update(['is_locked' => true]);
    }

    /**
     * Unlock all child fields when parent is unlocked
     */
    public function unlockChildFields($parentField)
    {
        \Illuminate\Support\Facades\DB::table('locked_fields')
            ->where('model', 'job_template')
            ->where('model_id', $this->id)
            ->where('field_name', 'like', $parentField . '%')
            ->update(['is_locked' => false]);
    }

    // ==============================================
    // COMMENTED OLD CODE - USE AS REFERENCE ONLY
    // ==============================================
    /*
    public function tasks(){
        return json_decode(json_encode([
            'pickup' => json_decode($this->pickuptask_data),
            'dropOffs' => json_decode($this->dropOffs_data),
            'return' => json_decode($this->return_data),
        ]));
    }

    public function changePackageTypeForDropoff($orderNumber, $packageTypeId)
    {
        $packageType  = PackageType::find($packageTypeId);
        $dropOffs = json_decode($this->dropOffs_data, true);
        foreach ($dropOffs as &$dropOff) {
          if (isset($dropOff['order_number']) && $dropOff['order_number'] === $orderNumber) {
            $dropOff['package']['package_type']['id'] = $packageType->id;
            $dropOff['package']['package_type']['name'] = $packageType->name;
            break;
          }
        }
        $this->dropOffs_data = $dropOffs;
        $this->save();
    }
    */
}
