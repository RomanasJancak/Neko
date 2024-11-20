<?php

namespace App\Http\Controllers;

use App\Models\ApprovedPostalCodeArea;
use App\Http\Requests\StoreApprovedPostalCodeAreaRequest;
use App\Http\Requests\UpdateApprovedPostalCodeAreaRequest;

class ApprovedPostalCodeAreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('approvedPostalCodeAreaController.index', compact('jobs','couriers','statuses'));
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApprovedPostalCodeArea $approvedPostalCodeArea)
    {
        //
    }
}
