<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
<div class="login-top-area container-fluid mt-0 pt-3">
    <div class="row justify-content-center align-items-start min-vh-100">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6">
            <div class="card card-body bg-light mt-3 mb-3 shadow-sm">
                <?php flash('register_success'); ?>
                <h2 class="mb-3 text-center">Login</h2>
                <p class="text-center">Please fill in your credentials to log in</p>
                <form action="<?php echo URLROOT; ?>/users/login" method="post">
                    <div class="form-group">
                        <label for="username">Username: <sup>*</sup></label>
                        <input type="text" name="username"
                            class="form-control form-control-lg <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>"
                            value="<?php echo isset($data['username']) ? $data['username'] : ''; ?>">
                        <span class="invalid-feedback"><?php echo $data['username_err']; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="password">Password: <sup>*</sup></label>
                        <input type="password" name="password"
                            class="form-control form-control-lg <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>"
                            value="<?php echo isset($data['password']) ? $data['password'] : ''; ?>">
                        <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
                    </div>
                    <div class="form-row">
                        <div class="col-12 col-md-6 mb-2 mb-md-0">
                            <input type="submit" value="Login" class="btn btn-success btn-block w-100">
                        </div>
                        <div class="col-12 col-md-6">
                            <a href="<?php echo URLROOT; ?>/users/register" class="btn btn-light btn-block w-100">No
                                account? Register</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'footer.php'; ?>