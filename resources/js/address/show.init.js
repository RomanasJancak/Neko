import {  } from './show.js';

document.addEventListener('DOMContentLoaded', function () {
  var addressId = document.getElementById('addressid').value;
  if (addressId) {
    fillAddressViewForm(addressId);
  } else {
    addressInforReadOnlyState.readOnly = true;
  }
});