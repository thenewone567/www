<!-- Profile page content - accessed from within dashboard -->
<div class="container-fluid theme-container">
    <!-- Page Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <h1 class="mb-0">
                <i class="fas fa-user-circle"></i>
                User Profile
            </h1>
            <p class="text-muted mb-0">Manage your account information and settings</p>
        </div>
    </div>

    <!-- Profile Content Row -->
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <!-- Profile Card -->
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white text-center profile-header">
                    <div class="profile-avatar">
                        <?php if (!empty($data['user']->profile_picture)): ?>
                            <img src="<?php echo htmlspecialchars($data['user']->profile_picture); ?>" alt="Profile Picture"
                                class="rounded-circle"
                                style="width: 80px; height: 80px; object-fit: cover; border: 2px solid #fff;">
                        <?php else: ?>
                            <i class="fas fa-user"></i>
                        <?php endif; ?>
                    </div>
                    <h2 class="mb-0">
                        <?php echo !empty($data['user']->full_name) ? htmlspecialchars($data['user']->full_name) : htmlspecialchars($data['user']->username); ?>
                    </h2>
                    <p class="mb-0 opacity-75">
                        <span class="badge badge-light">
                            <?php echo ucfirst($data['user']->role_name ?? 'employee'); ?>
                        </span>
                    </p>
                </div>

                <div class="card-body profile-body">
                    <?php flash('profile_message'); ?>
                    <?php flash('change_password_success'); ?>

                    <h5 class="mb-4"><i class="fas fa-info-circle text-primary"></i> Profile Information</h5>

                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-user text-muted"></i> Username</span>
                        <span class="info-value"><?php echo htmlspecialchars($data['user']->username); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-id-badge text-muted"></i> User ID</span>
                        <span class="info-value"><?php echo $data['user']->user_id; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-shield-alt text-muted"></i> Role</span>
                        <span class="info-value">
                            <span
                                class="badge badge-<?php echo $data['user']->role_name === 'admin' ? 'danger' : 'primary'; ?>">
                                <?php echo ucfirst($data['user']->role_name ?? 'employee'); ?>
                            </span>
                        </span>
                    </div>
                    <?php if (!empty($data['user']->job_title)): ?>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-briefcase text-muted"></i> Job Title</span>
                            <span class="info-value"><?php echo htmlspecialchars($data['user']->job_title); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($data['user']->address)): ?>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-map-marker-alt text-muted"></i> Address</span>
                            <span class="info-value"><?php echo htmlspecialchars($data['user']->address); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($data['user']->birthday) && $data['user']->birthday !== '0000-00-00'): ?>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-birthday-cake text-muted"></i> Birthday</span>
                            <span
                                class="info-value"><?php echo date('F j, Y', strtotime($data['user']->birthday)); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($data['user']->education)): ?>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-graduation-cap text-muted"></i> Education</span>
                            <span class="info-value"><?php echo htmlspecialchars($data['user']->education); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($data['user']->created_at)): ?>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-calendar-plus text-muted"></i> Member Since</span>
                            <span
                                class="info-value"><?php echo date('F j, Y', strtotime($data['user']->created_at)); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($data['user']->last_login) && $data['user']->last_login): ?>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-clock text-muted"></i> Last Login</span>
                            <span
                                class="info-value"><?php echo date('F j, Y g:i A', strtotime($data['user']->last_login)); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-footer profile-actions">
                    <h6 class="mb-3"><i class="fas fa-cogs text-primary"></i> Account Actions</h6>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="<?php echo URLROOT; ?>/users/editProfile" class="btn btn-info btn-block">
                                <i class="fas fa-user-edit"></i> Edit Profile
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="<?php echo URLROOT; ?>/users/changePassword" class="btn btn-warning btn-block">
                                <i class="fas fa-key"></i> Change Password
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="<?php echo URLROOT; ?>/pages/index" class="btn btn-secondary btn-block">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                        <?php if (isAdmin()): ?>
                            <div class="col-md-3 mb-2">
                                <a href="<?php echo URLROOT; ?>/admin" class="btn btn-primary btn-block">
                                    <i class="fas fa-cog"></i> Admin Panel
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>