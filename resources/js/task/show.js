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
function setReadOnlyToFieldsOfTaskModal(status){
    let fields = [];
    fields.push(document.getElementById('taskStatusIdField'));
    fields.push(document.getElementById('taskClientNameField'));
    fields.push(document.getElementById('taskCountryField'));
    fields.push(document.getElementById('taskCityField'));
    fields.push(document.getElementById('taskPostalCodeField'));
    fields.push(document.getElementById('taskAddressLineField'));
    fields.push(document.getElementById('taskTimeBegin'));
    fields.push(document.getElementById('taskTimeEnd'));
    fields.push(document.getElementById('taskTimeDate'));
    fields.forEach(function(field){
        field.disabled = status;
    });
}
function cleanTaskCreateWindow(type = 'none'){
    let selectStatusField =   document.getElementById('taskStatusIdField');
    let selectTypeField =   document.getElementById('taskTypeField');
    let taskClientNameField =   document.getElementById('taskClientNameField');
    let taskPostalCodeField =   document.getElementById('taskPostalCodeField');
    let taskAddressLineField =   document.getElementById('taskAddressLineField');
    let taskTimeBegin =   document.getElementById('taskTimeBegin');
    let taskTimeEnd =   document.getElementById('taskTimeEnd');
    selectStatusField.selectedIndex = 0;
    selectTypeField.selectedIndex = -1;
    selectTypeField.disabled = false;
    taskClientNameField.value   =   '';
    taskPostalCodeField.value   =   '';
    taskAddressLineField.value   =   '';
    switch(type){
        case 'pickup':
            taskTimeBegin.value = global_taskWindow_defaultValue_time_pickup[0];
            taskTimeEnd.value = global_taskWindow_defaultValue_time_pickup[1];
            break;
        case 'dropOff':
            taskTimeBegin.value = global_taskWindow_defaultValue_time_dropoff[0];
            taskTimeEnd.value = global_taskWindow_defaultValue_time_dropoff[1];
            break;
        case 'return':
            taskTimeBegin.value = global_taskWindow_defaultValue_time_return[0];
            taskTimeEnd.value = global_taskWindow_defaultValue_time_return[1];
            break;
        default:
            taskTimeBegin.value = '';
            taskTimeEnd.value = '';
            break;
    }
}
document.getElementById('submitTaskform').addEventListener('click', function(event) {
    event.preventDefault();
  const   typeField   =   document.getElementById('taskTypeField');
  var     route       = '';
  if(this.getAttribute('data-option') === 'delete'){
      route = window.ROUTES.WEB.TASK.DELETE;
  }else if(this.getAttribute('data-option') === 'update'){
      route = window.ROUTES.WEB.TASK.UPDATE;
  }else if(this.getAttribute('data-option') === 'view'){
      return;
  }else if(this.getAttribute('data-option') === 'create'){
    route = window.ROUTES.WEB.TASK.STORE;
  }
  var  taskSubmitData = {
    jobId       :   document.getElementById('idField').value,
    id          :   document.getElementById('taskIdField').value,
    status_id   :   document.getElementById('taskStatusIdField').value,
    type        :   document.getElementById('taskTypeField').value,
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
      taskSubmitData.package = {
          type        : document.getElementById('packageTypeSelect').value,
          quantity    : document.getElementById('quantityInput').value,
          weight      : document.getElementById('weightInput').value,
      }
  }
  if(typeField.value == 'return'){
      taskSubmitData.returnTask = {
          is_flexible : document.getElementById('returnTask_isFlexible').checked,
          date    : document.getElementById('taskTimeDate').value,
      }
  }
  updateTask(taskSubmitData,route);
});