<?php

namespace App\Http\Controllers;

use App\Models\AddOn;
use App\Http\Requests\StoreAddOnRequest;
use App\Http\Requests\UpdateAddOnRequest;

class AddOnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAddOnRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(AddOn $addOn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AddOn $addOn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAddOnRequest $request, AddOn $addOn)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AddOn $addOn)
    {
        //
    }
    public function createBackup()
    {
        $clients = AddOn::all();        
        $columns = Schema::getColumnListing('add_ons'); 

        $csvData = implode(',', $columns) . "\n";
        foreach ($clients as $client) {
            $rowData = [];
            foreach ($columns as $column) {
                $rowData[] = $client->{$column};
            }
            $csvData .= implode(',', $rowData) . "\n";
        }
        $timestamp = date('Y-m-d_H-i-s');
        $file_path = resource_path('files/backups/AddOn/add_on.backup_'.$timestamp.'.csv');

        file_put_contents($file_path, $csvData);
        return redirect()->back()->with('succeses', 'Client backup created successfully.');
    }
}
