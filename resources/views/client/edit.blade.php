@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Edit Client</h1>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('client.update', $client) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $client->name) }}">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $client->email) }}">
                </div>

                <div class="form-group">
                    <label for="vat">VAT:</label>
                    <input type="text" class="form-control" id="vat" name="vat" value="{{ old('vat', $client->vat) }}">
                </div>

                <div class="form-group">
                    <label for="regNumber">Registration Number:</label>
                    <input type="text" class="form-control" id="regNumber" name="regNumber" value="{{ old('regNumber', $client->regNumber) }}">
                </div>

                <div class="form-group">
                    <label for="address">Address:</label>
                    <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $client->address) }}">
                </div>

                <div class="form-group">
                    <label for="note">Note:</label>
                    <textarea class="form-control" id="note" name="note">{{ old('note', $client->note) }}</textarea>
                </div>
                <div class="form-group">
                    <label for="street">Street:</label>
                    <input type="text" class="form-control" id="street" name="street" value="{{ old('street', $client->street) }}">
                </div>
                <div class="form-group">
                    <label for="street">Apt/suite:</label>
                    <input type="text" class="form-control" id="apt_suite" name="apt_suite" value="{{ old('apt_suite', $client->apt_suite) }}">
                </div>
                <div class="form-group">
                    <label for="street">City:</label>
                    <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $client->city) }}">
                </div>
                <div class="form-group">
                    <label for="street">State/Province:</label>
                    <input type="text" class="form-control" id="state_province" name="state_province" value="{{ old('state_province', $client->state_province) }}">
                </div>
                <div class="form-group">
                    <label for="street">Postal_code:</label>
                    <input type="text" class="form-control" id="postal_code" name="postal_code" value="{{ old('postal_code', $client->postal_code) }}">
                </div>
                <div class="form-group">
                    <label for="street">Country:</label>
                    <input type="text" class="form-control" id="country" name="country" value="{{ old('country', $client->country) }}">
                </div>
                <button type="submit" class="btn btn-primary">Update Client</button>
            </form>
        </div>
    </div>
</div>
@endsection
