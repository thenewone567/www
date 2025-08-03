<?php
$pageTitle = 'Activity Logs - Admin Panel';
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

    .activity-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
</style>

<div class="admin-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-0">
                    <i class="fas fa-history"></i> Activity Logs
                </h1>
                <p class="mb-0 mt-2 opacity-75">Monitor system activity and user actions</p>
            </div>
            <div class="col-md-4 text-md-right">
                <div class="btn-group">
                    <button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="?filter=login">Login Activities</a>
                        <a class="dropdown-item" href="?filter=user_created">User Management</a>
                        <a class="dropdown-item" href="?filter=sales">Sales Activities</a>
                        <a class="dropdown-item" href="?filter=purchases">Purchase Activities</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="?">All Activities</a>
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
                <a class="nav-link" href="<?= URLROOT ?>/admin">
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
                <a class="nav-link active" href="<?= URLROOT ?>/admin/activityLogs">
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

    <!-- Activity Logs Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-history"></i> System Activity Log
                        </h6>
                        <div class="text-muted">
                            <small>Total: <?= $data['total_activities'] ?? 0 ?> activities</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="activityTable" width="100%">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>IP Address</th>
                                    <th>User Agent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['activities']) && !empty($data['activities'])): ?>
                                    <?php foreach ($data['activities'] as $activity): ?>
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong><?= date('M j, Y', strtotime($activity->created_at)) ?></strong>
                                                </div>
                                                <small class="text-muted">
                                                    <?= date('g:i:s A', strtotime($activity->created_at)) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar bg-primary mr-2">
                                                        <?= strtoupper(substr($activity->user_name ?? 'S', 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold">
                                                            <?= htmlspecialchars($activity->user_name ?? 'System') ?>
                                                        </div>
                                                        <small class="text-muted">ID: <?= $activity->user_id ?? 'N/A' ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $badgeClass = 'secondary';
                                                switch ($activity->action) {
                                                    case 'login':
                                                        $badgeClass = 'success';
                                                        break;
                                                    case 'logout':
                                                        $badgeClass = 'warning';
                                                        break;
                                                    case 'user_created':
                                                    case 'user_updated':
                                                        $badgeClass = 'info';
                                                        break;
                                                    case 'role_created':
                                                    case 'role_updated':
                                                        $badgeClass = 'primary';
                                                        break;
                                                    case 'error':
                                                    case 'failed_login':
                                                        $badgeClass = 'danger';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge activity-badge badge-<?= $badgeClass ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $activity->action)) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div style="max-width: 300px;">
                                                    <?php if (!empty($activity->details)): ?>
                                                        <?= htmlspecialchars($activity->details) ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">No details</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars($activity->ip_address ?? 'Unknown') ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div style="max-width: 200px; word-break: break-all;">
                                                    <small class="text-muted">
                                                        <?php
                                                        $userAgent = $activity->user_agent ?? 'Unknown';
                                                        if (strlen($userAgent) > 50) {
                                                            echo htmlspecialchars(substr($userAgent, 0, 50)) . '...';
                                                        } else {
                                                            echo htmlspecialchars($userAgent);
                                                        }
                                                        ?>
                                                    </small>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <p>No activity logs found</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if (isset($data['total_pages']) && $data['total_pages'] > 1): ?>
                        <nav aria-label="Activity logs pagination">
                            <ul class="pagination justify-content-center mt-3">
                                <?php for ($i = 1; $i <= $data['total_pages']; $i++): ?>
                                    <li class="page-item <?= ($i == $data['current_page']) ? 'active' : '' ?>">
                                        <a class="page-link"
                                            href="?page=<?= $i ?><?= isset($_GET['filter']) ? '&filter=' . $_GET['filter'] : '' ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Statistics -->
    <div class="row mt-4">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Successful Logins (24h)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                $successfulLogins = 0;
                                if (isset($data['activities'])) {
                                    foreach ($data['activities'] as $activity) {
                                        if (
                                            $activity->action === 'login' &&
                                            strtotime($activity->created_at) > strtotime('-24 hours')
                                        ) {
                                            $successfulLogins++;
                                        }
                                    }
                                }
                                echo $successfulLogins;
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sign-in-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                User Management Actions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                $userActions = 0;
                                if (isset($data['activities'])) {
                                    foreach ($data['activities'] as $activity) {
                                        if (in_array($activity->action, ['user_created', 'user_updated', 'role_created', 'role_updated'])) {
                                            $userActions++;
                                        }
                                    }
                                }
                                echo $userActions;
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users-cog fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Failed Login Attempts
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                $failedLogins = 0;
                                if (isset($data['activities'])) {
                                    foreach ($data['activities'] as $activity) {
                                        if ($activity->action === 'failed_login') {
                                            $failedLogins++;
                                        }
                                    }
                                }
                                echo $failedLogins;
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#activityTable').DataTable({
            "pageLength": 25,
            "order": [[0, "desc"]],
            "columnDefs": [
                { "orderable": false, "targets": [3, 5] }, // Details and User Agent columns
                { "width": "15%", "targets": 0 }, // Timestamp
                { "width": "15%", "targets": 1 }, // User
                { "width": "10%", "targets": 2 }, // Action
                { "width": "30%", "targets": 3 }, // Details
                { "width": "10%", "targets": 4 }, // IP Address
                { "width": "20%", "targets": 5 }  // User Agent
            ],
            "searching": true,
            "info": true,
            "language": {
                "search": "Search activities:",
                "lengthMenu": "Show _MENU_ activities per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ activities",
                "infoEmpty": "No activities found",
                "infoFiltered": "(filtered from _MAX_ total activities)"
            }
        });

        // Auto refresh every 30 seconds
        setInterval(function () {
            if (confirm('Refresh activity logs?')) {
                location.reload();
            }
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