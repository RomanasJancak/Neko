@extends('layouts.app')
@section('content')
{{-- dd($user->workloads[0]->bike->name) --}}
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">User Details</div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input id="name" type="text" class="form-control" value="{{ $user->name }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" class="form-control" value="{{ $user->email }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input id="username" type="text" class="form-control" value="{{ $user->username }}" readonly>
                    </div>
                    <div class="form-group">
                            <label for="phone">Phone</label>
                            <input id="phone" type="text" class="form-control" name="phone" value="{{ $user->phone }}">
                    </div>
                    <div class="form-group">
                        <label for="role">Roles</label>
                        @foreach ($user->getRoleNames() as $role)         
                        <input id="role" type="text" class="form-control" value="{{ $role}}" readonly>
                        @endforeach
                    </div>

                    <div class="form-group">
                        <label for="created_at">Created At</label>
                        <input id="created_at" type="text" class="form-control" value="{{ $user->created_at }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="updated_at">Updated At</label>
                        <input id="updated_at" type="text" class="form-control" value="{{ $user->updated_at }}" readonly>
                    </div>
                    
                    <div class="form-group">
                        <a href="{{ route('user.edit', $user->id) }}" class="btn btn-primary">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
