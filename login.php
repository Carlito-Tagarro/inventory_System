<?php
session_start(); 
include 'connection.php'; 

$error_message = "";

//Auto-fill username from cookie if available
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
  <link rel="stylesheet" href="CSS/login.css">
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
          <br>
          <a href="forgot_pass.php">Forgot your password? Reset it here</a>
      </div>
  </div>
</body>
</html>
  