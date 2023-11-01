@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Create Job</h1>
            <form method="POST" action="{{ route('job.store') }}" class="row g-3">
                @csrf
                <div class="form-group col-md-2">
                    <label for="courrier_id">Courier</label>
                    <select id="courrier_id" name="courrier_id" class="form-control" >
                        @foreach($couriers as $courier)
                            <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="status_id">Status</label>
                    <select id="status_id" name="status_id" class="form-control">
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-3">
                    <div class="row g-3">
                        <div class="form-group col-md-3">
                            <label for="sender_id">Sender</label>
                                <input type="text" id="client_search" class="form-control" placeholder="Search for a client">
                                <select id="sender_id" name="sender_id" class="form-control"  size="3">
                                    @foreach($clients as $client)
                                        @if ($loop->first)
                                            <option value="{{ $client->id }}" selected>{{ $client->name }}</option>
                                        @else
                                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                                        @endif  
                                    @endforeach
                                </select>
                        </div>
                    </div>
                    <div class="row g-3">    
                        <div class="form-group col-md-2">
                            <label for="pickup_time_begin">Pickup time begin:</label>
                            <input type="datetime-local" id="pickup_time_begin" name="pickup_time_begin" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="pickup_time_end">Pickup time end:</label>
                            <input type="datetime-local" id="pickup_time_end" name="pickup_time_end" class="form-control">
                        </div>
                        <div class="form-group col-md">
                            <label for="pickup_address">Pickup Address</label>
                            <input type="text" id="pickup_address" name="pickup_address" class="form-control">
                        </div>
                    </div> 
                    <div class="row g-3 ">
                        <div class="form-group col-md">
                            <label for="collection_details">Collection Details</label>
                                <textarea id="collection_details" name="collection_details" class="form-control" placeholder="Information about delivery to courier" ></textarea>
                        </div>
                    </div>
                    <div class="row g-3 ">
                        <div class="form-group col-md">
                            <label for="senderContacts">Sender Contacts</label>
                            <input type="text" id="senderContacts" name="senderContacts" class="form-control">
                        </div>
                    </div>    
                </div>

                <hr class="hr hr-blurry" />




                    
                    <div class="row g-3">
                        <div class="form-group col-md-3">
                            <label for="receiver_id">Receiver</label>
                                <input type="text" id="receiver_search" class="form-control" placeholder="Search for a client">
                                <select id="receiver_id" name="receiver_id" class="form-control"  size="3">
                                    @foreach($clients as $client)
                                        @if ($loop->first)
                                            <option value="{{ $client->id }}" selected>{{ $client->name }}</option>
                                        @else
                                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                                        @endif  
                                    @endforeach
                                </select>
                        </div>
                    </div>

                    <div class="row g-3">    
                        <div class="form-group col-md-2">
                            <label for="dropoff_time_begin">Drop off begin:</label>
                            <input type="datetime-local" id="dropoff_time_begin" name="dropoff_time_begin" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="dropoff_time_end">Drop off end:</label>
                            <input type="datetime-local" id="dropoff_time_end" name="dropoff_time_end" class="form-control">
                        </div>
                        <div class="form-group col-md">
                            <label for="delivery_address">Drop off adress:</label>
                            <input type="text" id="delivery_address" name="delivery_address" class="form-control">
                        </div>
                    </div>
                </div>






                <div class="form-group" hidden>
                    <label for="manager_id">Manager</label>
                    <select id="manager_id" name="manager_id" class="form-control" >
                        @foreach($managers as $manager)

                            <option value="{{ $manager->id }}"
                            @if (auth()->user()->id === $manager->id)
                                selected
                            @endif
                            >{{ $manager->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="receiverContacts">Receiver Contacts</label>
                    <input type="text" id="receiverContacts" name="receiverContacts" class="form-control">
                </div>
                <div class="row g-3 ">
                        <div class="form-group col-md">
                            <label for="dropoff_details">DropOff Details</label>
                                <textarea id="dropoff_details" name="dropoff_details" class="form-control" placeholder="Information about delivery to courier" ></textarea>
                        </div>
                    </div>
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Create Job</button>
            </form>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    
    const senderSelect = document.getElementById('sender_id');
    const receiverSelect = document.getElementById('receiver_id');

    const senderContactsInput       = document.getElementById('senderContacts');
    const receiverContactsInput       = document.getElementById('receiverContacts');

    const pickupAddressInput        = document.getElementById('pickup_address');
    const collection_detailsInput   = document.getElementById('collection_details');
    const deliveryAddressInput      = document.getElementById('delivery_address');
    const dropoff_detailsInput      = document.getElementById('dropoff_details');
    document.addEventListener('DOMContentLoaded', function() {
            // This code will run when the page is fully loaded
            // You can set the default pickup address here
            const senderSelectedOption = senderSelect.options[senderSelect.selectedIndex];
            const senderId = senderSelectedOption.getAttribute('value');
            const receiverSelectedOption = receiverSelect.options[senderSelect.selectedIndex];
            const recieverId = receiverSelectedOption.getAttribute('value');
            if (senderId) {                
                // Perform an AJAX request to the server to fetch the address based on the client ID
                fetch(`/get-client-info/${senderId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            pickupAddressInput.value = data.address;
                            senderContactsInput.value = data.senderContacts;
                            collection_detailsInput.value = data.collection_details;
                        }
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
            if (recieverId) {                
                // Perform an AJAX request to the server to fetch the address based on the client ID
                fetch(`/get-client-info/${recieverId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            deliveryAddressInput.value = data.address;
                            receiverContactsInput.value = data.receiverContacts;
                            dropoff_detailsInput.value  = data.dropoff_details;
                        }
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    senderSelect.addEventListener('change', function() {
        
            const selectedOption = senderSelect.options[senderSelect.selectedIndex];
            const clientId = selectedOption.getAttribute('value');
            if (clientId) {
                
                // Perform an AJAX request to the server to fetch the address based on the client ID
                fetch(`/get-client-info/${clientId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            pickupAddressInput.value = data.address;
                            senderContactsInput.value = data.senderContacts;
                            collection_detailsInput.value = data.collection_details;
                        }
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });
    receiverSelect.addEventListener('change', function() {
        const selectedOption = receiverSelect.options[receiverSelect.selectedIndex];
        const clientId = selectedOption.getAttribute('value');
        if (clientId) {
            // Perform an AJAX request to the server to fetch client information based on the client ID
            fetch(`/get-client-info/${clientId}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                            deliveryAddressInput.value = data.address;
                            receiverContactsInput.value = data.receiverContacts;
                            dropoff_detailsInput.value  = data.dropoff_details;
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        }
    });
    
    const clientSearchInput = document.getElementById('client_search');
    const receiverSearchInput = document.getElementById('receiver_search');
    const senderIdSelect = document.getElementById('sender_id');
    const receiverIdSelect = document.getElementById('receiver_id');

        clientSearchInput.addEventListener('input', function() {
            const searchValue = clientSearchInput.value.toLowerCase();
            const options = senderIdSelect.getElementsByTagName('option');

            // Show/hide options based on the search input
            for (let i = 0; i < options.length; i++) {
                const optionText = options[i].textContent.toLowerCase();
                if (optionText.includes(searchValue)) {
                    options[i].style.display = '';
                } else {
                    options[i].style.display = 'none';
                }
            }

            // Show the select dropdown if there are matching results, otherwise hide it
            senderIdSelect.style.display = Array.from(options).some(option => option.style.display !== 'none') ? '' : 'none';
        });
        receiverSearchInput.addEventListener('input', function() {
            const searchValue = receiverSearchInput.value.toLowerCase();
            const options = receiverIdSelect.getElementsByTagName('option');

            // Show/hide options based on the search input
            for (let i = 0; i < options.length; i++) {
                const optionText = options[i].textContent.toLowerCase();
                if (optionText.includes(searchValue)) {
                    options[i].style.display = '';
                } else {
                    options[i].style.display = 'none';
                }
            }

            // Show the select dropdown if there are matching results, otherwise hide it
            receiverIdSelect.style.display = Array.from(options).some(option => option.style.display !== 'none') ? '' : 'none';
        });


        // Handle option selection and populate the search input
        senderIdSelect.addEventListener('change', function() {
            clientSearchInput.value = senderIdSelect.options[senderIdSelect.selectedIndex].textContent;
        });
        receiverIdSelect.addEventListener('change', function() {
            receiverSearchInput.value = receiverIdSelect.options[receiverIdSelect.selectedIndex].textContent;
        });
//=================================================================

</script>
@endsection