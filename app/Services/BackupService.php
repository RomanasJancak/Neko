<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PDO;

class BackupService
{
    public static function createBackup2($model)
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

    public static function createBackup($randomVariable = null)
    {
      $tableNames = DB::select('SHOW TABLES');
      $tableNames = array_map('current', $tableNames);


      foreach ($tableNames as $tableName) {
        
        if($tableName == 'migrations'){
            continue;
        }
        $directory = 'tables/' . $tableName;
        Storage::disk('backups')->makeDirectory($directory);

        // Create CSV file path
        $fileName = $directory . '/' . $tableName . '_backup_' . now()->format('Y_m_d_His') . '.csv';

        // Get table data
        $tableData = DB::table($tableName)->get();

        // Get column names
        $csvData = '';
        if ($tableData->isNotEmpty()) {
          $csvData .= implode(',', array_keys((array)$tableData->first())) . "\n";
        }

        // Populate CSV data with table data
        foreach ($tableData as $row) {
          $csvData .= implode(',', (array)$row) . "\n";
        }

        // Write to backup file
        Storage::disk('backups')->put($fileName, $csvData);
      }
    }
}
