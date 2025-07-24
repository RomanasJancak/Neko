function openJobTemplateModal(id) {
    const modalEl = document.getElementById('jobModalWindow');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}
function fetchJobTemplates() {
    const tbody = document.querySelector('#tableForItemList tbody');

    ;
    const routeUrl = window.ROUTES.WEB.JOBTEMPLATE.FETCH;
    fetch(routeUrl)
        .then(response => response.json())
        .then(data => {
          if(data.success){            
            const tbody_fragment = document.createDocumentFragment();
            data.items.forEach(item => {
              console.log(item.dropOfftasks.data.map(itemtask => itemtask.id));

              const row = document.createElement('tr');

              const td_id = document.createElement('td');
              td_id.textContent = item.id;
              row.appendChild(td_id);
              const td_name = document.createElement('td');
              td_name.textContent = item.name;
              row.appendChild(td_name);

              const td_clientName = document.createElement('td');
              td_clientName.classList.add('p-0', 'm-0');
              const div = document.createElement('div');
              div.classList.add('border', 'w-100', 'p-3');
              div.style.borderRadius = item.clientToBill ? '1.975rem' : '0.375rem';
              div.textContent = item.clientToBill ? item.clientToBill.name : 'N/A';
              if (!item.clientToBill) {
                div.classList.add('text-danger');
              }
              td_clientName.appendChild(div);
              row.appendChild(td_clientName);
              
              const td_pickup = document.createElement('td');
              td_pickup.innerHTML = `<div class="text-center">${item.pickuptask ? item.pickuptask.data.pickupclientpostalcode	 : 'N/A'}</div>`;
              if(!item.pickuptask.addressIsFromClientList){
                td_pickup.classList.add('text-danger');
              }
              row.appendChild(td_pickup);
              const td_dropOffs = document.createElement('td');
              td_dropOffs.innerHTML = `
                <div class="t">
                  ${item.dropOfftasks && item.dropOfftasks.data.length > 0 
                    ? item.dropOfftasks.data.map(itemtask => {
                        const pkg = itemtask.package;
                        return `<blockquote class="blockquote-footer">
                                  <p><strong>${pkg.package_type.name} x ${pkg.quantity}</strong></p>
                                  <footer class="blockquote-footer">
                                    ${pkg.dropoff_postal_code}, ${pkg.dropoff_adress_line}
                                  </footer>
                                </blockquote>`;
                      }).join('')
                    : 'N/A'
                  }
                </div>`;
              row.appendChild(td_dropOffs);
              const td_return = document.createElement('td');
              td_return.innerHTML = `<div class="text-center">${item.returntask ? item.returntask.data.return.postal_code : 'N/A'}</div>`;
              if(!item.returntask.addressIsSameAsPickup){
                td_return.classList.add('text-danger');
              }
              row.appendChild(td_return);
              const td_price = document.createElement('td');
              td_price.innerHTML = `<div class="text-center">${item.fixedPrice > 0 ? item.price.toFixed(2) : 'N/A'}</div>`;
              row.appendChild(td_price);
              const td_actions = document.createElement('td');
              const button_edit = document.createElement('button');
              button_edit.classList.add('btn', 'btn-secondary', 'btn-sm');
              button_edit.textContent = 'Edit';
              button_edit.addEventListener('click', function() {
                openJobTemplateModal(item.id);
              });
              const button_createScheduleFromTemplate = document.createElement('button');
              button_createScheduleFromTemplate.classList.add('btn', 'btn-primary', 'btn-sm');
              button_createScheduleFromTemplate.textContent = 'Create Schedule';
              button_createScheduleFromTemplate.addEventListener('click', function() {
                console.log('Create schedule from template:', item.id);
              });
              td_actions.appendChild(button_edit);
              td_actions.appendChild(button_createScheduleFromTemplate); 
              row.appendChild(td_actions);
              tbody_fragment.appendChild(row);
            });
            tbody.innerHTML = '';
            tbody.appendChild(tbody_fragment);
          }
        })
        .catch(error => {
            console.error('Error fetching job templates:', error);
        });
}
//=====================================================================
document.addEventListener('DOMContentLoaded', function() {
  const modalEl = document.getElementById('jobModalWindow');
  const modal = new bootstrap.Modal(modalEl);
  modal.show();
  fetchJobTemplates(); 
});