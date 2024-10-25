@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Bikes</h1><button class="btn btn-secondary create-btn" >Add new Bike</i></button>
            <table class="table">
                <thead>
                    <tr>
                        <th data-column="id">ID</th>
                        <th data-column="name">Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="bikesTableBody">
                    @foreach($bikes as $bike)
                    <tr>
                        <td>{{ $bike->id }}</td>
                        <td>{{ $bike->name }}</td>
                        <td>
                            <button class="btn btn-primary edit-btn" 
                                data-bikeid="{{ $bike->id }}"
                                data-name="{{ $bike->name }}"
                            ><i class="bi bi-pen"></i></button>
                            <button class="btn btn-danger delete-btn" 
                                data-bikeid="{{ $bike->id }}"
                                data-name="{{ $bike->name }}"
                            ><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {!! $bikes->links() !!}
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="modalWindow" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <form id="bikeForm" action="" method="POST">
                    @csrf
                    <input type="hidden" name="bikeid" id="bikeid" value="">
                    <label for="nameField">Name : </label>
                    <input type="text" name="name" id="nameField" value="">
                
                <div class="form-group">
                            <button type="button" id="submitform" data-option="create" class="btn btn-primary">Apply</button>
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
            const bikeId = button.dataset.bikeid;
            console.log(button.dataset.bikeid);
            const bikeName = button.dataset.name;
            const form = document.querySelector(`#bikeForm`);
            if (form) {
                form.setAttribute('action', "{{ route('bike.update') }}");
                document.getElementById('bikeid').value = bikeId;
                document.getElementById('nameField').value = bikeName;
                document.getElementById('nameField').readOnly = false;
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-pen'></i>";
            }
            $('#modalWindow').modal('show');
        });
    });
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', () => {
            const bikeId = button.dataset.bikeid;
            const bikeName = button.dataset.name;
            const form = document.querySelector(`#bikeForm`);
            if (form) {
                form.setAttribute('action', "{{ route('bike.delete') }}");
                document.getElementById('bikeid').value = bikeId;
                document.getElementById('nameField').value = bikeName;
                document.getElementById('nameField').readOnly = true;
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-trash'></i>";
            }
            $('#modalWindow').modal('show');
        });
    });
    document.querySelectorAll('.create-btn').forEach(button => {
        button.addEventListener('click', () => {
            const form = document.querySelector(`#bikeForm`);
            if (form) {
                form.setAttribute('action', "{{ route('bike.store') }}");
                submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-save'></i>";
            }
            $('#modalWindow').modal('show');
        });
    });
    document.getElementById('submitform').addEventListener('click', function() {
        // Get form data
        const form = document.getElementById('bikeForm');
        const formData = new FormData(form);
        console.log(formData.get('bikeid'));
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
