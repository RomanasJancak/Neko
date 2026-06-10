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

        // Add keyboard navigation for arrow keys, enter, and tab confirmation.
        // Capture phase avoids missing events when plugins intercept key handlers.
        var inputEl = searchInput.get(0);
        if (!inputEl) return;

        if (inputEl._typeaheadTaskKeydownHandler) {
            inputEl.removeEventListener('keydown', inputEl._typeaheadTaskKeydownHandler, true);
        }

        var keydownHandler = function(e) {
            var typeahead = searchInput.data('typeahead');
            if (!typeahead || !typeahead.$menu) return;

            var $menu = typeahead.$menu;
            if (!$menu.is(':visible')) return;

            var $items = $menu.find('li:has(a)');
            if (!$items.length) {
                $items = $menu.find('a');
            }
            if (!$items.length) return;

            var $active = $items.filter('.active').first();
            if (!$active.length) {
                $active = $items.find('.active').closest('li, a').first();
            }
            var activeIndex = $items.index($active);

            function setActive(index) {
                $items.removeClass('active');
                $items.find('.active').removeClass('active');

                var $item = $items.eq(index);
                $item.addClass('active');
                $item.closest('li').addClass('active');
                $item.find('a').addClass('active');
                $item.children('a').addClass('active');
            }

            function selectActiveOrFirst() {
                var $selected = $items.filter('.active').first();
                if (!$selected.length) {
                    $selected = $items.find('.active').closest('li, a').first();
                }
                if (!$selected.length) {
                    $selected = $items.first();
                    setActive(0);
                }

                var $clickTarget = $selected.is('a') ? $selected : $selected.find('a').first();
                if ($clickTarget.length) {
                    $clickTarget.trigger('click');
                } else {
                    $selected.trigger('click');
                }
            }

            switch(e.keyCode) {
                case 38: // up
                    e.preventDefault();
                    if (activeIndex <= 0) {
                        setActive($items.length - 1);
                    } else {
                        setActive(activeIndex - 1);
                    }
                    break;
                case 40: // down
                    e.preventDefault();
                    if (activeIndex < 0 || activeIndex >= $items.length - 1) {
                        setActive(0);
                    } else {
                        setActive(activeIndex + 1);
                    }
                    break;
                case 13: // enter
                    e.preventDefault();
                    selectActiveOrFirst();
                    break;
                case 9: // tab
                    // Confirm current suggestion and keep native tab navigation to next field.
                    selectActiveOrFirst();
                    break;
            }
        };

        inputEl.addEventListener('keydown', keydownHandler, true);
        inputEl._typeaheadTaskKeydownHandler = keydownHandler;
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
    document.getElementById('addressMapMarker').addEventListener('click', function() {
        const addressLine = document.getElementById('taskAddressLineField').value;
        const postalCode = document.getElementById('taskPostalCodeField').value;
        const country = document.getElementById('taskCountryField').value; // Optionally, you can add country field if needed
        const city = document.getElementById('taskCityField').value; // Optionally, you can add city field if needed
        const query = encodeURIComponent(addressLine + ' ' + postalCode + ' ' + city + ' ' + country);
        const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${query}`;
        window.open(mapsUrl, '_blank');
    });
});
