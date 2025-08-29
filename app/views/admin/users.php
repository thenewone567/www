<?php
// Extract data passed from controller
extract($data);

$pageTitle = 'User Management - Admin Panel';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
?>

<!-- Inline styles removed in favor of unified stylesheet: public/css/app-unified.css -->
<!-- Use existing unified classes: .card-theme, .btn-theme, .user-avatar, .permission-group, etc. -->

<div class="admin-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-0">
                    <i class="fas fa-users"></i> User Management
                </h1>
                <p class="mb-0 mt-2 opacity-75">Manage users, roles, and permissions</p>
            </div>
            <div class="col-md-4 text-md-right">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-plus"></i> Add User
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo URLROOT; ?>/admin/addOfficial">
                            <i class="fas fa-user-tie"></i> Add Official/Employee
                        </a>
                        <a class="dropdown-item" href="<?php echo URLROOT; ?>/admin/addCustomer">
                            <i class="fas fa-shopping-cart"></i> Add Customer
                        </a>
                        <a class="dropdown-item" href="<?php echo URLROOT; ?>/admin/addContractor">
                            <i class="fas fa-hard-hat"></i> Add Contractor
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Admin Navigation as action cards -->
    <div class="container-fluid mb-4">
        <div class="row align-items-stretch admin-action-grid">
            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <a class="card-theme d-flex flex-column align-items-center justify-content-center p-3 h-100 text-center nav-link"
                    href="<?= URLROOT ?>/admin" role="button">
                    <div class="text-primary mb-2"><i class="fas fa-tachometer-alt fa-2x"></i></div>
                    <div class="font-weight-bold">Dashboard</div>
                    <small class="text-muted">Overview</small>
                </a>
            </div>

            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <a class="card-theme d-flex flex-column align-items-center justify-content-center p-3 h-100 text-center nav-link active"
                    href="<?= URLROOT ?>/admin/users" role="button">
                    <div class="text-primary mb-2"><i class="fas fa-users fa-2x"></i></div>
                    <div class="font-weight-bold">Users</div>
                    <small class="text-muted">Manage accounts</small>
                </a>
            </div>

            <!-- Roles & Permissions card removed per request -->

            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <a class="card-theme d-flex flex-column align-items-center justify-content-center p-3 h-100 text-center nav-link"
                    href="<?= URLROOT ?>/admin/activityLogs" role="button">
                    <div class="text-muted mb-2"><i class="fas fa-history fa-2x"></i></div>
                    <div class="font-weight-bold">Activity Logs</div>
                    <small class="text-muted">Audit trail</small>
                </a>
            </div>

            <div class="col-6 col-md-4 col-lg-2 mb-3">
                <a class="card-theme d-flex flex-column align-items-center justify-content-center p-3 h-100 text-center nav-link"
                    href="<?= URLROOT ?>/admin/settings" role="button">
                    <div class="text-dark mb-2"><i class="fas fa-cog fa-2x"></i></div>
                    <div class="font-weight-bold">Settings</div>
                    <small class="text-muted">System prefs</small>
                </a>
            </div>
        </div>
    </div>

    <!-- Users Table with Category Filtering -->
    <div class="row">
        <div class="col-12">
            <div class="card-theme">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-users"></i> System Users
                        </h6>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary active" data-filter="all"
                                onclick="filterUsers('all')">
                                <i class="fas fa-users"></i> All Users
                                <span class="badge badge-primary ml-1"
                                    id="count-all"><?= isset($users) ? count($users) : 0 ?></span>
                            </button>
                            <button type="button" class="btn btn-outline-primary" data-filter="official"
                                onclick="filterUsers('official')">
                                <i class="fas fa-user-tie"></i> Officials
                                <span class="badge badge-primary ml-1" id="count-official">0</span>
                            </button>
                            <button type="button" class="btn btn-outline-success" data-filter="customer"
                                onclick="filterUsers('customer')">
                                <i class="fas fa-shopping-cart"></i> Customers
                                <span class="badge badge-success ml-1" id="count-customer">0</span>
                            </button>
                            <button type="button" class="btn btn-outline-warning" data-filter="contractor"
                                onclick="filterUsers('contractor')">
                                <i class="fas fa-hard-hat"></i> Contractors
                                <span class="badge badge-warning ml-1" id="count-contractor">0</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="usersTable" width="100%">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($users) && !empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                        <?php
                                        // Ensure we never pass null to substr() or htmlspecialchars()
                                        $displayName = trim((string) ($user->name ?? ''));
                                        $initialSource = $displayName !== '' ? $displayName : ($user->username ?? 'U');
                                        $initial = strtoupper(substr((string) $initialSource, 0, 1));
                                        $roleNameLower = strtolower((string) ($user->role_name ?? ''));
                                        // Normalize status - supports 'active'/'inactive' or numeric flags (1/0)
                                        // Handle different status field names for different user types
                                        $rawStatus = '';
                                        if (isset($user->status)) {
                                            $rawStatus = $user->status;
                                        } elseif (isset($user->is_active)) {
                                            $rawStatus = $user->is_active;
                                        } elseif (isset($user->customer_status)) {
                                            $rawStatus = $user->customer_status;
                                        }
                                        $isActive = ($rawStatus === 'active' || $rawStatus == 1 || $rawStatus === true);

                                        // Get user category - now properly set from source table
                                        $userCategory = $user->user_category ?? 'official';

                                        // Determine canonical source table for this row. Prefer explicit
                                        // $user->source_table when available; otherwise derive from category.
                                        $sourceTable = strtolower(trim((string) ($user->source_table ?? '')));
                                        if (!in_array($sourceTable, ['users', 'customers', 'contractors'], true)) {
                                            $cat = strtolower((string) $userCategory);
                                            if ($cat === 'customer') {
                                                $sourceTable = 'customers';
                                            } elseif ($cat === 'contractor') {
                                                $sourceTable = 'contractors';
                                            } else {
                                                $sourceTable = 'users';
                                            }
                                        }

                                        ?>
                                        <tr class="user-row" data-category="<?= strtolower($userCategory) ?>"
                                            data-user-id="<?= htmlspecialchars($user->user_id) ?>"
                                            data-source-table="<?= htmlspecialchars($sourceTable) ?>"
                                            data-composite-id="<?= htmlspecialchars($sourceTable . ':' . $user->user_id) ?>">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php
                                                    // Avatar logic - check for user profile picture
                                                    $username = $user->username ?? $user->user_name ?? '';
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
                                                    <div class="user-avatar"
                                                        style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; margin-right: 10px; background: #f8f9fa;">
                                                        <img src="<?= htmlspecialchars($finalAvatarPath) ?>" alt="Avatar"
                                                            style="width: 100%; height: 100%; object-fit: cover;"
                                                            onerror="this.onerror=null; this.src='<?= htmlspecialchars($defaultAvatar) ?>';">
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold">
                                                            <?= htmlspecialchars($displayName ?: ($user->username ?? '')) ?>
                                                        </div>
                                                        <small
                                                            class="text-muted">@<?= htmlspecialchars($user->username ?? $user->user_name ?? 'N/A') ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($user->email ?? '') ?></td>
                                            <td>
                                                <span
                                                    class="badge role-badge badge-<?= $roleNameLower === 'admin' ? 'danger' : ($roleNameLower === 'manager' ? 'warning' : 'info') ?>">
                                                    <?= ucfirst((string) ($user->role_name ?? 'user')) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="d-none"><?= strtolower($userCategory) ?></span>
                                                <?php
                                                // Use a stable per-row source table variable for actions.
                                                // Avoid overwriting the canonical $sourceTable used above.
                                                $rowSourceTable = $sourceTable;
                                                $canChangeCategory = ($rowSourceTable === 'users'); // Only users from users table can change category
                                                ?>

                                                <?php if ($canChangeCategory): ?>
                                                    <!-- Editable category selector for users table -->
                                                    <div class="btn-group btn-group-sm category-selector" role="group">
                                                        <button type="button"
                                                            class="btn btn-sm category-btn <?= strtolower($userCategory) === 'official' ? 'btn-primary' : 'btn-outline-primary' ?>"
                                                            data-category="official" data-user-id="<?= $user->user_id ?>"
                                                            onclick="changeUserCategory(<?= $user->user_id ?>, 'official')"
                                                            title="Official">
                                                            <i class="fas fa-user-tie"></i>
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn-sm category-btn <?= strtolower($userCategory) === 'customer' ? 'btn-success' : 'btn-outline-success' ?>"
                                                            data-category="customer" data-user-id="<?= $user->user_id ?>"
                                                            onclick="changeUserCategory(<?= $user->user_id ?>, 'customer')"
                                                            title="Customer">
                                                            <i class="fas fa-shopping-cart"></i>
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn-sm category-btn <?= strtolower($userCategory) === 'contractor' ? 'btn-warning' : 'btn-outline-warning' ?>"
                                                            data-category="contractor" data-user-id="<?= $user->user_id ?>"
                                                            onclick="changeUserCategory(<?= $user->user_id ?>, 'contractor')"
                                                            title="Contractor">
                                                            <i class="fas fa-hard-hat"></i>
                                                        </button>
                                                    </div>
                                                <?php else: ?>
                                                    <!-- Read-only category display for customers/contractors -->
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <span
                                                            class="btn btn-sm <?= strtolower($userCategory) === 'official' ? 'btn-primary' : 'btn-outline-primary' ?>"
                                                            title="Official">
                                                            <i class="fas fa-user-tie"></i>
                                                        </span>
                                                        <span
                                                            class="btn btn-sm <?= strtolower($userCategory) === 'customer' ? 'btn-success' : 'btn-outline-success' ?>"
                                                            title="Customer">
                                                            <i class="fas fa-shopping-cart"></i>
                                                        </span>
                                                        <span
                                                            class="btn btn-sm <?= strtolower($userCategory) === 'contractor' ? 'btn-warning' : 'btn-outline-warning' ?>"
                                                            title="Contractor">
                                                            <i class="fas fa-hard-hat"></i>
                                                        </span>
                                                    </div>
                                                    <small class="text-muted d-block">From <?= ucfirst($sourceTable) ?>
                                                        table</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= $isActive ? 'success' : 'secondary' ?>">
                                                    <?= $isActive ? 'Active' : 'Inactive' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $lastLogin = isset($user->last_login) ? trim((string) $user->last_login) : '';
                                                if ($lastLogin !== '') {
                                                    $ts = strtotime($lastLogin);
                                                    echo $ts ? date('M j, Y g:i A', $ts) : 'Invalid date';
                                                } else {
                                                    echo 'Never';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $createdAt = isset($user->created_at) ? trim((string) $user->created_at) : '';
                                                if ($createdAt !== '') {
                                                    $ts2 = strtotime($createdAt);
                                                    echo $ts2 ? date('M j, Y', $ts2) : 'Unknown';
                                                } else {
                                                    echo 'Unknown';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-success"
                                                        onclick="viewUserProfile(<?= $user->user_id ?>)" title="View Profile">
                                                        <i class="fas fa-user"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="editUser(<?= htmlspecialchars($user->user_id) ?>, '<?= htmlspecialchars($rowSourceTable) ?>')"
                                                        title="Edit User">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-info"
                                                        onclick="managePermissions(<?= $user->user_id ?>)"
                                                        title="Manage Permissions">
                                                        <i class="fas fa-shield-alt"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm <?= $isActive ? 'btn-warning' : 'btn-outline-success' ?> toggle-status-btn"
                                                        data-user-id="<?= htmlspecialchars($user->user_id) ?>"
                                                        data-source-table="<?= htmlspecialchars($rowSourceTable) ?>"
                                                        data-current-status="<?= $isActive ? 'active' : 'inactive' ?>"
                                                        title="<?= $isActive ? 'Deactivate' : 'Activate' ?>">
                                                        <i class="fas fa-<?= $isActive ? 'pause' : 'play' ?>"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        onclick="viewActivity(<?= $user->user_id ?>)" title="View Activity">
                                                        <i class="fas fa-history"></i>
                                                    </button>
                                                    <?php if ($user->user_id != $_SESSION['user_id']): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="resetPassword(<?= $user->user_id ?>)" title="Reset Password">
                                                            <i class="fas fa-key"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No users found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus"></i> Add New User
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="userName" class="form-label">Full Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="userName" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="userUsername" class="form-label">Username <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="userUsername" name="username" required>
                                <small class="form-text text-muted">Unique username for login</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="userEmail" class="form-label">Email Address <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="userEmail" name="email" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="userRole" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-control" id="userRole" name="role_id" required>
                                    <option value="">Select Role</option>
                                    <?php if (isset($roles) && !empty($roles)): ?>
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?= $role->role_id ?>"><?= ucfirst($role->role_name) ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>No roles available</option>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="userPassword" class="form-label">Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="userPassword" name="password"
                                        required minlength="6">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Minimum 6 characters</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="userStatus" class="form-label">Status</label>
                                <select class="form-control" id="userStatus" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!-- User Category Preview -->
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">User Category</label>
                                <div class="alert alert-info" id="categoryPreview">
                                    <i class="fas fa-info-circle"></i>
                                    <span id="categoryText">Select a role to see the user category</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Edit User
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editUserForm">
                <input type="hidden" id="editUserId" name="user_id">
                <input type="hidden" id="editUserSourceTable" name="source_table">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editUserName" class="form-label">Full Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editUserName" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editUserUsername" class="form-label">Username <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editUserUsername" name="username" required>
                                <small class="form-text text-muted">Unique username for login</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editUserEmail" class="form-label">Email Address <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="editUserEmail" name="email" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editUserRole" class="form-label">Role <span
                                        class="text-danger">*</span></label>
                                <select class="form-control" id="editUserRole" name="role_id" required>
                                    <?php if (isset($roles) && !empty($roles)): ?>
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?= $role->role_id ?>"><?= ucfirst($role->role_name) ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>No roles available</option>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editUserStatus" class="form-label">Status</label>
                                <select class="form-control" id="editUserStatus" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editUserPassword" class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="editUserPassword" name="password"
                                        minlength="6">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" id="toggleEditPassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Leave blank to keep current password</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- User Permissions Modal -->
<div class="modal fade" id="permissionsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-shield-alt"></i> Manage User Permissions
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="permissionsForm">
                <input type="hidden" id="permissionsUserId" name="user_id">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> These permissions will override the role-based permissions for this
                        specific user.
                    </div>

                    <div id="permissionsContainer">
                        <!-- Permissions checkboxes will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-theme btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-theme btn-primary-theme">
                        <i class="fas fa-save"></i> Save Permissions
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- User Activity Modal -->
<div class="modal fade" id="activityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-history"></i> User Activity Log
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="activityContent">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p>Loading activity...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Required scripts for users page: jQuery and DataTables (loaded before inline JS) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>

<script>
    let currentFilter = 'all';

    $(document).ready(function () {
        // Initialize DataTable with updated column count and better error handling
        $('#usersTable').DataTable({
            "pageLength": 25,
            "order": [[0, "asc"]],
            "columnDefs": [
                { "orderable": false, "targets": [3, 7] }, // Category and Actions columns
                { "defaultContent": "", "targets": "_all" } // Provide default content for missing data
            ],
            "language": {
                "emptyTable": "No users found",
                "info": "Showing _START_ to _END_ of _TOTAL_ users",
                "infoEmpty": "Showing 0 to 0 of 0 users",
                "lengthMenu": "Show _MENU_ users per page",
                "search": "Search users:",
                "zeroRecords": "No matching users found"
            },
            "initComplete": function (settings, json) {
                // Ensure counts are initialized once when DataTable is ready
                updateCategoryCounts();
                // Apply filter button styles for default filter
                filterUsers('all');
            },
            "drawCallback": function () {
                // Update category counts after table is drawn (paging, filtering)
                updateCategoryCounts();
            },
            "errorCallback": function (settings, techNote, message) {
                console.error('DataTables error:', {
                    settings: settings,
                    techNote: techNote,
                    message: message
                });
                alert('DataTables Error: ' + message + '\nPlease refresh the page.');
            }
        });

        // Initialize category counts after a brief delay to ensure DataTable is ready
        setTimeout(function () {
            updateCategoryCounts();
        }, 300);

        // Add user form with improved error handling and validation
        $('#addUserForm').submit(function (e) {
            e.preventDefault();

            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            // Client-side validation
            let hasErrors = false;
            const name = $('#userName').val().trim();
            const username = $('#userUsername').val().trim();
            const email = $('#userEmail').val().trim();
            const password = $('#userPassword').val();
            const roleId = $('#userRole').val();

            // Validate required fields
            if (!name) {
                $('#userName').addClass('is-invalid');
                $('#userName').siblings('.invalid-feedback').text('Full name is required');
                hasErrors = true;
            }

            if (!username) {
                $('#userUsername').addClass('is-invalid');
                $('#userUsername').siblings('.invalid-feedback').text('Username is required');
                hasErrors = true;
            } else if (username.length < 3) {
                $('#userUsername').addClass('is-invalid');
                $('#userUsername').siblings('.invalid-feedback').text('Username must be at least 3 characters');
                hasErrors = true;
            }

            if (!email) {
                $('#userEmail').addClass('is-invalid');
                $('#userEmail').siblings('.invalid-feedback').text('Email is required');
                hasErrors = true;
            } else if (!isValidEmail(email)) {
                $('#userEmail').addClass('is-invalid');
                $('#userEmail').siblings('.invalid-feedback').text('Please enter a valid email address');
                hasErrors = true;
            }

            if (!password) {
                $('#userPassword').addClass('is-invalid');
                $('#userPassword').siblings('.invalid-feedback').text('Password is required');
                hasErrors = true;
            } else if (password.length < 6) {
                $('#userPassword').addClass('is-invalid');
                $('#userPassword').siblings('.invalid-feedback').text('Password must be at least 6 characters');
                hasErrors = true;
            }

            if (!roleId) {
                $('#userRole').addClass('is-invalid');
                $('#userRole').siblings('.invalid-feedback').text('Role is required');
                hasErrors = true;
            }

            if (hasErrors) {
                return;
            }

            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Creating User...').prop('disabled', true);

            console.log('Initiating user creation...');

            $.ajax({
                url: '<?= URLROOT ?>/admin/addUser',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                timeout: 30000,
                success: function (response) {
                    console.log('Server response:', response);

                    if (response.success) {
                        console.log('User creation successful!', response);

                        // Show success message
                        showSuccessMessage('User "' + response.user.name + '" created successfully!');

                        // Reset form and close modal
                        $('#addUserForm')[0].reset();
                        $('#addUserModal').modal('hide');

                        // Refresh the page to show new user
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    } else {
                        console.log('User creation failed:', response.message);

                        // Show error message
                        showErrorMessage('Failed to create user: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText,
                        statusCode: xhr.status
                    });

                    let errorMessage = 'Failed to create user. ';

                    if (xhr.status === 0) {
                        errorMessage += 'Network connection failed.';
                    } else if (xhr.status === 404) {
                        errorMessage += 'Admin endpoint not found.';
                    } else if (xhr.status === 500) {
                        errorMessage += 'Server error occurred.';
                    } else {
                        errorMessage += 'Status: ' + xhr.status;
                    }

                    // Try to parse error response
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        // Use default error message
                    }

                    showErrorMessage(errorMessage);
                },
                complete: function () {
                    // Restore button state
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        });

        // Password visibility toggle
        $('#togglePassword').click(function () {
            const passwordField = $('#userPassword');
            const icon = $(this).find('i');

            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        $('#toggleEditPassword').click(function () {
            const passwordField = $('#editUserPassword');
            const icon = $(this).find('i');

            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Role selection preview
        $('#userRole').change(function () {
            const selectedRole = $(this).find('option:selected').text();
            const categoryPreview = $('#categoryText');

            if (selectedRole && selectedRole !== 'Select Role') {
                const roleName = selectedRole.toLowerCase();
                let category = 'Official';
                let badgeClass = 'badge-primary';

                if (roleName.includes('customer') || roleName.includes('client')) {
                    category = 'Customer';
                    badgeClass = 'badge-success';
                } else if (roleName.includes('contractor') || roleName.includes('vendor')) {
                    category = 'Contractor';
                    badgeClass = 'badge-warning';
                }

                categoryPreview.html(`This user will be categorized as: <span class="badge ${badgeClass}">${category}</span>`);
            } else {
                categoryPreview.text('Select a role to see the user category');
            }
        });

        // Email validation function
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Success message function
        function showSuccessMessage(message) {
            const alertHtml = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> ${message}
                    <button type="button" class="close" data-dismiss="alert">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
            $('.content-header').after(alertHtml);

            // Auto-dismiss after 5 seconds
            setTimeout(function () {
                $('.alert-success').fadeOut();
            }, 5000);
        }

        // Error message function
        function showErrorMessage(message) {
            const alertHtml = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> ${message}
                    <button type="button" class="close" data-dismiss="alert">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
            $('.content-header').after(alertHtml);

            // Auto-dismiss after 7 seconds
            setTimeout(function () {
                $('.alert-danger').fadeOut();
            }, 7000);
        }

        // Delegated click handler for status toggle buttons (works with DataTable redraws)
        $(document).on('click', '.toggle-status-btn', function (e) {
            e.preventDefault();
            const userId = $(this).data('user-id');
            const sourceTable = $(this).data('source-table');
            const compositeId = $(this).data('composite-id');
            const currentStatus = $(this).data('current-status');
            toggleUserStatus(userId, currentStatus, sourceTable, compositeId);
        });

        // Edit user form
        $('#editUserForm').submit(function (e) {
            e.preventDefault();
            const userId = $('#editUserId').val();
            const sourceTable = $('#editUserSourceTable').val();
            console.log('Initiating submission...');
            $.ajax({
                url: '<?= URLROOT ?>/admin/editUser/' + userId + '?source=' + sourceTable,
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    console.log('Submission successful!', response);
                    if (response.success) {
                        alert('User updated successfully!');
                        location.reload();
                    } else {
                        alert('Submission failed! Details: ' + (response.message || 'Unknown'));
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Edit user error:', error);
                    alert('Submission failed! Details: ' + error);
                }
            });
        });

        // Permissions form
        $('#permissionsForm').submit(function (e) {
            e.preventDefault();
            const userId = $('#permissionsUserId').val();
            console.log('Initiating submission...');
            $.ajax({
                url: '<?= URLROOT ?>/admin/updateUserPermissions/' + userId,
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    console.log('Submission successful!', response);
                    if (response.success) {
                        alert('Permissions updated successfully!');
                        $('#permissionsModal').modal('hide');
                    } else {
                        alert('Submission failed! Details: ' + (response.message || 'Unknown'));
                    }
                }
            });
        });
    });

    function viewUserProfile(userId) {
        if (userId) {
            // Navigate to user details page in the same tab
            window.location.href = '<?= URLROOT ?>/admin/viewUser/' + encodeURIComponent(userId);
        } else {
            alert('User ID not available for this user');
        }
    }

    function editUser(userId, sourceTable = 'users') {
        $.ajax({
            url: '<?= URLROOT ?>/admin/editUser/' + userId + '?source=' + sourceTable,
            method: 'GET',
            dataType: 'json',
            success: function (user) {
                $('#editUserId').val(user.user_id);
                $('#editUserSourceTable').val(sourceTable);
                $('#editUserName').val(user.name);
                $('#editUserUsername').val(user.username);
                $('#editUserEmail').val(user.email);
                $('#editUserRole').val(user.role_id);
                $('#editUserStatus').val(user.status);
                $('#editUserModal').modal('show');
            },
            error: function (xhr, status, error) {
                alert('Error loading user data: ' + error);
            }
        });
    }

    function managePermissions(userId) {
        $('#permissionsUserId').val(userId);

        // Load permissions checkboxes
        const permissions = {
            'users': { label: 'User Management', permissions: ['create', 'read', 'update', 'delete'] },
            'sales': { label: 'Sales', permissions: ['create', 'read', 'update', 'delete'] },
            'purchases': { label: 'Purchases', permissions: ['create', 'read', 'update', 'delete', 'approve'] },
            'inventory': { label: 'Inventory', permissions: ['create', 'read', 'update', 'delete'] },
            'customers': { label: 'Customers', permissions: ['create', 'read', 'update', 'delete'] },
            'suppliers': { label: 'Suppliers', permissions: ['create', 'read', 'update', 'delete'] },
            'reports': { label: 'Reports', permissions: ['read', 'export'] },
            'settings': { label: 'Settings', permissions: ['read', 'update'] },
            'admin': { label: 'Admin Panel', permissions: ['access'] }
        };

        let html = '';
        for (const [module, config] of Object.entries(permissions)) {
            html += `
            <div class="permission-group">
                <h6><i class="fas fa-folder"></i> ${config.label}</h6>
                <div class="row">
        `;

            config.permissions.forEach(permission => {
                html += `
                <div class="col-md-3">
                    <div class="form-check permission-checkbox">
                        <input class="form-check-input" type="checkbox" 
                               name="permissions[${module}][]" 
                               value="${permission}" 
                               id="perm_${module}_${permission}">
                        <label class="form-check-label" for="perm_${module}_${permission}">
                            ${permission.charAt(0).toUpperCase() + permission.slice(1)}
                        </label>
                    </div>
                </div>
            `;
            });

            html += `
                </div>
            </div>
        `;
        }

        $('#permissionsContainer').html(html);
        $('#permissionsModal').modal('show');
    }

    function toggleUserStatus(userId, currentStatus, sourceTable = 'users', compositeId = null) {
        // Accept numeric flags (1/0) or string ('active'/'inactive')
        const normalized = ('' + currentStatus).trim();
        const isActive = normalized === '1' || normalized.toLowerCase() === 'active';
        const newStatus = isActive ? 'inactive' : 'active';
        const action = newStatus === 'active' ? 'activate' : 'deactivate';

        console.log('Toggle Debug:', {
            userId: userId,
            sourceTable: sourceTable,
            currentStatus: currentStatus,
            normalized: normalized,
            isActive: isActive,
            newStatus: newStatus,
            action: action
        });

        if (confirm(`Are you sure you want to ${action} this user?`)) {
            console.log('Initiating submission...');
            const payload = {
                user_id: userId,
                source_table: sourceTable,
                status: newStatus
            };
            // Prefer sending composite_id for unambiguous server-side parsing
            if (compositeId) payload.composite_id = compositeId;

            $.ajax({
                url: '<?= URLROOT ?>/admin/toggleUserStatus',
                method: 'POST',
                data: payload,
                dataType: 'json',
                success: function (response) {
                    console.log('Submission successful!', response);
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Submission failed! Details: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function (xhr, status, error) {
                    console.log('AJAX Error:', { xhr: xhr, status: status, error: error });
                    console.log('Response Text:', xhr.responseText);
                    alert('Submission failed! AJAX Error: ' + error);
                }
            });
        }
    }

    function resetPassword(userId) {
        if (confirm('Are you sure you want to reset this user\'s password? A new temporary password will be generated.')) {
            console.log('Initiating submission...');
            $.ajax({
                url: '<?= URLROOT ?>/admin/resetPassword',
                method: 'POST',
                data: { user_id: userId },
                dataType: 'json',
                success: function (response) {
                    console.log('Submission successful!', response);
                    if (response.success) {
                        alert('Password reset successfully! New password: ' + response.new_password);
                    } else {
                        alert('Submission failed! Details: ' + (response.message || 'Unknown'));
                    }
                }
            });
        }
    }

    function viewActivity(userId) {
        $('#activityModal').modal('show');
        $.ajax({
            url: '<?= URLROOT ?>/admin/getUserActivity/' + userId,
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                let html = '<div class="table-responsive"><table class="table table-sm">';
                html += '<thead><tr><th>Date</th><th>Action</th><th>Details</th><th>IP Address</th></tr></thead><tbody>';

                if (data.length > 0) {
                    data.forEach(function (activity) {
                        html += `<tr>
                        <td>${activity.created_at}</td>
                        <td><span class="badge badge-info">${activity.action}</span></td>
                        <td>${activity.details || '-'}</td>
                        <td><small class="text-muted">${activity.ip_address || '-'}</small></td>
                    </tr>`;
                    });
                } else {
                    html += '<tr><td colspan="4" class="text-center">No activity found</td></tr>';
                }

                html += '</tbody></table></div>';
                $('#activityContent').html(html);
            }
        });
    }

    // Category filtering functionality
    function filterUsers(category) {
        currentFilter = category;
        const table = $('#usersTable').DataTable();

        // Update button states
        // Style filter buttons: filled for active, outline for others
        $('.btn-group[role="group"] button[data-filter]').each(function () {
            const f = $(this).data('filter');
            // Reset to outline based on filter
            if (f === 'all' || f === 'official') {
                $(this).removeClass('btn-primary').addClass('btn-outline-primary');
            } else if (f === 'customer') {
                $(this).removeClass('btn-success').addClass('btn-outline-success');
            } else if (f === 'contractor') {
                $(this).removeClass('btn-warning').addClass('btn-outline-warning');
            }
        });
        // Activate selected button with filled color
        const activeBtn = $(`button[data-filter="${category}"]`);
        if (category === 'all' || category === 'official') {
            activeBtn.removeClass('btn-outline-primary').addClass('btn-primary');
        } else if (category === 'customer') {
            activeBtn.removeClass('btn-outline-success').addClass('btn-success');
        } else if (category === 'contractor') {
            activeBtn.removeClass('btn-outline-warning').addClass('btn-warning');
        }

        // Search lowercase category text from hidden span
        const searchTerm = category === 'all' ? '' : category.toLowerCase();
        table.column(3).search(searchTerm).draw();
    }

    // Change user category
    function changeUserCategory(userId, newCategory) {
        console.log('Initiating submission...');
        $.ajax({
            url: '<?= URLROOT ?>/admin/saveUserCategories',
            method: 'POST',
            data: {
                categories: JSON.stringify({ [userId]: newCategory })
            },
            dataType: 'json',
            success: function (response) {
                console.log('Submission successful!', response);
                if (response.success) {
                    // Update the row's data attribute
                    const row = $(`.user-row[data-user-id="${userId}"]`);
                    row.attr('data-category', newCategory);

                    // Update button states in that row
                    const categorySelector = row.find('.category-selector');
                    categorySelector.find('.category-btn').each(function () {
                        const btnCategory = $(this).data('category');
                        $(this).removeClass('btn-primary btn-success btn-warning')
                            .addClass(`btn-outline-${getColorForCategory(btnCategory)}`);

                        if (btnCategory === newCategory) {
                            $(this).removeClass(`btn-outline-${getColorForCategory(btnCategory)}`)
                                .addClass(`btn-${getColorForCategory(btnCategory)}`);
                        }
                    });

                    // Update counts
                    updateCategoryCounts();

                    showMessage('User category updated successfully!', 'success');
                } else {
                    showMessage('Submission failed! Details: ' + (response.message || 'Unknown'), 'danger');
                }
            },
            error: function (xhr, status, error) {
                console.log('Submission failed!', error);
                showMessage('Submission failed! Details: ' + error, 'danger');
            }
        });
    }

    // Update category counts
    function updateCategoryCounts() {
        const counts = { all: 0, official: 0, customer: 0, contractor: 0 };

        // Get DataTable instance
        try {
            const table = $('#usersTable').DataTable();
            console.log('updateCategoryCounts: DataTable instance', table);

            // Count all rows (not just visible ones)
            table.rows().every(function () {
                const row = this.node();
                let category = $(row).data('category');
                // Fallback: hidden span or cell text
                if (!category || category === '') {
                    const span = $(row).find('td').eq(3).find('span.d-none').first();
                    if (span && span.length) category = span.text();
                }
                if (!category || category === '') {
                    // Try raw cell text
                    category = $(row).find('td').eq(3).text();
                }
                category = ('' + (category || '')).toLowerCase().trim();
                console.log('Row category:', category);
                counts.all++;
                if (counts.hasOwnProperty(category)) {
                    counts[category]++;
                }
            });
        } catch (e) {
            console.error('DataTable error in updateCategoryCounts:', e);
            // Fallback to jQuery selector
            $('.user-row').each(function () {
                let category = $(this).data('category');
                if (!category || category === '') {
                    const span = $(this).find('td').eq(3).find('span.d-none').first();
                    if (span && span.length) category = span.text();
                }
                if (!category || category === '') {
                    category = $(this).find('td').eq(3).text();
                }
                category = ('' + (category || '')).toLowerCase().trim();
                console.log('Fallback - Row category:', category);
                counts.all++;
                if (counts.hasOwnProperty(category)) {
                    counts[category]++;
                }
            });
        }

        console.log('Final counts:', counts);

        $('#count-all').text(counts.all);
        $('#count-official').text(counts.official);
        $('#count-customer').text(counts.customer);
        $('#count-contractor').text(counts.contractor);

        // Debug visual removed; use console logs for diagnostics
    }

    // Get color class for category
    function getColorForCategory(category) {
        const colors = {
            'official': 'primary',
            'customer': 'success',
            'contractor': 'warning'
        };
        return colors[category] || 'primary';
    }

    // Show message notification
    function showMessage(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">${message}<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>`;
        // Append to top of container so it is visible and styled by unified CSS
        $('.container-fluid').first().prepend(alertHtml);

        // Auto-dismiss after 3 seconds
        setTimeout(function () {
            $('.alert').fadeOut(500, function () {
                $(this).remove();
            });
        }, 3000);
    }
</script>




</div> <!-- End container-fluid -->
</div> <!-- End page-content-wrapper -->
</div> <!-- End wrapper -->

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
    crossorigin="anonymous"></script>
<script src="<?php echo URLROOT; ?>/js/main.js"></script>
</body>

</html>