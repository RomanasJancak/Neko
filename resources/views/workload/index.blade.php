@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Workloads</h1>
                <div class="row">
                    @foreach ($workloads as $workload)
                        <div class="col-md-4 mb-3"> <!-- Adjust the col-md-* as needed -->
                            <div class="list-group">
                                <div class="list-group-item list-group-item-action">
                                    <div>{{$workload->id}}</div>
                                    <div>{{$workload->date}}</div>
                                    <div>{{$workload->capacity}}</div>
                                    <div>{{$workload->day->date}}</div>
                                    <div>{{$workload->user->name}}</div>
                                    <div>{{$workload->bike->name}}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
        </div>

    <div class="d-flex justify-content-center mt-3">
        {!! $workloads->links() !!}
    </div>
</div>
@endsection