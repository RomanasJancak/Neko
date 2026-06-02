@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col">
            <form method="GET" action="{{ route('workload.calendar') }}">
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
                    @for ($y = Carbon\Carbon::now()->year+3; $y >= Carbon\Carbon::now()->year; $y--)
                        <option value="{{ $y }}" @if ($y == $currentYear) selected @endif>{{ $y }}</option>
                    @endfor
                </select>
                <label for="view">Select View:</label>
                <select name="view" id="view">
                    <option value="monthly" @if ($view == 'monthly') selected @endif>Monthly</option>
                    <option value="weekly" @if ($view == 'weekly') selected @endif>Weekly</option>
                </select>
                @if ($view == 'weekly')
                    <label for="week">Select Week:</label>
                    <select name="week" id="week">
                        @for ($w = 1; $w <= 52; $w++)
                            <option value="{{ $w }}" @if ($w == $currentWeek) selected @endif>Week {{ $w }}</option>
                        @endfor
                    </select>
                @endif
                <button type="submit">Go</button>
            </form>
        </div>
        <div class="col">
            <!-- Additional Content Here -->
        </div>
    </div>
    <div class="row">
        @if ($view == 'monthly')
            @foreach(range(1, $daysInMonth) as $day)
                <div class="col-md-2">
                    <div class="card">
                        <div class="card-header">
                            <?php $p = DateTime::createFromFormat('Y-m-d',$currentYear."-".$currentMonth."-".$day)?>
                            <h5>@displayDate($currentYear.'-'.$currentMonth.'-'.$day) {{$p->format('l')}}<a class="btn btn-info" href="{{ route('day.showdashboard', ['date' => $currentYear.'-'.$currentMonth.'-'.$day]) }}">List</a></h5>
                        </div>
                        <div class="card-body">
                            @if(isset($workloadData[$day]))
                                @foreach($workloadData[$day] as $workload)
                                    <!-- Display workload card information here -->
                                    <div class="row" id="workload-{{$workload->id}}">
                                        <!-- Customize display based on workload details -->
                                        <div class="row">
                                            <div class="col">{{ $workload->user->name }} {{ $workload->capacity }}</div>
                                            <div class="col">
                                                <button class="workload-edit-button" data-day="{{ $day }}" data-month="{{request()->query('month') ?? $currentMonth}}" data-year="{{request()->query('year') ?? $currentYear}}" data-user="{{ $workload->user->id}}" data-name="{{ $workload->user->name}}" data-capacity="{{ $workload->capacity}}" data-bikeid="{{ $workload->bike->id}}" data-bikename="{{ $workload->bike->name}}" data-workloadid="{{ $workload->id}}"><i class="bi bi-pen"></i></button>
                                                <button class="workload-delete-button" data-day="{{ $day }}" data-month="{{request()->query('month') ?? $currentMonth}}" data-year="{{request()->query('year') ?? $currentYear}}" data-user="{{ $workload->user->id}}" data-name="{{ $workload->user->name}}" data-capacity="{{ $workload->capacity}}" data-bikeid="{{ $workload->bike->id}}" data-bikename="{{ $workload->bike->name}}" data-workloadid="{{ $workload->id}}"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">{{ $workload->bike->name }}</div>
                                        </div>
                                    </div>
                                @endforeach
                                @if($workloadData[$day][0]->day->freeCouriers()->isNotEmpty())
                                    <button class="work-button" data-day="{{ $day }}" data-month="{{request()->query('month') ?? $currentMonth}}" data-year="{{request()->query('year') ?? $currentYear}}">Add worker</button>
                                @endif
                            @else
                                <button class="work-button" data-day="{{ $day }}" data-month="{{request()->query('month') ?? $currentMonth}}" data-year="{{request()->query('year') ?? $currentYear}}">Add worker</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @elseif ($view == 'weekly')
            @foreach(range(1, $daysInWeek) as $day)
                <div class="col-md-2">
                    <div class="card">
                        <div class="card-header">
                            <?php $p = DateTime::createFromFormat('Y-m-d',$currentYear."-".$currentMonth."-".$day)?>
                            <h5>@displayDate($currentYear.'-'.$currentMonth.'-'.$day) {{$p->format('l')}}<a class="btn btn-info" href="{{ route('day.showdashboard', ['date' => $currentYear.'-'.$currentMonth.'-'.$day]) }}">List</a></h5>
                        </div>
                        <div class="card-body">
                            @if(isset($workloadData[$day]))
                                @foreach($workloadData[$day] as $workload)
                                    <div class="row" id="workload-{{$workload->id}}">
                                        <div class="row">
                                            <div class="col">{{ $workload->user->name }} {{ $workload->capacity }}</div>
                                            <div class="col">
                                                <button class="workload-edit-button" data-day="{{ $day }}" data-month="{{request()->query('month') ?? $currentMonth}}" data-year="{{request()->query('year') ?? $currentYear}}" data-user="{{ $workload->user->id}}" data-name="{{ $workload->user->name}}" data-capacity="{{ $workload->capacity}}" data-bikeid="{{ $workload->bike->id}}" data-bikename="{{ $workload->bike->name}}" data-workloadid="{{ $workload->id}}"><i class="bi bi-pen"></i></button>
                                                <button class="workload-delete-button" data-day="{{ $day }}" data-month="{{request()->query('month') ?? $currentMonth}}" data-year="{{request()->query('year') ?? $currentYear}}" data-user="{{ $workload->user->id}}" data-name="{{ $workload->user->name}}" data-capacity="{{ $workload->capacity}}" data-bikeid="{{ $workload->bike->id}}" data-bikename="{{ $workload->bike->name}}" data-workloadid="{{ $workload->id}}"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">{{ $workload->bike->name }}</div>
                                        </div>
                                    </div>
                                @endforeach
                                @if($workloadData[$day][0]->day->freeCouriers()->isNotEmpty())
                                    <button class="work-button" data-day="{{ $day }}" data-month="{{request()->query('month') ?? $currentMonth}}" data-year="{{request()->query('year') ?? $currentYear}}">Add worker</button>
                                @endif
                            @else
                                <button class="work-button" data-day="{{ $day }}" data-month="{{request()->query('month') ?? $currentMonth}}" data-year="{{request()->query('year') ?? $currentYear}}">Add worker</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<!-- MODAL WINDOWS -->
<div class="modal fade" id="workloadModal" tabindex="-1"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <form id="workloadForm" action="{{ route('workload.storeJavascript') }}" method="POST">
                    @csrf
                    <input type="hidden" name="day" id="dayField" value="">
                    <input type="hidden" name="month" id="monthField" value="">
                    <input type="hidden" name="year" id="yearField" value="">
                    <input type="hidden" name="workloadid" id="workloadid" value="">
                    <div class="form-group">
                        <label for="bike">Courier:</label>
                        <select class="form-control" id="courier" name="user">
                            @foreach($bikes as $bike)
                                <option value=""></option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="capacity">Capacity:</label>
                        <input type="text" class="form-control" id="capacity" name="capacity">
                    </div>
                    <div class="form-group">
                        <label for="bike">Choose Bike:</label>
                        <select class="form-control" id="bike" name="bike">
                            @foreach($bikes as $bike)
                                <option value="{{ $bike->id }}" >{{ $bike->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="button" id="submitWorkload" data-option="create" class="btn btn-primary">Apply</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.querySelectorAll('.workload-delete-button').forEach(button => {
    button.addEventListener('click', () => {
        const workloadid = button.dataset.workloadid;
        const user = button.dataset.user;
        const day = button.dataset.day;
        const month = button.dataset.month;
        const year = button.dataset.year;
        const name = button.dataset.name;
        const capacity = button.dataset.capacity;
        const bikeid = button.dataset.bikeid;
        const bikename = button.dataset.bikename;
        const form = document.querySelector(`#workloadForm`);
        if (form) {
            form.setAttribute('action', "{{ route('workload.deleteJavascript') }}");
            document.getElementById('dayField').value = day;
            document.getElementById('monthField').value = month;
            document.getElementById('yearField').value = year;
            document.getElementById('capacity').value = capacity;
            document.getElementById('capacity').readOnly = true;
            document.getElementById('workloadid').value = workloadid;
            const courierSelect = document.getElementById('courier'); courierSelect.innerHTML = '';
            const option = document.createElement('option');
            option.value = user;
            option.innerText = name;
            option.selected = true;
            courierSelect.appendChild(option);
            const bikeSelect = document.getElementById('bike'); bikeSelect.innerHTML = '';
            const optionbike = document.createElement('option');
            optionbike.value = bikeid;
            optionbike.innerText = bikename;
            optionbike.selected = true;
            bikeSelect.appendChild(optionbike);
            buttonas = document.getElementById('submitWorkload');
            buttonas.innerHTML = "Delete";
            buttonas.classList.remove('btn-primary');
            buttonas.classList.add('btn-danger');            
            $('#workloadModal').modal('show');  
        }
    });
});
document.querySelectorAll('.workload-edit-button').forEach(button => {
    button.addEventListener('click', () => {
        const workloadid = button.dataset.workloadid;
        const user = button.dataset.user;
        const day = button.dataset.day;
        const month = button.dataset.month;
        const year = button.dataset.year;
        const name = button.dataset.name;
        const capacity = button.dataset.capacity;
        const bikeid = button.dataset.bikeid;
        const bikename = button.dataset.bikename;
        const form = document.querySelector(`#workloadForm`);
        if (form) {
            form.setAttribute('action', "{{ route('workload.updateJavascript') }}");
            document.getElementById('dayField').value = day;
            document.getElementById('monthField').value = month;
            document.getElementById('yearField').value = year;
            document.getElementById('capacity').value = capacity;
            document.getElementById('capacity').readOnly = false;
            document.getElementById('workloadid').value = workloadid;
            getFreeCouriers(day,month,year,'free')
                .then(data => {
                    const courierSelect = document.getElementById('courier'); courierSelect.innerHTML = '';
                    data.forEach(courier => {
                        const option = document.createElement('option');
                        option.value = courier.id;
                        option.innerText = courier.name;
                        courierSelect.appendChild(option);
                    });
                    const option = document.createElement('option');
                    option.value = user;
                    option.innerText = name;
                    option.selected = true;
                    courierSelect.appendChild(option);
                });
            getFreeBikes(day,month,year,'free')
                .then(data => {
                    const bikeSelect = document.getElementById('bike'); bikeSelect.innerHTML = '';
                    data.forEach(bike => {
                        const option = document.createElement('option');
                        option.value = bike.id;
                        option.innerText = bike.name;
                        bikeSelect.appendChild(option);
                    });
                    const option = document.createElement('option');
                    option.value = bikeid;
                    option.innerText = bikename;
                    option.selected = true;
                    bikeSelect.appendChild(option);
                });
            buttonas = document.getElementById('submitWorkload');
            buttonas.innerHTML = "Apply";
            buttonas.classList.remove('btn-danger');
            buttonas.classList.add('btn-primary');            
            $('#workloadModal').modal('show');            
        }
    });
});
document.querySelectorAll('.work-button').forEach(button => {
    button.addEventListener('click', () => {
        const day = button.dataset.day;
        const month = button.dataset.month;
        const year = button.dataset.year;
        const form = document.querySelector(`#workloadForm`);
        if (form) {
            document.getElementById('dayField').value = day;
            document.getElementById('monthField').value = month;
            document.getElementById('yearField').value = year;
            getFreeBikes(day,month,year,'free')
                .then(data => {
                    const bikeSelect = document.getElementById('bike'); bikeSelect.innerHTML = '';
                    data.forEach(bike => {
                        const option = document.createElement('option');
                        option.value = bike.id;
                        option.innerText = bike.name;
                        bikeSelect.appendChild(option);
                    });
                });
            getFreeCouriers(day,month,year,'free')
                .then(data => {
                    const courierSelect = document.getElementById('courier'); courierSelect.innerHTML = '';
                    data.forEach(courier => {
                        const option = document.createElement('option');
                        option.value = courier.id;
                        option.innerText = courier.name;
                        courierSelect.appendChild(option);
                    });
                });
            $('#workloadModal').modal('show');
        }
    });
});
async function getFreeBikes(day, month, year, option) {
    try {
        const response = await fetch(`{{ asset('days/get-free-bikes') }}?day=${day}&month=${month}&year=${year}&option=${option}`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching free bikes:', error);
        return null;
    }
}
async function getFreeCouriers(day, month, year, option) {
    try {
        const response = await fetch(`{{ asset('days/get-free-couriers') }}?day=${day}&month=${month}&year=${year}&option=${option}`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching free couriers:', error);
        return null;
    }
}
document.getElementById('submitWorkload').addEventListener('click', function() {
    const form = document.getElementById('workloadForm');
    const formData = new FormData(form);
    const xhr = new XMLHttpRequest();
    xhr.open('POST', form.action, true);
    xhr.setRequestHeader('X-CSRF-Token', '{{ csrf_token() }}');
    xhr.onload = function() {
        parsedMessage = JSON.parse(xhr.responseText).message;
        if(parsedMessage === 'deleted'){
            document.getElementById('workload-'+formData.get('workloadid')).remove();
        }
        if(parsedMessage === 'updated'){
            workloadCard = document.getElementById('workload-'+formData.get('workloadid'));
            day = formData.get('day');
            month = formData.get('month');
            year = formData.get('year');
            user = formData.get('user');
            name = formData.get('name');
            capacity = formData.get('capacity');
            bikeId = formData.get('bike');
            bikeName = formData.get('bikeName');
            workloadId = formData.get('workloadId');
            populateData(day, month, year, user, name, capacity, bikeId, bikeName, workloadId);
        }
    };
    xhr.send(formData);
});
function populateData(day, month, year, user, name, capacity, bikeId, bikeName, workloadId) {
    const rowElement = document.getElementById(`workload-${workloadId}`);
    if (rowElement) {
        const firstRow = rowElement.querySelector('.col');
        if (firstRow) {
            firstRow.textContent = `${workloadId}${name} ${capacity}`;
        }
        const secondRow = rowElement.querySelectorAll('.row')[1];
        if (secondRow) {
            const bikeCol = secondRow.querySelector('.col');
            if (bikeCol) {
                bikeCol.textContent = bikeName;
            }
        }
        const editButton = rowElement.querySelector('.workload-edit-button');
        const deleteButton = rowElement.querySelector('.workload-delete-button');
        if (editButton && deleteButton) {
            editButton.dataset.day = day;
            editButton.dataset.month = month;
            editButton.dataset.year = year;
            editButton.dataset.user = user;
            editButton.dataset.name = name;
            editButton.dataset.capacity = capacity;
            editButton.dataset.bikeid = bikeId;
            editButton.dataset.bikename = bikeName;
            editButton.dataset.workloadid = workloadId;
            deleteButton.dataset.day = day;
            deleteButton.dataset.month = month;
            deleteButton.dataset.year = year;
            deleteButton.dataset.user = user;
            deleteButton.dataset.name = name;
            deleteButton.dataset.capacity = capacity;
            deleteButton.dataset.bikeid = bikeId;
            deleteButton.dataset.bikename = bikeName;
            deleteButton.dataset.workloadid = workloadId;
        }
    }
}
</script>
@endsection
