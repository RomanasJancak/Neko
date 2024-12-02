<?php

namespace App\Http\Controllers;

use App\Models\PackageType;
use App\Models\AddOnRule;
use App\Models\Client;
use App\Models\ClientPackageType;
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
        $addOnRules = AddOnRule::orderBy('name', 'asc')->get();

        return view('packageType.index', compact('packageTypes','clients','addOnRules'));
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
        foreach($request->selected_addOnRules as $selected_addOnRule ){
            $packageType->addOnRules()->attach($selected_addOnRule);
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
        try{
        $packageType = PackageType::findOrFail($request->packageTypeId);
        $packageType->name = $request->name;
        $packageType->baseQuantityThreshold =   $request->baseQuantityThreshold;
        $packageType->maxQuantityThreshold =   $request->maxQuantityThreshold;
        $packageType->price                 =   intval(str_replace('.', '', $request->input('priceField')));
        $packageType->clients()->detach();
        foreach($request->selected_clients as $selected_client ){
            $packageType->clients()->attach($selected_client);
        }
        $packageType->addOnRules()->detach();
        isset($request->selected_addOnRules) ? $packageType->addOnRules()->detach() : null;        
        foreach($request->selected_addOnRules as $selected_addOnRule ){
            $packageType->addOnRules()->attach($selected_addOnRule);
        }
        $packageType->save();

        return response()->json([
            'message' => 'PackageType updated successfully',
            'addOns' => $request->all(),
        ]);
        }         catch (\Exception $e){
        return response()->json(['error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),], 500);
    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,PackageType $packageType)
    {

        $packageType = PackageType::findOrFail($request->packageTypeId);
        $packageType->clients()->detach();
        $packageType->addOnRules()->detach(); 
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
                'addOns' => is_null($packageType->addOnRules)? 'none' : $packageType->addOnRules->map(function ($addOn) {
                    return [
                        'id'    => $addOn->id,
                        'name' => $addOn->name,
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

}
