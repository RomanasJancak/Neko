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

<!-- Modal -->

</div>
<div class="modal fade" id="modalWindow" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <form id="jobForm" action="" method="POST">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="jobid" id="jobid" value="">
                        <label for="idField">Id : </label>
                        <input type="text" name="id" id="idField" value="">
                    </div>
                    <div class="row">
                        <div class="col">                            
                            <label for="colorPicker-main">Color main: </label>
                            <input type="color" id="colorPicker-main" name="color-main" value="#808080">                      
                        </div>
                        <div class="col">
                            <label for="nameField">Color pickup: </label>
                            <input type="color" id="colorPicker-pickup" name="color-pickup" value="#808080">                       
                        </div>
                        <div class="col">
                            <label for="nameField">Color dropoff: </label>
                            <input type="color" id="colorPicker-dropoff" name="color-dropoff" value="#808080">
                        </div>
                        <div class="col">
                            <label for="nameField">Color return: </label>
                            <input type="color" id="colorPicker-return" name="color-return" value="#808080">
                        </div>
                        <div class="col">
                            <label for="nameField">Color custom: </label>
                            <input type="color" id="colorPicker-custom" name="color-custom" value="#808080">
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
    document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.edit-btn').forEach(button => {

        button.addEventListener('click', () => {
            const jobid          =   button.dataset.jobid;
            const jobName        =   button.dataset.name;
            const jobColorMain   =   button.dataset.colormain;
            const jobColorPickup   =   button.dataset.colorpickup;
            const jobColorDropoff   =   button.dataset.colordropoff;
            const jobColorReturn   =   button.dataset.return;
            const jobColorCustom   =   button.dataset.custom;
            const form = document.querySelector(`#jobForm`);
            if (form) {
                form.setAttribute('action', "{{ route('job.update') }}");
                document.getElementById('jobid').value = jobid;
                document.getElementById('nameField').value = jobName;
                document.getElementById('nameField').readOnly = false;
                document.getElementById('colorPicker-main').value = jobColorMain;
                document.getElementById('colorPicker-pickup').value = jobColorPickup;
                document.getElementById('colorPicker-dropoff').value = jobColorDropoff;
                document.getElementById('colorPicker-return').value = jobColorReturn;
                document.getElementById('colorPicker-custom').value = jobColorCustom;
                document.getElementById('colorPicker-main').disabled = false;
                document.getElementById('colorPicker-pickup').disabled = false;
                document.getElementById('colorPicker-dropoff').disabled = false;
                document.getElementById('colorPicker-return').disabled = false;
                document.getElementById('colorPicker-custom').disabled = false;
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