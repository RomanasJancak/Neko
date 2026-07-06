// No jQuery imports needed! Fully standalone code.

function updateTask(data, url, submitButton) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && typeof show_Success_Message === 'function') {
            show_Success_Message({ message: data.message });
        }
    })
    .catch(error => {
        console.error('Error:', error.message);
    })
    .finally(() => {
        if (submitButton) submitButton.disabled = false;
    });
}

/**
 * Global Event-Delegated Vanilla Typeahead Engine
 */
function addVanillaTypeAheadToTask() {
    const MIN_LENGTH = 2;
    let matches = [];
    let activeIndex = -1;

    // Helper to get the elements dynamically whenever needed
    const getElements = () => ({
        // CHANGE 'taskClientNameField' HERE if your element ID is actually 'type-ahead-input'
        input: document.getElementById('taskClientNameField'), 
        dropdown: document.getElementById('dropdown')
    });

    function renderDropdown(items) {
        const { dropdown } = getElements();
        if (!dropdown) return;

        dropdown.innerHTML = "";

        if (!items.length) {
            dropdown.style.display = "none";
            return;
        }

        items.forEach((item, index) => {
            const div = document.createElement("div");
            div.className = "typeahead-dropdown-item";
            div.textContent = item.name;

            // mousedown fires before focus loss / blur
            div.addEventListener("mousedown", (e) => {
                e.preventDefault(); 
                selectItem(index);
            });

            dropdown.appendChild(div);
        });

        dropdown.style.display = "block";
    }

    function updateActiveItem() {
        const { dropdown } = getElements();
        if (!dropdown) return;

        const items = dropdown.querySelectorAll(".typeahead-dropdown-item");
        items.forEach(item => item.classList.remove("active"));

        if (activeIndex >= 0 && items[activeIndex]) {
            items[activeIndex].classList.add("active");
        }
    }

    function selectItem(index) {
        const { input } = getElements();
        const selectedItem = matches[index];
        if (input && selectedItem) {
            input.value = selectedItem.name;
        }
        closeDropdown();

        if (selectedItem && selectedItem.id) {
            const clientInfoUrlTemplate = window.ROUTES.WEB.ADDRESS.GETINFO;
            const clientInfoUrl = clientInfoUrlTemplate.replace(':id', selectedItem.id);
            
            fetch(clientInfoUrl)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        const postalCodeEl = document.getElementById('taskPostalCodeField');
                        const addressLineEl = document.getElementById('taskAddressLineField');
                        
                        if (postalCodeEl) postalCodeEl.value = data.postal_code || '';
                        if (addressLineEl) addressLineEl.value = (data.address_line_1 || '') + ' ' + (data.address_line_2 || '');     
                    }
                })
                .catch(error => console.error('Error fetching address info:', error));
        }
    }

    function closeDropdown() {
        const { dropdown } = getElements();
        if (dropdown) dropdown.style.display = "none";
        activeIndex = -1;
    }

    // 1. GLOBAL DELEGATED INPUT EVENT (Captures typing even if the element is swapped/re-rendered)
    document.addEventListener("input", (e) => {
        const { input } = getElements();
        if (!input || e.target !== input) return; // Ignore inputs from other fields

        const query = input.value;

        if (query.length < MIN_LENGTH) {
            closeDropdown();
            return;
        }

        let clientIdEl = document.getElementById('clientIdField') || document.getElementById('task_clientIdField');
        let client_id = clientIdEl ? clientIdEl.value : '';
        let apiUrl = window.ROUTES.WEB.CLIENT.SEARCHADDRESSES + "?query=" + encodeURIComponent(query) + "&client_id=" + client_id;

        console.log("Triggering fetch to API:", apiUrl); // Verify this fires in console

        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                matches = data; 
                activeIndex = -1;
                renderDropdown(matches);
            })
            .catch(error => {
                console.error('Error fetching client data via API:', error);
            });
    });

    // 2. GLOBAL DELEGATED KEYDOWN EVENT
    document.addEventListener("keydown", (e) => {
        const { input } = getElements();
        if (!input || e.target !== input || !matches.length) return;

        if (e.key === "ArrowDown") {
            e.preventDefault();
            activeIndex++;
            if (activeIndex >= matches.length) activeIndex = 0;
            updateActiveItem();
        }

        if (e.key === "ArrowUp") {
            e.preventDefault();
            activeIndex--;
            if (activeIndex < 0) activeIndex = matches.length - 1;
            updateActiveItem();
        }

        if (e.key === "Tab" || e.key === "Enter") {
            if (activeIndex >= 0) {
                e.preventDefault();
                selectItem(activeIndex);
            }
        }

        if (e.key === "Escape") {
            closeDropdown();
        }
    });

    // 3. GLOBAL CLICK OUTSIDE HANDLER
    document.addEventListener("click", (e) => {
        const { input, dropdown } = getElements();
        if (!input || !dropdown) return;
        
        if (e.target !== input && !dropdown.contains(e.target)) {
            closeDropdown();
        }
    });
}

function initTaskPage() {
    // 1. Check for your specific form submit button to confirm we are on a task page
    const submitBtn = document.getElementById('submitTaskform');
    if (!submitBtn) return; // Exit quietly if we aren't even on the task form view

    // 2. Set up Map Marker Action
    const mapMarker = document.getElementById('addressMapMarker');
    if (mapMarker) {
        mapMarker.addEventListener('click', function() {
            const addressLine = document.getElementById('taskAddressLineField')?.value || '';
            const postalCode = document.getElementById('taskPostalCodeField')?.value || '';
            const country = document.getElementById('taskCountryField')?.value || ''; 
            const city = document.getElementById('taskCityField')?.value || ''; 
            
            const query = encodeURIComponent(`${addressLine} ${postalCode} ${city} ${country}`.trim());
            const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${query}`;
            window.open(mapsUrl, '_blank');
        });
    }

    // 3. Set up Form Submission Processing
    submitBtn.addEventListener('click', function(event) {
        console.log("Submit button clicked, processing task form submission..."); // Debugging log
        event.preventDefault();
        let submitButton = event.target;
        const typeField = document.getElementById('taskTypeField');
        let route = '';
        const option = this.getAttribute('data-option');

        if (option === 'delete') route = window.ROUTES.WEB.TASK.DELETE;
        else if (option === 'update') route = window.ROUTES.WEB.TASK.UPDATE;
        else if (option === 'view') return;
        else if (option === 'create') route = window.ROUTES.WEB.TASK.STORE;

        if (route) {
            submitButton.disabled = true;
        } else {
            return;
        }

        var taskSubmitData = {
            jobId: document.getElementById('idField')?.value || '',
            id: document.getElementById('taskIdField')?.value || '',
            status_id: document.getElementById('taskStatusIdField')?.value || '',
            type: typeField?.value || '',
            address: {
                name: document.getElementById('taskClientNameField')?.value || '',
                postalCode: document.getElementById('taskPostalCodeField')?.value || '',
                addressLine: document.getElementById('taskAddressLineField')?.value || '',
            },
            time: {
                begin: (document.getElementById('jobDateField')?.value || '') + ' ' + (document.getElementById('taskTimeBegin')?.value || ''),
                end: (document.getElementById('jobDateField')?.value || '') + ' ' + (document.getElementById('taskTimeEnd')?.value || ''),
            },
            date: document.getElementById('jobDateField')?.value || '',
            hasCrateCollection: document.getElementById('crateCollection')?.checked || false,
            note: document.getElementById('taskNoteField')?.value || '',
        };

        if (typeField && typeField.value === 'dropOff') {
            taskSubmitData.package = {
                type: document.getElementById('packageTypeSelect')?.value || '',
                quantity: document.getElementById('quantityInput')?.value || 0,
                weight: document.getElementById('weightInput')?.value || 0,
            };
        }
        if (typeField && typeField.value === 'return') {
            taskSubmitData.returnTask = {
                is_flexible: document.getElementById('returnTask_isFlexible')?.checked || false,
                date: document.getElementById('taskTimeDate')?.value || '',
            };
        }

        updateTask(taskSubmitData, route, submitButton);
    });
}

/**
 * Watcher engine ensuring that async UI/Vite rendering does not bypass typeahead attachment
 */
function watchForTypeaheadElements() {
    const taskInput = document.getElementById('taskClientNameField');
    const dropdownMenu = document.getElementById('dropdown');

    // If they are immediately available, attach and stop
    if (taskInput && dropdownMenu) {
        addVanillaTypeAheadToTask(taskInput, dropdownMenu);
        initTaskPage();
        return;
    }

    // Otherwise, observe DOM mutations until they exist
    const observer = new MutationObserver((mutations, obs) => {
        const targetInput = document.getElementById('taskClientNameField');
        const targetDropdown = document.getElementById('dropdown');

        if (targetInput && targetDropdown) {
            addVanillaTypeAheadToTask(targetInput, targetDropdown);
            initTaskPage();
            obs.disconnect(); // Stop watching once found
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}
addVanillaTypeAheadToTask();
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTaskPage);
} else {
    initTaskPage();
}