@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Jobs</h1>
            <ul class="list-group">
                @foreach($jobs as $job)
                    
                    <li class="list-group-item">
                        <div class="row border border-primary rounded">
                            @role('courier')
                            <div class="col-md-1 text-center border border-primary rounded"><h4>{{ $job->id }}</h4></div>
                            @else
                            <div class="col-md-1">{{ $job->id }}</div>
                            @endrole    
                            <div class="col-md-3 ">Client: {{ $job->client->name }}</div>
                            @role('courier')
                            @else
                            <div class="col-md-2 ">Courier: {{ $job->courier->name }}</div>
                            @endrole
                            <div class="col-md-1 ">Pickup time: {{ $job->creation_time }}</div>
                            <div class="col-md-1 ">Dropoff Time: {{ $job->completion_time }}</div>
                            <div class="col-md-2 ">Status: {{ $job->status->name }}</div>
                            <div class="col-md-2 ">Collection Details: {{ $job->collection_details }}</div>
                            <div class="col-md-2 ">Pickup Address: {{ $job->pickup_address }}</div>
                            <div class="col-md-2 ">Delivery Address: {{ $job->delivery_address }}</div>
                            <div class="col-md-2 ">Sender Contacts: {{ $job->senderContacts }}</div>
                            <div class="col-md-2 ">Manager: {{ $job->manager->name }}</div>
                            <div class="col-md-2 ">Receiver Contacts: {{ $job->receiverContacts }}</div>
                            <div class="col-md-2 ">Notes: {{ $job->notes }}</div>
                            @role('courier')
                            @else
                            <div class="col-md-2 border border-primary rounded">Invoice ID: {{ $job->invoice_id }}</div>
                            @endrole
                            <div class="col-md-1 border border-primary rounded "><a href="{{ route('job.show', $job) }}">More...</a></div>
                            <div class="col-md-1 border border-primary rounded "><a href="{{ route('job.edit', $job) }}">Edit...</a></div>
                            @role('courier')
                            @if($job->status->id === 7)
                            <form method="POST" action="{{ route('job.updateStatus', [$job]) }}">
                                <input type="hidden" name="status_id" value="3">
                                @csrf
                                <button type="submit" class="btn btn-info">Accept</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('job.updateStatus', [$job]) }}">
                                <input type="hidden" name="status_id" value="6">
                                @csrf
                                <button type="submit" class="btn btn-warning">Issue</button>
                            </form>
                            <form method="POST" action="{{ route('job.updateStatus', [$job]) }}">
                                <input type="hidden" name="status_id" value="5">
                                @csrf
                                <button type="submit" class="btn btn-danger">Decline</button>
                            </form>
                            <form method="POST" action="{{ route('job.updateStatus', [$job]) }}">
                                <input type="hidden" name="status_id" value="4">
                                @csrf
                                <button type="submit" class="btn btn-success">Complete</button>
                            </form>
                            @endrole
                        </div>
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