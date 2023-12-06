@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Clients</h1>
            <div class="mt-3">
        <form method="POST" action="{{ route('client.createBackup') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Create Backup</button>
        </form>
    </div>
            <ul class="list-group">
                @foreach($clients as $client)
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-md-1">NC{{ $client->id }}</div>
                            <div class="col-md-3">{{ $client->name }}</div>
                            <div class="col-md-2">{{ $client->email }}</div>
                            <div class="col-md-2">{{ $client->vat }}</div>
                            <div class="col-md-2">{{ $client->regNumber }}</div>
                            <div class="col-md-2">{{ $client->address }}</div>
                            <div class="col-md-2">{{ $client->note }}</div>
                            <div class="col-md-2">{{ $client->postal_code }}</div>
                            <div class="col-md-1"><a href="{{ route('client.show', $client) }}">More...</a></div>
                            <div class="col-md-1"><a href="{{ route('client.edit', $client) }}">Edit</a></div>
                            <div class="col-md-1"><a href="{{ route('client.delete', $client) }}">Delete</a></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {!! $clients->links() !!}
    </div>
</div>
@endsection