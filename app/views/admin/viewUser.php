<!-- View User Details page - Admin view of any user's profile -->
<div class="container-fluid theme-container">
    <!-- Page Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <h1 class="mb-0">
                <i class="fas fa-user-circle"></i>
                User Details
            </h1>
            <p class="text-muted mb-0">View user account information and details</p>
        </div>
    </div>

    <!-- Profile Content Row -->
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <!-- Profile Card -->
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white text-center profile-header">
                    <div class="profile-avatar">
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
                        <img src="<?php echo htmlspecialchars($finalAvatarPath); ?>" alt="Profile Picture"
                            class="rounded-circle"
                            style="width: 80px; height: 80px; object-fit: cover; border: 2px solid #fff;"
                            onerror="this.onerror=null; this.src='<?= htmlspecialchars($defaultAvatar) ?>';">
                    </div>
                    <h2 class="mb-0">
                        <?php echo htmlspecialchars($data['user']->name ?? $data['user']->full_name ?? 'Unknown User'); ?>
                    </h2>
                    <p class="mb-0 opacity-75">
                        <span class="badge badge-light">
                            <?php echo ucfirst($data['user']->role_name ?? 'user'); ?>
                        </span>
                    </p>
                </div>

                <div class="card-body profile-body">
                    <?php flash('user_message'); ?>

                    <h5 class="mb-4"><i class="fas fa-info-circle text-primary"></i> User Information</h5>

                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-id-badge text-muted"></i> User ID</span>
                        <span class="info-value"><?php echo $data['user']->user_id; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-link text-muted"></i> Composite ID</span>
                        <span
                            class="info-value"><?php echo htmlspecialchars(($data['user']->source_table ?? 'users') . ':' . ($data['user']->user_id ?? '0')); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-database text-muted"></i> Source Table</span>
                        <span
                            class="info-value"><?php echo htmlspecialchars($data['user']->source_table ?? 'users'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-user text-muted"></i> Username</span>
                        <span
                            class="info-value">@<?php echo htmlspecialchars($data['user']->username ?? $data['user']->user_name ?? 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-envelope text-muted"></i> Email</span>
                        <span class="info-value"><?php echo htmlspecialchars($data['user']->email ?? 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-phone text-muted"></i> Phone</span>
                        <span
                            class="info-value"><?php echo htmlspecialchars($data['user']->phone ?? ($data['user']->contact_info ?? 'N/A')); ?></span>
                    </div>
                    <?php if (!empty($data['user']->contact_person)): ?>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-user-tie text-muted"></i> Contact Person</span>
                            <span class="info-value"><?php echo htmlspecialchars($data['user']->contact_person); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-shield-alt text-muted"></i> Role</span>
                        <span class="info-value">
                            <span
                                class="badge badge-<?php echo $data['user']->role_name === 'admin' ? 'danger' : 'primary'; ?>">
                                <?php echo ucfirst($data['user']->role_name ?? 'user'); ?>
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-users text-muted"></i> Category</span>
                        <span class="info-value">
                            <span class="badge badge-<?php
                            $category = strtolower($data['user']->user_category ?? 'official');
                            echo $category === 'official' ? 'primary' : ($category === 'customer' ? 'success' : 'warning');
                            ?>">
                                <?php echo ucfirst($data['user']->user_category ?? 'Official'); ?>
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-circle text-muted"></i> Status</span>
                        <span class="info-value">
                            <?php
                            $rawStatus = $data['user']->status ?? ($data['user']->is_active ?? '');
                            $isActive = ($rawStatus === 'active' || $rawStatus == 1);
                            ?>
                            <span class="badge badge-<?php echo $isActive ? 'success' : 'danger'; ?>">
                                <?php echo $isActive ? 'Active' : 'Inactive'; ?>
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
                    <!-- Customer specific details -->
                    <?php if (strtolower($data['user']->user_category ?? ($data['user']->source_table ?? 'users')) === 'customer' || strtolower($data['user']->source_table ?? '') === 'customers'): ?>
                        <hr />
                        <h5 class="mb-3"><i class="fas fa-store text-primary"></i> Customer Details</h5>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-money-bill text-muted"></i> Credit Limit</span>
                            <span
                                class="info-value"><?php echo htmlspecialchars($data['user']->credit_limit ?? '0.00'); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-percent text-muted"></i> Discount Type</span>
                            <span
                                class="info-value"><?php echo htmlspecialchars($data['user']->discount_type ?? 'N/A'); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-percentage text-muted"></i> Discount Value</span>
                            <span
                                class="info-value"><?php echo htmlspecialchars($data['user']->discount_value ?? '0'); ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- Contractor specific details -->
                    <?php if (strtolower($data['user']->user_category ?? ($data['user']->source_table ?? '')) === 'contractor' || strtolower($data['user']->source_table ?? '') === 'contractors'): ?>
                        <hr />
                        <h5 class="mb-3"><i class="fas fa-hard-hat text-primary"></i> Contractor Details</h5>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-toolbox text-muted"></i> Specialization</span>
                            <span
                                class="info-value"><?php echo htmlspecialchars($data['user']->specialization ?? 'N/A'); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-coins text-muted"></i> Commission Type</span>
                            <span
                                class="info-value"><?php echo htmlspecialchars($data['user']->commission_type ?? 'N/A'); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-chart-line text-muted"></i> Commission Rate</span>
                            <span
                                class="info-value"><?php echo htmlspecialchars($data['user']->commission_rate ?? '0'); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-wallet text-muted"></i> Total Commission Earned</span>
                            <span
                                class="info-value"><?php echo htmlspecialchars($data['user']->total_commission_earned ?? '0'); ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- Permissions summary (if provided) -->
                    <?php if (!empty($data['permissions']) && is_array($data['permissions'])): ?>
                        <hr />
                        <h5 class="mb-3"><i class="fas fa-lock text-primary"></i> Permissions</h5>
                        <div class="info-row" style="flex-direction:column; align-items:flex-start; gap:8px;">
                            <?php foreach ($data['permissions'] as $module => $perms): ?>
                                <div><strong><?php echo htmlspecialchars($module); ?>:</strong>
                                    <?php echo htmlspecialchars(implode(', ', (array) $perms)); ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Recent activity container (loaded by existing viewActivity JS) -->
                    <hr />
                    <h5 class="mb-3"><i class="fas fa-history text-primary"></i> Recent Activity</h5>
                    <div id="recentActivityContainer">
                        <p class="text-muted">Click <button class="btn btn-sm btn-link p-0"
                                onclick="viewActivity(<?php echo (int) $data['user']->user_id; ?>)">Activity</button> to
                            load recent activity for this user.</p>
                    </div>

                    <!-- Raw JSON dump for debugging / audit -->
                    <hr />
                    <h6 class="mb-2"><i class="fas fa-code text-primary"></i> Raw User Data</h6>
                    <pre
                        style="max-height:240px; overflow:auto; background:#f7f7f9; padding:12px; border-radius:4px;"><?php echo htmlspecialchars(json_encode($data['user'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                    <?php if (isset($data['user']->last_login) && $data['user']->last_login): ?>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-clock text-muted"></i> Last Login</span>
                            <span
                                class="info-value"><?php echo date('F j, Y g:i A', strtotime($data['user']->last_login)); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-footer profile-actions">
                    <h6 class="mb-3"><i class="fas fa-cogs text-primary"></i> Admin Actions</h6>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="<?php echo URLROOT; ?>/admin/editUserProfile/<?= $data['user']->user_id ?>"
                                class="btn btn-primary btn-block">
                                <i class="fas fa-user-edit"></i> Edit Profile
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-outline-info btn-block"
                                onclick="managePermissions(<?= $data['user']->user_id ?>)">
                                <i class="fas fa-shield-alt"></i> Permissions
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-outline-secondary btn-block"
                                onclick="viewActivity(<?= $data['user']->user_id ?>)">
                                <i class="fas fa-history"></i> Activity
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="<?php echo URLROOT; ?>/admin/users" class="btn btn-secondary btn-block">
                                <i class="fas fa-arrow-left"></i> Back to Users
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles for this view have been moved to public/css/app-unified.css per unified CSS policy -->