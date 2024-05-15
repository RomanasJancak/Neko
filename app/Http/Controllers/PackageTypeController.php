<?php

namespace App\Http\Controllers;

use App\Models\PackageType;
use App\Models\Client;
use App\Models\Client_PackageType;
use App\Http\Requests\StorePackageTypeRequest;
use App\Http\Requests\UpdatePackageTypeRequest;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class PackageTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::orderBy('name', 'asc')->get();
        $packageTypes = PackageType::orderBy('id', 'asc')->paginate(10);

        return view('packageType.index', compact('packageTypes','clients'));
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
    public function store(StorePackageTypeRequest $request)
    {
        // return response()->json([
        //     'message' => 'Package type created successfully.',
        //     'packageType' => $request->input('selected_clients')
        // ]);
        $packageType = new PackageType();
        $packageType->name = $request->name;
        $packageType->baseQuantityThreshold =   $request->baseQuantityThreshold;
        $packageType->maxQuantityThreshold =   $request->maxQuantityThreshold;
        $packageType->price                 =   intval(str_replace('.', '', $request->input('priceField')));
        $packageType->save();
        foreach($request->selected_clients as $selected_client ){
            $packageType->clients()->attach($selected_client);
        }
        

        //$packageType->clients()->attach($request->packageTypeClientId, ['price' => $request->priceField]);
        $packageType->save();
        return response()->json([
            'message' => 'Package type created successfully.',
            'packageType' => $packageType
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(PackageType $packageType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PackageType $packageType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePackageTypeRequest $request, PackageType $packageType)
    {
        // return response()->json([
        //     $request->input()
        // ]);
        $packageType = PackageType::findOrFail($request->packageTypeId);
        $packageType->name = $request->name;
        $packageType->baseQuantityThreshold =   $request->baseQuantityThreshold;
        $packageType->maxQuantityThreshold =   $request->maxQuantityThreshold;
        $packageType->price                 =   intval(str_replace('.', '', $request->input('priceField')));
        $packageType->clients()->detach();
        foreach($request->selected_clients as $selected_client ){
            $packageType->clients()->attach($selected_client);
        }
        $packageType->save();

        return response()->json([
            'message' => 'PackageType updated successfully. '.$request->packageTypeClientIdOld.' '.$request->packageTypeClientId
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,PackageType $packageType)
    {

        $packageType = PackageType::findOrFail($request->packageTypeId);
        $packageType->clients()->detach();
        $packageType->delete();
        return response()->json([
            'message' => 'PackageType deleted successfully.'.$request->packageTypeId.' '.$request->name
        ]);

    }
    public function getPackageTypeInfo($packageTypeId)
    {
        // Fetch the client's information based on the $clientId
        $packageType = PackageType::find($packageTypeId);

        if ($packageType) {
            return response()->json([
                'name'                  => $packageType->name,
                'price'                 => $packageType->price,
                'baseQuantityThreshold' => $packageType->baseQuantityThreshold,
                'maxQuantityThreshold'  => $packageType->maxQuantityThreshold,
                'clients' => $packageType->clients->map(function ($client) {
                    return [
                        'id'    => $client->id,
                        'name' => $client->name,
                    ];
                }),  
                ]);
        }

        return response()->json(['error' => 'Client not found'], 404);
    }
    public function getPackageTypesForClient(Request $request)
    {
        try{
            $client = Client::findOrFail($request->clientId);

            return response()->json([
                'message' => 'Client found successfully.'
            ]);
        } catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),], 500);
        }
    }
    public function createBackup()
    {
        //dd(Client_PackageType::all());
        $packageTypes = PackageType::all();        
        $columns = Schema::getColumnListing('package_types'); 

        $csvData = implode(',', $columns) . "\n";
        foreach ($packageTypes as $packageType) {
            $rowData = [];
            foreach ($columns as $column) {
                $rowData[] = $packageType->{$column};
            }
            $csvData .= implode(',', $rowData) . "\n";
        }
        $timestamp = date('Y-m-d_H-i-s');
        $file_path = resource_path('files/backups/PackageType/packagetype.backup_'.$timestamp.'.csv');

        file_put_contents($file_path, $csvData);

        $clientPackageTypes = Client_PackageType::all();
        $columns = Schema::getColumnListing('client__package_types'); 

        $csvData = implode(',', $columns) . "\n";
        foreach ($clientPackageTypes as $clientPackageType) {
            $rowData = [];
            foreach ($columns as $column) {
                $rowData[] = $clientPackageType->{$column};
            }
            $csvData .= implode(',', $rowData) . "\n";
        }
        $timestamp = date('Y-m-d_H-i-s');
        $file_path = resource_path('files/backups/ClientPackageType/clientpackagetype.backup_'.$timestamp.'.csv');

        file_put_contents($file_path, $csvData);

        return redirect()->back()->with('succeses', 'PackageType backup created successfully.');
    }
}
