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
    if (data.success) {
        show_Success_Message({message :data.message});
    }
  })
  .catch(error => {
      console.error('Error:', error.message);
  });
}

function addTypeHeadSearchToTaskWindow(searchInput){
    if (searchInput.length > 0) {
        searchInput.typeahead({
        source: function(query, process) {
            let client_id = document.getElementById('clientIdField').value;
            var apiUrl = window.ROUTES.WEB.CLIENT.SEARCHADDRESSES+"?query=" + query + "&client_id=" + client_id;
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
        minLength: 2, 
        displayText: function(item) {
            return item.name;
        },
        afterSelect: function(item) {
            const clientInfoUrlTemplate = window.ROUTES.WEB.ADDRESS.GETINFO;
            const clientInfoUrl = clientInfoUrlTemplate.replace(':id', item.id);
            fetch(clientInfoUrl)
            .then(response => response.json())
            .then(data => {
                if (data) {

                     //document.getElementById('taskCountryField').value = data.country; 
                     //document.getElementById('taskCityField').value = data.city;
                     document.getElementById('taskPostalCodeField').value = data.postal_code;
                     document.getElementById('taskAddressLineField').value = data.address_line_1+' '+data.address_line_2;     
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
    let submitButton = event.target;
    const   typeField   =   document.getElementById('taskTypeField');
    var     route       = '';
    if(this.getAttribute('data-option') === 'delete'){
        route = window.ROUTES.WEB.TASK.DELETE;
        submitButton.disabled = true;
    }else if(this.getAttribute('data-option') === 'update'){
        route = window.ROUTES.WEB.TASK.UPDATE;
        submitButton.disabled = true;
    }else if(this.getAttribute('data-option') === 'view'){
        return;
    }else if(this.getAttribute('data-option') === 'create'){
        route = window.ROUTES.WEB.TASK.STORE;
        submitButton.disabled = true;
  }
  var  taskSubmitData = {
    jobId       :   document.getElementById('idField').value,
    id          :   document.getElementById('taskIdField').value,
    status_id   :   document.getElementById('taskStatusIdField').value,
    type        :   document.getElementById('taskTypeField').value,
    address     :   {
        name            :   document.getElementById('taskClientNameField').value,
        //country         :   document.getElementById('taskCountryField').value,
        //city            :   document.getElementById('taskCityField').value,
        postalCode      :   document.getElementById('taskPostalCodeField').value,
        addressLine     :   document.getElementById('taskAddressLineField').value,
    },
    time        :   {
        begin   :   document.getElementById('jobDateField').value+' '+document.getElementById('taskTimeBegin').value,
        end     :   document.getElementById('jobDateField').value+' '+document.getElementById('taskTimeEnd').value,
    },
    date        :   document.getElementById('jobDateField').value,
    hasCrateCollection  :   document.getElementById('crateCollection').checked,
    note    : document.getElementById('taskNoteField').value,
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

document.addEventListener('DOMContentLoaded', function() {
    addTypeHeadSearchToTaskWindow($('#taskClientNameField'));
});
