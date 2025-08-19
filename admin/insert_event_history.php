<?php
include '../connection.php';

$connection = CONNECTIVITY();

$fields = [
    'event_form_id','event_name','event_title','event_date','sender_email','date_time_ingress','date_time_egress','place','location','sponsorship_budg','target_audience','number_audience','set_up','booth_size','booth_inclusion','number_tables','number_chairs','speaking_slot','date_time','program_target','technical_team','trainer_needed','ready_to_use','provide_materials','created_at','user_id','request_status','processed_at'
];

$values = [];
foreach ($fields as $field) {
    if ($field === 'processed_at') {
        $values[$field] = date('Y-m-d H:i:s'); // set to current time
    } else {
        $values[$field] = isset($_POST[$field]) ? $_POST[$field] : '';
    }
}

$sql = "INSERT INTO event_form_history (" . implode(',', $fields) . ") VALUES (" . implode(',', array_fill(0, count($fields), '?')) . ")";
$stmt = mysqli_prepare($connection, $sql);

$types = str_repeat('s', count($fields));
mysqli_stmt_bind_param($stmt, $types, ...array_values($values));
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($connection)]);
}

mysqli_stmt_close($stmt);
DISCONNECTIVITY($connection);
?>
