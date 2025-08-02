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
                // Insert into history table
                $fields = array_keys($row);
                $fields[] = 'processed_at';
                $fields_str = implode(',', $fields);

                $values = array_map(function($v) use ($connection) {
                    return "'" . mysqli_real_escape_string($connection, $v) . "'";
                }, array_values($row));
                $values[] = "NOW()";
                $values_str = implode(',', $values);

                $insert = mysqli_query($connection, "INSERT INTO event_form_history ($fields_str) VALUES ($values_str)");

                if ($insert) {
                    // Delete from main table
                    mysqli_query($connection, "DELETE FROM event_form WHERE event_form_id = $id");
                    echo "success";
                } else {
                    echo "error";
                }
            } else {
                echo "not found";
            }
        } else {
            echo "success";
        }
    } else {
        echo "error";
    }
    $stmt->close();
    $connection->close();
}
?>
