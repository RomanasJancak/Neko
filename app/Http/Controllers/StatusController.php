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
    public function index(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage', 10);
        $statuses = Status::orderBy('id', 'asc')->paginate($itemsPerPage);

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
        $status->color_return =   $request->input('color-return');
        $status->color_custom =   $request->input('color-custom');
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
        $status->color_return =   $request->input('color-return');
        $status->color_custom =   $request->input('color-custom');
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
    public function getStatusInfo($statusId)
    {
        // Fetch the client's information based on the $clientId
        $status = Status::find($statusId);

        if ($status) {
            return response()->json([
                'id'                =>  $status->id,
                'color_main'        =>   $status->color_main,
                'color_pickup'        =>   $status->color_pickup,
                'color_dropoff'        =>   $status->color_dropoff,
                'color_return'        =>   $status->color_return,
                'color_custom'        =>   $status->color_custom,
                ]);
        }

        return response()->json(['error' => 'Job not found'], 404);
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
