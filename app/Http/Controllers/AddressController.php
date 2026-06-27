<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\PostalCode;
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
        try{
            $address = new Address();
            $model ="";
            $model_id ="";
            if($request->has('client_id')){
              $model = "App\Models\Client";
              $model_id = $request->input('client_id');
            }
            $postal_code = new PostalCode(['postal_code' => $request->input('postal_code')]);
            $postal_code->save();
            $address = Address::create([
                'name' => $request->input('name'),
                'address_line_1' => $request->input('address_line_1'),
                'address_line_2' => $request->input('address_line_2'),
                'city' => 'London',
                'country' => 'United Kingdom',
                'postal_code_id' => $postal_code->id,
                'model' => $model,
                'model_id' => $model_id,
            ]);
            $address->save();
            return response()->json([
                'success' => true,
                'message' => 'Address created successfully',
                'address' => $address,
            ], 201);
        } catch (\Exception $e){
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
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
    public function update(UpdateAddressRequest $request)
    {
        try{
            $address = Address::find($request->input('id'));
            $address->name = $request->input('name');
            $address->address_line_1 = $request->input('address_line_1');
            $address->address_line_2 = $request->input('address_line_2');
            $postalCode = $request->input('postal_code');
            if($address->postalCode->postal_code != $postalCode){
                $address->postalCode->delete();
                $address->addNewPostalCode($postalCode);
            }
            $address->city = $request->input('city');
            $address->country = $request->input('country');
            $address->save();
            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully',
                'address' => $address,
            ], 201);
        } catch (\Exception $e){
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
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
                    'success' => true,
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
