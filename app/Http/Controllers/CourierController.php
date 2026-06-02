<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Day;

class CourierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function today()
    {
        $user  = auth()->user();
        $today = Carbon::today()->toDateString();

        $jobs = Job::with([
                'clientToBill',
                'status',
                'tasks' => fn ($q) => $q->orderBy('order_number')
                    ->with(['pickup', 'package', 'return', 'customTask', 'status']),
            ])
            ->where('courrier_id', $user->id)
            ->whereDate('date', $today)
            ->orderBy('pickup_time_begin')
            ->get();

        return view('courier.today', compact('jobs', 'today'));
    }

    public function dashboard(?string $date = null)
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('courier')) {
            abort(403);
        }

        $date = $date ? Carbon::parse($date)->toDateString() : Carbon::today()->toDateString();

        $day = Day::firstOrCreate(
            ['date' => $date],
            ['name' => $date]
        );

        $users = collect([$user]);
        $jobsUnassigned = collect();
        $courierOnly = true;

        return view('day.dashboard', compact('date', 'jobsUnassigned', 'users', 'day', 'courierOnly'));
    }
    public function getJobsForCourier($courier,$date){
        $date = Carbon::parse($date)->toDateString();
        $jobs = Job::with([
                'clientToBill',
                'status',
                'tasks' => fn ($q) => $q->orderBy('order_number')
                    ->with(['pickup', 'package', 'return', 'customTask', 'status']),
            ])
            ->where('courrier_id', $courier)
            ->whereDate('date', $date)
            ->orderBy('pickup_time_begin')
            ->get();

        return response()->json([
            'success' => true,
            'date'    => $date,
            'jobs'    => $jobs,
        ]);
    }
    public function getCouriersForDate(Request $request, $date){
        $date = Carbon::parse($date)->toDateString();
        $couriers = User::getCouriersForDate($date);
        //$day = Day::getDayByDate($date);
        //$allCouriers = $day ? $day->allCouriersForThisDay() : collect();
        return response()->json([
            'success' => true,
            'date' => $date,
            'couriers' => $couriers,
        ]);
    }
}
