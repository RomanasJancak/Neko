export const clientInforReadOnlyState = {readOnly : false};

export function fillAddressViewForm(addressId,clientId = null){
  var button = document.getElementById('address-submitform');
  if(!clientId){
  var url = window.ROUTES.WEB.ADDRESS.GETINFO.replace(':id', addressId);
  fetch(url)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const id = data.id || '';
        const name = data.name || '';
        const addressLine1 = data.address_line_1 || '';
        const addressLine2 = data.address_line_2 || '';
        const postalCode = data.postal_code || '';
        const city = data.city || '';
        const country = data.country || '';
        document.getElementById('addressid').value = id;
        document.getElementById('address-nameField').value = name;
        document.getElementById('address-addressline_1-field').value = addressLine1;
        document.getElementById('address-addressline_2-field').value = addressLine2;
        document.getElementById('address-postalcode-field').value = postalCode;
        document.getElementById('address-city-field').value = city;
        document.getElementById('address-country-field').value = country;
        button.setAttribute('form-action', 'update');
      } else {
        console.error('Error fetching client info:', data.message);
      }
    })
    .catch(error => console.error('Fetch error:', error));
  }else{
    document.getElementById('addressid').value = '';
    document.getElementById('address-nameField').value = '';
    document.getElementById('address-addressline_1-field').value = '';
    document.getElementById('address-addressline_2-field').value = '';
    document.getElementById('address-postalcode-field').value = '';
    document.getElementById('address-city-field').value = '';
    document.getElementById('address-country-field').value = '';
    button.setAttribute('form-action', 'create');
    button.setAttribute('data-client-id', clientId);
  }
}
export function setupAddressFormSubmit(button){
  if (!button.hasAttribute('data-listener-click')) {
    button.setAttribute('data-listener-click', 'true');
    button.addEventListener('click', function() {
      const form = document.getElementById('addressForm');
      const formData = new FormData(form);
      
      let url;
    if(button.getAttribute('form-action') === 'update'){
      url = window.ROUTES.WEB.ADDRESS.UPDATE.replace(':id', formData.get('addressid'));
    } else if(button.getAttribute('form-action') === 'create'){
      url = window.ROUTES.WEB.ADDRESS.CREATE;
      formData.append('client_id', button.getAttribute('data-client-id'));
    }
    fetch(url, {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        show_Success_Message({message: data.message});
      } else {
        show_Error_Message({message: data.message});
      }
    })
    .catch(error => console.error('Fetch error:', error));
  })
  }
}
