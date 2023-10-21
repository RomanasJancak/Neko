<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;

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
        //
        return view('client.show', ['client' => $client]);
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
    public function update(UpdateClientRequest $request, Client $client)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'vat' => 'required|string|max:255',
            'regNumber' => 'nullable|string|max:255',
            'address' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $client->update($validatedData);

        return redirect()->route('client.show', $client)->with('success', 'Client updated successfully');
    
    }
    public function delete(Client $client)
    {
        return view('client.delete', ['client' => $client]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('client.index')->with('success', 'Client deleted successfully');
    }
}
