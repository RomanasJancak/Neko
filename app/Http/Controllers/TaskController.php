<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

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
        $status = Task::findOrFail($request->statusid);
        $status->name = $request->name;
        $status->color_main =   $request->input('color-main');
        $status->color_pickup =   $request->input('color-pickup');
        $status->color_dropoff =   $request->input('color-dropoff');
        $status->color_return =   $request->input('color-return');
        $status->color_custom =   $request->input('color-custom');
        $status->save();

        return response()->json([
            'message' => 'Status updated successfully. '.$status,
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
                'id'                => $task->id,
                ]);
        }

        return response()->json(['error' => 'Job not found'], 404);
    }
}
