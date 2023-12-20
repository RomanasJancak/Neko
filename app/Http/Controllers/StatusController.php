<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Http\Requests\StoreStatusRequest;
use App\Http\Requests\UpdateStatusRequest;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = Status::orderBy('id', 'asc')->paginate(10);

        return view('status.index', compact('statuses'));
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
    public function store(StoreStatusRequest $request)
    {
        $status = new Status();
        $status->name = $request->name;
        $status->color_main =   $request->input('color-main');
        $status->color_pickup =   $request->input('color-pickup');
        $status->color_dropoff =   $request->input('color-dropoff');
        $status->save();
        return response()->json([
            'message' => 'Status created successfully.',
            'status' => $status
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Status $status)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Status $status)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStatusRequest $request, Status $status)
    {
        $status = Status::findOrFail($request->statusid);
        $status->name = $request->name;
        $status->color_main =   $request->input('color-main');
        $status->color_pickup =   $request->input('color-pickup');
        $status->color_dropoff =   $request->input('color-dropoff');
        $status->save();

        return response()->json([
            'message' => 'Status updated successfully. '.$status,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,Status $status)
    {
        $status = Status::findOrFail($request->statusid);
        $status->delete();

        return response()->json([
            'message' => 'Status deleted successfully.'
        ]);
    }
    public function createBackup()
    {
        $clients = Status::all();        
        $columns = Schema::getColumnListing('statuses'); 

        $csvData = implode(',', $columns) . "\n";
        foreach ($clients as $client) {
            $rowData = [];
            foreach ($columns as $column) {
                $rowData[] = $client->{$column};
            }
            $csvData .= implode(',', $rowData) . "\n";
        }
        $timestamp = date('Y-m-d_H-i-s');
        $file_path = resource_path('files/backups/Status/status.backup_'.$timestamp.'.csv');

        file_put_contents($file_path, $csvData);
        return redirect()->back()->with('succeses', 'Status backup created successfully.');
    }
}
