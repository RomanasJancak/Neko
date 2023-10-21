<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Models\Client;
use App\Models\User;
use App\Models\Role;
use App\Models\Status;
class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if(auth()->user()->hasRole('courier')){
            $jobs = Job::where('courrier_id',auth()->user()->id)->latest()->paginate(10);
        }else{
            $jobs = Job::latest()->paginate(10);
        }

        return view('job.index', compact('jobs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        {
            $clients = Client::all();
            $couriers = User::all();
            // $statuses = Status::all();
            $managers = User::all();
            // $groups = Group::all();
    
            return view('job.create', compact('clients'
            , 'couriers', 
            // 'statuses',
             'managers'
            // , 'groups'
        ));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobRequest $request)
    {
        $request->validate([
            // Add validation rules for the job fields
            // 'client_id' => 'required',
             'courrier_id' => 'required',
            // 'creation_time' => 'required',
            // 'completion_time' => 'required',
            // 'status_id' => 'required',
            // 'collection_details' => 'required',
            // 'pickup_address' => 'required',
            // 'delivery_address' => 'required',
            // 'senderContacts' => 'required',
            // 'manager_id' => 'required',
            // 'receiverContacts' => 'required',
            // 'group_id' => 'required',
            // 'notes' => 'required',
            // 'invoice_id' => 'required',
        ]);
        $job    =   new Job();
        $job->client_id =   $request->client_id;
        $job->courrier_id   =   $request->courrier_id;
        $job->creation_time =   $request->creation_time;
        $job->completion_time   =   $request->completion_time;
        $job->status_id =   $request->status_id;
        $job->collection_details    =   $request->collection_details;
        $job->pickup_address    =   $request->pickup_address;
        $job->delivery_address  =   $request->delivery_address;
        $job->senderContacts    =   $request->senderContacts;
        $job->manager_id    =   $request->manager_id;
        $job->receiverContacts  =   $request->receiverContacts;
        $job->group_id  =   0;
        $job->notes =   $request->notes;
        $job->invoice_id    =   $request->invoice_id;
        //dd($job);
        $job->save();
        
        //Job::create($request->all());
        return redirect()->route('job.index')
            ->with('success', 'Job created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Job $job)
    {
        return view('job.show', ['job' => $job]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Job $job)
    {
        $clients = Client::all();
        $statuses   = Status::all();
        $courierRole = Role::where('name', 'courier')->first();
        $couriers = User::role($courierRole)->get();
        $managerRole = Role::where('name','manager')->first();
        $managers   =   User::role($managerRole)->get();

        return view('job.edit', compact('job', 'clients', 'couriers', 'managers','statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobRequest $request, Job $job)
    {
        $job->client_id =   $request->client_id;
        $job->courrier_id   =   $request->courrier_id;
        $job->creation_time =   $request->creation_time;
        $job->completion_time   =   $request->completion_time;
        $job->status_id =   $request->status_id;
        $job->collection_details    =   $request->collection_details;
        $job->pickup_address    =   $request->pickup_address;
        $job->delivery_address  =   $request->delivery_address;
        $job->senderContacts    =   $request->senderContacts;
        $job->manager_id    =   $request->manager_id;
        $job->receiverContacts  =   $request->receiverContacts;
        $job->group_id  =   0;
        $job->notes =   $request->notes;
        $job->invoice_id    =   $request->invoice_id;
        //dd($job);
        $job->save();
        return redirect()->route('job.show',['job' => $job])->with('success_message', 'Updated sucsesfully');
    }
    public function updateStatus(UpdateJobRequest $request, Job $job){
        $job->status_id =   $request->status_id;
        $job->save();
        return redirect()->route('job.show',['job' => $job])->with('success_message', 'Updated sucsesfully');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job)
    {
        //
    }
}
