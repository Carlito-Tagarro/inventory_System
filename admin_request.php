<?php 
include 'connection.php';
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php"); 
    exit;
}

$connection = CONNECTIVITY();

// Fetch event requests with sender email (assuming event_form.user_id exists)
$query = "SELECT event_form.*, users.email AS sender_email 
          FROM event_form 
          LEFT JOIN users ON event_form.user_id = users.user_id";
$result = mysqli_query($connection, $query);

// Fetch history requests (approved/declined)
$history_query = "SELECT * FROM event_form_history ORDER BY processed_at DESC";
$history_result = mysqli_query($connection, $history_query);

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
            margin: 5% auto;
            padding: 28px 30px 18px 30px;
            width: 440px;
            max-width: 98vw;
            border-radius: 12px;
            position: relative;
            box-shadow: 0 8px 32px rgba(34,34,59,0.18);
            animation: modalIn 0.25s;
        }
        @keyframes modalIn {
            from { transform: translateY(-40px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .close {
            position: absolute;
            top: 14px;
            right: 22px;
            cursor: pointer;
            font-size: 28px;
            color: #22223b;
            font-weight: bold;
            transition: color 0.2s;
        }
        .close:hover {
            color: #c1121f;
        }
        #modalBody {
            font-size: 15px;
            color: #22223b;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .modal-details-table {
            width: 100%;
            border-collapse: collapse;
        }
        .modal-details-table td {
            padding: 2px 0 2px 0;
            vertical-align: top;
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
        .modal-divider {
            border-top: 1px solid #e0e0e0;
            margin: 12px 0 10px 0;
        }
        #modalActions {
            text-align: right;
            margin-top: 18px;
        }
        #modalActions button {
            margin-left: 8px;
            min-width: 80px;
        }
        @media (max-width: 900px) {
            .tables-flex {
                flex-direction: column;
                gap: 0;
            }
            .table-container {
                width: 99vw;
                margin-bottom: 30px;
            }
        }
        @media (max-width: 600px) {
            .modal-content {
                width: 99vw;
                padding: 12px 2vw 10px 2vw;
            }
            .modal-label, .modal-value {
                font-size: 14px;
            }
            table {
                width: 99vw;
                font-size: 13px;
            }
            .table-container {
                width: 99vw;
            }
        }

    </style>
</head>
<body>
    <nav>
        <a href="admin.php"><img src="images/images__1_-removebg-preview.png" alt="Company Logo"></a>
    <div class="nav-container">
        <a href="brochure_inventory.php">Brochure Inventory</a>
        <a href="marketing_inventory.php">Materials Inventory</a>
        <a href="swags_inventory.php">Swags Inventory</a>
        <a href="admin_request.php">Event Requests</a>
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
                <?php while($row = mysqli_fetch_assoc($result)): ?>
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
                            >View Details</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
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
                <?php while($row = mysqli_fetch_assoc($history_result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['request_status']); ?></td>
                        <td><?php echo htmlspecialchars($row['processed_at']); ?></td>
                        <td>
                            <button 
                                class="view-request"
                                data-request='<?php echo json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>'
                            >View Details</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="requestModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modalBody"></div>
            <div class="modal-divider"></div>
            <div id="modalActions"></div>
        </div>
    </div>

    <script>
        // Show modal with request details
        document.querySelectorAll('.view-request').forEach(function(link) {
            link.onclick = function(e) {
                e.preventDefault();
                var data = JSON.parse(this.getAttribute('data-request'));
                var fields = [
                    {label: "Event Name", key: "event_name"},
                    {label: "Event Title", key: "event_title"},
                    {label: "Event Date", key: "event_date"},
                    {label: "Sender Email", key: "sender_email"},
                    {label: "Date Time Ingress", key: "date_time_ingress"},
                    {label: "Date Time Egress", key: "date_time_egress"},
                    {label: "Place", key: "place"},
                    {label: "Location", key: "location"},
                    {label: "Sponsorship Budget", key: "sponsorship_budget"}, // fixed label
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
                var table = '<table class="modal-details-table">';
                fields.forEach(function(f) {
                    if (data[f.key] !== undefined) {
                        table += '<tr><td class="modal-label">'+f.label+':</td><td class="modal-value">'+(data[f.key]||'')+'</td></tr>';
                    }
                });
                table += '</table>';
                table += '<div class="modal-divider"></div>';
                table += '<table class="modal-details-table">';
                extraFields.forEach(function(f) {
                    if (data[f.key] !== undefined) {
                        table += '<tr><td class="modal-label">'+f.label+':</td><td class="modal-value">'+(data[f.key]||'')+'</td></tr>';
                    }
                });
                table += '</table>';
                document.getElementById('modalBody').innerHTML = table;
                // Actions
                var actions = '';
                if (data.request_status === 'Pending') {
                    actions += '<button onclick="updateStatus('+data.event_form_id+',\'Approved\')">Approve</button>';
                    actions += '<button onclick="updateStatus('+data.event_form_id+',\'Declined\')">Decline</button>';
                }
                document.getElementById('modalActions').innerHTML = actions;
                document.getElementById('requestModal').style.display = 'block';
                document.getElementById('requestModal').setAttribute('data-id', data.event_form_id);
            };
        });

        function closeModal() {
            document.getElementById('requestModal').style.display = 'none';
        }

        // AJAX to update status
        function updateStatus(id, status) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_request_status.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status == 200) {
                    // Remove the row from the pending table
                    var row = document.getElementById('status-' + id)?.parentNode;
                    if (row) row.parentNode.removeChild(row);
                    closeModal();

                    // Parse the returned processed request data (assume backend returns JSON)
                    try {
                        var processed = JSON.parse(xhr.responseText);
                        if (!processed || !processed.event_form_id) {
                            // If backend did not return expected JSON, reload page
                            location.reload();
                            return;
                        }
                        // Add to history table
                        var historyTable = document.querySelector('.table-container:nth-child(2) tbody');
                        if (historyTable && processed) {
                            var tr = document.createElement('tr');
                            tr.innerHTML = 
                                '<td>' + (processed.event_name || '') + '</td>' +
                                '<td>' + (processed.event_date || '') + '</td>' +
                                '<td>' + (processed.request_status || '') + '</td>' +
                                '<td>' + (processed.processed_at || '') + '</td>' +
                                '<td><button class="view-request" data-request=\'' + JSON.stringify(processed) + '\'>View Details</button></td>';
                            historyTable.prepend(tr);

                            // Re-attach modal event for new button
                            tr.querySelector('.view-request').onclick = function(e) {
                                e.preventDefault();
                                var data = processed;
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
                                    {label: "Sponsorship Budget", key: "sponsorship_budget"}, // fixed label
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
                                var table = '<table class="modal-details-table">';
                                fields.forEach(function(f) {
                                    if (data[f.key] !== undefined) {
                                        table += '<tr><td class="modal-label">'+f.label+':</td><td class="modal-value">'+(data[f.key]||'')+'</td></tr>';
                                    }
                                });
                                table += '</table>';
                                table += '<div class="modal-divider"></div>';
                                table += '<table class="modal-details-table">';
                                extraFields.forEach(function(f) {
                                    if (data[f.key] !== undefined) {
                                        table += '<tr><td class="modal-label">'+f.label+':</td><td class="modal-value">'+(data[f.key]||'')+'</td></tr>';
                                    }
                                });
                                table += '</table>';
                                document.getElementById('modalBody').innerHTML = table;
                                document.getElementById('modalActions').innerHTML = '';
                                document.getElementById('requestModal').style.display = 'block';
                                document.getElementById('requestModal').setAttribute('data-id', data.event_form_id);
                            };
                        }
                    } catch (e) {
                        // fallback: reload page if backend does not return JSON
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