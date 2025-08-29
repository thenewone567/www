<?php // Edit Profile page ?>
<!-- Edit Profile page -->
<div class="container-fluid theme-container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white text-center profile-header">
                    <h2 class="mb-0"><i class="fas fa-user-edit"></i> Edit Profile</h2>
                </div>
                <div class="card-body profile-body">
                    <?php flash('edit_profile_message'); ?>
                    <form action="<?php echo URLROOT; ?>/users/editProfile" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control"
                                value="<?php echo htmlspecialchars($data['user']->username); ?>" readonly tabindex="-1"
                                style="background:#e9ecef; cursor:not-allowed;">
                        </div>
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" name="full_name" id="full_name" class="form-control"
                                value="<?php echo htmlspecialchars($data['user']->full_name ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                value="<?php echo htmlspecialchars($data['user']->email ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" name="address" id="address" class="form-control"
                                value="<?php echo htmlspecialchars($data['user']->address ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="job_title">Job Title</label>
                            <input type="text" name="job_title" id="job_title" class="form-control"
                                value="<?php echo htmlspecialchars($data['user']->job_title ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="birthday">Birthday</label>
                            <input type="date" name="birthday" id="birthday" class="form-control"
                                value="<?php echo htmlspecialchars($data['user']->birthday ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="education">Education</label>
                            <input type="text" name="education" id="education" class="form-control"
                                value="<?php echo htmlspecialchars($data['user']->education ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="profile_picture_file">Profile Picture</label>
                            <input type="file" name="profile_picture_file" id="profile_picture_file"
                                class="form-control-file" accept="image/*">
                            <div class="mt-2">
                                <?php if (!empty($data['user']->profile_picture)): ?>
                                    <img src="<?php echo htmlspecialchars($data['user']->profile_picture); ?>"
                                        alt="Current Profile Picture"
                                        style="max-width: 100px; max-height: 100px; border-radius: 50%; border: 1px solid #ccc;">
                                <?php else: ?>
                                    <img src="<?php echo URLROOT; ?>/storage/uploads/users/avatar.png" alt="Default Avatar"
                                        style="max-width: 100px; max-height: 100px; border-radius: 50%; border: 1px solid #ccc;">
                                    <small class="text-muted d-block">No profile picture uploaded. Default avatar will be
                                        used.</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                var form = document.querySelector('form[action$="/users/editProfile"]');
                                var birthdayInput = document.getElementById('birthday');
                                form.addEventListener('submit', function (e) {
                                    var birthday = birthdayInput.value;
                                    if (birthday) {
                                        var birthDate = new Date(birthday);
                                        var today = new Date();
                                        var age = today.getFullYear() - birthDate.getFullYear();
                                        var m = today.getMonth() - birthDate.getMonth();
                                        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                                            age--;
                                        }
                                        if (age < 18) {
                                            alert('User must be at least 18 years old.');
                                            birthdayInput.focus();
                                            e.preventDefault();
                                            return false;
                                        }
                                    }
                                });
                            });
                        </script>
                        <a href="<?php echo URLROOT; ?>/users/profile" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>