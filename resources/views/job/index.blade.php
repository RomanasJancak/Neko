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
                        <div @if(!$task->job->clientToBill->isSameAsPickupAdress($task->pickup->pickupAddressFull())) style="background-color: rgb(141, 153, 80);" @endif>
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
                <?php $PackageCounter = 1;?>
                @foreach ($job->tasks as $task)
                        
                    @if ($task->package)
                    <div class="row">
                        <div class="col">
                            <div class="row justify-content-center">
                                <div class="col">
                                    <blockquote class="blockquote border">
                                        <h6>Package No [ {{$PackageCounter++}} ]</h6>
                                        <p class="mb-0" style="padding-bottom: 1rem;">{{$task->package->dropoff_name}}@if($job->hasReturn())<i class="bi bi-arrow-counterclockwise" style="color: #00DD00;"></i>@endif</p>
                                        <footer class="blockquote-footer"><cite title="Source Title">{{$task->postalCode()}}, {{$task->addressLine()}}</cite></footer>
                                    </blockquote>
                                </div>
                            </div>
                        </div>
                    </div>           
                    @endif                               
                @endforeach
                </td>
                <td></td>
                <td></td>
                <td>
                    <button class="btn btn-success view-btn" data-jobid="{{ $job->id }}">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-primary edit-btn" data-jobid="{{ $job->id }}">
                        <i class="bi bi-pen"></i>
                    </button>
                    <button class="btn btn-danger delete-btn" data-jobid="{{ $job->id }}">
                        <i class="bi bi-trash"></i>
                    </button>
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
                                <label for="jobDateField">Date</label>
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
                    <div class="row justify-content-md-center">
                        <div class="col">
                            <button type="button" id="createNewTask" data-option="create" class="btn btn-primary">Create new Task</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer ">
                <!-- <div class="row"> -->
                    <div class="col-12">
                        <div class="form-group d-flex justify-content-between">
                            <button type="button" id="submitform" data-option="create" class="btn btn-success">Confirm</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="jobModalWindowCloseButton">Cancel</button>
                        </div>
                    </div>
                    <!-- <div class="col-auto"></div> -->
                <!-- </div>                 -->
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
                            <!-- <input class="form-control" type="text" name="taskTypename" id="taskTypeField" value=""> -->
                            <select class="form-control" name="taskTypename" id="taskTypeField">
                                <option value="pickup">Pickup</option>
                                <option value="dropOff">DropOff</option>
                                <option value="return">Return</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                    </div>
                    <div class="row justify-content-md-center">
                        <div class="col-auto">
                            <h3>Address</h3>
                        </div>
                    </div>
                    <div class="row justify-content-md-center">
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
                    <div class="row justify-content-md-center">
                        <div class="col" id="package-info">
                        </div>          
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-12">
                    <div class="form-group d-flex justify-content-between">
                        <button type="button" id="submitTaskform" data-option="create" class="btn btn-primary">Apply</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="taskModalWindowCloseButton">Close</button>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
<!-- Modal task end -->
@endsection
@section('scripts')
<script>
function showPackageDiv(status){
    const container =   document.getElementById('package-info');
    if(status){
        container.style.visibility = 'visible';
    }else{
        container.style.visibility = 'hidden';
        container.innerHTML =   '';
    }
}
function setReadOnlyToFieldsOfTaskModal(status){
    let fields = [];
    fields.push(document.getElementById('taskStatusIdField'));
    fields.push(document.getElementById('taskClientNameField'));
    fields.push(document.getElementById('taskCountryField'));
    fields.push(document.getElementById('taskCityField'));
    fields.push(document.getElementById('taskPostalCodeField'));
    fields.push(document.getElementById('taskAddressLineField'));
    fields.push(document.getElementById('taskTimeBegin'));
    fields.push(document.getElementById('taskTimeEnd'));//package-info
    fields.forEach(function(field){
        field.disabled = status;
    });
}
function cleanTaskCreateWindow(){
    let selectStatusField =   document.getElementById('taskStatusIdField');
    let selectTypeField =   document.getElementById('taskTypeField');
    let taskClientNameField =   document.getElementById('taskClientNameField');
    let taskPostalCodeField =   document.getElementById('taskPostalCodeField');
    let taskAddressLineField =   document.getElementById('taskAddressLineField');
    let taskTimeBegin =   document.getElementById('taskTimeBegin');
    let taskTimeEnd =   document.getElementById('taskTimeEnd');
    // let selectTypeField =   document.getElementById('taskTypeField');
    // let selectTypeField =   document.getElementById('taskTypeField');
    selectStatusField.selectedIndex = -1;
    selectTypeField.selectedIndex = -1;
    selectTypeField.disabled = false;
    taskClientNameField.value   =   '';
    taskPostalCodeField.value   =   '';
    taskAddressLineField.value   =   '';
    taskTimeBegin.value   =   '';
    taskTimeEnd.value   =   '';
}
function addEventListenerToButton(button){
    button.addEventListener('click', (e) => {
        e.preventDefault();
        $('#jobModalWindow').modal('hide');
        let submitButton = document.getElementById('submitTaskform');
        if(button.id === 'createNewTask'){
            setReadOnlyToFieldsOfTaskModal(false);
            cleanTaskCreateWindow();
            button.setAttribute('data-option', 'create');            
            submitButton.setAttribute('data-option', 'create');
            submitButton.textContent  = 'Confirm creation';
        }else{
            taskId  =   parseInt(button.id.match(/task-(\d+)-button/)[1],10);
            setTaskValues(taskId).then(() =>{
            if(button.id.match(/task-(\d+)-button-edit/)){
                setReadOnlyToFieldsOfTaskModal(false);
                button.setAttribute('data-option', 'edit');            
                submitButton.setAttribute('data-option', 'edit');
                submitButton.textContent  = 'Confirm edit';
            }
            if(button.id.match(/task-(\d+)-button-delete/)){
                setReadOnlyToFieldsOfTaskModal(true);
                button.setAttribute('data-option', 'delete');
                submitButton.setAttribute('data-option', 'delete');
                submitButton.textContent  = 'Confirm delete';
            }
            if(button.id.match(/task-(\d+)-button-view/)){
                setReadOnlyToFieldsOfTaskModal(true);
                button.setAttribute('data-option', 'view');
                submitButton.setAttribute('data-option', 'view');
                submitButton.textContent  = 'Confirm view';
            }
            });
        }
        $('#taskModalWindow').modal('show');
    });
}
function addInfoAboutPackageToTaskModal(package){   
    const container =   document.getElementById('package-info');
    container.innerHTML = '';
    const select = document.createElement('select');
    const clientIdField =   document.getElementById('clientIdField');
    const routeUrl = "{{ route('getClientInfo', ['clientId' => ':clientId']) }}".replace(':clientId', clientIdField.value);
    return fetch(routeUrl)
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
                let submitButton = document.getElementById('submitTaskform');
                if(submitButton.getAttribute('data-option') === 'edit'){
                    document.getElementById('packageTypeSelect').disabled = false;
                    document.getElementById('quantityInput').disabled = false;
                }else{
                    document.getElementById('packageTypeSelect').disabled = true;
                    document.getElementById('quantityInput').disabled = true;
                }
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
    return fetch(routeUrl)
        .then(response => response.json())
        .then(data => {
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
                showPackageDiv(true);

            }else{
                showPackageDiv(false);
            }            
        })
        .catch(error => {
            console.error(error);
    });
}

function appendButtonsToTaskRowColumn(task,buttonClicked){
    let colView = document.createElement('div');
    let colEdit = document.createElement('div');
    let colDelete = document.createElement('div');
    
    let row = document.createElement('div');
    let editButton  =   document.createElement('button');
    let deleteButton  =   document.createElement('button');
    let viewButton  =   document.createElement('button');

    editButton.textContent = 'Edit';
    editButton.className = 'btn btn-primary';
    editButton.id = `task-${task.id}-button-edit`;
    deleteButton.textContent = 'Delete';
    deleteButton.className = 'btn btn-danger';
    deleteButton.id = `task-${task.id}-button-delete`;
    viewButton.textContent = 'View';
    viewButton.className = 'btn btn-success';
    viewButton.id = `task-${task.id}-button-view`;
    colView.appendChild(viewButton);
    colEdit.appendChild(editButton);
    colDelete.appendChild(deleteButton);

    colEdit.className = 'col border ';
    colDelete.className = 'col border ';
    row.className = 'row border ';
    row.id = `container-task-buttons-${task.id}`;
    if(buttonClicked === 'view'){
        row.appendChild(colView);
    }else if(buttonClicked === 'edit'){
        row.appendChild(colView);
        row.appendChild(colEdit);
        row.appendChild(colDelete);
    }else if(buttonClicked === 'delete'){
        row.appendChild(colView);
    }
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
function formatDateTimeStringTo12HourFormat(dateString) {
            const date = new Date(dateString);
            let hours = date.getHours();
            const minutes = date.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            const minutesStr = minutes < 10 ? '0' + minutes : minutes;
            return hours + ':' + minutesStr + ' ' + ampm;
        }
function appendTaskToContainer(container,task,buttonClicked){
    // Create the main 'row' div
    let taskRow = document.createElement('div');
    taskRow.className = 'row';
    taskRow.id = 'container-task-0';

    // Create and append each column to the row
    taskRow.appendChild(createColumn('type', appendButtonsToTaskRowColumn(task,buttonClicked),task.id));
    taskRow.appendChild(createColumn('type', task.name,task.id));
    taskRow.appendChild(createColumn('addressName', task.addressName,task.id));
    taskRow.appendChild(createColumn('address', task.fullAddress,task.id));
    taskRow.appendChild(createColumn('timeWindow', formatDateTimeStringTo12HourFormat(task.timeWindow.begin)+' / '+formatDateTimeStringTo12HourFormat(task.timeWindow.end),task.id));
    if(task.name === 'dropoff'){
        taskRow.appendChild(createColumn('quantity', task.quantity+' * '+task.packageType,task.id));
    }
    container.appendChild(taskRow);
    if(document.getElementById(`task-${task.id}-button-view`)){
        addEventListenerToButton(document.getElementById(`task-${task.id}-button-view`));
    }
    if(document.getElementById(`task-${task.id}-button-edit`)){
        addEventListenerToButton(document.getElementById(`task-${task.id}-button-edit`));
    }
    if(document.getElementById(`task-${task.id}-button-delete`)){
        addEventListenerToButton(document.getElementById(`task-${task.id}-button-delete`));   
    }
     
}
function setJobValues(jobId,buttonClicked){
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
                appendTaskToContainer(containerTasks,task,buttonClicked);
            });
            if(document.getElementById(`createNewTask`)){
                addEventListenerToButton(document.getElementById(`createNewTask`)); 
            }            
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
function updateTask(data,route){
    console.log(data);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    // Send a POST request to the server using the generated route
    fetch(route, { // Blade syntax to generate the route URL
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
            const createNewTaskButton   =   document.getElementById('createNewTask'); 
            const form = document.querySelector(`#jobForm`);
            if (form) {
                form.setAttribute('action', "{{ route('job.update') }}");
                jobIdField.value = jobid;
                jobIdField.disabled = true;
                document.getElementById('courierIdField').disabled = false;
                document.getElementById('statusIdField').disabled = false;
                document.getElementById('clientSearchField').disabled = false;
                document.getElementById('jobDateField').disabled = false;
                
                setJobValues(jobid,'edit');
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-pen'></i>";
                submitButton.style.visibility = 'visible';
                createNewTaskButton.style.visibility = 'visible';
            }
            $('#jobModalWindow').modal('show');
        });
    });
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', () => {
            const jobid = button.dataset.jobid;
            const jobName = button.dataset.name;
            const jobIdField    =   document.getElementById('idField');
            const courierIdField    =   document.getElementById('courierIdField');
            const createNewTaskButton   =   document.getElementById('createNewTask');
            const form = document.querySelector(`#jobForm`);
            if (form) {
                form.setAttribute('action', "{{ route('job.delete') }}");
                jobIdField.value = jobid;
                jobIdField.disabled = true;
                document.getElementById('courierIdField').disabled = true;
                document.getElementById('statusIdField').disabled = true;
                document.getElementById('clientSearchField').disabled = true;
                document.getElementById('jobDateField').disabled = true;
                document.getElementById('jobid').value = jobid;
                setJobValues(jobid,'delete');
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-trash'></i>";
                submitButton.style.visibility = 'visible';
                createNewTaskButton.style.visibility = 'hidden';
            }
            $('#jobModalWindow').modal('show');
        });
    });
    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', () => {
            const jobid = button.dataset.jobid;
            const jobName = button.dataset.name;
            const jobIdField    =   document.getElementById('idField');
            const courierIdField    =   document.getElementById('courierIdField');
            const createNewTaskButton   =   document.getElementById('createNewTask');
            const form = document.querySelector(`#jobForm`);
            if (form) {
                form.setAttribute('action', "{{ route('job.delete') }}");
                jobIdField.value = jobid;
                jobIdField.disabled = true;
                document.getElementById('courierIdField').disabled = true;
                document.getElementById('statusIdField').disabled = true;
                document.getElementById('clientSearchField').disabled = true;
                document.getElementById('jobDateField').disabled = true;
                document.getElementById('jobid').value = jobid;
                setJobValues(jobid,'view');
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-trash'></i>";
                submitButton.style.visibility = 'hidden';
                createNewTaskButton.style.visibility = 'hidden';

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
    document.getElementById('submitTaskform').addEventListener('click', function(event) {
        event.preventDefault();
        const   typeField   =   document.getElementById('taskTypeField');
        var     route       = '';
        if(this.getAttribute('data-option') === 'delete'){
            route = '{{ route("task.delete") }}';
        }else if(this.getAttribute('data-option') === 'edit'){
            route = '{{ route("task.update") }}';
        }else if(this.getAttribute('data-option') === 'view'){
            return;
        }else if(this.getAttribute('data-option') === 'create'){
            route = '{{ route("task.store") }}';
        } 
        updateData = {
            jobId       :   document.getElementById('idField').value,
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
            date        :   document.getElementById('jobDateField').value,
        }
        if(typeField.value == 'dropOff'){
            updateData.package = {
                type: document.getElementById('packageTypeSelect').value,
                quantity: document.getElementById('quantityInput').value,
            }
        }
        updateTask(updateData,route);
    });
    document.getElementById('submitform').addEventListener('click', function(event) {
        event.preventDefault();
        const form = document.getElementById('jobForm');
        updateData  =   {
            id          :   document.getElementById('idField').value,
            courierId   :   document.getElementById('courierIdField').value,
            statusId    :   document.getElementById('statusIdField').value,
            clientId    :   document.getElementById('clientIdField').value,
        }
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        // Send a POST request to the server using the generated route
        fetch(form.action, { // Blade syntax to generate the route URL
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json', // Set Accept header
                // Add any additional headers if needed
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(updateData)
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
    });
});

</script>
@endsection