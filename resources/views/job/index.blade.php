@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Jobs</h1>
            <ul class="list-group">
            
                @foreach($jobs as $job)
                    <li class="list-group-item">
                        <div class="">ID :: {{$job->id}}</div>
                        <div class="">Billing :: {{$job->clientToBill->name}}</div>
                    </li>     
                @endforeach
            </ul>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {!! $jobs->links() !!}
    </div>
</div>
@endsection