<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Pickuptask;
use App\Models\ReturnTask;
use App\Models\Package;
use App\Models\Job;
use App\Models\Status;
use App\Models\PackageType;
use App\Models\CustomTask;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

use Illuminate\Http\Request;

use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreTaskRequest $request)
    {
        try{
            $task   =   new Task();
            $task->date             =   $request->input('date');
            $task->order_number     =   0;
            $task->job_id           =   $request->input('jobId');
            $task->status_id        =   $request->input('status_id');
            $task->save();
            if($request->input('type') === 'pickup'){
                $pickupTask = new PickupTask();
                $pickupTask->task_id = $task->id;
                $pickupTask->status_id = $task->status_id;
                $pickupTask->setTimeWindow($request->input('time.begin'),$request->input('time.end'));
                $pickupTask->setAddress(
                    $request->input('address.name'),
                    $request->input('address.country'),
                    $request->input('address.city'),
                    $request->input('address.postalCode'),
                    $request->input('address.addressLine'),
                );
                $pickupTask->save();
            }
            if($request->input('type') === 'dropOff'){
                $package = new Package;
                $package->task_id = $task->id;
                $package->status_id = $task->status_id;
                $package->packageType_id = $request->input('package.type');
                $package->weight = $request->input('package.weight');
                $package->dimensions = 0;
                $package->quantity = $request->input('package.quantity');
                $package->setTimeWindow($request->input('time.begin'),$request->input('time.end'));
                $package->setAddress(
                    $request->input('address.name'),
                    $request->input('address.country'),
                    $request->input('address.city'),
                    $request->input('address.postalCode'),
                    $request->input('address.addressLine'),
                );
                $package->save();
            }
            
            if($request->input('type') === 'return'){
                $returnTask = new ReturnTask();
                $returnTask->task_id = $task->id;
                $returnTask->status_id = $task->status_id;
                $returnTask->setTimeWindow($request->input('time.begin'),$request->input('time.end'));
                $returnTask->is_flexible = $request->input('returnTask.is_flexible');
                if($request->input('returnTask.is_flexible')){
                    if($request->input('date') !== $request->input('returnTask.date')){
                        $returnTaskDate = Carbon::parse($request->input('returnTask.date'));
                        $timeBegin = Carbon::parse($request->input('time.begin'));
                        $timeEnd = Carbon::parse($request->input('time.end'));
                        $timeBegin->setDate($returnTaskDate->year, $returnTaskDate->month, $returnTaskDate->day);
                        $timeEnd->setDate($returnTaskDate->year, $returnTaskDate->month, $returnTaskDate->day);
                        $returnTask->setTimeWindow(
                            $timeBegin->toDateTimeString(),
                            $timeEnd->toDateTimeString()
                        );
                    }
                }else{
                    $returnTaskDate = Carbon::parse($request->input('date'));
                    $timeBegin = Carbon::parse($request->input('time.begin'));
                    $timeEnd = Carbon::parse($request->input('time.end'));
                    $timeBegin->setDate($returnTaskDate->year, $returnTaskDate->month, $returnTaskDate->day);
                    $timeEnd->setDate($returnTaskDate->year, $returnTaskDate->month, $returnTaskDate->day);
                    $returnTask->setTimeWindow(
                        $timeBegin->toDateTimeString(),
                        $timeEnd->toDateTimeString()
                    );
                }
                $returnTask->setAddress(
                    $request->input('address.name'),
                    $request->input('address.country'),
                    $request->input('address.city'),
                    $request->input('address.postalCode'),
                    $request->input('address.addressLine'),
                );
                $returnTask->save();
            }
            return response()->json([
                'message'       => 'Task created successfully. ',
                'request'       =>  $request->all(),
                'task'          =>  $task,
                'pickupTask'    =>  $request->input('type') === 'pickup' ? $pickupTask : 'doesNotExist',
                'dropOff'       =>  $request->input('type') === 'dropOff' ? $package : 'doesNotExist',
            ]);
        } catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($task)
    {
        try {
            $statuses   =   Status::all();
            $task = Task::findOrFail($task);
            return view('task.show', compact('task','statuses'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task = Task::findOrFail($request->id);
        $task->date             =   $request->input('date');
        $task->order_number     =   0;
        $task->job_id           =   $request->input('jobId');
        $task->status_id        =   $request->input('status_id');
        $task->save();
        
        $taskTypeObject = null;

        if($request->input('type') === 'pickup'){

            $pickupTask = $task->pickup;
            $pickupTask->task_id = $task->id;
            $pickupTask->status_id = $task->status_id;
            $pickupTask->setTimeWindow($request->input('time.begin'),$request->input('time.end'));
            $pickupTask->setAddress(
                $request->input('address.name'),
                $request->input('address.country'),
                $request->input('address.city'),
                $request->input('address.postalCode'),
                $request->input('address.addressLine'),
            );
            $pickupTask->save();
            
            $taskTypeObject = $pickupTask;
        }
        if($request->input('type') === 'return'){
            $returnTask = $task->return;
            $returnTask->task_id = $task->id;
            $returnTask->status_id = $task->status_id;
            $returnTask->setTimeWindow($request->input('time.begin'),$request->input('time.end'));
            $returnTask->is_flexible = $request->input('returnTask.is_flexible');
            if($request->input('returnTask.is_flexible')){
                if($request->input('date') !== $request->input('returnTask.date')){
                    $returnTaskDate = Carbon::parse($request->input('returnTask.date'));
                    $timeBegin = Carbon::parse($request->input('time.begin'));
                    $timeEnd = Carbon::parse($request->input('time.end'));
                    $timeBegin->setDate($returnTaskDate->year, $returnTaskDate->month, $returnTaskDate->day);
                    $timeEnd->setDate($returnTaskDate->year, $returnTaskDate->month, $returnTaskDate->day);
                    $returnTask->setTimeWindow(
                        $timeBegin->toDateTimeString(),
                        $timeEnd->toDateTimeString()
                    );
                }
            }else{
                $returnTaskDate = Carbon::parse($request->input('date'));
                $timeBegin = Carbon::parse($request->input('time.begin'));
                $timeEnd = Carbon::parse($request->input('time.end'));
                $timeBegin->setDate($returnTaskDate->year, $returnTaskDate->month, $returnTaskDate->day);
                $timeEnd->setDate($returnTaskDate->year, $returnTaskDate->month, $returnTaskDate->day);
                $returnTask->setTimeWindow(
                    $timeBegin->toDateTimeString(),
                    $timeEnd->toDateTimeString()
                );
            }
            $returnTask->setAddress(
                $request->input('address.name'),
                $request->input('address.country'),
                $request->input('address.city'),
                $request->input('address.postalCode'),
                $request->input('address.addressLine'),
            );
            $returnTask->save();
        }
        
        if($request->input('type') === 'dropOff'){

            $package = $task->package;
            $package->task_id = $task->id;
            $package->status_id = $task->status_id;
            $package->packageType_id = $request->input('package.type');
            $package->weight = $request->input('package.weight');
            $package->dimensions = 0;
            $package->quantity = $request->input('package.quantity');
            $package->setTimeWindow($request->input('time.begin'),$request->input('time.end'));
            $package->setAddress(
                $request->input('address.name'),
                $request->input('address.country'),
                $request->input('address.city'),
                $request->input('address.postalCode'),
                $request->input('address.addressLine'),
            );
            $package->save();

            $taskTypeObject = $package;
        }
        
        return response()->json([
            //'message'   => 'Task updated successfully. ',
            'task'      =>  $task,
            'taskTypeObject'    =>   $taskTypeObject,
            'requestData' =>  $request->all(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try{
        $task = Task::findOrFail($request->id);
        if ($task->typeOfTask()) {
            $task->typeOfTask()->delete();
        }
        $task->delete();
        $task->job->save();

        return response()->json([
            'message'   => 'Task deleted successfully. ',
        ]);
        } catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),], 500);
        }   
    }
    public function getTaskInfo($taskId)
    {
        // Fetch the client's information based on the $clientId
        $task = Task::find($taskId);
        if ($task) {
            return response()->json([
                'id'                =>  $task->id,
                'date'              =>  $task->job->date,
                'time'          =>  [
                                            'begin' =>  Carbon::parse($task->timeWindowBegin()),
                                            'end'   =>  Carbon::parse($task->timeWindowEnd()),    
                ],
                'statusId'          =>  $task->status->id,
                'address'           =>  [
                                            'name'          =>  $task->nameOfAddress(),
                                            'country'       =>  $task->country(),
                                            'city'          =>  $task->city(),
                                            'postalCode'    =>  $task->postalCode(),
                                            'addressLine'   =>  $task->addressLine(),
                ],
                'type'              =>  $task->type(),
                'package'           =>  isset($task->package) 
                                            ?[
                                                'type'          =>  $task->package->packageType,
                                                'quantity'      =>  $task->package->quantity,
                                                'weight'        =>  $task->package->weight,
                                                'dimensions'    =>  $task->package->dimensions,
                                            ]
                                            :'none',
                'returnTask'        =>  isset($task->return)
                                            ?[
                                                'time'          =>  [
                                                                        'begin' =>  Carbon::parse($task->return->timeWindowBegin()),
                                                                        'end'   =>  Carbon::parse($task->return->timeWindowEnd()),    
                                                ],
                                                'address'       =>  [
                                                                        'name'          =>  $task->return->nameOfAddress(),
                                                                        'country'       =>  $task->return->country(),
                                                                        'city'          =>  $task->return->city(),
                                                                        'postalCode'    =>  $task->return->postalCode(),
                                                                        'addressLine'   =>  $task->return->addressLine(),
                                                ],
                                                'is_flexible'   =>  $task->return->is_flexible,
                                            ]
                                            :'none',
                ]);
        }

        return response()->json(['error' => 'Job task not found'], 404);
    }
}
