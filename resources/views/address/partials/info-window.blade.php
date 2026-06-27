<form class="row" id="addressForm" action="" method="POST">
    @csrf
    <input type="hidden" name="id" id="addressid" value="{{ $address->id ?? '' }}">
    <div class="col-lg-4 order-lg-1">
        <div class="mb-0">
            <label for="address-nameField" class="form-label">Name:</label>
            <input type="text" name="name" id="address-nameField" class="form-control" value="">
        </div>
        <div class="mb-3">
            <label for="address-addressline_1-field" class="form-label">Address:</label>
            <input type="text" name="address_line_1" id="address-addressline_1-field" class="form-control mb-2" value="" placeholder="Address line">
            <input type="text" name="address_line_2" id="address-addressline_2-field" class="form-control mb-2" value="" placeholder="Address line 2">
            <input type="text" name="postal_code" id="address-postalcode-field" class="form-control mb-2" value="" placeholder="Postal code">
            <input type="hidden" name="city" id="address-city-field" class="form-control mb-2" value="" placeholder="City">
            <input type="hidden" name="country" id="address-country-field" class="form-control mb-2" value="" placeholder="Country">
        </div>
        <div class="mb-3">
            <button type="button" id="address-submitform" data-option="create" class="btn btn-primary">Apply</button>
        </div>
    </div>
</form>