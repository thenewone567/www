<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container">
    <!-- Page Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <h1 class="mb-0">
                <i class="fas fa-warehouse"></i>
                Inventory Management
            </h1>
            <p class="text-muted mb-0">Advanced stock control and monitoring system</p>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-cubes"></i> Total SKUs</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-primary"><?php echo count($data['products'] ?? []); ?></h3>
                    <small class="text-success"><i class="fas fa-arrow-up"></i> +2.3% this month</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-success-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-boxes"></i> Units in Stock</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-success">
                        <?php
                        $totalStock = 0;
                        if (!empty($data['products'])) {
                            foreach ($data['products'] as $product) {
                                $totalStock += $product->current_stock ?? 0;
                            }
                        }
                        echo number_format($totalStock);
                        ?>
                    </h3>
                    <small class="text-warning"><i class="fas fa-arrow-down"></i> -1.2% this week</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-info-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-dollar-sign"></i> Inventory Value</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-info">
                        $<?php
                        $totalValue = 0;
                        if (!empty($data['products'])) {
                            foreach ($data['products'] as $product) {
                                if (isset($product->purchase_price) && $product->current_stock > 0) {
                                    $totalValue += ($product->purchase_price * $product->current_stock);
                                }
                            }
                        }
                        echo number_format($totalValue, 0);
                        ?>
                    </h3>
                    <small class="text-success"><i class="fas fa-arrow-up"></i> +5.7% this month</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-warning-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Critical Items</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-warning">
                        <?php
                        $lowStockCount = 0;
                        $outOfStockCount = 0;
                        if (!empty($data['products'])) {
                            foreach ($data['products'] as $product) {
                                if ($product->current_stock <= 0) {
                                    $outOfStockCount++;
                                } elseif ($product->current_stock <= $product->reorder_level) {
                                    $lowStockCount++;
                                }
                            }
                        }
                        echo $lowStockCount + $outOfStockCount;
                        ?>
                    </h3>
                    <small class="text-danger"><?php echo $outOfStockCount; ?> out of stock,
                        <?php echo $lowStockCount; ?> low stock</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Cards Row -->
    <div class="row mb-4">
        <!-- Stock Take Section -->
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-clipboard-check"></i> Stock Take</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Perform comprehensive stock verification.</p>
                    <button class="btn btn-primary btn-block" onclick="performStockTake()">
                        <i class="fas fa-clipboard-check"></i> Start Stock Take
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Operations Section -->
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="theme-card">
                <div class="card-header bg-info-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-upload"></i> Bulk Operations</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Update multiple items at once.</p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-info" onclick="bulkStockUpdate()">
                            <i class="fas fa-upload"></i> Bulk Update
                        </button>
                        <button class="btn btn-info" onclick="importInventory()">
                            <i class="fas fa-file-import"></i> Import Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts Section -->
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="theme-card">
                <div class="card-header bg-warning-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-bell"></i> Alerts</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Monitor stock levels and alerts.</p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-warning" onclick="generateAlerts()">
                            <i class="fas fa-exclamation-triangle"></i> View Alerts
                        </button>
                        <button class="btn btn-warning" onclick="configureAlerts()">
                            <i class="fas fa-cog"></i> Configure
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="theme-card">
                <div class="card-header bg-success-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Fast inventory adjustments.</p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-success" onclick="quickStockAdjustment()">
                            <i class="fas fa-plus-circle"></i> Quick Adjust
                        </button>
                        <a href="<?php echo URLROOT; ?>/products/add" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add Product
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="row">
        <!-- Inventory Overview -->
        <div class="col-lg-8">
            <!-- Stock Status Overview -->
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar"></i>
                        Inventory Status Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <canvas id="stockStatusChart" width="400" height="200"></canvas>
                        </div>
                        <div class="col-md-4">
                            <?php
                            $inStock = 0;
                            $lowStock = 0;
                            $outOfStock = 0;

                            if (!empty($data['products'])) {
                                foreach ($data['products'] as $product) {
                                    if ($product->current_stock <= 0) {
                                        $outOfStock++;
                                    } elseif ($product->current_stock <= $product->reorder_level) {
                                        $lowStock++;
                                    } else {
                                        $inStock++;
                                    }
                                }
                            }
                            ?>
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-circle text-success"></i> In Stock</span>
                                    <span class="badge badge-success"><?php echo $inStock; ?></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-circle text-warning"></i> Low Stock</span>
                                    <span class="badge badge-warning"><?php echo $lowStock; ?></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-circle text-danger"></i> Out of Stock</span>
                                    <span class="badge badge-danger"><?php echo $outOfStock; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Widgets -->
        <div class="col-lg-4">
            <!-- Critical Alerts -->
            <div class="theme-card">
                <div class="card-header bg-warning-theme text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bell"></i>
                        Critical Alerts
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['products'])): ?>
                        <?php
                        $criticalItems = array_filter($data['products'], function ($product) {
                            return $product->current_stock <= $product->reorder_level;
                        });
                        $criticalItems = array_slice($criticalItems, 0, 5);
                        ?>
                        <?php if (empty($criticalItems)): ?>
                            <div class="text-center text-success">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <p>All items are well stocked!</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($criticalItems as $item): ?>
                                    <div class="list-group-item d-flex align-items-center">
                                        <div class="me-3" style="margin-right: 1rem;">
                                            <?php if ($item->current_stock <= 0): ?>
                                                <i class="fas fa-times-circle text-danger"></i>
                                            <?php else: ?>
                                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="font-weight-medium">
                                                <?php echo htmlspecialchars(substr($item->product_name, 0, 20)); ?>...
                                            </div>
                                            <small class="text-muted">Stock: <?php echo $item->current_stock; ?> | Min:
                                                <?php echo $item->reorder_level; ?></small>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="adjustStock(<?php echo $item->product_id; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No products found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Controls and Table -->
    <div class="theme-card">
        <div class="card-header bg-secondary-theme text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i>
                    Inventory Management
                </h5>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-light" onclick="refreshData()">
                        <i class="fas fa-sync"></i> Refresh
                    </button>
                    <button class="btn btn-sm btn-outline-light" onclick="exportInventory()">
                        <i class="fas fa-download"></i> Export
                    </button>
                    <button class="btn btn-sm btn-outline-light" onclick="printReport()">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Search and Filters -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" id="inventorySearch" class="form-control" placeholder="Search products...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select id="categoryFilter" class="form-control">
                        <option value="">All Categories</option>
                        <?php if (!empty($data['categories'])): ?>
                            <?php foreach ($data['categories'] as $category): ?>
                                <option value="<?php echo $category->category_id; ?>">
                                    <?php echo htmlspecialchars($category->category_name); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="statusFilter" class="form-control">
                        <option value="">All Status</option>
                        <option value="in_stock">In Stock</option>
                        <option value="low_stock">Low Stock</option>
                        <option value="out_of_stock">Out of Stock</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="locationFilter" class="form-control">
                        <option value="">All Locations</option>
                        <option value="warehouse_a">Warehouse A</option>
                        <option value="warehouse_b">Warehouse B</option>
                        <option value="showroom">Showroom</option>
                        <option value="storage">Storage</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary btn-block" onclick="applyFilters()">
                        <i class="fas fa-filter"></i> Apply
                    </button>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-outline-secondary btn-block" onclick="clearFilters()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <?php if (!empty($product->image_path)): ?>
                <img src="<?php echo URLROOT . '/' . $product->image_path; ?>" class="rounded"
                    style="width: 50px; height: 50px; object-fit: cover;">
            <?php else: ?>
                <div class="placeholder-image">
                    <i class="fas fa-cube"></i>
                </div>
            <?php endif; ?>
        </div>
        </td>
        <td>
            <div class="product-details">
                <div class="product-name font-weight-bold">
                    <?php echo htmlspecialchars($product->product_name); ?>
                </div>
                <div class="product-sku text-muted">
                    SKU: <?php echo htmlspecialchars($product->sku ?? 'N/A'); ?>
                </div>
                <div class="product-brand text-muted">
                    <?php echo htmlspecialchars($product->brand_name ?? 'No Brand'); ?>
                </div>
            </div>
        </td>
        <td>
            <span class="location-badge">
                <i class="fas fa-map-marker-alt"></i>
                <?php echo htmlspecialchars($product->location ?? 'Main'); ?>
            </span>
        </td>
        <td>
            <div class="stock-display">
                <span class="stock-number font-weight-bold">
                    <?php echo number_format($product->current_stock ?? 0); ?>
                </span>
                <span class="stock-unit text-muted">
                    <?php echo htmlspecialchars($product->unit ?? 'units'); ?>
                </span>
            </div>
        </td>
        <td>
            <span class="reorder-level">
                <?php echo number_format($product->reorder_level ?? 0); ?>
            </span>
        </td>
        <td>
            <div class="value-display">
                <div class="total-value font-weight-bold">
                    $<?php echo number_format(($product->purchase_price ?? 0) * ($product->current_stock ?? 0), 2); ?>
                </div>
                <div class="unit-price text-muted">
                    $<?php echo number_format($product->purchase_price ?? 0, 2); ?>/unit
                </div>
            </div>
        </td>
        <td>
            <div class="turnover-info">
                <div class="turnover-rate">
                    <span class="badge badge-info">
                        <?php echo rand(1, 12); ?>x/yr
                    </span>
                </div>
                <div class="last-movement text-muted">
                    <?php echo date('M d', strtotime('-' . rand(1, 30) . ' days')); ?>
                </div>
            </div>
        </td>
        <td>
            <?php
            $stock = $product->current_stock ?? 0;
            $reorder = $product->reorder_level ?? 0;

            if ($stock <= 0) {
                echo '<span class="status-badge status-out-of-stock">
                                                            <i class="fas fa-times-circle"></i> Out of Stock
                                                          </span>';
            } elseif ($stock <= $reorder) {
                echo '<span class="status-badge status-low-stock">
                                                            <i class="fas fa-exclamation-triangle"></i> Low Stock
                                                          </span>';
            } elseif ($stock <= ($reorder * 2)) {
                echo '<span class="status-badge status-medium-stock">
                                                            <i class="fas fa-minus-circle"></i> Medium
                                                          </span>';
            } else {
                echo '<span class="status-badge status-good-stock">
                                                            <i class="fas fa-check-circle"></i> Good
                                                          </span>';
            }
            ?>
        </td>
        <td>
            <!-- Inventory Table -->
            <div class="theme-table">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th width="5%">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Current Stock</th>
                            <th>Min. Level</th>
                            <th>Value</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['products'])): ?>
                            <?php foreach ($data['products'] as $product): ?>
                                <tr data-product-id="<?php echo $product->product_id; ?>">
                                    <td>
                                        <input type="checkbox" class="product-checkbox"
                                            value="<?php echo $product->product_id; ?>">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-2"
                                                style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; margin-right: 0.5rem;">
                                                <i class="fas fa-box text-muted"></i>
                                            </div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($product->product_name); ?></strong>
                                                <?php if (!empty($product->brand_name)): ?>
                                                    <br><small
                                                        class="text-muted"><?php echo htmlspecialchars($product->brand_name); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code><?php echo htmlspecialchars($product->sku ?? 'N/A'); ?></code>
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-<?php echo ($product->current_stock <= 0) ? 'danger' : (($product->current_stock <= $product->reorder_level) ? 'warning' : 'success'); ?>">
                                            <?php echo number_format($product->current_stock ?? 0); ?>
                                        </span>
                                    </td>
                                    <td><?php echo number_format($product->reorder_level ?? 0); ?></td>
                                    <td>
                                        <?php
                                        $value = ($product->purchase_price ?? 0) * ($product->current_stock ?? 0);
                                        echo '$' . number_format($value, 2);
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($product->current_stock <= 0): ?>
                                            <span class="badge badge-danger">Out of Stock</span>
                                        <?php elseif ($product->current_stock <= $product->reorder_level): ?>
                                            <span class="badge badge-warning">Low Stock</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">In Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="theme-action-group">
                                            <button class="btn btn-sm btn-primary"
                                                onclick="adjustStock(<?php echo $product->product_id; ?>)" title="Adjust Stock">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info"
                                                onclick="viewMovements(<?php echo $product->product_id; ?>)"
                                                title="View History">
                                                <i class="fas fa-history"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning"
                                                onclick="setReorderLevel(<?php echo $product->product_id; ?>)"
                                                title="Set Reorder">
                                                <i class="fas fa-bell"></i>
                                            </button>
                                            <button class="btn btn-sm btn-secondary"
                                                onclick="printLabel(<?php echo $product->product_id; ?>)" title="Print Label">
                                                <i class="fas fa-barcode"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
                                    <br>
                                    <strong>No inventory items found</strong>
                                    <br>
                                    <small>Start by adding products to your inventory</small>
                                    <br><br>
                                    <a href="<?php echo URLROOT; ?>/products/add" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add First Product
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Bulk Actions Panel -->
            <div id="bulkActionsPanel" class="alert alert-info mt-3" style="display: none;">
                <div class="d-flex justify-content-between align-items-center">
                    <span><strong id="selectedCount">0</strong> items selected</span>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="bulkAdjustStock()">
                            <i class="fas fa-edit"></i> Bulk Adjust
                        </button>
                        <button class="btn btn-outline-info" onclick="bulkSetReorder()">
                            <i class="fas fa-bell"></i> Set Reorder
                        </button>
                        <button class="btn btn-outline-warning" onclick="bulkPrintLabels()">
                            <i class="fas fa-barcode"></i> Print Labels
                        </button>
                        <button class="btn btn-outline-secondary" onclick="bulkExport()">
                            <i class="fas fa-download"></i> Export Selected
                        </button>
                    </div>
                </div>
            </div>
    </div>
</div>
</div>

<!-- Enhanced Modals -->
<!-- Stock Adjustment Modal -->
<div class="modal fade" id="stockAdjustmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-edit mr-2"></i>Stock Adjustment
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="stockAdjustmentForm">
                    <input type="hidden" id="adjust_product_id" name="product_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Product</label>
                                <input type="text" id="adjust_product_name" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Current Stock</label>
                                <div class="input-group">
                                    <input type="number" id="adjust_current_stock" class="form-control" readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text">units</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Adjustment Type</label>
                                <select id="adjust_type" class="form-control" required>
                                    <option value="">Select Type</option>
                                    <option value="add">Add Stock (+)</option>
                                    <option value="remove">Remove Stock (-)</option>
                                    <option value="set">Set Stock Level (=)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Quantity</label>
                                <input type="number" id="adjust_quantity" class="form-control" required min="0"
                                    step="1">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Reason for Adjustment</label>
                        <select id="adjust_reason_type" class="form-control mb-2">
                            <option value="">Select Reason</option>
                            <option value="damaged">Damaged/Lost</option>
                            <option value="expired">Expired</option>
                            <option value="theft">Theft/Missing</option>
                            <option value="returned">Customer Return</option>
                            <option value="found">Found/Located</option>
                            <option value="received">New Stock Received</option>
                            <option value="transfer">Location Transfer</option>
                            <option value="correction">Inventory Correction</option>
                            <option value="other">Other</option>
                        </select>
                        <textarea id="adjust_reason" class="form-control" rows="3"
                            placeholder="Additional notes (optional)"></textarea>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Preview:</strong>
                        <span id="adjustment_preview">Make your selections to see the preview</span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="submitStockAdjustment()">
                    <i class="fas fa-save mr-1"></i>Apply Adjustment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js for visualizations -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function () {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Search functionality
        $('#searchBox').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            $('tbody tr').filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // Filter functionality
        $('#categoryFilter, #statusFilter').on('change', function () {
            filterTable();
        });

        // Checkbox functionality
        $('#selectAll').on('change', function () {
            toggleSelectAll();
        });

        $('.product-checkbox').on('change', function () {
            updateBulkActions();
        });
    });

    function toggleSelectAll() {
        var isChecked = $('#selectAll').is(':checked');
        $('.product-checkbox').prop('checked', isChecked);
        updateBulkActions();
    }

    function updateBulkActions() {
        var selectedCount = $('.product-checkbox:checked').length;
        $('#selectedCount').text(selectedCount);

        if (selectedCount > 0) {
            $('#bulkActionsPanel').fadeIn();
        } else {
            $('#bulkActionsPanel').fadeOut();
        }
    }

    function filterTable() {
        var categoryFilter = $('#categoryFilter').val();
        var statusFilter = $('#statusFilter').val();

        $('tbody tr').each(function () {
            var show = true;

            // Add your filtering logic here
            // This is a placeholder - implement based on your data structure

            $(this).toggle(show);
        });
    }

    // Stock status data from PHP
    const stockStatusData = {
        inStock: <?php echo json_encode($inStock ?? 0); ?>,
        lowStock: <?php echo json_encode($lowStock ?? 0); ?>,
        outOfStock: <?php echo json_encode($outOfStock ?? 0); ?>
    };

    document.addEventListener('DOMContentLoaded', function () {
        initializeStockStatusChart();
        // ...existing code...
    });

    function initializeStockStatusChart() {
        const ctx = document.getElementById('stockStatusChart');
        if (!ctx) return;
        const total = stockStatusData.inStock + stockStatusData.lowStock + stockStatusData.outOfStock;
        if (total === 0) {
            ctx.parentNode.innerHTML = '<div class="text-center text-muted py-5">No inventory data available for chart.</div>';
            return;
        }
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['In Stock', 'Low Stock', 'Out of Stock'],
                datasets: [{
                    data: [stockStatusData.inStock, stockStatusData.lowStock, stockStatusData.outOfStock],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '60%'
            }
        });
    }
    function adjustStock(productId) {
        // Implementation for stock adjustment
        alert('Adjust stock for product ID: ' + productId);
    }

    function viewMovements(productId) {
        // Implementation for viewing movements
        window.location.href = '<?php echo URLROOT; ?>/inventory/movements/' + productId;
    }

    function setReorderLevel(productId) {
        // Implementation for setting reorder level
        var newLevel = prompt('Enter new reorder level:');
        if (newLevel !== null) {
            // Ajax call to update reorder level
            alert('Reorder level updated for product ID: ' + productId);
        }
    }

    function printLabel(productId) {
        // Implementation for printing label
        window.open('<?php echo URLROOT; ?>/inventory/print-label/' + productId, '_blank');
    }

    function bulkAdjustStock() {
        var selectedIds = $('.product-checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        if (selectedIds.length > 0) {
            alert('Bulk adjust stock for ' + selectedIds.length + ' products');
        }
    }

    function bulkSetReorder() {
        var selectedIds = $('.product-checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        if (selectedIds.length > 0) {
            var newLevel = prompt('Enter reorder level for selected products:');
            if (newLevel !== null) {
                alert('Bulk reorder level set for ' + selectedIds.length + ' products');
            }
        }
    }

    function bulkPrintLabels() {
        var selectedIds = $('.product-checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        if (selectedIds.length > 0) {
            window.open('<?php echo URLROOT; ?>/inventory/bulk-print-labels?ids=' + selectedIds.join(','), '_blank');
        }
    }

    function bulkExport() {
        var selectedIds = $('.product-checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        if (selectedIds.length > 0) {
            window.location.href = '<?php echo URLROOT; ?>/inventory/export?ids=' + selectedIds.join(',');
        }
    }
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>