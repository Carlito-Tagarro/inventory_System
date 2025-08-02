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

    // Prepare SQL statement (add user_id to columns and values)
    $sql = "INSERT INTO event_form (
        event_name, event_title, event_date, date_time_ingress, date_time_egress, place, location,
        sponsorship_budg, target_audience, number_audience, set_up, booth_size, booth_inclusion,
        number_tables, number_chairs, speaking_slot, date_time, program_target, technical_team,
        trainer_needed, ready_to_use, provide_materials, user_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $connection->prepare($sql);
    // Add i for user_id at the end (23 params)
    $stmt->bind_param(
        "sssssssssisssiisssssssi",
        $event_name, $event_title, $event_date, $date_time_ingress, $date_time_egress, $place, $location,
        $sponsorship_budg, $target_audience, $number_audience, $set_up, $booth_size, $booth_inclusion,
        $number_tables, $number_chairs, $speaking_slot, $date_time, $program_target, $technical_team,
        $trainer_needed, $ready_to_use, $provide_materials, $user_id
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
<<<<<<< HEAD
=======
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
>>>>>>> 3156dcf (Add your commit message here)
        </style>
    </head>
    <body>
        <a href="logout.php">Logout</a>
        <div class="container">
            <h2>Event Form</h2>
            <form action="index.php" method="post">
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
                <div class="form-group button-group">
                    <button type="submit">Submit Form</button>
                </div>
            </form>
        </div>
<<<<<<< HEAD
    </body>
    </html>
    <?php
}
?>
=======
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
                <!-- Brochures Table -->
                <h4 style="color:#007bff; margin-bottom:8px;"><i class="fa fa-book"></i> Brochures</h4>
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    // Fetch brochures
                    $brochures = [];
                    $conn = CONNECTIVITY();
                    $result = $conn->query("SELECT brochure_name, quantity, total_brochure FROM brochures");
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['brochure_name']}</td>
                                <td>{$row['quantity']}</td>
                            </tr>";
                        }
                        $result->free();
                    }
                    ?>
                    </tbody>
                </table>
                <hr style="margin:16px 0;">
                <!-- Marketing Materials Table -->
                <h4 style="color:#007bff; margin-bottom:8px;"><i class="fa fa-bullhorn"></i> Marketing Materials</h4>
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Quantity</th>
                            <th>Others</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    // Fetch marketing materials
                    $result = $conn->query("SELECT material_name, quantity, others FROM marketing_materials");
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['material_name']}</td>
                                <td>{$row['quantity']}</td>
                                <td>{$row['others']}</td>
                            </tr>";
                        }
                        $result->free();
                    }
                    ?>
                    </tbody>
                </table>
                <hr style="margin:16px 0;">
                <!-- Swags Table -->
                <h4 style="color:#007bff; margin-bottom:8px;"><i class="fa fa-gift"></i> Swags</h4>
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    // Fetch swags
                    $result = $conn->query("SELECT swags_name, quantity FROM swags");
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['swags_name']}</td>
                                <td>{$row['quantity']}</td>
                            </tr>";
                        }
                        $result->free();
                    }
                    DISCONNECTIVITY($conn);
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <script>
        // Modal JS
        document.addEventListener('DOMContentLoaded', function() {
            var select = document.getElementById('provide_materials');
            var modal = document.getElementById('materialsModal');
            var closeBtn = document.getElementById('closeModal');
            select.addEventListener('change', function() {
                if (select.value === 'Yes') {
                    modal.style.display = 'block';
                } else {
                    modal.style.display = 'none';
                }
            });
            closeBtn.onclick = function() {
                modal.style.display = 'none';
                select.value = ''; // Optionally reset selection
            };
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            };
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- ...existing code... -->
    </html>
    <?php
?>

>>>>>>> 3156dcf (Add your commit message here)
