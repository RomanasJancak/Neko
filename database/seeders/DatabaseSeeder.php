<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call($this->getAllModelSeeders());
        //$this->restoreBackup();




        // $model_baseName = class_basename($this->getAllModels()[0]);
        // $folderPath = resource_path('files\backups\\'.$model_baseName);
        // $filePath = $this->getLatestBackup($folderPath);
        // $modelClass = $this->getAllModels()[0];
        // $tableName = with(new $modelClass)->getTable();
        // //dd($tableName);
        // foreach($this->getAllModels() as $model){
        //     $this->seed($this->getLatestBackup(resource_path('files\backups\\'.class_basename($model)))
        //                 ,$model);
        // }
        // //
    }
    private function restoreBackup()
    {
        $directories = Storage::disk('backups')->directories('tables');
        $directories = $this->sortDirectoriesByMigrations($directories);
        foreach ($directories as $directory) {
            $files = Storage::disk('backups')->files($directory);
            //foreach ($files as $file) {
                usort($files, function($a, $b) {
                    return strcmp($b, $a);
                });
                $file = $files[0];
                $tableName = basename($directory);
                $csvData = Storage::disk('backups')->get($file);
                $lines = explode("\n", $csvData);
                $columns = str_getcsv(array_shift($lines));
    
                foreach ($lines as $line) {
                    if (trim($line) === '') {
                        continue;
                    }
                    $row = str_getcsv($line);
                    $data = array_combine($columns, $row);
    
                    // Check for empty strings and replace with NULL if the column allows NULL
                    foreach ($data as $key => $value) {
                        if ($value === '') {
                            // Get the column details only if the value is an empty string
                            $columnDetails = DB::select("SHOW COLUMNS FROM $tableName LIKE '{$key}'")[0];
                            // Convert empty strings to null if the column allows null values
                            if ($columnDetails->Null === 'YES') {
                                $value = null;
                            }
                            $data[$key] = $value;
                        }
                    }
    
                    DB::statement('INSERT INTO ' . $tableName . ' (' . implode(',', array_keys($data)) . ') VALUES (' . implode(',', array_map(function($value) {
                        return DB::getPdo()->quote($value);
                    }, $data)) . ')');
                }
            //}
        }
    }
    private function sortDirectoriesByMigrations($directories) 
    {

      // Step 1: Fetch migration names from the "migrations" table
      $migrations = DB::table('migrations')->orderBy('id')->pluck('migration')->toArray();

      // Step 2: Extract table names from migration names
      $orderedTableNames = array_map(function($migration) {
          // Assuming the migration name format is "YYYY_MM_DD_HHMMSS_create_table_name"
          preg_match('/create_(.*)_table/', $migration, $matches);
          return $matches[1] ?? null;
      }, $migrations);

      // Filter out null values
      $orderedTableNames = array_filter($orderedTableNames);

      // Step 3: Sort the directories based on the order of migrations
      usort($directories, function($a, $b) use ($orderedTableNames) {
          // Extract table names from directory names
          $tableA = basename($a);
          $tableB = basename($b);

          // Get positions in the ordered table names
          $posA = array_search($tableA, $orderedTableNames);
          $posB = array_search($tableB, $orderedTableNames);

          // Handle cases where the table is not found in the ordered table names
          $posA = $posA === false ? PHP_INT_MAX : $posA;
          $posB = $posB === false ? PHP_INT_MAX : $posB;

          return $posA - $posB;
      });
      $items = [
          "tables/roles",
          "tables/permissions",
          "tables/role_has_permissions",
          "tables/model_has_permissions",
          "tables/model_has_roles",        
      ];
      foreach ($items as $item) {
          $key = array_search($item, $directories);
          if ($key !== false) {
              unset($directories[$key]);
          }
      }
      $directories = array_merge($directories, $items);
      $directories = array_values($directories);
      return $directories;
    }
    private function sortBackupFilesByMigrations($directory) 
    {
      // Step 1: Fetch the list of files from the backup directory
      $files = Storage::disk('backups')->files($directory);

      // Step 2: Fetch migration names from the "migrations" table
      $migrations = DB::table('migrations')->orderBy('id')->pluck('migration')->toArray();

      // Step 3: Extract table names from migration names
      $orderedTableNames = array_map(function($migration) {
          // Assuming the migration name format is "YYYY_MM_DD_HHMMSS_create_table_name"
          preg_match('/create_(.*)_table/', $migration, $matches);
          return $matches[1] ?? null;
      }, $migrations);

      // Filter out null values
      $orderedTableNames = array_filter($orderedTableNames);

      // Step 4: Sort the files based on the order of migrations
      usort($files, function($a, $b) use ($orderedTableNames) {
          // Extract table names from file names
          preg_match('/_(.*)_backup_/', $a, $matchesA);
          preg_match('/_(.*)_backup_/', $b, $matchesB);
          $tableA = $matchesA[1] ?? null;
          $tableB = $matchesB[1] ?? null;

          // Get positions in the ordered table names
          $posA = array_search($tableA, $orderedTableNames);
          $posB = array_search($tableB, $orderedTableNames);

          return $posA - $posB;
      });

      return $files;
    }
    private function seed2($file,$model):void
    {
        //echo $tableName = with(new $model)->getTable();
        //return;
        //$file = $request->file('csv_file');

        // Check if a file was uploaded
        if ($file !== null) {
            // Create a new Reader object
            //$reader = Reader::createFromPath($file->getPathname(), 'r');
            $reader = Reader::createFromPath($file, 'r');
            $reader->setDelimiter(',');

            // Read and parse the CSV file
            $records = $reader->getRecords();
            //dd($records);
            // Process the CSV data
            $column_names = $reader->first();
            foreach ($records as $record) {
                // Each $record is an associative array representing a row in the CSV file
                // You can access individual columns using array keys
                // For example:
                //dd($record);
                $column1 = $record[0];
                $column2 = $record[1];
                //dd($column1);
                // Process the data here...
            }
            $tableName = with(new $model)->getTable();
            //echo $tableName.'<br>';
            // Optionally, you can convert the records to an array for further manipulation
            $parsedData = iterator_to_array($records);
            for($i=1;$i < count($parsedData);$i++){
                $array = [];
                for($j=0;$j < count($column_names);$j++){
                    if (Schema::hasColumn($tableName, $column_names[$j])) {
                        $value = $parsedData[$i][$j];
                        if ($value === '') {
                            // Get the column details only if the value is an empty string
                            $columnDetails = DB::select("SHOW COLUMNS FROM $tableName LIKE '{$column_names[$j]}'")[0];
                            // Convert empty strings to null if the column allows null values
                            if ($columnDetails->Null === 'YES') {
                                $value = null;
                            }
                        }
                        $array[$column_names[$j]] = $value;
                    }else{
                        
                    }
                    
                }
                $model::create($array);
                
            }
            // Return or do something with the parsed data
            //return response()->json(['data' => $parsedData], 200);
        }

        // Handle cases where no valid file was uploaded
        //return response()->json(['error' => 'No valid file uploaded.'], 400);
    }
    private function getLatestBackup($directory)
    {
        $files = scandir($directory);
        $files = array_diff($files, array('.', '..'));
        $files = array_filter($files, function ($file) use ($directory) {
            return is_file($directory . '/' . $file);
        });
        sort($files);
        $filepath = $directory.'/'.end($files);
        return $filepath;
    }
    private function getAllModels()
    {
        $models = [];
        $modelsPath = app_path('Models'); // Models directory path
        $namespace = 'App\\Models\\'; // Models namespace

        // Scan the Models directory for model files
        $modelFiles = File::allFiles($modelsPath);
        foreach ($modelFiles as $file) {
            // Get the model name
            $modelName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $models[] = $namespace . $modelName;
        }

        return $models;
    
    }
    private function getAllModelSeeders(): array
    {
        $seeders = [];
        $modelsPath = app_path('Models'); // Models directory path
        $namespace = 'Database\\Seeders\\'; // Seeders namespace

        // Scan the Models directory for model files
        $modelFiles = File::allFiles($modelsPath);
        foreach ($modelFiles as $file) {
            // Get the model name
            $modelName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $seederClass = $namespace . $modelName . 'Seeder';

            // Check if the corresponding seeder class exists
            if (class_exists($seederClass)) {
                $seeders[] = $seederClass;
            }
        }

        return $seeders;
    }
    private function sortTableNamesByMigrations($tableNames) {
        // Step 1: Fetch migration names from the "migrations" table
        $migrations = DB::table('migrations')->orderBy('id')->pluck('migration')->toArray();
    
        // Step 2: Extract table names from migration names
        $orderedTableNames = array_map(function($migration) {
            // Assuming the migration name format is "YYYY_MM_DD_HHMMSS_create_table_name"
            preg_match('/create_(.*)_table/', $migration, $matches);
            return $matches[1];
        }, $migrations);
    
        // Step 3: Sort $tableNames based on the extracted table names
        usort($tableNames, function($a, $b) use ($orderedTableNames) {
            $posA = array_search($a, $orderedTableNames);
            $posB = array_search($b, $orderedTableNames);
            return $posA - $posB;
        });
    
        return $tableNames;
    }
}
