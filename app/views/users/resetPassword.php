<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'login_header.php'; ?>

<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/app-unified.css">

<div class="login-container">
    <div class="login-card">
        <div class="login-header text-center">
            <div class="login-logo"
                style="font-family:'Inter',sans-serif;font-weight:600;font-size:1.9rem;letter-spacing:.5px;">
                <i class="fas fa-key text-primary"></i> Reset Password
            </div>
            <div class="login-subtitle" style="font-size:.95rem;color:var(--text-muted);margin-top:4px;">
                Enter your new password below
            </div>
        </div>

        <?php
        // Display flash messages
        flash();
        ?>

        <form action="<?php echo URLROOT; ?>/users/resetPassword/<?php echo htmlspecialchars($data['token']); ?>"
            method="post" class="login-form">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($data['token']); ?>">

            <div class="login-form-group">
                <label for="password" class="login-form-label">New Password</label>
                <div class="login-input-group">
                    <i class="fas fa-lock login-input-icon"></i>
                    <input type="password" name="password" id="password"
                        class="login-form-input <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>"
                        placeholder="Enter new password" required>
                    <i class="fas fa-eye password-toggle" id="togglePasswordIcon"></i>
                </div>
                <?php if (!empty($data['password_err'])): ?>
                    <div class="login-invalid-feedback">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $data['password_err']; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="login-form-group">
                <label for="confirm_password" class="login-form-label">Confirm New Password</label>
                <div class="login-input-group">
                    <i class="fas fa-lock login-input-icon"></i>
                    <input type="password" name="confirm_password" id="confirm_password"
                        class="login-form-input <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>"
                        placeholder="Confirm new password" required>
                    <i class="fas fa-eye password-toggle" id="toggleConfirmPasswordIcon"></i>
                </div>
                <?php if (!empty($data['confirm_password_err'])): ?>
                    <div class="login-invalid-feedback">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $data['confirm_password_err']; ?>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-check-circle"></i> Update Password
            </button>
        </form>

        <div class="login-divider">
            <span></span>
        </div>

        <a href="<?php echo URLROOT; ?>/users/login" class="btn-register">
            <i class="fas fa-arrow-left"></i> Back to Login
        </a>
    </div>

    <div class="login-footer">
        <strong><?php echo SITENAME; ?></strong><br>
        <small>Powered by <?php echo APP_NAME; ?></small>
    </div>
</div>

<script>
    // Toggle password visibility
    document.addEventListener('DOMContentLoaded', function () {
        // Main password toggle
        const togglePassword = document.getElementById('togglePasswordIcon');
        const passwordInput = document.getElementById('password');

        // Confirm password toggle
        const toggleConfirmPassword = document.getElementById('toggleConfirmPasswordIcon');
        const confirmPasswordInput = document.getElementById('confirm_password');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function () {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    togglePassword.classList.remove('fa-eye');
                    togglePassword.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    togglePassword.classList.remove('fa-eye-slash');
                    togglePassword.classList.add('fa-eye');
                }
            });
        }

        if (toggleConfirmPassword && confirmPasswordInput) {
            toggleConfirmPassword.addEventListener('click', function () {
                if (confirmPasswordInput.type === 'password') {
                    confirmPasswordInput.type = 'text';
                    toggleConfirmPassword.classList.remove('fa-eye');
                    toggleConfirmPassword.classList.add('fa-eye-slash');
                } else {
                    confirmPasswordInput.type = 'password';
                    toggleConfirmPassword.classList.remove('fa-eye-slash');
                    toggleConfirmPassword.classList.add('fa-eye');
                }
            });
        }

        // Focus on password field
        passwordInput.focus();

        // Form validation
        const form = document.querySelector('.login-form');
        if (form) {
            form.addEventListener('submit', function (e) {
                let isValid = true;

                // Remove previous error states
                document.querySelectorAll('.login-form-input').forEach(control => {
                    control.classList.remove('is-invalid');
                });

                // Validate password
                const password = passwordInput.value.trim();
                if (password === '') {
                    passwordInput.classList.add('is-invalid');
                    isValid = false;
                } else if (password.length < 6) {
                    passwordInput.classList.add('is-invalid');
                    isValid = false;
                }

                // Validate confirm password
                const confirmPassword = confirmPasswordInput.value.trim();
                if (confirmPassword === '') {
                    confirmPasswordInput.classList.add('is-invalid');
                    isValid = false;
                } else if (password !== confirmPassword) {
                    confirmPasswordInput.classList.add('is-invalid');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });
        }
    });
</script>

</body>

</html>