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
        <div class="col-3">
            <div class="row">
                <label for="courierIdField">Courier</label>
                <select id="courierIdField" name="courierId" class="form-control" >
                    <option value="0">none</option>
                    @foreach($couriers as $courier)
                    <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                    @endforeach                                                                              
                </select>
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
            <button type="button" id="createNewTask" data-option="create" class="btn btn-primary d-none">Create new Task</button>
            <button type="button" id="createNewPickup"data-type="specifictask" data-option="pickup" class="btn btn-primary">+Pickup</button>
            <button type="button" id="createNewDropOff" data-type="specifictask" data-option="return" class="btn btn-primary">+DropOff</button>
            <button type="button" id="createNewReturn" data-type="specifictask" data-option="dropOff" class="btn btn-primary">+Return</button>                                    
        </div>
    </div>
    <div class="row">
        <div class="col">
            <label for="jobNoteField">Note</label>
            <input class="form-control" type="text" name="jobNote" id="jobNoteField" value="">
        </div>
    </div>
</form>
<button type="button" id="submitform" data-option="create" class="btn btn-success">Confirm</button>