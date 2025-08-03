<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container">
    <!-- Page Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <h1 class="mb-0">
                <i class="fas fa-clipboard-check"></i>
                Cycle Counts Management
            </h1>
            <p class="text-muted mb-0">Manage inventory cycle counts and track variances</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-info-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> Planned</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-info">
                        <?php echo isset($data['stats']) && isset($data['stats']->planned_counts) ? $data['stats']->planned_counts : 0; ?>
                    </h3>
                    <small class="text-muted">Planned Counts</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-warning-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-play"></i> In Progress</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-warning">
                        <?php echo isset($data['stats']) && isset($data['stats']->active_counts) ? $data['stats']->active_counts : 0; ?>
                    </h3>
                    <small class="text-muted">Active Counts</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-success-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-check"></i> Completed</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-success">
                        <?php echo isset($data['stats']) && isset($data['stats']->completed_counts) ? $data['stats']->completed_counts : 0; ?>
                    </h3>
                    <small class="text-muted">Completed</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Variances</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-danger">
                        <?php echo isset($data['stats']) && isset($data['stats']->variance_counts) ? $data['stats']->variance_counts : 0; ?>
                    </h3>
                    <small class="text-muted">With Variances</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Cards Row -->
    <div class="row mb-4">
        <!-- Create New Count -->
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="theme-card">
                <div class="card-header bg-success-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> New Cycle Count</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Start a new cycle count for inventory verification.</p>
                    <a href="<?php echo URLROOT; ?>/cycle_counts/create" class="btn btn-success btn-lg btn-block">
                        <i class="fas fa-plus"></i> Create New Count
                    </a>
                </div>
            </div>
        </div>

        <!-- Reports Section -->
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Reports</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Generate cycle count reports and analytics.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/cycle_counts/reports" class="btn btn-outline-primary">
                            <i class="fas fa-chart-line"></i> Variance Report
                        </a>
                        <a href="<?php echo URLROOT; ?>/cycle_counts/export" class="btn btn-primary">
                            <i class="fas fa-download"></i> Export Data
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="theme-card">
                <div class="card-header bg-info-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Quick access to common cycle count tasks.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/cycle_counts/templates" class="btn btn-outline-info">
                            <i class="fas fa-file-alt"></i> Count Templates
                        </a>
                        <a href="<?php echo URLROOT; ?>/cycle_counts/adjustments" class="btn btn-info">
                            <i class="fas fa-adjust"></i> View Adjustments
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Cycle Counts Table -->
        <div class="theme-card">
            <div class="card-header bg-secondary-theme text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i>
                    Cycle Counts List
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="theme-table">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Count Name</th>
                                <th>Status</th>
                                <th>Count Date</th>
                                <th>Location</th>
                                <th>Items</th>
                                <th>Created By</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data['cycle_counts'])): ?>
                                <?php foreach ($data['cycle_counts'] as $count): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($count->count_name); ?></strong>
                                            <?php if (!empty($count->description)): ?>
                                                <br><small
                                                    class="text-muted"><?php echo htmlspecialchars(substr($count->description, 0, 50)); ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = [
                                                'pending' => 'warning',
                                                'in_progress' => 'info',
                                                'completed' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $statusColor = $statusClass[$count->status] ?? 'secondary';
                                            ?>
                                            <span class="badge badge-<?php echo $statusColor; ?>">
                                                <?php echo ucwords(str_replace('_', ' ', $count->status)); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo date('M j, Y', strtotime($count->count_date)); ?>
                                            <?php
                                            if (!empty($data['cycle_counts'])) {
                                                foreach ($data['cycle_counts'] as $count) {
                                                    ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($count->count_name); ?></strong>
                                                    <?php if (!empty($count->description)) { ?>
                                                        <br><small
                                                            class="text-muted"><?php echo htmlspecialchars(substr($count->description, 0, 50)); ?>...</small>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $statusClass = [
                                                        'pending' => 'warning',
                                                        'in_progress' => 'info',
                                                        'completed' => 'success',
                                                        'cancelled' => 'danger'
                                                    ];
                                                    $statusColor = $statusClass[$count->status] ?? 'secondary';
                                                    ?>
                                                    <span class="badge badge-<?php echo $statusColor; ?>">
                                                        <?php echo ucwords(str_replace('_', ' ', $count->status)); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo date('M j, Y', strtotime($count->count_date)); ?>
                                                    <?php if ($count->created_at) { ?>
                                                        <br><small class="text-muted">Created:
                                                            <?php echo date('M j', strtotime($count->created_at)); ?></small>
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($count->location ?? 'All Locations'); ?></td>
                                                <td>
                                                    <span class="badge badge-primary"><?php echo $count->item_count ?? 0; ?></span>
                                                    items
                                                </td>
                                                <td><?php echo htmlspecialchars($count->created_by_name ?? 'Unknown'); ?></td>
                                                <td class="text-center">
                                                    <div class="theme-action-group">
                                                        <a href="<?php echo URLROOT; ?>/cycle_counts/view/<?php echo $count->id; ?>"
                                                            class="btn btn-sm btn-info" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <?php if ($count->status === 'pending') { ?>
                                                            <a href="<?php echo URLROOT; ?>/cycle_counts/start/<?php echo $count->id; ?>"
                                                                class="btn btn-sm btn-success" title="Start Count">
                                                                <i class="fas fa-play"></i>
                                                            </a>
                                                        <?php } ?>
                                                        <?php if ($count->status === 'in_progress') { ?>
                                                            <a href="<?php echo URLROOT; ?>/cycle_counts/count/<?php echo $count->id; ?>"
                                                                class="btn btn-sm btn-primary" title="Continue Count">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        <?php } ?>
                                                        <?php if ($count->status === 'completed') { ?>
                                                            <a href="<?php echo URLROOT; ?>/cycle_counts/report/<?php echo $count->id; ?>"
                                                                class="btn btn-sm btn-secondary" title="View Report">
                                                                <i class="fas fa-chart-bar"></i>
                                                            </a>
                                                        <?php } ?>
                                                        <a href="<?php echo URLROOT; ?>/cycle_counts/edit/<?php echo $count->id; ?>"
                                                            class="btn btn-sm btn-warning" title="Edit Count">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php
                                                }
                                            } else {
                                                ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-clipboard-check fa-3x mb-3 text-muted"></i>
                                                <br>
                                                <strong>No cycle counts found</strong>
                                                <br>
                                                <small>Create your first cycle count to get started</small>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    }
                                    }
                                    </script>

                                    <?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

                                    <?php if (in_array($count->status, ['planned', 'in_progress'])): ?>
                                        <button class="btn btn-danger" onclick="cancelCount(<?= $count->id ?>)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                        </div>
                        </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No cycle counts found</td>
                    </tr>
                <?php endif; ?>
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</section>
</div>

<script>
    $(document).ready(function () {
        $('#cycleCountsTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "order": [[2, "desc"]]
        });
    });

    function startCount(countId) {
        if (confirm('Are you sure you want to start this cycle count?')) {
            window.location.href = '/cycle_counts/start/' + countId;
        }
    }

    function completeCount(countId) {
        if (confirm('Are you sure you want to complete this cycle count?')) {
            window.location.href = '/cycle_counts/complete/' + countId;
        }
    }

    function cancelCount(countId) {
        if (confirm('Are you sure you want to cancel this cycle count?')) {
            window.location.href = '/cycle_counts/cancel/' + countId;
        }
    }

    function refreshData() {
        location.reload();
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