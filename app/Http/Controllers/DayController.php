<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\User;
use App\Models\Job;
use App\Http\Requests\StoreDayRequest;
use App\Http\Requests\UpdateDayRequest;

class DayController extends Controller
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
    public function store(StoreDayRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Day $day)
    {
        //$users  =   User::all();
        $usersWithCourierRole = User::whereHas('roles', function ($query) {
            $query->where('name', 'courier');
        })->get();
        $jobs   =   $day->jobs();
        $filteredJobs = $jobs->filter(function ($job) {
            return $job->status_id === 1;
        });
        return view('day.show', ['day' => $day,'users' => $usersWithCourierRole,'jobs' => $filteredJobs]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Day $day)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDayRequest $request, Day $day)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Day $day)
    {
        //
    }
}
