@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Delete Client</h1>

            <p>Are you sure you want to delete the client with the following details?</p>

            <ul class="list-group">
                <li class="list-group-item"><strong>Name:</strong> {{ $client->name }}</li>
                <li class="list-group-item"><strong>Email:</strong> {{ $client->email }}</li>
                <li class="list-group-item"><strong>VAT:</strong> {{ $client->vat }}</li>
                <li class="list-group-item"><strong>Registration Number:</strong> {{ $client->regNumber ?? 'N/A' }}</li>
                <li class="list-group-item"><strong>Address:</strong> {{ $client->address }}</li>
                <li class="list-group-item"><strong>Note:</strong> {{ $client->note }}</li>
            </ul>

            <form action="{{ route('client.destroy', $client) }}" method="POST">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn btn-danger">Confirm Delete</button>
                <a href="{{ route('client.show', $client) }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection