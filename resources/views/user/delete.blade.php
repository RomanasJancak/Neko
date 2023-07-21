@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">Delete User</div>
                <div class="card-body">
                    <p>Are you sure you want to delete this user?</p>
                    <p><strong>Name:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Username:</strong> {{ $user->username }}</p>
                    <p><strong>Roles:</strong>                         @foreach ($user->getRoleNames() as $role)         
                        <input id="role" type="text" class="form-control" value="{{ $role}}" readonly>
                        @endforeach</p>
                    <p><strong>Created At:</strong> {{ $user->created_at }}</p>
                    <p><strong>Updated At:</strong> {{ $user->updated_at }}</p>

                    <form method="POST" action="{{ route('user.destroy', $user->id) }}">
                        @csrf
                        <div class="form-group">
                            <button type="submit" class="btn btn-danger">Delete</button>
                            <a href="{{ route('user.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
