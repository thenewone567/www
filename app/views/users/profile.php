<?php require APPROOT . '/app/views/layout/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>User Profile</h2>
            <?php flash('profile_message'); ?>
            <?php flash('change_password_success'); ?>

            <div class="profile-info">
                <h4>Profile Information</h4>
                <table class="table table-striped">
                    <tr>
                        <td><strong>Username:</strong></td>
                        <td><?php echo $data['user']->username; ?></td>
                    </tr>
                    <tr>
                        <td><strong>User ID:</strong></td>
                        <td><?php echo $data['user']->user_id; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Role:</strong></td>
                        <td>
                            <span
                                class="badge badge-<?php echo $data['user']->role_name === 'admin' ? 'danger' : 'primary'; ?>">
                                <?php echo ucfirst($data['user']->role_name ?? 'employee'); ?>
                            </span>
                        </td>
                    </tr>
                    <?php if (isset($data['user']->created_at)): ?>
                        <tr>
                            <td><strong>Member Since:</strong></td>
                            <td><?php echo date('F j, Y', strtotime($data['user']->created_at)); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (isset($data['user']->last_login) && $data['user']->last_login): ?>
                        <tr>
                            <td><strong>Last Login:</strong></td>
                            <td><?php echo date('F j, Y g:i A', strtotime($data['user']->last_login)); ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>

            <div class="profile-actions mt-4">
                <h4>Actions</h4>
                <a href="<?php echo URLROOT; ?>/users/changePassword" class="btn btn-warning">
                    <i class="fas fa-key"></i> Change Password
                </a>
                <a href="<?php echo URLROOT; ?>/pages/index" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <a href="<?php echo URLROOT; ?>/admin" class="btn btn-primary">
                        <i class="fas fa-cog"></i> Admin Panel
                    </a>
                <?php endif; ?>
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