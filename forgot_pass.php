<?php
session_start();
include 'connection.php';
// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMAILMAN/src/Exception.php';
require 'PHPMAILMAN/src/PHPMailer.php';
require 'PHPMAILMAN/src/SMTP.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $connection = CONNECTIVITY();

    $stmt = $connection->prepare("SELECT user_id, username FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $results = $stmt->get_result();

    if ($results->num_rows > 0) {
        $user = $results->fetch_assoc();
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", time() + 3600); // 1 hour expiry

        // Save token and expiry in DB (make sure columns exist)
        $update = $connection->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE user_id = ?");
        $update->bind_param("ssi", $token, $expiry, $user['user_id']);
        $update->execute();

        // Send email using PHPMailer
        $reset_link = "http://".$_SERVER['HTTP_HOST']."/inventory_System-1/reset_password.php?token=".$token;
        $subject = "Password Reset Request";
        $body = "Hi ".$user['username'].",<br><br>Click the link below to reset your password:<br><a href='".$reset_link."'>Reset Password</a><br><br>This link will expire in 1 hour.";

        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Set your SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'carlitotagarro27@gmail.com';      // SMTP username
            $mail->Password   = 'lszvvhaevdddeaps';        // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('no-reply@audentest.com', 'Inventory System');
            $mail->addAddress($email, $user['username']);

            //Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            $message = "A password reset link has been sent to your email.";
        } catch (Exception $e) {
            $message = "Failed to send email. Mailer Error: {$mail->ErrorInfo}";
        }
        $update->close();
    } else {
        $message = "No account found with that email address.";
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
  <title>Forgot Password</title>
  <link rel="icon" type="image/png" href="images/images__1_-removebg-preview.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="CSS/register.css">
</head>
<body>
    <div class="form_container">
        <h2>Forgot Password</h2>
        <?php if (!empty($message)) { echo "<div class='error-message'>$message</div>"; } ?>
        <form method="POST" action="">
            <div class="form_group">
                <label for="email">Enter your email address</label>
                <i class="fa-solid fa-envelope"></i>
                <input type="email" id="email" name="email" placeholder="Email" required>
            </div>
            <input type="submit" value="Send Reset Link" class="button_submit">
        </form>
        <div class="links">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>
</html>
