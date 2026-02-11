<?php

namespace App\Http\Controllers;

use App\Models\JobTemplate;
use App\Models\Job;
use App\Models\Client;
use App\Models\Address;
use App\Models\PostalCode;
use App\Models\PackageType;
use App\Models\Task;
use App\Models\Pickuptask;
use App\Models\Package;
use App\Models\ReturnTask;
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
            $template->template_data = json_encode([
                'pickup' => ['time_begin' => '09:00', 'time_end' => '17:00', 'address_id' => null],
                'dropoffs' => [],
                'return' => null,
                'note' => '',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template created successfully',
                'template' => $template
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
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
                'templates' => collect($templates->items())->map(function($template) {
                    return [
                        'id' => $template->id,
                        'created_at' => $template->created_at->toDateTimeString(),
                        'name' => $template->name,
                        'client_id' => $template->client_id,
                        // 'pickup' => ($jsonData_pickup = json_decode($template->template_data, true)['pickup'] ?? null) ? [true] : [false],
                        'pickup' => ($jsonData_pickup = json_decode($template->template_data, true)['pickup'] ?? null) 
                          ? [
                              'time_begin' => $jsonData_pickup['time_begin'] ?? null,
                              'time_end' => $jsonData_pickup['time_end'] ?? null,
                              'address' => ($address = Address::find($jsonData_pickup['address_id'] ?? null))
                                  ? [
                                      'id' => $address->id,
                                      'name' => $address->name,
                                      'address_line_1' => $address->address_line_1,
                                      'address_line_2' => $address->address_line_2,
                                      'postal_code' => PostalCode::find($address->postal_code_id)->postal_code,
                                      'city' => $address->city,
                                      'country' => $address->country
                                  ]
                                  : null,
                          ]
                          : [],
                        'dropoffs' => ($dropoffs = $jsonData['dropoffs'] ?? []) ? collect($dropoffs)->map(function($dropoff) {
                            return [
                                'order_number' => $dropoff['order_number'] ?? null,
                                'address' => ($address = Address::find($dropoff['address_id'] ?? null))
                                    ? [
                                        'id' => $address->id,
                                        'name' => $address->name,
                                        'address_line_1' => $address->address_line_1,
                                        'address_line_2' => $address->address_line_2,
                                        'postal_code' => PostalCode::find($address->postal_code_id)->postal_code,
                                        'city' => $address->city,
                                        'country' => $address->country
                                    ]
                                    : null,
                                'package_type' => ($packageType = PackageType::find($dropoff['package_type_id'] ?? null))
                                    ? [
                                        'id' => $packageType->id,
                                        'name' => $packageType->name,
                                    ]
                                    : null,
                                'time_begin' => $dropoff['time_begin'] ?? null,
                                'time_end' => $dropoff['time_end'] ?? null,
                                'quantity' => $dropoff['quantity'] ?? null,
                                'note' => $dropoff['note'] ?? '',
                            ];
                        })->toArray() : [],
                        'return' => ($return = $jsonData['return'] ?? null) ? [
                            'address' => ($address = Address::find($return['address_id'] ?? null))
                                ? [
                                    'id' => $address->id,
                                    'name' => $address->name,
                                    'address_line_1' => $address->address_line_1,
                                    'address_line_2' => $address->address_line_2,
                                    'postal_code' => PostalCode::find($address->postal_code_id)->postal_code,
                                    'city' => $address->city,
                                    'country' => $address->country
                                ]
                                : null,
                        ] : null,
                        'note' => $jsonData['note'] ?? '',
                    ];
                })->toArray(),
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
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
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
                //'dropOffData' => json_decode($template->template_data, true)['dropoffs'],
                'template' => [
                        'id' => $template->id,
                        'created_at' => $template->created_at->toDateTimeString(),
                        'name' => $template->name,
                        'client_id' => $template->client_id,
                        'pickup' => ($jsonData_pickup = json_decode($template->template_data, true)['pickup'] ?? null)
                          ? [
                              'time_begin' => $jsonData_pickup['time_begin'] ?? null,
                              'time_end' => $jsonData_pickup['time_end'] ?? null,
                              'address' => ($address = Address::find($jsonData_pickup['address_id'] ?? null))
                                  ? [
                                      'id' => $address->id,
                                      'name' => $address->name,
                                      'address_line_1' => $address->address_line_1,
                                      'address_line_2' => $address->address_line_2,
                                      'postal_code' => PostalCode::find($address->postal_code_id)->postal_code,
                                      'city' => $address->city,
                                      'country' => $address->country
                                  ]
                                  : null,
                          ]
                          : [],
                        'dropoffs' => ($dropoffs = json_decode($template->template_data, true)['dropoffs'] ?? []) ? collect($dropoffs)->map(function($dropoff) {
                            return [
                                'order_number' => $dropoff['order_number'] ?? null,
                                'address' => ($address = Address::find($dropoff['address_id'] ?? null))
                                    ? [
                                        'id' => $address->id,
                                        'name' => $address->name,
                                        'address_line_1' => $address->address_line_1,
                                        'address_line_2' => $address->address_line_2,
                                        'postal_code' => PostalCode::find($address->postal_code_id)->postal_code,
                                        'city' => $address->city,
                                        'country' => $address->country
                                    ]
                                    : null,
                                'package_type' => ($packageType = PackageType::find($dropoff['package_type_id'] ?? null))
                                    ? [
                                        'id' => $packageType->id,
                                        'name' => $packageType->name,
                                    ]
                                    : null,
                                'time_begin' => $dropoff['time_begin'] ?? null,
                                'time_end' => $dropoff['time_end'] ?? null,
                                'quantity' => $dropoff['quantity'] ?? null,
                                'note' => $dropoff['note'] ?? '',
                            ];
                        })->toArray() : [],
                        'return' => ($return = $jsonData['return'] ?? null) ? [
                            'address' => ($address = Address::find($return['address_id'] ?? null))
                                ? [
                                    'id' => $address->id,
                                    'name' => $address->name,
                                    'address_line_1' => $address->address_line_1,
                                    'address_line_2' => $address->address_line_2,
                                    'postal_code' => PostalCode::find($address->postal_code_id)->postal_code,
                                    'city' => $address->city,
                                    'country' => $address->country
                                ]
                                : null,
                            'is_same_day' => $return['is_same_day'] ?? true,
                            'time_begin' => $return['time_begin'] ?? null,
                            'note' => $return['note'] ?? '',
                        ] : null,
                        'note' => $jsonData['note'] ?? '',
                        'is_price_fixed' => $jsonData['is_price_fixed'] ?? false,
                        'price' => $jsonData['price'] ?? 0,
                    ],
                'lockedFields' => $lockedFields,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
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
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
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
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    /**
     * Helper: Create single job from template
     */
    private function createJobFromTemplate(JobTemplate $template, $date)
    {
        \Log::info('Creating job from template ID: '.$template->id.' for date: '.$date);
        $templateData = json_decode($template->template_data, true);
        $job                    =   new Job();
        $job->eilesNumeris      =   0;
        $job->manager_id        =   auth()->user()->id;
        $job->status_id         =   10;
        $job->courrier_id       =   null;
        $job->clientToBill_id   =   $template->client_id;
        $job->date              =   $date;
        $job->job_template_id   =   $template->id;
        $job->fixed_price       =   $templateData['is_price_fixed'] ? ($templateData['price'] ?? false) : false;
        $job->save();
        
        // Create note from template data

        //dd($templateData,$templateData['pickup']);
        if (isset($templateData['note']) && !empty($templateData['note'])) {
            $job->notes()->create([
                'content' => $templateData['note'],
                'user_id' => auth()->id(),
            ]);
        }
        $job->save();
        \Log::info('Job created with ID: '.$job->id);
        
        // Copy locked fields from template to job
        foreach($template->lockedFields() as $field){
            if($field->is_locked){
                $job->changeLockedField($field->field_name, true);
            }
        }
        
        // Create pickup task from template_data
        if(isset($templateData['pickup']) && $templateData['pickup'] != null){
            $task                   =   new Task();
            $task->date             =   $date;
            $task->order_number     =   0;
            $task->job_id           =   $job->id;
            $task->status_id        =   10;
            $task->save();
            
            $pickuptaskData = $templateData['pickup'];
            $pickuptask                 =   new Pickuptask();
            $pickuptask->task_id        =   $task->id;
            $pickuptask->status_id      =   10;
            $address = Address::find($pickuptaskData['address_id'] ?? null);
            $pickuptask->pickupclientname       =   $address->name ?? null;
            $pickuptask->pickupclientaddressline    =   $address->address_line_1 ?? null;
            $pickuptask->pickupclientpostalcode    =   PostalCode::find($address->postal_code_id)->postal_code ?? null;
            $pickuptask->pickupclientcity           =   $address->city ?? null;
            $pickuptask->pickupclientcountry        =   $address->country ?? null; 
            // Handle pickup_time_begin as time or datetime
            $pickupTimeBegin = $pickuptaskData['pickup_time_begin'] ?? '09:00:00';
            if (strlen($pickupTimeBegin) <= 8) {
              // Just time, prepend date
              $pickuptask->pickup_time_begin = $date . ' ' . $pickupTimeBegin;
            } else {
              // Full datetime, replace date part
              $pickuptask->pickup_time_begin = $date . ' ' . preg_replace('/^\d{4}-\d{2}-\d{2} /', '', $pickupTimeBegin);
            }
            $pickuptask->pickup_time_end       =   $pickuptaskData['pickup_time_end'] ?? '17:00:00';
            if (strlen($pickuptask->pickup_time_end) <= 8) {
              // Just time, prepend date
              $pickuptask->pickup_time_end = $date . ' ' . $pickuptask->pickup_time_end;
            } else {
              // Full datetime, replace date part
              $pickuptask->pickup_time_end = $date . ' ' . preg_replace('/^\d{4}-\d{2}-\d{2} /', '', $pickuptask->pickup_time_end);
            }
            $pickuptask->note          =   $pickuptaskData['note'] ?? null;
            $pickuptask->save();
        }
        
        if($template->isLocked('pickup')){
            $job->changeLockedField('pickup', true);
        }
        
        // Create dropoff tasks from template_data
        $dropoffData = $templateData['dropoffs'] ?? [];
        \Log::info('Processing '.count($dropoffData).' drop-off(s) from template ID: '.$template->id);
        foreach ($dropoffData as $key => $dropoffDataItem) {
            \Log::info('Creating drop-off '.($key+1).' for job ID: '.$job->id);
            \Log::debug('Drop-off data: ', $dropoffDataItem);

            $task                               =   new Task();
            $task->date         =   $date;
            $task->order_number =   $dropoffDataItem['order_number'] ?? $key + 1;
            $task->job_id       =   $job->id;
            $task->status_id       =   10;
            $task->save();
            \Log::info('Created drop-off task ID: '.$task->id.' for job ID: '.$job->id);
            
            $package                            =   new Package();
            $packageType                        =   PackageType::find($dropoffDataItem['package_type_id'] ?? 1);
            $package->job_id                    =   $job->id;
            $package->task_id                   =   $task->id;
            $package->packageType_id            =   $packageType->id; 
            $package->orderNumber               =   $dropoffDataItem['order_number'] ?? $key + 1; 
            $package->weight                    =   $dropoffDataItem['weight'] ?? 0; 
            $package->dimensions                =   $dropoffDataItem['dimensions'] ?? '0x0x0';
            $package->quantity                  =   $dropoffDataItem['quantity'] ?? 1;
            $address                      =   Address::find($dropoffDataItem['address_id'] ?? null);
            $package->dropoff_name              =   $address->name ?? null;
            $package->dropoff_country           =   $address->country ?? null;
            $package->dropoff_city              =   $address->city ?? null;
            $package->dropoff_postal_code       =   PostalCode::find($address->postal_code_id)->postal_code ?? null;
            $package->dropoff_adress_line       =   $address->address_line_1 ?? null;
            $package->packagedropofftimebegin   =   $dropoffDataItem['time_begin'] ?? null;
            if(strlen($package->packagedropofftimebegin) <= 8) {
                // Just time, prepend date
                $package->packagedropofftimebegin = $date . ' ' . $package->packagedropofftimebegin;
            } else {
                // Full datetime, replace date part
                $package->packagedropofftimebegin = $date . ' ' . preg_replace('/^\d{4}-\d{2}-\d{2} /', '', $package->packagedropofftimebegin);
            }
            $package->packagedropofftimeend     =   $dropoffDataItem['time_end'] ?? null;
            if(strlen($package->packagedropofftimeend) <= 8) {
                // Just time, prepend date
                $package->packagedropofftimeend = $date . ' ' . $package->packagedropofftimeend;
            } else {
                // Full datetime, replace date part
                $package->packagedropofftimeend = $date . ' ' . preg_replace('/^\d{4}-\d{2}-\d{2} /', '', $package->packagedropofftimeend);
            }
            $package->name                      =   $packageType->name ?? null;
            $package->price                     =   $packageType->price ?? null;
            $package->baseQuantityThreshold     =   $packageType['baseQuantityThreshold'] ?? null;
            $package->maxQuantityThreshold      =   $packageType['maxQuantityThreshold'] ?? null;
            $package->save();
            \Log::info('Created drop-off package ID: '.$package->id.' for job ID: '.$job->id);
        }
        
        // Create return task from template_data
        $returnData = $templateData['return'] ?? null;
        if(isset($returnData) && !empty($returnData)){
            $task               =   new Task();
            $task->date         =   $date;
            $task->order_number =   count($dropoffData) + 1;
            $task->job_id       =   $job->id;
            $task->status_id       =   10;
            $task->save();
            
            $returntask         =   new Returntask();
            $returntask->task_id        =  $task->id;
            $returntask->status_id      =   10;
            
            // Handle time_begin - could be just time or datetime
            $timeBegin = $returnData['time_begin'] ?? '16:00:00';
            if (strlen($timeBegin) <= 8) {
                // Just time, prepend date
                $returntask->time_begin = $date . ' ' . $timeBegin;
            } else {
                // Full datetime, replace date part
                $returntask->time_begin = $date . ' ' . preg_replace('/^\d{4}-\d{2}-\d{2} /', '', $timeBegin);
            }
            
            // Handle time_end similarly
            $timeEnd = $returnData['time_end'] ?? '17:00:00';
            if (strlen($timeEnd) <= 8) {
                $returntask->time_end = $date . ' ' . $timeEnd;
            } else {
                $returntask->time_end = $date . ' ' . preg_replace('/^\d{4}-\d{2}-\d{2} /', '', $timeEnd);
            }
            $address = Address::find($returnData['address_id'] ?? null);
            $returntask->name           =   $address->name ?? null;
            $returntask->adress_line       =   $address->address_line_1 ?? null;
            $returntask->city       =   $address->city ?? null;
            $returntask->country       =   $address->country ?? null;
            $returntask->postal_code       =   PostalCode::find($address->postal_code_id)->postal_code ?? null;
            $returntask->notes       =   $returnData['note'] ?? null;
            $returntask->is_flexible       =   !($returnData['is_same_day'] ?? true);
            $returntask->save();
        }
        
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
                'pickup_time_begin' => 'date_format:H:i',
                'pickup_time_end' => 'date_format:H:i|after_or_equal:pickup_time_begin',
            ]);

            // Decode JSON data from database
            $jsonData = json_decode($template->template_data, true) ?? [];
            
            // Access nested data
            $pickupData = $jsonData['pickup'] ?? [];
            $dropOffsData = $jsonData['dropoffs'] ?? [];
            $returnData = $jsonData['return'] ?? [];
            
            // Update pickup data
            $pickupData['address_id'] = $request->pickup_address_id ?? $pickupData['address_id'] ?? null;
            $pickupData['time_begin'] = $request->pickup_time_begin ?? $pickupData['time_begin'] ?? null;
            $pickupData['time_end'] = $request->pickup_time_end ?? $pickupData['time_end'] ?? null;
            
            // Update dropoffs if provided
            if ($request->has('dropoffs')) {
                $dropOffsData = $request->input('dropoffs', []);
            }
            
            // Update return if provided
            if ($request->has('return_address_id')) {
                $returnData['address_id'] = $request->return_address_id ?? null;
            }
            if ($request->has('return_is_same_day')) {
                $returnData['is_same_day'] = $request->return_is_same_day === 'true' || $request->return_is_same_day === true;
            }
            if ($request->has('return_time')) {
                $returnData['time_begin'] = $request->return_time ?? null;
            }
            if ($request->has('return_datetime')) {
                $returnData['time_begin'] = $request->return_datetime ?? null;
            }
            if ($request->has('return_note')) {
                $returnData['note'] = $request->return_note ?? '';
            }
            
            // Update pricing if provided
            if ($request->has('is_price_fixed')) {
                $jsonData['is_price_fixed'] = $request->is_price_fixed === 'true' || $request->is_price_fixed === true;
            }
            if ($request->has('price')) {
                $jsonData['price'] = (float) $request->price ?? 0;
            }
            
            // Rebuild JSON structure
            $jsonData['pickup'] = $pickupData;
            $jsonData['dropoffs'] = $dropOffsData;
            $jsonData['return'] = $returnData;

            $template->update(
                array_merge(
                    $request->only(['name', 'client_id']),
                    ['template_data' => json_encode($jsonData)]
                )
            );

            return response()->json([
                'success' => true,
                'message' => 'Template updated successfully',
                'template' => $template
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
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
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
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
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    /**
     * Add empty dropoff to template
     */
    public function addDropOff(Request $request, JobTemplate $template)
    {
        try {
            // Decode template data
            $jsonData = json_decode($template->template_data, true) ?? [];
            $dropOffs = $jsonData['dropoffs'] ?? [];
            
            // Get client's default address
            $client = Client::find($template->client_id);
            $defaultAddress = $client->getAllAddresses()->first();
            
            if (!$defaultAddress) {
                return response()->json(['error' => 'No address found for client'], 400);
            }
            
            // Get default package type
            $packageType = $client->packageTypes()->first();
            
            $newOrderNumber = count($dropOffs) + 1;
            $newDropOff = [
                'order_number' => $newOrderNumber,
                'address_id' => $defaultAddress->id,
                'package_type_id' => $packageType ? $packageType->id : null,
                'time_begin' => '07:00',
                'time_end' => '18:00',
                'quantity' => 1,
                'note' => '',
            ];
            
            $dropOffs[] = $newDropOff;
            $jsonData['dropoffs'] = $dropOffs;
            
            $template->template_data = json_encode($jsonData);
            $template->save();
            
            return response()->json([
                'success' => true,
                'dropoff' => $newDropOff,
                'message' => 'Dropoff added successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Remove dropoff from template
     */
    public function removeDropOff(Request $request, JobTemplate $template)
    {
        try {
            $request->validate([
                'order_number' => 'required|integer',
            ]);
            
            // Decode template data
            $jsonData = json_decode($template->template_data, true) ?? [];
            $dropOffs = $jsonData['dropoffs'] ?? [];
            
            // Filter out the dropoff with matching order_number
            $updatedDropOffs = array_filter($dropOffs, function($dropOff) use ($request) {
                return $dropOff['order_number'] != $request->order_number;
            });
            
            // Reindex and update order numbers
            $updatedDropOffs = array_values($updatedDropOffs);
            foreach ($updatedDropOffs as $index => &$dropOff) {
                $dropOff['order_number'] = $index + 1;
            }
            
            $jsonData['dropoffs'] = $updatedDropOffs;
            $template->template_data = json_encode($jsonData);
            $template->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Dropoff removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Add return to template
     */
    public function addReturn(Request $request, JobTemplate $template)
    {
        try {
            // Decode template data
            $jsonData = json_decode($template->template_data, true) ?? [];
            
            // Get client's first address as default
            $client = Client::find($template->client_id);
            $defaultAddress = $client->getAllAddresses()->first();
            
            // Create return structure
            $returnData = [
                'address_id' => $defaultAddress ? $defaultAddress->id : null,
                'is_same_day' => true,
                'time_begin' => '16:00',
                'note' => '',
            ];
            //dd($jsonData, $returnData);
            $jsonData['return'] = $returnData;
            $template->template_data = json_encode($jsonData);
            //dd($template->template_data, $jsonData,json_encode($jsonData));
            $template->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Return added successfully',
                'return' => $returnData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
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
