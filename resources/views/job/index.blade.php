@extends('layouts.app')
@section('style')
<style>
.container-content{
    /* border-style: double; */
}
.no-padding {
    padding: 0 !important;
}
</style>
@endsection
@section('content')
<div class="container container-content">
<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th rowspan="2">Id</th>
			<th rowspan="2">Client</th>
			<th rowspan="2">Date</th>
			<th colspan="4">Tasks</th>
			<th rowspan="2">Price</th>
			<th rowspan="2" style="text-align:center;width:100px;">Create Job <button type="button" data-func="dt-add" class="btn btn-success btn-xs dt-add">
					<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
				</button></th>
		</tr>
        <tr>
            <th>Pickup</th>
            <th>Drops</th>
            <th>Return</th>
            <th>Custom</th>
        </tr>
	</thead>
	<tbody>
        @foreach ($jobs as $job)
        <tr>
            <td>
                {{$job->id}}
            </td>
            <td class="no-padding">
                <img src='{{ asset("files/logos/{$job->clientToBill->id}.png")}}' alt="Company Logo" style="max-width: 2rem;  height: auto;">
                <span> {{$job->clientToBill->name}}</span>  
            </td>
            <td>            
                {{date('d-m-Y',strtotime($job->pickup_time_begin))}}
            </td>
            <td>
            @foreach ($job->tasks as $task)
            
                @if ($task->pickup)
                    {{$task->pickup->pickupclientname}}
                @endif         
            @endforeach
            </td>
            <td>
            @foreach ($job->tasks as $task)
                @if ($task->package)
                <div class="row"><div class="col">{{$task->package->id}}</div></div>           
                @endif                               
            @endforeach
            </td>
            <td>                       
            @foreach ($job->tasks as $task)
                @isset($task->return)
                <div class="row">
                        <div class="col">{{$task->return->id}}</div>
                </div>   
                @endisset
            @endforeach
            </td>   
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Row information</h4>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
</div>
@endsection
@section('scripts')
<script>
</script>
@endsection