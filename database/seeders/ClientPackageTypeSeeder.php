<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\ClientPackageType;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use League\Csv\Reader;

class ClientPackageTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $folderPath = resource_path('files\backups\ClientPackageType');
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
            $tableName = with(new ClientPackageType)->getTable();
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
                ClientPackageType::create($array);
                
            }
            // Return or do something with the parsed data
            //return response()->json(['data' => $parsedData], 200);
        }

        // Handle cases where no valid file was uploaded
        //return response()->json(['error' => 'No valid file uploaded.'], 400);
    }
}




// {
//     /**
//      * Run the database seeds.
//      */
//     public function run(): void
//     {
//         $packageTypes = [
//             ['client_id' => 1,'package_type_id' => 2,'price' => 111],
//             ['client_id' => 1,'package_type_id' => 4,'price' => 222],
//             ['client_id' => 1,'package_type_id' => 6,'price' => 333],
//             ['client_id' => 1,'package_type_id' => 8,'price' => 444],
//             ['client_id' => 1,'package_type_id' => 10,'price' => 555],
//             ['client_id' => 2,'package_type_id' => 1,'price' => 666],
//             ['client_id' => 2,'package_type_id' => 3,'price' => 777],
//             ['client_id' => 2,'package_type_id' => 5,'price' => 888],
//             ['client_id' => 2,'package_type_id' => 7,'price' => 999],
//             ['client_id' => 2,'package_type_id' => 9,'price' => 1010],
//         ];
//         foreach($packageTypes as $packageType){
//             ClientPackageType::create([
//                 'client_id'=> $packageType['client_id'],
//                 'package_type_id' => $packageType['package_type_id'],
//                 'price' => $packageType['price'],
//             ]);
//         }
//     }
// }
