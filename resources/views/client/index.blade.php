@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Clients</h1><button class="btn btn-secondary create-btn" >Add new Client</i></button>        
            <form method="POST" action="{{ route('client.createBackup') }}">
                @csrf
                <button type="submit" class="btn btn-primary">Create Backup</button>
            </form>
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th scope="col" data-column="id">ID</th>
                        <th scope="col" data-column="name">Name</th>
                        <th scope="col" data-column="adress">Billing Adress</th>
                        <th scope="col" data-column="adress">PU. Adress</th>
                        <th scope="col" >Actions</th>
                    </tr>
                </thead>
                <tbody id="statusesTableBody">
                    @foreach($clients as $client)
                    <tr>
                        <th scope="row">{{ $client->id }}</th>
                        <td>{{ $client->name }}</td>
                        <td>{{$client->address_line}}<br>
                            {{$client->postal_code}}<br>
                            {{$client->city}},{{ $client->country }}         
                        </td>
                        <td>{{$client->pickup_adress_line}}<br>
                            {{$client->pickup_postal_code}}<br>
                            {{$client->pickup_city}},{{ $client->pickup_country }} 
                            
                        </td>
                        <td>
                            <button class="btn btn-primary edit-btn" 
                                data-clientid="{{ $client->id }}"
                            ><i class="bi bi-pen"></i></button>
                            <button class="btn btn-danger delete-btn" 
                                data-clientid="{{ $client->id }}"
                            ><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {!! $clients->links() !!}
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="modalWindow" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <form id="statusForm" action="" method="POST">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="clientid" id="clientid" value="">
                        <label for="nameField">Name : </label>
                        <input type="text" name="name" id="nameField" value="none" >
                    </div>
                    <div class="row">
                        <div class="col form-group" id="reg-adress-section">
                            <div class="row">
                                <label for="reg-adress-section-adress-country-field">Registration adress : </label>
                                <input type="text" name="address_line" id="reg-adress-section-adress-addressline-field" value="" placeholder="Address line">
                                <input type="text" name="postal_code" id="reg-adress-section-adress-postalcode-field" value="" placeholder="Postal code">
                                <input type="text" name="city" id="reg-adress-section-adress-city-field" value="" placeholder="City">
                                <input type="text" name="country" id="reg-adress-section-adress-country-field" value="" placeholder="Country">    
                            </div>
                        </div>
                        <div class="col form-group" id="pu-adress-section">
                            <div class="row">
                                <label for="pu-adress-section-adress-country-field">Pickup adress : </label>
                                <input type="text" name="pickup_adress_line" id="pu-adress-section-adress-addressline-field" value="" placeholder="Address line">
                                <input type="text" name="pickup_postal_code" id="pu-adress-section-adress-postalcode-field" value="" placeholder="Postal code">                                
                                <input type="text" name="pickup_city" id="pu-adress-section-adress-city-field" value="" placeholder="City">
                                <input type="text" name="pickup_country" id="pu-adress-section-adress-country-field" value="" placeholder="Country">                                                                
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <button type="button" id="submitform" data-option="create" class="btn btn-primary">Apply</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.edit-btn').forEach(button => {

        button.addEventListener('click', () => {
            const clientId          =   button.dataset.clientid;
            const form = document.querySelector(`#statusForm`);
            if (form) {
                form.setAttribute('action', "{{ route('client.update') }}");
                const routeUrl = `{{ route('getClientInfo', ['clientId' => ':clientId']) }}`.replace(':clientId', clientId);
                //fetch(`/get-client-info/${clientId}`)
                fetch(routeUrl)
                        .then(response => response.json())
                        .then(data => {
                            if (data) {
                                document.getElementById('clientid').value = clientId;
                                document.getElementById('nameField').value = data.name;

                                document.getElementById('reg-adress-section-adress-country-field').value = data.country;
                                document.getElementById('reg-adress-section-adress-city-field').value = data.city;
                                document.getElementById('reg-adress-section-adress-postalcode-field').value = data.postal_code;
                                document.getElementById('reg-adress-section-adress-addressline-field').value = data.address_line;
                                
                                document.getElementById('pu-adress-section-adress-country-field').value = data.pickup_country;
                                document.getElementById('pu-adress-section-adress-city-field').value = data.pickup_city;
                                document.getElementById('pu-adress-section-adress-postalcode-field').value = data.pickup_postal_code;
                                document.getElementById('pu-adress-section-adress-addressline-field').value = data.pickup_adress_line;
                            }
                        })
                        .catch(error => {
                            console.error(error);
                        });
                
                
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "Update";
            }
            $('#modalWindow').modal('show');
        });
    });
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', () => {
            const clientid = button.dataset.clientid;
            const statusName = button.dataset.name;
            const statusColorMain   =   button.dataset.colormain;
            const statusColorPickup   =   button.dataset.colorpickup;
            const statusColorDropoff   =   button.dataset.colordropoff;
            const form = document.querySelector(`#statusForm`);
            if (form) {
                form.setAttribute('action', "{{ route('status.delete') }}");
                document.getElementById('clientid').value = clientid;
                document.getElementById('nameField').value = statusName;
                document.getElementById('nameField').readOnly = true;
                document.getElementById('colorPicker-main').value = statusColorMain;
                document.getElementById('colorPicker-pickup').value = statusColorPickup;
                document.getElementById('colorPicker-dropoff').value = statusColorDropoff;
                document.getElementById('colorPicker-main').disabled = true;
                document.getElementById('colorPicker-pickup').disabled = true;
                document.getElementById('colorPicker-dropoff').disabled = true;
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-trash'></i>";
            }
            $('#modalWindow').modal('show');
        });
    });
    document.querySelectorAll('.create-btn').forEach(button => {
        button.addEventListener('click', () => {
            const form = document.querySelector(`#statusForm`);
            if (form) {
                document.getElementById('nameField').readOnly = false;
                document.getElementById('colorPicker-main').disabled = false;
                document.getElementById('colorPicker-pickup').disabled = false;
                document.getElementById('colorPicker-dropoff').disabled = false;
                form.setAttribute('action', "{{ route('status.store') }}");
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-save'></i>";
            }
            $('#modalWindow').modal('show');
        });
    });
    document.getElementById('submitform').addEventListener('click', function() {
        // Get form data
        const form = document.getElementById('statusForm');
        const formData = new FormData(form);

        //console.log(formData.get('workloadid'));

        // Create a new XMLHttpRequest object
        const xhr = new XMLHttpRequest();

        // Define the request type, URL, and set up the request
        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('X-CSRF-Token', '{{ csrf_token() }}'); // Replace with your CSRF token if not using Blade

        // Handle the response
        xhr.onload = function() {
            // Process the response if needed
            console.log(xhr.responseText);
            parsedMessage = JSON.parse(xhr.responseText).message;
            //console.log(parsedMessage);
            if(parsedMessage === 'deleted'){
                //document.getElementById('workload-'+formData.get('workloadid')).remove();
            }
            if(parsedMessage === 'updated'){
            }
            if(parsedMessage === 'created'){
            }

        };

        // Send the request
        xhr.send(formData);
        $('#modalWindow').modal('hide');
    });
    function getClientInfo(clientId){

    }
});

</script>
@endsection
