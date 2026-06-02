@extends('layouts.app')

@section('content')
<div class="container">
  <h1>AddOn Types</h1>
  @can('extratype-create')
  <a href="" class="btn btn-primary">Add New AddOn Type</a>
  @endcan
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
        <td>{{ $extraType->model_type}}</td>
        <td>{{ $extraType->description }}</td>
        <td>
          @can('extratype-edit')
          <button class="btn btn-warning">Edit</button>
          @endcan
          @can('extratype-delete')
          <button class="btn btn-danger">Delete</button>
          @endcan
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
    
  });
</script>
@endsection