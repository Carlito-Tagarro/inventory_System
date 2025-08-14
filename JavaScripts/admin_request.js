// Modal rendering logic
function renderModal(data, materials, isPending) {
    const fields = [
        {label: "Event Name", key: "event_name"},
        {label: "Event Title", key: "event_title"},
        {label: "Event Date", key: "event_date"},
        {label: "Sender Email", key: "sender_email"},
        {label: "Date Time Ingress", key: "date_time_ingress"},
        {label: "Date Time Egress", key: "date_time_egress"},
        {label: "Place", key: "place"},
        {label: "Location", key: "location"},
        {label: "Sponsorship Budget", key: "sponsorship_budget"},
        {label: "Target Audience", key: "target_audience"},
        {label: "Number Audience", key: "number_audience"},
        {label: "Set Up", key: "set_up"},
        {label: "Booth Size", key: "booth_size"},
        {label: "Booth Inclusion", key: "booth_inclusion"},
        {label: "Number Tables", key: "number_tables"},
        {label: "Number Chairs", key: "number_chairs"},
        {label: "Speaking Slot", key: "speaking_slot"},
        {label: "Date Time", key: "date_time"},
        {label: "Program Target", key: "program_target"},
        {label: "Technical Team", key: "technical_team"},
        {label: "Trainer Needed", key: "trainer_needed"},
        {label: "Ready To Use", key: "ready_to_use"},
        {label: "Provide Materials", key: "provide_materials"}
    ];
    const extraFields = [
        {label: "Event Form Id", key: "event_form_id"},
        {label: "Created At", key: "created_at"},
        {label: "Request Status", key: "request_status"}
    ];
    let leftHtml = '<div class="modal-section-title">Event Details</div><table class="modal-details-table" style="width:100%;">';
    fields.forEach(f => {
        if (data[f.key] !== undefined) {
            leftHtml += `<tr><td class="modal-label">${f.label}:</td><td class="modal-value">${data[f.key]||''}</td></tr>`;
        }
    });
    leftHtml += '</table>';
    document.getElementById('modalLeft').innerHTML = leftHtml;

    let rightHtml = '<div class="modal-section-title">Status & Meta Info</div><table class="modal-details-table" style="width:100%;">';
    extraFields.forEach(f => {
        if (data[f.key] !== undefined) {
            rightHtml += `<tr><td class="modal-label">${f.label}:</td><td class="modal-value">${data[f.key]||''}</td></tr>`;
        }
    });
    rightHtml += '</table>';
    if (materials && materials.length > 0) {
        rightHtml += '<div class="modal-section-title" style="margin-top:18px;">Requested Materials</div>';
        rightHtml += '<table class="modal-details-table" style="width:100%;"><tr><th>Category</th><th>Name</th><th>Quantity</th></tr>';
        materials.forEach(mat => {
            ['Brochure','Swag','Marketing Material'].forEach(type => {
                if (mat[type] && mat[type].name && mat[type].qty) {
                    rightHtml += `<tr><td>${type}</td><td>${mat[type].name}</td><td>${mat[type].qty}</td></tr>`;
                }
            });
        });
        rightHtml += '</table>';
    }
    document.getElementById('modalRight').innerHTML = rightHtml;

    let actions = '';
    if (isPending) {
        actions += `<button onclick="updateStatus(${data.event_form_id},'Approved')" style="background:#2563eb;">Approve</button>`;
        actions += `<button onclick="updateStatus(${data.event_form_id},'Declined')" style="background:#e53e3e;">Decline</button>`;
    }
    document.getElementById('modalActions').innerHTML = actions;
    document.getElementById('requestModal').style.display = 'block';
    document.getElementById('requestModal').setAttribute('data-id', data.event_form_id);
}

// Event delegation for view-request buttons
document.body.addEventListener('click', function(e) {
    if (e.target.classList.contains('view-request')) {
        e.preventDefault();
        let data = JSON.parse(e.target.getAttribute('data-request'));
        let materials = [];
        try { materials = JSON.parse(e.target.getAttribute('data-materials')); } catch {}
        renderModal(data, materials, data.request_status === 'Pending');
    }
});

function closeModal() {
    document.getElementById('requestModal').style.display = 'none';
}

// AJAX to update status and update history table dynamically
function updateStatus(id, status) {
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_request_status.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status == 200) {    
            let statusCell = document.getElementById('status-' + id);
            if (statusCell && statusCell.parentNode) {
                statusCell.parentNode.parentNode.removeChild(statusCell.parentNode);
            }
            closeModal();
            try {
                let processed = JSON.parse(xhr.responseText);
                if (!processed || !processed.success) { location.reload(); return; }
                let fetchXhr = new XMLHttpRequest();
                fetchXhr.open('GET', 'fetch_latest_history.php?id=' + id, true);
                fetchXhr.onload = function() {
                    if (fetchXhr.status == 200) {
                        try {
                            let latest = JSON.parse(fetchXhr.responseText);
                            if (latest && latest.event_form_id) {
                                let historyTable = document.querySelector('.table-container:nth-child(2) tbody');
                                let materials = latest.requested_materials ? JSON.stringify(latest.requested_materials) : '[]';
                                let tr = document.createElement('tr');
                                tr.innerHTML =
                                    `<td>${latest.event_name||''}</td>
                                    <td>${latest.event_date||''}</td>
                                    <td>${latest.request_status||''}</td>
                                    <td>${latest.processed_at||''}</td>
                                    <td><button class="view-request" data-request='${JSON.stringify(latest)}' data-materials='${materials.replace(/'/g, '&#39;')}'>View Details</button></td>`;
                                historyTable.prepend(tr);
                            }
                        } catch { location.reload(); }
                    }
                };
                fetchXhr.send();
            } catch { location.reload(); }
        }
    };
    xhr.send('id=' + id + '&status=' + status);
}

window.onclick = function(event) {
    let modal = document.getElementById('requestModal');
    if (event.target == modal) closeModal();
}
