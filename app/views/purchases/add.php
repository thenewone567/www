<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid mt-0 pt-3">
    <!-- Enhanced Header with Quick Actions -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-lg-4 mb-2 mb-lg-0">
            <div class="d-flex align-items-center">
                <a href="<?php echo URLROOT; ?>/purchases" class="btn btn-secondary mr-3">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
                <div>
                    <h2 class="mb-0"><i class="fa-solid fa-cart-plus text-primary"></i> New Purchase Order</h2>
                    <small class="text-muted">Add products to create purchase order</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-8">
            <!-- Unified Search Bar -->
            <div class="card border-primary">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fa-solid fa-search"></i>
                                    </span>
                                </div>
                                <input type="text" id="searchProducts" class="form-control"
                                    placeholder="Search products by name, SKU, barcode...">
                            </div>
                        </div>
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
                        <div class="col-md-2">
                            <select id="supplierFilter" class="form-control">
                                <option value="">All Suppliers</option>
                                <?php if (!empty($data['suppliers'])): ?>
                                    <?php foreach ($data['suppliers'] as $supplier): ?>
                                        <option value="<?php echo htmlspecialchars($supplier->supplier_id); ?>">
                                            <?php echo htmlspecialchars($supplier->supplier_name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" id="priceMinFilter" class="form-control" placeholder="Min ₹">
                        </div>
                        <div class="col-md-2">
                            <input type="number" id="priceMaxFilter" class="form-control" placeholder="Max ₹">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Enhanced Products Section (Wider) -->
        <div class="col-lg-8">
            <!-- Products Grid (Enhanced Layout) -->
            <div class="card">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0"><i class="fas fa-boxes text-primary"></i> Available Products</h6>
                            <small class="text-muted">Click to add to purchase order</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span id="resultsCount" class="badge badge-primary mr-2">0 products</span>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" id="grid-view" class="btn btn-outline-secondary active">
                                    <i class="fas fa-th-large"></i>
                                </button>
                                <button type="button" id="list-view" class="btn btn-outline-secondary">
                                    <i class="fas fa-list"></i>
                                </button>
                                <button type="button" id="table-view" class="btn btn-outline-secondary">
                                    <i class="fas fa-table"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-2">
                    <!-- Enhanced Products Container -->
                    <div id="products-container" class="row">
                        <?php if (empty($data['products'])): ?>
                            <div class="col-12">
                                <div class="alert alert-warning text-center">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                    <h5>No Products Found</h5>
                                    <p class="mb-0">Please add products to your inventory first.</p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php foreach ($data['products'] as $product): ?>
                            <div class="col-lg-6 col-xl-4 mb-3 product-item"
                                data-name="<?php echo strtolower($product->product_name); ?>"
                                data-sku="<?php echo strtolower($product->sku ?? ''); ?>"
                                data-category="<?php echo strtolower($product->category_name ?? ''); ?>"
                                data-id="<?php echo $product->product_id; ?>"
                                data-price="<?php echo $product->supplier_price ?? $product->unit_price ?? 0; ?>"
                                data-inventory="<?php echo $product->current_inventory ?? 0; ?>"
                                data-supplier-id="<?php echo $product->supplier_id ?? ''; ?>"
                                data-preferred="<?php echo ($product->preferred_supplier ?? false) ? 'true' : 'false'; ?>">

                                <div class="card product-card h-100 shadow-sm border-0"
                                    data-id="<?php echo $product->product_id; ?>"
                                    data-name="<?php echo htmlspecialchars($product->product_name); ?>"
                                    data-price="<?php echo $product->supplier_price ?? $product->unit_price ?? 0; ?>"
                                    style="cursor: pointer; transition: all 0.2s;">

                                    <!-- Enhanced Product Header -->
                                    <div class="card-header p-2 d-flex justify-content-between align-items-center"
                                        style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                        <div class="d-flex align-items-center">
                                            <div class="product-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-2"
                                                style="width: 32px; height: 32px; font-size: 12px;">
                                                <?php echo strtoupper(substr($product->product_name, 0, 2)); ?>
                                            </div>
                                            <div>
                                                <small class="font-weight-bold text-truncate d-block"
                                                    style="max-width: 120px;">
                                                    <?php echo htmlspecialchars($product->supplier_name ?? 'No Supplier'); ?>
                                                </small>
                                                <?php if (!empty($product->sku)): ?>
                                                    <small
                                                        class="text-muted"><?php echo htmlspecialchars($product->sku); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <?php if (($product->price_rank ?? 0) == 1): ?>
                                                <span class="badge badge-success badge-sm mr-1" title="Best Price">💰</span>
                                            <?php endif; ?>
                                            <?php if ($product->preferred_supplier ?? false): ?>
                                                <span class="badge badge-info badge-sm" title="Preferred">❤️</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Enhanced Product Body -->
                                    <div class="card-body p-3">
                                        <h6 class="card-title mb-2 text-truncate"
                                            title="<?php echo htmlspecialchars($product->product_name); ?>">
                                            <?php echo htmlspecialchars($product->product_name); ?>
                                        </h6>

                                        <div class="row align-items-center mb-2">
                                            <div class="col-8">
                                                <div class="h5 mb-0 text-primary font-weight-bold">
                                                    ₹<?php echo number_format($product->supplier_price ?? $product->unit_price ?? 0, 2); ?>
                                                </div>
                                                <small class="text-muted">per unit</small>
                                            </div>
                                            <div class="col-4 text-right">
                                                <?php $inventoryVal = $product->current_inventory ?? 0; ?>
                                                <?php if ($inventoryVal == 0): ?>
                                                    <span class="badge badge-danger">Out of Stock</span>
                                                <?php elseif ($inventoryVal <= 5): ?>
                                                    <span class="badge badge-warning">Low Stock</span>
                                                    <small class="text-muted d-block"><?php echo $inventoryVal; ?> left</small>
                                                <?php else: ?>
                                                    <span class="badge badge-success">In Stock</span>
                                                    <small class="text-muted d-block"><?php echo $inventoryVal; ?> units</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <?php if (!empty($product->category_name)): ?>
                                            <small class="text-muted">
                                                <i class="fas fa-tag"></i>
                                                <?php echo htmlspecialchars($product->category_name); ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Quick Add Button -->
                                    <div class="card-footer p-2 bg-transparent border-top-0 d-flex align-items-center">
                                        <select class="form-control form-control-sm mr-2 product-supplier-select"
                                            data-product-id="<?php echo $product->product_id; ?>"
                                            aria-label="Select supplier for <?php echo htmlspecialchars($product->product_name); ?>">
                                            <?php if (!empty($product->supplier_id)): ?>
                                                <option value="<?php echo $product->supplier_id; ?>" selected>
                                                    <?php echo htmlspecialchars($product->supplier_name); ?>
                                                </option>
                                            <?php else: ?>
                                                <option value="">Default</option>
                                            <?php endif; ?>
                                        </select>

                                        <button class="btn btn-primary btn-sm add-to-cart"
                                            data-id="<?php echo $product->product_id; ?>">
                                            <i class="fas fa-plus"></i> Add to Order
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Table View Container (Hidden by default) -->
                    <div id="table-container" class="table-responsive" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center p-2">
                            <div>
                                <small class="text-muted">Table view (server-paginated)</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <nav aria-label="Products pagination">
                                    <ul class="pagination pagination-sm mb-0" id="products-pagination">
                                        <!-- Filled by JS -->
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        <table class="table table-hover mb-0" id="products-table">
                            <thead class="thead-dark sticky-top">
                                <tr>
                                    <th width="3%">
                                        <input type="checkbox" id="select-all-table" class="form-check-input">
                                    </th>
                                    <th width="5%">Image</th>
                                    <th width="25%">Product Details</th>
                                    <th width="12%">Price</th>
                                    <th width="10%">Stock</th>
                                    <th width="15%">Supplier</th>
                                    <th width="12%">Category</th>
                                    <th width="10%">Last Ordered</th>
                                    <th width="8%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($data['products'])): ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="alert alert-warning mb-0">
                                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                                <h5>No Products Found</h5>
                                                <p class="mb-0">Please add products to your inventory first.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($data['products'] as $product): ?>
                                    <tr class="product-row-table" data-id="<?php echo $product->product_id; ?>"
                                        data-name="<?php echo strtolower($product->product_name); ?>"
                                        data-price="<?php echo $product->supplier_price ?? $product->unit_price ?? 0; ?>"
                                        data-inventory="<?php echo $product->current_inventory ?? 0; ?>"
                                        data-category="<?php echo strtolower($product->category_name ?? ''); ?>"
                                        data-supplier-id="<?php echo $product->supplier_id ?? ''; ?>"
                                        data-supplier-name="<?php echo strtolower($product->supplier_name ?? ''); ?>">

                                        <td>
                                            <input type="checkbox" class="form-check-input product-checkbox-table"
                                                value="<?php echo $product->product_id; ?>">
                                        </td>

                                        <!-- Product Image -->
                                        <td>
                                            <div class="product-image-container d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                <?php if (!empty($product->image_path)): ?>
                                                    <img src="<?php echo htmlspecialchars($product->image_path); ?>"
                                                        class="img-thumbnail" alt="Product image"
                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="product-avatar bg-primary text-white rounded d-flex align-items-center justify-content-center"
                                                        style="width: 40px; height: 40px; font-size: 14px; font-weight: bold;">
                                                        <?php echo strtoupper(substr($product->product_name, 0, 2)); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Product Details -->
                                        <td>
                                            <div class="product-details">
                                                <div class="font-weight-bold text-truncate" style="max-width: 200px;" 
                                                    title="<?php echo htmlspecialchars($product->product_name); ?>">
                                                    <?php echo htmlspecialchars($product->product_name); ?>
                                                </div>
                                                
                                                <?php if (!empty($product->sku)): ?>
                                                    <div class="text-muted small">
                                                        <i class="fas fa-barcode"></i> SKU: <?php echo htmlspecialchars($product->sku); ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($product->description)): ?>
                                                    <div class="text-muted small text-truncate" style="max-width: 250px;" 
                                                        title="<?php echo htmlspecialchars($product->description); ?>">
                                                        <?php echo htmlspecialchars(substr($product->description, 0, 50)); ?>...
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Price -->
                                        <td>
                                            <div class="price-container">
                                                <div class="h6 mb-1 text-success font-weight-bold">
                                                    ₹<?php echo number_format($product->supplier_price ?? $product->unit_price ?? 0, 2); ?>
                                                </div>
                                                <small class="text-muted">per unit</small>
                                                
                                                <?php if (!empty($product->unit_price) && !empty($product->supplier_price) && $product->unit_price != $product->supplier_price): ?>
                                                    <div class="small text-muted mt-1">
                                                        <del>₹<?php echo number_format($product->unit_price, 2); ?></del>
                                                        <span class="text-success ml-1">
                                                            <?php 
                                                            $discount = (($product->unit_price - $product->supplier_price) / $product->unit_price) * 100;
                                                            echo round($discount, 1) . '% off';
                                                            ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Stock -->
                                        <td>
                                            <?php $inventoryVal = $product->current_inventory ?? 0; ?>
                                            <div class="stock-container text-center">
                                                <div class="h6 mb-1"><?php echo $inventoryVal; ?></div>
                                                
                                                <?php if ($inventoryVal == 0): ?>
                                                    <span class="badge badge-danger">Out of Stock</span>
                                                <?php elseif ($inventoryVal <= 5): ?>
                                                    <span class="badge badge-warning">Low Stock</span>
                                                <?php elseif ($inventoryVal <= 20): ?>
                                                    <span class="badge badge-info">Moderate</span>
                                                <?php else: ?>
                                                    <span class="badge badge-success">In Stock</span>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($product->min_order_quantity) && $product->min_order_quantity > 1): ?>
                                                    <div class="small text-muted mt-1">
                                                        Min: <?php echo $product->min_order_quantity; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Supplier -->
                                        <td>
                                            <div class="supplier-container">
                                                <div class="font-weight-bold text-truncate" style="max-width: 120px;" 
                                                    title="<?php echo htmlspecialchars($product->supplier_name ?? 'No Supplier'); ?>">
                                                    <?php echo htmlspecialchars($product->supplier_name ?? 'No Supplier'); ?>
                                                </div>
                                                
                                                <?php if (!empty($product->lead_time_days)): ?>
                                                    <div class="small text-muted">
                                                        <i class="fas fa-clock"></i> <?php echo $product->lead_time_days; ?> days
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($product->delivery_rating)): ?>
                                                    <div class="small text-muted">
                                                        <i class="fas fa-star text-warning"></i> <?php echo number_format($product->delivery_rating, 1); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Category -->
                                        <td>
                                            <div class="category-container text-center">
                                                <?php if (!empty($product->category_name)): ?>
                                                    <span class="badge badge-secondary badge-lg">
                                                        <i class="fas fa-tag"></i> 
                                                        <?php echo htmlspecialchars($product->category_name); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Last Ordered -->
                                        <td>
                                            <div class="last-ordered-container text-center">
                                                <?php if (!empty($product->last_ordered_date)): ?>
                                                    <div class="small">
                                                        <?php 
                                                        $lastOrdered = new DateTime($product->last_ordered_date);
                                                        $now = new DateTime();
                                                        $diff = $now->diff($lastOrdered);
                                                        
                                                        if ($diff->days == 0) {
                                                            echo 'Today';
                                                        } elseif ($diff->days == 1) {
                                                            echo 'Yesterday';
                                                        } elseif ($diff->days <= 7) {
                                                            echo $diff->days . ' days ago';
                                                        } elseif ($diff->days <= 30) {
                                                            echo ceil($diff->days / 7) . ' weeks ago';
                                                        } else {
                                                            echo $lastOrdered->format('M d, Y');
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="text-muted small">
                                                        <?php echo $lastOrdered->format('M d'); ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">Never</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Action -->
                                        <td>
                                            <div class="action-container text-center">
                                                <button class="btn btn-primary btn-sm add-to-cart-btn"
                                                    data-id="<?php echo $product->product_id; ?>"
                                                    data-name="<?php echo htmlspecialchars($product->product_name); ?>"
                                                    data-price="<?php echo $product->supplier_price ?? $product->unit_price ?? 0; ?>"
                                                    title="Add to Cart">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                
                                                <button class="btn btn-outline-secondary btn-sm ml-1"
                                                    onclick="showProductDetails('<?php echo $product->product_id; ?>')"
                                                    title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Cart Section (Sticky) -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 20px;">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fa-solid fa-shopping-cart"></i> Purchase Order</h5>
                        <button type="button" id="clear-cart" class="btn btn-outline-light btn-sm"
                            style="display: none;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <!-- Cart Items Container -->
                        <div id="cart-items-container" style="max-height: 400px; overflow-y: auto;">
                            <!-- Dynamic cart items -->
                        </div>

                        <!-- Empty Cart State -->
                        <div id="empty-cart-message" class="text-center py-5">
                            <i class="fa-solid fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Cart is Empty</h6>
                            <p class="text-muted small mb-0">Click products to add them</p>
                        </div>
                    </div>

                    <!-- Cart Summary -->
                    <div id="cart-summary" class="card-footer bg-light" style="display: none;">
                        <form action="<?php echo URLROOT; ?>/purchases/add" method="post" id="purchase-form">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Total:</h5>
                                <h4 class="mb-0 text-success">₹<span id="total-amount">0.00</span></h4>
                            </div>

                            <!-- Hidden inputs -->
                            <input type="hidden" name="total_amount" id="total_amount_input">
                            <input type="hidden" name="supplier_id" id="supplier_id">

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-success btn-lg btn-block">
                                <i class="fas fa-check"></i> Create Purchase Order
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Order Summary Card -->
                <div class="card mt-3 border-info" id="order-stats" style="display: none;">
                    <div class="card-header bg-info text-white py-2">
                        <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Order Statistics</h6>
                    </div>
                    <div class="card-body py-2">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="h6 mb-0" id="stats-items">0</div>
                                <small class="text-muted">Items</small>
                            </div>
                            <div class="col-4">
                                <div class="h6 mb-0" id="stats-suppliers">0</div>
                                <small class="text-muted">Suppliers</small>
                            </div>
                            <div class="col-4">
                                <div class="h6 mb-0" id="stats-avg">₹0</div>
                                <small class="text-muted">Avg Price</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
    }

    .nav-pills .nav-link {
        border-radius: 20px;
        margin: 0 2px;
    }

    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, var(--primary) 0%, #0056b3 100%);
    }

    .sticky-top {
        z-index: 1020;
    }

    .product-avatar {
        font-weight: bold;
    }

    #cart-items-container::-webkit-scrollbar {
        width: 4px;
    }

    #cart-items-container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #cart-items-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 2px;
    }

    /* View toggle styles */
    .btn-group .btn.active {
        background-color: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* List view specific styles */
    .product-item.list-view .card {
        margin-bottom: 5px;
    }

    .product-item.list-view .card-body {
        padding: 8px 12px !important;
    }

    .product-item.list-view .card-header {
        padding: 8px 12px !important;
    }

    .product-item.list-view .card-footer {
        padding: 6px 12px !important;
    }

    .product-item.list-view .product-card {
        border-radius: 6px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Runtime safeguard: remove duplicate view-toggle buttons if any were injected
        (function dedupeToggleButtons() {
            ['grid-view', 'list-view', 'table-view'].forEach(function (id) {
                const nodes = document.querySelectorAll('#' + id);
                if (nodes && nodes.length > 1) {
                    // Keep first instance, remove the rest
                    for (let i = 1; i < nodes.length; i++) {
                        nodes[i].remove();
                    }
                }
            });
        })();
        // Enhanced cart functionality
        let cart = [];

        // Product filtering with enhanced performance
        function filterProducts() {
            const searchTerm = document.getElementById('searchProducts').value.toLowerCase();
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

        // Enhanced cart management
        function addToCart(productId, productName, price) {
            const existingItem = cart.find(item => item.id === productId);

            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({
                    id: productId,
                    name: productName,
                    price: parseFloat(price),
                    quantity: 1
                });
            }

            updateCartDisplay();
            updateOrderStats();
        }

        function updateCartDisplay() {
            const container = document.getElementById('cart-items-container');
            const emptyMessage = document.getElementById('empty-cart-message');
            const cartSummary = document.getElementById('cart-summary');
            const orderStats = document.getElementById('order-stats');
            const clearBtn = document.getElementById('clear-cart');

            if (cart.length === 0) {
                container.innerHTML = '';
                emptyMessage.style.display = 'block';
                cartSummary.style.display = 'none';
                orderStats.style.display = 'none';
                clearBtn.style.display = 'none';
                return;
            }

            emptyMessage.style.display = 'none';
            cartSummary.style.display = 'block';
            orderStats.style.display = 'block';
            clearBtn.style.display = 'inline-block';

            let html = '';
            let total = 0;

            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;

                html += `
                    <div class="cart-item border-bottom p-2">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div class="flex-grow-1">
                                <h6 class="mb-0 text-truncate" style="max-width: 150px;" title="${item.name}">${item.name}</h6>
                                <small class="text-muted">₹${item.price.toFixed(2)} × ${item.quantity}</small>
                                ${item.supplier_name ? `<br><span class="badge badge-secondary badge-sm">${item.supplier_name}</span>` : ''}
                            </div>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart('${item.id}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary" onclick="changeQuantity('${item.id}', -1)">−</button>
                                <span class="btn btn-outline-secondary">${item.quantity}</span>
                                <button class="btn btn-outline-secondary" onclick="changeQuantity('${item.id}', 1)">+</button>
                            </div>
                            <strong class="text-success">₹${itemTotal.toFixed(2)}</strong>
                        </div>
                    </div>
                `;
            });

            // Add hidden form inputs for submission
            const form = document.getElementById('purchase-form');
            if (form) {
                // Remove existing hidden inputs
                form.querySelectorAll('input[name^="products["]').forEach(input => input.remove());

                // Add new hidden inputs
                cart.forEach((item, index) => {
                    const hiddenInputs = `
                        <input type="hidden" name="products[${index}][product_id]" value="${item.id}">
                        <input type="hidden" name="products[${index}][quantity]" value="${item.quantity}">
                        <input type="hidden" name="products[${index}][price]" value="${item.price}">
                        <input type="hidden" name="products[${index}][supplier_id]" value="${item.supplier_id || ''}">
                    `;
                    form.insertAdjacentHTML('beforeend', hiddenInputs);
                });
            }

            container.innerHTML = html;
            document.getElementById('total-amount').textContent = total.toFixed(2);
            document.getElementById('total_amount_input').value = total.toFixed(2);

            // Auto-select supplier if all items from same supplier
            const supplierSelect = document.getElementById('supplier_id');
            const distinctSuppliers = Array.from(new Set(cart.filter(i => i.supplier_id).map(i => i.supplier_id)));
            if (supplierSelect && distinctSuppliers.length === 1) {
                supplierSelect.value = distinctSuppliers[0];
            }
        }

        function updateOrderStats() {
            if (cart.length === 0) return;

            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const totalValue = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const avgPrice = totalValue / totalItems;

            document.getElementById('stats-items').textContent = totalItems;
            document.getElementById('stats-suppliers').textContent = '1'; // Can be enhanced later
            document.getElementById('stats-avg').textContent = `₹${avgPrice.toFixed(2)}`;
        }

    // ...existing code... (duplicate cart helpers and simple add-to-cart handler removed; supplier-aware handlers exist later)

        // Event listeners
        document.getElementById('searchProducts').addEventListener('input', filterProducts);
        document.getElementById('categoryFilter').addEventListener('change', filterProducts);
        
        // Add supplier and price filter listeners
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
                    // Grid view - 3 columns on desktop, 2 on tablet, 1 on mobile
                    container.className = 'row';
                    document.querySelectorAll('.product-item').forEach(item => {
                        item.className = 'col-lg-6 col-xl-4 mb-3 product-item';
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
        document.getElementById('grid-view').addEventListener('click', function () {
            toggleView('grid');
        });

        document.getElementById('list-view').addEventListener('click', function () {
            toggleView('list');
        });

        document.getElementById('table-view').addEventListener('click', function () {
            toggleView('table');
        });

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

        // Notification system
        function showNotification(message, type = 'info', duration = 3000) {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification && notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, duration);
        }

        // Product card click handlers
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', function (e) {
                // Don't trigger if clicking the add button
                if (e.target.closest('.add-to-cart')) return;

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
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function (e) {
                e.stopPropagation();
                const card = this.closest('.product-card');
                const productItem = this.closest('.product-item');

                const productId = card.dataset.id;
                const productName = card.dataset.name;
                const price = card.dataset.price;
                const supplierSelect = productItem.querySelector('.product-supplier-select');
                const supplierId = supplierSelect ? supplierSelect.value : productItem.dataset.supplierId;
                const supplierName = supplierSelect ? (supplierSelect.selectedOptions[0] ? supplierSelect.selectedOptions[0].textContent.trim() : '') : (productItem.querySelector('.card-header small') ? productItem.querySelector('.card-header small').textContent.trim() : '');
                const inventory = productItem.dataset.inventory;

                window.addToCartWithDetails(productId, productName, price, supplierName, supplierId, inventory);
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

        // Event listeners
        document.getElementById('searchProducts').addEventListener('input', filterProducts);
        document.getElementById('categoryFilter').addEventListener('change', filterProducts);

        // Initialize
        filterProducts();
        // Server-driven table pagination
        let currentTablePage = 1;
        const perPage = 25;

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
                    <td><div class="product-avatar bg-primary text-white rounded" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center">${(product.product_name||'').substring(0,2).toUpperCase()}</div></td>
                    <td><div class="font-weight-bold text-truncate" style="max-width:200px">${escapeHtml(product.product_name || '')}</div><div class="text-muted small">SKU: ${escapeHtml(product.sku || '')}</div></td>
                    <td><div class="h6 mb-1 text-success font-weight-bold">₹${Number(price).toFixed(2)}</div><small class="text-muted">per unit</small></td>
                    <td><div class="h6 mb-1">${inventory}</div></td>
                    <td>
                        <select class="form-control form-control-sm table-supplier-select" data-product-id="${product.product_id}">
                            ${product.supplier_id ? `<option value="${product.supplier_id}" selected>${escapeHtml(supplierName)}</option>` : '<option value="">Default</option>'}
                        </select>
                    </td>
                    <td><div class="text-center">${escapeHtml(product.category_name || '')}</div></td>
                    <td><div class="text-center small text-muted">-</div></td>
                    <td><div class="text-center"><button class="btn btn-primary btn-sm add-to-cart-btn" data-id="${product.product_id}" data-name="${escapeHtml(product.product_name||'')}" data-price="${price}"><i class="fas fa-plus"></i></button></div></td>
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
            return (str+'').replace(/[&<>"'`]/g, function (m) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;","`":"&#96;"}[m]; });
        }

        function fetchTablePage(page = 1) {
            const params = new URLSearchParams();
            params.set('page', page);
            params.set('perPage', perPage);
            const search = document.getElementById('searchProducts').value;
            if (search) params.set('search', search);
            const supplier = document.getElementById('supplierFilter').value;
            if (supplier) params.set('supplier_id', supplier);

            fetch(`${location.origin}${location.pathname.split('/').slice(0, -1).join('/')}/productsForAdd?${params.toString()}`, {credentials: 'same-origin'})
                .then(r => r.json())
                .then(data => {
                    renderTableRows(data.rows || []);
                    renderPagination(page, data.count || 0);
                }).catch(err => {
                    console.error('Failed to load products page', err);
                });
        }

        // Fetch suppliers for a product and populate a select element
        function fetchSuppliersForProduct(productId, selectEl) {
            fetch(`${location.origin}${location.pathname.split('/').slice(0, -1).join('/')}/productSuppliers?product_id=${productId}`, {credentials: 'same-origin'})
                .then(r => r.json())
                .then(data => {
                    const suppliers = data.suppliers || [];
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
        document.getElementById('table-view').addEventListener('click', function () {
            currentTablePage = 1;
            fetchTablePage(currentTablePage);
        });
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>