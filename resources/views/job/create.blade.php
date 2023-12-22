@extends('layouts.app')

@section('content')
@if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
@endif
<div class="alert alert-danger custom-class-job-create">↩
</div>
<div class="container mt-5">
    <div class="row">
        <div class="col-auto">
            <div class="row justify-content-md-center">
                <div class="col-md-4">
                    <h1>Job creation page</h1>
                </div>
            </div>
            
            <form method="POST" action="{{ route('job.store') }}" class="row g-3">
                @csrf
                <div class="modal fade" id="workloadModal" tabindex="-1"  aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <!-- This part will be populated with detailed job information fetched via JavaScript -->
                            <div class="modal-body" >
                            <div class="row">
                                <div class="form-group col-md-2">
                                    <label for="baseprice">Base price</label>
                                    <span id="baseprice" class="form-control">8</span>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="baseprice">Final price</label>
                                    <span id="finalprice" class="form-control">8</span>
                                </div>
                            </div>
                                @for ($i = 2; $i <= 14; $i++)
                                    <div class="form-group col-md">
                                        <label for="rule_{{ $i }}_value" id="rule_{{ $i }}_name">Base price</label>
                                        <input type="checkbox" id="rule_{{ $i }}_value" name="fields[]" value="rule_{{ $i }}_value">
                                    </div>
                                @endfor                     
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-md-center" id='courier-status-selection'>
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
                    <div class="form-group col-md-2">
                        <label for="common_date">Date</label>
                            <input type="date" id="common_date" name="common_date" class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="common_date">Whom to bill</label>
                        <input type="text" id="billingclientsearch" name="billingclientId" class="form-control" placeholder="Search for clients">
                    </div>
                </div>
                <div class="row justify-content-md-center" id='sender-receiver-columns'>
                    <div class="col-auto" id="column-for-sender">
                        <div class="row justify-content-md-center">
                            <div class="col"><h2>Sender</h2></div>
                        </div>
                        <div class="row" id="sender-select">
                            <div class="col-auto d-none">
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
                            <div class="col">
                                <label for="common_date">Pickup client search</label>
                                <input type="text" id="pickupclientsearch" name="billingclientId" class="form-control" placeholder="Search for clients">
                            </div>
                        </div>
                        <div class="row justify-content-md-center" id="sender-pickup-name-select">
                            <div class="col-md-12">
                                <label for="pickup_time" class="mb-0">Name of sender</label>
                                <input type="text" id="sender_namefield" name="sender_namefield" class="form-control">
                            </div>
                        </div>                        
                        <div class="row" id="sender-pickup-time-select">
                            <div class="col-12 text-center mb-2">
                                <label for="pickup_time" class="mb-0">Time window</label>
                            </div>
                            <div class="col-md-6 form-group">
                                <input type="time" id="pickup_time_begin" name="pickup_time_begin" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <input type="time" id="pickup_time_end" name="pickup_time_end" class="form-control">
                            </div>
                            <div class="col form-group">
                                <input type="datetime-local" id="pickup_time_begin_old" name="pickup_time_begin" class="form-control">                                
                            </div>
                            <div class="col form-group">
                                <input type="datetime-local" id="pickup_time_end_old" name="pickup_time_end" class="form-control">
                            </div>
                        </div>
                        <div class="row" id="sender-pickup-address-postalcode-select">
                            <div class="col form-group">
                                <label for="pickup_adress_postalCode_id">Outer postal code</label>
                                    @can('job-create-chooseAnyPostalCode')
                                        <input type="text" id="pickup_adress_postalCode_id" name="pickup_adress_postalCode_id" class="form-control">
                                        
                                    @else
                                        <select id="pickup_adress_postalCode_id" name="pickup_adress_postalCode_id" class="form-control"  size="3">
                                        @foreach($postalCodes as $postalCode)
                                            @if ($loop->first)
                                                <option value="{{ $postalCode->id }}" selected>{{ $postalCode->name }}</option>
                                            @else
                                                <option value="{{ $postalCode->id }}">{{ $postalCode->name }}</option>
                                            @endif
                                        @endforeach
                                        </select>
                                    @endcan                                    
                            </div>
                            <div class="col form-group">
                                <label for="pickup_inner_postalCode">Inner Postal Code</label>
                                <input type="text" id="pickup_inner_postalCode" name="pickup_inner_postalCode" class="form-control">
                            </div>
                        </div>
                        <div class="row" id="sender-pickup-address-streetaptsuite-select">
                            <div class="col form-group">
                                <label for="shipping_street">Street</label>
                                <input type="text" id="shipping_street" name="shipping_street" class="form-control">
                            </div>
                            <div class="col form-group">
                                <label for="shipping_apt_suite">Apt/Suite</label>
                                <input type="text" id="shipping_apt_suite" name="shipping_apt_suite" class="form-control">
                            </div>
                        </div>
                        <div class="row" id="sender-pickup-address-collectiondetails-select">
                            <div class="col form-group">
                                <label for="collection_details">Collection Details</label>
                                <textarea id="collection_details" name="collection_details" class="form-control" placeholder="Information about delivery to courier" ></textarea>
                            </div>
                        </div>
                        <div class="row" id="sender-pickup-address-sendercontact-select">
                            <div class="col form-group">
                                <label for="senderContacts">Sender Contacts</label>
                                <input type="text" id="senderContacts" name="senderContacts" class="form-control">
                            </div>
                        </div>
                        <div class="row" id="note-about-dropoff">
                            <div class="col form-group">
                                <label for="noteaboutdropoff">Note about pickup</label>
                                <input type="text" id="noteaboutdropoff" name="noteaboutdropoff" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-auto" id="column-for-receiver">
                        <div class="row justify-content-md-center">
                            <div class="col"><h2>Receiver</h2></div>
                        </div>
                        <div class="row" id="receiver-select">
                            <div class="col">
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
                        <div class="row" id="receiver-dropoff-time-select">
                            <div class="form-group col">
                                <label for="dropoff_time_begin">Drop off begin:</label>
                                <input type="datetime-local" id="dropoff_time_begin" name="dropoff_time_begin" class="form-control">
                            </div>
                            <div class="form-group col">
                                <label for="dropoff_time_end">Drop off end:</label>
                                <input type="datetime-local" id="dropoff_time_end" name="dropoff_time_end" class="form-control">
                            </div>
                        </div>
                        <div class="row" id="receiver-dropoff-address-postalcode-select">
                            <div class="col form-group">
                                <label for="dropoff_adress_postalCode_id">Outer postal code</label>
                                    @can('job-create-chooseAnyPostalCode')
                                        <input type="text" id="dropoff_adress_postalCode_id" name="dropoff_adress_postalCode_id" class="form-control">
                                        
                                    @else
                                        <select id="dropoff_adress_postalCode_id" name="dropoff_adress_postalCode_id" class="form-control"  size="3">
                                        @foreach($postalCodes as $postalCode)
                                            @if ($loop->first)
                                                <option value="{{ $postalCode->id }}" selected>{{ $postalCode->name }}</option>
                                            @else
                                                <option value="{{ $postalCode->id }}">{{ $postalCode->name }}</option>
                                            @endif
                                        @endforeach
                                        </select>
                                    @endcan                                    
                            </div>
                            <div class="col form-group">
                                <label for="dropoff_inner_postalCode">Inner Postal Code</label>
                                <input type="text" id="dropoff_inner_postalCode" name="dropoff_inner_postalCode" class="form-control">
                            </div>
                        </div>
                        <div class="row" id="receiver-dropoff-address-streetaptsuite-select">
                            <div class="col form-group">
                                <label for="dropoff_street">Street</label>
                                <input type="text" id="dropoff_street" name="dropoff_street" class="form-control">
                            </div>
                            <div class="col form-group">
                                <label for="dropoff_apt_suite">Apt/Suite</label>
                                <input type="text" id="dropoff_apt_suite" name="dropoff_apt_suite" class="form-control">
                            </div>
                        </div>
                        <div class="row" id="receiver-dropoff-address-collectiondetails-select">
                            <div class="col form-group">
                                <label for="dropoff_details">Dropoff Details</label>
                                <textarea id="dropoff_details" name="dropoff_details" class="form-control" placeholder="Information about delivery to courier" ></textarea>
                            </div>
                        </div>
                        <div class="row" id="receiver-dropoff-address-sendercontact-select">
                            <div class="col form-group">
                                <label for="receiverContacts">Receiver Contacts</label>
                                <input type="text" id="receiverContacts" name="receiverContacts" class="form-control">
                            </div>
                        </div>
                        <div class="row" id="note-about-pickup">
                            <div class="col form-group">
                                <label for="noteaboutpickup">Note about pickup</label>
                                <input type="text" id="noteaboutpickup" name="noteaboutpickup" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-md-center" id='general-notes-column'>
                    <div class="col form-group" id="general-notes">
                        <label for="generalnotes">General notes</label>
                        <textarea id="generalnotes" name="generalnotes" class="form-control"></textarea>
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
                <button class="btn btn-secondary work-button">AddOns</button>
                <button type="submit" class="btn btn-primary">Create Job</button>
            </form>
        </div>
    </div>
</div>

<!-- ????????????????????????????? -->

@endsection
@section('scripts')
<script>
    const commonDate = document.getElementById('common_date');
    const finalpriceElement = document.getElementById('finalprice');
    commonDate.addEventListener('change', function() {
        var baseprice = document.getElementById('baseprice');
        var rule_2_name = document.getElementById('rule_2_name');var rule_2_value = document.getElementById('rule_2_value');
        var rule_3_name = document.getElementById('rule_3_name');var rule_3_value = document.getElementById('rule_3_value');
        var rule_4_name = document.getElementById('rule_4_name');var rule_4_value = document.getElementById('rule_4_value');
        var rule_5_name = document.getElementById('rule_5_name');var rule_5_value = document.getElementById('rule_5_value');
        var rule_6_name = document.getElementById('rule_6_name');var rule_6_value = document.getElementById('rule_6_value');
        var rule_7_name = document.getElementById('rule_7_name');var rule_7_value = document.getElementById('rule_7_value');
        var rule_8_name = document.getElementById('rule_8_name');var rule_8_value = document.getElementById('rule_8_value');
        var rule_9_name = document.getElementById('rule_9_name');var rule_9_value = document.getElementById('rule_9_value');
        var rule_10_name = document.getElementById('rule_10_name');var rule_10_value = document.getElementById('rule_10_value');
        var rule_11_name = document.getElementById('rule_11_name');var rule_11_value = document.getElementById('rule_11_value');
        var rule_12_name = document.getElementById('rule_12_name');var rule_12_value = document.getElementById('rule_12_value');
        var rule_13_name = document.getElementById('rule_13_name');var rule_13_value = document.getElementById('rule_13_value');
        var rule_14_name = document.getElementById('rule_14_name');var rule_14_value = document.getElementById('rule_14_value');
        getAddOnRule(commonDate.value)
        .then(data => {
            baseprice.textContent  =data.baseprice;
            rule_2_name.textContent = data.rule_2_name+": "+data.rule_2_value+"£";
            rule_3_name.textContent = data.rule_3_name+": "+data.rule_3_value+"£";
            rule_4_name.textContent = data.rule_4_name+": "+data.rule_4_value+"£";
            rule_5_name.textContent = data.rule_5_name+": "+data.rule_5_value+"£";
            rule_6_name.textContent = data.rule_6_name+": "+data.rule_6_value+"£";
            rule_7_name.textContent = data.rule_7_name+": "+data.rule_7_value+"£";
            rule_8_name.textContent = data.rule_8_name+": "+data.rule_8_value+"£";
            rule_9_name.textContent = data.rule_9_name+": "+data.rule_9_value+"£";
            rule_10_name.textContent = data.rule_10_name+": "+data.rule_10_value+"£";
            rule_11_name.textContent = data.rule_11_name+": "+data.rule_11_value+"£";
            rule_12_name.textContent = data.rule_12_name+": "+data.rule_12_value+"£";
            rule_13_name.textContent = data.rule_13_name+": "+data.rule_13_value+"£";
            rule_14_name.textContent = data.rule_14_name+": "+data.rule_14_value+"£";
            @for ($i = 2; $i <= 14; $i++)
            rule_{{$i}}_value.value = data.rule_{{$i}}_value;
            @endfor
            addListenersToCheckboxes();
        });
        baseprice = document.getElementById('baseprice');
    });
    function addListenersToCheckboxes(){
        @for ($i = 2; $i <= 14; $i++)
        rule_{{$i}}_value = document.getElementById('rule_{{$i}}_value');        
        @endfor
        rule_2_value.addEventListener('change', function() {
            if (this.checked) {
                finalpriceElement.textContent=parseInt(finalpriceElement.textContent)+parseInt(rule_2_value.value);
                // Perform actions when checkbox is checked
            } else {
                finalpriceElement.textContent=parseInt(finalpriceElement.textContent)-parseInt(rule_2_value.value);
                // Perform actions when checkbox is unchecked
            }
        });
        rule_3_value.addEventListener('change', function() {
            if (this.checked) {
                finalpriceElement.textContent=parseInt(finalpriceElement.textContent)+parseInt(rule_3_value.value);
                // Perform actions when checkbox is checked
            } else {
                finalpriceElement.textContent=parseInt(finalpriceElement.textContent)-parseInt(rule_3_value.value);
                // Perform actions when checkbox is unchecked
            }
        });
        rule_4_value.addEventListener('change', function() {
            if (this.checked) {
                finalpriceElement.textContent=parseInt(finalpriceElement.textContent)+parseInt(rule_4_value.value);
                // Perform actions when checkbox is checked
            } else {
                finalpriceElement.textContent=parseInt(finalpriceElement.textContent)-parseInt(rule_4_value.value);
                // Perform actions when checkbox is unchecked
            }
        });

    }
    //===============================MODAL FORM BEGIN
    document.querySelectorAll('.work-button').forEach(button => {
            button.addEventListener('click', () => {
                var pickupTimeBegin = document.getElementById('pickup_time_begin');
                var pickupTimeEnd = document.getElementById('pickup_time_end');
                if ((pickupTimeBegin.value !== '')&&(pickupTimeEnd.value !== '')) {
                    document.querySelector('.alert.alert-danger.custom-class-job-create').style.display = 'none';
                    $('#workloadModal').modal('show');
                } else {
                    console.log('No date and time selected');
                    document.querySelector('.alert.alert-danger.custom-class-job-create').style.display = 'block';
                    document.querySelector('.alert.alert-danger.custom-class-job-create').innerHTML = 'No date and time selected<br> To select addons, choose date and time of delivery pickup';
                }
                    
            });
        });
    //===============================MODAL FORM END
function getAddOnRule(datetime){
    return fetch(`{{ asset('addonrules/findAddOnRule') }}?datetime=${datetime}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            return data;
                        }
                    })
                    .catch(error => {
                        console.error(error);
                    });
}
function getDistance(origin,destination){
    return fetch(`{{ asset('distances/getDistance') }}?origin=${origin}&destination=${destination}`)
                    .then(response => response.json())
                    .then(data => {
                    })
                    .catch(error => {
                        console.error(error);
                    });
}
    //===============================CONNECT TO ADDONRULES BASED ON SELECTED DATE
    //
    const senderSelect = document.getElementById('sender_id');
    const receiverSelect = document.getElementById('receiver_id');

    const senderContactsInput       = document.getElementById('senderContacts');
    const receiverContactsInput       = document.getElementById('receiverContacts');

    const pickupAddressInput        = document.getElementById('pickup_address');
    const collection_detailsInput   = document.getElementById('collection_details');
    const deliveryAddressInput      = document.getElementById('delivery_address');
    const dropoff_detailsInput      = document.getElementById('dropoff_details');


document.addEventListener('DOMContentLoaded', function() {
            // Define the data source (your $clients variable)
            var billingClientSearchInput = $('#billingclientsearch');
            var  pickupClientSearchInput =  $('#pickupclientsearch');
            // Get the search input element
            if (billingClientSearchInput.length > 0) {
                billingClientSearchInput.typeahead({
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
                                console.log(data.name);
                                populateFields('sender',data);    
                            }
                        })
                        .catch(error => {
                            console.error(error);
                        });
                    }
                });
            }
        function populateFields(clientType,data){
            nameField = document.getElementById(clientType+'_namefield');
            nameField.value =data.name;
            //sender_namefield
        }
        document.querySelector('.alert.alert-danger.custom-class-job-create').style.display = 'none';
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
                            //console.log(data);
                            //pickupAddressInput.value = data.address;
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
                            //deliveryAddressInput.value = data.address;
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