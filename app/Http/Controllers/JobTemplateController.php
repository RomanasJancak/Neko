<?php

namespace App\Http\Controllers;

use App\Models\JobTemplate;
use App\Http\Requests\StoreJobTemplateRequest;
use App\Http\Requests\UpdateJobTemplateRequest;

//======== from model Job 
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
//========


class JobTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobTemplates = JobTemplate::paginate(10);
        $day = Day::find(1);
        $couriers = User::getCouriersWithWorkload($day);
        $statuses = Status::all();

        return view('jobTemplate.index', compact('jobTemplates','couriers','statuses'));
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
    public function store(StoreJobTemplateRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(JobTemplate $jobTemplate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobTemplate $jobTemplate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobTemplateRequest $request, JobTemplate $jobTemplate)
    {
      try{
            $request->validate([
                'id' => 'required|exists:job_templates,id',
                'name' => 'required|string|max:255',
                'locks.client' => ['sometimes','boolean'],
                'locks.pickup' => ['sometimes','boolean'],
                'locks.return' => ['sometimes','boolean'],
                'locks.drops.isLocked' => ['sometimes','boolean'],
                'locks.drops.items' => ['sometimes','array'],
                'locks.drops.items.*.id' => ['sometimes','integer'],
                'locks.drops.items.*.isLocked' => ['sometimes','boolean'],
            ]);
            $jobTemplate = JobTemplate::findOrFail($request->id);
            $jobTemplate->fill($request->except(['locks']));
            if($jobTemplate->isDirty()){
                $jobTemplate->save();
            }
            if(isset($request->locks)){
                if(isset($request->locks['client'])){
                    $jobTemplate->changeLockedField('client', $request->locks['client']);
                }
                if(isset($request->locks['pickup'])){
                    $jobTemplate->changeLockedField('pickup', $request->locks['pickup']);
                }
                if(isset($request->locks['return'])){
                    $jobTemplate->changeLockedField('return', $request->locks['return']);
                }
                if(isset($request->locks['drops'])){
                    $jobTemplate->changeLockedField('dropOffs', $request->locks['drops']['isLocked']);
                }
            }
            return response()->json([
                'success' => true,
                'message' => 'Job template updated successfully.',
                'request' => $request->all(),
                'template'  =>  json_decode($jobTemplate),
                //'template - tasks' => $jobTemplate->tasks(),
            ]);
      }catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            ], 500);
            
      }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobTemplate $jobTemplate)
    {
        //
    }
    public function getJobTemplateInfo($id)
    {
        try{
            $jobTemplate = JobTemplate::findOrFail($id);
            return response()->json([
                'template'  =>  json_decode($jobTemplate),
                //'template - tasks' => $jobTemplate->tasks(),
            ]);
        }catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            ], 500);
            
        }
    }
    protected function prepareDropOffTasks($item, $dropOffsData)
    {


        foreach ($dropOffsData as $index => &$dropOff) {
            $dropOff['isLocked'] = $item->isLocked('dropOff-' . $index);
        }

        return $dropOffsData;
    }
    public function fetchJobTemplatesPaginate(Request $request)
    {
        try{
            $jobTemplates = JobTemplate::paginate(10);
            return response()->json([
                'success' => true,
                'links' => $jobTemplates->links(),
                'items' => $jobTemplates->map(function($item){
                    $pickupData = $item->pickuptask_data ? json_decode($item->pickuptask_data) : null;
                    $dropOffsData = $item->dropOffs_data ? json_decode($item->dropOffs_data,true) : null;
                    $returnData = $item->return_data ? json_decode($item->return_data) : null;
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'clientToBill' => $item->clientToBill ? [
                            'id' => $item->clientToBill->id,
                            'name' => $item->clientToBill->name,
                            'isLocked' => $item->isLocked('client'),
                            ] : null,  
                        'status' => $item->status ? $item->status->name : null,
                        'notes' => $item->notes,
                        'price' => $item->price,
                        'distance' => $item->distance,
                        'price_adjustment_number' => $item->price_adjustment_number,
                        'fixedPrice' => $item->fixedPrice,
                        'date' => $item->date ? $item->date : null,
                        'pickuptask' => [
                            'data' => $pickupData,
                            'addressIsFromClientList' => $item->clientToBill ? $item->clientToBill->hasThisAddress([
                                'address_line_1' => $pickupData->pickupclientaddressline,
                                'postalCode' => $pickupData->pickupclientpostalcode,
                                'city' => $pickupData->pickupclientcity,
                                'country' => $pickupData->pickupclientcountry
                            ]) : false,
                            'isLocked' => $item->isLocked('pickup'),
                        ],
                        'dropOfftasks' => [
                            'data' => $dropOffsData ? $this->prepareDropOffTasks($item, $dropOffsData) : null,
                            'isLocked' => $item->isLocked('dropOffs'),
                        ],
                        'returntask' => [
                            'data' => $item->return_data ? json_decode($item->return_data) : null,
                            'addressIsSameAsPickup' => ($pickupData && $returnData) ? (
                                $pickupData->pickupclientaddressline === $returnData->return->adress_line &&
                                $pickupData->pickupclientpostalcode === $returnData->return->postal_code &&
                                $pickupData->pickupclientcity === $returnData->return->city &&
                                $pickupData->pickupclientcountry === $returnData->return->country
                            ) : false,
                        ],
                        'lockedFields' => $item->lockedFields(),
                    ];
                }),
                'jobTemplates' => $jobTemplates,
                'total' => $jobTemplates->total(),
                'perPage' => $jobTemplates->perPage(),
                'currentPage' => $jobTemplates->currentPage(),
                'lastPage' => $jobTemplates->lastPage(),
                'firstItem' => $jobTemplates->firstItem(),
                'lastItem' => $jobTemplates->lastItem(),
                'hasMorePages' => $jobTemplates->hasMorePages(),
                'isEmpty' => $jobTemplates->isEmpty(),
                'isNotEmpty' => $jobTemplates->isNotEmpty(),
                'count' => $jobTemplates->count(),
                'totalPages' => ceil($jobTemplates->total() / $jobTemplates->perPage()),
                'firstPageUrl' => $jobTemplates->url(1),
                'lastPageUrl' => $jobTemplates->url($jobTemplates->lastPage()),
                'nextPageUrl' => $jobTemplates->nextPageUrl(),
                'previousPageUrl' => $jobTemplates->previousPageUrl(),
                'path' => $jobTemplates->path(),
                'from' => $jobTemplates->firstItem(),
                'to' => $jobTemplates->lastItem(),
                'hasPages' => $jobTemplates->hasPages(),
                'hasMore' => $jobTemplates->hasMorePages(),
                'totalCount' => $jobTemplates->total(),
                'totalItems' => $jobTemplates->total(),
            ]);
        }catch (\Exception $e) {
                        return response()->json([
                            'request'   =>  $request->all(),
                            'error' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                        ], 500);
        }
    }
}
