<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::latest()->paginate(10);

        return view('client.index'
        , compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('client.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        try {
            $client = new Client();
            $client->name = $request->input('clientname');
            $client->shortenedName = $request->input('shortenedName');
            $client->country = $request->input('reg-addr-country');
            $client->city = $request->input('reg-addr-city');
            $client->city = $request->input('reg-addr-postal_code');
            $client->city = $request->input('reg-addr-address_line');
            //address_id
            if($request->name ?? false){
                foreach($request->name as $key => $value){
                    if (isset(
                            //$request->type[$key], 
                            $request->postal_code[$key], 
                            $request->city[$key], 
                            $request->country[$key]
                            )&&
                            ((isset($request->address_line_1[$key]))||(isset($request->address_line_2[$key])))) 
                        {
                        $client->createAndAddNewAddress($request->address_id[$key],$value, '$request->type[$key]', $request->address_line_1[$key], isset($request->address_line_2[$key])?isset($request->address_line_2[$key]):'', $request->postal_code[$key], $request->city[$key], $request->country[$key]);
                        }
                }
            }
    
    
            $client->save();

            return response()->json(['message' => 'Client created successfully', 'client' => $client], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $allClients = Client::all()->pluck('id')->toArray();
        $currentIndex = array_search($client->id, $allClients);
    
        $totalClients = count($allClients);
        $previousIndex = ($currentIndex - 1 + $totalClients) % $totalClients;
        $nextIndex = ($currentIndex + 1) % $totalClients;
    
        $previousClientId = $allClients[$previousIndex];
        $nextClientId = $allClients[$nextIndex];
    
        $previousClient = Client::find($previousClientId);
        $nextClient = Client::find($nextClientId);
    
        return view('client.show', [
            'client' => $client,
            'previousClient' => $previousClient,
            'nextClient' => $nextClient,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        //
        return view('client.edit', ['client' => $client]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request)
    {
        try {
        $client = Client::find($request->clientid);
        
        $client->name = $request->input('clientname');
        $client->shortenedName = $request->input('shortenedName');
        $client->country = $request->input('reg-addr-country');
        $client->city = $request->input('reg-addr-city');
        $client->city = $request->input('reg-addr-postal_code');
        $client->city = $request->input('reg-addr-address_line');
        //address_id
        if($request->name ?? false){
            foreach($request->name as $key => $value){
                if (isset(
                        //$request->type[$key], 
                        $request->postal_code[$key], 
                        $request->city[$key], 
                        $request->country[$key]
                        )&&
                        ((isset($request->address_line_1[$key]))||(isset($request->address_line_2[$key])))) 
                    {
                    $client->createAndAddNewAddress($request->address_id[$key],$value, '$request->type[$key]', $request->address_line_1[$key], isset($request->address_line_2[$key])?isset($request->address_line_2[$key]):'', $request->postal_code[$key], $request->city[$key], $request->country[$key]);
                    }
            }
        }


        $client->save();

        //return response()->json(['message' => $request->name]);
        return response()->json([
            'request' => $request->all(),
            'after_update_client' => $client,
            ]);
        } catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            '$request->jobid'   =>  $request->id,
            'file' => $e->getFile(),
            'line' => $e->getLine(),], 500);
        }
    
    }
    public function delete(Client $client)
    {
        return view('client.delete', ['client' => $client]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,Client $client)
    {
        $client = Client::findOrFail($request->clientid);
        $client->delete();

        return response()->json([
            'message' => 'Status deleted successfully.'
        ]);
    }
    public function createBackup()
    {
        $clients = Client::all();        
        $columns = Schema::getColumnListing('clients'); 

        $csvData = implode(',', $columns) . "\n";
        foreach ($clients as $client) {
            $rowData = [];
            foreach ($columns as $column) {
                $rowData[] = $client->{$column};
            }
            $csvData .= implode(',', $rowData) . "\n";
        }
        $timestamp = date('Y-m-d_H-i-s');
        $file_path = resource_path('files/backups/Client/client.backup_'.$timestamp.'.csv');

        file_put_contents($file_path, $csvData);
        return redirect()->back()->with('succeses', 'Client backup created successfully.');
    }
    public function getClientInfo($clientId)
    {
        try{
            // Fetch the client's information based on the $clientId
            $client = Client::find($clientId);

            if ($client) {
            return response()->json([
                'id'                    => $client->id,
                'name'                  => $client->name,
                'nickName'              => is_null($client->shortenedNameWithoutterPostalCode())?'':$client->shortenedNameWithoutterPostalCode(),
                'email'                 => $client->email,
                'country'               => $client->country,
                'city'                  => $client->city,
                'addresses'             => $client->getAllAddresses()->map(function ($address) {
                                            return [
                                                'id' => $address->id,
                                                'name' => $address->name,
                                                'type' => $address->type,
                                                'address_line_1' => $address->address_line_1,
                                                'address_line_2' => $address->address_line_2,
                                                'postal_code' => $address->postalCode->postal_code,
                                                'city' => $address->city,
                                                'country' => $address->country,
                                            ];
                                        }),
                'postal_code'                  => $client->postal_code,
                'address_line'                  => $client->address_line,
                'pickup_country'                  => $client->pickup_country,
                'pickup_city'                  => $client->pickup_city,
                'pickup_postal_code'                  => $client->pickup_postal_code,
                'pickup_adress_line'                  => $client->pickup_adress_line,
                'packageTypes'          => $client->packageTypes->map(function ($packageType) {
                                            return [
                                                'id' => $packageType->id,
                                                'name' => $packageType->name,
                                                'price' => $packageType->price,
                                                'baseQuantityThreshold' => $packageType->baseQuantityThreshold, 
                                                'maxQuantityThreshold' => $packageType->maxQuantityThreshold,  
                                            ];
                                        }),
                'phone'                 =>  $client->phone,
                ]);
            }

            return response()->json(['error' => 'Client not found'], 404);
        } catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            '$request->jobid'   =>  $request->id,
            'file' => $e->getFile(),
            'line' => $e->getLine(),], 500);
        }
    }
    public function searchClients(Request $request)
    {
        $query = $request->input('query');

        
        $clients = Client::where('name', 'like', '%' . $query . '%')
            ->select('id', 'name') 
            ->get();

        return response()->json($clients);
    }
    public function fetchClientsPaginate(Request $request)
{
    try {
        $id = $request->get('id', '');
        $name = $request->get('name', '');
        $address = $request->get('address', '');
        $sortField = $request->get('sortField', 'id');
        $sortOrder = $request->get('sortOrder', 'asc');

        $clients = Client::when($id, function ($queryBuilder) use ($id) {
                $queryBuilder->where('id', 'like', '%' . $id . '%');
            })
            ->when($name, function ($queryBuilder) use ($name) {
                $queryBuilder->where(function ($query) use ($name) {
                    $query->where('name', 'like', '%' . $name . '%')
                          ->orWhere('shortenedName', 'like', '%' . $name . '%');
                });
            })
            ->when($address, function ($queryBuilder) use ($address) {
                $queryBuilder->where(function ($query) use ($address) {
                    $query->where('country', 'like', '%' . $address . '%')
                          ->orWhere('city', 'like', '%' . $address . '%')
                          ->orWhere('postal_code', 'like', '%' . $address . '%')
                          ->orWhere('address_line', 'like', '%' . $address . '%')
                          ->orWhere('pickup_country', 'like', '%' . $address . '%')
                          ->orWhere('pickup_city', 'like', '%' . $address . '%')
                          ->orWhere('pickup_postal_code', 'like', '%' . $address . '%')
                          ->orWhere('pickup_adress_line', 'like', '%' . $address . '%');
                });
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate(10);

        $clients->appends([
            'id' => $id,
            'name' => $name,
            'address' => $address,
            'sortField' => $sortField,
            'sortOrder' => $sortOrder
        ]);

        return response()->json([
            'clients'   =>  $clients,
            'links'     =>  (string) $clients->links(),
        ]);
    } catch (QueryException $e) {
        return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),], 500);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),], 500);
    }
}

}
