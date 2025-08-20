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