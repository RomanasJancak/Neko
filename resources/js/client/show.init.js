import { fillClientViewForm,fetchPackageTypes,clientInforReadOnlyState,createAddress } from './show.js';


document.addEventListener('DOMContentLoaded', function () {
  var clientId  = document.getElementById('clientid').value;
  console.log('this');
  if (clientId) {
    fillClientViewForm(clientId);
  }else{
    clientInforReadOnlyState.readOnly = true;
  }
  document.getElementById('button-add-address').addEventListener('click', function(e) {
    createAddress();
    document.getElementById('address-submitform').setAttribute('form-action', 'create');
  });
  document.getElementById('container-addresses').addEventListener('click', function(e) {
    const removeButton = e.target.closest('.js-remove-new-address');
    if (!removeButton) {
      return;
    }

    const row = removeButton.closest('.row');
    if (row) {
      row.remove();
    }
  });
  document.getElementById('button-view-packages').addEventListener('click',function(e){
    const clientId = document.getElementById('clientid').value;
    fetchPackageTypes(clientId);
    $('#modalWindow-packages').modal('show');
  });
  document.getElementById('submitform').addEventListener('click', function() {
    // Get form data
    const form = document.getElementById('clientForm');
    const formData = new FormData(form);

    // Create a new XMLHttpRequest object
    const xhr = new XMLHttpRequest();

    // Define the request type, URL, and set up the request
    xhr.open('POST', form.action, true);
    xhr.setRequestHeader('X-CSRF-Token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    // Handle the response
    xhr.onload = function() {
        // Process the response if needed
        //parsedMessage = JSON.parse(xhr.responseText).message;
        // Handle the response based on the message
    };

    // Send the request
    xhr.send(formData);
    $('#modalWindow').modal('hide');
  });
  
});