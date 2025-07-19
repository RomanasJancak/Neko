import { fillClientViewForm,clientInforReadOnlyState } from '../client/show.js';

function viewJob(jobId) {
    const jobIdField = document.getElementById('idField');
    const courierIdField = document.getElementById('courierIdField');
    const statusIdField = document.getElementById('statusIdField');
    const clientSearchField = document.getElementById('clientSearchField');
    const jobDateField = document.getElementById('jobDateField');
    const createNewTaskButton = document.getElementById('createNewTask');
    const form = document.querySelector('#jobForm');

    if (form) {
        form.setAttribute('action', window.ROUTES.WEB.JOB.VIEW);
        jobIdField.value = jobId;
        jobIdField.disabled = true;
        courierIdField.disabled = true;
        statusIdField.disabled = true;
        clientSearchField.disabled = true;
        jobDateField.disabled = true;
        document.getElementById('jobid').value = jobId;
        
        setJobValues(jobId, 'view');
        global_typeOfButtonClickedToOpenJobModal = 'view';

        const submitButton = document.getElementById('submitform');
        submitButton.setAttribute('data-option', 'view');
        submitButton.innerHTML = "<i class='bi bi-eye'></i>";
        submitButton.style.visibility = 'hidden';
        createNewTaskButton.style.visibility = 'hidden';
    }
    toggle_CreateNewTaskButton(false);
    $('#jobModalWindow').modal('show');
}
function editJob(jobId) {
    const jobIdField = document.getElementById('idField');
    const courierIdField = document.getElementById('courierIdField');
    const statusIdField = document.getElementById('statusIdField');
    const clientSearchField = document.getElementById('clientSearchField');
    const jobDateField = document.getElementById('jobDateField');
    const createNewTaskButton = document.getElementById('createNewTask');
    const form = document.querySelector('#jobForm');

    if (form) {
        form.setAttribute('action', window.ROUTES.WEB.JOB.UPDATE);
        jobIdField.value = jobId;
        jobIdField.disabled = true;
        courierIdField.disabled = false;
        statusIdField.disabled = false;
        clientSearchField.disabled = false;
        jobDateField.disabled = false;
        
        setJobValues(jobId, 'edit');
        global_typeOfButtonClickedToOpenJobModal = 'edit';
        
        const submitButton = document.getElementById('submitform');
        submitButton.setAttribute('data-option', 'edit');
        submitButton.innerHTML = "<i class='bi bi-pen'></i>";
        submitButton.style.visibility = 'visible';
        createNewTaskButton.style.visibility = 'visible';
    }
    toggle_CreateNewTaskButton(true);
    $('#jobModalWindow').modal('show');
}
function deleteJob(jobId) {
    const jobIdField = document.getElementById('idField');
    const courierIdField = document.getElementById('courierIdField');
    const statusIdField = document.getElementById('statusIdField');
    const clientSearchField = document.getElementById('clientSearchField');
    const jobDateField = document.getElementById('jobDateField');
    const createNewTaskButton = document.getElementById('createNewTask');
    const form = document.querySelector('#jobForm');

    if (form) {
        form.setAttribute('action', window.ROUTES.WEB.JOB.DELETE);
        jobIdField.value = jobId;
        jobIdField.disabled = true;
        courierIdField.disabled = true;
        statusIdField.disabled = true;
        clientSearchField.disabled = true;
        jobDateField.disabled = true;
        document.getElementById('jobid').value = jobId;
        
        setJobValues(jobId, 'delete');
        global_typeOfButtonClickedToOpenJobModal = 'delete';
        const submitButton = document.getElementById('submitform');
        submitButton.setAttribute('data-option', 'delete');
        submitButton.innerHTML = "<i class='bi bi-trash'></i>";
        submitButton.style.visibility = 'visible';
        createNewTaskButton.style.visibility = 'hidden';
    }
    toggle_CreateNewTaskButton(false);
    $('#jobModalWindow').modal('show');
}
function getShareLinkJob(jobId){
    let queryParams = new URLSearchParams({
    id: jobId,
    openModal :true,
  }).toString();
  var newUrl = `${window.location.origin}${window.location.pathname}?${queryParams.toString()}`;
    navigator.clipboard.writeText(newUrl)
    .then(() => alert('Copied to clipboard!'))
}
function createJob(){
    const form = document.getElementById('jobForm');
    let updateData  =   {
        id          :   document.getElementById('idField').value,
        courierId   :   document.getElementById('courierIdField').value,
        status_id    :   document.getElementById('statusIdField').value,
        billingClientId    :   document.getElementById('clientIdField').value,
        common_date : document.getElementById('jobDateField').value,
        isJobCreationFromIndexPage : true,
    }
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(form.action, { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json', 
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(updateData)
    })
    .then(response => {
        return response.json();
    })
    .then(data => { 
        if(data.errors){
            let errorsMessage = '';
            for (const key in data.errors) {
                if (data.errors.hasOwnProperty(key)) {
                    errorsMessage+=(`${data.errors[key]}\n`);
                    if (key === "common_date") {
                        const jobDateField = document.getElementById('jobDateField');
                        jobDateField.classList.add('border', 'border-danger');
                        setTimeout(() => {
                            jobDateField.classList.remove('border', 'border-danger');
                        }, 3000);
                    }

                }
            }
        }else{
            let submitButton = document.getElementById('submitform');
            submitButton.setAttribute('data-option', 'update');
            submitButton.innerHTML = "<i class='bi bi-pen'></i>";
        }
        document.getElementById('idField').value = data.job.id;
        document.getElementById('jobid').value = data.job.id;
    })
    .catch(error => {
        console.error('Error:', error.message);
    });
}
function fetchJobs(page = 1, url) {
  const id = document.getElementById('search-id').value;
  const clientName = document.getElementById('search-clientName').value;
  const date = document.getElementById('search-date').value;
  const pakuote = document.getElementById('search-package').value;
  const dropOffSearchFields = document.getElementById('dropOffSearchFields').selectedOptions;
  const sortField = document.querySelector('.sort-btn.active')?.dataset.sortField || 'id';
  const sortOrder = document.querySelector('.sort-btn.active')?.dataset.sortOrder || 'asc';
  const startDate = document.getElementById("reportrange").getAttribute('data-start') || '';
  const endDate = document.getElementById("reportrange").getAttribute('data-end') || '';
  const statesSelected = document.getElementById('search-status').selectedOptions;


    let params = [];
    if (id) params.push(`id=${encodeURIComponent(id)}`);
    if (clientName) params.push(`clientName=${encodeURIComponent(clientName)}`);
    if (date) params.push(`date=${encodeURIComponent(date)}`);
    if (startDate) params.push(`startDate=${encodeURIComponent(startDate)}`);
    if (endDate) params.push(`endDate=${encodeURIComponent(endDate)}`);
    if (pakuote) params.push(`package=${encodeURIComponent(pakuote)}`);
    if (sortField) params.push(`sortField=${encodeURIComponent(sortField)}`);
    if (sortOrder) params.push(`sortOrder=${encodeURIComponent(sortOrder)}`);
    if (page) params.push(`page=${encodeURIComponent(page)}`);
    if(dropOffSearchFields){
        Array.from(dropOffSearchFields).forEach(option => {
            params.push(`dOsp[]=${encodeURIComponent(option.value)}`);
        });
    }
    Array.from(statesSelected).forEach(option => {
        params.push(`status[]=${encodeURIComponent(option.value)}`);
    });
    var queryParams = params.join('&');
    var newUrl = `${window.location.pathname}?${queryParams.toString()}`;
    history.replaceState({}, '', newUrl);


  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const xhr = new XMLHttpRequest();
  let statusParams = [];
    Array.from(statesSelected).forEach(option => {
    statusParams.push(`status[]=${encodeURIComponent(option.value)}`);
    });
  let dropOffSearchFieldsParams = [];
  Array.from(dropOffSearchFields).forEach(option => {
    dropOffSearchFieldsParams.push(`dOsp[]=${encodeURIComponent(option.value)}`);
});
console.log(dropOffSearchFieldsParams);
  if(url){
    xhr.open('GET', url, true);
  }else{
    xhr.open('GET', window.ROUTES.WEB.JOB.FETCH+`?id=${id}&clientName=${clientName}
        &${statusParams.join('&').toString()}
        &date=${date}&startDate=${startDate}&endDate=${endDate}&package=${pakuote}&sortField=${sortField}
        &sortOrder=${sortOrder}&page=${page}
        &${dropOffSearchFieldsParams.join('&').toString()}
        `, true);
  }
  
  xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

  xhr.onload = function() {
      if (xhr.status === 200) {
          const data = JSON.parse(xhr.responseText);
          document.getElementById('jobsTableBody').innerHTML = '';
          data.jobs.forEach(job => {
              let packageCounter = 1;
              let isAddressSameAsClientAdress = job.pickup.isAddressSameAsClientAdress;
              let addressNameToDisplay = isAddressSameAsClientAdress 
                                          ? (job.clientToBill.shortenedName !=='') 
                                          ? job.clientToBill.shortenedName+' '+job.clientToBill.pickup_postal_code
                                              :job.clientToBill.name
                                          : job.pickup.namdeOfAddress;
            let row = document.createElement('tr');
            let columnForId =  document.createElement('td');
            columnForId.textContent = job.id;
            row.appendChild(columnForId);
            
            let columnForStatus =  document.createElement('td');
            columnForStatus.textContent = job.status.name;
            row.appendChild(columnForStatus);

            let columnForLogoAndClientName =  document.createElement('td');
            columnForLogoAndClientName.className = 'no-padding';
            let img = document.createElement('img');
            img.src = job.urlToLogo;
            img.style.maxWidth = '2rem';
            img.style.height = 'auto';
            let span_linkToClient = document.createElement('span');
            span_linkToClient.textContent = job.clientName;
            span_linkToClient.className = 'span-toClientShow';
            span_linkToClient.dataset.clientid = job.clientToBill.id;
            span_linkToClient.style.cursor = 'pointer';
            columnForLogoAndClientName.appendChild(img);
            columnForLogoAndClientName.appendChild(span_linkToClient);
            row.appendChild(columnForLogoAndClientName);
            let columnForDate =  document.createElement('td');
            columnForDate.textContent = job.date;
            row.appendChild(columnForDate);
            let columnForAddress =  document.createElement('td');
            let div = document.createElement('div');
            let spanAddressName = document.createElement('span');
            spanAddressName.textContent = addressNameToDisplay;
            let spanInfoIcon = document.createElement('span');
            spanInfoIcon.className = 'info-icon';
            let i = document.createElement('i');
            i.className = 'bi bi-info-circle-fill';
            let spanTooltip = document.createElement('span');
            spanTooltip.className = 'tooltip';
            spanTooltip.textContent = job.pickup.fullAddress;
            spanInfoIcon.appendChild(i);
            spanInfoIcon.appendChild(spanTooltip);
            div.appendChild(spanAddressName);
            div.appendChild(spanInfoIcon);
            columnForAddress.appendChild(div);
            row.appendChild(columnForAddress);
            let columnForDropOffs =  document.createElement('td');
            job.tasks.forEach(task => {
                if(task.package){
                    let div = document.createElement('div');
                    div.className = 'row';
                    let div2 = document.createElement('div');
                    div2.className = 'col';
                    let blockquote = document.createElement('blockquote');
                    blockquote.className = 'blockquote border';
                    let h6 = document.createElement('h6');
                    h6.textContent = `Package No [${packageCounter++}]`;
                    let p = document.createElement('p');
                    p.className = 'mb-0';
                    p.textContent = task.package.dropoff_name;
                    if(job.hasReturn){
                        let i = document.createElement('i');
                        i.className = 'bi bi-arrow-counterclockwise';
                        i.style.color = '#00DD00';
                        p.appendChild(i);
                    }
                    let footer = document.createElement('footer');
                    footer.className = 'blockquote-footer';
                    let cite = document.createElement('cite');
                    cite.title = 'Source Title';
                    cite.textContent = task.package.dropoff_adress_line+task.package.dropoff_postal_code;
                    footer.appendChild(cite);
                    blockquote.appendChild(h6);
                    blockquote.appendChild(p);
                    blockquote.appendChild(footer);
                    div2.appendChild(blockquote);
                    div.appendChild(div2);
                    columnForDropOffs.appendChild(div);
                }
            });
            row.appendChild(columnForDropOffs);
            let columnForPrice =  document.createElement('td');
            let span1 = document.createElement('span');
            span1.textContent = '£';
            let span2 = document.createElement('span');
            span2.textContent = parseFloat(job.price/100,2);
            columnForPrice.appendChild(span1);
            columnForPrice.appendChild(span2);
            row.appendChild(columnForPrice);
            let columnForActions =  document.createElement('td');
            let jobViewButton   =   document.createElement('button');
            jobViewButton.className = 'btn btn-success view-btn job-view-btn';
            jobViewButton.dataset.jobid = job.id;
            jobViewButton.innerHTML = '<i class="bi bi-eye"></i>';
            let jobEditButton   =   document.createElement('button');
            jobEditButton.className = 'btn btn-primary edit-btn job-edit-btn';
            jobEditButton.dataset.jobid = job.id;
            jobEditButton.innerHTML = '<i class="bi bi-pen"></i>';
            let jobDeleteButton   =   document.createElement('button');
            jobDeleteButton.className = 'btn btn-danger delete-btn job-delete-btn';
            jobDeleteButton.dataset.jobid = job.id;
            jobDeleteButton.innerHTML = '<i class="bi bi-trash"></i>';
            let jobCopyButton   =   document.createElement('button');
            jobCopyButton.className = 'btn btn-info copy-btn job-copy-btn';
            jobCopyButton.dataset.jobid = job.id;
            jobCopyButton.innerHTML = '<i class="fa-solid fa-copy"></i>';
            let jobShareLinkButton   =   document.createElement('button');
            jobShareLinkButton.className = 'btn btn-info sharelink-btn job-sharelink-btn';
            jobShareLinkButton.dataset.jobid = job.id;
            jobShareLinkButton.innerHTML = '<i class="fa fa-share-alt" aria-hidden="true"></i>';
            columnForActions.appendChild(jobViewButton);
            columnForActions.appendChild(jobEditButton);
            columnForActions.appendChild(jobDeleteButton);
            columnForActions.appendChild(jobCopyButton);
            columnForActions.appendChild(jobShareLinkButton);
            row.appendChild(columnForActions);                   
            document.getElementById('jobsTableBody').appendChild(row);
            addEventListenerToCopyJobButton_click(jobCopyButton);
            addEventListenerToDeleteJobButton_click(jobDeleteButton);
            addEventListenerToEditJobButton_click(jobEditButton);
            addEventListenerToViewJobButton_click(jobViewButton);
            addEventListenerToShareLinkJobButton_click(jobShareLinkButton);
            addEventListenerToViewClientButton_click(span_linkToClient);
          });
          let botoomPagination = document.getElementById('paginationLinks_bottom');
          let topPagination = document.getElementById('paginationLinks_top');
          botoomPagination.innerHTML = data.links;
          topPagination.innerHTML = data.links;
          let paginationLinks = botoomPagination.querySelectorAll('a');
          paginationLinks = [...paginationLinks, ...topPagination.querySelectorAll('a')];
          paginationLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();

                    const urlObj = new URL(event.target.href);
                    const pageParam = urlObj.searchParams.get('page');
                    fetchJobs(pageParam, event.target.href);
                });
          });
                
      }
  };
  xhr.send();
}
function addEventListenerToSortButton(button){
  button.addEventListener('click', function() {
      const icon  =   button.querySelector('i');
      if (icon.classList.contains('fa-up-down')) {
          icon.classList.remove('fa-up-down');
          icon.classList.add('fa-up-long');
          button.dataset.sortOrder = 'asc';
      } else if(icon.classList.contains('fa-up-long')){
          icon.classList.remove('fa-up-long');
          icon.classList.add('fa-down-long');
          button.dataset.sortOrder = 'desc';
      } else {
          icon.classList.remove('fa-down-long');
          icon.classList.add('fa-up-long');
          button.dataset.sortOrder = 'asc';
      } 
      document.querySelectorAll('.sort-btn').forEach(btn => {
          if(btn.id === button.id){
              btn.classList.add('active');
          }else{
              btn.classList.remove('active');
              btn.dataset.sortOrder = 'asc';
              btn.querySelector('i').className   =   btn.querySelector('i').getAttribute('data-default-class');
          }
      });
      fetchJobs();
  });
}
const searchInputs = [
  { id: 'search-id'},
  { id: 'search-clientName'},
  { id: 'search-date'},
  { id: 'search-package'},
  { id: 'search-status'},
  { id: 'dropOffSearchFields'},
];
let searchTimeout;
searchInputs.forEach(input => {
    const inputElement = document.getElementById(input.id);
  
    inputElement.addEventListener('blur', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchJobs();
        }, 300);
    });
});
const sortButtons = [
    { id: 'button-sort-clientName'},
    { id: 'button-sort-date'},
    { id: 'button-sort-id'},
    { id: 'button-sort-status'}
];
sortButtons.forEach(input => {
    const inputElement = document.getElementById(input.id);
    addEventListenerToSortButton(inputElement);
});

var global_typeOfButtonClickedToOpenJobModal = 'create';

var global_taskWindow_defaultValue_time_pickup  = [ '08:00', '16:00'];
var global_taskWindow_defaultValue_time_dropoff = [ '08:00', '17:00'];
var global_taskWindow_defaultValue_time_return  = [ '08:00', '17:00'];


function addEventListenerToTasksCreationButtons(button){
    button.addEventListener('click', (e) => {
        let taskTypeField = document.getElementById('taskTypeField');
        let container_return = document.getElementById('return-info');
        if(document.getElementById('jobid').value == ''){
            createJob();
        }
        switch(button.id){
            case 'createNewPickup':
                setReadOnlyToFieldsOfTaskModal(false);                    
                cleanTaskCreateWindow('pickup');
                document.getElementById('taskIdField').disabled = true;
                taskTypeField.selectedIndex = 0;
                taskTypeField.disabled = true;
                container_return.style.visibility = 'hidden';
                document.getElementById('submitTaskform').setAttribute('data-option', 'create');
                break;
            case 'createNewDropOff':
                setReadOnlyToFieldsOfTaskModal(false);
                document.getElementById('taskIdField').disabled = true;
                cleanTaskCreateWindow('dropOff');
                taskTypeField.selectedIndex = 1;
                taskTypeField.disabled = true;
                container_return.style.visibility = 'hidden';
                document.getElementById('submitTaskform').setAttribute('data-option', 'create');
                break;
            case 'createNewReturn':
                setReadOnlyToFieldsOfTaskModal(false);
                document.getElementById('taskIdField').disabled = true;
                cleanTaskCreateWindow('return');
                taskTypeField.selectedIndex = 2;
                taskTypeField.disabled = true;
                container_return.style.visibility = 'visible';
                document.getElementById('submitTaskform').setAttribute('data-option', 'create');
                break;
        }
        if(taskTypeField.value === 'dropOff'){
            addInfoAboutPackageToTaskModal();
        }else{
            const container =   document.getElementById('package-info');
            container.innerHTML = '';
        }
        document.getElementById('submitTaskform').disabled = false;
        $('#taskModalWindow').modal('show');
    });
}
function showPackageDiv(status){
    const container =   document.getElementById('package-info');
    if(status){
        container.style.visibility = 'visible';
    }else{
        container.style.visibility = 'hidden';
    }
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
function addEventListenerToButton(button){
    button.addEventListener('click', (e) => {
        e.preventDefault();
        let submitButton = document.getElementById('submitTaskform');
        if(button.id === 'createNewTask'){
            setReadOnlyToFieldsOfTaskModal(false);
            document.getElementById('taskIdField').disabled = true;
            cleanTaskCreateWindow();
            button.setAttribute('data-option', 'create');            
            submitButton.setAttribute('data-option', 'create');
            submitButton.textContent  = 'Confirm creation';
            let submitFormInnerHTML = document.getElementById('submitform').innerHTML;
            if(submitFormInnerHTML == `<i class="bi bi-save"></i>`){
                createJob();
            }
        }else{
            let taskId  =   parseInt(button.id.match(/task-(\d+)-button/)[1],10);
            setTaskValues(taskId).then(() =>{
            if(button.id.match(/task-(\d+)-button-edit/)){
                setReadOnlyToFieldsOfTaskModal(false);
                button.setAttribute('data-option', 'edit');            
                submitButton.setAttribute('data-option', 'update');
                submitButton.textContent  = 'Confirm edit';
            }
            if(button.id.match(/task-(\d+)-button-delete/)){
                setReadOnlyToFieldsOfTaskModal(true);
                button.setAttribute('data-option', 'delete');
                submitButton.setAttribute('data-option', 'delete');
                submitButton.textContent  = 'Confirm delete';
            }
            if(button.id.match(/task-(\d+)-button-view/)){
                setReadOnlyToFieldsOfTaskModal(true);
                button.setAttribute('data-option', 'view');
                submitButton.setAttribute('data-option', 'view');
                submitButton.textContent  = 'Confirm view';
            }
            });
            document.getElementById('submitTaskform').disabled = false;
        }
        $('#taskModalWindow').modal('show');
    });
}
function setPackageWeightChoosingAbility(selectedValue){
    const routeUrl = window.ROUTES.WEB.PACKAGETYPE.GETINFO.replace(':id', selectedValue);
        fetch(routeUrl)
                .then(response => response.json())
                .then(data => {
                    let inputWeight = document.getElementById('weightInput');
                    let labelForWeight = document.getElementById('labelForWeightInput');
                    let label = document.getElementById('weightInputLabel');

                    if (data.extras && data.extras.length > 0) {
                        const hasWeight = data.extras.some(extra => extra.name === 'weight');
                        if (hasWeight) {
                            inputWeight.removeAttribute('style');
                            //inputWeight.value = 0;
                            labelForWeight.removeAttribute('style');
                            label.removeAttribute('style');
                        }else{
                            inputWeight.style.display = 'none';
                            labelForWeight.style.display = 'none';
                            label.style.display = 'none';
                            //inputWeight.value = 0;
                        }
                    }else{
                        inputWeight.style.display = 'none';
                        labelForWeight.style.display = 'none';
                        label.style.display = 'none';
                        //inputWeight.value = 0;
                    }
                })
                .catch(error => {
                    console.error('Error fetching package type info:', error);
                });
}
function addInfoAboutPackageToTaskModal(pakuote){  
    if(!pakuote){
        pakuote = {
                type: {
                id: 1, 
                },
                quantity: 1, 
                weight:0,
        };
    } 
    const container =   document.getElementById('package-info');
    container.innerHTML = '';
    const select = document.createElement('select');
    const clientIdField =   document.getElementById('clientIdField');
    const routeUrl = window.ROUTES.WEB.CLIENT.GETINFO.replace(':id', clientIdField.value);
    return fetch(routeUrl)
        .then(response => response.json())
        .then(data => {
            if(data.packageTypes !== 'none'){
                
                const select = document.createElement('select');
                select.id = 'packageTypeSelect';
                container.appendChild(select);
                data.packageTypes.forEach(packageType => {
                    const option = document.createElement('option');
                    option.value = packageType.id;
                    option.text = packageType.name;
                    select.appendChild(option);
                    if(option.value == pakuote.type.id){
                        option.selected = true;
                    }
                });
                const inputQuantity = document.createElement('input');
                inputQuantity.type = 'number';
                inputQuantity.id = 'quantityInput';
                inputQuantity.min = '1';
                inputQuantity.placeholder = 'Enter quantity';
                inputQuantity.value = pakuote.quantity;
                container.appendChild(inputQuantity);

                const label = document.createElement('label');
                label.setAttribute('for', 'weight');
                label.setAttribute('id', 'weightInputLabel');
                label.textContent = 'Weight:';
                container.appendChild(label);

                const inputWeight = document.createElement('input');
                
                inputWeight.setAttribute('type', 'number');        
                inputWeight.setAttribute('id', 'weightInput');          
                inputWeight.setAttribute('name', 'weight');        
                //inputWeight.setAttribute('min', '0');              
                //inputWeight.setAttribute('max', '500');            
                inputWeight.setAttribute('step', '0.1');           
                inputWeight.setAttribute('placeholder', 'Enter weight'); 
                inputWeight.setAttribute('required', '');
                inputWeight.setAttribute('value', pakuote.weight);
                inputWeight.innerHTML = pakuote.weight;          
                container.appendChild(inputWeight);
                const labelForWeight = document.createElement('span');
                labelForWeight.setAttribute('id', 'labelForWeightInput');
                labelForWeight.textContent = 'Kg';
                container.appendChild(labelForWeight);
                let submitButton = document.getElementById('submitTaskform');
                if(submitButton.getAttribute('data-option') === 'update'){
                    document.getElementById('packageTypeSelect').disabled = false;
                    document.getElementById('quantityInput').disabled = false;
                    inputWeight.disabled = false;
                }else if(submitButton.getAttribute('data-option') === 'create'){
                    document.getElementById('packageTypeSelect').disabled = false;
                    document.getElementById('quantityInput').disabled = false;
                    inputWeight.disabled = false;
                }else{
                    document.getElementById('packageTypeSelect').disabled = true;
                    document.getElementById('quantityInput').disabled = true;
                    inputWeight.disabled = true;
                }
                //inputWeight.style.display = 'none';
                //labelForWeight.style.display = 'none';
                //label.style.display = 'none';
            }
            
        }).then(()=>{
            let selectElement = document.getElementById('packageTypeSelect');

            setPackageWeightChoosingAbility(selectElement.value);
            selectElement.addEventListener('change', function(event) {
                const selectedValue = event.target.value;
                setPackageWeightChoosingAbility(selectedValue);
            });
        })
        .catch(error => {
            console.error(error);
    });
}
function appendButtonsToTaskRowColumn(task, buttonClicked) {
    
    let container = document.createElement('div');
    container.className = 'd-flex flex-column flex-grow-1';

    let viewButton = document.createElement('button');
    viewButton.className = 'btn btn-success';
    viewButton.style.flex = '1 1 0';
    viewButton.textContent = 'View';
    viewButton.id = `task-${task.id}-button-view`;
    container.appendChild(viewButton);

    if (buttonClicked === 'edit' || buttonClicked === 'create') {
        let editButton = document.createElement('button');
        editButton.className = 'btn btn-primary';
        editButton.style.flex = '1 1 0';
        editButton.textContent = 'Edit';
        editButton.id = `task-${task.id}-button-edit`;
        container.appendChild(editButton);

        let deleteButton = document.createElement('button');
        deleteButton.className = 'btn btn-danger';
        deleteButton.style.flex = '1 1 0';
        deleteButton.textContent = 'Delete';
        deleteButton.id = `task-${task.id}-button-delete`;
        container.appendChild(deleteButton);
    } else if (buttonClicked === 'delete') {
        let deleteButton = document.createElement('button');
        deleteButton.className = 'btn btn-danger';
        deleteButton.style.flex = '1 1 0';
        deleteButton.textContent = 'Delete';
        deleteButton.id = `task-${task.id}-button-delete`;
        container.appendChild(deleteButton);
    }

    return container;
}
function createColumn(idSuffix, content,id) {
    let col = document.createElement('div');
    col.className = 'col border';
    col.id = `container-task-${id}-${idSuffix}`;
    if (content instanceof HTMLElement) {
        col.appendChild(content);
    } else {
        col.textContent = content;
    }
    return col;
}
function formatDateTimeStringTo12HourFormat(dateString) {
            const date = new Date(dateString);
            let hours = date.getHours();
            const minutes = date.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            const minutesStr = minutes < 10 ? '0' + minutes : minutes;
            return hours + ':' + minutesStr + ' ' + ampm;
}
function appendTaskToContainer(container,task,buttonClicked,multiDrop){

    let taskRow = document.createElement('div');
    taskRow.className = 'row';
    taskRow.id = 'container-task-'+task.id;


    taskRow.appendChild(createColumn('controls', appendButtonsToTaskRowColumn(task,buttonClicked,multiDrop),task.id));
    taskRow.appendChild(createColumn('type', task.name,task.id));
    taskRow.appendChild(createColumn('addressName', task.addressName,task.id));
    taskRow.appendChild(createColumn('address', task.shortAddress,task.id));
    taskRow.appendChild(createColumn('timeWindow', formatDateTimeStringTo12HourFormat(task.timeWindow.begin)+' / '+formatDateTimeStringTo12HourFormat(task.timeWindow.end),task.id));
    if(task.name === 'dropoff'){
        taskRow.appendChild(createColumn('quantity', task.quantity+' * '+task.packageType,task.id));
    }
    container.appendChild(taskRow);
    if(document.getElementById(`task-${task.id}-button-view`)){
        addEventListenerToButton(document.getElementById(`task-${task.id}-button-view`));
    }
    if(document.getElementById(`task-${task.id}-button-edit`)){
        addEventListenerToButton(document.getElementById(`task-${task.id}-button-edit`));
    }
    if(document.getElementById(`task-${task.id}-button-delete`)){
        addEventListenerToButton(document.getElementById(`task-${task.id}-button-delete`));   
    }
     
}
function setPickupCreationButtonVisibility(isEnabled){
    const createNewPickupButton = document.getElementById('createNewPickup');
    createNewPickupButton.disabled = !isEnabled;
    if(isEnabled){
        createNewPickupButton.classList.remove('btn-secondary');
        createNewPickupButton.classList.add('btn-primary');
    }else{
        createNewPickupButton.classList.remove('btn-primary');
        createNewPickupButton.classList.add('btn-secondary');
    }
}
function setReturnCretionButtonVisibility(isEnabled){
    const createNewReturnButton = document.getElementById('createNewReturn');
    createNewReturnButton.style.disabled = !isEnabled;
    if(isEnabled){
        createNewReturnButton.classList.remove('btn-secondary');
        createNewReturnButton.classList.add('btn-primary');
    }else{
        createNewReturnButton.classList.remove('btn-primary');
        createNewReturnButton.classList.add('btn-secondary');
    }
}
function swapTaskOrder(taskId1, taskId2) {
    let routeUrl = window.ROUTES.WEB.TASK.SWAP_ORDER;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch(routeUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ origin_id: taskId1, destination_id: taskId2 })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
        } else {
            //console.error('Error swapping task order:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
function updatePriceColumn(){
    const jobId = document.getElementById('idField').value;
    const total_Price_DisplayField = document.getElementById('total_Price_DisplayField');
    const total_distance_DisplayField = document.getElementById('total_distance_DisplayField');
    const total_distance_price_DisplayField = document.getElementById('total_distance_price_DisplayField');
    const routeUrl = window.ROUTES.WEB.JOB.GETINFO.replace(':id', jobId);
    fetch(routeUrl)
        .then(response => response.json())
        .then(data => {
            total_Price_DisplayField.innerHTML = parseFloat(data.price.totalPrice/100);
            total_distance_DisplayField.innerHTML = parseFloat(data.price.price_Distance.value).toFixed(3);
            total_distance_price_DisplayField.innerHTML = parseFloat(data.price.price_Distance.price/100);
        })
        .catch(error => {
            console.error('Error fetching job info:', error);
        }   );
}
function addDropOffArrangeButtons() {

    // const dropOffs = Array.from(document.querySelectorAll('[id^="container-task-"]'))
    //     .filter(el => el.id.includes('-type') && el.innerHTML.trim() === 'dropoff')
    //     .map(el => el.closest('[id^="container-task-"]'));

    const dropOffs = Array.from(document.querySelectorAll('[id^="container-task-"]'))
            .filter(container => {
                const typeElement = container.querySelector('[id$="-type"]');
                return typeElement && typeElement.innerHTML.trim() === 'dropoff';
            });
    dropOffs.forEach((dropOff, index) => {
        //const row = dropOff.parentElement;
        const row = dropOff;
        const controlsColumn = row.querySelector('[id$="-controls"]');
        let divContainer = document.getElementById(`${row.id}-upDownButtonsContainer`);
        
        if (divContainer) {
            divContainer.innerHTML = '';
        } else {
            divContainer = document.createElement('div');
            divContainer.className = 'd-flex flex-column justify-content-between';
            divContainer.style.flex = '0 0 auto';
            divContainer.id = `${row.id}-upDownButtonsContainer`;
            controlsColumn.insertBefore(divContainer, controlsColumn.firstChild);
        }

        const upButton = document.createElement('button');
        upButton.className = 'btn btn-primary';
        upButton.textContent = '↑';
        upButton.style.marginBottom = '5px';
        upButton.disabled = index === 0;

        const downButton = document.createElement('button');
        downButton.className = 'btn btn-primary';
        downButton.textContent = '↓';
        downButton.disabled = index === dropOffs.length - 1;

        if (index === 0) {
            divContainer.appendChild(downButton);
        } else if (index === dropOffs.length - 1) {
            divContainer.appendChild(upButton);
        } else {
            divContainer.appendChild(upButton);
            divContainer.appendChild(downButton);
        }

        upButton.addEventListener('click', (e) => {
            e.preventDefault();
            if (index > 0) {
                const currentRow = dropOffs[index];
                const previousRow = dropOffs[index - 1];
                swapWithAnimation(currentRow, previousRow);
            }
        });

        downButton.addEventListener('click', (e) => {
            e.preventDefault();
            if (index < dropOffs.length - 1) {
                const currentRow = dropOffs[index];
                const nextRow = dropOffs[index + 1];
                swapWithAnimation(nextRow,currentRow ); // move current below next
            }
        });
    });

}

function swapWithAnimation(rowA, rowB) {
    const parent = rowA.parentElement;
    parent.insertBefore(rowA, rowB);

    const idA = rowA.id.split('-')[2];
    const idB = rowB.id.split('-')[2];
    swapTaskOrder(idA, idB);
    const boundsA = rowA.getBoundingClientRect();
    const boundsB = rowB.getBoundingClientRect();
    const dxA = boundsB.left - boundsA.left;
    const dyA = boundsB.top - boundsA.top;
    const dxB = boundsA.left - boundsB.left;
    const dyB = boundsA.top - boundsB.top;
    // Animate rowA to its new position
    rowB.animate([
    {
        transform: `translate(${dxB}px, ${dyB}px) scale(1)`, 
        offset: 0
    },
    {
        transform: `translate(${dxB / 2}px, ${dyB / 2}px) scale(0.7)`, 
        offset: 0.5
    },
    {
        transform: `translate(0, 0) scale(1)`, 
        offset: 1
    }
    ], {
    duration: 1300,
    easing: 'ease'
    });

    rowA.animate([
                { transform: `translate(${dxA}px, ${dyA}px)` },
                { transform: 'translate(0, 0)' }
            ], {
                duration: 1300,
                easing: 'ease'
            });
    addDropOffArrangeButtons();
    updatePriceColumn();
}

function setJobValues(jobId,buttonClicked){
    if(jobId === 0){return;}
    const courierIdField    =   document.getElementById('courierIdField');
    const statusIdField    =   document.getElementById('statusIdField');
    const clientSearchField =   document.getElementById('clientSearchField');
    const clientIdField =   document.getElementById('clientIdField');
    const jobDateField =   document.getElementById('jobDateField');
    const containerTasks =   document.getElementById('container-tasks');

    const price_total_field         =   document.getElementById('total_Price_DisplayField');
    const distance_total_field      =   document.getElementById('total_distance_DisplayField');
    const price_distance_field      =   document.getElementById('total_distance_price_DisplayField');
    const price_weight_field        =   document.getElementById('total_weight_price_DisplayField');
    const addon_package_oversize_price_DisplayField = document.getElementById('addon_package_oversize_price_DisplayField');
    const addon_package_food_price_DisplayField = document.getElementById('addon_package_food_price_DisplayField');
    const weight_total_field        =   document.getElementById('total_weight_DisplayField');
    const price_postalCode_field    =   document.getElementById('total_outsideZone_price_DisplayField');
    const total_timing_price_DisplayField   =   document.getElementById('total_timing_price_DisplayField');
    const addon_time_sunday_price_DisplayField   =   document.getElementById('addon_time_sunday_price_DisplayField');
    const addon_time_bankholiday_price_DisplayField   =   document.getElementById('addon_time_bankholiday_price_DisplayField');
    const pickup_timing_price_DisplayField   =   document.getElementById('pickup_timing_price_DisplayField');
    const pickup_timing_value_DisplayField  =   document.getElementById('pickup_timing_value_DisplayField');
    const dropoff_timing_price_DisplayField   =   document.getElementById('dropoff_timing_price_DisplayField');
    const price_magicNumber_DisplayField = document.getElementById('price_magicNumber_DisplayField');
    const dropOff_timing_value_DisplayField  =   document.getElementById('dropoff_timing_value_DisplayField');
    const packages_price_base_DisplayField = document.getElementById('packages_price_base_DisplayField');
    const addon_time_samedayreturn_price_DisplayField = document.getElementById('addon_time_samedayreturn_price_DisplayField');
    const jobNoteField = document.getElementById('jobNoteField');

    const routeUrl = window.ROUTES.WEB.JOB.GETINFO.replace(':id', jobId);
    containerTasks.innerHTML = ""; 
    fetch(routeUrl)
        .then(response => response.json())
        .then(data => {
            if(data.courierId === 'none'){
                courierIdField.value = 0;
            }else{
                courierIdField.value = data.courierId;
            }
            if(data.statusId === 'none'){
                statusIdField.value = 0;
            }else{
                statusIdField.value = data.statusId;
            }
            if(data.clientId === 'none'){
                clientSearchField.value = 'none';
                //clientIdField = 'none';
            }else{
                clientSearchField.value =   data.clientName;
                clientIdField.value     =   data.clientId;
            }
            jobDateField.value = data.date;
            let multiDrop = data.dropoffs.length > 1;
            data.tasks.forEach(function(task){
                appendTaskToContainer(containerTasks,task,buttonClicked,multiDrop);
            });
            if(multiDrop){
                let dropOffs = Array.from(document.querySelectorAll('[id^="container-task-"]'))
                                    .filter(element => element.id.includes('-type') && element.innerHTML.trim() === 'dropoff');
                addDropOffArrangeButtons(jobId,buttonClicked);
            }
            jobNoteField.innerHTML = data.note;
            jobNoteField.value = data.note;
            price_total_field.innerHTML = parseFloat(data.price.totalPrice/100);
            distance_total_field.innerHTML = parseFloat(data.price.price_Distance.value).toFixed(3);
            price_distance_field.innerHTML = parseFloat(data.price.price_Distance.price/100);
            price_weight_field.innerHTML = parseFloat(data.price.weight_price.price/100);
            weight_total_field.innerHTML = parseFloat(data.price.weight_price.value).toFixed(3);
            price_postalCode_field.innerHTML = parseFloat(data.price.price_OutOfZone/100,2);
            total_timing_price_DisplayField.innerHTML = parseFloat(data.price.timing_price.price/100,2);
            addon_time_sunday_price_DisplayField.innerHTML = parseFloat(data.price.price_time_sunday.price/100,2);
            addon_time_bankholiday_price_DisplayField.innerHTML = parseFloat(data.price.price_time_bankholiday.price/100,2);
            addon_package_oversize_price_DisplayField.innerHTML = parseFloat(data.price.breakdownOfPrice.oversizePrice/100,2);
            addon_package_food_price_DisplayField.innerHTML = parseFloat(data.price.breakdownOfPrice.price_food/100,2);
            addon_time_samedayreturn_price_DisplayField.innerHTML = parseFloat(data.price.breakdownOfPrice.price_sameDayReturn.price/100,2);
            pickup_timing_price_DisplayField.innerHTML = parseFloat(data.price.timing_price.pickup_price/100,2);
            pickup_timing_value_DisplayField.innerHTML = formatMinutesToHoursAndMinutes(data.price.timing_price.pickup_value);
            price_magicNumber_DisplayField.innerHTML = parseFloat(data.price.breakdownOfPrice.price_adjustment_number/100,2);
            dropOff_timing_value_DisplayField.innerHTM='';
            let string = '';
            data.price.timing_price.dropOff_value.forEach(function(value){
                string+= '<span style="color: green;">'+formatMinutesToHoursAndMinutes(value)+'</span><br>';
            });
            packages_price_base_DisplayField.innerHTML = "";
            data.dropoffs.forEach(function(dropoff){
                packages_price_base_DisplayField.innerHTML += '<span style="color: green";><span>Package base price : </span><span>&#163;'+parseFloat(dropoff.packageType_price/100,2)+'</span></span><br>';
            });
            dropOff_timing_value_DisplayField.innerHTML=string;
            string = '';
            dropoff_timing_price_DisplayField.innerHTML = parseFloat(data.price.timing_price.dropOff_price/100,2);
            setPickupCreationButtonVisibility(data.pickup === 'none');
            setReturnCretionButtonVisibility(data.return === 'none');              
        })
        .catch(error => {
            console.error(error);
        });
}
function formatMinutesToHoursAndMinutes(minutes) {

                const hours = Math.floor(minutes / 60);
                const remainingMinutes = minutes % 60;
                if (hours > 0) {
                    if (remainingMinutes > 0) {
                        return `${hours}h ${remainingMinutes}min`;
                    } else {
                        return `${hours}h`;
                    }
                } else {
                    return `${remainingMinutes}min`;
                }
            }
function addTypeHeadSearch(searchInput){
    if (searchInput.length > 0) {
        searchInput.typeahead({
        source: function(query, process) {
            var apiUrl = window.ROUTES.WEB.CLIENT.SEARCH+"?query=" + query;
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
            const clientInfoUrlTemplate = window.ROUTES.WEB.CLIENT.GETINFO;
            const clientInfoUrl = clientInfoUrlTemplate.replace(':id', item.id);
            fetch(clientInfoUrl)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    document.getElementById('clientIdField').value = data.id;       
                }
            })
            .catch(error => {
                console.error(error);
            });
        }
    });
    }
}

function copyJob(jobId){
    document.getElementById('jobIdToCopy').value = jobId;
    $('#jobCopyModalWindow').modal('show');
}
function addEventListenerToCopyJobButton_click(button){
    button.addEventListener('click', (e) => {
        e.preventDefault();
        const jobId = button.getAttribute('data-jobid');
        copyJob(jobId);
    });
}
function addEventListenerToDeleteJobButton_click(button){
    button.addEventListener('click', (e) => {
        e.preventDefault();
        const jobId = button.getAttribute('data-jobid');
        deleteJob(jobId)
    });
}
function addEventListenerToEditJobButton_click(button){
    button.addEventListener('click', (e) => {
        e.preventDefault();
        const jobId = button.getAttribute('data-jobid');
        editJob(jobId)
    });
}
function addEventListenerToViewJobButton_click(button){
    button.addEventListener('click', (e) => {
        e.preventDefault();
        const jobId = button.getAttribute('data-jobid');
        viewJob(jobId)
    });
}
function addEventListenerToShareLinkJobButton_click(button){
    button.addEventListener('click', (e) => {
        e.preventDefault();
        const jobId = button.getAttribute('data-jobid');
        getShareLinkJob(jobId);
    });
}
function addEventListenerToViewClientButton_click(element){
    element.addEventListener('click', (e) => {
        e.preventDefault();
        $('#clientModalWindow').modal('show');
        fillClientViewForm(element.getAttribute('data-clientid'));
    });
}
function toggle_CreateNewTaskButton(enable = true){
    let createNewTaskButton = document.getElementById('createNewTask');
    let createNewPickupButton = document.getElementById('createNewPickup');
    let createNewDropOffButton = document.getElementById('createNewDropOff');
    let createNewReturnButton = document.getElementById('createNewReturn');
    if(enable){
        createNewTaskButton.classList.remove('btn-secondary');
        createNewPickupButton.classList.remove('btn-secondary');
        createNewDropOffButton.classList.remove('btn-secondary');
        createNewReturnButton.classList.remove('btn-secondary');
        createNewTaskButton.classList.add('btn-primary');
        createNewPickupButton.classList.add('btn-primary');
        createNewDropOffButton.classList.add('btn-primary');
        createNewReturnButton.classList.add('btn-primary');
    } else {
        createNewTaskButton.classList.remove('btn-primary');
        createNewPickupButton.classList.remove('btn-primary');
        createNewDropOffButton.classList.remove('btn-primary');
        createNewReturnButton.classList.remove('btn-primary');
        createNewTaskButton.classList.add('btn-secondary');
        createNewPickupButton.classList.add('btn-secondary');
        createNewDropOffButton.classList.add('btn-secondary');
        createNewReturnButton.classList.add('btn-secondary');
    }
    createNewTaskButton.disabled = !enable;
    createNewPickupButton.disabled = !enable;
    createNewDropOffButton.disabled = !enable;
    createNewReturnButton.disabled = !enable;
}
function add_CreateNewTaskButtonHidder_EventListener_toInput(input){
    input.addEventListener('input', function() {
        let status = true;
        if(document.getElementById('courierIdField').value == ''){status = false;}
        if(document.getElementById('statusIdField').value == ''){status = false;}
        if(document.getElementById('clientSearchField').value == ''){status = false;}
        if(document.getElementById('jobDateField').value == ''){status = false;}
        if(status){
            toggle_CreateNewTaskButton(true);
        }else{
            toggle_CreateNewTaskButton(false);
        }

    });
}
function checkIf_All_JobCreationFields_HaveInputs(){
    let statusIdField_SelectElement = document.getElementById('statusIdField');
    let courierIdField_SelectElement = document.getElementById('courierIdField');
    let jobDateField = document.getElementById('jobDateField');
    let return_value = true;
    if(statusIdField_SelectElement.value == ''){
        return_value = false;
        return return_value;
    }
    if(courierIdField_SelectElement.value == ''){
        return_value = false;
        return return_value;
    }
    if(jobDateField.value == ''){
        return_value = false;
        return return_value;
    }
    return return_value;
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

function cleanTaskCreateWindow(type = 'none'){
    let selectStatusField =   document.getElementById('taskStatusIdField');
    let selectTypeField =   document.getElementById('taskTypeField');
    let taskClientNameField =   document.getElementById('taskClientNameField');
    let taskPostalCodeField =   document.getElementById('taskPostalCodeField');
    let taskAddressLineField =   document.getElementById('taskAddressLineField');
    let taskTimeBegin =   document.getElementById('taskTimeBegin');
    let taskTimeEnd =   document.getElementById('taskTimeEnd');
    let taskNoteField   =   document.getElementById('taskNoteField');
    let divForTaskFormCrateCollection  = document.getElementById('divForTaskFormCrateCollection');
    selectStatusField.selectedIndex = 0;
    selectTypeField.selectedIndex = -1;
    selectTypeField.disabled = false;
    taskClientNameField.value   =   '';
    taskPostalCodeField.value   =   '';
    taskAddressLineField.value   =   '';
    taskNoteField.value = '';
    divForTaskFormCrateCollection.style.display = 'none';
    switch(type){
        case 'pickup':
            taskTimeBegin.value = global_taskWindow_defaultValue_time_pickup[0];
            taskTimeEnd.value = global_taskWindow_defaultValue_time_pickup[1];
            break;
        case 'dropOff':
            taskTimeBegin.value = global_taskWindow_defaultValue_time_dropoff[0];
            taskTimeEnd.value = global_taskWindow_defaultValue_time_dropoff[1];
            divForTaskFormCrateCollection.style.display = 'block';
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
    const taskNoteField  =   document.getElementById('taskNoteField');
    idField.value = taskId;
    idField.disabled =  true;
    typeField.disabled =  true;
    const routeUrl = window.ROUTES.WEB.TASK.GETINFO.replace(':id', taskId);
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
            taskNoteField.value             =   data.note;
            taskNoteField.innerHTML         =   data.note;
            const dateBegin = new Date(data.time.begin);
            const dateEnd = new Date(data.time.end);
            timeBeginField.value            =   `${String(dateBegin.getUTCHours()).padStart(2, '0')}:${String(dateBegin.getUTCMinutes()).padStart(2, '0')}`;
            timeEndField.value              =   `${String(dateEnd.getUTCHours()).padStart(2, '0')}:${String(dateEnd.getUTCMinutes()).padStart(2, '0')}`;

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
            let container_return = document.getElementById('return-info');
            if(data.returnTask !== 'none'){
                taskTimeDate = document.getElementById('taskTimeDate');
                taskTimeDate.value  = `${dateBegin.getUTCFullYear()}-${String(dateBegin.getUTCMonth() + 1).padStart(2, '0')}-${String(dateBegin.getUTCDate()).padStart(2, '0')}`;
                
                let checkbox  = document.getElementById('returnTask_isFlexible');
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
function set_Some_JobCreationFields_ToDefaultValues(){
    let statusIdField_SelectElement = document.getElementById('statusIdField');
    let courierIdField_SelectElement = document.getElementById('courierIdField');
    let clientSearchField = document.getElementById('clientSearchField');
    let clientIdField = document.getElementById('clientIdField');
    let jobDateField = document.getElementById('jobDateField');
    statusIdField_SelectElement.value = 10; //10 - unassigned
    courierIdField_SelectElement.value = 0;
    jobDateField.value = "2024-08-19";

}
function repopulateJobRow(jobId,jobRow){
    const routeUrl = window.ROUTES.WEB.JOB.GETINFO.replace(':id', jobId);
    fetch(routeUrl)
        .then(response => response.json())
        .then(job => {
            
            let packageCounter = 1;
            let isAddressSameAsClientAdress = job.pickup.isAddressSameAsClientAddress;
            let addressNameToDisplay = '';            
            if(isAddressSameAsClientAdress){
                if(job.clientToBill.shortenedName !== ''){
                    addressNameToDisplay = job.clientToBill.shortenedName+' '+job.clientToBill.pickup_postal_code;
                }else{
                    addressNameToDisplay = job.clientToBill.name;
                }
            }else{
                addressNameToDisplay = job.pickup.nameOfAddress;
            }                
            let row = document.createElement('tr');
            row.id = 'jobTableRow_' + jobId;
            let columnForId =  document.createElement('td');
            columnForId.textContent = job.id;
            row.appendChild(columnForId);
            
            let columnForLogoAndClientName =  document.createElement('td');
            columnForLogoAndClientName.className = 'no-padding';
            let img = document.createElement('img');
            img.src = job.urlToLogo;
            img.style.maxWidth = '2rem';
            img.style.height = 'auto';
            let span = document.createElement('span');
            span.textContent = job.clientName;
            columnForLogoAndClientName.appendChild(img);
            columnForLogoAndClientName.appendChild(span);
            row.appendChild(columnForLogoAndClientName);
            let columnForDate =  document.createElement('td');
            columnForDate.textContent = job.date;
            row.appendChild(columnForDate);
            let columnForAddress =  document.createElement('td');
            let div = document.createElement('div');
            let spanAddressName = document.createElement('span');
            spanAddressName.textContent = addressNameToDisplay;
            let spanInfoIcon = document.createElement('span');
            spanInfoIcon.className = 'info-icon';
            let i = document.createElement('i');
            i.className = 'bi bi-info-circle-fill';
            let spanTooltip = document.createElement('span');
            spanTooltip.className = 'tooltip';
            spanTooltip.textContent = job.pickup.fullAddress;
            spanInfoIcon.appendChild(i);
            spanInfoIcon.appendChild(spanTooltip);
            div.appendChild(spanAddressName);
            div.appendChild(spanInfoIcon);
            columnForAddress.appendChild(div);
            row.appendChild(columnForAddress);
            let columnForDropOffs =  document.createElement('td');
            job.tasks.forEach(task => {
                if(task.package){
                    let div = document.createElement('div');
                    div.className = 'row';
                    let div2 = document.createElement('div');
                    div2.className = 'col';
                    let blockquote = document.createElement('blockquote');
                    blockquote.className = 'blockquote border';
                    let h6 = document.createElement('h6');
                    h6.textContent = `Package No [${packageCounter++}]`;
                    let p = document.createElement('p');
                    p.className = 'mb-0';
                    p.textContent = task.package.dropoff_name;
                    if(job.hasReturn){
                        let i = document.createElement('i');
                        i.className = 'bi bi-arrow-counterclockwise';
                        i.style.color = '#00DD00';
                        p.appendChild(i);
                    }
                    let footer = document.createElement('footer');
                    footer.className = 'blockquote-footer';
                    let cite = document.createElement('cite');
                    cite.title = 'Source Title';
                    cite.textContent = task.package.dropoff_adress_line+task.package.dropoff_postal_code;
                    footer.appendChild(cite);
                    blockquote.appendChild(h6);
                    blockquote.appendChild(p);
                    blockquote.appendChild(footer);
                    div2.appendChild(blockquote);
                    div.appendChild(div2);
                    columnForDropOffs.appendChild(div);
                }
            });
            row.appendChild(columnForDropOffs);
            let columnForPrice =  document.createElement('td');
            let span1 = document.createElement('span');
            span1.textContent = '£';
            let span2 = document.createElement('span');
            span2.textContent = parseFloat(job.price.totalPrice/100,2);
            columnForPrice.appendChild(span1);
            columnForPrice.appendChild(span2);
            row.appendChild(columnForPrice);
            let columnForActions =  document.createElement('td');
            let jobViewButton   =   document.createElement('button');
            jobViewButton.className = 'btn btn-success view-btn job-view-btn';
            jobViewButton.dataset.jobid = job.id;
            jobViewButton.innerHTML = '<i class="bi bi-eye"></i>';
            let jobEditButton   =   document.createElement('button');
            jobEditButton.className = 'btn btn-primary edit-btn job-edit-btn';
            jobEditButton.dataset.jobid = job.id;
            jobEditButton.innerHTML = '<i class="bi bi-pen"></i>';
            let jobDeleteButton   =   document.createElement('button');
            jobDeleteButton.className = 'btn btn-danger delete-btn job-delete-btn';
            jobDeleteButton.dataset.jobid = job.id;
            jobDeleteButton.innerHTML = '<i class="bi bi-trash"></i>';
            let jobCopyButton   =   document.createElement('button');
            jobCopyButton.className = 'btn btn-info copy-btn job-copy-btn';
            jobCopyButton.dataset.jobid = job.id;
            jobCopyButton.innerHTML = '<i class="fa-solid fa-copy"></i>';
            let jobShareLinkButton   =   document.createElement('button');
            jobShareLinkButton.className = 'btn btn-info sharelink-btn job-sharelink-btn';
            jobShareLinkButton.dataset.jobid = job.id;
            jobShareLinkButton.innerHTML = '<i class="fa fa-share-alt" aria-hidden="true"></i>';
            columnForActions.appendChild(jobViewButton);
            columnForActions.appendChild(jobEditButton);
            columnForActions.appendChild(jobDeleteButton);
            columnForActions.appendChild(jobCopyButton);
            columnForActions.appendChild(jobShareLinkButton);
            row.appendChild(columnForActions);                   
            document.getElementById('jobsTableBody').appendChild(row);
            addEventListenerToCopyJobButton_click(jobCopyButton);
            addEventListenerToDeleteJobButton_click(jobDeleteButton);
            addEventListenerToEditJobButton_click(jobEditButton);
            addEventListenerToViewJobButton_click(jobViewButton);
            addEventListenerToShareLinkJobButton_click(jobShareLinkButton);
            jobRow.replaceWith(row);
        })
        .catch(error => {
            console.error('Error fetching job info:', error);
    }   );
    return ;
}
function datepicker_function(start, end) {
    document.querySelector('#reportrange input').value = start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY');
    document.getElementById('reportrange').setAttribute('data-start', start.format('YYYY-MM-DD'));
    document.getElementById('reportrange').setAttribute('data-end', end.format('YYYY-MM-DD'));
    const inputForDatPicker = document.getElementById('search-date-range');
    inputForDatPicker.value = start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD');
    fetchJobs();
}
//=====================================================================
//=====================================================================
//=====================================================================
//=====================================================================
//=====================================================================
//=====================================================================
//=====================================================================
//=====================================================================
//=====================================================================
//=====================================================================
//=====================================================================
//=====================================================================
//=====================================================================
//=====================================================================
//=====================================================================
//=====================================================================
//=====================================================================
//=====================================================================
//=====================================================================
//=====================================================================
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalShowElement-dropOffSearchColumns').addEventListener('click', function (event){

        event.preventDefault();
        const modalEl = document.getElementById('dropOffSearchColumnsModalWindow');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    });
    var initial_datePicker_start = moment().subtract(29, 'days');
    var initial_datePicker_end = moment();
    var datepicker_element = document.getElementById('reportrange');
    const datePicker = new daterangepicker(datepicker_element, {
        startDate: initial_datePicker_start,
        endDate: initial_datePicker_end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'This Week': [moment().startOf('isoWeek'), moment().endOf('isoWeek')],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'This Year': [moment().startOf('year'), moment().endOf('year')],
        }
    }, datepicker_function);
    datepicker_function(initial_datePicker_start, initial_datePicker_end);
    const inputForDatPicker = document.getElementById('search-date-range');
    inputForDatPicker.addEventListener('change', function () {
        const val = inputForDatPicker.value.trim();
        const parts = val.split(/\s*-\s*/);
        if (parts.length === 6) {
            const start = moment(parts[0]+"-"+parts[1]+"-"+parts[2], 'YYYY-MM-DD', true);
            const end = moment(parts[3]+"-"+parts[4]+"-"+parts[5], 'YYYY-MM-DD', true);

            if (start.isValid() && end.isValid()) {
                // Update picker selection manually
                datePicker.setStartDate(start);
                datePicker.setEndDate(end);

                // Trigger the callback
                datepicker_function(start, end);
            }
        }
    });
    const urlParams = new URLSearchParams(window.location.search);
    const jobId_openMoal = urlParams.get('id');
    const openModal_param = urlParams.get('openModal');
    if (jobId_openMoal && openModal_param === 'true') {
        editJob(jobId_openMoal);
    }
    const   jobModalWindowCloseButton   =   document.getElementById('jobModalWindowCloseButton');
    jobModalWindowCloseButton.addEventListener('click', function(event) {
        const jobId = document.getElementById('jobid').value;
        if(jobId){
            const jobRow = document.getElementById('jobTableRow_'+jobId);
            if(jobRow){
                repopulateJobRow(jobId,jobRow);
            }
        };
    });
    document.querySelectorAll('.job-copy-btn').forEach(button => {
        addEventListenerToCopyJobButton_click(button);
    });
    document.querySelectorAll('.job-delete-btn').forEach(button => {
        addEventListenerToDeleteJobButton_click(button);
    });
    document.querySelectorAll('.job-edit-btn').forEach(button => {
        addEventListenerToEditJobButton_click(button);
    });
    document.querySelectorAll('.job-view-btn').forEach(button => {
        addEventListenerToViewJobButton_click(button);
    });
    document.querySelectorAll('.job-sharelink-btn').forEach(button => {
        addEventListenerToShareLinkJobButton_click(button);
    });
    document.querySelectorAll('.span-toClientShow').forEach(element => {
        addEventListenerToViewClientButton_click(element);
    });
    const submitTaskform_button = document.getElementById('submitTaskform');
    if(submitTaskform_button){
        submitTaskform_button.addEventListener('click', function(event) {
            $('#taskModalWindow').modal('hide');
            setJobValues(document.getElementById('idField').value, global_typeOfButtonClickedToOpenJobModal);
        });
    }
    const price_magicNumber_DisplayField = document.getElementById('price_magicNumber_DisplayField');
    const confirmCopyJob    =   document.getElementById('confirmCopyJob');
    const createJobFromClipboard = document.getElementById('createJobFromClipboard');
    const CopyJobClipboard = document.getElementById('CopyJobClipboard');
    createJobFromClipboard.addEventListener('click', async function(event) {
        event.preventDefault();
        try {
            const jobId = document.getElementById('jobIdToCopy').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Wait for clipboard text:
            const clipboardText = await navigator.clipboard.readText();

            const response = await fetch(window.ROUTES.WEB.JOB.STOREFROMSTRING, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ job_string: clipboardText })
            });

            const data = await response.json();

            if (data.success) {
                editJob(data.data.jobId);
            } else {
                console.error('Error copying job:', data);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });

    CopyJobClipboard.addEventListener('click', function(event){
        event.preventDefault();
        const jobId = document.getElementById('jobIdToCopy').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(window.ROUTES.WEB.JOB.GETJOBTOSTRING.replace(':id', jobId), {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            //body: JSON.stringify({id: jobId})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                navigator.clipboard.writeText(data.data.Job_to_json)
                .then(() => alert('Copied to clipboard!'))
                .catch(err => console.error('Failed to copy: ', err));
            } else {
                console.error('Error copying job:', data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
    confirmCopyJob.addEventListener('click',function(event){
        event.preventDefault();
        const jobId = document.getElementById('jobIdToCopy').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(window.ROUTES.WEB.JOB.COPY, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({id: jobId})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#jobCopyModalWindow').modal('hide');
                editJob(data.data.NewJobId);
            } else {
                console.error('Error copying job:', data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
    price_magicNumber_DisplayField.addEventListener('focus', function(event) {
        document.getElementById('magic_number_actions').removeAttribute('style');
    });
    price_magicNumber_DisplayField.addEventListener('blur', function(event) {
        setTimeout(() => {
            if (!document.getElementById('confirmMagicNumber').clicked) {
                document.getElementById('magic_number_actions').style.display = 'none';
                let jobId = document.getElementById('idField').value;
                setJobValues(jobId, global_typeOfButtonClickedToOpenJobModal);
            }
        }, 500);
    });
    document.getElementById('confirmMagicNumber').addEventListener('click', function(event) {
        event.preventDefault();
        const inputValue = parseFloat(document.getElementById('price_magicNumber_DisplayField').textContent);
        if (!isNaN(inputValue)) {
            document.getElementById('price_magicNumber_DisplayField').textContent = inputValue.toFixed(2);
        } else {
            document.getElementById('price_magicNumber_DisplayField').textContent = '0.00';
        }
        const jobId = document.getElementById('idField').value;
        const priceAdjustmentNumber = parseFloat(price_magicNumber_DisplayField.textContent) * 100;

        const updateData = {
            id: jobId,
            price_adjustment_number: priceAdjustmentNumber
        };
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(window.ROUTES.WEB.JOB.UPDATE_PRICEADJUSTMENTNUMBER, {
            method: 'POST',
            headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(updateData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
            document.getElementById('magic_number_actions').style.display = 'none';
            setJobValues(jobId, global_typeOfButtonClickedToOpenJobModal);
            } else {
            console.error('Error updating price adjustment number:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
    document.getElementById('cancelMagicNumber').addEventListener('click', function(event) {
        event.preventDefault();
        document.getElementById('magic_number_actions').style.display = 'none';
        let jobId = document.getElementById('idField').value;
        setJobValues(jobId,global_typeOfButtonClickedToOpenJobModal);
    });


    addTypeHeadSearch($('#clientSearchField'));
    

    addEventListenerToButton(document.getElementById(`createNewTask`));
    addEventListenerToTasksCreationButtons(document.getElementById(`createNewPickup`));
    addEventListenerToTasksCreationButtons(document.getElementById(`createNewReturn`));
    addEventListenerToTasksCreationButtons(document.getElementById(`createNewDropOff`));
    document.getElementById('createJobButton').addEventListener('click', () => {
            const createNewTaskButton   =   document.getElementById('createNewTask');
            createNewTaskButton.style.visibility = 'visible';
            const jobIdField    =   document.getElementById('idField');                
            jobIdField.disabled = true;
            const form = document.querySelector(`#jobForm`);
            if (form) {
                global_typeOfButtonClickedToOpenJobModal = 'create';
                set_Some_JobCreationFields_ToDefaultValues();
                document.getElementById('container-tasks').innerHTML = '';
                document.getElementById('idField').value = '';
                document.getElementById('jobid').value = '';
                document.getElementById('courierIdField').disabled = false;
                document.getElementById('statusIdField').disabled = false;
                document.getElementById('clientSearchField').disabled = false;
                document.getElementById('jobDateField').disabled = false;
                document.getElementById('clientSearchField').value = '';
                document.getElementById('jobDateField').value = ''; 
                form.setAttribute('action', window.ROUTES.WEB.JOB.STORE);
                let submitButton = document.getElementById('submitform');
                submitButton.innerHTML = "<i class='bi bi-save'></i>";
                submitButton.setAttribute('data-option', 'create');
                if(!checkIf_All_JobCreationFields_HaveInputs()){
                    toggle_CreateNewTaskButton(false);
                }else{
                    toggle_CreateNewTaskButton(true);
                }
                add_CreateNewTaskButtonHidder_EventListener_toInput(document.getElementById('courierIdField'));
                add_CreateNewTaskButtonHidder_EventListener_toInput(document.getElementById('statusIdField'));
                add_CreateNewTaskButtonHidder_EventListener_toInput(document.getElementById('clientSearchField'));
                add_CreateNewTaskButtonHidder_EventListener_toInput(document.getElementById('jobDateField'));
            }
            $('#jobModalWindow').modal('show');
    });

    document.getElementById('taskModalWindowCloseButton').addEventListener('click', function() {
        setJobValues(document.getElementById('idField').value, global_typeOfButtonClickedToOpenJobModal);
        $('#taskModalWindow').modal('hide');
        $('#jobModalWindow').modal('show');        
    });
    document.getElementById('submitform').addEventListener('click', function(event) {
        let submitFormInnerHTML = document.getElementById('submitform').innerHTML;
      
        if(submitFormInnerHTML == `<i class="bi bi-save"></i>`){
            createJob();
        }else {

        
            event.preventDefault();
            const form = document.getElementById('jobForm');
            let updateData  =   {
                id          :   document.getElementById('idField').value,
                courierId   :   document.getElementById('courierIdField').value,
                status_id    :   document.getElementById('statusIdField').value,
                clientId    :   document.getElementById('clientIdField').value,
                date        :   document.getElementById('jobDateField').value,
                note        :   document.getElementById('jobNoteField').value,
            }
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(form.action, { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json', 
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(updateData)
            })
            .then(response => {
                return response.json();
            })
            .then(data => {
                if(data.errors){
                    let errorsMessage = '';
                    for (const key in data.errors) {
                        if (data.errors.hasOwnProperty(key)) {
                            errorsMessage+=(`${data.errors[key]}\n`);
                        }
                    }
                    alert(errorsMessage);
                };
                if(submitFormInnerHTML == `<i class="bi bi-trash"></i>`){
                    $('#jobModalWindow').modal('hide');
                    let row = document.getElementById('jobTableRow_'+updateData.id);
                    console.log(row);
                    
                    if(row){
                        row.parentNode.removeChild(row);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error.message);
            });
        }
    });
    const taskPostalCodeField =  document.getElementById('taskPostalCodeField');
    taskPostalCodeField.addEventListener('input',function(e){
        // Pakeičia visus "white space" simbolius į paprastą tarpą
        e.target.value = e.target.value.replace(/[\u00A0\u2000-\u200D\u202F\u205F\u3000]/g, ' ');
    });
});