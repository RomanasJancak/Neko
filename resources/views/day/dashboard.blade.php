@extends('layouts.app')
@section('content')
<div class="container">
  <div class="row">
    <div class="col-2">
      <label for="nameField">Date : </label>
      <input type="date" id="datepicker" class="form-control" value="{{$day->name}}" >
    </div>
  </div>
  <div class="row">
    <div class="job-columenToGetDropEvent col-2 border border-dark rounded" id="job-column-unassigned">
      <div class="row border job-header" id="job-column-unassigned-header">
        <div class="col border">Unassigned jobs list</div>
      </div>
      <div class="row job-dropableListArea">
        @foreach ($jobsUnassigned as $job)
        @foreach ($job->tasks as $task)
        <div class="col-12 border border-dark border-2 rounded draggable" style="background-color: {{$task->job->status->color_main}};" draggable="true" id="taskElement-{{$task->id}}" data-jobid="{{$task->job->id}}">
          <div class="row job-header-div">
            <div class="col job-id-div">
              NJ{{$task->job->id}}
            </div>
            <div class="col job-status-div">
                {{$task->resolvedStatus()->name}} [{{$task->order_number}}]
            </div>
          </div>
          @isset($task->pickup)  
          <div class="row pickup-row border" style="background-color: {{$task->job->status->color_pickup}};">
            <div  class="col-2" 
                    style="
                      writing-mode: vertical-lr;
                      transform: rotate(180deg);
                      text-align: center;
                      vertical-align: middle;"
            >
              PU
            </div>          
            <div class="col-8">
                <div class="row"><div class="col">{{$task->pickup->pickupclientname}}</div></div>
                <div class="row"><div class="col">{{ \Illuminate\Support\Carbon::parse($task->pickup->pickup_time_begin)->format('H:i') }}</div><div class="col">{{ \Illuminate\Support\Carbon::parse($task->pickup->pickup_time_end)->format('H:i') }}</div></div>
                <div class="row"><div class="col">{{$task->pickup->pickupAddressShort()}}</div></div>
            </div>
          </div>
          @endisset
          @isset($task->package)
          <div class="row pickup-row border" style="background-color: {{$task->job->status->color_dropoff}};">
            <div  class="col-2" 
                    style="
                      writing-mode: vertical-lr;
                      transform: rotate(180deg);
                      text-align: center;
                      vertical-align: middle;"
            >
              DROP
            </div>          
            <div class="col-8">
                <div class="row"><div class="col">{{$task->package->dropoff_name}}</div></div>
                <div class="row"><div class="col">{{ \Illuminate\Support\Carbon::parse($task->package->packagedropofftimebegin)->format('H:i') }}</div><div class="col">{{ \Illuminate\Support\Carbon::parse($task->package->packagedropofftimeend)->format('H:i') }}</div></div>
                <div class="row"><div class="col">{{$task->package->addressShort()}}</div></div>
            </div>
          </div>
          @endisset
          @isset($task->return)
          <div class="row pickup-row border" style="background-color: {{$task->job->status->color_dropoff}};">
            <div  class="col-2" 
                    style="
                      writing-mode: vertical-lr;
                      transform: rotate(180deg);
                      text-align: center;
                      vertical-align: middle;"
            >
              RETURN
            </div>          
            <div class="col-8">
                <div class="row"><div class="col">{{$task->return->name}}</div></div>
                <div class="row"><div class="col">{{ \Illuminate\Support\Carbon::parse($task->return->time_begin)->format('H:i') }}</div><div class="col">{{ \Illuminate\Support\Carbon::parse($task->return->time_end)->format('H:i') }}</div></div>
                <div class="row"><div class="col">{{$task->return->addressShort()}}</div></div>
            </div>
          </div>
          @endisset
          @isset($task->customTask)
          <div class="row pickup-row border" style="background-color: {{$task->job->status->color_dropoff}};">
            <div  class="col-2" 
                    style="
                      writing-mode: vertical-lr;
                      transform: rotate(180deg);
                      text-align: center;
                      vertical-align: middle;"
            >
              CUSTOM
            </div>          
            <div class="col-8">
                <div class="row"><div class="col">{{$task->customTask->name}}</div></div>
                <div class="row"><div class="col">{{ \Illuminate\Support\Carbon::parse($task->customTask->time_begin)->format('H:i') }}</div><div class="col">{{ \Illuminate\Support\Carbon::parse($task->customTask->time_end)->format('H:i') }}</div></div>
                <div class="row"><div class="col">{{$task->customTask->addressShort()}}</div></div>
            </div>
          </div>
          @endisset
        </div> 
        @endforeach
        @endforeach
      </div>
    </div>
    @foreach ($users as $user)
    <div class="job-columenToGetDropEvent col border border-dark rounded" id="job-column-usercolumn-{{$user->id}}">
      <div class="row border job-header" id="job-column-usercolumn_{{$user->id}}-header">
        <div class="col-auto">{{$user->name}}</div>
        <div class="col-auto">{{$user->workload($day)->bike->name}}</div>
        <div class="col"><button class="btn btn-primary button-copy-user-jobs" id="button-copy-user-jobs-{{$user->id}}" data-jobs="">Copy</button></div>
      </div>
      <div class="row job-dropableListArea">
        @foreach ($user->tasksByDate($date) as $task)
        <div class="col-12 border border-dark border-2 rounded draggable" style="background-color: {{$task->job->status->color_main}};" draggable="true" id="taskElement-{{$task->id}}" data-jobid="{{$task->job->id}}">
          <div class="row job-header-div">
            <div class="col job-id-div">
              NJ {{$task->job->id}}
            </div>
            <div class="col job-status-div">
                {{$task->status->name}} [{{$task->order_number}}]
            </div>
          </div>
          @isset($task->pickup)  
          <div class="row pickup-row border" style="background-color: {{$task->job->status->color_pickup}};">
            <div  class="col-2" 
                    style="
                      writing-mode: vertical-lr;
                      transform: rotate(180deg);
                      text-align: center;
                      vertical-align: middle;"
            >PU---> 
            </div>          
            <div class="col-8">
                <div class="row"><div class="col">{{$task->pickup->pickupclientname}}</div></div>
                <div class="row"><div class="col">{{ \Illuminate\Support\Carbon::parse($task->pickup->pickup_time_begin)->format('H:i') }}</div><div class="col">{{ \Illuminate\Support\Carbon::parse($task->pickup->pickup_time_end)->format('H:i') }}</div></div>
                <div class="row"><div class="col">{{$task->pickup->pickupAddressShort()}}</div></div>
            </div>
          </div>
          @endisset
          @isset($task->package)
          <div class="row pickup-row border" style="background-color: {{$task->job->status->color_dropoff}};">
            <div  class="col-2" 
                    style="
                      writing-mode: vertical-lr;
                      transform: rotate(180deg);
                      text-align: center;
                      vertical-align: middle;"
            >
              <---DROP
            </div>          
            <div class="col-8">
                <div class="row"><div class="col">{{$task->package->dropoff_name}}</div></div>
                <div class="row"><div class="col">{{ \Illuminate\Support\Carbon::parse($task->package->packagedropofftimebegin)->format('H:i') }}</div><div class="col">{{ \Illuminate\Support\Carbon::parse($task->package->packagedropofftimeend)->format('H:i') }}</div></div>
                <div class="row"><div class="col">{{$task->package->addressShort()}}</div></div>
            </div>
          </div>
          @endisset
          @isset($task->return)
          <div class="row pickup-row border" style="background-color: {{$task->job->status->color_dropoff}};">
            <div  class="col-2" 
                    style="
                      writing-mode: vertical-lr;
                      transform: rotate(180deg);
                      text-align: center;
                      vertical-align: middle;"
            >
              RETURN
            </div>          
            <div class="col-8">
                <div class="row"><div class="col">{{$task->return->name}}</div></div>
                <div class="row"><div class="col">{{ \Illuminate\Support\Carbon::parse($task->return->time_begin)->format('H:i') }}</div><div class="col">{{ \Illuminate\Support\Carbon::parse($task->return->time_end)->format('H:i') }}</div></div>
                <div class="row"><div class="col">{{$task->return->addressShort()}}</div></div>
            </div>
          </div>
          @endisset
          @isset($task->customTask)
          <div class="row pickup-row border" style="background-color: {{$task->job->status->color_dropoff}};">
            <div  class="col-2" 
                    style="
                      writing-mode: vertical-lr;
                      transform: rotate(180deg);
                      text-align: center;
                      vertical-align: middle;"
            >
              CUSTOM
            </div>          
            <div class="col-8">
                <div class="row"><div class="col">{{$task->customTask->name}}</div></div>
                <div class="row"><div class="col">{{ \Illuminate\Support\Carbon::parse($task->customTask->time_begin)->format('H:i') }}</div><div class="col">{{ \Illuminate\Support\Carbon::parse($task->customTask->time_end)->format('H:i') }}</div></div>
                <div class="row"><div class="col">{{$task->customTask->addressShort()}}</div></div>
            </div>
          </div>
          @endisset
          <div class="row btn-group btn-group-sm" role="group">
            @php
              $nextStatusInfo = $task->statusNextInfo();
              $nextStatusOptions = $nextStatusInfo['status_next_options'] ?? [];
            @endphp
            <div class="col-auto">
              <button type="button" class="btn btn-success" id="button-currentTaskStatus-{{$task->id}}" disabled>{{$task->status->name}}</button>
            </div>
            <div class="col-auto task-next-status-container" id="task-next-status-container-{{$task->id}}">
              @if(count($nextStatusOptions) > 0)
                @foreach($nextStatusOptions as $statusOption)
                  <button
                    type="button"
                    class="btn btn-outline-primary button-changeTaskStatus"
                    data-jobid="{{$task->id}}"
                    data-statusid="{{$statusOption['id']}}"
                    id="button-changeTaskStatus-{{$task->id}}-{{$statusOption['id']}}"
                  >{{$statusOption['name']}}</button>
                @endforeach
              @else
                <button type="button" class="btn btn-secondary" disabled>No next status</button>
              @endif
            </div>
          </div>
        </div> 
        @endforeach

      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection
@section('scripts')
<script>

document.addEventListener('DOMContentLoaded', function() {
  var datepicker = document.getElementById('datepicker');

  datepicker.addEventListener('change', function(event) {
                var selectedDate = event.target.value;
                var url = '{{ route("day.showdashboard", ":date") }}';
                url = url.replace(':date', selectedDate);
                window.location.href = url;
            });
  const updateTaskStatusRouteUrl =
    (window.ROUTES && window.ROUTES.WEB && window.ROUTES.WEB.TASK && window.ROUTES.WEB.TASK.UPDATE_STATUS)
      ? window.ROUTES.WEB.TASK.UPDATE_STATUS
      : '{{ route("task.updateStatus") }}';

  function renderNextStatusButtons(taskId, nextStatusOptions) {
    const container = document.getElementById('task-next-status-container-' + taskId);
    if (!container) {
      return;
    }

    container.innerHTML = '';

    if (!Array.isArray(nextStatusOptions) || nextStatusOptions.length === 0) {
      const disabledButton = document.createElement('button');
      disabledButton.type = 'button';
      disabledButton.className = 'btn btn-secondary';
      disabledButton.disabled = true;
      disabledButton.textContent = 'No next status';
      container.appendChild(disabledButton);
      return;
    }

    nextStatusOptions.forEach(function(option) {
      const nextButton = document.createElement('button');
      nextButton.type = 'button';
      nextButton.className = 'btn btn-outline-primary button-changeTaskStatus';
      nextButton.setAttribute('data-jobid', String(taskId));
      nextButton.setAttribute('data-statusid', String(option.id));
      nextButton.id = 'button-changeTaskStatus-' + taskId + '-' + option.id;
      nextButton.textContent = option.name;
      container.appendChild(nextButton);
    });
  }

  document.addEventListener('click', function(event) {
    const button = event.target.closest('.button-changeTaskStatus');
    if (!button) {
      return;
    }

    event.preventDefault();
    event.stopPropagation();

    const taskId = button.getAttribute('data-jobid');
    const selectedStatusId = button.getAttribute('data-statusid');

    if (!taskId || !selectedStatusId) {
      return;
    }

    const postData = {
      id: taskId,
      status_id: selectedStatusId,
    };

    fetch(updateTaskStatusRouteUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(postData)
    })
    .then(async response => {
      const data = await response.json().catch(() => ({}));
      return {
        ok: response.ok,
        status: response.status,
        data: data,
      };
    })
    .then(result => {
      const data = result.data || {};

      if (!result.ok) {
        renderNextStatusButtons(taskId, data.next_status_options || []);
        if (data.message) {
          console.warn('Task status update rejected:', data.message);
        }
        return;
      }

      if (!data.success) {
        renderNextStatusButtons(taskId, data.next_status_options || []);
        if (data.message) {
          console.warn('Task status update skipped:', data.message);
        }
        return;
      }

      const currentStatusButton = document.getElementById('button-currentTaskStatus-' + taskId);
      if (currentStatusButton && data.new_status_name) {
        currentStatusButton.textContent = data.new_status_name;
      }

      renderNextStatusButtons(taskId, data.next_status_options || []);

      const jobElement = button.closest('.draggable');
      if (jobElement) {
        jobElement.style.backgroundColor = data.new_status_color;

        jobElement.querySelectorAll('.pickup-row').forEach(function(element) {
          if (element) {
            element.style.backgroundColor = data.new_status_color_pickup;
          }
        });

        jobElement.querySelectorAll('.package-row').forEach(function(element) {
          if (element) {
            element.style.backgroundColor = data.new_status_color_dropoff;
          }
        });
      }
    })
    .catch(error => {
      console.error('Error:', error.message);
    });
  });

  var draggableElements = document.querySelectorAll('.draggable');
  var jobdropableListAreas = document.querySelectorAll('.job-dropableListArea');
  var jobdropableListAreas2 = document.querySelectorAll('.job-columenToGetDropEvent');
  var splitButtons = document.querySelectorAll('.button-split');
  var copyButtons = document.querySelectorAll('.button-copy-user-jobs'); 
  var dragedElement;
  assignJobInformationToCopyButtons();
  function assignJobInformationToCopyButtons(){
    copyButtons.forEach(function(button){
      button.setAttribute('data-jobs','');
      var jobsArray = [];
      var column  = button.closest('.job-columenToGetDropEvent');
      var draggableElementsForCopy = column.querySelectorAll('[id^="jobElement-"]');
      draggableElementsForCopy.forEach(function(jobElement){
        var id = jobElement.id.replace("jobElement-", "");
        const routeUrl = "{{ route('job.getJobInfo', ['id' => ':id']) }}".replace(':id', id);
        fetch(routeUrl)
          .then(response => response.json())
          .then(data => {
            if (data) {
              jobsArray.push(data);
              var jobsJSON = JSON.stringify(jobsArray);
              button.setAttribute('data-jobs', jobsJSON);
            }
          })
          .catch(error => {
            console.error(error);
          });
      });    
    });
  }
  copyButtons.forEach(function(button){
    button.addEventListener('click', function(event) {
      console.log(button.getAttribute('data-jobs'));
    });
  });

  splitButtons.forEach(function(button) {
    button.addEventListener('click', function(event) {
      event.preventDefault();
      var jobElement = button.closest('.draggable');
      var packages = jobElement.querySelectorAll('.package-row');
      packages.forEach(function(package) {
        var clonedJobElement = jobElement.cloneNode(true);
        var clonedPickupRows = clonedJobElement.querySelectorAll('.pickup-row');
        clonedPickupRows.forEach(function(pickupRow) {
          pickupRow.remove();
        });
        var clonedpackages = clonedJobElement.querySelectorAll('.package-row');
        clonedpackages.forEach(function(clonedpackage) {
          if(clonedpackage.id === package.id){
            clonedJobElement.id = clonedJobElement.id+'-'+package.id;
          }else{
            clonedpackage.remove();
          }
        });
        clonedJobElement.addEventListener('dragstart', function(event) {
          event.dataTransfer.setData("id", event.target.id);
          dragedElement = clonedJobElement;
        });
        clonedJobElement.addEventListener('drag', function(event) {
        });
        jobElement.parentNode.insertBefore(clonedJobElement, jobElement.nextSibling);
      });
      packages.forEach(function(package) {
        package.remove();
      });
      jobElement.id = jobElement.id+'-pickup';
    });
  });

  draggableElements.forEach(function(element) {
    element.addEventListener('dragstart', function(event) {
      event.dataTransfer.setData("id", event.target.id);
      dragedElement = element;
    });
  });
  draggableElements.forEach(function(element) {
    element.addEventListener('drag', function(event) {
    });
  });
  jobdropableListAreas2.forEach(function(element) {
    element.addEventListener('dragover', function(event) {
      event.preventDefault();
    });
  });
  jobdropableListAreas2.forEach(function(element) {
    element.addEventListener('drop', function(event) {
      statusId = 0;
      event.preventDefault();
      var dropedElementId = event.dataTransfer.getData("id");
      var targetList;
      // console.log('dropedElementId : ',dropedElementId);
      if(event.target.closest('.job-header')){
        var column  = event.target.closest('.job-columenToGetDropEvent');
        targetList = column.querySelectorAll('.job-dropableListArea')[0];
      }else{     
        targetList = event.target.querySelectorAll('.job-dropableListArea')[0];
      }
      if(targetList){
        if(event.target.closest('.job-header')){ //jeigu numetama sarašo headeryje pridedama sarašo pradžioje
          targetList.insertBefore(document.getElementById(dropedElementId), targetList.firstChild);
          console.log('Task droped on the top of the list');
        }else{ 
          targetList.appendChild(document.getElementById(dropedElementId));
        }
      }else{
        targetList  = event.target.closest('.job-dropableListArea');
        targetJob   = event.target.closest('.draggable');
        targetList.insertBefore(document.getElementById(dropedElementId), targetJob.nextSibling);
      }
      if(event.target.closest('.job-columenToGetDropEvent').id === 'job-column-unassigned'){
        userId = 0;
        statusId  = 10;
        dropedElement = document.getElementById(dropedElementId);
        // jobId = dropedElementId.replace("jobElement-", ""); // IMPORTANT
        jobId = dropedElement.getAttribute('data-jobid');
        taskListAreaElement = event.target.closest('.job-dropableListArea');
        //console.log(taskListAreaElement);
        if(taskListAreaElement){
          tasks = taskListAreaElement.querySelectorAll('.draggable');
          tasks.forEach(function(task,index){
            //console.log(task.id.replace("taskElement-", ""));
            updateTaskCourierAndStatus(task.id.replace("taskElement-", ""),userId,statusId,index+1);
          });
        }else{
          //console.log(dropedElement);
          updateTaskCourierAndStatus(dropedElement.id.replace("taskElement-", ""),userId,statusId,1);
          allOtherJobTasksElements = document.querySelectorAll('.draggable[data-jobid="' + jobId + '"]');
          var copiedNodeList = document.createDocumentFragment();
          allOtherJobTasksElements.forEach(function(task,index){
            if(task.id.replace("taskElement-", "") == dropedElement.id.replace("taskElement-", "")){
            }else{
              copiedNodeList.appendChild(task.cloneNode(true));
            }
          });
          // console.log(copiedNodeList);
          var copiedNodeArray = Array.from(copiedNodeList.childNodes);
          copiedNodeArray.forEach(function(task,index){
            updateTaskCourierAndStatus(task.id.replace("taskElement-", ""),userId,statusId,index+2);
          });
        }
        
        //console.log(userId+' '+jobId);
        updateJobCourierAndStatus(jobId,'none',statusId);
        const routeUrl = "{{ route('status.getStatusInfo', ['id' => ':id']) }}".replace(':id', statusId);
        // fetch(routeUrl)
        //     .then(response => response.json())
        //     .then(data => {
        //       dropedElement.style.backgroundColor = data.color_main;
        //       dropedElement.querySelectorAll('.pickup-row').forEach(function(element) {
        //         if(element){
        //           element.style.backgroundColor = data.color_pickup;
        //         }
        //       });
        //       dropedElement.querySelectorAll('.package-row').forEach(function(element) {
        //         if(element){
        //           element.style.backgroundColor = data.color_dropoff;
        //         }
        //       });
        //     })
        //     .catch(error => {
        //         console.error(error);
        // });

      }else if(/^job-column-usercolumn-\d+$/.test(event.target.closest('.job-columenToGetDropEvent').id)){
        statusId  = 13;
        dropedElement = document.getElementById(dropedElementId);
        //jobId = dropedElementId.replace("jobElement-", "");
        jobId = dropedElement.getAttribute('data-jobid');
        userId  = event.target.closest('.job-columenToGetDropEvent').id.replace("job-column-usercolumn-", "");
        taskListAreaElement = event.target.closest('.job-dropableListArea');
        //console.log(taskListAreaElement);
        if(taskListAreaElement){
          tasks = taskListAreaElement.querySelectorAll('.draggable');
          tasks.forEach(function(task,index){
            //console.log(task.id.replace("taskElement-", ""));
            updateTaskCourierAndStatus(task.id.replace("taskElement-", ""),userId,statusId,index+1);
          });
        }else{
          //console.log(dropedElement);
          updateTaskCourierAndStatus(dropedElement.id.replace("taskElement-", ""),userId,statusId,1);
          allOtherJobTasksElements = document.querySelectorAll('.draggable[data-jobid="' + jobId + '"]');
          var copiedNodeList = document.createDocumentFragment();
          allOtherJobTasksElements.forEach(function(task,index){
            if(task.id.replace("taskElement-", "") == dropedElement.id.replace("taskElement-", "")){
            }else{
              copiedNodeList.appendChild(task.cloneNode(true));
            }
          });
          console.log(copiedNodeList);
          var copiedNodeArray = Array.from(copiedNodeList.childNodes);
          copiedNodeArray.forEach(function(task,index){
            updateTaskCourierAndStatus(task.id.replace("taskElement-", ""),userId,statusId,index+2);
          });
        }
        
        updateJobCourierAndStatus(jobId,userId,statusId);
        const routeUrl = "{{ route('status.getStatusInfo', ['id' => ':id']) }}".replace(':id', statusId);
        // fetch(routeUrl)
        //     .then(response => response.json())
        //     .then(data => {              
        //       dropedElement.style.backgroundColor = data.color_main;
        //       dropedElement.querySelectorAll('.pickup-row').forEach(function(element) {
        //         if(element){
        //           element.style.backgroundColor = data.color_pickup;
        //         }
        //       });
        //       dropedElement.querySelectorAll('.package-row').forEach(function(element) {
        //         if(element){
        //           element.style.backgroundColor = data.color_dropoff;
        //         }
        //       });
        //     })
        //     .catch(error => {
        //         console.error(error);
        // });

      }
      const routeUrl = "{{ route('status.getStatusInfo', ['id' => ':id']) }}".replace(':id', statusId);
      fetch(routeUrl)
            .then(response => response.json())
            .then(data => {              
              dropedElement.style.backgroundColor = data.color_main;
              dropedElement.querySelectorAll('.pickup-row').forEach(function(element) {
                if(element){
                  element.style.backgroundColor = data.color_pickup;
                }
              });
              dropedElement.querySelectorAll('.package-row').forEach(function(element) {
                if(element){
                  element.style.backgroundColor = data.color_dropoff;
                }
              });
            })
            .catch(error => {
                console.error(error);
        });          
    });
  });
});
function updateJobElementColors(jobId){

}
function updateJobCourierAndStatus(jobId,userId,statusId){
        // Prepare the data to send in the request body
        const data = {
            id: jobId,
            courrier_id: userId,
            status_id: statusId
        };
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        // Send a POST request to the server using the generated route
        fetch('{{ route("job.updateajax") }}', { // Blade syntax to generate the route URL
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json', // Set Accept header
                // Add any additional headers if needed
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            // if (!response.ok) {
            //     throw new Error('Failed to update job');
            // }
            return response.json();
        })
        .then(data => {
            console.log(data); // Log the success message
            // Optionally handle the response data, e.g., update UI
        })
        .catch(error => {
            console.error('Error:', error.message); // Log any errors
            // Optionally handle errors, e.g., display an error message
        });
}
function updateTaskCourierAndStatus(taskId,userId = 0,statusId,order_number){
  // console.log('Function updateTaskCourierAndStatus parameters :');
  // console.log('taskId : '+taskId);
  // console.log('userId : '+userId);
  // console.log('statusId : '+statusId);
  // console.log('order_number : '+order_number);
          const data = {
            id: taskId,
            courrier_id: userId,
            status_id: statusId,
            order_number: order_number,
        };
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        // Send a POST request to the server using the generated route
        fetch('{{ route("task.updateOrder") }}', { // Blade syntax to generate the route URL
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json', // Set Accept header
                // Add any additional headers if needed
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            // if (!response.ok) {
            //     throw new Error('Failed to update job');
            // }
            return response.json();
        })
        .then(data => {
            console.log(data); // Log the success message
            // Optionally handle the response data, e.g., update UI
        })
        .catch(error => {
            console.error('Error:', error.message); // Log any errors
            // Optionally handle errors, e.g., display an error message
        });
}   
</script>
@endsection