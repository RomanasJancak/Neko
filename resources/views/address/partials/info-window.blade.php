<form class="row" id="addressForm" action="" method="POST">
    @csrf
    <input type="hidden" name="addressid" id="addressid" value="{{ $address->id ?? '' }}">
    <div class="col-lg-4 order-lg-1">
        <div class="mb-0">
            <label for="nameField" class="form-label">Name:</label>
            <input type="text" name="clientname" id="nameField" class="form-control" value="">
        </div>
        <div class="mb-3">
            <button type="button" id="submitform" data-option="create" class="btn btn-primary">Apply</button>
        </div>
    </div>
</form>