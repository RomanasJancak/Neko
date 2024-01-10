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
                        <th data-column="name">Used by & value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="packageTypeesTableBody">
                    @foreach($packageTypes as $packageType)
                    <tr>
                        <td>{{ $packageType->id }}</td>
                        <td>{{ $packageType->name }}</td>
                        <td>
                        @foreach ($packageType->clients as $client)
                            <div class="row">
                                <div class="col">{{$client->name}}</div>
                                <div class="col">{{$client->pivot->price}}</div>
                            </div>
                        @endforeach
                        </td>
                        <td>
                            <button class="btn btn-primary edit-btn" 
                                data-packagetypeid="{{ $packageType->id }}"
                                data-oldclientid="{{ $packageType->clients[0]->id }}"
                                data-name="{{ $packageType->name }}"
                            ><i class="bi bi-pen"></i></button>
                            <button class="btn btn-danger delete-btn" 
                                data-packagetypeid="{{ $packageType->id }}"
                                data-oldclientid="{{ $packageType->clients[0]->id }}"
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
                        <input type="text" name="name" id="nameField" value="">
                    </div>
                    <div class="row">
                        <label for="clientNameField">Used by : </label>
                        <input type="text" name="clientNameField" id="clientNameField" value="" placeholder="Search for client">
                        <label for="priceField">Price : </label>
                        <input type="text" name="priceField" id="priceField" value="">
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
                                document.getElementById('priceField').value = data.clients[0].price;
                                document.getElementById('packageTypeClientId').value = data.clients[0].id;
                                document.getElementById('packageTypeClientIdOld').value = packageTypeOldClientId; 
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
                                document.getElementById('priceField').value = data.clients[0].price;
                                document.getElementById('packageTypeClientId').value = data.clients[0].id;
                                document.getElementById('packageTypeClientIdOld').value = packageTypeOldClientId;
                                document.getElementById('nameField').readOnly = true;
                                document.getElementById('clientNameField').readOnly = true;
                                document.getElementById('priceField').readOnly = true; 
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
