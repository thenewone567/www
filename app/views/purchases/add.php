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

                        <!-- Quick Filter Actions (keeps legacy IDs for JS compatibility) -->
                        <div class="row mb-3 mt-2">
                            <div class="col-md-2">
                                <select id="categoryFilter" class="form-control">
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
                            <div class="col-md-3">
                                <select id="supplierFilter" class="form-control">
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
                                <input id="priceMinFilter" class="form-control" placeholder="Min Price" type="number"
                                    min="0" step="0.01">
                            </div>
                            <div class="col-md-2">
                                <input id="priceMaxFilter" class="form-control" placeholder="Max Price" type="number"
                                    min="0" step="0.01">
                            </div>
                            <div class="col-md-3 d-flex align-items-center">
                                <small class="results-counter ml-2"><i class="fas fa-list mr-1"></i> <span
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
                                            data-inventory="<?php echo $product->current_inventory ?? 0; ?>">
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

                                                    <!-- Compact Middle Row: Supplier and Inventory -->
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <small class="text-dark font-weight-medium" style="font-size: 0.7rem;">
                                                            <i class="fas fa-building text-info mr-1"
                                                                style="font-size: 0.6rem;"></i>
                                                            <?php echo htmlspecialchars($product->supplier_name ?? 'No Supplier'); ?>
                                                        </small>
                                                        <span
                                                            class="badge <?php echo (($product->current_inventory ?? 0) > 0) ? 'badge-success' : 'badge-warning'; ?> px-1 py-0"
                                                            style="font-size: 0.65rem;">
                                                            <?php echo $product->current_inventory ?? 0; ?>
                                                        </span>
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
                                    <strong>Multi-Supplier Support:</strong> Items from different suppliers will automatically create separate purchase orders.
                                    <div id="po-preview" style="display: none;" class="mt-1">
                                        <small><i class="fas fa-arrow-right mr-1"></i>Your cart will create <strong><span id="po-count">0</span> purchase order(s)</strong> with unique PO numbers.</small>
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

        .toast-container {
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
                    <td>₹${(item.price * item.quantity).toFixed(2)}</td>
                    <td><button class="btn btn-sm btn-link text-danger remove-line" data-idx="${idx}">Remove</button></td>
                </tr>`;
                    });
                    tbody.innerHTML = rows;
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
                if (existing) existing.quantity += product.quantity;
                else cart.push(product);
                updateCartDisplay();
                updateOrderStats();
                showNotification(`${product.name} added to cart`, 'success', 2000);
            }

            // Escape helper
            function escapeHtml(str) {
                return String(str || '').replace(/[&<>"'`]/g, function (m) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": "&#39;", "`": "&#96;" }[m]; });
            }

            // Product filtering
            function filterProducts() {
                const searchTerm = (document.getElementById('unifiedPurchaseSearch') ? document.getElementById('unifiedPurchaseSearch').value : (document.getElementById('searchProducts') ? document.getElementById('searchProducts').value : '')).toLowerCase();
                const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
                const supplierFilter = document.getElementById('supplierFilter').value;
                const priceMin = parseFloat(document.getElementById('priceMinFilter').value) || 0;
                const priceMax = parseFloat(document.getElementById('priceMaxFilter').value) || Infinity;
                const products = document.querySelectorAll('.product-item');
                const tableRows = document.querySelectorAll('.product-row-table');
                let visibleCount = 0;

                // Filter card view products
                products.forEach(product => {
                    const name = product.dataset.name || '';
                    const sku = product.dataset.sku || '';
                    const category = product.dataset.category || '';
                    const supplierId = product.dataset.supplierId || '';
                    const price = parseFloat(product.dataset.price) || 0;

                    const matchesSearch = !searchTerm || name.includes(searchTerm) || sku.includes(searchTerm);
                    const matchesCategory = !categoryFilter || category === categoryFilter;
                    const matchesSupplier = !supplierFilter || supplierId === supplierFilter;
                    const matchesPrice = price >= priceMin && price <= priceMax;

                    if (matchesSearch && matchesCategory && matchesSupplier && matchesPrice) {
                        product.style.display = '';
                        visibleCount++;
                    } else {
                        product.style.display = 'none';
                    }
                });

                // Filter table view rows
                tableRows.forEach(row => {
                    const name = row.dataset.name || '';
                    const category = row.dataset.category || '';
                    const supplierId = row.dataset.supplierId || '';
                    const price = parseFloat(row.dataset.price) || 0;

                    const matchesSearch = !searchTerm || name.includes(searchTerm);
                    const matchesCategory = !categoryFilter || category === categoryFilter;
                    const matchesSupplier = !supplierFilter || supplierId === supplierFilter;
                    const matchesPrice = price >= priceMin && price <= priceMax;

                    if (matchesSearch && matchesCategory && matchesSupplier && matchesPrice) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                document.getElementById('resultsCount').textContent = `${visibleCount} products`;
            }

            // Debounced product filtering
            const debouncedFilterProducts = debounce(filterProducts, requirements.debounceMs);

            // Event listeners
            const searchEl = document.getElementById('unifiedPurchaseSearch') || document.getElementById('searchProducts');
            if (searchEl) searchEl.addEventListener('input', debouncedFilterProducts);
            document.getElementById('categoryFilter').addEventListener('change', filterProducts);
            document.getElementById('supplierFilter').addEventListener('change', filterProducts);
            document.getElementById('priceMinFilter').addEventListener('input', filterProducts);
            document.getElementById('priceMaxFilter').addEventListener('input', filterProducts);

            // View toggle functionality
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

                // Lazy-load suppliers when a table supplier select is focused
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
            function fetchSuppliersForProduct(productId, selectEl) {
                const supUrl = '<?php echo URLROOT; ?>/purchases/productSuppliers?product_id=' + encodeURIComponent(productId);
                console.debug('[DEBUG] fetchSuppliersForProduct url:', supUrl);
                fetch(supUrl, { credentials: 'same-origin' })
                    .then(r => r.json())
                    .then(data => {
                        const suppliers = data.suppliers || [];
                        console.debug('[DEBUG] suppliers for product', productId, 'count:', suppliers.length);
                        if (!selectEl) return;
                        // clear existing options (keep default)
                        const existingDefault = Array.from(selectEl.options).find(o => o.value === '');
                        selectEl.innerHTML = '';
                        if (existingDefault) selectEl.appendChild(existingDefault);

                        suppliers.forEach(s => {
                            const opt = document.createElement('option');
                            opt.value = s.supplier_id;
                            opt.textContent = s.supplier_name + (s.purchase_price ? ` — ₹${Number(s.purchase_price).toFixed(2)}` : '');
                            // mark primary if present
                            if (s.is_primary) opt.selected = true;
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

                // Lazy-load suppliers when a table supplier select is focused
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
                            // mark primary if present
                            if (s.is_primary) opt.selected = true;
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
                } else {
                    cart.push({
                        id: productId,
                        name: productName,
                        price: parseFloat(price),
                        quantity: 1,
                        supplier_name: supplierName || 'No Supplier',
                        supplier_id: supplierId || '',
                        inventory: parseInt(inventory || 0)
                    });
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