<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container theme-unified">
    <!-- Header Section -->
    <div class="row align-items-center theme-header mb-4">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 m-0">
                    <li class="breadcrumb-item">
                        <a href="<?php echo URLROOT; ?>/dashboard" class="text-decoration-none">
                            <i class="fa-solid fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="fa-solid fa-dolly"></i> Receiving
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h1 class="theme-page-title">
                <i class="fa-solid fa-dolly theme-icon"></i>
                Receiving Center
            </h1>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php flash('receive_message'); ?>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <a href="<?php echo URLROOT; ?>/receiving/pending" class="text-decoration-none">
                <div class="theme-stat-card theme-stat-primary">
                    <div class="theme-stat-content">
                        <div class="theme-stat-icon">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div class="theme-stat-details">
                            <h3 class="theme-stat-number">
                                <?php echo isset($data['pending_count']) ? $data['pending_count'] : 0; ?>
                            </h3>
                            <p class="theme-stat-label">Pending Receipts</p>
                            <small class="theme-stat-description">Awaiting processing</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <a href="<?php echo URLROOT; ?>/receiving/partial" class="text-decoration-none">
                <div class="theme-stat-card theme-stat-warning">
                    <div class="theme-stat-content">
                        <div class="theme-stat-icon">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <div class="theme-stat-details">
                            <h3 class="theme-stat-number">
                                <?php echo isset($data['partial_count']) ? $data['partial_count'] : 0; ?>
                            </h3>
                            <p class="theme-stat-label">Partial Receipts</p>
                            <small class="theme-stat-description">Partially received</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <a href="<?php echo URLROOT; ?>/receiving/completed_today" class="text-decoration-none">
                <div class="theme-stat-card theme-stat-success">
                    <div class="theme-stat-content">
                        <div class="theme-stat-icon">
                            <i class="fa-solid fa-check-circle"></i>
                        </div>
                        <div class="theme-stat-details">
                            <h3 class="theme-stat-number">
                                <?php echo isset($data['completed_count']) ? $data['completed_count'] : 0; ?>
                            </h3>
                            <p class="theme-stat-label">Completed Today</p>
                            <small class="theme-stat-description">Fully received</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <a href="<?php echo URLROOT; ?>/receiving/items_received" class="text-decoration-none">
                <div class="theme-stat-card theme-stat-info">
                    <div class="theme-stat-content">
                        <div class="theme-stat-icon">
                            <i class="fa-solid fa-boxes"></i>
                        </div>
                        <div class="theme-stat-details">
                            <h3 class="theme-stat-number">
                                <?php echo isset($data['total_items']) ? $data['total_items'] : 0; ?>
                            </h3>
                            <p class="theme-stat-label">Items Received</p>
                            <small class="theme-stat-description">This week</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="theme-action-bar">
                <div class="theme-action-group">
                    <a href="<?php echo URLROOT; ?>/receiving/pending" class="btn theme-btn-primary">
                        <i class="fa-solid fa-clock"></i> Pending Receipts
                    </a>
                    <a href="<?php echo URLROOT; ?>/receiving/partial" class="btn theme-btn-warning">
                        <i class="fa-solid fa-clock-rotate-left"></i> Partial Receipts
                    </a>
                    <a href="<?php echo URLROOT; ?>/receiving/completed" class="btn theme-btn-success">
                        <i class="fa-solid fa-check-circle"></i> Completed Receipts
                    </a>
                    <a href="<?php echo URLROOT; ?>/receiving/reports" class="btn theme-btn-info">
                        <i class="fa-solid fa-chart-bar"></i> Receiving Reports
                    </a>
                </div>
                <div class="theme-action-group">
                    <button class="btn theme-btn-secondary" onclick="refreshData()">
                        <i class="fa-solid fa-refresh"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Grid -->
    <div class="row">
        <div class="col-lg-8">
            <div class="theme-card">
                <div class="theme-card-header">
                    <h5 class="theme-card-title">
                        <i class="fa-solid fa-list"></i> Recent Activity
                    </h5>
                    <div class="theme-card-actions">
                        <button class="btn btn-sm theme-btn-outline-primary" onclick="viewAll()">
                            View All
                        </button>
                    </div>
                </div>
                <div class="theme-card-body">
                    <?php if (isset($data['recent_activity']) && !empty($data['recent_activity'])): ?>
                        <div class="theme-table-container">
                            <table class="table theme-table">
                                <thead>
                                    <tr>
                                        <th>Purchase Order</th>
                                        <th>Supplier</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['recent_activity'] as $activity): ?>
                                        <tr>
                                            <td>
                                                <strong>#<?php echo $activity->purchase_id; ?></strong>
                                            </td>
                                            <td>
                                                <i class="fa-solid fa-building text-muted"></i>
                                                <?php echo htmlspecialchars($activity->supplier_name ?? 'Unknown'); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = 'theme-badge-primary';
                                                if ($activity->status === 'received')
                                                    $statusClass = 'theme-badge-success';
                                                elseif ($activity->status === 'partially_received')
                                                    $statusClass = 'theme-badge-warning';
                                                ?>
                                                <span class="theme-badge <?php echo $statusClass; ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $activity->status)); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('M j, Y', strtotime($activity->updated_at)); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="theme-action-group theme-action-group-sm">
                                                    <a href="<?php echo URLROOT; ?>/receiving/details/<?php echo $activity->purchase_id; ?>"
                                                        class="btn btn-sm theme-btn-outline-primary" title="View Details">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    <?php if ($activity->status !== 'received'): ?>
                                                        <a href="<?php echo URLROOT; ?>/receiving/process/<?php echo $activity->purchase_id; ?>"
                                                            class="btn btn-sm theme-btn-outline-success" title="Process Receipt">
                                                            <i class="fa-solid fa-dolly"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="theme-empty-state">
                            <div class="theme-empty-icon">
                                <i class="fa-solid fa-inbox"></i>
                            </div>
                            <h5 class="theme-empty-title">No Recent Activity</h5>
                            <p class="theme-empty-description">
                                No receiving activities have been recorded recently.
                                <a href="<?php echo URLROOT; ?>/purchases/add">Create a purchase order</a> to get started.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="theme-card mb-4">
                <div class="theme-card-header">
                    <h5 class="theme-card-title">
                        <i class="fa-solid fa-bolt"></i> Quick Actions
                    </h5>
                </div>
                <div class="theme-card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/receiving/pending" class="btn theme-btn-outline-primary">
                            <i class="fa-solid fa-truck-loading"></i> Process Pending Receipts
                        </a>
                        <a href="<?php echo URLROOT; ?>/receiving/partial" class="btn theme-btn-outline-warning">
                            <i class="fa-solid fa-clock-rotate-left"></i> Continue Partial Receipts
                        </a>
                        <a href="<?php echo URLROOT; ?>/receiving/bulk-receive" class="btn theme-btn-outline-warning">
                            <i class="fa-solid fa-boxes"></i> Bulk Receive Items
                        </a>
                        <a href="<?php echo URLROOT; ?>/receiving/print-labels" class="btn theme-btn-outline-info">
                            <i class="fa-solid fa-print"></i> Print Receiving Labels
                        </a>
                        <hr class="theme-divider">
                        <a href="<?php echo URLROOT; ?>/purchases/add" class="btn theme-btn-outline-secondary">
                            <i class="fa-solid fa-plus"></i> Create Purchase Order
                        </a>
                    </div>
                </div>
            </div>

            <!-- Help & Tips -->
            <div class="theme-card">
                <div class="theme-card-header">
                    <h5 class="theme-card-title">
                        <i class="fa-solid fa-lightbulb"></i> Tips & Guidelines
                    </h5>
                </div>
                <div class="theme-card-body">
                    <div class="theme-tip-list">
                        <div class="theme-tip-item">
                            <i class="fa-solid fa-check-circle text-success"></i>
                            <span>Scan barcodes for faster processing</span>
                        </div>
                        <div class="theme-tip-item">
                            <i class="fa-solid fa-exclamation-triangle text-warning"></i>
                            <span>Verify quantities before marking complete</span>
                        </div>
                        <div class="theme-tip-item">
                            <i class="fa-solid fa-map-marker-alt text-info"></i>
                            <span>Assign proper bulk locations</span>
                        </div>
                        <div class="theme-tip-item">
                            <i class="fa-solid fa-clock text-primary"></i>
                            <span>Process receipts within 24 hours</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function refreshData() {
        window.location.reload();
    }

    function viewAll() {
        window.location.href = '<?php echo URLROOT; ?>/receiving/all';
    }

    // Auto-refresh every 5 minutes
    setInterval(function () {
        // You can implement auto-refresh logic here if needed
    }, 300000);
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>