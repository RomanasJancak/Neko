<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetCouriersWithWorkloadRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class CourierWorkloadController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/couriers/with-workload",
     *     summary="List couriers who have workload on a given date",
     *     tags={"Courier"},
     *     security={{"sanctum_auth": {}}},
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         required=true,
     *         description="Date in YYYY-MM-DD format",
     *         @OA\Schema(type="string", format="date", example="2026-05-27")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="date", type="string", example="2026-05-27"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=3),
     *                     @OA\Property(property="name", type="string", example="Courier One"),
     *                     @OA\Property(property="email", type="string", example="courier@example.com"),
     *                     @OA\Property(property="phone", type="string", example="+44123456789")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function __invoke(GetCouriersWithWorkloadRequest $request): JsonResponse
    {
        $date = $request->validated('date');

        $couriers = User::query()
            ->couriers()
            ->withWorkloadOnDate($date)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);

        return response()->json([
            'success' => true,
            'date' => $date,
            'data' => $couriers,
        ]);
    }
}
