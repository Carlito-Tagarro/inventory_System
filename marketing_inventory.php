<?php
include 'connection.php';
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php"); 
    exit;
}

$connection = CONNECTIVITY();

// Handle add swag form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_materials'])) {
    $material_name = $_POST['material_name'];
    $quantity = intval($_POST['quantity']);
    $stmt = $connection->prepare("INSERT INTO marketing_materials (material_name, quantity) VALUES (?, ?)");
    $stmt->bind_param("si", $material_name, $quantity);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle edit quantity form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_material'])) {
    $material_id = intval($_POST['material_id']);
    $new_quantity = intval($_POST['new_quantity']);
    $stmt = $connection->prepare("UPDATE marketing_materials SET quantity = ? WHERE material_id = ?");
    $stmt->bind_param("ii", $new_quantity, $material_id);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle delete swag form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_material'])) {
    $material_id = intval($_POST['material_id']);
    $stmt = $connection->prepare("DELETE FROM marketing_materials WHERE material_id = ?");
    $stmt->bind_param("i", $material_id);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$sql = "SELECT material_id, material_name, quantity FROM marketing_materials";
$result = mysqli_query($connection, $sql);

// Calculate total swag quantity
$total_material = 0;
if ($result && mysqli_num_rows($result) > 0) {
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
        $total_material += intval($row['quantity']);
    }
}
DISCONNECTIVITY($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/images__1_-removebg-preview.png"> 
    <title>Swags Inventory</title>
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
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
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
        .no-data {
            text-align: center;
            color: #888;
            font-style: italic;
        }
        .container-flex {
            display: flex;
            gap: 32px;
            align-items: flex-start;
            justify-content: center;
        }
        .form-card {
            background: #fff;
            padding: 28px 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            width: 340px;
            margin: 50px;
        }
        .form-card h3 {
            margin-top: 0;
            margin-bottom: 18px;
            color: #1976d2;
        }
        .form-card label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
        }
        .form-card input {
            width: 100%;
            padding: 8px 10px;
            margin-bottom: 14px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }
        .form-card button {
            width: 100%;
            background: #1976d2; 
            color: #fff;
            border: none;
            padding: 10px 0;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 8px;
        }
        .form-card button:hover {
            background: #1565c0; 
        }
        nav {
            /* background: #1565c0; */
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

        .action-btn {
            color: #fff;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 400;
            font-size: 0.95rem;
            display: inline-block;
            min-width: 48px;
            text-align: center;
        }
        .edit-btn {
            background: #ff9800;
        }
        .edit-btn:hover {
            background: #fb8c00;
        }
        .delete-btn {
            background: #d32f2f;
        }
        .delete-btn:hover {
            background: #b71c1c;
        }
        .cancel-btn {
            background: #bdbdbd;
        }
        .cancel-btn:hover {
            background: #9e9e9e;
        }
    </style>
</head>
<body>
    <nav>
        <a href="admin.php"><img src="images/images__1_-removebg-preview.png" alt="Company Logo"></a>
    <div class="nav-container">
        <a href="brochure_inventory.php">Brochure Inventory</a>
        <a href="marketing_inventory.php">Materials Inventory</a>
        <a href="swags_inventory.php">Swags Inventory</a>
        <a href="admin_request.php">Event Requests</a>
    </div>
    </nav>
    
    <div class="container-flex">
        <!-- Add Swag Form -->
        <div class="form-card">
            <h3>Add Materials</h3>
            <form method="post" autocomplete="off">
                <label for="material_name">Material Name</label>
                <input type="text" id="material_name" name="material_name" required>

                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" min="1" required>

                <button type="submit" name="add_materials">Add Material</button>
            </form>
        </div>
        <!-- Swags Table -->
        <div style="flex:1;">
            <table>
                <caption style="caption-side:top; text-align:left; font-size:1.5rem; color:#333; margin-bottom:12px; font-weight:600;">
                    Materials Inventory
                </caption>
                <thead>
                    <tr>
                        <th>Material ID</th>
                        <th>Material Name</th>
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
                            echo "<td>{$row['material_id']}</td>";
                            echo "<td>{$row['material_name']}</td>";
                            echo "<td>";
                            if ($edit_id === intval($row['material_id'])) {
                                echo "<form method='post' style='display:inline;'>";
                                echo "<input type='hidden' name='material_id' value='{$row['material_id']}'>";
                                echo "<input type='number' name='new_quantity' value='{$row['quantity']}' min='1' required style='width:70px;'>";
                                echo "<button type='submit' name='edit_material' style='margin-left:6px;'>Save</button>";
                                echo "</form>";
                            } else {
                                echo "{$row['quantity']}";
                            }
                            echo "</td>";
                            echo "<td style='display:flex;gap:8px;align-items:center;'>";
                            if ($edit_id === intval($row['material_id'])) {
                                echo "<a href='{$_SERVER['PHP_SELF']}' class='action-btn cancel-btn'>Cancel</a>";
                            } else {
                                echo "<a href='{$_SERVER['PHP_SELF']}?edit_id={$row['material_id']}' class='action-btn edit-btn'>Edit</a>";
                            }
                            echo "<form method='post' style='display:inline;' onsubmit=\"return confirm('Are you sure you want to delete this material?');\">";
                            echo "<input type='hidden' name='material_id' value='{$row['material_id']}'>";
                            echo "<button type='submit' name='delete_material' class='action-btn delete-btn'>Delete</button>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        // Add summary row for total swag quantity
                        echo "<tr style='font-weight:bold; background:#e3f2fd;'>";
                        echo "<td colspan='2' style='text-align:right;'>Total Material Quantity:</td>";
                        echo "<td>{$total_material}</td>";
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