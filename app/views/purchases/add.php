<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid mt-0 pt-3">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <a href="<?php echo URLROOT; ?>/purchases" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Purchases
            </a>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h2 class="mb-0"><i class="fa-solid fa-cart-plus"></i> New Purchase Order</h2>
        </div>
    </div>

    <div class="row">
        <!-- Products Section -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa-solid fa-boxes"></i> Products</h5>
                    <small class="text-muted"><?php echo count($data['products']); ?> products available</small>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-search"></i>
                                    </span>
                                </div>
                                <input type="text" id="searchProducts" class="form-control" 
                                       placeholder="Search by name, SKU...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-barcode"></i>
                                    </span>
                                </div>
                                <input type="text" id="barcodeSearch" class="form-control" 
                                       placeholder="Scan/Enter barcode">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="categoryFilter" class="form-control">
                                <option value="">All Categories</option>
                                <?php 
                                // Get unique categories from products
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
                    </div>

                    <!-- Quick Actions Row -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" id="clear-search" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-times"></i> Clear Search
                                </button>
                                <button type="button" id="show-all" class="btn btn-outline-primary">
                                    <i class="fa-solid fa-list"></i> Show All
                                </button>
                                <button type="button" id="show-low-inventory" class="btn btn-outline-warning">
                                    <i class="fa-solid fa-exclamation-triangle"></i> Low Inventory
                                </button>
                                <button type="button" id="show-out-of-inventory" class="btn btn-outline-danger">
                                    <i class="fa-solid fa-ban"></i> Out of Inventory
                                </button>
                                <button type="button" id="show-recently-added" class="btn btn-outline-success">
                                    <i class="fa-solid fa-clock"></i> Recently Added
                                </button>
                                <button type="button" id="show-high-value" class="btn btn-outline-info">
                                    <i class="fa-solid fa-gem"></i> High Value
                                </button>
                                <button type="button" id="sort-price" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-sort"></i> Sort by Price
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Filters Row -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="small text-muted">Price Range Filter:</label>
                            <div class="input-group input-group-sm">
                                <input type="number" id="priceMinFilter" class="form-control" placeholder="Min ₹" min="0" step="0.01">
                                <div class="input-group-append input-group-prepend">
                                    <span class="input-group-text">to</span>
                                </div>
                                <input type="number" id="priceMaxFilter" class="form-control" placeholder="Max ₹" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted">Inventory Level Filter:</label>
                            <select id="InventoryLevelFilter" class="form-control form-control-sm">
                                <option value="">All Inventory Levels</option>
                                <option value="out-of-Inventory">Out of Inventory (0 units)</option>
                                <option value="critical">Critical (1-5 units)</option>
                                <option value="low">Low (6-20 units)</option>
                                <option value="normal">Normal (21-50 units)</option>
                                <option value="high">High Inventory (51+ units)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted">Product Status:</label>
                            <select id="statusFilter" class="form-control form-control-sm">
                                <option value="">All Products</option>
                                <option value="needs-reorder">Needs Reorder</option>
                                <option value="recently-updated">Recently Updated</option>
                                <option value="never-ordered">Never Ordered</option>
                                <option value="frequently-ordered">Frequently Ordered</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filter Summary -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div id="filterSummary" class="alert alert-light py-2 mb-0" style="display: none;">
                                <small>
                                    <i class="fas fa-filter"></i> 
                                    <strong>Active Filters:</strong> 
                                    <span id="activeFilters"></span>
                                    <button type="button" id="clearFilters" class="btn btn-sm btn-outline-secondary ml-2">
                                        Clear All
                                    </button>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-boxes"></i> Available Products
                        </h5>
                        <span id="resultsCount" class="badge badge-info">Loading products...</span>
                    </div>
                    <div id="product-list">
                        <div class="row" id="products-container">
                            <?php if (empty($data['products'])): ?>
                                <div class="alert alert-warning w-100 text-center">
                                    No products found in database. Please check your product table or seed data.
                                </div>
                            <?php endif; ?>
                            <?php foreach ($data['products'] as $product): ?>
                                <div class="col-md-4 col-sm-6 mb-3 product-item" 
                                     data-name="<?php echo strtolower($product->product_name); ?>"
                                     data-sku="<?php echo strtolower($product->sku ?? ''); ?>"
                                     data-category="<?php echo strtolower($product->category_name ?? ''); ?>"
                                     data-id="<?php echo $product->product_id; ?>"
                                     data-price="<?php echo $product->unit_price; ?>"
                                     data-inventory="<?php echo $product->current_inventory ?? 0; ?>"
                                     data-created-date="<?php echo $product->created_at ?? ''; ?>">
                                    <div class="card product-card h-100" 
                                         data-id="<?php echo $product->product_id; ?>"
                                         data-name="<?php echo htmlspecialchars($product->product_name); ?>"
                                         data-price="<?php echo $product->unit_price; ?>"
                                         data-inventory="<?php echo $product->current_inventory ?? 0; ?>"
                                         data-created-date="<?php echo $product->created_at ?? ''; ?>">
                                        <div class="card-body text-center">
                                            <?php if (!empty($product->image_path)): ?>
                                                <img src="<?php echo htmlspecialchars($product->image_path); ?>" 
                                                    class="product-img-thumb mb-2" alt="Product image" style="max-width: 60px; max-height: 60px;">
                                            <?php endif; ?>
                                            <h6 class="card-title mb-2">
                                                <?php echo htmlspecialchars($product->product_name); ?>
                                                <?php $inventoryVal = $product->current_inventory ?? 0; ?>
                                                <?php if ($inventoryVal == 0): ?>
                                                    <span class="badge badge-danger">Out of Inventory</span>
                                                <?php elseif ($inventoryVal <= 5): ?>
                                                    <span class="badge badge-warning">Low Inventory</span>
                                                <?php elseif ($inventoryVal > 50): ?>
                                                    <span class="badge badge-success">High Inventory</span>
                                                <?php elseif ($inventoryVal > 20): ?>
                                                    <span class="badge badge-info">Good Inventory</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Normal</span>
                                                <?php endif; ?>
                                                
                                                <?php if ($product->unit_price >= 500): ?>
                                                    <span class="badge badge-info">High Value</span>
                                                <?php endif; ?>
                                                
                                                <?php if (($product->product_id ?? 0) > 50): ?>
                                                    <span class="badge badge-success">Recently Added</span>
                                                <?php endif; ?>
                                            </h6>
                                            <p class="text-muted small mb-1">
                                                SKU: <?php echo htmlspecialchars($product->sku ?? 'N/A'); ?>
                                            </p>
                                            <p class="text-muted small mb-2">
                                                <?php echo htmlspecialchars($product->category_name ?? 'Uncategorized'); ?>
                                            </p>
                                            <div class="mb-2">
                                                <span class="badge badge-info">
                                                    Current Inventory: <?php echo $inventoryVal; ?>
                                                </span>
                                            </div>
                                            <h5 class="text-primary mb-2"><?php echo formatCurrency($product->unit_price, 2); ?></h5>
                                            <button class="btn btn-success btn-sm w-100">
                                                <i class="fa-solid fa-plus"></i> Add to Order
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- No products found message -->
                        <div id="no-products-found" class="text-center py-4" style="display: none;">
                            <i class="fa-solid fa-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No products found</h5>
                            <p class="text-muted">Try adjusting your search terms or filters</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Purchase Order Cart Section -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa-solid fa-shopping-cart"></i> Purchase Order</h5>
                    <button type="button" id="clear-cart" class="btn btn-outline-danger btn-sm" style="display: none;">
                        <i class="fa-solid fa-trash"></i> Clear Order
                    </button>
                </div>
                <div class="card-body">
                    <!-- Display validation errors -->
                    <?php if (!empty($data['products_err'])): ?>
                        <div class="alert alert-danger">
                            <i class="fa-solid fa-exclamation-triangle"></i> <?php echo $data['products_err']; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo URLROOT; ?>/purchases/add" method="post" id="purchase-form"
                          data-redirect="<?php echo URLROOT; ?>/purchases">
                        <!-- Cart Items -->
                        <div class="table-responsive">
                            <table class="table table-sm" id="cart-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
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
                            <h6 class="text-muted">Order is empty</h6>
                            <p class="text-muted small">Click on products to add them to purchase order</p>
                        </div>

                        <!-- Cart Summary -->
                        <div id="cart-summary" style="display: none;">
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0">Total: </h4>
                                <h4 class="mb-0 text-success">₹<span id="total-amount">0.00</span></h4>
                            </div>
                            <input type="hidden" name="total_amount" id="total_amount_input">
                            
                            <!-- Inventory Costing Method -->
                            <div class="form-group">
                                <label class="font-weight-bold">
                                    <i class="fas fa-calculator"></i> Inventory Costing Method
                                </label>
                                <div class="card border-info">
                                    <div class="card-body py-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="average_price_method" name="average_price_method" value="1">
                                            <label class="custom-control-label" for="average_price_method">
                                                <strong>Average Price Method</strong>
                                                <small class="text-muted d-block">
                                                    Blend new purchase with existing Inventory at average price
                                                </small>
                                            </label>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-info">
                                                <i class="fas fa-info-circle"></i>
                                                <strong>Checked:</strong> Mix old and new Inventory at average price |
                                                <strong>Unchecked:</strong> Keep separate batches with different prices
                                            </small>
                                        </div>
                                        <div id="costing-example" class="mt-2" style="display: none;">
                                            <div class="alert alert-light py-2 mb-0">
                                                <small id="costing-explanation"></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Supplier Selection -->
                            <div class="form-group">
                                <label for="supplier_id">Supplier: <span class="text-danger">*</span></label>
                                <select name="supplier_id" id="supplier_id" class="form-control <?php echo (!empty($data['supplier_id_err'])) ? 'is-invalid' : ''; ?>" required>
                                    <option value="">Select Supplier</option>
                                    <?php foreach ($data['suppliers'] as $supplier): ?>
                                        <option value="<?php echo $supplier->supplier_id; ?>"
                                            <?php echo (isset($data['supplier_id']) && $data['supplier_id'] == $supplier->supplier_id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($supplier->supplier_name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="invalid-feedback"><?php echo $data['supplier_id_err'] ?? ''; ?></span>
                            </div>
                            
                            <button type="submit" class="btn btn-success btn-lg btn-block">
                                <i class="fa-solid fa-cart-plus"></i> Create Purchase Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize variables
    const productCards = document.querySelectorAll('.product-card');
    const cartTableBody = document.querySelector('#cart-table tbody');
    const totalAmountSpan = document.getElementById('total-amount');
    const totalAmountInput = document.getElementById('total_amount_input');
    const emptyCartMessage = document.getElementById('empty-cart-message');
    const cartSummary = document.getElementById('cart-summary');
    const clearCartBtn = document.getElementById('clear-cart');
    const productSearch = document.getElementById('searchProducts');
    const barcodeSearch = document.getElementById('barcodeSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const productItems = document.querySelectorAll('.product-item');
    const noProductsFound = document.getElementById('no-products-found');
    const clearSearchBtn = document.getElementById('clear-search');
    const showAllBtn = document.getElementById('show-all');
    const showLowInventoryBtn = document.getElementById('show-low-inventory');
    const showOutOfInventoryBtn = document.getElementById('show-out-of-inventory');
    const showRecentlyAddedBtn = document.getElementById('show-recently-added');
    const showHighValueBtn = document.getElementById('show-high-value');
    const sortPriceBtn = document.getElementById('sort-price');
    
    // Advanced filter elements
    const priceMinInput = document.getElementById('priceMinFilter');
    const priceMaxInput = document.getElementById('priceMaxFilter');
    const InventoryFilter = document.getElementById('InventoryLevelFilter');
    const statusFilter = document.getElementById('statusFilter');
    const filterSummary = document.getElementById('filterSummary');
    const activeFiltersSpan = document.getElementById('activeFilters');
    const clearAllFiltersBtn = document.getElementById('clearFilters');
    
    let cart = [];
    let currentFilter = 'all'; // 'all', 'low-inventory', 'out-of-inventory', 'recently-added', 'high-value'
    let currentSort = 'name'; // 'name', 'price-asc', 'price-desc'
    let activeFilters = {
        priceRange: false,
        InventoryLevel: false,
        status: false
    };

    // Helper function to check if date is within specified days
    function isWithinDays(dateString, days) {
        if (!dateString) return false;
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return diffDays <= days;
    }

    // Search and Filter Functionality
    function filterProducts() {
        const searchTerm = productSearch.value.toLowerCase();
        const barcodeSearchTerm = barcodeSearch.value.toLowerCase();
        const selectedCategory = categoryFilter.value.toLowerCase();
        const priceMin = parseFloat(priceMinInput.value) || 0;
        const priceMax = parseFloat(priceMaxInput.value) || Infinity;
        const InventoryLevel = InventoryFilter.value;
        const status = statusFilter.value;
        
        let visibleCount = 0;
        let appliedFilters = [];

        productItems.forEach(item => {
            const name = item.dataset.name;
            const sku = item.dataset.sku;
            const category = item.dataset.category;
            const productId = parseInt(item.dataset.id);
            const inventory = parseInt(item.dataset.inventory);
            const price = parseFloat(item.dataset.price);
            const createdDate = item.dataset.createdDate;
            
            // Basic search matches
            const matchesSearch = searchTerm === '' || 
                                name.includes(searchTerm) || 
                                sku.includes(searchTerm);
            
            const matchesBarcode = barcodeSearchTerm === '' || 
                                 sku.includes(barcodeSearchTerm) ||
                                 name.includes(barcodeSearchTerm);
            
            const matchesCategory = selectedCategory === '' || category === selectedCategory;
            
            // Price range filter
            const matchesPriceRange = price >= priceMin && price <= priceMax;
            
            // Inventory level filter
            let matchesInventoryLevel = true;
            if (InventoryLevel) {
                switch (InventoryLevel) {
                    case 'out-of-Inventory':
                        matchesInventoryLevel = inventory === 0;
                        break;
                    case 'critical':
                        matchesInventoryLevel = inventory >= 1 && inventory <= 5;
                        break;
                    case 'low':
                        matchesInventoryLevel = inventory >= 6 && inventory <= 20;
                        break;
                    case 'normal':
                        matchesInventoryLevel = inventory >= 21 && inventory <= 50;
                        break;
                    case 'high':
                        matchesInventoryLevel = inventory >= 51;
                        break;
                }
            }
            
            // Quick filter buttons
            let matchesQuickFilter = true;
            switch (currentFilter) {
                case 'all':
                    matchesQuickFilter = true;
                    break;
                case 'low-inventory':
                    matchesQuickFilter = inventory <= 5;
                    break;
                case 'out-of-inventory':
                    matchesQuickFilter = inventory === 0;
                    break;
                case 'recently-added':
                    // Check if product was created recently (last 30 days or high ID)
                    const isRecentByDate = createdDate && isWithinDays(createdDate, 30);
                    const isRecentById = productId > 50; // Fallback for products without created_at
                    matchesQuickFilter = isRecentByDate || isRecentById;
                    break;
                case 'high-value':
                    matchesQuickFilter = price >= 500; // Products over ₹500
                    break;
            }
            
            // Status filter (simulated logic - you can enhance based on your database)
            let matchesStatus = true;
            if (status) {
                switch (status) {
                    case 'needs-reorder':
                        matchesStatus = inventory <= 10; // Low inventory needs reorder
                        break;
                    case 'recently-updated':
                        matchesStatus = productId % 3 === 0; // Simulate some recently updated
                        break;
                    case 'never-ordered':
                        matchesStatus = inventory === 0; // Out of inventory = never ordered
                        break;
                    case 'frequently-ordered':
                        matchesStatus = inventory < 20 && inventory > 0; // Low but not zero = frequently ordered
                        break;
                }
            }
            
            // Final visibility check
            if (matchesSearch && matchesBarcode && matchesCategory && 
                matchesPriceRange && matchesInventoryLevel && matchesQuickFilter && matchesStatus) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Update filter summary
        updateFilterSummary();
        
        // Update results count
        const resultsCountEl = document.getElementById('resultsCount');
        if (resultsCountEl) {
            const totalProducts = document.querySelectorAll('.product-item').length;
            resultsCountEl.textContent = `Showing ${visibleCount} of ${totalProducts} products`;
        }
        
        // Show/hide no products found message
        noProductsFound.style.display = visibleCount === 0 ? 'block' : 'none';
    }
    
    // Update filter summary
    function updateFilterSummary() {
        let filters = [];
        
        if (productSearch.value) filters.push(`Search: "${productSearch.value}"`);
        if (barcodeSearch.value) filters.push(`Barcode: "${barcodeSearch.value}"`);
        if (categoryFilter.value) filters.push(`Category: ${categoryFilter.value}`);
        if (priceMinInput.value || priceMaxInput.value) {
            const min = priceMinInput.value || '0';
            const max = priceMaxInput.value || '∞';
            filters.push(`Price: ₹${min} - ₹${max}`);
        }
        if (InventoryFilter.value) filters.push(`Inventory: ${InventoryFilter.selectedOptions[0].text}`);
        if (statusFilter.value) filters.push(`Status: ${statusFilter.selectedOptions[0].text}`);
        if (currentFilter !== 'all') {
            const filterLabels = {
                'low-inventory': 'Low Inventory',
                'out-of-inventory': 'Out of Inventory', 
                'recently-added': 'Recently Added',
                'high-value': 'High Value'
            };
            if (filterLabels[currentFilter]) {
                filters.push(`Quick: ${filterLabels[currentFilter]}`);
            }
        }
        
        if (filters.length > 0) {
            activeFiltersSpan.textContent = filters.join(' | ');
            filterSummary.style.display = 'block';
        } else {
            filterSummary.style.display = 'none';
        }
    }

    // Barcode search with auto-add to cart
    function handleBarcodeSearch() {
        const barcode = barcodeSearch.value.trim().toLowerCase();
        if (barcode.length > 2) {
            // Find product by SKU/barcode
            const matchingCard = Array.from(productCards).find(card => {
                const sku = card.closest('.product-item').dataset.sku;
                return sku === barcode;
            });

            if (matchingCard) {
                // Auto-add to cart and clear barcode field
                matchingCard.click();
                barcodeSearch.value = '';
                // Highlight the product briefly
                matchingCard.style.border = '2px solid #28a745';
                setTimeout(() => {
                    matchingCard.style.border = '';
                }, 1000);
            }
        }
    }

    // Sort products
    function sortProducts() {
        const container = document.getElementById('products-container');
        const items = Array.from(productItems);
        
        items.sort((a, b) => {
            const cardA = a.querySelector('.product-card');
            const cardB = b.querySelector('.product-card');
            
            if (currentSort === 'price-asc') {
                return parseFloat(cardA.dataset.price) - parseFloat(cardB.dataset.price);
            } else if (currentSort === 'price-desc') {
                return parseFloat(cardB.dataset.price) - parseFloat(cardA.dataset.price);
            } else {
                return cardA.dataset.name.localeCompare(cardB.dataset.name);
            }
        });
        
        items.forEach(item => container.appendChild(item));
    }

    // Event listeners for search and filter
    productSearch.addEventListener('input', filterProducts);
    barcodeSearch.addEventListener('input', filterProducts);
    barcodeSearch.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            handleBarcodeSearch();
        }
    });
    categoryFilter.addEventListener('change', filterProducts);
    
    // Advanced filter event listeners
    priceMinInput.addEventListener('input', filterProducts);
    priceMaxInput.addEventListener('input', filterProducts);
    InventoryFilter.addEventListener('change', filterProducts);
    statusFilter.addEventListener('change', filterProducts);

    // Quick action buttons
    clearSearchBtn.addEventListener('click', () => {
        clearAllFilters();
    });
    
    function clearAllFilters() {
        productSearch.value = '';
        barcodeSearch.value = '';
        categoryFilter.value = '';
        priceMinInput.value = '';
        priceMaxInput.value = '';
        InventoryFilter.value = '';
        statusFilter.value = '';
        currentFilter = 'all';
        
        // Reset button states
        document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
        showAllBtn.classList.add('active');
        
        filterProducts();
    }
    
    clearAllFiltersBtn.addEventListener('click', clearAllFilters);

    showAllBtn.addEventListener('click', () => {
        currentFilter = 'all';
        updateActiveButton(showAllBtn);
        filterProducts();
    });

    showLowInventoryBtn.addEventListener('click', () => {
        currentFilter = 'low-inventory';
        updateActiveButton(showLowInventoryBtn);
        filterProducts();
    });
    
    showOutOfInventoryBtn.addEventListener('click', () => {
        currentFilter = 'out-of-inventory';
        updateActiveButton(showOutOfInventoryBtn);
        filterProducts();
    });
    
    showRecentlyAddedBtn.addEventListener('click', () => {
        currentFilter = 'recently-added';
        updateActiveButton(showRecentlyAddedBtn);
        filterProducts();
    });
    
    showHighValueBtn.addEventListener('click', () => {
        currentFilter = 'high-value';
        updateActiveButton(showHighValueBtn);
        filterProducts();
    });
    
    function updateActiveButton(activeBtn) {
        document.querySelectorAll('.btn-group .btn').forEach(btn => {
            if (btn !== sortPriceBtn) { // Don't affect sort button
                btn.classList.remove('active');
            }
        });
        activeBtn.classList.add('active');
    }
    
    // Sort button functionality
    sortPriceBtn.addEventListener('click', () => {
        if (currentSort === 'price-asc') {
            currentSort = 'price-desc';
            sortPriceBtn.innerHTML = '<i class="fa-solid fa-sort-down"></i> Price: High to Low';
            sortPriceBtn.classList.add('active');
        } else if (currentSort === 'price-desc') {
            currentSort = 'name';
            sortPriceBtn.innerHTML = '<i class="fa-solid fa-sort-alpha-down"></i> Name A-Z';
            sortPriceBtn.classList.remove('active');
        } else {
            currentSort = 'price-asc';
            sortPriceBtn.innerHTML = '<i class="fa-solid fa-sort-up"></i> Price: Low to High';
            sortPriceBtn.classList.add('active');
        }
        sortProducts();
        filterProducts(); // Update filter summary
    });
    
    // Enhanced sort function
    function sortProducts() {
        const container = document.getElementById('products-container');
        const items = Array.from(productItems);
        
        items.sort((a, b) => {
            const cardA = a.querySelector('.product-card');
            const cardB = b.querySelector('.product-card');
            
            if (currentSort === 'price-asc') {
                return parseFloat(cardA.dataset.price) - parseFloat(cardB.dataset.price);
            } else if (currentSort === 'price-desc') {
                return parseFloat(cardB.dataset.price) - parseFloat(cardA.dataset.price);
            } else if (currentSort === 'Inventory-asc') {
                return parseInt(cardA.dataset.Inventory) - parseInt(cardB.dataset.Inventory);
            } else if (currentSort === 'Inventory-desc') {
                return parseInt(cardB.dataset.Inventory) - parseInt(cardA.dataset.Inventory);
            } else {
                return cardA.dataset.name.localeCompare(cardB.dataset.name);
            }
        });
        
        items.forEach(item => container.appendChild(item));
    }

    sortPriceBtn.addEventListener('click', () => {
        if (currentSort === 'price-asc') {
            currentSort = 'price-desc';
            sortPriceBtn.innerHTML = '<i class="fa-solid fa-sort-down"></i> Price: High to Low';
        } else {
            currentSort = 'price-asc';
            sortPriceBtn.innerHTML = '<i class="fa-solid fa-sort-up"></i> Price: Low to High';
        }
        sortProducts();
    });

    // Product card click handlers - Add to purchase order
    productCards.forEach(card => {
        card.addEventListener('click', () => {
            const productId = card.dataset.id;
            const productName = card.dataset.name;
            const productPrice = parseFloat(card.dataset.price);
            const productInventory = parseInt(card.dataset.inventory);

            const existingProduct = cart.find(item => item.id === productId);

            if (existingProduct) {
                // For purchase orders, we can order more than current Inventory
                existingProduct.quantity++;
            } else {
                cart.push({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    quantity: 1,
                    inventory: productInventory
                });
            }
            updateCart();
            
            // Visual feedback
            card.style.transform = 'scale(0.95)';
            setTimeout(() => {
                card.style.transform = 'scale(1)';
            }, 150);
        });
    });

    // Clear cart functionality
    clearCartBtn.addEventListener('click', () => {
        if (confirm('Are you sure you want to clear the purchase order?')) {
            cart = [];
            updateCart();
        }
    });

    function updateCart() {
        cartTableBody.innerHTML = '';
        let total = 0;

        if (cart.length === 0) {
            emptyCartMessage.style.display = 'block';
            cartSummary.style.display = 'none';
            clearCartBtn.style.display = 'none';
        } else {
            emptyCartMessage.style.display = 'none';
            cartSummary.style.display = 'block';
            clearCartBtn.style.display = 'inline-block';

            cart.forEach((item, index) => {
                const itemTotal = item.quantity * item.price;
                total += itemTotal;
                
                const row = `
                    <tr>
                        <td>
                            <small class="font-weight-bold">${item.name}</small>
                            <br><small class="text-muted">Inventory: ${item.inventory}</small>
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm quantity-input" 
                                   data-index="${index}" value="${item.quantity}" min="1" max="9999" 
                                   style="width: 70px;">
                        </td>
                        <td><small>₹${item.price.toFixed(2)}</small></td>
                        <td><small class="font-weight-bold">₹${itemTotal.toFixed(2)}</small></td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-item" data-index="${index}">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `;
                cartTableBody.innerHTML += row;

                // Add hidden inputs for form submission
                cartTableBody.innerHTML += `
                    <input type="hidden" name="products[${index}][id]" value="${item.id}">
                    <input type="hidden" name="products[${index}][quantity]" value="${item.quantity}">
                    <input type="hidden" name="products[${index}][price]" value="${item.price}">
                `;
            });
        }

        totalAmountSpan.textContent = total.toFixed(2);
        totalAmountInput.value = total.toFixed(2);

        // Add event listeners for quantity inputs
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', (e) => {
                const index = e.target.dataset.index;
                const newQuantity = parseInt(e.target.value);
                
                if (newQuantity > 0) {
                    cart[index].quantity = newQuantity;
                } else {
                    cart[index].quantity = 1;
                    e.target.value = 1;
                }
                updateCart();
            });
        });

        // Add event listeners for remove buttons
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', (e) => {
                const index = e.target.closest('button').dataset.index;
                cart.splice(index, 1);
                updateCart();
            });
        });
    }

    // Initialize cart display
    updateCart();
    
    // Set initial button states
    showAllBtn.classList.add('active');
    
    // Initialize product count
    const totalProducts = document.querySelectorAll('.product-item').length;
    const resultsCountEl = document.getElementById('resultsCount');
    if (resultsCountEl) {
        resultsCountEl.textContent = `Showing ${totalProducts} products`;
    }
    
    // Costing Method Functionality
    const averagePriceCheckbox = document.getElementById('average_price_method');
    const costingExample = document.getElementById('costing-example');
    const costingExplanation = document.getElementById('costing-explanation');
    
    // Show example when checkbox changes
    averagePriceCheckbox.addEventListener('change', function() {
        showCostingExample();
    });
    
    // Also show example when products are added to cart
    function showCostingExample() {
        if (cart.length === 0) {
            costingExample.style.display = 'none';
            return;
        }
        
        // Find a product example from cart
        const sampleProduct = cart[0];
        const currentInventory = 100; // Example current Inventory
        const currentPrice = 100; // Example current price ₹100
        const newPrice = sampleProduct.price;
        const newQuantity = sampleProduct.quantity;
        
        let exampleText = '';
        
        if (averagePriceCheckbox.checked) {
            // Average price method
            const totalValue = (currentInventory * currentPrice) + (newQuantity * newPrice);
            const totalQuantity = currentInventory + newQuantity;
            const averagePrice = totalValue / totalQuantity;
            
            exampleText = `<strong>Average Price Example:</strong><br>
                Current Inventory: ${currentInventory} units @ ₹${currentPrice} = ₹${(currentInventory * currentPrice).toLocaleString('en-IN')}<br>
                New Purchase: ${newQuantity} units @ ₹${newPrice} = ₹${(newQuantity * newPrice).toLocaleString('en-IN')}<br>
                <strong>Result:</strong> ${totalQuantity} units @ ₹${averagePrice.toFixed(2)} = ₹${totalValue.toLocaleString('en-IN')}`;
        } else {
            // Separate batches method
            exampleText = `<strong>Separate Batches Example:</strong><br>
                Batch-1: ${currentInventory} units @ ₹${currentPrice} = ₹${(currentInventory * currentPrice).toLocaleString('en-IN')}<br>
                Batch-2: ${newQuantity} units @ ₹${newPrice} = ₹${(newQuantity * newPrice).toLocaleString('en-IN')}<br>
                <strong>Total Value:</strong> ₹${(currentInventory * currentPrice + newQuantity * newPrice).toLocaleString('en-IN')} (2 separate batches)`;
        }
        
        costingExplanation.innerHTML = exampleText;
        costingExample.style.display = 'block';
    }
    
    // Enhanced addToCart function to update examples
    const originalAddToCart = window.addToCart || function() {};
    window.addToCart = function() {
        originalAddToCart.apply(this, arguments);
        setTimeout(showCostingExample, 100); // Slight delay to ensure cart is updated
    };

    // Purchase order confirmation handler
    let formSubmissionHandled = false;
    
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.id === 'purchase-form' && !formSubmissionHandled) {
            e.preventDefault();
            formSubmissionHandled = true;
            
            // First popup: Confirmation
            const confirmed = confirm('Are you sure you want to create this purchase order?');
            
            if (confirmed) {
                const submitBtn = form.querySelector('button[type="submit"]');
                
                // Show loading state
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
                }
                
                setTimeout(() => {
                    // Second popup: Success message
                    alert('✅ Purchase order has been created successfully!');
                    
                    // Submit the form after success message
                    setTimeout(() => {
                        form.submit();
                    }, 200);
                }, 800);
            } else {
                // Reset if user cancels
                formSubmissionHandled = false;
            }
        }
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
