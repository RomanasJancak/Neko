<?php

namespace App\Services;

use App\Enums\TaskDropoffStatus;
use App\Enums\TaskPickupStatus;
use App\Models\Status;
use App\Models\Task;

class TaskStatusTransitionService
{
    public function getNextStatusInfo(Task $task): array
    {
        $currentStatus = $this->resolveCurrentStatusForTransitions($task);

        if (! $currentStatus) {
            return [
                'status_next' => false,
                'status_next_instance' => null,
                'status_next_instances' => [],
                'status_next_options' => [],
            ];
        }

        $nextStatuses = $this->resolveNextStatuses($task, (int) $currentStatus->id);
        $nextStatus = $nextStatuses[0] ?? null;

        return [
            'status_next' => $nextStatus !== null,
            'status_next_instance' => $nextStatus,
            'status_next_instances' => $nextStatuses,
            'status_next_options' => array_map(static function (Status $status): array {
                return [
                    'id' => (int) $status->id,
                    'name' => (string) $status->name,
                ];
            }, $nextStatuses),
        ];
    }
    public function revertToFirstStatus(Task $task): void
    {
          if (isset($task->pickup)) {
              $firstPickupStatus = $this->resolvePickupStatusModel(TaskPickupStatus::WAITING);
              if (! $firstPickupStatus) {
                  return;
              }

              $task->pickup()->update(['status_id' => $firstPickupStatus->id]);
              $task->status_id = $firstPickupStatus->id;
              $task->saveQuietly();
              return;
          }

          if (isset($task->package) && $task->package) {
              $firstDropoffStatus = $this->resolveDropoffStatusModel(TaskDropoffStatus::WAITING);

              if (! $firstDropoffStatus) {
                  return;
              }

              $task->package()->update(['status_id' => $firstDropoffStatus->id]);
              $task->status_id = $firstDropoffStatus->id;
              $task->saveQuietly();
          }
    }

    public function isTransitionAllowed(Task $task, int $nextStatusId): bool
    {
        $currentStatusId = (int) ($this->resolveCurrentStatusForTransitions($task)?->id ?? $task->status_id);

        if (isset($task->pickup) && $task->pickup) {
            $currentPickupStatus = $this->resolvePickupEnumFromStatusId($currentStatusId);
            $nextPickupStatus = $this->resolvePickupEnumFromStatusId($nextStatusId);

            if (! $currentPickupStatus || ! $nextPickupStatus) {
                return false;
            }

            return in_array($nextPickupStatus, $currentPickupStatus->allowedNextStatuses(), true);
        }

        if (! isset($task->package) || ! $task->package) {
            return true;
        }

        $currentDropoffStatus = $this->resolveDropoffEnumFromStatusId($currentStatusId);
        $nextDropoffStatus = $this->resolveDropoffEnumFromStatusId($nextStatusId);

        if (! $currentDropoffStatus || ! $nextDropoffStatus) {
            return false;
        }

        return in_array($nextDropoffStatus, $currentDropoffStatus->allowedNextStatuses(), true);
    }

    public function getAllowedFlowLabel(Task $task): string
    {
        if (isset($task->pickup) && $task->pickup) {
            return 'WAITING -> ATPU -> POB -> COMPLETED';
        }

        if (isset($task->package) && $task->package) {
            return 'WAITING -> ATDROP -> (UNABLE | POD | POD OP)';
        }

        return 'No transition map configured for this task type.';
    }

    private function resolveNextStatus(Task $task, int $currentStatusId): ?Status
    {
        return $this->resolveNextStatuses($task, $currentStatusId)[0] ?? null;
    }

    /**
     * @return array<Status>
     */
    private function resolveNextStatuses(Task $task, int $currentStatusId): array
    {
        if (isset($task->pickup) && $task->pickup) {
            $currentPickupStatus = $this->resolvePickupEnumFromStatusId($currentStatusId);
            if (! $currentPickupStatus) {
                return [];
            }

            $nextEnums = $currentPickupStatus->allowedNextStatuses();

            return array_values(array_filter(array_map(function (TaskPickupStatus $nextEnum): ?Status {
                return $this->resolvePickupStatusModel($nextEnum);
            }, $nextEnums)));
        }

        if (isset($task->package) && $task->package) {
            $currentDropoffStatus = $this->resolveDropoffEnumFromStatusId($currentStatusId);
            if (! $currentDropoffStatus) {
                return [];
            }

            $nextEnums = $currentDropoffStatus->allowedNextStatuses();

            return array_values(array_filter(array_map(function (TaskDropoffStatus $nextEnum): ?Status {
                return $this->resolveDropoffStatusModel($nextEnum);
            }, $nextEnums)));
        }

        if (isset($task->return) || isset($task->customTask)) {
            $currentPickupStatus = $this->resolvePickupEnumFromStatusId($currentStatusId);
            if (! $currentPickupStatus) {
                return [];
            }

            $nextEnums = $currentPickupStatus->allowedNextStatuses();

            return array_values(array_filter(array_map(function (TaskPickupStatus $nextEnum): ?Status {
                return $this->resolvePickupStatusModel($nextEnum);
            }, $nextEnums)));
        }

        // TODO: replace with dedicated per-type enum maps when those flows are finalized.
        return [];
    }

    private function resolveCurrentStatusForTransitions(Task $task): ?Status
    {
        $taskStatus = $task->status;
        $resolvedStatus = $task->resolvedStatus();

        if (! $taskStatus) {
            return $resolvedStatus;
        }

        if (! $resolvedStatus) {
            return $taskStatus;
        }

        if ((int) $taskStatus->id === (int) $resolvedStatus->id) {
            return $taskStatus;
        }

        // Prefer task.status (used in dashboard) when it still has forward transitions.
        if (count($this->resolveNextStatuses($task, (int) $taskStatus->id)) > 0) {
            return $taskStatus;
        }

        // Fallback to subtype status when task.status appears stale.
        if (count($this->resolveNextStatuses($task, (int) $resolvedStatus->id)) > 0) {
            return $resolvedStatus;
        }

        return $taskStatus;
    }

    private function resolvePickupEnumFromStatusId(int $statusId): ?TaskPickupStatus
    {
        $enumById = TaskPickupStatus::tryFrom($statusId);
        if ($enumById) {
            return $enumById;
        }

        $statusName = Status::find($statusId)?->name;
        return TaskPickupStatus::fromStatusName($statusName);
    }

    private function resolvePickupStatusModel(TaskPickupStatus $status): ?Status
    {
        return $this->resolveStatusModelByAliases($status->aliases());
    }

    private function resolveDropoffEnumFromStatusId(int $statusId): ?TaskDropoffStatus
    {
        $enumById = TaskDropoffStatus::tryFrom($statusId);
        if ($enumById) {
            return $enumById;
        }

        $statusName = Status::find($statusId)?->name;
        return TaskDropoffStatus::fromStatusName($statusName);
    }

    private function resolveDropoffStatusModel(TaskDropoffStatus $status): ?Status
    {
        return $this->resolveStatusModelByAliases($status->aliases());
    }

    /**
     * @param array<string> $aliases
     */
    private function resolveStatusModelByAliases(array $aliases): ?Status
    {
        $statuses = Status::all(['id', 'name']);

        // Prefer strict matches first to avoid ambiguous status selection.
        foreach ($aliases as $alias) {
            $target = $this->normalizeStatusName($alias);
            foreach ($statuses as $status) {
                if ($this->normalizeStatusName((string) $status->name) === $target) {
                    return $status;
                }
            }
        }

        // Fallback to contains matching for labels like "Pickup - AtPu".
        foreach ($aliases as $alias) {
            $target = $this->normalizeStatusName($alias);
            foreach ($statuses as $status) {
                $statusName = $this->normalizeStatusName((string) $status->name);
                if ($target !== '' && (str_contains($statusName, $target) || str_contains($target, $statusName))) {
                    return $status;
                }
            }
        }

        return null;
    }

    private function normalizeStatusName(string $name): string
    {
        $normalized = strtoupper(str_replace(['-', '_'], ' ', trim($name)));
        return preg_replace('/\s+/', ' ', $normalized) ?? $normalized;
    }
}
