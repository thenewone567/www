<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'login_header.php'; ?>
<div class="facebook-container">
    <div class="facebook-left">
        <div class="facebook-logo">
            <?php echo SITENAME; ?>
        </div>
        <div class="facebook-tagline">
            Join our team and start managing your hardware store.
        </div>
    </div>

    <div class="facebook-right">
        <div class="login-card">
            <h2 style="text-align: center; margin-bottom: 20px; font-size: 32px; font-weight: bold; color: #1c1e21;">
                Sign Up</h2>
            <p style="text-align: center; margin-bottom: 20px; color: #606770; font-size: 15px;">It's quick and easy.
            </p>

            <form action="<?php echo URLROOT; ?>/users/register" method="post">
                <div class="form-group">
                    <input type="text" name="username" id="username"
                        class="form-control <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>"
                        value="<?php echo isset($data['username']) ? $data['username'] : ''; ?>" placeholder="Username">
                    <?php if (!empty($data['username_err'])): ?>
                        <span class="invalid-feedback"><?php echo $data['username_err']; ?></span>
                    <?php endif; ?>
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
                <a href="<?php echo URLROOT; ?>/users/login" class="btn btn-facebook"
                    style="width: auto; padding: 12px 24px;">
                    Already have an account?
                </a>
            </div>
        </div>
    </div>
</div>

            </div> <!-- End container-fluid -->
        </div> <!-- End page-content-wrapper -->
    </div> <!-- End wrapper -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
    <script src="<?php echo URLROOT; ?>/js/main.js"></script>
</body>
</html>