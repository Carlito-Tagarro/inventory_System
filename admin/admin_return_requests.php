<?php
include '../connection.php';
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../images/images__1_-removebg-preview.png"> 
    <title>Return Requests</title>
    <link rel="stylesheet" href="../CSS/admin_return.css">
</head>
<body>
    <nav>
        <a href="admin.php"><img src="../images/images__1_-removebg-preview.png" alt="Company Logo"></a>
        <div class="nav-container">
            <a href="admin.php">Dashboard</a>
            <a href="brochure_inventory.php">Brochure Inventory</a>
            <a href="marketing_inventory.php">Materials Inventory</a>
            <a href="swags_inventory.php">Swags Inventory</a>
        </div>
    </nav>
    <div class="container">
        <h2>Pending Material Return Requests</h2>
        <div style="overflow-x:auto;">
        <table>
            <caption style="caption-side:top; text-align:left; font-size:1.3rem; color:#333; margin-bottom:12px; font-weight:600;">
                Material Return Requests
            </caption>
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
            <?php if (count($requests) > 0): ?>
                <?php foreach ($requests as $req): ?>
                    <tr>
                        <td><?= htmlspecialchars($req['event_name']) ?></td>
                        <td><?= htmlspecialchars($req['username']) ?></td>
                        <td><?= $req['requested_at'] ?></td>
                        <td>
                            <ul>
                            <?php foreach ($req['items'] as $item): ?>
                                <li>
                                    <span style="font-weight:600;"><?= htmlspecialchars($item['type']) ?></span>: 
                                    <?= htmlspecialchars($item['name']) ?> 
                                    <span style="color:#1976d2;">(<?= $item['qty'] ?>)</span>
                                </li>
                            <?php endforeach; ?>
                            </ul>
                        </td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">
                                <button type="submit" name="action" value="approve" class="action-btn approve-btn">Approve</button>
                                <button type="submit" name="action" value="decline" class="action-btn decline-btn">Decline</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td class="no-data" colspan="5">No pending requests found</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</body>
</html>
