@extends('layouts.app')

@section('content')
<?php

//   dd(App\Models\AddOnRule::getAllThatAreApplicableToThisDateForSpecificClient('2024-01-26','81'));
// dd(App\Models\AddOnRule::getAllThatAreApplicableToThisDateForSpecificClientByPatern('2024-01-26','1','job-distance'));
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Pricing rules</h1>@can('addonrule-create')<button class="btn btn-secondary create-btn" >Add new Pricing rule</i></button>@endcan
            @can('addonrule-create')
            <form method="POST" action="{{ route('addonrule.createBackup') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Create Backup</button>
        </form>
            @endcan
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Begin date</th>
                        <th>End date</th>
                        <th>Display name</th>
                        <th>Code</th>
                        <th>Price</th>
                        <th data-column="clients">Used by</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="addOnRulesTableBody">
                    @foreach($addOnRules as $addonrule)
                    <tr>
                        <td>{{ $addonrule->id }}</td>
                        <td>{{ $addonrule->begin_date }}</td>
                        <td>{{ $addonrule->end_date }}</td>
                        <td>{{ $addonrule->display_name }}</td>
                        <td>{{ $addonrule->name }}</td>
                        <td>{{ $addonrule->price }}</td>
                        <td class="client-list" data-package-type-id="{{ $addonrule->id }}">
                        @foreach ($addonrule->clients as $client)
                            <div class="row">
                                <div class="col">{{$client->name}}</div>
                            </div>
                        @endforeach
                        <button id="expandButton-{{ $addonrule->id }}" class="btn btn-primary expand-button" style="display: none;">Expand</button>
                        <button id="collapseButton-{{ $addonrule->id }}" class="btn btn-primary collapse-button" style="display: none;">Collapse</button>
                        </td>
                        
                        <td>
                            @can('addonrule-edit')
                            <button class="btn btn-primary edit-btn" 
                                data-addonruleid="{{ $addonrule->id }}"
                            ><i class="bi bi-pen"></i></button>
                            @endcan
                            @can('addonrule-delete')
                            <button class="btn btn-danger delete-btn" 
                                data-addonruleid="{{ $addonrule->id }}"
                            ><i class="bi bi-trash"></i></button>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {!! $addOnRules->links() !!}
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="modalWindow" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <form id="addonruleForm" action="" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col">
                            <input type="hidden" name="addonruleid" id="addonruleid" value="">
                            <input type="hidden" name="client_id" id="clientIdField" value="">
                            <label for="displaynameField">Display name : </label>
                            <input type="text" name="display_name" id="displaynameField" value="">
                            <label for="nameField">Code : </label>
                            <input type="text" name="name" id="nameField" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="nameField">Begin date : </label>
                            <input type="date" name="begin_date" id="beginDateField" value="">
                            <label for="nameField">End date : </label>
                            <input type="date" name="end_date" id="endDateField" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">        
                            <label for="nameField">Price : </label>
                            <input type="text" name="price" id="priceField" value="">
                        </div>
                    </div>
                    <hr class="my-divider">
                    <div class="row">
                        <div class="col-md-6"><button type="button" id="checkAllClientsButton" class="btn btn-primary">Check All</button></div>
                        <div class="col-md-6"><button type="button" id="unCheckAllClientsButton" class="btn btn-primary">Uncheck All</button></div>
                    </div>
                    <hr class="my-divider">
                    <div class="row modal-client-list" >
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6">
                                <button  class="btn btn-primary expand-button" style="display: none;">Expand</button>                                    
                            </div>
                            <div class="col-6">
                                <button  class="btn btn-primary collapse-button" style="display: none;">Collapse</button>
                            </div>
                        </div>
                    </div>
                    @foreach ($clients as $client)
                            <div class="col-md-4 client-item" data-client-id="{{ $client->id }}">
                                <label>
                                    <input type="checkbox" name="selected_clients[]"  value="{{ $client->id }}" {{ $client->id == 1 ? 'checked' : '' }} onclick="if(this.value == 1) { return false; }">
                                    <?php $maxLengthOfCLientName = 21;?>
                                    @if(strlen($client->name) > $maxLengthOfCLientName)
                                        <span data-toggle="tooltip" data-placement="top" title="{{ $client->name }}">
                                            {{ substr($client->name, 0, $maxLengthOfCLientName) }}...
                                        </span>
                                    @else
                                        {{ $client->name }}
                                    @endif
                                </label>
                            </div>
                         @endforeach

                    </div>
                    <div class="row">
                        <div class="form-group">
                            <button type="button" id="submitform" data-option="create" class="btn btn-primary">Apply</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-center">
                <div class="row">
                </div>
                <div class="row">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function addHideShowFunctionalityForModalClientList(clientList){
    console.log(clientList);
    //var expandButton = clientList.getElementById('expandButton-' + packageTypeId);
    //var collapseButton = clientList.getElementById('collapseButton-' + packageTypeId);
}
document.addEventListener('DOMContentLoaded', function() {
//===========================================================================================
var clientLists = document.querySelectorAll('.client-list');
const modalClientList = document.querySelector('.modal-client-list');
addHideShowFunctionalityForModalClientList(modalClientList);

clientLists.forEach(function(clientList) {
    var packageTypeId = clientList.getAttribute('data-package-type-id');
    var expandButton = document.getElementById('expandButton-' + packageTypeId);
    var collapseButton = document.getElementById('collapseButton-' + packageTypeId);
    var rows = clientList.querySelectorAll('.row');

    if (rows.length > 3) {
        expandButton.style.display = 'block';
        rows.forEach(function(row, index) {
                if (index >= 3) {
                    row.style.display = 'none';
                }
        });

        expandButton.addEventListener('click', function() {
            rows.forEach(function(row, index) {
                if (index >= 3) {
                    row.style.display = 'block';
                }
            });
            expandButton.style.display = 'none';
            collapseButton.style.display = 'block';
        });

        collapseButton.addEventListener('click', function() {
            rows.forEach(function(row, index) {
                if (index >= 3) {
                    row.style.display = 'none';
                }
            });
            collapseButton.style.display = 'none';
            expandButton.style.display = 'block';
        });
    }
});
//===========================================================================================
    addSearchAbilityForClientsToField('clientNameField');
    document.querySelectorAll('.edit-btn').forEach(button => {

        button.addEventListener('click', () => {
            const addonruleid          =   button.dataset.addonruleid;
            //console.log(addonruleid);
            const routeUrl = "{{ route('addonrule.getAddOnRuleInfo', ['id' => ':id']) }}".replace(':id', addonruleid);
            const form = document.querySelector(`#addonruleForm`);
            if (form) {
                form.setAttribute('action', "{{ route('addonrule.update') }}");
                fetch(routeUrl)
                        .then(response => response.json())
                        .then(data => {
                            if (data) {
                                console.log(data);
                                document.getElementById('addonruleid').value = addonruleid;
                                document.getElementById('displaynameField').readOnly = false;
                                document.getElementById('nameField').readOnly = false;
                                document.getElementById('beginDateField').readOnly = false;
                                document.getElementById('endDateField').readOnly = false;
                                document.getElementById('priceField').readOnly = false;
                                
                                document.getElementById('displaynameField').value = data.display_name;
                                document.getElementById('nameField').value = data.name;
                                document.getElementById('beginDateField').value = data.begin_date.split(' ')[0];
                                document.getElementById('endDateField').value = data.end_date.split(' ')[0];
                                document.getElementById('priceField').value = data.price;
                                data.clients.forEach(function(client) {
                                    var checkbox = document.querySelector('input[name="selected_clients[]"][value="' + client.id + '"]');
                                    if (checkbox) {
                                        checkbox.checked = true;
                                    }

                                });
                                var checkboxes = document.querySelectorAll('input[name="selected_clients[]"]');
                                    checkboxes.forEach(function(checkBox){      
                                        checkBox.removeAttribute('disabled');  
                                });
                                
                            }
                        })
                        .catch(error => {
                            console.error(error);
                });
                document.getElementById('checkAllClientsButton').style.display = '';
                document.getElementById('unCheckAllClientsButton').style.display = '';
                document.querySelector('input[name="selected_clients[]"][value="' + 1 + '"]').checked;
                document.querySelector('input[name="selected_clients[]"][value="' + 1 + '"]').setAttribute('readonly', true);
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-pen'></i>";
            }
            $('#modalWindow').modal('show');
        });
    });
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', () => {
            const addonruleid = button.dataset.addonruleid;
            const routeUrl = "{{ route('addonrule.getAddOnRuleInfo', ['id' => ':id']) }}".replace(':id', addonruleid);
            const form = document.querySelector(`#addonruleForm`);
            if (form) {
                form.setAttribute('action', "{{ route('addonrule.delete') }}");
                
                fetch(routeUrl)
                        .then(response => response.json())
                        .then(data => {
                            if (data) {
                                document.getElementById('addonruleid').value = addonruleid;
                                document.getElementById('displaynameField').readOnly = true;
                                document.getElementById('nameField').readOnly = true;
                                document.getElementById('beginDateField').readOnly = true;
                                document.getElementById('endDateField').readOnly = true;
                                document.getElementById('priceField').readOnly = true;

                                document.getElementById('displaynameField').value = data.display_name;
                                document.getElementById('nameField').value = data.name;
                                document.getElementById('beginDateField').value = data.begin_date.split(' ')[0];
                                document.getElementById('endDateField').value = data.end_date.split(' ')[0];
                                document.getElementById('priceField').value = data.price;
                            }
                        })
                        .catch(error => {
                            console.error(error);
                });
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-trash'></i>";
            }
            $('#modalWindow').modal('show');
        });
    });
    document.querySelectorAll('.create-btn').forEach(button => {
        button.addEventListener('click', () => {
            const form = document.querySelector(`#addonruleForm`);
            if (form) {
                document.getElementById('nameField').readOnly = false;
                form.setAttribute('action', "{{ route('addonrule.store') }}");
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-save'></i>";
            }
            $('#modalWindow').modal('show');
        });
    });
    document.getElementById('submitform').addEventListener('click', function() {
        // Get form data
        const form = document.getElementById('addonruleForm');
        const formData = new FormData(form);
        //console.log(formData.get('addonruleid'));
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
    });
    document.getElementById('checkAllClientsButton').addEventListener('click', (event) => {
        event.preventDefault();
        var checkboxes = document.querySelectorAll('input[name="selected_clients[]"]');
        checkboxes.forEach(function(checkBox){
            checkBox.checked = true; 
        });
        }
    );
    document.getElementById('unCheckAllClientsButton').addEventListener('click', (event) => {
        event.preventDefault();
        var checkboxes = document.querySelectorAll('input[name="selected_clients[]"]');
        checkboxes.forEach(function(checkBox){
            if(checkBox.value == '1'){

            }
            else{
                checkBox.checked = false;
            }
        });
        }
    );

});
//==========================FUNCTIONS =================================

function addSearchAbilityForClientsToField(searchElementId) {
        var searchInput = $('#' + searchElementId);
        var splitId = searchElementId.split('-');
        var number = splitId[splitId.length - 1];
        var address_origin;
        var address_destination;
        if (searchInput.length > 0) {
            searchInput.typeahead({
                source: function(query, process) {
                    var apiUrl = "{{ route('client.searchClients') }}?query=" + query;
                    fetch(apiUrl)
                        .then(response => response.json())
                        .then(data => {
                            process(data);
                        })
                        .catch(error => {
                            console.error('Error fetching client data:', error);
                        });
                },
                autoSelect: true,
                minLength: 2, // Minimum characters required before searching
                displayText: function(item) {
                    return item.name; // Adjust based on your data structure
                },
                afterSelect: function(item) {
                    fetch(`/get-client-info/${item.id}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data) {
                                //console.log(data);
                                document.getElementById('clientIdField').value = data.id;
                            }
                        })
                        .catch(error => {
                            console.error(error);
                        });
                }
            });
        }
}
</script>
@endsection
