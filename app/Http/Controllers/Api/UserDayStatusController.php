<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDayStatus;
use App\Models\Day;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;


#[OA\Server(url: "/api", description: "Main API Server")]
class UserDayStatusController extends Controller
{
    #[OA\Get(
        path: "/api/user-day-statuses",
        summary: "Get list of user daily statuses",
        tags: ["UserDayStatus"],
        parameters: [
            new OA\Parameter(name: "start_date", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date")),
            new OA\Parameter(name: "end_date", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Paginated list of statuses")
        ]
    )]
    public function index(Request $request)
    {
        $query = UserDayStatus::with(['user', 'userStatus.status', 'day']);

        if ($request->filled(['start_date', 'end_date'])) {
            $startDay = Day::where('date', '>=', $request->start_date)->orderBy('date')->first();
            $endDay = Day::where('date', '<=', $request->end_date)->orderBy('date', 'desc')->first();
            
            if ($startDay && $endDay) {
                $query->whereBetween('day_id', [$startDay->id, $endDay->id]);
            }
        }
        return response()->json($query->orderByDesc('day_id')->paginate(20));
    }

    #[OA\Post(
        path: "/user-day-statuses",
        summary: "Assign status to user for a specific day",
        tags: ["UserDayStatus"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["user_id", "user_status_id", "day_id"],
                properties: [
                    new OA\Property(property: "user_id", type: "integer", example: 1),
                    new OA\Property(property: "user_status_id", type: "integer", example: 5),
                    new OA\Property(property: "day_id", type: "integer", example: 10)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Status assigned successfully"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'user_status_id' => 'required|exists:user_statuses,id',
            'date' => 'required|date',
        ]);

        // Find or create the Day by date
        $day = Day::firstOrCreate(['name' => $validated['date'], 'date' => $validated['date']]);


        $userDayStatus = UserDayStatus::create([
            'user_id' => $validated['user_id'],
            'user_status_id' => $validated['user_status_id'],
            'day_id' => $day->id,
        ]);
        return response()->json([
            'message' => 'Created successfully',
            'data' => $userDayStatus->load(['user', 'userStatus.status', 'day'])
        ], Response::HTTP_CREATED);
    }

    #[OA\Get(
        path: "/user-day-statuses/{user_day_status}",
        summary: "Retrieve a specific daily status entry",
        tags: ["UserDayStatus"],
        parameters: [
            new OA\Parameter(name: "user_day_status", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Success"),
            new OA\Response(response: 404, description: "Not Found")
        ]
    )]
    public function show(UserDayStatus $user_day_status)
    {
        return response()->json($user_day_status->load(['user', 'userStatus.status', 'day']));
    }

    #[OA\Put(
        path: "/user-day-statuses/{user_day_status}",
        summary: "Update an existing daily status entry",
        tags: ["UserDayStatus"],
        parameters: [
            new OA\Parameter(name: "user_day_status", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "user_status_id", type: "integer", example: 2)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Updated successfully")
        ]
    )]
    public function update(Request $request, UserDayStatus $user_day_status)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'user_status_id' => 'required|exists:user_statuses,id',
            'day_id' => 'required|exists:days,id',
        ]);

        $user_day_status->update($validated);

        return response()->json([
            'message' => 'Updated successfully',
            'data' => $user_day_status->fresh(['user', 'userStatus.status', 'day'])
        ]);
    }

    #[OA\Delete(
        path: "/user-day-statuses/{user_day_status}",
        summary: "Delete a daily status entry",
        tags: ["UserDayStatus"],
        parameters: [
            new OA\Parameter(name: "user_day_status", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Deleted successfully")
        ]
    )]
    public function destroy(UserDayStatus $user_day_status)
    {
        $user_day_status->delete();
        return response()->json(['message' => 'Deleted successfully'], Response::HTTP_OK);
    }
}