<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid mt-0 pt-3">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <a href="<?php echo URLROOT; ?>/sales" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Sales
            </a>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h2 class="mb-0"><i class="fa-solid fa-cash-register"></i> New Sale</h2>
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
                    <!-- Enhanced Search and Scanner Section -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <!-- Unified Smart Search & Scanner -->
                            <div class="unified-search-container">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" 
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                    id="searchTypeDropdown">
                                                <i class="fas fa-search"></i> <span id="searchTypeText">All Items</span>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item search-type-option active" href="#" data-search-type="all">
                                                    <i class="fas fa-search mr-2"></i>All Items
                                                </a>
                                                <a class="dropdown-item search-type-option" href="#" data-search-type="barcode">
                                                    <i class="fas fa-barcode mr-2"></i>Barcode
                                                </a>
                                                <a class="dropdown-item search-type-option" href="#" data-search-type="sku">
                                                    <i class="fas fa-tag mr-2"></i>SKU
                                                </a>
                                                <a class="dropdown-item search-type-option" href="#" data-search-type="name">
                                                    <i class="fas fa-box mr-2"></i>Product Name
                                                </a>
                                                <a class="dropdown-item search-type-option" href="#" data-search-type="category">
                                                    <i class="fas fa-folder mr-2"></i>Category
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control" id="unifiedSalesSearch" 
                                           placeholder="🔍 Search by product name, SKU, barcode... or scan barcode" 
                                           autocomplete="off">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" onclick="toggleSalesScanner()" 
                                                id="salesScanBtn" title="Toggle Barcode Scanner">
                                            <i class="fas fa-barcode"></i>
                                        </button>
                                        <button class="btn btn-info" type="button" onclick="openCustomerScanner()" 
                                                id="customerScanBtn" title="Scan Customer Card">
                                            <i class="fas fa-id-card"></i>
                                        </button>
                                        <button class="btn btn-success" type="button" onclick="performSalesSearch()" 
                                                id="salesSearchBtn">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Scanner Interface -->
                                <div id="salesScannerContainer" class="mt-3" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="scanner-preview-container">
                                                <video id="salesScannerVideo" class="scanner-video" autoplay muted></video>
                                                <div class="scanner-overlay">
                                                    <div class="scanner-line"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="scanner-controls">
                                                <h6>Scanner Controls</h6>
                                                
                                                <!-- Scanner Mode Selection -->
                                                <div class="mb-3">
                                                    <label class="form-label small">Scanner Mode:</label>
                                                    <div class="btn-group btn-group-sm w-100" role="group">
                                                        <button class="btn btn-outline-primary scanner-mode-btn active" 
                                                                data-mode="product" onclick="setSalesMode('product')">
                                                            <i class="fas fa-box"></i> Product
                                                        </button>
                                                        <button class="btn btn-outline-info scanner-mode-btn" 
                                                                data-mode="customer" onclick="setSalesMode('customer')">
                                                            <i class="fas fa-user"></i> Customer
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <div class="btn-group-vertical btn-group-sm w-100 mb-2">
                                                    <button class="btn btn-primary" id="startSalesScanner">
                                                        <i class="fas fa-play"></i> Start Scanner
                                                    </button>
                                                    <button class="btn btn-outline-secondary" id="stopSalesScanner" disabled>
                                                        <i class="fas fa-stop"></i> Stop Scanner
                                                    </button>
                                                    <button class="btn btn-outline-primary" id="switchSalesCamera" disabled>
                                                        <i class="fas fa-sync"></i> Switch Camera
                                                    </button>
                                                    <button class="btn btn-outline-success" id="salesManualEntry">
                                                        <i class="fas fa-keyboard"></i> Manual Entry
                                                    </button>
                                                </div>
                                                
                                                <div class="scanner-status">
                                                    <small class="text-muted">Status: <span id="salesScannerStatus">Scanner ready</span></small>
                                                </div>
                                                
                                                <!-- Recent Scans -->
                                                <div class="mt-3">
                                                    <h6 class="small">Recent Scans</h6>
                                                    <div id="salesScanHistory" class="scan-history" style="max-height: 150px; overflow-y: auto;">
                                                        <small class="text-muted">No scans yet</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Search Results -->
                                <div id="salesSearchResults" class="mt-3" style="display: none;">
                                    <div class="search-results-header d-flex justify-content-between align-items-center">
                                        <h6>Search Results <span id="salesResultsCount" class="badge badge-primary">0</span></h6>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="clearSalesSearchResults()">
                                            <i class="fas fa-times"></i> Clear
                                        </button>
                                    </div>
                                    <div id="salesSearchList" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Filter Actions -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" id="clear-search" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-times"></i> Clear Search
                                </button>
                                <button type="button" id="show-all" class="btn btn-outline-primary">
                                    <i class="fa-solid fa-list"></i> Show All
                                </button>
                                <button type="button" id="show-in-Inventory" class="btn btn-outline-success">
                                    <i class="fa-solid fa-check"></i> In Inventory Only
                                </button>
                                <button type="button" id="sort-price" class="btn btn-outline-info">
                                    <i class="fa-solid fa-sort"></i> Sort by Price
                                </button>
                                <button type="button" id="quick-add-mode" class="btn btn-outline-warning">
                                    <i class="fa-solid fa-bolt"></i> Quick Add Mode
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Legacy Search (Hidden by default, shown when needed) -->
                    <div class="legacy-search-container" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fa-solid fa-search"></i>
                                        </span>
                                    </div>
                                    <input type="text" id="product-search" class="form-control" 
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
                                    <input type="text" id="barcode-search" class="form-control" 
                                           placeholder="Scan/Enter barcode">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select id="category-filter" class="form-control">
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
                    </div>

                    <!-- Products Grid -->
                    <div id="product-list">
                        <div class="row" id="products-container">
                            <?php foreach ($data['products'] as $product): ?>
                                <div class="col-md-4 col-sm-6 mb-3 product-item" 
                                     data-name="<?php echo strtolower($product->product_name); ?>"
                                     data-sku="<?php echo strtolower($product->sku ?? ''); ?>"
                                     data-category="<?php echo strtolower($product->category_name ?? ''); ?>">
                                    <div class="card product-card h-100" 
                                         data-id="<?php echo $product->product_id; ?>"
                                         data-name="<?php echo htmlspecialchars($product->product_name); ?>"
                                         data-price="<?php echo $product->unit_price; ?>"
                                         data-inventory="<?php echo $product->current_inventory ?? 0; ?>">
                                        <div class="card-body text-center">
                                            <h6 class="card-title mb-2"><?php echo htmlspecialchars($product->product_name); ?></h6>
                                            <p class="text-muted small mb-1">
                                                SKU: <?php echo htmlspecialchars($product->sku ?? 'N/A'); ?>
                                            </p>
                                            <p class="text-muted small mb-2">
                                                <?php echo htmlspecialchars($product->category_name ?? 'Uncategorized'); ?>
                                            </p>
                                            <div class="mb-2">
                                                <span class="badge <?php echo ($product->current_inventory > 0) ? 'badge-success' : 'badge-danger'; ?>">
                                                    Inventory: <?php echo $product->current_inventory ?? 0; ?>
                                                </span>
                                            </div>
                                            <h5 class="text-primary mb-2"><?php echo formatCurrency($product->unit_price, 2); ?></h5>
                                            <button class="btn btn-success btn-sm w-100" 
                                                    <?php echo ($product->current_inventory <= 0) ? 'disabled' : ''; ?>>
                                                <i class="fa-solid fa-plus"></i> Add to Cart
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
        
        <!-- Cart Section -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa-solid fa-shopping-cart"></i> Shopping Cart</h5>
                    <button type="button" id="clear-cart" class="btn btn-outline-danger btn-sm" style="display: none;">
                        <i class="fa-solid fa-trash"></i> Clear Cart
                    </button>
                </div>
                <div class="card-body">
                    <form action="<?php echo URLROOT; ?>/sales/add" method="post" data-verify="sale" data-verify-redirect="<?php echo URLROOT; ?>/sales">
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
                            <h6 class="text-muted">Cart is empty</h6>
                            <p class="text-muted small">Click on products to add them to cart</p>
                        </div>

                        <!-- Cart Summary -->
                        <div id="cart-summary" style="display: none;">
                            <!-- Customer Display -->
                            <div id="customer-display" class="mb-3 p-3 rounded" style="display: none; background-color: #e8f5e8; border: 1px solid #d4edda;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><i class="fa-solid fa-user text-success"></i> Customer:</strong>
                                        <span id="selected-customer-name" class="text-success font-weight-bold">None Selected</span>
                                    </div>
                                    <button type="button" id="clear-customer" class="btn btn-sm btn-outline-secondary" title="Clear Customer">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0">Total: </h4>
                                <h4 class="mb-0 text-success">$<span id="total-amount">0.00</span></h4>
                            </div>
                            <input type="hidden" name="total_amount" id="total_amount_input">
                            
                            <!-- Customer and Payment Info -->
                            <div class="form-group">
                                <label for="customer_id">Customer:</label>
                                <select name="customer_id" class="form-control">
                                    <option value="">Select Customer (Optional)</option>
                                    <?php foreach ($data['customers'] as $customer): ?>
                                        <option value="<?php echo $customer->customer_id; ?>"
                                            <?php echo ($data['customer_id'] == $customer->customer_id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($customer->customer_name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="invalid-feedback"><?php echo $data['customer_id_err']; ?></span>
                            </div>
                            <div class="form-group">
                                <label for="payment_mode">Payment Mode:</label>
                                <select name="payment_mode" class="form-control" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="Cash">Cash</option>
                                    <option value="UPI">UPI</option>
                                    <option value="Card">Debit/Credit Card</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg btn-block">
                                <i class="fa-solid fa-credit-card"></i> Process Sale
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
    const productSearch = document.getElementById('product-search');
    const barcodeSearch = document.getElementById('barcode-search');
    const categoryFilter = document.getElementById('category-filter');
    const productItems = document.querySelectorAll('.product-item');
    const noProductsFound = document.getElementById('no-products-found');
    const clearSearchBtn = document.getElementById('clear-search');
    const showAllBtn = document.getElementById('show-all');
    const showInInventoryBtn = document.getElementById('show-in-Inventory');
    const sortPriceBtn = document.getElementById('sort-price');
    const customerSelect = document.querySelector('select[name="customer_id"]');
    const customerDisplay = document.getElementById('customer-display');
    const selectedCustomerName = document.getElementById('selected-customer-name');
    const clearCustomerBtn = document.getElementById('clear-customer');
    
    let cart = [];
    let currentFilter = 'all'; // 'all', 'in-Inventory'
    let currentSort = 'name'; // 'name', 'price-asc', 'price-desc'

    // Search and Filter Functionality
    function filterProducts() {
        const searchTerm = productSearch.value.toLowerCase();
        const barcodeSearchTerm = barcodeSearch.value.toLowerCase();
        const selectedCategory = categoryFilter.value.toLowerCase();
        let visibleCount = 0;

        productItems.forEach(item => {
            const name = item.dataset.name;
            const sku = item.dataset.sku;
            const category = item.dataset.category;
            const productCard = item.querySelector('.product-card');
            const inventory = parseInt(productCard.dataset.inventory);
            
            const matchesSearch = searchTerm === '' || 
                                name.includes(searchTerm) || 
                                sku.includes(searchTerm);
            
            const matchesBarcode = barcodeSearchTerm === '' || 
                                 sku.includes(barcodeSearchTerm) ||
                                 name.includes(barcodeSearchTerm);
            
            const matchesCategory = selectedCategory === '' || category === selectedCategory;
            
            const matchesInventoryFilter = currentFilter === 'all' || 
                                     (currentFilter === 'in-Inventory' && inventory > 0);
            
            if (matchesSearch && matchesBarcode && matchesCategory && matchesInventoryFilter) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Show/hide no products found message
        noProductsFound.style.display = visibleCount === 0 ? 'block' : 'none';
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

    // Quick action buttons
    clearSearchBtn.addEventListener('click', () => {
        productSearch.value = '';
        barcodeSearch.value = '';
        categoryFilter.value = '';
        currentFilter = 'all';
        filterProducts();
    });

    showAllBtn.addEventListener('click', () => {
        currentFilter = 'all';
        showAllBtn.classList.add('active');
        showInInventoryBtn.classList.remove('active');
        filterProducts();
    });

    showInInventoryBtn.addEventListener('click', () => {
        currentFilter = 'in-Inventory';
        showInInventoryBtn.classList.add('active');
        showAllBtn.classList.remove('active');
        filterProducts();
    });

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

    // Product card click handlers
    productCards.forEach(card => {
        card.addEventListener('click', () => {
            const productId = card.dataset.id;
            const productName = card.dataset.name;
            const productPrice = parseFloat(card.dataset.price);
            const productInventory = parseInt(card.dataset.inventory);

            if (productInventory <= 0) {
                alert('This product is out of Inventory!');
                return;
            }

            const existingProduct = cart.find(item => item.id === productId);

            if (existingProduct) {
                if (existingProduct.quantity < productInventory) {
                    existingProduct.quantity++;
                } else {
                    alert(`Cannot add more items. Only ${productInventory} units available in Inventory.`);
                    return;
                }
            } else {
                cart.push({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    quantity: 1,
                    inventory: productInventory,
                    discount: 0
                });
            }
            updateCart();
        });
    });

    // Clear cart functionality
    clearCartBtn.addEventListener('click', () => {
        if (confirm('Are you sure you want to clear the cart?')) {
            cart = [];
            updateCart();
        }
    });

    // Customer selection functionality
    customerSelect.addEventListener('change', () => {
        const selectedOption = customerSelect.options[customerSelect.selectedIndex];
        if (customerSelect.value) {
            selectedCustomerName.textContent = selectedOption.text;
            customerDisplay.style.display = 'block';
        } else {
            selectedCustomerName.textContent = 'None Selected';
            customerDisplay.style.display = 'none';
        }
    });

    // Clear customer functionality
    clearCustomerBtn.addEventListener('click', () => {
        customerSelect.value = '';
        selectedCustomerName.textContent = 'None Selected';
        customerDisplay.style.display = 'none';
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

            // Show customer display if customer is selected
            if (customerSelect.value) {
                customerDisplay.style.display = 'block';
            }

            cart.forEach((item, index) => {
                const itemTotal = (item.quantity * item.price) - (item.discount || 0);
                total += itemTotal;
                
                const row = `
                    <tr>
                        <td>
                            <small class="font-weight-bold">${item.name}</small>
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm quantity-input" 
                                   data-index="${index}" value="${item.quantity}" min="1" max="${item.inventory}" 
                                   style="width: 60px;">
                        </td>
                        <td><small>$${item.price.toFixed(2)}</small></td>
                        <td><small class="font-weight-bold">$${itemTotal.toFixed(2)}</small></td>
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
                    <input type="hidden" name="products[${index}][discount]" value="${item.discount || 0}">
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
                const maxInventory = cart[index].inventory;
                
                if (newQuantity > 0 && newQuantity <= maxInventory) {
                    cart[index].quantity = newQuantity;
                } else if (newQuantity > maxInventory) {
                    alert(`Maximum available quantity is ${maxInventory}`);
                    e.target.value = maxInventory;
                    cart[index].quantity = maxInventory;
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

    // ==================== ENHANCED SALES SEARCH & SCANNER ====================
    
    // Initialize Enhanced Sales Manager
    let salesManager = null;
    let quickAddMode = false;
    
    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeSalesEnhancements();
    });
    
    function initializeSalesEnhancements() {
        try {
            // Initialize scanner if libraries are available
            if (window.EnhancedBarcodeScanner) {
                salesManager = new SalesManager();
                console.log('Enhanced Sales Manager initialized');
            } else {
                console.warn('Enhanced Scanner not available, using fallback');
                initializeFallbackSearch();
            }
        } catch (error) {
            console.error('Failed to initialize sales enhancements:', error);
            initializeFallbackSearch();
        }
    }
    
    // Sales Manager Class
    class SalesManager {
        constructor() {
            this.scanner = null;
            this.currentMode = 'product'; // 'product' or 'customer'
            this.searchType = 'all';
            this.scanHistory = [];
            this.init();
        }
        
        init() {
            this.initBarcodeScanner();
            this.initEventListeners();
        }
        
        initBarcodeScanner() {
            try {
                this.scanner = new EnhancedBarcodeScanner({
                    videoElement: document.getElementById('salesScannerVideo'),
                    onScan: (result) => this.handleBarcodeScanned(result),
                    onError: (message, error) => this.handleScannerError(message, error),
                    onStatusChange: (status) => this.updateScannerStatus(status),
                    autoStop: false,
                    scanCooldown: 1000
                });
            } catch (error) {
                console.error('Failed to initialize sales scanner:', error);
            }
        }
        
        initEventListeners() {
            // Search type dropdown
            document.querySelectorAll('.search-type-option').forEach(option => {
                option.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.setSearchType(e.target.dataset.searchType);
                });
            });
            
            // Unified search input
            const searchInput = document.getElementById('unifiedSalesSearch');
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    this.handleUnifiedSearch(e.target.value);
                });
                
                searchInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.performSearch(e.target.value);
                    }
                });
            }
            
            // Scanner controls
            document.getElementById('startSalesScanner')?.addEventListener('click', () => this.startScanning());
            document.getElementById('stopSalesScanner')?.addEventListener('click', () => this.stopScanning());
            document.getElementById('switchSalesCamera')?.addEventListener('click', () => this.switchCamera());
            document.getElementById('salesManualEntry')?.addEventListener('click', () => this.showManualEntry());
        }
        
        async handleBarcodeScanned(result) {
            console.log('Sales barcode scanned:', result);
            
            // Add to scan history
            this.scanHistory.unshift({
                ...result,
                timestamp: new Date(),
                mode: this.currentMode
            });
            
            // Keep only last 10 scans
            if (this.scanHistory.length > 10) {
                this.scanHistory = this.scanHistory.slice(0, 10);
            }
            
            // Update search input
            const searchInput = document.getElementById('unifiedSalesSearch');
            if (searchInput) {
                searchInput.value = result.value;
            }
            
            // Process based on current mode
            switch (this.currentMode) {
                case 'product':
                    await this.handleProductScan(result.value);
                    break;
                case 'customer':
                    await this.handleCustomerScan(result.value);
                    break;
            }
            
            // Update scan history display
            this.updateScanHistoryDisplay();
            
            // Visual feedback
            this.showScanSuccess(result.value);
        }
        
        async handleProductScan(barcode) {
            try {
                // First try to search in current products
                const product = this.findProductByBarcode(barcode);
                
                if (product) {
                    // Add directly to cart if in quick add mode
                    if (quickAddMode) {
                        this.addToCartByBarcode(product);
                    } else {
                        this.highlightProduct(product.id);
                    }
                } else {
                    // Search via API
                    await this.searchProductByBarcode(barcode);
                }
            } catch (error) {
                console.error('Product scan error:', error);
                this.showAlert('Product scan failed', 'error');
            }
        }
        
        async handleCustomerScan(barcode) {
            try {
                // Search for customer by barcode/ID
                const response = await fetch(`${window.URLROOT}/sales/search-customer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ barcode: barcode })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.selectCustomer(data.customer);
                } else {
                    this.showAlert('Customer not found', 'warning');
                }
            } catch (error) {
                console.error('Customer scan error:', error);
                this.showAlert('Customer scan failed', 'error');
            }
        }
        
        findProductByBarcode(barcode) {
            // Search in current product list
            const productCards = document.querySelectorAll('.product-card');
            for (let card of productCards) {
                const productData = this.extractProductData(card);
                if (productData.sku === barcode || productData.barcode === barcode) {
                    return productData;
                }
            }
            return null;
        }
        
        extractProductData(card) {
            return {
                id: card.dataset.id,
                name: card.dataset.name,
                price: parseFloat(card.dataset.price),
                inventory: parseInt(card.dataset.inventory),
                sku: card.querySelector('.text-muted')?.textContent.replace('SKU: ', '') || '',
                barcode: card.dataset.barcode || ''
            };
        }
        
        addToCartByBarcode(product) {
            // Add product to cart directly
            const existingProduct = cart.find(item => item.id === product.id);
            
            if (existingProduct) {
                if (existingProduct.quantity < product.inventory) {
                    existingProduct.quantity++;
                    this.showAlert(`Added ${product.name} to cart (Qty: ${existingProduct.quantity})`, 'success');
                } else {
                    this.showAlert(`Cannot add more ${product.name}. Only ${product.inventory} units available.`, 'warning');
                }
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    quantity: 1,
                    inventory: product.inventory,
                    discount: 0
                });
                this.showAlert(`Added ${product.name} to cart`, 'success');
            }
            
            updateCart();
        }
        
        highlightProduct(productId) {
            // Remove previous highlights
            document.querySelectorAll('.product-card').forEach(card => {
                card.classList.remove('highlight-product');
            });
            
            // Highlight the found product
            const productCard = document.querySelector(`[data-id="${productId}"]`);
            if (productCard) {
                productCard.classList.add('highlight-product');
                productCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Remove highlight after 3 seconds
                setTimeout(() => {
                    productCard.classList.remove('highlight-product');
                }, 3000);
            }
        }
        
        selectCustomer(customer) {
            const customerSelect = document.querySelector('select[name="customer_id"]');
            if (customerSelect) {
                customerSelect.value = customer.id;
                this.showAlert(`Customer selected: ${customer.name}`, 'success');
            }
        }
        
        setSearchType(type) {
            this.searchType = type;
            
            // Update dropdown text
            const searchTypeText = document.getElementById('searchTypeText');
            const selectedOption = document.querySelector(`[data-search-type="${type}"]`);
            if (searchTypeText && selectedOption) {
                searchTypeText.innerHTML = selectedOption.innerHTML;
            }
            
            // Update active state
            document.querySelectorAll('.search-type-option').forEach(option => {
                option.classList.toggle('active', option.dataset.searchType === type);
            });
            
            // Update placeholder
            const searchInput = document.getElementById('unifiedSalesSearch');
            if (searchInput) {
                const placeholders = {
                    all: '🔍 Search by product name, SKU, barcode...',
                    barcode: '📦 Scan or enter barcode',
                    sku: '🏷️ Enter product SKU',
                    name: '📦 Enter product name',
                    category: '📁 Enter category name'
                };
                searchInput.placeholder = placeholders[type] || placeholders.all;
            }
        }
        
        handleUnifiedSearch(query) {
            if (query.length < 2) {
                this.clearSearchResults();
                return;
            }
            
            // Debounce search
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.performSearch(query);
            }, 300);
        }
        
        async performSearch(query) {
            if (!query.trim()) return;
            
            try {
                this.showLoading(true);
                
                const response = await fetch(`${window.URLROOT}/sales/search-products`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ 
                        query: query.trim(),
                        type: this.searchType
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.displaySearchResults(data.products);
                } else {
                    this.showAlert(data.message || 'No products found', 'info');
                    this.clearSearchResults();
                }
            } catch (error) {
                console.error('Search error:', error);
                this.showAlert('Search failed. Please try again.', 'error');
            } finally {
                this.showLoading(false);
            }
        }
        
        displaySearchResults(products) {
            const container = document.getElementById('salesSearchResults');
            const listContainer = document.getElementById('salesSearchList');
            const countBadge = document.getElementById('salesResultsCount');
            
            if (!container || !listContainer) return;
            
            if (products.length === 0) {
                container.style.display = 'none';
                return;
            }
            
            countBadge.textContent = products.length;
            container.style.display = 'block';
            
            const html = products.map(product => `
                <div class="search-result-item p-3 border rounded mb-2" data-product-id="${product.id}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${this.escapeHtml(product.name)}</h6>
                            <small class="text-muted">
                                SKU: ${product.sku || 'N/A'} | 
                                Category: ${product.category || 'N/A'} |
                                Inventory: ${product.inventory || 0}
                            </small>
                            <div class="mt-1">
                                <span class="h6 text-success">$${parseFloat(product.price || 0).toFixed(2)}</span>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-success add-to-cart-btn" 
                                    data-product='${JSON.stringify(product)}'
                                    ${product.inventory <= 0 ? 'disabled' : ''}>
                                <i class="fas fa-plus"></i> Add to Cart
                            </button>
                            <button class="btn btn-sm btn-outline-secondary show-in-grid-btn" 
                                    data-product-id="${product.id}">
                                <i class="fas fa-eye"></i> Show
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
            
            listContainer.innerHTML = html;
            
            // Add event listeners for result actions
            listContainer.querySelectorAll('.add-to-cart-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const productData = JSON.parse(e.target.dataset.product);
                    this.addProductToCart(productData);
                });
            });
            
            listContainer.querySelectorAll('.show-in-grid-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const productId = e.target.dataset.productId;
                    this.highlightProduct(productId);
                    this.clearSearchResults();
                });
            });
        }
        
        addProductToCart(product) {
            if (product.inventory <= 0) {
                this.showAlert('This product is out of inventory!', 'warning');
                return;
            }
            
            const existingProduct = cart.find(item => item.id == product.id);
            
            if (existingProduct) {
                if (existingProduct.quantity < product.inventory) {
                    existingProduct.quantity++;
                    this.showAlert(`Added ${product.name} to cart (Qty: ${existingProduct.quantity})`, 'success');
                } else {
                    this.showAlert(`Cannot add more items. Only ${product.inventory} units available.`, 'warning');
                }
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: parseFloat(product.price),
                    quantity: 1,
                    inventory: parseInt(product.inventory),
                    discount: 0
                });
                this.showAlert(`Added ${product.name} to cart`, 'success');
            }
            
            updateCart();
        }
        
        clearSearchResults() {
            const container = document.getElementById('salesSearchResults');
            if (container) {
                container.style.display = 'none';
            }
        }
        
        updateScanHistoryDisplay() {
            const container = document.getElementById('salesScanHistory');
            if (!container) return;
            
            if (this.scanHistory.length === 0) {
                container.innerHTML = '<small class="text-muted">No scans yet</small>';
                return;
            }
            
            const html = this.scanHistory.slice(0, 5).map(scan => `
                <div class="scan-history-item p-2 border-bottom">
                    <div class="scan-value text-primary">${this.escapeHtml(scan.value)}</div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">${scan.format}</small>
                        <small class="text-muted">${this.formatTime(scan.timestamp)}</small>
                    </div>
                </div>
            `).join('');
            
            container.innerHTML = html;
        }
        
        // Scanner control methods
        async startScanning() {
            if (!this.scanner) {
                this.showAlert('Scanner not available', 'error');
                return;
            }
            
            try {
                await this.scanner.startScanning();
                this.updateScannerUI(true);
            } catch (error) {
                console.error('Failed to start scanner:', error);
                this.showAlert('Failed to start scanner. Check camera permissions.', 'error');
            }
        }
        
        stopScanning() {
            if (this.scanner) {
                this.scanner.stopScanning();
                this.updateScannerUI(false);
            }
        }
        
        async switchCamera() {
            if (this.scanner) {
                await this.scanner.switchCamera();
            }
        }
        
        showManualEntry() {
            const barcode = prompt('Enter barcode manually:');
            if (barcode && barcode.trim()) {
                this.handleBarcodeScanned({
                    value: barcode.trim(),
                    format: 'manual'
                });
            }
        }
        
        updateScannerUI(isScanning) {
            const startBtn = document.getElementById('startSalesScanner');
            const stopBtn = document.getElementById('stopSalesScanner');
            const switchBtn = document.getElementById('switchSalesCamera');
            
            if (startBtn) startBtn.disabled = isScanning;
            if (stopBtn) stopBtn.disabled = !isScanning;
            if (switchBtn) switchBtn.disabled = !isScanning;
        }
        
        updateScannerStatus(status) {
            const statusElement = document.getElementById('salesScannerStatus');
            if (statusElement) {
                statusElement.textContent = status;
            }
        }
        
        showScanSuccess(barcode) {
            // Visual feedback for successful scan
            const notification = document.createElement('div');
            notification.className = 'scan-success-notification position-fixed';
            notification.style.cssText = `
                top: 20px; right: 20px; z-index: 9999;
                background: linear-gradient(45deg, #28a745, #20c997);
                color: white; padding: 1rem 1.5rem; border-radius: 0.5rem;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                animation: slideInRight 0.3s ease-out;
            `;
            notification.innerHTML = `
                <i class="fas fa-check-circle mr-2"></i>
                Scanned: ${this.escapeHtml(barcode)}
            `;
            
            document.body.appendChild(notification);
            
            // Auto-remove after 2 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 2000);
        }
        
        showAlert(message, type = 'info') {
            // Simple alert system - can be enhanced with toast notifications
            const alertClass = {
                success: 'alert-success',
                error: 'alert-danger',
                warning: 'alert-warning',
                info: 'alert-info'
            }[type] || 'alert-info';
            
            // For now, use a simple alert - can be replaced with toast system
            console.log(`${type.toUpperCase()}: ${message}`);
            
            // You can implement a toast notification system here
            if (type === 'error' || type === 'warning') {
                alert(message);
            }
        }
        
        showLoading(show) {
            const btn = document.getElementById('salesSearchBtn');
            if (btn) {
                if (show) {
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
                    btn.disabled = true;
                } else {
                    btn.innerHTML = '<i class="fas fa-search"></i> Search';
                    btn.disabled = false;
                }
            }
        }
        
        escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, (m) => map[m]);
        }
        
        formatTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleTimeString('en-US', { 
                hour12: false, 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        }
    }
    
    // Global functions for UI integration
    window.toggleSalesScanner = function() {
        const container = document.getElementById('salesScannerContainer');
        const button = document.getElementById('salesScanBtn');
        
        if (container.style.display === 'none') {
            container.style.display = 'block';
            button.innerHTML = '<i class="fas fa-barcode"></i> Hide';
            button.classList.remove('btn-primary');
            button.classList.add('btn-secondary');
        } else {
            container.style.display = 'none';
            button.innerHTML = '<i class="fas fa-barcode"></i>';
            button.classList.remove('btn-secondary');
            button.classList.add('btn-primary');
            
            // Stop scanner when hiding
            if (salesManager && salesManager.scanner) {
                salesManager.stopScanning();
            }
        }
    };
    
    window.openCustomerScanner = function() {
        if (salesManager) {
            salesManager.currentMode = 'customer';
            document.querySelectorAll('.scanner-mode-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.mode === 'customer');
            });
            
            // Show scanner if not visible
            const container = document.getElementById('salesScannerContainer');
            if (container.style.display === 'none') {
                toggleSalesScanner();
            }
        }
    };
    
    window.setSalesMode = function(mode) {
        if (salesManager) {
            salesManager.currentMode = mode;
            
            // Update button states
            document.querySelectorAll('.scanner-mode-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.mode === mode);
            });
        }
    };
    
    window.performSalesSearch = function() {
        const searchInput = document.getElementById('unifiedSalesSearch');
        if (searchInput && searchInput.value.trim()) {
            if (salesManager) {
                salesManager.performSearch(searchInput.value.trim());
            }
        }
    };
    
    window.clearSalesSearchResults = function() {
        const searchInput = document.getElementById('unifiedSalesSearch');
        if (searchInput) {
            searchInput.value = '';
        }
        
        if (salesManager) {
            salesManager.clearSearchResults();
        }
    };
    
    // Toggle quick add mode
    document.getElementById('quick-add-mode')?.addEventListener('click', function() {
        quickAddMode = !quickAddMode;
        this.classList.toggle('btn-warning', !quickAddMode);
        this.classList.toggle('btn-success', quickAddMode);
        
        const icon = this.querySelector('i');
        const text = quickAddMode ? 'Quick Add: ON' : 'Quick Add Mode';
        this.innerHTML = `<i class="fas fa-bolt"></i> ${text}`;
        
        if (quickAddMode) {
            alert('Quick Add Mode: ON\nScanned products will be added directly to cart!');
        }
    });
    
    // Fallback search functionality for when enhanced scanner is not available
    function initializeFallbackSearch() {
        const productSearch = document.getElementById('product-search');
        const barcodeSearch = document.getElementById('barcode-search');
        
        if (productSearch) {
            productSearch.addEventListener('input', function() {
                filterProducts();
            });
        }
        
        if (barcodeSearch) {
            barcodeSearch.addEventListener('input', function() {
                const barcode = this.value.trim();
                if (barcode.length > 3) {
                    searchProductsByBarcode(barcode);
                }
            });
            
            barcodeSearch.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const barcode = this.value.trim();
                    if (barcode) {
                        searchProductsByBarcode(barcode);
                    }
                }
            });
        }
    }
    
    function searchProductsByBarcode(barcode) {
        // Simple barcode search in current products
        const productCards = document.querySelectorAll('.product-card');
        let found = false;
        
        productCards.forEach(card => {
            const sku = card.querySelector('.text-muted')?.textContent.replace('SKU: ', '') || '';
            if (sku.toLowerCase().includes(barcode.toLowerCase())) {
                card.style.display = 'block';
                card.classList.add('highlight-product');
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                found = true;
                
                // Remove highlight after 3 seconds
                setTimeout(() => {
                    card.classList.remove('highlight-product');
                }, 3000);
            } else {
                card.style.display = 'none';
            }
        });
        
        if (!found) {
            alert('Product not found with barcode: ' + barcode);
            // Show all products again
            productCards.forEach(card => {
                card.style.display = 'block';
            });
        }
    }
</script>

<!-- Include Enhanced Scanner Libraries -->
<script src="<?php echo URLROOT; ?>/public/js/enhanced-barcode-scanner.js"></script>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/enhanced-receiving.css">

<!-- Additional CSS for Sales Scanner -->
<style>
    .unified-search-container {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .search-result-item {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .search-result-item:hover {
        background-color: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .highlight-product {
        animation: highlightPulse 1s ease-in-out;
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.5) !important;
    }
    
    @keyframes highlightPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    
    .scanner-preview-container {
        position: relative;
        background: #000;
        border-radius: 0.375rem;
        overflow: hidden;
        height: 240px;
    }
    
    .scanner-video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .scanner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .scanner-line {
        width: 200px;
        height: 2px;
        background: linear-gradient(90deg, transparent, #ff0000, transparent);
        animation: scannerLine 2s ease-in-out infinite;
    }
    
    @keyframes scannerLine {
        0%, 100% { transform: translateX(-50px); opacity: 0; }
        50% { transform: translateX(50px); opacity: 1; }
    }
    
    .scan-history {
        max-height: 150px;
        overflow-y: auto;
        border: 1px solid #e9ecef;
        border-radius: 0.25rem;
        background: #fff;
    }
    
    .scan-history-item {
        border-bottom: 1px solid #e9ecef;
    }
    
    .scan-history-item:last-child {
        border-bottom: none;
    }
    
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
</style>
</script>

            </div> <!-- End container-fluid -->
        </div> <!-- End page-content-wrapper -->
    </div> <!-- End wrapper -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
    <script src="<?php echo URLROOT; ?>/public/js/main.js"></script>
</body>
</html>