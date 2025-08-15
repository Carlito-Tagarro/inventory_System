<?php 
include 'connection.php';
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php"); 
    exit;
}

$connection = CONNECTIVITY();
//COUNT PENDING REQUEST
$pending_count = 0;
$sql = "SELECT COUNT(*) as cnt FROM event_form WHERE request_status = 'Pending'";
$result = mysqli_query($connection, $sql);
if ($result && $row = mysqli_fetch_assoc($result)) {
    $pending_count = (int)$row['cnt'];
}

DISCONNECTIVITY($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/images__1_-removebg-preview.png"> 
    <title>Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px;
            margin: 60px auto;
            background: #fff;
            padding: 32px 24px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            text-align: center;
        }
        h1 {
            color: #333;
            margin-bottom: 18px;
        }
        p {
            color: #666;
            margin-bottom: 32px;
        }
        .btn-group {
            margin-bottom: 24px;
        }
        .btn-group a {
            text-decoration: none;
            margin: 0 8px;
        }
        button {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px 22px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-bottom: 8px;
            transition: background 0.2s;
        }
        button:hover {
            background: #0056b3;
        }
        .logout-link {
            display: inline-block;
            margin-top: 18px;
            color: #dc3545;
            text-decoration: none;
            font-weight: bold;
        }
        .logout-link:hover {
            text-decoration: underline;
        }
        .notification-badge {
            display: inline-block;
            min-width: 20px;
            padding: 2px 7px;
            font-size: 13px;
            font-weight: bold;
            color: #fff;
            background: #dc3545;
            border-radius: 12px;
            vertical-align: top;
            margin-left: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the Admin Page</h1>
        <p>This is a protected area for administrators.</p>
        <div class="btn-group">
            <a href="brochure_inventory.php">
                <button type="button">Inventory Management</button>
            </a>
            <a href="admin_request.php">
                <button type="button">
                    View Requests
                    <?php if ($pending_count > 0): ?>
                        <span class="notification-badge"><?php echo $pending_count; ?></span>
                    <?php endif; ?>
                </button>
            </a>
        </div>
        <a class="logout-link" href="logout.php">Logout</a>
    </div>
</body>
</html>