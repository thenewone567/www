<?php // Admin Edit User Profile page ?>
<!-- Admin Edit User Profile page -->
<div class="container-fluid theme-container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white text-center profile-header">
                    <h2 class="mb-0"><i class="fas fa-user-edit"></i> Edit User Profile</h2>
                    <p class="mb-0 opacity-75">Editing:
                        <?php echo htmlspecialchars($data['user']->name ?? $data['user']->username); ?></p>
                </div>
                <div class="card-body profile-body">
                    <?php flash('profile_message'); ?>
                    <form action="<?php echo URLROOT; ?>/admin/editUserProfile/<?= $data['user']->user_id ?>"
                        method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control"
                                value="<?php echo htmlspecialchars($data['user']->username ?? $data['user']->user_name ?? ''); ?>"
                                readonly tabindex="-1" style="background:#e9ecef; cursor:not-allowed;">
                            <small class="text-muted">Username cannot be changed</small>
                        </div>

                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="<?php echo htmlspecialchars($data['user']->name ?? $data['user']->full_name ?? ''); ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control"
                                value="<?php echo htmlspecialchars($data['user']->email ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="job_title">Job Title</label>
                            <input type="text" name="job_title" id="job_title" class="form-control"
                                value="<?php echo htmlspecialchars($data['user']->job_title ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" id="address" class="form-control"
                                rows="3"><?php echo htmlspecialchars($data['user']->address ?? ''); ?></textarea>
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
                                <?php
                                // Avatar logic - check for user profile picture
                                $username = $data['user']->username ?? $data['user']->user_name ?? '';
                                $avatarPath = null;
                                $defaultAvatar = URLROOT . '/storage/uploads/users/avatar.png';

                                // Try to find user avatar by username
                                if (!empty($username)) {
                                    $avatarExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                                    foreach ($avatarExtensions as $ext) {
                                        $filePath = 'storage/uploads/users/' . $username . '.' . $ext;
                                        if (file_exists($filePath)) {
                                            $avatarPath = URLROOT . '/' . $filePath;
                                            break;
                                        }
                                    }
                                }

                                $finalAvatarPath = $avatarPath ?: $defaultAvatar;
                                ?>
                                <img src="<?php echo htmlspecialchars($finalAvatarPath); ?>"
                                    alt="Current Profile Picture"
                                    style="max-width: 100px; max-height: 100px; border-radius: 50%; border: 1px solid #ccc;"
                                    onerror="this.onerror=null; this.src='<?= htmlspecialchars($defaultAvatar) ?>';">
                                <?php if (!$avatarPath): ?>
                                    <small class="text-muted d-block">No profile picture uploaded. Default avatar is being
                                        used.</small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- User Info Display -->
                        <div class="card mt-4 bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-info-circle"></i> User Information</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">User ID:</small>
                                        <strong><?= $data['user']->user_id ?></strong><br>
                                        <small class="text-muted">Role:</small>
                                        <strong><?= ucfirst($data['user']->role_name ?? 'user') ?></strong><br>
                                        <small class="text-muted">Category:</small>
                                        <strong><?= ucfirst($data['user']->user_category ?? 'official') ?></strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Source Table:</small>
                                        <strong><?= $data['user']->source_table ?? 'users' ?></strong><br>
                                        <?php if (isset($data['user']->created_at)): ?>
                                            <small class="text-muted">Member Since:</small>
                                            <strong><?= date('M j, Y', strtotime($data['user']->created_at)) ?></strong><br>
                                        <?php endif; ?>
                                        <?php if (isset($data['user']->last_login) && $data['user']->last_login): ?>
                                            <small class="text-muted">Last Login:</small>
                                            <strong><?= date('M j, Y g:i A', strtotime($data['user']->last_login)) ?></strong>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="<?php echo URLROOT; ?>/admin/viewUser/<?= $data['user']->user_id ?>"
                                class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <a href="<?php echo URLROOT; ?>/admin/users" class="btn btn-outline-secondary ml-2">
                                <i class="fas fa-arrow-left"></i> Back to Users
                            </a>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                var form = document.querySelector('form[action*="/admin/editUserProfile/"]');
                                var birthdayInput = document.getElementById('birthday');

                                form.addEventListener('submit', function (e) {
                                    console.log('Form submission initiated...');

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

                                    console.log('Form validation passed. Submitting...');
                                });
                            });
                        </script>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>