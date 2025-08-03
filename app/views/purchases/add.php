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
            <h2 class="mb-0"><i class="fa-solid fa-cart-plus"></i> New Purchase</h2>
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
                                <input type="text" id="product-search" class="form-control" placeholder="Search by name, SKU...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-barcode"></i>
                                    </span>
                                </div>
                                <input type="text" id="barcode-search" class="form-control" placeholder="Scan/Enter barcode">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="category-filter" class="form-control">
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
                                         data-price="<?php echo $product->unit_price; ?>">
                                        <div class="card-body text-center">
                                            <h6 class="card-title mb-2"><?php echo htmlspecialchars($product->product_name); ?></h6>
                                            <p class="text-muted small mb-1">
                                                SKU: <?php echo htmlspecialchars($product->sku ?? 'N/A'); ?>
                                            </p>
                                            <p class="text-muted small mb-2">
                                                <?php echo htmlspecialchars($product->category_name ?? 'Uncategorized'); ?>
                                            </p>
                                            <h5 class="text-primary mb-2">$<?php echo number_format($product->unit_price, 2); ?></h5>
                                            <button class="btn btn-success btn-sm w-100">
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
                    <h5 class="mb-0"><i class="fa-solid fa-shopping-cart"></i> Purchase Cart</h5>
                    <button type="button" id="clear-cart" class="btn btn-outline-danger btn-sm" style="display: none;">
                        <i class="fa-solid fa-trash"></i> Clear Cart
                    </button>
                </div>
                <div class="card-body">
                    <form action="<?php echo URLROOT; ?>/purchases/add" method="post">
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
                            <div class="form-group">
                                <label for="supplier_id">Supplier:</label>
                                <select name="supplier_id" class="form-control" required>
                                    <option value="">Select Supplier</option>
                                    <?php foreach ($data['suppliers'] as $supplier): ?>
                                        <option value="<?php echo $supplier->supplier_id; ?>" 
                                            <?php echo ($data['supplier_id'] == $supplier->supplier_id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($supplier->supplier_name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="invalid-feedback"><?php echo $data['supplier_id_err']; ?></span>
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
<script>
// ...existing JavaScript logic from previous script block goes here...
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