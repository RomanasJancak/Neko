@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Clients</h1>
            <button class="btn btn-secondary create-btn">Add new Client</button>        
            <form method="POST" action="{{ route('client.createBackup') }}">
                @csrf
                <button type="submit" class="btn btn-primary">Create Backup</button>
            </form>
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <input type="text" id="search-id" class="form-control" placeholder="Search by ID">
                        <input type="text" id="search-name" class="form-control" placeholder="Search by Name">
                        <input type="text" id="search-address" class="form-control" placeholder="Search by Address">
                    </tr>
                    <tr>
                        <th scope="col" data-column="id">
                            ID
                            <button class="sort-btn" data-sort-field="id" data-sort-order="asc">Sort</button>
                        </th>
                        <th scope="col" data-column="name">
                            Name
                            <button class="sort-btn" data-sort-field="name" data-sort-order="asc">Sort</button>
                        </th>
                        <th scope="col" data-column="address">Billing Address</th>
                        <th scope="col" data-column="pickup_address">PU. Address</th>
                        <th scope="col" data-column="phone">Phone number</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody id="clientsTableBody">
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
                            @if($client->phone !== '')
                                {{$client->phone}}    
                                <a href="tel:{{$client->phone}}"><i class="fa-solid fa-phone"></i></a>    
                                <a href="https://wa.me/{{$client->phone}}" target="_blank"><i class="fa-brands fa-whatsapp"></i></a>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-primary edit-btn" 
                                data-clientid="{{ $client->id }}"
                                onclick="editClient({{ $client->id }})"
                            ><i class="bi bi-pen"></i></button>
                            <button class="btn btn-danger delete-btn" 
                                data-clientid="{{ $client->id }}"
                                onclick="deleteClient({{ $client->id }})"
                            ><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3" id='paginationLinks'>
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
                        <div class="col">
                            <input type="hidden" name="clientid" id="clientid" value="">
                            <label for="nameField">Name : </label>
                            <input type="text" name="name" id="nameField" value="" >
                        </div>
                        <div class="col">
                            <label for="nameField">Short name : </label>
                            <input type="text" name="shortenedName" id="shortenedNameField" value="" >
                        </div>
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
                        <div class="col">
                            <label for="phoneNumberField"><i class="fa-solid fa-phone"></i> : </label>
                            <input type="text" name="phone" id="phoneNumberField" value="" >
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
    function editClient(clientId) {
        const form = document.querySelector('#statusForm');
        if (form) {
            form.setAttribute('action', "{{ route('client.update') }}");
            const routeUrl = `{{ route('getClientInfo', ['clientId' => ':clientId']) }}`.replace(':clientId', clientId);

            fetch(routeUrl)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        document.getElementById('clientid').value = clientId;
                        document.getElementById('nameField').value = data.name;
                        document.getElementById('shortenedNameField').value = data.nickName;

                        document.getElementById('reg-adress-section-adress-country-field').value = data.country;
                        document.getElementById('reg-adress-section-adress-city-field').value = data.city;
                        document.getElementById('reg-adress-section-adress-postalcode-field').value = data.postal_code;
                        document.getElementById('reg-adress-section-adress-addressline-field').value = data.address_line;

                        document.getElementById('pu-adress-section-adress-country-field').value = data.pickup_country;
                        document.getElementById('pu-adress-section-adress-city-field').value = data.pickup_city;
                        document.getElementById('pu-adress-section-adress-postalcode-field').value = data.pickup_postal_code;
                        document.getElementById('pu-adress-section-adress-addressline-field').value = data.pickup_adress_line;

                        document.getElementById('phoneNumberField').value = data.phone;
                    }
                })
                .catch(error => {
                    console.error(error);
                });

            document.getElementById('nameField').readOnly = false;
            document.getElementById('reg-adress-section-adress-country-field').readOnly = false;
            document.getElementById('reg-adress-section-adress-city-field').readOnly = false;
            document.getElementById('reg-adress-section-adress-postalcode-field').readOnly = false;
            document.getElementById('reg-adress-section-adress-addressline-field').readOnly = false;  
            document.getElementById('pu-adress-section-adress-country-field').readOnly = false;
            document.getElementById('pu-adress-section-adress-city-field').readOnly = false;
            document.getElementById('pu-adress-section-adress-postalcode-field').readOnly = false;
            document.getElementById('pu-adress-section-adress-addressline-field').readOnly = false;
            document.getElementById('phoneNumberField').readOnly = false;

            const submitButton = document.getElementById('submitform');
            submitButton.innerHTML = "Update";
        }
        $('#modalWindow').modal('show');
    }

    function deleteClient(clientId) {
        const form = document.querySelector('#statusForm');
        if (form) {
            form.setAttribute('action', "{{ route('client.delete') }}");
            const routeUrl = `{{ route('getClientInfo', ['clientId' => ':clientId']) }}`.replace(':clientId', clientId);

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

                        document.getElementById('phoneNumberField').value = data.phone;
                    }
                })
                .catch(error => {
                    console.error(error);
                });

            document.getElementById('nameField').readOnly = true;
            document.getElementById('reg-adress-section-adress-country-field').readOnly = true;
            document.getElementById('reg-adress-section-adress-city-field').readOnly = true;
            document.getElementById('reg-adress-section-adress-postalcode-field').readOnly = true;
            document.getElementById('reg-adress-section-adress-addressline-field').readOnly = true;
            document.getElementById('pu-adress-section-adress-country-field').readOnly = true;
            document.getElementById('pu-adress-section-adress-city-field').readOnly = true;
            document.getElementById('pu-adress-section-adress-postalcode-field').readOnly = true;
            document.getElementById('pu-adress-section-adress-addressline-field').readOnly = true;
            document.getElementById('phoneNumberField').readOnly = true;

            const submitButton = document.getElementById('submitform');
            submitButton.innerHTML = "Delete";
        }
        $('#modalWindow').modal('show');
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.create-btn').forEach(button => {
            button.addEventListener('click', () => {
                const form = document.querySelector(`#statusForm`);
                if (form) {
                    document.getElementById('nameField').readOnly = false;
                    form.setAttribute('action', "{{ route('client.store') }}");
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
                // Handle the response based on the message
            };

            // Send the request
            xhr.send(formData);
            $('#modalWindow').modal('hide');
        });

        const searchInputs = [
            { id: 'search-id', field: 'id' },
            { id: 'search-name', field: 'name' },
            { id: 'search-address', field: 'address' }
        ];

        searchInputs.forEach(input => {
            const inputElement = document.getElementById(input.id);

            inputElement.addEventListener('input', function() {
                fetchClients();
            });
        });

        function fetchClients(page = 1) {
            const id = document.getElementById('search-id').value;
            const name = document.getElementById('search-name').value;
            const address = document.getElementById('search-address').value;
            const sortField = document.querySelector('.sort-btn.active')?.dataset.sortField || 'id';
            const sortOrder = document.querySelector('.sort-btn.active')?.dataset.sortOrder || 'asc';

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `{{ route('client.fetch') }}?id=${id}&name=${name}&address=${address}&sortField=${sortField}&sortOrder=${sortOrder}&page=${page}`, true);
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const data = JSON.parse(xhr.responseText);
                    document.getElementById('clientsTableBody').innerHTML = '';
                    console.log(data);
                    data.clients.data.forEach(client => {
                        const clientRow = `
                            <tr>
                                <th scope="row">${client.id}</th>
                                <td>${client.name}</td>
                                <td>${client.address_line}<br>
                                    ${client.postal_code}<br>
                                    ${client.city},${client.country}
                                </td>
                                <td>${client.pickup_adress_line}<br>
                                    ${client.pickup_postal_code}<br>
                                    ${client.pickup_city},${client.pickup_country}
                                </td>
                                <td>
                                    ${client.phone !== '' ? `${client.phone}
                                    <a href="tel:${client.phone}"><i class="fa-solid fa-phone"></i></a>
                                    <a href="https://wa.me/${client.phone}" target="_blank"><i class="fa-brands fa-whatsapp"></i></a>` : ''}
                                </td>
                                <td>
                                    <button class="btn btn-primary edit-btn" 
                                        data-clientid="${client.id}"
                                        onclick="editClient(${client.id})"
                                    ><i class="bi bi-pen"></i></button>
                                    <button class="btn btn-danger delete-btn" 
                                        data-clientid="${client.id}"
                                        onclick="deleteClient(${client.id})"
                                    ><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        `;
                        
                        document.getElementById('clientsTableBody').insertAdjacentHTML('beforeend', clientRow);
                    });
                    paginationLinks = document.getElementById('paginationLinks');
                    paginationLinks.innerHTML = data.links;
                }
            };
            xhr.send();
        }

        document.addEventListener('click', function(event) {
            if (event.target.closest('.pagination a')) {
                event.preventDefault();
                const page = event.target.getAttribute('href').split('page=')[1];
                fetchClients(page);
            }

            if (event.target.closest('.sort-btn')) {
                const button = event.target.closest('.sort-btn');
                const sortField = button.dataset.sortField;
                const currentOrder = button.dataset.sortOrder;
                const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';

                document.querySelectorAll('.sort-btn').forEach(btn => {
                    btn.classList.remove('active');
                    btn.dataset.sortOrder = 'asc';
                });

                button.classList.add('active');
                button.dataset.sortOrder = newOrder;
                
                fetchClients();
            }
        });

        fetchClients();
    });
</script>
@endsection
