export const clientInforReadOnlyState = {readOnly : false};
export function fillClientViewForm(clientId){
  var clientInfoUrl = window.ROUTES.WEB.CLIENT.GETINFO.replace(':id', clientId);
  fetch(clientInfoUrl)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        document.getElementById('clientid').value = clientId;
        document.getElementById('nameField').value = data.name;
        document.getElementById('shortenedNameField').value = data.nickName;

        document.getElementById('reg-adress-section-adress-country-field').value = data.country;
        document.getElementById('reg-adress-section-adress-city-field').value = data.city;
        document.getElementById('reg-adress-section-adress-postalcode-field').value = data.postal_code;
        document.getElementById('reg-adress-section-adress-addressline-field').value = data.address_line;

        document.getElementById('phoneNumberField').value = data.phone;
        populateWithAddresses(data.addresses);
        populateWithEmails(data.emails);
      } else {
        console.error('Error fetching client info:', data.message);
      }
    })
    .catch(error => console.error('Fetch error:', error));
  document.getElementById('nameField').readOnly = clientInforReadOnlyState.readOnly;
  document.getElementById('reg-adress-section-adress-country-field').readOnly = clientInforReadOnlyState.readOnly;
  document.getElementById('reg-adress-section-adress-city-field').readOnly = clientInforReadOnlyState.readOnly;
  document.getElementById('reg-adress-section-adress-postalcode-field').readOnly = clientInforReadOnlyState.readOnly;
  document.getElementById('reg-adress-section-adress-addressline-field').readOnly = clientInforReadOnlyState.readOnly;
  document.getElementById('phoneNumberField').readOnly = clientInforReadOnlyState.readOnly;
}
export function cleanClientForm(){
  document.getElementById('clientid').value = '';
  document.getElementById('nameField').value = '';
  document.getElementById('shortenedNameField').value = '';
  document.getElementById('reg-adress-section-adress-country-field').value = '';
  document.getElementById('reg-adress-section-adress-city-field').value = '';
  document.getElementById('reg-adress-section-adress-postalcode-field').value = '';
  document.getElementById('reg-adress-section-adress-addressline-field').value = '';
  document.getElementById('phoneNumberField').value = '';
  const container = document.getElementById('container-addresses');
  container.innerHTML = '';
}
function populateWithEmails(emails){
  console.log("THIS 1", emails);
    const container_main = document.getElementById('container-emails');
    const container = document.createElement('div');
    container.className = 'row';

    // Add label "Emails" to the main div
    const label = document.createElement('label');
    label.textContent = 'Emails';
    label.style.fontWeight = 'bold';
    container_main.innerHTML = '';
    container_main.appendChild(label);
    container_main.appendChild(container);

    emails.forEach(email => {
      const emailRow = `
        <div class="row">        
            <div class="col email-input-field" style="display: none;"><input type="hidden" name="email_id[]" class="form-control" value="${email.id}"></div>
            <div class="col email-input-field"><input style="font-size: 0.8em;" type="text" name="email[]" class="form-control" value="${email.email}" placeholder="Email" ></div>
            <div class="col email-input-field"><input style="font-size: 0.8em;" type="text" name="email_type[]" class="form-control" value="${email.type}" placeholder="Type (e.g. work, personal)" ></div>
            <div class="col email-input-field"><button type="button" class="btn btn-info btn-xs text-info" style="background: none; border: none;" id='button-edit-email' idofemail="${email.id}" onclick="editEmail(${email.id})">
                <i class="fa-solid fa-pencil" aria-hidden="true" style="color: inherit;"></i>
            </div>
            <div class="col email-input-field"><button type="button" class="btn btn-danger btn-xs text-danger" style="background: none; border: none;" id='button-remove-email' idofemail="${email.id}" onclick="deleteEmail(${email.id})">
                <i class="fa fa-minus-circle" aria-hidden="true" style="color: inherit;"></i>
            </div>
      `;
      container.insertAdjacentHTML('beforeend', emailRow);
    });

    // Add "Add Email" button at the bottom
    const addButton = document.createElement('button');
    addButton.type = 'button';
    addButton.className = 'btn btn-primary';
    addButton.textContent = 'Add Email';
    addButton.onclick = () => {
        const newEmailRow = `
            <div class="row">        
                <div class="col email-input-field" style="display: none;"><input type="hidden" name="email_id[]" class="form-control" value=""></div>
                <div class="col email-input-field"><input style="font-size: 0.8em;" type="text" name="email[]" class="form-control" value="" placeholder="Email" ></div>
                <div class="col email-input-field"><input style="font-size: 0.8em;" type="text" name="email_type[]" class="form-control" value="" placeholder="Type (e.g. work, personal)" ></div>
                <div class="col email-input-field"><button type="button" class="btn btn-danger btn-xs text-danger" style="background: none; border: none;" onclick="this.parentElement.parentElement.remove()">
                    <i class="fa fa-minus-circle" aria-hidden="true" style="color: inherit;"></i>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newEmailRow);
    };
    container_main.appendChild(addButton);
}
function populateWithAddresses(addresses){
        const container_main = document.getElementById('container-addresses');
        const container = document.createElement('div');
        container.className = 'row';
        container_main.innerHTML = '';
        container_main.appendChild(container);
        addresses.forEach(address => {
          const addressRow = `
            <div class="row">        
                <div class="col address-input-field" style="display: none;"><input type="hidden" name="address_id[]" class="form-control" value="${address.id}"></div>
                <div class="col address-input-field"><input style="font-size: 0.8em;" type="text" name="name[]" class="form-control" value="${address.name}" placeholder="Name" ></div>
                
                <div class="col address-input-field"><input type="text" name="address_line_1[]" class="form-control" value="${address.address_line_1}" placeholder="Address line 1"></div>
                <div class="col address-input-field"><input type="text" name="address_line_2[]" class="form-control" value="${address.address_line_2}" placeholder="Address line 1"></div>
                <div class="col address-input-field"><input type="text" name="postal_code[]" class="form-control" value="${address.postal_code}" placeholder="Postal code"></div>
                <div class="col address-input-field"><input type="text" name="city[]" class="form-control" value="${address.city}" placeholder="City"></div>
                <div class="col address-input-field"><input type="text" name="country[]" class="form-control" value="${address.country}" placeholder="Country"></div>
                <div class="col address-input-field"><button type="button" class="btn btn-info btn-xs text-info" style="background: none; border: none;" id='button-edit-address' idofaddress="${address.id}" onclick="editAddress(${address.id})">
                    <i class="fa-solid fa-pencil" aria-hidden="true" style="color: inherit;"></i>
                </div>
                <div class="col address-input-field"><button type="button" class="btn btn-danger btn-xs text-danger" style="background: none; border: none;" id='button-remove-address' idofaddress="${address.id}" onclick="deleteAddress(${address.id})">
                    <i class="fa fa-minus-circle" aria-hidden="true" style="color: inherit;"></i>
                </div>
                
          `;
          //container.insertAdjacentHTML('beforeend', addressRow);
          const addressCard = document.createElement('div');
addressCard.className = 'col-6 col-md-4 col-lg-3'; // Responsive Bootstrap grid classes
addressCard.innerHTML = `
    <div class="card h-100" style="border: 1px solid #dee2e6; border-radius: .375rem; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);">
        <div class="card-body" style="padding: 0.75rem;">
            <h6 class="card-title mb-1" style="font-size: 0.95rem; line-height: 1.2;">
                ${address.name} <small style="color: #6c757d;">(${address.shortname})</small>
                <button type="button" class="btn btn-info btn-xs text-info float-end" style="background: none; border: none; padding: 0; font-size: 0.8rem;" idofaddress="${address.id}">
                    <i class="fa-solid fa-pencil" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-danger btn-xs text-danger float-end me-2" style="background: none; border: none; padding: 0; font-size: 0.8rem;" idofaddress="${address.id}">
                    <i class="fa fa-minus-circle" aria-hidden="true"></i>
                </button>
            </h6>
            <p class="card-text mb-0 small" style="font-size: 0.8rem; line-height: 1.3;">
                ${address.address_line_1}<br>
                ${address.address_line_2 ? address.address_line_2 + '<br>' : ''}
                ${address.postal_code} ${address.city}<br>
                ${address.country}
            </p>
        </div>
    </div>
`;
          container.appendChild(addressCard);
        });
}
function addClickListenerToAddNewPackageButton(button) {
      button.addEventListener('click', function(e){
          const clientId = document.getElementById('clientid').value;
          fetch_UnassignedPackageTypes(clientId);
      });
}
function populate_Container_withPackageTypes(packageTypes){
    const container = document.getElementById('client-packagetypeslist');
    container.innerHTML = '';
    packageTypes.forEach(packageType => {
        const packageTypeCard = `
            <div class="card m-2" style="flex: 1 1 calc(33.333% - 1rem); max-width: calc(33.333% - 1rem);">
                <div class="card-header">
                    ${packageType.name} <button class="btn btn-danger">
                                            <i class="fa fa-circle-minus packageTypeRemovalButton" data-packagetypeid = ${packageType.id} aria-hidden="true" style="color: inherit;"></i>
                                        </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-auto">
                            <p><strong>Price:</strong> ${packageType.price}</p>
                        </div>
                        <div class="col-auto">
                            <p><strong>Max before oversize :</strong> ${packageType.baseQuantityThreshold}</p>
                        </div>
                        <div class="col-auto">
                            <p><strong>Maximum allowed to order in a job :</strong> ${packageType.maxQuantityThreshold}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', packageTypeCard);
    });
    document.querySelectorAll('.packageTypeRemovalButton').forEach(button => {
        button.addEventListener('click', function(e) {
            const clientId = document.getElementById('clientid').value;
            const packageTypeId = e.target.getAttribute('data-packagetypeid');
            if(clientId){
                removePackageTypeFromClient(clientId, packageTypeId);
            }
        });
    });
    const addNewPackageCard = document.createElement('div');
    addNewPackageCard.className = 'card m-2';
    addNewPackageCard.style.flex = '1 1 calc(33.333% - 1rem)';
    addNewPackageCard.style.maxWidth = 'calc(33.333% - 1rem)';

    const cardHeader = document.createElement('div');
    cardHeader.className = 'card-header';
    cardHeader.textContent = 'Add new package';

    const cardBody = document.createElement('div');
    cardBody.className = 'card-body';

    const addButton = document.createElement('button');
    addButton.className = 'btn btn-primary';
    addButton.innerHTML = '<i class="fa fa-plus-circle" aria-hidden="true" style="color: inherit;"></i>';

    cardBody.appendChild(addButton);
    addNewPackageCard.appendChild(cardHeader);
    addNewPackageCard.appendChild(cardBody);

    container.appendChild(addNewPackageCard);
    addClickListenerToAddNewPackageButton(addButton);
}
function populateSelectionOfUnassignedPackageTypes(packageTypes){
  const selectELement = document.getElementById('packageTypeSelect');
  selectELement.innerHTML = '<option value="" disabled selected>Select a package type</option>';
  packageTypes.forEach(packageType => {
      const option = document.createElement('option');
      option.value = packageType.id;
      option.textContent = packageType.name;
      selectELement.appendChild(option);
  });
}
function fetch_UnassignedPackageTypes(clientId){
  const routeUrl = window.ROUTES.WEB.CLIENT.FETCHUNASSIGNEDPACKAGETYPES.replace(':id', clientId);
  fetch(routeUrl)
      .then(response => response.json())
      .then(data => {
          if (data) {
              populateSelectionOfUnassignedPackageTypes(data.packageTypes);
              $('#modalWindow-packages-addNewFromList').modal('show');
          }
      })
      .catch(error => {
          console.error(error);
      });
}
function removePackageTypeFromClient(clientId, packageTypeId){
    const routeUrl = window.ROUTES.WEB.CLIENT.REMOVEPACKAGETYPE;
    fetch(routeUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
                package_type_id: packageTypeId,
                client_id : clientId
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data){
            fetchPackageTypes(clientId);
        }
    })
    .catch(error => {
        console.error(error);
    });
}
export function fetchPackageTypes(clientId){
    const routeUrl = window.ROUTES.WEB.CLIENT.FETCHPACKAGETYPES.replace(':id', clientId);
    fetch(routeUrl)
        .then(response => response.json())
        .then(data => {
            if (data) {
                populate_Container_withPackageTypes(data.packageTypes);
            }
        })
        .catch(error => {
            console.error(error);
        });
}