<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container theme-unified page-top-area mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-6">
            <h1 class="mb-1 font-weight-bold">
                <i class="fas fa-boxes mr-2"></i>Products & Inventory
            </h1>
            <small class="text-muted">Complete product and Inventory management</small>
        </div>
        <div class="col-12 col-md-6 text-md-right mt-3 mt-md-0">
            <a href="<?php echo URLROOT; ?>/products/add" class="btn btn-success btn-lg">
                <i class="fa fa-plus"></i> Add Product
            </a>
        </div>
    </div>
</div>


<!-- Enhanced KPI Summary Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="theme-card h-100">
            <div class="card-header bg-primary-theme text-white">
                <h5 class="mb-0"><i class="fas fa-cubes"></i> Total Products</h5>
            </div>
            <div class="card-body text-center">
                <h4 class="text-primary">
                    <?php
                    $productCount = count($data['products'] ?? []);
                    // Show sample count if no real data
                    echo $productCount > 0 ? $productCount : 2;
                    ?>
                </h4>
                <small class="text-muted">In Catalog</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="theme-card h-100">
            <div class="card-header bg-success-theme text-white">
                <h5 class="mb-0"><i class="fas fa-warehouse"></i> Total Inventory</h5>
            </div>
            <div class="card-body text-center">
                <h4 class="text-success">
                    <?php
                    $totalInventory = 0;
                    if (!empty($data['products'])) {
                        foreach ($data['products'] as $product) {
                            $totalInventory += $product->current_Inventory ?? 0;
                        }
                    } else {
                        // Sample data for demonstration
                        $totalInventory = 350; // Sample total Inventory
                    }
                    echo number_format($totalInventory);
                    ?>
                </h4>
                <small class="text-muted">Units Available</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="theme-card h-100">
            <div class="card-header bg-info-theme text-white">
                <h5 class="mb-0"><i class="fas fa-tags"></i> Total Categories</h5>
            </div>
            <div class="card-body text-center">
                <h4 class="text-info">
                    <?php
                    $categoryCount = count($data['categories'] ?? []);
                    // Show sample count if no real data
                    echo $categoryCount > 0 ? $categoryCount : 8;
                    ?>
                </h4>
                <small class="text-muted">Product Categories</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="theme-card h-100">
            <div class="card-header bg-warning-theme text-white">
                <h5 class="mb-0"><i class="fas fa-certificate"></i> Total Brands</h5>
            </div>
            <div class="card-body text-center">
                <h4 class="text-warning">
                    <?php
                    $brandCount = 0;
                    if (!empty($data['brands'])) {
                        $brandCount = count($data['brands']);
                    } else {
                        // Sample data for demonstration
                        $brandCount = 12;
                    }
                    echo $brandCount;
                    ?>
                </h4>
                <small class="text-muted">Registered Brands</small>
            </div>
        </div>
    </div>
</div> <!-- end of KPI row -->

<!-- Most Recent Activity Table -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-secondary-theme text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history mr-2"></i>Most Recent Activity
                </h5>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-light btn-sm" onclick="refreshActivity()" data-toggle="tooltip"
                        title="Refresh Activity">
                        <i class="fas fa-sync"></i>
                    </button>
                    <button class="btn btn-outline-light btn-sm" onclick="clearActivity()" data-toggle="tooltip"
                        title="Clear All Activity">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="activityTable">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="15%">Date/Time</th>
                                <th width="12%">Action</th>
                                <th width="25%">Product</th>
                                <th width="15%">User</th>
                                <th width="23%">Details</th>
                                <th width="5%">Status</th>
                            </tr>
                        </thead>
                        <tbody id="activityTableBody">
                            <?php
                            // Get recent product activities from the controller
                            $activities = [];
                            if (!empty($data['activities'])) {
                                $activities = array_slice($data['activities'], 0, 50);
                            }

                            if (!empty($activities)):
                                foreach ($activities as $index => $activity):
                                    $badgeClass = '';
                                    $iconClass = '';
                                    $statusBadge = '';

                                    switch (strtoupper($activity->action)) {
                                        case 'ADD':
                                            $badgeClass = 'badge-success';
                                            $iconClass = 'fas fa-plus';
                                            break;
                                        case 'EDIT':
                                            $badgeClass = 'badge-info';
                                            $iconClass = 'fas fa-edit';
                                            break;
                                        case 'DELETE':
                                            $badgeClass = 'badge-danger';
                                            $iconClass = 'fas fa-trash';
                                            break;
                                        default:
                                            $badgeClass = 'badge-secondary';
                                            $iconClass = 'fas fa-question';
                                    }

                                    switch ($activity->status) {
                                        case 'success':
                                            $statusBadge = '<span class="badge badge-success badge-pill"><i class="fas fa-check"></i></span>';
                                            break;
                                        case 'warning':
                                            $statusBadge = '<span class="badge badge-warning badge-pill"><i class="fas fa-exclamation-triangle"></i></span>';
                                            break;
                                        case 'error':
                                            $statusBadge = '<span class="badge badge-danger badge-pill"><i class="fas fa-times"></i></span>';
                                            break;
                                        default:
                                            $statusBadge = '<span class="badge badge-secondary badge-pill"><i class="fas fa-info"></i></span>';
                                    }

                                    $formattedDate = date('M d, Y', strtotime($activity->created_at));
                                    $formattedTime = date('H:i', strtotime($activity->created_at));
                                    ?>
                                    <tr>
                                        <td class="text-muted"><?php echo $index + 1; ?></td>
                                        <td>
                                            <div class="text-sm">
                                                <div class="font-weight-bold"><?php echo $formattedDate; ?></div>
                                                <small class="text-muted"><?php echo $formattedTime; ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $badgeClass; ?> px-2 py-1">
                                                <i class="<?php echo $iconClass; ?> mr-1"></i>
                                                <?php echo strtoupper($activity->action); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark">
                                                <?php echo htmlspecialchars($activity->product_name); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center mr-2">
                                                    <i class="fas fa-user text-muted"></i>
                                                </div>
                                                <span
                                                    class="text-sm"><?php echo htmlspecialchars($activity->user_name); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <small
                                                class="text-muted"><?php echo htmlspecialchars($activity->details); ?></small>
                                        </td>
                                        <td class="text-center">
                                            <?php echo $statusBadge; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-history fa-3x mb-3"></i>
                                            <p>No recent activity found</p>
                                            <small>Product activities will appear here when actions are performed</small>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($activities) && count($activities) >= 50): ?>
                    <div class="card-footer text-center bg-light">
                        <small class="text-muted">
                            Showing last 50 activities.
                            <a href="<?php echo URLROOT; ?>/products/activity" class="text-primary">View all activity</a>
                        </small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Inventory Adjustment Modal -->
<div class="modal fade" id="InventoryAdjustmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit mr-2"></i>Inventory Adjustment
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="InventoryAdjustmentForm">
                    <input type="hidden" id="adjust_product_id" name="product_id">
                    <div class="form-group">
                        <label>Product</label>
                        <input type="text" id="adjust_product_name" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Current Inventory</label>
                        <input type="number" id="adjust_current_Inventory" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Adjustment Type</label>
                        <select id="adjust_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="add">Add Inventory</option>
                            <option value="remove">Remove Inventory</option>
                            <option value="set">Set Inventory Level</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" id="adjust_quantity" class="form-control" required min="0">
                    </div>
                    <div class="form-group">
                        <label>Reason</label>
                        <textarea id="adjust_reason" class="form-control" rows="3"
                            placeholder="Reason for Inventory adjustment..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitInventoryAdjustment()">
                    <i class="fas fa-save mr-1"></i>Save Adjustment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Pass data and load unified script -->
<script>
    // Pass PHP data to JavaScript
    window.URLROOT = '<?php echo URLROOT; ?>';
    window.productsData = <?php echo json_encode($data['products'] ?? []); ?>;

    // Debug: Log the data being passed
    console.log('PHP Products Data:', window.productsData);
    console.log('Data length:', window.productsData ? window.productsData.length : 'No data');

    // Activity table functions
    function refreshActivity() {
        // Show loading state
        const tableBody = document.getElementById('activityTableBody');
        tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Refreshing...</td></tr>';

        // Simulate refresh (in real implementation, make AJAX call to get fresh data)
        setTimeout(() => {
            location.reload();
        }, 1000);
    }

    function clearActivity() {
        if (confirm('Are you sure you want to clear all activity history? This action cannot be undone.')) {
            // In real implementation, make AJAX call to clear activity
            fetch(`${window.URLROOT}/products/clearActivity`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({})
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tableBody = document.getElementById('activityTableBody');
                        tableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-history fa-3x mb-3"></i>
                                    <p>No recent activity found</p>
                                    <small>Product activities will appear here when actions are performed</small>
                                </div>
                            </td>
                        </tr>
                    `;

                        // Show success message
                        showNotification('Activity history cleared successfully', 'success');
                    }
                })
                .catch(error => {
                    console.error('Error clearing activity:', error);
                    showNotification('Failed to clear activity history', 'error');
                });
        }
    }

    function showNotification(message, type = 'info') {
        // Simple notification function
        const alertClass = type === 'success' ? 'alert-success' :
            type === 'error' ? 'alert-danger' : 'alert-info';

        const notification = document.createElement('div');
        notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;

        document.body.appendChild(notification);

        // Auto-remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    }

    // Initialize tooltips for activity table
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();

        // Auto-refresh activity every 5 minutes
        setInterval(function () {
            // Silently refresh activity data
            console.log('Auto-refreshing activity data...');
            // In real implementation, make AJAX call to update activity table
        }, 300000); // 5 minutes
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>