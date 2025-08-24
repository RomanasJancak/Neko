<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTemplate extends Job
{
    use HasFactory;
    protected $fillable = ['name'];
    public function tasks(){
        return json_decode(json_encode([
            'pickup' => json_decode($this->pickuptask_data),
            'dropOffs' => json_decode($this->dropOffs_data),
            'return' => json_decode($this->return_data),
        ]));
    }
    public function clientToBill()
    {
        return $this->belongsTo(Client::class, 'clientToBill_id');
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
    public function lockedFields()
    {
        return \Illuminate\Support\Facades\DB::table('locked_fields')
            ->where('model', 'job_template')
            ->where('model_id', $this->id)
            ->get()
            ->toArray();
    }
    public function isLocked($fieldName)
    {
        $lockedField = \Illuminate\Support\Facades\DB::table('locked_fields')
            ->where('model', 'job_template')
            ->where('model_id', $this->id)
            ->where('field_name', $fieldName)
            ->first();

        return $lockedField ? $lockedField->is_locked : false;
    }
    public function changeLockedField($fieldName, $isLocked)
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
}
