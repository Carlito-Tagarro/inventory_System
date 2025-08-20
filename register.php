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

// Initialize variables for form values
$username_val = '';
$email_val = '';
$password_val = '';
$confirm_password_val = '';

$error_msg = ''; // Add this variable for error messages

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
        // Preserve entered values
        $username_val = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '';
        $email_val = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
        $password_val = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm_password_val = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);
        $email = trim($_POST['email']);
        $user_type = 'trainer'; 

        // Validate required fields
        if (empty($username) || empty($password) || empty($confirm_password) || empty($email)) {
            $error_msg = 'All fields are required.';
        } elseif (strlen($password) < 8) {
            $error_msg = 'Password must be at least 8 characters long.';
        } elseif ($password !== $confirm_password) {
            $error_msg = 'Passwords do not match.';
        } else {
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
                $error_msg = "Username or email already exists.";
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
                        $error_msg = "Verification email could not be sent. Registration failed. Mailer Error: {$mail->ErrorInfo}";
                    }
                } else {
                    $error_msg = "Error: " . $stmt->error;
                }
            }
            $stmt->close();
            DISCONNECTIVITY($connection);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Register</title>
    <link rel="icon" type="image/x-icon" href="images/images__1_-removebg-preview.png"> 
    <link rel="stylesheet" href="CSS/register.css"><!-- keep your external file if used -->
    <!-- Icons for input adornments -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>
        /* Match login page look & feel */
        *{box-sizing:border-box}
        body{
            background: linear-gradient(135deg,#a94442,#1e40af);
            font-family:'Segoe UI',Arial,sans-serif;
            min-height:100vh; margin:0;
            display:flex; align-items:center; justify-content:center;
            padding:16px;
        }
        .form_container{
            background:#fff; width:100%; max-width:420px;
            padding:32px 28px; border-radius:16px;
            box-shadow:0 8px 30px rgba(0,0,0,.12);
            animation:fadeIn .6s ease-in-out;
        }
        @keyframes fadeIn{from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:translateY(0)}}
        h2{margin:0 0 24px; text-align:center; color:#a94442}

        /* Messages (kept logic; just styling helpers if you use these classes) */
        .error-message{
            background:#fee2e2; color:#b91c1c; padding:10px; border-radius:6px; margin-bottom:16px; font-size:14px; text-align:center;
        }
        .success-message{
            background:#dcfce7; color:#166534; padding:10px; border-radius:6px; margin-bottom:16px; font-size:14px; text-align:center;
        }
        .notification-success{ /* your existing success div keeps working */
            display:none; background:#dcfce7; color:#166534;
            border:1px solid #22c55e; border-radius:6px; padding:10px 16px;
            margin-bottom:18px; text-align:center; font-size:16px;
        }

        .form_group{margin-bottom:18px; position:relative}
        label{display:block; margin-bottom:6px; color:#4a5568; font-size:14px; font-weight:500}

        /* Input with icons + toggles */
        .form_group .fa-solid{position:absolute; left:12px; top:38px; color:#94a3b8}
        .toggle-eye{
            position:absolute; right:30px; top:38px; cursor:pointer; user-select:none; color:#64748b; font-size:14px;
        }
        input[type="text"],input[type="password"],input[type="email"]{
            width:100%; padding:10px 36px 10px 36px;
            border:1px solid #cbd5e0; border-radius:8px;
            background:#f9fafb; font-size:15px;
            transition:border-color .2s, box-shadow .2s;
        }
        input[type="text"]:focus,input[type="password"]:focus,input[type="email"]:focus{
            border-color:#a94442; outline:none; box-shadow:0 0 0 2px rgba(169,68,66,.2);
        }

        .button_submit{
            width:100%; padding:12px 0; background:#a94442; color:#fff; border:none;
            border-radius:8px; font-size:16px; font-weight:600; cursor:pointer;
            transition:background .2s, transform .1s;
        }
        .button_submit:hover{background:#922b21}
        .button_submit:active{transform:scale(.98)}

        .links{margin-top:18px; text-align:center}
        .links a{color:#a94442; text-decoration:none; font-size:14px}
        .links a:hover{text-decoration:underline}

        /* Responsive tweaks */
        @media (max-width:480px){
            .form_container{padding:24px 20px; border-radius:12px}
            h2{font-size:1.5rem}
            .button_submit{padding:10px 0; font-size:15px}
        }
        /* Slightly wider on tablets, like login */
        @media (min-width:768px){
            .form_container{max-width:500px}
        }
    </style>
</head>
<body>
    <div class="form_container">
        <h2>Register</h2>

        <!-- Success notification area (unchanged logic) -->
        <div id="successNotification" class="notification-success"
            <?php if ($registration_success) echo 'style="display:block;"'; ?>>
            <?php if ($registration_success): ?>
                Registration successful! Your account is now verified.
                <script>
                    setTimeout(function() { window.location.href = "login.php"; }, 3000);
                </script>
            <?php endif; ?>
        </div>

        <?php if (!empty($error_msg)): ?>
            <!-- Keeping your original echo; style remains inline as before -->
            <div style="color:#e53e3e; font-size:15px; margin-bottom:12px; text-align:center;">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <?php if ($verification_sent && ! $registration_success): ?>
            <!-- Verification code step (frontend only styled) -->
            <form method="POST" action="">
                <div class="form_group">
                    <label for="verification_code">Enter Verification Code</label>
                    <i class="fa-solid fa-key"></i>
                    <input type="text" id="verification_code" name="verification_code" placeholder="Code sent to your email" required>
                </div>
                <?php if ($verification_error): ?>
                    <div class="error-message">
                        <?php echo $verification_error; ?>
                    </div>
                <?php endif; ?>
                <input type="submit" value="Verify" class="button_submit">
            </form>

        <?php elseif (! $registration_success): ?>
            <!-- Registration form (fields unchanged; styling only) -->
            <form method="POST" action="">
                <div class="form_group">
                    <label for="username">Username</label>
                    <i class="fa-solid fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required value="<?php echo $username_val; ?>">
                </div>

                <div class="form_group">
                    <label for="password">Password</label>
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required value="<?php echo htmlspecialchars($password_val); ?>">
                    <span class="toggle-eye" data-target="password" style="display:none;"><i class="fa-solid fa-eye"></i></span>
                    <div id="passwordValidationMsg" style="color:#e53e3e; font-size:14px; margin-top:4px;"></div>
                </div>

                <div class="form_group">
                    <label for="confirm_password">Confirm Password</label>
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required value="<?php echo htmlspecialchars($confirm_password_val); ?>">
                    <span class="toggle-eye" data-target="confirm_password" style="display:none;"><i class="fa-solid fa-eye"></i></span>
                    <div id="confirmPasswordMsg" style="color:#e53e3e; font-size:14px; margin-top:4px;"></div>
                </div>

                <div class="form_group">
                    <label for="email">Email</label>
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required value="<?php echo $email_val; ?>">
                </div>

                <input type="submit" value="Register" class="button_submit" id="registerBtn">
            </form>
            <div class="links">
                <a href="login.php">Already have an account? Sign in</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Show/Hide password toggles (frontend only)
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
        var passwordInput = document.getElementById('password');
        var passwordEye = document.querySelector('span.toggle-eye[data-target="password"]');
        if (passwordInput && passwordEye) {
            passwordInput.addEventListener('input', function() {
                toggleEyeVisibility('password', 'span.toggle-eye[data-target="password"]');
                // ...existing code for validationMsg...
                var len = passwordInput.value.length;
                if (len === 0) {
                    validationMsg.textContent = '';
                } else if (len < 8) {
                    validationMsg.textContent = " (minimum 8 characters required)";
                }
                validationMsg.style.color = (len < 8) ? "#e53e3e" : "#38a169";
            });
            // Initial state
            toggleEyeVisibility('password', 'span.toggle-eye[data-target="password"]');
        }

        var confirmInput = document.getElementById('confirm_password');
        var confirmEye = document.querySelector('span.toggle-eye[data-target="confirm_password"]');
        if (confirmInput && confirmEye) {
            confirmInput.addEventListener('input', function() {
                toggleEyeVisibility('confirm_password', 'span.toggle-eye[data-target="confirm_password"]');
                checkPasswordMatch();
            });
            // Initial state
            toggleEyeVisibility('confirm_password', 'span.toggle-eye[data-target="confirm_password"]');
        }

        // Keep your existing validation logic, just left as-is
        var passwordInput = document.getElementById('password');
        var validationMsg = document.getElementById('passwordValidationMsg');
        if (passwordInput && validationMsg) {
            passwordInput.addEventListener('input', function() {
                var len = passwordInput.value.length;
                if (len === 0) {
                    validationMsg.textContent = '';
                } else if (len < 8) {
                    validationMsg.textContent = " (minimum 8 characters required)";
                }
                validationMsg.style.color = (len < 8) ? "#e53e3e" : "#38a169";
            });
        }

        var confirmInput = document.getElementById('confirm_password');
        var confirmMsg = document.getElementById('confirmPasswordMsg');
        function checkPasswordMatch() {
            if (!confirmInput || !passwordInput || !confirmMsg) return;
            if (confirmInput.value.length === 0) {
                confirmMsg.textContent = '';
            } else if (passwordInput.value !== confirmInput.value) {
                confirmMsg.textContent = "Passwords do not match.";
                confirmMsg.style.color = "#e53e3e";
            } else {
                confirmMsg.textContent = "Passwords match.";
                confirmMsg.style.color = "#38a169";
            }
        }
        if (passwordInput && confirmInput) {
            passwordInput.addEventListener('input', checkPasswordMatch);
            confirmInput.addEventListener('input', checkPasswordMatch);
        }

        // Optional: simple submit disable to prevent double clicks
        const regBtn = document.getElementById('registerBtn');
        if (regBtn) {
            regBtn.closest('form').addEventListener('submit', function(){
                regBtn.disabled = true;
                regBtn.textContent = 'Registering...';
            });
        }
    </script>
</body>
</html>
