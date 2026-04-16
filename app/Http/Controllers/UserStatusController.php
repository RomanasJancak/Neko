<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserStatusRequest;
use App\Http\Requests\UpdateUserStatusRequest;
use App\Models\UserStatus;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $userStatuses = UserStatus::with('status')->get();
      return view('user-statuses.index', compact('userStatuses'));
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
    public function store(StoreUserStatusRequest $request)
    {
      $request->validate(['name' => 'required|string|max:255']);

        DB::transaction(function () use ($request) {
            // 1. Create the global status record
            $status = Status::create([
                'name' => $request->name,
            ]);

            // 2. Map it to the UserStatus table
            UserStatus::create(['status_id' => $status->id]);
        });

        return back()->with('success', 'User status created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserStatus $userStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserStatus $userStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserStatus $userStatus)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $status = $userStatus->status;
        $status->name = $request->name;
        $status->save();
        return redirect()->route('user-statuses.index')->with('success', 'Status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserStatus $userStatus)
    {
        DB::transaction(function () use ($userStatus) {
            // Delete the master status (Cascade will handle user_status if set, 
            // but we'll do it explicitly for safety)
            Status::find($userStatus->status_id)->delete();
            $userStatus->delete();
        });

        return back()->with('success', 'Status removed.');
    }
}
