<?php
$pageTitle = 'Roles & Permissions - Admin Panel';
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
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #dee2e6;
    }

    .permission-checkbox {
        margin-bottom: 0.5rem;
    }

    .role-card {
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .role-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .role-badge {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }

    .permission-badge {
        margin: 0.2rem;
        font-size: 0.75rem;
    }

    .select-all-group {
        background: #e3f2fd;
        border: 1px solid #90caf9;
        border-radius: 5px;
        padding: 0.5rem 1rem;
        margin-bottom: 0.5rem;
    }
</style>

<div class="admin-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-0">
                    <i class="fas fa-user-tag"></i> Roles & Permissions
                </h1>
                <p class="mb-0 mt-2 opacity-75">Manage system roles and their permissions</p>
            </div>
            <div class="col-md-4 text-md-right">
                <button type="button" class="btn btn-light" data-toggle="modal" data-target="#addRoleModal">
                    <i class="fas fa-plus"></i> Add New Role
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
                <a class="nav-link" href="<?php echo URLROOT; ?>/admin">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo URLROOT; ?>/admin/users">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="<?php echo URLROOT; ?>/admin/roles">
                    <i class="fas fa-user-tag"></i> Roles & Permissions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo URLROOT; ?>/admin/activityLogs">
                    <i class="fas fa-history"></i> Activity Logs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo URLROOT; ?>/admin/settings">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </li>
        </ul>
    </div>

    <!-- Roles Overview Cards -->
    <div class="row mb-4">
        <?php if (isset($data['roles']) && !empty($data['roles'])): ?>
            <?php foreach ($data['roles'] as $role): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card role-card h-100">
                        <div
                            class="card-header bg-<?= $role->role_name === 'admin' ? 'danger' : ($role->role_name === 'manager' ? 'warning' : 'primary') ?> text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-user-tag"></i> <?= ucfirst($role->role_name) ?>
                                </h6>
                                <span class="badge badge-light role-badge">
                                    <?= $role->user_count ?? 0 ?> users
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3"><?= htmlspecialchars($role->description) ?></p>

                            <div class="mb-3">
                                <strong>Permissions:</strong>
                                <div class="mt-2">
                                    <?php
                                    $permissions = json_decode($role->permissions, true);
                                    if ($permissions && !empty($permissions)): ?>
                                        <?php foreach ($permissions as $module => $perms): ?>
                                            <?php if (is_array($perms)): ?>
                                                <?php foreach ($perms as $perm): ?>
                                                    <span class="badge badge-info permission-badge">
                                                        <?= ucfirst($module) ?>: <?= ucfirst($perm) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="badge badge-success permission-badge">
                                                    <?= ucfirst($module) ?>: Full Access
                                                </span>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted">No specific permissions assigned</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="btn-group btn-group-sm w-100">
                                <button type="button" class="btn btn-outline-primary" onclick="editRole(<?= $role->role_id ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="button" class="btn btn-outline-info"
                                    onclick="managePermissions(<?= $role->role_id ?>)">
                                    <i class="fas fa-shield-alt"></i> Permissions
                                </button>
                                <?php if ($role->role_name !== 'admin'): ?>
                                    <button type="button" class="btn btn-outline-danger"
                                        onclick="deleteRole(<?= $role->role_id ?>)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Detailed Roles Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-table"></i> Detailed Role Management
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="rolesTable" width="100%">
                            <thead>
                                <tr>
                                    <th>Role</th>
                                    <th>Description</th>
                                    <th>Users</th>
                                    <th>Permissions</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['roles']) && !empty($data['roles'])): ?>
                                    <?php foreach ($data['roles'] as $role): ?>
                                        <tr>
                                            <td>
                                                <span
                                                    class="badge badge-<?= $role->role_name === 'admin' ? 'danger' : ($role->role_name === 'manager' ? 'warning' : 'primary') ?> role-badge">
                                                    <?= ucfirst($role->role_name) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($role->description) ?></td>
                                            <td>
                                                <span class="badge badge-info"><?= $role->user_count ?? 0 ?> users</span>
                                            </td>
                                            <td>
                                                <?php
                                                $permissions = json_decode($role->permissions, true);
                                                $permCount = 0;
                                                if ($permissions) {
                                                    foreach ($permissions as $module => $perms) {
                                                        if (is_array($perms)) {
                                                            $permCount += count($perms);
                                                        } else {
                                                            $permCount++;
                                                        }
                                                    }
                                                }
                                                ?>
                                                <span class="badge badge-secondary"><?= $permCount ?> permissions</span>
                                            </td>
                                            <td>
                                                <?= isset($role->created_at) ? date('M j, Y', strtotime($role->created_at)) : 'N/A' ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="editRole(<?= $role->role_id ?>)" title="Edit Role">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-info"
                                                        onclick="managePermissions(<?= $role->role_id ?>)"
                                                        title="Manage Permissions">
                                                        <i class="fas fa-shield-alt"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        onclick="viewRoleUsers(<?= $role->role_id ?>)" title="View Users">
                                                        <i class="fas fa-users"></i>
                                                    </button>
                                                    <?php if ($role->role_name !== 'admin'): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="deleteRole(<?= $role->role_id ?>)" title="Delete Role">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No roles found</td>
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

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle"></i> Add New Role
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addRoleForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="roleName">Role Name *</label>
                                <input type="text" class="form-control" id="roleName" name="role_name" required>
                                <small class="form-text text-muted">Use lowercase with underscores (e.g.,
                                    sales_manager)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="roleDescription">Description *</label>
                                <input type="text" class="form-control" id="roleDescription" name="description"
                                    required>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6><i class="fas fa-shield-alt"></i> Assign Permissions</h6>
                    <div id="addRolePermissions">
                        <!-- Permissions will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Edit Role
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editRoleForm">
                <input type="hidden" id="editRoleId" name="role_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editRoleName">Role Name *</label>
                                <input type="text" class="form-control" id="editRoleName" name="role_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editRoleDescription">Description *</label>
                                <input type="text" class="form-control" id="editRoleDescription" name="description"
                                    required>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6><i class="fas fa-shield-alt"></i> Manage Permissions</h6>
                    <div id="editRolePermissions">
                        <!-- Permissions will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#rolesTable').DataTable({
            "pageLength": 25,
            "order": [[0, "asc"]],
            "columnDefs": [
                { "orderable": false, "targets": 5 } // Actions column
            ]
        });

        // Load permissions for add role modal
        loadPermissionsCheckboxes('addRolePermissions');

        // Add role form
        $('#addRoleForm').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?= URLROOT ?>/admin/addRole',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Role created successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function () {
                    alert('An error occurred. Please try again.');
                }
            });
        });

        // Edit role form
        $('#editRoleForm').submit(function (e) {
            e.preventDefault();
            const roleId = $('#editRoleId').val();
            $.ajax({
                url: '<?= URLROOT ?>/admin/editRole/' + roleId,
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Role updated successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        });
    });

    function loadPermissionsCheckboxes(containerId, existingPermissions = {}) {
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
                <div class="select-all-group">
                    <div class="form-check">
                        <input class="form-check-input select-all-module" type="checkbox" 
                               data-module="${module}" id="selectAll_${module}_${containerId}">
                        <label class="form-check-label font-weight-bold" for="selectAll_${module}_${containerId}">
                            <i class="fas fa-folder"></i> ${config.label} - Select All
                        </label>
                    </div>
                </div>
                <div class="row">
        `;

            config.permissions.forEach(permission => {
                const isChecked = existingPermissions[module] &&
                    existingPermissions[module].includes(permission) ? 'checked' : '';
                html += `
                <div class="col-md-3">
                    <div class="form-check permission-checkbox">
                        <input class="form-check-input module-permission" type="checkbox" 
                               name="permissions[${module}][]" 
                               value="${permission}" 
                               data-module="${module}"
                               id="perm_${module}_${permission}_${containerId}" ${isChecked}>
                        <label class="form-check-label" for="perm_${module}_${permission}_${containerId}">
                            <i class="fas fa-check-circle text-success"></i>
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

        $(`#${containerId}`).html(html);

        // Add select all functionality
        $(`.select-all-module`).change(function () {
            const module = $(this).data('module');
            const isChecked = $(this).is(':checked');
            $(`.module-permission[data-module="${module}"]`).prop('checked', isChecked);
        });

        // Update select all when individual permissions change
        $(`.module-permission`).change(function () {
            const module = $(this).data('module');
            const totalPerms = $(`.module-permission[data-module="${module}"]`).length;
            const checkedPerms = $(`.module-permission[data-module="${module}"]:checked`).length;
            $(`.select-all-module[data-module="${module}"]`).prop('checked', totalPerms === checkedPerms);
        });
    }

    function editRole(roleId) {
        $.ajax({
            url: '<?= URLROOT ?>/admin/editRole/' + roleId,
            method: 'GET',
            dataType: 'json',
            success: function (role) {
                $('#editRoleId').val(role.role_id);
                $('#editRoleName').val(role.role_name);
                $('#editRoleDescription').val(role.description);

                const permissions = JSON.parse(role.permissions || '{}');
                loadPermissionsCheckboxes('editRolePermissions', permissions);

                $('#editRoleModal').modal('show');
            }
        });
    }

    function managePermissions(roleId) {
        editRole(roleId); // Reuse the edit functionality for permissions
    }

    function deleteRole(roleId) {
        if (confirm('Are you sure you want to delete this role? This action cannot be undone.')) {
            $.ajax({
                url: '<?= URLROOT ?>/admin/deleteRole/' + roleId,
                method: 'POST',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Role deleted successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        }
    }

    function viewRoleUsers(roleId) {
        window.location.href = '<?= URLROOT ?>/admin/users?role=' + roleId;
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