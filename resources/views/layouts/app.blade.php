<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap CSS (via CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
          rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Vite compiled SCSS + JS -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @yield('style')
</head>
<body>
<!-- Example single danger button -->
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
                      <!-- <li>
                          <div class="btn-group">
                              <button type="button" class="btn  dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              Workloads
                              </button>
                              <ul class="dropdown-menu">
                                  <li><a class="dropdown-item" href="{{route('workload.index')}}">List</a></li>
                                  <li><a class="dropdown-item" href="{{route('workload.calendar')}}">Calendar view</a></li>
                              </ul>
                          </div>
                      </li> -->
                      <!-- <li>
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
                      </li> -->
                      <li>
                      <li>
                        <a class="btn dropdown-toggle " href="{{route('invoice.index')}}">Invoices</a>
                      </li>
                      <div class="dropdown">
                          <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                              Other
                          </button>
                              <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                  <li>
                                  <a class="dropdown-item " href="{{route('jobtemplate.index')}}">Job templates</a>
                                  </li>
                                  <li>
                                  <a class="dropdown-item " href="{{route('bike.index')}}">Bikes</a>
                                  </li>
                                  <li>
                                  <a class="dropdown-item " href="{{route('extratype.index')}}">Addon types</a>
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
                                  <a class="dropdown-item " href="{{route('role.index')}}">Users and roles</a>
                                  </li>
                                  <li>
                                  <a class="dropdown-item " href="{{route('role.permissionsMatrix')}}">Roles and permissions</a>
                                  </li>
                                  @if(auth()->user()->isAdminOrSuperAdmin())
                                  <li>
                                  <a class="dropdown-item " href="{{route('role.manage')}}">Roles</a>
                                  </li>
                                  <li>
                                  <a class="dropdown-item " href="{{route('permission.index')}}">Permissions</a>
                                  </li>
                                  <li>
                                  <a class="dropdown-item " href="{{route('role.hierarchy')}}">Hierarchy</a>
                                  </li>
                                  @endif
                                  <li>
                                  <a class="dropdown-item " href="{{route('approvedpostalcodearea.index')}}">Postal code areas</a>
                                  </li>
                                  @can('setting-view')
                                  <li>
                                  <a class="dropdown-item " href="{{route('setting.index')}}">Settings</a>
                                  </li>
                                  @endcan
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
      @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                show_Success_Message({ message: @json(session('success')) });
            });
        </script>
      @endif

      @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                show_Error_Message({ message: @json(session('error')) });
            });
        </script>
      @endif

      <div id="navAlert" class="alert text-center fade show w-100" 
          style="position: fixed; top: -100px; left: 0; z-index: 1050; transition: top 0.5s ease;" role="alert">
        <span id="navAlertText"></span>
      </div>

      <main class="py-4">
          @yield('content')
      </main>
  </div>
    <!-- External JS dependencies -->

  @yield('scripts')
  @stack('scripts')
      <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.js"></script>
  <script>
      var roleSelect = document.getElementById('role');
      if(roleSelect){
          roleSelect.addEventListener('change', function() {
          document.getElementById('rolechangeForm').submit(); // Submit the form
      }); 
      }
      
  </script>
<script>
  function showAlert({message, type = 'info', duration = 3000, zIndex = 10000}) {
      const alertBox = document.getElementById('navAlert');
      const alertText = document.getElementById('navAlertText');

      if (!alertBox) return;

      alertText.textContent = message;

      // Reset alert classes
      alertBox.className = `alert alert-${type} text-center fade show w-100`;
      alertBox.style.position = 'fixed';
      alertBox.style.left = '0';
      alertBox.style.top = '0';
      alertBox.style.zIndex = zIndex;

      // Slide up after duration
      setTimeout(() => {
          alertBox.style.top = `-${alertBox.offsetHeight}px`;
      }, duration);
  }

  function show_Success_Message(options) {
      showAlert({ ...options, type: 'success' });
  }

  function show_Error_Message(options) {
      showAlert({ ...options, type: 'danger' });
  }
</script>

</body>
</html>
