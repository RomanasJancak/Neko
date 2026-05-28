<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\Csv\Reader;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (auth()->user()->hasRole('courier')) {
            return redirect()->route('courier.today');
        }

        return view('home');
    }
    public function parseCSV(Request $request)
    {
        // Retrieve the uploaded CSV file
        $file = $request->file('csv_file');

        // Check if a file was uploaded
        if ($file !== null && $file->isValid()) {
            // Create a new Reader object
            $reader = Reader::createFromPath($file->getPathname(), 'r');
            $reader->setDelimiter(',');

            // Read and parse the CSV file
            $records = $reader->getRecords();
            //dd($records);
            dd(config('services.google_maps.api_key'));
            // Process the CSV data
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

            // Optionally, you can convert the records to an array for further manipulation
            $parsedData = iterator_to_array($records);
            dd($parsedData);
            // Return or do something with the parsed data
            return response()->json(['data' => $parsedData], 200);
        }

        // Handle cases where no valid file was uploaded
        return response()->json(['error' => 'No valid file uploaded.'], 400);
    }
}
