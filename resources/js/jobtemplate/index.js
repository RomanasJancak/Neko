/**
 * JobTemplate Index JavaScript
 * Handles AJAX operations for template listing, CRUD, and batch job creation
 */

const API_BASE = window.ROUTES.WEB.JOBTEMPLATE;

let currentPage = 1;
let currentSort = { field: 'id', order: 'asc' };
let selectedTemplateId = null;
let originalTemplateData = {};

/**
 * Initialize page
 */
document.addEventListener('DOMContentLoaded', function() {
    fetchTemplates();
    attachEventListeners();
});

/**
 * Attach all event listeners
 */
function attachEventListeners() {
    // Search
    document.getElementById('search-input').addEventListener('input', debounce(() => {
        currentPage = 1;
        fetchTemplates();
    }, 300));

    // Create Template button
    document.getElementById('btn-create-template').addEventListener('click', openCreateTemplateModal);

    // Create Template Modal
    document.getElementById('create-template-close-btn').addEventListener('click', closeCreateTemplateModal);
    document.getElementById('create-template-cancel-btn').addEventListener('click', closeCreateTemplateModal);
    document.getElementById('create-template-submit-btn').addEventListener('click', handleCreateTemplate);

    // Modal close buttons
    document.getElementById('modal-close-btn').addEventListener('click', closeTemplateModal);
    document.getElementById('modal-close-footer-btn').addEventListener('click', closeTemplateModal);
    document.getElementById('modal-backdrop').addEventListener('click', closeTemplateModal);

    // Create Jobs Modal
    document.getElementById('create-jobs-close-btn').addEventListener('click', closeCreateJobsModal);
    document.getElementById('create-jobs-cancel-btn').addEventListener('click', closeCreateJobsModal);
    document.getElementById('create-jobs-submit-btn').addEventListener('click', handleCreateJobs);

    // Date inputs for job creation
    document.getElementById('start-date').addEventListener('change', updateJobsSummary);
    document.getElementById('end-date').addEventListener('change', updateJobsSummary);

    // Day checkboxes
    document.querySelectorAll('.day-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateJobsSummary);
    });

    // Load clients on page init
    loadClients();

    // Templates table actions (event delegation)
    document.getElementById('templates-container').addEventListener('click', handleTemplatesContainerClick);

    // Pagination actions (event delegation)
    document.getElementById('pagination-container').addEventListener('click', handlePaginationClick);

    // Modal footer buttons
    document.getElementById('modal-delete-btn').addEventListener('click', () => {
        if (selectedTemplateId) {
            handleDeleteTemplate(selectedTemplateId);
        }
    });
    document.getElementById('modal-save-btn').addEventListener('click', handleUpdateTemplate);

    // Modal body actions (event delegation)
    const modalBody = document.getElementById('modal-body-content');
    modalBody.addEventListener('click', handleModalBodyClick);
    modalBody.addEventListener('change', handleModalBodyChange);
}

/**
 * Handle templates table button clicks
 */
function handleTemplatesContainerClick(event) {
    const actionButton = event.target.closest('[data-action]');
    if (!actionButton) return;

    const action = actionButton.getAttribute('data-action');
    const templateId = actionButton.getAttribute('data-id');

    switch (action) {
        case 'view-template':
            if (templateId) handleViewTemplate(Number(templateId));
            break;
        case 'create-jobs':
            if (templateId) handleCreateJobsClick(Number(templateId));
            break;
        case 'delete-template':
            if (templateId) handleDeleteTemplate(Number(templateId));
            break;
        default:
            break;
    }
}

/**
 * Handle pagination button clicks
 */
function handlePaginationClick(event) {
    const actionButton = event.target.closest('[data-action="go-to-page"]');
    if (!actionButton) return;

    const page = actionButton.getAttribute('data-page');
    if (page) {
        goToPage(Number(page));
    }
}

/**
 * Handle modal body button clicks
 */
function handleModalBodyClick(event) {
    const actionButton = event.target.closest('[data-action]');
    if (!actionButton) return;

    const action = actionButton.getAttribute('data-action');

    switch (action) {
        case 'add-dropoff':
            handleAddDropOff();
            break;
        case 'remove-dropoff': {
            const orderNumber = actionButton.getAttribute('data-order');
            if (orderNumber) handleRemoveDropOff(Number(orderNumber));
            break;
        }
        case 'add-return':
            handleAddReturn();
            break;
        case 'remove-return':
            handleRemoveReturn();
            break;
        default:
            break;
    }
}

/**
 * Handle modal body input changes
 */
function handleModalBodyChange(event) {
    const target = event.target;
    if (!target) return;

    if (target.id === 'is-price-fixed-toggle') {
        handlePriceToggle();
        return;
    }

    if (target.id === 'return-type-toggle') {
        handleReturnTypeToggle();
    }
}

/**
 * Debounce utility
 */
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

/**
 * Fetch templates via AJAX
 */
function fetchTemplates() {
    const searchQuery = document.getElementById('search-input').value;
    const url = new URL(API_BASE.FETCH, window.location.origin);
    
    url.searchParams.append('search', searchQuery);
    url.searchParams.append('sortField', currentSort.field);
    url.searchParams.append('sortOrder', currentSort.order);
    url.searchParams.append('page', currentPage);

    const container = document.getElementById('templates-container');
    container.innerHTML = '<div class="loading">Loading templates...</div>';

    fetch(url.toString())
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderTemplates(data.templates);
                renderPagination(data.pagination);
            } else {
                showError(data.error || 'Failed to fetch templates');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An error occurred while fetching templates');
        });
}

/**
 * Render templates table
 */
function renderTemplates(templates) {
    const container = document.getElementById('templates-container');

    if (templates.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3>No templates found</h3>
                <p>Create a template to get started</p>
            </div>
        `;
        return;
    }

    let html = `
        <table class="templates-table">
            <thead>
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th>Name</th>
                    <th style="width: 280px; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
    `;

    templates.forEach(template => {
        html += `
            <tr>
                <td><strong>#${template.id}</strong></td>
                <td>${sanitizeHtml(template.name)}</td>
                <td>
                    <div class="row-actions" style="justify-content: flex-end;">
                        <button class="btn-action btn-view" data-action="view-template" data-id="${template.id}">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn-action btn-jobs" data-action="create-jobs" data-id="${template.id}">
                            <i class="fas fa-plus-circle"></i> Create Jobs
                        </button>
                        <button class="btn-action btn-delete" data-action="delete-template" data-id="${template.id}">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    html += `
            </tbody>
        </table>
    `;

    container.innerHTML = html;
}

/**
 * Render pagination
 */
function renderPagination(pagination) {
    const container = document.getElementById('pagination-container');
    
    if (pagination.last_page <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = `<div class="pagination-info">Page ${pagination.current_page} of ${pagination.last_page}</div>`;
    
    if (pagination.current_page > 1) {
        html += `<button class="btn btn-sm btn-outline-secondary" data-action="go-to-page" data-page="1">First</button>`;
        html += `<button class="btn btn-sm btn-outline-secondary" data-action="go-to-page" data-page="${pagination.current_page - 1}">Previous</button>`;
    }

    if (pagination.current_page < pagination.last_page) {
        html += `<button class="btn btn-sm btn-outline-secondary" data-action="go-to-page" data-page="${pagination.current_page + 1}">Next</button>`;
        html += `<button class="btn btn-sm btn-outline-secondary" data-action="go-to-page" data-page="${pagination.last_page}">Last</button>`;
    }

    container.innerHTML = html;
}

/**
 * Go to specific page
 */
function goToPage(page) {
    currentPage = page;
    fetchTemplates();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/**
 * View template details
 */
function handleViewTemplate(templateId) {
    selectedTemplateId = templateId;
    const modal = document.getElementById('template-modal');
    const body = document.getElementById('modal-body-content');
    
    body.innerHTML = '<div class="loading">Loading template details...</div>';
    modal.classList.add('active');
    document.getElementById('modal-backdrop').classList.add('active');

    fetch(API_BASE.GETINFO.replace(':id', templateId))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderTemplateModal(data.template, data.lockedFields);
            } else {
                body.innerHTML = `<div class="error-message show">${data.error}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            body.innerHTML = `<div class="error-message show">Failed to load template details</div>`;
        });
}

/**
 * Render template modal content (editable form)
 */
function renderTemplateModal(template, lockedFields) {
    const dropoffs = template.dropoffs || [];
    const returnData = template.return || null;
    const body = document.getElementById('modal-body-content');
    let html = `
      <form id="edit-template-form">
        <div class="form-group mb-3">
          <label class="form-label">Template ID</label>
          <input type="text" class="form-control" value="#${template.id}" disabled>
        </div>

        <div class="form-group mb-3">
          <label for="edit-template-name" class="form-label">Template Name</label>
          <input 
            type="text" 
            id="edit-template-name" 
            name="name"
            class="form-control inputs-forJobTemplate" 
            data-orgdata="${sanitizeHtml(template.name)}"
            value="${sanitizeHtml(template.name)}" 
            required>
        </div>

        <div class="form-group mb-3">
          <label for="edit-template-client" class="form-label">Client</label>
          <select 
            id="edit-template-client" 
            name="client_id"
            class="form-select inputs-forJobTemplate" 
            data-orgdata="${template.client_id || ''}"
            required>
            <option value="">Loading clients...</option>
          </select>
        </div>

        <div class="form-group mb-3">
          <label for="edit-template-address" class="form-label">Pickup Address</label>
          <select 
            id="edit-template-address" 
            name="pickup_address_id"
            class="form-select inputs-forJobTemplate" 
            data-orgdata="${template.pickup.address?.id ?? ''}"
            <option data-fullAddress="${sanitizeHtml(template.pickup.address?.postal_code ?? ''+''+template.pickup.address?.address_line_1 ?? ''+''+template.pickup.address?.address_line_2 ?? '')}"
            value="">Pickup_option</option>
          </select>
          <div id="edit-template-address-details" style="font-size: 0.95em; color: #555; margin-top: 4px; min-height: 18px;">
          ${sanitizeHtml(template.pickup.address?.postal_code ?? ''+' , '+template.pickup.address?.address_line_1 ?? ''+' '+template.pickup.address?.address_line_2 ?? '')}</div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group mb-3">
              <label for="edit-template-time-begin" class="form-label">Pickup Time Begin</label>
              <input 
                type="time" 
                id="edit-template-time-begin" 
                name="pickup_time_begin"
                class="form-control inputs-forJobTemplate" 
                data-orgdata="${template.pickup.time_begin || ''}"
                value="${template.pickup.time_begin || ''}">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group mb-3">
              <label for="edit-template-time-end" class="form-label">Pickup Time End</label>
              <input 
                type="time" 
                id="edit-template-time-end" 
                name="pickup_time_end"
                class="form-control inputs-forJobTemplate" 
                data-orgdata="${template.pickup_time_end || ''}"
                value="${template.pickup_time_end || ''}">
            </div>
          </div>
        </div>

        <div style="margin-top: 20px; padding: 15px; background: rgba(13, 110, 253, 0.1); border-radius: 4px; border-left: 4px solid #0d6efd;">
          <small style="color: rgba(255,255,255,0.7);"><strong>Created:</strong> ${new Date(template.created_at).toLocaleDateString()} | <strong>Updated:</strong> ${new Date(template.updated_at).toLocaleDateString()}</small>
        </div>

        <!-- Pricing Section -->
        <div style="margin-top: 25px; padding: 15px; background: rgba(255,193,7,0.1); border-radius: 4px; border-left: 4px solid #ffc107;">
          <div class="row g-3 align-items-center">
            <div class="col-12">
              <div class="form-check form-switch">
                                <input 
                                    class="form-check-input inputs-forJobTemplate" 
                                    type="checkbox" 
                                    id="is-price-fixed-toggle"
                                    name="is_price_fixed"
                                    data-orgdata="${template.is_price_fixed ?? false}"
                                    ${template.is_price_fixed ? 'checked' : ''}>
                <label class="form-check-label" for="is-price-fixed-toggle">
                  Fixed Price
                </label>
              </div>
            </div>
            <div class="col-12" id="price-input-container" style="${!template.is_price_fixed ? 'display: none;' : ''}">
              <label for="template-price" class="form-label">Price (£)</label>
              <input 
                type="number" 
                id="template-price"
                name="price"
                class="form-control inputs-forJobTemplate" 
                data-orgdata="${template.price || 0}"
                value="${template.price || 0}"
                step="0.01"
                min="0"
                placeholder="0.00">
              <small style="color: rgba(255,255,255,0.6);">Enter price with up to 2 decimal places</small>
            </div>
          </div>
        </div>

        <!-- Dropoffs Section -->
        <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h5 style="margin: 0; color: #fff;">Drop-offs</h5>
                <button type="button" class="btn btn-sm btn-success" data-action="add-dropoff">
                    <i class="fas fa-plus"></i> Add Drop-off
                </button>
            </div>
            <div id="dropoffs-container">
                ${dropoffs.length === 0 ? '<p style="color: rgba(255,255,255,0.5); text-align: center; padding: 20px;">No drop-offs added yet</p>' : ''}
                ${dropoffs.map((dropoff, index) => `
                    <div class="dropoff-item" data-order="${dropoff.order_number}" style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 6px; margin-bottom: 10px; border: 1px solid rgba(255,255,255,0.1);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <strong style="color: #fff;">Drop-off #${dropoff.order_number}</strong>
                            <button type="button" class="btn btn-sm btn-danger" data-action="remove-dropoff" data-order="${dropoff.order_number}">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                                                <select class="form-select inputs-forJobTemplate dropoff-address-select" 
                                                                            data-order="${dropoff.order_number}"
                                                                            name="dropoff_address_${dropoff.order_number}"
                                                                        data-orgdata="${dropoff.address?.id ?? dropoff.address_id ?? ''}">
                                    <option value="">Select address...</option>
                                    ${dropoff.address ? `<option value="${dropoff.address_id}" selected>${sanitizeHtml(dropoff.address.name || dropoff.address.address_line_1)} - ${sanitizeHtml(dropoff.address.postal_code)}</option>` : ''}
                                </select>
                                <div class="dropoff-address-details" style="font-size: 0.95em; color: rgba(255,255,255,0.6); margin-top: 4px; min-height: 18px;">
                                  ${sanitizeHtml((dropoff.address?.postal_code || '') + ' , ' + (dropoff.address?.address_line_1 || '') + ' ' + (dropoff.address?.address_line_2 || ''))}
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Time Begin</label>
                                <input type="time" class="form-control inputs-forJobTemplate dropoff-time-begin" 
                                    data-order="${dropoff.order_number}"
                                    name="dropoff_time_begin_${dropoff.order_number}"
                                    data-orgdata="${dropoff.time_begin || ''}"
                                    value="${dropoff.time_begin || ''}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Time End</label>
                                <input type="time" class="form-control inputs-forJobTemplate dropoff-time-end" 
                                    data-order="${dropoff.order_number}"
                                    name="dropoff_time_end_${dropoff.order_number}"
                                    data-orgdata="${dropoff.time_end || ''}"
                                    value="${dropoff.time_end || ''}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Package Type</label>
                                <select class="form-select inputs-forJobTemplate dropoff-packagetype" 
                                    data-order="${dropoff.order_number}"
                                    name="dropoff_package_type_${dropoff.order_number}"
                                    data-orgdata="${dropoff.package_type?.id || ''}">
                                  <option value="">Select package type...</option>
                                  ${dropoff.package_type ? `<option value="${dropoff.package_type.id}" selected>${sanitizeHtml(dropoff.package_type.name || '')}</option>` : ''}
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Quantity</label>
                                <input type="number" class="form-control inputs-forJobTemplate dropoff-quantity" 
                                    data-order="${dropoff.order_number}"
                                    name="dropoff_quantity_${dropoff.order_number}"
                                    data-orgdata="${dropoff.quantity || ''}"
                                    value="${dropoff.quantity || ''}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Note</label>
                                <textarea class="form-control inputs-forJobTemplate dropoff-note" 
                                    data-order="${dropoff.order_number}"
                                    name="dropoff_note_${dropoff.order_number}"
                                    data-orgdata="${sanitizeHtml(dropoff.note || '')}">${dropoff.note || ''}</textarea>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>

        <!-- Return Section -->
        <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h5 style="margin: 0; color: #fff;">Return</h5>
                ${!returnData ? `<button type="button" class="btn btn-sm btn-success" data-action="add-return">
                    <i class="fas fa-plus"></i> Add Return
                </button>` : `<button type="button" class="btn btn-sm btn-danger" data-action="remove-return">
                    <i class="fas fa-trash"></i> Remove Return
                </button>`}
            </div>
            <div id="return-container" style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1);">
                ${returnData ? `
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label">Return Address</label>
                            <select 
                                id="return-address-select"
                                class="form-select inputs-forJobTemplate" 
                                name="return_address_id"
                                data-orgdata="${returnData.address?.id ?? ''}">
                                <option value="">Select address...</option>
                                ${returnData.address ? `<option value="${returnData.address.id}" selected>${sanitizeHtml(returnData.address.name || returnData.address.address_line_1)} - ${sanitizeHtml(returnData.address.postal_code)}</option>` : ''}
                            </select>
                            <div id="return-address-details" style="font-size: 0.95em; color: rgba(255,255,255,0.6); margin-top: 4px; min-height: 18px;">
                              ${sanitizeHtml((returnData.address?.postal_code || '') + ' , ' + (returnData.address?.address_line_1 || '') + ' ' + (returnData.address?.address_line_2 || ''))}
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input 
                                    class="form-check-input inputs-forJobTemplate" 
                                    type="checkbox" 
                                    id="return-type-toggle"
                                    name="return_is_same_day"
                                    data-orgdata="${returnData.is_same_day ?? 'false'}"
                                    ${returnData.is_same_day === true || returnData.is_same_day === 'true' ? 'checked' : ''}>
                                <label class="form-check-label" for="return-type-toggle">
                                    Same Day Return
                                </label>
                            </div>
                        </div>
                        <div class="col-12" id="return-time-container">
                            ${returnData.is_same_day === true || returnData.is_same_day === 'true' ? `
                                <label class="form-label">Return Time</label>
                                <input 
                                    type="time" 
                                    class="form-control inputs-forJobTemplate" 
                                    id="return-time-input"
                                    name="return_time"
                                    data-orgdata="${returnData.time_begin || ''}"
                                    value="${returnData.time_begin || ''}">
                            ` : `
                                <label class="form-label">Return Date & Time</label>
                                <input 
                                    type="datetime-local" 
                                    class="form-control inputs-forJobTemplate" 
                                    id="return-datetime-input"
                                    name="return_datetime"
                                    data-orgdata="${returnData.time_begin || ''}"
                                    value="${returnData.time_begin || ''}">
                            `}
                        </div>
                        <div class="col-12">
                            <label class="form-label">Return Note</label>
                            <textarea 
                                class="form-control inputs-forJobTemplate" 
                                name="return_note"
                                data-orgdata="${sanitizeHtml(returnData.note || '')}"
                                rows="2">${returnData.note || ''}</textarea>
                        </div>
                    </div>
                ` : '<p style="color: rgba(255,255,255,0.5); text-align: center; padding: 20px;">No return configured</p>'}
            </div>
        </div>
      </form>
    `;

    body.innerHTML = html;
    body.querySelector
    // Load clients and set selected value
    loadEditClients(template.client_id);

    // Add event listener for client change to update addresses
    document.getElementById('edit-template-client').addEventListener('change', function() {
        updateEditClientAddresses(this.value);
        updateClientpackageTypes(this.value);
        updateEditReturnAddress(this.value);
    });
    document.getElementById('edit-template-address').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (!selectedOption) return;
        document.getElementById('edit-template-address-details').textContent = selectedOption.getAttribute('data-fullAddress') ?? '';
    });
    
    // Add event listener for return address select
    const returnAddressSelect = document.getElementById('return-address-select');
    if (returnAddressSelect) {
        returnAddressSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (!selectedOption) return;
            document.getElementById('return-address-details').textContent = selectedOption.getAttribute('data-fullAddress') ?? '';
        });
    }
    
    document.getElementById('edit-template-address').value = template.pickup_address_id;
        setTimeout(() => {
            let temp = document.getElementById('edit-template-address');
            temp.value = template.pickup.address?.id ?? '';
            temp.dispatchEvent(new Event('change'));
            updateClientpackageTypes(template.client_id);
            updateEditReturnAddress(template.client_id);
    }, 500);
    
    // Load addresses for each dropoff
    // dropoffs.forEach(dropoff => {
    //     if (dropoff.address_id) {
    //         loadDropOffAddresses(template.client_id, dropoff.order_number, dropoff.address_id);
    //     }
    // });
}

/**
 * Load clients for edit modal
 */
function loadEditClients(selectedClientId = null) {
    fetch(window.ROUTES.WEB.CLIENT.SEARCH)
        .then(response => response.json())
        .then(data => {
            const clientSelect = document.getElementById('edit-template-client');
            clientSelect.innerHTML = '<option value="">Select a client...</option>';
            
            if (data && Array.isArray(data)) {
                data.forEach(client => {
                    const option = document.createElement('option');
                    option.value = client.id;
                    option.textContent = client.name;
                    if (selectedClientId && client.id == selectedClientId) {
                        option.selected = true;
                    }
                    clientSelect.appendChild(option);
                });
                if (selectedClientId) {
                    updateEditClientAddresses(selectedClientId);
                }
            }
        })
        .catch(error => console.error('Error loading clients:', error));
}

/**
 * Update addresses for selected client in edit modal
 */
function updateEditClientAddresses(clientId) {
    if (!clientId) {
        document.getElementById('edit-template-address').innerHTML = '<option value="">Select a client first</option>';
        return;
    }

    fetch(window.ROUTES.WEB.CLIENT.SEARCHADDRESSES + '?client_id=' + clientId)
        .then(response => response.json())
        .then(data => {
            const addressSelect = document.getElementById('edit-template-address');
            const dropoffAddressSelects = document.querySelectorAll('.dropoff-address-select');
            
            // Store original selected values before clearing
            const originalValues = {};
            dropoffAddressSelects.forEach(select => {
                originalValues[select.getAttribute('data-order')] = select.getAttribute('data-orgdata');
            });
            
            // Clear and rebuild pickup address select
            addressSelect.innerHTML = '<option value="">Select an address...</option>';
            
            if (data && Array.isArray(data)) {
                data.forEach(address => {
                    const option = document.createElement('option');
                    option.value = address.id;
                    option.textContent = address.name;
                    option.setAttribute('data-fullAddress', sanitizeHtml(address.postal_code + ' , ' + address.address_line_1 + ' ' + address.address_line_2));
                    addressSelect.appendChild(option);
                });
                
                // Clear and rebuild dropoff address selects, preserving original values
                dropoffAddressSelects.forEach(select => {
                    const orderNumber = select.getAttribute('data-order');
                    const originalValue = originalValues[orderNumber];
                    
                    select.innerHTML = '<option value="">Select address...</option>';
                    
                    data.forEach(address => {
                        const option = document.createElement('option');
                        option.value = address.id;
                        option.textContent = address.name;
                        option.setAttribute('data-fullAddress', sanitizeHtml(address.postal_code + ' , ' + address.address_line_1 + ' ' + address.address_line_2));
                        

                        if (originalValue && address.id == originalValue) {
                            option.selected = true;
                        }
                        
                        select.appendChild(option);
                    });
                });
            }
        })
        .catch(error => console.error('Error loading addresses:', error));
}

/**
 * Update return address for selected client in edit modal
 */
function updateEditReturnAddress(clientId) {
    if (!clientId) {
        const returnAddressSelect = document.getElementById('return-address-select');
        if (returnAddressSelect) {
            returnAddressSelect.innerHTML = '<option value="">Select a client first</option>';
        }
        return;
    }

    fetch(window.ROUTES.WEB.CLIENT.SEARCHADDRESSES + '?client_id=' + clientId)
        .then(response => response.json())
        .then(data => {
            const returnAddressSelect = document.getElementById('return-address-select');
            if (!returnAddressSelect) return;
            
            // Store original selected value before clearing
            const originalValue = returnAddressSelect.getAttribute('data-orgdata');
            
            // Clear and rebuild return address select
            returnAddressSelect.innerHTML = '<option value="">Select an address...</option>';
            
            if (data && Array.isArray(data)) {
                data.forEach(address => {
                    const option = document.createElement('option');
                    option.value = address.id;
                    option.textContent = address.name;
                    option.setAttribute('data-fullAddress', sanitizeHtml(address.postal_code + ' , ' + address.address_line_1 + ' ' + address.address_line_2));
                    
                    if (originalValue && address.id == originalValue) {
                        option.selected = true;
                    }
                    
                    returnAddressSelect.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error loading addresses:', error));
}

/**
 * Handle template update - only sends changed fields
 * Scans all inputs with class "inputs-forJobTemplate" and compares with data-orgdata attribute
 */
function handleUpdateTemplate() {
    const form = document.getElementById('edit-template-form');
    if (!form.checkValidity()) {
        showError('Please fill in all required fields');
        return;
    }

    // Get all inputs marked for template update
    const templateInputs = document.querySelectorAll('.inputs-forJobTemplate');
    const changedData = {};
    let hasChanges = false;

    templateInputs.forEach(input => {
        const originalValue = input.getAttribute('data-orgdata');
        let currentValue = input.value;

        if (input.type === 'checkbox') {
            currentValue = input.checked ? 'true' : 'false';
        }

        const normalizedOriginal = normalizeInputValue(originalValue, input.type);
        const normalizedCurrent = normalizeInputValue(currentValue, input.type);

        // Only include fields that have changed
        if (normalizedOriginal !== normalizedCurrent) {
            // Use the name attribute directly - much cleaner!
            const fieldName = input.name;
            changedData[fieldName] = input.type === 'checkbox'
                ? input.checked
                : (currentValue || null);
            hasChanges = true;
        }
    });

    // Collect dropoff changes separately
    const dropoffItems = document.querySelectorAll('.dropoff-item');
    const dropoffsData = [];
    
    dropoffItems.forEach(item => {
        const orderNumber = parseInt(item.getAttribute('data-order'));
        const addressSelect = item.querySelector(`.dropoff-address-select[data-order="${orderNumber}"]`);
        const packageTypeSelect = item.querySelector(`.dropoff-packagetype[data-order="${orderNumber}"]`);
        
        // Use current value, or fall back to original if current is empty
        const addressValue = addressSelect?.value || addressSelect?.getAttribute('data-orgdata') || null;
        const packageTypeValue = packageTypeSelect?.value || packageTypeSelect?.getAttribute('data-orgdata') || null;
        
        const dropoff = {
            order_number: orderNumber,
            address_id: addressValue,
            time_begin: item.querySelector(`.dropoff-time-begin[data-order="${orderNumber}"]`)?.value || null,
            time_end: item.querySelector(`.dropoff-time-end[data-order="${orderNumber}"]`)?.value || null,
            package_type_id: packageTypeValue,
            quantity: item.querySelector(`.dropoff-quantity[data-order="${orderNumber}"]`)?.value || null,
            note: item.querySelector(`.dropoff-note[data-order="${orderNumber}"]`)?.value || '',
        };
        dropoffsData.push(dropoff);
    });

    if (dropoffsData.length > 0) {
        changedData['dropoffs'] = dropoffsData;
        hasChanges = true;
    }

    if (!hasChanges) {
        showError('No changes were made');
        return;
    }

    fetch(API_BASE.UPDATE.replace(':id', selectedTemplateId), {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(changedData),
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showSuccess('Template updated successfully');
                closeTemplateModal();
                fetchTemplates();
            } else {
                showError(result.error || 'Failed to update template');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Failed to update template');
        });
}

/**
 * Normalize input values for comparison
 */
function normalizeInputValue(value, inputType) {
    if (inputType === 'checkbox') {
        return value === true || value === 'true' ? 'true' : 'false';
    }

    return value ?? '';
}

/**
 * Close template modal
 */
function closeTemplateModal() {
    document.getElementById('template-modal').classList.remove('active');
    document.getElementById('modal-backdrop').classList.remove('active');
    selectedTemplateId = null;
}

/**
 * Open create jobs modal
 */
function handleCreateJobsClick(templateId) {
    selectedTemplateId = templateId;
    const modal = document.getElementById('create-jobs-modal');
    
    // Get template info
    fetch(API_BASE.GETINFO.replace(':id', templateId))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('create-jobs-template-id').textContent = `#${data.template.id}`;
                document.getElementById('create-jobs-template-name').textContent = sanitizeHtml(data.template.name);
                
                // Reset form
                document.getElementById('create-jobs-form').reset();
                document.querySelectorAll('.day-checkbox').forEach(cb => cb.checked = false);
                updateJobsSummary();
                
                modal.classList.add('active');
                document.getElementById('modal-backdrop').classList.add('active');
            }
        })
        .catch(error => console.error('Error:', error));
}

/**
 * Close create jobs modal
 */
function closeCreateJobsModal() {
    document.getElementById('create-jobs-modal').classList.remove('active');
    document.getElementById('modal-backdrop').classList.remove('active');
    selectedTemplateId = null;
}

/**
 * Update jobs summary
 */
function updateJobsSummary() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    const selectedDays = Array.from(document.querySelectorAll('.day-checkbox:checked')).map(cb => cb.value);

    if (!startDate || !endDate || selectedDays.length === 0) {
        document.getElementById('jobs-summary').textContent = 'Select dates and days above';
        return;
    }

    const start = new Date(startDate);
    const end = new Date(endDate);
    const daysCount = countJobsInRange(start, end, selectedDays);

    document.getElementById('jobs-summary').textContent = 
        `${daysCount} job${daysCount !== 1 ? 's' : ''} will be created from ${startDate} to ${endDate}`;
}

/**
 * Count how many jobs will be created
 */
function countJobsInRange(start, end, selectedDays) {
    let count = 0;
    const current = new Date(start);
    
    while (current <= end) {
        const dayName = current.toLocaleDateString('en-US', { weekday: 'long' });
        if (selectedDays.includes(dayName)) {
            count++;
        }
        current.setDate(current.getDate() + 1);
    }
    
    return count;
}

/**
 * Handle create jobs submission
 */
function handleCreateJobs() {
    if (!selectedTemplateId) return;

    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    const selectedDays = Array.from(document.querySelectorAll('.day-checkbox:checked')).map(cb => cb.value);

    if (!startDate || !endDate || selectedDays.length === 0) {
        showError('Please select dates and at least one day');
        return;
    }

    const btn = document.getElementById('create-jobs-submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';

    const payload = {
        template_id: selectedTemplateId,
        start_date: startDate,
        end_date: endDate,
        days: selectedDays
    };

    fetch(API_BASE.CREATE_JOBS, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus-circle"></i> Create Jobs';

        if (data.success) {
            showSuccess(data.message);
            closeCreateJobsModal();
            setTimeout(() => fetchTemplates(), 1000);
        } else {
            showError(data.error || 'Failed to create jobs');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus-circle"></i> Create Jobs';
        showError('An error occurred');
    });
}

/**
 * Load clients from server
 */
function loadClients() {
  
    fetch(window.ROUTES.WEB.CLIENT.SEARCH || '/clients/searchClients')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('template-client');
            select.innerHTML = '<option value="">Select a client...</option>';
            
            if (data && Array.isArray(data)) {
                data.forEach(client => {
                    const option = document.createElement('option');
                    option.value = client.id;
                    option.textContent = sanitizeHtml(client.name || `Client #${client.id}`);
                    select.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error loading clients:', error));
}
function updateClientpackageTypes(clientId) {
  const packageTypesArray = document.querySelectorAll('.dropoff-packagetype');
  
  // Store original selected values before clearing
  const originalValues = {};
  packageTypesArray.forEach(select => {
    originalValues[select.getAttribute('data-order')] = select.getAttribute('data-orgdata');
  });
  
  fetch(window.ROUTES.WEB.CLIENT.FETCHPACKAGETYPES.replace(':id', clientId))
      .then(response => response.json())
      .then(data => {

          if (data) {

              packageTypesArray.forEach(select => {
                  const orderNumber = select.getAttribute('data-order');
                  const originalValue = originalValues[orderNumber];
                  
                  select.innerHTML = '<option value="">Select package type...</option>';
                  data.packageTypes.forEach(packageType => {
                      const option = document.createElement('option');
                      option.value = packageType.id;
                      option.textContent = sanitizeHtml(packageType.name);
                      if (originalValue && packageType.id == originalValue) {
                        option.selected = true;
                      }
                      select.appendChild(option);
                  });
              });
          }
      })
      .catch(error => console.error('Error loading package types:', error));
}


/**
 * Open create template modal
 */
function openCreateTemplateModal() {
    const modal = document.getElementById('create-template-modal');
    document.getElementById('create-template-form').reset();
    modal.classList.add('active');
    document.getElementById('modal-backdrop').classList.add('active');
}

/**
 * Close create template modal
 */
function closeCreateTemplateModal() {
    document.getElementById('create-template-modal').classList.remove('active');
    document.getElementById('modal-backdrop').classList.remove('active');
    document.getElementById('create-template-form').reset();
}

/**
 * Handle create template submission
 */
function handleCreateTemplate() {
    const name = document.getElementById('template-name').value.trim();
    const clientId = document.getElementById('template-client').value;

    // Validation
    if (!name) {
        showError('Template name is required');
        return;
    }

    if (!clientId) {
        showError('Client is required');
        return;
    }

    const btn = document.getElementById('create-template-submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';

    const payload = {
        name: name,
        client_id: clientId,
        template_data: {}
    };

    fetch(API_BASE.STORE , {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus"></i> Create Template';

        if (data.success) {
            showSuccess('Template created successfully!');
            closeCreateTemplateModal();
            currentPage = 1;
            setTimeout(() => fetchTemplates(), 800);
        } else {
            showError(data.error || 'Failed to create template');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus"></i> Create Template';
        showError('An error occurred while creating the template');
    });
}

/**
 * Load addresses for a specific dropoff
 */
// function loadDropOffAddresses(clientId, orderNumber, selectedAddressId = null) {
//     const url = window.ROUTES.WEB.CLIENT.SEARCHADDRESSES + '?client_id=' + clientId;
    
//     fetch(url)
//         .then(response => response.json())
//         .then(addresses => {
//             const select = document.querySelector(`.dropoff-address-select[data-order="${orderNumber}"]`);
//             if (!select) return;
            
//             select.innerHTML = '<option value="">Select address...</option>' +
//                 addresses.map(addr => `
//                     <option value="${addr.id}" ${addr.id == selectedAddressId ? 'selected' : ''}>
//                         ${sanitizeHtml(addr.name || addr.address_line_1)} - ${sanitizeHtml(addr.postal_code)}
//                     </option>
//                 `).join('');
//         })
//         .catch(error => console.error('Error loading dropoff addresses:', error));
// }

/**
 * Handle adding a new dropoff
 */
// function handleAddDropOff() {
//     if (!selectedTemplateId) return;
    
//     const url = window.ROUTES.WEB.JOBTEMPLATE.ADD_DROPOFF.replace(':id', selectedTemplateId);
    
//     fetch(url, {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//         }
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success) {
//             // Reload the modal to show updated dropoffs
//             handleViewTemplate(selectedTemplateId);
//             showSuccess('Drop-off added successfully');
//         } else {
//             showError(data.error || 'Failed to add drop-off');
//         }
//     })
//     .catch(error => {
//         console.error('Error adding dropoff:', error);
//         showError('Failed to add drop-off');
//     });
// }

/**
 * Handle removing a dropoff
 */
// function handleRemoveDropOff(orderNumber) {
//     if (!selectedTemplateId) return;
//     if (!confirm('Are you sure you want to remove this drop-off?')) return;
    
//     const url = window.ROUTES.WEB.JOBTEMPLATE.REMOVE_DROPOFF.replace(':id', selectedTemplateId);
    
//     fetch(url, {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//         },
//         body: JSON.stringify({ order_number: orderNumber })
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success) {
//             // Reload the modal to show updated dropoffs
//             handleViewTemplate(selectedTemplateId);
//             showSuccess('Drop-off removed successfully');
//         } else {
//             showError(data.error || 'Failed to remove drop-off');
//         }
//     })
//     .catch(error => {
//         console.error('Error removing dropoff:', error);
//         showError('Failed to remove drop-off');
//     });
// }

/**
 * Handle delete template
 */
function handleDeleteTemplate(templateId) {
    if (!confirm('Are you sure you want to delete this template? This action cannot be undone.')) {
        return;
    }

    fetch(API_BASE.DELETE.replace(':id', templateId) || `/jobtemplates/${templateId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message);
            currentPage = 1;
            setTimeout(() => fetchTemplates(), 800);
        } else {
            showError(data.error || 'Failed to delete template');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('An error occurred');
    });
}

/**
 * Show success message
 */
function showSuccess(message) {
    const messageEl = document.getElementById('success-message');
    messageEl.textContent = message;
    messageEl.classList.add('show');
    
    setTimeout(() => {
        messageEl.classList.remove('show');
    }, 5000);
}

/**
 * Show error message
 */
function showError(message) {
    const messageEl = document.getElementById('error-message');
    messageEl.textContent = message;
    messageEl.classList.add('show');
    
    setTimeout(() => {
        messageEl.classList.remove('show');
    }, 5000);
}

/**
 * Load addresses for a specific dropoff
 */
function loadDropOffAddresses(clientId, orderNumber, selectedAddressId = null) {
    const url = window.ROUTES.WEB.CLIENT.SEARCHADDRESSES + '?client_id=' + clientId;
    
    fetch(url)
        .then(response => response.json())
        .then(addresses => {
            const select = document.querySelector(`.dropoff-address-select[data-order="${orderNumber}"]`);
            if (!select) return;
            
            select.innerHTML = '<option value="">Select address...</option>' +
                addresses.map(addr => `
                    <option value="${addr.id}" ${addr.id == selectedAddressId ? 'selected' : ''}>
                        ${sanitizeHtml(addr.name || addr.address_line_1)} - ${sanitizeHtml(addr.postal_code)}
                    </option>
                `).join('');
        })
        .catch(error => console.error('Error loading dropoff addresses:', error));
}

/**
 * Handle adding a new dropoff
 */
function handleAddDropOff() {
    if (!selectedTemplateId) return;
    
    const url = window.ROUTES.WEB.JOBTEMPLATE.ADD_DROPOFF.replace(':id', selectedTemplateId);
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {

        if (data.success) {
            // Reload the modal to show updated dropoffs
            handleViewTemplate(selectedTemplateId);
            showSuccess('Drop-off added successfully');
        } else {
            showError(data.error || 'Failed to add drop-off');
        }
    })
    .catch(error => {
        console.error('Error adding dropoff:', error);
        showError('Failed to add drop-off');
    });
}

/**
 * Handle removing a dropoff
 */
function handleRemoveDropOff(orderNumber) {
    if (!selectedTemplateId) return;
    if (!confirm('Are you sure you want to remove this drop-off?')) return;
    
    const url = window.ROUTES.WEB.JOBTEMPLATE.REMOVE_DROPOFF.replace(':id', selectedTemplateId);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ order_number: orderNumber })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the modal to show updated dropoffs
            handleViewTemplate(selectedTemplateId);
            showSuccess('Drop-off removed successfully');
        } else {
            showError(data.error || 'Failed to remove drop-off');
        }
    })
    .catch(error => {
        console.error('Error removing dropoff:', error);
        showError('Failed to remove drop-off');
    });
}

/**
 * Handle adding a return
 */
function handleAddReturn() {
    if (!selectedTemplateId) return;
    
    const url = window.ROUTES.WEB.JOBTEMPLATE.ADD_RETURN.replace(':id', selectedTemplateId);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the modal to show the return section
            handleViewTemplate(selectedTemplateId);
            showSuccess('Return added successfully');
        } else {
            showError(data.error || 'Failed to add return');
        }
    })
    .catch(error => {
        console.error('Error adding return:', error);
        showError('Failed to add return');
    });
}

/**
 * Handle removing a return
 */
function handleRemoveReturn() {
    if (!selectedTemplateId) return;
    if (!confirm('Are you sure you want to remove the return?')) return;

    const url = window.ROUTES.WEB.JOBTEMPLATE.REMOVE_RETURN.replace(':id', selectedTemplateId);

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            handleViewTemplate(selectedTemplateId);
            showSuccess('Return removed successfully');
        } else {
            showError(data.error || 'Failed to remove return');
        }
    })
    .catch(error => {
        console.error('Error removing return:', error);
        showError('Failed to remove return');
    });
}

/**
 * Handle return type toggle (Same Day vs Flexible)
 */
function handleReturnTypeToggle() {
    const toggle = document.getElementById('return-type-toggle');
    const timeContainer = document.getElementById('return-time-container');
    
    if (!toggle || !timeContainer) return;
    
    const isSameDay = toggle.checked;
    
    // Get current value if exists
    const currentTimeInput = timeContainer.querySelector('input');
    const currentValue = currentTimeInput ? currentTimeInput.value : '';
    
    // Rebuild the time input based on toggle state
    if (isSameDay) {
        // Same day - show only time input
        timeContainer.innerHTML = `
            <label class="form-label">Return Time</label>
            <input 
                type="time" 
                class="form-control inputs-forJobTemplate" 
                id="return-time-input"
                name="return_time"
                data-orgdata="${currentValue}"
                value="${currentValue}">
        `;
    } else {
        // Flexible - show datetime input
        timeContainer.innerHTML = `
            <label class="form-label">Return Date & Time</label>
            <input 
                type="datetime-local" 
                class="form-control inputs-forJobTemplate" 
                id="return-datetime-input"
                name="return_datetime"
                data-orgdata="${currentValue}"
                value="${currentValue}">
        `;
    }
}

/**
 * Handle price toggle (Fixed Price vs Dynamic)
 */
function handlePriceToggle() {
    const toggle = document.getElementById('is-price-fixed-toggle');
    const container = document.getElementById('price-input-container');
    const priceInput = document.getElementById('template-price');
    
    if (toggle && container) {
        if (toggle.checked) {
            container.style.display = 'block';
            if (priceInput && !priceInput.value) {
                priceInput.value = '0.00';
            }
        } else {
            container.style.display = 'none';
        }
    }
}

/**
 * Sanitize HTML to prevent XSS
 */
function sanitizeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// ==============================================
// COMMENTED OLD CODE - USE AS REFERENCE ONLY
// ==============================================
/*
window.clientIdSpanMap = new Map();

function sanitizeInput(input) {
  if (typeof input !== 'string') {
    return '';
  }
  
  const div = document.createElement('div');
  div.textContent = input;
  return div.innerHTML;
}

function safeSetText(element, text) {
  if (!element) return;
  element.textContent = text || '';
}

function safeSetAttribute(element, attr, value) {
  if (!element || !attr) return;
  
  if ((attr === 'href' || attr === 'src') && value) {
    if (value.startsWith('javascript:') || value.startsWith('data:')) {
      return;
    }
  }
  
  element.setAttribute(attr, sanitizeInput(value));
}

function getTimeInputElement(){
  const timeInput = document.createElement('input');
  timeInput.type = 'time';
  timeInput.className = 'form-control';
  timeInput.style.width = '100px';
  timeInput.style.marginRight = '10px';
  return timeInput;
}

// ... rest of old code ...
*/

