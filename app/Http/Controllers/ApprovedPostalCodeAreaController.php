<?php

namespace App\Http\Controllers;

use App\Models\ApprovedPostalCodeArea;
use App\Http\Requests\StoreApprovedPostalCodeAreaRequest;
use App\Http\Requests\UpdateApprovedPostalCodeAreaRequest;

use Illuminate\Http\Request;

class ApprovedPostalCodeAreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $approvedPostalCodeAreas = ApprovedPostalCodeArea::paginate(10);
        return view('approvedpostalcodearea.index', compact('approvedPostalCodeAreas'));
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
    public function store(StoreApprovedPostalCodeAreaRequest $request)
    {
        try {
            $validated = $request->validated();
            ApprovedPostalCodeArea::create($validated);
            return response()->json(['message' => 'Created successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error creating data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ApprovedPostalCodeArea $approvedPostalCodeArea)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApprovedPostalCodeArea $approvedPostalCodeArea)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateApprovedPostalCodeAreaRequest $request, ApprovedPostalCodeArea $approvedPostalCodeArea)
    {
        try {
            $approvedPostalCodeArea = ApprovedPostalCodeArea::findOrFail($request->id);
            $validated = $request->validated();
            $approvedPostalCodeArea->update($validated);
            //$approvedPostalCodeArea->save();
            return response()->json([
                'message' => 'Updated successfully',
                'validated' => $validated,
                'request' => $request->all(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error updating data: ' . $e->getMessage(),
                'request' => $request->all(),
                //'validated' => $validated,
                'approvedPostalCodeArea' => $approvedPostalCodeArea,
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $approvedPostalCodeArea = ApprovedPostalCodeArea::findOrFail($request->id);
            $approvedPostalCodeArea->delete();
            return response()->json(['message' => 'Deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting data: ' . $e->getMessage()], 500);
        }
    }

    public function getById($id)// it is what it is 
    {
        try {
            $approvedPostalCodeArea = ApprovedPostalCodeArea::findOrFail($id);
            return response()->json($approvedPostalCodeArea);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching data: ' . $e->getMessage()], 500);
        }
    }
}
