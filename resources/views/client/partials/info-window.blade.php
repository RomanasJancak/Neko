<form class="row" id="clientForm" action="" method="POST">
    @csrf
    <input type="hidden" name="clientid" id="clientid" value="{{ $client->id ?? '' }}">
    <div class="col-lg-4 order-lg-1">
        <div class="mb-0">
            <label for="nameField" class="form-label">Company name:</label>
            <input type="text" name="clientname" id="nameField" class="form-control" value="">
        </div>
        <div class="mb-3">
            <label for="shortenedNameField" class="form-label">Short name:</label>
            <input type="text" name="shortenedName" id="shortenedNameField" class="form-control" value="">
        </div>
        <div class="mb-3">
            <label for="reg-adress-section-adress-addressline-field" class="form-label">Registration address:</label>
            <input type="text" name="reg-addr-address_line" id="reg-adress-section-adress-addressline-field" class="form-control mb-2" value="" placeholder="Address line">
            <input type="text" name="reg-addr-postal_code" id="reg-adress-section-adress-postalcode-field" class="form-control mb-2" value="" placeholder="Postal code">
            <input type="hidden" name="reg-addr-city" id="reg-adress-section-adress-city-field" class="form-control mb-2" value="London" placeholder="City">
            <input type="hidden" name="reg-addr-country" id="reg-adress-section-adress-country-field" class="form-control mb-2" value="United Kingdom" placeholder="Country">
        </div>
        <div class="mb-3">
            <label for="phoneNumberField" class="form-label"><i class="fa-solid fa-phone"></i>:</label>
            <input type="text" name="phone" id="phoneNumberField" class="form-control" value="">
        </div>
        <div class="mb-3">
            <button type="button" class="btn btn-success btn-sm" id="button-add-address">
                <i class="fa fa-plus-circle"></i> Add address
            </button>
            <button type="button" class="btn btn-info btn-sm" id="button-view-packages">
                View packages
            </button>
            <button type="button" class="btn btn-info btn-sm" id="button-view-addons">
                View AddOns
            </button>
        </div>
        <div class="mb-3">
            <button type="button" id="submitform" data-option="create" class="btn btn-primary">Apply</button>
        </div>
        <div class="mb-3" id="container-emails"></div>
    </div>
    <div class="col-lg-8 order-lg-2 mb-3" id="container-addresses"></div>
</form>
{{-- To be moved to separate  view packages partial maybe--}}
<!-- Packages view window-->
<div class="modal" id="modalWindow-packages" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="ModalLabel">Packages</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                 @include('packageType.partials.info-window-client')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- End of Packages view window-->
<div class="modal" id="client-modalWindow-adddress" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="ModalLabel">Address</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                 @include('address.partials.info-window')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
{{--  --}}
@push('scripts')
    @vite('resources/js/client/show.init.js')
@endpush