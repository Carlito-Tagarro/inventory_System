<?php 
include 'connection.php';
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php"); 
    exit;
}

$connection = CONNECTIVITY();

// Fetch event requests with sender email and requested materials
$query = "SELECT event_form.*, users.email AS sender_email 
          FROM event_form 
          LEFT JOIN users ON event_form.user_id = users.user_id";
$result = mysqli_query($connection, $query);

// Prepare event requests array with materials
$event_requests = [];
while ($row = mysqli_fetch_assoc($result)) {
    $materials = [];
    if ($row['request_mats']) {
        $mat_id = intval($row['request_mats']);
        $mat_query = "SELECT * FROM material_request_form WHERE request_mats = $mat_id";
        $mat_result = mysqli_query($connection, $mat_query);
        while ($mat_row = mysqli_fetch_assoc($mat_result)) {
            $materials[] = [
                'Brochure' => [
                    'name' => $mat_row['name_brochures'],
                    'qty' => $mat_row['brochure_quantity']
                ],
                'Swag' => [
                    'name' => $mat_row['name_swag'],
                    'qty' => $mat_row['swag_quantity']
                ],
                'Marketing Material' => [
                    'name' => $mat_row['name_material'],
                    'qty' => $mat_row['material_quantity']
                ]
            ];
        }
    }
    $row['requested_materials'] = $materials;
    $event_requests[] = $row;
}

// Fetch history requests (all records, with required columns)
$history_query = "SELECT event_form_id, event_name, event_title, event_date, sender_email, date_time_ingress, date_time_egress, place, location, sponsorship_budget, target_audience, number_audience, set_up, booth_size, booth_inclusion, number_tables, number_chairs, speaking_slot, date_time, program_target, technical_team, trainer_needed, ready_to_use, provide_materials, created_at, user_id, request_mats, request_status, processed_at FROM event_form_history ORDER BY processed_at DESC";
$history_result = mysqli_query($connection, $history_query);
$history_requests = [];
while ($row = mysqli_fetch_assoc($history_result)) {
    $materials = [];
    if ($row['request_mats']) {
        $mat_id = intval($row['request_mats']);
        $mat_query = "SELECT * FROM material_request_form WHERE request_mats = $mat_id";
        $mat_result = mysqli_query($connection, $mat_query);
        while ($mat_row = mysqli_fetch_assoc($mat_result)) {
            $materials[] = [
                'Brochure' => [
                    'name' => $mat_row['name_brochures'],
                    'qty' => $mat_row['brochure_quantity']
                ],
                'Swag' => [
                    'name' => $mat_row['name_swag'],
                    'qty' => $mat_row['swag_quantity']
                ],
                'Marketing Material' => [
                    'name' => $mat_row['name_material'],
                    'qty' => $mat_row['material_quantity']
                ]
            ];
        }
    }
    $row['requested_materials'] = $materials;
    $history_requests[] = $row;
}

DISCONNECTIVITY($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/images__1_-removebg-preview.png"> 
    <title>Requests</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
        }
        nav {
            
            padding: 16px 0;
            margin-bottom: 32px;
            display: grid;
            grid-template-columns: auto 1fr;
            align-items: center;
            gap: 20px;
        }
        .nav-container {
            display: flex;
            justify-content: center;
            gap: 32px;
            width: 100%;
        }
        nav img {
            height: 50px;
            margin-left: 20px;
        }
        nav a {
            color: #333;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
        }
        h2 {
            text-align: left;
            margin-top: 0;
            color: #22223b;
            letter-spacing: 1px;
        }
        .tables-flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            width: 100%;
            margin-top: 30px;
        }
        .table-container {
            width: 48%;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 0;
            background: #fff;
            box-shadow: 0 2px 8px rgba(34,34,59,0.08);
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            border: none;
            padding: 14px 18px;
            text-align: left;
        }
        th {
            background: #22223b;
            color: #fff;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        tr:nth-child(even) {
            background: #f7f7fb;
        }
        tr:hover {
            background: #e9ecef;
            transition: background 0.2s;
        }
        button {
            background: #4f8cff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 8px 18px;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 1px 4px rgba(34,34,59,0.07);
        }
        button:hover {
            background: #2563eb;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(34,34,59,0.25);
        }
        .modal-content {
            background: #fff;
            position: relative;
            margin: auto;
            top: 0; left: 0; right: 0; bottom: 0;
            /* Center using flexbox */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 300px;
            width: 900px;
            max-width: 98vw;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(34,34,59,0.18);
            animation: modalIn 0.25s;
            padding: 28px 30px 18px 30px;
        }
        @media (max-width: 900px) {
            .modal-content {
                width: 99vw;
                padding: 12px 2vw 10px 2vw;
            }
            .modal-content > div > div {
                flex-direction: column;
                gap: 0;
            }
            .modal-left, .modal-right {
                min-width: 0 !important;
                width: 99vw;
            }
        }
        @media (max-width: 600px) {
            .modal-content {
                width: 99vw;
                padding: 8px 1vw 8px 1vw;
            }
        }
        @media (max-width: 600px) {
            .modal-content {
                width: 99vw;
                padding: 8px 1vw 8px 1vw;
            }
        }

    </style>
</head>
<body>
    <nav>
        <a href="admin.php"><img src="images/images__1_-removebg-preview.png" alt="Company Logo"></a>
    <div class="nav-container">
        <a href="admin.php">Dashboard</a>
        <a href="brochure_inventory.php">Brochure Inventory</a>
        <a href="marketing_inventory.php">Materials Inventory</a>
        <a href="swags_inventory.php">Swags Inventory</a>
    </div>
    </nav>
    <div class="tables-flex">
        <div class="table-container">
            <h2>Event Requests</h2>
            <table>
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Event Date</th>
                        <th>Sender Email</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($event_requests as $row): ?>
                    <?php if($row['request_status'] === 'Pending'): // Only show Pending ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['sender_email']); ?></td>
                        <td id="status-<?php echo $row['event_form_id']; ?>">
                            <?php echo htmlspecialchars($row['request_status']); ?>
                        </td>
                        <td>
                            <button 
                                class="view-request"
                                data-request='<?php echo json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>'
                                data-materials='<?php echo json_encode($row['requested_materials'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>'
                            >View Details</button>
                        </td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="table-container">
            <h2>Processed Requests History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Event Date</th>
                        <th>Status</th>
                        <th>Processed At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($history_requests as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['request_status']); ?></td>
                        <td><?php echo htmlspecialchars($row['processed_at']); ?></td>
                        <td>
                            <button 
                                class="view-request"
                                data-request='<?php echo json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>'
                                data-materials='<?php echo json_encode($row['requested_materials'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>'
                            >View Details</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="requestModal" class="modal">
        <div class="modal-content">
            <div style="position: absolute; top: 18px; right: 24px;">
                <button class="close" onclick="closeModal()" style="
                    background: none;
                    border: none;
                    font-size: 28px;
                    color: #2563eb;
                    cursor: pointer;
                    font-weight: bold;
                    transition: color 0.2s;
                " title="Close">&times;</button>
            </div>
            <div style="width:100%;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 18px;">
                    <img src="images/images__1_-removebg-preview.png" alt="Event" style="height:38px;">
                    <span style="font-size: 1.5rem; font-weight: 700; color: #22223b;">Event Request Details</span>
                </div>
                <div class="modal-divider"></div>
                <div style="display: flex; flex-wrap: wrap; gap: 32px;">
                    <div class="modal-left" id="modalLeft" style="flex:1 1 320px; min-width:260px;"></div>
                    <div class="modal-right" id="modalRight" style="flex:1 1 320px; min-width:260px;"></div>
                </div>
                <div class="modal-divider"></div>
                <div id="modalActions" style="margin-top: 18px; text-align: right;"></div>
            </div>
        </div>
    </div>
    <style>
        .modal-content .modal-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2563eb;
            margin: 0 0 10px 0;
            letter-spacing: 0.5px;
            border-bottom: 1.5px solid #e0e0e0;
            padding-bottom: 4px;
        }
        .modal-details-table th, .modal-details-table td {
            border-bottom: 1px solid #f0f0f0;
        }
        .modal-details-table th {
            background: #f0f8ff;
            color: #2563eb;
            font-weight: 600;
            padding: 8px 0;
            font-size: 15px;
        }
        .modal-details-table td {
            padding: 6px 0 6px 0;
            vertical-align: top;
            font-size: 15px;
        }
        .modal-label {
            color: #2563eb;
            font-weight: 600;
            width: 44%;
            padding-right: 10px;
            text-align: right;
        }
        .modal-value {
            color: #22223b;
            font-weight: 400;
            width: 56%;
            padding-left: 10px;
            word-break: break-word;
        }
        @media (max-width: 900px) {
            .modal-content > div > div {
                flex-direction: column;
                gap: 0;
            }
            .modal-left, .modal-right {
                min-width: 0 !important;
                width: 99vw;
            }
        }
    </style>
    <script>
        // Show modal with request details
        document.querySelectorAll('.view-request').forEach(function(link) {
            link.onclick = function(e) {
                e.preventDefault();
                var data = JSON.parse(this.getAttribute('data-request'));
                var materials = [];
                try {
                    materials = JSON.parse(this.getAttribute('data-materials'));
                } catch (err) {}
                var fields = [
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
                var extraFields = [
                    {label: "Event Form Id", key: "event_form_id"},
                    {label: "Created At", key: "created_at"},
                    {label: "Request Status", key: "request_status"}
                ];

                // Left: Event Details
                var leftHtml = '<div class="modal-section-title">Event Details</div>';
                leftHtml += '<table class="modal-details-table" style="width:100%;">';
                fields.forEach(function(f) {
                    if (data[f.key] !== undefined) {
                        leftHtml += '<tr><td class="modal-label">'+f.label+':</td><td class="modal-value">'+(data[f.key]||'')+'</td></tr>';
                    }
                });
                leftHtml += '</table>';
                document.getElementById('modalLeft').innerHTML = leftHtml;

                // Right: Status, Meta, Materials
                var rightHtml = '<div class="modal-section-title">Status & Meta Info</div>';
                rightHtml += '<table class="modal-details-table" style="width:100%;">';
                extraFields.forEach(function(f) {
                    if (data[f.key] !== undefined) {
                        rightHtml += '<tr><td class="modal-label">'+f.label+':</td><td class="modal-value">'+(data[f.key]||'')+'</td></tr>';
                    }
                });
                rightHtml += '</table>';

                // Requested Materials Section
                if (materials && materials.length > 0) {
                    rightHtml += '<div class="modal-section-title" style="margin-top:18px;">Requested Materials</div>';
                    rightHtml += '<table class="modal-details-table" style="width:100%;">';
                    rightHtml += '<tr><th>Category</th><th>Name</th><th>Quantity</th></tr>';
                    materials.forEach(function(mat) {
                        ['Brochure','Swag','Marketing Material'].forEach(function(type) {
                            if (mat[type] && mat[type].name && mat[type].qty) {
                                rightHtml += '<tr><td>'+type+'</td><td>'+mat[type].name+'</td><td>'+mat[type].qty+'</td></tr>';
                            }
                        });
                    });
                    rightHtml += '</table>';
                }
                document.getElementById('modalRight').innerHTML = rightHtml;

                // Actions
                var actions = '';
                if (data.request_status === 'Pending') {
                    actions += '<button onclick="updateStatus('+data.event_form_id+',\'Approved\')" style="background:#2563eb;">Approve</button>';
                    actions += '<button onclick="updateStatus('+data.event_form_id+',\'Declined\')" style="background:#e53e3e;">Decline</button>';
                }
                document.getElementById('modalActions').innerHTML = actions;
                document.getElementById('requestModal').style.display = 'block';
                document.getElementById('requestModal').setAttribute('data-id', data.event_form_id);
            };
        });

        function closeModal() {
            document.getElementById('requestModal').style.display = 'none';
        }

        // AJAX to update status and update history table dynamically
        function updateStatus(id, status) {
            var row = document.getElementById('status-' + id)?.parentNode;
            var eventData = null;
            if (row) {
                var btn = row.querySelector('.view-request');
                if (btn) {
                    try {
                        eventData = JSON.parse(btn.getAttribute('data-request'));
                    } catch (e) {}
                }
            }
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_request_status.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status == 200) {    
                    // Remove the row from the pending table (Event Requests)
                    var statusCell = document.getElementById('status-' + id);
                    if (statusCell && statusCell.parentNode) {
                        statusCell.parentNode.parentNode.removeChild(statusCell.parentNode);
                    } else {
                        var rows = document.querySelectorAll('.table-container:first-child tbody tr');
                        rows.forEach(function(tr) {
                            if (tr.querySelector('[id^="status-"]') &&
                                tr.querySelector('[id^="status-"]').id === 'status-' + id) {
                                tr.parentNode.removeChild(tr);
                            }
                        });
                    }
                    closeModal();

                    // Parse the returned processed request data (assume backend returns JSON)
                    try {
                        var processed = JSON.parse(xhr.responseText);
                        if (!processed || !processed.success) {
                            location.reload();
                            return;
                        }
                        // Add to history table via AJAX fetch
                        // We'll fetch the latest record from event_form_history and prepend it
                        var fetchXhr = new XMLHttpRequest();
                        fetchXhr.open('GET', 'fetch_latest_history.php?id=' + id, true);
                        fetchXhr.onload = function() {
                            if (fetchXhr.status == 200) {
                                try {
                                    var latest = JSON.parse(fetchXhr.responseText);
                                    if (latest && latest.event_form_id) {
                                        var historyTable = document.querySelector('.table-container:nth-child(2) tbody');
                                        var materials = latest.requested_materials ? JSON.stringify(latest.requested_materials) : '[]';
                                        var tr = document.createElement('tr');
                                        tr.innerHTML =
                                            '<td>' + (latest.event_name || '') + '</td>' +
                                            '<td>' + (latest.event_date || '') + '</td>' +
                                            '<td>' + (latest.request_status || '') + '</td>' +
                                            '<td>' + (latest.processed_at || '') + '</td>' +
                                            '<td><button class="view-request" data-request=\'' + JSON.stringify(latest) + '\' data-materials=\'' + materials.replace(/'/g, '&#39;') + '\'>View Details</button></td>';
                                        historyTable.prepend(tr);

                                        // Attach modal event for new button
                                        tr.querySelector('.view-request').onclick = function(e) {
                                            e.preventDefault();
                                            var data = latest;
                                            var materials = [];
                                            try {
                                                materials = JSON.parse(this.getAttribute('data-materials'));
                                            } catch (err) {}
                                            // ...existing modal code for showing details...
                                            var fields = [
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
                                            var extraFields = [
                                                {label: "Event Form Id", key: "event_form_id"},
                                                {label: "Created At", key: "created_at"},
                                                {label: "Request Status", key: "request_status"}
                                            ];
                                            var leftHtml = '<div class="modal-section-title">Event Details</div>';
                                            leftHtml += '<table class="modal-details-table" style="width:100%;">';
                                            fields.forEach(function(f) {
                                                if (data[f.key] !== undefined) {
                                                    leftHtml += '<tr><td class="modal-label">'+f.label+':</td><td class="modal-value">'+(data[f.key]||'')+'</td></tr>';
                                                }
                                            });
                                            leftHtml += '</table>';
                                            document.getElementById('modalLeft').innerHTML = leftHtml;

                                            var rightHtml = '<div class="modal-section-title">Status & Meta Info</div>';
                                            rightHtml += '<table class="modal-details-table" style="width:100%;">';
                                            extraFields.forEach(function(f) {
                                                if (data[f.key] !== undefined) {
                                                    rightHtml += '<tr><td class="modal-label">'+f.label+':</td><td class="modal-value">'+(data[f.key]||'')+'</td></tr>';
                                                }
                                            });
                                            rightHtml += '</table>';

                                            if (materials && materials.length > 0) {
                                                rightHtml += '<div class="modal-section-title" style="margin-top:18px;">Requested Materials</div>';
                                                rightHtml += '<table class="modal-details-table" style="width:100%;">';
                                                rightHtml += '<tr><th>Category</th><th>Name</th><th>Quantity</th></tr>';
                                                materials.forEach(function(mat) {
                                                    ['Brochure','Swag','Marketing Material'].forEach(function(type) {
                                                        if (mat[type] && mat[type].name && mat[type].qty) {
                                                            rightHtml += '<tr><td>'+type+'</td><td>'+mat[type].name+'</td><td>'+mat[type].qty+'</td></tr>';
                                                        }
                                                    });
                                                });
                                                rightHtml += '</table>';
                                            }
                                            document.getElementById('modalRight').innerHTML = rightHtml;
                                            document.getElementById('modalActions').innerHTML = '';
                                            document.getElementById('requestModal').style.display = 'block';
                                            document.getElementById('requestModal').setAttribute('data-id', data.event_form_id);
                                        };
                                    }
                                } catch (err) {
                                    location.reload();
                                }
                            }
                        };
                        fetchXhr.send();
                    } catch (e) {
                        location.reload();
                    }
                }
            };
            xhr.send('id=' + id + '&status=' + status);
        }

        // Close modal on outside click
        window.onclick = function(event) {
            var modal = document.getElementById('requestModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>