<!-- Change Password page content - accessed from within dashboard -->
<div class="container-fluid theme-container">
    <!-- Page Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <h1 class="mb-0">
                <i class="fas fa-key"></i>
                Change Password
            </h1>
            <p class="text-muted mb-0">Update your account password for security</p>
        </div>
    </div>

    <!-- Change Password Content Row -->
    <div class="row justify-content-center">
        <div class="col-lg-6 col-xl-5">
            <!-- Change Password Card -->
            <div class="theme-card">
                <div class="card-header bg-warning-theme text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-shield-alt"></i>
                        Security Update
                    </h5>
                </div>

                <div class="card-body change-password-body">
                    <?php flash('change_password_message'); ?>

                    <p class="text-muted mb-4">
                        <i class="fas fa-info-circle text-primary"></i>
                        Please fill out this form to change your password. Make sure to use a strong password for better
                        security.
                    </p>

                    <form action="<?php echo URLROOT; ?>/users/changePassword" method="post">
                        <div class="form-group">
                            <label for="current_password">
                                <i class="fas fa-lock text-muted"></i>
                                Current Password <sup class="text-danger">*</sup>
                            </label>
                            <input type="password" name="current_password" id="current_password"
                                class="form-control form-control-lg <?php echo (!empty($data['current_password_err'])) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $data['current_password']; ?>"
                                placeholder="Enter your current password">
                            <span class="invalid-feedback"><?php echo $data['current_password_err']; ?></span>
                        </div>

                        <div class="form-group">
                            <label for="new_password">
                                <i class="fas fa-key text-muted"></i>
                                New Password <sup class="text-danger">*</sup>
                            </label>
                            <input type="password" name="new_password" id="new_password"
                                class="form-control form-control-lg <?php echo (!empty($data['new_password_err'])) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $data['new_password']; ?>" placeholder="Enter your new password">
                            <span class="invalid-feedback"><?php echo $data['new_password_err']; ?></span>
                            <small class="form-text text-muted">
                                <i class="fas fa-lightbulb"></i>
                                Password should be at least 6 characters long
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">
                                <i class="fas fa-check-double text-muted"></i>
                                Confirm New Password <sup class="text-danger">*</sup>
                            </label>
                            <input type="password" name="confirm_password" id="confirm_password"
                                class="form-control form-control-lg <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $data['confirm_password']; ?>"
                                placeholder="Confirm your new password">
                            <span class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></span>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <button type="submit" class="btn btn-warning btn-lg btn-block">
                                    <i class="fas fa-save"></i> Change Password
                                </button>
                            </div>
                            <div class="col-md-6 mb-2">
                                <a href="<?php echo URLROOT; ?>/users/profile"
                                    class="btn btn-secondary btn-lg btn-block">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-footer theme-card-footer">
                    <div class="row align-items-center">
                        <div class="col">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt text-success"></i>
                                Your password is encrypted and secure
                            </small>
                        </div>
                        <div class="col-auto">
                            <a href="<?php echo URLROOT; ?>/users/profile" class="btn btn-link btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Tips Card -->
            <div class="theme-card mt-4">
                <div class="card-header bg-info-theme text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb"></i>
                        Password Security Tips
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            Use a combination of letters, numbers, and symbols
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            Make it at least 8 characters long
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            Avoid using personal information
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success"></i>
                            Don't reuse passwords from other accounts
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>