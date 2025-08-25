<?php
include '../connection.php';
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php"); 
    exit;
}

$connection = CONNECTIVITY();

// Handle add swag form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_swag'])) {
    $swags_name = $_POST['swags_name'];
    $quantity = intval($_POST['quantity']);
    $stmt = $connection->prepare("INSERT INTO swags (swags_name, quantity) VALUES (?, ?)");
    $stmt->bind_param("si", $swags_name, $quantity);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle edit quantity form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_swag'])) {
    $swag_id = intval($_POST['swag_id']);
    $new_quantity = intval($_POST['new_quantity']);
    $stmt = $connection->prepare("UPDATE swags SET quantity = ? WHERE swag_id = ?");
    $stmt->bind_param("ii", $new_quantity, $swag_id);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle delete swag form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_swag'])) {
    $swag_id = intval($_POST['swag_id']);
    $stmt = $connection->prepare("DELETE FROM swags WHERE swag_id = ?");
    $stmt->bind_param("i", $swag_id);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$sql = "SELECT swag_id, swags_name, quantity FROM swags";
$result = mysqli_query($connection, $sql);

// Calculate total swag quantity
$total_swag = 0;
if ($result && mysqli_num_rows($result) > 0) {
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
        $total_swag += intval($row['quantity']);
    }
}
DISCONNECTIVITY($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../images/images__1_-removebg-preview.png"> 
    <link rel="stylesheet" href="../CSS/inventory.css">
    <title>Swags Inventory</title>
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
    <div class="container-flex">
        <!-- Add Swag Form -->
        <div class="form-card">
            <h3>Add Swag</h3>
            <form method="post" autocomplete="off">
                <label for="swags_name">Swag Name</label>
                <input type="text" id="swags_name" name="swags_name" required>

                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" min="1" required>

                <button type="submit" name="add_swag">Add Swag</button>
            </form>
        </div>
        <!-- Swags Table -->
        <div style="flex:1;">
            <table>
                <caption style="caption-side:top; text-align:left; font-size:1.5rem; color:#333; margin-bottom:12px; font-weight:600;">
                    Swags Inventory
                </caption>
                <thead>
                    <tr>
                        <th>Swag ID</th>
                        <th>Swag Name</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : null;

                    if (!empty($rows)) {
                        foreach ($rows as $row) {
                            echo "<tr>";
                            echo "<td>{$row['swag_id']}</td>";
                            echo "<td>{$row['swags_name']}</td>";
                            echo "<td>";
                            if ($edit_id === intval($row['swag_id'])) {
                                echo "<form method='post' style='display:inline;'>";
                                echo "<input type='hidden' name='swag_id' value='{$row['swag_id']}'>";
                                echo "<input type='number' name='new_quantity' value='{$row['quantity']}' min='1' required style='width:70px;'>";
                                echo "<button type='submit' name='edit_swag' style='margin-left:6px;'>Save</button>";
                                echo "</form>";
                            } else {
                                echo "{$row['quantity']}";
                            }
                            echo "</td>";
                            echo "<td style='display:flex;gap:8px;align-items:center;'>";
                            if ($edit_id === intval($row['swag_id'])) {
                                echo "<a href='{$_SERVER['PHP_SELF']}' class='action-btn cancel-btn'>Cancel</a>";
                            } else {
                                echo "<a href='{$_SERVER['PHP_SELF']}?edit_id={$row['swag_id']}' class='action-btn edit-btn'>Edit</a>";
                            }
                            echo "<form method='post' style='display:inline;' onsubmit=\"return confirm('Are you sure you want to delete this swag?');\">";
                            echo "<input type='hidden' name='swag_id' value='{$row['swag_id']}'>";
                            echo "<button type='submit' name='delete_swag' class='action-btn delete-btn'>Delete</button>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        // Add summary row for total swag quantity
                        echo "<tr style='font-weight:bold; background:#e3f2fd;'>";
                        echo "<td colspan='2' style='text-align:right;'>Total Swag Quantity:</td>";
                        echo "<td>{$total_swag}</td>";
                        echo "<td></td>";
                        echo "</tr>";
                    } else {
                        echo "<tr><td class='no-data' colspan='4'>No data found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>