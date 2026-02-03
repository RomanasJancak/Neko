<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserStatus;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[OA\Server(url: "/api", description: "Main API Server")]
class UserStatusController extends Controller
{
    #[OA\Get(
        path: "/user-statuses",
        summary: "Get list of user statuses",
        tags: ["UserStatus"],
        responses: [
            new OA\Response(response: 200, description: "List of user statuses")
        ]
    )]
    public function index()
    {
        $userStatuses = UserStatus::with('status')->get();
        return response()->json($userStatuses);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    #[OA\Post(
        path: "/user-statuses",
        summary: "Create a new user status",
        tags: ["UserStatus"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Sick")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "User status created successfully"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $userStatus = DB::transaction(function () use ($request) {
            $status = Status::create([
                'name' => $request->name,
            ]);
            return UserStatus::create(['status_id' => $status->id]);
        });

        return response()->json([
            'message' => 'User status created successfully.',
            'data' => $userStatus->load('status')
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserStatus $userStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserStatus $userStatus)
    {
        //
    }

    #[OA\Patch(
        path: "/user-statuses/{user_status}",
        summary: "Update a user status",
        tags: ["UserStatus"],
        parameters: [
            new OA\Parameter(name: "user_status", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Vacation")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Status updated successfully"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function update(Request $request, UserStatus $user_status)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $status = $user_status->status;
        $status->name = $request->name;
        $status->save();
        return response()->json([
            'message' => 'Status updated successfully.',
            'data' => $user_status->fresh('status')
        ]);
    }

    #[OA\Delete(
        path: "/user-statuses/{user_status}",
        summary: "Delete a user status",
        tags: ["UserStatus"],
        parameters: [
            new OA\Parameter(name: "user_status", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Status removed."),
            new OA\Response(response: 404, description: "Not found")
        ]
    )]
    public function destroy(UserStatus $user_status)
    {
        DB::transaction(function () use ($user_status) {
            Status::find($user_status->status_id)->delete();
            $user_status->delete();
        });
        return response()->json(['message' => 'Status removed.'], Response::HTTP_OK);
    }
}
