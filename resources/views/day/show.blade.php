@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col">{{$day->name}}</div>
        <div class="col">
            <form method="GET" action="{{ route('workload.calendar') }}">
            <input type="hidden" name="month" id="monthField" value="{{$day->month()}}">
            <input type="hidden" name="year" id="yearField" value="{{$day->year()}}">
    <button type="submit">Workload view</button>
</form>
        </div>
    </div>
    <div class="row">
    <div class="col-md-2">
        <div class="card-header" id="job-list-header" >Jobs</div>
        <ul class="list-group droppable-list" ullistType="job-list">
            @foreach ($jobs as $job)
                <li class="list-group-item draggable-item" draggable="true" data-job-id="{{ $job->id }}" listType="job-list">
                <div class="row border-bottom border-4">
                        <div class="col" style="color: green;"id="job-id-{{$job->id}}-display" >NJ{{ $job->id }}</div>
                        <div class="col" style="color: red;" id="job-no-{{$job->id}}-display"  hidden>{{ $job->eilesNumeris }}</div>
                        <div class="col" style="background-color: {{$job->status->color}};" id="job-no-{{$job->id}}-display"  >{{ $job->status->name }}</div>
                    </div>
                    <div class="row border-bottom border-4">
                        <div class="row">
                            <div class="col" style="">{{$job->sender->name}}</div>
                        </div>
                        <div class="row">                        
                            <div class="col" style="">{{ date('H:i', strtotime($job->pickup_time_begin)) }} - {{ date('H:i', strtotime($job->pickup_time_end)) }}</div>
                        </div>
                        <div class="row">
                            <div class="col" style="">{{$job->pickup_address}}</div>
                        </div>
                    </div>
                    <div class="row border-bottom border-4" >
                        <div class="row">
                            <div class="col" style="">{{$job->receiver->name}}</div>
                        </div>
                        <div class="row">
                            <div class="col" style="">{{ date('H:i', strtotime($job->dropoff_time_begin)) }} - {{ date('H:i', strtotime($job->dropoff_time_end)) }}</div>
                        </div>
                        <div class="row">
                            <div class="col" style="">{{$job->delivery_address}}</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col"><button data-show-url="{{ asset('jobs/show') }}/{{ $job->id }}" data-job-id="{{ $job->id }}" type="button" class="btn btn-primary job-details" data-bs-toggle="modal" data-bs-target="#jobModal">More</button></div>
                        <div class="col"><button data-edit-url="{{ asset('jobs/edit') }}/{{ $job->id }}" data-job-id="{{ $job->id }}" type="button" class="btn btn-secondary job-edit" data-bs-toggle="modal" data-bs-target="#jobModal">Edit</button></div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
    @foreach ($users as $user)
    @if ($users->count() <= 3)
    <div class="col-md-3">
    @else
    <div class="col-md-2">
    @endif
    
        <div class="card-header" ><div class="row">{{ $user->name }} {{$user->workload($day)->capacity}}</div><div class="row border"> {{$user->workload($day)->bike->name}}</div></div>
        <ul class="list-group droppable-list" ullistType="user-list-{{$user->id }}">
            @foreach ($user->jobs as $job)
                <li class="list-group-item draggable-item border border-5" draggable="true" data-job-id="{{ $job->id }}" listType="user-list-{{$user->id }}">
                    <div class="row border-bottom border-4">
                        <div class="col" style="color: green;"id="job-id-{{$job->id}}-display" >NJ{{ $job->id }}</div>
                        <div class="col" style="color: red;" id="job-no-{{$job->id}}-display"  hidden>{{ $job->eilesNumeris }}</div>
                        <div class="col" style="background-color: {{$job->status->color_main}};" id="job-no-{{$job->id}}-display"  >{{ $job->status->name }}</div>
                    </div>
                    <div class="row border-bottom border-4" >
                        <div class="row">
                            <div class="col" style="">{{$job->sender->name}}</div>
                        </div>
                        <div class="row">                        
                            <div class="col" style="">{{ date('H:i', strtotime($job->pickup_time_begin)) }} - {{ date('H:i', strtotime($job->pickup_time_end)) }}</div>
                        </div>
                        <div class="row">
                            <div class="col" style="">{{$job->pickup_address}}</div>
                        </div>
                    </div>
                    <div class="row border-bottom border-4">
                        <div class="row">
                            <div class="col" style="">{{$job->receiver->name}}</div>
                        </div>
                        <div class="row">
                            <div class="col" style="">{{ date('H:i', strtotime($job->dropoff_time_begin)) }} - {{ date('H:i', strtotime($job->dropoff_time_end)) }}</div>
                        </div>
                        <div class="row">
                            <div class="col" style="">{{$job->delivery_address}}</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col"><button data-show-url="{{ asset('jobs/show') }}/{{ $job->id }}" data-job-id="{{ $job->id }}" type="button" class="btn btn-primary job-details" data-bs-toggle="modal" data-bs-target="#jobModal">More</button></div>
                        <div class="col"><button data-edit-url="{{ asset('jobs/edit') }}/{{ $job->id }}" data-job-id="{{ $job->id }}" type="button" class="btn btn-secondary job-edit" data-bs-toggle="modal" data-bs-target="#jobModal">Edit</button></div>
                    </div>
                        <!-- </div> -->
                </li>
            @endforeach
        </ul>
    </div>    
    @endforeach
    </div>
</div>
<div class="modal fade" id="jobModal" tabindex="-1" aria-labelledby="jobModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- This part will be populated with detailed job information fetched via JavaScript -->
            <div class="modal-body" id="jobDetails">
                <!-- Detailed job information will be displayed here -->
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
    let draggedItem = null;
    const droppableLists = document.querySelectorAll('.droppable-list');
    const userCards = document.querySelectorAll('.card-header');
    droppableLists.forEach(item => {
        item.addEventListener('dragstart', (e) => {
            draggedItem = e.target;
        });
        item.addEventListener('dragover', (e) => {
            e.preventDefault();
            const targetList = e.target.closest('.list-group');
            const afterElement = getDragAfterElement(targetList, e.clientY);
            if (afterElement == null) {
                targetList.appendChild(draggedItem);
            } else {
                targetList.insertBefore(draggedItem, afterElement);
            }
        });
        item.addEventListener('drop', (e) => {
            e.preventDefault();
            const targetList    = e.target.closest('.list-group');
            const data_job_id   = e.target.closest('.draggable-item').getAttribute('data-job-id');
            const listtype      = e.target.closest('.draggable-item').getAttribute('listType');
            modifyEilesNumeris(targetList);
        });
    });
    userCards.forEach(card => {
        card.addEventListener('dragover', (e) => {
            e.preventDefault();
            let targetList = e.target.closest('.col-md-3');
            if (targetList) {
                const ulList = targetList.querySelector('.list-group.droppable-list');
                ulList.appendChild(draggedItem);
                modifyEilesNumeris(targetList);
            }else {
                targetList = e.target.closest('.col-md-2');
                if (targetList) {
                    const ulList = targetList.querySelector('.list-group.droppable-list');
                ulList.appendChild(draggedItem);
                modifyEilesNumeris(targetList);
                }
            }
        });
    });
    function modifyEilesNumeris(targetList){
        const liElements = targetList.querySelectorAll('li');
        let iteration = 1;
        liElements.forEach(li => {
            const jobNoDisplay = li.querySelector('[id^="job-no-"]');
            jobNoDisplay.textContent = iteration++;
        });
        liElements.forEach(li => {
            targetListId    =   targetList.getAttribute('ullistType');
            id              = li.getAttribute('data-job-id');
            eilesNumeris    = li.querySelector('[id^="job-no-"]').textContent;
            updateJobStatusAndCourier(id, targetListId,eilesNumeris);
        });
    }
    function updateJobStatusAndCourier(jobId, targetListId,eilesNumeris) {
        // Use AJAX to send a request to your Laravel controller to update the job data
        const url = '{{ asset("jobs/update-job-ajax") }}'; // Replace with your actual route URL
        const data = {
            jobId,
            targetListId,
            eilesNumeris
        };

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}', // Include the CSRF token
            },
            body: JSON.stringify(data),
        })
        .then(response => response.json())
        .then(data => {
            //console.log(data); // Handle the response from the server
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.draggable-item:not(.dragging)')];
    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        } else {
            return closest;
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}
//žžžžžžžžžžžžžžžžžžžžžžžžžžžžžžž MODAL žžžžžžžžžžžžžžžžžžžžžžžžžž
/*$('.job-details').on('click', function (event) {
    var button = $(event.currentTarget);
    var editUrl = button.data('show-url');
    var jobId = button.data('job-id');
    var modal = $('#jobModal');
    fetch(editUrl)
    //const url = '{{ asset("jobs/show/") }}'; 
        .then(response => response.text())
        .then(data => {
            modal.find('.modal-body').html(data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
});
*/
document.querySelectorAll('.job-details').forEach(function(element) {
    element.addEventListener('click', function(event) {
        var button = event.currentTarget;
        var editUrl = button.getAttribute('data-show-url');
        var jobId = button.getAttribute('data-job-id');
        var modal = document.getElementById('jobModal');
        console.log("button "+button);
        console.log(editUrl);       
        fetch(editUrl)
            .then(function(response) {
                return response.text();
            })
            .then(function(data) {
                console.log(data);
                modal.querySelector('.modal-body').innerHTML = data;
            })
            .catch(function(error) {
                console.error('Error:', error);
            });
    });
});
/*$('.job-edit').on('click', function (event) {
    var button = $(event.currentTarget);
    var editUrl = button.data('edit-url');
    var jobId = button.data('job-id');
    var modal = $('#jobModal');

    fetch(editUrl)
        .then(response => response.text())
        .then(data => {
            modal.find('.modal-body').html(data); // Assuming the same body for editing
        })
        .catch(error => {
            console.error('Error:', error);
        });
});
*/
document.querySelectorAll('.job-edit').forEach(function(element) {
    element.addEventListener('click', function(event) {
        var button = event.currentTarget;
        var editUrl = button.getAttribute('data-edit-url');
        var jobId = button.getAttribute('data-job-id');
        var modal = document.getElementById('jobModal');

        fetch(editUrl)
            .then(function(response) {
                return response.text();
            })
            .then(function(data) {
                
                modal.querySelector('.modal-body').innerHTML = data;
            })
            .catch(function(error) {
                console.error('Error:', error);
            });
    });
});
</script>
@endsection