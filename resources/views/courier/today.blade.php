@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">My Jobs &mdash; @displayDate($today)</h4>

    @if($jobs->isEmpty())
        <div class="alert alert-info">No jobs assigned to you today.</div>
    @else
        @foreach($jobs as $job)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <strong>Job #{{ $job->id }}</strong>
                    @if($job->clientToBill)
                        &mdash; {{ $job->clientToBill->name }}
                    @endif
                </span>
                <span class="badge bg-secondary">{{ optional($job->status)->name ?? 'N/A' }}</span>
            </div>
            <div class="card-body p-0">
                @if($job->tasks->isEmpty())
                    <p class="p-3 mb-0 text-muted">No tasks.</p>
                @else
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Type</th>
                            <th>Address</th>
                            <th>Time Window</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($job->tasks as $task)
                        @php
                            $type  = $task->type();
                            $model = match($type) {
                                'pickup'  => $task->pickup,
                                'dropOff' => $task->package,
                                'return'  => $task->return,
                                'custom'  => $task->customTask,
                                default   => null,
                            };
                            $badge = match($type) {
                                'pickup'  => 'bg-primary',
                                'dropOff' => 'bg-success',
                                'return'  => 'bg-warning text-dark',
                                'custom'  => 'bg-info text-dark',
                                default   => 'bg-secondary',
                            };
                        @endphp
                        <tr>
                            <td class="text-center">{{ $task->order_number }}</td>
                            <td><span class="badge {{ $badge }}">{{ ucfirst($type ?? 'unknown') }}</span></td>
                            <td>{{ $model ? $model->addressShort() : '—' }}</td>
                            <td>
                                @if($model)
                                    {{ $model->timeWindowBeginFormatted() }} – {{ $model->timeWindowEndFormatted() }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ optional($task->status)->name ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
        @endforeach
    @endif
</div>
@endsection
