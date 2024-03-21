@extends('layouts.app')
@section('content')

<div class="container">
  <div class="row">
    <div class="job-columenToGetDropEvent col-2 border border-dark rounded" id="job-column-unassigned">
      <div class="row border job-header" id="job-column-unassigned-header">
        <div class="col border">Unassigned jobs list</div>
      </div>
      <div class="row job-dropableListArea">
        @foreach ($jobsUnassigned as $job)
          <div class="col-12 border border-dark border-2 rounded draggable" style="background-color: {{$job->status->color_main}};" draggable="true" id="jobElement-{{$job->id}}">
            <div class="row job-header-div">
              <div class="col job-id-div">
                NJ{{$job->id}}
              </div>
              <div class="col job-status-div">
                {{$job->status->name}}
              </div>   
            </div>
            <div class="row pickup-row border" style="background-color: {{$job->status->color_pickup}};">
              <div  class="col-2" 
                    style="
                      writing-mode: vertical-lr;
                      transform: rotate(180deg);
                      text-align: center;
                      vertical-align: middle;"
              >PU</div>
              <div class="col-8">
                @foreach ($job->tasks as $task)
                  @isset($task->pickup)                    
                    <div class="row">
                    <div class="col">
                      {{$task->pickup->pickupclientname}}
                    </div>
                    </div>
                    <div class="row"><div class="col">{{ \Illuminate\Support\Carbon::parse($task->pickup->pickup_time_begin)->format('H:i') }}</div><div class="col">{{ \Illuminate\Support\Carbon::parse($task->pickup->pickup_time_end)->format('H:i') }}</div></div>
                    <div class="row"><div class="col">{{$task->pickup->pickupAddressShort()}}</div></div>
                  @endisset
                @endforeach
              </div>
              <div  class="col-2" 
                    style="
                      writing-mode: vertical-lr;
                      transform: rotate(180deg);
                      text-align: center;
                      vertical-align: middle;"
              >
              <button class="button-completeTaskPickup" data-jobid="{{$job->id}}" id="button-completeTaskPickup-{{$job->id}}"><span class="bi bi-check"></span></button>
              </div>
            </div>
            @foreach ($job->tasks as $task)
              @isset($task->package)
              <div class="row package-row border" id="package-{{$task->id}}" style="background-color: {{$job->status->color_dropoff}};">
                <div  class="col-2" 
                    style="
                      writing-mode: vertical-lr;
                      transform: rotate(180deg);
                      text-align: center;
                      vertical-align: middle;"
                  >
                    DROP
                </div>
                <div class="col-10 package" >
                  <div class="row">
                    <div class="col-auto">
                      {{$task->package->dropoff_name}}
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-6">
                      {{ \Illuminate\Support\Carbon::parse($task->package->packagedropofftimebegin)->format('H:i') }}
                    </div>
                    <div class="col-6">
                      {{ \Illuminate\Support\Carbon::parse($task->package->packagedropofftimeend)->format('H:i') }}  
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      {{$task->package->pickupAddressShort()}}
                    </div>
                  </div>
                </div>
              </div>
              @endisset
            @endforeach
            @foreach ($job->tasks as $task)
              @isset($task->return)
              <div class="row package-row border" id="package-{{$task->id}}" style="background-color: {{$job->status->color_dropoff}};">
                <div  class="col-2" 
                    style="
                      writing-mode: vertical-lr;
                      transform: rotate(180deg);
                      text-align: center;
                      vertical-align: middle;"
                  >
                    RETURN
                </div>
                <div class="col-10 package" >
                  <div class="row">
                    <div class="col-auto">
                      {{$task->return->name}}
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-6">
                      {{ \Illuminate\Support\Carbon::parse($task->return->time_begin)->format('H:i') }}
                    </div>
                    <div class="col-6">
                      {{ \Illuminate\Support\Carbon::parse($task->return->time_end)->format('H:i') }}  
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      {{$task->return->addressShort()}}
                    </div>
                  </div>
                </div>
              </div>
              @endisset
            @endforeach
            <div class="row">
              <div class="col">
                <button class="button-split" data-jobid="{{$job->id}}" id="button-split-{{$job->id}}">Split</button>
                <button class="button-completeJob" data-jobid="{{$job->id}}" id="button-completeJob-{{$job->id}}">Complete</button>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
    @foreach ($users as $user)
    <div class="job-columenToGetDropEvent col-3 border border-dark rounded" id="job-column-usercolumn-{{$user->id}}">
      <div class="row border job-header" id="job-column-usercolumn_{{$user->id}}-header">
        <div class="col-auto">{{$user->name}}</div>
        <div class="col-auto">{{$user->workload($day)->bike->name}}</div>
        <div class="col"><button class="btn btn-primary button-copy-user-jobs" id="button-copy-user-jobs-{{$user->id}}" data-jobs="">Copy</button></div>
      </div>
      <div class="row job-dropableListArea">
        @foreach ($user->tasks as $task)
        <div class="col-12 border border-dark border-2 rounded draggable" style="background-color: {{$task->job->status->color_main}};" draggable="true" id="jobElement-{{$task->job->id}}">
          <div class="row job-header-div">
            <div class="col job-id-div">
              NJ{{$task->job->id}}
            </div>
            <div class="col job-status-div">
                {{$task->status->name}}
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
            <div class="col-2">
              <button class="btn btn-success button-completeTask" data-jobid="{{$task->id}}" id="button-completeTask-{{$task->id}}"><span class="bi bi-check"></span></button>
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
            <div class="col-2">
              <button class="btn btn-success button-completeTask" data-jobid="{{$task->id}}" id="button-completeTask-{{$task->id}}"><span class="bi bi-check"></span></button>
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
            <div class="col-2">
              <button class="btn btn-success button-completeTask" data-jobid="{{$task->id}}" id="button-completeTask-{{$task->id}}"><span class="bi bi-check"></span></button>
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
              RETURN
            </div>          
            <div class="col-8">
                <div class="row"><div class="col">{{$task->customTask->name}}</div></div>
                <div class="row"><div class="col">{{ \Illuminate\Support\Carbon::parse($task->customTask->time_begin)->format('H:i') }}</div><div class="col">{{ \Illuminate\Support\Carbon::parse($task->customTask->time_end)->format('H:i') }}</div></div>
                <div class="row"><div class="col">{{$task->customTask->addressShort()}}</div></div>
            </div>
            <div class="col-2">
              <button class="btn btn-success button-completeTask" data-jobid="{{$task->id}}" id="button-completeTask-{{$task->id}}"><span class="bi bi-check"></span></button>
            </div>
          </div>
          @endisset
        </div> 
        @endforeach
        <!-- @foreach ($user->jobsWithDate($day->date) as $job)
        <div class="col-12 border border-dark border-2 rounded draggable" style="background-color: {{$job->status->color_main}};" draggable="true" id="jobElement-{{$job->id}}">
            <div class="row job-header-div">
              <div class="col job-id-div">
                NJ{{$job->id}}
              </div>
              <div class="col job-status-div">
                {{$job->status->name}}
              </div>   
            </div>
            <div class="row pickup-row border" style="background-color: {{$job->status->color_pickup}};">
              <div  class="col-2" 
                    style="
                      writing-mode: vertical-lr;
                      transform: rotate(180deg);
                      text-align: center;
                      vertical-align: middle;"
              >PU</div>
              <div class="col-10">
                <div class="row"><div class="col">{{$job->pickupclientname}}</div></div>
                <div class="row"><div class="col">{{ \Illuminate\Support\Carbon::parse($job->pickup_time_begin)->format('H:i') }}</div><div class="col">{{ \Illuminate\Support\Carbon::parse($job->pickup_time_end)->format('H:i') }}</div></div>
                <div class="row"><div class="col">{{$job->pickupAddressShort()}}</div></div>
              </div>
            </div>
            @foreach ($job->packages as $package)
            <div class="row package-row border" id="package-{{$package->id}}" style="background-color: {{$job->status->color_dropoff}};">
              <div  class="col-2" 
                    style="
                      writing-mode: vertical-lr;
                      transform: rotate(180deg);
                      text-align: center;
                      vertical-align: middle;"
              >
              @if (strcasecmp($package->name, 'return') == 0)
                RETURN
              @else
                DROP
              @endif
              </div>
              <div class="col-10 package" >
                <div class="row">
                  <div class="col-auto">
                    {{$package->dropoff_name}}
                  </div>
                </div>
                <div class="row">
                  <div class="col-6">
                    {{ \Illuminate\Support\Carbon::parse($package->packagedropofftimebegin)->format('H:i') }}
                  </div>
                  <div class="col-6">
                    {{ \Illuminate\Support\Carbon::parse($package->packagedropofftimeend)->format('H:i') }}  
                  </div>
                </div>
                <div class="row">
                  <div class="col">
                    {{$package->pickupAddressShort()}}
                  </div>
                </div>
              </div>
              
            </div>
            @endforeach
            <div class="row">
              <div class="col">
                <button class="button-split" data-jobid="{{$job->id}}" id="button-split-{{$job->id}}">Split</button>
                <button class="button-completeJob" data-jobid="{{$job->id}}" id="button-completeJob-{{$job->id}}">Complete</button>
              </div>
            </div>
        </div>
        @endforeach -->
      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  var jobElements = document.querySelectorAll('.draggable');
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
      var jobElementsForCopy = column.querySelectorAll('[id^="jobElement-"]');
      jobElementsForCopy.forEach(function(jobElement){
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

  jobElements.forEach(function(element) {
    element.addEventListener('dragstart', function(event) {
      event.dataTransfer.setData("id", event.target.id);
      dragedElement = element;
    });
  });
  jobElements.forEach(function(element) {
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
      event.preventDefault();
      var dropedElementId = event.dataTransfer.getData("id");
      var targetList;
      if(event.target.closest('.job-header')){
        var column  = event.target.closest('.job-columenToGetDropEvent');
        targetList = column.querySelectorAll('.job-dropableListArea')[0];
      }else{     
        targetList = event.target.querySelectorAll('.job-dropableListArea')[0];
      }
      if(targetList){
        if(event.target.closest('.job-header')){
          targetList.insertBefore(document.getElementById(dropedElementId), targetList.firstChild);
        }else{
          targetList.appendChild(document.getElementById(dropedElementId));
        }
      }else{
        targetList  = event.target.closest('.job-dropableListArea');
        targetJob   = event.target.closest('.draggable');
        targetList.insertBefore(document.getElementById(dropedElementId), targetJob.nextSibling);
      }
      if(event.target.closest('.job-columenToGetDropEvent').id === 'job-column-unassigned'){
        dropedElement = document.getElementById(dropedElementId);
        jobId = dropedElementId.replace("jobElement-", ""); // IMPORTANT
        userId  = event.target.closest('.job-columenToGetDropEvent').id.replace("job-column-usercolumn-", "");
        statusId  = 10;
        console.log(userId+' '+jobId);
        updateJobCourierAndStatus(jobId,'none',statusId);
        const routeUrl = "{{ route('status.getStatusInfo', ['id' => ':id']) }}".replace(':id', 10);
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

      }else if(/^job-column-usercolumn-\d+$/.test(event.target.closest('.job-columenToGetDropEvent').id)){
        dropedElement = document.getElementById(dropedElementId);
        jobId = dropedElementId.replace("jobElement-", "");
        userId  = event.target.closest('.job-columenToGetDropEvent').id.replace("job-column-usercolumn-", "");
        statusId  = 13;
        console.log(userId+' '+jobId);
        updateJobCourierAndStatus(jobId,userId,statusId);
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

      }
      //assignJobInformationToCopyButtons();
      //updateJobCourierAndStatus(jobId,UserId,statusId);           
    });
  });
});
function updateJobElementColors(jobId){

}
function updateJobCourierAndStatus(jobId,userId,statusId){
  console.log(jobId);
  console.log(userId);
  console.log(statusId);
        // Prepare the data to send in the request body
        const data = {
            id: jobId,
            courrier_id: userId,
            status_id: statusId
        };
        console.log(data);
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
</script>
@endsection