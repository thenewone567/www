<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid mt-0 pt-3">
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <a href="<?php echo URLROOT; ?>/purchases" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Purchases
            </a>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h2 class="mb-0"><i class="fa-solid fa-truck"></i> New Purchase</h2>
        </div>
    </div>

    <div class="row">
        <!-- Product List (70%) -->
        <div class="col-lg-8 col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Available Products</h5>
                </div>
                <div class="card-body">
                    <!-- Unified Smart Search & Scanner -->
                    <div class="unified-search-container mb-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                        id="purchaseSearchTypeDropdown">
                                        <i class="fas fa-search"></i> <span id="purchaseSearchTypeText">All Items</span>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item purchase-search-type active" href="#"
                                            data-search-type="all">
                                            <i class="fas fa-search mr-2"></i>All Items
                                        </a>
                                        <a class="dropdown-item purchase-search-type" href="#"
                                            data-search-type="barcode">
                                            <i class="fas fa-barcode mr-2"></i>Barcode
                                        </a>
                                        <a class="dropdown-item purchase-search-type" href="#" data-search-type="sku">
                                            <i class="fas fa-tag mr-2"></i>SKU
                                        </a>
                                        <a class="dropdown-item purchase-search-type" href="#" data-search-type="name">
                                            <i class="fas fa-box mr-2"></i>Product Name
                                        </a>
                                        <a class="dropdown-item purchase-search-type" href="#"
                                            data-search-type="category">
                                            <i class="fas fa-folder mr-2"></i>Category
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <input type="text" class="form-control" id="unifiedPurchaseSearch"
                                placeholder="🔍 Search by product name, SKU, barcode... or scan barcode"
                                autocomplete="off">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="purchaseScanBtn"
                                    title="Toggle Barcode Scanner">
                                    <i class="fas fa-barcode"></i>
                                </button>
                                <button class="btn btn-success" type="button" id="purchaseSearchBtn">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>

                        <div id="purchaseScannerContainer" class="mt-3" style="display: none;">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="scanner-preview-container">
                                        <video id="purchaseScannerVideo" class="scanner-video" autoplay muted></video>
                                        <div class="scanner-overlay">
                                            <div class="scanner-line"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="scanner-controls">
                                        <h6>Scanner Controls</h6>
                                        <div class="mb-3">
                                            <label class="form-label small">Scanner Mode:</label>
                                            <div class="btn-group btn-group-sm w-100" role="group">
                                                <button class="btn btn-outline-primary purchase-scanner-mode active"
                                                    data-mode="product">Product</button>
                                                <button class="btn btn-outline-info purchase-scanner-mode"
                                                    data-mode="supplier">Supplier</button>
                                            </div>
                                        </div>
                                        <div class="btn-group-vertical btn-group-sm w-100 mb-2">
                                            <button class="btn btn-primary" id="startPurchaseScanner"><i
                                                    class="fas fa-play"></i> Start Scanner</button>
                                            <button class="btn btn-outline-secondary" id="stopPurchaseScanner"
                                                disabled><i class="fas fa-stop"></i> Stop Scanner</button>
                                            <button class="btn btn-outline-primary" id="switchPurchaseCamera"
                                                disabled><i class="fas fa-sync"></i> Switch Camera</button>
                                            <button class="btn btn-outline-success" id="purchaseManualEntry"><i
                                                    class="fas fa-keyboard"></i> Manual Entry</button>
                                        </div>
                                        <div class="scanner-status"><small class="text-muted">Status: <span
                                                    id="purchaseScannerStatus">Scanner ready</span></small></div>
                                        <div class="mt-3">
                                            <h6 class="small">Recent Scans</h6>
                                            <div id="purchaseScanHistory" class="scan-history"
                                                style="max-height:150px;overflow-y:auto;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Smart Purchase Filters - Cost Optimization -->
                        <div class="row mb-3 mt-2">
                            <!-- Priority Filters for Cost Optimization -->
                            <div class="col-md-12 mb-2">
                                <div class="alert alert-info py-2 px-3">
                                    <i class="fas fa-dollar-sign mr-2"></i>
                                    <strong>Smart Purchase Filters:</strong> Find the best deals and avoid expensive
                                    products
                                </div>
                            </div>
                        </div>

                        <!-- Smart Filter Buttons Row -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="btn-group btn-group-sm" role="group" aria-label="Smart Filters">
                                    <button type="button" class="btn btn-outline-success" id="lowestPriceFilter"
                                        title="Show products with lowest prices first">
                                        <i class="fas fa-arrow-down"></i> Lowest Price First
                                    </button>
                                    <button type="button" class="btn btn-outline-warning" id="fastDeliveryFilter"
                                        title="Show products with fastest delivery times">
                                        <i class="fas fa-shipping-fast"></i> Fast Delivery
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="topRatedFilter"
                                        title="Show products from highest rated suppliers">
                                        <i class="fas fa-star"></i> Top Rated Suppliers
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="clearSmartFilters"
                                        title="Clear all smart filters">
                                        <i class="fas fa-times"></i> Clear Filters
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Standard Filter Row -->
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <select id="categoryFilter" class="form-control form-control-sm">
                                    <option value="">All Categories</option>
                                    <?php
                                    $categories = [];
                                    foreach ($data['products'] as $product) {
                                        if (!empty($product->category_name) && !in_array($product->category_name, $categories)) {
                                            $categories[] = $product->category_name;
                                        }
                                    }
                                    sort($categories);
                                    foreach ($categories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category); ?>">
                                            <?php echo htmlspecialchars($category); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="supplierFilter" class="form-control form-control-sm">
                                    <option value="">All Suppliers</option>
                                    <?php if (!empty($data['suppliers'])): ?>
                                        <?php foreach ($data['suppliers'] as $supplier): ?>
                                            <option value="<?php echo $supplier->supplier_id; ?>">
                                                <?php echo htmlspecialchars($supplier->supplier_name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input id="priceMinFilter" class="form-control form-control-sm" placeholder="Min Price"
                                    type="number" min="0" step="0.01">
                            </div>
                            <div class="col-md-2">
                                <input id="priceMaxFilter" class="form-control form-control-sm" placeholder="Max Price"
                                    type="number" min="0" step="0.01">
                            </div>
                            <div class="col-md-2">
                                <select id="deliveryTimeFilter" class="form-control form-control-sm">
                                    <option value="">Any Delivery Time</option>
                                    <option value="1-3">1-3 days</option>
                                    <option value="4-7">4-7 days</option>
                                    <option value="8-14">8-14 days</option>
                                    <option value="15+">15+ days</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-center">
                                <small class="results-counter"><i class="fas fa-list mr-1"></i> <span
                                        id="resultsCount">0</span> products found</small>
                            </div>
                        </div>

                        <div id="products-container">
                            <div class="row" id="products-row">
                                <?php if (!empty($data['products'])): ?>
                                    <?php foreach ($data['products'] as $product): ?>
                                        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-3 col-6 mb-4 product-item"
                                            data-id="<?php echo $product->product_id; ?>"
                                            data-name="<?php echo strtolower($product->product_name ?? ''); ?>"
                                            data-sku="<?php echo strtolower($product->sku ?? ''); ?>"
                                            data-category="<?php echo strtolower($product->category_name ?? ''); ?>"
                                            data-supplier-id="<?php echo $product->supplier_id ?? ''; ?>"
                                            data-supplier-name="<?php echo strtolower($product->supplier_name ?? ''); ?>"
                                            data-price="<?php echo $product->supplier_price ?? $product->unit_price ?? 0; ?>"
                                            data-current-cost="<?php echo $product->purchase_price ?? $product->selling_price ?? $product->supplier_price ?? 0; ?>"
                                            data-inventory="<?php echo $product->current_inventory ?? 0; ?>"
                                            data-lead-time="<?php echo $product->lead_time_days ?? 999; ?>"
                                            data-supplier-rating="<?php echo isset($product->reliability_score) ? $product->reliability_score : 5.0; ?>"
                                            data-delivery-performance="<?php echo isset($product->on_time_delivery_rate) ? $product->on_time_delivery_rate : 50; ?>">
                                            <div class="card product-card h-100 shadow-sm border border-light">
                                                <!-- Compact Card Header -->
                                                <div class="card-header bg-gradient-primary text-white py-1 px-2 border-bottom">
                                                    <h6 class="card-title mb-0 font-weight-bold text-truncate"
                                                        style="font-size: 0.85rem;">
                                                        <?php echo htmlspecialchars($product->product_name ?? ''); ?>
                                                    </h6>
                                                </div>

                                                <div class="card-body p-2 d-flex flex-column">
                                                    <!-- Compact Top Row: SKU and Category -->
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <small class="text-muted" style="font-size: 0.7rem;">
                                                            <i class="fas fa-hashtag text-secondary"
                                                                style="font-size: 0.6rem;"></i>
                                                            <?php echo htmlspecialchars($product->sku ?? 'N/A'); ?>
                                                        </small>
                                                        <small class="badge badge-outline-info px-1 py-0"
                                                            style="font-size: 0.65rem;">
                                                            <?php echo htmlspecialchars($product->category_name ?? 'Uncategorized'); ?>
                                                        </small>
                                                    </div>

                                                    <!-- Compact Middle Row: Supplier and Performance Indicators -->
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <small class="text-dark font-weight-medium" style="font-size: 0.7rem;">
                                                            <i class="fas fa-building text-info mr-1"
                                                                style="font-size: 0.6rem;"></i>
                                                            <?php echo htmlspecialchars($product->supplier_name ?? 'No Supplier'); ?>
                                                        </small>
                                                        <span
                                                            class="badge <?php echo (($product->current_inventory ?? 0) > 0) ? 'badge-success' : 'badge-warning'; ?> px-1 py-0"
                                                            style="font-size: 0.65rem;">
                                                            Stock: <?php echo $product->current_inventory ?? 0; ?>
                                                        </span>
                                                    </div>

                                                    <!-- Performance Indicators Row -->
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <!-- Supplier Rating -->
                                                        <small class="text-muted" style="font-size: 0.65rem;">
                                                            <i class="fas fa-star text-warning mr-1"></i>
                                                            <?php
                                                            $rating = isset($product->reliability_score) ? $product->reliability_score : 5.0;
                                                            echo number_format($rating, 1);
                                                            ?>/10
                                                        </small>

                                                        <!-- Delivery Time -->
                                                        <small class="text-muted" style="font-size: 0.65rem;">
                                                            <i class="fas fa-shipping-fast text-primary mr-1"></i>
                                                            <?php
                                                            $leadTime = $product->lead_time_days ?? 999;
                                                            if ($leadTime >= 999) {
                                                                echo 'TBD';
                                                            } else {
                                                                echo $leadTime . 'd';
                                                            }
                                                            ?>
                                                        </small>
                                                    </div>

                                                    <!-- Spacer -->
                                                    <div class="flex-grow-1"></div>

                                                    <!-- Compact Bottom: Price and Button -->
                                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                                        <div>
                                                            <div class="text-success font-weight-bold mb-0"
                                                                style="font-size: 0.9rem;">
                                                                ₹<?php echo number_format($product->supplier_price ?? $product->unit_price ?? 0, 2); ?>
                                                            </div>
                                                            <small class="text-muted" style="font-size: 0.6rem;">per
                                                                unit</small>
                                                        </div>
                                                        <button class="btn btn-primary btn-sm px-2 py-1 add-to-cart-btn"
                                                            style="font-size: 0.7rem;"
                                                            data-product-id="<?php echo $product->product_id; ?>"
                                                            data-product-name="<?php echo htmlspecialchars($product->product_name ?? ''); ?>"
                                                            data-price="<?php echo $product->supplier_price ?? $product->unit_price ?? 0; ?>"
                                                            data-current-cost="<?php echo $product->purchase_price ?? $product->selling_price ?? $product->supplier_price ?? 0; ?>"
                                                            data-supplier-id="<?php echo $product->supplier_id ?? ''; ?>"
                                                            data-supplier-name="<?php echo htmlspecialchars($product->supplier_name ?? ''); ?>">
                                                            <i class="fas fa-plus mr-1"></i>Add
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <div class="alert alert-info text-center">
                                            <i class="fa-solid fa-info-circle fa-2x mb-2"></i>
                                            <h5>No Products Available</h5>
                                            <p class="mb-0">No products with active suppliers found. Please add products and
                                                link them to suppliers first.</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div id="resultsHint" class="text-muted small mt-2">Showing <span
                                id="resultsCount"><?php echo count($data['products'] ?? []); ?></span> products</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart (30%) -->
        <div class="col-lg-4 col-xl-4">
            <div class="sticky-top cart-section" style="top: 1rem;">
                <form id="purchase-form" method="post" action="<?php echo URLROOT; ?>/purchases/add">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fa-solid fa-shopping-cart"></i> Purchase Cart</h5>
                            <button type="button" id="clear-cart" class="btn btn-outline-danger btn-sm"
                                style="display: none;">
                                <i class="fa-solid fa-trash"></i> Clear Cart
                            </button>
                        </div>
                        <div class="card-body">
                            <!-- Cart Items -->
                            <div class="table-responsive">
                                <table class="table table-sm" id="cart-table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Supplier</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Avg Price</th>
                                            <th>Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Cart items will be added here dynamically -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Empty Cart Message -->
                            <div id="empty-cart-message" class="text-center py-4">
                                <i class="fa-solid fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">Cart is empty</h6>
                                <p class="text-muted small">Click on products to add them to cart</p>
                            </div>

                            <!-- Cart Summary -->
                            <div id="cart-summary" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="mb-0">Total: </h4>
                                    <h4 class="mb-0 text-success">₹<span id="total-amount">0.00</span></h4>
                                </div>
                                <!-- total_amount_input appears later; removed duplicate -->

                                <!-- Cart automatically creates separate POs per supplier -->
                                <div class="alert alert-info small mb-3" id="multi-po-info">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <strong>Multi-Supplier Support:</strong> Items from different suppliers will
                                    automatically create separate purchase orders.
                                    <div id="po-preview" style="display: none;" class="mt-1">
                                        <small><i class="fas fa-arrow-right mr-1"></i>Your cart will create
                                            <strong><span id="po-count">0</span> purchase order(s)</strong> with unique
                                            PO numbers.</small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="expected_date">Expected Delivery</label>
                                    <input id="expected_date" name="expected_date" type="date" class="form-control"
                                        value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="notes">Notes (Optional)</label>
                                    <textarea id="notes" name="notes" class="form-control" rows="2"
                                        placeholder="Purchase order notes..."></textarea>
                                </div>

                                <div id="cart-dynamic-inputs"></div>
                                <input type="hidden" id="cart_items_input" value="[]">
                                <input type="hidden" id="total_amount_input" name="total_amount" value="0">
                            </div>

                            <button id="submit-purchase" type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-check mr-2"></i>Create Purchase Order(s)
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Order Statistics -->
                <div class="card mt-3 border-info d-none" id="order-stats">
                    <div class="card-body p-3">
                        <h6 class="card-title mb-2">Order Summary</h6>
                        <div class="small">
                            <div class="d-flex justify-content-between">
                                <span>Items:</span>
                                <span id="stats-items">0</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Suppliers:</span>
                                <span id="stats-suppliers">0</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Avg Price:</span>
                                <span id="stats-avg">₹0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Purchase Order Specific Styles */
        .product-card {
            transition: all 0.2s ease-in-out;
            border: 1px solid var(--card-border);
            background-color: var(--card-bg);
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--card-shadow-hover);
            border-color: var(--primary);
        }

        .product-item {
            transition: opacity 0.3s ease-in-out;
        }

        .product-item[style*="display: none"] {
            display: none !important;
        }

        .cart-section {
            background-color: var(--bg-secondary);
            border-radius: var(--border-radius);
        }

        .cart-line {
            border-bottom: 1px solid var(--card-border);
            padding: 0.5rem 0;
        }

        .cart-line:last-child {
            border-bottom: none;
        }

        .add-to-cart-btn {
            transition: all 0.2s ease-in-out;
        }

        .add-to-cart-btn:hover {
            transform: scale(1.05);
        }

        .sticky-top {
            position: sticky;
            top: 1rem;
            z-index: 1020;
        }

        /* Smart Filter Button Styles */
        .btn-group .btn {
            transition: all 0.3s ease-in-out;
        }

        #lowestPriceFilter.active {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: white !important;
            box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
        }

        #fastDeliveryFilter.active {
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
            color: #212529 !important;
            box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
        }

        #topRatedFilter.active {
            background-color: #007bff !important;
            border-color: #007bff !important;
            color: white !important;
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
        }

        #clearSmartFilters:hover {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: white !important;
        }

        /* Toast notification styles */
        .toast {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 9999;
        }

        .toast {
            min-width: 250px;
            margin-bottom: 0.5rem;
            opacity: 1;
            animation: slideInRight 0.3s ease-out;
        }

        .toast.fade {
            opacity: 0;
            animation: slideOutRight 0.3s ease-in;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .filter-controls {
            background-color: var(--bg-light);
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .results-counter {
            font-size: 0.875rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .price-display {
            font-weight: 600;
            color: var(--success);
        }

        .supplier-info {
            font-size: 0.875rem;
            color: var(--info);
        }

        .inventory-badge {
            background-color: var(--warning);
            color: var(--text-primary);
            padding: 0.25rem 0.5rem;
            border-radius: var(--border-radius-sm);
            font-size: 0.75rem;
            font-weight: 500;
        }

        .low-stock {
            background-color: var(--danger);
            color: var(--text-white);
        }

        .form-section {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--border-radius);
            padding: 1.5rem;
        }

        .page-header {
            background-color: var(--bg-primary);
            border-bottom: 1px solid var(--card-border);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .col-lg-8 {
                margin-bottom: 1rem;
            }

            .sticky-top {
                position: relative;
                top: auto;
            }

            .product-card {
                margin-bottom: 1rem;
            }
        }

        /* Dark theme adjustments */
        [data-theme="dark"] .filter-controls {
            background-color: var(--bg-dark);
            border: 1px solid var(--card-border);
        }

        [data-theme="dark"] .toast {
            background-color: var(--card-bg);
            border-color: var(--card-border);
            color: var(--text-primary);
        }
    </style>

    <?php // CSS styles for purchase order interface added above ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Requirements checklist mapping (keeps visibility during implementation)
            const requirements = {
                debounceMs: 300,
                endpoints: {
                    productsForAdd: 'productsForAdd',
                    productSuppliers: 'productSuppliers'
                }
            };

            // Small utilities
            function debounce(fn, ms) {
                let t;
                return function () {
                    const args = arguments;
                    clearTimeout(t);
                    t = setTimeout(() => fn.apply(this, args), ms);
                };
            }

            function showNotification(message, type = 'info', duration = 3000) {
                const container = document.createElement('div');
                container.className = `toast p-2 border ${type === 'success' ? 'border-success bg-light' : 'border-info bg-white'} toast-pos-top-right`;
                container.innerHTML = `<div class="small">${message}</div>`;
                document.body.appendChild(container);
                setTimeout(() => { container.classList.add('fade'); container.remove(); }, duration);
            }

            // Cart state
            let cart = [];

            function updateOrderStats() {
                const totalItems = cart.reduce((s, i) => s + i.quantity, 0);
                const totalValue = cart.reduce((s, i) => s + (i.price * i.quantity), 0);
                const avg = totalItems ? totalValue / totalItems : 0;
                document.getElementById('stats-items').textContent = totalItems;
                const suppliersCount = new Set(cart.filter(i => i.supplier_id).map(i => i.supplier_id)).size;
                document.getElementById('stats-suppliers').textContent = suppliersCount;
                document.getElementById('stats-avg').textContent = '₹' + avg.toFixed(2);
                document.getElementById('order-stats').style.display = cart.length ? '' : 'none';

                // Update PO preview
                const poPreview = document.getElementById('po-preview');
                const poCount = document.getElementById('po-count');
                if (cart.length > 0 && suppliersCount > 0) {
                    poCount.textContent = suppliersCount;
                    poPreview.style.display = '';
                } else {
                    poPreview.style.display = 'none';
                }
            }

            function updateCartDisplay() {
                const containerDiv = document.getElementById('cart-items-container');
                const tbody = document.querySelector('#cart-table tbody');
                const emptyMessage = document.getElementById('empty-cart-message');
                const cartSummary = document.getElementById('cart-summary');
                const orderStats = document.getElementById('order-stats');
                const clearBtn = document.getElementById('clear-cart');
                // helper ensures hidden form inputs mirror cart state
                function syncCartFormFields() {
                    const holder = document.getElementById('cart-dynamic-inputs');
                    if (!holder) return;
                    holder.innerHTML = '';
                    cart.forEach((line, idx) => {
                        [['product_id', line.id], ['quantity', line.quantity], ['price', line.price], ['supplier_id', line.supplier_id || '']].forEach(([k, v]) => {
                            const inp = document.createElement('input');
                            inp.type = 'hidden';
                            inp.name = `products[${idx}][${k}]`;
                            inp.value = v;
                            holder.appendChild(inp);
                        });
                    });
                }

                if (cart.length === 0) {
                    if (containerDiv) containerDiv.innerHTML = '';
                    if (tbody) tbody.innerHTML = '';
                    emptyMessage.style.display = 'block';
                    cartSummary.style.display = 'none';
                    orderStats.style.display = 'none';
                    clearBtn.style.display = 'none';
                    document.getElementById('cart_items_input').value = JSON.stringify([]);
                    document.getElementById('total_amount_input').value = '0';
                    document.getElementById('total-amount').textContent = '0.00';
                    syncCartFormFields();
                    return;
                }

                emptyMessage.style.display = 'none';
                clearBtn.style.display = 'inline-block';
                cartSummary.style.display = '';

                let total = 0;

                if (tbody) {
                    // Render as table rows
                    let rows = '';
                    cart.forEach((item, idx) => {
                        total += item.price * item.quantity;

                        // Handle average price display
                        let avgPriceDisplay, avgPriceTooltip;
                        if (item.averagePrice !== undefined && item.averagePrice !== null) {
                            avgPriceDisplay = `₹${parseFloat(item.averagePrice).toFixed(2)}`;
                            avgPriceTooltip = item.calculation ?
                                `Current: ${item.calculation.current_stock} @ ₹${item.calculation.current_avg_price}\\nNew: ${item.calculation.new_quantity} @ ₹${item.calculation.new_price}\\nAverage: ₹${item.averagePrice}` :
                                `Average price: ₹${item.averagePrice}`;
                        } else {
                            avgPriceDisplay = '<span class="text-muted">Calculating...</span>';
                            avgPriceTooltip = 'Average price calculation in progress';
                        }

                        rows += `
                <tr data-idx="${idx}">
                    <td>${escapeHtml(item.name)}</td>
                    <td><small class="text-muted">${escapeHtml(item.supplier_name || 'No Supplier')}</small></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-link change-qty" data-idx="${idx}" data-change="-1">-</button>
                            <span class="mx-1">${item.quantity}</span>
                            <button class="btn btn-sm btn-link change-qty" data-idx="${idx}" data-change="1">+</button>
                        </div>
                    </td>
                    <td>₹${item.price.toFixed(2)}</td>
                    <td>
                        <span class="text-info" title="${avgPriceTooltip}" data-toggle="tooltip">
                            ${avgPriceDisplay}
                        </span>
                    </td>
                    <td>₹${(item.price * item.quantity).toFixed(2)}</td>
                    <td><button class="btn btn-sm btn-link text-danger remove-line" data-idx="${idx}">Remove</button></td>
                </tr>`;
                    });
                    tbody.innerHTML = rows;

                    // Initialize tooltips for average price
                    if (typeof $ !== 'undefined') {
                        $('[data-toggle="tooltip"]').tooltip();
                    }

                    document.getElementById('total-amount').textContent = total.toFixed(2);
                    document.getElementById('total_amount_input').value = total.toFixed(2);
                    document.getElementById('cart_items_input').value = JSON.stringify(cart);
                    syncCartFormFields();

                    // Bind quantity buttons
                    tbody.querySelectorAll('.change-qty').forEach(btn => {
                        btn.addEventListener('click', function (e) {
                            const idx = parseInt(this.dataset.idx, 10);
                            const change = parseInt(this.dataset.change, 10);
                            if (!isNaN(idx)) {
                                cart[idx].quantity = Math.max(1, cart[idx].quantity + change);
                                // Recalculate average price when quantity changes
                                calculateSimpleAveragePrice(cart[idx].id, cart[idx].quantity, cart[idx].price);
                                updateCartDisplay();
                                updateOrderStats();
                            }
                        });
                    });

                    tbody.querySelectorAll('.remove-line').forEach(btn => {
                        btn.addEventListener('click', function () {
                            const idx = parseInt(this.dataset.idx, 10);
                            cart.splice(idx, 1);
                            updateCartDisplay();
                            updateOrderStats();
                        });
                    });
                } else if (containerDiv) {
                    // Legacy fallback: render as stacked div lines
                    let html = '';
                    cart.forEach((item, idx) => {
                        total += item.price * item.quantity;
                        html += `
                <div class="d-flex align-items-center mb-2 cart-line" data-idx="${idx}">
                    <div class="flex-grow-1">
                        <div class="font-weight-bold">${escapeHtml(item.name)}</div>
                        <div class="small text-muted">SKU: ${escapeHtml(item.sku || '')} • ${escapeHtml(item.supplier_name || '')}</div>
                        <div class="small">₹${item.price.toFixed(2)} x <button class="btn btn-sm btn-link change-qty" data-idx="${idx}" data-change="-1">-</button> <span class="mx-1">${item.quantity}</span> <button class="btn btn-sm btn-link change-qty" data-idx="${idx}" data-change="1">+</button></div>
                    </div>
                    <div class="text-right ml-2">₹${(item.price * item.quantity).toFixed(2)}<br><button class="btn btn-sm btn-link text-danger remove-line" data-idx="${idx}">Remove</button></div>
                </div>
            `;
                    });
                    containerDiv.innerHTML = html;
                    document.getElementById('total-amount').textContent = total.toFixed(2);
                    document.getElementById('total_amount_input').value = total.toFixed(2);
                    document.getElementById('cart_items_input').value = JSON.stringify(cart);
                    syncCartFormFields();

                    // Bind quantity buttons
                    containerDiv.querySelectorAll('.change-qty').forEach(btn => {
                        btn.addEventListener('click', function (e) {
                            const idx = parseInt(this.dataset.idx, 10);
                            const change = parseInt(this.dataset.change, 10);
                            if (!isNaN(idx)) {
                                cart[idx].quantity = Math.max(1, cart[idx].quantity + change);
                                // Recalculate average price when quantity changes
                                calculateSimpleAveragePrice(cart[idx].id, cart[idx].quantity, cart[idx].price);
                                updateCartDisplay();
                                updateOrderStats();
                            }
                        });
                    });

                    containerDiv.querySelectorAll('.remove-line').forEach(btn => {
                        btn.addEventListener('click', function () {
                            const idx = parseInt(this.dataset.idx, 10);
                            cart.splice(idx, 1);
                            updateCartDisplay();
                            updateOrderStats();
                        });
                    });
                }

                // Auto-select supplier if single supplier
                const supplierSelect = document.getElementById('supplier_id');
                const distinctSuppliers = Array.from(new Set(cart.filter(i => i.supplier_id).map(i => i.supplier_id)));
                if (supplierSelect) {
                    if (distinctSuppliers.length === 1) {
                        supplierSelect.value = distinctSuppliers[0] || '';
                    }
                }
            }

            // Add item (used by modal and quick-add)
            function addToCartWithDetails(product) {
                const existing = cart.find(i => i.id === product.id && i.supplier_id === product.supplier_id && i.price === product.price);
                if (existing) {
                    existing.quantity += product.quantity;
                } else {
                    // Set initial averagePrice to the purchase price
                    product.averagePrice = product.price;
                    cart.push(product);
                }

                // Calculate weighted average price immediately using available data
                calculateSimpleAveragePrice(product.id, product.quantity, product.price);

                updateCartDisplay();
                updateOrderStats();
                showNotification(`${product.name} added to cart`, 'success', 2000);
            }

            // Calculate weighted average price using server-side API
            async function calculateSimpleAveragePrice(productId, newQuantity, newPrice) {
                // Find the cart item
                const cartItem = cart.find(i => i.id == productId);
                if (!cartItem) return;

                console.log('API Calculation Debug:');
                console.log('Product ID:', productId);
                console.log('New Quantity:', newQuantity);
                console.log('New Price:', newPrice);

                try {
                    // Call the API to get accurate calculation
                    const response = await fetch('/purchases/apiCalculateAveragePrice', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: newQuantity,
                            price: newPrice
                        })
                    });

                    console.log('API Response Status:', response.status);
                    const data = await response.json();
                    console.log('API Response Data:', data);

                    if (data.success) {
                        cartItem.averagePrice = data.average_price;
                        cartItem.calculation = data.calculation;

                        // Debug the API calculation
                        console.log('*** API CALCULATION DETAILS ***');
                        console.log('API Result:', data.average_price);
                        console.log('Calculation breakdown:', data.calculation);
                        console.log('Expected for Bulb: 16 × ₹83.20 + qty × ₹70 = ₹80.56');

                        if (data.calculation.current_avg_price && data.calculation.current_avg_price < 70) {
                            console.warn('*** PROBLEM DETECTED ***');
                            console.warn(`API is using ₹${data.calculation.current_avg_price} as current cost`);
                            console.warn('This should be ₹83.20 for correct bulb calculation');
                            console.warn('The database purchase_price field is likely incorrect');
                        }

                        // Show calculation hint
                        const calc = data.calculation;
                        const hint = `💡 Average Price Calculation:\n` +
                            `Current: ${calc.current_stock} units @ ₹${calc.current_avg_price} = ₹${calc.current_total_value}\n` +
                            `+ New: ${calc.new_quantity} units @ ₹${calc.new_price} = ₹${calc.new_total_value}\n` +
                            `= Total: ${calc.total_stock_after} units @ ₹${data.average_price} average`;
                        showNotification(hint, 'info', 4000);

                        // Update the display immediately
                        updateCartDisplay();
                    } else {
                        console.error('API Error:', data.message);
                        // Check if it's an authentication error
                        if (response.status === 401) {
                            console.warn('Authentication required for API, using fallback calculation');
                        }
                        // Fallback to simple calculation if API fails
                        fallbackCalculation(cartItem, newQuantity, newPrice);
                    }
                } catch (error) {
                    console.error('Network error:', error);
                    // Fallback to simple calculation if network fails
                    fallbackCalculation(cartItem, newQuantity, newPrice);
                }
            }

            // Fallback calculation for when API is unavailable
            function fallbackCalculation(cartItem, newQuantity, newPrice) {
                // Get current inventory and current COST (not supplier price) from the product card
                const productCard = document.querySelector(`[data-id="${cartItem.id}"]`);

                // Also try to find by product name if ID doesn't work
                let productButton = null;
                if (!productCard) {
                    productButton = document.querySelector(`[data-product-id="${cartItem.id}"]`);
                }

                const dataSource = productCard || productButton;
                const currentInventory = dataSource ? parseInt(dataSource.dataset.inventory) || 0 : 0;

                // Use current inventory cost (purchase_price) for weighted average, not supplier price
                const currentCost = dataSource ? parseFloat(dataSource.dataset.currentCost) || 0 : 0;
                const supplierPrice = dataSource ? parseFloat(dataSource.dataset.price) || newPrice : newPrice;

                // Debug logging
                console.log('=== DETAILED FALLBACK CALCULATION DEBUG ===');
                console.log('Product ID:', cartItem.id);
                console.log('Product Name:', cartItem.name);
                console.log('Product Card (by data-id):', productCard);
                console.log('Product Button (by data-product-id):', productButton);
                console.log('Data Source Used:', dataSource);

                if (dataSource) {
                    console.log('All data attributes:', dataSource.dataset);
                    console.log('Raw currentCost value:', dataSource.dataset.currentCost);
                    console.log('Raw price value:', dataSource.dataset.price);
                    console.log('Raw inventory value:', dataSource.dataset.inventory);
                }

                console.log('Parsed Values:');
                console.log('Current Inventory:', currentInventory);
                console.log('Current Cost (for averaging):', currentCost);
                console.log('Supplier Price (for purchasing):', supplierPrice);
                console.log('New Quantity:', newQuantity);
                console.log('New Price:', newPrice);

                // If no current cost data, show a detailed error
                if (currentCost <= 0 && currentInventory > 0) {
                    console.warn('*** WARNING: No current cost data found! ***');
                    console.warn('This means purchase_price is empty in the database');
                    console.warn('Using supplier price as fallback for current cost');

                    const fallbackCurrentCost = supplierPrice;

                    if (currentInventory > 0 && fallbackCurrentCost > 0) {
                        const currentTotalValue = currentInventory * fallbackCurrentCost;
                        const newTotalValue = newQuantity * newPrice;
                        const totalStock = currentInventory + newQuantity;
                        const totalValue = currentTotalValue + newTotalValue;
                        const averagePrice = totalValue / totalStock;

                        console.log('FALLBACK Calculation Details:');
                        console.log(`Current: ${currentInventory} × ₹${fallbackCurrentCost} = ₹${currentTotalValue}`);
                        console.log(`New: ${newQuantity} × ₹${newPrice} = ₹${newTotalValue}`);
                        console.log(`Total: ${totalStock} units = ₹${totalValue}`);
                        console.log('Average Price:', averagePrice);

                        cartItem.averagePrice = averagePrice;
                        cartItem.calculation = {
                            current_stock: currentInventory,
                            current_avg_price: fallbackCurrentCost,
                            current_total_value: currentTotalValue,
                            new_quantity: newQuantity,
                            new_price: newPrice,
                            new_total_value: newTotalValue,
                            total_stock_after: totalStock,
                            total_value_after: totalValue,
                            note: 'Using supplier price as fallback (no purchase_price data)'
                        };

                        showNotification(`⚠️ No cost data found, using supplier price (₹${fallbackCurrentCost}) for averaging`, 'warning', 4000);
                        return;
                    }
                }

                if (currentInventory > 0 && currentCost > 0) {
                    // Use the correct formula: weighted average based on CURRENT COST (not supplier price)
                    const currentTotalValue = currentInventory * currentCost;
                    const newTotalValue = newQuantity * newPrice;
                    const totalStock = currentInventory + newQuantity;
                    const totalValue = currentTotalValue + newTotalValue;
                    const averagePrice = totalValue / totalStock;

                    console.log('CORRECT Calculation Details:');
                    console.log(`Current: ${currentInventory} × ₹${currentCost} = ₹${currentTotalValue}`);
                    console.log(`New: ${newQuantity} × ₹${newPrice} = ₹${newTotalValue}`);
                    console.log(`Total: ${totalStock} units = ₹${totalValue}`);
                    console.log('Average Price:', averagePrice);

                    cartItem.averagePrice = averagePrice;
                    cartItem.calculation = {
                        current_stock: currentInventory,
                        current_avg_price: currentCost,
                        current_total_value: currentTotalValue,
                        new_quantity: newQuantity,
                        new_price: newPrice,
                        new_total_value: newTotalValue,
                        total_stock_after: totalStock,
                        total_value_after: totalValue,
                        note: 'Using current inventory cost for calculation'
                    };

                    // Show the calculation
                    const hint = `💡 Weighted Average Calculation:\n` +
                        `Current: ${currentInventory} units @ ₹${currentCost.toFixed(2)} = ₹${currentTotalValue.toFixed(2)}\n` +
                        `+ New: ${newQuantity} units @ ₹${newPrice.toFixed(2)} = ₹${newTotalValue.toFixed(2)}\n` +
                        `= Total: ${totalStock} units @ ₹${averagePrice.toFixed(2)} average`;
                    showNotification(hint, 'info', 4000);
                } else {
                    // No existing inventory or no cost data, average price is just the new price
                    cartItem.averagePrice = newPrice;
                    cartItem.calculation = {
                        current_stock: 0,
                        current_avg_price: 0,
                        current_total_value: 0,
                        new_quantity: newQuantity,
                        new_price: newPrice,
                        new_total_value: newQuantity * newPrice,
                        total_stock_after: newQuantity,
                        total_value_after: newQuantity * newPrice,
                        note: currentInventory > 0 ? 'No current cost data available' : 'No existing inventory'
                    };

                    if (currentInventory > 0 && currentCost <= 0) {
                        showNotification('⚠️ No current cost data found, using new price only', 'warning', 3000);
                    }
                }
            }

            // Escape helper
            function escapeHtml(str) {
                return String(str || '').replace(/[&<>"'`]/g, function (m) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": "&#39;", "`": "&#96;" }[m]; });
            }

            // Product filtering with smart filters for cost optimization
            function filterProducts() {
                const searchTerm = (document.getElementById('unifiedPurchaseSearch') ? document.getElementById('unifiedPurchaseSearch').value : (document.getElementById('searchProducts') ? document.getElementById('searchProducts').value : '')).toLowerCase();
                const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
                const supplierFilter = document.getElementById('supplierFilter').value;
                const priceMin = parseFloat(document.getElementById('priceMinFilter').value) || 0;
                const priceMax = parseFloat(document.getElementById('priceMaxFilter').value) || Infinity;
                const deliveryTimeFilter = document.getElementById('deliveryTimeFilter').value;
                const products = document.querySelectorAll('.product-item');
                const tableRows = document.querySelectorAll('.product-row-table');
                let visibleCount = 0;

                // Get current smart filter states
                const isLowestPriceActive = document.getElementById('lowestPriceFilter').classList.contains('active');
                const isFastDeliveryActive = document.getElementById('fastDeliveryFilter').classList.contains('active');
                const isTopRatedActive = document.getElementById('topRatedFilter').classList.contains('active');

                // Collect all products and organize by product name for smart filtering
                let productArray = Array.from(products).map(product => ({
                    element: product,
                    id: product.dataset.id,
                    name: product.dataset.name || '',
                    sku: product.dataset.sku || '',
                    category: product.dataset.category || '',
                    supplierId: product.dataset.supplierId || '',
                    price: parseFloat(product.dataset.price) || 0,
                    leadTime: parseInt(product.dataset.leadTime) || 999,
                    supplierRating: parseFloat(product.dataset.supplierRating) || 5.0,
                    deliveryPerformance: parseFloat(product.dataset.deliveryPerformance) || 50
                }));

                // Filter products by basic criteria first
                productArray = productArray.filter(product => {
                    const matchesSearch = !searchTerm || product.name.includes(searchTerm) || product.sku.includes(searchTerm);
                    const matchesCategory = !categoryFilter || product.category === categoryFilter;
                    const matchesSupplier = !supplierFilter || product.supplierId === supplierFilter;
                    const matchesPrice = product.price >= priceMin && product.price <= priceMax;

                    // Delivery time filter
                    let matchesDeliveryTime = true;
                    if (deliveryTimeFilter) {
                        switch (deliveryTimeFilter) {
                            case '1-3':
                                matchesDeliveryTime = product.leadTime >= 1 && product.leadTime <= 3;
                                break;
                            case '4-7':
                                matchesDeliveryTime = product.leadTime >= 4 && product.leadTime <= 7;
                                break;
                            case '8-14':
                                matchesDeliveryTime = product.leadTime >= 8 && product.leadTime <= 14;
                                break;
                            case '15+':
                                matchesDeliveryTime = product.leadTime >= 15;
                                break;
                        }
                    }

                    return matchesSearch && matchesCategory && matchesSupplier && matchesPrice && matchesDeliveryTime;
                });

                // Apply smart filters - show only the best option for each unique product
                let finalProductsToShow = [];

                if (isLowestPriceActive || isFastDeliveryActive || isTopRatedActive) {
                    // Group products by product ID (same product from different suppliers)
                    const productGroups = {};
                    productArray.forEach(product => {
                        if (!productGroups[product.id]) {
                            productGroups[product.id] = [];
                        }
                        productGroups[product.id].push(product);
                    });

                    // For each product group, select the best option based on active filter
                    Object.values(productGroups).forEach(group => {
                        let bestProduct;

                        if (isLowestPriceActive) {
                            // Find product with lowest price
                            bestProduct = group.reduce((min, current) =>
                                current.price < min.price ? current : min
                            );
                        } else if (isFastDeliveryActive) {
                            // Find product with fastest delivery
                            bestProduct = group.reduce((fastest, current) =>
                                current.leadTime < fastest.leadTime ? current : fastest
                            );
                        } else if (isTopRatedActive) {
                            // Find product from highest rated supplier
                            bestProduct = group.reduce((topRated, current) =>
                                current.supplierRating > topRated.supplierRating ? current : topRated
                            );
                        }

                        if (bestProduct) {
                            finalProductsToShow.push(bestProduct);
                        }
                    });
                } else {
                    // No smart filter active, show all filtered products
                    finalProductsToShow = productArray;
                }

                // Hide all products first
                products.forEach(product => product.style.display = 'none');

                // Show only the selected products
                finalProductsToShow.forEach(product => {
                    product.element.style.display = '';
                    visibleCount++;
                });

                // Filter table view rows (if any) - using same logic
                tableRows.forEach(row => {
                    const name = row.dataset.name || '';
                    const category = row.dataset.category || '';
                    const supplierId = row.dataset.supplierId || '';
                    const price = parseFloat(row.dataset.price) || 0;
                    const leadTime = parseInt(row.dataset.leadTime) || 999;

                    const matchesSearch = !searchTerm || name.includes(searchTerm);
                    const matchesCategory = !categoryFilter || category === categoryFilter;
                    const matchesSupplier = !supplierFilter || supplierId === supplierFilter;
                    const matchesPrice = price >= priceMin && price <= priceMax;

                    let matchesDeliveryTime = true;
                    if (deliveryTimeFilter) {
                        switch (deliveryTimeFilter) {
                            case '1-3':
                                matchesDeliveryTime = leadTime >= 1 && leadTime <= 3;
                                break;
                            case '4-7':
                                matchesDeliveryTime = leadTime >= 4 && leadTime <= 7;
                                break;
                            case '8-14':
                                matchesDeliveryTime = leadTime >= 8 && leadTime <= 14;
                                break;
                            case '15+':
                                matchesDeliveryTime = leadTime >= 15;
                                break;
                        }
                    }

                    if (matchesSearch && matchesCategory && matchesSupplier && matchesPrice && matchesDeliveryTime) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                document.getElementById('resultsCount').textContent = `${visibleCount}`;
            }            // Debounced product filtering
            const debouncedFilterProducts = debounce(filterProducts, requirements.debounceMs);

            // Event listeners
            const searchEl = document.getElementById('unifiedPurchaseSearch') || document.getElementById('searchProducts');
            if (searchEl) searchEl.addEventListener('input', debouncedFilterProducts);
            document.getElementById('categoryFilter').addEventListener('change', filterProducts);
            document.getElementById('supplierFilter').addEventListener('change', filterProducts);
            document.getElementById('priceMinFilter').addEventListener('input', filterProducts);
            document.getElementById('priceMaxFilter').addEventListener('input', filterProducts);
            document.getElementById('deliveryTimeFilter').addEventListener('change', filterProducts);

            // Smart Filter Button Event Listeners
            document.getElementById('lowestPriceFilter').addEventListener('click', function () {
                // Toggle active state
                this.classList.toggle('active');

                // Remove other smart filter active states
                document.getElementById('fastDeliveryFilter').classList.remove('active');
                document.getElementById('topRatedFilter').classList.remove('active');

                // Update button styles
                updateSmartFilterButtons();
                filterProducts();

                // Show notification
                if (this.classList.contains('active')) {
                    showNotification('🏷️ Showing only the lowest priced option for each product', 'success');
                } else {
                    showNotification('💼 Price filtering cleared - showing all options', 'info');
                }
            });

            document.getElementById('fastDeliveryFilter').addEventListener('click', function () {
                // Toggle active state
                this.classList.toggle('active');

                // Remove other smart filter active states
                document.getElementById('lowestPriceFilter').classList.remove('active');
                document.getElementById('topRatedFilter').classList.remove('active');

                // Update button styles
                updateSmartFilterButtons();
                filterProducts();

                // Show notification
                if (this.classList.contains('active')) {
                    showNotification('🚚 Showing only the fastest delivery option for each product', 'success');
                } else {
                    showNotification('📦 Delivery filtering cleared - showing all options', 'info');
                }
            });

            document.getElementById('topRatedFilter').addEventListener('click', function () {
                // Toggle active state
                this.classList.toggle('active');

                // Remove other smart filter active states
                document.getElementById('lowestPriceFilter').classList.remove('active');
                document.getElementById('fastDeliveryFilter').classList.remove('active');

                // Update button styles
                updateSmartFilterButtons();
                filterProducts();

                // Show notification
                if (this.classList.contains('active')) {
                    showNotification('⭐ Showing only the top-rated supplier option for each product', 'success');
                } else {
                    showNotification('🏢 Supplier rating filtering cleared - showing all options', 'info');
                }
            });

            document.getElementById('clearSmartFilters').addEventListener('click', function () {
                // Clear all smart filter active states
                document.getElementById('lowestPriceFilter').classList.remove('active');
                document.getElementById('fastDeliveryFilter').classList.remove('active');
                document.getElementById('topRatedFilter').classList.remove('active');

                // Clear standard filters too
                document.getElementById('categoryFilter').value = '';
                document.getElementById('supplierFilter').value = '';
                document.getElementById('priceMinFilter').value = '';
                document.getElementById('priceMaxFilter').value = '';
                document.getElementById('deliveryTimeFilter').value = '';

                // Update button styles
                updateSmartFilterButtons();
                filterProducts();

                showNotification('🧹 All filters cleared - showing all products', 'info');
            });

            // Function to update smart filter button styles
            function updateSmartFilterButtons() {
                const buttons = ['lowestPriceFilter', 'fastDeliveryFilter', 'topRatedFilter'];
                buttons.forEach(buttonId => {
                    const button = document.getElementById(buttonId);
                    if (button.classList.contains('active')) {
                        // Convert outline button to solid button
                        button.className = button.className.replace('btn-outline-', 'btn-');
                    } else {
                        // Convert solid button to outline button
                        button.className = button.className.replace('btn-success', 'btn-outline-success');
                        button.className = button.className.replace('btn-warning', 'btn-outline-warning');
                        button.className = button.className.replace('btn-primary', 'btn-outline-primary');
                    }
                });
            }

            // Initialize filters on page load
            filterProducts();
            function toggleView(viewType) {
                const container = document.getElementById('products-container');
                const tableContainer = document.getElementById('table-container');
                const gridBtn = document.getElementById('grid-view');
                const listBtn = document.getElementById('list-view');
                const tableBtn = document.getElementById('table-view');

                // Hide all containers first
                container.style.display = 'none';
                tableContainer.style.display = 'none';

                // Remove all active states
                gridBtn.classList.remove('active');
                listBtn.classList.remove('active');
                tableBtn.classList.remove('active');

                if (viewType === 'table') {
                    // Show table view
                    tableContainer.style.display = 'block';
                    tableBtn.classList.add('active');
                    // Always fetch first page when showing table to ensure rows are loaded
                    try {
                        if (typeof fetchTablePage === 'function') {
                            currentTablePage = 1;
                            fetchTablePage(currentTablePage);
                        }
                    } catch (e) {
                        console.error('Failed to fetch products for table view', e);
                    }
                } else {
                    // Show grid/list view
                    container.style.display = 'block';

                    if (viewType === 'grid') {
                        // Grid view - 6 columns on XL/LG, 6 on MD, 4 on SM, 2 on XS
                        container.className = 'row';
                        document.querySelectorAll('.product-item').forEach(item => {
                            item.className = 'col-xl-2 col-lg-2 col-md-2 col-sm-3 col-6 mb-4 product-item';
                            item.classList.remove('list-view');

                            // Reset card styling
                            const card = item.querySelector('.product-card');
                            if (card) {
                                card.style.flexDirection = '';
                                card.style.height = '';

                                const cardBody = card.querySelector('.card-body');
                                if (cardBody) {
                                    cardBody.style.padding = '';
                                    cardBody.style.flex = '';
                                }

                                const cardHeader = card.querySelector('.card-header');
                                if (cardHeader) {
                                    cardHeader.style.flex = '';
                                    cardHeader.style.width = '';
                                }

                                const cardFooter = card.querySelector('.card-footer');
                                if (cardFooter) {
                                    cardFooter.style.flex = '';
                                }
                            }
                        });
                        gridBtn.classList.add('active');
                    } else {
                        // List view - single column with horizontal layout
                        container.className = 'row';
                        document.querySelectorAll('.product-item').forEach(item => {
                            item.className = 'col-12 mb-2 product-item list-view';

                            // Modify card layout for horizontal list view
                            const card = item.querySelector('.product-card');
                            if (card) {
                                card.style.flexDirection = 'row';
                                card.style.height = 'auto';

                                const cardHeader = card.querySelector('.card-header');
                                if (cardHeader) {
                                    cardHeader.style.flex = '0 0 200px';
                                    cardHeader.style.width = '200px';
                                }

                                const cardBody = card.querySelector('.card-body');
                                if (cardBody) {
                                    cardBody.style.flex = '1';
                                    cardBody.style.padding = '8px 12px';
                                }

                                const cardFooter = card.querySelector('.card-footer');
                                if (cardFooter) {
                                    cardFooter.style.flex = '0 0 120px';
                                }
                            }
                        });
                        listBtn.classList.add('active');
                    }
                }
            }

            // View toggle event listeners
            const gridToggle = document.getElementById('grid-view');
            if (gridToggle) gridToggle.addEventListener('click', () => toggleView('grid'));
            const listToggle = document.getElementById('list-view');
            if (listToggle) listToggle.addEventListener('click', () => toggleView('list'));
            const tableToggle = document.getElementById('table-view');
            if (tableToggle) tableToggle.addEventListener('click', () => toggleView('table'));

            // Server-driven table pagination
            let currentTablePage = 1;
            const perPage = 25;

            function renderTableRows(rows) {
                const tbody = document.querySelector('#products-table tbody');
                tbody.innerHTML = '';
                if (!rows || rows.length === 0) {
                    tbody.innerHTML = `
            <tr><td colspan="9" class="text-center py-4">
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <h5>No Products Found</h5>
                    <p class="mb-0">No products for this page/filter.</p>
                </div>
            </td></tr>`;
                    return;
                }

                rows.forEach(product => {
                    const price = product.supplier_price || product.unit_price || 0;
                    const inventory = product.current_inventory || 0;
                    const supplierName = product.supplier_name || 'No Supplier';

                    const tr = document.createElement('tr');
                    tr.className = 'product-row-table';
                    tr.dataset.id = product.product_id;
                    tr.dataset.name = (product.product_name || '').toLowerCase();
                    tr.dataset.price = price;
                    tr.dataset.inventory = inventory;
                    tr.dataset.category = (product.category_name || '').toLowerCase();
                    tr.dataset.supplierId = product.supplier_id || '';
                    tr.dataset.supplierName = supplierName.toLowerCase();

                    tr.innerHTML = `
            <td><input type="checkbox" class="form-check-input product-checkbox-table" value="${product.product_id}"></td>
            <td><div class="product-avatar bg-primary text-white product-avatar-sm product-avatar-circle">${(product.product_name || '').substring(0, 2).toUpperCase()}</div></td>
            <td><div class="font-weight-bold text-truncate-max-200">${escapeHtml(product.product_name || '')}</div><div class="text-muted small">SKU: ${escapeHtml(product.sku || '')}</div></td>
            <td><div class="h6 mb-1 text-success font-weight-bold">₹${Number(price).toFixed(2)}</div><small class="text-muted">per unit</small></td>
            <td><div class="h6 mb-1">${inventory}</div></td>
            <td>
                <select class="form-control form-control-sm table-supplier-select" data-product-id="${product.product_id}">
                    ${product.supplier_id ? `<option value="${product.supplier_id}" selected>${escapeHtml(supplierName)}</option>` : '<option value="">Default</option>'}
                </select>
            </td>
            <td><div class="text-center">${escapeHtml(product.category_name || '')}</div></td>
            <td><div class="text-center small text-muted">-</div></td>
            <td><div class="text-center"><button class="btn btn-primary btn-sm add-to-cart-btn" data-id="${product.product_id}" data-name="${escapeHtml(product.product_name || '')}" data-price="${price}"><i class="fas fa-plus"></i></button></div></td>
        `;

                    tbody.appendChild(tr);
                });

                // Rebind add-to-cart buttons for new rows
                document.querySelectorAll('#products-table .add-to-cart-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        const pid = this.dataset.id;
                        const pname = this.dataset.name;
                        const pprice = this.dataset.price;
                        // read selected supplier for this product row
                        const sel = document.querySelector(`#products-table .table-supplier-select[data-product-id="${pid}"]`);
                        const supplierId = sel ? sel.value : '';
                        const supplierName = sel ? (sel.selectedOptions[0] ? sel.selectedOptions[0].textContent : '') : '';
                        window.addToCartWithDetails(pid, pname, pprice, supplierName, supplierId, 0);
                    });
                });

                // Enhanced lazy-load suppliers with smart recommendations when a table supplier select is focused
                document.querySelectorAll('#products-table .table-supplier-select').forEach(sel => {
                    sel.addEventListener('focus', function () {
                        const pid = this.dataset.productId;
                        if (this.options.length > 1 || !pid) return; // already loaded
                        fetchSuppliersForProduct(pid, this);
                    });
                });
            }

            function renderPagination(current, count) {
                const totalPages = Math.max(1, Math.ceil(count / perPage));
                const ul = document.getElementById('products-pagination');
                ul.innerHTML = '';

                // Prev
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${current <= 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `<a class="page-link" href="#">«</a>`;
                prevLi.addEventListener('click', function (e) { e.preventDefault(); if (current > 1) { currentTablePage = current - 1; fetchTablePage(currentTablePage); } });
                ul.appendChild(prevLi);

                // Current only (simple)
                const pageLi = document.createElement('li');
                pageLi.className = 'page-item active';
                pageLi.innerHTML = `<span class="page-link">${current}/${totalPages}</span>`;
                ul.appendChild(pageLi);

                // Next
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${current >= totalPages ? 'disabled' : ''}`;
                nextLi.innerHTML = `<a class="page-link" href="#">»</a>`;
                nextLi.addEventListener('click', function (e) { e.preventDefault(); if (current < totalPages) { currentTablePage = current + 1; fetchTablePage(currentTablePage); } });
                ul.appendChild(nextLi);
            }

            // Fetch first page when switching to table view
            if (tableToggle) tableToggle.addEventListener('click', () => { currentTablePage = 1; fetchTablePage(currentTablePage); });

            // Wire up card supplier selects: lazy-load on focus and prefetch for known supplier ids
            document.querySelectorAll('.product-supplier-select').forEach(sel => {
                const pid = sel.dataset.productId;
                if (!pid) return;
                // If select only has default option and product has suppliers in server data, lazy-load when focused
                sel.addEventListener('focus', function () {
                    if (sel.options.length <= 1) {
                        fetchSuppliersForProduct(pid, sel);
                    }
                });

                // If the product-item already has a supplier id dataset, ensure select reflects it
                const item = document.querySelector(`.product-item[data-id="${pid}"]`);
                if (item && item.dataset.supplierId && sel.options.length <= 1) {
                    // fetch to populate and set
                    fetchSuppliersForProduct(pid, sel);
                }
            });

            // Fetch suppliers for a product and populate a select element
            function fetchSuppliersForProduct(productId, selectEl, quantity = 10, urgency = 'normal') {
                const supUrl = '<?php echo URLROOT; ?>/api/smartSupplierRecommendations.php?product_id=' +
                    encodeURIComponent(productId) +
                    '&quantity=' + encodeURIComponent(quantity) +
                    '&urgency=' + encodeURIComponent(urgency);
                console.debug('[DEBUG] fetchSuppliersForProduct (smart) url:', supUrl);

                fetch(supUrl, { credentials: 'same-origin' })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) {
                            console.error('Smart supplier API error:', data.error);
                            // Fallback to legacy API
                            return fetchSuppliersForProductLegacy(productId, selectEl);
                        }

                        const suppliers = data.suppliers || [];
                        console.debug('[DEBUG] smart suppliers for product', productId, 'count:', suppliers.length);

                        if (!selectEl) return;

                        // Clear existing options (keep default)
                        const existingDefault = Array.from(selectEl.options).find(o => o.value === '');
                        selectEl.innerHTML = '';
                        if (existingDefault) selectEl.appendChild(existingDefault);

                        suppliers.forEach(s => {
                            const opt = document.createElement('option');
                            opt.value = s.supplier_id;

                            // Build option text with price and recommendation badge
                            let optionText = s.supplier_name;
                            if (s.purchase_price) {
                                optionText += ` — ₹${Number(s.purchase_price).toFixed(2)}`;
                            }
                            if (s.recommendation_badge) {
                                optionText += ` [${s.recommendation_badge}]`;
                            }

                            opt.textContent = optionText;

                            // Add CSS classes for styling
                            if (s.is_recommended) {
                                opt.className = 'smart-recommended';
                                opt.selected = true; // Auto-select recommended supplier
                            }

                            // Add data attributes for additional info
                            opt.dataset.leadTime = s.lead_time_days;
                            opt.dataset.minOrder = s.min_order_quantity;
                            opt.dataset.reasoning = s.recommendation_reasoning || '';

                            selectEl.appendChild(opt);
                        });

                        // Show selection reasoning if available
                        const recommendedSupplier = suppliers.find(s => s.is_recommended);
                        if (recommendedSupplier && recommendedSupplier.recommendation_reasoning) {
                            showSupplierSelectionReasoning(selectEl, recommendedSupplier);
                        }

                        // If this is a card select, update the product-item dataset when changed
                        selectEl.addEventListener('change', function () {
                            const pid = this.dataset.productId;
                            const item = document.querySelector(`.product-item[data-id="${pid}"]`);
                            if (item) item.dataset.supplierId = this.value;

                            // Show reasoning for selected supplier
                            const selectedOption = this.options[this.selectedIndex];
                            if (selectedOption && selectedOption.dataset.reasoning) {
                                showSupplierSelectionInfo(selectedOption);
                            }
                        });

                    }).catch(err => {
                        console.error('Failed to load smart suppliers for product', productId, err);
                        // Fallback to legacy API
                        fetchSuppliersForProductLegacy(productId, selectEl);
                    });
            }

            // Legacy supplier fetching (fallback)
            function fetchSuppliersForProductLegacy(productId, selectEl) {
                const legacyUrl = '<?php echo URLROOT; ?>/purchases/productSuppliers?product_id=' + encodeURIComponent(productId);
                console.debug('[DEBUG] fetchSuppliersForProduct (legacy fallback) url:', legacyUrl);

                fetch(legacyUrl, { credentials: 'same-origin' })
                    .then(r => r.json())
                    .then(data => {
                        const suppliers = data.suppliers || [];
                        console.debug('[DEBUG] legacy suppliers for product', productId, 'count:', suppliers.length);

                        if (!selectEl) return;

                        // Clear existing options (keep default)
                        const existingDefault = Array.from(selectEl.options).find(o => o.value === '');
                        selectEl.innerHTML = '';
                        if (existingDefault) selectEl.appendChild(existingDefault);

                        suppliers.forEach(s => {
                            const opt = document.createElement('option');
                            opt.value = s.supplier_id;
                            opt.textContent = s.supplier_name + (s.purchase_price ? ` — ₹${Number(s.purchase_price).toFixed(2)}` : '');
                            selectEl.appendChild(opt);
                        });

                        // If this is a card select, update the product-item dataset when changed
                        selectEl.addEventListener('change', function () {
                            const pid = this.dataset.productId;
                            const item = document.querySelector(`.product-item[data-id="${pid}"]`);
                            if (item) item.dataset.supplierId = this.value;
                        });
                    }).catch(err => {
                        console.error('Failed to load legacy suppliers for product', productId, err);
                    });
            }

            // Show supplier selection reasoning tooltip
            function showSupplierSelectionReasoning(selectElement, supplier) {
                // Remove any existing reasoning display
                const existingReasoning = selectElement.parentNode.querySelector('.supplier-reasoning');
                if (existingReasoning) {
                    existingReasoning.remove();
                }

                // Add reasoning display
                const reasoningDiv = document.createElement('div');
                reasoningDiv.className = 'supplier-reasoning alert alert-info mt-1';
                reasoningDiv.innerHTML = `
                    <small>
                        <i class="fas fa-lightbulb mr-1"></i>
                        <strong>Smart Selection:</strong> ${supplier.supplier_name} recommended
                        <br>
                        <em>${supplier.recommendation_reasoning}</em>
                    </small>
                `;
                selectElement.parentNode.appendChild(reasoningDiv);
            }

            // Show supplier selection info when user changes selection
            function showSupplierSelectionInfo(selectedOption) {
                if (!selectedOption.dataset.reasoning) return;

                // Create a temporary tooltip or update existing reasoning
                const selectElement = selectedOption.parentElement;
                const existingReasoning = selectElement.parentNode.querySelector('.supplier-reasoning');

                if (existingReasoning) {
                    existingReasoning.innerHTML = `
                        <small>
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Selection Info:</strong> ${selectedOption.textContent}
                            <br>
                            <em>Lead time: ${selectedOption.dataset.leadTime} days, Min order: ${selectedOption.dataset.minOrder}</em>
                        </small>
                    `;
                }
            }

            // Product card click handlers
            document.querySelectorAll('.product-card').forEach(card => {
                card.addEventListener('click', function (e) {
                    // Don't trigger if clicking the add button
                    if (e.target.closest('.add-to-cart-btn')) return;

                    const productId = this.dataset.id;
                    const productName = this.dataset.name;
                    const price = this.dataset.price;
                    const supplierName = this.closest('.product-item').querySelector('.card-header small').textContent.trim();
                    const supplierId = this.closest('.product-item').dataset.supplierId;
                    const inventory = this.closest('.product-item').dataset.inventory;

                    window.addToCartWithDetails(productId, productName, price, supplierName, supplierId, inventory);

                    // Visual feedback
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });

            // Add to cart button handlers
            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                button.addEventListener('click', function (e) {
                    // read data attributes robustly (support data-id or data-product-id)
                    const pid = this.dataset.productId || this.dataset.id;
                    const pname = this.dataset.productName || this.dataset.name || '';
                    const pprice = parseFloat(this.dataset.price || 0) || 0;
                    const psupplierId = this.dataset.supplierId || this.dataset.supplier || '';
                    const psupplierName = this.dataset.supplierName || '';
                    const pinventory = parseInt(this.closest('.product-item') ? this.closest('.product-item').dataset.inventory : 0) || 0;

                    const product = {
                        id: pid,
                        name: pname,
                        price: pprice,
                        quantity: 1,
                        supplier_id: psupplierId || '',
                        supplier_name: psupplierName || 'No Supplier'
                    };

                    // Call the shared add function if available
                    if (typeof window.addToCartWithDetails === 'function') {
                        window.addToCartWithDetails(pid, pname, pprice, psupplierName, psupplierId, pinventory);
                    } else {
                        addToCartWithDetails(product);
                    }
                });
            });

            // Clear cart functionality
            document.getElementById('clear-cart').addEventListener('click', function () {
                if (cart.length === 0) {
                    showNotification('Cart is already empty.', 'info', 3000);
                    return;
                }

                if (confirm('Are you sure you want to clear the entire cart?')) {
                    cart = [];
                    updateCartDisplay();
                    updateOrderStats();
                    showNotification('Cart cleared successfully.', 'success', 3000);
                }
            });

            // Initialize
            filterProducts();
            updateCartDisplay(); // Initialize cart display
            // Server-driven table pagination (declared earlier above)

            // Wire up card supplier selects: lazy-load on focus and prefetch for known supplier ids
            document.querySelectorAll('.product-supplier-select').forEach(sel => {
                const pid = sel.dataset.productId;
                if (!pid) return;
                // If select only has default option and product has suppliers in server data, lazy-load when focused
                sel.addEventListener('focus', function () {
                    if (sel.options.length <= 1) {
                        fetchSuppliersForProduct(pid, sel);
                    }
                });

                // If the product-item already has a supplier id dataset, ensure select reflects it
                const item = document.querySelector(`.product-item[data-id="${pid}"]`);
                if (item && item.dataset.supplierId && sel.options.length <= 1) {
                    // fetch to populate and set
                    fetchSuppliersForProduct(pid, sel);
                }
            });

            function renderTableRows(rows) {
                const tbody = document.querySelector('#products-table tbody');
                tbody.innerHTML = '';
                if (!rows || rows.length === 0) {
                    tbody.innerHTML = `
            <tr><td colspan="9" class="text-center py-4">
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <h5>No Products Found</h5>
                    <p class="mb-0">No products for this page/filter.</p>
                </div>
            </td></tr>`;
                    return;
                }

                rows.forEach(product => {
                    const price = product.supplier_price || product.unit_price || 0;
                    const inventory = product.current_inventory || 0;
                    const supplierName = product.supplier_name || 'No Supplier';

                    const tr = document.createElement('tr');
                    tr.className = 'product-row-table';
                    tr.dataset.id = product.product_id;
                    tr.dataset.name = (product.product_name || '').toLowerCase();
                    tr.dataset.price = price;
                    tr.dataset.inventory = inventory;
                    tr.dataset.category = (product.category_name || '').toLowerCase();
                    tr.dataset.supplierId = product.supplier_id || '';
                    tr.dataset.supplierName = supplierName.toLowerCase();

                    tr.innerHTML = `
            <td><input type="checkbox" class="form-check-input product-checkbox-table" value="${product.product_id}"></td>
            <td><div class="product-avatar bg-primary text-white product-avatar-sm product-avatar-circle">${(product.product_name || '').substring(0, 2).toUpperCase()}</div></td>
            <td><div class="font-weight-bold text-truncate-max-200">${escapeHtml(product.product_name || '')}</div><div class="text-muted small">SKU: ${escapeHtml(product.sku || '')}</div></td>
            <td><div class="h6 mb-1 text-success font-weight-bold">₹${Number(price).toFixed(2)}</div><small class="text-muted">per unit</small></td>
            <td><div class="h6 mb-1">${inventory}</div></td>
            <td>
                <select class="form-control form-control-sm table-supplier-select" data-product-id="${product.product_id}">
                    ${product.supplier_id ? `<option value="${product.supplier_id}" selected>${escapeHtml(supplierName)}</option>` : '<option value="">Default</option>'}
                </select>
            </td>
            <td><div class="text-center">${escapeHtml(product.category_name || '')}</div></td>
            <td><div class="text-center small text-muted">-</div></td>
            <td><div class="text-center"><button class="btn btn-primary btn-sm add-to-cart-btn" data-id="${product.product_id}" data-name="${escapeHtml(product.product_name || '')}" data-price="${price}"><i class="fas fa-plus"></i></button></div></td>
        `;

                    tbody.appendChild(tr);
                });

                // Rebind add-to-cart buttons for new rows  
                document.querySelectorAll('#products-table .add-to-cart-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        const pid = this.dataset.id;
                        const pname = this.dataset.name;
                        const pprice = this.dataset.price;
                        // read selected supplier for this product row
                        const sel = document.querySelector(`#products-table .table-supplier-select[data-product-id="${pid}"]`);
                        const supplierId = sel ? sel.value : '';
                        const supplierName = sel ? (sel.selectedOptions[0] ? sel.selectedOptions[0].textContent : '') : '';
                        window.addToCartWithDetails(pid, pname, pprice, supplierName, supplierId, 0);
                    });
                });

                // Enhanced lazy-load suppliers with smart recommendations when a table supplier select is focused
                document.querySelectorAll('#products-table .table-supplier-select').forEach(sel => {
                    sel.addEventListener('focus', function () {
                        const pid = this.dataset.productId;
                        if (this.options.length > 1 || !pid) return; // already loaded
                        fetchSuppliersForProduct(pid, this);
                    });
                });
            }

            function escapeHtml(str) {
                return (str + '').replace(/[&<>"'`]/g, function (m) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": "&#39;", "`": "&#96;" }[m]; });
            }

            function fetchTablePage(page = 1) {
                const params = new URLSearchParams();
                params.set('page', page);
                params.set('perPage', perPage);
                const search = (document.getElementById('unifiedPurchaseSearch') ? document.getElementById('unifiedPurchaseSearch').value : (document.getElementById('searchProducts') ? document.getElementById('searchProducts').value : ''));
                if (search) params.set('search', search);
                const supplier = document.getElementById('supplierFilter').value;
                if (supplier) params.set('supplier_id', supplier);

                const tbody = document.querySelector('#products-table tbody');
                tbody.innerHTML = `<tr><td colspan="9" class="text-center py-4">Loading...</td></tr>`;

                const url = '<?php echo URLROOT; ?>/purchases/productsForAdd?' + params.toString();
                console.debug('[DEBUG] fetchTablePage url:', url);
                fetch(url, { credentials: 'same-origin' })
                    .then(r => {
                        if (!r.ok) throw new Error('Network response was not ok');
                        return r.json();
                    })
                    .then(data => {
                        console.debug('[DEBUG] productsForAdd response:', data);
                        // Support multiple response shapes
                        let rows = [];
                        let count = 0;

                        if (!data) {
                            rows = [];
                        } else if (Array.isArray(data)) {
                            rows = data;
                            count = data.length;
                        } else if (Array.isArray(data.items)) {
                            rows = data.items;
                            count = data.count || data.total || rows.length;
                        } else if (Array.isArray(data.rows)) {
                            rows = data.rows;
                            count = data.count || data.total || rows.length;
                        } else if (Array.isArray(data.data)) {
                            rows = data.data;
                            count = data.count || data.total || rows.length;
                        } else if (Array.isArray(data.results)) {
                            rows = data.results;
                            count = data.count || data.total || rows.length;
                        } else if (Array.isArray(data.products)) {
                            rows = data.products;
                            count = data.count || data.total || rows.length;
                        } else {
                            // single-object response
                            rows = data.rows || data.items || [];
                            count = data.count || data.total || rows.length;
                        }

                        renderTableRows(rows || []);
                        renderPagination(page, Number(count) || (rows ? rows.length : 0));

                        // Update results hint when table mode is active
                        try {
                            const rc = Number(count) || (rows ? rows.length : 0);
                            document.getElementById('resultsCount').textContent = rc + ' products';
                        } catch (e) { console.warn('Failed to update resultsCount', e); }

                        // Show server-side fallback banner when present
                        const warn = document.getElementById('table-warning');
                        if (data && data.fallback) {
                            if (warn) {
                                warn.textContent = 'Showing a permissive product list because no active product-supplier links were found. Some products may not have supplier selections.';
                                warn.classList.remove('d-none');
                            }
                        } else {
                            if (warn) warn.classList.add('d-none');
                        }
                    }).catch(err => {
                        console.error('Failed to load products page', err);
                        tbody.innerHTML = `<tr><td colspan="9" class="text-center text-danger py-4">Failed to load products</td></tr>`;
                    });
            }

            // Fetch suppliers for a product and populate a select element
            function fetchSuppliersForProduct(productId, selectEl) {
                const supUrl = '<?php echo URLROOT; ?>/purchases/productSuppliers?product_id=' + encodeURIComponent(productId);
                console.debug('[DEBUG] fetchSuppliersForProduct (modal) url:', supUrl);
                fetch(supUrl, { credentials: 'same-origin' })
                    .then(r => r.json())
                    .then(data => {
                        const suppliers = data.suppliers || [];
                        console.debug('[DEBUG] suppliers (modal) for product', productId, 'count:', suppliers.length);
                        if (!selectEl) return;
                        // clear existing options (keep default)
                        const existingDefault = Array.from(selectEl.options).find(o => o.value === '');
                        selectEl.innerHTML = '';
                        if (existingDefault) selectEl.appendChild(existingDefault);

                        suppliers.forEach(s => {
                            const opt = document.createElement('option');
                            opt.value = s.supplier_id;
                            opt.textContent = s.supplier_name + (s.purchase_price ? ` — ₹${Number(s.purchase_price).toFixed(2)}` : '');
                            selectEl.appendChild(opt);
                        });

                        // If this is a card select, update the product-item dataset when changed
                        selectEl.addEventListener('change', function () {
                            const pid = this.dataset.productId;
                            const item = document.querySelector(`.product-item[data-id="${pid}"]`);
                            if (item) item.dataset.supplierId = this.value;
                        });
                    }).catch(err => {
                        console.error('Failed to load suppliers for product', productId, err);
                    });
            }

            // Initial population of category and supplier filters
            (function initFilters() {
                const categoryFilter = document.getElementById('categoryFilter');
                const supplierFilter = document.getElementById('supplierFilter');

                // Category filter options (server-populated)
                <?php if (!empty($data['categories'])): ?>
                                            const categories = <?php echo json_encode($data['categories']); ?>;
                                            categories.forEach(cat => {
                                                const option = document.createElement('option');
                                                option.value = cat.category_name;
                                                option.textContent = cat.category_name;
                                                categoryFilter.appendChild(option);
                                            });
                <?php endif; ?>

                // Supplier filter options (server-populated)
                <?php if (!empty($data['suppliers'])): ?>
                                            const suppliers = <?php echo json_encode($data['suppliers']); ?>;
                                            suppliers.forEach(sup => {
                                                const option = document.createElement('option');
                                                option.value = sup.supplier_id;
                                                option.textContent = sup.supplier_name;
                                                supplierFilter.appendChild(option);
                                            });
                <?php endif; ?>
            })();

            // Global functions for cart operations
            window.changeQuantity = function (productId, change) {
                const item = cart.find(item => item.id === productId);
                if (item) {
                    item.quantity += change;
                    if (item.quantity <= 0) {
                        removeFromCart(productId);
                    } else {
                        updateCartDisplay();
                        updateOrderStats();
                    }
                }
            };

            window.removeFromCart = function (productId) {
                cart = cart.filter(item => item.id !== productId);
                updateCartDisplay();
                updateOrderStats();
            };

            // Enhanced addToCart function with supplier tracking
            window.addToCartWithDetails = function (productId, productName, price, supplierName, supplierId, inventory) {
                const existingItem = cart.find(item => item.id === productId);

                if (existingItem) {
                    existingItem.quantity++;
                    // Recalculate average price when quantity increases
                    calculateSimpleAveragePrice(existingItem.id, existingItem.quantity, existingItem.price);
                } else {
                    const newItem = {
                        id: productId,
                        name: productName,
                        price: parseFloat(price),
                        quantity: 1,
                        supplier_name: supplierName || 'No Supplier',
                        supplier_id: supplierId || '',
                        inventory: parseInt(inventory || 0)
                    };
                    // Set initial averagePrice to the purchase price
                    newItem.averagePrice = newItem.price;
                    cart.push(newItem);
                    // Calculate weighted average price immediately
                    calculateSimpleAveragePrice(newItem.id, newItem.quantity, newItem.price);
                }

                updateCartDisplay();
                updateOrderStats();
                showNotification(`${productName} added to cart`, 'success', 2000);
            };

            // Modal quick add
            document.getElementById('modal-add-btn').addEventListener('click', function () {
                const productId = document.getElementById('modal-product-id').value;
                const productName = document.getElementById('modal-product-name').textContent.trim();
                const quantity = parseInt(document.getElementById('modal-qty').value, 10) || 1;
                const supplierId = document.getElementById('modal-supplier').value;
                const supplierName = document.getElementById('modal-supplier').selectedOptions[0]?.textContent.trim() || 'No Supplier';
                const price = parseFloat(document.getElementById('modal-price').value) || 0;

                addToCartWithDetails({ id: productId, name: productName, quantity, supplier_id: supplierId, supplier_name: supplierName, price });

                $('#quickAddModal').modal('hide');
            });

            // Show quick add modal with product details
            document.querySelectorAll('.product-card').forEach(card => {
                card.addEventListener('click', function (e) {
                    // Don't trigger if clicking the add button
                    if (e.target.closest('.add-to-cart-btn')) return;

                    const productId = this.dataset.id;
                    const productName = this.dataset.name;
                    const price = this.dataset.price;
                    const supplierName = this.closest('.product-item').querySelector('.card-header small').textContent.trim();
                    const supplierId = this.closest('.product-item').dataset.supplierId;
                    const inventory = this.closest('.product-item').dataset.inventory;

                    document.getElementById('modal-product-id').value = productId;
                    document.getElementById('modal-product-name').textContent = productName;
                    document.getElementById('modal-qty').value = 1;
                    document.getElementById('modal-price').value = price;
                    const modalSupplierSelect = document.getElementById('modal-supplier');
                    modalSupplierSelect.innerHTML = ''; // Clear existing options
                    // Add default option
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'Select supplier';
                    modalSupplierSelect.appendChild(defaultOption);
                    // Fetch and populate suppliers
                    fetchSuppliersForProduct(productId, modalSupplierSelect);

                    $('#quickAddModal').modal('show');
                });
            });
        });
    </script>

    <?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>