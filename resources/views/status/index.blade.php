@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Statuses</h1><button class="btn btn-secondary create-btn" >Add new Status</i></button>
            <table class="table">
                <thead>
                    <tr>
                        <th data-column="id">ID</th>
                        <th data-column="name">Name</th>
                        <th data-column="name">Color main</th>
                        <th data-column="name">Color pickup</th>
                        <th data-column="name">Color dropoff</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="statusesTableBody">
                    @foreach($statuses as $status)
                    <tr>
                        <td>{{ $status->id }}</td>
                        <td>{{ $status->name }}</td>
                        <td style="background-color: {{$status->color_main}}">{{ $status->color_main }}</td>
                        <td style="background-color: {{$status->color_pickup}}">{{ $status->color_pickup }}</td>
                        <td style="background-color: {{$status->color_dropoff}}">{{ $status->color_dropoff }}</td>
                        <td>
                            <button class="btn btn-primary edit-btn" 
                                data-statusid="{{ $status->id }}"
                                data-name="{{ $status->name }}"
                                data-colormain="{{ $status->color_main }}"
                                data-colorpickup="{{ $status->color_pickup }}"
                                data-colordropoff="{{ $status->color_dropoff }}"
                            ><i class="bi bi-pen"></i></button>
                            <button class="btn btn-danger delete-btn" 
                                data-statusid="{{ $status->id }}"
                                data-name="{{ $status->name }}"
                                data-colormain="{{ $status->color_main }}"
                                data-colorpickup="{{ $status->color_pickup }}"
                                data-colordropoff="{{ $status->color_dropoff }}"
                            ><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {!! $statuses->links() !!}
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="modalWindow" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <form id="statusForm" action="" method="POST">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="statusid" id="statusid" value="">
                        <label for="nameField">Name : </label>
                        <input type="text" name="name" id="nameField" value="">
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
            const statusid          =   button.dataset.statusid;
            const statusName        =   button.dataset.name;
            const statusColorMain   =   button.dataset.colormain;
            const statusColorPickup   =   button.dataset.colorpickup;
            const statusColorDropoff   =   button.dataset.colordropoff;
            const form = document.querySelector(`#statusForm`);
            if (form) {
                form.setAttribute('action', "{{ route('status.update') }}");
                document.getElementById('statusid').value = statusid;
                document.getElementById('nameField').value = statusName;
                document.getElementById('nameField').readOnly = false;
                document.getElementById('colorPicker-main').value = statusColorMain;
                document.getElementById('colorPicker-pickup').value = statusColorPickup;
                document.getElementById('colorPicker-dropoff').value = statusColorDropoff;
                document.getElementById('colorPicker-main').disabled = false;
                document.getElementById('colorPicker-pickup').disabled = false;
                document.getElementById('colorPicker-dropoff').disabled = false;
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-pen'></i>";
            }
            $('#modalWindow').modal('show');
        });
    });
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', () => {
            const statusid = button.dataset.statusid;
            const statusName = button.dataset.name;
            const statusColorMain   =   button.dataset.colormain;
            const statusColorPickup   =   button.dataset.colorpickup;
            const statusColorDropoff   =   button.dataset.colordropoff;
            const form = document.querySelector(`#statusForm`);
            if (form) {
                form.setAttribute('action', "{{ route('status.delete') }}");
                document.getElementById('statusid').value = statusid;
                document.getElementById('nameField').value = statusName;
                document.getElementById('nameField').readOnly = true;
                document.getElementById('colorPicker-main').value = statusColorMain;
                document.getElementById('colorPicker-pickup').value = statusColorPickup;
                document.getElementById('colorPicker-dropoff').value = statusColorDropoff;
                document.getElementById('colorPicker-main').disabled = true;
                document.getElementById('colorPicker-pickup').disabled = true;
                document.getElementById('colorPicker-dropoff').disabled = true;
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-trash'></i>";
            }
            $('#modalWindow').modal('show');
        });
    });
    document.querySelectorAll('.create-btn').forEach(button => {
        button.addEventListener('click', () => {
            const form = document.querySelector(`#statusForm`);
            if (form) {
                document.getElementById('nameField').readOnly = false;
                document.getElementById('colorPicker-main').disabled = false;
                document.getElementById('colorPicker-pickup').disabled = false;
                document.getElementById('colorPicker-dropoff').disabled = false;
                form.setAttribute('action', "{{ route('status.store') }}");
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-save'></i>";
            }
            $('#modalWindow').modal('show');
        });
    });
    document.getElementById('submitform').addEventListener('click', function() {
        // Get form data
        const form = document.getElementById('statusForm');
        const formData = new FormData(form);
        console.log(formData.get('statusid'));
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
