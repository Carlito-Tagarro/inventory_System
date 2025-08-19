<?php
//Return Function
include 'connection.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_mats'])) {
    $request_mats = intval($_POST['request_mats']);
    $conn = CONNECTIVITY();
    $items = [];
    $res = $conn->query("SELECT name_brochures, brochure_quantity, name_swag, swag_quantity, name_material, material_quantity
        FROM material_request_form WHERE request_mats = $request_mats");
    while ($row = $res->fetch_assoc()) {
        if ($row['name_brochures'] && $row['brochure_quantity']) {
            $items[] = ['type' => 'Brochure', 'name' => $row['name_brochures'], 'qty' => intval($row['brochure_quantity'])];
        }
        if ($row['name_swag'] && $row['swag_quantity']) {
            $items[] = ['type' => 'Swag', 'name' => $row['name_swag'], 'qty' => intval($row['swag_quantity'])];
        }
        if ($row['name_material'] && $row['material_quantity']) {
            $items[] = ['type' => 'Marketing Material', 'name' => $row['name_material'], 'qty' => intval($row['material_quantity'])];
        }
    }
    $res->free();
    DISCONNECTIVITY($conn);
    header('Content-Type: application/json');
    echo json_encode($items);
}
