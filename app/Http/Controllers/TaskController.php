<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Pickuptask;
use App\Models\ReturnTask;
use App\Models\Package;
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
                $package->weight = 0;
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
            return response()->json([
                'message'       => 'Task created successfully. ',
                'request'       =>  $request->all(),
                'task'          =>  $task,
                'pickupTask'    =>  $request->input('type') === 'pickup' ? $pickupTask : 'doesNotExist',
                'dropOff'       =>  $request->input('type') === 'dropOff' ? $package : 'doesNotExist',
            ]);
        } catch (\Exception $e){
                return response()->json([
                'location' =>  'TaskController : Store',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
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
        if($request->input('type') === 'dropOff'){

            $package = $task->package;
            $package->task_id = $task->id;
            $package->status_id = $task->status_id;
            $package->packageType_id = $request->input('package.type');
            $package->weight = 0;
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

        if($request->input('courrier_id')){
            
        }
        if($request->input('courrier_id')){
            if($request->input('courrier_id') === 'none'){
                $task->job->courrier_id = null;
            }else{
                $task->job->courrier_id = $request->input('courrier_id');
            }
        }
        if($request->input('status_id')){
            $status = $request->input('status_id');
                $task->job->status_id = $status;
                $task->status_id = $status;

        }
        $debug = false;
        if($request->input('order_number')){
            $task->order_number = $request->input('order_number');
            $debug = true;
        }
        if($request->input('address')){
            $task->typeOfTask->setAddress(
                $request->input('address.name'),
                $request->input('address.country'),
                $request->input('address.city'),
                $request->input('address.postalCode'),
                $request->input('address.addressLine'),
            );
        }
        if($request->input('time')){
            $task->typeOfTask->setTimeWindow($request->input('time.begin'),$request->input('time.end'));
        }
        if($request->input('package')){
            $task->package->changeTypeWithId($request->input('package.type'));
            $task->package->setQuantity($request->input('package.quantity'));
            $task->package->save();
        }
        $task->typeOfTask->save();
        $task->save();
        $task->job->save();

        return response()->json([
            'message'   => 'Task updated successfully. ',
            'task'      =>  $task,
            'debug'     =>  $debug,
            'request->order_number' =>  $request->input('order_number'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try{
        $task = Task::findOrFail($request->id);
        $task->typeOfTask->delete();
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
                'time'          =>  [
                                            'begin' =>  Carbon::parse($task->timeWindowBegin())->format('H:i:s'),
                                            'end'   =>  Carbon::parse($task->timeWindowEnd())->format('H:i:s'),    
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
                                                'type'      =>  $task->package->packageType,
                                                'quantity'  =>  $task->package->quantity,
                                            ]
                                            :'none',    
                ]);
        }

        return response()->json(['error' => 'Job not found'], 404);
    }
}
