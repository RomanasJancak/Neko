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
<div class="alert alert-danger custom-class-job-create">
</div>
<div class="container mt-5">
    <div class="row">
        <div class="col-auto">
            <div class="row justify-content-md-center">
                <div class="col-md-4">
                    <h1>Job creation page</h1>
                </div>
            </div>            
            <form method="POST" action="{{ route('job.store') }}" class="row g-3" id="jobCreateForm">
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
                            <input type="text" id="billingclientsearch" name="billingclientName" class="form-control" placeholder="Search for clients">
                            <input type="hidden" name="billingClientId" id="billingClientIdField" value="">
                        </div>
                        @if ($customjob)
                        @else                        
                        <div class="form-group col-md-2">
                            <label for="checkButton-return_of_crates">Return of crates ? </label>
                            <input type="checkbox" id="checkButton-return_of_crates" name="isreturncreates">
                        </div>
                        @endif
                    </div>
                    <div class="row justify-content-md-center" id="job-addon-container">
                    </div>
                </div>
                @if ($customjob)
                <div class="row justify-content-md-center" id="area-customjob">
                    <div class="col">
                        <div class="row">
                            <div class="col">Custom Job information</div>
                        </div>
                        <div class="row justify-content-md-center">
                                <div class="col-12 text-center mb-2">
                                    <h5 class="mb-0">Time window</h5>
                                </div>
                                <div class="col-md-6 form-group">
                                    <input type="time" id="custom_time_begin" name="custom_time_begin" class="form-control">
                                </div>
                                <div class="col-md-6 form-group">
                                    <input type="time" id="custom_time_begin" name="custom_time_end" class="form-control">
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label for="return_name_search">Name of the job adress</label>
                                <input type="text"          class="form-control" name="returnclientname"        id="return_name_search"         placeholder="Adress name">
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="returnaddress_addressline">Address line</label>
                                <input type="text"          class="form-control" name="returnclientaddressline" id="returnaddress_addressline"  placeholder="Address line">     
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="returnaddress_postalcode">Postal code</label>
                                <input type="text"          class="form-control" name="returnclientpostalcode"  id="returnaddress_postalcode"   placeholder="Postal code">
                                <input hidden type="text"   class="form-control" name="returnclientcity"        id="returnaddress_city"         placeholder="City" value="London">
                                <input hidden type="text"   class="form-control" name="returnclientcountry"     id="returnaddress_country"      placeholder="Country" value="UK">     
                            </div>
                        </div>
                    </div>
                </div>    
                @else                
                <div class="row justify-content-md-center" id="area-pickup">
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
                                    <div><h6>Total distance :</h6><span id="total-distance"></span><span> miles</span></div>
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
                                <div>
                                    <input type="number" name="packagedropoffquantity[]" id="packageQuantity-0" class="form-control" placeholder="Quantity">
                                    <i class="bi bi-exclamation-triangle" style="display: none;"></i>
                                </div>
                                <button class="btn btn-secondary" data-direction="up" id="package_button_up-0"><i class="bi bi-arrow-up-circle-fill"></i></button>
                                <button class="btn btn-secondary" data-direction="down" id="package_button_down-0"><i class="bi bi-arrow-down-circle-fill"></i></button>
                            </div>
                            <div class="col-auto">
                                <span>Drop off</span>
                                <div class="row">
                                    <input type="text"          name="packagedropoffname[]" id="package_name_search-0" class="form-control" placeholder="Name">
                                    <input type="text"          name="packagedropooffaddressline[]" id="package_addressline-0" class="form-control" placeholder="Addressline">
                                    <input type="text"          name="packagedropoffpostalcode[]" id="package_postalcode-0" class="form-control" placeholder="Postal Code">
                                    <input hidden type="text"   name="packagedropoffcity[]" id="package_city-0" class="form-control" placeholder="City" value="London">
                                    <input hidden type="text"   name="packagedropoffcountry[]" id="package_country-0" class="form-control" placeholder="Country" value="UK">
                                    <input type="time" id="package_timebegin-0" name="packagedropofftimebegin[]" class="form-control" value="09:00">
                                    <input type="time" id="package_timeend-0" name="packagedropofftimeend[]" class="form-control" value="16:00">
                                    <div>Distance : <span id="package_distance-0">0</span> miles</div>
                                    <div>Price : <span id="package_price-0">0</span><span>&#163;</span></div>                         
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="row"><div><h6>Add Ons</h6></div></div>
                                <div class="row" id='packageaddoncontainer-0'>

                                </div>
                            </div>
                        </div>
                        <div class="row" id='addPackageRow'>
                            <div class="col">
                                <button class="btn btn-secondary" id='addPackageButton'>Add package</button>
                            </div>
                        </div>
                </div>
                <div class="row justify-content-md-center" id='area-return' style="display: none;">
                    <div class="col-12 text-center mb-2">
                        <div class="row justify-content-md-center">
                            <div class="col-12 text-center mb-2">
                                <h4>Return</h4>
                            </div>
                        </div>
                        <div class="row justify-content-md-center">
                            <div class="col-md-3 form-group">
                                <label for="return_name_search">Name of the return adress</label>
                                <input type="text"          class="form-control" name="returnclientname"        id="return_name_search"         placeholder="Adress name">
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="returnaddress_addressline">Address line</label>
                                <input type="text"          class="form-control" name="returnclientaddressline" id="returnaddress_addressline"  placeholder="Address line">     
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="returnaddress_postalcode">Postal code</label>
                                <input type="text"          class="form-control" name="returnclientpostalcode"  id="returnaddress_postalcode"   placeholder="Postal code">
                                <input hidden type="text"   class="form-control" name="returnclientcity"        id="returnaddress_city"         placeholder="City" value="London">
                                <input hidden type="text"   class="form-control" name="returnclientcountry"     id="returnaddress_country"      placeholder="Country" value="UK">     
                            </div>
                            <div class="col-md-9 form-group">
                                <div class="row justify-content-md-center">
                                    <div class="col-12 text-center mb-2">
                                        <h5 class="mb-0">Time window</h5>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <input type="time" id="return_time_begin" name="return_time_begin" class="form-control">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <input type="time" id="return_time_end" name="return_time_end" class="form-control">
                                    </div>
                                </div>
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
                @endif  

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
                <button type="submit" class="btn btn-primary" id="submitform">Create Job<br><span id="job-creation-button-extra-text"></span></button>
            </form>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    const commonDate = document.getElementById('common_date');
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
document.addEventListener('DOMContentLoaded', function() {
    const pickupClientSearchInput =  $('#pickup_name_search');
    const billingClientSearchInput = $('#billingclientsearch');
    const returnClientSearchInput  =   $('#return_name_search');
    addTypeHeadSearchToReturn(returnClientSearchInput);
    const checkInputElementForReturn    =   document.getElementById("checkButton-return_of_crates");
    if(checkInputElementForReturn){
        checkInputElementForReturn.addEventListener('change', function (event) {
            const areaForReturn    =   document.getElementById("area-return");
            if(checkInputElementForReturn.checked){
                areaForReturn.style.display = "";
            }else{
                areaForReturn.style.display = "none";
            }   
        });
    }
    function populateReturnAddressValues(data){
        document.getElementById('return_name_search').value = data.name;
        document.getElementById('returnaddress_addressline').value = data.pickup_adress_line;
        document.getElementById('returnaddress_postalcode').value = data.pickup_postal_code;
        document.getElementById('returnaddress_city').value = data.pickup_city;
        document.getElementById('returnaddress_country').value = data.pickup_country;
    }
    function addTypeHeadSearchToReturn(searchInput){
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
                const routeUrl = "{{ route('getClientInfo', ['clientId' => ':id']) }}".replace(':id', item.id);
                fetch(routeUrl)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        populateReturnAddressValues(data);
                    }
                })
                .catch(error => {
                    console.error(error);
                });
            }
        });
        }
    }
    
    autoFillForm();
    packageAddonContainer    =   document.getElementById("packageaddoncontainer-0");
    updatePackageAddons(packageAddonContainer);
    
    function autoFillForm() {
        var today = new Date();
        var month = (today.getMonth() + 1).toString().padStart(2, '0');
        var day = today.getDate().toString().padStart(2, '0');
        var formattedDate = today.getFullYear() + '-' + month + '-' + day;
        // Example values to fill the form
        var formData = {
            status_id: 10,   // Assuming 1 is a valid status ID
            common_date: formattedDate,

            billingclientName: 'Neko home Delivery LLP',
            billingclientId: 1,

            pickupclientname: 'Neko home Delivery LLP',pickupclientaddressline: 'FLAT 22 BAKERSFIELD',pickupclientpostalcode: 'N7 0LT',pickupclientcity: 'London',pickupclientcountry: 'Uk',
            package_dropOff_address_name : 'Athlyn Flower',package_dropOff_addressLine : 'Unit 10',package_dropOff_postalCode: 'N15 4QN',package_dropOff_city: 'London',package_dropOff_Country:'Uk',
            pickup_time_begin: '08:00',
            pickup_time_end: '17:00',
            generalnotes: 'Some notes here'
        };

        // Set the values for each form field
        document.getElementById('status_id').value = formData.status_id;
        document.getElementById('common_date').value = formData.common_date;
        //document.getElementById('billingclientsearch').value = formData.billingclientName;
        document.getElementById('billingClientIdField').value = formData.billingclientId;

        document.getElementById('pickup_name_search').value = formData.pickupclientname;
        document.getElementById('pickupaddress_addressline').value = formData.pickupclientaddressline;
        document.getElementById('pickupaddress_postalcode').value = formData.pickupclientpostalcode;
        document.getElementById('pickupaddress_city').value = formData.pickupclientcity;
        document.getElementById('pickupaddress_country').value = formData.pickupclientcountry;

        document.getElementById('pickup_time_begin').value = formData.pickup_time_begin;
        document.getElementById('pickup_time_end').value = formData.pickup_time_end;

        document.getElementById('package_name_search-0').value = formData.package_dropOff_address_name;
        document.getElementById('package_addressline-0').value = formData.package_dropOff_addressLine;
        document.getElementById('package_postalcode-0').value = formData.package_dropOff_postalCode;
        document.getElementById('package_city-0').value = formData.package_dropOff_city;
        document.getElementById('package_country-0').value = formData.package_dropOff_Country;

        document.getElementById('generalnotes').value = formData.generalnotes;
    }
    function populateFields(data,clientsPacakgeTypes,isItFromBillingInput){
            if(isItFromBillingInput){
                document.getElementById('pickup_name_search').value = data.name;
            }
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
                    var option = document.createElement('option');
                        option.value = pkg.id;
                        option.text = pkg.name;
                        option.setAttribute('data-price', parseFloat(pkg.price/100));
                        option.setAttribute('data-baseQuantityThreshold', pkg.baseQuantityThreshold);
                        option.setAttribute('data-maxQuantityThreshold', pkg.maxQuantityThreshold);
                        select.appendChild(option);
                });
                if (select.options.length > 0) {
                    select.selectedIndex = 0;                    
                }
                select.previousPrice = select.options[select.selectedIndex].getAttribute('data-price');
                
                select.addEventListener('change', function(event) {
                    updateQuantityRelatedThings();
                    updatePrices();                
                });
            });
    }
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
                        distance = data*0.000621371192;
                        finalDistance+=distance;
                        document.getElementById('package_distance-'+id).innerHTML = parseFloat(distance).toFixed(3); 
                        document.getElementById('total-distance').innerHTML = parseFloat(finalDistance).toFixed(3);
                        updatePrices(); 
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }                    
        });        
    }
    function updatePrices(){
        var finalPrice = 0;
        let totalValueOfCheckedAddOns = 0;
        document.querySelectorAll('[id^="packageaddoncontainer-"]').forEach(packageaddoncontainerElement => {
            const checkboxes = packageaddoncontainerElement.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) { 
                    totalValueOfCheckedAddOns += parseFloat(checkbox.getAttribute('data-value')).toFixed(2);
                }
            });
        });
        finalPrice+=totalValueOfCheckedAddOns;
        document.querySelectorAll('[id^="packagetypeselect-"]').forEach(packageSelectElement => {
            if(packageSelectElement.options[packageSelectElement.selectedIndex]){
            var id = packageSelectElement.id.split('-')[packageSelectElement.id.split('-').length - 1];
            var price=parseFloat(packageSelectElement.options[packageSelectElement.selectedIndex].getAttribute('data-price'));
            finalPrice+=price;

            document.getElementById('package_price-'+id).innerHTML = parseFloat(parseFloat(price).toFixed(2)+parseFloat(totalValueOfCheckedAddOns).toFixed(2)).toFixed(2); 
            }
        });
        


        //document.getElementById('total-price').innerHTML = finalPrice;
        updatePriceForDistance(finalPrice);
        //updatePriceForAddOns(finalPrice)
        document.getElementById('total-price').innerHTML = parseFloat(parseFloat(finalPrice)+parseFloat(updatePriceForAddOns(finalPrice))).toFixed(2);
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
    function addInputCheckBox(container,rule,visibilityStatus = 'none',checkedStatus = false){
        const divElement = document.createElement('div');
        divElement.classList.add('form-group');
        divElement.classList.add('col-1');
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.id = container.id+'-'+rule.id // Set the id attribute to a unique identifier, e.g., rule name
        if (container.id.split('-')[0] === 'job') {
            checkbox.name = 'jobcheckboxaddon[]'; // Set the name attribute if needed
        }else{
            var id = container.id.split('-')[container.id.split('-').length - 1];
            checkbox.name = 'packagecheckboxaddon['+id+'][]';
        }
        checkbox.setAttribute('data-codename', rule.name);
        checkbox.setAttribute('data-value', parseFloat(rule.price/100).toFixed(2));
        checkbox.value = rule.id;
        checkbox.checked = checkedStatus;
        // Create a label element associated with the checkbox (optional)
        const label = document.createElement('label');
        label.textContent = rule.display_name; // Label text, you can customize it as needed
        label.setAttribute('for', checkbox.id); // Set 'for' attribute to match the checkbox's id
        label.classList.add('btn');
        label.classList.add('btn-primary');
        checkbox.style.display = visibilityStatus;
        label.style.display = visibilityStatus;
        label.style.backgroundColor = '#ffcc00';
        label.style.color = '#000000';
        container.appendChild(divElement);
        divElement.appendChild(label);
        divElement.appendChild(checkbox);
        return checkbox;
    }
    function updateJobAddons(){
        jobAddonContainer    =   document.getElementById('job-addon-container');
        date                =   document.getElementById('common_date').value;
        billingClientId       =   document.getElementById('billingClientIdField').value;
        //console.log(date+' '+billingClientId);
        if(date && billingClientId){
            jobAddonContainer.innerHTML = '';
        const routeUrl = "{{ route('addonrule.getRulesForDateAndClient', ['date' => ':date','client' =>':clientId']) }}".replace(':date', date).replace(':clientId',billingClientId);

                fetch(routeUrl)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(function (element){
                            if(element.name.split('-')[0] === 'job'){
                                if(element.name.split('-')[1] === 'distance'){
                                    addInputCheckBox(jobAddonContainer,element,'none',true).addEventListener('change', function (event) {

                                    });
                                }else{
                                    addInputCheckBox(jobAddonContainer,element,'',false).addEventListener('change', function (event) {
                                        console.log('Checked :',element);
                                    });
                                }

                            }
                        });
                    })
                    .catch(error => {
                        console.error(error);
                    });
        }
    }
    function updatePackageAddons(packageAddonContainer){
        console.log('0');
        date                =   document.getElementById('common_date').value;
        billingClientId       =   document.getElementById('billingClientIdField').value;
        //console.log(date+' '+billingClientId);
        if(date && billingClientId){
            packageAddonContainer.innerHTML = '';
        const routeUrl = "{{ route('addonrule.getRulesForDateAndClient', ['date' => ':date','client' =>':clientId']) }}".replace(':date', date).replace(':clientId',billingClientId);

                fetch(routeUrl)
                    .then(response => response.json())
                    .then(data => {
                        console.log('0');
                        console.log(data);
                        data.forEach(function (element){
                            if(element.name.split('-')[0] === 'package'){
                                addInputCheckBox(packageAddonContainer,element).addEventListener('change', function (event) {
                                    updatePrices();
                                });
                            }
                        });
                    })
                    .catch(error => {
                        console.error(error);
                    });
        }
    }
    function updatePriceForDistance(finalPrice){
        billingClientId       =   document.getElementById('billingClientIdField').value;
        date                =   document.getElementById('common_date').value;
        distance            =   document.getElementById('total-distance').innerHTML;
        const routeUrl = "{{ route('addonrule.getPriceForDistance', ['date' => ':date','client' =>':clientId','distance' =>':distance']) }}"
            .replace(':date', date).replace(':clientId',billingClientId).replace(':distance',distance);
            //console.log(routeUrl);
        if(date && billingClientId && distance){
        fetch(routeUrl)
            .then(response => response.json())
            .then(data => {
                document.getElementById('total-price').innerHTML = parseFloat(parseFloat(finalPrice)+parseFloat(data/100)).toFixed(2);
            })
            .catch(error => {
                console.error(error);
        });
        }
    }
    function updatePriceForAddOns(finalPrice){
        let totalValueOfCheckedAddOns = 0;
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="checkboxaddon[]"]');
        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) { 
                totalValueOfCheckedAddOns += parseFloat(checkbox.value);
            }
        });
        return parseFloat(totalValueOfCheckedAddOns).toFixed(2);
    }
    //========================================================
    const addPackageButtonElement = document.getElementById('addPackageButton');
    var clientsPacakgeTypes;
    
    addTypeHeadSearch(billingClientSearchInput);
    

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

// Dispatch the event on the jobCreationDatePicker element
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
        packageElement.querySelector('[id^="labelpackagetypeselect"]').setAttribute('for', 'packagetypeselect-' + packageCount);
        packageElement.querySelector('[id^="package_price"]').id = 'package_price-' + packageCount;
        packageElement.querySelector('[id^="package_button_up"]').id = 'package_button_up-' + packageCount;
        packageElement.querySelector('[id^="package_button_down"]').id = 'package_button_down-' + packageCount;

        packageElement.querySelector('[id^="package_timebegin"]').id = 'package_timebegin-' + packageCount;
        packageElement.querySelector('[id^="package_timeend"]').id = 'package_timeend-' + packageCount;

        packageElement.querySelector('[id^="packageaddoncontainer"]').id = 'packageaddoncontainer-' + packageCount;
        updatePackageAddons(packageElement.querySelector('[id^="packageaddoncontainer"]'));
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
        document.getElementById('packageQuantity-'+packageCount).addEventListener('change', function(event) { 
            updateQuantityRelatedThings();
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
    document.getElementById('packageQuantity-0').addEventListener('change', function(event) {
        updateQuantityRelatedThings();
        updatePrices();
    });
    function updateQuantityRelatedThings(){
        let thereIsAQuantityOverMaxQuantityThreshold = false;
        document.querySelectorAll('[id^="package-"]').forEach(packageElement => {
            const id = packageElement.id.split('-')[packageElement.id.split('-').length - 1];
            const checkbox = document.getElementById('packageaddoncontainer-'+id+'-6');
            console.log(checkbox);
            const label = document.querySelector(`label[for="`+checkbox.id+`"]`);   
            const quantity = parseInt(document.getElementById('packageQuantity-'+id).value);
            const selectElement = document.getElementById('packagetypeselect-'+id);
            const selectedIndex = selectElement.selectedIndex;
            const selectedOption = selectElement.options[selectedIndex];
            const baseQuantityThreshold = parseInt(selectedOption.getAttribute('data-baseQuantityThreshold'));
            const maxQuantityThreshold = parseInt(selectedOption.getAttribute('data-maxQuantityThreshold'));
            const extraTextElement = document.getElementById("job-creation-button-extra-text");
            if(quantity >= baseQuantityThreshold){
                checkbox.checked = true;
                label.style.display = '';
            }else{
                checkbox.checked = false;
                label.style.display = 'none';
            }
            if(quantity >= maxQuantityThreshold){
                thereIsAQuantityOverMaxQuantityThreshold = true;
                packageElement.style.backgroundColor = '#f5e4d0';
            }else{
                packageElement.style.backgroundColor = '';
            }
            if(thereIsAQuantityOverMaxQuantityThreshold){
                extraTextElement.innerHTML = " Warning : will need confirmation ";
                extraTextElement.style.backgroundColor = 'rgb(217 124 14)';
                extraTextElement.style.color = '#000000';
                extraTextElement.style.borderRadius = '5px';
                extraTextElement.style.border = '1px solid black';
            }else{
                extraTextElement.innerHTML = "";
                extraTextElement.style.border = '';
            }
        });
    }
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
                        populateFields(data,clientsPacakgeTypes,true);
                        populateReturnAddressValues(data);
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
                        populateFields(data,clientsPacakgeTypes,false);    
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
    }   
        document.querySelector('.alert.alert-danger.custom-class-job-create').style.display = 'none';           

//--------------------------
    document.getElementById('submitform').addEventListener('click', function() {
        event.preventDefault();
        // Get form data
        const form = document.getElementById('jobCreateForm');
        const formData = new FormData(form);
        //console.log(formData.get('addonruleid'));
        //console.log(formData.get('workloadid'));

        // Create a new XMLHttpRequest object
        const xhr = new XMLHttpRequest();

        // Define the request type, URL, and set up the request
        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('X-CSRF-Token', '{{ csrf_token() }}'); // Replace with your CSRF token if not using Blade
        xhr.setRequestHeader('Accept', 'application/json');
        // Handle the response
        xhr.onload = function() {
            if(xhr.status === 422) {
                const errors = JSON.parse(xhr.responseText).errors;
                let errorDisplayDiv = document.querySelector('.alert.alert-danger.custom-class-job-create');
                errorDisplayDiv.innerHTML = "";                
                const divElement = document.createElement('div');
                const ulElement = document.createElement('ul');
                Object.keys(errors).forEach((field) => {
                    errors[field].forEach((error) => {
                        const liElement = document.createElement("li");
                        liElement.textContent = `${field}: ${error}`;
                        ulElement.appendChild(liElement);
                    });
                });
                divElement.appendChild(ulElement);
                errorDisplayDiv.appendChild(divElement);
                errorDisplayDiv.style.display = 'inline-block';
                errorDisplayDiv.scrollIntoView({ behavior: "smooth" });
            }else{
                document.querySelector('.alert.alert-danger.custom-class-job-create').style.display = 'none';
                console.log(JSON.parse(xhr.responseText));
            }
        };
        xhr.onerror = function () {
            // Handle network errors or other unexpected issues here
        };
        // Send the request
        xhr.send(formData);
    });
});

    









</script>
@endsection