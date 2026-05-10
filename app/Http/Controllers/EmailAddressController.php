<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmailAddressRequest;
use App\Http\Requests\UpdateEmailAddressRequest;
use App\Models\EmailAddress;
use Illuminate\Http\Request;

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
    public function destroy(Request $request, EmailAddress $emailAddress)
    {
        try {
            $clientId = (int) $request->input('client_id');

            if (
                $emailAddress->emailable_type !== 'App\\Models\\Client' ||
                ($clientId > 0 && (int) $emailAddress->emailable_id !== $clientId)
            ) {
                return response()->json([
                    'error' => 'Email does not belong to the selected client.',
                ], 403);
            }

            $emailAddress->delete();

            return response()->json([
                'message' => 'Email deleted successfully.',
                'email_id' => $emailAddress->id,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
