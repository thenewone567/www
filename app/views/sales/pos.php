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
            <!-- Barcode Scanner Section -->
            <div class="theme-card mb-4">
                <div class="card-header bg-primary-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-barcode"></i> Barcode Scanner</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="fa-solid fa-barcode"></i></span>
                                <input type="text" id="barcode-input" class="form-control"
                                    placeholder="Scan or type barcode here..." autofocus>
                                <button class="btn btn-primary" type="button" onclick="scanBarcode()">
                                    <i class="fa-solid fa-search"></i> Scan
                                </button>
                            </div>
                            <small class="text-muted">Press Enter after scanning or typing barcode</small>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-secondary btn-lg w-100" onclick="showProductSearch()">
                                <i class="fa-solid fa-list"></i> Browse Products
                            </button>
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
                        <?php foreach ($data['customers'] as $customer): ?>
                            <option value="<?php echo $customer->customer_id; ?>">
                                <?php echo htmlspecialchars($customer->name); ?>
                            </option>
                        <?php endforeach; ?>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                            <?php foreach ($data['products'] as $product): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($product->product_name); ?></strong></td>
                                    <td><?php echo htmlspecialchars($product->sku ?? 'N/A'); ?></td>
                                    <td><span
                                            class="badge badge-info"><?php echo $product->Inventory_quantity ?? 0; ?></span>
                                    </td>
                                    <td><?php echo formatCurrency($product->sale_price ?? 0, 2); ?></td>
                                    <td>
                                        <button class="btn btn-primary btn-sm"
                                            onclick="addProductToCart(<?php echo $product->product_id; ?>, '<?php echo addslashes($product->product_name); ?>', <?php echo $product->sale_price ?? 0; ?>)">
                                            <i class="fa-solid fa-plus"></i> Add
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = [];
    let cartTotal = 0;

    // Barcode scanning
    document.getElementById('barcode-input').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            scanBarcode();
        }
    });

    function scanBarcode() {
        const barcode = document.getElementById('barcode-input').value.trim();
        if (!barcode) {
            alert('Please enter a barcode');
            return;
        }

        fetch('<?php echo URLROOT; ?>/sales/scan_barcode', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'barcode=' + encodeURIComponent(barcode)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addProductToCart(data.product.id, data.product.name, data.product.price, data.product.Inventory);
                    document.getElementById('barcode-input').value = '';
                    document.getElementById('barcode-input').focus();
                } else {
                    alert('Product not found: ' + data.message);
                    document.getElementById('barcode-input').select();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error scanning barcode');
            });
    }

    function addProductToCart(productId, productName, price, Inventory = 999) {
        const existingItem = cart.find(item => item.id === productId);

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                id: productId,
                name: productName,
                price: parseFloat(price),
                quantity: 1,
                Inventory: Inventory
            });
        }

        updateCartDisplay();
        updateOrderSummary();
    }

    function updateCartDisplay() {
        const cartBody = document.getElementById('cart-items');
        const emptyCart = document.getElementById('empty-cart');

        if (cart.length === 0) {
            emptyCart.style.display = 'table-row';
            return;
        }

        emptyCart.style.display = 'none';

        let html = '';
        cart.forEach((item, index) => {
            const subtotal = item.price * item.quantity;
            html += `
            <tr>
                <td><strong>${item.name}</strong></td>
                <td>$${item.price.toFixed(2)}</td>
                <td>
                    <div class="input-group input-group-sm">
                        <button class="btn btn-outline-secondary" onclick="updateQuantity(${index}, -1)">-</button>
                        <input type="number" class="form-control text-center" value="${item.quantity}" 
                               onchange="setQuantity(${index}, this.value)" min="1">
                        <button class="btn btn-outline-secondary" onclick="updateQuantity(${index}, 1)">+</button>
                    </div>
                </td>
                <td>$${subtotal.toFixed(2)}</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="removeFromCart(${index})">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        });

        cartBody.innerHTML = html;
    }

    function updateQuantity(index, change) {
        cart[index].quantity += change;
        if (cart[index].quantity <= 0) {
            cart.splice(index, 1);
        }
        updateCartDisplay();
        updateOrderSummary();
    }

    function setQuantity(index, quantity) {
        const qty = parseInt(quantity);
        if (qty > 0) {
            cart[index].quantity = qty;
        } else {
            cart.splice(index, 1);
        }
        updateCartDisplay();
        updateOrderSummary();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCartDisplay();
        updateOrderSummary();
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

        document.getElementById('total-items').textContent = totalItems;
        document.getElementById('subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('tax').textContent = tax.toFixed(2);
        document.getElementById('total').textContent = total.toFixed(2);

        cartTotal = total;

        // Enable/disable pay button
        const payButton = document.getElementById('pay-button');
        payButton.disabled = cart.length === 0;

        // Update change calculation
        calculateChange();
    }

    // Calculate change
    document.getElementById('amount-received').addEventListener('input', calculateChange);

    function calculateChange() {
        const amountReceived = parseFloat(document.getElementById('amount-received').value) || 0;
        const change = Math.max(0, amountReceived - cartTotal);
        document.getElementById('change').textContent = change.toFixed(2);
    }

    function showProductSearch() {
        const modal = new bootstrap.Modal(document.getElementById('productSearchModal'));
        modal.show();
    }

    function processPayment() {
        if (cart.length === 0) {
            alert('Cart is empty');
            return;
        }

        const customerId = document.getElementById('customer-select').value;
        const paymentMethod = document.getElementById('payment-method').value;
        const amountReceived = parseFloat(document.getElementById('amount-received').value) || 0;

        if (paymentMethod === 'cash' && amountReceived < cartTotal) {
            alert('Insufficient payment amount');
            return;
        }

        // Here you would process the payment
        alert('Payment processing would be implemented here');

        // Clear cart after successful payment
        cart = [];
        updateCartDisplay();
        updateOrderSummary();
        document.getElementById('amount-received').value = '';
        document.getElementById('barcode-input').focus();
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
        document.getElementById('barcode-input').focus();
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>