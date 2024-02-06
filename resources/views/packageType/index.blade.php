@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Package Types</h1><button class="btn btn-secondary create-btn" >Add new Package Type</i></button>        
            <form method="POST" action="{{ route('packageType.createBackup') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Create Backup</button>
        </form>
            <table class="table">
                <thead>
                    <tr>
                        <th data-column="id">ID</th>
                        <th data-column="name">Name</th>
                        <th data-column="value">Value</th>
                        <th data-column="clients">Used</th>
                        <th data-column="baseQuantityThreshold">Base price qnt.</th>
                        <th data-column="maxQuantityThreshold">Warning qnt.</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="packageTypeesTableBody">
                    @foreach($packageTypes as $packageType)
                    <tr>
                        <td>{{ $packageType->id }}</td>
                        <td>{{ $packageType->name }}</td>
                        <td>{{ number_format($packageType->price / 100, 2) }}</td>
                        <td>{{ $packageType->baseQuantityThreshold }}</td>
                        <td>{{ $packageType->maxQuantityThreshold }}</td>
                        <td class="client-list" data-package-type-id="{{ $packageType->id }}">
                        @foreach ($packageType->clients as $client)
                            <div class="row">
                                <div class="col">{{$client->name}}</div>
                            </div>
                        @endforeach
                        <button id="expandButton-{{ $packageType->id }}" class="btn btn-primary expand-button" style="display: none;">Expand</button>
                        <button id="collapseButton-{{ $packageType->id }}" class="btn btn-primary collapse-button" style="display: none;">Collapse</button>
                        </td>
                        <td>
                            <button class="btn btn-primary edit-btn" 
                                data-packagetypeid="{{ $packageType->id }}"
                                
                                data-name="{{ $packageType->name }}"
                            ><i class="bi bi-pen"></i></button>
                            <button class="btn btn-danger delete-btn" 
                                data-packagetypeid="{{ $packageType->id }}"
                                
                                data-name="{{ $packageType->name }}"
                            ><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {!! $packageTypes->links() !!}
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="modalWindow" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <form id="packageTypeForm" action="" method="POST">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="packageTypeId" id="packageTypeId" value="">
                        <input type="hidden" name="packageTypeClientIdOld" id="packageTypeClientIdOld" value="">
                        <input type="hidden" name="packageTypeClientId" id="packageTypeClientId" value="">
                        <label for="nameField">Name : </label>
                        <input type="text" name="name" id="nameField" value="" placeholder="Package type name">
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label for="priceField"> Base price add : </label>
                            <input type="text" name="priceField" id="priceField" value="1.11"><i class="bi bi-info-circle-fill" data-toggle="tooltip" data-placement="right" title="Use period instead of comma"></i>
                        </div>
                        <div class="col-md-3">
                            <label for="baseQuantityThresholdField">Quantity before oversize : </label>
                            <input type="number" name="baseQuantityThreshold" id="baseQuantityThresholdField" value="1">
                        </div>
                        <div class="col-md-3">
                            <label for="maxQuantityThresholdField">Max allowable : </label>
                            <input type="number" name="maxQuantityThreshold" id="maxQuantityThresholdField" value="1">
                        </div>
                    </div>
                    <hr class="my-divider">
                    <div class="row">
                        <div class="col-md-6"><button type="button" id="checkAllClientsButton" class="btn btn-primary">Check All</button></div>
                        <div class="col-md-6"><button type="button" id="unCheckAllClientsButton" class="btn btn-primary">Uncheck All</button></div>
                    </div>
                    <hr class="my-divider">
                    <div class="row">
                        <!-- <label for="clientNameField">Used by : </label> -->
                        <input hidden type="text" name="clientNameField" id="clientNameField" value="" placeholder="Search for client">
                        @foreach ($clients as $client)
                            <div class="col-md-4 client-item" data-client-id="{{ $client->id }}">
                                <label>
                                    <input type="checkbox" name="selected_clients[]" value="{{ $client->id }}" {{ $client->id == 1 ? 'checked' : '' }} {{ $client->id == 1 ? 'disabled' : '' }}>
                                        {{ $client->name }}
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
//===========================================================================================
var clientLists = document.querySelectorAll('.client-list');

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
    var clientSearchInput = $('#clientNameField');
    if (clientSearchInput.length > 0) {
        clientSearchInput.typeahead({
                    source: function(query, process) {
                        var apiUrl = "{{ route('client.searchClients') }}?query=" + query;
                        // Perform a fetch request to get client data from a remote source
                        fetch(apiUrl)
                            .then(response => response.json())
                            .then(data => {
                                // Process the fetched data and pass it to the typeahead
                                process(data);
                            })
                            .catch(error => {
                                console.error('Error fetching client data:', error);
                            });
                    },
                    autoSelect: true,
                    minLength: 2, // Minimum characters required before searching
                    displayText: function(item) {
                        return item.name; // Adjust this based on your client data structure
                    },
                    afterSelect: function(item) {
                        // Handle the selection here (e.g., redirect to client details page)
                        fetch(`/get-client-info/${item.id}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data) {
                                document.getElementById('packageTypeClientId').value = data.id;
                                //console.log(data.name);
                                //populateFields('sender',data);    
                            }
                        })
                        .catch(error => {
                            console.error(error);
                        });
                    }
                });
            }
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
            checkBox.checked = false; 
        });
        }
    );
    document.querySelectorAll('.edit-btn').forEach(button => {

        button.addEventListener('click', () => {
            const packageTypeId             =   button.dataset.packagetypeid;
            const packageTypeOldClientId    =   button.dataset.oldclientid;
            const packageTypeName           =   button.dataset.name; 
            const routeUrl = "{{ route('packageType.getPackageTypeInfo', ['id' => ':id']) }}".replace(':id', packageTypeId);    
            const form = document.querySelector(`#packageTypeForm`);
            if (form) {
                form.setAttribute('action', "{{ route('packageType.update') }}");
                document.getElementById('packageTypeId').value = packageTypeId;
                document.getElementById('nameField').value = packageTypeName;
                fetch(routeUrl)
                        .then(response => response.json())
                        .then(data => {
                            if (data) {
                                document.getElementById('clientNameField').value = data.clients[0].name;
                                document.getElementById('priceField').value = (data.price / 100).toFixed(2);
                                document.getElementById('baseQuantityThresholdField').value = data.baseQuantityThreshold;
                                document.getElementById('maxQuantityThresholdField').value = data.maxQuantityThreshold;
                                document.getElementById('packageTypeClientId').value = data.clients[0].id;
                                document.getElementById('packageTypeClientIdOld').value = packageTypeOldClientId;
                                data.clients.forEach(function(client) {
                                    var checkbox = document.querySelector('input[name="selected_clients[]"][value="' + client.id + '"]');
                                    if (checkbox) {
                                        checkbox.checked = true;
                                    }
                                    var checkboxes = document.querySelectorAll('input[name="selected_clients[]"]');
                                    checkboxes.forEach(function(checkBox){      
                                        checkbox.removeAttribute('disabled');  
                                    });
                                });
                                var clientDivs = document.querySelectorAll('.client-item');
                                clientDivs.forEach(function(clientDiv){
                                    clientDiv.style.display = '';     
                                });
                                document.getElementById('checkAllClientsButton').style.display = '';
                                document.getElementById('unCheckAllClientsButton').style.display = '';
                                document.querySelector('input[name="selected_clients[]"][value="' + 1 + '"]').checked;
                                document.querySelector('input[name="selected_clients[]"][value="' + 1 + '"]').setAttribute('disabled', 'disabled');
                            }
                        })
                        .catch(error => {
                            console.error(error);
                }); 
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-pen'></i>";
            }
            document.getElementById('nameField').readOnly = false;
            document.getElementById('clientNameField').readOnly = false;
            document.getElementById('priceField').readOnly = false;
            document.getElementById('baseQuantityThresholdField').readOnly = false;
            document.getElementById('maxQuantityThresholdField').readOnly = false;
            $('#modalWindow').modal('show');
        });
    });
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', () => {
            const packageTypeId             =   button.dataset.packagetypeid;
            const packageTypeOldClientId    =   button.dataset.oldclientid;
            const packageTypeName           =   button.dataset.name; 
            const routeUrl = "{{ route('packageType.getPackageTypeInfo', ['id' => ':id']) }}".replace(':id', packageTypeId);    
            const form = document.querySelector(`#packageTypeForm`);
            if (form) {
                form.setAttribute('action', "{{ route('packageType.delete') }}");
                document.getElementById('packageTypeId').value = packageTypeId;
                document.getElementById('nameField').value = packageTypeName;
                fetch(routeUrl)
                        .then(response => response.json())
                        .then(data => {
                            if (data) {
                                document.getElementById('clientNameField').value = data.clients[0].name;
                                document.getElementById('priceField').value = (data.price / 100).toFixed(2);
                                document.getElementById('baseQuantityThresholdField').value = data.baseQuantityThreshold;
                                document.getElementById('maxQuantityThresholdField').value = data.maxQuantityThreshold;
                                document.getElementById('packageTypeClientId').value = data.clients[0].id;
                                document.getElementById('packageTypeClientIdOld').value = packageTypeOldClientId;
                                document.getElementById('nameField').readOnly = true;
                                
                                document.getElementById('clientNameField').readOnly = true;
                                document.getElementById('priceField').readOnly = true;
                                document.getElementById('baseQuantityThresholdField').readOnly = true;
                                document.getElementById('maxQuantityThresholdField').readOnly = true;
                                
                                data.clients.forEach(function(client) {
                                    var checkbox = document.querySelector('input[name="selected_clients[]"][value="' + client.id + '"]');
                                    
                                    
                                    if (checkbox) {
                                        checkbox.checked = true;
                                        checkbox.setAttribute('disabled', 'disabled');
                                    }
                                });
                                $('input[type="checkbox"][name="selected_clients[]"]').each(function() {
                                    var clientId = $(this).val();
                                    var clientItem = $('.client-item[data-client-id="' + clientId + '"]');
                                    
                                    if ($(this).prop('checked')) {
                                        clientItem.show(); // Show the client element if the checkbox is checked
                                    } else {
                                        clientItem.hide(); // Hide the client element if the checkbox is unchecked
                                    }
                                });
                                document.getElementById('checkAllClientsButton').style.display = 'none';
                                document.getElementById('unCheckAllClientsButton').style.display = 'none'; 
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
            const form = document.querySelector(`#packageTypeForm`);
            if (form) {
                document.getElementById('nameField').readOnly = false;
                document.getElementById('clientNameField').readOnly = false;
                document.getElementById('priceField').readOnly = false;
                form.setAttribute('action', "{{ route('packageType.store') }}");
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-save'></i>";
                document.getElementById('checkAllClientsButton').style.display = '';
                document.getElementById('unCheckAllClientsButton').style.display = '';
            }
            $('#modalWindow').modal('show');
        });
    });
    document.getElementById('submitform').addEventListener('click', function() {
        // Get form data
        const form = document.getElementById('packageTypeForm');
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
    });
});
//const routeUrl = "{{ route('packageType.getPackageTypeInfo', ['id' => 1]) }}";
//fetch(`/getPackageTypeInfo/${1}`)
// fetch(routeUrl)
//                         .then(response => response.json())
//                         .then(data => {
//                             if (data) {
//                                 console.log(data.name);
//                                 console.log(data.clients[0].name);    
//                             }
//                         })
//                         .catch(error => {
//                             console.error(error);
// });
</script>
@endsection
