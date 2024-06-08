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
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients',
            'vat' => 'required|string|max:255',
            'regNumber' => 'nullable|string|max:255',
            'address' => 'required|string',
            'note' => 'nullable|string',
        ]);

        Client::create($validatedData);

        return redirect()->route('client.index')->with('success', 'Client created successfully');
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
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'shortenedName'     =>  'max:255',


            
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'address_line' => 'required|string|max:255',

            'pickup_country' => 'max:255',
            'pickup_city' => 'max:255',
            'pickup_postal_code' => 'max:255',
            'pickup_adress_line' => 'max:255',
            

            'phone' => ['nullable', 'string', 'regex:/^\+?[1-9]\d{1,14}$/'],
        ]);

        $client->update($validatedData);
        $client->shortenedName = $request->shortenedName;

        $client->save();

        //return response()->json(['message' => $request->name]);
        return response()->json([
            'after_update_client' => $client,
            '$request->phone'   =>$request->phone,
            ]);
        } catch (\Exception $e) {
        // Handle other types of exceptions as needed
            return response()->json(['errors' => $e->errors()], 422);
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
    }
    public function searchClients(Request $request)
    {
        $query = $request->input('query');

        // Perform a search query to retrieve client data based on the user's input
        $clients = Client::where('name', 'like', '%' . $query . '%')
            ->select('id', 'name') // Add the fields you want to include
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
