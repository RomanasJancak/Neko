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
        submitButton.innerHTML = "<i class='bi bi-trash'></i>";
        submitButton.style.visibility = 'hidden';
        createNewTaskButton.style.visibility = 'hidden';
    }
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
        submitButton.innerHTML = "<i class='bi bi-pen'></i>";
        submitButton.style.visibility = 'visible';
        createNewTaskButton.style.visibility = 'visible';
    }
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
        
        setJobValues(jobId, 'delete');
        global_typeOfButtonClickedToOpenJobModal = 'delete';
        const submitButton = document.getElementById('submitform');
        submitButton.innerHTML = "<i class='bi bi-trash'></i>";
        submitButton.style.visibility = 'visible';
        createNewTaskButton.style.visibility = 'hidden';
    }
    $('#jobModalWindow').modal('show');
}
function createJob(){
    const form = document.getElementById('jobForm');
    updateData  =   {
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
            submitButton.innerHTML = "<i class='bi bi-pen'></i>";
        }
        document.getElementById('idField').value = data.job.id;
        document.getElementById('jobid').value = data.job.id;
    })
    .catch(error => {
        console.error('Error:', error.message);
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

}
function getHtmlOfActionButtonsForTheJob(jobId){
    return `
        <button class="btn btn-success view-btn" onclick="viewJob(${jobId})" data-jobid="${jobId}"><i class="bi bi-eye"></i></button>
        <button class="btn btn-primary edit-btn" onclick="editJob(${jobId})" data-jobid="${jobId}"><i class="bi bi-pen"></i></button>
        <button class="btn btn-danger delete-btn" onclick="deleteJob(${jobId})" data-jobid="${jobId}"><i class="bi bi-trash"></i></button>
        <button class="btn btn-info copy-btn" onclick="copyJob(${jobId})" data-jobid="${jobId}"><i class="fa-solid fa-copy"></i></button>
    `;
}
function addPaginationEventListeners() {
    document.querySelectorAll('#paginationLinks_bottom a, #paginationLinks_top a').forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            const url = this.href;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('jobsTableBody').innerHTML = '';
                    data.jobs.forEach(job => {
                        let packageCounter = 1;
                        let isAddressSameAsClientAdress = job.pickup.isAddressSameAsClientAdress;
                        let addressNameToDisplay = isAddressSameAsClientAdress 
                                                    ? (job.clientToBill.shortenedName !=='') 
                                                    ? job.clientToBill.shortenedName+' '+job.clientToBill.pickup_postal_code
                                                        :job.clientToBill.name
                                                    : job.pickup.namdeOfAddress;
                        const jobRow = `
                            <tr>
                                <td>${job.id}</td>
                                <td class="no-padding">
                                    <img src='${job.urlToLogo}' alt="Company Logo" style="max-width: 2rem;  height: auto;">
                                    <span>${job.clientName}</span>
                                </td>
                                <td>${job.date}</td>
                                <td>
                                    <div>
                                        <span>${addressNameToDisplay}</span>
                                        <span class="info-icon">
                                            <i class="bi bi-info-circle-fill"></i>
                                            <span class="tooltip">
                                                ${job.pickup.fullAddress}
                                            </span>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    ${job.tasks.map(
                                        task => task.package 
                                                    ? `<div class="row"><div class="col"><blockquote class="blockquote border"><h6>Package No [${packageCounter++}]</h6><p class="mb-0">${task.package.dropoff_name}
                                                            ${job.hasReturn ? '<i class="bi bi-arrow-counterclockwise" style="color: #00DD00;"></i>' : ''}
                                                    </p><footer class="blockquote-footer"><cite title="Source Title">${task.package.dropoff_adress_line}${task.package.dropoff_postal_code}</cite></footer></blockquote></div></div>` 
                                                            : '')
                                        .join('')}
                                </td>
                                <td></td>
                                <td><span>&#163;</span><span>${parseFloat(job.price/100,2)}</span></td>
                                <td>`+getHtmlOfActionButtonsForTheJob(job.id)+`
                                </td>
                            </tr>
                        `;
                        document.getElementById('jobsTableBody').insertAdjacentHTML('beforeend', jobRow);
                    });
                    document.getElementById('paginationLinks_bottom').innerHTML = data.links;
                    document.getElementById('paginationLinks_top').innerHTML = data.links;
                    addPaginationEventListeners();
                })
                .catch(error => console.error('Error fetching data:', error));
        });
    });
}
function fetchJobs(page = 1) {
  const id = document.getElementById('search-id').value;
  const clientName = document.getElementById('search-clientName').value;
  const date = document.getElementById('search-date').value;
  const pakuote = document.getElementById('search-package').value;
  const sortField = document.querySelector('.sort-btn.active')?.dataset.sortField || 'id';
  const sortOrder = document.querySelector('.sort-btn.active')?.dataset.sortOrder || 'asc';

  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const xhr = new XMLHttpRequest();
  xhr.open('GET', window.ROUTES.WEB.JOB.FETCH+`?id=${id}&clientName=${clientName}&date=${date}&package=${pakuote}&sortField=${sortField}&sortOrder=${sortOrder}&page=${page}`, true);
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
              const jobRow = `
                  <tr>
                      <td>${job.id}</td>
                      <td class="no-padding">
                          <img src='${job.urlToLogo}' alt="Company Logo" style="max-width: 2rem;  height: auto;">
                          <span>${job.clientName}</span>
                      </td>
                      <td>${job.date}</td>
                      <td>
                          <div>
                              <span>${addressNameToDisplay}</span>
                              <span class="info-icon">
                                  <i class="bi bi-info-circle-fill"></i>
                                  <span class="tooltip">
                                      ${job.pickup.fullAddress}
                                  </span>
                              </span>
                          </div>
                      </td>
                      <td>
                          ${job.tasks.map(
                              task => task.package 
                                          ? `<div class="row"><div class="col"><blockquote class="blockquote border"><h6>Package No [${packageCounter++}]</h6><p class="mb-0">${task.package.dropoff_name}
                                                  
                                                  ${job.hasReturn ? '<i class="bi bi-arrow-counterclockwise" style="color: #00DD00;"></i>' : ''}
                                          </p><footer class="blockquote-footer"><cite title="Source Title">${task.package.dropoff_adress_line}${task.package.dropoff_postal_code}</cite></footer></blockquote></div></div>` 
                                                  : '')
                              .join('')}
                      </td>
                      <td></td>
                      <td><span>&#163;</span><span>${parseFloat(job.price/100,2)}${job.fixed_price ?'':'<i class="fa-solid fa-lock" style="color:rgb(226, 34, 223);"></i>'}</span></td>
                      <td>`+getHtmlOfActionButtonsForTheJob(job.id)+`
                      </td>
                  </tr>
              `;
              document.getElementById('jobsTableBody').insertAdjacentHTML('beforeend', jobRow);
          });
          document.getElementById('paginationLinks_bottom').innerHTML = data.links;
          document.getElementById('paginationLinks_top').innerHTML = data.links;
          addPaginationEventListeners();
          
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
  { id: 'search-id', field: 'id' },
  { id: 'search-clientName', field: 'name' },
  { id: 'search-date', field: 'date' },
  { id: 'search-package', field: 'package' }
];
var sortButton_clientName   =   document.getElementById('button-sort-clientName');
var sortButton_date   =   document.getElementById('button-sort-date');
var sortButton_id   =   document.getElementById('button-sort-id');

searchInputs.forEach(input => {
    const inputElement = document.getElementById(input.id);
  
    inputElement.addEventListener('input', function() {
        fetchJobs();
    });
  });

addEventListenerToSortButton(sortButton_clientName);
addEventListenerToSortButton(sortButton_date);
addEventListenerToSortButton(sortButton_id);