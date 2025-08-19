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
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .container {
            max-width: 1100px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px #ccc;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            margin-top: 18px;
        }
        th, td {
            padding: 12px 18px;
            text-align: left;
        }
        th {
            background: #1976d2;
            color: #fff;
            font-weight: 600;
            border-bottom: 2px solid #1565c0;
        }
        tr:nth-child(even) {
            background: #f2f7fb;
        }
        tr:hover {
            background: #e3f2fd;
        }
        td {
            border-bottom: 1px solid #e0e0e0;
        }
        .action-btn {
            color: #fff;
            border: none;
            padding: 4px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 400;
            font-size: 0.95rem;
            display: inline-block;
            min-width: 60px;
            text-align: center;
            margin-right: 8px;
        }
        .approve-btn {
            background: #1976d2;
        }
        .approve-btn:hover {
            background: #1565c0;
        }
        .decline-btn {
            background: #d32f2f;
        }
        .decline-btn:hover {
            background: #b71c1c;
        }
        nav {
            padding: 16px 0;
            margin-bottom: 32px;
            display: grid;
            grid-template-columns: auto 1fr;
            align-items: center;
            gap: 20px;
        }
        .nav-container {
            display: flex;
            justify-content: center;
            gap: 32px;
            width: 100%;
        }
        nav img {
            height: 50px;
            margin-left: 20px;
        }
        nav a {
            color: #333;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
        }
        ul {
            margin: 0;
            padding-left: 18px;
        }
        .no-data {
            text-align: center;
            color: #888;
            font-style: italic;
        }
    </style>
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
</body>
</html>
