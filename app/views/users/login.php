<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'login_header.php'; ?>

<div class="login-container">
    <div class="login-card">
        <div class="login-header text-center">
            <?php if (!empty($data['company_logo'])): ?>
                <div class="mb-2">
                    <img src="<?php echo URLROOT . '/' . htmlspecialchars($data['company_logo']); ?>" alt="Logo"
                        style="max-height:80px;max-width:200px;object-fit:contain;filter:drop-shadow(0 2px 4px rgba(0,0,0,0.15));">
                </div>
            <?php endif; ?>
            <div class="login-logo"
                style="font-family:'Inter',sans-serif;font-weight:600;font-size:1.9rem;letter-spacing:.5px;">
                <?php echo htmlspecialchars($data['company_name'] ?? company_name()); ?>
            </div>
            <div class="login-subtitle" style="font-size:.95rem;color:var(--text-muted);margin-top:4px;">Sign in to your
                account</div>
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
            <div class="login-form-group">
                <label for="username" class="login-form-label">Username</label>
                <div class="login-input-group">
                    <i class="fas fa-user login-input-icon"></i>
                    <input type="text" name="username" id="username"
                        class="login-form-input <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>"
                        value="<?php echo isset($data['username']) ? $data['username'] : ''; ?>"
                        placeholder="Enter your username" autocomplete="username" required>
                </div>
                <?php if (!empty($data['username_err'])): ?>
                    <div class="login-invalid-feedback">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $data['username_err']; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="login-form-group">
                <label for="password" class="login-form-label">Password</label>
                <div class="login-input-group">
                    <i class="fas fa-lock login-input-icon"></i>
                    <input type="password" name="password" id="password"
                        class="login-form-input <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>"
                        placeholder="Enter your password" autocomplete="current-password" required>
                    <i class="fas fa-eye password-toggle" id="togglePasswordIcon"></i>
                </div>
                <?php if (!empty($data['password_err'])): ?>
                    <div class="login-invalid-feedback">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $data['password_err']; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="login-checkbox-group">
                <input type="checkbox" class="login-checkbox" id="remember_me" name="remember_me">
                <label class="login-checkbox-label" for="remember_me">
                    Remember me for 30 days
                </label>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>

        <div class="login-forgot-password">
            <a href="#" onclick="showForgotPassword()">
                <i class="fas fa-question-circle"></i> Forgot your password?
            </a>
        </div>

        <div class="login-divider">
            <span>or</span>
        </div>

        <a href="<?php echo URLROOT; ?>/users/register" class="btn-register">
            <i class="fas fa-user-plus"></i> Create New Account
        </a>

        <!-- Quick Login for Development -->
        <?php if (APP_ENV === 'development'): ?>
            <div class="dev-tools">
                <small><strong>Development Mode:</strong></small><br>
                <small>Quick login options (remove in production):</small>
                <div class="mt-2">
                    <button type="button" class="btn-dev" onclick="quickLogin('admin', 'admin')">
                        <i class="fas fa-crown"></i> Super Admin
                    </button>
                    <button type="button" class="btn-dev" onclick="quickLogin('manager', 'manager')">
                        <i class="fas fa-user-tie"></i> Manager
                    </button>
                    <button type="button" class="btn-dev" onclick="quickLogin('cashier', 'cashier')">
                        <i class="fas fa-cash-register"></i> Cashier
                    </button>
                    <button type="button" class="btn-dev" onclick="quickLogin('clerk', 'clerk')">
                        <i class="fas fa-boxes"></i> Inventory Clerk
                    </button>
                    <button type="button" class="btn-dev" onclick="quickLogin('user', 'user')">
                        <i class="fas fa-user"></i> Basic User
                    </button>
                </div>
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        All accounts use the same username/password pattern for easy testing
                    </small>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="login-footer">
        <strong><?php echo htmlspecialchars($data['company_name'] ?? company_name()); ?></strong><br>
        <small>Powered by <?php echo APP_NAME; ?></small>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal-modern" id="forgotPasswordModal">
    <div class="modal-dialog-modern">
        <div class="modal-content-modern">
            <div class="modal-header-modern">
                <h5 class="modal-title-modern">Reset Password</h5>
                <button type="button" class="modal-close-modern" onclick="hideModal('forgotPasswordModal')">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body-modern" id="forgotPasswordBody">
                <div>
                    <i class="fas fa-key modal-icon"></i>
                </div>
                <p style="margin-bottom: 12px;"><strong>Reset Your Password</strong></p>
                <p style="margin-bottom: 20px;">Enter your username and new password to reset it directly. <small
                        class="text-muted">(Local development mode)</small></p>

                <form id="forgotPasswordForm">
                    <div class="form-group mb-3">
                        <label for="resetUsername">Username</label>
                        <input type="text" id="resetUsername" class="form-control" placeholder="Enter your username"
                            required>
                        <div class="invalid-feedback" id="resetUsernameError"></div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="newPassword">New Password</label>
                        <div class="input-group">
                            <input type="password" id="newPassword" class="form-control"
                                placeholder="Enter new password" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="toggleModalPassword('newPassword', 'toggleNewPassword')">
                                    <i class="fas fa-eye" id="toggleNewPassword"></i>
                                </button>
                            </div>
                        </div>
                        <div class="invalid-feedback" id="resetPasswordError"></div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="confirmPassword">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" id="confirmPassword" class="form-control"
                                placeholder="Confirm new password" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="toggleModalPassword('confirmPassword', 'toggleConfirmPassword')">
                                    <i class="fas fa-eye" id="toggleConfirmPassword"></i>
                                </button>
                            </div>
                        </div>
                        <div class="invalid-feedback" id="resetConfirmError"></div>
                    </div>

                    <div class="alert alert-info" id="resetInfo" style="display: none;"></div>
                    <div class="alert alert-danger" id="resetError" style="display: none;"></div>
                    <div class="alert alert-success" id="resetSuccess" style="display: none;"></div>
                </form>
            </div>
            <div class="modal-footer-modern">
                <button type="button" class="btn-register" onclick="hideModal('forgotPasswordModal')"
                    style="margin-right: 10px;">Cancel</button>
                <button type="button" class="btn-login" onclick="resetPasswordDirectly()" id="resetButton">
                    <i class="fas fa-key"></i> Reset Password
                </button>
            </div>
        </div>
    </div>
</div>

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
                document.querySelectorAll('.login-form-input').forEach(control => {
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

    // Modern modal functions
    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function hideModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    // Show forgot password modal
    function showForgotPassword() {
        showModal('forgotPasswordModal');
    }

    // Close modal when clicking outside
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('modal-modern')) {
            hideModal(e.target.id);
        }
    });

    // Development quick login (remove in production)
    function quickLogin(username, password) {
        document.getElementById('username').value = username;
        document.getElementById('password').value = password;
    }

    // Toggle password visibility in modal
    function toggleModalPassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Direct password reset functionality for local use
    function resetPasswordDirectly() {
        const username = document.getElementById('resetUsername').value.trim();
        const newPassword = document.getElementById('newPassword').value.trim();
        const confirmPassword = document.getElementById('confirmPassword').value.trim();
        const resetButton = document.getElementById('resetButton');

        // Get error elements
        const usernameError = document.getElementById('resetUsernameError');
        const passwordError = document.getElementById('resetPasswordError');
        const confirmError = document.getElementById('resetConfirmError');
        const resetInfo = document.getElementById('resetInfo');
        const resetError = document.getElementById('resetError');
        const resetSuccess = document.getElementById('resetSuccess');

        // Clear previous messages and errors
        usernameError.style.display = 'none';
        passwordError.style.display = 'none';
        confirmError.style.display = 'none';
        resetInfo.style.display = 'none';
        resetError.style.display = 'none';
        resetSuccess.style.display = 'none';

        document.getElementById('resetUsername').classList.remove('is-invalid');
        document.getElementById('newPassword').classList.remove('is-invalid');
        document.getElementById('confirmPassword').classList.remove('is-invalid');

        let isValid = true;

        // Validate username
        if (!username) {
            usernameError.textContent = 'Please enter your username';
            usernameError.style.display = 'block';
            document.getElementById('resetUsername').classList.add('is-invalid');
            isValid = false;
        } else if (username.length < 3) {
            usernameError.textContent = 'Username must be at least 3 characters';
            usernameError.style.display = 'block';
            document.getElementById('resetUsername').classList.add('is-invalid');
            isValid = false;
        }

        // Validate new password
        if (!newPassword) {
            passwordError.textContent = 'Please enter a new password';
            passwordError.style.display = 'block';
            document.getElementById('newPassword').classList.add('is-invalid');
            isValid = false;
        } else if (newPassword.length < 6) {
            passwordError.textContent = 'Password must be at least 6 characters';
            passwordError.style.display = 'block';
            document.getElementById('newPassword').classList.add('is-invalid');
            isValid = false;
        }

        // Validate confirm password
        if (!confirmPassword) {
            confirmError.textContent = 'Please confirm your password';
            confirmError.style.display = 'block';
            document.getElementById('confirmPassword').classList.add('is-invalid');
            isValid = false;
        } else if (newPassword !== confirmPassword) {
            confirmError.textContent = 'Passwords do not match';
            confirmError.style.display = 'block';
            document.getElementById('confirmPassword').classList.add('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            return;
        }

        // Show loading state
        resetButton.disabled = true;
        resetButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resetting...';
        resetInfo.textContent = 'Updating password...';
        resetInfo.style.display = 'block';

        // Send AJAX request
        fetch('<?php echo URLROOT; ?>/users/resetPasswordDirect', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'username=' + encodeURIComponent(username) +
                '&password=' + encodeURIComponent(newPassword) +
                '&confirm_password=' + encodeURIComponent(confirmPassword)
        })
            .then(response => response.json())
            .then(data => {
                resetInfo.style.display = 'none';

                if (data.success) {
                    resetSuccess.textContent = data.message;
                    resetSuccess.style.display = 'block';

                    // Clear form
                    document.getElementById('resetUsername').value = '';
                    document.getElementById('newPassword').value = '';
                    document.getElementById('confirmPassword').value = '';

                    // Auto-close modal after success
                    setTimeout(() => {
                        hideModal('forgotPasswordModal');
                        // Optionally auto-fill the login form
                        document.getElementById('username').value = username;
                        document.getElementById('password').focus();
                    }, 2000);
                } else {
                    if (data.username_err) {
                        usernameError.textContent = data.username_err;
                        usernameError.style.display = 'block';
                        document.getElementById('resetUsername').classList.add('is-invalid');
                    }
                    if (data.password_err) {
                        passwordError.textContent = data.password_err;
                        passwordError.style.display = 'block';
                        document.getElementById('newPassword').classList.add('is-invalid');
                    }
                    if (data.confirm_password_err) {
                        confirmError.textContent = data.confirm_password_err;
                        confirmError.style.display = 'block';
                        document.getElementById('confirmPassword').classList.add('is-invalid');
                    }
                    if (data.message && !data.username_err && !data.password_err && !data.confirm_password_err) {
                        resetError.textContent = data.message;
                        resetError.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resetInfo.style.display = 'none';
                resetError.textContent = 'Network error. Please check your connection and try again.';
                resetError.style.display = 'block';
            })
            .finally(() => {
                // Reset button state
                resetButton.disabled = false;
                resetButton.innerHTML = '<i class="fas fa-key"></i> Reset Password';
            });
    }

    // Allow enter key to submit forgot password form
    document.addEventListener('DOMContentLoaded', function () {
        ['resetUsername', 'newPassword', 'confirmPassword'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        resetPasswordDirectly();
                    }
                });
            }
        });
    });
</script>

</body>

</html>