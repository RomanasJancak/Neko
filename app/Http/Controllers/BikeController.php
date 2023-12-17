<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Bike;
use App\Http\Requests\StoreBikeRequest;
use App\Http\Requests\UpdateBikeRequest;

class BikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bikes = Bike::latest()->paginate(10);

        return view('bike.index', compact('bikes'));
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
    public function store(StoreBikeRequest $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $bike = new Bike();
        $bike->name = $request->name;
        $bike->save();

        return response()->json([
            'message' => 'Bike created successfully.',
            'bike' => $bike
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Bike $bike)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bike $bike)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBikeRequest $request, Bike $bike)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        //return response()->json(['message' => $request->bikeid]);
        $bike = Bike::findOrFail($request->bikeid);
        $bike->name = $request->name;
        $bike->save();

        return response()->json([
            'message' => 'Bike updated successfully.',
            'bike' => $bike
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,Bike $bike)
    {
        $bike = Bike::findOrFail($request->bikeid);
        $bike->delete();

        return response()->json([
            'message' => 'Bike deleted successfully.'
        ]);
    }
}
