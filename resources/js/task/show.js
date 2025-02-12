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
  updateData = {
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
      updateData.package = {
          type        : document.getElementById('packageTypeSelect').value,
          quantity    : document.getElementById('quantityInput').value,
          weight      : document.getElementById('weightInput').value,
      }
  }
  if(typeField.value == 'return'){
      updateData.returnTask = {
          is_flexible : document.getElementById('returnTask_isFlexible').checked,
          date    : document.getElementById('taskTimeDate').value,
      }
  }
  updateTask(updateData,route);
  $('#jobModalWindow').modal('show');
  $('#taskModalWindow').modal('hide');
});