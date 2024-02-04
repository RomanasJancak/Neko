<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;

use App\Models\Client;
use App\Models\User;
use App\Models\Role;
use App\Models\Status;
use App\Models\PostalCode;
use App\Models\PackageType;

use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

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
            $statuses = Status::all();
            $managers = User::all();
            $postalCodes    =   PostalCode::all();

    
            return view('job.create', compact('clients'
            , 'couriers', 
             'statuses',
             'managers',
             'postalCodes'
        ));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobRequest $request)
    {
        $request->validated();
        try{
            $job = new Job();
            $job->eilesNumeris      =   0;
            $job->courrier_id       =   $request->courrier_id;
            $job->status_id         =   $request->status_id; 
            $job->clientToBill_id   =   $request->billingClientId;
            $job->pickupClientName  =   $request->pickupclientname;
            




            return response()->json(['success'  => true,
            'message'   => 'Validation succes.',
            'inputs'    => $request->input(),
            'validated inputs'    =>  $request->validated(),                       
            ], 200);
        } catch (QueryException $e){
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
        

        //Job::create($request->all());
        //return redirect()->route('job.index')->with('success', 'Job created successfully');
        //return redirect()->back()->with('succses', 'Addon Rule created successfully.');
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
        $job->sender_id =   $request->sender_id;
        $job->pickup_time_begin =   $request->pickup_time_begin;
        $job->pickup_time_end =   $request->pickup_time_end;
        $job->dropoff_time_begin =   $request->dropoff_time_begin;
        
        $job->dropoff_time_end =   $request->dropoff_time_end;
        $job->receiver_id =   $request->receiver_id;
        $job->courrier_id   =   $request->courrier_id;
        $job->status_id =   $request->status_id;
        $job->collection_details    =   $request->collection_details;
        $job->pickup_address    =   $request->pickup_address;
        $job->delivery_address  =   $request->delivery_address;
        $job->senderContacts    =   $request->senderContacts;
        $job->manager_id    =   $request->manager_id;
        $job->receiverContacts  =   $request->receiverContacts;
        $job->notes =   $request->notes;
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
    public function updateJobAjax(UpdateJobRequest $request)
    {
        $jobId = $request->input('jobId');
        $targetListId = $request->input('targetListId');
        $eilesNumeris = $request->input('eilesNumeris');
        // Find the job by its ID
        $job = Job::find($jobId);

        if (!$job) {
            return response()->json(['error' => 'Job not found'], 404);
        }

        // Update job data based on the target list
        if ($targetListId === 'job-list') {
            $job->status_id = 1; //;
            $job->courrier_id = null;
            $job->eilesNumeris = $eilesNumeris;
        } else {
            // Extract the user ID from the targetListId
            $userId = substr($targetListId, strrpos($targetListId, '-') + 1);
            $job->status_id = 2;//'assigned';
            $job->courrier_id = $userId;
            $job->eilesNumeris = $eilesNumeris;
        }

        $job->save();

        return response()->json(['message' => 'Job updated successfully']);
    }
    public function assign(){
        {

            if(auth()->user()->hasRole('courier')){
                $jobs = Job::where('courrier_id',auth()->user()->id)->latest()->paginate(10);
            }else{
                $jobs = Job::latest()->paginate(10);
            }
            return view('job.assign', compact('jobs'));
        }
    }
}
