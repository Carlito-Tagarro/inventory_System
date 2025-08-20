<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$connection = CONNECTIVITY();

// Handle activation/deactivation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['action'])) {
    $user_id = intval($_POST['user_id']);
    $action = $_POST['action'] === 'activate' ? 'Activated' : 'Deactivated';
    $stmt = $connection->prepare("UPDATE users SET Account_status = ? WHERE user_id = ?");
    $stmt->bind_param("si", $action, $user_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all users except admin
$result = $connection->query("SELECT user_id, username, email, user_type, verified, Account_status FROM users WHERE user_type != 'admin'");

DISCONNECTIVITY($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Accounts</title>
    <link rel="icon" type="image/x-icon" href="../images/images__1_-removebg-preview.png">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 800px; margin: 40px auto; background: #fff; padding: 32px 24px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);}
        h2 { text-align: center; color: #333; margin-bottom: 24px;}
        table { width: 100%; border-collapse: collapse; margin-bottom: 18px;}
        th, td { padding: 10px 8px; border-bottom: 1px solid #e2e8f0; text-align: center;}
        th { background: #3182ce; color: #fff;}
        tr:nth-child(even) { background: #f7fafc;}
        .btn { padding: 6px 16px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;}
        .activate { background: #38a169; color: #fff;}
        .deactivate { background: #e53e3e; color: #fff;}
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage User Accounts</h2>
        <table>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>User Type</th>
                <th>Verified</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_type']); ?></td>
                    <td><?php echo $row['verified'] ? 'Yes' : 'No'; ?></td>
                    <td><?php echo htmlspecialchars($row['Account_status']); ?></td>
                    <td>
                        <?php if ($row['Account_status'] === 'Deactivated'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                <input type="hidden" name="action" value="activate">
                                <button type="submit" class="btn activate">Activate</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                <input type="hidden" name="action" value="deactivate">
                                <button type="submit" class="btn deactivate">Deactivate</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <a href="admin.php" style="color:#3182ce; text-decoration:none;">&#8592; Back to Admin Home</a>
    </div>
</body>
</html>
