<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class JobFilter
{
    private $packageFilters = [];
    public function filter(Builder $query, array $filters): Builder
    {

        if($filters['package']){
          $this->packageFilters = is_string($filters['dOsp']) 
            ? json_decode($filters['dOsp'], true) 
            : $filters['dOsp'];
        }
        //dd(request()->all(), $this->packageFilters,$filters['dOsp']);
        foreach ($filters as $filter => $value) {
            if (method_exists($this, $filter)) {
                $this->$filter($query, $value);
            }
        }

        return $query;
    }

    protected function id(Builder $query, $value): void
    {
        $query->where('jobs.id', 'like', "%{$value}%");
    }
    protected function clientName(Builder $query, $value): void
    {
        $query->whereHas('clientToBill', function (Builder $query) use ($value) {
            $query->where('name', 'like', "%{$value}%");
        });
    }
    protected function startDate(Builder $query, $value): void
    {
        $query->where('date', '>=', $value);
    }
    protected function endDate(Builder $query, $value): void
    {
        $query->where('date', '<=', $value);
    }
    protected function status(Builder $query, $value): void
    {
        if (is_array($value) && count($value) > 0) {
          $query->whereIn('jobs.status_id', $value);
        }
    }
    protected function package(Builder $query, $value): void
    {
        // 1. Grab columns safely from the array payload context
        $columns = $this->packageFilters;
        if (is_string($columns)) {
            $columns = json_decode($columns, true) ?? [];
        }

        if (!is_array($columns) || count($columns) === 0) {
            return;
        }

        // 2. Bypass polymorphic ambiguities by filtering directly via a custom explicit subquery
        $query->whereHas('tasks', function (Builder $taskQuery) use ($value, $columns) {
            // Enforce the morphed type explicitly at the task level
            $taskQuery->where('taskable_type', \App\Models\Package::class)
                // Join straight to the packages table directly by matching IDs manually
                ->whereIn('taskable_id', function ($subQuery) use ($value, $columns) {
                    $subQuery->select('id')
                        ->from('packages')
                        ->where(function ($innerQ) use ($value, $columns) {
                            foreach ($columns as $column) {
                                $innerQ->orWhere($column, 'like', "%{$value}%");
                            }
                        });
                });
        });
    }
}