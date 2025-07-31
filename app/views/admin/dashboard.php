<?php
$pageTitle = 'Admin Panel';
require_once '../app/views/layout/header.php';
?>

<style>
    .admin-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
    }

    .stat-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .system-health .badge {
        font-size: 0.9rem;
    }

    .activity-item {
        border-left: 4px solid #007bff;
        background: #f8f9fa;
        padding: 1rem;
        margin-bottom: 0.5rem;
        border-radius: 0 5px 5px 0;
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

    .admin-nav .nav-link:hover,
    .admin-nav .nav-link.active {
        background: #007bff;
        color: white;
    }
</style>

<div class="admin-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-0">
                    <i class="fas fa-shield-alt"></i> Admin Panel
                </h1>
                <p class="mb-0 mt-2 opacity-75">System Administration & Management</p>
            </div>
            <div class="col-md-4 text-md-right">
                <div class="d-flex align-items-center justify-content-md-end">
                    <i class="fas fa-user-shield fa-2x mr-3"></i>
                    <div>
                        <div class="font-weight-bold"><?= $_SESSION['user_name'] ?></div>
                        <small>System Administrator</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Admin Navigation -->
    <div class="admin-nav">
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link active" href="<?= URLROOT ?>/admin">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= URLROOT ?>/admin/users">
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

    <!-- System Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-left-primary h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                <?= $data['stats']['total_users'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-left-success h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Users
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                <?= $data['stats']['active_users'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-left-info h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                System Roles
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                <?= $data['stats']['total_roles'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card border-left-warning h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Recent Logins (7d)
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                <?= $data['stats']['recent_logins'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sign-in-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health & Recent Activity -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-heartbeat"></i> System Health
                    </h6>
                </div>
                <div class="card-body system-health">
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
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock"></i> Recent Activity
                    </h6>
                    <a href="<?= URLROOT ?>/admin/activityLogs" class="btn btn-sm btn-outline-primary">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <?php if (isset($data['recent_activity']) && !empty($data['recent_activity'])): ?>
                        <?php foreach ($data['recent_activity'] as $activity): ?>
                            <div class="activity-item">
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
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?= URLROOT ?>/admin/users" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-user-plus"></i> Add New User
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

<script>
    $(document).ready(function () {
        // Auto-refresh system stats every 30 seconds
        setInterval(function () {
            // You can implement AJAX refresh here if needed
        }, 30000);
    });
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