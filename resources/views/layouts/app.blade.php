<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Include Bootstrap CSS from a CDN -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css"> -->

    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet"> -->

    <!-- For Dark them -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Add Bootstrap Typeahead CSS Jquery-->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.js"></script>
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.css"> -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js-bootstrap-css/0.1/typeaheadjs.css"> -->


    <!-- Include Font Awesome for icons (if needed) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Include your custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">


    <!-- Include additional stylesheets or scripts you might need -->
    @yield('style')
</head>
<body>
    {{-- dd(auth()->user()->role()) --}}
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    @auth
                    <ul class="navbar-nav me-auto">
                        <li>
                            <div class="btn-group">
                                <button type="button" class="btn  dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Dashboard
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('day.showdashboard', date('Y-m-d')) }}">Today</a></li>
                                    <li><a class="dropdown-item" href="{{ route('day.showdashboard', date('Y-m-d', strtotime('+1 day'))) }}">Tommorow</a></li>
                                </ul>
                            </div>
                        </li>
                        @if(auth()->user()->can('user-view') || auth()->user()->can('user-create'))
                        <li>
                            <div class="btn-group">
                                <button type="button" class="btn  dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Users
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{route('users.index')}}">List</a></li>
                                    @can('user-create')
                                    <li><a class="dropdown-item" href="{{route('user.create')}}">Create</a></li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                        @endif
                        @if(auth()->user()->can('client-view') || auth()->user()->can('client-create'))
                        <li>
                            <div class="btn-group">
                                <button type="button" class="btn  dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Clients
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{route('client.index')}}">List</a></li>
                                </ul>
                            </div>
                        </li>
                        @endif
                        @if(auth()->user()->can('job-view') || auth()->user()->can('job-create'))
                        <li>
                            <div class="btn-group">
                                <button type="button" class="btn  dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Jobs
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{route('job.index')}}">List</a></li>
                                    @can('client-create')
                                    <li><a class="dropdown-item" href="{{route('job.create')}}">Create pickup</a></li>
                                    <li><a class="dropdown-item" href="{{route('job.create', ['customjob' => true]) }}">Create custom</a></li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                        @endif
                        <li>
                            <div class="btn-group">
                                <button type="button" class="btn  dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Workloads
                                </button>
                                <ul class="dropdown-menu">
                                    <!-- <li><a class="dropdown-item" href="{{route('job.index')}}">List</a></li> -->
                                    <li><a class="dropdown-item" href="{{route('workload.calendar')}}">Calendar view</a></li>
                                    <!-- <li><a class="dropdown-item" href="{{route('job.create')}}">Create</a></li> -->
                                    <!-- <li><a class="dropdown-item" href="{{route('job.assign')}}">Assign</a></li> -->
                                    
                                </ul>
                            </div>
                        </li>
                        <li>
                            <div>
                                <form method="POST" action="{{ route('user.updateRole', ['user' => auth()->user()]) }}" id="rolechangeForm">
                                    @csrf
                                    <select id="role" name="role" class="form-control" >
                                        @foreach(auth()->user()->getAllRoles() as $role)
                                            @if ($role->id === auth()->user()->currentrole()->id)
                                            <option value="{{ $role->id }}" selected>{{ $role->name }}</option>
                                            @else
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </li>
                        <li>
                        <div class="dropdown">
                            <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                Other
                            </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li>
                                    <a class="dropdown-item " href="{{route('jobTemplate.index')}}">Job templates</a>
                                    </li>
                                    <li>
                                    <a class="dropdown-item " href="{{route('bike.index')}}">Bikes</a>
                                    </li>
                                    <li>
                                    <a class="dropdown-item " href="{{route('addonrule.index')}}">AddOnRules</a>
                                    </li>
                                    <li>
                                    <a class="dropdown-item " href="{{route('status.index')}}">Statuses</a>
                                    </li>
                                    <li>
                                    <a class="dropdown-item " href="{{route('packageType.index')}}">Package types</a>
                                    </li>
                                    <li>
                                    <a class="dropdown-item " href="{{route('role.index')}}">Roles</a>
                                    </li>
                                    <li>
                                    <a class="dropdown-item " href="{{route('approvedpostalcodearea.index')}}">Postal code areas</a>
                                    </li>
                                    <li>
                                    <a class="dropdown-item " href="{{route('setting.index')}}">Settings</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                    @endauth

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <!-- <li class="nav-item"> -->
                                    <!-- <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a> -->
                                <!-- </li> -->
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <!-- Include jQuery, Popper.js, and Bootstrap JavaScript from CDNs -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script> -->

    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script> -->
    <!-- Include additional JavaScript libraries or your custom scripts -->
    @yield('scripts')
    <script>
           var roleSelect = document.getElementById('role');

// Add an event listener to submit the form when the select element changes
roleSelect.addEventListener('change', function() {
    document.getElementById('rolechangeForm').submit(); // Submit the form
}); 
    </script>
</body>
</html>
