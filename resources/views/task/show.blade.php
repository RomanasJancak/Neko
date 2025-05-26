<form id="taskForm" action="" method="POST">
    @csrf
    <div class="row justify-content-md-center">
        <div class="col-10">
            <h5 id="taskWIndow_name">Task creation window</h5>
        </div>
        <div class="col-2" id="divForTaskFormCrateCollection"style="display: none;">
            <div class="form-check form-switch">

                <input class="form-check-input" type="checkbox" id="crateCollection" name="crateCollection">
                
                
                <label class="form-check-label" for="crateCollection">Crate Collection</label>
                
            </div>
        </div>
    </div>
    <div class="row justify-content-md-center">
        <div class="col-2">
            <div class="row" style="display: none;">
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
        <div class="col-auto d-none">
            <label for="taskCountryField">Country</label>
            <input class="form-control" type="text" name="id" id="taskCountryField" value="">
        </div>
        <div class="col-auto d-none">
            <label for="taskCityField">City</label>
            <input class="form-control" type="text" name="id" id="taskCityField" value="">
        </div>
        <div class="col-auto">
            <label for="taskPostalCodeField">Postal code</label>
            <input class="form-control" type="text" name="id" id="taskPostalCodeField" value="">
        </div>
        <div class="col-auto">
            <label for="taskAddressLineField">Address line</label>
            <input class="form-control" type="text" name="id" id="taskAddressLineField" value="">
        </div>
    </div>
    <div class="row justify-content-md-center">
        <div class="col">
            <h3>Time window</h3>
        </div>
    </div>
    <div class="row justify-content-md-center">
        <div class="col-3 form-group" style="visibility: hidden;" id="container_taskTimeDate">
            <label for="taskTimeDate">Date</label>
                <input type="date" id="taskTimeDate" name="timeDate" class="form-control">
        </div>
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
    <div class="row justify-content-md-center">
        <div class="col" id="return-info">
            <input type="checkbox" name="returnTask_isFlexible" id="returnTask_isFlexible" checked>
            <label for="returnTask_isFlexible">Flexible Return</label>
        </div>          
    </div>
    <div class="row">
        <div class="col">
            <label for="taskNoteField">Note</label>
            <input class="form-control" type="text" name="taskNote" id="taskNoteField" value="">
        </div>
    </div>
</form>
<button type="button" id="submitTaskform" data-option="create" class="btn btn-primary">Apply</button>
@push('scripts')
@vite('resources/js/task/show.js')
@endpush