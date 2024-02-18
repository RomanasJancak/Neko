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
        @foreach ($user->jobsWithDate($day->date) as $job)
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
  var jobElements = document.querySelectorAll('.draggable');
  var jobdropableListAreas = document.querySelectorAll('.job-dropableListArea');
  var jobdropableListAreas2 = document.querySelectorAll('.job-columenToGetDropEvent');
  var splitButtons = document.querySelectorAll('.button-split');
  var copyButtons = document.querySelectorAll('.button-copy-user-jobs'); 
  var dragedElement;
  const routeUrl = "{{ route('job.getJobInfo', ['id' => ':id']) }}".replace(':id', '1');
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
      var data = event.dataTransfer.getData("id");
      var targetList;
      if(event.target.closest('.job-header')){
        var column  = event.target.closest('.job-columenToGetDropEvent');
        targetList = column.querySelectorAll('.job-dropableListArea')[0];
      }else{     
        targetList = event.target.querySelectorAll('.job-dropableListArea')[0];
      }
      if(targetList){
        if(event.target.closest('.job-header')){
          targetList.insertBefore(document.getElementById(data), targetList.firstChild);
        }else{
          targetList.appendChild(document.getElementById(data));
        }
      }else{
        targetList  = event.target.closest('.job-dropableListArea');
        targetJob   = event.target.closest('.draggable');
        targetList.insertBefore(document.getElementById(data), targetJob.nextSibling);
      }
      assignJobInformationToCopyButtons();           
    });
  });
});
function updateJobCourierAndStatus(job){

}   
</script>
@endsection