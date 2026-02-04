
window.clientIdSpanMap = new Map();

/**
 * Sanitizes user input to prevent XSS attacks
 * @param {string} input - The input string to sanitize
 * @returns {string} - Sanitized string safe for DOM insertion
 */
function sanitizeInput(input) {
  if (typeof input !== 'string') {
    return String(input);
  }
  
  const div = document.createElement('div');
  div.textContent = input;
  return div.innerHTML;
}

/**
 * Safely sets text content, escaping HTML entities
 * @param {HTMLElement} element - The element to update
 * @param {string} text - The text to set
 */
function safeSetText(element, text) {
  if (!element) return;
  element.textContent = text || '';
}

/**
 * Safely sets an attribute value
 * @param {HTMLElement} element - The element to update
 * @param {string} attr - Attribute name
 * @param {string} value - Attribute value
 */
function safeSetAttribute(element, attr, value) {
  if (!element || !attr) return;
  
  // Prevent javascript: and data: URLs in href/src attributes
  if ((attr === 'href' || attr === 'src') && value) {
    const lowerValue = value.toLowerCase().trim();
    if (lowerValue.startsWith('javascript:') || lowerValue.startsWith('data:')) {
      console.warn('Blocked potentially malicious URL:', value);
      return;
    }
  }
  
  element.setAttribute(attr, sanitizeInput(value));
}

function getTimeInputElement(){
  const timeInput = document.createElement('input');
  timeInput.type = 'time';
  timeInput.className = 'form-control';
  timeInput.style.width = '200px';
  timeInput.style.position = 'absolute';
  timeInput.style.zIndex = 9999;

  return timeInput;
}
function enableTimeEditing(span, updateField, itemId, initialValue) {
  safeSetText(span, convertTo12Hour(initialValue.split(' ')[1]?.substring(0, 5)));
  span.className = 'text-muted';
  span.setAttribute('data-updatefield', updateField);
  span.setAttribute('data-template-id', itemId);

  span.addEventListener('click', () => {
    const timeInput = getTimeInputElement();
    const currentTime = span.textContent.trim();
    timeInput.value = convertTo24Hour(currentTime);
    const rect = span.getBoundingClientRect();
    timeInput.style.left = `${rect.left + window.scrollX}px`;
    timeInput.style.top = `${rect.top + window.scrollY}px`;

    document.body.appendChild(timeInput);
    timeInput.focus();

    const removeInput = () => {
      const selectedTime = timeInput.value;
      if (selectedTime) {
        safeSetText(span, convertTo12Hour(selectedTime));
      }
      document.body.removeChild(timeInput);
    };

    timeInput.addEventListener('blur', () => {
      removeInput();
      const field = buildNestedObject(updateField,timeInput.value);
      updateJobTemplate(itemId,field);
    });
    span.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        span.click();
      }
    });
  });
  
}
function convertTo12Hour(time24) {
  const [hour, minute] = time24.split(':');
  let h = parseInt(hour, 10);
  const ampm = h >= 12 ? 'PM' : 'AM';
  h = h % 12 || 12;
  return `${h}:${minute} ${ampm}`;
}
function convertTo24Hour(time12) {
  const [time, modifier] = time12.split(' ');
  let [hours, minutes] = time.split(':');

  hours = parseInt(hours, 10);
  if (modifier === 'PM' && hours !== 12) hours += 12;
  if (modifier === 'AM' && hours === 12) hours = 0;

  return `${String(hours).padStart(2, '0')}:${minutes}`;
}
function addTypeHeadSearch_fromClientList(editableSpan) {
    if (!editableSpan) return;

    const dropdown = document.createElement("ul");
    dropdown.style.position = "absolute";
    dropdown.style.border = "1px solid #ccc";
    dropdown.style.listStyle = "none";
    dropdown.style.margin = "0";
    dropdown.style.padding = "0";
    dropdown.style.maxHeight = "200px";
    dropdown.style.overflowY = "auto";
    dropdown.style.zIndex = "1000";
    dropdown.style.display = "none";

    dropdown.classList.add('bg-dark', 'text-light');

    document.body.appendChild(dropdown);

    let currentItems = [];
    let selectedIndex = -1;

    editableSpan.addEventListener("input", onInput);
    editableSpan.addEventListener("keydown", onKeyDown);

    function onInput() {
        const query = editableSpan.textContent.trim();
        if (query.length < 2) {
            dropdown.style.display = "none";
            return;
        }

        const rect = editableSpan.getBoundingClientRect();
        dropdown.style.top = `${rect.bottom + window.scrollY}px`;
        dropdown.style.left = `${rect.left + window.scrollX}px`;
        //dropdown.style.width = `${rect.width}px`;
        dropdown.style.width = `200px`;

        const apiUrl = window.ROUTES.WEB.CLIENT.SEARCH + "?query=" + encodeURIComponent(query);
        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                currentItems = data;
                selectedIndex = -1;
                renderDropdown(data);
            })
            .catch(error => {
                console.error('Error fetching client data:', error);
            });
    }

    function renderDropdown(items) {
        dropdown.innerHTML = "";

        if (!items.length) {
            dropdown.style.display = "none";
            return;
        }

        items.forEach((item, index) => {
            const li = document.createElement("li");
            safeSetText(li, item.name);
            li.style.padding = "5px 10px";
            li.style.cursor = "pointer";
            li.classList.add('bg-dark', 'text-light');

            li.addEventListener("mouseenter", () => {
                highlightItem(index);
            });

            li.addEventListener("mousedown", (e) => {
                e.preventDefault(); // prevent blur
                handleSelect(item);
                dropdown.style.display = "none";
            });

            dropdown.appendChild(li);
        });

        dropdown.style.display = "block";
    }

    function highlightItem(index) {
      const lis = dropdown.querySelectorAll("li");
      lis.forEach((li, i) => {
        if(li.classList.contains('bg-secondary')){
          li.classList.toggle('bg-dark');
          li.classList.toggle('text-light');
          li.classList.toggle('bg-secondary');
          li.classList.toggle('text-white');
        }
        if (i === index) {
          if(li.classList.contains('bg-secondary')){}else{
            li.classList.toggle('bg-dark');
            li.classList.toggle('text-light');
            li.classList.toggle('bg-secondary');
            li.classList.toggle('text-white');
          }
          li.scrollIntoView({ block: "nearest", behavior: "smooth" });
        }
        else 
        {
        //   li.classList.toggle('bg-dark');
        //   li.classList.toggle('text-light');
        //   li.classList.toggle('bg-secondary');
        //   li.classList.toggle('text-white');
        }
      });
      selectedIndex = index;
    }

    function onKeyDown(e) {
        const lis = dropdown.querySelectorAll("li");

        if (dropdown.style.display === "block") {
            if (e.key === "ArrowDown") {
                e.preventDefault();
                selectedIndex = (selectedIndex + 1) % lis.length;
                highlightItem(selectedIndex);
            } else if (e.key === "ArrowUp") {
                e.preventDefault();
                selectedIndex = (selectedIndex - 1 + lis.length) % lis.length;
                highlightItem(selectedIndex);
            } else if (e.key === "Enter") {
                e.preventDefault();
                if (selectedIndex >= 0 && currentItems[selectedIndex]) {
                    handleSelect(currentItems[selectedIndex]);
                    dropdown.style.display = "none";
                }
            } else if (e.key === "Escape") {
                dropdown.style.display = "none";
            }
        }
    }

    function handleSelect(item) {
        safeSetText(editableSpan, item.name);
        editableSpan.blur(); // <-- Blur here

        const clientInfoUrlTemplate = window.ROUTES.WEB.CLIENT.GETINFO;
        const clientInfoUrl = clientInfoUrlTemplate.replace(':id', item.id);
        fetch(clientInfoUrl)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    editableSpan.setAttribute('data-client-id', `${data.id}`);
                    updateJobTemplate(editableSpan.getAttribute('data-template-id'), { 'clientToBill_id': editableSpan.getAttribute('data-client-id') });
                    clientIdSpanMap.get(Number(editableSpan.getAttribute('data-template-id'))).forEach(span => span.setAttribute('data-client-id', data.id));
                }
            })
            .catch(error => {
                console.error(error);
            });
    }


    document.addEventListener("click", function (e) {
        if (!editableSpan.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = "none";
        }
    });

    // Utility to darken color (basic RGB manipulation)
    function darkenColor(color, factor) {
        const ctx = document.createElement('canvas').getContext('2d');
        ctx.fillStyle = color;
        const [r, g, b] = ctx.fillStyle.match(/\d+/g).map(Number);
        return `rgb(${Math.max(0, r - r * factor)}, ${Math.max(0, g - g * factor)}, ${Math.max(0, b - b * factor)})`;
    }
}
function addTypeHeadSearch_fromClient_AddressList(editableSpan) {
    if (!editableSpan) return;
    const dropdown = document.createElement("ul");
    dropdown.style.position = "absolute";
    dropdown.style.border = "1px solid #ccc";
    dropdown.style.listStyle = "none";
    dropdown.style.margin = "0";
    dropdown.style.padding = "0";
    dropdown.style.maxHeight = "200px";
    dropdown.style.overflowY = "auto";
    dropdown.style.zIndex = "1000";
    dropdown.style.display = "none";

    // Match colors from span
    dropdown.classList.add('bg-dark', 'text-light');

    document.body.appendChild(dropdown);

    let currentItems = [];
    let selectedIndex = -1;

    editableSpan.addEventListener("input", onInput);
    editableSpan.addEventListener("keydown", onKeyDown);

    function onInput() {
        const query = editableSpan.textContent.trim();
        if (query.length < 2) {
            dropdown.style.display = "none";
            return;
        }

        const rect = editableSpan.getBoundingClientRect();
        dropdown.style.top = `${rect.bottom + window.scrollY}px`;
        dropdown.style.left = `${rect.left + window.scrollX}px`;
        //dropdown.style.width = `${rect.width}px`;
        dropdown.style.width = `200px`;
        var apiUrl = window.ROUTES.WEB.CLIENT.SEARCHADDRESSES+"?query=" + encodeURIComponent(query) + "&client_id=" + editableSpan.getAttribute('data-client-id');
        //const apiUrl = window.ROUTES.WEB.CLIENT.SEARCH + "?query=" + encodeURIComponent(query);
        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                currentItems = data;
                selectedIndex = -1;
                renderDropdown(data);
            })
            .catch(error => {
                console.error('Error fetching client data:', error);
            });
    }

    function renderDropdown(items) {
        dropdown.innerHTML = "";

        if (!items.length) {
            dropdown.style.display = "none";
            return;
        }

        items.forEach((item, index) => {
            const li = document.createElement("li");
            safeSetText(li, item.name);
            li.style.padding = "5px 10px";
            li.style.cursor = "pointer";
            li.classList.add('bg-dark', 'text-light');

            li.addEventListener("mouseenter", () => {
                highlightItem(index);
            });

            li.addEventListener("mousedown", (e) => {
                e.preventDefault(); // prevent blur
                handleSelect(item);
                dropdown.style.display = "none";
            });

            dropdown.appendChild(li);
        });

        dropdown.style.display = "block";
    }

    function highlightItem(index) {
      const lis = dropdown.querySelectorAll("li");
      lis.forEach((li, i) => {
          if (i === index) {
              li.style.backgroundColor = darkenColor(window.getComputedStyle(li).backgroundColor, 0.1);
              li.style.color = darkenColor(window.getComputedStyle(li).color, 0.1);
              li.scrollIntoView({ block: "nearest", behavior: "smooth" });
          } else {
              //li.style.backgroundColor = styles.backgroundColor;
              //li.style.color = styles.color;
          }
      });
      selectedIndex = index;
    }

    function onKeyDown(e) {
        const lis = dropdown.querySelectorAll("li");

        if (dropdown.style.display === "block") {
            if (e.key === "ArrowDown") {
                e.preventDefault();
                selectedIndex = (selectedIndex + 1) % lis.length;
                highlightItem(selectedIndex);
            } else if (e.key === "ArrowUp") {
                e.preventDefault();
                selectedIndex = (selectedIndex - 1 + lis.length) % lis.length;
                highlightItem(selectedIndex);
            } else if (e.key === "Enter") {
                e.preventDefault();
                if (selectedIndex >= 0 && currentItems[selectedIndex]) {
                    handleSelect(currentItems[selectedIndex]);
                    dropdown.style.display = "none";
                }
            } else if (e.key === "Escape") {
                dropdown.style.display = "none";
            }
        }
    }

    function handleSelect(item) {
        safeSetText(editableSpan, item.name);
        editableSpan.blur(); // <-- Blur here

        const clientInfoUrlTemplate = window.ROUTES.WEB.ADDRESS.GETINFO;
        const clientInfoUrl = clientInfoUrlTemplate.replace(':id', item.id);
        fetch(clientInfoUrl)
            .then(response => response.json())
            .then(data => {
                if (data) {
                  editableSpan.setAttribute('data-address-id', `${data.id}`);
                  const nextSpan = editableSpan.nextElementSibling;
                  if (nextSpan && nextSpan.classList.contains('full-address')) {
                    safeSetText(nextSpan, `${data.address_line_1}), (${data.postal_code}`);
                  }
                }
                /* pritaikyti kad tiktu ir droppoffui */
                updateJobTemplate(
                  editableSpan.getAttribute('data-template-id'),
                  buildNestedObject(editableSpan.getAttribute('data-updatefield'), item.id));
                  //{ pickup :{addressId: item.id}});

            })
            .catch(error => {
                console.error(error);
            });
    }


    document.addEventListener("click", function (e) {
        if (!editableSpan.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = "none";
        }
    });

    // Utility to darken color (basic RGB manipulation)
    function darkenColor(color, factor) {
        const ctx = document.createElement('canvas').getContext('2d');
        ctx.fillStyle = color;
        const [r, g, b] = ctx.fillStyle.match(/\d+/g).map(Number);
        return `rgb(${Math.max(0, r - r * factor)}, ${Math.max(0, g - g * factor)}, ${Math.max(0, b - b * factor)})`;
    }
}
function addPackageTypeSelect_fromClient(editableSpan) {
  if (!editableSpan) return;

  const dropdown = document.createElement("ul");
  dropdown.style.position = "absolute";
  dropdown.style.border = "1px solid #ccc";
  dropdown.style.listStyle = "none";
  dropdown.style.margin = "0";
  dropdown.style.padding = "0";
  dropdown.style.maxHeight = "200px";
  dropdown.style.overflowY = "auto";
  dropdown.style.zIndex = "1000";
  dropdown.style.display = "none";
  dropdown.style.width = "200px";
  dropdown.classList.add('bg-dark', 'text-light');

  document.body.appendChild(dropdown);

  editableSpan.addEventListener("click", () => {
    const clientId = editableSpan.getAttribute("data-client-id");
    if (!clientId) return;

    const apiUrl = window.ROUTES.WEB.CLIENT.FETCHPACKAGETYPES.replace(":id", clientId);

    const rect = editableSpan.getBoundingClientRect();
    dropdown.style.top = `${rect.bottom + window.scrollY}px`;
    dropdown.style.left = `${rect.left + window.scrollX}px`;

    fetch(apiUrl)
      .then(response => response.json())
      .then(data => {
        dropdown.innerHTML = "";

        data.packageTypes.forEach(packageType => {
          const li = document.createElement("li");
          safeSetText(li, packageType.name);
          li.style.padding = "5px 10px";
          li.style.cursor = "pointer";
          li.classList.add('bg-dark', 'text-light');

          li.addEventListener("mousedown", (e) => {
            e.preventDefault();
            handleSelect(packageType);
            dropdown.style.display = "none";
          });

          dropdown.appendChild(li);
        });

        dropdown.style.display = "block";
      })
      .catch(error => {
        console.error("Error fetching package types:", error);
      });
  });

  function handleSelect(item) {
    safeSetText(editableSpan, item.name);
    editableSpan.blur();

    // Send selected package type ID to backend
    buildNestedObject(editableSpan.getAttribute('data-updatefield'), item.id);
    updateJobTemplate(editableSpan.getAttribute('data-template-id'), buildNestedObject(editableSpan.getAttribute('data-updatefield'), item.id));
  }

  document.addEventListener("click", function (e) {
    if (!editableSpan.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.style.display = "none";
    }
  });
}
function updateJobTemplate(id,field){
  const routeUrl = window.ROUTES.WEB.JOBTEMPLATE.UPDATE;
  let updateData = {
    id: id,
    ...field
  }
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  fetch(window.ROUTES.WEB.JOBTEMPLATE.UPDATE, { 
      method: 'PATCH',
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
      }else if(data.success){
        show_Success_Message({message : data.message});
      }
  })
  .catch(error => {
      console.error('Error:', error.message);
  });
}
function buildNestedObject(path,value){
  const keys = path.split('.');
  return keys.reduceRight((acc, key) => {
    return { [key]: acc };
  }, value);
}
function lockIconChanger(span,id,element = null) {
  span.addEventListener('click', () => {
    const icon = span.querySelector('i');
    icon.classList.toggle('fa-lock');
    icon.classList.toggle('text-danger');
    icon.classList.toggle('fa-unlock');
    const path = span.getAttribute('data-updatefield');
    const field = buildNestedObject(path, !icon.classList.contains('fa-unlock'));

    updateJobTemplate(id, field);
  });
  span.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      span.click();
    }
  });

}
function handleClientParagraph(item, element = null){  
  const client = item.clientToBill;
  const clientParagraph = document.createElement('p');
  const clientName = client ? client.name : 'N/A';
  const clientClass = client ? '' : 'text-danger';
  const spanForIcon = document.createElement('span');
  safeSetAttribute(spanForIcon, 'data-updatefield', 'locks.client');
  spanForIcon.style.cursor = 'pointer'; 
  const icon = document.createElement('i');
  const iconClass = client.isLocked ? 'fa fa-lock text-danger' : 'fa fa-unlock';
  icon.className = iconClass;
  safeSetAttribute(icon, 'aria-hidden', 'true');
  spanForIcon.appendChild(icon);
  lockIconChanger(spanForIcon,item.id,element);
  if(element){
    element.locks ? element.locks.push(spanForIcon) : element.locks = [spanForIcon];
  }
  clientParagraph.appendChild(spanForIcon);
  const spanForClientIdentifier = document.createElement('span');
  safeSetText(spanForClientIdentifier, "Client: ");
  spanForClientIdentifier.className = 'card-text';
  clientParagraph.appendChild(spanForClientIdentifier);
  const spanForName = document.createElement('span');
  safeSetText(spanForName, clientName);
  spanForName.className = clientClass;
  spanForName.classList.add('border-bottom');
  spanForName.setAttribute('data-template-id', item.id);
  spanForName.setAttribute('data-client-id', client ? item.clientToBill.id : '');
  if (!clientIdSpanMap.has(item.id)) {
    clientIdSpanMap.set(item.id, []);
  }
  clientIdSpanMap.get(item.id).push(spanForName);
  clientParagraph.appendChild(spanForName);
  addTypeHeadSearch_fromClientList(spanForName);
  const editIconSpan = document.createElement('span');
  editIconSpan.className = 'edit-pencil';
  editIconSpan.innerHTML = '<i class="fa fa-pencil" aria-hidden="true"></i>';
  editIconSpan.style.cursor = 'pointer';
  editIconSpan.addEventListener('click', () => {
    spanForName.contentEditable = true;
    spanForName.focus();
    spanForName.addEventListener('blur', function onBlur() {
      spanForName.contentEditable = false;
      spanForName.removeEventListener('blur', onBlur);
    });
  });
  editIconSpan.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      editIconSpan.click();
    }
  });
  if(element){
    element.clientNameEditIcon = editIconSpan;
  }
  editIconSpan.setAttribute('tabindex', '-1');
  clientParagraph.appendChild(editIconSpan);

  return {paragraph : clientParagraph,clientSpan : spanForName};
}
function handlePickupParagraph(item,clientSpan,element = null) {
  const paragraph = document.createElement('p');
  const spanForIcon = document.createElement('span');
  const icon = document.createElement('i');
  icon.className = item.pickuptask.isLocked ? 'fa fa-lock text-danger' : 'fa fa-unlock';
  icon.setAttribute('aria-hidden', 'true');
  spanForIcon.appendChild(icon);
  spanForIcon.setAttribute('data-updatefield', 'locks.pickup');
  spanForIcon.style.cursor = 'pointer';
  lockIconChanger(spanForIcon,item.id,element);
  if(element){
    element.locks ? element.locks.push(spanForIcon) : element.locks = [spanForIcon];
  }
  const label = document.createElement('strong');
  safeSetText(label, 'Pickup address: ');

  const spanForName = document.createElement('span');
  safeSetText(spanForName, item.pickuptask.data.pickupclientname || 'N/A');
  spanForName.setAttribute('data-client-id', clientSpan.getAttribute('data-client-id') || '');
  spanForName.setAttribute('data-template-id', item.id);
  if (!clientIdSpanMap.has(item.id)) {
    clientIdSpanMap.set(item.id, []);
  }
  clientIdSpanMap.get(item.id).push(spanForName);
  spanForName.setAttribute('data-updatefield', 'pickup.addressId');
  addTypeHeadSearch_fromClient_AddressList(spanForName);
  const fullAddress = item.pickuptask.data.pickupclientaddressline+', '+item.pickuptask.data.pickupclientpostalcode;
  const addressSpan = document.createElement('span');
  safeSetText(addressSpan, fullAddress ?  '('+fullAddress+')' : '');
  addressSpan.className = 'text-muted full-address';

  const editIconSpan = document.createElement('span');
  editIconSpan.className = 'edit-pencil';
  editIconSpan.innerHTML = '<i class="fa fa-pencil" aria-hidden="true"></i>';
  editIconSpan.style.cursor = 'pointer';
  editIconSpan.addEventListener('click', () => {
    spanForName.contentEditable = true;
    spanForName.focus();
    spanForName.addEventListener('blur', function onBlur() {
      spanForName.contentEditable = false;
      spanForName.removeEventListener('blur', onBlur);
    });
  });
  editIconSpan.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      editIconSpan.click();
    }
  });
  if(element){
    element.clientNameEditIcon = editIconSpan;
  }
  editIconSpan.setAttribute('tabindex', '-1');
  const timeWindowSpan = document.createElement('span');

  const timeWindowBeginSpan = document.createElement('span');
  const timeWindowEndSpan = document.createElement('span');
  enableTimeEditing(timeWindowBeginSpan, 'pickup.time.begin', item.id, item.pickuptask.data.pickup_time_begin);
  enableTimeEditing(timeWindowEndSpan, 'pickup.time.end', item.id, item.pickuptask.data.pickup_time_end);
  const editIconSpanTimeBegin = document.createElement('span');
  editIconSpanTimeBegin.className = 'edit-pencil';
  editIconSpanTimeBegin.innerHTML = '<i class="fa fa-pencil" aria-hidden="true"></i>';
  editIconSpanTimeBegin.style.cursor = 'pointer';
  editIconSpanTimeBegin.addEventListener('click', () => {
    timeWindowBeginSpan.contentEditable = true;
    timeWindowBeginSpan.focus();
  });
  editIconSpanTimeBegin.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      editIconSpanTimeBegin.click();
    }
  });
  const editIconSpanTimeEnd = document.createElement('span');
  editIconSpanTimeEnd.className = 'edit-pencil';
  editIconSpanTimeEnd.innerHTML = '<i class="fa fa-pencil" aria-hidden="true"></i>';
  editIconSpanTimeEnd.style.cursor = 'pointer';
  editIconSpanTimeEnd.addEventListener('click', () => {
    timeWindowEndSpan.contentEditable = true;
    timeWindowEndSpan.focus();
  });
  editIconSpanTimeEnd.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      editIconSpanTimeEnd.click();
    }
  });
  //=========================================================
  const notesIconSpan = document.createElement('span');
  notesIconSpan.className = 'notes-icon';
  notesIconSpan.innerHTML = '<i class="fa fa-sticky-note" aria-hidden="true"></i>';
  notesIconSpan.style.cursor = 'pointer';
  item.pickuptask.data.note ? notesIconSpan.classList.add('text-success') : notesIconSpan.classList.remove('text-success');
  notesIconSpan.addEventListener('click', () => {

    const existingTextarea = notesIconSpan.querySelector('textarea');
    if (existingTextarea) return;

    const textarea = document.createElement('textarea');
    textarea.value = item.pickuptask.data.note || '';
    textarea.style.width = '200px';
    textarea.style.height = '80px';
    textarea.style.resize = 'vertical';
    textarea.style.display = 'block';
    textarea.style.marginTop = '5px';

    notesIconSpan.appendChild(textarea);
    textarea.focus();

    textarea.addEventListener('blur', () => {
      const note = textarea.value.trim();
      item.pickuptask.data.note = note;
      item.pickuptask.data.note ? notesIconSpan.classList.add('text-success') : notesIconSpan.classList.remove('text-success');

      updateJobTemplate(item.id, buildNestedObject(`pickup.note`, note));
      textarea.remove();
    });
  });
  
  notesIconSpan.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      notesIconSpan.click();
    }
  });
  if(element){
    element.pickupTimeBegin = timeWindowBeginSpan;
    element.pickupTimeEnd = timeWindowEndSpan;
    element.pickupNotesIcon = notesIconSpan;
    timeWindowBeginSpan.setAttribute('tabindex', '-1');
    timeWindowEndSpan.setAttribute('tabindex', '-1');
    notesIconSpan.setAttribute('tabindex', '-1');
  }
  let hoverTimeout;
  notesIconSpan.addEventListener('mouseenter', () => {
    hoverTimeout = setTimeout(() => {
      notesIconSpan.setAttribute('title', item.pickuptask.data.note || 'No notes');
    }, 300);
  });
  notesIconSpan.addEventListener('mouseleave', () => {
    clearTimeout(hoverTimeout);
  });
  //=========================================================


  timeWindowSpan.appendChild(timeWindowBeginSpan);
  timeWindowSpan.appendChild(editIconSpanTimeBegin);
  timeWindowSpan.appendChild(document.createTextNode(' - '));
  timeWindowSpan.appendChild(timeWindowEndSpan);
  timeWindowSpan.appendChild(editIconSpanTimeEnd);
  paragraph.appendChild(spanForIcon);
  paragraph.appendChild(label);
  paragraph.appendChild(spanForName);
  paragraph.appendChild(editIconSpan);
  paragraph.appendChild(document.createElement('br'));
  paragraph.appendChild(addressSpan);
  
  paragraph.appendChild(document.createElement('br'));
  paragraph.appendChild(timeWindowSpan);
  paragraph.appendChild(document.createElement('br'));
  paragraph.appendChild(notesIconSpan);
  return paragraph;
}
function getDropOffParagraph({dropOff,item,clientSpan,element = null}) {
  const dropOffParagraph = document.createElement('p');
  dropOffParagraph.className = 'drop-off-item';
  dropOffParagraph.classList.add('border', 'border-secondary', 'rounded', 'p-2', 'mb-2');
  dropOffParagraph.setAttribute('data-template-id', item.id);
  const spanPackageName = document.createElement('span');
  
  spanPackageName.className = 'package-name';
  spanPackageName.setAttribute('data-template-id', item.id);
  spanPackageName.setAttribute('data-client-id', clientSpan.getAttribute('data-client-id') || '');
  addPackageTypeSelect_fromClient(spanPackageName);
  spanPackageName.addEventListener('click', () => {
    spanPackageName.contentEditable = true;
    spanPackageName.focus();
      spanPackageName.addEventListener('blur', function onBlur() {
        spanPackageName.contentEditable = false;
        
        spanPackageName.removeEventListener('blur', onBlur);
      });
    }); 
  spanPackageName.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      spanPackageName.click();
    }
  });
  if (element) {
    element.packageNames ? element.packageNames.push(spanPackageName) : element.packageNames = [spanPackageName];
  }
  const editIconSpan = document.createElement('span');
  editIconSpan.className = 'edit-pencil';
  editIconSpan.innerHTML = '<i class="fa fa-pencil" aria-hidden="true"></i>';
  editIconSpan.style.cursor = 'pointer';
  editIconSpan.addEventListener('click', () => {
    spanPackageName.click();
  } );
  editIconSpan.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      editIconSpan.click();
    }
  });
  if (element) {
    element.packageNameEditIcon ? element.packageNameEditIcon.push(editIconSpan) : element.packageNameEditIcon = [editIconSpan];
  }
  var packageQuantity = 1;
  var pakuote = null;
  const spanForPackageQuantity = document.createElement('span');
  
  safeSetText(spanForPackageQuantity, packageQuantity);
  spanForPackageQuantity.className = 'text-muted';
  spanForPackageQuantity.setAttribute('data-template-id', item.id);
  if(!dropOff) {
    safeSetText(spanPackageName, "select package");
    //spanPackageName.setAttribute('data-package-id', pakuote.id);
    spanPackageName.setAttribute('data-updatefield', `drop.new.packageTypeId`); // IMPORTANT FOR BACKEND ADJUST NEW POSIBILITY OF NEW PACKAGE
    spanForPackageQuantity.setAttribute('data-updatefield', `drop.new.packageQuantity`);
  }else{
    dropOffParagraph.setAttribute('data-dropoff-orderNumber', dropOff.order_number);
    if(!(pakuote = dropOff.package)){
      return document.createElement('div');
    };
    safeSetText(spanPackageName, pakuote.package_type.name);
    spanPackageName.setAttribute('data-package-id', pakuote.id);
    spanPackageName.setAttribute('data-updatefield', `drop.${dropOff.order_number}.packageTypeId`);
    packageQuantity = pakuote.quantity;
    spanForPackageQuantity.setAttribute('data-updatefield', `drop.${dropOff.order_number}.packageQuantity`);
  }

  
  spanForPackageQuantity.setAttribute('data-client-id', clientSpan.getAttribute('data-client-id') || '');
  spanForPackageQuantity.addEventListener('click', () => {
    spanForPackageQuantity.contentEditable = true;
    spanForPackageQuantity.focus();

      const oldValue = spanForPackageQuantity.textContent.trim();

      function onBlur() {
        spanForPackageQuantity.contentEditable = false;

        const rawValue = spanForPackageQuantity.textContent.trim();
        const value = Number(rawValue);

        if (!Number.isInteger(value)) {
          alert("Please enter a valid integer.");
          safeSetText(spanForPackageQuantity, oldValue); // restore
        } else if (value < 0) {
          alert("Quantity cannot be negative.");
          safeSetText(spanForPackageQuantity, oldValue); // restore
        } else if (value === 0) {
          if (confirm("Quantity is 0. Do you want to delete this package?")) {
            updateJobTemplate(
              item.id,
              buildNestedObject(spanForPackageQuantity.getAttribute('data-updatefield'), 0)
            );
          } else {
            safeSetText(spanForPackageQuantity, oldValue); // restore
          }
        } else {
          updateJobTemplate(
            item.id,
            buildNestedObject(spanForPackageQuantity.getAttribute('data-updatefield'), value)
          );
        }

        spanForPackageQuantity.removeEventListener('blur', onBlur);
      }
      spanForPackageQuantity.addEventListener('blur', onBlur);
    });
  const editIconSpanQuantity = document.createElement('span');
  editIconSpanQuantity.className = 'edit-pencil';
  editIconSpanQuantity.innerHTML = '<i class="fa fa-pencil" aria-hidden="true"></i>';
  editIconSpanQuantity.style.cursor = 'pointer';
  editIconSpanQuantity.addEventListener('click', () => {
    spanForPackageQuantity.click();
  });
  editIconSpanQuantity.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      editIconSpanQuantity.click();
    }
  });
  if (element) {
    element.packageQuantityEditIcon ? element.packageQuantityEditIcon.push(editIconSpanQuantity) : element.packageQuantityEditIcon = [editIconSpanQuantity];
  }
  spanForPackageQuantity.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      spanForPackageQuantity.click();
    }
  });
    if (element) {
      element.packageQuantities ? element.packageQuantities.push(spanForPackageQuantity) : element.packageQuantities = [spanForPackageQuantity];
    }
    //=================================================================
    const divForAddress = document.createElement('div');
    const spanForAddressName = document.createElement('span');
    const textContentnForspanForAddressName = dropOff ? !dropOff.address ? pakuote.dropoff_name	: dropOff.address.name : '';
    safeSetText(spanForAddressName, textContentnForspanForAddressName);
    divForAddress.appendChild(spanForAddressName);
    
    divForAddress.className = 'drop-off-address';
    spanForAddressName.setAttribute('data-client-id', clientSpan.getAttribute('data-client-id') || '');
    spanForAddressName.setAttribute('data-template-id', item.id);
    if(!dropOff) {
      spanForAddressName.setAttribute('data-updatefield', `drop.new.addressId`);
      spanForAddressName.setAttribute('data-address-id', '');
    } else {
      spanForAddressName.setAttribute('data-updatefield', `drop.${dropOff.order_number}.addressId`);
      spanForAddressName.setAttribute('data-address-id', !dropOff.address ? '' : dropOff.address.id);
    }
    
    addTypeHeadSearch_fromClient_AddressList(spanForAddressName);
    spanForAddressName.addEventListener('click', () => {
      spanForAddressName.contentEditable = true;
      spanForAddressName.focus();
      spanForAddressName.addEventListener('blur', function onBlur() {
        spanForAddressName.contentEditable = false;
        spanForAddressName.removeEventListener('blur', onBlur);
      });
    });
    var fullAddress = '';
    if(!dropOff) {
      fullAddress = '';
    }else{
      if(dropOff.address) {
        fullAddress = dropOff.address.address_line_1+', '+dropOff.address.postal_code;
      }else{
        fullAddress = pakuote.dropoff_address_line+', '+pakuote.dropoff_postal_code;
      }
    }
    const addressSpan = document.createElement('span');
    safeSetText(addressSpan, fullAddress ?  '('+fullAddress+')' : '');
    addressSpan.className = 'text-muted full-address';
    
    const editIconSpanAddress = document.createElement('span');
    editIconSpanAddress.className = 'edit-pencil';
    editIconSpanAddress.innerHTML = '<i class="fa fa-pencil" aria-hidden="true"></i>';
    editIconSpanAddress.style.cursor = 'pointer';
    editIconSpanAddress.addEventListener('click', () => {
      spanForAddressName.contentEditable = true;
      spanForAddressName.focus();
    });
    editIconSpanAddress.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        editIconSpanAddress.click();
      }
    });
    if (element) {
      element.dropOffAddressEditIcon ? element.dropOffAddressEditIcon.push(editIconSpanAddress) : element.dropOffAddressEditIcon = [editIconSpanAddress];
    }
    divForAddress.appendChild(editIconSpanAddress);
    divForAddress.appendChild(document.createElement('br'));
    divForAddress.appendChild(addressSpan);
    //=================================================================
    const timeWindowSpan = document.createElement('span');
    const timeWindowBeginSpan = document.createElement('span');
    const timeWindowEndSpan = document.createElement('span');
    if(!dropOff){
      enableTimeEditing(timeWindowBeginSpan, `drop.new.time.begin`, item.id, '0000-01-01 00:00:00');
      enableTimeEditing(timeWindowEndSpan, `drop.new.time.end`, item.id, '0000-01-01 00:00:00');
    }else{
      enableTimeEditing(timeWindowBeginSpan, `drop.${dropOff.order_number}.time.begin`, item.id, pakuote.packagedropofftimebegin);
      enableTimeEditing(timeWindowEndSpan, `drop.${dropOff.order_number}.time.end`, item.id, pakuote.packagedropofftimeend);
    }
    const editIconSpanTimeBegin = document.createElement('span');
    editIconSpanTimeBegin.className = 'edit-pencil';
    editIconSpanTimeBegin.innerHTML = '<i class="fa fa-pencil" aria-hidden="true"></i>';
    editIconSpanTimeBegin.style.cursor = 'pointer';
    editIconSpanTimeBegin.addEventListener('click', () => {
      timeWindowBeginSpan.click();
    }); 
    editIconSpanTimeBegin.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        editIconSpanTimeBegin.click();
      }
    });
    const editIconSpanTimeEnd = document.createElement('span');
    editIconSpanTimeEnd.className = 'edit-pencil';
    editIconSpanTimeEnd.innerHTML = '<i class="fa fa-pencil" aria-hidden="true"></i>';
    editIconSpanTimeEnd.style.cursor = 'pointer';
    editIconSpanTimeEnd.addEventListener('click', () => {
      timeWindowEndSpan.click();
    });
    editIconSpanTimeEnd.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        editIconSpanTimeEnd.click();
      }
    });
    if (element) {
      element.dropOffTimeBegin ? element.dropOffTimeBegin.push(timeWindowBeginSpan) : element.dropOffTimeBegin = [timeWindowBeginSpan];
      element.dropOffTimeEnd ? element.dropOffTimeEnd.push(timeWindowEndSpan) : element.dropOffTimeEnd = [timeWindowEndSpan];
    }

    timeWindowSpan.appendChild(timeWindowBeginSpan);
    timeWindowSpan.appendChild(editIconSpanTimeBegin);
    timeWindowSpan.appendChild(document.createTextNode(' - '));
    timeWindowSpan.appendChild(timeWindowEndSpan);
    timeWindowSpan.appendChild(editIconSpanTimeEnd);
    dropOffParagraph.appendChild(spanPackageName);
    dropOffParagraph.appendChild(editIconSpan);
    dropOffParagraph.appendChild(document.createTextNode(' x '));
    dropOffParagraph.appendChild(spanForPackageQuantity);
    dropOffParagraph.appendChild(editIconSpanQuantity);
    dropOffParagraph.appendChild(divForAddress);
    //divForAddress.appendChild(editIconSpanAddress);
    dropOffParagraph.appendChild(timeWindowSpan);
    dropOffParagraph.appendChild(document.createElement('br'));
    //=========================================================
    const notesIconSpan = document.createElement('span');
    notesIconSpan.className = 'notes-icon';
    notesIconSpan.innerHTML = '<i class="fa fa-sticky-note" aria-hidden="true"></i>';
    notesIconSpan.style.cursor = 'pointer';
    dropOff.note ? notesIconSpan.classList.add('text-success') : notesIconSpan.classList.remove('text-success');
    notesIconSpan.addEventListener('click', () => {
      // Remove any existing textarea to avoid duplicates
      const existingTextarea = notesIconSpan.querySelector('textarea');
      if (existingTextarea) return;

      const textarea = document.createElement('textarea');
      textarea.value = dropOff.note || '';
      textarea.style.width = '200px';
      textarea.style.height = '80px';
      textarea.style.resize = 'vertical';
      textarea.style.display = 'block';
      textarea.style.marginTop = '5px';

      notesIconSpan.appendChild(textarea);
      textarea.focus();

      textarea.addEventListener('blur', () => {
        const note = textarea.value.trim();
        dropOff.note = note;
        dropOff.note ? notesIconSpan.classList.add('text-success') : notesIconSpan.classList.remove('text-success');

        updateJobTemplate(item.id, buildNestedObject(`drop.${dropOff.order_number}.note`, note));
        textarea.remove();
      });
    });
    let hoverTimeout;
    notesIconSpan.addEventListener('mouseenter', () => {
      hoverTimeout = setTimeout(() => {
        notesIconSpan.setAttribute('title', dropOff.note || 'No notes');
      }, 300);
    });
    notesIconSpan.addEventListener('mouseleave', () => {
      clearTimeout(hoverTimeout);
    });
    //=========================================================
    const footer = document.createElement('div');
    footer.style.display = 'flex';
    footer.style.justifyContent = 'space-between';
    footer.style.alignItems = 'center';
    footer.appendChild(notesIconSpan);
    const deleteButton = document.createElement('button');
    deleteButton.className = 'btn btn-sm btn-danger';
    deleteButton.innerHTML = '<i class="fa fa-trash" aria-hidden="true"></i> Delete';
    footer.appendChild(deleteButton);
    deleteButton.addEventListener('click', () => {
      if (confirm('Are you sure you want to delete this drop-off?')) {
        removeDropOff({templateId : item.id,dropOffContainer : dropOffParagraph});
      }
    });
    dropOffParagraph.appendChild(footer);

    return dropOffParagraph;
}
function addEmptyDropOff({item,clientSpan,element = null,container}) {
  fetch(window.ROUTES.WEB.JOBTEMPLATE.ADDEMPTYDROPOFF, { 
      method: 'PATCH',
      headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json', 
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ id: item.id })
  }).then(response => response.json())
    .then(data => {
      console.log(data);
      if (data.success) {
        container.appendChild(getDropOffParagraph({dropOff : data.newDropOff,item : item,clientSpan : clientSpan,element : element}));
      }
    });
}
function removeDropOff({templateId,dropOffContainer}) {
  fetch(window.ROUTES.WEB.JOBTEMPLATE.REMOVEDROPOFF, { 
      method: 'PATCH',
      headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json', 
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ id: templateId, order_number: dropOffContainer.getAttribute('data-dropoff-orderNumber') })
  }).then(response => response.json())
    .then(data => {
      if (data.success) {
        dropOffContainer.remove();
      }
    });
}
function removeReturn({templateId,returnContainer}) {
  fetch(window.ROUTES.WEB.JOBTEMPLATE.REMOVERETURN, { 
      method: 'PATCH',
      headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json', 
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ id: templateId })
  }).then(response => response.json())
    .then(data => {
      if (data.success) {
        returnContainer.remove();
      }
    });
}
function handleDropsParagraph(item, clientSpan,element = null) {
  const divForEntireDropOffs = document.createElement('div');
  divForEntireDropOffs.style.borderRadius = '8px';
  divForEntireDropOffs.className = 'drop-offs-container';

  const paragraph = document.createElement('p');
  paragraph.style.display = 'flex';
  paragraph.style.justifyContent = 'center';
  paragraph.style.alignItems = 'center';
  const spanForIcon = document.createElement('span');
  const icon = document.createElement('i');
  icon.className = item.dropOfftasks.isLocked ? 'fa fa-lock text-danger' : 'fa fa-unlock';
  icon.setAttribute('aria-hidden', 'true');
  spanForIcon.appendChild(icon);
  spanForIcon.setAttribute('data-updatefield', 'locks.drops');
  spanForIcon.style.cursor = 'pointer';
  lockIconChanger(spanForIcon,item.id);
  const label = document.createElement('strong');
  safeSetText(label, '<Drop-offs>');
  label.className = 'ms-1';
  const buttonForAddDropOff = document.createElement('button');
  buttonForAddDropOff.className = 'btn btn-sm btn-primary ms-2';
  buttonForAddDropOff.innerHTML = '<i class="fa fa-plus" aria-hidden="true"></i> Add Drop-off';
  buttonForAddDropOff.style.cursor = 'pointer';
  buttonForAddDropOff.addEventListener('click', () => {
    addEmptyDropOff({item : item,clientSpan : clientSpan,element : element,container : divForEntireDropOffs});
  });
  paragraph.appendChild(spanForIcon);
  paragraph.appendChild(label);
  paragraph.appendChild(buttonForAddDropOff);
  divForEntireDropOffs.appendChild(paragraph);
  // accessibility improvements can be added later
  /* 
  divForEntireDropOffs.setAttribute('tabindex', '-1');
  if (element) {
      element.DropoffsContainers = divForEntireDropOffs;
  }
  */
 if(item.dropOfftasks.data){
   item.dropOfftasks.data.forEach((dropOff) => {
     const dropOffParagraph = getDropOffParagraph({dropOff : dropOff,item : item,clientSpan : clientSpan,element : element});
     divForEntireDropOffs.appendChild(dropOffParagraph);
   });
  }
  return divForEntireDropOffs;
}
function createJobsForTemplate({id,start,end,days}) {
  const routeUrl = window.ROUTES.WEB.JOB.STOREFROMTEMPLATE;
  let postData = {
    id,
    start,
    end,
    days
  };
  console.log('postData : ',postData);
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  fetch(routeUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify(postData)
  }).then(response => response.json())
    .then(data => {
      if(data.success){
        show_Success_Message({message : data.message});
      }
      if(data.error){
        show_Error_Message({message : data.error});
      } 
    });
}
function handleReturnParagraph(item, clientSpan) {
  const returnTask = item.returntask?.data;


  const paragraph = document.createElement('p');
  paragraph.className = 'return-item border border-secondary rounded p-2 mb-2';
  paragraph.style.display = 'grid';
  paragraph.style.gridTemplateRows = 'auto 1fr';
  paragraph.style.rowGap = '0.5rem';

  
  const titleContainer = document.createElement('div');
  titleContainer.style.display = 'flex';
  titleContainer.style.justifyContent = 'center';
  titleContainer.style.alignItems = 'center';
  titleContainer.style.gap = '0.5rem';
  if (!returnTask) {
    const addReturnButton = document.createElement('button');
    addReturnButton.className = 'btn btn-sm btn-primary';
    addReturnButton.innerHTML = '<i class="fa fa-plus" aria-hidden="true"></i> Add Return';
    addReturnButton.style.cursor = 'pointer';
    addReturnButton.addEventListener('click', () => {
      fetch(window.ROUTES.WEB.JOBTEMPLATE.ADDEMPTYRETURN, {
          method: 'PATCH',
          headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ id: item.id })
      }).then(response => response.json())
        .then(data => {
          if (data.success) {
            //data is not used  // modify "item" to take in new return.
            const itemA = {returntask : data.newReturn}
            const newReturnParagraph = handleReturnParagraph(itemA , clientSpan);
            paragraph.replaceWith(newReturnParagraph);
          }
        });
    });
    paragraph.appendChild(addReturnButton);
    return paragraph;
  }else{
    const lockIconSpan = document.createElement('span');
    const lockIcon = document.createElement('i');
    lockIcon.className = item.returntask.isLocked ? 'fa fa-lock text-danger' : 'fa fa-unlock';
    lockIcon.setAttribute('aria-hidden', 'true');
    lockIconSpan.setAttribute('data-updatefield', 'locks.return');
    lockIconSpan.style.cursor = 'pointer';
    lockIconChanger(lockIconSpan, item.id);
    lockIconSpan.appendChild(lockIcon);

    const titleText = document.createElement('strong');
    safeSetText(titleText, 'Return');
    titleText.className = 'ms-1';

    titleContainer.appendChild(lockIconSpan);
    titleContainer.appendChild(titleText);

    
    const body = document.createElement('div');
    body.className = 'return-body row';
    body.style.display = 'grid';
    body.style.gridTemplateColumns = '1fr'; // can change to '1fr 1fr' for two columns
    body.style.rowGap = '0.5rem';


    const addressContainer = document.createElement('div');
    addressContainer.className = 'col';
    const addressNameSpan = document.createElement('span');
    safeSetText(addressNameSpan, returnTask.return?.name || 'N/A');
    addressContainer.appendChild(addressNameSpan);
    
    addressNameSpan.setAttribute('data-client-id', clientSpan.getAttribute('data-client-id') || '');
    addressNameSpan.setAttribute('data-template-id', item.id);
    addressNameSpan.setAttribute('data-updatefield', 'return.addressId');
    addTypeHeadSearch_fromClient_AddressList(addressNameSpan);
    addressNameSpan.addEventListener('click', () => {
      addressNameSpan.contentEditable = true;
      addressNameSpan.focus();
      addressNameSpan.addEventListener('blur', function onBlur() {
        addressNameSpan.contentEditable = false;
        addressNameSpan.removeEventListener('blur', onBlur);
      });
    });
    const editIconSpan = document.createElement('span');
    editIconSpan.className = 'edit-pencil';
    editIconSpan.innerHTML = '<i class="fa fa-pencil" aria-hidden="true"></i>';
    editIconSpan.style.cursor = 'pointer';
    editIconSpan.addEventListener('click', () => {
      addressNameSpan.click();  
    });
    editIconSpan.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        editIconSpan.click();
      }
    });
    addressContainer.appendChild(editIconSpan);
    addressContainer.appendChild(document.createElement('br')); 

    var fullAddress = '';
    if(returnTask.return?.address) {
        fullAddress = returnTask.return.address.address_line_1+', '+returnTask.return.address.postal_code;
    }else{
        fullAddress = returnTask.return.adress_line+', '+returnTask.return.postal_code;
    }
    const addressSpan = document.createElement('span');
    safeSetText(addressSpan, fullAddress ?  '('+fullAddress+')' : '');
    addressSpan.className = 'text-muted full-address';
    addressContainer.appendChild(addressSpan);
    const timeContainer = document.createElement('div');
    timeContainer.className = 'col';
    const timeWindowBeginSpan = document.createElement('span');
    enableTimeEditing(timeWindowBeginSpan, `return.time.begin`, item.id, returnTask.return.time_begin);
    const timeWindowEndSpan = document.createElement('span');
    enableTimeEditing(timeWindowEndSpan, `return.time.end`, item.id, returnTask.return.time_end);
    const editIconSpanTimeBegin = document.createElement('span');
    editIconSpanTimeBegin.className = 'edit-pencil';
    editIconSpanTimeBegin.innerHTML = '<i class="fa fa-pencil" aria-hidden="true"></i>';
    editIconSpanTimeBegin.style.cursor = 'pointer';
    editIconSpanTimeBegin.addEventListener('click', () => {
      timeWindowBeginSpan.click();
    });
    editIconSpanTimeBegin.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        editIconSpanTimeBegin.click();
      }
    });
    const editIconSpanTimeEnd = document.createElement('span');
    editIconSpanTimeEnd.className = 'edit-pencil';
    editIconSpanTimeEnd.innerHTML = '<i class="fa fa-pencil" aria-hidden="true"></i>';
    editIconSpanTimeEnd.style.cursor = 'pointer';
    editIconSpanTimeEnd.addEventListener('click', () => {
      timeWindowEndSpan.click();
    });
    editIconSpanTimeEnd.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        editIconSpanTimeEnd.click();
      }
    });
    timeContainer.appendChild(timeWindowBeginSpan);
    timeContainer.appendChild(editIconSpanTimeBegin);
    timeContainer.appendChild(document.createTextNode(' - '));
    timeContainer.appendChild(timeWindowEndSpan);
    timeContainer.appendChild(editIconSpanTimeEnd);
    body.appendChild(addressContainer);
    body.appendChild(timeContainer);
    const bottom = document.createElement('div');
    bottom.className = 'return-bottom row';
    const notesIconSpan = document.createElement('span');
    notesIconSpan.className = 'notes-icon';
    notesIconSpan.innerHTML = '<i class="fa fa-sticky-note" aria-hidden="true"></i>';
    notesIconSpan.style.cursor = 'pointer';
    notesIconSpan.addEventListener('click', () => {
      // Remove any existing textarea to avoid duplicates
      const existingTextarea = notesIconSpan.querySelector('textarea');
      if (existingTextarea) return;

      const textarea = document.createElement('textarea');
      textarea.value = returnTask.note || '';
      textarea.style.width = '200px';
      textarea.style.height = '80px';
      textarea.style.resize = 'vertical';
      textarea.style.display = 'block';
      textarea.style.marginTop = '5px';

      notesIconSpan.appendChild(textarea);
      textarea.focus();

      textarea.addEventListener('blur', () => {
        const note = textarea.value.trim();
        returnTask.note = note;
        updateJobTemplate(item.id, buildNestedObject('return.note', note));
        textarea.remove();
      });
    });
    let hoverTimeout;
    notesIconSpan.addEventListener('mouseenter', () => {
      hoverTimeout = setTimeout(() => {
        notesIconSpan.setAttribute('title', returnTask.note || 'No notes');
      }, 300);
    });
    notesIconSpan.addEventListener('mouseleave', () => {
      clearTimeout(hoverTimeout);
    });
    const footer = document.createElement('div');
    footer.style.display = 'flex';
    footer.style.justifyContent = 'space-between';
    footer.style.alignItems = 'center';
    footer.appendChild(notesIconSpan);
    const deleteButton = document.createElement('button');
    deleteButton.className = 'btn btn-sm btn-danger';
    deleteButton.innerHTML = '<i class="fa fa-trash" aria-hidden="true"></i> Delete';
    footer.appendChild(deleteButton);
    deleteButton.addEventListener('click', () => {
      if (confirm('Are you sure you want to delete this return?')) {
        removeReturn({templateId : item.id,returnContainer : paragraph});
      }
    });
    // --- Assemble ---
    paragraph.appendChild(titleContainer);
    paragraph.appendChild(body);
    paragraph.appendChild(bottom);
    paragraph.appendChild(footer);

    return paragraph;
  }
}
function openEditableTextarea(targetElement, item, propertyName) {
  const existingTextarea = targetElement.querySelector('textarea');
  if (existingTextarea) return;

  const textarea = document.createElement('textarea');
  textarea.value = item.notes || '';
  textarea.style.width = '200px';
  textarea.style.height = '80px';
  textarea.style.resize = 'vertical';
  textarea.style.display = 'block';
  textarea.style.marginTop = '5px';

  targetElement.appendChild(textarea);
  textarea.focus();

  textarea.addEventListener('blur', () => {
    const note = textarea.value.trim();
    item.note = note;
    textarea.remove();
    if(note){
      targetElement.classList.add('text-success');
    }else{
      targetElement.classList.remove('text-success');
    }
    updateJobTemplate(item.id, buildNestedObject(propertyName, note));
    
  });
  textarea.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      textarea.blur();
    }
  });
}
var templateFocusableElements = [];
function setTabIndex(obj, startIndex = 0) {
  let index = startIndex;

  function traverse(item) {
    if (!item) return;

    if (item instanceof Element) {
      // It's a DOM element
      item.setAttribute('tabindex', index);
    } else if (typeof item === 'object') {
      // It's an object → recurse into its properties
      for (let key in item) {
        traverse(item[key]);
      }
    }
  }

  traverse(obj);
  return index; // return next index if needed
}

function fetchJobTemplates() {
  templateFocusableElements = [];
  let element = null;
  const routeUrl = window.ROUTES.WEB.JOBTEMPLATE.FETCH;
  fetch(routeUrl)
    .then(response => response.json())
    .then(data => {
      const gridContainer = document.querySelector('#itemListGrid');
      if (data.success) {
        const fragment = document.createDocumentFragment();
        gridContainer.innerHTML = '';
        data.items.forEach(item => {
          templateFocusableElements.push({id : item.id});
          const col = document.createElement('div');
          col.className = 'col-12 col-md-6 col-lg-4 col-xl-3';
          col.setAttribute('data-id', item.id);
          col.setAttribute('id', `template-${item.id}`);

          const card = document.createElement('div');
          card.className = 'card h-100 shadow-sm main-card';
          card.setAttribute('tabindex', '0');
          card.addEventListener('keydown', (e) => {
            if(!e.target.classList.contains('main-card')){
              console.log('not main-card');
              return;
            }
              
            if (e.key === 'Enter') {
              e.preventDefault();
              setTabIndex(templateFocusableElements.find(obj => obj.id === item.id), 0);
              card.querySelector('.card-body').focus();
            }
            if (e.key === 'Tab'){
              setTabIndex(templateFocusableElements.find(obj => obj.id === item.id), -1);
            }
          });

          const cardBody = document.createElement('div');
          cardBody.setAttribute('tabindex', '-1');
          cardBody.className = 'card-body';

          const title = document.createElement('div');
          title.className = 'card-title d-flex justify-content-between align-items-center';
          
          const nameSpan = document.createElement('span');
          safeSetText(nameSpan, item.name);
          nameSpan.className = 'fw-bold me-2 border-bottom-grow ';
          
          nameSpan.contentEditable = true;
          nameSpan.addEventListener('click', () => {
            nameSpan.focus();
          });
          nameSpan.addEventListener('blur', () => {
            const newName = nameSpan.textContent.trim();
            if (newName === '') {
              alert('Name cannot be empty.');
              safeSetText(nameSpan, item.name); // Restore original value
              return;
            }
            if(newName !== item.name){
              item.name = newName;
              updateJobTemplate(item.id, { name: newName });
            }
            
          });
          nameSpan.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
            e.preventDefault();
            nameSpan.blur();
            }
          });
          const editIconSpan = document.createElement('span');
          editIconSpan.className = 'edit-pencil';
          editIconSpan.innerHTML = '<i class="fa fa-pencil" aria-hidden="true"></i>';
          editIconSpan.style.cursor = 'pointer';
          editIconSpan.addEventListener('click', () => {
            nameSpan.contentEditable = true;
            nameSpan.focus();
          });
          editIconSpan.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
              e.preventDefault();
              editIconSpan.click();
            }
          });
          const idBadge = document.createElement('span');
          safeSetText(idBadge, `#${item.id}`);
          idBadge.className = 'badge bg-secondary';
          const templateNameDiv = document.createElement('div');

          title.appendChild(nameSpan);
          title.appendChild(editIconSpan);
          templateNameDiv.appendChild(nameSpan);
          templateNameDiv.appendChild(editIconSpan);
          title.appendChild(templateNameDiv);
          cardBody.appendChild(title);
          //=========================================================================
          const notesIconSpan = document.createElement('span');
          notesIconSpan.setAttribute('tabindex', '-1');
          element = templateFocusableElements.find(obj => obj.id === item.id);
          if (element) {
              element.mainNote = notesIconSpan;
          }
          notesIconSpan.className = 'notes-icon';
          notesIconSpan.innerHTML = '<i class="fa fa-sticky-note" aria-hidden="true"></i>';
          notesIconSpan.style.cursor = 'pointer';
          item.notes ? notesIconSpan.classList.add('text-success') : notesIconSpan.classList.remove('text-success');
          notesIconSpan.addEventListener('click', () => {
            openEditableTextarea(notesIconSpan, item, 'note');
          });
          notesIconSpan.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
              e.preventDefault();
              openEditableTextarea(notesIconSpan, item, 'note');
            }
          });
          let hoverTimeout;
          notesIconSpan.addEventListener('mouseenter', () => {
            hoverTimeout = setTimeout(() => {
              notesIconSpan.setAttribute('title', item.notes || 'No notes');
            }, 300);
          });
          notesIconSpan.addEventListener('mouseleave', () => {
            clearTimeout(hoverTimeout);
          });
          cardBody.appendChild(notesIconSpan);
          cardBody.appendChild(document.createElement('br'));
          const fixedPriceDiv = document.createElement('div');
          fixedPriceDiv.className = 'fixed-price-div mb-2';
          const fixedPriceLabel = document.createElement('span');
          safeSetText(fixedPriceLabel, 'Price: ');
          fixedPriceLabel.className = 'card-text';
          const fixedPriceValue = document.createElement('span');
          safeSetText(fixedPriceValue, item.fixedPrice === 0 ? 'Flexible' : item.fixedPrice.toFixed(2));
          fixedPriceValue.className = 'border-bottom fw-bold ms-1';
          if (item.fixedPrice === 0) {
          fixedPriceValue.classList.add('text-muted');
          } else {
          fixedPriceValue.classList.remove('text-muted');
          }
          fixedPriceValue.setAttribute('data-updatefield', 'fixedPrice');
          fixedPriceValue.setAttribute('data-placeholder', 'Flexible');
          fixedPriceValue.setAttribute('tabindex', '-1');

          element.price = fixedPriceValue;


          fixedPriceDiv.appendChild(fixedPriceLabel);
          fixedPriceDiv.appendChild(fixedPriceValue);
          cardBody.appendChild(fixedPriceDiv);
          fixedPriceValue.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
              e.preventDefault();
              const numberInput = document.createElement('input');
              numberInput.type = 'number';
              numberInput.step = '0.01';
              numberInput.min = '0';
              numberInput.value = item.fixedPrice || '';
              numberInput.style.width = '80px';
              //fixedPriceValue.textContent = '';
              fixedPriceValue.appendChild(numberInput);
              numberInput.focus();
              numberInput.addEventListener('blur', () => {
                let newValue = parseFloat(numberInput.value);
                if (!isNaN(newValue)) {
                  if(newValue == '0'){
                    newValue = 0;
                    safeSetText(fixedPriceValue, 'Flexible');
                    fixedPriceValue.classList.add('text-muted');
                  }else{
                    safeSetText(fixedPriceValue, newValue.toFixed(2));
                    fixedPriceValue.classList.remove('text-muted');
                  }
                  item.fixedPrice = newValue;
                  updateJobTemplate(item.id, buildNestedObject(`fixedPrice.true`, newValue));
                } else {
                  numberInput.remove();
                    
                    
                  

                }
              });
            }
          });
          //=========================================================================
          
          const clientParagraph = handleClientParagraph(item,element);
          const pickupParagraph = handlePickupParagraph(item, clientParagraph.clientSpan,element);
          const dropsParagraph = handleDropsParagraph(item, clientParagraph.clientSpan,element);
          const returnParagraph = handleReturnParagraph(item, clientParagraph.clientSpan,element);
          cardBody.appendChild(clientParagraph.paragraph);
          cardBody.appendChild(pickupParagraph);
          cardBody.appendChild(dropsParagraph);
          cardBody.appendChild(returnParagraph);
          //==========================================================================================
          const createJobsButton = document.createElement('button');
          safeSetText(createJobsButton, 'Create Jobs');
          createJobsButton.className = 'btn btn-primary';
          createJobsButton.addEventListener('click', () => {
            const modal = document.getElementById('calendar-modal');
            if (!modal) return;
            // Set today's date as default
            const today = new Date().toISOString().split('T')[0];
            const startInput = document.getElementById('calendar-modal-start');
            const endInput = document.getElementById('calendar-modal-end');
            if (startInput) startInput.value = today;
            if (endInput) endInput.value = today;
            // Render checkboxes for days
            const days = [
              { label: 'Monday', value: 1 },
              { label: 'Tuesday', value: 2 },
              { label: 'Wednesday', value: 3 },
              { label: 'Thursday', value: 4 },
              { label: 'Friday', value: 5 },
              { label: 'Saturday', value: 6 },
              { label: 'Sunday', value: 7 },
              { label: 'Workdays', value: JSON.stringify([1, 2, 3, 4, 5]) },
              { label: 'Weekends', value: JSON.stringify([6,7]) },
              { label: 'Bank Holidays', value: 'bankholidays' },
              { label: 'All', value: 'all' },
            ];
            const daysContainer = document.getElementById('calendar-modal-days');
            if (daysContainer) {
              daysContainer.innerHTML = '';
              days.forEach(day => {
                const checkboxLabel = document.createElement('label');
                checkboxLabel.style.marginRight = '12px';
                checkboxLabel.style.display = 'flex';
                checkboxLabel.style.alignItems = 'center';
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.value = day.value;
                checkbox.style.marginRight = '4px';
                checkbox.classList.add('day-checkbox');
                checkbox.id = `day-checkbox-${day.value}`;
                if(day.value === 'all'){
                  checkbox.addEventListener('change', function() {
                    const allCheckboxes = document.querySelectorAll('#calendar-modal .day-checkbox');
                    allCheckboxes.forEach(cb => {
                      if (cb !== this) {
                        cb.checked = this.checked;
                      }
                    });
                  });
                }
                if(day.value === JSON.stringify([1, 2, 3, 4, 5])){
                  checkbox.addEventListener('change', function() {
                    const workdayCheckboxes = ['1','2','3','4','5'].map(v => document.getElementById(`day-checkbox-${v}`));
                    if(this.checked){
                      workdayCheckboxes.forEach(cb => {
                        cb.checked = true;
                      });
                    }else{
                    }
                  });
                }
                if(day.value === JSON.stringify([6,7])){
                  checkbox.addEventListener('change', function() {
                    const weekendCheckboxes = ['6','7'].map(v => document.getElementById(`day-checkbox-${v}`));
                    if(this.checked){                      
                      weekendCheckboxes.forEach(cb => {
                        cb.checked = true;
                      });
                    }
                  });
                }
                checkboxLabel.appendChild(checkbox);
                checkboxLabel.appendChild(document.createTextNode(day.label));
                daysContainer.appendChild(checkboxLabel);
              });
            }
            // Store the item id for use on confirm
            modal.setAttribute('data-template-id', item.id);
            $('#calendar-modal').modal('show');
          });
          cardBody.appendChild(createJobsButton);
          card.appendChild(cardBody);
          col.appendChild(card);
          fragment.appendChild(col);
        });
        gridContainer.appendChild(fragment);
      } 
    })
    .catch(error => {
      console.error('Error fetching job templates:', error);
    });
}
function createNewTemplate() {
  const templateName = prompt('Enter template name:');
  if (!templateName || templateName.trim() === '') {
    alert('Template name is required.');
    return;
  }

  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  
  fetch(window.ROUTES.WEB.JOBTEMPLATE.STORE, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({ name: templateName.trim() })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      fetchJobTemplates();
      if (typeof show_Success_Message === 'function') {
        show_Success_Message({ message: data.message || 'Template created successfully.' });
      }
    } else {
      alert(data.message || 'Failed to create template.');
    }
  })
  .catch(error => {
    console.error('Error creating template:', error);
    alert('Error creating template: ' + error.message);
  });
}
//======================================================================================================================================================================
//======================================================================================================================================================================
//======================================================================================================================================================================
//======================================================================================================================================================================
//======================================================================================================================================================================
//======================================================================================================================================================================
//======================================================================================================================================================================
//======================================================================================================================================================================
//======================================================================================================================================================================
//======================================================================================================================================================================
//======================================================================================================================================================================
//======================================================================================================================================================================
//======================================================================================================================================================================
//======================================================================================================================================================================
//======================================================================================================================================================================
//======================================================================================================================================================================
//======================================================================================================================================================================
//======================================================================================================================================================================
//======================================================================================================================================================================

// Attach confirm button handler ONCE, robustly, without replacing the node
let calendarModalConfirmHandlerAttached = false;
function attachCalendarModalConfirmHandler() {
  if (calendarModalConfirmHandlerAttached) return;
  const confirmBtn = document.getElementById('calendar-modal-confirm');
  if (!confirmBtn) return;
  confirmBtn.addEventListener('click', () => {
    const modal = document.getElementById('calendar-modal');
    const startInput = document.getElementById('calendar-modal-start');
    const endInput = document.getElementById('calendar-modal-end');
    const startDate = startInput ? startInput.value : '';
    const endDate = endInput ? endInput.value : '';
    if (!startDate || !endDate || startDate > endDate) {
      alert('Please select a valid date range.');
      return;
    }
    const days = Array.from(document.querySelectorAll('#calendar-modal .day-checkbox:checked')).map(cb => cb.value);
    const templateId = modal.getAttribute('data-template-id');
    console.log('Confirm clicked. templateId:', templateId, 'startDate:', startDate, 'endDate:', endDate, 'days:', days);
    createJobsForTemplate({ 
      id: templateId,
      start: startDate,
      end: endDate,
      days: days,
    });
    // Hide modal
    if (window.bootstrap && window.bootstrap.Modal) {
      const bsModal = window.bootstrap.Modal.getOrCreateInstance(modal);
      bsModal.hide();
    } else if (window.$ && window.$.fn && window.$.fn.modal) {
      window.$(modal).modal('hide');
    }
  });
  calendarModalConfirmHandlerAttached = true;
  console.log('Confirm button handler attached');
}

document.addEventListener('DOMContentLoaded', function () {
  fetchJobTemplates();
  const createTemplateBtn = document.getElementById('createTemplateBtn');
  if (createTemplateBtn) {
    createTemplateBtn.addEventListener('click', createNewTemplate);
  }
  attachCalendarModalConfirmHandler();
  console.log('DOMContentLoaded: attachCalendarModalConfirmHandler called');
});