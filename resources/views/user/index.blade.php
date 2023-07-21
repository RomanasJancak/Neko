@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <ul class="list-group">
                @foreach($users as $user)
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-md-1">{{ $user->id }}</div>
                            <div class="col-md-3">{{ $user->name }}</div>
                            <div class="col-md-1">{{ $user->email }}</div>
                            <div class="col-md-2">{{ $user->username }}</div>
                            @foreach($user->getRoleNames() as $rolename)
                                {{$rolename.' , '}}
                            @endforeach
                            <div class="col-md-1"><a href="{{route('user.show',$user)}}">More...</a></div>
                            <div class="col-md-1"><a href="{{route('user.edit',$user)}}">Edit</a></div>
                            <div class="col-md-1"><a href="{{route('user.delete',$user)}}">Delete</a></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {!! $users->links() !!}
    </div>
</div>
@endsection
