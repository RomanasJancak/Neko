<?php

namespace App\Http\Controllers;

use App\Models\JobTemplate;
use App\Models\Job;
use App\Models\Client;
use Illuminate\Http\Request;

/**
 * JobTemplateController
 * 
 * Handles CRUD operations for JobTemplates.
 * Supports creating batch jobs from templates.
 * 
 * COMMENTED OLD CODE AT BOTTOM - USE AS REFERENCE ONLY
 */
class JobTemplateController extends Controller
{
    /**
     * Display paginated list of templates
     */
    public function index()
    {
        $templates = JobTemplate::paginate(15);
        return view('jobtemplate.index', compact('templates'));
    }

    /**
     * Store a newly created template (placeholder)
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'client_id' => 'required|exists:clients,id',
            ]);

            $template = JobTemplate::create($request->only([
                'name', 'client_id'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Template created successfully',
                'template' => $template
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get paginated templates (AJAX)
     */
    public function fetchTemplatesPaginate(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $sortField = $request->get('sortField', 'id');
            $sortOrder = $request->get('sortOrder', 'asc');

            $query = JobTemplate::query();

            // Search by ID or Name
            if (!empty($search)) {
                $query->where('id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            }

            // Sort
            $query->orderBy($sortField, $sortOrder);

            $templates = $query->paginate(15);

            return response()->json([
                'success' => true,
                'templates' => $templates->items(),
                'pagination' => [
                    'total' => $templates->total(),
                    'per_page' => $templates->perPage(),
                    'current_page' => $templates->currentPage(),
                    'last_page' => $templates->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed template info (for modal)
     */
    public function getTemplateInfo($id)
    {
        try {
            $template = JobTemplate::findOrFail($id);
            $lockedFields = $template->lockedFields();

            return response()->json([
                'success' => true,
                'template' => $template,
                'lockedFields' => $lockedFields,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create template from existing job
     */
    public function createFromJob(Request $request)
    {
        try {
            $request->validate([
                'job_id' => 'required|exists:jobs,id',
                'name' => 'required|string|max:255',
            ]);

            $job = Job::findOrFail($request->job_id);

            // Create new template from job data
            $template = JobTemplate::create([
                'name' => $request->name,
                'client_id' => $job->clientToBill_id,
                'pickup_address_id' => $job->pickup_address_id ?? null,
                'pickup_time_begin' => $job->pickup_time_begin,
                'pickup_time_end' => $job->pickup_time_end,
                'template_data' => json_encode([
                    'pickup' => $job->pickup_address_id ? [
                        'address_id' => $job->pickup_address_id,
                        'time_begin' => $job->pickup_time_begin,
                        'time_end' => $job->pickup_time_end,
                    ] : null,
                    'dropoffs' => $job->getDropOffs(),
                    'return' => $job->getReturnTask(),
                ])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template created successfully',
                'template' => $template
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create batch jobs from template
     */
    public function createJobsBatch(Request $request)
    {
        try {
            $request->validate([
                'template_id' => 'required|exists:job_templates,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'days' => 'required|array|min:1', // e.g., ['Monday', 'Wednesday', 'Friday']
            ]);

            $template = JobTemplate::findOrFail($request->template_id);
            $startDate = new \DateTime($request->start_date);
            $endDate = new \DateTime($request->end_date);
            $selectedDays = $request->days;

            $createdJobs = [];

            // Iterate through date range
            $currentDate = $startDate;
            while ($currentDate <= $endDate) {
                $dayName = $currentDate->format('l'); // e.g., 'Monday'

                // Check if this day is selected
                if (in_array($dayName, $selectedDays)) {
                    $job = $this->createJobFromTemplate($template, $currentDate->format('Y-m-d'));
                    $createdJobs[] = $job;
                }

                $currentDate->modify('+1 day');
            }

            return response()->json([
                'success' => true,
                'message' => count($createdJobs) . ' jobs created',
                'jobs' => $createdJobs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Create single job from template
     */
    private function createJobFromTemplate(JobTemplate $template, $date)
    {
        $templateData = $template->template_data;

        $job = Job::create([
            'clientToBill_id' => $template->client_id,
            'job_template_id' => $template->id,
            'date' => $date,
            'status_id' => 1, // Default status
            'manager_id' => auth()->id(),
            'fixed_price' => 1, // Mark as fixed price
            // Add other fields from template_data as needed
        ]);

        return $job;
    }

    /**
     * Update template
     */
    public function update(Request $request, JobTemplate $template)
    {
        try {
            $request->validate([
                'name' => 'string|max:255',
                'client_id' => 'exists:clients,id',
                'pickup_address_id' => 'exists:addresses,id',
                'pickup_time_begin' => 'date',
                'pickup_time_end' => 'date|after_or_equal:pickup_time_begin',
            ]);

            $template->update($request->only(['name', 'client_id', 'pickup_address_id', 'pickup_time_begin', 'pickup_time_end']));

            return response()->json([
                'success' => true,
                'message' => 'Template updated successfully',
                'template' => $template
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete template
     */
    public function destroy(JobTemplate $template)
    {
        try {
            $template->delete();

            return response()->json([
                'success' => true,
                'message' => 'Template deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set field lock status
     */
    public function setFieldLock(Request $request, JobTemplate $template)
    {
        try {
            $request->validate([
                'field_name' => 'required|string',
                'is_locked' => 'required|boolean',
            ]);

            $template->setLockedField($request->field_name, $request->is_locked);

            return response()->json([
                'success' => true,
                'message' => 'Field lock status updated'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==============================================
    // COMMENTED OLD CODE - USE AS REFERENCE ONLY
    // ==============================================
    /*
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        if (is_null($request->clientToBill_id)) {
            $request->merge(['clientToBill_id' => 1]);
        }
        
      $template = JobTemplate::findOrFail(1)->replicate();
      $template->name = $request->name;
      $template->save();

        return response()->json([
            'template' => $template,
        ]);
    }

    public function show(JobTemplate $jobTemplate)
    {
        //
    }

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
                return response()->json(['error' => 'No address found'], 400);
            }
            $dropOffs = json_decode($jobTemplate->dropOffs_data, true) ?? [];
            $newOrderNumber = count($dropOffs) + 1;
            $newDropOff = [
                'order_number' => $newOrderNumber,
                'package' => [
                    'id' => null,
                    'package_type' => [
                        'id' => $packageType->id,
                        'name' => $packageType->name,
                    ],
                    'dropoff_adress_line' => $defaultAddress->address_line,
                    'dropoff_postal_code' => $defaultAddress->postal_code,
                    'dropoff_name' => $defaultAddress->address_name,
                ]
            ];
            $dropOffs[] = $newDropOff;
            $jobTemplate->dropOffs_data = json_encode($dropOffs);
            $jobTemplate->save();
            return response()->json([
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
            }
            $jobTemplate->dropOffs_data = json_encode($updatedDropOffs);
            $jobTemplate->save();
            return response()->json([
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
                return response()->json(['error' => 'No address found'], 400);
            }
            $returnTask = [
                'address' => [
                    'address_line' => $defaultAddress->address_line,
                    'postal_code' => $defaultAddress->postal_code,
                    'name' => $defaultAddress->address_name,
                ]
            ];
            $jobTemplate->return_data = json_encode($returnTask);
            $jobTemplate->save();
            return response()->json([
                'newReturn' => $returnTask,
            ]);
        }catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            ]);
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
            ]);
        }catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            ]);
        }
    }

    public function getJobTemplateInfo($id)
    {
        try{
            $template = JobTemplate::find($id);
            return response()->json([
                'template' => $template,
            ]);
        }catch (\Exception $e){
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            ]);
        }
    }

    protected function prepareDropOffTasks($item, $dropOffsData)
    {
        foreach ($dropOffsData as $index => &$dropOff) {
            $dropOff['order_number'] = $index + 1;
        }

        return $dropOffsData;
    }

    public function fetchJobTemplatesPaginate(Request $request)
    {
        try{
            $search = $request->get('search', '');
            $sortField = $request->get('sortField', 'id');
            $sortOrder = $request->get('sortOrder', 'asc');

            $query = JobTemplate::query();

            if (!empty($search)) {
                $query->where('id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            }

            $query->orderBy($sortField, $sortOrder);
            $templates = $query->paginate(10);

            return response()->json([
                'success' => true,
                'templates' => $templates->items(),
                'pagination' => [
                    'total' => $templates->total(),
                    'per_page' => $templates->perPage(),
                    'current_page' => $templates->currentPage(),
                    'last_page' => $templates->lastPage(),
                ]
            ]);
        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            ], 500);
        }
    }
    */
}
