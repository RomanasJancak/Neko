@extends('layouts.app')

@section('content')
<?php

//  dd(App\Models\AddOnRule::getAllThatAreApplicableToThisDate('2024-01-26'));
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Pricing rules</h1><button class="btn btn-secondary create-btn" >Add new Pricing rule</i></button>        
            <form method="POST" action="{{ route('addonrule.createBackup') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Create Backup</button>
        </form>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client name</th>
                        <th>Begin date</th>
                        <th>End date</th>
                        <th>Display name</th>
                        <th>Code</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="addOnRulesTableBody">
                    @foreach($addOnRules as $addonrule)
                    <tr>
                        <td>{{ $addonrule->id }}</td>
                        <td>{{ $addonrule->client->name }}</td>
                        <td>{{ $addonrule->begin_date }}</td>
                        <td>{{ $addonrule->end_date }}</td>
                        <td>{{ $addonrule->display_name }}</td>
                        <td>{{ $addonrule->name }}</td>
                        <td>{{ $addonrule->price }}</td>
                        <td>
                            <button class="btn btn-primary edit-btn" 
                                data-addonruleid="{{ $addonrule->id }}"
                            ><i class="bi bi-pen"></i></button>
                            <button class="btn btn-danger delete-btn" 
                                data-addonruleid="{{ $addonrule->id }}"
                            ><i class="bi bi-trash"></i></button>
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
                            <label for="nameField">Client : </label>
                            <input type="text" name="clientName" id="clientNameField" value="">
                        </div>
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
    
document.addEventListener('DOMContentLoaded', function() {
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
                                document.getElementById('clientNameField').readOnly = false;
                                
                                document.getElementById('displaynameField').value = data.display_name;
                                document.getElementById('nameField').value = data.name;
                                document.getElementById('beginDateField').value = data.begin_date.split(' ')[0];
                                document.getElementById('endDateField').value = data.end_date.split(' ')[0];
                                document.getElementById('priceField').value = data.price;
                                document.getElementById('clientNameField').value = data.clientName;
                                document.getElementById('clientIdField').value = data.clientId;
                            }
                        })
                        .catch(error => {
                            console.error(error);
                });
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
                                document.getElementById('clientNameField').readOnly = true;

                                document.getElementById('displaynameField').value = data.display_name;
                                document.getElementById('nameField').value = data.name;
                                document.getElementById('beginDateField').value = data.begin_date.split(' ')[0];
                                document.getElementById('endDateField').value = data.end_date.split(' ')[0];
                                document.getElementById('priceField').value = data.price;
                                document.getElementById('clientNameField').value = data.clientName;
                                document.getElementById('clientIdField').value = data.clientId;
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
