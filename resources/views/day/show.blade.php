@extends('layouts.app')
@section('styles')
@endsection
@section('content')
    <div class="container">
        <h2 class="mt-4 mb-4">Schedule for {{-- $date --}}</h2>
        
        <div class="row">
            <div class="col-md-2">
                <!-- User List -->
                <div class="card-header">Jobs</div>
                <ul class="list-group">
                    @foreach($jobs as $job)
                        <li class="list-group-item">{{ $job->sender->name}}</li>
                    @endforeach
                </ul>
            </div>
            
            <div class="col-md-10">
                <!-- Calendar -->
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    @foreach($users as $user)
                        <div class="col">
                            <div class="card">
                                <div class="card-header">{{ $user->name }}</div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        @foreach($user->jobs as $job)
                                            <li class="list-group-item">{{ $job->sender->name }} - {{ $job->receiver->name }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')

@endsection