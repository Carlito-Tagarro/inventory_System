
<?php

//ADMIN INVENTORY AND REQUESTS FUNCTIONS !!!

include '../connection.php';
session_start();

date_default_timezone_set('Asia/Manila'); // Set timezone before any date() calls

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if (!$id || !in_array($status, ['Approved', 'Declined'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
        exit;
    }

    $connection = CONNECTIVITY();

    // Fetch request and requested materials BEFORE deleting
$event_query = "SELECT request_mats FROM event_form WHERE event_form_id = $id";
$event_result = mysqli_query($connection, $event_query);
$event_row = mysqli_fetch_assoc($event_result);

if ($event_row && $event_row['request_mats'] && $status === 'Approved') {
    $mat_ids = $event_row['request_mats'];
    $mat_id_array = array_map('intval', explode(',', $mat_ids));
    $mat_id_list = implode(',', $mat_id_array);

    // Fetch all material requests for this event
    $mat_query = "SELECT * FROM material_request_form WHERE request_mats IN ($mat_id_list)";
    $mat_result = mysqli_query($connection, $mat_query);

    while ($mat_row = mysqli_fetch_assoc($mat_result)) {
        // Deduct Brochure(s)
        if (!empty($mat_row['name_brochures']) && intval($mat_row['brochure_quantity']) > 0) {
            // Support multiple brochures separated by comma
            $brochure_names = explode(',', $mat_row['name_brochures']);
            $brochure_qtys = explode(',', $mat_row['brochure_quantity']);
            foreach ($brochure_names as $idx => $brochure_name) {
                $qty = isset($brochure_qtys[$idx]) ? intval($brochure_qtys[$idx]) : intval($mat_row['brochure_quantity']);
                $brochure_name = trim($brochure_name);
                if ($brochure_name && $qty > 0) {
                    $update_brochure = $connection->query("UPDATE brochures SET quantity = GREATEST(quantity - $qty, 0) WHERE TRIM(brochure_name) = '" . $connection->real_escape_string($brochure_name) . "'");
                    if (!$update_brochure) {
                        echo "Error updating brochures: " . $connection->error;
                    }
                }
            }
        }

        // Deduct Swag(s)
        if (!empty($mat_row['name_swag']) && intval($mat_row['swag_quantity']) > 0) {
            $swag_names = explode(',', $mat_row['name_swag']);
            $qty = intval($mat_row['swag_quantity']);
            foreach ($swag_names as $swag_name) {
                $swag_name = trim($swag_name);
                if ($swag_name && $qty > 0) {
                    $update_swag = $connection->query("UPDATE swags SET quantity = GREATEST(quantity - $qty, 0) WHERE TRIM(swags_name) = '" . $connection->real_escape_string($swag_name) . "'");
                    if (!$update_swag) {
                        error_log("Error updating swags for $swag_name: " . $connection->error);
                    }
                }
            }
        }

        // Deduct Marketing Material(s)
        if (!empty($mat_row['name_material']) && intval($mat_row['material_quantity']) > 0) {
            $material_names = explode(',', $mat_row['name_material']);
            $qty = intval($mat_row['material_quantity']);
            foreach ($material_names as $material_name) {
                $material_name = trim($material_name);
                if ($material_name && $qty > 0) {
                    $update_material = $connection->query("UPDATE marketing_materials SET quantity = GREATEST(quantity - $qty, 0) WHERE TRIM(material_name) = '" . $connection->real_escape_string($material_name) . "'");
                    if (!$update_material) {
                        error_log("Error updating marketing materials for $material_name: " . $connection->error);
                    }
                }
            }
        }
    }
}

    // Update status
    $stmt = $connection->prepare("UPDATE event_form SET request_status = ? WHERE event_form_id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Move to history
        $select = mysqli_query($connection, "SELECT event_form.*, users.email AS sender_email 
          FROM event_form 
          LEFT JOIN users ON event_form.user_id = users.user_id");
        $row = mysqli_fetch_assoc($select);

        if ($row) {
            // Insert into history table using prepared statement
            $fields = [
                'event_form_id','event_name','event_title','event_date','sender_email','date_time_ingress','date_time_egress','place','location','sponsorship_budg','target_audience','number_audience','set_up','booth_size','booth_inclusion','number_tables','number_chairs','speaking_slot','date_time','program_target','technical_team','trainer_needed','ready_to_use','provide_materials','created_at','user_id','request_mats','request_status','processed_at'
            ];
            $values = [];
            foreach ($fields as $field) {
                if ($field === 'request_status') {
                    $values[] = $status;
                } elseif ($field === 'processed_at') {
                    $values[] = date('Y-m-d H:i:s'); // Use PHP date with correct timezone
                } else {
                    $values[] = isset($row[$field]) ? $row[$field] : '';
                }
            }
            $sql = "INSERT INTO event_form_history (" . implode(',', $fields) . ") VALUES (" . implode(',', array_fill(0, count($fields), '?')) . ")";
            $insert_stmt = mysqli_prepare($connection, $sql);
            $types = str_repeat('s', count($fields));
            mysqli_stmt_bind_param($insert_stmt, $types, ...$values);
            mysqli_stmt_execute($insert_stmt);

            if (mysqli_stmt_affected_rows($insert_stmt) > 0) {
                mysqli_query($connection, "DELETE FROM event_form WHERE event_form_id = $id");
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => mysqli_error($connection)]);
            }
            mysqli_stmt_close($insert_stmt);
        } else {
            echo json_encode(['success' => false, 'error' => 'not found']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'update failed']);
    }
    $stmt->close();
    DISCONNECTIVITY($connection);
}


   