@extends('layouts.app') <!-- Use your layout if available -->

@section('content')
<form method="GET" action="{{ route('user.workload', ['user' => $user]) }}">
    <label for="month">Select Month:</label>
    <select name="month" id="month">
        @for ($i = 1; $i <= 12; $i++)
            <option value="{{ $i }}" @if ($i == $currentMonth) selected @endif>
                {{ Carbon\Carbon::create(null, $i, 1)->monthName }}
            </option>
        @endfor
    </select>
    <label for="year">Select Year:</label>
    <select name="year" id="year">
        @for ($y = Carbon\Carbon::now()->year+1; $y >= 2000; $y--)
            <option value="{{ $y }}" @if ($y == $currentYear) selected @endif>{{ $y }}</option>
        @endfor
    </select>
    <button type="submit">Go</button>
</form>
<div class="row">
        @foreach(range(1, $daysInMonth) as $day)
            <div class="col-md-1">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ $day }}</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($workloadData[$day]))
                            @foreach($workloadData[$day] as $workload)
                                <!-- Display workload card information here -->
                                <div class="row "id={{$workload}}>
                                    <!-- Customize display based on workload details -->
                                    <div class="col">{{ $workload->capacity }}</div>
                                    <div class="col">{{ $workload->bike->name }}</div>
                                    <!-- Add more workload details as needed -->

                                    <!-- Modify the "Work" button to show the form -->
                                </div>
                            @endforeach
                        @else
                            <!-- Show the "Work" button when there's no workload -->
                            <button class="work-button"
                             data-day="{{ $day }}" 
                             data-month="{{request()->query('month') ?? $currentMonth}}"
                             data-year="{{request()->query('year') ?? $currentYear}}"
                             data-user="{{ $user->id}}">Work</button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
<!-- MODAL WINDOWS -->
<div class="modal fade" id="workloadModal" tabindex="-1"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- This part will be populated with detailed job information fetched via JavaScript -->
            <div class="modal-body" >
            <form id="workloadForm" action="{{ route('workload.storeJavascript') }}" method="POST">
                    @csrf
                    <input type="hidden" name="day" id="dayField" value="">
                    <input type="hidden" name="month" id="monthField" value="">
                    <input type="hidden" name="year" id="yearField" value="">
                    <input type="hidden" name="user" id="user" value="">
                    <div class="form-group">
                        <label for="capacity">Capacity:</label>
                        <input type="text" class="form-control" id="capacity" name="capacity">
                    </div>
                    <div class="form-group">
                        <label for="bike">Choose Bike:</label>
                        <select class="form-control" id="bike" name="bike">
                            @foreach($bikes as $bike)
                                <option value="{{ $bike->id }}">{{ $bike->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                            <button type="button" id="submitWorkload" class="btn btn-primary">Apply</button>
                        </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <!-- <button type="button" id="submitWorkload" class="btn btn-primary">Apply</button> -->
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
        document.querySelectorAll('.work-button').forEach(button => {
            button.addEventListener('click', () => {
                const day = button.dataset.day;
                const month = button.dataset.month;
                const year = button.dataset.year;
                const user = button.dataset.user;
                const form = document.querySelector(`#workloadForm`);
                if (form) {
                    // Set the day value in the hidden field
                    document.getElementById('dayField').value = day;
                    document.getElementById('monthField').value =   month;
                    document.getElementById('yearField').value = year;
                    document.getElementById('user').value = user;
                    // Show the modal
                    $('#workloadModal').modal('show');
                }
            });
        });
//žžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžž
        // Handle submit button click inside the modal

//žžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžžž
//===================================================
document.getElementById('submitWorkload').addEventListener('click', function() {
        // Get form data
        const form = document.getElementById('workloadForm');
        const formData = new FormData(form);
        // Create a new XMLHttpRequest object
        const xhr = new XMLHttpRequest();

        // Define the request type, URL, and set up the request
        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('X-CSRF-Token', '{{ csrf_token() }}'); // Replace with your CSRF token if not using Blade

        // Handle the response
        xhr.onload = function() {
            // Process the response if needed
            //console.log(xhr.responseText);
        };

        // Send the request
        xhr.send(formData);
    });
</script>
@endsection