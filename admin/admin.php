<?php 
include '../connection.php';
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /..login.php"); 
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

// COUNT PENDING RETURN REQUESTS
$return_pending_count = 0;
$sql_return = "SELECT COUNT(*) as cnt FROM material_return_request WHERE status = 'Pending'";
$result_return = mysqli_query($connection, $sql_return);
if ($result_return && $row_return = mysqli_fetch_assoc($result_return)) {
    $return_pending_count = (int)$row_return['cnt'];
}

DISCONNECTIVITY($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../images/images__1_-removebg-preview.png">
    <link rel="stylesheet" href="../styles/admin.css">
    <title>Admin</title>
</head>
<body>
    <div class="container">
        <img class="logo" src="../images/images__1_-removebg-preview.png" alt="Logo">
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
            <a href="admin_return_requests.php">
                <button type="button">
                    Return Requests
                    <?php if ($return_pending_count > 0): ?>
                        <span class="notification-badge"><?php echo $return_pending_count; ?></span>
                    <?php endif; ?>
                </button>
            </a>
            <a href="manage_accounts.php">
                <button type="button">Manage Accounts</button>
            </a>
        </div>
        <a class="logout-link" href="../logout.php">Logout</a>
    </div>
</body>
</html>