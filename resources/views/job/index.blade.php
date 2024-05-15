@extends('layouts.app')
@section('style')
<style>
.container-content{
    /* border-style: double; */
}
.no-padding {
    padding: 0 !important;
}
</style>
@endsection
@section('content')
<div class="container container-content">
    <div class="d-flex justify-content-center mt-3">
            {!! $jobs->links() !!}
    </div>
    <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th rowspan="2">Id</th>
                <th rowspan="2">Client</th>
                <th rowspan="2">Date</th>
                <th colspan="4">Tasks</th>
                <th rowspan="2">Actions</th>
                <th rowspan="2" style="text-align:center;width:100px;">Create Job <button type="button" data-func="dt-add" class="btn btn-success btn-xs dt-add">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                    </button></th>
            </tr>
            <tr>
                <th>Pickup</th>
                <th>Drops</th>
                <th>Custom</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($jobs as $job)
            <tr>
                <td>
                    {{$job->id}}
                </td>
                <td class="no-padding">
                    <?php
                        $logoPath = "files/logos/{$job->clientToBill->id}.png";
                        if (file_exists(public_path($logoPath))) {
                            $logoUrl = asset($logoPath);
                        } else {
                            $logoUrl = asset("files/logos/0.png");
                        }
                    ?>
                    <img src='{{ $logoUrl }}' alt="Company Logo" style="max-width: 2rem;  height: auto;">
                    <span> {{$job->clientToBill->name}}</span>  
                </td>
                <td>            
                    {{date('d-m-Y',strtotime($job->pickup_time_begin))}}
                </td>
                <td>
                @foreach ($job->tasks as $task)                    
                    @if ($task->pickup)
                        <div @if(!$task->job->clientToBill->isSameAsPickupAdress($task->pickup->pickupAddressFull())) style="background-color: yellow;" @endif>
                        @if($task->job->clientToBill->isSameAsPickupAdress($task->pickup->pickupAddressFull()))
                            {{$task->job->clientToBill->shortenedNameWithoutterPostalCode().' '.$task->pickup->pickupclientpostalcode}}
                        @else
                            {{$task->job->clientToBill->shortenedNameWithoutterPostalCode().' '.$task->pickup->pickupclientpostalcode}}
                        @endif
                        </div>
                    @endif                             
                @endforeach
                </td>
                <td>
                @foreach ($job->tasks as $task)
                        
                    @if ($task->package)
                    <!-- <div class="row"><div class="col">{{$task->package->dropoff_name}}</div></div> -->
                    <div class="row">
                        <div class="col">
                            <blockquote class="blockquote">
                                <p class="mb-0">{{$task->package->dropoff_name}}@if($job->hasReturn())<i class="bi bi-arrow-counterclockwise" style="color: #00DD00;"></i>@endif</p>
                                <footer class="blockquote-footer"><cite title="Source Title">{{$task->fullAddress()}}</cite></footer>
                            </blockquote>
                        </div>
                    </div>           
                    @endif                               
                @endforeach
                </td>
                <td></td>
                <td></td>
                <td>
                <button class="btn btn-primary edit-btn" 
                                    data-jobid="{{ $job->id }}"
                                ><i class="bi bi-pen"></i></button>
                                <button class="btn btn-danger delete-btn" 
                                    data-jobid="{{ $job->id }}"
                                ><i class="bi bi-trash"></i></button>
                </td>   
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center mt-3">
            {!! $jobs->links() !!}
    </div>
<!-- Modal job begin -->

</div>
<div class="modal fade" id="jobModalWindow" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <form id="jobForm" action="" method="POST">
                    @csrf
                    <div class="row justify-content-md-center">
                        <div class="col-2">
                            <div class="row">
                                <input type="hidden" name="jobid" id="jobid" value="">
                                <label for="idField">Id</label>
                                <input class="form-control" type="text" name="id" id="idField" value="">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="row">
                                <label for="courierIdField">Courier</label>
                                <select id="courierIdField" name="courierId" class="form-control" >
                                    <option value="0">none</option>
                                    @foreach($couriers as $courier)
                                    <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                                    @endforeach                                  
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row">
                                <label for="statusIdField">Status</label>
                                <select id="statusIdField" name="statusId" class="form-control">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach 
                            </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row">
                                <label for="clientSearchField">CLient</label>
                                <input type="text" id="clientSearchField" name="clientName" class="form-control" placeholder="Search for clients">
                                <input type="hidden" name="clientId" id="clientIdField" value="">
                            </div>
                        </div>
                        <div class="col">
                            <div class="row">
                                <label for="jobDateField">CLient</label>
                                <input type="date" id="jobDateField" name="jobDate" class="form-control" placeholder="Search for clients">
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-md-center">
                        Tasks
                    </div>
                    <div class="row justify-content-md-center border rounded border-info" >
                        <div class="col" id="container-tasks">
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <button type="button" id="submitform" data-option="create" class="btn btn-success">Apply</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="jobModalWindowCloseButton">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal job end -->
<!-- Modal task begin -->
<div class="modal fade" id="taskModalWindow" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <form id="taskForm" action="" method="POST">
                    @csrf
                    <div class="row justify-content-md-center">
                        <div class="col-2">
                            <div class="row">
                                <input type="hidden" name="taskid" id="taskid" value="">
                                <label for="taskIdField">Id</label>
                                <input class="form-control" type="text" name="id" id="taskIdField" value="">
                            </div>
                        </div>
                        <div class="col">
                            <div class="row">
                                <label for="statusIdField">Status</label>
                                <select id="taskStatusIdField" name="statusId" class="form-control">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach 
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <label for="taskTypeField">Type</label>
                            <input class="form-control" type="text" name="taskTypename" id="taskTypeField" value="">
                        </div>
                    </div>
                    <div class="row justify-content-md-center">
                        <div class="col-auto">
                            <h3>Address</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-auto">
                            <label for="taskClientNameField">Name</label>
                            <input class="form-control" type="text" name="id" id="taskClientNameField" value="">
                        </div>
                        <div class="col-auto">
                            <label for="taskCountryField">Country</label>
                            <input class="form-control" type="text" name="id" id="taskCountryField" value="">
                        </div>
                        <div class="col-auto">
                            <label for="taskCityField">City</label>
                            <input class="form-control" type="text" name="id" id="taskCityField" value="">
                        </div>
                        <div class="col-auto">
                            <label for="taskPostalCodeField">Postal code</label>
                            <input class="form-control" type="text" name="id" id="taskPostalCodeField" value="">
                        </div>
                        <div class="col-auto">
                            <label for="taskAddressLineField">Adsress line</label>
                            <input class="form-control" type="text" name="id" id="taskAddressLineField" value="">
                        </div>
                    </div>
                    <div class="row justify-content-md-center">
                        <div class="col">
                            <h3>Time winow</h3>
                        </div>
                    </div>
                    <div class="row justify-content-md-center">
                        <div class="col-3 form-group">
                            <label for="taskTimeBegin">Begin</label>
                                <input type="time" id="taskTimeBegin" name="timeBegin" class="form-control">
                        </div>
                        <div class="col-3 form-group">
                            <label for="taskTimeEnd">End</label>
                                <input type="time" id="taskTimeEnd" name="timeEnd" class="form-control">
                        </div>
                    </div>
                    <div class="row justify-content-md-center border rounded border-info" >
                        <div class="col" id="package-info">
                        </div>          
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <button type="button" id="submitTaskform" data-option="create" class="btn btn-primary">Apply</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="taskModalWindowCloseButton">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal task end -->
@endsection
@section('scripts')
<script>
function addEventListner(button){
    button.addEventListener('click', (e) => {
        e.preventDefault();
        $('#jobModalWindow').modal('hide');
        $('#taskModalWindow').modal('show');
        taskId  =   parseInt(button.id.match(/task-(\d+)-button/)[1],10);
        setTaskValues(taskId);
    });
}
function addInfoAboutPackageToTaskModal(package){
    const container   =   document.getElementById('package-info');
    container.innerHTML = '';
    const select = document.createElement('select');
    const clientIdField =   document.getElementById('clientIdField');
    const routeUrl = "{{ route('getClientInfo', ['clientId' => ':clientId']) }}".replace(':clientId', clientIdField.value);
    fetch(routeUrl)
        .then(response => response.json())
        .then(data => {
            if(data.packageTypes !== 'none'){
                const select = document.createElement('select');
                select.id = 'packageTypeSelect';
                container.appendChild(select);
                data.packageTypes.forEach(packageType => {
                    const option = document.createElement('option');
                    option.value = packageType.id;
                    option.text = packageType.name;
                    select.appendChild(option);
                    if(option.value == package.type.id){
                        option.selected = true;
                    }
                });
                const inputQuantity = document.createElement('input');
                inputQuantity.type = 'number';
                inputQuantity.id = 'quantityInput';
                inputQuantity.min = '1'; // Only allow positive integers
                inputQuantity.placeholder = 'Enter quantity';
                inputQuantity.value = package.quantity;
                container.appendChild(inputQuantity);
            }
            
        })
        .catch(error => {
            console.error(error);
    });
}
function setTaskValues(taskId){
    const idField    =   document.getElementById('taskIdField');
    const typeField    =   document.getElementById('taskTypeField');
    const statusIdField     =   document.getElementById('taskStatusIdField');
    const clientNameField   =   document.getElementById('taskClientNameField');
    const addressCountryField   =   document.getElementById('taskCountryField');
    const addressCityField   =   document.getElementById('taskCityField');
    const addressPostalCodeField   =   document.getElementById('taskPostalCodeField');
    const addressAddressLineField   =   document.getElementById('taskAddressLineField');
    const timeBeginField   =   document.getElementById('taskTimeBegin');
    const timeEndField   =   document.getElementById('taskTimeEnd');
    idField.value = taskId;
    idField.disabled =  true;
    typeField.disabled =  true;
    //statusIdField.disabled = true;
    const routeUrl = "{{ route('task.getTaskInfo', ['id' => ':id']) }}".replace(':id', taskId);
    fetch(routeUrl)
        .then(response => response.json())
        .then(data => {
            console.log(data);
            typeField.value                 =   data.type;
            statusIdField.value             =   data.statusId;
            clientNameField.value           =   data.address.name;
            addressCountryField.value       =   data.address.country;
            addressCityField.value          =   data.address.city;
            addressPostalCodeField.value    =   data.address.postalCode;
            addressAddressLineField.value   =   data.address.addressLine;
            timeBeginField.value            =   data.time.begin;
            timeEndField.value              =   data.time.end;
            if(data.package !== 'none'){
                addInfoAboutPackageToTaskModal(data.package);
            }            
        })
        .catch(error => {
            console.error(error);
    });
}
function appendButtonsToTaskRowColumn(task){
    let colEdit = document.createElement('div');
    let colDelete = document.createElement('div');
    let row = document.createElement('div');
    let editButton  =   document.createElement('button');
    let deleteButton  =   document.createElement('button');

    editButton.textContent = 'Edit';
    editButton.className = 'btn btn-primary';
    editButton.id = `task-${task.id}-button-edit`;
    addEventListner(editButton);
    deleteButton.textContent = 'Delete';
    deleteButton.className = 'btn btn-danger';
    deleteButton.id = `task-${task.id}-button-delete`;
    addEventListner(deleteButton);
    colEdit.appendChild(editButton);
    colDelete.appendChild(deleteButton);

    colEdit.className = 'col border ';
    colDelete.className = 'col border ';
    row.className = 'row border ';
    row.id = `container-task-buttons-${task.id}`;
    row.appendChild(colEdit);
    row.appendChild(colDelete);
    return row;
}
function createColumn(idSuffix, content,id) {
    let col = document.createElement('div');
    col.className = 'col border';
    col.id = `container-task-${id}-${idSuffix}`;
    if (content instanceof HTMLElement) {
        col.appendChild(content);
    } else {
        col.textContent = content;
    }
    return col;
}
function appendTaskToContainer(container,task){
    // Create the main 'row' div
    let taskRow = document.createElement('div');
    taskRow.className = 'row';
    taskRow.id = 'container-task-0';

    // Create and append each column to the row
    taskRow.appendChild(createColumn('type', appendButtonsToTaskRowColumn(task),task.id));
    taskRow.appendChild(createColumn('type', task.name,task.id));
    taskRow.appendChild(createColumn('addressName', task.addressName,task.id));
    taskRow.appendChild(createColumn('address', task.fullAddress,task.id));
    taskRow.appendChild(createColumn('timeWindow', task.timeWindow,task.id));
    if(task.name === 'dropoff'){
        taskRow.appendChild(createColumn('quantity', task.quantity+' * '+task.packageType,task.id));
    }

    // Select the target container and append the new row to it
    container.appendChild(taskRow);
}
function setJobValues(jobId){
    const courierIdField    =   document.getElementById('courierIdField');
    const statusIdField    =   document.getElementById('statusIdField');
    const clientSearchField =   document.getElementById('clientSearchField');
    const clientIdField =   document.getElementById('clientIdField');
    const jobDateField =   document.getElementById('jobDateField');
    const containerTasks =   document.getElementById('container-tasks');
    const routeUrl = "{{ route('job.getJobInfo', ['id' => ':id']) }}".replace(':id', jobId);
    containerTasks.innerHTML = "";
    fetch(routeUrl)
        .then(response => response.json())
        .then(data => {
            // console.log(data);
            if(data.courierId === 'none'){
                courierIdField.value = 0;
            }else{
                courierIdField.value = data.courierId;
            }
            if(data.statusId === 'none'){
                statusIdField.value = 0;
            }else{
                statusIdField.value = data.statusId;
            }
            if(data.clientId === 'none'){
                clientSearchField.value = 'none';
                clientIdField = 'none';
            }else{
                clientSearchField.value =   data.clientName;
                clientIdField.value     =   data.clientId;
            }
            jobDateField.value = data.date;
            data.tasks.forEach(function(task){
                console.log(task);
                appendTaskToContainer(containerTasks,task);
            });            
        })
        .catch(error => {
            console.error(error);
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
            //fetch(`/get-client-info/${item.id}`)
            const clientInfoUrlTemplate = "{{ route('getClientInfo', ['clientId' => ':clientId']) }}";
            const clientInfoUrl = clientInfoUrlTemplate.replace(':clientId', item.id);
            fetch(clientInfoUrl)
            .then(response => response.json())
            .then(data => {
                if (data) {
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
function updateTask(data){
    console.log(data);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    // Send a POST request to the server using the generated route
    fetch('{{ route("task.update") }}', { // Blade syntax to generate the route URL
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json', // Set Accept header
            // Add any additional headers if needed
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        // if (!response.ok) {
        //     throw new Error('Failed to update job');
        // }
        return response.json();
    })
    .then(data => {
        console.log(data); // Log the success message
        // Optionally handle the response data, e.g., update UI
    })
    .catch(error => {
        console.error('Error:', error.message); // Log any errors
        // Optionally handle errors, e.g., display an error message
    });
}
document.addEventListener('DOMContentLoaded', function() {
    addTypeHeadSearch($('#clientSearchField'));
    document.querySelectorAll('.edit-btn').forEach(button => {

        button.addEventListener('click', () => {
            const jobid         =   button.dataset.jobid;
            const jobIdField    =   document.getElementById('idField');
            const courierIdField    =   document.getElementById('courierIdField');
            const jobName        =   button.dataset.name;
            const form = document.querySelector(`#jobForm`);
            if (form) {
                form.setAttribute('action', "{{ route('job.update') }}");
                jobIdField.value = jobid;
                jobIdField.readOnly = true;
                setJobValues(jobid);
                // document.getElementById('nameField').value = jobName;
                // document.getElementById('nameField').readOnly = false;
                // document.getElementById('colorPicker-main').value = jobColorMain;
                // document.getElementById('colorPicker-pickup').value = jobColorPickup;
                // document.getElementById('colorPicker-dropoff').value = jobColorDropoff;
                // document.getElementById('colorPicker-return').value = jobColorReturn;
                // document.getElementById('colorPicker-custom').value = jobColorCustom;
                // document.getElementById('colorPicker-main').disabled = false;
                // document.getElementById('colorPicker-pickup').disabled = false;
                // document.getElementById('colorPicker-dropoff').disabled = false;
                // document.getElementById('colorPicker-return').disabled = false;
                // document.getElementById('colorPicker-custom').disabled = false;
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-pen'></i>";
            }
            $('#jobModalWindow').modal('show');
        });
    });
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', () => {
            const jobid = button.dataset.jobid;
            const jobName = button.dataset.name;
            const jobColorMain   =   button.dataset.colormain;
            const jobColorPickup   =   button.dataset.colorpickup;
            const jobColorDropoff   =   button.dataset.colordropoff;
            const jobColorReturn   =   button.dataset.return;
            const jobColorCustom   =   button.dataset.custom;
            const form = document.querySelector(`#jobForm`);
            if (form) {
                form.setAttribute('action', "{{ route('job.delete') }}");
                document.getElementById('jobid').value = jobid;
                //document.getElementById('nameField').value = jobName;
                //document.getElementById('nameField').readOnly = true;
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-trash'></i>";
            }
            $('#jobModalWindow').modal('show');
        });
    });
    document.querySelectorAll('.create-btn').forEach(button => {
        button.addEventListener('click', () => {
            const form = document.querySelector(`#jobForm`);
            if (form) {
                document.getElementById('nameField').readOnly = false;
                document.getElementById('colorPicker-main').disabled = false;
                document.getElementById('colorPicker-pickup').disabled = false;
                document.getElementById('colorPicker-dropoff').disabled = false;
                document.getElementById('colorPicker-return').disabled = false;
                document.getElementById('colorPicker-custom').disabled = false;
                form.setAttribute('action', "{{ route('job.store') }}");
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-save'></i>";
            }
            $('#jobModalWindow').modal('show');
        });
    });
    document.getElementById('taskModalWindowCloseButton').addEventListener('click', function() {
        $('#jobModalWindow').modal('show');
        $('#taskModalWindow').modal('hide');
    });
    document.getElementById('submitTaskform').addEventListener('click', function() {
        const   typeField   =   document.getElementById('taskTypeField');
        var     type        =   ''
        updateData = {
            id          :   document.getElementById('taskIdField').value,
            status_id   :   document.getElementById('taskStatusIdField').value,
            address     :   {
                name            :   document.getElementById('taskClientNameField').value,
                country         :   document.getElementById('taskCountryField').value,
                city            :   document.getElementById('taskCityField').value,
                postalCode      :   document.getElementById('taskPostalCodeField').value,
                addressLine     :   document.getElementById('taskAddressLineField').value,
            },
            time        :   {
                begin   :   document.getElementById('jobDateField').value+' '+document.getElementById('taskTimeBegin').value,
                end     :   document.getElementById('jobDateField').value+' '+document.getElementById('taskTimeEnd').value,
            },
        }
        if(typeField.value == 'dropOff'){
            updateData.package = {
                type: document.getElementById('packageTypeSelect').value,
                quantity: document.getElementById('quantityInput').value,
            }
        }
        // console.log(updateData);
        updateTask(updateData);
    });
    document.getElementById('submitform').addEventListener('click', function() {
        // Get form data
        const form = document.getElementById('jobForm');
        const formData = new FormData(form);
        console.log(formData.get('jobid'));
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

</script>
@endsection