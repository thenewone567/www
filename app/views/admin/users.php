<?php
$pageTitle = 'User Management - Admin Panel';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
?>

<style>
    .admin-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }

    .admin-nav {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .admin-nav .nav-link {
        color: #495057;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        margin: 0 0.25rem;
        border-radius: 5px;
        transition: all 0.2s;
    }

    .admin-nav .nav-link:hover {
        background: #e9ecef;
        color: #007bff;
    }

    .admin-nav .nav-link.active {
        background: #007bff;
        color: white;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
        margin-right: 10px;
    }

    .permission-group {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        background: #f8f9fa;
    }

    .permission-group h6 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }

    .permission-checkbox {
        margin-right: 1rem;
        margin-bottom: 0.5rem;
    }

    .role-badge {
        font-size: 0.875rem;
    }
</style>

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
                <button type="button" class="btn btn-light" data-toggle="modal" data-target="#addUserModal">
                    <i class="fas fa-plus"></i> Add New User
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Admin Navigation -->
    <div class="admin-nav">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link" href="<?= URLROOT ?>/admin">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="<?= URLROOT ?>/admin/users">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= URLROOT ?>/admin/roles">
                    <i class="fas fa-user-tag"></i> Roles & Permissions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= URLROOT ?>/admin/activityLogs">
                    <i class="fas fa-history"></i> Activity Logs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= URLROOT ?>/admin/settings">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </li>
        </ul>
    </div>

    <!-- Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card-theme">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users"></i> System Users
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="usersTable" width="100%">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['users']) && !empty($data['users'])): ?>
                                    <?php foreach ($data['users'] as $user): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="user-avatar bg-<?= $user->role_name === 'admin' ? 'danger' : ($user->role_name === 'manager' ? 'warning' : 'primary') ?>">
                                                        <?= strtoupper(substr($user->name, 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold"><?= htmlspecialchars($user->name) ?></div>
                                                        <small
                                                            class="text-muted">@<?= htmlspecialchars($user->username ?? $user->user_name ?? 'N/A') ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($user->email) ?></td>
                                            <td>
                                                <span
                                                    class="badge role-badge badge-<?= $user->role_name === 'admin' ? 'danger' : ($user->role_name === 'manager' ? 'warning' : 'info') ?>">
                                                    <?= ucfirst($user->role_name) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-<?= $user->status === 'active' ? 'success' : ($user->status === 'inactive' ? 'secondary' : 'danger') ?>">
                                                    <?= ucfirst($user->status) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= $user->last_login ? date('M j, Y g:i A', strtotime($user->last_login)) : 'Never' ?>
                                            </td>
                                            <td>
                                                <?= date('M j, Y', strtotime($user->created_at)) ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="editUser(<?= $user->user_id ?>)" title="Edit User">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-info"
                                                        onclick="managePermissions(<?= $user->user_id ?>)"
                                                        title="Manage Permissions">
                                                        <i class="fas fa-shield-alt"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-<?= $user->status === 'active' ? 'warning' : 'success' ?>"
                                                        onclick="toggleUserStatus(<?= $user->user_id ?>, '<?= $user->status ?>')"
                                                        title="<?= $user->status === 'active' ? 'Deactivate' : 'Activate' ?>">
                                                        <i
                                                            class="fas fa-<?= $user->status === 'active' ? 'pause' : 'play' ?>"></i>
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
                                        <td colspan="7" class="text-center">No users found</td>
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
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus"></i> Add New User
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="userName">Full Name *</label>
                                <input type="text" class="form-theme" id="userName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="userUsername">Username *</label>
                                <input type="text" class="form-theme" id="userUsername" name="username" required>
                                <small class="form-text text-muted">Unique username for login</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="userEmail">Email Address *</label>
                                <input type="email" class="form-theme" id="userEmail" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="userRole">Role *</label>
                                <select class="form-theme" id="userRole" name="role_id" required>
                                    <option value="">Select Role</option>
                                    <?php if (isset($data['roles'])): ?>
                                        <?php foreach ($data['roles'] as $role): ?>
                                            <option value="<?= $role->role_id ?>"><?= ucfirst($role->role_name) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="userPassword">Password *</label>
                                <input type="password" class="form-theme" id="userPassword" name="password" required
                                    minlength="6">
                                <small class="form-text text-muted">Minimum 6 characters</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="userStatus">Status</label>
                                <select class="form-theme" id="userStatus" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-theme btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-theme btn-primary-theme">
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
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Edit User
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editUserForm">
                <input type="hidden" id="editUserId" name="user_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editUserName">Full Name *</label>
                                <input type="text" class="form-theme" id="editUserName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editUserUsername">Username *</label>
                                <input type="text" class="form-theme" id="editUserUsername" name="username" required>
                                <small class="form-text text-muted">Unique username for login</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editUserEmail">Email Address *</label>
                                <input type="email" class="form-theme" id="editUserEmail" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editUserRole">Role *</label>
                                <select class="form-theme" id="editUserRole" name="role_id" required>
                                    <?php if (isset($data['roles'])): ?>
                                        <?php foreach ($data['roles'] as $role): ?>
                                            <option value="<?= $role->role_id ?>"><?= ucfirst($role->role_name) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editUserStatus">Status</label>
                                <select class="form-theme" id="editUserStatus" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-theme btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-theme btn-primary-theme">
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

<script>
    $(document).ready(function () {
        $('#usersTable').DataTable({
            "pageLength": 25,
            "order": [[0, "asc"]],
            "columnDefs": [
                { "orderable": false, "targets": 6 } // Actions column
            ]
        });

        // Add user form with improved error handling
        $('#addUserForm').submit(function (e) {
            e.preventDefault();

            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Creating...').prop('disabled', true);

            $.ajax({
                url: '<?= URLROOT ?>/admin/addUser',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                timeout: 30000, // 30 seconds timeout
                success: function (response) {
                    console.log('Server response:', response);
                    if (response.success) {
                        alert('User created successfully!');
                        $('#addUserModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Unknown error occurred'));
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText,
                        statusCode: xhr.status
                    });

                    let errorMessage = 'An error occurred. ';
                    if (xhr.status === 0) {
                        errorMessage += 'Network connection failed.';
                    } else if (xhr.status === 404) {
                        errorMessage += 'Admin endpoint not found.';
                    } else if (xhr.status === 500) {
                        errorMessage += 'Server error occurred.';
                    } else {
                        errorMessage += 'Status: ' + xhr.status;
                    }

                    alert(errorMessage + '\n\nCheck browser console for details.');
                },
                complete: function () {
                    // Restore button state
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        });

        // Edit user form
        $('#editUserForm').submit(function (e) {
            e.preventDefault();
            const userId = $('#editUserId').val();
            $.ajax({
                url: '<?= URLROOT ?>/admin/editUser/' + userId,
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('User updated successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        });

        // Permissions form
        $('#permissionsForm').submit(function (e) {
            e.preventDefault();
            const userId = $('#permissionsUserId').val();
            $.ajax({
                url: '<?= URLROOT ?>/admin/updateUserPermissions/' + userId,
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Permissions updated successfully!');
                        $('#permissionsModal').modal('hide');
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        });
    });

    function editUser(userId) {
        $.ajax({
            url: '<?= URLROOT ?>/admin/editUser/' + userId,
            method: 'GET',
            dataType: 'json',
            success: function (user) {
                $('#editUserId').val(user.user_id);
                $('#editUserName').val(user.name);
                $('#editUserUsername').val(user.username);
                $('#editUserEmail').val(user.email);
                $('#editUserRole').val(user.role_id);
                $('#editUserStatus').val(user.status);
                $('#editUserModal').modal('show');
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

    function toggleUserStatus(userId, currentStatus) {
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        const action = newStatus === 'active' ? 'activate' : 'deactivate';

        if (confirm(`Are you sure you want to ${action} this user?`)) {
            $.ajax({
                url: '<?= URLROOT ?>/admin/toggleUserStatus',
                method: 'POST',
                data: {
                    user_id: userId,
                    status: newStatus
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        }
    }

    function resetPassword(userId) {
        if (confirm('Are you sure you want to reset this user\'s password? A new temporary password will be generated.')) {
            $.ajax({
                url: '<?= URLROOT ?>/admin/resetPassword',
                method: 'POST',
                data: { user_id: userId },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Password reset successfully! New password: ' + response.new_password);
                    } else {
                        alert('Error: ' + response.message);
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
</script>


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