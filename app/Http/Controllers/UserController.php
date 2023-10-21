<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;



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

        $users = User::latest()->paginate(10);

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
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);
        $user->assignRole($request->role);

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
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {

            return view('user.edit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBudgetRequest  $request
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\Http\Response
     */
    public function addBudget(UpdateUserRequest $request,User $user, Budget $budget)
    {
        $user->budgets()->attach($budget);
    }
    public function update(UpdateUserRequest $request, User $user)
    {
        //
        //dd();
        $user->name = $request->user_name;
        $user->email = $request->user_email;
        $user->syncRoles(Role::find($request->role));
        $user->save();
        return redirect()->route('user.show',['user' => $user])->with('success_message', 'Sėkmingai pakeistas.');
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
}
