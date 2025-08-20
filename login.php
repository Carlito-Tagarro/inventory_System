<?php
session_start(); 
include 'connection.php'; 

$error_message = "";

// ✅ Auto-fill username from cookie if available
$saved_username = isset($_COOKIE['remember_username']) ? $_COOKIE['remember_username'] : "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);

    $connection = CONNECTIVITY(); 

    $stmt = $connection->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $results = $stmt->get_result();

    if ($results->num_rows > 0) {
        $user_acc = $results->fetch_assoc();
        $stored_hash = trim($user_acc['password']);
        
        if (!empty($stored_hash) && password_verify($password, $stored_hash)) {
            // Check if account is deactivated
            if ($user_acc['Account_status'] === 'Deactivated') {
                $error_message = "Your account is deactivated. Please contact support or admin to activate your account.";
            } else {
                $_SESSION['user_id'] = $user_acc['user_id'];
                $_SESSION['username'] = $user_acc['username'];
                $_SESSION['user_type'] = $user_acc['user_type'];

                // ✅ Save username in cookie if Remember Me is checked
                if ($remember) {
                    setcookie("remember_username", $username, time() + (86400 * 30), "/"); // 30 days
                } else {
                    setcookie("remember_username", "", time() - 3600, "/"); // Clear cookie
                }

                if($_SESSION['user_type'] == 'admin') {
                    header("Location: admin/admin.php");
                } else {
                    header("Location: userpage.php");
                }
                exit; 
            }
        } else {
            $error_message = "❌ Incorrect password. Please try again.";
        }
    } else {
        $error_message = "⚠️ Account does not exist.";
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
  <link rel="icon" type="image/png" href="images/images__1_-removebg-preview.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body {
        background: linear-gradient(135deg, #a94442, #1e40af);
        font-family: 'Segoe UI', Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
        padding: 16px;
        box-sizing: border-box;
    }
    .form_container {
        background: #fff;
        max-width: 420px;
        width: 100%;
        padding: 32px 28px;
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        animation: fadeIn 0.6s ease-in-out;
    }
    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(-10px);}
        to {opacity: 1; transform: translateY(0);}
    }
    h2 {
        text-align: center;
        color: #a94442;
        margin-bottom: 24px;
        font-size: 1.8rem;
    }
    .error-message {
        background: #fee2e2;
        color: #b91c1c;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 16px;
        font-size: 14px;
    }
    .form_group {
        margin-bottom: 18px;
        position: relative;
    }
    label {
        display: block;
        margin-bottom: 6px;
        color: #4a5568;
        font-size: 14px;
        font-weight: 500;
    }
    .form_group i {
        position: absolute;
        top: 38px;
        left: 12px;
        color: #94a3b8;
    }
    input[type="text"], input[type="password"] {
        width: 100%;
        padding: 10px 36px 10px 36px;
        border: 1px solid #cbd5e0;
        border-radius: 8px;
        font-size: 15px;
        background: #f9fafb;
        transition: border-color 0.2s;
        box-sizing: border-box;
    }
    input[type="text"]:focus, input[type="password"]:focus {
        border-color: #a94442;
        outline: none;
    }
    .remember_me {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 18px;
        font-size: 14px;
        color: #4a5568;
    }
    .button_submit {
        width: 100%;
        padding: 12px 0;
        background: #a94442;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.2s;
    }
    .button_submit:hover {
        background: #922b21;
    }
    .links {
        margin-top: 18px;
        text-align: center;
    }
    .links a {
        color: #a94442;
        text-decoration: none;
        font-size: 14px;
    }
    .links a:hover {
        text-decoration: underline;
    }

    @media (max-width: 480px) {
        .form_container {
            padding: 24px 20px;
            border-radius: 12px;
        }
        h2 {
            font-size: 1.5rem;
        }
        input[type="text"], input[type="password"] {
            font-size: 14px;
            padding: 9px 36px 9px 36px;
        }
        .button_submit {
            font-size: 15px;
            padding: 10px 0;
        }
    }

    @media (min-width: 768px) {
        .form_container {
            max-width: 500px;
        }
    }
  </style>
</head>
<body>
  <div class="form_container">
      <h2>Sign In</h2>
      <?php if (!empty($error_message)) { echo "<div class='error-message'>$error_message</div>"; } ?>
      <form method="POST" action="">
          <div class="form_group">
              <label for="username">Username</label>
              <i class="fa fa-user"></i>
              <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($saved_username); ?>" placeholder="Enter your username" required>
          </div>
          <div class="form_group">
              <label for="password">Password</label>
              <i class="fa fa-lock"></i>
              <input type="password" id="password" name="password" placeholder="Enter your password" required>
          </div>
          <div class="remember_me">
              <input type="checkbox" id="remember" name="remember" <?php echo !empty($saved_username) ? "checked" : ""; ?>>
              <label for="remember">Remember Me</label>
          </div>
          <input type="submit" value="Sign In" class="button_submit">
      </form>
      <div class="links">
          <a href="register.php">Don’t have an account? Register here</a>
      </div>
  </div>
</body>
</html>
  
</body>
</html>
