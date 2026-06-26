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
                    @include('client.partials.info-window')
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- AddOns view window-->
<div class="modal" id="modalWindow-addons" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="ModalLabel">AddOns</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row" id="addonsContainer">
                <div class="col-auto" id="addonsContainer-distanceRules">
                </div>
                <div class="col-auto" id="addonsContainer-weightRules">
                </div>
                <div class="col-auto" id="addonsContainer-timingRules">
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
            const addressNode = addressElement.closest('.address-card-item') || addressElement.closest('.row');
            if (addressNode && container.contains(addressNode)) {
                container.removeChild(addressNode);
            }
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
    function editClient(clientId) {
        if (window.openClientFormForEdit) {
            window.openClientFormForEdit({
                clientId,
                formAction: "{{ route('client.update') }}",
                submitButtonText: 'Update',
                modalSelector: '#modalWindow',
            });
        }
    }
    function deleteClient(clientId) {
        if (window.openClientFormForDelete) {
            window.openClientFormForDelete({
                clientId,
                formAction: "{{ route('client.delete') }}",
                submitButtonText: 'Delete',
                modalSelector: '#modalWindow',
            });
        }
    }

    function submitUpdateWeightRules(){
        list = document.querySelectorAll('.list-group-item-weight-rules');
        const rulesArray = Array.from(list).map(item => {
            const id = item.id.split('-')[3];
            const name = item.querySelector('input.n_ames').value;
            const price = item.querySelector('input.p_rices').value;
            const display_name = item.querySelector('input.dn_ames').value;
            const begin_date = item.querySelector('input.begin_dates').value;
            const end_date = item.querySelector('input.end_dates').value;
            return { id,name, price, display_name, end_date, begin_date };
        });
        const routeUrl = window.ROUTES.WEB.CLIENT.UPDATEWEIGHTRULES;
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
    function makeWeightRulesEditable(rules){
        list = document.querySelectorAll('.list-group-item-weight-rules');
        list.forEach(item => {
            const itemId = item.id.split('-')[3];
            const rule = rules.find(rule => rule.id == itemId);
            item.innerHTML = '<input type="text" class="n_ames form-control " value="'+rule.name+'">';
            item.innerHTML += '<input type="text" class="p_rices form-control w-25" value="'+rule.price+'">';
            item.innerHTML += '<input type="text" class="dn_ames form-control " value="'+rule.display_name+'">';
            item.innerHTML += '<input type="text" class="begin_dates form-control " value="'+rule.begin_date+'">';
            item.innerHTML += '<input type="text" class="end_dates form-control " value="'+rule.end_date+'">';
        });
        editButton = document.getElementById('edit-weight-rules');
        const saveButton = editButton.cloneNode(true);
        saveButton.className = 'btn bi bi-save';
        editButton.replaceWith(saveButton);
        saveButton.addEventListener('click', function(e) {
            e.preventDefault();
            submitUpdateWeightRules();
        });       
    }
    function displayTimingRules(rules){
        rules = Object.keys(rules).map(key => {
            const rule = rules[key];
            rule.size = parseInt(rule.name.split('-')[3], 10);
            rule.type = rule.name.split('-')[1];
            return rule;
        });
        const ruleSet = Object.keys(rules.reduce((groupedRules, rule) => {
            const type = rule.type;
            if (!groupedRules[type]) {
            groupedRules[type] = [];
            }
            groupedRules[type].push(rule);
            return groupedRules;
        }, {})).map(type => ({
            type: type,
            rules: rules.filter(rule => rule.type === type)
        }));
        ruleSet.forEach(ruleSet => {
            const rules = ruleSet.rules;
            rules.sort((a, b) => a.size - b.size);
            console.log(rules);
            const distanceRules = document.getElementById('addonsContainer-timingRules');
            const distanceRulesContainer = document.createElement('div');
            distanceRulesContainer.className = 'alert alert-info';
            distanceRulesContainer.role = 'alert';
            const strongElement = document.createElement('strong');
            const textNode = document.createTextNode(""+ruleSet.type+" timing rules:");
            strongElement.appendChild(textNode);
            const editbutton = document.createElement('i');
            editbutton.className = 'btn bi bi-pencil';
            editbutton.id = 'edit-timing-rules';
            editbutton.addEventListener('click', function() {
                makeDistanceRulesEditable(rules);
            });
            distanceRulesContainer.appendChild(strongElement);
            distanceRulesContainer.appendChild(editbutton);
            const ulElement = document.createElement('ul');
            rules.forEach(rule => {
                //console.log(rule);
                const liElement = document.createElement('li');
                liElement.id = 'list-group-item-'+rule.id;
                liElement.className = 'list-group-item-timing-rules d-flex';
                if (rules.indexOf(rule) < rules.length - 1) {
                    const from = rule.name.split('-')[3]; 
                    const to = rules[rules.indexOf(rule)+1].name.split('-')[3]; 
                    const price = parseFloat(rule.price/100);
                    const step = rule.name.split('-')[4];
                    const price_based_text = price === 0 ? 'Free' : `each ${step} minutes £${price} `;
                    liElement.innerHTML = `${from} - ${to} minutes : ${price_based_text}`;
                }else{
                    const from = rule.name.split('-')[2]; 
                    const price = parseFloat(rule.price/100);
                    const step = rule.name.split('-')[4]; 
                    liElement.innerHTML = `${from}+ minutes : each ${step} minutes £${price} `;
                }
                ulElement.appendChild(liElement);
            });
            distanceRulesContainer.appendChild(ulElement);            
            distanceRules.appendChild(distanceRulesContainer);   
        });
    }
    function displayWeighteRules(rules){
        rules = Object.keys(rules).map(key => rules[key]);
        rules.sort((a, b) => a.name.localeCompare(b.name));
        const distanceRules = document.getElementById('addonsContainer-weightRules');
        const distanceRulesContainer = document.createElement('div');
        distanceRulesContainer.className = 'alert alert-info';
        distanceRulesContainer.role = 'alert';
        const strongElement = document.createElement('strong');
        const textNode = document.createTextNode("Weight rules:");
        strongElement.appendChild(textNode);
        const editbutton = document.createElement('i');
        editbutton.className = 'btn bi bi-pencil';
        editbutton.id = 'edit-weight-rules';
        editbutton.addEventListener('click', function() {
            makeWeightRulesEditable(rules);
        });
        distanceRulesContainer.appendChild(strongElement);
        distanceRulesContainer.appendChild(editbutton);   
        const ulElement = document.createElement('ul');
        rules.forEach(rule => {
            const liElement = document.createElement('li');
            liElement.id = 'list-group-item-'+rule.id;
            liElement.className = 'list-group-item-weight-rules d-flex';
            if (rules.indexOf(rule) < rules.length - 1) {
                
                const from = rule.name.split('-')[2]; 
                const to = rules[rules.indexOf(rule)+1].name.split('-')[2]; 
                const price = parseFloat(rule.price/100);
                const step = rule.name.split('-')[4];
                const price_based_text = price === 0 ? 'Free' : `each ${step} kg £${price} `;
                liElement.innerHTML = `${from} - ${to} kg : ${price_based_text}`;
            }else{
                const from = rule.name.split('-')[2]; 
                const price = parseFloat(rule.price/100);
                const step = rule.name.split('-')[4]; 
                liElement.innerHTML = `${from}+ kg : each ${step} kg £${price} `;
            }
            ulElement.appendChild(liElement);
        });
        distanceRulesContainer.appendChild(ulElement);
        distanceRules.innerHTML = '';
        distanceRules.appendChild(distanceRulesContainer);
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
    }
    function fetchAddOns(clientId){
        const routeUrl = window.ROUTES.WEB.CLIENT.FETCHADDONS.replace(':id', clientId);
        fetch(routeUrl)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    displayDistanceRules(data.addOns.distanceRules);
                    displayWeighteRules(data.addOns.weightRules);
                    displayTimingRules(data.addOns.timingRules);
                }
            })
            .catch(error => {
                console.error(error);
            });
    }

document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('button_addSelectedPackageType').setAttribute('disabled', 'disabled');
        document.querySelectorAll('.create-btn').forEach(button => {
            button.addEventListener('click', () => {
                if (window.openClientFormForCreate) {
                    window.openClientFormForCreate({
                        formAction: "{{ route('client.store') }}",
                        submitButtonHtml: "<i class='bi bi-save'></i>",
                        modalSelector: '#modalWindow',
                    });
                }
            });
        });

        document.getElementById('button-view-addons').addEventListener('click',function(e){
            const clientId = document.getElementById('clientid').value;
            fetchAddOns(clientId);
            $('#modalWindow-addons').modal('show');
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
