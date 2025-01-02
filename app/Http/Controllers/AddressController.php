<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;

class AddressController extends Controller
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
    public function store(StoreAddressRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Address $address)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Address $address)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAddressRequest $request, Address $address)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address)
    {
        try{
            $address->delete();
            return response()->json([
                'message' => 'Address deleted successfully',
                //'address_id' => $address,
            ], 201);
        } catch (\Exception $e){
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
    public function getAddressInfo($addressId){
        try{
            $address = Address::find($addressId);
            return response()->json([
                    'id' => $address->id,
                    'name' => $address->name,
                    'type' => $address->type,
                    'address_line_1' => $address->address_line_1,
                    'address_line_2' => $address->address_line_2,
                    'postal_code' => $address->postalCode->postal_code,
                    'city' => $address->city,
                    'country' => $address->country,
            ], 201);
        } catch (\Exception $e){
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
