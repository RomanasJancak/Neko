@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col">
      <h1>Approved Postal Code Areas</h1><button class="btn btn-secondary create-btn" >Add new Postal Code Area</button> 
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      @foreach($approvedPostalCodeAreas as $postalcodearea)
        @if($postalcodearea->parent_id === 0)
        <div class="row" style="border: 0.001rem solid rgb(128, 128, 128);">
        <div class="col-12 d-flex justify-content-center align-items-center">{{$postalcodearea->area()}}</div>
          @if($postalcodearea->children->isEmpty())                     
          @else
            @foreach ($postalcodearea->children as $postalcodedistrict)              
              @if($postalcodedistrict->children->isEmpty())                    
                  <div class="col-6" data-id="{{$postalcodedistrict->id}}">
                    {{$postalcodedistrict->district()}}
                  </div>
              @else
                @foreach ($postalcodedistrict->children as $postalcodeSubDistrict)
                  @if($postalcodeSubDistrict->children->isEmpty())
                  @endif
                @endforeach
                @endif
              @endforeach
              @endif
        </div>
        @endif
      @endforeach
    </div>
    <div class="col-md-12">
      <table class="table">
        <thead>
          <tr>
            <input type="text" id="search-id" class="form-control" placeholder="Search by ID">
            <input type="text" id="search-name" class="form-control" placeholder="Search by Code">
            <input type="text" id="search-address" class="form-control" placeholder="Search by Type">
          </tr>
          <tr>
            <th>ID</th>
            <th>Type</th>
            <th>Code</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="approvedPostalCodeAreasTableBody">
          @foreach($approvedPostalCodeAreas as $postalcodearea)
          <tr>
            <td>{{ $postalcodearea->id }}</td>
            <td>{{ $postalcodearea->type }}</td>
            <td>{{ $postalcodearea->name }}</td>
            <td>
              <button class="btn btn-primary edit-btn" data-postalcodeareaid="{{ $postalcodearea->id }}"><i class="bi bi-pen"></i></button>
              <button class="btn btn-danger delete-btn" data-postalcodeareaid="{{ $postalcodearea->id }}"><i class="bi bi-trash"></i></button>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div class="d-flex justify-content-center mt-3">
    {--!! $approvedPostalCodeAreas->links() !!--}
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="modalWindow" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-body">
        <form id="postalcodeareaForm" action="" method="POST">
          @csrf
          <div class="row">
            <div class="col">
              <input type="hidden" name="id" id="postalcodeareaid" value="">
              <label for="nameField">Code : </label>
              <input type="text" name="name" id="nameField" value="">
              <label for="typeField">Type : </label>
              <input type="text" name="type" id="typeField" value="">
            </div>
          </div>
          <div class="row">
            <div class="form-group">
              <button type="button" id="submitform" data-option="create" class="btn btn-primary">Apply</button>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer justify-content-center">
        <div class="row">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
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
      const postalcodeareaid = button.dataset.postalcodeareaid;
      const routeUrl = "{{ route('approvedpostalcodearea.getById', ['id' => ':id']) }}".replace(':id', postalcodeareaid);
      const form = document.querySelector(`#postalcodeareaForm`);
      if (form) {
        form.setAttribute('action', "{{ route('approvedpostalcodearea.update') }}");
        fetch(routeUrl)
            .then(response => response.json())
            .then(data => {
              if (data) {
                document.getElementById('postalcodeareaid').value = postalcodeareaid;
                document.getElementById('nameField').readOnly = false;
                document.getElementById('typeField').readOnly = false;
                document.getElementById('nameField').value = data.name;
                document.getElementById('typeField').value = data.type;
              }
            })
            .catch(error => {
              console.error(error);
        });
        submitButton = document.getElementById('submitform');
        submitButton.innerHTML = "<i class='bi bi-pen'></i>";
      }
      $('#modalWindow').modal('show');
    });
  });

  document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', () => {
      const postalcodeareaid = button.dataset.postalcodeareaid;
      const routeUrl = "{{ route('approvedpostalcodearea.getById', ['id' => ':id']) }}".replace(':id', postalcodeareaid);
      const form = document.querySelector(`#postalcodeareaForm`);
      if (form) {
        form.setAttribute('action', "{{ route('approvedpostalcodearea.delete') }}");
        fetch(routeUrl)
            .then(response => response.json())
            .then(data => {
              if (data) {
                document.getElementById('postalcodeareaid').value = postalcodeareaid;
                document.getElementById('nameField').readOnly = true;
                document.getElementById('typeField').readOnly = true;
                document.getElementById('nameField').value = data.name;
                document.getElementById('typeField').value = data.type;
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
      const form = document.querySelector(`#postalcodeareaForm`);
      if (form) {
        document.getElementById('nameField').readOnly = false;
        document.getElementById('typeField').readOnly = false;
        form.setAttribute('action', "{{ route('approvedpostalcodearea.store') }}");
        submitButton = document.getElementById('submitform');
        submitButton.innerHTML = "<i class='bi bi-save'></i>";
      }
      $('#modalWindow').modal('show');
    });
  });

  document.getElementById('submitform').addEventListener('click', function() {
    const form = document.getElementById('postalcodeareaForm');
    const formData = new FormData(form);
    const xhr = new XMLHttpRequest();
    xhr.open('POST', form.action, true);
    xhr.setRequestHeader('X-CSRF-Token', '{{ csrf_token() }}');
    xhr.onload = function() {
      console.log(xhr.responseText);
      parsedMessage = JSON.parse(xhr.responseText).message;
      if(parsedMessage === 'deleted'){
        // Handle deletion
      }
      if(parsedMessage === 'updated'){
        // Handle update
      }
      if(parsedMessage === 'created'){
        // Handle creation
      }
    };
    xhr.send(formData);
  });
});
</script>
@endsection
