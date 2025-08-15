<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container theme-unified page-top-area mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-6">
            <h1 class="mb-1 font-weight-bold">
                <i class="fas fa-boxes mr-2"></i>Products
            </h1>
            <small class="text-muted">Product Details</small>
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
        <div class="card h-100 primary">
            <div class="card-header">
                <i class="fas fa-cubes"></i>
                <h5>Total Products</h5>
            </div>
            <div class="card-body text-center">
                <div class="value">
                    <?php
                    $productCount = count($data['products'] ?? []);
                    // Show sample count if no real data
                    echo $productCount > 0 ? $productCount : 2;
                    ?>
                </div>
                <div class="label">In Catalog</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card h-100 success">
            <div class="card-header">
                <i class="fas fa-warehouse"></i>
                <h5>Total Inventory</h5>
            </div>
            <div class="card-body text-center">
                <div class="value">
                    <?php
                    $totalInventory = 0;
                    if (!empty($data['products'])) {
                        foreach ($data['products'] as $product) {
                            $totalInventory += $product->current_inventory ?? 0;
                        }
                    } else {
                        // Sample data for demonstration
                        $totalInventory = 350; // Sample total Inventory
                    }
                    echo number_format($totalInventory);
                    ?>
                </div>
                <div class="label">Units Available</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card h-100 info">
            <div class="card-header">
                <i class="fas fa-tags"></i>
                <h5>Total Categories</h5>
            </div>
            <div class="card-body text-center">
                <div class="value">
                    <?php
                    $categoryCount = count($data['categories'] ?? []);
                    // Show sample count if no real data
                    echo $categoryCount > 0 ? $categoryCount : 8;
                    ?>
                </div>
                <div class="label">Product Categories</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card h-100 warning">
            <div class="card-header">
                <i class="fas fa-certificate"></i>
                <h5>Total Brands</h5>
            </div>
            <div class="card-body text-center">
                <div class="value">
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
                </div>
                <div class="label">Registered Brands</div>
            </div>
        </div>
    </div>
</div> <!-- end of KPI row -->

<!-- Quick Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card-theme bg-gradient-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-chart-line fa-2x mb-2"></i>
                <h4>₹2,45,000</h4>
                <small>Total Inventory Value</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card-theme bg-gradient-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-percentage fa-2x mb-2"></i>
                <h4>28.5%</h4>
                <small>Average Profit Margin</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card-theme bg-gradient-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <h4>5</h4>
                <small>Items Need Reorder</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card-theme bg-gradient-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-sync-alt fa-2x mb-2"></i>
                <h4>12</h4>
                <small>Fast Moving Items</small>
            </div>
        </div>
    </div>
</div>

<!-- Analytics Dashboard -->
<div class="row mb-4">
    <!-- Inventory Status Chart -->
    <div class="col-lg-3 mb-3">
        <div class="card-theme theme-card-light h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie text-info mr-2"></i>Inventory Status Distribution
                </h5>
            </div>
            <div class="card-body">
                <canvas id="inventoryStatusChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Price Range Distribution -->
    <div class="col-lg-3 mb-3">
        <div class="card-theme theme-card-light h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar text-warning mr-2"></i>Price Range Distribution
                </h5>
            </div>
            <div class="card-body">
                <canvas id="priceRangeChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Category Performance -->
    <div class="col-lg-3 mb-3">
        <div class="card-theme theme-card-light h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tags text-success mr-2"></i>Category Performance
                </h5>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary active"
                        onclick="toggleCategoryView('inventory')">Inventory</button>
                    <button class="btn btn-outline-primary" onclick="toggleCategoryView('value')">Value</button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="categoryChart" width="400" height="150"></canvas>
            </div>
        </div>
    </div>

    <!-- Most Recent Activity -->
    <div class="col-lg-3 mb-3">
        <div class="card-theme h-100">
            <div class="card-header bg-secondary-theme text-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-history mr-2"></i>Recent Activity
                </h6>
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
            <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                <div id="activityTableBody">
                    <?php
                    // Get recent product activities from the controller
                    $activities = [];
                    if (!empty($data['activities'])) {
                        $activities = array_slice($data['activities'], 0, 5); // Show only 5 items in compact view
                    }

                    if (!empty($activities)):
                        foreach ($activities as $index => $activity):
                            $badgeClass = '';
                            $iconClass = '';

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

                            $formattedDate = date('M d', strtotime($activity->created_at));
                            $formattedTime = date('H:i', strtotime($activity->created_at));
                            ?>
                            <div class="d-flex align-items-center p-2 border-bottom">
                                <span class="badge <?php echo $badgeClass; ?> mr-2">
                                    <i class="<?php echo $iconClass; ?>"></i>
                                </span>
                                <div class="flex-grow-1">
                                    <div class="font-weight-bold text-dark" style="font-size: 0.8rem;">
                                        <?php echo htmlspecialchars(substr($activity->product_name, 0, 20)); ?>...
                                    </div>
                                    <small class="text-muted"><?php echo $formattedDate; ?>
                                        <?php echo $formattedTime; ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="text-center p-2">
                            <a href="<?php echo URLROOT; ?>/products/activity" class="btn btn-sm btn-outline-secondary">
                                View All Activity
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-history fa-2x mb-2"></i>
                                <p class="mb-0" style="font-size: 0.8rem;">No recent activity</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Products Management Table -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card-theme theme-card-light">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list mr-2"></i>Products Management
                </h5>
                <div class="d-flex align-items-center">
                    <form method="GET" class="mr-3" id="searchForm">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" class="form-theme" id="productSearch" name="search"
                                placeholder="Search products..."
                                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <input type="hidden" name="per_page"
                                value="<?php echo isset($_GET['per_page']) ? (int) $_GET['per_page'] : 25; ?>">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                                    <button type="button" class="btn btn-outline-danger" onclick="clearSearch()"
                                        title="Clear search">
                                        <i class="fas fa-times"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                    <div class="d-flex align-items-center mr-2">
                        <label class="mb-0 mr-2 text-muted" style="font-size: 0.875rem;">Show:</label>
                        <select class="form-control form-control-sm" id="itemsPerPage" onchange="changeItemsPerPage()"
                            style="width: auto;">
                            <option value="25" <?php echo (!isset($_GET['per_page']) || $_GET['per_page'] == 25) ? 'selected' : ''; ?>>25</option>
                            <option value="50" <?php echo (isset($_GET['per_page']) && $_GET['per_page'] == 50) ? 'selected' : ''; ?>>50</option>
                            <option value="100" <?php echo (isset($_GET['per_page']) && $_GET['per_page'] == 100) ? 'selected' : ''; ?>>100</option>
                            <option value="500" <?php echo (isset($_GET['per_page']) && $_GET['per_page'] == 500) ? 'selected' : ''; ?>>500</option>
                        </select>
                    </div>
                    <div class="btn-group btn-group-sm mr-2">
                        <button class="btn btn-outline-secondary" onclick="exportProducts('csv')">
                            <i class="fas fa-file-csv mr-1"></i>CSV
                        </button>
                        <button class="btn btn-outline-secondary" onclick="exportProducts('excel')">
                            <i class="fas fa-file-excel mr-1"></i>Excel
                        </button>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button"
                            data-toggle="dropdown">
                            <i class="fas fa-filter mr-1"></i>Filter
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick="filterProducts('all')">All Products</a>
                            <a class="dropdown-item" href="#" onclick="filterProducts('low_stock')">Low Stock</a>
                            <a class="dropdown-item" href="#" onclick="filterProducts('out_of_stock')">Out of Stock</a>
                            <a class="dropdown-item" href="#" onclick="filterProducts('high_value')">High Value</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Column Toggle Panel -->
            <div class="card-body border-bottom p-2" style="background-color: var(--card-bg-secondary, #f8f9fa);">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <small class="text-muted font-weight-bold mr-3">
                            <i class="fas fa-columns mr-1"></i>Table Columns:
                        </small>
                        <div class="btn-group-toggle d-flex flex-wrap" data-toggle="buttons">
                            <!-- Core columns (always visible) -->
                            <label class="btn btn-sm btn-outline-secondary active mr-1 mb-1"
                                style="font-size: 0.75rem; cursor: default;" disabled>
                                <input type="checkbox" checked disabled> <i class="fas fa-image mr-1"></i>Image
                            </label>
                            <label class="btn btn-sm btn-outline-secondary active mr-1 mb-1"
                                style="font-size: 0.75rem; cursor: default;" disabled>
                                <input type="checkbox" checked disabled> <i class="fas fa-box mr-1"></i>Product
                            </label>
                            <label class="btn btn-sm btn-outline-secondary active mr-1 mb-1"
                                style="font-size: 0.75rem; cursor: default;" disabled>
                                <input type="checkbox" checked disabled> <i class="fas fa-barcode mr-1"></i>SKU
                            </label>

                            <!-- Toggleable columns -->
                            <label class="btn btn-sm btn-outline-info mr-1 mb-1 column-toggle"
                                style="font-size: 0.75rem;" data-column="brand">
                                <input type="checkbox"> <i class="fas fa-tag mr-1"></i>Brand
                            </label>
                            <label class="btn btn-sm btn-outline-secondary active mr-1 mb-1"
                                style="font-size: 0.75rem; cursor: default;" disabled>
                                <input type="checkbox" checked disabled> <i class="fas fa-folder mr-1"></i>Category
                            </label>
                            <label class="btn btn-sm btn-outline-secondary active mr-1 mb-1"
                                style="font-size: 0.75rem; cursor: default;" disabled>
                                <input type="checkbox" checked disabled> <i class="fas fa-cubes mr-1"></i>Stock
                            </label>
                            <label class="btn btn-sm btn-outline-info mr-1 mb-1 column-toggle"
                                style="font-size: 0.75rem;" data-column="reorder">
                                <input type="checkbox"> <i class="fas fa-exclamation-triangle mr-1"></i>Reorder
                            </label>
                            <label class="btn btn-sm btn-outline-secondary active mr-1 mb-1"
                                style="font-size: 0.75rem; cursor: default;" disabled>
                                <input type="checkbox" checked disabled> <i class="fas fa-dollar-sign mr-1"></i>Price
                            </label>
                            <label class="btn btn-sm btn-outline-info mr-1 mb-1 column-toggle"
                                style="font-size: 0.75rem;" data-column="margin">
                                <input type="checkbox"> <i class="fas fa-percentage mr-1"></i>Margin
                            </label>
                            <label class="btn btn-sm btn-outline-info mr-1 mb-1 column-toggle"
                                style="font-size: 0.75rem;" data-column="supplier">
                                <input type="checkbox"> <i class="fas fa-truck mr-1"></i>Supplier
                            </label>
                            <label class="btn btn-sm btn-outline-info mr-1 mb-1 column-toggle"
                                style="font-size: 0.75rem;" data-column="unit">
                                <input type="checkbox"> <i class="fas fa-balance-scale mr-1"></i>Unit
                            </label>
                            <label class="btn btn-sm btn-outline-info mr-1 mb-1 column-toggle"
                                style="font-size: 0.75rem;" data-column="lastOrdered">
                                <input type="checkbox"> <i class="fas fa-calendar mr-1"></i>Last Ordered
                            </label>
                            <label class="btn btn-sm btn-outline-secondary active mr-1 mb-1"
                                style="font-size: 0.75rem; cursor: default;" disabled>
                                <input type="checkbox" checked disabled> <i class="fas fa-info-circle mr-1"></i>Status
                            </label>
                            <label class="btn btn-sm btn-outline-secondary active mr-1 mb-1"
                                style="font-size: 0.75rem; cursor: default;" disabled>
                                <input type="checkbox" checked disabled> <i class="fas fa-clock mr-1"></i>Updated
                            </label>
                        </div>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-sm" onclick="resetColumnView()">
                            <i class="fas fa-eye mr-1"></i>Default View
                        </button>
                        <button class="btn btn-outline-success btn-sm" onclick="showAllColumns()">
                            <i class="fas fa-expand mr-1"></i>Show All
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="productsTable">
                        <thead class="thead-light">
                            <tr>
                                <th width="3%">
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </th>
                                <th width="8%">Image</th>
                                <th width="15%">Product</th>
                                <th width="10%">SKU</th>
                                <th width="8%" class="column-brand" style="display: none;">Brand</th>
                                <th width="12%">Category</th>
                                <th width="10%">Stock</th>
                                <th width="8%" class="column-reorder" style="display: none;">Reorder</th>
                                <th width="10%">Price</th>
                                <th width="8%" class="column-margin" style="display: none;">Margin</th>
                                <th width="12%" class="column-supplier" style="display: none;">Suppliers</th>
                                <th width="8%" class="column-unit" style="display: none;">Unit</th>
                                <th width="10%" class="column-lastOrdered" style="display: none;">Last Ordered</th>
                                <th width="8%">Status</th>
                                <th width="12%">Last Updated</th>
                                <th width="12%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            <?php if (!empty($data['products'])): ?>
                                <?php foreach ($data['products'] as $product): ?>
                                    <tr>
                                        <td><input type="checkbox" class="product-checkbox"
                                                value="<?php echo $product->product_id; ?>"></td>
                                        <td>
                                            <?php if (!empty($product->image_path)): ?>
                                                <img src="<?php echo URLROOT; ?>/public/uploads/<?php echo $product->image_path; ?>"
                                                    alt="Product" class="img-thumbnail"
                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <img src="<?php echo URLROOT; ?>/public/images/products/default.jpg" alt="Product"
                                                    class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo htmlspecialchars($product->product_name); ?></strong>
                                                <?php if (!empty($product->model_number)): ?>
                                                    <br><small class="text-muted">Model:
                                                        <?php echo htmlspecialchars($product->model_number); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><code><?php echo htmlspecialchars($product->sku); ?></code></td>

                                        <!-- Brand Column (toggleable) -->
                                        <td class="column-brand" style="display: none;">
                                            <?php if (!empty($product->brand_name)): ?>
                                                <span
                                                    class="badge badge-primary"><?php echo htmlspecialchars($product->brand_name); ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-light">No Brand</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if (!empty($product->category_name)): ?>
                                                <span
                                                    class="badge badge-secondary"><?php echo htmlspecialchars($product->category_name); ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-light">No Category</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php
                                                $currentInventory = $product->current_inventory ?? 0;
                                                $badgeClass = 'badge-success';
                                                if ($currentInventory <= 5) {
                                                    $badgeClass = 'badge-danger';
                                                } elseif ($currentInventory <= 10) {
                                                    $badgeClass = 'badge-warning';
                                                }
                                                ?>
                                                <span
                                                    class="badge <?php echo $badgeClass; ?>"><?php echo $currentInventory; ?></span>
                                                <button class="btn btn-link btn-sm p-0 ml-2"
                                                    onclick="adjustInventory(<?php echo $product->product_id; ?>, '<?php echo htmlspecialchars($product->product_name); ?>', <?php echo $currentInventory; ?>)">
                                                    <i class="fas fa-edit text-primary"></i>
                                                </button>
                                            </div>
                                        </td>

                                        <!-- Reorder Level Column (toggleable) -->
                                        <td class="column-reorder" style="display: none;">
                                            <?php
                                            $reorderLevel = $product->reorder_level ?? 10;
                                            $needsReorder = $currentInventory <= $reorderLevel;
                                            ?>
                                            <div class="d-flex align-items-center">
                                                <?php if ($needsReorder): ?>
                                                    <i class="fas fa-exclamation-triangle text-warning mr-1"></i>
                                                    <small class="text-warning">Reorder</small>
                                                <?php else: ?>
                                                    <i class="fas fa-check-circle text-success mr-1"></i>
                                                    <small class="text-success">OK</small>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <td>
                                            <div>
                                                <strong><?php echo formatCurrency($product->selling_price ?? 0, 2); ?></strong>
                                                <?php if (!empty($product->cost_price)): ?>
                                                    <br><small class="text-muted">Cost:
                                                        <?php echo formatCurrency($product->cost_price, 2); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Profit Margin Column (toggleable) -->
                                        <td class="column-margin" style="display: none;">
                                            <?php
                                            $sellingPrice = $product->selling_price ?? 0;
                                            $costPrice = $product->primary_purchase_price ?? $product->unit_price ?? 0;
                                            $margin = 0;

                                            if ($sellingPrice > 0 && $costPrice > 0) {
                                                $margin = (($sellingPrice - $costPrice) / $sellingPrice) * 100;
                                            }

                                            $marginClass = 'text-muted';
                                            if ($margin > 30)
                                                $marginClass = 'text-success';
                                            elseif ($margin > 15)
                                                $marginClass = 'text-warning';
                                            elseif ($margin > 0)
                                                $marginClass = 'text-info';
                                            else
                                                $marginClass = 'text-danger';
                                            ?>
                                            <span class="font-weight-bold <?php echo $marginClass; ?>">
                                                <?php echo number_format($margin, 1); ?>%
                                            </span>
                                        </td>

                                        <!-- Suppliers Column (toggleable) -->
                                        <td class="column-supplier" style="display: none;">
                                            <?php if (!empty($product->supplier_count) && $product->supplier_count > 0): ?>
                                                <div>
                                                    <small class="font-weight-bold text-primary">
                                                        <?php echo $product->supplier_count; ?>
                                                        Supplier<?php echo ($product->supplier_count > 1) ? 's' : ''; ?> Available
                                                    </small>
                                                    <?php if (!empty($product->min_supplier_price) && $product->min_supplier_price > 0): ?>
                                                        <br><small class="text-success">
                                                            Best: ₹<?php echo number_format($product->min_supplier_price, 2); ?>
                                                            <?php if ($product->max_supplier_price != $product->min_supplier_price): ?>
                                                                - ₹<?php echo number_format($product->max_supplier_price, 2); ?>
                                                            <?php endif; ?>
                                                        </small>
                                                    <?php endif; ?>
                                                    <?php if (!empty($product->avg_supplier_price) && $product->avg_supplier_price > 0): ?>
                                                        <br><small class="text-muted">
                                                            Avg: ₹<?php echo number_format($product->avg_supplier_price, 2); ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge badge-light">No Supplier</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Unit Column (toggleable) -->
                                        <td class="column-unit" style="display: none;">
                                            <?php if (!empty($product->unit_name)): ?>
                                                <span
                                                    class="badge badge-info"><?php echo htmlspecialchars($product->unit_name); ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-light">-</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Last Ordered Column (toggleable) -->
                                        <td class="column-lastOrdered" style="display: none;">
                                            <?php if (!empty($product->last_ordered_date)): ?>
                                                <small><?php echo date('M d, Y', strtotime($product->last_ordered_date)); ?></small>
                                            <?php else: ?>
                                                <small class="text-muted">Never</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $status = $product->product_status ?? 'active';
                                            $statusClass = 'badge-success';
                                            $statusText = 'Active';

                                            switch ($status) {
                                                case 'discontinued':
                                                    $statusClass = 'badge-danger';
                                                    $statusText = 'Discontinued';
                                                    break;
                                                case 'seasonal':
                                                    $statusClass = 'badge-warning';
                                                    $statusText = 'Seasonal';
                                                    break;
                                                case 'special_order':
                                                    $statusClass = 'badge-info';
                                                    $statusText = 'Special Order';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                        </td>
                                        <td>
                                            <small>
                                                <?php echo date('M d, Y', strtotime($product->updated_at ?? $product->created_at)); ?><br>
                                                <?php echo date('H:i', strtotime($product->updated_at ?? $product->created_at)); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary"
                                                    onclick="viewProduct(<?php echo $product->product_id; ?>)" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-warning"
                                                    onclick="editProduct(<?php echo $product->product_id; ?>)" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <div class="dropdown">
                                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                        data-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="#"
                                                            onclick="viewSuppliers(<?php echo $product->product_id; ?>)">
                                                            <i class="fas fa-users mr-2"></i>Suppliers
                                                        </a>
                                                        <a class="dropdown-item" href="#"
                                                            onclick="linkSupplier(<?php echo $product->product_id; ?>, '<?php echo htmlspecialchars($product->product_name, ENT_QUOTES); ?>')">
                                                            <i class="fas fa-link mr-2"></i>Link Supplier
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-danger" href="#"
                                                            onclick="deleteProduct(<?php echo $product->product_id; ?>)">
                                                            <i class="fas fa-trash mr-2"></i>Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-box-open fa-3x mb-3"></i>
                                            <h5>No Products Found</h5>
                                            <p>Start by adding your first product to the inventory.</p>
                                            <a href="<?php echo URLROOT; ?>/products/add"
                                                class="btn-theme btn-primary-theme">
                                                <i class="fas fa-plus mr-2"></i>Add First Product
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls -->
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-md-6 offset-md-3">
                            <?php if (isset($data['pagination']) && $data['pagination']['total_pages'] > 1): ?>
                                <nav aria-label="Products pagination">
                                    <ul class="pagination pagination-sm justify-content-center mb-0">
                                        <!-- Previous Button -->
                                        <?php if ($data['pagination']['current_page'] > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="?page=<?php echo $data['pagination']['current_page'] - 1; ?>&per_page=<?php echo $data['pagination']['per_page']; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">
                                                    <i class="fas fa-chevron-left"></i>
                                                </a>
                                            </li>
                                        <?php else: ?>
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                                            </li>
                                        <?php endif; ?>

                                        <!-- Page Numbers -->
                                        <?php
                                        $start_page = max(1, $data['pagination']['current_page'] - 2);
                                        $end_page = min($data['pagination']['total_pages'], $data['pagination']['current_page'] + 2);

                                        // Show first page if not in range
                                        if ($start_page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="?page=1&per_page=<?php echo $data['pagination']['per_page']; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">1</a>
                                            </li>
                                            <?php if ($start_page > 2): ?>
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            <?php endif;
                                        endif;

                                        // Show page numbers in range
                                        for ($i = $start_page; $i <= $end_page; $i++): ?>
                                            <li
                                                class="page-item <?php echo ($i == $data['pagination']['current_page']) ? 'active' : ''; ?>">
                                                <a class="page-link"
                                                    href="?page=<?php echo $i; ?>&per_page=<?php echo $data['pagination']['per_page']; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor;

                                        // Show last page if not in range
                                        if ($end_page < $data['pagination']['total_pages']):
                                            if ($end_page < $data['pagination']['total_pages'] - 1): ?>
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            <?php endif; ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="?page=<?php echo $data['pagination']['total_pages']; ?>&per_page=<?php echo $data['pagination']['per_page']; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>"><?php echo $data['pagination']['total_pages']; ?></a>
                                            </li>
                                        <?php endif; ?>

                                        <!-- Next Button -->
                                        <?php if ($data['pagination']['current_page'] < $data['pagination']['total_pages']): ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="?page=<?php echo $data['pagination']['current_page'] + 1; ?>&per_page=<?php echo $data['pagination']['per_page']; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">
                                                    <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        <?php else: ?>
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <?php if (isset($data['pagination'])): ?>
                                <small class="text-muted text-right d-block">
                                    Showing <?php echo $data['pagination']['start_record']; ?> to
                                    <?php echo $data['pagination']['end_record']; ?>
                                    of <?php echo $data['pagination']['total_records']; ?> entries
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div class="card-footer bg-light" id="bulkActions" style="display: none;">
                    <div class="d-flex align-items-center">
                        <span class="mr-3" id="selectedCount">0 items selected</span>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="bulkEdit()">
                                <i class="fas fa-edit mr-1"></i>Bulk Edit
                            </button>
                            <button class="btn btn-outline-warning" onclick="bulkPriceUpdate()">
                                <i class="fas fa-tags mr-1"></i>Update Prices
                            </button>
                            <button class="btn btn-outline-info" onclick="bulkCategoryChange()">
                                <i class="fas fa-folder mr-1"></i>Change Category
                            </button>
                            <button class="btn btn-outline-danger" onclick="bulkDelete()">
                                <i class="fas fa-trash mr-1"></i>Delete Selected
                            </button>
                        </div>
                    </div>
                </div>
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
                        <input type="text" id="adjust_product_name" class="form-theme" readonly>
                    </div>
                    <div class="form-group">
                        <label>Current Inventory</label>
                        <input type="number" id="adjust_current_inventory" class="form-theme" readonly>
                    </div>
                    <div class="form-group">
                        <label>Adjustment Type</label>
                        <select id="adjust_type" class="form-theme" required>
                            <option value="">Select Type</option>
                            <option value="add">Add Inventory</option>
                            <option value="remove">Remove Inventory</option>
                            <option value="set">Set Inventory Level</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" id="adjust_quantity" class="form-theme" required min="0">
                    </div>
                    <div class="form-group">
                        <label>Reason</label>
                        <textarea id="adjust_reason" class="form-theme" rows="3"
                            placeholder="Reason for Inventory adjustment..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-theme btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn-theme btn-primary-theme" onclick="submitInventoryAdjustment()">
                    <i class="fas fa-save mr-1"></i>Save Adjustment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Link Supplier Modal -->
<div class="modal fade" id="LinkSupplierModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-link mr-2"></i>Link Supplier to Product
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="link_product_id" name="product_id">
                <div class="form-group mb-3">
                    <label><strong>Product:</strong></label>
                    <div id="link_product_name" class="form-control-plaintext"></div>
                </div>

                <!-- Existing Suppliers Display -->
                <div id="existing_suppliers_section" class="mb-4" style="display: none;">
                    <h6 class="mb-3"><i class="fas fa-users mr-2"></i>Current Suppliers</h6>
                    <div id="existing_suppliers_list" class="border rounded p-2" style="background-color: #f8f9fa;">
                        <!-- Existing suppliers will be loaded here -->
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label>Search Suppliers:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" id="supplier_search" class="form-theme"
                            placeholder="Type to search by name, contact, or phone...">
                    </div>
                    <small class="form-text text-muted">Search will update results automatically as you type</small>
                </div>
                <div id="suppliers_loading" class="text-center py-3" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i> Loading suppliers...
                </div>
                <div id="suppliers_list" class="suppliers-list">
                    <!-- Suppliers will be loaded here -->
                </div>

                <!-- Supplier Details Form -->
                <div id="supplier_details_form" class="mt-4 p-3 border rounded bg-light" style="display: none;">
                    <h6 class="mb-3"><i class="fas fa-info-circle mr-2"></i>Supplier Details</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="purchase_price">Purchase Price (₹) <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-theme" id="purchase_price" step="0.01" min="0"
                                    required>
                                <small class="text-muted">Cost price from this supplier</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="supplier_sku">Supplier SKU</label>
                                <input type="text" class="form-theme" id="supplier_sku"
                                    placeholder="Supplier's product code">
                                <small class="text-muted">Optional supplier product code</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lead_time_days">Lead Time (days)</label>
                                <input type="number" class="form-theme" id="lead_time_days" min="1" value="7">
                                <small class="text-muted">Delivery time from this supplier</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="min_order_quantity">Minimum Order Quantity</label>
                                <input type="number" class="form-theme" id="min_order_quantity" min="1" value="1">
                                <small class="text-muted">Minimum quantity to order</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supplier_notes">Supplier Notes</label>
                                    <input type="text" class="form-theme" id="supplier_notes"
                                        placeholder="e.g., Best for bulk orders, Good quality">
                                    <small class="text-muted">Optional notes about this supplier relationship</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supplier_rating">Quality Rating</label>
                                    <select class="form-theme" id="supplier_rating">
                                        <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                                        <option value="4" selected>⭐⭐⭐⭐ Good</option>
                                        <option value="3">⭐⭐⭐ Average</option>
                                        <option value="2">⭐⭐ Below Average</option>
                                        <option value="1">⭐ Poor</option>
                                    </select>
                                    <small class="text-muted">Rate supplier quality for smart PO suggestions</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-theme btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn-theme btn-primary-theme" id="confirm_link_supplier"
                        onclick="confirmLinkSupplier()" disabled>
                        <i class="fas fa-link mr-1"></i>Link Supplier
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Pass data and load unified script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    // Pass PHP data to JavaScript
        window.URLROOT = '<?php echo URLROOT; ?>';
        window.productsData = <?php echo json_encode($data['products'] ?? []); ?>;

        // Debug: Log the data being passed
        console.log('PHP Products Data:', window.productsData);
        console.log('Data length:', window.productsData ? window.productsData.length : 'No data');

        // Initialize Charts
        let inventoryChart, priceChart, categoryChart;

        document.addEventListener('DOMContentLoaded', function () {
            initializeCharts();
            initializeProductTable();
        });

        function initializeCharts() {
            // Inventory Status Chart
            const inventoryCtx = document.getElementById('inventoryStatusChart').getContext('2d');
            inventoryChart = new Chart(inventoryCtx, {
                type: 'doughnut',
                data: {
                    labels: ['In Stock', 'Low Stock', 'Out of Stock', 'Reorder Level'],
                    datasets: [{
                        data: [65, 20, 5, 10],
                        backgroundColor: [
                            '#28a745',
                            '#ffc107',
                            '#dc3545',
                            '#17a2b8'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Price Range Chart
            const priceCtx = document.getElementById('priceRangeChart').getContext('2d');
            priceChart = new Chart(priceCtx, {
                type: 'bar',
                data: {
                    labels: ['₹0-500', '₹500-2K', '₹2K-5K', '₹5K-10K', '₹10K+'],
                    datasets: [{
                        label: 'Number of Products',
                        data: [12, 35, 28, 15, 8],
                        backgroundColor: '#007bff',
                        borderColor: '#0056b3',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 5
                            }
                        }
                    }
                }
            });

            // Category Chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            categoryChart = new Chart(categoryCtx, {
                type: 'horizontalBar',
                data: {
                    labels: ['Power Tools', 'Hand Tools', 'Hardware', 'Electrical', 'Plumbing', 'Safety'],
                    datasets: [{
                        label: 'Inventory Count',
                        data: [45, 38, 32, 28, 22, 15],
                        backgroundColor: [
                            '#007bff',
                            '#28a745',
                            '#ffc107',
                            '#17a2b8',
                            '#6f42c1',
                            '#e83e8c'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function toggleCategoryView(viewType) {
            // Update active button
            document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            if (viewType === 'value') {
                categoryChart.data.datasets[0].label = 'Total Value (₹)';
                categoryChart.data.datasets[0].data = [450000, 320000, 280000, 220000, 180000, 120000];
            } else {
                categoryChart.data.datasets[0].label = 'Inventory Count';
                categoryChart.data.datasets[0].data = [45, 38, 32, 28, 22, 15];
            }
            categoryChart.update();
        }

        // Product Management Functions
        function initializeProductTable() {
            // Add real-time search functionality with debounce
            let searchTimeout;
            const searchInput = document.getElementById('productSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        const searchTerm = this.value.trim();
                        const currentUrl = new URL(window.location.href);

                        if (searchTerm.length > 0) {
                            currentUrl.searchParams.set('search', searchTerm);
                        } else {
                            currentUrl.searchParams.delete('search');
                        }
                        currentUrl.searchParams.set('page', '1'); // Reset to first page
                        window.location.href = currentUrl.toString();
                    }, 500); // Wait 500ms after user stops typing
                });
            }

            // Add checkbox event listeners
            document.querySelectorAll('.product-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActions);
            });

            // Initialize column toggles
            initializeColumnToggles();
        }

        // Column Toggle Functions
        function initializeColumnToggles() {
            // Load saved column preferences from localStorage
            const savedColumns = JSON.parse(localStorage.getItem('productTableColumns') || '{}');

            // Apply saved preferences
            Object.keys(savedColumns).forEach(columnName => {
                if (savedColumns[columnName]) {
                    showColumn(columnName, false); // false = don't save to localStorage again
                    // Update toggle button state
                    const toggleBtn = document.querySelector(`.column-toggle[data-column="${columnName}"]`);
                    if (toggleBtn) {
                        toggleBtn.classList.add('active');
                        toggleBtn.querySelector('input[type="checkbox"]').checked = true;
                    }
                }
            });

            // Add event listeners to toggle buttons
            document.querySelectorAll('.column-toggle').forEach(btn => {
                btn.addEventListener('change', function () {
                    const columnName = this.dataset.column;
                    const isChecked = this.querySelector('input[type="checkbox"]').checked;

                    if (isChecked) {
                        showColumn(columnName);
                        this.classList.add('active');
                    } else {
                        hideColumn(columnName);
                        this.classList.remove('active');
                    }
                });
            });
        }

        function showColumn(columnName, savePreference = true) {
            const columnElements = document.querySelectorAll(`.column-${columnName}`);
            columnElements.forEach(el => {
                el.style.display = '';
            });

            if (savePreference) {
                saveColumnPreference(columnName, true);
            }
        }

        function hideColumn(columnName, savePreference = true) {
            const columnElements = document.querySelectorAll(`.column-${columnName}`);
            columnElements.forEach(el => {
                el.style.display = 'none';
            });

            if (savePreference) {
                saveColumnPreference(columnName, false);
            }
        }

        function saveColumnPreference(columnName, isVisible) {
            const savedColumns = JSON.parse(localStorage.getItem('productTableColumns') || '{}');
            savedColumns[columnName] = isVisible;
            localStorage.setItem('productTableColumns', JSON.stringify(savedColumns));
        }

        function resetColumnView() {
            // Hide all optional columns
            const optionalColumns = ['brand', 'reorder', 'margin', 'supplier', 'unit', 'lastOrdered'];
            optionalColumns.forEach(columnName => {
                hideColumn(columnName, false);
                // Update toggle button state
                const toggleBtn = document.querySelector(`.column-toggle[data-column="${columnName}"]`);
                if (toggleBtn) {
                    toggleBtn.classList.remove('active');
                    toggleBtn.querySelector('input[type="checkbox"]').checked = false;
                }
            });

            // Clear saved preferences
            localStorage.removeItem('productTableColumns');
            showNotification('Table reset to default view', 'success');
        }

        function showAllColumns() {
            // Show all optional columns
            const optionalColumns = ['brand', 'reorder', 'margin', 'supplier', 'unit', 'lastOrdered'];
            optionalColumns.forEach(columnName => {
                showColumn(columnName, false);
                // Update toggle button state
                const toggleBtn = document.querySelector(`.column-toggle[data-column="${columnName}"]`);
                if (toggleBtn) {
                    toggleBtn.classList.add('active');
                    toggleBtn.querySelector('input[type="checkbox"]').checked = true;
                }
            });

            // Save all preferences
            const savedColumns = {};
            optionalColumns.forEach(columnName => {
                savedColumns[columnName] = true;
            });
            localStorage.setItem('productTableColumns', JSON.stringify(savedColumns));
            showNotification('Showing all columns', 'success');
        }

        // Clear search functionality
        function clearSearch() {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.delete('search');
            currentUrl.searchParams.set('page', '1');
            window.location.href = currentUrl.toString();
        }

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.product-checkbox');

            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });

            updateBulkActions();
        }

        function updateBulkActions() {
            const checkboxes = document.querySelectorAll('.product-checkbox:checked');
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');

            if (checkboxes.length > 0) {
                bulkActions.style.display = 'block';
                selectedCount.textContent = `${checkboxes.length} item${checkboxes.length > 1 ? 's' : ''} selected`;
            } else {
                bulkActions.style.display = 'none';
            }
        }

        // Product Action Functions
        function viewProduct(productId) {
            window.location.href = `${window.URLROOT}/products/view/${productId}`;
        }

        function editProduct(productId) {
            window.location.href = `${window.URLROOT}/products/edit/${productId}`;
        }

        function viewSuppliers(productId) {
            window.location.href = `${window.URLROOT}/products/suppliers/${productId}`;
        }

        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                window.location.href = `${window.URLROOT}/products/delete/${productId}`;
            }
        }

        function linkSupplier(productId, productName) {
            // Set product details
            document.getElementById('link_product_id').value = productId;
            document.getElementById('link_product_name').textContent = productName;

            // Reset modal state
            document.getElementById('supplier_search').value = '';
            document.getElementById('confirm_link_supplier').disabled = true;
            selectedSupplierId = null;

            // Reset existing suppliers display
            document.getElementById('existing_suppliers_section').style.display = 'none';
            document.getElementById('existing_suppliers_list').innerHTML = '';

            // Reset supplier details form
            document.getElementById('supplier_details_form').style.display = 'none';
            document.getElementById('purchase_price').value = '';
            document.getElementById('supplier_sku').value = '';
            document.getElementById('lead_time_days').value = '7';
            document.getElementById('min_order_quantity').value = '1';
            document.getElementById('supplier_notes').value = '';
            document.getElementById('supplier_rating').value = '4';

            // Set up search functionality for this modal instance
            setupSupplierSearch();

            // Add validation event listeners
            document.getElementById('purchase_price').addEventListener('input', validateSupplierForm);

            // Load existing suppliers first
            loadExistingSuppliers(productId);

            // Show modal and load suppliers
            $('#LinkSupplierModal').modal('show');

            // Focus on search input after modal is shown
            $('#LinkSupplierModal').on('shown.bs.modal', function () {
                document.getElementById('supplier_search').focus();
            });

            loadSuppliersForLinking();
        }

        // Separate function to set up supplier search
        function setupSupplierSearch() {
            const supplierSearchInput = document.getElementById('supplier_search');
            if (supplierSearchInput) {
                // Remove any existing event listeners
                supplierSearchInput.removeEventListener('input', supplierSearchHandler);

                // Add the event listener
                supplierSearchInput.addEventListener('input', supplierSearchHandler);
            }
        }

        // Load existing suppliers for the product
        function loadExistingSuppliers(productId) {
            console.log('Loading existing suppliers for product:', productId);

            fetch(`${window.URLROOT}/products/getProductSuppliers/${productId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Existing suppliers data:', data);
                    displayExistingSuppliers(data.suppliers || []);
                })
                .catch(error => {
                    console.error('Error loading existing suppliers:', error);
                    // Don't show an error message, just continue without existing suppliers display
                });
        }

        function displayExistingSuppliers(suppliers) {
            const existingSection = document.getElementById('existing_suppliers_section');
            const existingList = document.getElementById('existing_suppliers_list');

            if (!suppliers || suppliers.length === 0) {
                existingSection.style.display = 'none';
                return;
            }

            let html = '';

            suppliers.forEach(supplier => {
                const price = supplier.purchase_price ? parseFloat(supplier.purchase_price).toFixed(2) : 'Not set';
                const leadTime = supplier.lead_time_days || 'Not set';
                const minOrderQty = supplier.min_order_quantity || 'Not set';
                const rating = supplier.supplier_rating || 'Not rated';

                html += `
                <div class="existing-supplier-item mb-2 p-2 border rounded border-secondary">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <strong class="text-primary">${escapeHtml(supplier.supplier_name)}</strong>
                            ${supplier.supplier_sku ? `<br><small class="text-muted">SKU: ${escapeHtml(supplier.supplier_sku)}</small>` : ''}
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">Purchase Price:</small>
                            <div class="font-weight-bold">₹${price}</div>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">Lead Time:</small>
                            <div class="font-weight-bold">${leadTime} days</div>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">Min Order:</small>
                            <div class="font-weight-bold">${minOrderQty}</div>
                        </div>
                        <div class="col-md-1">
                            <small class="text-muted">Rating:</small>
                            <div class="font-weight-bold">${rating !== 'Not rated' ? rating + '/5' : 'N/A'}</div>
                        </div>
                        <div class="col-md-1 text-right">
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="unlinkSupplier(${supplier.supplier_id})" title="Remove Supplier">
                                <i class="fas fa-unlink"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            });

            if (suppliers.length > 0) {
                html = `
                <div class="mb-2">
                    <small class="text-muted">This product currently has ${suppliers.length} supplier${suppliers.length > 1 ? 's' : ''} linked:</small>
                </div>
                ${html}
            `;
            }

            existingList.innerHTML = html;
            existingSection.style.display = 'block';
        }

        // Search handler function
        let supplierSearchTimeout;
        function supplierSearchHandler() {
            clearTimeout(supplierSearchTimeout);
            supplierSearchTimeout = setTimeout(() => {
                const searchTerm = this.value.trim();
                console.log('Searching for suppliers with term:', searchTerm); // Debug log
                loadSuppliersForLinking(searchTerm);
            }, 300);
        }

        let selectedSupplierId = null;

        function loadSuppliersForLinking(searchTerm = '') {
            console.log('loadSuppliersForLinking called with searchTerm:', searchTerm); // Debug log

            const loadingDiv = document.getElementById('suppliers_loading');
            const listDiv = document.getElementById('suppliers_list');

            if (!loadingDiv || !listDiv) {
                console.error('Required DOM elements not found');
                return;
            }

            loadingDiv.style.display = 'block';
            listDiv.innerHTML = '';

            const url = searchTerm
                ? `${window.URLROOT}/api/getSuppliers.php?search=${encodeURIComponent(searchTerm)}`
                : `${window.URLROOT}/api/getSuppliers.php`;

            console.log('Fetching suppliers from URL:', url); // Debug log

            fetch(url)
                .then(response => {
                    console.log('Response status:', response.status); // Debug log
                    return response.json();
                })
                .then(data => {
                    console.log('Suppliers data received:', data); // Debug log
                    loadingDiv.style.display = 'none';

                    if (data.success && data.suppliers && data.suppliers.length > 0) {
                        let html = '<div class="table-responsive"><table class="table table-hover"><tbody>';

                        data.suppliers.forEach(supplier => {
                            html += `
                            <tr class="supplier-row" style="cursor: pointer;" onclick="selectSupplier(${supplier.supplier_id}, '${supplier.supplier_name.replace(/'/g, '\\\'')}')" data-supplier-id="${supplier.supplier_id}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="form-check mr-3">
                                            <input type="radio" name="selected_supplier" value="${supplier.supplier_id}" class="form-check-input">
                                        </div>
                                        <div>
                                            <strong>${supplier.supplier_name}</strong><br>
                                            <small class="text-muted">
                                                ${supplier.contact_person ? supplier.contact_person + ' • ' : ''}
                                                ${supplier.phone ? supplier.phone + ' • ' : ''}
                                                ${supplier.email ? supplier.email : ''}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;
                        });

                        html += '</tbody></table></div>';
                        listDiv.innerHTML = html;
                    } else {
                        const message = searchTerm
                            ? `No suppliers found matching "${searchTerm}".`
                            : 'No suppliers found.';
                        listDiv.innerHTML = `<div class="alert alert-info"><i class="fas fa-info-circle mr-2"></i>${message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error fetching suppliers:', error); // Debug log
                    loadingDiv.style.display = 'none';
                    listDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle mr-2"></i>Error loading suppliers. Please try again.</div>';
                });
        }

        function selectSupplier(supplierId, supplierName) {
            // Clear previous selection
            document.querySelectorAll('.supplier-row').forEach(row => {
                row.classList.remove('table-active');
            });

            // Mark new selection
            const selectedRow = document.querySelector(`[data-supplier-id="${supplierId}"]`);
            if (selectedRow) {
                selectedRow.classList.add('table-active');
                selectedRow.querySelector('input[type="radio"]').checked = true;
            }

            // Show supplier details form
            const detailsForm = document.getElementById('supplier_details_form');
            detailsForm.style.display = 'block';

            // Focus on purchase price field
            setTimeout(() => {
                document.getElementById('purchase_price').focus();
            }, 100);

            // Enable confirm button (but require purchase price validation)
            selectedSupplierId = supplierId;
            validateSupplierForm();
        }

        function validateSupplierForm() {
            const purchasePrice = document.getElementById('purchase_price').value;
            const confirmButton = document.getElementById('confirm_link_supplier');

            // Enable button only if supplier is selected and purchase price is provided
            if (selectedSupplierId && purchasePrice && parseFloat(purchasePrice) > 0) {
                confirmButton.disabled = false;
            } else {
                confirmButton.disabled = true;
            }
        }

        function confirmLinkSupplier() {
            if (!selectedSupplierId) {
                showNotification('Please select a supplier', 'error');
                return;
            }

            // Validate purchase price
            const purchasePrice = document.getElementById('purchase_price').value;
            if (!purchasePrice || parseFloat(purchasePrice) <= 0) {
                showNotification('Please enter a valid purchase price', 'error');
                document.getElementById('purchase_price').focus();
                return;
            }

            const productId = document.getElementById('link_product_id').value;
            console.log('Linking supplier:', selectedSupplierId, 'to product:', productId);

            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('supplier_id', selectedSupplierId);
            formData.append('purchase_price', purchasePrice);
            formData.append('supplier_sku', document.getElementById('supplier_sku').value || '');
            formData.append('lead_time_days', document.getElementById('lead_time_days').value || '7');
            formData.append('min_order_quantity', document.getElementById('min_order_quantity').value || '1');
            formData.append('supplier_notes', document.getElementById('supplier_notes').value || '');
            formData.append('supplier_rating', document.getElementById('supplier_rating').value || '4');

            fetch(`${window.URLROOT}/products/linkSupplier`, {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    console.log('Link supplier response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Link supplier response data:', data);
                    if (data.success) {
                        $('#LinkSupplierModal').modal('hide');
                        showNotification('Supplier linked successfully', 'success');
                        // Optionally reload the page to show updated data
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification(data.error || 'Failed to link supplier', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error linking supplier:', error);
                    showNotification('An error occurred while linking supplier', 'error');
                });
        }

        function adjustInventory(productId, productName, currentStock) {
            document.getElementById('adjust_product_id').value = productId;
            document.getElementById('adjust_product_name').value = productName;
            document.getElementById('adjust_current_inventory').value = currentStock;
            $('#InventoryAdjustmentModal').modal('show');
        }

        function submitInventoryAdjustment() {
            const formData = new FormData();
            formData.append('product_id', document.getElementById('adjust_product_id').value);
            formData.append('adjustment_type', document.getElementById('adjust_type').value);
            formData.append('quantity', document.getElementById('adjust_quantity').value);
            formData.append('reason', document.getElementById('adjust_reason').value);

            fetch(`${window.URLROOT}/products/adjustInventory`, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('#InventoryAdjustmentModal').modal('hide');
                        showNotification('Inventory adjusted successfully', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification(data.error || 'Failed to adjust inventory', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred', 'error');
                });
        }

        // Filter Functions
        function filterProducts(filterType) {
            const rows = document.querySelectorAll('#productsTableBody tr');

            rows.forEach(row => {
                let show = true;

                // Skip empty state row
                if (row.cells.length === 1 || row.cells.length < 10) return;

                const stockBadge = row.cells[5].querySelector('.badge');

                if (!stockBadge) return; // Skip if no stock badge found

                switch (filterType) {
                    case 'low_stock':
                        const stockValue = parseInt(stockBadge.textContent);
                        show = stockValue <= 10 && stockValue > 0;
                        break;
                    case 'out_of_stock':
                        const outStockValue = parseInt(stockBadge.textContent);
                        show = outStockValue === 0;
                        break;
                    case 'high_value':
                        const priceCell = row.cells[6];
                        if (priceCell) {
                            const priceText = priceCell.textContent;
                            const price = parseInt(priceText.replace(/[₹,]/g, ''));
                            show = price >= 5000;
                        }
                        break;
                    case 'all':
                    default:
                        show = true;
                        break;
                }

                row.style.display = show ? '' : 'none';
            });
        }

        // Pagination Functions
        function changeItemsPerPage() {
            const perPage = document.getElementById('itemsPerPage').value;
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('per_page', perPage);
            currentUrl.searchParams.set('page', '1'); // Reset to first page
            window.location.href = currentUrl.toString();
        }

        // Export Functions
        function exportProducts(format) {
            const selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
            const exportType = selectedProducts.length > 0 ? 'selected' : 'all';

            const params = new URLSearchParams({
                format: format,
                type: exportType,
                products: selectedProducts.join(',')
            });

            window.location.href = `${window.URLROOT}/products/export?${params}`;
        }

        // Bulk Operations
        function bulkEdit() {
            const selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
            if (selectedProducts.length === 0) {
                alert('Please select products to edit');
                return;
            }
            window.location.href = `${window.URLROOT}/products/bulkEdit?products=${selectedProducts.join(',')}`;
        }

        function bulkPriceUpdate() {
            const selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
            if (selectedProducts.length === 0) {
                alert('Please select products to update prices');
                return;
            }

            const percentage = prompt('Enter price increase/decrease percentage (e.g., 10 for +10%, -5 for -5%):');
            if (percentage !== null && !isNaN(percentage)) {
                if (confirm(`Apply ${percentage}% price change to ${selectedProducts.length} selected products?`)) {
                    window.location.href = `${window.URLROOT}/products/bulkPriceUpdate?products=${selectedProducts.join(',')}&percentage=${percentage}`;
                }
            }
        }

        function bulkCategoryChange() {
            const selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
            if (selectedProducts.length === 0) {
                alert('Please select products to change category');
                return;
            }
            window.location.href = `${window.URLROOT}/products/bulkCategoryChange?products=${selectedProducts.join(',')}`;
        }

        function bulkDelete() {
            const selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
            if (selectedProducts.length === 0) {
                alert('Please select products to delete');
                return;
            }

            if (confirm(`Are you sure you want to delete ${selectedProducts.length} selected products? This action cannot be undone.`)) {
                window.location.href = `${window.URLROOT}/products/bulkDelete?products=${selectedProducts.join(',')}`;
            }
        }

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

        function unlinkSupplier(supplierId) {
            const productId = document.getElementById('link_product_id').value;

            if (confirm('Are you sure you want to remove this supplier link? This action cannot be undone.')) {
                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('supplier_id', supplierId);

                fetch(`${window.URLROOT}/products/unlinkSupplier`, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('Supplier unlinked successfully', 'success');
                            // Reload the existing suppliers display
                            loadExistingSuppliers(productId);
                        } else {
                            showNotification(data.message || 'Failed to unlink supplier', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error unlinking supplier:', error);
                        showNotification('An error occurred while unlinking supplier', 'error');
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