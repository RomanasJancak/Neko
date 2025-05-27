<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Day;
use App\Models\User;
use App\Models\Job;
use App\Http\Requests\StoreDayRequest;
use App\Http\Requests\UpdateDayRequest;

use Carbon\Carbon;

class DayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $days = Day::paginate(35); // Paginate the days (change 35 to the desired number)
    
        return view('day.index', compact('days'));
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
    public function showByDate($date){
        
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
        $usersWithCourierRoleAndWorkload = $usersWithCourierRole->filter(function ($user) use ($day) {
            return $user->workload($day) !== null;
        });
        $jobs   =   $day->jobs();
        //$jobs = Job::with('status')->get();
        //dd($jobs[0]->status);
        $filteredJobs = $jobs->filter(function ($job) {
            return $job->status->name === 'unfinishedss';
        });
        //dd($usersWithCourierRoleAndWorkload);
        return view('day.show', ['day' => $day,'users' => $usersWithCourierRoleAndWorkload,'jobs' => $filteredJobs]);
    }
    public function showdashboard($date){
        $day    =   Day::where('date', $date)->first();
        if(!$day){
            $day = Day::create([
                'name' => $date,
                'date' => $date,
            ]);
        }
        $usersWithCourierRole = User::whereHas('roles', function ($query) {
            $query->where('name', 'courier');
        })->get();
        $users = $usersWithCourierRole->filter(function ($user) use ($day) {
            return $user->workload($day) !== null;
        });
        $carbobn = new Carbon();
        //=====
        $jobsUnassigned = Job::whereHas('status', function ($query) {
                            $query->where('name', 'unassigned');
            })->whereHas('tasks', function ($query) use ($date) {
                $query->where(function ($taskQuery) use ($date) {
            $taskQuery
                ->whereHas('pickup', function ($pickupQuery) use ($date) {
                    $pickupQuery->whereBetween('pickup_time_begin', [Carbon::parse($date)->startOfDay(), Carbon::parse($date)->endOfDay()]);
                })
                ->orWhereHas('package', function ($packageQuery) use ($date) {
                    $packageQuery->whereBetween('packagedropofftimebegin', [Carbon::parse($date)->startOfDay(), Carbon::parse($date)->endOfDay()]);
                })
                ->orWhereHas('return', function ($returnQuery) use ($date) {
                    $returnQuery->whereBetween('time_begin', [Carbon::parse($date)->startOfDay(), Carbon::parse($date)->endOfDay()]);
                });
        });
    })
    ->with(['tasks.pickup', 'tasks.package', 'tasks.return']) // eager load if needed
    ->get();
        //dd($jobs);
        //======
        $jobsUnassigned_2 = Job::whereHas('status', function($query) {
            $query->where('name', 'unassigned');
        })
        ->whereBetween('pickup_time_begin', [Carbon::parse($date)->startOfDay(), Carbon::parse($date)->endOfDay()])
                ->get();
        return view('day.dashboard', compact('date','jobsUnassigned','users','day'));
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
        /**
     * Return free bikes for use for the specific day for javascript.
     */
    public function getFreeBikes(Request $request)
    {
        $day = $request->input('day');
        $month = $request->input('month');
        $year = $request->input('year');
        $option =   $request->input('option');
            
        $dayModel = Day::where('date', Carbon::createFromDate($year, $month, $day)->format('Y-m-d'))->first();
            
        if (!$dayModel) {
            $dayModel = new Day;
            $dateTimeString = $year."-".$month."-".$day;
            $dayModel->date  = $dateTimeString;
            $dayModel->name  = $dateTimeString;
            $dayModel->save();
        }
        switch($option){
            case 'free':
                $freeBikes = $dayModel->freeBikes();
                break;
        }
        
        return response()->json($freeBikes);
    }
    public function getFreeCouriers(Request $request)
    {
        $day = $request->input('day');
        $month = $request->input('month');
        $year = $request->input('year');
        $option =   $request->input('option');
            
        $dayModel = Day::where('date', Carbon::createFromDate($year, $month, $day)->format('Y-m-d'))->first();
            
        if (!$dayModel) {
            $dayModel = new Day;
            $dateTimeString = $year."-".$month."-".$day;
            $dayModel->date  = $dateTimeString;
            $dayModel->name  = $dateTimeString;
            $dayModel->save();
        }
        switch($option){
            case 'free':
                $freeCouriers = $dayModel->freeCouriers();
                break;
        }
        $freeCouriers = $dayModel->freeCouriers();
        return response()->json($freeCouriers);
        }
}
