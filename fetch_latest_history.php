<?php
include 'connection.php';
$connection = CONNECTIVITY();
$id = intval($_GET['id']);
$query = "SELECT * FROM event_form_history WHERE event_form_id = $id ORDER BY processed_at DESC LIMIT 1";
$result = mysqli_query($connection, $query);
$row = mysqli_fetch_assoc($result);

if ($row && $row['request_mats']) {
    $materials = [];
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
    $row['requested_materials'] = $materials;
} else {
    $row['requested_materials'] = [];
}

echo json_encode($row);
DISCONNECTIVITY($connection);
?>
