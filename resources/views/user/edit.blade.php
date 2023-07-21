@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">Edit User</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.update', $user->id) }}">
                        

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input id="name" type="text" class="form-control" name="user_name" value="{{ $user->name }}" required autofocus>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" type="email" class="form-control" name="user_email" value="{{ $user->email }}" required>
                        </div>

                        <div class="form-group">
                            <label for="username">Roles</label>
                            <select class="form-select" aria-label="Default select example" name="role" id="roles" required>
                                @foreach (App\Models\Role::all() as $role)
                                <option value="{{$role->id}}" {{ $user->role_id === $role->id ? 'selected' : '' }}>{{$role->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input id="password" type="password" class="form-control" name="password">
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation">
                        </div>
                        @csrf
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
