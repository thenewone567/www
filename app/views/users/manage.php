<?php
$pageTitle = 'User Management';
require_once '../app/views/layout/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><i class="fas fa-users-cog"></i> User Management</h1>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">
                    <i class="fas fa-plus"></i> Add User
                </button>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Users</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="usersTable" width="100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['users']) && !empty($data['users'])): ?>
                                    <?php foreach ($data['users'] as $user): ?>
                                        <tr>
                                            <td><?= $user->user_id ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm rounded-circle bg-secondary text-white mr-2">
                                                        <?= strtoupper(substr($user->name, 0, 1)) ?>
                                                    </div>
                                                    <?= htmlspecialchars($user->name) ?>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($user->email) ?></td>
                                            <td>
                                                <span
                                                    class="badge badge-<?= $user->role_name === 'admin' ? 'danger' : ($user->role_name === 'manager' ? 'warning' : 'info') ?>">
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
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="editUser(<?= $user->user_id ?>)" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-<?= $user->status === 'active' ? 'warning' : 'success' ?>"
                                                        onclick="toggleUserStatus(<?= $user->user_id ?>, '<?= $user->status ?>')"
                                                        title="<?= $user->status === 'active' ? 'Deactivate' : 'Activate' ?>">
                                                        <i
                                                            class="fas fa-<?= $user->status === 'active' ? 'pause' : 'play' ?>"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-info"
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

    <!-- Roles Management -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Roles & Permissions</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal"
                        data-target="#addRoleModal">
                        <i class="fas fa-plus"></i> Add Role
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%">
                            <thead>
                                <tr>
                                    <th>Role</th>
                                    <th>Description</th>
                                    <th>Users</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['roles']) && !empty($data['roles'])): ?>
                                    <?php foreach ($data['roles'] as $role): ?>
                                        <tr>
                                            <td>
                                                <span class="font-weight-bold"><?= ucfirst($role->role_name) ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($role->description) ?></td>
                                            <td>
                                                <span class="badge badge-info"><?= $role->user_count ?? 0 ?> users</span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="editRole(<?= $role->role_id ?>)">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-info"
                                                    onclick="viewPermissions(<?= $role->role_id ?>)">
                                                    <i class="fas fa-shield-alt"></i> Permissions
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
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
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="userName">Full Name *</label>
                        <input type="text" class="form-control" id="userName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="userEmail">Email *</label>
                        <input type="email" class="form-control" id="userEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="userPassword">Password *</label>
                        <input type="password" class="form-control" id="userPassword" name="password" required
                            minlength="6">
                    </div>
                    <div class="form-group">
                        <label for="userRole">Role *</label>
                        <select class="form-control" id="userRole" name="role_id" required>
                            <option value="">Select Role</option>
                            <?php if (isset($data['roles'])): ?>
                                <?php foreach ($data['roles'] as $role): ?>
                                    <option value="<?= $role->role_id ?>"><?= ucfirst($role->role_name) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editUserForm">
                <input type="hidden" id="editUserId" name="user_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editUserName">Full Name *</label>
                        <input type="text" class="form-control" id="editUserName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="editUserEmail">Email *</label>
                        <input type="email" class="form-control" id="editUserEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="editUserRole">Role *</label>
                        <select class="form-control" id="editUserRole" name="role_id" required>
                            <?php if (isset($data['roles'])): ?>
                                <?php foreach ($data['roles'] as $role): ?>
                                    <option value="<?= $role->role_id ?>"><?= ucfirst($role->role_name) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
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
                <h5 class="modal-title">User Activity Log</h5>
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
            "order": [[0, "desc"]]
        });

        // Add user form
        $('#addUserForm').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?= URLROOT ?>/users/add',
                method: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    if (response.success) {
                        alert('User created successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        });

        // Edit user form
        $('#editUserForm').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?= URLROOT ?>/users/update',
                method: 'POST',
                data: $(this).serialize(),
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
    });

    function editUser(userId) {
        $.ajax({
            url: '<?= URLROOT ?>/users/getUser/' + userId,
            method: 'GET',
            success: function (user) {
                $('#editUserId').val(user.user_id);
                $('#editUserName').val(user.name);
                $('#editUserEmail').val(user.email);
                $('#editUserRole').val(user.role_id);
                $('#editUserModal').modal('show');
            }
        });
    }

    function toggleUserStatus(userId, currentStatus) {
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        const action = newStatus === 'active' ? 'activate' : 'deactivate';

        if (confirm(`Are you sure you want to ${action} this user?`)) {
            $.ajax({
                url: '<?= URLROOT ?>/users/toggleStatus',
                method: 'POST',
                data: {
                    user_id: userId,
                    status: newStatus
                },
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
                url: '<?= URLROOT ?>/users/resetPassword',
                method: 'POST',
                data: { user_id: userId },
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
            url: '<?= URLROOT ?>/users/activity/' + userId,
            method: 'GET',
            success: function (data) {
                let html = '<div class="table-responsive"><table class="table table-sm">';
                html += '<thead><tr><th>Date</th><th>Action</th><th>Details</th></tr></thead><tbody>';

                if (data.length > 0) {
                    data.forEach(function (activity) {
                        html += `<tr>
                        <td>${activity.created_at}</td>
                        <td><span class="badge badge-info">${activity.action}</span></td>
                        <td>${activity.details || '-'}</td>
                    </tr>`;
                    });
                } else {
                    html += '<tr><td colspan="3" class="text-center">No activity found</td></tr>';
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