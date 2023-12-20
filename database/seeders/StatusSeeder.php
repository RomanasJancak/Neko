<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

use App\Models\Status;



use League\Csv\Reader;


class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'unassigned',//1
            'assigned',//2
            'accepted',//3
            'completed',//4
            'declined',//5
            'issue',//6
            'proposed',//7
            'completedwithIssue',//8
        ];
        $colors =   [
            '#808080',
            '#3d85c6',
            '#d9ead3',
            '#274e13',
            '#a64d79',
            '#cc0000',
            '#7f6000',
            '#e69138',
        ];
        // foreach ($statuses as $index => $status) {
        //     Status::create([
        //         'name' => $status,
        //         'color_main' => $colors[$index], // Use the corresponding color from the $colors array
        //         'color_pickup' => $colors[$index],
        //         'color_dropoff' => $colors[$index],
        //     ]);
        // }
        $folderPath = resource_path('files\backups\status');
        $filePath = $this->getLatestBackup($folderPath);
        $this->seed($filePath);
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
                // Each $record is an associative array representing a row in the CSV file
                // You can access individual columns using array keys
                // For example:
                //dd($record);
                $column1 = $record[0];
                $column2 = $record[1];
                //dd($column1);
                // Process the data here...
            }
            $tableName = with(new Status)->getTable();
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
                Status::create($array);
                
            }
            // Return or do something with the parsed data
            //return response()->json(['data' => $parsedData], 200);
        }

        // Handle cases where no valid file was uploaded
        //return response()->json(['error' => 'No valid file uploaded.'], 400);
    }
}
