<?php require APPROOT . '/app/views/layout/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>Change Password</h2>
            <p>Please fill out this form to change your password</p>

            <form action="<?php echo URLROOT; ?>/users/changePassword" method="post">
                <div class="form-group">
                    <label for="current_password">Current Password: <sup>*</sup></label>
                    <input type="password" name="current_password"
                        class="form-control form-control-lg <?php echo (!empty($data['current_password_err'])) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $data['current_password']; ?>">
                    <span class="invalid-feedback"><?php echo $data['current_password_err']; ?></span>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password: <sup>*</sup></label>
                    <input type="password" name="new_password"
                        class="form-control form-control-lg <?php echo (!empty($data['new_password_err'])) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $data['new_password']; ?>">
                    <span class="invalid-feedback"><?php echo $data['new_password_err']; ?></span>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password: <sup>*</sup></label>
                    <input type="password" name="confirm_password"
                        class="form-control form-control-lg <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $data['confirm_password']; ?>">
                    <span class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></span>
                </div>

                <div class="row">
                    <div class="col">
                        <input type="submit" value="Change Password" class="btn btn-warning btn-block">
                    </div>
                    <div class="col">
                        <a href="<?php echo URLROOT; ?>/users/profile" class="btn btn-secondary btn-block">Cancel</a>
                    </div>
                </div>
            </form>
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