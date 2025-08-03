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
                    <!-- Search and Filter Section -->
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
                                <button type="button" id="show-in-stock" class="btn btn-outline-success">
                                    <i class="fa-solid fa-check"></i> In Stock Only
                                </button>
                                <button type="button" id="sort-price" class="btn btn-outline-info">
                                    <i class="fa-solid fa-sort"></i> Sort by Price
                                </button>
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
                                         data-stock="<?php echo $product->current_stock ?? 0; ?>">
                                        <div class="card-body text-center">
                                            <h6 class="card-title mb-2"><?php echo htmlspecialchars($product->product_name); ?></h6>
                                            <p class="text-muted small mb-1">
                                                SKU: <?php echo htmlspecialchars($product->sku ?? 'N/A'); ?>
                                            </p>
                                            <p class="text-muted small mb-2">
                                                <?php echo htmlspecialchars($product->category_name ?? 'Uncategorized'); ?>
                                            </p>
                                            <div class="mb-2">
                                                <span class="badge <?php echo ($product->current_stock > 0) ? 'badge-success' : 'badge-danger'; ?>">
                                                    Stock: <?php echo $product->current_stock ?? 0; ?>
                                                </span>
                                            </div>
                                            <h5 class="text-primary mb-2">$<?php echo number_format($product->unit_price, 2); ?></h5>
                                            <button class="btn btn-success btn-sm w-100" 
                                                    <?php echo ($product->current_stock <= 0) ? 'disabled' : ''; ?>>
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
                    <form action="<?php echo URLROOT; ?>/sales/add" method="post">
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
    const showInStockBtn = document.getElementById('show-in-stock');
    const sortPriceBtn = document.getElementById('sort-price');
    
    let cart = [];
    let currentFilter = 'all'; // 'all', 'in-stock'
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
            const stock = parseInt(productCard.dataset.stock);
            
            const matchesSearch = searchTerm === '' || 
                                name.includes(searchTerm) || 
                                sku.includes(searchTerm);
            
            const matchesBarcode = barcodeSearchTerm === '' || 
                                 sku.includes(barcodeSearchTerm) ||
                                 name.includes(barcodeSearchTerm);
            
            const matchesCategory = selectedCategory === '' || category === selectedCategory;
            
            const matchesStockFilter = currentFilter === 'all' || 
                                     (currentFilter === 'in-stock' && stock > 0);
            
            if (matchesSearch && matchesBarcode && matchesCategory && matchesStockFilter) {
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
        showInStockBtn.classList.remove('active');
        filterProducts();
    });

    showInStockBtn.addEventListener('click', () => {
        currentFilter = 'in-stock';
        showInStockBtn.classList.add('active');
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
            const productStock = parseInt(card.dataset.stock);

            if (productStock <= 0) {
                alert('This product is out of stock!');
                return;
            }

            const existingProduct = cart.find(item => item.id === productId);

            if (existingProduct) {
                if (existingProduct.quantity < productStock) {
                    existingProduct.quantity++;
                } else {
                    alert(`Cannot add more items. Only ${productStock} units available in stock.`);
                    return;
                }
            } else {
                cart.push({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    quantity: 1,
                    stock: productStock,
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
                const itemTotal = (item.quantity * item.price) - (item.discount || 0);
                total += itemTotal;
                
                const row = `
                    <tr>
                        <td>
                            <small class="font-weight-bold">${item.name}</small>
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm quantity-input" 
                                   data-index="${index}" value="${item.quantity}" min="1" max="${item.stock}" 
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
                const maxStock = cart[index].stock;
                
                if (newQuantity > 0 && newQuantity <= maxStock) {
                    cart[index].quantity = newQuantity;
                } else if (newQuantity > maxStock) {
                    alert(`Maximum available quantity is ${maxStock}`);
                    e.target.value = maxStock;
                    cart[index].quantity = maxStock;
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
</script>

            </div> <!-- End container-fluid -->
        </div> <!-- End page-content-wrapper -->
    </div> <!-- End wrapper -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
    <script src="<?php echo URLROOT; ?>/js/main.js"></script>
</body>
</html>