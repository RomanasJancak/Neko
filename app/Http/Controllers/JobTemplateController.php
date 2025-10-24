<?php

namespace App\Http\Controllers;

use App\Models\JobTemplate;
use App\Http\Requests\StoreJobTemplateRequest;
use App\Http\Requests\UpdateJobTemplateRequest;

//======== from model Job 
use Illuminate\Http\Request;

use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\Address;
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

        return view('jobtemplate.index', compact('jobTemplates','couriers','statuses'));
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
    public function addEmptyDropOff(Request $request, JobTemplate $jobTemplate)
    {
        try{
            $request->validate([
                'id' => 'required|exists:job_templates,id',
            ]);
            $jobTemplate = JobTemplate::findOrFail($request->id);
            $client = Client::find($jobTemplate->clientToBill_id);
            $packageType = $client->packageTypes()->first();
            $defaultAddress = $client->getAllAddresses()->first();
            if(!$defaultAddress){
                return response()->json(['error' => 'Client has no addresses to select for drop-off. Please add an address to the client first.'], 400);
            }
            $dropOffs = json_decode($jobTemplate->dropOffs_data, true) ?? [];
            $newOrderNumber = count($dropOffs) + 1;
            $newDropOff = [
                'id' => null,
                'date' => '0000-01-01 00:00:00',
                'note' => '',
                'job_id' => null,
                'pickup' => null,
                'package' => [
                  'id' => null,
                  'name' => null,
                  'notes' => null,
                  'price' => null,
                  'job_id' => null,
                  'weight' => 0,
                  'task_id' => null,
                  'quantity' => 1,
                  'hasReturn' => 0,
                  'status_id' => config('custom.package.status.default'),
                  'address_id' => null,
                  'created_at' => now()->toDateTimeString(),
                  'updated_at' => now()->toDateTimeString(),
                  'dimensions' => 0,
                  'order_number' => $newOrderNumber,
                  'package_type' => [
                    'id' => $packageType->id,
                    'name' => $packageType->name,
                    'price' => $packageType->price,
                    'created_at' => $packageType->created_at,
                    'updated_at' => $packageType->updated_at,
                    'is_fixed_price' => 0,
                    'maxQuantityThreshold' => $packageType->maxQuantityThreshold,
                    'baseQuantityThreshold' => $packageType->baseQuantityThreshold,
                  ],
                  'packageType_id' => $packageType->id,
                  'dropoff_name' => $defaultAddress->name,
                  'dropoff_country' => $defaultAddress->country,
                  'dropoff_city' => $defaultAddress->city,
                  'dropoff_address_line' => $defaultAddress->address_line_1,
                  'dropoff_postal_code' => $defaultAddress->postalCode->postal_code,
                  'maxQuantityThreshold' => null,
                  'baseQuantityThreshold' => null,
                  'packagedropofftimebegin' => '0000-01-01 00:00:00',
                  'packagedropofftimeend' => '0000-01-01 00:00:01',
                ],
                'status_id' => config('custom.task.status.default'),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'order_number' => $newOrderNumber,
                'isLocked' => false,
            ];
            $dropOffs[] = $newDropOff;
            $jobTemplate->dropOffs_data = json_encode($dropOffs);
            $jobTemplate->save();
            return response()->json([
                'success' => true,
                'message' => 'Drop-off added successfully.',
                'template'  =>  json_decode($jobTemplate),
                'newDropOff' => $newDropOff,
            ]);
        }catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            ], 500);
            
        }
    }
    public function removeDropOff(Request $request){
        try{
            $request->validate([
                'id' => 'required|exists:job_templates,id',
                'order_number' => 'required|integer',
            ]);
            $jobTemplate = JobTemplate::findOrFail($request->id);
            $dropOffs = json_decode($jobTemplate->dropOffs_data, true) ?? [];
            $updatedDropOffs = array_filter($dropOffs, function($dropOff) use ($request) {
                return $dropOff['order_number'] != $request->order_number;
            });
            // Reindex array to maintain order
            $updatedDropOffs = array_values($updatedDropOffs);
            // Update order numbers
            foreach ($updatedDropOffs as $index => &$dropOff) {
                $dropOff['order_number'] = $index + 1;
                if (isset($dropOff['package'])) {
                    $dropOff['package']['order_number'] = $index + 1;
                }
            }
            $jobTemplate->dropOffs_data = json_encode($updatedDropOffs);
            $jobTemplate->save();
            return response()->json([
                'success' => true,
                'message' => 'Drop-off removed successfully.',
                'template'  =>  json_decode($jobTemplate),
            ]);
        }catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            ], 500);
            
        }
    }
    public function addEmptyReturn(Request $request, JobTemplate $jobTemplate)
    {
        try{
            $request->validate([
                'id' => 'required|exists:job_templates,id',
            ]);
            $jobTemplate = JobTemplate::findOrFail($request->id);
            $client = Client::find($jobTemplate->clientToBill_id);
            $defaultAddress = $client->getAllAddresses()->first();
            if(!$defaultAddress){
                return response()->json(['error' => 'Client has no addresses to select for return. Please add an address to the client first.'], 400);
            }
            $returnTask = [
                'id'          => 0,
                'date'        => now()->toDateTimeString(),
                'note'        => '',
                'job_id'      => 0,
                'pickup'      => null,
                'return'      => [
                    'id'           => null,
                    'city'         => $defaultAddress->city,
                    'name'         => $defaultAddress->name,
                    'notes'        => null,
                    'price'        => null,
                    'country'      => $defaultAddress->country,
                    'task_id'      => null,
                    'time_end'     => '0000-01-01 00:00:01',
                    'status_id'    => config('custom.task.status.default'),
                    'address_id'   => null,
                    'created_at'   => now()->toDateTimeString(),   // fixed spelling
                    'updated_at'   => now()->toDateTimeString(),
                    'time_begin'   => '0000-01-01 00:00:00',
                    'adress_line'  => $defaultAddress->address_line_1, // keep original key
                    'is_flexible'  => 1,
                    'postal_code'  => $defaultAddress->postalCode->postal_code,
                ],
                'package'     => null,
                'status_id'   => config('custom.task.status.default'),
                'created_at'  => now()->toDateTimeString(),
                'updated_at'  => now()->toDateTimeString(),
                'order_number'=> 1,
            ];

            $jobTemplate->return_data = json_encode($returnTask);

            $jobTemplate->save();
            return response()->json([
                'success' => true,
                'message' => 'Return task added successfully.',
                'template'  =>  json_decode($jobTemplate),
                'newReturn' => $returnTask,
            ]);
        }catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            ], 500);
            
        }
    }
    public function removeReturn(Request $request){
        try{
            $request->validate([
                'id' => 'required|exists:job_templates,id',
            ]);
            $jobTemplate = JobTemplate::findOrFail($request->id);
            $jobTemplate->return_data = null;
            $jobTemplate->save();
            return response()->json([
                'success' => true,
                'message' => 'Return task removed successfully.',
                'template'  =>  json_decode($jobTemplate),
            ]);
        }catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            ], 500);
            
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobTemplateRequest $request, JobTemplate $jobTemplate)
    {
      try{
            $request->validate([
                'id' => 'required|exists:job_templates,id',
                'name' => 'sometimes|string|max:255',
                'clientToBill_id' => ['sometimes','integer'],
                'pickup.addressId' => ['sometimes','integer'],
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
            $isDirty = false;
            if($jobTemplate->isDirty()){
                $isDirty = true;
                $jobTemplate->save();
            }
            if(isset($request->fixedPrice)){
              if(isset($request->fixedPrice['true'])){
                $jobTemplate->fixedPrice = intval($request->fixedPrice['true'] * 100);
                $jobTemplate->save();
              }
              if(isset($request->fixedPrice['false'])){
                $jobTemplate->fixedPrice = 0;
                $jobTemplate->save();
              }
            }
            if($request->has('note')){
                $jobTemplate->notes = $request->note;
                $jobTemplate->save();
            }
            if(isset($request->drop)){
              if (is_array($request->drop) && !empty($request->drop)) {
                $firstKey = array_key_first($request->drop);
                if(isset($request->drop[$firstKey])) {
                  if (isset($request->drop[$firstKey]['packageQuantity'])) {
                      $packageQuantity = $request->drop[$firstKey]['packageQuantity'];
                      $dropOffs = json_decode($jobTemplate->dropOffs_data, true);

                      foreach ($dropOffs as $index => &$dropOff) {
                          if (isset($dropOff['order_number']) && $dropOff['order_number'] === $firstKey) {
                              if ($packageQuantity == 0) {
                                  
                                  unset($dropOffs[$index]);
                                  
                              } else {
                                  $dropOff['package']['quantity'] = $packageQuantity;
                              }
                              break;
                          }
                      }

                      // reindex array to keep JSON clean
                      $dropOffs = array_values($dropOffs);
                      
                      $jobTemplate->dropOffs_data = $dropOffs;
                      //dd($jobTemplate,$jobTemplate->dropOffs_data);
                  }
                  if(isset($request->drop[$firstKey]['packageTypeId'])) {
                    $packageTypeId = $request->drop[$firstKey]['packageTypeId'];
                    $jobTemplate->changePackageTypeForDropoff($firstKey, $packageTypeId);
                  }
                  if(isset($request->drop[$firstKey]['addressId'])) {
                    $addressId = $request->drop[$firstKey]['addressId'];
                    $address = Address::find($addressId);
                    if ($address) {
                      $existingData = json_decode($jobTemplate->dropOffs_data ?? '{}', true);
                      if (!isset($existingData[$firstKey])) {
                        $existingData[$firstKey] = [];
                      }
                      $existingData[$firstKey]['address'] = [
                        'id' => $address->id,
                        'name' => $address->name,
                        'address_line_1' => $address->address_line_1,
                        'postal_code' => $address->postalCode ? $address->postalCode->postal_code : null,
                        'city' => $address->city,
                        'country' => $address->country,
                      ];
                      $jobTemplate->dropOffs_data = json_encode($existingData);
                    }
                  }
                  if(isset($request->drop[$firstKey]['time']) && is_array($request->drop[$firstKey]['time'])) {
                    $timeData = $request->drop[$firstKey]['time'];
                    $dropOffs = json_decode($jobTemplate->dropOffs_data, true);
                    foreach ($dropOffs as &$dropOff) {
                      if (isset($dropOff['order_number']) && $dropOff['order_number'] === $firstKey) {
                        if (isset($timeData['begin'])) {
                          $existingDateTime = $dropOff['package']['packagedropofftimebegin'] ?? null;
                          $newTime = $timeData['begin'];
                            $datePart = date('Y-m-d', strtotime($existingDateTime));
                            $timePart = date('H:i:s', strtotime($newTime));
                            $dropOff['package']['packagedropofftimebegin'] = $datePart . ' ' . $timePart;
                            //dd($dropOff['package']['packagedropofftimebegin'], $datePart, $timePart, $existingDateTime, $newTime);
                        }
                        if (isset($timeData['end'])) {
                          $existingDateTime = $dropOff['package']['packagedropofftimeend'] ?? null;
                          $newTime = $timeData['end'];
                            $datePart = date('Y-m-d', strtotime($existingDateTime));
                            $timePart = date('H:i:s', strtotime($newTime));
                            $dropOff['package']['packagedropofftimeend'] = $datePart . ' ' . $timePart;
                        }
                      }
                    }
                    $jobTemplate->dropOffs_data = json_encode($dropOffs);
                  }
                  if($request->has("drop.$firstKey.note")) {
                    $note = $request->drop[$firstKey]['note'];
                    $dropOffs = json_decode($jobTemplate->dropOffs_data, true);
                    foreach ($dropOffs as &$dropOff) {
                      if (isset($dropOff['order_number']) && $dropOff['order_number'] === $firstKey) {
                        $dropOff['note'] = $note;
                        break;
                      }
                    }
                    $jobTemplate->dropOffs_data = json_encode($dropOffs);
                  }
                }
                $jobTemplate->save();
              }
            } 
            if(isset($request->return)){
              if (isset($request->return['addressId'])) {
                $returnAddress = Address::find($request->return['addressId']);
                  if ($returnAddress) {
                      $existingData = json_decode($jobTemplate->returntask_data ?? '{}', true);
                      $existingData['returnclientname'] = $returnAddress->name;
                      $existingData['returnclientaddressline'] = $returnAddress->address_line_1;
                      $existingData['returnclientpostalcode'] = $returnAddress->postalCode->postal_code;
                      $existingData['returnclientcity'] = $returnAddress->city;
                      $existingData['returnclientcountry'] = $returnAddress->country;
                      $jobTemplate->returntask_data = json_encode($existingData);
                      $jobTemplate->save();
                }
              }
              if(isset($request->return['time'])){
                $returnData = json_decode($jobTemplate->return_data, true);
                if(isset($request->return['time']['begin'])){
                  $existingDateTime = $returnData['return']['time_begin'];
                  $newTime = $request->return['time']['begin'];
                    $datePart = date('Y-m-d', strtotime($existingDateTime));
                    $timePart = date('H:i:s', strtotime($newTime));
                    $returnData['return']['time_begin'] = $datePart . ' ' . $timePart;
                }
                if(isset($request->return['time']['end'])){
                  $existingDateTime = $returnData['return']['time_end'];
                  $newTime = $request->return['time']['end'];
                    $datePart = date('Y-m-d', strtotime($existingDateTime));
                    $timePart = date('H:i:s', strtotime($newTime));
                    $returnData['return']['time_end'] = $datePart . ' ' . $timePart;
                }
                $jobTemplate->return_data = json_encode($returnData);
                $jobTemplate->save();
              }
              if($request->has("return.note")) {
                $returnData = json_decode($jobTemplate->return_data, true);
                $returnData['note'] = $request->return['note'];
                $jobTemplate->return_data = json_encode($returnData);
                $jobTemplate->save();
              }
            }
            if(isset($request->clientToBill_id)){
                $client = Client::find($request->clientToBill_id);
                if($client){
                    $jobTemplate->clientToBill()->associate($client);
                    $jobTemplate->save();
                }
            }
            if(isset($request->pickup)){
              //if(isset($request->pickup['note'])){
              if($request->has('pickup.note')){
                $pickupData = json_decode($jobTemplate->pickuptask_data, true);
                $pickupData['note'] = $request->pickup['note'];
                $jobTemplate->pickuptask_data = json_encode($pickupData);
                $jobTemplate->save();
              }
            }
            if (isset($request->pickup) && isset($request->pickup['addressId'])) {
                $pickupAddress = Address::find($request->pickup['addressId']);
                if ($pickupAddress) {

                    $existingData = json_decode($jobTemplate->pickuptask_data ?? '{}', true);


                    $existingData['pickupclientname'] = $pickupAddress->name;
                    $existingData['pickupclientaddressline'] = $pickupAddress->address_line_1;
                    $existingData['pickupclientpostalcode'] = $pickupAddress->postalCode->postal_code;
                    $existingData['pickupclientcity'] = $pickupAddress->city;
                    $existingData['pickupclientcountry'] = $pickupAddress->country;


                    $jobTemplate->pickuptask_data = json_encode($existingData);
                    $jobTemplate->save();
                }
            }
            if(isset($request->pickup['time'])){
                $pickupData = json_decode($jobTemplate->pickuptask_data, true);
                if(isset($request->pickup['time']['begin'])){
                  $existingDateTime = $pickupData['pickup_time_begin'];
                  $newTime = $request->pickup['time']['begin'];
                    $datePart = date('Y-m-d', strtotime($existingDateTime));
                    $timePart = date('H:i:s', strtotime($newTime));
                    $pickupData['pickup_time_begin'] = $datePart . ' ' . $timePart;
                }
                if(isset($request->pickup['time']['end'])){
                  $existingDateTime = $pickupData['pickup_time_end'];
                  $newTime = $request->pickup['time']['end'];
                    $datePart = date('Y-m-d', strtotime($existingDateTime));
                    $timePart = date('H:i:s', strtotime($newTime));
                    $pickupData['pickup_time_end'] = $datePart . ' ' . $timePart;
                }
                $jobTemplate->pickuptask_data = json_encode($pickupData);
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
                    $jobTemplate->changeLockedField('dropOffs', $request->locks['drops']);
                }
            }
            return response()->json([
                'success' => true,
                'isDirty' => $isDirty,
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
                        'fixedPrice' => $item->fixedPrice !== null ? $item->fixedPrice / 100 : null,
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
                            //'a' => dd($pickupData,$returnData),
                            'addressIsSameAsPickup' => ($pickupData && $returnData) ? (
                                
                                $pickupData->pickupclientaddressline === $returnData->return->adress_line &&
                                $pickupData->pickupclientpostalcode === $returnData->return->postal_code &&
                                $pickupData->pickupclientcity === $returnData->return->city &&
                                $pickupData->pickupclientcountry === $returnData->return->country
                            ) : false,
                            'isLocked' => $item->isLocked('return'),
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
