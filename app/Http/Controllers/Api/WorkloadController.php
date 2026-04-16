<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Workload;
use App\Models\Day;
use App\Models\Bike;
use App\Http\Requests\StoreWorkloadRequest;
use App\Http\Requests\UpdateWorkloadRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\BikeAssignmentService;

/**
 * @OA\Tag(name="Workloads", description="API Endpoints for Workload Management")
 */
class WorkloadController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/workloads",
     * summary="List all workloads",
     * tags={"Workloads"},
     * security={{"sanctum_auth":{}}},
     * @OA\Response(response=200, description="Success")
     * )
     */
    public function index()
    {
        $workloads = Workload::with(['user', 'bike', 'day'])->latest()->paginate(10);
        return response()->json([
            'success' => true,
            'data' => $workloads
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/workloads",
     * summary="Create a workload",
     * tags={"Workloads"},
     * security={{"sanctum_auth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"user_id", "bike_id", "year", "month", "day"},
     * @OA\Property(property="user_id", type="integer"),
     * @OA\Property(property="bike_id", type="integer"),
     * @OA\Property(property="capacity", type="string", example="100%"),
     * @OA\Property(property="year", type="integer"),
     * @OA\Property(property="month", type="integer"),
     * @OA\Property(property="day", type="integer")
     * )
     * ),
     * @OA\Response(response=201, description="Created")
     * )
     */
    public function store(StoreWorkloadRequest $request)
    {
        $dateString = "{$request->year}-{$request->month}-{$request->day}";
        
        // Find or create the Day record
        $day = Day::firstOrCreate(
            ['date' => $dateString],
            ['name' => $dateString]
        );

        $workload = new Workload();
        $workload->capacity = $request->capacity ?? "100%";
        $workload->user_id  = $request->user_id; // Standardized from $request->user
        $workload->bike_id  = $request->bike_id; // Standardized from $request->bike
        $workload->day_id   = $day->id;
        $workload->save();

        return response()->json([
            'success' => true,
            'message' => 'Workload created successfully',
            'data' => $workload->load(['user', 'bike', 'day'])
        ], 201);
    }

    /**
     * @OA\Get(
     * path="/api/workloads/{workload}",
     * tags={"Workloads"},
     * security={{"sanctum_auth":{}}},
     * @OA\Parameter(name="workload", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Success")
     * )
     */
    public function show(Workload $workload)
    {
        return response()->json([
            'success' => true,
            'data' => $workload->load([
          'user:id,name,email',
          'bike:id,name',
          'day:id,date,name'
            ])
        ]);
    }

    /**
     * @OA\Put(
     * path="/api/workloads/{workload}",
     * tags={"Workloads"},
     * security={{"sanctum_auth":{}}},
     * @OA\Parameter(name="workload", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(UpdateWorkloadRequest $request, Workload $workload)
    {
        $workload->capacity = $request->capacity ?? $workload->capacity;
        $workload->user_id  = $request->user_id ?? $workload->user_id;
        $workload->bike_id  = $request->bike_id ?? $workload->bike_id;
        $workload->save();

        return response()->json([
            'success' => true,
            'message' => 'Workload updated',
            'data' => $workload->load(['user', 'bike', 'day'])
        ]);
    }

    /**
     * @OA\Delete(
     * path="/api/workloads/{workload}",
     * tags={"Workloads"},
     * security={{"sanctum_auth":{}}},
     * @OA\Parameter(name="workload", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Deleted")
     * )
     */
    public function destroy(Workload $workload)
    {
        $workload->delete();
        return response()->json([
            'success' => true,
            'message' => 'Workload deleted'
        ]);
    }

    /**
     * @OA\Get(
     * path="/api/workloads/calendar",
     * summary="Get workload calendar data",
     * tags={"Workloads"},
     * security={{"sanctum_auth":{}}},
     * @OA\Parameter(name="year", in="query", @OA\Schema(type="integer")),
     * @OA\Parameter(name="month", in="query", @OA\Schema(type="integer")),
     * @OA\Parameter(name="view", in="query", @OA\Schema(type="string", enum={"monthly", "weekly"})),
     * @OA\Response(response=200, description="Success")
     * )
     */
    public function calendar(Request $request)
    {
        $currentYear = $request->year ?? Carbon::now()->year;
        $currentMonth = $request->month ?? Carbon::now()->month;
        $view = $request->view ?? 'monthly';

        $query = Workload::with(['user', 'bike', 'day'])
            ->join('days', 'workloads.day_id', '=', 'days.id');

        if ($view == 'weekly') {
            $currentWeek = $request->week ?? Carbon::now()->weekOfYear;
            $startOfWeek = Carbon::now()->setISODate($currentYear, $currentWeek)->startOfWeek();
            $endOfWeek = Carbon::now()->setISODate($currentYear, $currentWeek)->endOfWeek();
            $query->whereBetween('days.date', [$startOfWeek, $endOfWeek]);
        } else {
            $query->whereYear('days.date', $currentYear)
                  ->whereMonth('days.date', $currentMonth);
        }

        $workloads = $query->select('workloads.*')->get();

        $workloadData = [];
        foreach ($workloads as $workload) {
            $dayKey = Carbon::parse($workload->day->date)->format('Y-m-d');
            $workloadData[$dayKey][] = $workload;
        }

        return response()->json([
            'success' => true,
            'view' => $view,
            'year' => $currentYear,
            'month' => $currentMonth,
            'data' => $workloadData,
            'bikes' => Bike::all()
        ]);
    }
    /**
     * @OA\Post(
     *   path="/api/workloads/{workload}/assign-bike",
     *   summary="Assign or swap a bike for a workload",
     *   tags={"Workloads"},
     *   security={{"sanctum_auth":{}}},
     *   @OA\Parameter(
     *     name="workload",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"bike_id"},
     *       @OA\Property(property="bike_id", type="integer", example=1)
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Bike swapped successfully"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validation error"
     *   )
     * )
     */
    public function assignBike(AssignBikeRequest $request, Workload $workload)
    {
      try {
          // We use the Service to handle the "Zero Argument" execute principle
          BikeAssignmentService::forCourier($workload->user)
              ->onDay($workload->day->format('Y-m-d')) // Ensuring correct format
              ->toBike($request->validated('bike_id'))
              ->execute();

          return response()->json([
              'message' => 'Bike swapped successfully',
              'workload' => $workload->load('bike')
          ]);
      } catch (\Exception $e) {
          return response()->json(['error' => $e->getMessage()], 422);
      }
    }
}