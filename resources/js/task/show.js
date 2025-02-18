function showPackageDiv(status){
    const container =   document.getElementById('package-info');
    if(status){
        container.style.visibility = 'visible';
    }else{
        container.style.visibility = 'hidden';
    }
}
function setTaskValues(taskId){
    const idField    =   document.getElementById('taskIdField');
    const typeField    =   document.getElementById('taskTypeField');
    const statusIdField     =   document.getElementById('taskStatusIdField');
    const clientNameField   =   document.getElementById('taskClientNameField');
    const addressCountryField   =   document.getElementById('taskCountryField');
    const addressCityField   =   document.getElementById('taskCityField');
    const addressPostalCodeField   =   document.getElementById('taskPostalCodeField');
    const addressAddressLineField   =   document.getElementById('taskAddressLineField');
    const timeBeginField   =   document.getElementById('taskTimeBegin');
    const timeEndField   =   document.getElementById('taskTimeEnd');
    const crateCollection   =   document.getElementById('crateCollection');
    idField.value = taskId;
    idField.disabled =  true;
    typeField.disabled =  true;
    const routeUrl = "{{ route('task.getTaskInfo', ['id' => ':id']) }}".replace(':id', taskId);
    return fetch(routeUrl)
        .then(response => response.json())
        .then(data => {
            typeField.value                 =   data.type;
            statusIdField.value             =   data.statusId;
            clientNameField.value           =   data.address.name;
            addressCountryField.value       =   data.address.country;
            addressCityField.value          =   data.address.city;
            addressPostalCodeField.value    =   data.address.postalCode;
            addressAddressLineField.value   =   data.address.addressLine;
            const dateBegin = new Date(data.time.begin);
            const dateEnd = new Date(data.time.end);
            timeBeginField.value            =   `${String(dateBegin.getUTCHours()).padStart(2, '0')}:${String(dateBegin.getUTCMinutes()).padStart(2, '0')}:${String(dateBegin.getUTCSeconds()).padStart(2, '0')}`;
            timeEndField.value              =   `${String(dateEnd.getUTCHours()).padStart(2, '0')}:${String(dateEnd.getUTCMinutes()).padStart(2, '0')}:${String(dateEnd.getUTCSeconds()).padStart(2, '0')}`;

            if(data.package !== 'none'){
                addInfoAboutPackageToTaskModal(data.package).then(()=>{
                });
                crateCollection.checked = data.package.hasCollection;
                showPackageDiv(true);
                    divForTaskFormCrateCollection.style.display = 'block';
            }else{
                showPackageDiv(false);
                divForTaskFormCrateCollection.style.display = 'none';
            }
            container_return = document.getElementById('return-info');
            if(data.returnTask !== 'none'){
                taskTimeDate = document.getElementById('taskTimeDate');
                taskTimeDate.value  = `${dateBegin.getUTCFullYear()}-${String(dateBegin.getUTCMonth() + 1).padStart(2, '0')}-${String(dateBegin.getUTCDate()).padStart(2, '0')}`;
                
                checkbox  = document.getElementById('returnTask_isFlexible');
                if (checkbox.checked) {
                        container_taskTimeDate.style.visibility = 'visible';
                    } else {
                        container_taskTimeDate.style.visibility = 'hidden';
                    }
                container_return.style.visibility = 'visible';
                
                
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        container_taskTimeDate.style.visibility = 'visible';

                    } else {
                        container_taskTimeDate.style.visibility = 'hidden';
                    }
                });
            }else{
                container_return.style.visibility = 'hidden';
            }
        })
        .catch(error => {
            console.error(error);
    });
}
function updateTask(data,url){
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  fetch(url, {
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
function addTypeHeadSearchToTaskWindow(searchInput){
    if (searchInput.length > 0) {
        searchInput.typeahead({
        source: function(query, process) {
            let client_id = document.getElementById('clientIdField').value;
            //console.log("client_id : ",client_id);
            var apiUrl = window.ROUTES.WEB.CLIENT.SEARCHADDRESSES+"?query=" + query + "&client_id=" + client_id;
            //var apiUrl = "{{ route('client.searchClients') }}?query=" + query;
            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    process(data);
                })
                .catch(error => {
                    console.error('Error fetching client data:', error);
                });
        },
        autoSelect: true,
        minLength: 2, // Minimum characters required before searching
        displayText: function(item) {
            return item.name;
        },
        afterSelect: function(item) {
            // Handle the selection here (e.g., redirect to client details page)
            //fetch(`/get-client-info/${item.id}`)
            const clientInfoUrlTemplate = "{{ route('address.getAddressInfo', ['id' => ':addressId']) }}";
            const clientInfoUrl = clientInfoUrlTemplate.replace(':addressId', item.id);
            fetch(clientInfoUrl)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    console.log(data);
                    // const addressSelect = document.getElementById('taskWindow_addressSelectField');
                    // addressSelect.innerHTML = '';
                    // document.getElementById('task_clientIdField').value = data.id;
                     document.getElementById('taskCountryField').value = data.country; 
                     document.getElementById('taskCityField').value = data.city;
                     document.getElementById('taskPostalCodeField').value = data.postal_code;
                     document.getElementById('taskAddressLineField').value = data.address_line_1+' '+data.address_line_2;     
                    // const addressSelect = document.getElementById('taskWindow_addressSelectField');
                    // addressSelect.style.visibility = 'hidden';
                }
            })
            .catch(error => {
                console.error(error);
            });
        }
    });
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
    hasCrateCollection  :   document.getElementById('crateCollection').checked,
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
addTypeHeadSearchToTaskWindow($('#taskClientNameField'));

