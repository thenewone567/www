<?php
$pageTitle = 'Admin Panel';
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
    }

    .activity-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .admin-section {
        display: none;
    }

    .admin-section.active {
        display: block;
    }

    .nav-pills .nav-link {
        border-radius: 50px;
        margin: 0 5px;
    }

    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    /* Additional Admin Dashboard Styles */
    .admin-section {
        min-height: 500px;
    }

    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
    }

    .nav-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
    }

    .nav-tabs .nav-link.active {
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }

    .stat-card {
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .quick-action-btn {
        transition: all 0.2s;
    }

    .quick-action-btn:hover {
        transform: scale(1.05);
    }

    @media (max-width: 768px) {
        .admin-tabs {
            flex-direction: column;
        }
        
        .admin-tabs .nav-link {
            text-align: center;
            margin-bottom: 5px;
        }
    }
</style>

<div class="container-fluid page-top-area mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-6">
            <h1 class="mb-1 font-weight-bold">
                <i class="fas fa-shield-alt mr-2"></i>Admin Panel
            </h1>
            <small class="text-muted">System Administration & Management</small>
        </div>
    </div>
</div>

<!-- Navigation Tabs -->
<div class="container-fluid mb-4">
    <ul class="nav nav-pills nav-fill" id="adminTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="dashboard-tab" data-toggle="pill" href="#dashboard" role="tab">
                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= URLROOT ?>/admin/priceManagement">
                <i class="fas fa-dollar-sign mr-2"></i>Price Management
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="users-tab" data-toggle="pill" href="#users" role="tab">
                <i class="fas fa-users mr-2"></i>Users
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="roles-tab" data-toggle="pill" href="#roles" role="tab">
                <i class="fas fa-user-tag mr-2"></i>Roles
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="permissions-tab" data-toggle="pill" href="#permissions" role="tab">
                <i class="fas fa-user-shield mr-2"></i>Permissions
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="logs-tab" data-toggle="pill" href="#logs" role="tab">
                <i class="fas fa-history mr-2"></i>Activity Logs
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="settings-tab" data-toggle="pill" href="#settings" role="tab">
                <i class="fas fa-cog mr-2"></i>Settings
            </a>
        </li>
    </ul>
</div>

<div class="container-fluid">
    <div class="tab-content" id="adminTabContent">
        
        <!-- DASHBOARD TAB -->
        <div class="tab-pane fade show active admin-section" id="dashboard" role="tabpanel">
            <!-- System Statistics -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card-theme h-100">
                        <div class="card-body text-center">
                            <div class="text-primary mb-3">
                                <i class="fas fa-users fa-3x"></i>
                            </div>
                            <h2 class="mb-2 font-weight-bold text-primary">
                                <?= $data['stats']['total_users'] ?? 0 ?>
                            </h2>
                            <p class="text-muted mb-0 font-weight-600">Total Users</p>
                            <small class="text-muted">Registered</small>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card-theme h-100">
                        <div class="card-body text-center">
                            <div class="text-success mb-3">
                                <i class="fas fa-user-check fa-3x"></i>
                            </div>
                            <h2 class="mb-2 font-weight-bold text-success">
                                <?= $data['stats']['active_users'] ?? 0 ?>
                            </h2>
                            <p class="text-muted mb-0 font-weight-600">Active Users</p>
                            <small class="text-muted">Online</small>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card-theme h-100">
                        <div class="card-body text-center">
                            <div class="text-info mb-3">
                                <i class="fas fa-user-tag fa-3x"></i>
                            </div>
                            <h2 class="mb-2 font-weight-bold text-info">
                                <?= $data['stats']['total_roles'] ?? 0 ?>
                            </h2>
                            <p class="text-muted mb-0 font-weight-600">System Roles</p>
                            <small class="text-muted">Configured</small>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card-theme h-100">
                        <div class="card-body text-center">
                            <div class="text-warning mb-3">
                                <i class="fas fa-sign-in-alt fa-3x"></i>
                            </div>
                            <h2 class="mb-2 font-weight-bold text-warning">
                                <?= $data['stats']['recent_logins'] ?? 0 ?>
                            </h2>
                            <p class="text-muted mb-0 font-weight-600">Recent Logins</p>
                            <small class="text-muted">Last 7 days</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-theme mb-4">
                        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-clock mr-2"></i>Recent Activity
                            </h6>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                            <?php if (isset($data['recent_activity']) && !empty($data['recent_activity'])): ?>
                                <?php foreach ($data['recent_activity'] as $activity): ?>
                                    <div class="border-left border-primary bg-light p-3 mb-2 rounded-right">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong><?= htmlspecialchars($activity->user_name ?? 'System') ?></strong>
                                                <span class="text-muted">performed</span>
                                                <strong><?= htmlspecialchars($activity->action) ?></strong>
                                            </div>
                                            <small class="text-muted">
                                                <?= date('M j, g:i A', strtotime($activity->created_at)) ?>
                                            </small>
                                        </div>
                                        <?php if (!empty($activity->details)): ?>
                                            <div class="mt-1">
                                                <small class="text-muted"><?= htmlspecialchars($activity->details) ?></small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p>No recent activity</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PRICE MANAGEMENT TAB - Moved to separate page -->
        <!-- Price Management is now available at <?= URLROOT ?>/admin/priceManagement -->

        <!-- USERS TAB -->
        <div class="tab-pane fade admin-section" id="users" role="tabpanel">
            <div class="card-theme">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users mr-2"></i>User Management
                    </h5>
                    <button class="btn-theme btn-primary-theme" data-toggle="modal" data-target="#addUserModal">
                        <i class="fas fa-plus mr-2"></i>Add New User
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="usersTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>User</th>
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
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar" style="background: linear-gradient(45deg, #667eea, #764ba2);">
                                                        <?= strtoupper(substr($user->name ?? $user->username ?? 'U', 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <strong><?= htmlspecialchars($user->name ?? $user->username ?? 'Unknown') ?></strong>
                                                        <br>
                                                        <small class="text-muted"><?= htmlspecialchars($user->username ?? '') ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($user->email ?? 'N/A') ?></td>
                                            <td>
                                                <span class="badge badge-<?= ($user->role_name ?? 'user') === 'admin' ? 'danger' : 'primary' ?>">
                                                    <?= ucfirst($user->role_name ?? 'User') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= ($user->status ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                                                    <?= ucfirst($user->status ?? 'Active') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($user->last_login)): ?>
                                                    <?= date('M j, Y g:i A', strtotime($user->last_login)) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Never</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="editUser(<?= $user->user_id ?? 0 ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(<?= $user->user_id ?? 0 ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="fas fa-users fa-2x mb-2"></i>
                                            <p>No users found</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROLES TAB -->
        <div class="tab-pane fade admin-section" id="roles" role="tabpanel">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card-theme">
                        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-user-tag mr-2"></i>System Roles
                            </h5>
                            <button class="btn-theme btn-primary-theme" data-toggle="modal" data-target="#addRoleModal">
                                <i class="fas fa-plus mr-2"></i>Add New Role
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Role Name</th>
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
                                                        <strong><?= ucfirst($role->role_name ?? 'Unknown') ?></strong>
                                                    </td>
                                                    <td><?= htmlspecialchars($role->description ?? 'No description') ?></td>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            <?= $role->user_count ?? 0 ?> users
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button class="btn btn-sm btn-outline-primary" onclick="editRole(<?= $role->role_id ?? 0 ?>)">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteRole(<?= $role->role_id ?? 0 ?>)">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">
                                                    <i class="fas fa-user-tag fa-2x mb-2"></i>
                                                    <p>No roles found</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card-theme">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-shield-alt mr-2"></i>Role Permissions
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Select a role to view and manage its permissions.</p>
                            <div id="rolePermissions">
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-hand-pointer fa-2x mb-2"></i>
                                    <p>Click on a role to view permissions</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PERMISSIONS TAB -->
        <div class="tab-pane fade admin-section" id="permissions" role="tabpanel">
            <div class="card-theme">
                <div class="card-header bg-white border-bottom">
                    <h5 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-shield mr-2"></i>User Permissions Management
                    </h5>
                    <small class="text-muted">Control which pages each user can access</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th width="50%">Permissions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['users']) && !empty($data['users'])): ?>
                                    <?php foreach ($data['users'] as $user): ?>
                                        <tr id="user-row-<?= $user->user_id ?? 0 ?>">
                                            <td>
                                                <div>
                                                    <strong><?= htmlspecialchars($user->name ?? $user->username ?? 'Unknown') ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?= htmlspecialchars($user->email ?? '') ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= ($user->role_name ?? 'user') === 'admin' ? 'danger' : 'primary' ?>">
                                                    <?= ucfirst($user->role_name ?? 'User') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= ($user->status ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                                                    <?= ucfirst($user->status ?? 'Active') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox" value="dashboard" <?= in_array('dashboard', $user->permissions ?? []) ? 'checked' : '' ?>>
                                                            <label class="form-check-label">Dashboard</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox" value="products" <?= in_array('products', $user->permissions ?? []) ? 'checked' : '' ?>>
                                                            <label class="form-check-label">Products</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox" value="inventory" <?= in_array('inventory', $user->permissions ?? []) ? 'checked' : '' ?>>
                                                            <label class="form-check-label">Inventory</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox" value="reports" <?= in_array('reports', $user->permissions ?? []) ? 'checked' : '' ?>>
                                                            <label class="form-check-label">Reports</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox" value="settings" <?= in_array('settings', $user->permissions ?? []) ? 'checked' : '' ?>>
                                                            <label class="form-check-label">Settings</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="checkbox" value="admin" <?= in_array('admin', $user->permissions ?? []) ? 'checked' : '' ?>>
                                                            <label class="form-check-label">Admin</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="updatePermissions(<?= $user->user_id ?? 0 ?>)">
                                                    <i class="fas fa-save"></i> Save
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="fas fa-user-shield fa-2x mb-2"></i>
                                            <p>No users found</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ACTIVITY LOGS TAB -->
        <div class="tab-pane fade admin-section" id="logs" role="tabpanel">
            <div class="card-theme">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history mr-2"></i>Activity Logs
                    </h5>
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-primary" onclick="filterLogs('today')">Today</button>
                        <button class="btn btn-outline-primary" onclick="filterLogs('week')">This Week</button>
                        <button class="btn btn-outline-primary" onclick="filterLogs('month')">This Month</button>
                        <button class="btn btn-outline-danger" onclick="clearLogs()">Clear Logs</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="activityLogsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Timestamp</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['activity_logs']) && !empty($data['activity_logs'])): ?>
                                    <?php foreach ($data['activity_logs'] as $log): ?>
                                        <tr>
                                            <td>
                                                <?= date('M j, Y g:i A', strtotime($log->created_at ?? '')) ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($log->user_name ?? 'System') ?></strong>
                                            </td>
                                            <td>
                                                <span class="activity-badge badge badge-primary">
                                                    <?= htmlspecialchars($log->action ?? 'Unknown') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small><?= htmlspecialchars($log->details ?? 'No details') ?></small>
                                            </td>
                                            <td>
                                                <code><?= htmlspecialchars($log->ip_address ?? 'N/A') ?></code>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="fas fa-history fa-2x mb-2"></i>
                                            <p>No activity logs found</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- SETTINGS TAB -->
        <div class="tab-pane fade admin-section" id="settings" role="tabpanel">
            <form action="<?= URLROOT ?>/admin/settings" method="POST" id="settingsForm">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- General Settings -->
                        <div class="card-theme mb-4 border-0 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-cog mr-2"></i>General Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="site_name">Site Name</label>
                                        <input type="text" class="form-theme" id="site_name" name="site_name" 
                                               value="<?= $data['settings']['site_name'] ?? 'Hardware Store' ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="site_description">Site Description</label>
                                        <input type="text" class="form-theme" id="site_description" name="site_description" 
                                               value="<?= $data['settings']['site_description'] ?? 'Professional Hardware Management' ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="admin_email">Admin Email</label>
                                        <input type="email" class="form-theme" id="admin_email" name="admin_email" 
                                               value="<?= $data['settings']['admin_email'] ?? 'admin@example.com' ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="timezone">Timezone</label>
                                        <select class="form-theme" id="timezone" name="timezone">
                                            <option value="UTC">UTC</option>
                                            <option value="America/New_York">Eastern Time</option>
                                            <option value="America/Chicago">Central Time</option>
                                            <option value="America/Denver">Mountain Time</option>
                                            <option value="America/Los_Angeles">Pacific Time</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Settings -->
                        <div class="card-theme mb-4 border-0 shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-server mr-2"></i>System Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="max_upload_size">Max Upload Size (MB)</label>
                                        <input type="number" class="form-theme" id="max_upload_size" name="max_upload_size" 
                                               value="<?= $data['settings']['max_upload_size'] ?? '10' ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="session_timeout">Session Timeout (minutes)</label>
                                        <input type="number" class="form-theme" id="session_timeout" name="session_timeout" 
                                               value="<?= $data['settings']['session_timeout'] ?? '30' ?>">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="enable_logging" name="enable_logging" 
                                                   <?= ($data['settings']['enable_logging'] ?? true) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="enable_logging">
                                                Enable Activity Logging
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" 
                                                   <?= ($data['settings']['maintenance_mode'] ?? false) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="maintenance_mode">
                                                Maintenance Mode
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Actions -->
                        <div class="card-theme mb-4 border-0 shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-save mr-2"></i>Actions</h5>
                            </div>
                            <div class="card-body">
                                <button type="submit" class="btn btn-success btn-block mb-2">
                                    <i class="fas fa-save mr-2"></i>Save Settings
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-block mb-2" onclick="resetToDefaults()">
                                    <i class="fas fa-undo mr-2"></i>Reset to Defaults
                                </button>
                                <button type="button" class="btn btn-outline-info btn-block mb-2" onclick="exportSettings()">
                                    <i class="fas fa-download mr-2"></i>Export Settings
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-block" onclick="importSettings()">
                                    <i class="fas fa-upload mr-2"></i>Import Settings
                                </button>
                            </div>
                        </div>

                        <!-- System Info -->
                        <div class="card-theme">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>System Information</h5>
                            </div>
                            <div class="card-body">
                                <small class="text-muted">
                                    <strong>Version:</strong> 1.0.0<br>
                                    <strong>PHP Version:</strong> <?= phpversion() ?><br>
                                    <strong>Server:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?><br>
                                    <strong>Database:</strong> MySQL<br>
                                    <strong>Last Backup:</strong> <?= $data['settings']['last_backup'] ?? 'Never' ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Admin Dashboard JavaScript
$(document).ready(function() {
    // Initialize tab switching
    initializeTabs();
    
    // Initialize DataTables for better table functionality
    if ($.fn.DataTable) {
        $('#usersTable, #activityLogsTable').DataTable({
            pageLength: 10,
            responsive: true,
            order: [[0, 'desc']]
        });
    }
    
    // Load initial data
    loadDashboardData();
    
    // Auto-refresh dashboard data every 30 seconds
    setInterval(loadDashboardData, 30000);
});

function initializeTabs() {
    // Handle tab switching
    $('.nav-link[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('href');
        console.log('Switched to tab:', target);
        
        // Load tab-specific data
        switch(target) {
            case '#users':
                loadUsers();
                break;
            case '#roles':
                loadRoles();
                break;
            case '#logs':
                loadActivityLogs();
                break;
        }
    });
}

function loadDashboardData() {
    // Update system statistics
    $.ajax({
        url: '<?= URLROOT ?>/admin/getSystemStats',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                updateSystemStats(response.data);
            }
        },
        error: function() {
            console.error('Failed to load dashboard data');
        }
    });
}

function updateSystemStats(stats) {
    // Update dashboard statistics
    if (stats.total_products !== undefined) {
        $('#totalProducts').text(stats.total_products);
    }
    if (stats.total_users !== undefined) {
        $('#totalUsers').text(stats.total_users);
    }
    if (stats.low_stock_count !== undefined) {
        $('#lowStockCount').text(stats.low_stock_count);
    }
    if (stats.total_orders !== undefined) {
        $('#totalOrders').text(stats.total_orders);
    }
}

// Tab switching function for quick actions
function switchTab(tabName) {
    console.log('Switching to tab:', tabName);
    $(`#${tabName}-tab`).tab('show');
}

// User Management Functions
function loadUsers() {
    console.log('Loading users...');
    // Additional user loading logic can be added here
}

function editUser(userId) {
    console.log('Initiating user edit for ID:', userId);
    // Implementation for editing user
    $('#addUserModal').modal('show');
    // Load user data and populate form
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        console.log('Initiating user deletion for ID:', userId);
        
        $.ajax({
            url: '<?= URLROOT ?>/admin/deleteUser',
            method: 'POST',
            data: { user_id: userId },
            success: function(response) {
                if (response.success) {
                    alert('User deleted successfully!');
                    location.reload();
                } else {
                    alert('Failed to delete user: ' + (response.message || 'Unknown error'));
                }
            },
            error: function() {
                alert('Deletion failed! Please try again.');
            }
        });
    }
}

// Role Management Functions
function loadRoles() {
    console.log('Loading roles...');
}

function editRole(roleId) {
    console.log('Initiating role edit for ID:', roleId);
    // Implementation for editing role
}

function deleteRole(roleId) {
    if (confirm('Are you sure you want to delete this role?')) {
        console.log('Initiating role deletion for ID:', roleId);
        
        $.ajax({
            url: '<?= URLROOT ?>/admin/deleteRole',
            method: 'POST',
            data: { role_id: roleId },
            success: function(response) {
                if (response.success) {
                    alert('Role deleted successfully!');
                    location.reload();
                } else {
                    alert('Failed to delete role: ' + (response.message || 'Unknown error'));
                }
            },
            error: function() {
                alert('Deletion failed! Please try again.');
            }
        });
    }
}

// Permission Management Functions
function updatePermissions(userId) {
    console.log('Initiating permission update for user ID:', userId);
    
    const permissions = [];
    $(`#user-row-${userId} input[type="checkbox"]:checked`).each(function() {
        permissions.push($(this).val());
    });
    
    $.ajax({
        url: '<?= URLROOT ?>/admin/updatePermissions',
        method: 'POST',
        data: { 
            user_id: userId,
            permissions: permissions
        },
        success: function(response) {
            if (response.success) {
                alert('Permissions updated successfully!');
            } else {
                alert('Failed to update permissions: ' + (response.message || 'Unknown error'));
            }
        },
        error: function() {
            alert('Permission update failed! Please try again.');
        }
    });
}

// Activity Log Functions
function loadActivityLogs() {
    console.log('Loading activity logs...');
}

function filterLogs(period) {
    console.log('Filtering logs for period:', period);
    
    $.ajax({
        url: '<?= URLROOT ?>/admin/filterLogs',
        method: 'POST',
        data: { period: period },
        success: function(response) {
            if (response.success) {
                // Reload the logs table
                location.reload();
            }
        }
    });
}

function clearLogs() {
    if (confirm('Are you sure you want to clear all activity logs? This action cannot be undone.')) {
        console.log('Initiating log clearance...');
        
        $.ajax({
            url: '<?= URLROOT ?>/admin/clearLogs',
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    alert('Activity logs cleared successfully!');
                    location.reload();
                } else {
                    alert('Failed to clear logs: ' + (response.message || 'Unknown error'));
                }
            },
            error: function() {
                alert('Log clearance failed! Please try again.');
            }
        });
    }
}

// Settings Functions
function resetToDefaults() {
    if (confirm('Are you sure you want to reset all settings to defaults?')) {
        console.log('Initiating settings reset...');
        
        $.ajax({
            url: '<?= URLROOT ?>/admin/resetSettings',
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    alert('Settings reset to defaults successfully!');
                    location.reload();
                } else {
                    alert('Failed to reset settings: ' + (response.message || 'Unknown error'));
                }
            },
            error: function() {
                alert('Settings reset failed! Please try again.');
            }
        });
    }
}

function exportSettings() {
    console.log('Initiating settings export...');
    window.location.href = '<?= URLROOT ?>/admin/exportSettings';
}

function importSettings() {
    console.log('Initiating settings import...');
    // Create file input for import
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.json';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('settings_file', file);
            
            $.ajax({
                url: '<?= URLROOT ?>/admin/importSettings',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert('Settings imported successfully!');
                        location.reload();
                    } else {
                        alert('Failed to import settings: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function() {
                    alert('Settings import failed! Please try again.');
                }
            });
        }
    };
    input.click();
}

// Form submission with alerts
$('#settingsForm').on('submit', function(e) {
    e.preventDefault();
    console.log('Initiating settings save...');
    
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                alert('Settings saved successfully!');
            } else {
                alert('Failed to save settings: ' + (response.message || 'Unknown error'));
            }
        },
        error: function() {
            alert('Settings save failed! Please try again.');
        }
    });
});
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>