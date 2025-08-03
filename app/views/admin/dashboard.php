<?php
$pageTitle = 'Admin Panel';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
?>

<div class="container-fluid page-top-area mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-6">
            <h1 class="mb-1 font-weight-bold">
                <i class="fas fa-shield-alt mr-2"></i>Admin Panel
            </h1>
            <small class="text-muted">System Administration & Management</small>
        </div>
        <div class="col-12 col-md-6 text-md-right mt-3 mt-md-0">
            <div class="btn-group" role="group">
                <a href="<?= URLROOT ?>/admin/users" class="btn btn-outline-info">
                    <i class="fas fa-users"></i> Users
                </a>
                <a href="<?= URLROOT ?>/admin/roles" class="btn btn-outline-secondary">
                    <i class="fas fa-user-tag"></i> Roles
                </a>
                <a href="<?= URLROOT ?>/admin/activityLogs" class="btn btn-outline-warning">
                    <i class="fas fa-history"></i> Logs
                </a>
                <a href="<?= URLROOT ?>/admin/settings" class="btn btn-outline-primary">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- System Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
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
            <div class="card border-0 shadow-sm h-100">
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
            <div class="card border-0 shadow-sm h-100">
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
            <div class="card border-0 shadow-sm h-100">
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

    <!-- System Health & Recent Activity -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-heartbeat mr-2"></i>System Health
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Database Status:</span>
                                <span
                                    class="badge badge-<?= $data['system_health']['database_status'] === 'Connected' ? 'success' : 'danger' ?>">
                                    <?= $data['system_health']['database_status'] ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Disk Space:</span>
                                <span class="badge badge-info">
                                    <?= $data['system_health']['disk_space'] ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>PHP Version:</span>
                                <span class="badge badge-secondary">
                                    <?= $data['system_health']['php_version'] ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Memory Usage:</span>
                                <span class="badge badge-primary">
                                    <?= $data['system_health']['memory_usage'] ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock mr-2"></i>Recent Activity
                    </h6>
                    <a href="<?= URLROOT ?>/admin/activityLogs" class="btn btn-sm btn-outline-primary">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
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

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt mr-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?= URLROOT ?>/admin/users" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-users"></i> Manage Users
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= URLROOT ?>/admin/roles" class="btn btn-outline-success btn-block">
                                <i class="fas fa-plus-circle"></i> Create Role
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= URLROOT ?>/admin/settings" class="btn btn-outline-info btn-block">
                                <i class="fas fa-cogs"></i> System Settings
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= URLROOT ?>/reports/analytics" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-chart-bar"></i> View Analytics
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>