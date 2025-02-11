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
        //
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
}
