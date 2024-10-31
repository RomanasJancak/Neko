<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class BackupService
{
    public static function createBackup($model)
    {
        $modelName = class_basename($model);
        $timestamp = now()->format('Y_m_d_His');

        // Define directory and file path
        $directory = $modelName;
        $filePath = $directory . '/' . strtolower($modelName) . '.backup_' . $timestamp . '.csv';

        // Ensure directory exists
        Storage::disk('backups')->makeDirectory($directory);


        $models = $model::all();
        $columns = Schema::getColumnListing((new $model)->getTable());
        $csvData = implode(',', $columns) . "\n";
        foreach ($models as $model) {
          $rowData = [];
          foreach ($columns as $column) {
            $rowData[] = $model->{$column};
          }
          $csvData .= implode(',', $rowData) . "\n";
        }



        // Write to backup file
        Storage::disk('backups')->put($filePath, $csvData);
    }
}
