<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'login_header.php'; ?>

<div class="facebook-container">
    <div class="facebook-left">
        <div class="facebook-logo">
            <?php echo SITENAME; ?>
        </div>
        <div class="facebook-tagline">
            Connect with your team and manage your hardware store efficiently.
            Access inventory, sales, and reporting tools with your secure account.
        </div>
    </div>

    <div class="facebook-right">
        <div class="login-card">
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
                <h2 class="text-center mb-4">Login to Your Account</h2>

                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" name="username" id="username"
                            class="form-control <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>"
                            value="<?php echo isset($data['username']) ? $data['username'] : ''; ?>"
                            placeholder="Enter your username" autocomplete="username" required>
                    </div>
                    <?php if (!empty($data['username_err'])): ?>
                        <div class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $data['username_err']; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" name="password" id="password"
                            class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>"
                            placeholder="Enter your password" autocomplete="current-password" required>
                        <div class="input-group-append">
                            <span class="input-group-text toggle-password" style="cursor: pointer;">
                                <i class="fas fa-eye" id="togglePasswordIcon"></i>
                            </span>
                        </div>
                    </div>
                    <?php if (!empty($data['password_err'])): ?>
                        <div class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $data['password_err']; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                    <label class="form-check-label" for="remember_me">
                        Remember me for 30 days
                    </label>
                </div>

                <button type="submit" class="btn btn-facebook btn-block">
                    <i class="fas fa-sign-in-alt"></i> Log In
                </button>
            </form>

            <div class="forgot-password text-center mt-3">
                <a href="#" onclick="showForgotPassword()">
                    <i class="fas fa-question-circle"></i> Forgotten password?
                </a>
            </div>

            <div class="divider">
                <span>or</span>
            </div>

            <div class="text-center">
                <a href="<?php echo URLROOT; ?>/users/register" class="btn btn-create">
                    <i class="fas fa-user-plus"></i> Create New Account
                </a>
            </div>

            <!-- Quick Login for Development -->
            <?php if (APP_ENV === 'development'): ?>
                <div class="dev-tools mt-3 p-2" style="background: #f8f9fa; border-radius: 5px; border: 1px solid #dee2e6;">
                    <small class="text-muted"><strong>Development Mode:</strong></small><br>
                    <small class="text-muted">Quick login options (remove in production):</small>
                    <div class="btn-group-vertical btn-group-sm w-100 mt-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            onclick="quickLogin('admin', 'admin123')">
                            Quick Login as Admin
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            onclick="quickLogin('manager', 'manager123')">
                            Quick Login as Manager
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            onclick="quickLogin('sukhdev2', 'sukhdev123')">
                            Quick Login as sukhdev2 (Admin)
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="create-page-text text-center mt-3">
            <small><strong>Hardware Store Management System</strong><br>
                Streamline your business operations</small>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Password reset functionality is not yet implemented.</p>
                <p>Please contact your system administrator for password reset assistance.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<script>
    // Toggle password visibility
    $(document).ready(function () {
        $('.toggle-password').click(function () {
            const passwordInput = $('#password');
            const icon = $('#togglePasswordIcon');

            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordInput.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Focus on username field
        $('#username').focus();
    });

    // Show forgot password modal
    function showForgotPassword() {
        $('#forgotPasswordModal').modal('show');
    }

    // Development quick login (remove in production)
    function quickLogin(username, password) {
        $('#username').val(username);
        $('#password').val(password);
    }

    // Form validation
    $('.login-form').on('submit', function (e) {
        let isValid = true;

        // Remove previous error states
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').hide();

        // Validate username
        const username = $('#username').val().trim();
        if (username === '') {
            $('#username').addClass('is-invalid');
            isValid = false;
        }

        // Validate password
        const password = $('#password').val().trim();
        if (password === '') {
            $('#password').addClass('is-invalid');
            isValid = false;
        }

        return isValid;
    });
</script>

</body>

</html>