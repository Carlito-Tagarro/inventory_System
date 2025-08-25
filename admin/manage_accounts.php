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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/manage_account.css">
</head>
<body>
    <div class="container">
        <h2>Manage User Accounts</h2>
        <div class="table-responsive">
            <table>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <!-- <th>User Type</th>
                    <th>Verified</th> -->
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <!-- <td><?php echo htmlspecialchars($row['user_type']); ?></td>
                        <td><?php echo $row['verified'] ? 'Yes' : 'No'; ?></td> -->
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
        </div>
        <div class="user-card-list">
            <?php
            // Re-run query for card view
            $connection = CONNECTIVITY();
            $result2 = $connection->query("SELECT user_id, username, email, user_type, verified, Account_status FROM users WHERE user_type != 'admin'");
            while ($row = $result2->fetch_assoc()):
            ?>
            <div class="user-card">
                <div class="card-row"><span class="card-label">Username:</span> <span class="card-value"><?php echo htmlspecialchars($row['username']); ?></span></div>
                <div class="card-row"><span class="card-label">Email:</span> <span class="card-value"><?php echo htmlspecialchars($row['email']); ?></span></div>
                <!-- <div class="card-row"><span class="card-label">Type:</span> <span class="card-value"><?php echo htmlspecialchars($row['user_type']); ?></span></div>
                <div class="card-row"><span class="card-label">Verified:</span> <span class="card-value"><?php echo $row['verified'] ? 'Yes' : 'No'; ?></span></div> -->
                <div class="card-row"><span class="card-label">Status:</span> <span class="card-value"><?php echo htmlspecialchars($row['Account_status']); ?></span></div>
                <div class="card-actions">
                    <?php if ($row['Account_status'] === 'Deactivated'): ?>
                        <form method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                            <input type="hidden" name="action" value="activate">
                            <button type="submit" class="btn activate">Activate</button>
                        </form>
                    <?php else: ?>
                        <form method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                            <input type="hidden" name="action" value="deactivate">
                            <button type="submit" class="btn deactivate">Deactivate</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; DISCONNECTIVITY($connection); ?>
        </div>
        <a href="admin.php" class="back-link">&#8592; Back to Admin Home</a>
    </div>
</body>
</html>
