@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Client Details</h1>
            <div class="col-md-1"><a href="{{ route('client.show', $previousClient) }}">Previous</a></div>
            <div class="col-md-1"><a href="{{ route('client.show', $nextClient) }}">Next</a></div>
            <ul class="list-group">
                <li class="list-group-item"><strong>Name:</strong> {{ $client->name }}</li>
                <li class="list-group-item"><strong>Email:</strong> {{ $client->email }}</li>
                <li class="list-group-item"><strong>VAT:</strong> {{ $client->vat }}</li>
                <li class="list-group-item"><strong>Company Address: </strong>{{ $client->postal_code }} {{ $client->apt_suite }} {{ $client->street }} {{ $client->city }}</li>
                <li class="list-group-item"><strong>Note about deliveries:</strong> {{ $client->note }}</li>
                <li class="list-group-item"><strong>Sender Contacts:</strong> {{ $client->senderContacts }}</li>
                <li class="list-group-item"><strong>Receiver Contacts:</strong> {{ $client->receiverContacts }}</li>
                <li class="list-group-item"><strong>Collection Details:</strong> {{ $client->collection_details }}</li>
                <li class="list-group-item"><strong>Dropoff Details:</strong> {{ $client->dropoff_details }}</li>
                <li class="list-group-item"><strong>Client User:</strong> {{ $client->client_user }}</li>
                <li class="list-group-item"><strong>Client Assigned User:</strong> {{ $client->client_assigned_user }}</li>
                <li class="list-group-item"><strong>Balance:</strong> {{ $client->balance }}</li>
                <li class="list-group-item"><strong>Paid to Date:</strong> {{ $client->paid_to_date }}</li>
                <li class="list-group-item"><strong>Client Currency:</strong> {{ $client->client_currency }}</li>
                <li class="list-group-item"><strong>Website:</strong> {{ $client->website }}</li>
                <li class="list-group-item"><strong>Private Notes:</strong> {{ $client->private_notes }}</li>
                <li class="list-group-item"><strong>Industry:</strong> {{ $client->industry }}</li>
                <li class="list-group-item"><strong>Client Size:</strong> {{ $client->client_size }}</li>
                <li class="list-group-item"><strong>Client Phone:</strong> {{ $client->phone }}</li>
                <li class="list-group-item"><strong>Street:</strong> {{ $client->street }}</li>
                <li class="list-group-item"><strong>Apt/Suite:</strong> {{ $client->apt_suite }}</li>
                <li class="list-group-item"><strong>City:</strong> {{ $client->city }}</li>
                <li class="list-group-item"><strong>State/Province:</strong> {{ $client->state_province }}</li>
                <li class="list-group-item"><strong>Postal Code:</strong> {{ $client->postal_code }}</li>
                <li class="list-group-item"><strong>Country:</strong> {{ $client->country }}</li>
                <li class="list-group-item"><strong>Shipping Street:</strong> {{ $client->shipping_street }}</li>
                <li class="list-group-item"><strong>Shipping Apt/Suite:</strong> {{ $client->shipping_apt_suite }}</li>
                <li class="list-group-item"><strong>Shipping City:</strong> {{ $client->shipping_city }}</li>
                <li class="list-group-item"><strong>Shipping State/Province:</strong> {{ $client->shipping_state_province }}</li>
                <li class="list-group-item"><strong>Shipping Postal Code:</strong> {{ $client->shipping_postal_code }}</li>
                <li class="list-group-item"><strong>Shipping Country:</strong> {{ $client->shipping_country }}</li>
                <li class="list-group-item"><strong>Client Payment Terms:</strong> {{ $client->client_payment_terms }}</li>
                <li class="list-group-item"><strong>ID Number:</strong> {{ $client->id_number }}</li>
                <li class="list-group-item"><strong>Public Notes:</strong> {{ $client->public_notes }}</li>
                <li class="list-group-item"><strong>Contact Phone:</strong> {{ $client->contact_phone }}</li>
                <li class="list-group-item"><strong>First Name:</strong> {{ $client->first_name }}</li>
                <li class="list-group-item"><strong>Last Name:</strong> {{ $client->last_name }}</li>
                <li class="list-group-item"><strong>Custom Value 1:</strong> {{ $client->custom_value_1 }}</li>
                <li class="list-group-item"><strong>Custom Value 2:</strong> {{ $client->custom_value_2 }}</li>
                <li class="list-group-item"><strong>Custom Value 3:</strong> {{ $client->custom_value_3 }}</li>
                <li class="list-group-item"><strong>Custom Value 4:</strong> {{ $client->custom_value_4 }}</li>
                <li class="list-group-item"><strong>Contact Custom Value 1:</strong> {{ $client->contact_custom_value_1 }}</li>
                <li class="list-group-item"><strong>Contact Custom Value 2:</strong> {{ $client->Contact_Custom_Value_2 }}</li>
                <li class="list-group-item"><strong>Contact Custom Value 3:</strong> {{ $client->Contact_Custom_Value_3 }}</li>
                <li class="list-group-item"><strong>Contact Custom Value 4:</strong> {{ $client->Contact_Custom_Value_4 }}</li>
                <li class="list-group-item"><strong>Payment Balance:</strong> {{ $client->Payment_Balance }}</li>
                <li class="list-group-item"><strong>Credit Balance:</strong> {{ $client->Credit_Balance }}</li>
                <li class="list-group-item"><strong>Classification:</strong> {{ $client->Classification }}</li>
            </ul>

            <div class="mt-3">
                <a href="{{ route('client.index') }}" class="btn btn-primary">Back to Clients</a>
                <a href="{{ route('client.edit', $client) }}" class="btn btn-warning">Edit Client</a>
            </div>
        </div>
    </div>
</div>
@endsection
