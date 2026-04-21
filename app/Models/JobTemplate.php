<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\FieldLockService;

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
        return app(FieldLockService::class)->getLockedFields('job_template', (int) $this->id);
    }

    /**
     * Check if a specific field is locked
     */
    public function isLocked($fieldName)
    {
        return app(FieldLockService::class)->isLocked('job_template', (int) $this->id, (string) $fieldName);
    }

    /**
     * Set lock status for a field
     * If parent field is locked, all children are considered locked
     */
    public function setLockedField($fieldName, $isLocked)
    {
        app(FieldLockService::class)->setLock('job_template', (int) $this->id, (string) $fieldName, (bool) $isLocked);
    }

    /**
     * Get all child fields of a parent field
     * Example: if 'dropoff_1' is parent, returns 'dropoff_1.address', 'dropoff_1.time', etc.
     */
    public function getChildFields($parentField)
    {
        return app(FieldLockService::class)->getChildFields('job_template', (int) $this->id, (string) $parentField);
    }

    /**
     * Lock all child fields when parent is locked
     */
    public function lockChildFields($parentField)
    {
        app(FieldLockService::class)->setChildLocks('job_template', (int) $this->id, (string) $parentField, true);
    }

    /**
     * Unlock all child fields when parent is unlocked
     */
    public function unlockChildFields($parentField)
    {
        app(FieldLockService::class)->setChildLocks('job_template', (int) $this->id, (string) $parentField, false);
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
