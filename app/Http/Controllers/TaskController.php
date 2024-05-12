<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

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
        //
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
        //$task->order_number = 99;
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
    public function destroy(Task $task)
    {
        //
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
                ]);
        }

        return response()->json(['error' => 'Job not found'], 404);
    }
}
