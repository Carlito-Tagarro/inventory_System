<?php
include 'connection.php';
session_start();


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'trainer') {
    header("Location: login.php"); 
    exit;
}
$connection = CONNECTIVITY();
// $connection->query("PRAGMA journal_mode = WAL;"); // Sqlite WAL mode

// Simple input sanitization
function clean($data) {
    return htmlspecialchars(trim($data));
}

$submit_status = null; // Track submit status

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $event_name = clean($_POST['event_name']);
    $event_title = clean($_POST['event_title']);
    $event_date = clean($_POST['event_date']);
    $contact_person = clean($_POST['contact_person']);
    $contact_number = clean($_POST['contact_number']);
    $event_duration = clean($_POST['event_duration']);
    $date_time_ingress = clean($_POST['date_time_ingress']);
    $date_time_egress = clean($_POST['date_time_egress']);
    $claiming_id = clean($_POST['claiming_id']);
    $place = clean($_POST['place']);
    $location = clean($_POST['location']);
    $sponsorship_budg = clean($_POST['sponsorship_budg']);
    $amount = floatval($_POST['amount']);
    $target_audience = clean($_POST['target_audience']);
    $number_audience = clean($_POST['number_audience']); 
    $set_up = clean($_POST['set_up']);
    $booth_size = clean($_POST['booth_size']);
    $booth_inclusion = clean($_POST['booth_inclusion']);
    $number_tables = intval($_POST['number_tables']);
    $number_chairs = intval($_POST['number_chairs']);
    $speaking_slot = clean($_POST['speaking_slot']);
    $speaker_name = clean($_POST['speaker_name']);
    $date_time = clean($_POST['date_time']);
    $duration = clean($_POST['duration']);
    $topic = clean($_POST['topic']);
    $technical_team = clean($_POST['technical_team']);
    $technical_task = clean($_POST['technical_task']);
    $trainer_needed = clean($_POST['trainer_needed']);
    $trainer_task = clean($_POST['trainer_task']);
    $provide_materials = clean($_POST['provide_materials']);
    $requested_by = clean($_POST['requested_by']);

    
    $user_id = $_SESSION['user_id'];

    // NEW: Get selected materials from hidden input 
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
        event_name, event_title, event_date, contact_person, contact_number, event_duration, date_time_ingress, date_time_egress, claiming_id, place, location,
        sponsorship_budg, amount, target_audience, number_audience, set_up, booth_size, booth_inclusion,
        number_tables, number_chairs, speaking_slot, speaker_name, date_time, duration, Topic, technical_team, technical_task,
        trainer_needed, trainer_task, provide_materials, requested_by, user_id, request_mats
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";

    $stmt = $connection->prepare($sql);
    $stmt->bind_param(
        "ssssssssssssisssssiisssssssssssis",
        $event_name, $event_title, $event_date, $contact_person, $contact_number, $event_duration, $date_time_ingress, $date_time_egress, $claiming_id, $place, $location,
        $sponsorship_budg, $amount, $target_audience, $number_audience, $set_up, $booth_size, $booth_inclusion,
        $number_tables, $number_chairs, $speaking_slot, $speaker_name, $date_time, $duration, $topic, $technical_team, $technical_task,
        $trainer_needed, $trainer_task, $provide_materials, $requested_by, $user_id, $request_mats
    );

    if ($stmt->execute()) {
        $event_form_id = $connection->insert_id;
        // Save team members to Team table
        for ($i = 1; $i <= 5; $i++) {
            for ($j = 1; $j <= 2; $j++) {
                $field = "team_member_{$i}_{$j}";
                if (!empty($_POST[$field])) {
                    $attendee_name = clean($_POST[$field]);
                    $stmt_att = $connection->prepare("INSERT INTO team (event_form_id, attendee_name) VALUES (?, ?)");
                    $stmt_att->bind_param("is", $event_form_id, $attendee_name);
                    $stmt_att->execute();
                    $stmt_att->close();
                }
            }
        }
        $_SESSION['submit_status'] = "success";
    } else {
        $_SESSION['submit_status'] = "error";
        $_SESSION['submit_error'] = $stmt->error;
    }
    $stmt->close();
    $connection->close();

    // Redirect back to userpage.php to avoid blank page and resubmission
    header("Location: userpage.php");
    exit;
} else {

    // --- Fetch booked event dates and names ---
    $connection = CONNECTIVITY();
    $booked_events = [];
    // Only fetch events with request_status = 'Approved'
    $result = $connection->query("SELECT date_time_ingress, date_time_egress, event_name FROM event_form_history WHERE request_status = 'Approved'");
$booked_events = [];
if ($result) {
   while ($row = $result->fetch_assoc()) {
    $start = date('c', strtotime($row['date_time_ingress']));
    $end = date('c', strtotime($row['date_time_egress']));
    $booked_events[] = [
        'start' => $start,
        'end' => $end,
        'name' => $row['event_name']
    ];
}
    $result->free();
    echo "<script>var bookedEvents = " . json_encode($booked_events) . ";</script>";
}
    DISCONNECTIVITY($connection);
    ?>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Event Form</title>
        <link rel="icon" type="image/x-icon" href="images/images__1_-removebg-preview.png">
        <!-- Add external CSS -->
        <link rel="stylesheet" href="CSS/userpage.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
        <!-- Remove embedded <style> block -->
    </head>
    <body>
    <nav>
    <img src="images/AUDENTES LOGO.png" alt="Company Logo">

    <div class="nav-container">
        <!-- Hamburger -->
        <button class="hamburger" id="hamburgerBtn" aria-label="Toggle menu" aria-expanded="false">
            <span class="hamburger-bar"></span>
            <span class="hamburger-bar"></span>
            <span class="hamburger-bar"></span>
        </button>

        <!-- Links -->
        <div class="nav-links" id="navLinks">
            <a href="index.php">Home</a>
            <a href="https://www.facebook.com/audentestechnologies" target="_blank">About</a>
            <a href="https://www.facebook.com/audentestechnologies" target="_blank">Contact</a>
            <a href="logout.php" class="logout-link">Logout</a>
        </div>
    </div>
</nav>


    <!-- <div style="display:flex; gap:40px;">
    <div class="container">
    </div> -->
    <div style="flex:1;">
        <div id="calendar"></div>
    </div>
    </div>
        <div class="container">
            <h2>Event Form</h2>
            <form action="userpage.php" method="post" id="eventForm">
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
                        <input type="text" name="event_date" id="event_date" required>
                    </div>
                    <div class="form-group">
                        <label for="contact_person">Contact Person</label>
                        <input type="text" name="contact_person" id="contact_person" required>
                    </div>
                     <div class="form-group">
                        <label for="contact_number">Contact Number</label>
                        <input type="text" name="contact_number" id="contact_number" placeholder="e.g +63 --- --- ----" required>
                    </div>
                    <div class="form-group">
                        <label for="event_duration">Number of Event Days</label>
                        <input type="text" name="event_duration" id="event_duration" required>
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
                        <label for="claiming_id">Claiming of ID</label>
                        <textarea name="claiming_id" id="claiming_id"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="place">Event Place</label>
                        <input type="text" name="place" id="place" maxlength="100" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" name="location" id="location" maxlength="255" required>
                    </div>
                </fieldset>
                 <fieldset>
                    <legend>Team Members</legend>
                    <table style="width:100%; border-collapse:collapse; margin-bottom:12px;">
                        
                        <tbody>
                            <tr>
                                <td><input type="text" name="team_member_1_1" maxlength="100" style="width:95%"></td>
                                <td><input type="text" name="team_member_1_2" maxlength="100" style="width:95%"></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="team_member_2_1" maxlength="100" style="width:95%"></td>
                                <td><input type="text" name="team_member_2_2" maxlength="100" style="width:95%"></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="team_member_3_1" maxlength="100" style="width:95%"></td>
                                <td><input type="text" name="team_member_3_2" maxlength="100" style="width:95%"></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="team_member_4_1" maxlength="100" style="width:95%"></td>
                                <td><input type="text" name="team_member_4_2" maxlength="100" style="width:95%"></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="team_member_5_1" maxlength="100" style="width:95%"></td>
                                <td><input type="text" name="team_member_5_2" maxlength="100" style="width:95%"></td>
                            </tr>
                             <tr>
                                <td><input type="text" name="team_member_5_1" maxlength="100" style="width:95%"></td>
                                <td><input type="text" name="team_member_5_2" maxlength="100" style="width:95%"></td>
                            </tr>
                             <tr>
                                <td><input type="text" name="team_member_5_1" maxlength="100" style="width:95%"></td>
                                <td><input type="text" name="team_member_5_2" maxlength="100" style="width:95%"></td>
                            </tr>
                             <tr>
                                <td><input type="text" name="team_member_5_1" maxlength="100" style="width:95%"></td>
                                <td><input type="text" name="team_member_5_2" maxlength="100" style="width:95%"></td>
                            </tr>
                        </tbody>
                    </table>
                    <small style="color:#888;">Enter the names of people that will help for this event.</small>
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
                    <div class="form-group">
                        <label for="amount">Amount (₱)</label>
                        <input type="number" name="amount" id="amount" min="0">
                    </div>
                    </div>
                    <div class="form-group">
                        <label for="target_audience">Target Audience</label>
                        <textarea name="target_audience" id="target_audience"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="number_audience">Number of Audience</label>
                        <textarea name="number_audience" id="number_audience"></textarea>
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
                        <select name="speaking_slot" id="speaking_slot">
                            <option value="">--Select--</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                     <div class="form-group">
                        <label for="speaker_name">Speaker</label>
                        <input type="text" name="speaker_name" id="speaker_name" maxlength="100">
                    </div>    
                    </div>
                    <div class="form-group">
                        <label for="date_time">Date Time</label>
                        <input type="datetime-local" name="date_time" id="date_time">
                    </div>
                     <div class="form-group">
                        <label for="duration">Duration</label>
                        <input type="text" name="duration" id="duration" maxlength="255">
                    </div>
                    <div class="form-group">
                        <label for="topic">Topic</label>
                        <input type="text" name="topic" id="topic" maxlength="255">
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
                        <label for="technical_task">Technical Task</label>
                        <textarea name="technical_task" id="technical_task"></textarea>
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
                        <label for="trainer_task">Trainer Task</label>
                        <textarea name="trainer_task" id="trainer_task"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="provide_materials">Provide Materials</label>
                        <select name="provide_materials" id="provide_materials" required>
                            <option value="">--Select--</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="requested_by">Requested By</label>
                        <input type="text" name="requested_by" id="requested_by" maxlength="100" required>
                    </div>
                </fieldset>
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
            <!-- Add link/button to open return request form -->
            <div style="text-align:right; margin-bottom:16px;">
                <a href="return_request.php" style="background:#28a745;color:#fff;padding:8px 18px;border-radius:4px;text-decoration:none;">
                    Return Unused Materials
                </a>
            </div>
        </div>
        <!-- Modal HTML -->
        <div id="materialsModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeModal">&times;</span>
                <h3 style="margin-top:0; text-align:center;">
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
        <!-- Remove embedded <script> block -->
        <!-- Output bookedEvents and bookedDates for JS -->
        <script>
            var bookedEvents = <?php echo json_encode($booked_events); ?>;
            // var bookedDates = bookedEvents.map(function(ev) { return ev.date; });
            <?php if (isset($_SESSION['submit_status'])): ?>
                document.addEventListener('DOMContentLoaded', function() {
                    <?php if ($_SESSION['submit_status'] === "success"): ?>
                        alert("Event submitted successfully!");
                        document.getElementById('eventForm').reset();
                        var selectedPreview = document.getElementById('selectedMaterialsPreview');
                        var selectedList = document.getElementById('selectedMaterialsList');
                        selectedPreview.style.display = 'none';
                        selectedList.innerHTML = '';
                    <?php else: ?>
                        alert("Error submitting event: <?php echo addslashes($_SESSION['submit_error']); ?>");
                    <?php endif; ?>
                });
            <?php
                unset($_SESSION['submit_status']);
                unset($_SESSION['submit_error']);
            endif;
            ?>

            
           
        </script>
       
        <script src="JavaScripts/userpage.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    </body>
    </html>
    <?php
}
?>