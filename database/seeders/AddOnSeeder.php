<?php

namespace Database\Seeders;

use App\Models\AddOn;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use League\Csv\Reader;

class AddOnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $folderPath = resource_path('files\backups\AddOn');
        // $filePath = $this->getLatestBackup($folderPath);
        // $this->seed($filePath);
    }
    private function getLatestBackup($directory){
        $files = scandir($directory);
        $files = array_diff($files, array('.', '..'));
        $files = array_filter($files, function ($file) use ($directory) {
            return is_file($directory . '/' . $file);
        });
        sort($files);
        $filepath = $directory.'/'.end($files);
        return $filepath;
    }
    private function seed($file):void
    {
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
                //dd($record);
                // Each $record is an associative array representing a row in the CSV file
                // You can access individual columns using array keys
                // For example:
                //dd($record);
                //dd($column1);
                // Process the data here...
            }
            $tableName = with(new AddOn)->getTable();
            // Optionally, you can convert the records to an array for further manipulation
            $parsedData = iterator_to_array($records);
            for($i=1;$i < count($parsedData);$i++){
                $array = [];
                for($j=0;$j < count($column_names);$j++){
                    if (Schema::hasColumn($tableName, $column_names[$j])) {
                        $array[$column_names[$j]] = $parsedData[$i][$j];
                    }else{
                        
                    }
                    
                }
                AddOn::create($array);
            }
            // Return or do something with the parsed data
            //return response()->json(['data' => $parsedData], 200);
        }

        // Handle cases where no valid file was uploaded
        //return response()->json(['error' => 'No valid file uploaded.'], 400);
    }
    
}
