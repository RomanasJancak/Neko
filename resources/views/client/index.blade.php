@extends('layouts.app')

@section('content')
{{-- dd($clients[0]->createAndAddNewAddress("name","type","addr1","addr2","N7 0LT","city","country",)) --}}
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

<!-- Client windwo Modal -->
<div class="modal" id="modalWindow" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="ModalLabel">Edit Client</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form class="row" id="statusForm" action="" method="POST">
                        @csrf
                        <input type="hidden" name="clientid" id="clientid" value="">
                        <div class="col-lg-4 order-lg-1">
                            <div class="mb-0">
                                <label for="nameField" class="form-label">Name:</label>
                                <input type="text" name="clientname" id="nameField" class="form-control" value="">
                            </div>
                            <div class="mb-3">
                                <label for="shortenedNameField" class="form-label">Short name:</label>
                                <input type="text" name="shortenedName" id="shortenedNameField" class="form-control" value="">
                            </div>
                            <div class="mb-3">
                                <label for="reg-adress-section-adress-addressline-field" class="form-label">Registration address:</label>
                                <input type="text" name="reg-addr-address_line" id="reg-adress-section-adress-addressline-field" class="form-control mb-2" value="" placeholder="Address line">
                                <input type="text" name="reg-addr-postal_code" id="reg-adress-section-adress-postalcode-field" class="form-control mb-2" value="" placeholder="Postal code">
                                <input type="hidden" name="reg-addr-city" id="reg-adress-section-adress-city-field" class="form-control mb-2" value="London" placeholder="City">
                                <input type="hidden" name="reg-addr-country" id="reg-adress-section-adress-country-field" class="form-control mb-2" value="United Kingdom" placeholder="Country">
                            </div>
                            <div class="mb-3">
                                <label for="phoneNumberField" class="form-label"><i class="fa-solid fa-phone"></i>:</label>
                                <input type="text" name="phone" id="phoneNumberField" class="form-control" value="">
                            </div>
                            <div class="mb-3">
                                <button type="button" class="btn btn-success btn-sm" id="button-add-address">
                                    <i class="fa fa-plus-circle"></i> Add address
                                </button>
                                <button type="button" class="btn btn-info btn-sm" id="button-view-packages">
                                    View packages
                                </button>
                                <button type="button" class="btn btn-info btn-sm" id="button-view-addons">
                                    View AddOns
                                </button>
                            </div>
                            <div class="mb-3">
                                <button type="button" id="submitform" data-option="create" class="btn btn-primary">Apply</button>
                            </div>
                        </div>
                        <div class="col-lg-8 order-lg-2 mb-3" id="container-addresses"></div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Packages view window-->
<div class="modal" id="modalWindow-packages" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="ModalLabel">Packages</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- End of Packages view window-->
<!-- AddOns view window-->
<div class="modal" id="modalWindow-addons" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="ModalLabel">AddOns</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="addonsContainer">
                <div class="col" id="addonsContainer-distanceRules">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Add new package from selection modal window-->
<div class="modal" id="modalWindow-packages-addNewFromList" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="ModalLabel">Add new Package</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="packageTypeSelect">Select Package Type:</label>
                    <select id="packageTypeSelect" class="form-control">
                        <option value="0" disabled selected>Select a package type</option>
                    </select>
                </div>
                <button class="btn btn-secondary" id="button_addSelectedPackageType" disabled>Add selected packageType</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> 
<!-- End of Add new package from selection modal window-->
@endsection

@section('scripts')
<script>
    function deleteAddress(address_id = null) {

        

        const container = document.getElementById('container-addresses');
        const addressElement = document.querySelector(`input[name="address_id[]"][value="${address_id}"]`);
        if(addressElement) {
            container.removeChild(addressElement.parentElement.parentElement);
        }
        if(address_id) {
            const routeUrl = `{{ route('address.delete', ['address' => ':addressId']) }}`.replace(':addressId', address_id);
            fetch(routeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response =>{
                return response.json();
            })    .then(data => {


            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting address. Please try again.');
            });
        }else{
            container.removeChild(event.target.parentElement.parentElement.parentElement);
        }
    }
    function populateWithAddresses(addresses){
        const container = document.getElementById('container-addresses');
        //<div class="col address-input-field"><input type="text" name="type[]" class="form-control" value="${address.type}" placeholder="Type"></div>
        container.innerHTML = '';
        addresses.forEach(address => {
        const addressRow = `
        <div class="row">
            <div class="col address-input-field" style="display: none;"><input type="hidden" name="address_id[]" class="form-control" value="${address.id}"></div>
            <div class="col address-input-field"><input style="font-size: 0.8em;" type="text" name="name[]" class="form-control" value="${address.name}" placeholder="Name" ></div>
            
            <div class="col address-input-field"><input type="text" name="address_line_1[]" class="form-control" value="${address.address_line_1}" placeholder="Address line 1"></div>
            <div class="col address-input-field"><input type="text" name="address_line_2[]" class="form-control" value="${address.address_line_2}" placeholder="Address line 1"></div>
            <div class="col address-input-field"><input type="text" name="postal_code[]" class="form-control" value="${address.postal_code}" placeholder="Postal code"></div>
            <div class="col address-input-field"><input type="text" name="city[]" class="form-control" value="${address.city}" placeholder="City"></div>
            <div class="col address-input-field"><input type="text" name="country[]" class="form-control" value="${address.country}" placeholder="Country"></div>
            <div class="col address-input-field"><button type="button" class="btn btn-danger btn-xs text-danger" style="background: none; border: none;" id='button-remove-address' idofaddress="${address.id}" onclick="deleteAddress(${address.id})">
                <i class="fa fa-minus-circle" aria-hidden="true" style="color: inherit;"></i>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', addressRow);
        });
    }
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

                        document.getElementById('phoneNumberField').value = data.phone;
                        populateWithAddresses(data.addresses);
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
    function fetchPackageTypes(clientId){
        const routeUrl = window.ROUTES.WEB.CLIENT.FETCHPACKAGETYPES.replace(':id', clientId);
        fetch(routeUrl)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    populate_Container_withPackageTypes(data.packageTypes);
                }
            })
            .catch(error => {
                console.error(error);
            });
    }
    function submitUpdatedDistanceRules(){
        list = document.querySelectorAll('.list-group-item-distance-rules');
        const rulesArray = Array.from(list).map(item => {
            const id = item.id.split('-')[3];
            const name = item.querySelector('input.n_ames').value;
            const price = item.querySelector('input.p_rices').value;
            const display_name = item.querySelector('input.dn_ames').value;
            const begin_date = item.querySelector('input.begin_dates').value;
            const end_date = item.querySelector('input.end_dates').value;
            return { id,name, price, display_name, end_date, begin_date };
        });
        const routeUrl = window.ROUTES.WEB.CLIENT.UPDATEDISTANCERULES;
        fetch(routeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                id: document.getElementById('clientid').value,
                rules: rulesArray
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data){
                console.log(data);
                //fetchAddOns(document.getElementById('clientid').value);
            }
        })
        .catch(error => {
            console.error(error);
        });
    }
    function makeDistanceRulesEditable(rules){
        list = document.querySelectorAll('.list-group-item-distance-rules');
        list.forEach(item => {
            const itemId = item.id.split('-')[3];
            const rule = rules.find(rule => rule.id == itemId);
            item.innerHTML = '<input type="text" class="n_ames form-control " value="'+rule.name+'">';
            item.innerHTML += '<input type="text" class="p_rices form-control w-25" value="'+rule.price+'">';
            item.innerHTML += '<input type="text" class="dn_ames form-control " value="'+rule.display_name+'">';
            item.innerHTML += '<input type="text" class="begin_dates form-control " value="'+rule.begin_date+'">';
            item.innerHTML += '<input type="text" class="end_dates form-control " value="'+rule.end_date+'">';
        });
        editButton = document.getElementById('edit-distance-rules');
        const saveButton = editButton.cloneNode(true);
        saveButton.className = 'btn bi bi-save';
        editButton.replaceWith(saveButton);
        saveButton.addEventListener('click', function(e) {
            e.preventDefault();
            submitUpdatedDistanceRules();
        });       
    }
    function displayDistanceRules(rules){
        
        rules = Object.keys(rules).map(key => rules[key]);
        rules.sort((a, b) => a.name.localeCompare(b.name));
        const distanceRules = document.getElementById('addonsContainer-distanceRules');
        const distanceRulesContainer = document.createElement('div');
        distanceRulesContainer.className = 'alert alert-info';
        distanceRulesContainer.role = 'alert';
        const strongElement = document.createElement('strong');
        const textNode = document.createTextNode("Distance rules:");
        strongElement.appendChild(textNode);
        const editbutton = document.createElement('i');
        editbutton.className = 'btn bi bi-pencil';
        editbutton.id = 'edit-distance-rules';
        editbutton.addEventListener('click', function() {
            makeDistanceRulesEditable(rules);
        });
        distanceRulesContainer.appendChild(strongElement);
        distanceRulesContainer.appendChild(editbutton);   
        const ulElement = document.createElement('ul');
        rules.forEach(rule => {
            const liElement = document.createElement('li');
            liElement.id = 'list-group-item-'+rule.id;
            liElement.className = 'list-group-item-distance-rules d-flex';
            //liElement.style = 'display: flex; justify-content: space-between;';
            if (rules.indexOf(rule) < rules.length - 1) {
                
                const from = rule.name.split('-')[2]; 
                const to = rules[rules.indexOf(rule)+1].name.split('-')[2]; 
                const price = parseFloat(rule.price/100);
                const step = rule.name.split('-')[4];
                const price_based_text = price === 0 ? 'Free' : `each ${step} mile £${price} `;
                liElement.innerHTML = `${from} - ${to} miles : ${price_based_text}`;
            }else{
                const from = rule.name.split('-')[2]; 
                const price = parseFloat(rule.price/100);
                const step = rule.name.split('-')[4]; 
                liElement.innerHTML = `${from}+ miles : each ${step} mile £${price} `;
            }
            ulElement.appendChild(liElement);
        });
        distanceRulesContainer.appendChild(ulElement);
        distanceRules.innerHTML = '';
        distanceRules.appendChild(distanceRulesContainer);
        // distanceRules.innerHTML = `
        //     <div class="alert alert-info" role="alert">
        //         <strong>Distance rules:</strong> 
        //         <ul>
        //             <li>0-5 miles: £10</li>
        //             <li>5-10 miles: £20</li>
        //             <li>10-15 miles: £30</li>
        //             <li>15+ miles: £40</li>
        //         </ul>
        //     </div>
        // `;
    }
    function fetchAddOns(clientId){
        const routeUrl = window.ROUTES.WEB.CLIENT.FETCHADDONS.replace(':id', clientId);
        fetch(routeUrl)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    console.log(data['addOns']);
                    displayDistanceRules(data.addOns.distanceRules);
                    //populate_Container_withPackageTypes(data.packageTypes);
                }
            })
            .catch(error => {
                console.error(error);
            });
    }
    function populate_Container_withPackageTypes(packageTypes){
        const modalBody = document.getElementById('modalWindow-packages').querySelector('.modal-body');
        modalBody.innerHTML = '';
        const container = document.createElement('div');
        container.className = 'container-fluid d-flex flex-wrap';
        modalBody.appendChild(container);
        container.innerHTML = '';
        packageTypes.forEach(packageType => {
            const packageTypeCard = `
                <div class="card m-2" style="flex: 1 1 calc(33.333% - 1rem); max-width: calc(33.333% - 1rem);">
                    <div class="card-header">
                        ${packageType.name} <button class="btn btn-danger">
                                                <i class="fa fa-circle-minus packageTypeRemovalButton" data-packagetypeid = ${packageType.id} aria-hidden="true" style="color: inherit;"></i>
                                            </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-auto">
                                <p><strong>Price:</strong> ${packageType.price}</p>
                            </div>
                            <div class="col-auto">
                                <p><strong>Max before oversize :</strong> ${packageType.baseQuantityThreshold}</p>
                            </div>
                            <div class="col-auto">
                                <p><strong>Maximum allowed to order in a job :</strong> ${packageType.maxQuantityThreshold}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', packageTypeCard);
        });
        document.querySelectorAll('.packageTypeRemovalButton').forEach(button => {
            button.addEventListener('click', function(e) {
                const clientId = document.getElementById('clientid').value;
                const packageTypeId = e.target.getAttribute('data-packagetypeid');
                if(clientId){
                    removePackageTypeFromClient(clientId, packageTypeId);
                }
            });
        });
        const addNewPackageCard = document.createElement('div');
        addNewPackageCard.className = 'card m-2';
        addNewPackageCard.style.flex = '1 1 calc(33.333% - 1rem)';
        addNewPackageCard.style.maxWidth = 'calc(33.333% - 1rem)';

        const cardHeader = document.createElement('div');
        cardHeader.className = 'card-header';
        cardHeader.textContent = 'Add new package';

        const cardBody = document.createElement('div');
        cardBody.className = 'card-body';

        const addButton = document.createElement('button');
        addButton.className = 'btn btn-primary';
        addButton.innerHTML = '<i class="fa fa-plus-circle" aria-hidden="true" style="color: inherit;"></i>';

        cardBody.appendChild(addButton);
        addNewPackageCard.appendChild(cardHeader);
        addNewPackageCard.appendChild(cardBody);

        container.appendChild(addNewPackageCard);
        addClickListenerToAddNewPackageButton(addButton);
    }
    function populateSelectionOfUnassignedPackageTypes(packageTypes){
        const selectELement = document.getElementById('packageTypeSelect');
        selectELement.innerHTML = '<option value="" disabled selected>Select a package type</option>';
        packageTypes.forEach(packageType => {
            const option = document.createElement('option');
            option.value = packageType.id;
            option.textContent = packageType.name;
            selectELement.appendChild(option);
        });
    }
    function addClickListenerToAddNewPackageButton(button) {
            button.addEventListener('click', function(e){
                const clientId = document.getElementById('clientid').value;
                fetch_UnassignedPackageTypes(clientId);
            });
    }
    function fetch_UnassignedPackageTypes(clientId){
        const routeUrl = window.ROUTES.WEB.CLIENT.FETCHUNASSIGNEDPACKAGETYPES.replace(':id', clientId);
        fetch(routeUrl)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    populateSelectionOfUnassignedPackageTypes(data.packageTypes);
                    $('#modalWindow-packages-addNewFromList').modal('show');
                }
            })
            .catch(error => {
                console.error(error);
            });
    }
    function removePackageTypeFromClient(clientId, packageTypeId){
        const routeUrl = window.ROUTES.WEB.CLIENT.REMOVEPACKAGETYPE;
        fetch(routeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                    package_type_id: packageTypeId,
                    client_id : clientId
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data){
                fetchPackageTypes(clientId);
            }
        })
        .catch(error => {
            console.error(error);
        });
    }
document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('button_addSelectedPackageType').setAttribute('disabled', 'disabled');
        document.getElementById('button-add-address').addEventListener('click', function(e) {
            e.preventDefault();
            //<div class="col address-input-field"><input type="text" name="type[]" class="form-control" placeholder="Type"></div>
            const container = document.getElementById('container-addresses');
            const address = `
                <div class="row">
                    <div class="col address-input-field" style="display: none;"><input type="hidden" name="address_id[]" class="form-control"></div>
                    <div class="col address-input-field"><input type="text" name="name[]" class="form-control" placeholder="Name"></div>
                    
                    <div class="col address-input-field"><input type="text" name="address_line_1[]" class="form-control" placeholder="Address line 1"></div>
                    <div class="col address-input-field"><input type="text" name="address_line_2[]" class="form-control" placeholder="Address line 2"></div>
                    <div class="col address-input-field"><input type="text" name="postal_code[]" class="form-control" placeholder="Postal code"></div>
                    <div class="col address-input-field"><input type="text" name="city[]" class="form-control" placeholder="City"></div>
                    <div class="col address-input-field"><input type="text" name="country[]" class="form-control" placeholder="Country"></div>
                    <div class="col address-input-field"><button type="button" class="btn btn-danger btn-xs text-danger" style="background: none; border: none;" id='button-remove-address' idofaddress="" onclick="deleteAddress()">
                        <i class="fa fa-minus-circle" aria-hidden="true" style="color: inherit;"></i>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', address);
        });
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
        document.getElementById('button-view-packages').addEventListener('click',function(e){
            const clientId = document.getElementById('clientid').value;
            fetchPackageTypes(clientId);
            $('#modalWindow-packages').modal('show');
        });
        document.getElementById('button-view-addons').addEventListener('click',function(e){
            const clientId = document.getElementById('clientid').value;
            fetchAddOns(clientId);
            $('#modalWindow-addons').modal('show');
        });
        document.getElementById('submitform').addEventListener('click', function() {
            // Get form data
            const form = document.getElementById('statusForm');
            const formData = new FormData(form);

            // Create a new XMLHttpRequest object
            const xhr = new XMLHttpRequest();

            // Define the request type, URL, and set up the request
            xhr.open('POST', form.action, true);
            xhr.setRequestHeader('X-CSRF-Token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            // Handle the response
            xhr.onload = function() {
                // Process the response if needed
                //parsedMessage = JSON.parse(xhr.responseText).message;
                // Handle the response based on the message
            };

            // Send the request
            xhr.send(formData);
            $('#modalWindow').modal('hide');
        });
        document.getElementById('packageTypeSelect').addEventListener('change', function(e){
            console.log('change');
            const button = document.getElementById('button_addSelectedPackageType');
            if(e.target.value){
                button.removeAttribute('disabled');
            }else{
                button.setAttribute('disabled', 'disabled');
            }
        });
        document.getElementById('button_addSelectedPackageType').addEventListener('click', function(e){
            const clientId = document.getElementById('clientid').value;
            const packageTypeId = document.getElementById('packageTypeSelect').value;
            const routeUrl = window.ROUTES.WEB.CLIENT.ADDPACKAGETYPE;
            fetch(routeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    package_type_id: packageTypeId,
                    client_id : clientId
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data){
                    fetchPackageTypes(clientId);
                }
            })
            .catch(error => {
                console.error(error);
            });
            $('#modalWindow-packages-addNewFromList').modal('hide');
            button.setAttribute('disabled', 'disabled');
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
