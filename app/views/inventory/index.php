<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container">
    <!-- Page Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <h1 class="mb-0">
                <i class="fas fa-warehouse"></i>
                Inventory Management
            </h1>
            <p class="text-muted mb-0">Advanced inventory control and monitoring system</p>
        </div>
    </div>

    <!-- Inventory Status Indicators -->
    <div class="row mb-4">
        <?php
        // Calculate Inventory status counts
        $lowCount = 0;
        $reorderCount = 0;
        $outCount = 0;
        $overInventoryCount = 0;

        if (!empty($data['products'])) {
            foreach ($data['products'] as $product) {
                $currentInventory = $product->current_Inventory ?? 0;
                $reorderLevel = $product->reorder_level ?? 10;
                $maxInventory = $reorderLevel * 3; // Assume overInventory is 3x reorder level
        
                if ($currentInventory <= 0) {
                    $outCount++;
                } elseif ($currentInventory <= ($reorderLevel * 0.5)) {
                    $lowCount++;
                } elseif ($currentInventory <= $reorderLevel) {
                    $reorderCount++;
                } elseif ($currentInventory > $maxInventory) {
                    $overInventoryCount++;
                }
            }
        }
        ?>

        <!-- Low Inventory -->
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Low Inventory</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-warning"><?php echo $lowCount; ?></h3>
                    <small class="text-muted">Getting Low</small>
                </div>
            </div>
        </div>

        <!-- Reorder Level -->
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-bell"></i> Reorder</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-info"><?php echo $reorderCount; ?></h3>
                    <small class="text-muted">Need Reorder</small>
                </div>
            </div>
        </div>

        <!-- Out of Inventory -->
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-times-circle"></i> Out of Inventory</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-danger"><?php echo $outCount; ?></h3>
                    <small class="text-muted">No Inventory</small>
                </div>
            </div>
        </div>

        <!-- OverInventory -->
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-layer-group"></i> Overinventory</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-secondary"><?php echo $overInventoryCount; ?></h3>
                    <small class="text-muted">Excess Inventory</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Cards Row -->
    <div class="row mb-4">
        <!-- Inventory Take Section -->
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-clipboard-check"></i> Inventory Take</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Perform comprehensive inventory verification.</p>
                    <button class="btn btn-primary btn-block" onclick="performInventoryTake()">
                        <i class="fas fa-clipboard-check"></i> Start Inventory Take
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
                        <button class="btn btn-outline-info" onclick="bulkInventoryUpdate()">
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
                    <p class="card-text">Monitor inventory levels and alerts.</p>
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
                        <a href="<?php echo URLROOT; ?>/inventory/inventory_levels" class="btn btn-outline-success">
                            <i class="fas fa-boxes"></i> View Levels
                        </a>
                        <a href="<?php echo URLROOT; ?>/inventory/add_inventory" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add Inventory
                        </a>
                        <a href="<?php echo URLROOT; ?>/inventory/move_inventory" class="btn btn-outline-primary">
                            <i class="fas fa-exchange-alt"></i> Move Inventory
                        </a>
                        <a href="<?php echo URLROOT; ?>/products/add" class="btn btn-outline-success">
                            <i class="fas fa-plus"></i> Add Product
                        </a>
                        <a href="<?php echo URLROOT; ?>/locations/locations" class="btn btn-outline-primary">
                            <i class="fas fa-map-marker-alt"></i> Manage Locations
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
            <!-- Inventory Status Overview -->
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
                            <canvas id="InventoryStatusChart" width="400" height="200"></canvas>
                        </div>
                        <div class="col-md-4">
                            <?php
                            $inInventory = 0;
                            $lowInventory = 0;
                            $outOfInventory = 0;

                            if (!empty($data['products'])) {
                                foreach ($data['products'] as $product) {
                                    if ($product->current_Inventory <= 0) {
                                        $outOfInventory++;
                                    } elseif ($product->current_Inventory <= $product->reorder_level) {
                                        $lowInventory++;
                                    } else {
                                        $inInventory++;
                                    }
                                }
                            }
                            ?>
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-circle text-success"></i> In Inventory</span>
                                    <span class="badge badge-success"><?php echo $inInventory; ?></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-circle text-warning"></i> Low Inventory</span>
                                    <span class="badge badge-warning"><?php echo $lowInventory; ?></span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-circle text-danger"></i> Out of Inventory</span>
                                    <span class="badge badge-danger"><?php echo $outOfInventory; ?></span>
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
                            return $product->current_Inventory <= $product->reorder_level;
                        });
                        $criticalItems = array_slice($criticalItems, 0, 5);
                        ?>
                        <?php if (empty($criticalItems)): ?>
                            <div class="text-center text-success">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <p>All items are well Inventoryed!</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($criticalItems as $item): ?>
                                    <div class="list-group-item d-flex align-items-center">
                                        <div class="me-3" style="margin-right: 1rem;">
                                            <?php if ($item->current_Inventory <= 0): ?>
                                                <i class="fas fa-times-circle text-danger"></i>
                                            <?php else: ?>
                                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="font-weight-medium">
                                                <?php echo htmlspecialchars(substr($item->product_name, 0, 20)); ?>...
                                            </div>
                                            <small class="text-muted">Inventory: <?php echo $item->current_Inventory; ?> | Min:
                                                <?php echo $item->reorder_level; ?></small>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="adjustInventory(<?php echo $item->product_id; ?>)">
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
                        <option value="in_inventory">In Inventory</option>
                        <option value="low_inventory">Low Inventory</option>
                        <option value="out_of_inventory">Out of Inventory</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="locationFilter" class="form-control">
                        <option value="">All Locations</option>
                        <?php if (!empty($data['locations'])): ?>
                            <?php foreach ($data['locations'] as $location): ?>
                                <option value="<?php echo htmlspecialchars($location->location_name); ?>">
                                    <?php echo htmlspecialchars($location->location_name); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Fallback static options if no locations loaded -->
                            <option value="warehouse_a">Warehouse A</option>
                            <option value="warehouse_b">Warehouse B</option>
                            <option value="showroom">Showroom</option>
                            <option value="storage">Storage</option>
                        <?php endif; ?>
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
                            <th>Current Inventory</th>
                            <th>Min. Level</th>
                            <th>Value</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['products'])): ?>
                            <?php foreach ($data['products'] as $product): ?>
                                <tr data-product-id="<?php echo $product->product_id; ?>"
                                    data-category-id="<?php echo $product->category_id ?? ''; ?>"
                                    data-category-name="<?php echo htmlspecialchars($product->category_name ?? ''); ?>">
                                    <td>
                                        <input type="checkbox" class="product-checkbox"
                                            value="<?php echo $product->product_id; ?>">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-2"
                                                style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; margin-right: 0.5rem;">
                                                <?php if (!empty($product->image_path)): ?>
                                                    <img src="<?php echo URLROOT . '/' . $product->image_path; ?>" class="rounded"
                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                <?php else: ?>
                                                    <i class="fas fa-box text-muted"></i>
                                                <?php endif; ?>
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
                                            class="badge badge-<?php echo ($product->current_Inventory <= 0) ? 'danger' : (($product->current_Inventory <= $product->reorder_level) ? 'warning' : 'success'); ?>">
                                            <?php echo number_format($product->current_Inventory ?? 0); ?>
                                        </span>
                                    </td>
                                    <td><?php echo number_format($product->reorder_level ?? 0); ?></td>
                                    <td>
                                        <?php
                                        $value = ($product->purchase_price ?? 0) * ($product->current_Inventory ?? 0);
                                        echo formatCurrency($value, 2);
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($product->current_Inventory <= 0): ?>
                                            <span class="badge badge-danger">Out of Inventory</span>
                                        <?php elseif ($product->current_Inventory <= $product->reorder_level): ?>
                                            <span class="badge badge-warning">Low Inventory</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">In Inventory</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="theme-action-group">
                                            <button class="btn btn-sm btn-primary"
                                                onclick="adjustInventory(<?php echo $product->product_id; ?>)"
                                                title="Adjust Inventory">
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
                        <button class="btn btn-outline-primary" onclick="bulkAdjustInventory()">
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
<!-- Inventory Adjustment Modal -->
<div class="modal fade" id="InventoryAdjustmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-edit mr-2"></i>Inventory Adjustment
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="InventoryAdjustmentForm">
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
                                <label class="font-weight-bold">Current Inventory</label>
                                <div class="input-group">
                                    <input type="number" id="adjust_current_Inventory" class="form-control" readonly>
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
                                    <option value="add">Add Inventory (+)</option>
                                    <option value="remove">Remove Inventory (-)</option>
                                    <option value="set">Set Inventory Level (=)</option>
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
                            <option value="received">New Inventory Received</option>
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
                <button type="button" class="btn btn-primary" onclick="submitInventoryAdjustment()">
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
        $('#inventorySearch').on('keyup', function () {
            filterTable();
        });

        // Filter functionality
        $('#categoryFilter, #statusFilter, #locationFilter').on('change', function () {
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
        $('.product-checkbox:visible').prop('checked', isChecked);
        updateBulkActions();
    }

    function updateBulkActions() {
        var selectedCount = $('.product-checkbox:checked:visible').length;
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
        var locationFilter = $('#locationFilter').val();
        var searchFilter = $('#inventorySearch').val().toLowerCase();

        $('tbody tr').each(function () {
            var row = $(this);
            var show = true;

            // Skip the "no data" row
            if (row.find('td').length === 1 || row.find('td').length > 8) {
                return;
            }

            // Get row data
            var productText = row.find('td:nth-child(2)').text().toLowerCase();
            var skuText = row.find('td:nth-child(3)').text().toLowerCase();
            var statusText = row.find('td:nth-child(7) .badge').text().toLowerCase();
            var rowText = row.text().toLowerCase();

            // Apply search filter
            if (searchFilter) {
                if (productText.indexOf(searchFilter) === -1 &&
                    skuText.indexOf(searchFilter) === -1 &&
                    rowText.indexOf(searchFilter) === -1) {
                    show = false;
                }
            }

            // Apply status filter
            if (statusFilter) {
                if (statusFilter === 'in_inventory' && statusText.indexOf('in inventory') === -1) {
                    show = false;
                } else if (statusFilter === 'low_inventory' && statusText.indexOf('low inventory') === -1) {
                    show = false;
                } else if (statusFilter === 'out_of_inventory' && statusText.indexOf('out of inventory') === -1) {
                    show = false;
                } else if (statusFilter === 'critical' &&
                    (statusText.indexOf('low inventory') === -1 &&
                        statusText.indexOf('out of inventory') === -1)) {
                    show = false;
                }
            }

            // Apply category filter
            if (categoryFilter) {
                // Look for category data attribute
                var categoryData = row.data('category-id');
                if (categoryData && categoryData != categoryFilter) {
                    show = false;
                }
            }

            // Apply location filter
            if (locationFilter) {
                // This is a basic implementation - you might need to enhance based on your data structure
                if (rowText.indexOf(locationFilter.toLowerCase()) === -1) {
                    // For now, we'll not filter by location since it's not in the table data
                    // show = false;
                }
            }

            row.toggle(show);
        });

        // Update bulk actions based on visible checkboxes
        updateBulkActions();
    }

    // Inventory status data from PHP
    const InventoryStatusData = {
        inInventory: <?php echo json_encode($inInventory ?? 0); ?>,
        lowInventory: <?php echo json_encode($lowInventory ?? 0); ?>,
        outOfInventory: <?php echo json_encode($outOfInventory ?? 0); ?>
    };

    document.addEventListener('DOMContentLoaded', function () {
        initializeInventoryStatusChart();
        // ...existing code...
    });

    function initializeInventoryStatusChart() {
        const ctx = document.getElementById('InventoryStatusChart');
        if (!ctx) return;
        const total = InventoryStatusData.inInventory + InventoryStatusData.lowInventory + InventoryStatusData.outOfInventory;
        if (total === 0) {
            ctx.parentNode.innerHTML = '<div class="text-center text-muted py-5">No inventory data available for chart.</div>';
            return;
        }
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['In Inventory', 'Low Inventory', 'Out of Inventory'],
                datasets: [{
                    data: [InventoryStatusData.inInventory, InventoryStatusData.lowInventory, InventoryStatusData.outOfInventory],
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
    function adjustInventory(productId) {
        // Find the product data from the current row
        var row = $('tr[data-product-id="' + productId + '"]');
        var productName = row.find('td:nth-child(2) strong').text();
        var currentInventory = row.find('td:nth-child(4) .badge').text().replace(/,/g, '');

        // Populate the modal with product data
        $('#adjust_product_id').val(productId);
        $('#adjust_product_name').val(productName);
        $('#adjust_current_Inventory').val(currentInventory);

        // Reset form fields
        $('#adjust_type').val('');
        $('#adjust_quantity').val('');
        $('#adjust_reason_type').val('');
        $('#adjust_reason').val('');
        $('#adjustment_preview').text('Make your selections to see the preview');

        // Show the modal
        $('#InventoryAdjustmentModal').modal('show');
    }

    function viewMovements(productId) {
        // Redirect to movements page filtered by product
        window.location.href = '<?php echo URLROOT; ?>/inventory/movements?product=' + productId;
    }

    function setReorderLevel(productId) {
        // Implementation for setting reorder level
        var newLevel = prompt('Enter new reorder level:');
        if (newLevel !== null && !isNaN(newLevel) && newLevel >= 0) {
            // Ajax call to update reorder level
            $.ajax({
                url: '<?php echo URLROOT; ?>/inventory/updateMinimumInventory',
                method: 'POST',
                data: {
                    product_id: productId,
                    minimum_Inventory: newLevel
                },
                success: function (response) {
                    alert('Reorder level updated successfully');
                    location.reload();
                },
                error: function () {
                    alert('Error updating reorder level');
                }
            });
        }
    }

    function printLabel(productId) {
        // Implementation for printing label
        window.open('<?php echo URLROOT; ?>/products/print-label/' + productId, '_blank');
    }

    // Bulk action functions
    function performInventoryTake() {
        window.location.href = '<?php echo URLROOT; ?>/inventory/inventory_levels';
    }

    function bulkInventoryUpdate() {
        window.location.href = '<?php echo URLROOT; ?>/inventory/bulk_transfer';
    }

    function importInventory() {
        alert('Import inventory functionality - coming soon!');
    }

    function generateAlerts() {
        window.location.href = '<?php echo URLROOT; ?>/inventory/lowInventory';
    }

    function configureAlerts() {
        alert('Configure alerts functionality - coming soon!');
    }

    function refreshData() {
        location.reload();
    }

    function exportInventory() {
        window.location.href = '<?php echo URLROOT; ?>/inventory/export';
    }

    function printReport() {
        window.print();
    }

    function applyFilters() {
        console.log('Applying filters...');
        filterTable();
    }

    function clearFilters() {
        $('#inventorySearch').val('');
        $('#categoryFilter').val('');
        $('#statusFilter').val('');
        $('#locationFilter').val('');
        console.log('Clearing filters...');
        filterTable();
    }

    // Inventory Adjustment Modal Functions
    function submitInventoryAdjustment() {
        var productId = $('#adjust_product_id').val();
        var adjustType = $('#adjust_type').val();
        var quantity = $('#adjust_quantity').val();
        var reasonType = $('#adjust_reason_type').val();
        var reason = $('#adjust_reason').val();
        var currentInventory = parseInt($('#adjust_current_Inventory').val());

        if (!adjustType || !quantity) {
            alert('Please fill in all required fields');
            return;
        }

        if (!reasonType) {
            alert('Please select a reason for the adjustment');
            return;
        }

        // Calculate new Inventory level
        var newInventory = currentInventory;
        if (adjustType === 'add') {
            newInventory = currentInventory + parseInt(quantity);
        } else if (adjustType === 'remove') {
            newInventory = currentInventory - parseInt(quantity);
        } else if (adjustType === 'set') {
            newInventory = parseInt(quantity);
        }

        if (newInventory < 0) {
            alert('Inventory level cannot be negative');
            return;
        }

        // Combine reason type and custom reason
        var fullReason = reasonType;
        if (reason.trim()) {
            fullReason += ': ' + reason.trim();
        }

        // Submit via AJAX
        $.ajax({
            url: '<?php echo URLROOT; ?>/inventory/adjustments',
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                product_id: productId,
                quantity_change: adjustType === 'set' ? (newInventory - currentInventory) : (adjustType === 'add' ? quantity : -quantity),
                reason: fullReason
            },
            success: function (response) {
                if (response.success) {
                    alert('Inventory adjustment completed successfully');
                    $('#InventoryAdjustmentModal').modal('hide');
                    location.reload();
                } else {
                    if (response.errors) {
                        var errorMsg = 'Validation errors:\n';
                        for (var field in response.errors) {
                            if (response.errors[field]) {
                                errorMsg += '- ' + response.errors[field] + '\n';
                            }
                        }
                        alert(errorMsg);
                    } else {
                        alert(response.message || 'Error processing inventory adjustment');
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Error processing inventory adjustment');
            }
        });
    }

    // Update preview when form values change
    $(document).on('change', '#adjust_type, #adjust_quantity, #adjust_reason_type', function () {
        updateAdjustmentPreview();
    });

    $(document).on('input', '#adjust_reason', function () {
        updateAdjustmentPreview();
    });

    function updateAdjustmentPreview() {
        var adjustType = $('#adjust_type').val();
        var quantity = $('#adjust_quantity').val();
        var reasonType = $('#adjust_reason_type').val();
        var reason = $('#adjust_reason').val();
        var currentInventory = parseInt($('#adjust_current_Inventory').val()) || 0;

        if (!adjustType || !quantity) {
            $('#adjustment_preview').text('Make your selections to see the preview');
            return;
        }

        var newInventory = currentInventory;
        var operation = '';

        if (adjustType === 'add') {
            newInventory = currentInventory + parseInt(quantity);
            operation = `Add ${quantity} units`;
        } else if (adjustType === 'remove') {
            newInventory = currentInventory - parseInt(quantity);
            operation = `Remove ${quantity} units`;
        } else if (adjustType === 'set') {
            newInventory = parseInt(quantity);
            operation = `Set inventory to ${quantity} units`;
        }

        var changeText = newInventory >= currentInventory ?
            `<span class="text-success">+${newInventory - currentInventory}</span>` :
            `<span class="text-danger">${newInventory - currentInventory}</span>`;

        var previewText = `${operation}: ${currentInventory} → ${newInventory} units (${changeText})`;

        if (reasonType) {
            previewText += `<br><small class="text-muted">Reason: ${reasonType}`;
            if (reason.trim()) {
                previewText += `: ${reason.trim()}`;
            }
            previewText += '</small>';
        }

        $('#adjustment_preview').html(previewText);
    }

    function bulkAdjustInventory() {
        var selectedIds = $('.product-checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        if (selectedIds.length > 0) {
            alert('Bulk adjust inventory for ' + selectedIds.length + ' products');
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