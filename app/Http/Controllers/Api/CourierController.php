<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CourierController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/courier/today-jobs",
     *     summary="Get today's jobs for the authenticated courier",
     *     tags={"Courier"},
     *     security={{"sanctum_auth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Today's jobs with tasks ordered by order_number",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="date", type="string", example="2026-05-27"),
     *             @OA\Property(property="jobs", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="client", type="string"),
     *                     @OA\Property(property="status", type="string"),
     *                     @OA\Property(property="pickup_address", type="string"),
     *                     @OA\Property(property="pickup_time_begin", type="string"),
     *                     @OA\Property(property="pickup_time_end", type="string"),
     *                     @OA\Property(property="tasks", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="order_number", type="integer"),
     *                             @OA\Property(property="type", type="string"),
     *                             @OA\Property(property="status", type="string"),
     *                             @OA\Property(property="address", type="string"),
     *                             @OA\Property(property="time_begin", type="string"),
     *                             @OA\Property(property="time_end", type="string")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Not a courier")
     * )
     */
    public function todayJobs(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->hasRole('courier')) {
            return response()->json([
                'success' => false,
                'message' => 'Access restricted to couriers.',
            ], 403);
        }

        $today = Carbon::today()->toDateString();

        $jobs = Job::with([
                'clientToBill',
                'status',
                'tasks' => fn ($q) => $q->orderBy('order_number')
                    ->with(['pickup', 'package', 'return', 'customTask', 'status']),
            ])
            ->where('courrier_id', $user->id)
            ->whereDate('date', $today)
            ->orderBy('pickup_time_begin')
            ->get();

        $data = $jobs->map(function (Job $job) {
            return [
                'id'                 => $job->id,
                'client'             => optional($job->clientToBill)->name,
                'status'             => optional($job->status)->name,
                'pickup_address'     => trim($job->pickupclientaddressline . ' ' . $job->pickupclientpostalcode),
                'pickup_time_begin'  => $job->pickup_time_begin,
                'pickup_time_end'    => $job->pickup_time_end,
                'tasks'              => $job->tasks->map(fn ($task) => $this->formatTask($task)),
            ];
        });

        return response()->json([
            'success' => true,
            'date'    => $today,
            'jobs'    => $data,
        ]);
    }

    private function formatTask($task): array
    {
        $type  = $task->type();
        $model = match ($type) {
            'pickup'  => $task->pickup,
            'dropOff' => $task->package,
            'return'  => $task->return,
            'custom'  => $task->customTask,
            default   => null,
        };

        return [
            'id'           => $task->id,
            'order_number' => $task->order_number,
            'type'         => $type,
            'status'       => optional($task->status)->name,
            'address'      => $model ? $model->addressShort() : null,
            'time_begin'   => $model ? $model->timeWindowBegin() : null,
            'time_end'     => $model ? $model->timeWindowEnd() : null,
        ];
    }
}
