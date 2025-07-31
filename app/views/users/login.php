<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'login_header.php'; ?>
<div class="facebook-container">
    <div class="facebook-left">
        <div class="facebook-logo">
            <?php echo SITENAME; ?>
        </div>
        <div class="facebook-tagline">
            Connect with your team and manage your hardware store efficiently.
        </div>
    </div>

    <div class="facebook-right">
        <div class="login-card">
            <?php
            // Display flash message only if it exists
            if (isset($_SESSION['register_success'])) {
                flash('register_success');
            }
            ?>
            <form action="<?php echo URLROOT; ?>/users/login" method="post">
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

                <button type="submit" class="btn btn-facebook">
                    Log In
                </button>
            </form>

            <div class="forgot-password">
                <a href="#">Forgotten password?</a>
            </div>

            <div class="divider"></div>

            <div class="text-center">
                <a href="<?php echo URLROOT; ?>/users/register" class="btn btn-create">
                    Create New Account
                </a>
            </div>
        </div>

        <div class="create-page-text">
            <strong>Create a Page</strong> for your business.
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