<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmailAddressRequest;
use App\Http\Requests\UpdateEmailAddressRequest;
use App\Models\EmailAddress;

class EmailAddressController extends Controller
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
    public function store(StoreEmailAddressRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailAddress $emailAddress)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailAddress $emailAddress)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmailAddressRequest $request, EmailAddress $emailAddress)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailAddress $emailAddress)
    {
        //
    }
}
