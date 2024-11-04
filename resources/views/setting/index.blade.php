
@extends('layouts.app')

@section('title', 'Roles and Permissions')
@section('style')
<style>

</style>
@endsection    
@section('content')
<div>
  <form method="POST" action="{{ route('setting.backupAll') }}">
    @csrf
    <button type="submit" class="btn btn-primary">Create Backup For All</button>
  </form>
</div>
@endsection
@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {

  });
</script>

@endsection