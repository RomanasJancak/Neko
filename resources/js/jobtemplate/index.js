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
    document.getElementById('template-client').addEventListener('change', updateClientAddresses);

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
                        <button class="btn-action btn-view" onclick="handleViewTemplate(${template.id})">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn-action btn-jobs" onclick="handleCreateJobsClick(${template.id})">
                            <i class="fas fa-plus-circle"></i> Create Jobs
                        </button>
                        <button class="btn-action btn-delete" onclick="handleDeleteTemplate(${template.id})">
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
        html += `<button class="btn btn-sm btn-outline-secondary" onclick="goToPage(1)">First</button>`;
        html += `<button class="btn btn-sm btn-outline-secondary" onclick="goToPage(${pagination.current_page - 1})">Previous</button>`;
    }

    if (pagination.current_page < pagination.last_page) {
        html += `<button class="btn btn-sm btn-outline-secondary" onclick="goToPage(${pagination.current_page + 1})">Next</button>`;
        html += `<button class="btn btn-sm btn-outline-secondary" onclick="goToPage(${pagination.last_page})">Last</button>`;
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
                    data-orgdata="${template.pickup_address_id || ''}">
                    <option value="">Select an address...</option>
                </select>
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
                            data-orgdata="${template.pickup_time_begin || ''}"
                            value="${template.pickup_time_begin || ''}">
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
        </form>
    `;

    body.innerHTML = html;

    // Load clients and set selected value
    loadEditClients(template.client_id);

    // Add event listener for client change to update addresses
    document.getElementById('edit-template-client').addEventListener('change', function() {
        updateEditClientAddresses(this.value);
    });

    // Set initial address value
    if (template.pickup_address_id) {
        setTimeout(() => {
            document.getElementById('edit-template-address').value = template.pickup_address_id;
        }, 500);
    }
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
            addressSelect.innerHTML = '<option value="">Select an address...</option>';
            
            if (data && Array.isArray(data)) {
                data.forEach(address => {
                    const option = document.createElement('option');
                    option.value = address.id;
                    option.textContent = address.name;
                    addressSelect.appendChild(option);
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
        const currentValue = input.value;

        // Only include fields that have changed
        if (originalValue !== currentValue) {
            // Use the name attribute directly - much cleaner!
            const fieldName = input.name;
            changedData[fieldName] = currentValue || null;
            hasChanges = true;
        }
    });

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

    fetch(API_BASE.CREATE_JOBS || '/jobtemplates/createJobsBatch', {
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

/**
 * Update available addresses based on selected client
 */
function updateClientAddresses() {
    const clientId = document.getElementById('template-client').value;
    console.log('Selected client ID:', clientId);
    console.log('',document.getElementById('template-client').value); 
    const addressSelect = document.getElementById('template-pickup-address');
    
    addressSelect.innerHTML = '<option value="">Select an address...</option>';

    if (!clientId) {
        return;
    }

    fetch(window.ROUTES.WEB.CLIENT.SEARCHADDRESSES + '?client_id=' + clientId)
        .then(response => response.json())
        .then(data => {
            if (data && Array.isArray(data)) {
                data.forEach(address => {
                    const option = document.createElement('option');
                    option.value = address.id;
                    option.textContent = sanitizeHtml(address.name);
                    addressSelect.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error loading addresses:', error));
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
    const pickupAddressId = document.getElementById('template-pickup-address').value;
    const pickupBegin = document.getElementById('template-pickup-begin').value;
    const pickupEnd = document.getElementById('template-pickup-end').value;

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
        pickup_address_id: pickupAddressId || null,
        pickup_time_begin: pickupBegin || null,
        pickup_time_end: pickupEnd || null,
        template_data: {}
    };

    fetch(API_BASE.STORE || '/jobtemplates', {
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

// ================================================================
// MAKE FUNCTIONS GLOBALLY AVAILABLE FOR ONCLICK HANDLERS
// ================================================================
window.fetchTemplates = fetchTemplates;
window.handleViewTemplate = handleViewTemplate;
window.handleCreateJobsClick = handleCreateJobsClick;
window.handleCreateTemplate = handleCreateTemplate;
window.handleUpdateTemplate = handleUpdateTemplate;
window.handleDeleteTemplate = handleDeleteTemplate;
window.goToPage = goToPage;
window.openCreateTemplateModal = openCreateTemplateModal;
window.closeCreateTemplateModal = closeCreateTemplateModal;
