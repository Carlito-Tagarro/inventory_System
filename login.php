<?php
session_start(); 
include 'connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $connection = CONNECTIVITY(); 

    $stmt = $connection->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $results = $stmt->get_result();

    if ($results->num_rows > 0) {
        $user_acc = $results->fetch_assoc();

     
        $stored_hash = trim($user_acc['password']);
        if (!empty($stored_hash) && password_verify($password, $stored_hash)) {
            $_SESSION['user_id'] = $user_acc['user_id'];
            $_SESSION['username'] = $user_acc['username'];
            $_SESSION['user_type'] = $user_acc['user_type'];

            if($_SESSION['user_type'] == 'admin') {
                header("Location: admin/admin.php");
            } else {
                header("Location: userpage.php");
            }
            exit; 
        } else {
            echo "<script>alert('Incorrect password. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Account does not exist.');</script>";
    }

    $stmt->close();
    DISCONNECTIVITY($connection);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="icon" type="image/x-icon" href="images/images__1_-removebg-preview.png">
    <link rel="stylesheet" href="CSS/sign_in.css">
    <style>
        body {
            background: #f4f6f8;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .form_container {
            background: #fff;
            max-width: 400px;
            margin: 60px auto;
            padding: 32px 28px;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);

        }
        h2 {
            text-align: center;
            color: #2d3748;
            margin-bottom: 24px;
        }
        .form_group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            color: #4a5568;
            font-size: 15px;
            
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            font-size: 15px;
            background: #f7fafc;
            transition: border-color 0.2s;
             box-sizing: border-box;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #3182ce;
            outline: none;
        }
        .button_submit {
            width: 100%;
            padding: 10px 0;
            background: #3182ce;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .button_submit:hover {
            background: #2563eb;
        }
        .links {
            margin-top: 18px;
            text-align: center;
        }
        .links a {
            color: #3182ce;
            text-decoration: none;
            font-size: 14px;
        }
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form_container">
        <h2>Sign In to Your Account</h2>
        <form method="POST" action="">
            <div class="form_group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div class="form_group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <input type="submit" value="Sign In" class="button_submit">
        </form>
        <div class="links">
            <a href="register.php">Don't have an account? Register here</a>
            <!-- <a href="forgot_password.php">Forgot your password? Reset it here</a> -->
        </div>
    </div>
</body>
</html>