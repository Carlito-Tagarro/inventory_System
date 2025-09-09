// Modal rendering logic
function renderModal(data, materials, isPending) {
    // Group fields by section
    const sections = [

        {
            fields: [
                // {label: "Sender Email", key: "sender_email"},
                {label: "Event Name", key: "event_name"},
                {label: "Event Title", key: "event_title"},
                {label: "Event Date", key: "event_date"},
                {label: "Contact Person", key: "contact_person"},
                {label: "Contact Number", key: "contact_number"},
                {label: "Number of Event Days", key: "event_duration"},
                {label: "Date Time Ingress", key: "date_time_ingress"},
                {label: "Date Time Egress", key: "date_time_egress"},
                {label: "Claiming of ID", key: "claiming_id"},
                {label: "Event Place", key: "place"},
                {label: "Location", key: "location"}
            ]
        },
        {
            title: "II. Budgeting & Audience",
            fields: [
                {label: "Sponsorship Budget", key: "sponsorship_budg"},
                {label: "Amount (₱)", key: "amount"},
                {label: "Target Audience", key: "target_audience"},
                {label: "Number of Audience", key: "number_audience"},
                {label: "Other Company attending (Exhibitor)", key: "other_attendee"}
            ]
        },
        {
            title: "III. Booth & Other Setup",
            fields: [
                {label: "Set Up", key: "set_up"},
                {label: "Booth Size", key: "booth_size"},
                {label: "Booth Inclusion", key: "booth_inclusion"},
                {label: "Number Tables", key: "number_tables"},
                {label: "Number Chairs", key: "number_chairs"},
            ]
        },
        {
            title: "IV. Programs & Marketing",
            fields: [
                {label: "Speaking Slot", key: "speaking_slot"},
                {label: "Speaker", key: "speaker_name"},
                {label: "Date Time", key: "date_time"},
                {label: "Duration", key: "duration"},
                {label: "Topic", key: "topic"},
                {label: "Technical Team", key: "technical_team"},
                {label: "Technical Task", key: "technical_task"},
                {label: "Trainer Needed", key: "trainer_needed"},
                {label: "Trainer Task", key: "trainer_task"},
                {label: "Provide Materials", key: "provide_materials"},
                {label: "Requested By", key: "requested_by"}
            ]
        }
    ];
    // const extraFields = [
    //     {label: "Event Form Id", key: "event_form_id"},
    //     {label: "Created At", key: "created_at"},
    //     {label: "Request Status", key: "request_status"}
    // ];

    let leftHtml = '';
    sections.forEach(section => {
        leftHtml += `<div class="modal-section-title">${section.title}</div><table class="modal-details-table" style="width:100%;">`;
        section.fields.forEach(f => {
            if (data[f.key] !== undefined) {
                leftHtml += `<tr><td class="modal-label">${f.label}:</td><td class="modal-value">${data[f.key]||''}</td></tr>`;
            }
        });
        leftHtml += '</table>';
    });
    document.getElementById('modalLeft').innerHTML = leftHtml;

    // FIX: Initialize rightHtml before using it
    let rightHtml = '';
    rightHtml += '<div class="modal-section-title">Status & Meta Info</div><table class="modal-details-table" style="width:100%;">';
    extraFields.forEach(f => {
        if (data[f.key] !== undefined) {
            rightHtml += `<tr><td class="modal-label">${f.label}:</td><td class="modal-value">${data[f.key]||''}</td></tr>`;
        }
    });
    rightHtml += '</table>';
    // Add Team Members section if present
    if (data.team_members && Array.isArray(data.team_members) && data.team_members.length > 0) {
        rightHtml += '<div class="modal-section-title" style="margin-top:18px;">II. Team</div>';
        rightHtml += '<table class="modal-details-table" style="width:100%;"><thead><tr><th style="width:50%;">&nbsp;</th><th style="width:50%;">&nbsp;</th></tr></thead><tbody>';
        for (let i = 0; i < data.team_members.length; i += 2) {
            let left = data.team_members[i] || '';
            let right = data.team_members[i+1] || '';
            rightHtml += `<tr><td>${left}</td><td>${right}</td></tr>`;
        }
        rightHtml += '</tbody></table>';
    }
    if (materials && materials.length > 0) {
        // Change section title to include Roman numeral V.
        rightHtml += '<div class="modal-section-title" style="margin-top:18px;">V. Requested Materials</div>';
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
    } else {
        actions += `<button onclick="downloadRequestPDF()" style="background:#2563eb;">Download PDF</button>`;
    }
    document.getElementById('modalActions').innerHTML = actions;
    document.getElementById('requestModal').style.display = 'block';
    document.getElementById('requestModal').setAttribute('data-id', data.event_form_id);
   

    // Add border to all tables inside modal
    setTimeout(() => {
        document.querySelectorAll('#requestModal .modal-details-table').forEach(table => {
            table.style.border = '1px solid #22223b';
            table.style.borderCollapse = 'collapse';
            table.querySelectorAll('td, th').forEach(cell => {
                cell.style.border = '1px solid #22223b';
                cell.style.padding = '6px 10px';
            });
        });
        // Optional: style section titles
        document.querySelectorAll('#requestModal .modal-section-title').forEach(title => {
            // title.style.background = '#f3f3f3';
            title.style.fontWeight = 'bold';
            title.style.padding = '8px 10px';
            // title.style.borderLeft = '4px solid #22223b';
            title.style.marginBottom = '0px';
        });
    }, 0);
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

// PDF generation using html2pdf.js
function downloadRequestPDF() {
    let modalContent = document.querySelector('#requestModal .modal-content').cloneNode(true);

    // Remove the close button and actions from the clone
    let closeBtn = modalContent.querySelector('.close');
    if (closeBtn) closeBtn.parentNode.removeChild(closeBtn);
    let actions = modalContent.querySelector('#modalActions');
    if (actions) actions.parentNode.removeChild(actions);

    // Remove overflow and max-height styles for PDF rendering
    modalContent.style.overflow = 'visible';
    modalContent.style.maxHeight = 'none';
    modalContent.style.height = 'auto';

    // Change flex layout to block for PDF rendering
    let flexWrap = modalContent.querySelector('div[style*="display: flex"]');
    if (flexWrap) flexWrap.style.display = 'block';

    // Prevent page breaks inside tables
    let tables = modalContent.querySelectorAll('table');
    tables.forEach(table => {
        table.style.pageBreakInside = 'avoid';
        table.style.breakInside = 'avoid';
        // Set solid border, remove border-radius
        table.style.border = '1px solid #22223b';
        table.style.borderCollapse = 'collapse';
        table.style.borderRadius = '0';
        table.querySelectorAll('td, th').forEach(cell => {
            cell.style.border = '1px solid #22223b';
            cell.style.padding = '6px 10px';
            cell.style.borderRadius = '0';
        });
    });

    // --- Add custom header with logo and company info ---
    let pdfHeader = document.createElement('div');
    pdfHeader.style.display = 'flex';
    pdfHeader.style.alignItems = 'flex-start';
    pdfHeader.style.justifyContent = 'space-between';
    pdfHeader.style.marginBottom = '18px';
    pdfHeader.style.width = '100%';
    pdfHeader.innerHTML = `
        <div style="flex:1; text-align:left; padding-right:32px;">
            <div style="font-weight:bold;font-size:1.15em;">AUDENTES TECHNOLOGIES INC.</div>
            <div style="font-style:italic;font-size:0.95em;">Be PHENOMENAL or Be FORGOTTEN</div>
            <div style="margin-top:8px;font-size:0.95em;">
                Block 16 Lot 2 San Augustin Village<br>
                San Francisco (Halang) 4024<br>
                City of Biñan, Philippines<br>
                +63 2 624-6414 | +63 985 931 9156<br>
                certification@audentestechnologies.com
            </div>
        </div>
        <div style="width:auto; text-align:right;">
            <img src="../images/AUDENTES LOGO.png" alt="Audentes Logo" style="height:130px; width:130px; display:inline-block;">
        </div>
    `;
    // NOTE: Replace the src URL above with your actual logo image URL or base64 if needed.

    modalContent.insertBefore(pdfHeader, modalContent.firstChild);

    // Optional: Add a title/header for the PDF
    let header = document.createElement('div');
    header.style.textAlign = 'center';
    // header.style.marginBottom = '10px';
    header.innerHTML = `<h2 style="color:#22223b;">Event Request Details</h2>`;
    modalContent.insertBefore(header, pdfHeader.nextSibling);

    // Move "Booth & Other Setup" section to start of second page in PDF
    let boothSection = Array.from(modalContent.querySelectorAll('.modal-section-title'))
        .find(el => el.textContent.trim().startsWith('III. Booth & Other Setup'));
    if (boothSection) {
        // Insert a page break before Booth section
        boothSection.style.pageBreakBefore = 'always';
        // Optionally add extra spacing if needed
        boothSection.style.marginTop = '220px';
    }

    // Move "Booth & Other Setup" section to start of second page in PDF
    let materialSection = Array.from(modalContent.querySelectorAll('.modal-section-title'))
        .find(el => el.textContent.trim().startsWith('V. Requested Materials'));
    if (materialSection) {
        // Insert a page break before Material section
        materialSection.style.pageBreakBefore = 'always';
        // Optionally add extra spacing if needed
        materialSection.style.marginTop = '200px';
    }

    // Add spacing before each section title except the first
    let sectionTitles = modalContent.querySelectorAll('.modal-section-title');
    sectionTitles.forEach((el, idx) => {
        if (idx > 0) {
            let spacer = document.createElement('div');
            spacer.style.height = '30px';
            el.parentNode.insertBefore(spacer, el);
        }
    });

    // Remove any forced page breaks between sections
    sectionTitles.forEach((el) => {
        el.style.pageBreakBefore = '';
    });

    // Set options for html2pdf with pagebreak enabled (css/legacy)
    let opt = {
        margin:       0.5,
        filename:     'event_request_' + (document.getElementById('requestModal').getAttribute('data-id') || 'details') + '.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'in', format: 'a4', orientation: 'Portrait' },
        pagebreak:    { mode: ['css', 'legacy'] }
    };

    html2pdf().set(opt).from(modalContent).save();
}

window.onclick = function(event) {
    let modal = document.getElementById('requestModal');
    if (event.target == modal) closeModal();
}

