<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>




<div class="container-fluid page-top-area mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-6">
            <h1 class="mb-1 font-weight-bold">
                <i class="fas fa-boxes mr-2"></i>Products & Inventory
            </h1>
            <small class="text-muted">Complete product and stock management</small>
        </div>
        <div class="col-12 col-md-6 text-md-right mt-3 mt-md-0">
            <div class="btn-group" role="group">
                <button class="btn btn-outline-info" onclick="refreshData()" data-toggle="tooltip"
                    title="Refresh Data (Ctrl+R)">
                    <i class="fa fa-sync"></i> Refresh
                </button>
                <button class="btn btn-outline-secondary" onclick="exportData('csv')" data-toggle="tooltip"
                    title="Export to CSV">
                    <i class="fa fa-download"></i> Export
                </button>
                <button class="btn btn-outline-warning" onclick="generateLowStockReport()" data-toggle="tooltip"
                    title="Low Stock Alerts">
                    <i class="fa fa-exclamation-triangle"></i> Alerts
                </button>
                <button class="btn btn-outline-primary" onclick="showImportModal()" data-toggle="tooltip"
                    title="Import from CSV">
                    <i class="fa fa-upload"></i> Import
                </button>
            </div>
            <a href="<?php echo URLROOT; ?>/products/add" class="btn btn-success btn-lg ml-2">
                <i class="fa fa-plus"></i> Add Product
            </a>
        </div>
    </div>
</div>


<!-- Enhanced KPI Summary Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card kpi-card enhanced-card h-100">
            <div class="card-body text-center">
                <div class="text-primary mb-3">
                    <i class="fas fa-cubes fa-3x"></i>
                </div>
                <h2 class="mb-2 font-weight-bold text-primary">
                    <?php echo count($data['products'] ?? []); ?>
                </h2>
                <p class="text-muted mb-0 font-weight-600">Total Products</p>
                <small class="text-muted">In Catalog</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card kpi-card enhanced-card h-100">
            <div class="card-body text-center">
                <div class="text-success mb-3">
                    <i class="fas fa-warehouse fa-3x"></i>
                </div>
                <h2 class="mb-2 font-weight-bold text-success">
                    <?php
                    $totalStock = 0;
                    if (!empty($data['products'])) {
                        foreach ($data['products'] as $product) {
                            $totalStock += $product->current_stock ?? 0;
                        }
                    }
                    echo number_format($totalStock);
                    ?>
                </h2>
                <p class="text-muted mb-0 font-weight-600">Total Stock Units</p>
                <small class="text-muted">Available</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card kpi-card enhanced-card h-100">
            <div class="card-body text-center">
                <div class="text-info mb-3">
                    <i class="fas fa-dollar-sign fa-3x"></i>
                </div>
                <h2 class="mb-2 font-weight-bold text-info">
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
                </h2>
                <p class="text-muted mb-0 font-weight-600">Inventory Value</p>
                <small class="text-muted">Total Worth</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card kpi-card enhanced-card h-100">
            <div class="card-body text-center">
                <div class="text-warning mb-3">
                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                </div>
                <h2 class="mb-2 font-weight-bold text-warning">
                    <?php
                    $lowStockCount = 0;
                    if (!empty($data['products'])) {
                        foreach ($data['products'] as $product) {
                            if ($product->current_stock <= $product->reorder_level) {
                                $lowStockCount++;
                            }
                        }
                    }
                    echo $lowStockCount;
                    ?>
                </h2>
                <p class="text-muted mb-0 font-weight-600">Low Stock Items</p>
                <small class="text-muted">Need Attention</small>
            </div>
        </div>
    </div>
</div>

<!-- Stock Status Overview -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-3">Stock Status Overview</h6>
                <div class="progress mb-3" style="height: 12px;">
                    <?php
                    $inStock = 0;
                    $lowStock = 0;
                    $outOfStock = 0;
                    $total = count($data['products'] ?? []);

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

                    $inStockPercent = $total > 0 ? ($inStock / $total) * 100 : 0;
                    $lowStockPercent = $total > 0 ? ($lowStock / $total) * 100 : 0;
                    $outStockPercent = $total > 0 ? ($outOfStock / $total) * 100 : 0;
                    ?>
                    <div class="progress-bar bg-success" style="width: <?php echo $inStockPercent; ?>%"
                        data-toggle="tooltip" title="<?php echo $inStock; ?> products in stock"></div>
                    <div class="progress-bar bg-warning" style="width: <?php echo $lowStockPercent; ?>%"
                        data-toggle="tooltip" title="<?php echo $lowStock; ?> products low stock"></div>
                    <div class="progress-bar bg-danger" style="width: <?php echo $outStockPercent; ?>%"
                        data-toggle="tooltip" title="<?php echo $outOfStock; ?> products out of stock"></div>
                </div>
                <div class="row text-center">
                    <div class="col">
                        <span class="badge badge-success px-3 py-2">
                            <i class="fa fa-circle mr-1" style="font-size: 8px;"></i>
                            In Stock: <?php echo $inStock; ?>
                        </span>
                    </div>
                    <div class="col">
                        <span class="badge badge-warning px-3 py-2">
                            <i class="fa fa-circle mr-1" style="font-size: 8px;"></i>
                            Low Stock: <?php echo $lowStock; ?>
                        </span>
                    </div>
                    <div class="col">
                        <span class="badge badge-danger px-3 py-2">
                            <i class="fa fa-circle mr-1" style="font-size: 8px;"></i>
                            Out of Stock: <?php echo $outOfStock; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Tabs -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <ul class="nav nav-tabs card-header-tabs" id="productTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="products-tab" data-toggle="tab" href="#products" role="tab"
                            aria-controls="products" aria-selected="true">
                            <i class="fas fa-cube mr-2"></i>Product Catalog
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content" id="productTabContent">
                    <!-- Products Tab Only -->
                    <div class="tab-pane fade show active" id="products" role="tabpanel" aria-labelledby="products-tab">
                        <!-- Search and Filters -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0">
                                            <i class="fa fa-search text-muted"></i>
                                        </span>
                                    </div>
                                    <input type="text" id="search" class="form-control border-left-0"
                                        placeholder="Search products...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select id="category_filter" class="form-control">
                                    <option value="">All Categories</option>
                                    <?php if (!empty($data['categories'])): ?>
                                        <?php foreach ($data['categories'] as $category): ?>
                                            <option value="<?php echo $category->category_id; ?>">
                                                <?php echo $category->category_name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="status_filter" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="in_stock">In Stock</option>
                                    <option value="low_stock">Low Stock</option>
                                    <option value="out_of_stock">Out of Stock</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="btn-group w-100">
                                    <button class="btn btn-outline-secondary" id="listViewBtn">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" id="cardViewBtn">
                                        <i class="fa fa-th"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-primary w-100" onclick="clearFilters()">
                                    <i class="fa fa-sync mr-1"></i> Reset
                                </button>
                            </div>
                        </div>

                        <!-- Products Table/Cards Container -->
                        <div id="productsContainer">
                            <!-- This will be populated by JavaScript based on view mode -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- CSV Import Modal -->
<div class="modal fade" id="csvImportModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload mr-2"></i>Import Products from CSV
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6><i class="fa fa-info-circle mr-2"></i>CSV Format Requirements:</h6>
                    <ul class="mb-0 small">
                        <li>Required columns: <b>product_name</b>, <b>sku</b></li>
                        <li>Optional columns: <b>category_id</b> or <b>category_name</b>, <b>brand_id</b> or
                            <b>brand_name</b>, <b>unit_id</b> or <b>unit_name</b>, min_stock_level, max_stock_level,
                            reorder_level
                        </li>
                        <li>You can use either the ID or the name for category, brand, and unit. If both are provided,
                            ID takes priority.</li>
                        <li>First row should contain column headers</li>
                        <li>Use comma (,) as delimiter</li>
                    </ul>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="downloadSampleCSV()">
                            <i class="fa fa-download"></i> Download Sample CSV
                        </button>
                        <a href="<?php echo URLROOT; ?>/products/downloadMappingsCSV"
                            class="btn btn-sm btn-outline-secondary ml-2" target="_blank">
                            <i class="fa fa-table"></i> Download Mappings Table
                        </a>
                    </div>
                </div>

                <form id="csvImportForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="csvFile">Select CSV File</label>
                        <div class="d-flex align-items-center">
                            <input type="file" id="csvFile" name="csvFile" class="form-control-file mr-2" accept=".csv"
                                required>
                            <a href="<?php echo URLROOT; ?>/products/downloadSampleCSV" target="_blank"
                                class="btn btn-link btn-sm p-0 ml-2">Sample CSV</a>
                        </div>
                        <small class="form-text text-muted">Maximum file size: 5MB</small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="updateExisting"
                                name="update_existing">
                            <label class="custom-control-label" for="updateExisting">
                                Update existing products (match by SKU)
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="validateOnly" name="validate_only">
                            <label class="custom-control-label" for="validateOnly">
                                Validate only (don't import, just check for errors)
                            </label>
                        </div>
                    </div>
                </form>

                <!-- Import Progress -->
                <div id="importProgress" class="mt-3" style="display: none;">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                            style="width: 0%"></div>
                    </div>
                    <div class="mt-2">
                        <span id="progressText">Processing...</span>
                    </div>
                </div>

                <!-- Import Results -->
                <div id="importResults" class="mt-3" style="display: none;">
                    <div class="alert" role="alert">
                        <h6 id="resultTitle"></h6>
                        <div id="resultContent"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="importBtn" onclick="startImport()">
                    <i class="fas fa-upload mr-1"></i>Start Import
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="stockAdjustmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit mr-2"></i>Stock Adjustment
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="stockAdjustmentForm">
                    <input type="hidden" id="adjust_product_id" name="product_id">
                    <div class="form-group">
                        <label>Product</label>
                        <input type="text" id="adjust_product_name" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Current Stock</label>
                        <input type="number" id="adjust_current_stock" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Adjustment Type</label>
                        <select id="adjust_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="add">Add Stock</option>
                            <option value="remove">Remove Stock</option>
                            <option value="set">Set Stock Level</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" id="adjust_quantity" class="form-control" required min="0">
                    </div>
                    <div class="form-group">
                        <label>Reason</label>
                        <textarea id="adjust_reason" class="form-control" rows="3"
                            placeholder="Reason for stock adjustment..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitStockAdjustment()">
                    <i class="fas fa-save mr-1"></i>Save Adjustment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Action Floating Button -->
<a href="<?php echo URLROOT; ?>/products/add" class="quick-action-fab" data-toggle="tooltip"
    title="Quick Add Product (Ctrl+N)">
    <i class="fas fa-plus"></i>
</a>

<script>
    // Pass PHP data to JavaScript
    window.URLROOT = '<?php echo URLROOT; ?>';
    window.productsData = <?php echo json_encode($data['products'] ?? []); ?>;

    $(document).ready(function () {
        // Initialize DataTable for stock management
        $('#stockTable').DataTable({
            "responsive": true,
            "pageLength": 25,
            "order": [[7, "asc"]], // Sort by status
            "columnDefs": [
                { "orderable": false, "targets": [8] } // Disable sorting for actions column
            ]
        });

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Load initial products view
        loadProductsView('list');
    });
</script>

<!-- Include the dedicated JavaScript file -->
<script src="<?php echo URLROOT; ?>/public/js/products-unified.js"></script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>