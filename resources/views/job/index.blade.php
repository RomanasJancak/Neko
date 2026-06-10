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
    width: 100%;
    }
.input-container input {
    width: 100%;
    padding: 10px 10px 10px 1.5rem; 
    box-sizing: border-box;
    }

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
    <div class="d-flex justify-content-center mt-3" id="paginationLinks_top">
            {!! $jobs->links() !!}
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
                            <button id="button-sort-id" 
                            class="sort-btn {{ $sortField === 'id' ? 'active' : '' }}" 
                            data-sort-field="id" data-sort-order="{{ $sortField === 'id' ? $sortOrder : 'asc' }}">
                                <i id="button-sort-id-icon" data-default-class="fa-solid fa-up-down" class="fa-solid fa-up-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col input-container">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="search-id" class="form-control" placeholder="Search..." value="{{ request()->query('id', '') }}">
                        </div>
                    </div>                    
                </th>
                <th rowspan="2">
                    <div class="row">
                        <div class="col">
                            Status
                            <button id="button-sort-status" class="sort-btn {{ $sortField === 'status' ? 'active' : '' }}" data-sort-field="status" data-sort-order="{{ $sortField === 'status' ? $sortOrder : 'asc' }}">
                                <i id="button-sort-status-icon" data-default-class="fa-solid fa-up-down" class="fa-solid fa-up-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col input-container">
                            <select id="search-status" class="form-control" name="status[]" multiple="">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ collect(request()->query('status'))->contains($status->id) ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>                    
                </th>
                <th rowspan="2">
                    <div class="row">
                        <div class="col">
                            Client
                            <button id="button-sort-clientName" class="sort-btn {{ $sortField === 'clientName' ? 'active' : '' }}" data-sort-field="clientName" data-sort-order="{{ $sortField === 'clientName' ? $sortOrder : 'asc' }}">
                                <i id="button-sort-clientName-icon" data-default-class="fa-solid fa-up-down" class="fa-solid fa-up-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col input-container">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="search-clientName" class="form-control" placeholder="Search..." value="{{ request()->query('clientName', '') }}">
                        </div>
                    </div>
                </th>
                <th rowspan="2">
                    <div class="row">
                        <div class="col">
                            Date                    
                            <button id="button-sort-date" class="sort-btn {{ $sortField === 'date' ? 'active' : '' }}" data-sort-field="date" data-sort-order="{{ $sortField === 'date' ? $sortOrder : 'asc' }}">
                                <i id="button-sort-date-icon" data-default-class="fa-solid fa-up-down" class="fa-solid fa-up-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row" style="display: none;">
                        <div class="col input-container">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="search-date" class="form-control" placeholder="Search..." value="{{ request()->query('date', '') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col" id="reportrange" data-start="{{ request()->query('startDate', '') }}" data-end="{{ request()->query('endDate', '') }}">
                            <i class="fa fa-calendar"></i>&nbsp;
                                <input type="text" id="search-date-range" value="{{ request()->query('startDate', now()->startOfYear()->toDateString()) }} - {{ request()->query('endDate', now()->toDateString()) }}">
                            <i class="fa fa-caret-down"></i>
                        </div>
                    </div>
                </th>
                <th colspan="3">Tasks</th>
                <th rowspan="2">Actions</th>
                <th rowspan="2" style="text-align:center;width:100px;">
                    <span>Create Job</span>
                    <button type="button" class="btn btn-success btn-xs text-success create-btn job-create-btn" style="background: none; border: none;" id="createJobButton"> 
                        <i class="fa fa-plus-circle" aria-hidden="true" style="color: inherit;"></i>
                    </button>
                </th>
            </tr>
            <tr>
                <th>Pickup</th>
                <th>
                    <span class="">
                        <span class="p-2">Drops</span>
                    </span>
                    <span class="info-icon">
                        <i id="modalShowElement-dropOffSearchColumns" class="fa-solid fa-gear"></i>
                    </span>
                    <div class="row input-container">
                            <input type="text" id="search-package" class="col form-control" placeholder="Search..." value="{{ request()->query('package', '') }}">
                    </div>
                </th>
                <!-- <th>Custom</th> -->
                <th>Price</th>
            </tr>
        </thead>
        <tbody id="jobsTableBody">
            @foreach ($jobs as $job)
            @php($isJobLocked = $job->isLockedForUser(auth()->user()))
            <tr id="jobTableRow_{{$job->id}}">
                <td >
                    {{$job->id}}
                    @if($job->invoiceItem && $job->invoiceItem->invoice)
                        <a
                            href="{{ route('invoice.show', $job->invoiceItem->invoice->id) }}"
                            class="info-icon ms-1 text-danger text-decoration-none"
                            title="{{ $job->invoiceItem->invoice->invoice_number }}"
                            aria-label="Invoice {{ $job->invoiceItem->invoice->invoice_number }}"
                        >
                            <i class="fa-solid fa-biohazard"></i>
                            <span class="tooltip">{{ $job->invoiceItem->invoice->invoice_number }}</span>
                        </a>
                    @endif
                </td>
                <td>
                    {{ $job->status->name}}
                </td>
                <td class="no-padding">
                    <?php
                        $logoPath = "files/logos/{$job->clientToBill->id}.png";
                        if (file_exists(public_path($logoPath))) {
                            $logoUrl = asset($logoPath);
                        } else {
                            $logoUrl = asset("files/logos/0.png");
                        }
                    ?>
                    <img src='{{ $logoUrl }}' alt="Company Logo" style="max-width: 2rem;  height: auto;">
                    <span style="cursor : pointer" class="span-toClientShow" data-clientid="{{$job->clientToBill->id}}"> {{$job->clientToBill->name}}</span>  
                </td>
                <td>            
                    @displayDate($job->date)
                </td>
                <td>
                @foreach ($job->tasks as $task)                    
                    @if ($task->pickup)
                        <div @if(!$task->job->clientToBill->isSameAsPickupAdress($task->pickup->pickupAddressFull())) style="background-color: rgb(141, 153, 80);" @endif >
                            @if($task->job->clientToBill->isSameAsPickupAdress($task->pickup->pickupAddressFull()))
                                {{$task->job->clientToBill->shortenedNameWithoutterPostalCode().' '.$task->pickup->pickupclientpostalcode}}
                            @else
                                {{$task->job->clientToBill->shortenedNameWithoutterPostalCode().' '.$task->pickup->pickupclientpostalcode}}
                            @endif
                            <span class="info-icon">
                                <i class="bi bi-info-circle-fill"></i>
                                <span class="tooltip">{{$task->fullAddress()}}</span>
                            </span>
                        </div>
                    @endif                             
                @endforeach
                </td>
                <td>
                <?php $PackageCounter = 1;?>
                @foreach ($job->tasks as $task)
                        
                    @if ($task->package)
                    <div class="row">
                        <div class="col">
                            <div class="row justify-content-center">
                                <div class="col">
                                    <blockquote class="blockquote border">
                                        <h6>Package No [ {{$PackageCounter++}} ]</h6>
                                        <p class="mb-0" style="padding-bottom: 1rem;">{{$task->package->dropoff_name}}@if($job->hasReturn())<i class="bi bi-arrow-counterclockwise" style="color: #00DD00;"></i>@endif</p>
                                        <footer class="blockquote-footer"><cite title="Source Title"> {{$task->addressLine()}},{{$task->postalCode()}}</cite></footer>
                                    </blockquote>
                                </div>
                            </div>
                        </div>
                    </div>           
                    @endif                               
                @endforeach
                </td>
                <!-- <td></td> -->
                <td><span>&#163;</span>@if($job->fixed_price === 0){{ number_format($job->price()['totalPrice'] / 100, 2) }}@else {{ number_format($job->fixed_price / 100, 2) }}@endif<span>@if($job->fixed_price !== 0) <i class="fa-solid fa-lock" style="color:rgb(226, 34, 223);"></i>@endif</span></td>
                <td>
                    <button class="btn btn-success view-btn job-view-btn" data-jobid="{{ $job->id }}">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-primary edit-btn job-edit-btn" data-jobid="{{ $job->id }}" @if($isJobLocked) disabled title="Locked after invoice date" aria-disabled="true" @endif>
                        <i class="bi bi-pen"></i>
                    </button>
                    <button class="btn btn-danger delete-btn job-delete-btn" data-jobid="{{ $job->id }}">
                        <i class="bi bi-trash"></i>
                    </button>
                    <button class="btn btn-info job-copy-btn"   data-jobid="{{ $job->id }}"><i class="fa-solid fa-copy"></i></button>
                    <button class="btn btn-info job-sharelink-btn"   data-jobid="{{ $job->id }}"><i class="fa fa-share-alt" aria-hidden="true"></i></button>
                </td>   
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center mt-3" id='paginationLinks_bottom'>
        {!! $jobs->links() !!}
    </div>


</div>
<!-- Modal job begin -->
<div class="modal fade" data-bs-backdrop="static" id="jobModalWindow" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="row g-0">
                <div class="col-9 border rounded d-flex flex-column "> <!--Left side -->
                    <div class="modal-body">
                        @include('job.show')
                    </div>
                    <div class="modal-footer ">
                        <!-- <div class="row"> -->
                            <div class="col-12">
                                <div class="form-group d-flex justify-content-between">
                                    
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="jobModalWindowCloseButton">Close/Cancel</button>
                                </div>
                            </div>
                            <!-- <div class="col-auto"></div> -->
                        <!-- </div>                 -->
                    </div>
                </div>
                <!--Right side -->
                <div class="col-3 border rounded justify-content-left" id="priceColumnContainer"> 
                    <div class="row">
                        <div class="col-12">
                            <span style="font-size: larger; font-weight: bold;">Total price : </span><span style="font-size: larger; font-weight: bold;">&#163;</span><span id="total_Price_DisplayField" style="font-size: larger; font-weight: bold;">0.00</span>
                        </div>
                    </div>
                    <div class="row" id="priceColumnContainer_distanceRow">
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
                        <div class="col-12">
                            <span>Price from oversize : </span><span>&#163;</span><span id="addon_package_oversize_price_DisplayField">0.00</span>
                        </div>
                        <div class="col-12">
                            <span>Price from food : </span><span>&#163;</span><span id="addon_package_food_price_DisplayField">0.00</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" id='packages_price_base_DisplayField'>
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
                            <span>Sunday : </span><span>&#163;</span><span id="addon_time_sunday_price_DisplayField">0.00</span>
                        </div>
                        <div class="col-12">
                            <span>Bank holiday : </span><span>&#163;</span><span id="addon_time_bankholiday_price_DisplayField">0.00</span>
                        </div>
                        <div class="col-12">
                            <span>Same day order : </span><span>&#163;</span><span id="addon_time_samedayorder_price_DisplayField">0.00</span>
                        </div>
                        <div class="col-12">
                            <span>Same day return : </span><span>&#163;</span><span id="addon_time_samedayreturn_price_DisplayField">0.00</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12">
                                    <span>Pickup window : </span><span id="pickup_timing_value_DisplayField"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <span>Price from pickup : </span><span>&#163;</span><span id="pickup_timing_price_DisplayField">0.00</span>
                                </div>
                            </div>    
                        </div>
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12">
                                    <span>Dropoff window : </span><span id="dropoff_timing_value_DisplayField"></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="row">
                                    <div class="col-12">
                                        <span>Price from dropOff : </span><span>&#163;</span><span id="dropoff_timing_price_DisplayField">0.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="row">
                                    <div class="col-12">
                                        <span>"Magic number" : </span><span>&#163;</span><span contenteditable="true" id="price_magicNumber_DisplayField" style="min-width: 5ch; display: inline-block;">0.00</span>
                                        <span id="magic_number_actions" style="display: none;">
                                            <button type="button" id="confirmMagicNumber" class="btn btn-success btn-sm">
                                                <i class="bi bi-check"></i>
                                            </button>
                                            <button type="button" id="cancelMagicNumber" class="btn btn-danger btn-sm">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>            
            </div>
        </div>
    </div>
</div>
<!-- Modal job end -->

<!-- Modal task begin -->
<div class="modal fade" data-bs-backdrop="static" id="taskModalWindow" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                @include('task.show')
            </div>
            <div class="modal-footer">
                <div class="col-12">
                    <div class="form-group d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="taskModalWindowCloseButton">Close</button>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
<!-- Modal task end -->
<!-- Modal client view begin -->
<div class="modal fade" data-bs-backdrop="static" id="clientModalWindow" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                @include('client.partials.info-window')
            </div>
            <div class="modal-footer">
                <div class="col-12">
                    <div class="form-group d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="taskModalWindowCloseButton">Close</button>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
<!-- Modal client view end -->
<!-- Modal job copy window begin-->
<div class="modal fade" data-bs-backdrop="static" id="jobCopyModalWindow" tabindex="-1" aria-labelledby="jobCopyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jobCopyModalLabel">Copy Job</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to copy this job?</p>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="jobIdToCopy" id="jobIdToCopy" value="">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-secondary" id="CopyJobClipboard">Copy to clip</button>
                <button type="button" class="btn btn-primary" id="confirmCopyJob">Confirm</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal job copy window end-->
<!-- Modal dropOff search columns begin-->
<div class="modal fade" data-bs-backdrop="static" id="dropOffSearchColumnsModalWindow" tabindex="-1" aria-labelledby="dropOffSearchColumnsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dropOffSearchColumnsModalLabel">DropOff Search Columns</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="dropOffSearchColumnsForm">
                    @csrf
                    <div class="form-group">
                        <label for="dropOffSearchFields">Select fields to display:</label>
                        <select id="dropOffSearchFields" class="form-control" name="dropOffSearchFields[]" multiple>
                            @foreach($optionsForDropOffsSearch as $key => $option)
                                <option value="{{ $key }}" {{ in_array($key, $dropOffSearchFields) ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>      
        </div>
    </div>
</div>
<!-- Modal dropOff search columns end-->
<div class="modal fade" data-bs-backdrop="static" id="jobTemplateCreateModalWindow" tabindex="-1" aria-labelledby="jobTemplateCreateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jobTemplateCreateModalLabel">Create Job Template from job with id: <span id="jobTemplateCreateModalWindow_modalHeader_JobId"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Please enter the name for the template : <input id="jobTemplateCreateModalWindow_inputForTemplateName" type="text"></p>
                <button type="button" id="jobTemplateWindow_templateCreateButton" data-jobid="" class="btn btn-success" >Create</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="jontemplateWindowToRedirect" data-jobtemplateid="" data-bs-dismiss="modal">Close and redirect to editing</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close and stay here</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal job template create end-->
<!-- Modal Note begin -->
<div class="modal fade" id="jobNoteModalWindow" tabindex="-1" aria-labelledby="jobNoteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Header -->
      <div class="modal-header">
          <h5 class="modal-title" id="jobNoteModalLabel">Note History</h5>
            <div class="ms-auto">
              <button class="btn btn-sm btn-outline-primary" id="prevNoteBtn">Previous</button>
              <button class="btn btn-sm btn-outline-primary" id="nextNoteBtn">Next</button>
            </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Body -->
      <div class="modal-body">
        <div class="modal-body">
          <div id="noteContent"></div>
          <small class="text-muted d-block mt-2" id="noteMeta"></small>
        </div>
      </div>

      <!-- Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<!-- Modal Note end -->
@endsection
@push('scripts')
@vite('resources/js/job/index.js')
@endpush
@section('scripts')
<script>

</script>
@endsection