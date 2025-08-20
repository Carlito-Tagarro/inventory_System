<?php
session_start();
include 'connection.php'; 
// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMAILMAN/src/Exception.php';
require 'PHPMAILMAN/src/PHPMailer.php';
require 'PHPMAILMAN/src/SMTP.php';
$registration_success = false; 
$verification_sent = false;
$verification_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // If verification code is submitted
    if (isset($_POST['verification_code'])) {
        $entered_code = trim($_POST['verification_code']);
        if (isset($_SESSION['pending_user'])) {
            $pending = $_SESSION['pending_user'];
            $connection = CONNECTIVITY();
            $stmt = $connection->prepare("SELECT verification_code FROM users WHERE username = ? AND email = ?");
            $stmt->bind_param("ss", $pending['username'], $pending['email']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                if ($entered_code == $row['verification_code']) {
                    // Mark user as verified (add a 'verified' column in users table, default 0)
                    $update = $connection->prepare("UPDATE users SET verified = 1 WHERE username = ? AND email = ?");
                    $update->bind_param("ss", $pending['username'], $pending['email']);
                    $update->execute();
                    $update->close();
                    $registration_success = true;
                    unset($_SESSION['pending_user']);
                    // Do NOT change Account_status here; keep as 'Deactivated'
                } else {
                    $verification_error = "Incorrect verification code. Please try again.";
                }
            } else {
                $verification_error = "User not found.";
            }
            $stmt->close();
            DISCONNECTIVITY($connection);
        } else {
            $verification_error = "Session expired. Please register again.";
        }
    } else {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $email = trim($_POST['email']);
        $user_type = 'trainer'; 

        // Validate required fields
        if (empty($username) || empty($password) || empty($email)) {
            echo "All fields are required.";
            exit;
        }

        // Password length validation
        if (strlen($password) < 8) {
            echo "Password must be at least 8 characters long.";
            exit;
        }

        //Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);


        // Check if username or email already exists
        $connection = CONNECTIVITY();
        $stmt = $connection->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Generate verification code
        $verification_code = rand(100000, 999999);

        if ($result->num_rows > 0) {
            echo "Username or email already exists.";
        } else {
            // Insert user with verification code, verified=0, and Account_status='Deactivated'
            $stmt = $connection->prepare("INSERT INTO users (username,password, email, user_type, verification_code, verified, Account_status) VALUES (?, ?, ?, ?, ?, 0, 'Deactivated')");
            $stmt->bind_param("sssss", $username, $hashed_password, $email, $user_type, $verification_code);

            if ($stmt->execute()) {
                // Send verification code via email using PHPMailer
                $mail = new PHPMailer(true);
                try {
                    //Server settings
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com'; // Set your SMTP server
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'carlitotagarro27@gmail.com';  // Your SMTP username
                    $mail->Password   = 'lszvvhaevdddeaps'; // Your SMTP password

                    // Use STARTTLS and port 587 for Gmail
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    //Recipients
                    $mail->setFrom('carlitotagarro27@gmail.com', 'Inventory System');
                    $mail->addAddress($email, $username);

                    //Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Email Verification Code';
                    $mail->Body    = "Your verification code is: <b>$verification_code</b>";

                    $mail->send();
                    $verification_sent = true;
                    // Store pending user in session
                    $_SESSION['pending_user'] = [
                        'username' => $username,
                        'email' => $email
                    ];
                } catch (Exception $e) {
                    // Delete the user if email sending fails
                    $del_stmt = $connection->prepare("DELETE FROM users WHERE username = ? AND email = ?");
                    $del_stmt->bind_param("ss", $username, $email);
                    $del_stmt->execute();
                    $del_stmt->close();
                    echo "Verification email could not be sent. Registration failed. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                echo "Error: " . $stmt->error;
                header("Location: register.php");
            }
        }

        $stmt->close();
        DISCONNECTIVITY($connection);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="CSS/register.css">
    <link rel="icon" type="image/x-icon" href="images/images__1_-removebg-preview.png"> 
    <style>
        body {
            background: #f4f6f8;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .form_container {
            background: #fff;
            max-width: 400px;
            margin: 60px auto;
            padding: 32px 24px;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
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
            font-weight: 500;
        }
        input[type="text"], input[type="password"], input[type="email"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            background: #f7fafc;
            font-size: 16px;
            transition: border-color 0.2s;
             box-sizing: border-box;
        }
        input[type="text"]:focus, input[type="password"]:focus, input[type="email"]:focus {
            border-color: #3182ce;
            outline: none;
        }
        .button_submit {
            width: 100%;
            padding: 12px;
            background: #3182ce;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 17px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .button_submit:hover {
            background: #2563eb;
        }
        .links {
            text-align: center;
            margin-top: 18px;
        }
        .links a {
            color: #3182ce;
            text-decoration: none;
            font-size: 15px;
        }
        .notification-success {
            display: none;
            background: #c6f6d5;
            color: #22543d;
            border: 1px solid #38a169;
            border-radius: 6px;
            padding: 10px 16px;
            margin-bottom: 18px;
            text-align: center;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="form_container">
        <h2>Register</h2>
        <!-- Success notification area -->
        <div id="successNotification" class="notification-success"
            <?php if ($registration_success) echo 'style="display:block;"'; ?>>
            <?php if ($registration_success): ?>
                Registration successful! Your account is now verified.
                <script>
                    setTimeout(function() {
                        window.location.href = "login.php";
                    }, 3000);
                </script>
            <?php endif; ?>
        </div>
        <?php if ($verification_sent && ! $registration_success): ?>
            <form method="POST" action="">
                <div class="form_group">
                    <label for="verification_code">Enter Verification Code:</label>
                    <input type="text" id="verification_code" name="verification_code" placeholder="Code sent to your email" required>
                </div>
                <?php if ($verification_error): ?>
                    <div style="color:#e53e3e; font-size:14px; margin-bottom:8px;">
                        <?php echo $verification_error; ?>
                    </div>
                <?php endif; ?>
                <input type="submit" value="Verify" class="button_submit">
            </form>
        <?php elseif (! $registration_success): ?>
            <form method="POST" action="">
                <div class="form_group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="form_group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <div id="passwordValidationMsg" style="color:#e53e3e; font-size:14px; margin-top:4px;"></div>
                </div>
                <div class="form_group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <input type="submit" value="Register" class="button_submit">
            </form>
            <div class="links">
                <a href="login.php">Already have an account? Sign in</a>
            </div>
        <?php endif; ?>
    </div>
    <script>
        // Password validation popup
        var passwordInput = document.getElementById('password');
        var validationMsg = document.getElementById('passwordValidationMsg');
        passwordInput.addEventListener('input', function() {
            var len = passwordInput.value.length;
            if (len === 0) {
                validationMsg.textContent = '';
            } else if (len < 8) {
                validationMsg.textContent = " (minimum 8 characters required)";
            
            }
            if (len < 8) {
                validationMsg.style.color = "#e53e3e";
            } else {
                validationMsg.style.color = "#38a169";
            }

        
        });
    </script>
</body>
</html>