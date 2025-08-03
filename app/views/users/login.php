<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'login_header.php'; ?>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo"><?php echo SITENAME; ?></div>
            <div class="login-subtitle">Sign in to your account</div>
        </div>

        <?php
        // Display flash messages
        if (isset($_SESSION['register_success'])) {
            flash('register_success');
        }
        if (isset($_SESSION['login_error'])) {
            flash('login_error');
        }
        ?>

        <form action="<?php echo URLROOT; ?>/users/login" method="post" class="login-form">
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <div class="input-group">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="username" id="username"
                        class="form-control has-icon <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>"
                        value="<?php echo isset($data['username']) ? $data['username'] : ''; ?>"
                        placeholder="Enter your username" autocomplete="username" required>
                </div>
                <?php if (!empty($data['username_err'])): ?>
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $data['username_err']; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" id="password"
                        class="form-control has-icon <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>"
                        placeholder="Enter your password" autocomplete="current-password" required>
                    <i class="fas fa-eye password-toggle" id="togglePasswordIcon"></i>
                </div>
                <?php if (!empty($data['password_err'])): ?>
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $data['password_err']; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                <label class="form-check-label" for="remember_me">
                    Remember me for 30 days
                </label>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>

        <div class="forgot-password">
            <a href="#" onclick="showForgotPassword()">
                <i class="fas fa-question-circle"></i> Forgot your password?
            </a>
        </div>

        <div class="divider">
            <span>or</span>
        </div>

        <a href="<?php echo URLROOT; ?>/users/register" class="btn-secondary">
            <i class="fas fa-user-plus"></i> Create New Account
        </a>

        <!-- Quick Login for Development -->
        <?php if (APP_ENV === 'development'): ?>
            <div class="dev-tools">
                <small><strong>Development Mode:</strong></small><br>
                <small>Quick login options (remove in production):</small>
                <div class="mt-2">
                    <button type="button" class="btn-dev" onclick="quickLogin('admin', 'admin')">
                        Quick Login as Admin
                    </button>
                    <button type="button" class="btn-dev" onclick="quickLogin('manager', 'manager')">
                        Quick Login as Manager
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="login-footer">
        <strong><?php echo APP_NAME; ?></strong><br>
        <small>Streamline your business operations</small>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header" style="border-bottom: 1px solid #e2e8f0; padding: 24px;">
                <h5 class="modal-title" style="color: #2d3748; font-weight: 600;">Reset Password</h5>
                <button type="button" class="close" data-dismiss="modal" style="font-size: 1.5rem; color: #9ca3af;">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 24px; color: #374151;">
                <div style="text-align: center; margin-bottom: 16px;">
                    <i class="fas fa-lock" style="font-size: 3rem; color: #e2e8f0; margin-bottom: 16px;"></i>
                </div>
                <p style="margin-bottom: 12px;"><strong>Password reset functionality is not yet implemented.</strong>
                </p>
                <p style="margin-bottom: 0;">Please contact your system administrator for password reset assistance.</p>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 16px 24px;">
                <button type="button" class="btn-secondary" data-dismiss="modal"
                    style="margin: 0; padding: 12px 24px;">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<script>
    // Toggle password visibility
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.getElementById('togglePasswordIcon');
        const passwordInput = document.getElementById('password');

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

        // Focus on username field
        document.getElementById('username').focus();

        // Form validation
        const form = document.querySelector('.login-form');
        if (form) {
            form.addEventListener('submit', function (e) {
                let isValid = true;

                // Remove previous error states
                document.querySelectorAll('.form-control').forEach(control => {
                    control.classList.remove('is-invalid');
                });

                // Validate username
                const username = document.getElementById('username').value.trim();
                if (username === '') {
                    document.getElementById('username').classList.add('is-invalid');
                    isValid = false;
                }

                // Validate password
                const password = document.getElementById('password').value.trim();
                if (password === '') {
                    document.getElementById('password').classList.add('is-invalid');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });
        }
    });

    // Show forgot password modal
    function showForgotPassword() {
        const modal = document.getElementById('forgotPasswordModal');
        if (modal) {
            // If using Bootstrap modal
            if (typeof bootstrap !== 'undefined') {
                new bootstrap.Modal(modal).show();
            } else if (typeof $ !== 'undefined') {
                $(modal).modal('show');
            } else {
                // Fallback - simple alert for now
                alert('Password reset functionality is not yet implemented.\n\nPlease contact your system administrator for password reset assistance.');
            }
        }
    }

    // Development quick login (remove in production)
    function quickLogin(username, password) {
        document.getElementById('username').value = username;
        document.getElementById('password').value = password;
    }
</script>

</body>

</html>