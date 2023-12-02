<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Workload;
use App\Models\Day;
use App\Models\Bike;
use App\Http\Requests\StoreWorkloadRequest;
use App\Http\Requests\UpdateWorkloadRequest;

use Carbon\Carbon;

class WorkloadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workloads = Workload::latest()->paginate(10);
        return view('workload.index', compact('workloads'));
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
    public function store(StoreWorkloadRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Workload $workload)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workload $workload)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkloadRequest $request, Workload $workload)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workload $workload)
    {
        //
    }
    public function calendar(Request $request){
        $currentYear = $request->year ?? $currentYear ?? Carbon::now()->year;
        $currentMonth = $request->month ?? $currentMonth ?? Carbon::now()->month;
        // Fetch workloads for the specified user for the current month
        $workloads = Workload::select('workloads.*')
        ->join('days', 'workloads.day_id', '=', 'days.id')
        ->whereYear('days.date', $currentYear)
        ->whereMonth('days.date', $currentMonth)
        ->distinct()
        ->get();
        // Organize workload data for the calendar view
        $workloadData = [];
        foreach ($workloads as $workload) {
            $day = Carbon::parse($workload->day->date)->format('j');
            $workloadData[$day][] = $workload;
        }
        $daysInMonth = Carbon::create($currentYear, $currentMonth)->daysInMonth;
        $bikes = Bike::all();
        return view('workload.calendar', compact('workloadData', 'daysInMonth', 'currentYear', 'currentMonth','bikes'));
    }
    public function storeJavascript(StoreWorkloadRequest $request)
    {
        $workload   =   new Workload();
        if($request->capacity){
            $workload->capacity =   $request->capacity;
        }else{
            $workload->capacity =   "100%";
        }
        $workload->user_id  =   $request->user;
        $workload->bike_id  =   $request->bike;
        $dateTimeString = $request->year."-".$request->month."-".$request->day;
        $day = Day::where('date', $dateTimeString)->first();
        if ($day) {
            // If the day is found, assign its ID to the workload
            $workload->day_id = $day->id;
            $workload->save();
    
            return response()->json(['message' => $dateTimeString]);
        }else{
            $day = new Day;
            $day->date  = $dateTimeString;
            $day->name  = $dateTimeString;
            $day->save();
            $workload->day_id = $day->id;
            $workload->save();
            return response()->json(['message' => 'New day created: '.$dateTimeString." id: ".$day->id]);
        }
        return response()->json(['message' => $day]);
    }
    public function updateJavascript(UpdateWorkloadRequest $request)
    {   
        $workload           =   Workload::find($request->workloadid);
        if($request->capacity){
            $workload->capacity =   $request->capacity;
        }else{
            $workload->capacity =   "100%";
        }
        $workload->user_id  =   $request->user;
        $workload->bike_id  =   $request->bike;
        $workload->save();
        return response()->json(['message' => 'updated']);
    }
    public function deleteJavascript(UpdateWorkloadRequest $request)
    {
        $workload           =   Workload::find($request->workloadid);
        $workload->delete();
        return response()->json(['message' => 'deleted']);
    }
}
