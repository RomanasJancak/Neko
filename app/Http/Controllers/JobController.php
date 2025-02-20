<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;

use Illuminate\Http\Request;

use Illuminate\Pagination\LengthAwarePaginator;


use App\Models\Client;
use App\Models\Distance;
use App\Models\User;
use App\Models\Role;
use App\Models\Status;
use App\Models\PostalCode;
use App\Models\PackageType;
use App\Models\Package;
use App\Models\AddOn;
use App\Models\AddOnRule;
use App\Models\Task;
use App\Models\Day;
use App\Models\Pickuptask;
use App\Models\Returntask;
use App\Models\Customtask;

use App\Services\BackupService;

use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

use App\Models\JobTemplate;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {


            $jobs = Job::paginate(10);
            $day = Day::first();
            $couriers = User::getCouriersWithWorkload($day);
            $statuses = Status::all();
            $packageTypes = PackageType::all();
            

        return view('job.index', compact('jobs','couriers','statuses','packageTypes'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create($customjob = false)
    {
        $clients = Client::all();
        $couriers = User::all();
        $statuses = Status::all();
        $managers = User::all();
        $postalCodes    =   PostalCode::all();


        return view('job.create', compact(
            'clients',
            'couriers', 
            'statuses',
            'managers',
            'postalCodes',
            'customjob',
        ));
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobRequest $request)
    {


        if($request->input('isJobCreationFromIndexPage')){
            try{
                $job                    =   new Job();
                $job->eilesNumeris      =   0;
                $job->manager_id        =   auth()->user()->id;
                $job->status_id         =   $request->input('status_id');
                $job->courrier_id       =   $request->input('courrier_id') == 0 ? null : $request->input('courrier_id');
                $job->clientToBill_id   =   $request->input('billingClientId');
                $job->date              =   $request->input('common_date');
                $job->note  =   $request->input('note');
                $job->save();


                return response()->json([
                'success'  => true,
                'job'       =>  $job,
                'logedInUser'       =>  auth()->user(),                     
                ], 200);
            }catch (\Exception $e){
                return response()->json(['error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'date'  =>  'this',
            ], 500);
            }
        }
        if($request->input('isItCustomJob') === 'true'){
            try{
            $job                    =   new Job();
            $job->eilesNumeris      =   0;
            $job->manager_id        =   1;
            $job->status_id         =   $request->input('status_id');
            $job->courrier_id       =   $request->input('courrier_id') == 0 ? null : $request->input('courrier_id');
            $job->clientToBill_id   =   $request->input('billingClientId');
            $job->date              =   $request->input('common_date');
            $job->save();
            $task                   =   new Task();
            $task->date             =   $request->input('common_date');
            $task->order_number     =   0;
            $task->job_id           =   $job->id;
            $task->status_id        =   $request->input('status_id');
            $task->save();
            $customTask                 =   new Customtask();
            $customTask->task_id        =   $task->id;
            $customTask->status_id      =   $request->input('status_id');
            $customTask->name           =   $request->input('customclientname');
            $customTask->adress_line    =   $request->input('customclientaddressline');
            $customTask->postal_code    =   $request->input('customclientpostalcode');
            $customTask->city           =   $request->input('customclientcity');
            $customTask->country        =   $request->input('customclientcountry'); 
            $customTask->time_begin     =   $request->input('common_date').' '.$request->input('custom_time_begin');
            $customTask->time_end       =   $request->input('common_date').' '.$request->input('custom_time_end');
            $customTask->notes          =   $request->input('generalnotes');
            $customTask->save();
            return response()->json(['success'  => true,
            'message'           =>  'Validation succes.',
            'inputs'            =>  $request->input(),
            'validated inputs'  =>  $request->validated(),
            'customJob'         =>  $request->input('isItCustomJob'),
            'isReturn'          =>  isset($request->isreturncreates),
            'jobPrice'          =>  $job->price(),
            'job'               =>  $job,                        
            ], 200);
            }catch (QueryException $e){
                return response()->json(['error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'date'  =>  'this',
                ], 500);
                
            } catch (\Exception $e){
                return response()->json(['error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'date'  =>  'this',
            ], 500);
            }
        }else{
            //$request->validated();
        }
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
            $job->date              =   $request->input('common_date');
            $job->save();

            $task               =   new Task();
            $task->date         =   $request->input('common_date');
            $task->order_number =   0;
            $task->job_id       =   $job->id;
            $task->status_id       =   $request->input('status_id');
            $task->save();
            $pickuptask         =   new Pickuptask();
            $pickuptask->task_id       =  $task->id;
            $pickuptask->status_id       =   $request->input('status_id');
            $pickuptask->pickup_time_begin       =   $request->input('common_date').' '.$request->input('pickup_time_begin');
            $pickuptask->pickup_time_end       =   $request->input('common_date').' '.$request->input('pickup_time_end');
            $pickuptask->pickupclientname       =   $request->input('pickupclientname');
            $pickuptask->pickupclientaddressline       =   $request->input('pickupclientaddressline');
            $pickuptask->pickupclientcity       =   $request->input('pickupclientcity');
            $pickuptask->pickupclientcountry       =   $request->input('pickupclientcountry');
            $pickuptask->pickupclientpostalcode       =   $request->input('pickupclientpostalcode');
            $pickuptask->notes       =   null;
            $pickuptask->price       =   null;
            $pickuptask->save();
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
            foreach( $request->input('packageType') as $key => $packageTypeId){
                $task                               =   new Task();
                $task->date         =   $request->input('common_date');
                $task->order_number =   $key+1;
                $task->job_id       =   $job->id;
                $task->status_id       =   $request->input('status_id');
                $task->save();
                $package                            =   new Package();
                $packageType                        =   PackageType::find($packageTypeId);
                $package->job_id                    =   $job->id;
                $package->task_id                    =  $task->id;
                $package->packageType_id            =   $packageTypeId; 
                $package->orderNumber               =   $key+1; 
                $package->weight                    =   $key; 
                $package->dimensions                =   $key; 
                $package->quantity                  =   $request->input('packagedropoffquantity')[$key]; 
                $package->dropoff_adress_line       =   $request->input('packagedropooffaddressline')[$key]; 
                $package->dropoff_postal_code       =   $request->input('packagedropoffpostalcode')[$key]; 
                $package->dropoff_city              =   $request->input('packagedropoffcity')[$key]; 
                $package->dropoff_country           =   $request->input('packagedropoffcountry')[$key]; 
                $package->dropoff_name              =   $request->input('packagedropoffname')[$key];
                $package->packagedropofftimebegin   =   $request->input('common_date').' '.$request->input('packagedropofftimebegin')[$key];
                $package->packagedropofftimeend     =   $request->input('common_date').' '.$request->input('packagedropofftimeend')[$key];
                $package->name                      =   $packageType->name;
                $package->price                     =   $packageType->price;
                $package->baseQuantityThreshold     =   $packageType->baseQuantityThreshold;
                $package->maxQuantityThreshold      =   $packageType->maxQuantityThreshold;
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
            if(isset($request->isreturncreates)){           
                $task               =   new Task();
                $task->date         =   $request->input('common_date');
                $task->order_number =   count($request->input('packageType'))+1;
                $task->job_id       =   $job->id;
                $task->status_id       =   $request->input('status_id');
                $task->save();
                $returntask         =   new Returntask();
                $returntask->task_id       =  $task->id;
                $returntask->status_id       =   $request->input('status_id');
                $returntask->time_begin       =   $request->input('common_date').' '.$request->input('return_time_begin');
                $returntask->time_end       =   $request->input('common_date').' '.$request->input('return_time_end');
                $returntask->name       =   $request->input('returnclientname');
                $returntask->adress_line       =   $request->input('returnclientaddressline');
                $returntask->city       =   $request->input('returnclientcity');
                $returntask->country       =   $request->input('returnclientcountry');
                $returntask->postal_code       =   $request->input('returnclientpostalcode');
                $returntask->notes       =   null;
                $returntask->save();
            }
            return response()->json(['success'  => true,
            'message'           =>  'Validation succes.',
            'inputs'            =>  $request->input(),
            'validated inputs'  =>  $request->validated(),
            'customJob'         =>  $request->input('isItCustomJob'),
            'isReturn'          =>  isset($request->isreturncreates),
            'jobPrice'          =>  $job->price(),
            'job'               =>  $job,                        
            ], 200);
        } catch (QueryException $e){
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),], 500);
        } catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),], 500);
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

        try{
            $job = Job::findOrFail($request->id);
            if($request->courierId == '0'){
                $job->courrier_id   =   null;
            }else{
                $job->courrier_id   =   $request->courierId;                    
            }
            $job->date  =   $request->input('common_date') === null ?$request->input('date'):$request->input('common_date');
            $job->status_id =   $request->input('status_id');
            $job->clientToBill_id   =   $request->input('clientId');
            $job->note  =   $request->input('note');
            $job->save();
            return response()->json([
                'message'   => 'Job updated successfully. ',
                'job'       =>  $job,
                'requestData'   =>  $request->all(),
            ]);
        } catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'requests'  =>  $request->all(),
            'common_date'   =>  $request->input('common_date'),
            ], 500);
            
        }
    }
    public function updateStatus(UpdateJobRequest $request, Job $job){
        $job->status_id =   $request->status_id;
        $job->save();
        return redirect()->route('job.show',['job' => $job])->with('success_message', 'Updated sucsesfully');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Job $job)
    {
        try{
            $job = Job::findOrFail($request->id);
            foreach($job->tasks as $task){
                if(isset($task->pickup)){
                    $task->pickup->delete();
                }
                if(isset($task->package)){
                    $task->package->delete();
                }
                if(isset($task->return)){
                    $task->return->delete();
                }
                if(isset($task->customTask)){
                    $task->customTask->delete();
                }
                $task->delete();
            }
            $job->delete();

            return response()->json([
                'message' => 'Job deleted successfully.'
            ]);
        } catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            '$request->jobid'   =>  $request->id,
            'file' => $e->getFile(),
            'line' => $e->getLine(),], 500);
        }
    }
    public function copy(Request $request,Job $job){
        try{
            //dd($request->all());
            $job = Job::find($request->id);
            $newJob = $job->replicate()->fill([
                //'eilesNumeris'  =>  0,
                //'status_id'     =>  $job->status_id,

            ]);
            $newJob->save();
            foreach($job->tasks as $task){
                $newTask = $task->replicate()->fill([
                    'job_id'    =>  $newJob->id,
                ]);
                $newTask->save();
                if(isset($task->pickup)){

                    $newPickup = $task->pickup->replicate()->fill([
                        'task_id'   =>  $newTask->id,
                    ]);
                    $newPickup->save();
                }
                if(isset($task->package)){
                    $newPackage = $task->package->replicate()->fill([
                        'task_id'   =>  $newTask->id,
                    ]);
                    $newPackage->save();
                }
                if(isset($task->return)){
                    $newReturn = $task->return->replicate()->fill([
                        'task_id'   =>  $newTask->id,
                    ]);
                    $newReturn->save();
                }
            }
            
            return response()->json([
                'success'   => true,
                'message'   => 'Job copied successfully. ',
                'data'      => [
                    'NewJobId'   =>  $newJob->id,
                ],
            ]);
        } catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),

            ], 500);
        
        }
    }
    public function getJobInfo($jobId)
    {
        // Fetch the client's information based on the $clientId
        $job = Job::find($jobId);

        if ($job) {
            return response()->json([
                'price'             =>  $job->price(),
                'id'                =>  $job->id,
                'note'              =>  $job->note,
                'courierId'             =>  is_null($job->courier) ? 'none' : $job->courier->id,
                'statusId'              =>  is_null($job->status) ? 'none' : $job->status->id,
                'clientName'            =>  is_null($job->clientToBill) ? 'none' : $job->clientToBill->name,
                'clientId'              =>  is_null($job->clientToBill) ? 'none' : $job->clientToBill->id,
                'pickup'                =>  is_null($job->getPickupTask()) ? 'none' : $job->getPickupTask(),
                'dropoffs'              =>  is_null($job->getDropOffTasks()) ? 'none' : collect($job->getDropOffTasks())->map(function ($dropoff) {
                    return [
                        'id'    =>  $dropoff->id,
                        'packageType_price' => $dropoff->package->packageType->price,
                        'nameOfAddress'  =>  $dropoff->nameOfAddress(),
                        'address'   =>  $dropoff->fullAddress(),
                        'timeWindow'    =>  [
                            'begin' =>  $dropoff->timeWindowBegin(),
                            'end'   =>  $dropoff->timeWindowEnd(),
                        ],
                    ];
                }),
                'returns'               =>  is_null($job->getReturnTask()) ? 'none' : $job->getReturnTask(),
                'return'               =>  is_null($job->getReturnTask()) ? 'none' : $job->getReturnTask(),
                'date'                  =>  $job->date,
                'tasks'                 =>  is_null($job->tasks) ? 'none' : $job->tasks->map(function ($task) {
                    return [
                        'id'        => $task->id,
                        'status'    => $task->status,
                        'name'      => isset($task->pickup)
                                        ?   'Pickup' 
                                        :   (isset($task->package)
                                            ? 'dropoff' 
                                            :   (isset($task->return)
                                                ? 'return'
                                                :(isset($task->customTask)?'custom':null))),
                        'type'      => isset($task->pickup)
                                        ?   $task->pickup 
                                        :   (isset($task->package)
                                            ? $task->package 
                                            :   (isset($task->return)
                                                ? $task->return
                                                :(isset($task->customTask)?$task->customTask:null))),
                        'addressName'   =>  $task->nameOfAddress(),
                        //'timeWindow'    =>  $task->timeWindow(),
                        'timeWindow'    =>  [
                                            'begin' =>  $task->timeWindowBegin(),
                                            'end'   =>  $task->timeWindowEnd(),    
                        ],
                        'fullAddress'   =>  $task->fullAddress(),
                        'quantity'      =>  isset($task->package)?$task->package->quantity:null,
                        'packageType'   =>  isset($task->package)?$task->package->packageType->name:null,
                    ];
                }),      
                ]);
        }

        return response()->json([
            'error' => 'Job not found',
            'jobId' => $jobId,
        ], 404);
    }
    public function update_price_adjustment_number(UpdateJobRequest $request){
        try {
            $job = Job::findOrFail($request->id);
            $job->price_adjustment_number = $request->input('price_adjustment_number');
            $job->save();

            return response()->json([
                'success' => true,
                'message' => 'Price adjustment number updated successfully.',
                'job' => $job,
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
    public function updateJobAjax(UpdateJobRequest $request)
    {
        try{
            $job = Job::findOrFail($request->id);            
            if($request->input('courrier_id')){
                if($request->input('courrier_id') === 'none'){
                    $job->courrier_id = null;
                }else{
                    $job->courrier_id = $request->input('courrier_id');
                }
            }
            if($request->input('status_id')){
                $job->status_id = $request->input('status_id');
                
            }
            $job->save();
            return response()->json([
                'message' => 'Job updated successfully',
                'job' => $job,
            ]);
        } catch (QueryException $e){
        return response()->json(['error' => $e->getMessage()], 500);
        } catch (\Exception $e){
        return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function fetchJobsPaginate(Request $request)
    {
        try {
            $id = $request->get('id', '');
            $clientName = $request->get('clientName', '');
            $date = $request->get('date', '');
            $package = $request->get('package', '');
            $sortField = $request->get('sortField', 'id');
            $sortOrder = $request->get('sortOrder', 'asc');

    
            $jobs = Job::with(['clientToBill', 'tasks'])
            ->when($id, function ($queryBuilder) use ($id) {
                $queryBuilder->where('jobs.id', 'like', '%' . $id . '%');
            })
            ->when($date, function ($queryBuilder) use ($date) {
                $queryBuilder->where('jobs.date', 'like', '%' . $date . '%');
            })
            ->when($clientName, function ($queryBuilder) use ($clientName) {
                $queryBuilder->whereHas('clientToBill', function ($query) use ($clientName) {
                    $query->where('name', 'like', '%' . $clientName . '%');
                });
            })
            ->join('tasks', 'tasks.job_id', '=', 'jobs.id')
            ->join('packages', 'packages.task_id', '=', 'tasks.id')
            ->join('clients', 'jobs.clientToBill_id', '=', 'clients.id')
            ->when($package, function ($queryBuilder) use ($package) {
                $queryBuilder->where(function ($query) use ($package) {
                    $query->where('packages.dropoff_adress_line', 'like', '%' . $package . '%')
                          ->orWhere('packages.dropoff_postal_code', 'like', '%' . $package . '%')
                          ->orWhere('packages.dropoff_name', 'like', '%' . $package . '%');
                });
            })
            ->orderBy($sortField === 'clientName' ? 'clients.name' : 'jobs.' . $sortField, $sortOrder)
            ->distinct() // Ensure distinct results
            ->select('jobs.*') // Select only fields from the jobs table
            ->paginate(10);
    
            $jobs->appends([
                'id' => $id,
                'clientName' => $clientName,
                'date' => $date,
                'sortField' => $sortField,
                'sortOrder' => $sortOrder
            ]);
    
            return response()->json([
                'request'   =>  $request->all(),
                'jobs' =>  $jobs->map(function ($job) {
                    return[
                        'id'    =>  $job->id,
                        'hasReturn' =>  $job->hasReturn(),
                        'urlToLogo'   =>  $job->urlToLogo(),
                        'clientName'    =>  $job->clientToBill->name,
                        'tasks' =>  $job->tasks,
                        'date'  =>  $job->getDate(),
                        'pickup'    =>  (null !== $job->getPickupTask())?[
                                'id'  =>  $job->getPickupTask()->id,
                                'isAddressSameAsClientAdress' =>   $job->clientToBill->isSameAsPickupAdress($job->getPickupTask()->pickupAddressFull()),
                                'namdeOfAddress'    =>  $job->getPickupTask()->nameOfAddress(),
                                'fullAddress'   =>  $job->getPickupTask()->pickupAddressFull(),
                            ]:'',
                        'clientToBill'  =>  $job->clientToBill,
                        'price' =>  $job->price()['totalPrice'],
                        'price' =>  $job->fixed_price === 0? $job->price()['totalPrice'] : $job->fixed_price,
                        'fixed_price'           =>  $job->fixed_price === 0,
                    ];
                }),
                'links' => (string) $jobs->links(),
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
    public function create_JobTemplate_fromThisJob($jobId){
        try{
            $job = Job::findOrFail($jobId);
            $jobTemplate = new JobTemplate();
            $jobTemplate->clientToBill_id = $job->clientToBill_id;
            $jobTemplate->status_id = $job->status_id;
            $jobTemplate->notes = $job->notes;
            $jobTemplate->price = $job->price()['totalPrice'];
            //$jobTemplate->distance = $job->distance();
            $jobTemplate->price_adjustment_number = $job->price_adjustment_number;
            // $jobTemplate->fixedPrice = $job->fixedPrice;
            $jobTemplate->date = $job->date;
            $jobTemplate->pickuptask_data = json_encode($job->getPickupTask());
            $jobTemplate->dropOffs_data = json_encode(collect($job->getDropOffTasks()));
            $jobTemplate->return_data = is_null($job->getReturnTask()) ? null : $job->getReturnTask();
            $jobTemplate->save();
            return response()->json([
                'message' => 'Job template created successfully.',
                'jobTemplate' => $jobTemplate,
            ]);
        } catch (QueryException $e){
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        } catch (\Exception $e){
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
    public function createBackup()
    {
        BackupService::createBackup(new Job());
        
        return redirect()->back()->with('succeses', "model_name".' backup created successfully.');
    }

    
}
