<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $status = $_POST['status'];

    $connection = CONNECTIVITY();

    $stmt = $connection->prepare("UPDATE event_form SET request_status = ? WHERE event_form_id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        if ($status === 'Approved' || $status === 'Declined') {
            // Get the request data
            $select = mysqli_query($connection, "SELECT * FROM event_form WHERE event_form_id = $id");
            $row = mysqli_fetch_assoc($select);

            if ($row) {
                // Insert into history table using prepared statement
                $fields = [
                    'event_form_id','event_name','event_title','event_date','sender_email','date_time_ingress','date_time_egress','place','location','sponsorship_budget','target_audience','number_audience','set_up','booth_size','booth_inclusion','number_tables','number_chairs','speaking_slot','date_time','program_target','technical_team','trainer_needed','ready_to_use','provide_materials','created_at','user_id','request_mats','request_status','processed_at'
                ];
                $values = [];
                foreach ($fields as $field) {
                    if ($field === 'request_status') {
                        $values[] = $status;
                    } elseif ($field === 'processed_at') {
                        $values[] = date('Y-m-d H:i:s');
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
                    // Delete from main table
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
            echo json_encode(['success' => true]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'update failed']);
    }
    $stmt->close();
    $connection->close();
}
?>
