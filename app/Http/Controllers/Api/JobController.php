<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Job;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Services\JobPriceSnapshotService;

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
use App\Models\Note;
use App\Models\InvoiceItem;
use App\Models\Invoice;
use App\Services\InvoicePricingService;


use App\Services\BackupService;
use App\Services\SettingsService;

use App\Settings\UserSettingDefinition;

use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

use App\Models\JobTemplate;

class JobController extends Controller
{
  /**
   * @OA\Get(
   *     path="/api/jobs",
   *     summary="Get list of jobs",
   *     tags={"Jobs"},
   *     @OA\Parameter(
   *         name="id",
   *         in="query",
   *         description="Filter jobs by ID (partial match)",
   *         required=false,
   *         @OA\Schema(type="string")
   *     ),
   *     @OA\Parameter(
   *         name="date",
   *         in="query",
   *         description="Filter by date (YYYY-MM-DD)",
   *         required=false,
   *         @OA\Schema(type="string", format="date")
   *     ),
   *     @OA\Parameter(
   *         name="status",
   *         in="query",
   *         description="Filter by job status name",
   *         required=false,
   *         @OA\Schema(type="string")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Successful response",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="data", type="object",
   *                 @OA\Property(property="current_page", type="integer", example=1),
   *                 @OA\Property(property="data", type="array",
   *                     @OA\Items(
   *                         @OA\Property(property="id", type="integer", example=15),
   *                         @OA\Property(property="clientName", type="string", example="Acme Corp"),
   *                         @OA\Property(property="status", type="string", example="Completed"),
   *                         @OA\Property(property="date", type="string", example="2025-12-18"),
   *                         @OA\Property(property="price", type="number", format="float", example=1200.50)
   *                     )
   *                 )
   *             )
   *         )
   *     )
   * )
   */

  public function index(Request $request)
    {

        $query = Job::with(['clientToBill', 'tasks.package']);
        if ($id = $request->get('id')) {
            $query->where('jobs.id', 'like', "%{$id}%");
        }
        $jobs = $query->paginate(10)->appends($request->query());
        //$jobs = Job::paginate(2);
        $jobs->getCollection()->transform(function ($job) {
          return [
            'id' => $job->id,
            'clientName' => optional($job->clientToBill)->name,
            'status' => optional($job->status)->name,
            'date' => $job->date,
            'price' => method_exists($job, 'price') ? $job->price() : null,
          ];
        });
        return response()->json([
            'success' => true,
            'data' => $jobs,
        ]);
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
                $job->note              =   $request->input('note');
                if(($request->input('note') !== null)){
                  $job->notes()->create([
                      'content' => $request->input('note'),
                      'user_id' => auth()->id(),
                  ]);
                }
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
            //$job->note  =   $request->input('note');
            if($request->input('note') !== null && $request->input('note') !== ''){
              
              if($job->latestNote){
                  //dd($job->latestNote->content, $request->input('note'),$job->latestNote->content !== $request->input('note'));
                  if($job->latestNote->content !== $request->input('note')){
                    
                      $job->notes()->create([
                          'content' => $request->input('note'),
                          'user_id' => auth()->id(),
                      ]);
                  }
              }else{
                $job->notes()->create([
                    'content' => $request->input('note'),
                    'user_id' => auth()->id(),
                ]);
              }
            }

            $job->save();
            return response()->json([
                'success' => true,
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
        return redirect()->route('job.show',['job' => $job])->with('success_message', 'Job status updated sucsesfully');
    }
    public function moveToOtherInvoiceItem(Request $request, Job $job){
      try{
        $validated = $request->validate([
          'invoice_item_id' => 'required|exists:invoice_items,id',
        ]);
        $newItem = InvoiceItem::findOrFail($validated['invoice_item_id']);
                $pricingService = app(InvoicePricingService::class);
                $pricingService->recalculateItemAndInvoice($job->invoiceItem);
        $job->invoice_item_id = $newItem->id;
        $job->save();
                $pricingService->recalculateItemAndInvoice($newItem);
        /*
        return response()->json([
          'success' => true,
          'message' => 'Job moved to new invoice item successfully.',
          'jobId'   =>  $job->id,
          'newInvoiceItemId'   =>  $newItem->id,
          'newInvoiceId'   =>  $newItem->invoice ? $newItem->invoice->id : null,
        ]);
        */
        return redirect()->back()->with('success', 'Job moved to new invoice item successfully.');
      }catch (\Exception $e){
            return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'requests'  =>  $request->all(),
            ], 500);
      }
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
    public function storeFromString(Request $request){
        try{
            $jobData = json_decode($request->input('job_string'), true);
            $jobArray = collect($jobData)->except(['id', 'tasks'])->toArray();
            $job = new Job($jobArray);
            $job->save();
            foreach ($jobData['tasks'] as $taskWrapper) {
                $taskData = collect($taskWrapper['task'])->except(['id', 'pickup', 'package', 'return'])->toArray();
                $taskData['job_id'] = $job->id;
                $task = new Task($taskData);
                $task->save();
                $type = $taskWrapper['type'];
                $attributes = $taskWrapper['attributes'];
                $attributes['task_id'] = $task->id;
                switch ($type) {
                case 'pickup':
                    Pickuptask::create($attributes);
                    break;
                case 'package':
                    Package::create($attributes);
                    break;
                case 'return':
                    Returntask::create($attributes); // assuming the model name is ReturnDelivery
                    break;
            }

            }
            return response()->json([
                'success'   => true,
                'message'   => 'Job copied successfully. ',
                'data'      => [
                    'request'   =>  $request->all(),
                    'jobToArray'   =>  $jobData,
                    'jobToArrayWithoutTasks'   =>  $jobArray,
                    'jobId'       =>  $job->id,
                ],
            ]);
        } catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'request'   =>  $request->all(),
            ], 500);
        
        }
    }
    public function restoreNoteFromTemplate(Request $request){
      if($request->input('jobId') !== null && $request->input('jobId') !== ''){
        $job = Job::find($request->input('jobId'));
        if($job){
          //dd($job,$job->jobTemplate);
          $template = $job->jobTemplate;
          if($template){
            $job->notes()->create([
                'content' => $template->notes,
                'user_id' => auth()->id(),
            ]);
            $job->save();
        }    return response()->json([
            'success' => true,
            'message' => 'Note restored from template successfully',
            'noteContent' => $job->latestNote->content,
        ], 200);
          }else{
            return response()->json(['error' => 'No template associated with this job',], 500);
          }
      }else{
        return response()->json(['error' => 'No job id provided',], 500);
      }
    }
    private function createJobFromTemplate(JobTemplate $template, $date){

        $job                    =   new Job();
        $job->eilesNumeris      =   0;
        $job->manager_id        =   auth()->user()->id;
        $job->status_id         =   10;
        $job->courrier_id       =   null;
        $job->clientToBill_id   =   $template->clientToBill_id;
        $job->date              =   $date;
        $job->save();
        $job->notes()->create([
            'content' => $template->notes,
            'user_id' => auth()->id(),
        ]);
        $job->save();
        foreach($template->lockedFields() as $field){
          if($field->is_locked){
            $job->changeLockedField($field->field_name, true);
          }
        }
        if(isset($template->pickuptask_data) && $template->pickuptask_data != null){
            $task                   =   new Task();
            $task->date             =   $date;
            $task->order_number     =   0;
            $task->job_id           =   $job->id;
            $task->status_id        =   10;
            $task->save();
            $pickuptaskData = json_decode($template->pickuptask_data, true);
            $pickuptask                 =   new Pickuptask();
            $pickuptask->task_id        =   $task->id;
            $pickuptask->status_id      =   10;
            $pickuptask->pickupclientaddressline    =   $pickuptaskData['pickupclientaddressline'] ?? null;
            $pickuptask->pickupclientpostalcode    =   $pickuptaskData['pickupclientpostalcode'] ?? null;
            $pickuptask->pickupclientcity           =   $pickuptaskData['pickupclientcity'] ?? null;
            $pickuptask->pickupclientcountry        =   $pickuptaskData['pickupclientcountry'] ?? null; 
            $pickuptask->pickup_time_begin     =   $pickuptaskData['pickup_time_begin'] ?? '09:00:00';
            $pickuptask->pickup_time_end       =   $pickuptaskData['pickup_time_end'] ?? '17:00:00';
            $pickuptask->note          =   $pickuptaskData['note'] ?? null;
            $pickuptask->save();
        }
        
        if($template->isLocked('pickup')){
          $job->changeLockedField('pickup', true);
        }
        $dropoffData = json_decode($template->dropOffs_data, true);
        foreach ($dropoffData as $key => $dropoffDataItem) {
            //dd($dropoffDataItem);
            $task                               =   new Task();
            $task->date         =   $date;
            $task->order_number =   $dropoffDataItem['order_number'];
            $task->job_id       =   $job->id;
            $task->status_id       =   10;
            $task->save();
            $package                            =   new Package();
            $packageType                        =   PackageType::find($dropoffDataItem['package']['package_type']['id']);
            $package->job_id                    =   $job->id;
            $package->task_id                   =   $task->id;
            $package->packageType_id            =   $packageType->id; 
            $package->orderNumber               =   $dropoffDataItem['order_number']; 
            $package->weight                    =   $dropoffDataItem['weight'] ?? 0; 
            $package->dimensions                =   $dropoffDataItem['dimensions'] ?? '0x0x0'; 
            $package->quantity                  =   $dropoffDataItem['package']['quantity'] ?? 1;
            $package->dropoff_adress_line       =   $dropoffDataItem['address']['address_line_1'] ?? null;
            $package->dropoff_postal_code       =   $dropoffDataItem['address']['postal_code'] ?? null;
            $package->dropoff_city              =   $dropoffDataItem['address']['city'] ?? null;
            $package->dropoff_country           =   $dropoffDataItem['address']['country'] ?? null;
            $package->dropoff_name              =   $dropoffDataItem['address']['name'] ?? null;
            $package->packagedropofftimebegin   =   $dropoffDataItem['package']['packagedropofftimebegin'] ?? null;
            $package->packagedropofftimeend     =   $dropoffDataItem['package']['packagedropofftimeend'] ?? null;
            $package->name                      =   $dropoffDataItem['package']['name'] ?? null;
            $package->price                     =   $dropoffDataItem['package']['price'] ?? null;
            $package->baseQuantityThreshold     =   $dropoffDataItem['package']['baseQuantityThreshold'] ?? null;
            $package->maxQuantityThreshold      =   $dropoffDataItem['package']['maxQuantityThreshold'] ?? null;
            $package->save();
        }
        $returnData = json_decode($template->return_data, true);
        //dd(isset($template->returntask_data), $template->returntask_data);
        if(isset($returnData)){
            $task               =   new Task();
            $task->date         =   $date;
            $task->order_number =   count($dropoffData)+1;
            $task->job_id       =   $job->id;
            $task->status_id       =   10;
            $task->save();
            $returntask         =   new Returntask();
            $returntask->task_id        =  $task->id;
            $returntask->status_id      =   10;
            $returntask->time_begin     =   $date . ' ' . preg_replace('/^\d{4}-\d{2}-\d{2} /', '', $returnData['return']['time_begin']) ;
            $returntask->time_end       =   $date . ' ' . preg_replace('/^\d{4}-\d{2}-\d{2} /', '', $returnData['return']['time_end']) ;
            $returntask->name           =   $returnData['return']['name'] ?? null;
            $returntask->adress_line       =   $returnData['return']['adress_line'] ?? null;
            $returntask->city       =   $returnData['return']['city'] ?? null;
            $returntask->country       =   $returnData['return']['country'] ?? null;
            $returntask->postal_code       =   $returnData['return']['postal_code'] ?? null;
            $returntask->notes       =   $returnData['return']['notes'] ?? null;
            $returntask->is_flexible       =   $returnData['return']['is_flexible'] ?? false;
            $returntask->save();
        }
        return $job;
    }
    private function transformToProperFilterValue($filters){
      $days = collect($filters)
        ->flatMap(function ($item) {
            return explode(',', $item);
        })
        ->map(fn ($day) => (int) $day) // convert to integers
        ->unique()
        ->values()
        ->all();
        return $days;
    }
    public function storeFromTemplate(Request $request){
     try{
      $template = JobTemplate::find($request->id);
      $jobs = [];
      if(!$template){
          return response()->json(['error' => 'Template not found'], 404);
      }else{
        if ($request->input('start') === $request->input('end')) {
          $date = $request->input('start');
          $jobs[] = $this->createJobFromTemplate($template, $date);
        }else{
            $filters = $request->input('days');
            $filters = $this->transformToProperFilterValue($filters);
            if($filters && is_array($filters)){
                $startDate = \Carbon\Carbon::parse($request->input('start'));
                $endDate = \Carbon\Carbon::parse($request->input('end'));
                $currentDate = $startDate->copy();
                    $includeBankHolidays = in_array('bankholidays', $request->input('days'));

                // Example: you might load holidays from DB or config
                $bankHolidays = new Job();
                $bankHolidays = $bankHolidays->getBankHolidaysAttribute()->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->toDateString())->toArray();

                while ($currentDate->lte($endDate)) {
                  $dayNumber = (int) $currentDate->format('N'); // 1 = Monday, ... 7 = Sunday

                  // Skip if bank holiday and not explicitly included
                  if (!$includeBankHolidays && in_array($currentDate->toDateString(), $bankHolidays)) {
                      $currentDate->addDay();
                      continue;
                  }

                  if (in_array($dayNumber, $filters)) {
                      $jobs[] = $this->createJobFromTemplate($template, $currentDate->toDateString());
                  }

                  $currentDate->addDay();
            }
            }else{  
                return response()->json(['error' => 'No days selected for multi-day job creation.'], 400);
            }
        }
        if(empty($jobs)){
            return response()->json(['error' => 'No jobs were created. Please check your input.'], 400);
        }else{
          foreach ($jobs as $job) {
            $job->jobTemplate()->associate($template);
            $job->save();
          }
        }
          return response()->json([
            'success' => true,
            'message' => 'Job(s) created from template successfully. ',
            'request'   =>  $request->all(),
            'template'  =>  $template,
            'template_data'  =>  json_decode($template->pickuptask_data, true),
            'job'       =>  $jobs,
          ], 200);
      }
     }catch (\Exception $e){
        return response()->json(['error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'request'   =>  $request->all(),
        ], 500);
     }



        try{
            $template = JobTemplate::find($request->templateId);
            if(!$template){
                return response()->json(['error' => 'Template not found'], 404);
            }
            $jobData = json_decode($template->template_data, true);
            $jobArray = collect($jobData)->except(['id', 'tasks'])->toArray();
            $job = new Job($jobArray);
            $job->date = $request->date; // Override date from request
            $job->clientToBill_id = $request->clientId; // Override client from request
            $job->save();
            foreach ($jobData['tasks'] as $taskWrapper) {
                $taskData = collect($taskWrapper['task'])->except(['id', 'pickup', 'package', 'return'])->toArray();
                $taskData['job_id'] = $job->id;
                $task = new Task($taskData);
                $task->save();
                $type = $taskWrapper['type'];
                $attributes = $taskWrapper['attributes'];
                $attributes['task_id'] = $task->id;
                switch ($type) {
                case 'pickup':
                    Pickuptask::create($attributes);
                    break;
                case 'package':
                    Package::create($attributes);
                    break;
                case 'return':
                    Returntask::create($attributes); // assuming the model name is ReturnDelivery
                    break;
            }

            }
            return response()->json([
                'success'   => true,
                'message'   => 'Job(s) created from template successfully. ',
                'data'      => [
                    'request'   =>  $request->all(),
                    'jobToArray'   =>  $jobData,
                    'jobToArrayWithoutTasks'   =>  $jobArray,
                    'jobId'       =>  $job->id,
                ],
            ]);
        } catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'request'   =>  $request->all(),
            ], 500);
        
        }
    }
    public function getJobToString($jobId){
        try{
            $job = Job::find($jobId);
            $newJob_array = $job->toArray();
            $newJob_array['tasks'] = $job->tasks->map(function ($task) {
                $subtaskType = null;
                $subtaskData = null;
                if ($task->pickup) {
                    $subtaskType = 'pickup';
                    $subtaskData = $task->pickup->toArray();
                } elseif ($task->package) {
                    $subtaskType = 'package';
                    $subtaskData = $task->package->toArray();
                } elseif ($task->return) {
                    $subtaskType = 'return';
                    $subtaskData = $task->return->toArray();
                }
                return [
                    'id' => $task->id,
                    'task' => $task->toArray(),
                    'type' => $subtaskType,
                    'attributes' => $subtaskData,
                ];
            });
            return response()->json([
                'success'   => true,
                'message'   => 'Job string representation fetched successfully. ',
                'data'      => [
                    'Job_to_array'   =>  $newJob_array,
                    'Job_to_json'    =>  json_encode($newJob_array),
                ],
            ]);
        } catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),

            ], 500);
        
        }
    }
    public function copy(Request $request,Job $job){
        try{

            $job = Job::find($request->id);
            $newJob = $job->replicate()->fill([
            ]);
            $newJob->save();
            foreach($job->tasks as $task){
                $newTask = $task->replicate()->fill([
                    'job_id'    =>  $newJob->id,
                ]);
                $newTask->save();
                $task_string = (string) $task;
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
            $newJob->refresh();
            $newJob->save();

            //$newJob_array = json_encode($newJob_array, JSON_PRETTY_PRINT);
            return response()->json([
                'success'   => true,
                'message'   => 'Job copied successfully. ',
                'data'      => [
                    'NewJobId'   =>  $newJob->id,
                    //'NewJobStringRepresentation'   =>  $newJob_string,
                    //'NewJobArrayRepresentation'   =>  $newJob_array,
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
            $snapshotService = app(JobPriceSnapshotService::class);
            $pricePayload = $snapshotService->snapshotToPayload($job) ?? $job->price();
            return response()->json([
                'price'             =>  $pricePayload,
                'id'                =>  $job->id,
                'note'              =>  $job->latestNote,
                'is_note_different_from_template_note' => $job->isNoteDifferentThanTemplateNote(),
                'notes'             =>  $job->notes->isEmpty() ? 'none' : $job->notes->map(function ($note) {
                    return [
                        'id'        =>  $note->id,
                        'content'   =>  $note->content,
                        'user'      =>  is_null($note->user) ? 'none' : $note->user->name,
                        'created_at'=>  $note->created_at->toDateTimeString(),
                    ];
                }),
                'eilesNumeris'      =>  $job->eilesNumeris,
                'hasReturn' =>  $job->hasReturn(),
                'urlToLogo'   =>  $job->urlToLogo(),
                'courierId'             =>  is_null($job->courier) ? 'none' : $job->courier->id,
                'statusId'              =>  is_null($job->status) ? 'none' : $job->status->id,
                'clientToBill'  =>  $job->clientToBill,
                'clientName'            =>  is_null($job->clientToBill) ? 'none' : $job->clientToBill->name,
                'clientId'              =>  is_null($job->clientToBill) ? 'none' : $job->clientToBill->id,
                'template'              =>  is_null($job->jobTemplate) ? 'none' : $job->jobTemplate,
                'lockedFields' => empty($job->lockedFields())
                    ? 'none'
                    : array_map(function ($field) {
                        return [
                            'field_name' => $field->field_name,
                            'is_locked'  => $field->is_locked,
                        ];
                    }, $job->lockedFields()),
                'pickup' => is_null($job->getPickupTask()) ? 'none' : array_merge(
                    $job->getPickupTask()->toArray(),
                    [
                        'nameOfAddress' => $job->getPickupTask()->nameOfAddress(),
                        'fullAddress'   => $job->getPickupTask()->pickupAddressFull(),
                        'timeWindow'    => [
                            'begin' => $job->getPickupTask()->timeWindowBegin(),
                            'end'   => $job->getPickupTask()->timeWindowEnd(),
                        ],
                        'isAddressSameAsClientAddress' => $job->clientToBill
                            ? $job->clientToBill->isSameAsPickupAdress($job->getPickupTask()->pickupAddressFull())
                            : false,
                    ]
                ),
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
                'fixed_price'           =>  $job->fixed_price === 0,
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
                        'shortAddress'  =>  $task->addressShort(),
                        'quantity'      =>  isset($task->package)?$task->package->quantity:null,
                        'packageType'   =>  isset($task->package)?$task->package->packageType->name:null,
                        'package'       =>  isset($task->package)?$task->package:null,
                    ];
                }),
                'invoiceItem'           =>  is_null($job->invoiceItem) ? 'none' : $job->invoiceItem,    
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

            if ($job->isInvoiced()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoiced jobs are immutable and cannot change price adjustment.',
                ], 422);
            }

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
            $statusFilterValue = $request->get('status','');
            $package = $request->get('package', '');
            $startDate = $request->get('startDate', '');
            $endDate = $request->get('endDate', '');
            
            $sortField = $request->get('sortField', 'id');
            $sortOrder = $request->get('sortOrder', 'asc');
            $dropOffFilterValue = $request->get('dOsp', []); // expects an array
            $dropOffFilterValue = is_string($dropOffFilterValue) ? json_decode($dropOffFilterValue, true) : $dropOffFilterValue;


    
            $jobIds = Job::query()
                ->leftJoin('tasks', 'tasks.job_id', '=', 'jobs.id')
                ->leftJoin('packages', 'packages.task_id', '=', 'tasks.id')
                ->join('clients', 'jobs.clientToBill_id', '=', 'clients.id')
                ->when($id, fn($q) => $q->where('jobs.id', 'like', "%$id%"))
                ->when($date, fn($q) => $q->where('jobs.date', 'like', "%$date%"))
                ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('jobs.date', [$startDate, $endDate]);
                })
                ->when($clientName, fn($q) => 
                    $q->where('clients.name', 'like', "%$clientName%")
                )
                ->when($statusFilterValue, function ($q) use ($statusFilterValue) {
                    if (is_array($statusFilterValue)) {
                        $q->whereIn('jobs.status_id', $statusFilterValue);
                    } else {
                        $q->where('jobs.status_id', $statusFilterValue);
                    }
                })
                ->when($package && is_array($dropOffFilterValue) && count($dropOffFilterValue) > 0, function ($q) use ($package, $dropOffFilterValue) {
                    $q->where(function ($subQ) use ($package, $dropOffFilterValue) {
                        foreach ($dropOffFilterValue as $column) {
                            if ($column === 'packageType_id') {
                                $matchingTypeIds = \App\Models\PackageType::where('name', 'LIKE', "%{$package}%")->pluck('id');
                                if ($matchingTypeIds->isNotEmpty()) {
                                    $subQ->orWhereIn('packages.packageType_id', $matchingTypeIds);
                                }
                            } else {
                                $subQ->orWhere("packages.$column", 'LIKE', "%{$package}%");
                            }
                        }
                        // include jobs that simply have no package at all
                        //$subQ->orWhereNull('packages.id');
                    });
                })


                ->distinct();
                //dd($jobIds->toSql(), $jobIds->getBindings());
                $jobIds = $jobIds
                ->pluck('jobs.id'); 

            $jobs = Job::with(['clientToBill', 'tasks'])
                ->whereIn('jobs.id', $jobIds)
                ->when($sortField === 'clientName', function ($q) use ($sortOrder) {
                    $q->join('clients', 'jobs.clientToBill_id', '=', 'clients.id')
                    ->orderBy('clients.name', $sortOrder)
                    ->select('jobs.*'); 
                })
                ->when($sortField === 'status', function ($q) use ($sortOrder) {
                    $q->join('statuses', 'jobs.status_id', '=', 'statuses.id')
                    ->orderBy('statuses.name', $sortOrder)
                    ->select('jobs.*'); 
                }, function ($q) use ($sortField, $sortOrder) {
                    $q->orderBy("jobs.$sortField", $sortOrder);
                })
                ->paginate(10);


                        $jobs->appends([
                            'id' => $id,
                            'clientName' => $clientName,
                            'date' => $date,
                            'sortField' => $sortField,
                            'sortOrder' => $sortOrder,
                            'package' => $package,
                            'status' => $statusFilterValue,
                            'startDate' => $startDate,
                            'endDate' => $endDate,
                            'dOsp' => json_encode($dropOffFilterValue),
                        ]);
                
                        return response()->json([
                            'request'   =>  $request->all(),
                            'jobs' =>  $jobs->map(function ($job) {
                                return[
                                    'id'    =>  $job->id,
                                    'is_note_different_from_template_note' => $job->isNoteDifferentThanTemplateNote(),
                                    'hasReturn' =>  $job->hasReturn(),
                                    'urlToLogo'   =>  $job->urlToLogo(),
                                    'clientName'    =>  $job->clientToBill->name,
                                    'status'        =>  $job->status,
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
                                    'template'              =>  is_null($job->jobTemplate) ? 'none' : $job->jobTemplate,
                                    'lockedFields' => empty($job->lockedFields())
                                        ? 'none'
                                        : array_map(function ($field) {
                                            return [
                                                'field_name' => $field->field_name,
                                                'is_locked'  => $field->is_locked,
                                            ];
                                        }, $job->lockedFields()),
                                ];
                            }),
                            'links' => (string) $jobs->links(),
                            // 'jobsIdQuery' => $jobIdsQuery->toSql(),
                        ]);
                    } catch (QueryException $e) {
                        return response()->json([
                            'request'   =>  $request->all(),
                            'error' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                        ], 500);
                    } catch (\Exception $e) {
                        return response()->json([
                            'request'   =>  $request->all(),
                            'error' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                        ], 500);
                    }
    }
    public function create_JobTemplate_fromThisJob(Request $request, $jobId ){
        try{
            $job = Job::findOrFail($jobId);
            $jobTemplate = new JobTemplate();
            $jobTemplate->name = $request->input('name');
            $jobTemplate->clientToBill_id = $job->clientToBill_id;
            $jobTemplate->status_id = $job->status_id;
            $jobTemplate->notes = $job->latestNote->content ?? null;
            $jobTemplate->price = $job->price()['totalPrice'];
            //$jobTemplate->distance = $job->distance();
            $jobTemplate->price_adjustment_number = $job->price_adjustment_number;
            // $jobTemplate->fixedPrice = $job->fixedPrice;
            $jobTemplate->date = $job->date;
            $jobTemplate->pickuptask_data = json_encode($job->getPickupTask());
            $jobTemplate->dropOffs_data = json_encode(collect($job->getDropOffTasks()));
            $jobTemplate->return_data = is_null($job->getReturnTask()) ? null : json_encode($job->getReturnTask());
            $jobTemplate->save();
            return response()->json([
                'success' => true,
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
    public function removeFromInvoiceItem(Job $job)
    {
      try{
        $invoiceItem = $job->invoiceItem;
        $job->invoice_item_id = null;
        $job->status_id = 23;
        $job->save();
                $pricingService = app(InvoicePricingService::class);
                $pricingService->recalculateItemAndInvoice($invoiceItem);
        // return response()->json([
        //   'success' => true,
        //   'message' => 'Job removed from invoice item successfully.',
        //   'job' => $job,
        //   'invoiceItem' => $invoiceItem,
        // ]);
        return redirect()->back()->with('success', 'Job removed from invoice item successfully.');
      }catch (\Exception $e){
        // return response()->json([
        //   'success' => false,
        //   'message' => 'Failed to remove job from invoice item.',
        //   'error' => $e->getMessage(),
        // ], 500);
        return redirect()->back()->with('error', 'Failed to remove job from invoice item: ' . $e->getMessage());
      }
    }

    
}
