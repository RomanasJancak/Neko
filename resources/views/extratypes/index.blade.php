@extends('layouts.app')

@section('content')
<div class="container">
  <h1>AddOn Types</h1>
  <a href="" class="btn btn-primary">Add New AddOn Type</a>
  <div class="row mb-3">
    <div class="col-md-4">
      <input type="text" id="search-id" class="form-control" placeholder="Search by ID">
    </div>
    <div class="col-md-4">
      <input type="text" id="search-name" class="form-control" placeholder="Search by Name">
    </div>
    <div class="col-md-4">
      <input type="text" id="search-description" class="form-control" placeholder="Search by Description">
    </div>
  </div>
  <table class="table table-bordered mt-3">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Type</th>
        <th>Description</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($extraTypes as $extraType)
      <tr>
        <td>{{ $extraType->id }}</td>
        <td>{{ $extraType->name }}</td>
        <td>{{ $extraType->description }}</td>
        <td>
          <a href="" class="btn btn-warning">Edit</a>
          <form action="" method="POST" style="display:inline-block;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <div id="paginationLinks">
    {{ $extraTypes->links() }}
  </div>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form').forEach(form => {
      form.addEventListener('submit', function(event) {
        event.preventDefault();
        if (confirm('Are you sure you want to delete this \'Addon\' type?')) {
          form.submit();
        }
      });
    });

    const searchInputs = [
      { id: 'search-id', field: 'id' },
      { id: 'search-name', field: 'name' },
      { id: 'search-description', field: 'description' }
    ];

    searchInputs.forEach(input => {
      const inputElement = document.getElementById(input.id);
      inputElement.addEventListener('input', fetchExtraTypes);
    });

    function fetchExtraTypes(page = 1) {
      const id = document.getElementById('search-id')?.value || '';
      const name = document.getElementById('search-name')?.value || '';
      const description = document.getElementById('search-description')?.value || '';
      const sortField = document.querySelector('.sort-btn.active')?.dataset.sortField || 'id';
      const sortOrder = document.querySelector('.sort-btn.active')?.dataset.sortOrder || 'asc';

      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      const xhr = new XMLHttpRequest();
      xhr.open('GET', `{{ route('extratype.fetch') }}?id=${id}&name=${name}&description=${description}&sortField=${sortField}&sortOrder=${sortOrder}&page=${page}`, true);
      xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

      xhr.onload = function() {
        if (xhr.status === 200) {
          const data = JSON.parse(xhr.responseText);
          const tbody = document.querySelector('tbody');
          tbody.innerHTML = '';
          data.extraTypes.data.forEach(extraType => {
            const extraTypeRow = `
              <tr>
                <td>${extraType.id}</td>
                <td>${extraType.name}</td>
                <td>${extraType.description}</td>
                <td>
                  <a href="" class="btn btn-warning">Edit</a>
                  <form action="" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                  </form>
                </td>
              </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', extraTypeRow);
          });
          document.getElementById('paginationLinks').innerHTML = data.links;
        }
      };
      xhr.send();
    }

    document.addEventListener('click', function(event) {
      if (event.target.closest('.pagination a')) {
        event.preventDefault();
        const page = event.target.getAttribute('href').split('page=')[1];
        fetchExtraTypes(page);
      }

      if (event.target.closest('.sort-btn')) {
        const button = event.target.closest('.sort-btn');
        const sortField = button.dataset.sortField;
        const currentOrder = button.dataset.sortOrder;
        const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';

        document.querySelectorAll('.sort-btn').forEach(btn => {
          btn.classList.remove('active');
          btn.dataset.sortOrder = 'asc';
        });

        button.classList.add('active');
        button.dataset.sortOrder = newOrder;
        
        fetchExtraTypes();
      }
    });

    fetchExtraTypes();
  });
</script>
@endsection