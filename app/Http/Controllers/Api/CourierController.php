<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Status;
use App\Models\Task;
use App\Services\TaskStatusTransitionService;
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

    /**
     * @OA\Get(
     *     path="/api/courier/jobs/{date}",
     *     summary="Get jobs for the authenticated courier on a specific date",
     *     tags={"Courier"},
     *     security={{"sanctum_auth": {}}},
     *     @OA\Parameter(
     *         name="date",
     *         in="path",
     *         required=true,
     *         description="Date in YYYY-MM-DD format",
     *         @OA\Schema(type="string", format="date", example="2026-05-31")
     *     ),
     *     @OA\Response(response=200, description="Jobs for the given date"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Not a courier"),
     *     @OA\Response(response=422, description="Invalid date format")
     * )
     */
    public function jobsByDate(Request $request, string $date): JsonResponse
    {
        $user = $request->user();

        if (! $user->hasRole('courier')) {
            return response()->json([
                'success' => false,
                'message' => 'Access restricted to couriers.',
            ], 403);
        }

        try {
            $targetDate = Carbon::parse($date)->toDateString();
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format. Use YYYY-MM-DD.',
            ], 422);
        }

        $jobs = Job::with([
                'clientToBill',
                'status',
                'tasks' => fn ($q) => $q->orderBy('order_number')
                    ->with(['pickup', 'package', 'return', 'customTask', 'status']),
            ])
            ->where('courrier_id', $user->id)
            ->whereDate('date', $targetDate)
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
            'date'    => $targetDate,
            'jobs'    => $data,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/courier/tasks/{task}/status",
     *     summary="Initiate courier task status transition and return next possible status options",
     *     tags={"Courier"},
     *     security={{"sanctum_auth": {}}},
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         required=true,
     *         description="Task ID",
     *         @OA\Schema(type="integer", example=123)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         description="Optional explicit target status. If omitted, service auto-selects the first allowed next status.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_id", type="integer", example=26)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Task status updated successfully."),
     *             @OA\Property(property="task_id", type="integer", example=123),
     *             @OA\Property(property="job_id", type="integer", example=77),
     *             @OA\Property(property="new_status", type="object",
     *                 @OA\Property(property="id", type="integer", example=26),
     *                 @OA\Property(property="name", type="string", example="Available")
     *             ),
     *             @OA\Property(property="possible_status_options", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=18),
     *                     @OA\Property(property="name", type="string", example="Completed - POD - proof of delivery")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Not a courier or task not assigned to courier"),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid transition or no valid next status"
     *     )
     * )
     */
    public function updateTaskStatus(Request $request, Task $task): JsonResponse
    {
        $user = $request->user();

        if (! $user->hasRole('courier')) {
            return response()->json([
                'success' => false,
                'message' => 'Access restricted to couriers.',
            ], 403);
        }

        $task->load(['job', 'status', 'pickup', 'package', 'return', 'customTask']);

        if (! $task->job || (int) $task->job->courrier_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Task is not assigned to the authenticated courier.',
            ], 403);
        }

        $transitionService = app(TaskStatusTransitionService::class);
        $targetStatus = $request->filled('status_id')
            ? Status::find((int) $request->input('status_id'))
            : $transitionService->getNextStatusInfo($task)['status_next_instance'];

        if (! $targetStatus) {
            $nextInfo = $transitionService->getNextStatusInfo($task);

            return response()->json([
                'success' => false,
                'message' => 'No valid next status found for this task.',
                'task_id' => (int) $task->id,
                'current_status' => [
                    'id' => (int) ($task->status?->id ?? 0),
                    'name' => (string) ($task->status?->name ?? ''),
                ],
                'possible_status_options' => $nextInfo['status_next_options'],
            ], 422);
        }

        if (! $transitionService->isTransitionAllowed($task, (int) $targetStatus->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status transition.',
                'allowed_flow' => $transitionService->getAllowedFlowLabel($task),
                'task_id' => (int) $task->id,
                'current_status' => [
                    'id' => (int) ($task->status?->id ?? 0),
                    'name' => (string) ($task->status?->name ?? ''),
                ],
                'requested_status' => [
                    'id' => (int) $targetStatus->id,
                    'name' => (string) $targetStatus->name,
                ],
            ], 422);
        }

        $task->status_id = $targetStatus->id;
        $task->save();

        if ($task->pickup) {
            $task->pickup->status_id = $targetStatus->id;
            $task->pickup->save();
        } elseif ($task->package) {
            $task->package->status_id = $targetStatus->id;
            $task->package->save();
        } elseif ($task->return) {
            $task->return->status_id = $targetStatus->id;
            $task->return->save();
        } elseif ($task->customTask) {
            $task->customTask->status_id = $targetStatus->id;
            $task->customTask->save();
        }

        $task->refresh();
        $task->load(['status', 'pickup.status', 'package.status', 'return.status', 'customTask.status']);

        $nextInfo = $transitionService->getNextStatusInfo($task);

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully.',
            'task_id' => (int) $task->id,
            'job_id' => (int) ($task->job?->id ?? 0),
            'new_status' => [
                'id' => (int) $targetStatus->id,
                'name' => (string) $targetStatus->name,
            ],
            'possible_status_options' => $nextInfo['status_next_options'],
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
