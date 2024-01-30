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
                            <div class="modal-body" >                    
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-md-center" id='courier-status-selection'>
                    <div class="row justify-content-md-center">
                        <div class="form-group col-md-2">
                            <label for="courrier_id">Courier</label>
                            <select id="courrier_id" name="courrier_id" class="form-control" >
                                
                                @foreach($couriers as $courier)
                                    <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                                @endforeach
                                <option value="0" selected>none</option>    
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
                            <label for="billingclientsearch">Whom to bill</label>
                            <input type="text" id="billingclientsearch" name="billingclientId" class="form-control" placeholder="Search for clients">
                            <input type="hidden" name="billingClient_id" id="billingClientIdField" value="">
                        </div>
                        
                    </div>
                    <div class="row justify-content-md-center" id="job-addon-container">
                    </div>
                </div>
                <div class="row">
                    <div class="row justify-content-md-center">
                        <div class="col-auto">
                            <div class="row justify-content-md-center">
                                <div class="col"><h2>Pickup</h2></div>
                            </div>
                            <div class="col-auto">
                                <h6>Total price : <span id="total-price"></span><span>&#163;</span></h6>                                
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-md-center">
                        <div class="col-auto">
                            <div class="row justify-content-md-center">
                                <div class="col-auto">
                                    <label for="pickup_name_search">Name of the pickup adress</label>
                                    <input type="text" id="pickup_name_search" name="pickupclientname"class="form-control" placeholder="Pickup name">
                                </div>
                                <div class="col-auto">
                                    <label for="pickupaddress_addressline">Address line</label>
                                    <input type="text" class="form-control" name="pickupclientaddressline" id="pickupaddress_addressline" placeholder="Pickup address line">
                                </div>
                                <div class="col-auto">
                                    <label for="pickupaddress_postalcode">Postal code</label>
                                    <input type="text" class="form-control" name="pickupclientpostalcode" id="pickupaddress_postalcode" placeholder="Pickup postal code">
                                    <input hidden type="text"   name="pickupclientcity" id="pickupaddress_city" class="form-control" placeholder="City">
                                    <input hidden type="text"   name="pickupclientcountry" id="pickupaddress_country" class="form-control" placeholder="Country">
                                </div>
                                <div class="col-auto">
                                    <div><h6>Total distance :</h6><span id="total-distance"></span></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center mb-2">
                                    <h5 class="mb-0">Time window</h5>
                                </div>
                                <div class="col-md-6 form-group">
                                    <input type="time" id="pickup_time_begin" name="pickup_time_begin" class="form-control">
                                </div>
                                <div class="col-md-6 form-group">
                                    <input type="time" id="pickup_time_end" name="pickup_time_end" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-md-center">
                            <div class="col-12 text-center mb-2">
                                <h4>Packages</h4>
                            </div>
                </div>
                <div class="row justify-content-md-center" id="area-packages">
                        <div class="row justify-content-md-center border border-dark packageclass" id="package-0">
                            <div class="col-auto">
                                <label id="labelpackagetypeselect-0" for="packagetypeselect-0">Package</label>
                                <select class="select-ClientPackageType form-select" id="packagetypeselect-0" name="packageType[]" class="form-control">
                                </select>
                                <input type="number" id="packageQuantity-0" class="form-control" placeholder="Quantity">
                                <button class="btn btn-secondary" data-direction="up" id="package_button_up-0"><i class="bi bi-arrow-up-circle-fill"></i></button>
                                <button class="btn btn-secondary" data-direction="down" id="package_button_down-0"><i class="bi bi-arrow-down-circle-fill"></i></button>
                            </div>
                            <div class="col-auto">
                                <span>Drop off</span>
                                <div class="row">
                                    <input type="text"          name="packagedropoffname[]" id="package_name_search-0" class="form-control" placeholder="Name">
                                    <input type="text"          name="packagedropooffaddressline[]" id="package_addressline-0" class="form-control" placeholder="Addressline">
                                    <input type="text"          name="packagedropoffpostalcode[]" id="package_postalcode-0" class="form-control" placeholder="Postal Code">
                                    <input hidden type="text"   name="packagedropoffcity[]" id="package_city-0" class="form-control" placeholder="City">
                                    <input hidden type="text"   name="packagedropoffcountry[]" id="package_country-0" class="form-control" placeholder="Country">
                                    <div>Distance : <span id="package_distance-0">0</span> meters</div>
                                    <div>Price : <span id="package_price-0">0</span><span>&#163;</span></div>                         
                                </div>
                            </div>
                            <div class="col-auto">
                                <div><h6>Add Ons</h6></div>
                            </div>
                        </div>
                        <div class="row" id='addPackageRow'>
                            <div class="col">
                                <button class="btn btn-secondary" id='addPackageButton'>Add package</button>
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

@endsection
@section('scripts')
<script>
    //let totalPriceOfTheJob = 0;
    //let totalDistanceOfTheJob = 0;
    //const totalPriceOfTheJobElement =  document.getElementById('total-price');
    //const totalDistanceOfTheJobElement =  document.getElementById('total-distance');
    //let arrayOfSelectedOptionsForPackageTypeSelect = [];
    const commonDate = document.getElementById('common_date');
    //const finalpriceElement = document.getElementById('finalprice');
    //===============================MODAL FORM BEGIN
    document.querySelectorAll('.work-button').forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                if (true) {
                    document.querySelector('.alert.alert-danger.custom-class-job-create').style.display = 'none';
                    $('#workloadModal').modal('show');
                } else {
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


document.addEventListener('DOMContentLoaded', function() {
    function updateDistances(){
        var finalDistance=0;
        document.querySelectorAll('[id^="package_distance-"]').forEach(packageDistanceElement => {
            var id = packageDistanceElement.id.split('-')[packageDistanceElement.id.split('-').length - 1];
            var origin_addressLine; var destination_addressLine;
            var origin_postalcode;var destination_postalcode;
            var origin_city;var destination_city;
            var origin_country;var destination_country;
            var origin_fullAddress;var destination_fullAddress;
            var distance=0;
            if( id == 0){
                origin_addressLine = document.getElementById('pickupaddress_addressline').value;
                origin_postalcode = document.getElementById('pickupaddress_postalcode').value;
                origin_city = document.getElementById('pickupaddress_city').value;
                origin_country = document.getElementById('pickupaddress_country').value;
                origin_fullAddress = origin_country+' '+origin_city+' '+origin_postalcode+' '+origin_addressLine;      
            }else{
                origin_addressLine = document.getElementById('package_addressline-'+(id-1)).value;
                origin_postalcode = document.getElementById('package_postalcode-'+(id-1)).value;
                origin_city = document.getElementById('package_city-'+(id-1)).value;
                origin_country = document.getElementById('package_country-'+(id-1)).value;
                origin_fullAddress = origin_country+' '+origin_city+' '+origin_postalcode+' '+origin_addressLine;
            }
            destination_addressLine = document.getElementById('package_addressline-'+id).value;
            destination_postalcode = document.getElementById('package_postalcode-'+id).value;
            destination_city = document.getElementById('package_city-'+id).value;
            destination_country = document.getElementById('package_country-'+id).value;
            destination_fullAddress = destination_country+' '+destination_city+' '+destination_postalcode+' '+destination_addressLine;

            if(origin_fullAddress && destination_fullAddress){
                var baseUrl = "{{ route('distance.getDistance') }}";
                var fullUrl = `${baseUrl}?origin=${encodeURIComponent(origin_fullAddress)}&destination=${encodeURIComponent(destination_fullAddress)}`;
                fetch(fullUrl)
                    .then(response => response.json())
                    .then(data => {
                        //console.log('data is ',data);
                        distance = data;
                        finalDistance+=distance;
                        document.getElementById('package_distance-'+id).innerHTML = distance; 
                        document.getElementById('total-distance').innerHTML = finalDistance; 
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }

                    
        });
    }
    function updatePrices(){
        var finalPrice = 0;
        document.querySelectorAll('[id^="packagetypeselect-"]').forEach(packageSelectElement => {
            var id = packageSelectElement.id.split('-')[packageSelectElement.id.split('-').length - 1];
            var price=parseInt(packageSelectElement.options[packageSelectElement.selectedIndex].getAttribute('data-price'));
            finalPrice+=price;

            document.getElementById('package_price-'+id).innerHTML = price; 
            document.getElementById('total-price').innerHTML = finalPrice;
        });
    }
    function adjustPackagesId(){
        var packageCount=0;
        var listOfPackagesElements = document.querySelectorAll('[id^="package-"]');
        listOfPackagesElements.forEach(packageElement =>{
            packageElement.id = 'package-' + packageCount;
            packageElement.querySelector('[id^="package_name_search"]').id = 'package_name_search-' + packageCount;
            packageElement.querySelector('[id^="packagetypeselect"]').id = 'packagetypeselect-' + packageCount;
            packageElement.querySelector('[id^="package_addressline"]').id = 'package_addressline-' + packageCount;
            packageElement.querySelector('[id^="package_postalcode"]').id = 'package_postalcode-' + packageCount;
            packageElement.querySelector('[id^="package_city"]').id = 'package_city-' + packageCount;
            packageElement.querySelector('[id^="package_country"]').id = 'package_country-' + packageCount;
            packageElement.querySelector('[id^="package_distance"]').id = 'package_distance-' + packageCount;
            packageElement.querySelector('[id^="packageQuantity"]').id = 'packageQuantity-' + packageCount;
            packageElement.querySelector('[id^="labelpackagetypeselect"]').id = 'labelpackagetypeselect-' + packageCount;
            packageElement.querySelector('[id^="package_price"]').id = 'package_price-' + packageCount;
            packageElement.querySelector('[id^="package_button_up"]').id = 'package_button_up-' + packageCount;
            packageElement.querySelector('[id^="package_button_down"]').id = 'package_button_down-' + packageCount;
            packageCount++;
        });
        updateDistances();
        updatePrices();
    }
    function addInputCheckBox(container,rule){
        const divElement = document.createElement('div');
        divElement.classList.add('form-group');
        divElement.classList.add('col-1');
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.id = 'this'; // Set the id attribute to a unique identifier, e.g., rule name
        checkbox.name = 'checkboxName'; // Set the name attribute if needed

        // Create a label element associated with the checkbox (optional)
        const label = document.createElement('label');
        label.textContent = rule.display_name; // Label text, you can customize it as needed
        label.setAttribute('for', checkbox.id); // Set 'for' attribute to match the checkbox's id

        container.appendChild(divElement);
        divElement.appendChild(label);
        divElement.appendChild(checkbox);
        
    }
    function updateJobAddons(){
        jobAddonContainer    =   document.getElementById('job-addon-container');
        date                =   document.getElementById('common_date').value;
        billingClientId       =   document.getElementById('billingClientIdField').value;
        console.log(date+' '+billingClientId);
        if(date && billingClientId){
            jobAddonContainer.innerHTML = '';
        const routeUrl = "{{ route('addonrule.getRulesForDateAndClient', ['date' => ':date','client' =>':clientId']) }}".replace(':date', date).replace(':clientId',billingClientId);

                fetch(routeUrl)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(function (element){
                            if(element.name.split('-')[0] === 'job'){
                                addInputCheckBox(jobAddonContainer,element);
                            }
                        });
                    })
                    .catch(error => {
                        console.error(error);
                    });
        }

    }
    //========================================================
    const addPackageButtonElement = document.getElementById('addPackageButton');
    var clientsPacakgeTypes;
    var billingClientSearchInput = $('#billingclientsearch');
    addTypeHeadSearch(billingClientSearchInput);
    var pickupClientSearchInput =  $('#pickup_name_search');

    function movePackage(ButtonElement) {
        direction = ButtonElement.getAttribute('data-direction');
        var id = ButtonElement.id.split('-')[ButtonElement.id.split('-').length - 1];
        var packageElement = document.getElementById('package-'+id);
        var swapElement;
        if(direction == 'up'){
            swapElement = document.getElementById('package-'+(parseInt(id)-1));
            packageElement.parentNode.insertBefore(packageElement,swapElement);
        }else{
            swapElement = document.getElementById('package-'+(parseInt(id)+1));
            packageElement.parentNode.insertBefore(swapElement,packageElement);
        }
        adjustPackagesId();
    }

    const moveupPackageButtonElement = document.getElementById('package_button_up-0');
    const movedownPackageButtonElement = document.getElementById('package_button_down-0');
    const jobCreationDatePicker = document.getElementById('common_date');
    jobCreationDatePicker.addEventListener('change', function (event) {
        updateJobAddons();
    });
    moveupPackageButtonElement.addEventListener('click', function (event) {
        event.preventDefault();
        movePackage(moveupPackageButtonElement);
    });

    movedownPackageButtonElement.addEventListener('click', function (event) {
        event.preventDefault();
        movePackage(movedownPackageButtonElement);
    });

    addPackageButtonElement.addEventListener('click', function(event) {
        event.preventDefault();
        var packageCount = document.querySelectorAll('[id^="package-"]').length;
        var packageElement = document.getElementById('package-0').cloneNode(true);
        packageElement.id = 'package-' + packageCount;
        packageElement.querySelector('[id^="package_name_search"]').id = 'package_name_search-' + packageCount;
        packageElement.querySelector('[id^="packagetypeselect"]').id = 'packagetypeselect-' + packageCount;
        packageElement.querySelector('[id^="package_addressline"]').id = 'package_addressline-' + packageCount;
        packageElement.querySelector('[id^="package_postalcode"]').id = 'package_postalcode-' + packageCount;
        packageElement.querySelector('[id^="package_city"]').id = 'package_city-' + packageCount;
        packageElement.querySelector('[id^="package_country"]').id = 'package_country-' + packageCount;
        packageElement.querySelector('[id^="package_distance"]').id = 'package_distance-' + packageCount;
        packageElement.querySelector('[id^="packageQuantity"]').id = 'packageQuantity-' + packageCount;
        packageElement.querySelector('[id^="labelpackagetypeselect"]').id = 'labelpackagetypeselect-' + packageCount;
        packageElement.querySelector('[id^="package_price"]').id = 'package_price-' + packageCount;
        packageElement.querySelector('[id^="package_button_up"]').id = 'package_button_up-' + packageCount;
        packageElement.querySelector('[id^="package_button_down"]').id = 'package_button_down-' + packageCount;

        var addPackageRowDiv = document.getElementById('addPackageRow');
        addPackageRowDiv.parentNode.insertBefore(packageElement, addPackageRowDiv);
        document.getElementById('package_addressline-'+packageCount).addEventListener('change', function(event) {
            updateDistances();
        });
        document.getElementById('package_postalcode-'+packageCount).addEventListener('change', function(event) { 
            updateDistances();
        });
        document.getElementById('packagetypeselect-'+packageCount).addEventListener('change', function(event) { 
            updatePrices();
        });
        movedownPackageButtonElement_local = document.getElementById('package_button_down-'+packageCount)
        movedownPackageButtonElement_local.addEventListener('click', function (event) {
            event.preventDefault();
            movePackage(movedownPackageButtonElement_local);
        });
        moveupPackageButtonElement_local = document.getElementById('package_button_up-'+packageCount)
        moveupPackageButtonElement_local.addEventListener('click', function (event) {
            event.preventDefault();
            movePackage(moveupPackageButtonElement_local);
        });

        addSearchAbilityToPackageDropOffNameField(packageElement.querySelector('[id^="package_name_search"]').id);
        updateDistances();
        updatePrices();
    });
    //============================================================
    addSearchAbilityToPackageDropOffNameField('package_name_search-0');
    
    document.getElementById('package_addressline-0').addEventListener('change', function(event) {
                                    updateDistances();
    });
    document.getElementById('package_postalcode-0').addEventListener('change', function(event) { 
                                    updateDistances();
    });

    function addTypeHeadSearch(searchInput){
        if (searchInput.length > 0) {
            searchInput.typeahead({
            source: function(query, process) {
                var apiUrl = "{{ route('client.searchClients') }}?query=" + query;
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
                        document.getElementById('billingClientIdField').value = data.id;
                        clientsPacakgeTypes = data.packageTypes;
                        populateFields('sender',data,clientsPacakgeTypes,true);
                        updateJobAddons(); 
                        updatePrices();
                           
                    }
                })
                .catch(error => {
                    console.error(error);
                });
            }
        });
        }
    }

    if (pickupClientSearchInput.length > 0) {
        pickupClientSearchInput.typeahead({
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
                        //console.log(data.packageTypes);
                        clientsPacakgeTypes = data.packageTypes;
                        populateFields('sender',data,clientsPacakgeTypes,false);    
                    }
                })
                .catch(error => {
                    console.error(error);
                });
            }
        });
    }
    function addSearchAbilityToPackageDropOffNameField(searchElementId) {
        var searchInput = $('#' + searchElementId);
        var splitId = searchElementId.split('-');
        var number = splitId[splitId.length - 1];
        var package_Element_Addressline = document.getElementById('package_addressline-'+number);
        var package_postalcode = document.getElementById('package_postalcode-'+number);
        var package_city = document.getElementById('package_city-'+number);
        var package_country = document.getElementById('package_country-'+number);
        var package_distance = document.getElementById('package_distance-'+number);
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
                                package_Element_Addressline.value = data.pickup_adress_line;
                                package_postalcode.value = data.pickup_postal_code;
                                package_city.value = data.pickup_city;
                                package_country.value = data.pickup_country;
                                address_destination = package_Element_Addressline.value+' '+package_postalcode.value+' '+package_city.value+' '+package_country.value;
                                updateDistances();

                            }
                        })
                        .catch(error => {
                            console.error(error);
                        });
                }
            });
        }
        function updateOriginAddress(){
            
            if(number == 0 ){
                console.log('Upadating origin address "number ==0" WAS : ',address_origin);
                address_origin = document.getElementById('pickupaddress_addressline').value+' '+
                document.getElementById('pickupaddress_postalcode').value+' '+
                document.getElementById('pickupaddress_city').value+' '+
                document.getElementById('pickupaddress_country').value;
                console.log('Upadating origin address "number !=0" NOW : ',address_origin);
            }else{
                console.log('Upadating origin address "number ==0" WAS : ',address_origin);
                address_origin = document.getElementById('package_addressline-'+parseInt(number-1)).value+' '+
                document.getElementById('package_postalcode-'+parseInt(number-1)).value+' '+
                document.getElementById('package_city-'+parseInt(number-1)).value+' '+
                document.getElementById('package_country-'+parseInt(number-1)).value;
                console.log('Upadating origin address "number !=0" NOW : ',address_origin);
            }
        }
        function updateDestinationAddress(){
            address_destination = package_Element_Addressline.value+' '+package_postalcode.value+' '+package_city.value+' '+package_country.value;
        }

        function updatePackageDistance(package_distance_innerVariable,address_origin,address_destination){
        var baseUrl = "{{ route('distance.getDistance') }}";
        var fullUrl = `${baseUrl}?origin=${encodeURIComponent(address_origin)}&destination=${encodeURIComponent(address_destination)}`;
        fetch(fullUrl)
            .then(response => response.json())
            .then(data => {
                //console.log('data is ',data);
                package_distance_innerVariable.innerHTML = data;
                totalDistanceOfTheJob = parseInt(totalDistanceOfTheJob)+parseInt(package_distance.innerHTML);
                totalDistanceOfTheJobElement.innerHTML = totalDistanceOfTheJob;
                //console.log('package_distance_innerVariable.innerHTML is ',package_distance_innerVariable.innerHTML);
            })
            .catch(error => {
                console.error(error);
            });
            //console.log(package_distance_innerVariable.innerHTML);
        }
    }
       
    // function onPackageTypeChange(event) {
    //     // You can access the selected value using event.target.value
    //     //console.log('Selected package type:', event.target);
    //     //console.log('Selected package price:',event.target.options[event.target.selectedIndex].getAttribute('data-price'));
    //     totalPriceOfTheJob=parseInt(totalPriceOfTheJob)+parseInt(event.target.options[event.target.selectedIndex].getAttribute('data-price'));
    //     totalPriceOfTheJobElement.textContent=totalPriceOfTheJob;
    // } 
    function populateFields(clientType,data,clientsPacakgeTypes,isItFromBillingInput){
            if(isItFromBillingInput){
                document.getElementById('pickup_name_search').value = data.name;
            }
            //totalPriceOfTheJobElement.textContent=0;
            //totalPriceOfTheJob=0;
            document.getElementById('pickupaddress_addressline').value = data.pickup_adress_line;
            document.getElementById('pickupaddress_postalcode').value = data.pickup_postal_code;
            document.getElementById('pickupaddress_city').value = data.pickup_city;
            document.getElementById('pickupaddress_country').value = data.pickup_country;
            var selects = document.querySelectorAll('.select-ClientPackageType');
            selects.forEach(function(select) {
                while (select.options.length > 0) {
                    select.remove(0);
                }
                clientsPacakgeTypes.forEach(function(pkg) {
                    //console.log(pkg.price);
                    var option = document.createElement('option');
                        option.value = pkg.id;
                        option.text = pkg.name;
                        option.setAttribute('data-price', pkg.price);
                        select.appendChild(option);
                });
                if (select.options.length > 0) {
                    select.selectedIndex = 0;
                    
                    //totalPriceOfTheJob=parseInt(totalPriceOfTheJob)+parseInt(select.options[select.selectedIndex].getAttribute('data-price'));
                    //totalPriceOfTheJobElement.textContent=totalPriceOfTheJob;
                    
                }
                select.previousPrice = select.options[select.selectedIndex].getAttribute('data-price');
                
                select.addEventListener('change', function(event) {
                    updatePrices();
                    //totalPriceOfTheJob=parseInt(totalPriceOfTheJob)-parseInt(select.previousPrice);
                    //onPackageTypeChange(event);
                    //select.previousPrice = parseInt(event.target.options[event.target.selectedIndex].getAttribute('data-price'));
                
                });
            });
        }
        document.querySelector('.alert.alert-danger.custom-class-job-create').style.display = 'none';


            // This code will run when the page is fully loaded
            // You can set the default pickup address here
            

//--------------------------
});

    









</script>
@endsection