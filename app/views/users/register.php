<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'login_header.php'; ?>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo"><?php echo company_name(); ?></div>
            <div class="login-subtitle">Create your account</div>
        </div>

        <div class="facebook-right">
            <div class="login-card">
                <h2
                    style="text-align: center; margin-bottom: 20px; font-size: 32px; font-weight: bold; color: #1c1e21;">
                    Sign Up</h2>
                <p style="text-align: center; margin-bottom: 20px; color: #606770; font-size: 15px;">It's quick and
                    easy.
                </p>

                <form action="<?php echo URLROOT; ?>/users/register" method="post" id="registerForm" data-verify="user"
                    data-verify-redirect="<?php echo URLROOT; ?>/users" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="text" name="username" id="username"
                            class="form-control <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>"
                            value="<?php echo isset($data['username']) ? $data['username'] : ''; ?>"
                            placeholder="Username">
                        <div id="username-feedback" class="invalid-feedback"></div>
                        <?php if (!empty($data['username_err'])): ?>
                            <span class="invalid-feedback"><?php echo $data['username_err']; ?></span>
                        <?php endif; ?>
                        <small class="form-text text-muted">
                            3-20 characters, letters/numbers/underscores only, must start with letter
                        </small>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email"
                            value="<?php echo isset($data['email']) ? $data['email'] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <input type="file" name="profile_picture_file" id="profile_picture_file"
                            class="form-control-file" accept="image/*">
                    </div>
                    <div class="form-group">
                        <input type="text" name="address" id="address" class="form-control" placeholder="Address"
                            value="<?php echo isset($data['address']) ? $data['address'] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" name="job_title" id="job_title" class="form-control" placeholder="Job Title"
                            value="<?php echo isset($data['job_title']) ? $data['job_title'] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <input type="date" name="birthday" id="birthday" class="form-control" placeholder="Birthday"
                            value="<?php echo isset($data['birthday']) ? $data['birthday'] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <input type="text" name="education" id="education" class="form-control" placeholder="Education"
                            value="<?php echo isset($data['education']) ? $data['education'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <input type="password" name="password" id="password"
                            class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>"
                            placeholder="Password">
                        <?php if (!empty($data['password_err'])): ?>
                            <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <input type="password" name="confirm_password" id="confirm_password"
                            class="form-control <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>"
                            placeholder="Confirm Password">
                        <?php if (!empty($data['confirm_password_err'])): ?>
                            <span class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <select name="role_id" id="role_id" class="form-control" style="color: #8a8d91;">
                            <option value="1">Admin</option>
                            <option value="2">Manager</option>
                            <option value="3">Supervisor</option>
                            <option value="4">Warehouse Associate</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-create" style="width: 100%; margin: 16px 0;">
                        Sign Up
                    </button>

                    <p style="color: #777; font-size: 11px; text-align: center; margin-bottom: 20px;">
                        By clicking Sign Up, you agree to our Terms, Data Policy and Cookie Policy.
                    </p>
                </form>

                <div class="divider"></div>

                <div class="text-center">
                    <a href="<?php echo URLROOT; ?>/users/login" class="btn-secondary">
                        Already have an account? Sign in
                    </a>
                </div>
            </div>

            <div class="login-footer">
                <strong><?php echo APP_NAME; ?></strong><br>
                <small>Streamline your business operations</small>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
    <script src="<?php echo URLROOT; ?>/public/js/main.js"></script>

    <script>
        // Real-time username validation
        $(document).ready(function () {
            const usernameInput = $('#username');
            const usernameFeedback = $('#username-feedback');

            // Reserved usernames
            const reservedUsernames = ['admin', 'root', 'user', 'test', 'guest', 'null', 'undefined', 'system', 'administrator'];

            function validateUsername(username) {
                const errors = [];

                // Length validation
                if (username.length > 0 && username.length < 3) {
                    errors.push('Username must be at least 3 characters long');
                }
                if (username.length > 20) {
                    errors.push('Username must not exceed 20 characters');
                }

                // No spaces
                if (username.includes(' ')) {
                    errors.push('Username cannot contain spaces');
                }

                // Only alphanumeric and underscores
                if (!/^[a-zA-Z0-9_]*$/.test(username)) {
                    errors.push('Username can only contain letters, numbers, and underscores');
                }

                // Must start with letter
                if (username.length > 0 && !/^[a-zA-Z]/.test(username)) {
                    errors.push('Username must start with a letter');
                }

                // Cannot end with underscore
                if (username.endsWith('_')) {
                    errors.push('Username cannot end with an underscore');
                }

                // No consecutive underscores
                if (username.includes('__')) {
                    errors.push('Username cannot contain consecutive underscores');
                }

                // Reserved usernames
                if (reservedUsernames.includes(username.toLowerCase())) {
                    errors.push('This username is reserved and cannot be used');
                }

                return errors;
            }

            usernameInput.on('input', function () {
                const username = $(this).val();
                const errors = validateUsername(username);

                if (username.length === 0) {
                    // Clear validation when empty
                    $(this).removeClass('is-invalid is-valid');
                    usernameFeedback.text('').hide();
                } else if (errors.length > 0) {
                    // Show errors
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    usernameFeedback.text(errors[0]).show();
                } else {
                    // Valid username
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    usernameFeedback.text('Username looks good!').removeClass('invalid-feedback').addClass('valid-feedback').show();
                }
            });

            // Form submission validation
            $('#registerForm').on('submit', function (e) {
                const username = usernameInput.val();
                const errors = validateUsername(username);

                if (errors.length > 0) {
                    e.preventDefault();
                    usernameInput.addClass('is-invalid');
                    usernameFeedback.text(errors.join('. ')).addClass('invalid-feedback').removeClass('valid-feedback').show();
                    usernameInput.focus();
                }
            });
        });
    </script>
    </body>

    </html>