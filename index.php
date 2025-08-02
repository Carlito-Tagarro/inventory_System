<?php
include 'connection.php';
session_start();


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'trainer') {
    header("Location: login.php"); 
    exit;
}
$connection = CONNECTIVITY();

// Simple input sanitization
function clean($data) {
    return htmlspecialchars(trim($data));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $event_name = clean($_POST['event_name']);
    $event_title = clean($_POST['event_title']);
    $event_date = clean($_POST['event_date']);
    $date_time_ingress = clean($_POST['date_time_ingress']);
    $date_time_egress = clean($_POST['date_time_egress']);
    $place = clean($_POST['place']);
    $location = clean($_POST['location']);
    $sponsorship_budg = clean($_POST['sponsorship_budg']);
    $target_audience = clean($_POST['target_audience']);
    $number_audience = intval($_POST['number_audience']);
    $set_up = clean($_POST['set_up']);
    $booth_size = clean($_POST['booth_size']);
    $booth_inclusion = clean($_POST['booth_inclusion']);
    $number_tables = intval($_POST['number_tables']);
    $number_chairs = intval($_POST['number_chairs']);
    $speaking_slot = clean($_POST['speaking_slot']);
    $date_time = clean($_POST['date_time']);
    $program_target = clean($_POST['program_target']);
    $technical_team = clean($_POST['technical_team']);
    $trainer_needed = clean($_POST['trainer_needed']);
    $ready_to_use = clean($_POST['ready_to_use']);
    $provide_materials = clean($_POST['provide_materials']);

    // Get user_id from session (assuming it's set at login)
    $user_id = $_SESSION['user_id'];

    // --- NEW: Get selected materials from hidden input ---
    $selected_materials_json = isset($_POST['selected_materials']) ? $_POST['selected_materials'] : '';
    $selected_materials = [];
    if ($selected_materials_json) {
        $selected_materials = json_decode($selected_materials_json, true);
    }

    $request_mats = NULL;
    $conn = CONNECTIVITY();
    if ($provide_materials === 'Yes' && !empty($selected_materials)) {
        $first_id = null;
        $first_row_inserted = false;
        foreach ($selected_materials as $mat) {
            $name_brochures = '';
            $brochure_quantity = '';
            $name_swag = '';
            $swag_quantity = '';
            $name_material = '';
            $material_quantity = '';
            if ($mat['type'] === 'Brochure') {
                $name_brochures = $mat['name'];
                $brochure_quantity = $mat['qty'];
            } elseif ($mat['type'] === 'Swag') {
                $name_swag = $mat['name'];
                $swag_quantity = $mat['qty'];
            } elseif ($mat['type'] === 'Marketing Material') {
                $name_material = $mat['name'];
                $material_quantity = $mat['qty'];
            }
            if (!$first_row_inserted) {
                // Insert first row with request_mats = 0 (temporary, must be NOT NULL)
                $stmt = $conn->prepare("INSERT INTO material_request_form (request_mats, name_brochures, brochure_quantity, name_swag, swag_quantity, name_material, material_quantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $tmp = 0;
                $stmt->bind_param(
                    "issssss",
                    $tmp,
                    $name_brochures,
                    $brochure_quantity,
                    $name_swag,
                    $swag_quantity,
                    $name_material,
                    $material_quantity
                );
                $stmt->execute();
                $first_id = $conn->insert_id;
                $stmt->close();
                // Update the first row to set request_mats = first_id
                $stmt = $conn->prepare("UPDATE material_request_form SET request_mats = ? WHERE material_request_id = ?");
                $stmt->bind_param("ii", $first_id, $first_id);
                $stmt->execute();
                $stmt->close();
                $first_row_inserted = true;
            } else {
                // For subsequent materials, use the first_id as the group id
                $stmt = $conn->prepare("INSERT INTO material_request_form (request_mats, name_brochures, brochure_quantity, name_swag, swag_quantity, name_material, material_quantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param(
                    "issssss",
                    $first_id,
                    $name_brochures,
                    $brochure_quantity,
                    $name_swag,
                    $swag_quantity,
                    $name_material,
                    $material_quantity
                );
                $stmt->execute();
                $stmt->close();
            }
        }
        $request_mats = $first_id;
    }
    DISCONNECTIVITY($conn);

    // --- Insert event form, including request_mats (or NULL if none) ---
    $connection = CONNECTIVITY();
    $sql = "INSERT INTO event_form (
        event_name, event_title, event_date, date_time_ingress, date_time_egress, place, location,
        sponsorship_budg, target_audience, number_audience, set_up, booth_size, booth_inclusion,
        number_tables, number_chairs, speaking_slot, date_time, program_target, technical_team,
        trainer_needed, ready_to_use, provide_materials, user_id, request_mats
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $connection->prepare($sql);
    $stmt->bind_param(
        "sssssssssisssiisssssssis",
        $event_name, $event_title, $event_date, $date_time_ingress, $date_time_egress, $place, $location,
        $sponsorship_budg, $target_audience, $number_audience, $set_up, $booth_size, $booth_inclusion,
        $number_tables, $number_chairs, $speaking_slot, $date_time, $program_target, $technical_team,
        $trainer_needed, $ready_to_use, $provide_materials, $user_id, $request_mats
    );

    if ($stmt->execute()) {
        echo "<h2>Event submitted successfully!</h2>";
        echo '<a href="index.php">Back to form</a>';
    } else {
        echo "<h2>Error submitting event: " . $stmt->error . "</h2>";
        echo '<a href="index.php">Back to form</a>';
    }
    $stmt->close();
    $connection->close();
} else {
    // Show the form on GET request (no redirect)
    DISCONNECTIVITY($connection);
    ?>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inventory System</title>
        <link rel="icon" type="image/x-icon" href="images/images__1_-removebg-preview.png">
        <style>
            body { font-family: Arial, sans-serif; background: #f7f7f7; }
            .container { max-width: 800px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px #ccc; }
            h2 { text-align: center; }
            form { display: flex; flex-wrap: wrap; gap: 16px; }
            .form-group { 
                flex: 1 1 45%; 
                display: flex; 
                flex-direction: column; 
            }
            /* Add this to prevent button from stretching */
            .form-group.button-group {
                flex: 1 1 100%;
                align-items: center; /* center horizontally */
                width: 100%;
            }
            label { margin-bottom: 4px; font-weight: bold; }
            input, select, textarea { padding: 6px; border: 1px solid #ccc; border-radius: 4px; }
            .full-width { flex: 1 1 100%; }
            button { 
                padding: 4px 12px;
                font-size: 12px;
                width: auto;
                min-width: 0;
                max-width: 120px; /* limit button width */
                background: #007bff; 
                color: #fff; 
                border: none; 
                border-radius: 4px; 
                cursor: pointer;
                display: inline-block;
                margin: 16px 0 0 0;
            }
            button:hover { background: #0056b3; }
            fieldset { border: 1px solid #007bff; border-radius: 4px; padding: 16px; margin-bottom: 16px; }
            legend { font-weight: bold; padding: 0 10px; }
            /* Modal styles */
            .modal {
                display: none; 
                position: fixed; 
                z-index: 999; 
                left: 0; top: 0; width: 100%; height: 100%;
                overflow: auto; background: rgba(0,0,0,0.4);
            }
            .modal-content {
                background: #fff; margin: 5% auto; padding: 32px 24px;
                border: 1px solid #007bff; width: 900px; border-radius: 12px;
                position: relative; box-shadow: 0 8px 32px rgba(0,123,255,0.15);
            }
            .modal-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 8px;
                font-size: 14px;
            }
            .modal-table th, .modal-table td {
                border: 1px solid #e3e3e3;
                padding: 8px 10px;
                text-align: left;
            }
            .modal-table th {
                background: #f0f8ff;
                color: #007bff;
                font-weight: 600;
            }
            .modal-table tr:nth-child(even) {
                background: #f9f9f9;
            }
            .modal-table tr:hover {
                background: #e6f0ff;
            }
            .modal-content h4 {
                font-size: 16px;
                margin-top: 18px;
                margin-bottom: 8px;
                font-weight: 600;
            }
            .modal-content hr {
                border: none;
                border-top: 1px solid #e3e3e3;
            }
            .close {
                color: #aaa; position: absolute; right: 18px; top: 12px;
                font-size: 32px; font-weight: bold; cursor: pointer;
                transition: color 0.2s;
            }
            .close:hover { color: #007bff; }
            @media (max-width: 600px) {
                .modal-content { width: 98%; padding: 12px; }
                .modal-table th, .modal-table td { padding: 6px 4px; font-size: 12px; }
            }
        </style>
    </head>
    <body>
        <a href="logout.php">Logout</a>
        <div class="container">
            <h2>Event Form</h2>
            <form action="index.php" method="post" id="eventForm">
                <fieldset>
                    <legend>Event Details</legend>
                    <div class="form-group">
                        <label for="event_name">Event Name</label>
                        <input type="text" name="event_name" id="event_name" maxlength="100" required>
                    </div>
                    <div class="form-group">
                        <label for="event_title">Event Title</label>
                        <input type="text" name="event_title" id="event_title" maxlength="100" required>
                    </div>
                    <div class="form-group">
                        <label for="event_date">Event Date</label>
                        <input type="date" name="event_date" id="event_date" required>
                    </div>
                    <div class="form-group">
                        <label for="date_time_ingress">Date Time Ingress</label>
                        <input type="datetime-local" name="date_time_ingress" id="date_time_ingress" required>
                    </div>
                    <div class="form-group">
                        <label for="date_time_egress">Date Time Egress</label>
                        <input type="datetime-local" name="date_time_egress" id="date_time_egress" required>
                    </div>
                    <div class="form-group">
                        <label for="place">Place</label>
                        <input type="text" name="place" id="place" maxlength="100" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" name="location" id="location" maxlength="255" required>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Budgeting & Audience</legend>
                    <div class="form-group">
                        <label for="sponsorship_budg">Sponsorship Budget</label>
                        <select name="sponsorship_budg" id="sponsorship_budg">
                            <option value="">--Select--</option>
                            <option value="Free">Free</option>
                            <option value="Sponsorship">Sponsorship</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="target_audience">Target Audience</label>
                        <textarea name="target_audience" id="target_audience"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="number_audience">Number Audience</label>
                        <input type="number" name="number_audience" id="number_audience" min="0">
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Booth & Other Setup</legend>
                    <div class="form-group">
                        <label for="set_up">Set Up</label>
                        <select name="set_up" id="set_up">
                            <option value="">--Select--</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="booth_size">Booth Size</label>
                        <textarea name="booth_size" id="booth_size"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="booth_inclusion">Booth Inclusion</label>
                        <textarea name="booth_inclusion" id="booth_inclusion"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="number_tables">Number of Tables</label>
                        <input type="number" name="number_tables" id="number_tables" min="0" max="999">
                    </div>
                    <div class="form-group">
                        <label for="number_chairs">Number of Chairs</label>
                        <input type="number" name="number_chairs" id="number_chairs" min="0" max="999">
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Programs & Marketing</legend>
                    <div class="form-group">
                        <label for="speaking_slot">Speaking Slot</label>
                        <input type="text" name="speaking_slot" id="speaking_slot" maxlength="255">
                    </div>
                    <div class="form-group">
                        <label for="date_time">Date Time</label>
                        <input type="datetime-local" name="date_time" id="date_time">
                    </div>
                    <div class="form-group">
                        <label for="program_target">Program Target</label>
                        <input type="text" name="program_target" id="program_target" maxlength="255">
                    </div>
                    <div class="form-group">
                        <label for="technical_team">Technical Team</label>
                        <select name="technical_team" id="technical_team" required>
                            <option value="">--Select--</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="trainer_needed">Trainer Needed</label>
                        <select name="trainer_needed" id="trainer_needed" required>
                            <option value="">--Select--</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ready_to_use">Ready to Use</label>
                        <textarea name="ready_to_use" id="ready_to_use"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="provide_materials">Provide Materials</label>
                        <select name="provide_materials" id="provide_materials" required>
                            <option value="">--Select--</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                </fieldset>
                <!-- New fieldset to show selected items from modal -->
                <fieldset>
                    <legend>Selected Materials</legend>
                    <div id="selectedMaterialsPreview" class="form-group full-width" style="margin-top:8px;">
                        <label style="font-weight:bold; color:#007bff;">Selected Materials:</label>
                        <table style="width:100%; background:#f8faff; border:1px solid #e3e3e3; border-radius:6px; border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding:8px;">Name</th>
                                    <th style="text-align:left; padding:8px;">Quantity</th>
                                </tr>
                            </thead>
                            <tbody id="selectedMaterialsList"></tbody>
                        </table>
                    </div>
                </fieldset>
                <div class="form-group button-group">
                    <button type="submit">Submit Form</button>
                </div>
                <!-- NEW: Hidden input for selected materials -->
                <input type="hidden" name="selected_materials" id="selected_materials_input">
            </form>
        </div>
        <!-- Modal HTML -->
        <div id="materialsModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeModal">&times;</span>
                <h3 style="margin-top:0; text-align:center;">
                    <img src="images/materials_icon.png" alt="Materials" style="height:32px;vertical-align:middle;margin-right:8px;">
                    Provide Materials
                </h3>
                <p style="text-align:center; color:#555;">Please specify the materials you will provide for this event.</p>
                <hr style="margin:16px 0;">
                <!-- Filter Dropdown -->
                <div style="margin-bottom:16px;">
                    <label for="categoryFilter" style="font-weight:600;">Filter by Category:</label>
                    <select id="categoryFilter" style="padding:4px 8px; border-radius:4px;">
                        <option value="All">All</option>
                        <option value="Brochure">Brochure</option>
                        <option value="Marketing Material">Marketing Material</option>
                        <option value="Swag">Swag</option>
                    </select>
                    <input type="text" id="nameSearch" placeholder="Search by name..." style="margin-left:16px; padding:4px 8px; border-radius:4px; border:1px solid #ccc;">
                </div>
                <form id="materialsForm">
                <table class="modal-table" id="materialsTable">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Available</th>
                            <th>Others</th>
                            <th>Quantity to Provide</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $conn = CONNECTIVITY();
                    // Brochures
                    $result = $conn->query("SELECT brochure_id AS id, brochure_name AS name, quantity, '' AS others, 'Brochure' AS category FROM brochures");
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr data-category='Brochure'>
                                <td><input type='checkbox' name='brochures[]' value='{$row['id']}'></td>
                                <td>Brochure</td>
                                <td>{$row['name']}</td>
                                <td>{$row['quantity']}</td>
                                <td></td>
                                <td><input type='number' name='brochure_qty_{$row['id']}' min='1' max='{$row['quantity']}' style='width:60px;'></td>
                            </tr>";
                        }
                        $result->free();
                    }
                    // Marketing Materials
                    $result = $conn->query("SELECT material_id AS id, material_name AS name, quantity, others, 'Marketing Material' AS category FROM marketing_materials");
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr data-category='Marketing Material'>
                                <td><input type='checkbox' name='materials[]' value='{$row['id']}'></td>
                                <td>Marketing Material</td>
                                <td>{$row['name']}</td>
                                <td>{$row['quantity']}</td>
                                <td>{$row['others']}</td>
                                <td><input type='number' name='material_qty_{$row['id']}' min='1' max='{$row['quantity']}' style='width:60px;'></td>
                            </tr>";
                        }
                        $result->free();
                    }
                    // Swags
                    $result = $conn->query("SELECT swag_id AS id, swags_name AS name, quantity, '' AS others, 'Swag' AS category FROM swags");
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr data-category='Swag'>
                                <td><input type='checkbox' name='swags[]' value='{$row['id']}'></td>
                                <td>Swag</td>
                                <td>{$row['name']}</td>
                                <td>{$row['quantity']}</td>
                                <td></td>
                                <td><input type='number' name='swag_qty_{$row['id']}' min='1' max='{$row['quantity']}' style='width:60px;'></td>
                            </tr>";
                        }
                        $result->free();
                    }
                    DISCONNECTIVITY($conn);
                    ?>
                    </tbody>
                </table>
                <div style="text-align:right; margin-top:18px;">
                    <button type="submit" id="submitMaterialsBtn" style="background:#007bff;color:#fff;border:none;padding:8px 24px;border-radius:4px;font-size:15px;cursor:pointer;">
                        Submit Selected Materials
                    </button>
                </div>
                </form>
            </div>
        </div>
        <script>
        // Modal JS
        document.addEventListener('DOMContentLoaded', function() {
            var select = document.getElementById('provide_materials');
            var modal = document.getElementById('materialsModal');
            var closeBtn = document.getElementById('closeModal');
            var categoryFilter = document.getElementById('categoryFilter');
            var nameSearch = document.getElementById('nameSearch');
            var materialsTable = document.getElementById('materialsTable');
            select.addEventListener('change', function() {
                if (select.value === 'Yes') {
                    modal.style.display = 'block';
                } else {
                    modal.style.display = 'none';
                    // Clear selected materials if "No" is chosen
                    var selectedPreview = document.getElementById('selectedMaterialsPreview');
                    var selectedList = document.getElementById('selectedMaterialsList');
                    selectedPreview.style.display = 'none';
                    selectedList.innerHTML = '';
                    // Also uncheck all checkboxes and clear quantities in modal
                    var rows = document.querySelectorAll('#materialsTable tbody tr');
                    rows.forEach(function(row) {
                        var checkbox = row.querySelector('input[type="checkbox"]');
                        var qtyInput = row.querySelector('input[type="number"]');
                        if (checkbox) checkbox.checked = false;
                        if (qtyInput) {
                            qtyInput.value = '';
                            qtyInput.disabled = true;
                        }
                    });
                }
            });
            closeBtn.onclick = function() {
                modal.style.display = 'none';
                select.value = '';
            };
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            };
            // Filter logic
            function filterTable() {
                var categoryValue = categoryFilter.value;
                var searchValue = nameSearch.value.toLowerCase();
                var rows = materialsTable.querySelectorAll('tbody tr');
                rows.forEach(function(row) {
                    var matchesCategory = (categoryValue === 'All' || row.getAttribute('data-category') === categoryValue);
                    var nameCell = row.querySelector('td:nth-child(3)');
                    var matchesSearch = nameCell && nameCell.textContent.toLowerCase().includes(searchValue);
                    if (matchesCategory && matchesSearch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
            categoryFilter.addEventListener('change', filterTable);
            nameSearch.addEventListener('input', filterTable);

            // Enable quantity input only if checkbox is checked
            var table = document.getElementById('materialsTable');
            table.addEventListener('change', function(e) {
                if (e.target.type === 'checkbox') {
                    var row = e.target.closest('tr');
                    var qtyInput = row.querySelector('input[type="number"]');
                    if (qtyInput) {
                        qtyInput.disabled = !e.target.checked;
                        if (!e.target.checked) qtyInput.value = '';
                    }
                }
                // Quantity input validation
                if (e.target.type === 'number') {
                    var row = e.target.closest('tr');
                    var availableCell = row.querySelector('td:nth-child(4)');
                    var available = parseInt(availableCell.textContent, 10);
                    var val = parseInt(e.target.value, 10);
                    if (val > available) {
                        e.target.value = available;
                    } else if (val < 1 && e.target.value !== '') {
                        e.target.value = 1;
                    }
                }
            });
            // On page load, disable all quantity inputs
            var qtyInputs = table.querySelectorAll('input[type="number"]');
            qtyInputs.forEach(function(input) {
                input.disabled = true;
                // Prevent manual input above available
                input.addEventListener('input', function(e) {
                    var row = input.closest('tr');
                    var availableCell = row.querySelector('td:nth-child(4)');
                    var available = parseInt(availableCell.textContent, 10);
                    var val = parseInt(input.value, 10);
                    if (val > available) {
                        input.value = available;
                    } else if (val < 1 && input.value !== '') {
                        input.value = 1;
                    }
                });
            });
            // Show selected materials outside modal
            var materialsForm = document.getElementById('materialsForm');
            var selectedPreview = document.getElementById('selectedMaterialsPreview');
            var selectedList = document.getElementById('selectedMaterialsList');
            var submitMaterialsBtn = document.getElementById('submitMaterialsBtn');
            var selectedMaterialsInput = document.getElementById('selected_materials_input');
            materialsForm.addEventListener('submit', function(e) {
                e.preventDefault();
                var selected = [];
                var rows = materialsTable.querySelectorAll('tbody tr');
                rows.forEach(function(row) {
                    var checkbox = row.querySelector('input[type="checkbox"]');
                    var qtyInput = row.querySelector('input[type="number"]');
                    if (checkbox && checkbox.checked && qtyInput && qtyInput.value) {
                        var name = row.querySelector('td:nth-child(3)').textContent;
                        var qty = qtyInput.value;
                        var type = row.querySelector('td:nth-child(2)').textContent;
                        selected.push({name: name, qty: qty, type: type});
                    }
                });
                // Store selected materials as JSON in hidden input
                selectedMaterialsInput.value = JSON.stringify(selected);

                // Display selected materials as table
                var selectedPreview = document.getElementById('selectedMaterialsPreview');
                var selectedList = document.getElementById('selectedMaterialsList');
                if (selected.length > 0) {
                    selectedPreview.style.display = 'block';
                    var html = '';
                    selected.forEach(function(item) {
                        html += '<tr><td style="padding:8px;">' + item.name + '</td><td style="padding:8px;">' + item.qty + '</td></tr>';
                    });
                    selectedList.innerHTML = html;
                } else {
                    selectedPreview.style.display = 'none';
                    selectedList.innerHTML = '';
                }
                modal.style.display = 'none';
                select.value = 'Yes'; // keep selection
            });

            // --- NEW: On event form submit, ensure selected materials are included ---
            var eventForm = document.getElementById('eventForm');
            eventForm.addEventListener('submit', function(e) {
                // If provide_materials is Yes and no materials selected, prevent submit
                if (select.value === 'Yes' && !selectedMaterialsInput.value) {
                    alert('Please select materials to provide.');
                    e.preventDefault();
                }
            });
        });
        </script>
    </body>
    <style>
        /* ...existing code... */
        .modal-content {
            background: #fff; margin: 5% auto; padding: 32px 24px;
            border: 1px solid #007bff; width: 900px; border-radius: 12px;
            position: relative; box-shadow: 0 8px 32px rgba(0,123,255,0.15);
        }
        .modal-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .modal-table th, .modal-table td {
            border: 1px solid #e3e3e3;
            padding: 8px 10px;
            text-align: left;
        }
        .modal-table th {
            background: #f0f8ff;
            color: #007bff;
            font-weight: 600;
        }
        .modal-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        .modal-table tr:hover {
            background: #e6f0ff;
        }
        .modal-content h4 {
            font-size: 16px;
            margin-top: 18px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .modal-content hr {
            border: none;
            border-top: 1px solid #e3e3e3;
        }
        .close {
            color: #aaa; position: absolute; right: 18px; top: 12px;
            font-size: 32px; font-weight: bold; cursor: pointer;
            transition: color 0.2s;
        }
        .close:hover { color: #007bff; }
        @media (max-width: 600px) {
            .modal-content { width: 98%; padding: 12px; }
            .modal-table th, .modal-table td { padding: 6px 4px; font-size: 12px; }
        }
    </style>
    <!-- Font Awesome for icons (optional, can remove if not available) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- ...existing code... -->
    </html>
    <?php
}
?>

