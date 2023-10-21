@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Edit Job</h1>

            <form method="POST" action="{{ route('job.update', $job->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="client_id">Client</label>
                    <select id="client_id" name="client_id" class="form-control">
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ $client->id == $job->client_id ? 'selected' : '' }}>{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="courrier_id">Courier</label>
                    <select id="courrier_id" name="courrier_id" class="form-control">
                        @foreach($couriers as $courier)
                            <option value="{{ $courier->id }}" {{ $courier->id == $job->courrier_id ? 'selected' : '' }}>{{ $courier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="status_id">Status</label>
                    <select id="status_id" name="status_id" class="form-control">
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ $status->id == $job->status_id ? 'selected' : '' }}>{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div> 

                <div class="form-group">
                    <label for="creation_time">Creation Time</label>
                    <input type="datetime-local" id="creation_time" name="creation_time" class="form-control" value="{{ $job->creation_time }}">
                </div>

                <div class="form-group">
                    <label for="completion_time">Completion Time</label>
                    <input type="datetime-local" id="completion_time" name="completion_time" class="form-control" value="{{ $job->completion_time }}">
                </div>
                <div class="form-group">
                    <label for="collection_details">Collection Details</label>
                    <textarea id="collection_details" name="collection_details" class="form-control">{{ $job->collection_details }}</textarea>
                </div>

                <div class="form-group">
                    <label for="pickup_address">Pickup Address</label>
                    <input type="text" id="pickup_address" name="pickup_address" class="form-control" value="{{ $job->pickup_address }}">
                </div>

                <div class="form-group">
                    <label for="delivery_address">Delivery Address</label>
                    <input type="text" id="delivery_address" name="delivery_address" class="form-control" value="{{ $job->delivery_address }}">
                </div>

                <div class="form-group">
                    <label for="senderContacts">Sender Contacts</label>
                    <input type="text" id="senderContacts" name="senderContacts" class="form-control" value="{{ $job->senderContacts }}">
                </div>

                <div class="form-group">
                    <label for="manager_id">Manager</label>
                    <select id="manager_id" name="manager_id" class="form-control">
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}" {{ $manager->id == $job->manager_id ? 'selected' : '' }}>{{ $manager->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="receiverContacts">Receiver Contacts</label>
                    <input type="text" id="receiverContacts" name="receiverContacts" class="form-control" value="{{ $job->receiverContacts }}">
                </div>
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" class="form-control">{{ $job->notes }}</textarea>
                </div>

                <div class="form-group">
                    <label for="invoice_id">Invoice ID</label>
                    <input type="text" id="invoice_id" name="invoice_id" class="form-control" value="{{ $job->invoice_id }}">
                </div>

                <button type="submit" class="btn btn-primary">Update Job</button>
            </form>
        </div>
    </div>
</div>
@endsection
