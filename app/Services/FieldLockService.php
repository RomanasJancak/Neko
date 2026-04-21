<?php

namespace App\Services;

use App\Models\LockedField;

class FieldLockService
{
    public function getLockedFields(string $model, int $modelId): array
    {
        return LockedField::query()
            ->where('model', $model)
            ->where('model_id', $modelId)
            ->get()
            ->all();
    }

    public function isLocked(string $model, int $modelId, string $fieldName): bool
    {
        $lockedField = LockedField::query()
            ->where('model', $model)
            ->where('model_id', $modelId)
            ->where('field_name', $fieldName)
            ->first();

        return $lockedField ? (bool) $lockedField->is_locked : false;
    }

    public function setLock(string $model, int $modelId, string $fieldName, bool $isLocked): void
    {
        $lockedField = LockedField::query()
            ->where('model', $model)
            ->where('model_id', $modelId)
            ->where('field_name', $fieldName)
            ->first();

        if ($lockedField) {
            $lockedField->update(['is_locked' => $isLocked]);
            return;
        }

        LockedField::query()->create([
            'model' => $model,
            'model_id' => $modelId,
            'field_name' => $fieldName,
            'is_locked' => $isLocked,
        ]);
    }

    public function getChildFields(string $model, int $modelId, string $parentField): array
    {
        return LockedField::query()
            ->where('model', $model)
            ->where('model_id', $modelId)
            ->where('field_name', 'like', $parentField . '%')
            ->get()
            ->all();
    }

    public function setChildLocks(string $model, int $modelId, string $parentField, bool $isLocked): void
    {
        LockedField::query()
            ->where('model', $model)
            ->where('model_id', $modelId)
            ->where('field_name', 'like', $parentField . '%')
            ->update(['is_locked' => $isLocked]);
    }
}
