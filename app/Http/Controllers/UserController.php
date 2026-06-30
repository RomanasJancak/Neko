<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Role;
use App\Models\Client;
use App\Models\Workload;
use App\Models\Bike;
use App\Models\Day;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

use Carbon\Carbon;



class UserController extends Controller
{




    //
    // PRITAIKYTI USERIUI NUKOPIJUOTA IS BUDGET Controllerio
    //


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() 
    {
        if(!auth()->user()->can('user-view')){
            abort(403, 'You do not have permission to view users.');
        }
        $users = auth()->user()->getVisibleUsers()->paginate(10);

        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        foreach(auth()->user()->roles as $role){
            if(($role->id === 1)||($role->id === 2)){
                return view('user.create');
                
            }
        }
        $users = User::latest()->paginate(10);
        return view('user.index', compact('users'));
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBudgetRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'phone' => ['nullable', 'string', 'regex:/^\+?[1-9]\d{1,14}$/'],
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'password' => bcrypt($validatedData['password']),
            
        ]);
        $user->assignRole($request->role);
        $user->activity()->create([
            'is_active' => true,
            'last_activity_at' => now(),
        ]);
        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //*
            return view('user.show', ['user' => $user]);
        //*/
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    { 
            $clients = Client::orderBy('name')->get(['id', 'name']);
            if(!(auth()->user()->id === $user->id)){     
              if(!auth()->user()->can('user-edit')){
                  abort(403, 'You do not have permission to edit users.');
              }
            }
            return view('user.edit', ['user' => $user, 'clients' => $clients]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */

    public function update(UpdateUserRequest $request, User $user)
    {
        if (auth()->id() !== $user->id && !auth()->user()->can('user-edit')) {
            abort(403, 'You do not have permission to edit this user.');
        }

            $activityChanged = $user->isActive() !== $request->input('is_active');
            $user->name = $request->user_name;
            $user->phone = $request->get('phone', '');
            $user->activity()->update([
                'is_active' => true,
                'last_activity_at' => now(),
            ]);
            $user->email = $request->user_email;
            $user->client_id = $request->filled('client_id') ? (int) $request->client_id : null;

        if (auth()->user()->can('user-edit') && $request->filled('role')) {
            $user->syncRoles(Role::find($request->role));
        }
        //dd(Role::find($request->role));
        $user->save();
        return redirect()->route('user.show',['user' => $user])->with('success_message', 'Sėkmingai pakeistas.');
    }
    public function updateRole(UpdateUserRequest $request,User $user){
        //dd($request->input('role_id'));
        //$user->roles()->detach();
        //$newRoleId = $request->input('role_id');
        //$newRole = Role::find($newRoleId);
        //dd($newRole);
        //$user->roles()->attach($newRole);
        $user->syncRoles(Role::find($request->role));
        $user->save();
        return redirect()->back()->with('success', 'Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('success_message', 'Vartotojas : '.$user->name.' Sekmingai ištrintas.');
    }
    public function delete(User $user ){
        return view('user.delete', ['user' => $user]);
    }
    public function workload(Request $request,User $user)
    {
        $currentYear = $request->year ?? $currentYear ?? Carbon::now()->year;
        $currentMonth = $request->month ?? $currentMonth ?? Carbon::now()->month;
        // Fetch workloads for the specified user for the current month
        $workloads = Workload::where('user_id', $user->id)
        ->join('days', 'workloads.day_id', '=', 'days.id')
        ->whereYear('days.date', $currentYear)
        ->whereMonth('days.date', $currentMonth)
        ->get();
        // Organize workload data for the calendar view
        $workloadData = [];
        foreach ($workloads as $workload) {
            $day = Carbon::parse($workload->day->date)->format('j');
            $workloadData[$day][] = $workload;
        }
        //$currentMonth = Carbon::now()->month;
        //$currentYear = Carbon::now()->year;
        $daysInMonth = Carbon::create($currentYear, $currentMonth)->daysInMonth;
        $bikes = Bike::all();

        return view('user.workload', compact('workloadData', 'daysInMonth', 'currentYear', 'currentMonth','user','bikes'));
    }
    public function getCouriersWithWorkloadOnDay($date)
    {
        // Fetch the client's information based on the $clientId
        // $job = Job::find($jobId);
        $day = Day::whereRaw('DATE(date) = ?', [$date])->first();
        $courriers = User::getCouriersWithWorkload($day);
        return response()->json([
            'couriers' => $courriers,
        ]);
        return response()->json(['error' => 'Couriers not found for  : '.$date], 404);
    }
}
