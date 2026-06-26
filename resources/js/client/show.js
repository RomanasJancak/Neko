export const clientInforReadOnlyState = {readOnly : false};
function showModal(modalSelector) {
    if (window.$) {
        window.$(modalSelector).modal('show');
        return;
    }

    const modalElement = document.querySelector(modalSelector);
    if (modalElement && window.bootstrap) {
        const modal = new window.bootstrap.Modal(modalElement);
        modal.show();
    }
}

export function setClientFormReadOnlyState(readOnly = false) {
    clientInforReadOnlyState.readOnly = readOnly;
    const fieldIds = [
        'nameField',
        'reg-adress-section-adress-country-field',
        'reg-adress-section-adress-city-field',
        'reg-adress-section-adress-postalcode-field',
        'reg-adress-section-adress-addressline-field',
        'phoneNumberField',
    ];

    fieldIds.forEach(id => {
        const field = document.getElementById(id);
        if (field) {
            field.readOnly = readOnly;
        }
    });
}
export function fillClientViewForm(clientId){
  var clientInfoUrl = window.ROUTES.WEB.CLIENT.GETINFO.replace(':id', clientId);
  fetch(clientInfoUrl)
    .then(response => response.json())
    .then(data => {
            const payload = data && data.success === false ? null : (data.success ? data : data);
            if (payload) {
        document.getElementById('clientid').value = clientId;
                document.getElementById('nameField').value = payload.name || '';
                document.getElementById('shortenedNameField').value = payload.nickName || '';

                document.getElementById('reg-adress-section-adress-country-field').value = payload.country || '';
                document.getElementById('reg-adress-section-adress-city-field').value = payload.city || '';
                document.getElementById('reg-adress-section-adress-postalcode-field').value = payload.postal_code || '';
                document.getElementById('reg-adress-section-adress-addressline-field').value = payload.address_line || '';

                document.getElementById('phoneNumberField').value = payload.phone || '';
                populateWithAddresses(payload.addresses || []);
                populateWithEmails(payload.emails || []);
      } else {
        console.error('Error fetching client info:', data.message);
      }
    })
    .catch(error => console.error('Fetch error:', error));
    setClientFormReadOnlyState(clientInforReadOnlyState.readOnly);
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
    const emailsContainer = document.getElementById('container-emails');
    if (emailsContainer) {
        emailsContainer.innerHTML = '';
    }
}

function deleteEmail(emailId = null){
    const container = document.getElementById('container-emails');

    if (!emailId) {
        return;
    }

    const emailElement = document.querySelector(`input[name="email_id[]"][value="${emailId}"]`);
    if (!emailElement) {
        return;
    }

    const routeUrl = window.ROUTES.WEB.EMAIL.DELETE.replace(':id', emailId);
    const clientId = document.getElementById('clientid') ? document.getElementById('clientid').value : null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(routeUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            client_id: clientId,
        }),
    })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.error || 'Failed to delete email.');
            }
            return data;
        })
        .then(() => {
            const row = emailElement.closest('.row');
            if (row && container.contains(row)) {
                row.remove();
            }
        })
        .catch(error => {
            console.error('Error deleting email:', error);
            alert(error.message || 'Error deleting email. Please try again.');
        });
}

function bindEmailContainerEvents(containerMain) {
    if (!containerMain || containerMain.dataset.eventsBound === '1') {
        return;
    }

    containerMain.addEventListener('click', event => {
        const deleteButton = event.target.closest('.js-email-delete');
        if (deleteButton) {
            const emailId = deleteButton.getAttribute('data-email-id');
            if (emailId) {
                deleteEmail(emailId);
            }
            return;
        }

        const removeLocalButton = event.target.closest('.js-email-remove-local');
        if (removeLocalButton) {
            const row = removeLocalButton.closest('.row');
            if (row) {
                row.remove();
            }
            return;
        }

        const editButton = event.target.closest('.js-email-edit');
        if (editButton) {
            const emailId = editButton.getAttribute('data-email-id');
            if (typeof window.editEmail === 'function') {
                window.editEmail(emailId);
            }
        }
    });

    containerMain.dataset.eventsBound = '1';
}

function populateWithEmails(emails){
  console.log("THIS 1", emails);
    const container_main = document.getElementById('container-emails');
    if (!container_main) {
        return;
    }
    bindEmailContainerEvents(container_main);

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
            <div class="col email-input-field"><button type="button" class="btn btn-info btn-xs text-info js-email-edit" style="background: none; border: none;" data-email-id="${email.id}">
                <i class="fa-solid fa-pencil" aria-hidden="true" style="color: inherit;"></i>
            </div>
            <div class="col email-input-field"><button type="button" class="btn btn-danger btn-xs text-danger js-email-delete" style="background: none; border: none;" data-email-id="${email.id}">
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
    addButton.addEventListener('click', () => {
        const newEmailRow = `
            <div class="row">        
                <div class="col email-input-field" style="display: none;"><input type="hidden" name="email_id[]" class="form-control" value=""></div>
                <div class="col email-input-field"><input style="font-size: 0.8em;" type="text" name="email[]" class="form-control" value="" placeholder="Email" ></div>
                <div class="col email-input-field"><input style="font-size: 0.8em;" type="text" name="email_type[]" class="form-control" value="" placeholder="Type (e.g. work, personal)" ></div>
                <div class="col email-input-field"><button type="button" class="btn btn-danger btn-xs text-danger js-email-remove-local" style="background: none; border: none;">
                    <i class="fa fa-minus-circle" aria-hidden="true" style="color: inherit;"></i>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newEmailRow);
    });
    container_main.appendChild(addButton);
}
function editAdddress(addressId = null){
  console.log("editAdddress called with addressId:", addressId);
    if (!addressId) {
        return;
    }
    const addressElement = document.querySelector(`input[name="address_id[]"][value="${addressId}"]`);
    if (!addressElement) {
        return;
    }
    const row = addressElement.closest('.row');
    if (row) {
        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        row.classList.add('highlight');
        setTimeout(() => {
            row.classList.remove('highlight');
        }, 2000);
    }
}
function bindAddressContainerEvents(containerMain) {
    if (!containerMain || containerMain.dataset.eventsBound === '1') {
        return;
    }

    containerMain.addEventListener('click', event => {
        const editButton = event.target.closest('.js-address-edit');
        if (editButton) {
            const addressId = editButton.getAttribute('data-address-id');
            editAdddress(addressId);
            return;
        }

        const deleteButton = event.target.closest('.js-address-delete');
        if (deleteButton) {
            const addressId = deleteButton.getAttribute('data-address-id');
            if (typeof window.deleteAddress === 'function') {
                window.deleteAddress(addressId);
            }
        }
    });

    containerMain.dataset.eventsBound = '1';
}

window.editAdddress = editAdddress;
function populateWithAddresses(addresses){
        const container_main = document.getElementById('container-addresses');
        if (!container_main) {
            return;
        }
        bindAddressContainerEvents(container_main);

        const container = document.createElement('div');
        container.className = 'row';
        container_main.innerHTML = '';
        container_main.appendChild(container);
        addresses.forEach(address => {
          const addressCard = document.createElement('div');
addressCard.className = 'col-6 col-md-4 col-lg-3'; // Responsive Bootstrap grid classes
addressCard.innerHTML = `
    <div class="card h-100" style="border: 1px solid #dee2e6; border-radius: .375rem; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);">
        <input type="hidden" name="address_id[]" class="form-control" value="${address.id}">
        <div class="card-body" style="padding: 0.75rem;">
            <h6 class="card-title mb-1" style="font-size: 0.95rem; line-height: 1.2;">
                ${address.name} <small style="color: #6c757d;">(${address.shortname})</small>
                <button type="button" class="btn btn-info btn-xs text-info float-end js-address-edit" style="background: none; border: none; padding: 0; font-size: 0.8rem;" data-address-id="${address.id}">
                    <i class="fa-solid fa-pencil" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-danger btn-xs text-danger float-end me-2 js-address-delete" style="background: none; border: none; padding: 0; font-size: 0.8rem;" data-address-id="${address.id}">
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

export function openClientFormForView({ clientId, modalSelector = '#clientModalWindow' }) {
    if (!clientId) {
        return;
    }
    setClientFormReadOnlyState(true);
    fillClientViewForm(clientId);
    showModal(modalSelector);
}

export function openClientFormForEdit({ clientId, formAction, submitButtonText = 'Update', modalSelector = '#modalWindow' }) {
    const form = document.querySelector('#clientForm');
    if (form && formAction) {
        form.setAttribute('action', formAction);
    }
    setClientFormReadOnlyState(false);
    fillClientViewForm(clientId);
    const submitButton = document.getElementById('submitform');
    if (submitButton) {
        submitButton.innerHTML = submitButtonText;
    }
    showModal(modalSelector);
}

export function openClientFormForDelete({ clientId, formAction, submitButtonText = 'Delete', modalSelector = '#modalWindow' }) {
    const form = document.querySelector('#clientForm');
    if (form && formAction) {
        form.setAttribute('action', formAction);
    }
    setClientFormReadOnlyState(true);
    fillClientViewForm(clientId);
    const submitButton = document.getElementById('submitform');
    if (submitButton) {
        submitButton.innerHTML = submitButtonText;
    }
    showModal(modalSelector);
}

export function openClientFormForCreate({ formAction, submitButtonHtml = "<i class='bi bi-save'></i>", modalSelector = '#modalWindow' }) {
    const form = document.querySelector('#clientForm');
    if (form && formAction) {
        form.setAttribute('action', formAction);
    }
    setClientFormReadOnlyState(false);
    cleanClientForm();
    const submitButton = document.getElementById('submitform');
    if (submitButton) {
        submitButton.innerHTML = submitButtonHtml;
    }
    showModal(modalSelector);
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

window.fillClientViewForm = fillClientViewForm;
window.cleanClientForm = cleanClientForm;
window.setClientFormReadOnlyState = setClientFormReadOnlyState;
window.openClientFormForView = openClientFormForView;
window.openClientFormForEdit = openClientFormForEdit;
window.openClientFormForDelete = openClientFormForDelete;
window.openClientFormForCreate = openClientFormForCreate;