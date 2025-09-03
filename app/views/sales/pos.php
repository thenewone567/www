<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container theme-unified mt-0 pt-3">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <a href="<?php echo URLROOT; ?>/sales" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Sales
            </a>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h2 class="mb-0"><i class="fa-solid fa-cash-register"></i> <?php echo $data['title']; ?></h2>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php flash('pos_message'); ?>

    <div class="row">
        <!-- Left Panel - Product Entry & Cart -->
        <div class="col-md-8">
            <!-- Unified Product Search Section -->
            <div class="theme-card mb-4">
                <div class="card-header bg-primary-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-search"></i> Product Search</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                                <input type="text" id="unified-search-input" class="form-control"
                                    placeholder="Search by product name, SKU, or scan barcode..." autofocus>
                                <button class="btn btn-primary" type="button" onclick="performUnifiedSearch()">
                                    <i class="fa-solid fa-plus"></i> Add
                                </button>
                            </div>
                            <small class="text-muted">Type product name, SKU, or scan barcode - Press Enter to add to
                                cart</small>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-secondary btn-lg w-100" onclick="showProductSearch()">
                                <i class="fa-solid fa-list"></i> Browse All Products
                            </button>
                        </div>
                    </div>

                    <!-- Live Search Results -->
                    <div id="search-results-dropdown" class="mt-3" style="display: none;">
                        <div class="list-group shadow-sm">
                            <!-- Search results will be populated here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shopping Cart -->
            <div class="theme-card">
                <div class="card-header bg-success-theme text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa-solid fa-shopping-cart"></i> Shopping Cart</h5>
                    <button class="btn btn-outline-light btn-sm" onclick="clearCart()">
                        <i class="fa-solid fa-trash"></i> Clear Cart
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="cart-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th width="100">Qty</th>
                                    <th>Subtotal</th>
                                    <th width="50">Action</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items">
                                <tr id="empty-cart" class="text-center">
                                    <td colspan="5" class="text-muted py-4">
                                        <i class="fa-solid fa-shopping-cart fa-2x mb-2"></i><br>
                                        Cart is empty. Scan a barcode to add items.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Customer & Payment -->
        <div class="col-md-4">
            <!-- Customer Selection -->
            <div class="theme-card mb-4">
                <div class="card-header bg-info-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-user"></i> Customer</h5>
                </div>
                <div class="card-body">
                    <select id="customer-select" class="form-control form-control-lg">
                        <option value="">Walk-in Customer</option>
                        <?php if (isset($data['customers']) && is_array($data['customers'])): ?>
                            <?php foreach ($data['customers'] as $customer): ?>
                                <option value="<?php echo $customer->customer_id; ?>">
                                    <?php echo htmlspecialchars($customer->name); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <button class="btn btn-outline-info btn-sm mt-2 w-100">
                        <i class="fa-solid fa-user-plus"></i> Add New Customer
                    </button>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="theme-card mb-4">
                <div class="card-header bg-warning-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-calculator"></i> Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6"><strong>Items:</strong></div>
                        <div class="col-6 text-right"><span id="total-items">0</span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6"><strong>Subtotal:</strong></div>
                        <div class="col-6 text-right">$<span id="subtotal">0.00</span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">Tax (0%):</div>
                        <div class="col-6 text-right">$<span id="tax">0.00</span></div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-6">
                            <h5><strong>Total:</strong></h5>
                        </div>
                        <div class="col-6 text-right">
                            <h5><strong>$<span id="total">0.00</span></strong></h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="theme-card">
                <div class="card-header bg-dark-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-credit-card"></i> Payment</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label>Payment Method:</label>
                        <select id="payment-method" class="form-control">
                            <option value="cash">Cash</option>
                            <option value="card">Credit/Debit Card</option>
                            <option value="check">Check</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label>Amount Received:</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" id="amount-received" class="form-control" step="0.01"
                                placeholder="0.00">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6"><strong>Change:</strong></div>
                        <div class="col-6 text-right">$<span id="change">0.00</span></div>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-success btn-lg" onclick="processPayment()" disabled id="pay-button">
                            <i class="fa-solid fa-money-bill"></i> Process Payment
                        </button>
                        <button class="btn btn-outline-secondary" onclick="holdTransaction()">
                            <i class="fa-solid fa-pause"></i> Hold Transaction
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Search Modal -->
<div class="modal fade" id="productSearchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-search"></i> Product Search</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <input type="text" id="product-search" class="form-control"
                        placeholder="Search products by name, SKU, or barcode...">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Inventory</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="product-search-results">
                            <?php if (isset($data['products']) && is_array($data['products'])): ?>
                                <?php foreach ($data['products'] as $product): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($product->product_name); ?></strong></td>
                                        <td><?php echo htmlspecialchars($product->sku ?? 'N/A'); ?></td>
                                        <td><span
                                                class="badge badge-info"><?php echo $product->Inventory_quantity ?? 0; ?></span>
                                        </td>
                                        <td><?php echo formatCurrency($product->selling_price ?? 0, 2); ?></td>
                                        <td>
                                            <button class="btn btn-primary btn-sm"
                                                onclick="addProductToCart(<?php echo $product->product_id; ?>, '<?php echo addslashes($product->product_name); ?>', <?php echo $product->selling_price ?? 0; ?>, <?php echo $product->current_inventory ?? $product->Inventory_quantity ?? 0; ?>)">
                                                <i class="fa-solid fa-plus"></i> Add
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fa-solid fa-box-open fa-2x mb-2"></i><br>
                                        No products available
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #search-results-dropdown {
        position: relative;
        z-index: 1000;
    }

    #search-results-dropdown .list-group {
        max-height: 300px;
        overflow-y: auto;
        border-radius: 0.375rem;
    }

    #search-results-dropdown .list-group-item {
        border-left: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6;
    }

    #search-results-dropdown .list-group-item:first-child {
        border-top: 1px solid #dee2e6;
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
    }

    #search-results-dropdown .list-group-item:last-child {
        border-bottom: 1px solid #dee2e6;
        border-bottom-left-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
    }

    #search-results-dropdown .list-group-item:hover {
        background-color: #f8f9fa;
    }
</style>

<script>
    // Initialize cart system
    let cart = [];
    let cartTotal = 0;

    // Initialize cart display on page load
    document.addEventListener('DOMContentLoaded', function () {
        updateCartDisplay();
        updateOrderSummary();
        console.log('POS system initialized');
    });

    // Unified search functionality
    const unifiedSearchInput = document.getElementById('unified-search-input');
    if (unifiedSearchInput) {
        // Enter key to add product
        unifiedSearchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                performUnifiedSearch();
            }
        });

        // Live search as user types
        unifiedSearchInput.addEventListener('input', function () {
            performLiveSearch();
        });
    } else {
        console.error('Unified search input not found');
    }

    // Product search functionality
    const productSearchInput = document.getElementById('product-search');
    if (productSearchInput) {
        productSearchInput.addEventListener('input', function () {
            filterProductSearch();
        });
    } else {
        console.error('Product search input not found');
    }

    function filterProductSearch() {
        const searchTerm = document.getElementById('product-search').value.toLowerCase().trim();
        const tableRows = document.querySelectorAll('#product-search-results tr');

        console.log('Filtering products with search term:', searchTerm);
        console.log('Found table rows:', tableRows.length);

        let visibleCount = 0;

        tableRows.forEach((row, index) => {
            if (!row.querySelector('td')) {
                console.log('Skipping row', index, '- no td elements');
                return; // Skip empty rows
            }

            const productName = row.querySelector('td:first-child')?.textContent.toLowerCase() || '';
            const sku = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';

            console.log('Row', index, '- Product:', productName, 'SKU:', sku);

            // Search in product name and SKU
            const matches = productName.includes(searchTerm) || sku.includes(searchTerm);

            if (!searchTerm || matches) {
                row.style.display = '';
                visibleCount++;
                console.log('Row', index, '- SHOWING');
            } else {
                row.style.display = 'none';
                console.log('Row', index, '- HIDING');
            }
        });

        console.log('Visible products after filter:', visibleCount);

        // Show "no results" message if no products match
        let noResultsRow = document.getElementById('no-search-results');
        if (visibleCount === 0 && searchTerm) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'no-search-results';
                noResultsRow.innerHTML = '<td colspan="5" class="text-center text-muted py-4"><i class="fa-solid fa-search fa-2x mb-2"></i><br>No products found matching "' + searchTerm + '"</td>';
                document.getElementById('product-search-results').appendChild(noResultsRow);
            } else {
                noResultsRow.querySelector('td').innerHTML = '<i class="fa-solid fa-search fa-2x mb-2"></i><br>No products found matching "' + searchTerm + '"';
                noResultsRow.style.display = '';
            }
        } else if (noResultsRow) {
            noResultsRow.style.display = 'none';
        }
    }

    // Unified search functions
    function performUnifiedSearch() {
        const searchTerm = document.getElementById('unified-search-input').value.trim();
        if (!searchTerm) {
            alert('Please enter a product name, SKU, or barcode');
            return;
        }

        // Hide search dropdown
        document.getElementById('search-results-dropdown').style.display = 'none';

        // First try barcode scan
        fetch('<?php echo URLROOT; ?>/sales/scan_barcode', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'barcode=' + encodeURIComponent(searchTerm)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Found by barcode
                    addProductFromSearchResult(data.product);
                    clearUnifiedSearch();
                } else {
                    // Try searching by name/SKU
                    searchProductByName(searchTerm);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Fallback to name search
                searchProductByName(searchTerm);
            });
    }

    function searchProductByName(searchTerm) {
        // Search through available products (client-side search)
        const products = <?php echo json_encode(isset($data['products']) && is_array($data['products']) ? $data['products'] : []); ?>;
        const matches = products.filter(product =>
            product.product_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
            (product.sku && product.sku.toLowerCase().includes(searchTerm.toLowerCase()))
        );

        if (matches.length === 1) {
            // Exact match - add directly
            addProductFromSearchResult({
                id: matches[0].product_id,
                name: matches[0].product_name,
                price: matches[0].selling_price,
                inventory: matches[0].inventory_quantity || matches[0].Inventory_quantity || 0
            });
            clearUnifiedSearch();
        } else if (matches.length > 1) {
            // Multiple matches - show dropdown
            showSearchResults(matches);
        } else {
            alert('Product not found: "' + searchTerm + '"');
            document.getElementById('unified-search-input').select();
        }
    }

    function showSearchResults(products) {
        const dropdown = document.getElementById('search-results-dropdown');
        const listGroup = dropdown.querySelector('.list-group');

        listGroup.innerHTML = '';

        products.slice(0, 5).forEach(product => { // Show max 5 results
            const item = document.createElement('a');
            item.className = 'list-group-item list-group-item-action';
            item.style.cursor = 'pointer';
            item.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${product.product_name}</strong><br>
                        <small class="text-muted">SKU: ${product.sku || 'N/A'}</small>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-info">${product.inventory_quantity || product.Inventory_quantity || 0} in stock</span><br>
                        <strong>₹${parseFloat(product.selling_price || 0).toFixed(2)}</strong>
                    </div>
                </div>
            `;

            item.addEventListener('click', function () {
                addProductFromSearchResult({
                    id: product.product_id,
                    name: product.product_name,
                    price: product.selling_price,
                    inventory: product.inventory_quantity || product.Inventory_quantity || 0
                });
                clearUnifiedSearch();
            });

            listGroup.appendChild(item);
        });

        dropdown.style.display = 'block';
    }

    function performLiveSearch() {
        const searchTerm = document.getElementById('unified-search-input').value.trim();

        if (searchTerm.length < 2) {
            document.getElementById('search-results-dropdown').style.display = 'none';
            return;
        }

        // Search through available products
        const products = <?php echo json_encode(isset($data['products']) && is_array($data['products']) ? $data['products'] : []); ?>;
        const matches = products.filter(product =>
            product.product_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
            (product.sku && product.sku.toLowerCase().includes(searchTerm.toLowerCase()))
        );

        if (matches.length > 0) {
            showSearchResults(matches);
        } else {
            document.getElementById('search-results-dropdown').style.display = 'none';
        }
    }

    function addProductFromSearchResult(productData) {
        let price = productData.price;
        if (typeof price === 'string') {
            price = parseFloat(price.replace(/[$,]/g, ''));
        } else {
            price = parseFloat(price);
        }

        addProductToCart(
            productData.id,
            productData.name,
            price,
            productData.inventory || 999
        );
    }

    function clearUnifiedSearch() {
        document.getElementById('unified-search-input').value = '';
        document.getElementById('search-results-dropdown').style.display = 'none';
        document.getElementById('unified-search-input').focus();
    }

    function addProductToCart(productId, productName, price, inventory = 999) {
        // Ensure proper data types
        productId = parseInt(productId);
        price = parseFloat(price) || 0;
        inventory = parseInt(inventory) || 999;

        // Validate inputs
        if (!productId || !productName || price <= 0) {
            console.error('Invalid product data:', { productId, productName, price });
            alert('Invalid product data');
            return;
        }

        // Find existing item in cart
        const existingItemIndex = cart.findIndex(item => parseInt(item.id) === productId);

        if (existingItemIndex !== -1) {
            // Product already exists, increase quantity
            cart[existingItemIndex].quantity += 1;
            console.log('Increased quantity for existing product:', cart[existingItemIndex]);
        } else {
            // Add new product to cart
            const newItem = {
                id: productId,
                name: productName,
                price: price,
                quantity: 1,
                inventory: inventory
            };
            cart.push(newItem);
            console.log('Added new product to cart:', newItem);
        }

        updateCartDisplay();
        updateOrderSummary();

        console.log('Current cart state:', cart);
    }

    function updateCartDisplay() {
        const cartBody = document.getElementById('cart-items');
        const emptyCart = document.getElementById('empty-cart');

        if (!cartBody) {
            console.error('Cart body element not found');
            return;
        }

        if (cart.length === 0) {
            if (emptyCart) {
                emptyCart.style.display = 'table-row';
            }
            cartBody.innerHTML = '<tr id="empty-cart" class="text-center"><td colspan="5" class="text-muted py-4"><i class="fa-solid fa-shopping-cart fa-2x mb-2"></i><br>Cart is empty. Scan a barcode to add items.</td></tr>';
            return;
        }

        if (emptyCart) {
            emptyCart.style.display = 'none';
        }

        let html = '';
        cart.forEach((item, index) => {
            const subtotal = (item.price * item.quantity);
            html += `
            <tr data-product-id="${item.id}">
                <td><strong>${escapeHtml(item.name)}</strong></td>
                <td>$${item.price.toFixed(2)}</td>
                <td>
                    <div class="input-group input-group-sm" style="width: 120px;">
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="updateQuantity(${index}, -1)">-</button>
                        <input type="number" class="form-control form-control-sm text-center" value="${item.quantity}" 
                               onchange="setQuantity(${index}, this.value)" min="1" style="width: 50px;">
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="updateQuantity(${index}, 1)">+</button>
                    </div>
                </td>
                <td><strong>$${subtotal.toFixed(2)}</strong></td>
                <td>
                    <button class="btn btn-danger btn-sm" type="button" onclick="removeFromCart(${index})">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>`;
        });

        cartBody.innerHTML = html;
        console.log('Cart display updated, items:', cart.length);
    }

    // Helper function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function updateQuantity(index, change) {
        if (index < 0 || index >= cart.length) {
            console.error('Invalid cart index:', index);
            return;
        }

        cart[index].quantity += change;

        if (cart[index].quantity <= 0) {
            cart.splice(index, 1);
        }

        updateCartDisplay();
        updateOrderSummary();
        console.log('Quantity updated, cart:', cart);
    }

    function setQuantity(index, quantity) {
        if (index < 0 || index >= cart.length) {
            console.error('Invalid cart index:', index);
            return;
        }

        const qty = parseInt(quantity);
        if (qty > 0) {
            cart[index].quantity = qty;
        } else {
            cart.splice(index, 1);
        }

        updateCartDisplay();
        updateOrderSummary();
        console.log('Quantity set, cart:', cart);
    }

    function removeFromCart(index) {
        if (index < 0 || index >= cart.length) {
            console.error('Invalid cart index:', index);
            return;
        }

        const removedItem = cart.splice(index, 1)[0];
        updateCartDisplay();
        updateOrderSummary();
        console.log('Removed item from cart:', removedItem);
    }

    function clearCart() {
        if (confirm('Are you sure you want to clear the cart?')) {
            cart = [];
            updateCartDisplay();
            updateOrderSummary();
        }
    }

    function updateOrderSummary() {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const tax = 0; // No tax for now
        const total = subtotal + tax;

        // Update display elements
        const totalItemsEl = document.getElementById('total-items');
        const subtotalEl = document.getElementById('subtotal');
        const taxEl = document.getElementById('tax');
        const totalEl = document.getElementById('total');

        if (totalItemsEl) totalItemsEl.textContent = totalItems;
        if (subtotalEl) subtotalEl.textContent = subtotal.toFixed(2);
        if (taxEl) taxEl.textContent = tax.toFixed(2);
        if (totalEl) totalEl.textContent = total.toFixed(2);

        cartTotal = total;

        // Enable/disable pay button
        const payButton = document.getElementById('pay-button');
        if (payButton) {
            payButton.disabled = cart.length === 0;
        }

        // Update change calculation
        calculateChange();

        console.log('Order summary updated:', { totalItems, subtotal, total });
    }

    // Calculate change
    document.getElementById('amount-received').addEventListener('input', calculateChange);

    function calculateChange() {
        const amountReceived = parseFloat(document.getElementById('amount-received').value) || 0;
        const change = Math.max(0, amountReceived - cartTotal);
        document.getElementById('change').textContent = change.toFixed(2);
    }

    function showProductSearch() {
        $('#productSearchModal').modal('show');
    }

    function processPayment() {
        if (cart.length === 0) {
            alert('Cart is empty');
            return;
        }

        const customerId = document.getElementById('customer-select').value || null;
        const paymentMethod = document.getElementById('payment-method').value;
        const amountReceived = parseFloat(document.getElementById('amount-received').value) || 0;

        if (paymentMethod === 'cash' && amountReceived < cartTotal) {
            alert('Insufficient payment amount');
            return;
        }

        // Disable the pay button to prevent double submission
        const payButton = document.getElementById('pay-button');
        payButton.disabled = true;
        payButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';

        // Prepare sale data
        const saleData = {
            customer_id: customerId,
            payment_method: paymentMethod,
            amount_received: amountReceived,
            cart_items: cart,
            total_amount: cartTotal
        };

        // Send to server
        fetch('<?php echo URLROOT; ?>/sales/process_sale', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(saleData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Sale processed successfully!\nSale ID: ${data.sale_id}\nChange: $${data.change.toFixed(2)}`);

                    // Clear cart after successful payment
                    cart = [];
                    updateCartDisplay();
                    updateOrderSummary();
                    document.getElementById('amount-received').value = '';
                    document.getElementById('customer-select').value = '';
                    document.getElementById('unified-search-input').focus();
                } else {
                    alert('Error processing sale: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error processing payment');
            })
            .finally(() => {
                // Re-enable the pay button
                payButton.disabled = false;
                payButton.innerHTML = '<i class="fa-solid fa-money-bill"></i> Process Payment';
            });
    }

    function holdTransaction() {
        if (cart.length === 0) {
            alert('Cart is empty');
            return;
        }

        // Save transaction for later
        alert('Hold transaction functionality would be implemented here');
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('unified-search-input').focus();
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>