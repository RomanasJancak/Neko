@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Client Details</h1>

            <ul class="list-group">
                <li class="list-group-item"><strong>Name:</strong> {{ $client->name }}</li>
                <li class="list-group-item"><strong>Email:</strong> {{ $client->email }}</li>
                <li class="list-group-item"><strong>VAT:</strong> {{ $client->vat }}</li>
                <li class="list-group-item"><strong>Registration Number:</strong> {{ $client->regNumber ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Address:</strong> {{ $client->address }}</li>
                <li class="list-group-item"><strong>Note:</strong> {{ $client->note }}</li>
            </ul>

            <div class="mt-3">
                <a href="{{ route('client.index') }}" class="btn btn-primary">Back to Clients</a>
                <a href="{{ route('client.edit', $client) }}" class="btn btn-warning">Edit Client</a>
            </div>
        </div>
    </div>
</div>
@endsection
