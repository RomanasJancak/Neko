@extends('layouts.app')
@section('style')
<style>
.container-content{
    /* border-style: double; */
}
.no-padding {
    padding: 0 !important;
}
.info-icon {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

.info-icon .tooltip {
            visibility: hidden;
            width: 200px;
            background-color: #f9f9f9;
            color: #333;
            text-align: left;
            border-radius: 5px;
            padding: 10px;
            position: absolute;
            z-index: 1;
            bottom: 125%; /* Position the tooltip above the icon */
            left: 50%;
            margin-left: -100px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transition: opacity 0.3s;
        }

.info-icon:hover .tooltip {
            visibility: visible;
            opacity: 1;
        }

.info-icon .tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #f9f9f9 transparent transparent transparent;
        }

.info-content {
            display: none;
            margin-top: 10px;
        }

.info-content.active {
            display: block;
        }
.input-container {
    position: relative;
    width: 100%;}
.input-container input {
    width: 100%;
    padding: 10px 10px 10px 1.5rem; 
    box-sizing: border-box;}

.input-container .fa-magnifying-glass {
    position: absolute;
    top: 50%;
    left: 1rem;
    transform: translateY(-50%);
    color: #aaa; 
    }

</style>
@endsection
@section('content')
<div class="container container-content">
    <div class="d-flex justify-content-center mt-3">
            {!! $jobTemplates->links() !!}
    </div>
    <form method="POST" action="{{ route('job.createBackup') }}">
        @csrf
        <button type="submit" class="btn btn-primary">Create Backup</button>
    </form>
    <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th rowspan="2">
                    <div class="row">
                        <div class="col">
                            Id
                            <button id="button-sort-id" class="sort-btn" data-sort-field="id" data-sort-order="asc">
                                <i id="button-sort-id-icon" data-default-class="fa-solid fa-up-down" class="fa-solid fa-up-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col input-container">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="search-id" class="form-control" placeholder="Search...">
                        </div>
                    </div>                    
                </th>
                <th rowspan="2">
                    <div class="row">
                        <div class="col">
                            Client
                            <button id="button-sort-clientName" class="sort-btn" data-sort-field="clientName" data-sort-order="asc">
                                <i id="button-sort-clientName-icon" data-default-class="fa-solid fa-up-down" class="fa-solid fa-up-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col input-container">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="search-clientName" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                </th>
                <th rowspan="2">
                    <div class="row">
                        <div class="col">
                            Date                    
                            <button id="button-sort-date" class="sort-btn" data-sort-field="date" data-sort-order="asc">
                                <i id="button-sort-date-icon" data-default-class="fa-solid fa-up-down" class="fa-solid fa-up-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col input-container">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="search-date" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                </th>
                <th colspan="4">Tasks</th>
                <th rowspan="2">Actions</th>
                <th rowspan="2" style="text-align:center;width:100px;">
                    <span>Create Job</span>
                    <button type="button" class="btn btn-success btn-xs text-success create-btn" style="background: none; border: none;">
                        <i class="fa fa-plus-circle" aria-hidden="true" style="color: inherit;"></i>
                    </button>
                </th>
            </tr>
            <tr>
                <th>Pickup</th>
                <th>Drops</th>
                <th>Custom</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody id="jobsTableBody">
            @foreach ($jobTemplates as $jobTemplate)
            <tr id="jobTableRow_{{$jobTemplate->id}}">
                <td >
                    {{$jobTemplate->id}}
                </td>
                <td class="no-padding">
                    <?php
                        $logoPath = "files/logos/{$jobTemplate->clientToBill->id}.png";
                        if (file_exists(public_path($logoPath))) {
                            $logoUrl = asset($logoPath);
                        } else {
                            $logoUrl = asset("files/logos/0.png");
                        }
                    ?>
                    <img src='{{ $logoUrl }}' alt="Company Logo" style="max-width: 2rem;  height: auto;">
                    <span> {{$jobTemplate->clientToBill->name}}</span>  
                </td>
                <td>            
                    {{$jobTemplate->date}}
                </td>
                <td>
                    {{$jobTemplate->tasks()->pickup->pickupclientpostalcode}}
                </td>
                <td>
                    @foreach ($jobTemplate->tasks()->dropOffs as $dropOff)
                    <div class="row">
                        <div class="col">
                            <div class="row justify-content-center">
                                <div class="col">
                                    <blockquote class="blockquote border">
                                        <p class="mb-0" style="padding-bottom: 1rem;">{{$dropOff->package->dropoff_name}}</p>
                                        <footer class="blockquote-footer"><cite title="Source Title"> {{$dropOff->package->dropoff_adress_line}},{{$dropOff->package->dropoff_postal_code}}</cite></footer>
                                    </blockquote>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </td>
                <td></td>
                <td><span>&#163;</span>@if($jobTemplate->fixedPrice === 0){{ number_format($jobTemplate->price / 100, 2) }}@else {{ number_format($jobTemplate->fixedPrice / 100, 2) }}@endif<span>@if($jobTemplate->fixedPrice !== 0) <i class="fa-solid fa-lock" style="color:rgb(226, 34, 223);"></i>@endif</span></td>
                <td>
                    <button class="btn btn-success view-btn" data-jobid="{{ $jobTemplate->id }}">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-primary edit-btn" data-jobid="{{ $jobTemplate->id }}">
                        <i class="bi bi-pen"></i>
                    </button>
                    <button class="btn btn-danger delete-btn" data-jobid="{{ $jobTemplate->id }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>   
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center mt-3" id='paginationLinks'>
        {!! $jobTemplates->links() !!}
    </div>
<!-- Modal job begin -->

</div>
<div class="modal fade" id="jobModalWindow" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="row g-0">
                <div class="col-9 border rounded d-flex flex-column "> <!--Left side -->
                    <div class="modal-body">
                        <form id="jobForm" action="" method="POST">
                            @csrf
                            <div class="row justify-content-md-center">
                                <div class="col-2">
                                    <div class="row">
                                        <input type="hidden" name="jobid" id="jobid" value="">
                                        <label for="idField">Id</label>
                                        <input class="form-control" type="text" name="id" id="idField" value="">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row">
                                        <label for="statusIdField">Status</label>
                                        <select id="statusIdField" name="statusId" class="form-control">
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                        @endforeach 
                                    </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row">
                                        <label for="clientSearchField">Client</label>
                                        <input type="text" id="clientSearchField" name="clientName" class="form-control" placeholder="Search for clients">
                                        <input type="hidden" name="clientId" id="clientIdField" value="">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row">
                                        <label for="jobDateField">Date</label>
                                        <input type="date" id="jobDateField" name="jobDate" class="form-control" placeholder="Search for clients">
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-md-center">
                                Tasks
                            </div>
                            <div class="row justify-content-md-center border rounded border-info" >
                                <div class="col" id="container-tasks">
                                </div>                        
                            </div>
                            <div class="row justify-content-md-center">
                                <div class="col">
                                    <button type="button" id="createNewTask" data-option="create" class="btn btn-primary ">Create new Task</button>
                                    <button type="button" id="createNewPickup"data-type="specifictask" data-option="pickup" class="btn btn-primary">+Pickup</button>
                                    <button type="button" id="createNewReturn" data-type="specifictask" data-option="dropOff" class="btn btn-primary">+DropOff</button>
                                    <button type="button" id="createNewDropOff" data-type="specifictask" data-option="return" class="btn btn-primary">+Return</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer ">
                        <!-- <div class="row"> -->
                            <div class="col-12">
                                <div class="form-group d-flex justify-content-between">
                                    <button type="button" id="submitform" data-option="create" class="btn btn-success">Confirm</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="jobModalWindowCloseButton">Cancel</button>
                                </div>
                            </div>
                            <!-- <div class="col-auto"></div> -->
                        <!-- </div>                 -->
                    </div>
                </div>
                <div class="col-3 border rounded justify-content-left"> <!--Right side -->
                    <div class="row">
                        <div class="col-12">
                            <span>Total price : </span><span>&#163;</span><span id="total_Price_DisplayField">0.00</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <span>Total distance : </span><span id="total_distance_DisplayField">0.00</span><span> miles</span>
                        </div>
                        <div class="col-12">
                            <span>Price from distance : </span><span>&#163;</span><span id="total_distance_price_DisplayField">0.00</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <span>Total weight : </span><span id="total_weight_DisplayField">0.00</span><span>kg</span>
                        </div>
                        <div class="col-12">
                            <span>Price from weight : </span><span>&#163;</span><span id="total_weight_price_DisplayField">0.00</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <span>Outside Zone Price : </span><span>&#163;</span><span id="total_outsideZone_price_DisplayField">0.00</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <span>Total timing : </span><span>&#163;</span><span id="total_timing_price_DisplayField">0.00</span>
                        </div>
                        <div class="col-12">
                            <span>Price from pickup : </span><span>&#163;</span><span id="pickup_timing_price_DisplayField">0.00</span>
                        </div>
                        <div class="col-12">
                            <span>Price from dropOff : </span><span>&#163;</span><span id="dropoff_timing_price_DisplayField">0.00</span>
                        </div>
                    </div>
                </div>            
            </div>
        </div>
    </div>
</div>
<!-- Modal job end -->
<!-- Modal task begin -->
<div class="modal fade" id="taskModalWindow" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <form id="taskForm" action="" method="POST">
                    @csrf
                    <div class="row justify-content-md-center">
                        <div class="col">
                            <h5 id="taskWIndow_name">Task creation window</h5>
                        </div>
                    </div>
                    <div class="row justify-content-md-center">
                        <div class="col-2">
                            <div class="row">
                                <input type="hidden" name="taskid" id="taskid" value="">
                                <label for="taskIdField">Id</label>
                                <input class="form-control" type="text" name="id" id="taskIdField" value="">
                            </div>
                        </div>
                        <div class="col">
                            <div class="row">
                                <label for="statusIdField">Status</label>
                                <select id="taskStatusIdField" name="statusId" class="form-control">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach 
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <label for="taskTypeField">Type</label>
                            <!-- <input class="form-control" type="text" name="taskTypename" id="taskTypeField" value=""> -->
                            <select class="form-control" name="taskTypename" id="taskTypeField">
                                <option value="pickup">Pickup</option>
                                <option value="dropOff">DropOff</option>
                                <option value="return">Return</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                    </div>
                    <div class="row justify-content-md-center">
                        <div class="col-auto">
                            <h3>Address</h3>
                        </div>
                    </div>
                    <div class="row justify-content-md-center">
                        <div class="col-auto">
                            <label for="taskClientNameField">Name</label>
                            <input class="form-control" type="text" name="id" id="taskClientNameField" value="">
                            <input type="hidden" name="clientId" id="task_clientIdField" value="">
                        </div>
                        <div class="col-auto">
                            <label for="taskCountryField">Country</label>
                            <input class="form-control" type="text" name="id" id="taskCountryField" value="">
                        </div>
                        <div class="col-auto">
                            <label for="taskCityField">City</label>
                            <input class="form-control" type="text" name="id" id="taskCityField" value="">
                        </div>
                        <div class="col-auto">
                            <label for="taskPostalCodeField">Postal code</label>
                            <input class="form-control" type="text" name="id" id="taskPostalCodeField" value="">
                        </div>
                        <div class="col-auto">
                            <label for="taskAddressLineField">Adsress line</label>
                            <input class="form-control" type="text" name="id" id="taskAddressLineField" value="">
                        </div>
                    </div>
                    <div class="row justify-content-md-center">
                        <div class="col">
                            <h3>Time winow</h3>
                        </div>
                    </div>
                    <div class="row justify-content-md-center">
                        <div class="col-3 form-group">
                            <label for="taskTimeBegin">Begin</label>
                                <input type="time" id="taskTimeBegin" name="timeBegin" class="form-control">
                        </div>
                        <div class="col-3 form-group">
                            <label for="taskTimeEnd">End</label>
                                <input type="time" id="taskTimeEnd" name="timeEnd" class="form-control">
                        </div>
                    </div>
                    <div class="row justify-content-md-center">
                        <div class="col" id="package-info">
                        </div>          
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="col-12">
                    <div class="form-group d-flex justify-content-between">
                        <button type="button" id="submitTaskform" data-option="create" class="btn btn-primary">Apply</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="taskModalWindowCloseButton">Close</button>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
<!-- Modal task end -->
@endsection
@push('scripts')
@vite('resources/js/jobtemplate/index.js')
@endpush
@section('scripts')
<script>

</script>
@endsection