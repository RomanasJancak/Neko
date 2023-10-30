@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Job Details</h1>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Job Information</h5>
                    <ul>
                        <li><strong>ID:</strong> {{ $job->id }}</li>
                        <li><strong>Client:</strong> {{ $job->client->name }}</li>
                        <li><strong>Courier:</strong> {{ $job->courier->name }}</li>
                        <li><strong>Pickup time:</strong> {{ $job->creation_time }}</li>
                        <li><strong>Dropoff Time:</strong> {{ $job->completion_time }}</li>
                        <li><strong>Status:</strong> {{ $job->status->name}}</li>
                        <li><strong>Collection Details:</strong> {{ $job->collection_details }}</li>
                        <li><strong>Pickup Address:</strong> {{ $job->pickup_address }}</li>
                        <li><strong>Delivery Address:</strong> {{ $job->delivery_address }}</li>
                        <li><strong>Sender Contacts:</strong> {{ $job->senderContacts }}</li>
                        <li><strong>Manager:</strong> {{ $job->manager->name }}</li>
                        <li><strong>Receiver Contacts:</strong> {{ $job->receiverContacts }}</li>
                        <li><strong>Notes:</strong> {{ $job->notes }}</li>
                        <li><strong>Invoice ID:</strong> {{ $job->invoice_id }}</li>
                    </ul>
                </div>
            </div>

            <a href="{{ route('job.index') }}" class="btn btn-primary mt-3">Back to Jobs</a>
            <a href="{{ route('job.edit', $job) }}" class="btn btn-warning mt-3">Edit Job</a>
        </div>
    </div>
</div>
@endsection