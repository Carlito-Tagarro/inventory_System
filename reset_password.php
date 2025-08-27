<?php
session_start();
include 'connection.php';

$message = "";
$show_form = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $connection = CONNECTIVITY();

    $stmt = $connection->prepare("SELECT user_id, token_expiry FROM users WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $results = $stmt->get_result();

    if ($results->num_rows > 0) {
        $user = $results->fetch_assoc();
        if (strtotime($user['token_expiry']) > time()) {
            $show_form = true;
            $user_id = $user['user_id'];
        } else {
            $message = "This reset link has expired. Please request a new one.";
        }
    } else {
        $message = "Invalid reset link.";
    }
    $stmt->close();

    // Handle password reset form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && $show_form) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $message = "Passwords do not match.";
        } elseif (strlen($new_password) < 6) {
            $message = "Password must be at least 6 characters.";
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $connection->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE user_id = ?");
            $update->bind_param("si", $hashed, $user_id);
            $update->execute();
            $update->close();
            $message = "Your password has been reset. You can now <a href='login.php'>login</a>.";
            $show_form = false;
        }
    }
    DISCONNECTIVITY($connection);
} else {
    $message = "No reset token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link rel="icon" type="image/png" href="images/images__1_-removebg-preview.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="CSS/register.css">
</head>
<body>
    <div class="form_container">
        <h2>Reset Password</h2>
        <?php if (!empty($message)) { echo "<div class='error-message'>$message</div>"; } ?>
        <?php if ($show_form): ?>
        <form method="POST" action="">
            <div class="form_group">
                <label for="new_password">New Password</label>
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                <span class="toggle-eye" data-target="new_password" style="display:none;"><i class="fa-solid fa-eye"></i></span>
                <div id="newPasswordMsg" style="color:#e53e3e; font-size:14px; margin-top:4px;"></div>
            </div>
            <div class="form_group">
                <label for="confirm_password">Confirm Password</label>
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                <span class="toggle-eye" data-target="confirm_password" style="display:none;"><i class="fa-solid fa-eye"></i></span>
                <div id="confirmPasswordMsg" style="color:#e53e3e; font-size:14px; margin-top:4px;"></div>
            </div>
            <input type="submit" value="Reset Password" class="button_submit">
        </form>
        <?php endif; ?>
        <div class="links">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
    <script>
    // Show/Hide password toggles
    document.querySelectorAll('.toggle-eye').forEach(function(tog){
        tog.addEventListener('click', function(){
            const id = this.getAttribute('data-target');
            const input = document.getElementById(id);
            if (!input) return;
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Hide/show eye icon based on input value
    function toggleEyeVisibility(inputId, eyeSelector) {
        var input = document.getElementById(inputId);
        var eye = document.querySelector(eyeSelector);
        if (!input || !eye) return;
        eye.style.display = input.value.length > 0 ? 'inline' : 'none';
    }

    var newPasswordInput = document.getElementById('new_password');
    var newPasswordEye = document.querySelector('span.toggle-eye[data-target="new_password"]');
    var confirmInput = document.getElementById('confirm_password');
    var confirmEye = document.querySelector('span.toggle-eye[data-target="confirm_password"]');
    var newPasswordMsg = document.getElementById('newPasswordMsg');
    var confirmMsg = document.getElementById('confirmPasswordMsg');

    if (newPasswordInput && newPasswordEye) {
        newPasswordInput.addEventListener('input', function() {
            toggleEyeVisibility('new_password', 'span.toggle-eye[data-target="new_password"]');
            var len = newPasswordInput.value.length;
            if (len === 0) {
                newPasswordMsg.textContent = '';
            } else if (len < 6) {
                newPasswordMsg.textContent = " (minimum 8 characters required)";
            } else {
                newPasswordMsg.textContent = "";
            }
            newPasswordMsg.style.color = (len < 8) ? "#e53e3e" : "#38a169";
            checkPasswordMatch();
        });
        toggleEyeVisibility('new_password', 'span.toggle-eye[data-target="new_password"]');
    }

    if (confirmInput && confirmEye) {
        confirmInput.addEventListener('input', function() {
            toggleEyeVisibility('confirm_password', 'span.toggle-eye[data-target="confirm_password"]');
            checkPasswordMatch();
        });
        toggleEyeVisibility('confirm_password', 'span.toggle-eye[data-target="confirm_password"]');
    }

    function checkPasswordMatch() {
        if (!confirmInput || !newPasswordInput || !confirmMsg) return;
        if (confirmInput.value.length === 0) {
            confirmMsg.textContent = '';
        } else if (newPasswordInput.value !== confirmInput.value) {
            confirmMsg.textContent = "Passwords do not match.";
            confirmMsg.style.color = "#e53e3e";
        } else {
            confirmMsg.textContent = "Passwords match.";
            confirmMsg.style.color = "#38a169";
        }
    }
    if (newPasswordInput && confirmInput) {
        newPasswordInput.addEventListener('input', checkPasswordMatch);
        confirmInput.addEventListener('input', checkPasswordMatch);
    }
    </script>
</body>
</html>
