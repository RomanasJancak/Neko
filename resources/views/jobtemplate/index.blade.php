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

  <!-- Top Pagination -->
  <div class="d-flex justify-content-center mt-3 links-pagination"></div>

  <!-- Filter + Sort Header -->
  <div class="row g-3 mb-3">
    <!-- ID -->
    <div class="col-12 col-md-4">
      <label class="form-label d-flex align-items-center justify-content-between">
        <span>Id</span>
        <button id="button-sort-id" class="btn btn-sm sort-btn" data-sort-field="id" data-sort-order="asc">
          <i id="button-sort-id-icon" class="fa-solid fa-up-down" data-default-class="fa-solid fa-up-down"></i>
        </button>
      </label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" id="search-id" class="form-control" placeholder="Search...">
      </div>
    </div>

    <!-- Name -->
    <div class="col-12 col-md-4">
      <label class="form-label d-flex align-items-center justify-content-between">
        <span>Name</span>
        <button id="button-sort-name" class="btn btn-sm sort-btn" data-sort-field="name" data-sort-order="asc">
          <i id="button-sort-name-icon" class="fa-solid fa-up-down" data-default-class="fa-solid fa-up-down"></i>
        </button>
      </label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" id="search-name" class="form-control" placeholder="Search...">
      </div>
    </div>

    <!-- Client -->
    <div class="col-12 col-md-4">
      <label class="form-label d-flex align-items-center justify-content-between">
        <span>Client</span>
        <button id="button-sort-clientName" class="btn btn-sm sort-btn" data-sort-field="clientName" data-sort-order="asc">
          <i id="button-sort-clientName-icon" class="fa-solid fa-up-down" data-default-class="fa-solid fa-up-down"></i>
        </button>
      </label>
      <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" id="search-clientName" class="form-control" placeholder="Search...">
      </div>
    </div>
  </div>

  <!-- Data Grid -->
  <div class="row g-3" id="itemListGrid">
    <!-- Example Card: Repeat per item -->
    <!--
    <div class="col-12 col-md-6 col-lg-4">
      <div class="card h-100 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Job #123</h5>
          <p><strong>Name:</strong> Example Job</p>
          <p><strong>Client:</strong> Client A</p>
          <p><strong>Tasks:</strong> Pickup: 1, Drop: 2, Return: 0</p>
          <p><strong>Price:</strong> £120.00</p>
        </div>
        <div class="card-footer d-flex justify-content-between">
          <button class="btn btn-sm btn-outline-primary">Edit</button>
          <button class="btn btn-sm btn-outline-danger">Delete</button>
        </div>
      </div>
    </div>
    -->
  </div>

  <!-- Create Template Button -->
  <div class="text-end my-3">
    <button type="button" class="btn btn-success create-btn">
      <i class="fa fa-plus-circle me-1"></i>Create Template
    </button>
  </div>

  <!-- Bottom Pagination -->
  <div class="d-flex justify-content-center mt-3 links-pagination"></div>
</div>


<div class="modal fade" id="jobModalWindow" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div class="container">
          <div class="row">
            <div class="col">
              <h5>Job Details</h5>
              <p><strong>Name:</strong> <span id="jobName"></span></p>
              <p><strong>Client:</strong> <span id="jobClient"></span></p>
              <p><strong>Pickup:</strong> <span id="jobPickup"></span></p>
              <div id="jobDrops">
                <strong>Drops:</strong>
                <div class="text-muted">N/A</div>
              </div>
              <p><strong>Return:</strong> <span id="jobReturn"></span></p>
              <p><strong>Price:</strong> <span id="jobPrice"></span></p>
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