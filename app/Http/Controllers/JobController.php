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
use App\Models\Package;
use App\Models\AddOn;
use App\Models\AddOnRule;

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
            $job->courrier_id       =   $request->input('courrier_id') == 0 ? null : $request->input('courrier_id');
            $job->status_id         =   $request->input('status_id'); 
            $job->clientToBill_id   =   $request->input('billingClientId');
            $job->pickupClientName  =   $request->input('pickupclientname');
            $job->pickup_time_begin =   $request->input('common_date').' '.$request->input('pickup_time_begin');
            $job->pickup_time_end =   $request->input('common_date').' '.$request->input('pickup_time_end');
            $job->pickupclientaddressline   =   $request->input('pickupclientaddressline');
            $job->pickupclientcity          =   $request->input('pickupclientcity');
            $job->pickupclientcountry       =   $request->input('pickupclientcountry');
            $job->pickupclientpostalcode    =   $request->input('pickupclientpostalcode');
            $job->manager_id                =   $request->input('manager_id');
            $job->save();
            if(isset($request->jobcheckboxaddon)){
                foreach($request->input('jobcheckboxaddon') as $key => $addOnRuleId){
                    $addOn = new Addon();
                    $addOn->model_type = 'app/models/Job';
                    $addOn->model_id = $job->id;
                    $addOnRule = AddOnRule::find($addOnRuleId);
                    $addOn->begin_date = $addOnRule->begin_date;
                    $addOn->end_date = $addOnRule->end_date;
                    $addOn->name = $addOnRule->name;
                    $addOn->display_name = $addOnRule->display_name;
                    $addOn->price = $addOnRule->price;
                    $addOn->save();
                }
            }
            foreach( $request->input('packageType') as $key => $packageType){
                $package    = new Package();
                $package->job_id    =   $job->id;
                $package->packageType_id    =   $packageType; 
                $package->orderNumber    =   $key; 
                $package->weight    =   $key; 
                $package->dimensions    =   $key; 
                $package->quantity    =   $request->input('packagedropoffquantity')[$key]; 
                $package->dropoff_adress_line    =   $request->input('packagedropooffaddressline')[$key]; 
                $package->dropoff_postal_code    =   $request->input('packagedropoffpostalcode')[$key]; 
                $package->dropoff_city    =   $request->input('packagedropoffcity')[$key]; 
                $package->dropoff_country    =   $request->input('packagedropoffcountry')[$key]; 
                $package->dropoff_name    =   $request->input('packagedropoffname')[$key];
                $package->save();
                if(isset($request->packagecheckboxaddon[$key])){
                    foreach($request->input('packagecheckboxaddon')[$key] as $keyB => $addOnRuleId){
                        $addOn = new Addon();
                        $addOn->model_type = 'app/models/Package';
                        $addOn->model_id = $package->id;
                        $addOnRule = AddOnRule::find($addOnRuleId);
                        $addOn->begin_date = $addOnRule->begin_date;
                        $addOn->end_date = $addOnRule->end_date;
                        $addOn->name = $addOnRule->name;
                        $addOn->display_name = $addOnRule->display_name;
                        $addOn->price = $addOnRule->price;
                        $addOn->save();
                    }
                }     
            }
            $job->save();          
            return response()->json(['success'  => true,
            'message'           =>  'Validation succes.',
            'inputs'            =>  $request->input(),
            'validated inputs'  =>  $request->validated(),
            'testValue'         =>  isset($request->packagecheckboxaddon[0]),
            'jobPrice'          =>  $job->price(),
            'job'               =>  $job,                        
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
