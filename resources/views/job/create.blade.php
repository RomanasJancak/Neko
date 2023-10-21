@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Create Job</h1>

            <form method="POST" action="{{ route('job.store') }}">
                @csrf
                <div class="form-group">
                    <label for="client_id">Client</label>
                    <select id="client_id" name="client_id" class="form-control">
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="courrier_id">Courier</label>
                    <select id="courrier_id" name="courrier_id" class="form-control">
                        @foreach($couriers as $courrier)
                            <option value="{{ $courrier->id }}">{{ $courrier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="creation_time">Creation Time</label>
                    <input type="datetime-local" id="creation_time" name="creation_time" class="form-control">
                </div>
                <div class="form-group">
                    <label for="completion_time">Completion Time</label>
                    <input type="datetime-local" id="completion_time" name="completion_time" class="form-control">
                </div>
                <div class="form-group">
                    <label for="collection_details">Collection Details</label>
                    <textarea id="collection_details" name="collection_details" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="pickup_address">Pickup Address</label>
                    <input type="text" id="pickup_address" name="pickup_address" class="form-control">
                </div>
                <div class="form-group">
                    <label for="delivery_address">Delivery Address</label>
                    <input type="text" id="delivery_address" name="delivery_address" class="form-control">
                </div>
                <div class="form-group">
                    <label for="senderContacts">Sender Contacts</label>
                    <input type="text" id="senderContacts" name="senderContacts" class="form-control">
                </div>
                <div class="form-group">
                    <label for="manager_id">Manager</label>
                    <select id="manager_id" name="manager_id" class="form-control">
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="receiverContacts">Receiver Contacts</label>
                    <input type="text" id="receiverContacts" name="receiverContacts" class="form-control">
                </div>
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="invoice_id">Invoice ID</label>
                    <input type="text" id="invoice_id" name="invoice_id" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Create Job</button>
            </form>
        </div>
    </div>
</div>
@endsection
