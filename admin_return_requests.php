<?php
include 'connection.php';
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit;
}
$conn = CONNECTIVITY();

// Handle approve/decline
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['action'])) {
    $request_id = intval($_POST['request_id']);
    $action = $_POST['action'];
    // Fetch items
    $res = $conn->query("SELECT items_json FROM material_return_request WHERE request_id = $request_id");
    $row = $res->fetch_assoc();
    $items = json_decode($row['items_json'], true);
    if ($action === 'approve') {
        // Update inventory
        foreach ($items as $item) {
            $qty = intval($item['qty']);
            $name = $conn->real_escape_string($item['name']);
            if ($item['type'] === 'Brochure') {
                $conn->query("UPDATE brochures SET quantity = quantity + $qty WHERE brochure_name = '$name'");
            } elseif ($item['type'] === 'Swag') {
                $conn->query("UPDATE swags SET quantity = quantity + $qty WHERE swags_name = '$name'");
            } elseif ($item['type'] === 'Marketing Material') {
                $conn->query("UPDATE marketing_materials SET quantity = quantity + $qty WHERE material_name = '$name'");
            }
        }
        $conn->query("UPDATE material_return_request SET status = 'Approved', reviewed_at = NOW() WHERE request_id = $request_id");
    } else {
        $conn->query("UPDATE material_return_request SET status = 'Declined', reviewed_at = NOW() WHERE request_id = $request_id");
    }
    header("Location: admin_return_requests.php");
    exit;
}

// List pending requests
$requests = [];
$res = $conn->query("SELECT r.request_id, r.event_id, r.user_id, r.items_json, r.status, r.requested_at, u.username, eh.event_name
    FROM material_return_request r
    JOIN users u ON r.user_id = u.user_id
    JOIN event_form_history eh ON r.event_id = eh.event_form_id
    WHERE r.status = 'Pending'
    ORDER BY r.requested_at ASC");
while ($row = $res->fetch_assoc()) {
    $row['items'] = json_decode($row['items_json'], true);
    $requests[] = $row;
}
DISCONNECTIVITY($conn);
?>
<html>
<head>
    <title>Return Requests - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; }
        .container { max-width: 900px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px #ccc; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { border: 1px solid #e3e3e3; padding: 8px; }
        th { background: #f0f8ff; color: #007bff; }
        button { background: #007bff; color: #fff; border: none; border-radius: 4px; padding: 6px 16px; font-size: 14px; cursor: pointer; margin-right: 8px; }
        button.decline { background: #dc3545; }
    </style>
</head>
<body>
<div class="container">
    <h2>Pending Material Return Requests</h2>
    <table>
        <thead>
            <tr>
                <th>Event</th>
                <th>Trainer</th>
                <th>Requested At</th>
                <th>Items</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($requests as $req): ?>
            <tr>
                <td><?= htmlspecialchars($req['event_name']) ?></td>
                <td><?= htmlspecialchars($req['username']) ?></td>
                <td><?= $req['requested_at'] ?></td>
                <td>
                    <ul>
                    <?php foreach ($req['items'] as $item): ?>
                        <li><?= htmlspecialchars($item['type']) ?>: <?= htmlspecialchars($item['name']) ?> (<?= $item['qty'] ?>)</li>
                    <?php endforeach; ?>
                    </ul>
                </td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">
                        <button type="submit" name="action" value="approve">Approve</button>
                        <button type="submit" name="action" value="decline" class="decline">Decline</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
