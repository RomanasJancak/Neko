export const clientInforReadOnlyState = {readOnly : false};
export function fillAddressViewForm(clientId){
  var clientInfoUrl = window.ROUTES.WEB.ADDRESS.GETINFO.replace(':id', clientId);
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
