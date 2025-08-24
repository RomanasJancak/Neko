window.clientIdSpanMap = new Map();
function enableTimeEditing(span, updateField, itemId, initialValue) {
  //alert('enableTimeEditing called with updateField: ' + updateField + ', itemId: ' + itemId + ', initialValue: ' + initialValue);
  span.textContent = convertTo12Hour(initialValue.split(' ')[1]?.substring(0, 5));
  span.className = 'text-muted';
  span.setAttribute('data-updatefield', updateField);
  span.setAttribute('data-template-id', itemId);

  span.addEventListener('click', () => {
    const timeInput = document.createElement('input');
    timeInput.type = 'time';
    timeInput.className = 'form-control';
    timeInput.style.width = '200px';
    timeInput.style.position = 'absolute';
    timeInput.style.zIndex = 9999;

    // Parse and set time in 24-hour format expected by input[type="time"]
    const currentTime = span.textContent.trim();
    // const date = new Date(`1970-01-01T${convertTo24Hour(currentTime)}`);
    // timeInput.value = date.toISOString().substring(11, 16); // "HH:MM"
    timeInput.value = convertTo24Hour(currentTime);
    const rect = span.getBoundingClientRect();
    timeInput.style.left = `${rect.left + window.scrollX}px`;
    timeInput.style.top = `${rect.top + window.scrollY}px`;

    document.body.appendChild(timeInput);
    timeInput.focus();

    const removeInput = () => {
      const selectedTime = timeInput.value;
      if (selectedTime) {
        span.textContent = convertTo12Hour(selectedTime);
      }
      document.body.removeChild(timeInput);
    };

    timeInput.addEventListener('blur', () => {
      removeInput();
      const field = buildNestedObject(updateField,timeInput.value);
      updateJobTemplate(itemId,field);
    });
    //timeInput.addEventListener('change', removeInput);
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
            li.textContent = item.name;
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
        editableSpan.textContent = item.name;
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
            li.textContent = item.name;
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
        editableSpan.textContent = item.name;
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
                    nextSpan.textContent = ` (${data.postal_code}, ${data.address_line_1})`;
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
          li.textContent = packageType.name;
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
    editableSpan.textContent = item.name;
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
      };
      console.log('Update successful:', data);
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
function lockIconChanger(span,id) {
  span.addEventListener('click', () => {
    const icon = span.querySelector('i');
    icon.classList.toggle('fa-lock');
    icon.classList.toggle('text-danger');
    icon.classList.toggle('fa-unlock');
    const path = span.getAttribute('data-updatefield');
    const field = buildNestedObject(path, !icon.classList.contains('fa-unlock'));

    updateJobTemplate(id, field);
  });
}
function handleClientParagraph(item){
  const client = item.clientToBill;
  const clientParagraph = document.createElement('p');
  const clientName = client ? client.name : 'N/A';
  const clientClass = client ? '' : 'text-danger';
  const spanForIcon = document.createElement('span');
  spanForIcon.setAttribute('data-updatefield', 'locks.client');
  spanForIcon.style.cursor = 'pointer'; 
  const icon = document.createElement('i');
  const iconClass = client.isLocked ? 'fa fa-lock text-danger' : 'fa fa-unlock';
  icon.className = iconClass;
  icon.setAttribute('aria-hidden', 'true');
  spanForIcon.appendChild(icon);
  lockIconChanger(spanForIcon,item.id);
  clientParagraph.appendChild(spanForIcon);
  const spanForClientIdentifier = document.createElement('span');
  spanForClientIdentifier.textContent = "Client: ";
  spanForClientIdentifier.className = 'card-text';
  clientParagraph.appendChild(spanForClientIdentifier);
  const spanForName = document.createElement('span');
  spanForName.textContent = clientName;
  spanForName.className = clientClass;
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
  clientParagraph.appendChild(editIconSpan);

  return {paragraph : clientParagraph,clientSpan : spanForName};
}
function handlePickupParagraph(item,clientSpan) {
  const paragraph = document.createElement('p');
  const spanForIcon = document.createElement('span');
  const icon = document.createElement('i');
  icon.className = item.pickuptask.isLocked ? 'fa fa-lock text-danger' : 'fa fa-unlock';
  icon.setAttribute('aria-hidden', 'true');
  spanForIcon.appendChild(icon);
  spanForIcon.setAttribute('data-updatefield', 'locks.pickup');
  spanForIcon.style.cursor = 'pointer';
  lockIconChanger(spanForIcon,item.id);
  
  const label = document.createElement('strong');
  label.textContent = 'Pickup address: ';

  const spanForName = document.createElement('span');
  spanForName.textContent = item.pickuptask.data.pickupclientname || 'N/A';
  spanForName.setAttribute('data-client-id', clientSpan.getAttribute('data-client-id') || '');
  spanForName.setAttribute('data-template-id', item.id);
  if (!clientIdSpanMap.has(item.id)) {
    clientIdSpanMap.set(item.id, []);
  }
  clientIdSpanMap.get(item.id).push(spanForName);
  spanForName.setAttribute('data-updatefield', 'pickup.addressId');
  addTypeHeadSearch_fromClient_AddressList(spanForName);
  const fullAddress = item.pickuptask.data.pickupclientpostalcode+', '+item.pickuptask.data.pickupclientaddressline;
  const addressSpan = document.createElement('span');
  addressSpan.textContent = fullAddress ?  '('+fullAddress+')' : '';
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
  const timeWindowSpan = document.createElement('span');

  const timeWindowBeginSpan = document.createElement('span');

  enableTimeEditing(timeWindowBeginSpan, 'pickup.time.begin', item.id, item.pickuptask.data.pickup_time_begin);
  
  const timeWindowEndSpan = document.createElement('span');
  enableTimeEditing(timeWindowEndSpan, 'pickup.time.end', item.id, item.pickuptask.data.pickup_time_end);
  //=========================================================
  console.log(item);
  const notesIconSpan = document.createElement('span');
  notesIconSpan.className = 'notes-icon';
  notesIconSpan.innerHTML = '<i class="fa fa-sticky-note" aria-hidden="true"></i>';
  notesIconSpan.style.cursor = 'pointer';
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
      updateJobTemplate(item.id, buildNestedObject(`pickup.note`, note));
      textarea.remove();
    });
  });
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
  timeWindowSpan.appendChild(document.createTextNode(' - '));
  timeWindowSpan.appendChild(timeWindowEndSpan);
  paragraph.appendChild(spanForIcon);
  paragraph.appendChild(label);
  paragraph.appendChild(spanForName);
  paragraph.appendChild(addressSpan);
  paragraph.appendChild(editIconSpan);
  paragraph.appendChild(document.createElement('br'));
  paragraph.appendChild(timeWindowSpan);
  paragraph.appendChild(document.createElement('br'));
  paragraph.appendChild(notesIconSpan);
  return paragraph;
}
function getDropOffParagraph(dropOff,item,clientSpan) {
    const pakuote = dropOff.package;
    const dropOffParagraph = document.createElement('p');
    dropOffParagraph.className = 'drop-off-item';
    dropOffParagraph.classList.add('border', 'border-secondary', 'rounded', 'p-2', 'mb-2');
    dropOffParagraph.setAttribute('data-template-id', item.id);
    dropOffParagraph.setAttribute('data-dropoff-id', dropOff.id);
    
    const spanPackageName = document.createElement('span');

    spanPackageName.textContent = pakuote.package_type.name;
    addPackageTypeSelect_fromClient(spanPackageName);
    spanPackageName.className = 'package-name';
    spanPackageName.setAttribute('data-package-id', pakuote.id);
    spanPackageName.setAttribute('data-template-id', item.id);
    spanPackageName.setAttribute('data-client-id', clientSpan.getAttribute('data-client-id') || '');
    spanPackageName.setAttribute('data-updatefield', `drop.${dropOff.order_number}.packageTypeId`);

    spanPackageName.addEventListener('click', () => {
      spanPackageName.contentEditable = true;
      spanPackageName.focus();
      spanPackageName.addEventListener('blur', function onBlur() {
        spanPackageName.contentEditable = false;
        
        spanPackageName.removeEventListener('blur', onBlur);
      });
    });
    const spanForPackageQuantity = document.createElement('span');
    const packageQuantity = pakuote.quantity;
    spanForPackageQuantity.textContent = packageQuantity;
    spanForPackageQuantity.className = 'text-muted';
    spanForPackageQuantity.setAttribute('data-updatefield', `drop.${dropOff.order_number}.packageQuantity`);
    spanForPackageQuantity.setAttribute('data-template-id', item.id);
    spanForPackageQuantity.setAttribute('data-client-id', clientSpan.getAttribute('data-client-id') || '');
    spanForPackageQuantity.addEventListener('click', () => {
      spanForPackageQuantity.contentEditable = true;
      spanForPackageQuantity.focus();
      spanForPackageQuantity.addEventListener('blur', function onBlur() {
        spanForPackageQuantity.contentEditable = false;
        updateJobTemplate(item.id, buildNestedObject(spanForPackageQuantity.getAttribute('data-updatefield'), spanForPackageQuantity.textContent));
        spanForPackageQuantity.removeEventListener('blur', onBlur);
      });
    });
    //=================================================================
    const divForAddress = document.createElement('div');
    const spanForAddressName = document.createElement('span');
    spanForAddressName.textContent = !dropOff.address ? pakuote.dropoff_name	: dropOff.address.name;
    divForAddress.appendChild(spanForAddressName);
    divForAddress.className = 'drop-off-address';
    spanForAddressName.setAttribute('data-client-id', clientSpan.getAttribute('data-client-id') || '');
    spanForAddressName.setAttribute('data-template-id', item.id);
    spanForAddressName.setAttribute('data-updatefield', `drop.${dropOff.order_number}.addressId`);
    spanForAddressName.setAttribute('data-address-id', !dropOff.address ? '' : dropOff.address.id);
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
    if(dropOff.address) {
      fullAddress = dropOff.address.postal_code+', '+dropOff.address.address_line_1;
    }else{
      fullAddress = pakuote.dropoff_postal_code+', '+pakuote.dropoff_address_line;
    }
    const addressSpan = document.createElement('span');
    addressSpan.textContent = fullAddress ?  '('+fullAddress+')' : '';
    addressSpan.className = 'text-muted full-address';
    divForAddress.appendChild(addressSpan);
    //=================================================================
    const timeWindowSpan = document.createElement('span');
    const timeWindowBeginSpan = document.createElement('span');
    enableTimeEditing(timeWindowBeginSpan, `drop.${dropOff.order_number}.time.begin`, item.id, pakuote.packagedropofftimebegin);
    const timeWindowEndSpan = document.createElement('span');
    enableTimeEditing(timeWindowEndSpan, `drop.${dropOff.order_number}.time.end`, item.id, pakuote.packagedropofftimeend);

    timeWindowSpan.appendChild(timeWindowBeginSpan);
    timeWindowSpan.appendChild(document.createTextNode(' - '));
    timeWindowSpan.appendChild(timeWindowEndSpan);
    dropOffParagraph.appendChild(spanPackageName);
    dropOffParagraph.appendChild(document.createTextNode(' x '));
    dropOffParagraph.appendChild(spanForPackageQuantity);
    dropOffParagraph.appendChild(divForAddress);
    dropOffParagraph.appendChild(timeWindowSpan);
    dropOffParagraph.appendChild(document.createElement('br'));
    //=========================================================
    const notesIconSpan = document.createElement('span');
    notesIconSpan.className = 'notes-icon';
    notesIconSpan.innerHTML = '<i class="fa fa-sticky-note" aria-hidden="true"></i>';
    notesIconSpan.style.cursor = 'pointer';
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
    dropOffParagraph.appendChild(notesIconSpan);

    return dropOffParagraph;
}
function handleDropsParagraph(item, clientSpan) {
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
  label.textContent = 'Drop-offs: ';
  paragraph.appendChild(spanForIcon);
  paragraph.appendChild(label);
  divForEntireDropOffs.appendChild(paragraph);
  item.dropOfftasks.data.forEach((dropOff) => {
    const dropOffParagraph = getDropOffParagraph(dropOff,item,clientSpan);
    divForEntireDropOffs.appendChild(dropOffParagraph);
  });
  return divForEntireDropOffs;
}
/*
function handleReturnParagraph(item, clientSpan) {
  const returnTask = item.returntask.data;
  if(!returnTask){
    return document.createElement('p');
  }
  const paragraph = document.createElement('p');
  paragraph.className = 'return-item';
  paragraph.classList.add('border', 'border-secondary', 'rounded', 'p-2', 'mb-2');
  paragraph.style.display = 'grid';
  paragraph.style.justifyContent = 'center';
  paragraph.style.alignItems = 'center';
  const divForTitle = document.createElement('div');
  divForTitle.className = 'return-title';
  const spanForTitle = document.createElement('span');
  const spanForLockIcon = document.createElement('span');
  const lockIcon = document.createElement('i');

  lockIcon.className = item.returntask.isLocked ? 'fa fa-lock text-danger' : 'fa fa-unlock';
  lockIcon.setAttribute('aria-hidden', 'true');
  spanForLockIcon.setAttribute('data-updatefield', 'locks.return');
  spanForLockIcon.style.cursor = 'pointer';
  lockIconChanger(spanForLockIcon, item.id);
  const titleText = document.createElement('strong');
  titleText.textContent = 'Return';
  titleText.className = 'ms-2';
  spanForLockIcon.appendChild(lockIcon);

  divForTitle.appendChild(spanForLockIcon);
  divForTitle.appendChild(titleText);
  const body = document.createElement('div');
  body.className = 'return-body row';
  const addressContainer = document.createElement('div');
  addressContainer.style.justifyContent = '';
  addressContainer.style.alignItems = '';
  addressContainer.className = 'col-12 col-md-6';
  const addressNameSpan = document.createElement('span');
  addressNameSpan.textContent = returnTask.return.name || 'N/A';
  addressContainer.appendChild(addressNameSpan);
  body.appendChild(addressContainer);
  paragraph.appendChild(divForTitle);
  paragraph.appendChild(body);

  return paragraph;
}
*/
function handleReturnParagraph(item, clientSpan) {
  const returnTask = item.returntask?.data;
  if (!returnTask) {
    return document.createElement('p');
  }

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

  const lockIconSpan = document.createElement('span');
  const lockIcon = document.createElement('i');
  lockIcon.className = item.returntask.isLocked ? 'fa fa-lock text-danger' : 'fa fa-unlock';
  lockIcon.setAttribute('aria-hidden', 'true');
  lockIconSpan.setAttribute('data-updatefield', 'locks.return');
  lockIconSpan.style.cursor = 'pointer';
  lockIconChanger(lockIconSpan, item.id);
  lockIconSpan.appendChild(lockIcon);

  const titleText = document.createElement('strong');
  titleText.textContent = 'Return';
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
  addressNameSpan.textContent = returnTask.return?.name || 'N/A';
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
  var fullAddress = '';
  if(returnTask.return?.address) {
      fullAddress = returnTask.return.address.postal_code+', '+returnTask.return.address.address_line_1;
  }else{
      fullAddress = returnTask.return.postal_code+', '+returnTask.return.adress_line;
  }
  const addressSpan = document.createElement('span');
  addressSpan.textContent = fullAddress ?  '('+fullAddress+')' : '';
  addressSpan.className = 'text-muted full-address';
  addressContainer.appendChild(addressSpan);
  const timeContainer = document.createElement('div');
  timeContainer.className = 'col';
  const timeWindowBeginSpan = document.createElement('span');
  enableTimeEditing(timeWindowBeginSpan, `return.time.begin`, item.id, returnTask.return.time_begin);
  const timeWindowEndSpan = document.createElement('span');
  enableTimeEditing(timeWindowEndSpan, `return.time.end`, item.id, returnTask.return.time_end);

  timeContainer.appendChild(timeWindowBeginSpan);
  timeContainer.appendChild(document.createTextNode(' - '));
  timeContainer.appendChild(timeWindowEndSpan);
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
  // --- Assemble ---
  paragraph.appendChild(titleContainer);
  paragraph.appendChild(body);
  paragraph.appendChild(bottom);
  paragraph.appendChild(notesIconSpan);

  return paragraph;
}

function fetchJobTemplates() {
  const routeUrl = window.ROUTES.WEB.JOBTEMPLATE.FETCH;
  fetch(routeUrl)
    .then(response => response.json())
    .then(data => {
      const gridContainer = document.querySelector('#itemListGrid');
      if (data.success) {
        const fragment = document.createDocumentFragment();
        gridContainer.innerHTML = '';
        data.items.forEach(item => {
          const col = document.createElement('div');
          col.className = 'col-12 col-md-6 col-lg-4 col-xl-3';
          col.setAttribute('data-id', item.id);
          col.setAttribute('id', `template-${item.id}`);

          const card = document.createElement('div');
          card.className = 'card h-100 shadow-sm';

          const cardBody = document.createElement('div');
          cardBody.className = 'card-body';

          const title = document.createElement('h5');
          title.className = 'card-title';
          title.textContent = `Template #${item.id}`;
          cardBody.appendChild(title);
          //=========================================================================
          const notesIconSpan = document.createElement('span');
          notesIconSpan.className = 'notes-icon';
          notesIconSpan.innerHTML = '<i class="fa fa-sticky-note" aria-hidden="true"></i>';
          notesIconSpan.style.cursor = 'pointer';
          notesIconSpan.addEventListener('click', () => {

            const existingTextarea = notesIconSpan.querySelector('textarea');
            if (existingTextarea) return;

            const textarea = document.createElement('textarea');
            textarea.value = item.notes || '';
            textarea.style.width = '200px';
            textarea.style.height = '80px';
            textarea.style.resize = 'vertical';
            textarea.style.display = 'block';
            textarea.style.marginTop = '5px';

            notesIconSpan.appendChild(textarea);
            textarea.focus();

            textarea.addEventListener('blur', () => {
              const note = textarea.value.trim();
              item.note = note;
              updateJobTemplate(item.id, buildNestedObject(`note`, note));
              textarea.remove();
            });
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
          //=========================================================================
          const nameParagraph = document.createElement('p');
          const namespan = document.createElement('span');
          namespan.className = 'card-text';
          namespan.textContent = `Name:`;
          nameParagraph.appendChild(namespan);
          const nameText = document.createElement('span');
          nameText.textContent = item.name;
          nameText.className = 'item-Name';
          nameText.setAttribute('data-updatefield', 'name');
          nameParagraph.appendChild(nameText);
          const editPencilSpanName = document.createElement('span');
          editPencilSpanName.className = 'edit-pencil';
          editPencilSpanName.innerHTML = '<i class="fa fa-pencil" aria-hidden="true"></i>';
          editPencilSpanName.style.cursor = 'pointer';
          editPencilSpanName.addEventListener('click', () => {
            nameText.contentEditable = true;
            nameText.focus();
            nameText.addEventListener('blur', () => {
              nameText.contentEditable = false;
              updateJobTemplate(item.id, { name: nameText.textContent });
            });
            nameText.addEventListener('keydown', (event) => {
              if (event.key === 'Enter') {
                event.preventDefault();
                nameText.blur();
              }
            });
          });
          nameParagraph.appendChild(editPencilSpanName);
          cardBody.appendChild(nameParagraph);

          const clientParagraph = handleClientParagraph(item);
          const pickupParagraph = handlePickupParagraph(item, clientParagraph.clientSpan);
          const dropsParagraph = handleDropsParagraph(item, clientParagraph.clientSpan);
          const returnParagraph = handleReturnParagraph(item, clientParagraph.clientSpan);
          cardBody.appendChild(clientParagraph.paragraph);
          cardBody.appendChild(pickupParagraph);
          cardBody.appendChild(dropsParagraph);
          cardBody.appendChild(returnParagraph);
          //==========================================================================================
          const createJobsButton = document.createElement('button');
          createJobsButton.textContent = 'Create Jobs';
          createJobsButton.className = 'btn btn-primary';
          createJobsButton.addEventListener('click', () => {
            // Create calendar modal
            let modal = document.getElementById('calendar-modal');
            if (!modal) {
              modal = document.createElement('div');
              modal.id = 'calendar-modal';
              modal.style.position = 'fixed';
              modal.style.top = '0';
              modal.style.left = '0';
              //modal.style.width = '50vw';
              //modal.style.height = '100vh';
              modal.classList.add('bg-dark');
              modal.style.display = 'flex';
              modal.style.justifyContent = 'center';
              modal.style.alignItems = 'center';
              modal.style.zIndex = '9999';

              const calendarBox = document.createElement('div');
              //calendarBox.style.background = '#fff';
              calendarBox.style.padding = '24px';
              calendarBox.style.borderRadius = '8px';
              calendarBox.style.boxShadow = '0 2px 8px rgba(0,0,0,0.2)';
              calendarBox.style.display = 'flex';
              calendarBox.style.flexDirection = 'column';
              calendarBox.style.alignItems = 'center';

              const title = document.createElement('h5');
              title.textContent = 'Select Date Range';
              calendarBox.appendChild(title);

              // Use two <input type="date"> for range selection
              const startLabel = document.createElement('label');
              startLabel.textContent = 'Start date: ';
              const startInput = document.createElement('input');
              startInput.type = 'date';
              startInput.style.marginRight = '8px';
              const today = new Date().toISOString().split('T')[0];
              startInput.value = today;
              
              startLabel.appendChild(startInput);

              const endLabel = document.createElement('label');
              endLabel.textContent = 'End date: ';
              const endInput = document.createElement('input');
              endInput.type = 'date';
              endInput.value = today;
              endLabel.appendChild(endInput);

              calendarBox.appendChild(startLabel);
              calendarBox.appendChild(endLabel);

              const btnRow = document.createElement('div');
              btnRow.style.marginTop = '16px';
              btnRow.style.display = 'flex';
              btnRow.style.gap = '12px';

              const confirmBtn = document.createElement('button');
              confirmBtn.textContent = 'Confirm';
              confirmBtn.className = 'btn btn-success';
              confirmBtn.addEventListener('click', () => {
                const startDate = startInput.value;
                const endDate = endInput.value;
                if (!startDate || !endDate || startDate > endDate) {
                  alert('Please select a valid date range.');
                  return;
                }
                // Call your function with selected range
                createJobsForTemplate(item.id, { start: startDate, end: endDate });
                document.body.removeChild(modal);
              });

              const cancelBtn = document.createElement('button');
              cancelBtn.textContent = 'Cancel';
              cancelBtn.className = 'btn btn-secondary';
              cancelBtn.addEventListener('click', () => {
                document.body.removeChild(modal);
              });

              btnRow.appendChild(confirmBtn);
              btnRow.appendChild(cancelBtn);
              calendarBox.appendChild(btnRow);

              modal.appendChild(calendarBox);
              const daysBox = document.createElement('div');
              daysBox.style.marginTop = '16px';
              daysBox.style.display = 'flex';
              daysBox.style.flexDirection = 'column';
              daysBox.style.alignItems = 'flex-start';

              const days = [
                { label: 'Monday', value: 'monday' },
                { label: 'Tuesday', value: 'tuesday' },
                { label: 'Wednesday', value: 'wednesday' },
                { label: 'Thursday', value: 'thursday' },
                { label: 'Friday', value: 'friday' },
                { label: 'Saturday', value: 'saturday' },
                { label: 'Sunday', value: 'sunday' },
                { label: 'Workdays', value: 'workdays' },
                { label: 'Weekends', value: 'weekends' },
                { label: 'Bank Holidays', value: 'bankholidays' },
                { label: 'All', value: 'all' },
              ];

              const daysLabel = document.createElement('label');
              daysLabel.textContent = 'Select days:';
              daysLabel.style.fontWeight = 'bold';
              daysBox.appendChild(daysLabel);

              const checkboxesContainer = document.createElement('div');
              checkboxesContainer.style.display = 'flex';
              checkboxesContainer.style.flexWrap = 'wrap';
              checkboxesContainer.style.gap = '8px';

              days.forEach(day => {
                const checkboxLabel = document.createElement('label');
                checkboxLabel.style.marginRight = '12px';
                checkboxLabel.style.display = 'flex';
                checkboxLabel.style.alignItems = 'center';

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.value = day.value;
                checkbox.style.marginRight = '4px';

                checkboxLabel.appendChild(checkbox);
                checkboxLabel.appendChild(document.createTextNode(day.label));
                checkboxesContainer.appendChild(checkboxLabel);
              });

              daysBox.appendChild(checkboxesContainer);
              calendarBox.appendChild(daysBox);
              document.body.appendChild(modal);
            }
          });
          //==========================================================================================
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

document.addEventListener('DOMContentLoaded', function () {

  fetchJobTemplates();
});