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
                <th rowspan="2">Price</th>
                <th rowspan="2" style="text-align:center;width:100px;">Create Job <button type="button" data-func="dt-add" class="btn btn-success btn-xs dt-add">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                    </button></th>
            </tr>
            <tr>
                <th>Pickup</th>
                <th>Drops</th>
                <th>Return</th>
                <th>Custom</th>
                <th>Actions</th>
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
                        {{$task->pickup->pickupclientname}}
                    @endif         
                @endforeach
                </td>
                <td>
                @foreach ($job->tasks as $task)
                    @if ($task->package)
                    <div class="row"><div class="col">{{$task->package->id}}</div></div>           
                    @endif                               
                @endforeach
                </td>
                <td>                       
                @foreach ($job->tasks as $task)
                    @isset($task->return)
                    <div class="row">
                            <div class="col">{{$task->return->id}}</div>
                    </div>   
                    @endisset
                @endforeach
                </td>
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
<!-- Modal -->

</div>
<div class="modal fade" id="modalWindow" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
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
                    </div>
                    <div class="row justify-content-md-center">
                        Tasks
                    </div>
                    <div class="row justify-content-md-center border rounded border-info" >
                        <div class="col" id="container-tasks">
                            <!-- <div class="row" id="container-task-0">
                                <div class="col" id="container-task-0-type">
                                    Pickup
                                </div>
                                <div class="col" id="container-task-0-addressName">
                                    NameOfPickup
                                </div>
                                <div class="col" id="container-task-0-address">
                                    Address
                                </div>
                                <div class="col" id="container-task-0-timeWindow">
                                    TimeWindow
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    Pickup
                                </div>
                                <div class="col">
                                    NameOfPickup
                                </div>
                                <div class="col">
                                    Address
                                </div>
                                <div class="col">
                                    TimeWindow
                                </div>
                            </div> -->
                        </div>
                        
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
function createColumn(idSuffix, content) {
    let col = document.createElement('div');
    col.className = 'col border';
    col.id = `container-task-0-${idSuffix}`;
    col.textContent = content;
    return col;
}
function appendTaskToContainer(container,task){
    // Create the main 'row' div
    let taskRow = document.createElement('div');
    taskRow.className = 'row';
    taskRow.id = 'container-task-0';

    // Create and append each column to the row
    taskRow.appendChild(createColumn('type', task.name));
    taskRow.appendChild(createColumn('addressName', task.location));
    taskRow.appendChild(createColumn('address', 'Address'));
    taskRow.appendChild(createColumn('timeWindow', 'TimeWindow'));

    // Select the target container and append the new row to it
    console.log(taskRow);
    container.appendChild(taskRow);
}
function setJobValues(jobid){
    const courierIdField    =   document.getElementById('courierIdField');
    const statusIdField    =   document.getElementById('statusIdField');
    const clientSearchField =   document.getElementById('clientSearchField');
    const clientIdField =   document.getElementById('clientIdField');
    const containerTasks =   document.getElementById('container-tasks');
    const routeUrl = "{{ route('job.getJobInfo', ['id' => ':id']) }}".replace(':id', jobid);
    fetch(routeUrl)
        .then(response => response.json())
        .then(data => {
            //console.log(data);
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
            data.tasks.forEach(function(task){
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
document.addEventListener('DOMContentLoaded', function() {
    addTypeHeadSearch($('#clientSearchField'));
    document.querySelectorAll('.edit-btn').forEach(button => {

        button.addEventListener('click', () => {
            const jobid         =   button.dataset.jobid;
            const jobIdField    =   document.getElementById('idField');
            const courierIdField    =   document.getElementById('courierIdField');
            const jobName        =   button.dataset.name;
            const jobColorMain   =   button.dataset.colormain;
            const jobColorPickup   =   button.dataset.colorpickup;
            const jobColorDropoff   =   button.dataset.colordropoff;
            const jobColorReturn   =   button.dataset.return;
            const jobColorCustom   =   button.dataset.custom;
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
            $('#modalWindow').modal('show');
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
                document.getElementById('colorPicker-main').value = jobColorMain;
                document.getElementById('colorPicker-pickup').value = jobColorPickup;
                document.getElementById('colorPicker-dropoff').value = jobColorDropoff;
                document.getElementById('colorPicker-return').value = jobColorReturn;
                document.getElementById('colorPicker-custom').value = jobColorCustom;
                document.getElementById('colorPicker-main').disabled = true;
                document.getElementById('colorPicker-pickup').disabled = true;
                document.getElementById('colorPicker-dropoff').disabled = true;
                document.getElementById('colorPicker-return').disabled = true;
                document.getElementById('colorPicker-custom').disabled = true;
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-trash'></i>";
            }
            $('#modalWindow').modal('show');
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
            $('#modalWindow').modal('show');
        });
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