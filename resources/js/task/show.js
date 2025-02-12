function updateTask(data,route){
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  // Send a POST request to the server using the generated route
  fetch(route, {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json', // Set Accept header
          // Add any additional headers if needed
          'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify(data)
  })
  .then(response => {
      // if (!response.ok) {
      //     throw new Error('Failed to update job');
      // }
      return response.json();
  })
  .then(data => {

      setJobValues(document.getElementById('idField').value,global_typeOfButtonClickedToOpenJobModal);
  })
  .catch(error => {
      console.error('Error:', error.message);
  });
}
function setTaskFormSubmitButtonDataOptionToView(){
  document.getElementById('submitTaskform').setAttribute('data-option', 'view');
}
function setTaskFormSubmitButtonDataOptionToDelete(){
  document.getElementById('submitTaskform').setAttribute('data-option', 'delete');
}
function setTaskFormSubmitButtonDataOptionToUpdate(){
  document.getElementById('submitTaskform').setAttribute('data-option', 'update');
}
function setTaskFormSubmitButtonDataOptionToCreate(){
  document.getElementById('submitTaskform').setAttribute('data-option', 'create');
}
document.getElementById('submitTaskform').addEventListener('click', function(event) {
  event.preventDefault();
  const   typeField   =   document.getElementById('taskTypeField');
  var     route       = '';
  if(this.getAttribute('data-option') === 'delete'){
      route = '{{ route("task.delete") }}';
  }else if(this.getAttribute('data-option') === 'edit'){
      route = '{{ route("task.update") }}';
  }else if(this.getAttribute('data-option') === 'view'){
      return;
  }else if(this.getAttribute('data-option') === 'create'){
      route = '{{ route("task.store") }}';
  } 
  data = {
      jobId       :   document.getElementById('idField').value,
      id          :   document.getElementById('taskIdField').value,
      status_id   :   document.getElementById('taskStatusIdField').value,
      type        :   typeField.value,
      address     :   {
          name            :   document.getElementById('taskClientNameField').value,
          country         :   document.getElementById('taskCountryField').value,
          city            :   document.getElementById('taskCityField').value,
          postalCode      :   document.getElementById('taskPostalCodeField').value,
          addressLine     :   document.getElementById('taskAddressLineField').value,
      },
      time        :   {
          begin   :   document.getElementById('jobDateField').value+' '+document.getElementById('taskTimeBegin').value,
          end     :   document.getElementById('jobDateField').value+' '+document.getElementById('taskTimeEnd').value,
      },
      date        :   document.getElementById('jobDateField').value,
         
  }
  if(typeField.value == 'dropOff'){
      data.package = {
          type        : document.getElementById('packageTypeSelect').value,
          quantity    : document.getElementById('quantityInput').value,
          weight      : document.getElementById('weightInput').value,
      }
  }
  if(typeField.value == 'return'){
      data.returnTask = {
          is_flexible : document.getElementById('returnTask_isFlexible').checked,
          date    : document.getElementById('taskTimeDate').value,
      }
  }
  updateTask(data,route);
  document.getElementById('jobModalWindow').style.display = 'block';
  document.getElementById('taskModalWindow').style.display = 'none';
});