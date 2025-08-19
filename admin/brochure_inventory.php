<?php

    include '../connection.php';
    session_start();

    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php"); 
    exit;
}

    $connection = CONNECTIVITY();

    // Handle add brochure form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_brochure'])) {
        $brochure_name = $_POST['brochure_name'];
        $quantity = intval($_POST['quantity']);

        // Remove total_brochure from insert, only insert name and quantity
        $stmt = $connection->prepare("INSERT INTO brochures (brochure_name, quantity) VALUES (?, ?)");
        $stmt->bind_param("si", $brochure_name, $quantity);
        $stmt->execute();
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Handle edit quantity form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_brochure'])) {
        $brochure_id = intval($_POST['brochure_id']);
        $new_quantity = intval($_POST['new_quantity']);
        $stmt = $connection->prepare("UPDATE brochures SET quantity = ? WHERE brochure_id = ?");
        $stmt->bind_param("ii", $new_quantity, $brochure_id);
        $stmt->execute();
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Handle delete brochure form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_brochure'])) {
        $brochure_id = intval($_POST['brochure_id']);
        $stmt = $connection->prepare("DELETE FROM brochures WHERE brochure_id = ?");
        $stmt->bind_param("i", $brochure_id);
        $stmt->execute();
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    $sql = "SELECT brochure_id, brochure_name, quantity FROM brochures";
    $result = mysqli_query($connection, $sql);

    // Calculate total brochure quantity
    $total_brochure = 0;
    if ($result && mysqli_num_rows($result) > 0) {
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
            $total_brochure += intval($row['quantity']);
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
    <title>Brochure Inventory</title>
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
        /* Add below existing styles or merge with previous .action-btn styles if present */
        .action-btn {
            color: #fff;
            border: none;
            padding: 4px 8px; /* smaller padding */
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 400; /* changed from 600 to 400 */
            font-size: 0.95rem;
            display: inline-block;
            min-width: 48px; /* smaller min width */
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
    <!-- Navigation Bar -->
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
        <!-- Add Brochure Form -->
        <div class="form-card">
            <h3>Add Brochure</h3>
            <form method="post" autocomplete="off">
                <label for="brochure_name">Brochure Name</label>
                <input type="text" id="brochure_name" name="brochure_name" required>

                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" min="1" required>

                <button type="submit" name="add_brochure">Add Brochure</button>
            </form>
        </div>
        <!-- Brochure Table -->
        <div style="flex:1;">
            <table>
                <caption style="caption-side:top; text-align:left; font-size:1.5rem; color:#333; margin-bottom:12px; font-weight:600;">
                    Brochures Inventory
                </caption>
                <thead>
                    <tr>
                        <th>Brochure ID</th>
                        <th>Brochure Name</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // For edit form state
                    $edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : null;

                    if (!empty($rows)) {
                        foreach ($rows as $row) {
                            echo "<tr>";
                            echo "<td>{$row['brochure_id']}</td>";
                            echo "<td>{$row['brochure_name']}</td>";
                            echo "<td>";
                            if ($edit_id === intval($row['brochure_id'])) {
                                // Show edit form for this row
                                echo "<form method='post' style='display:inline;'>";
                                echo "<input type='hidden' name='brochure_id' value='{$row['brochure_id']}'>";
                                echo "<input type='number' name='new_quantity' value='{$row['quantity']}' min='1' required style='width:70px;'>";
                                echo "<button type='submit' name='edit_brochure' style='margin-left:6px;'>Save</button>";
                                echo "</form>";
                            } else {
                                echo "{$row['quantity']}";
                            }
                            echo "</td>";
                            echo "<td style='display:flex;gap:8px;align-items:center;'>";
                            if ($edit_id === intval($row['brochure_id'])) {
                                // Cancel button styled like Edit but gray
                                echo "<a href='{$_SERVER['PHP_SELF']}' class='action-btn cancel-btn'>Cancel</a>";
                            } else {
                                // Edit button styled like Delete but orange
                                echo "<a href='{$_SERVER['PHP_SELF']}?edit_id={$row['brochure_id']}' class='action-btn edit-btn'>Edit</a>";
                            }
                            // Add delete button next to Edit/Cancel
                            echo "<form method='post' style='display:inline;' onsubmit=\"return confirm('Are you sure you want to delete this brochure?');\">";
                            echo "<input type='hidden' name='brochure_id' value='{$row['brochure_id']}'>";
                            echo "<button type='submit' name='delete_brochure' class='action-btn delete-btn'>Delete</button>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        // Add summary row for total brochure quantity
                        echo "<tr style='font-weight:bold; background:#e3f2fd;'>";
                        echo "<td colspan='2' style='text-align:right;'>Total Brochure Quantity:</td>";
                        echo "<td>{$total_brochure}</td>";
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