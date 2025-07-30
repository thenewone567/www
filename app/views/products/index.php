<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>

<div class="container-fluid mt-0 pt-3">
    <!-- Header Section -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-6">
            <h1 class="mb-1 font-weight-bold">Inventory</h1>
            <small class="text-muted">Hardware Store Management</small>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <button class="btn btn-outline-secondary mr-2">
                <i class="fa fa-download"></i> Import
            </button>
            <button class="btn btn-outline-secondary mr-2">
                <i class="fa fa-upload"></i> Export
            </button>
            <a href="<?php echo URLROOT; ?>/products/add" class="btn btn-primary">
                <i class="fa fa-plus"></i> Add Product
            </a>
            <a href="<?php echo URLROOT; ?>/products/index_card_view" class="btn btn-outline-info ml-2">
                <i class="fa fa-th"></i> Card View
            </a>
        </div>
    </div>

    <!-- Dashboard Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Asset Value</h6>
                    <h3 class="mb-0 font-weight-bold">
                        $<?php
                        $totalValue = 0;
                        if (!empty($data['products'])) {
                            foreach ($data['products'] as $product) {
                                if (isset($product->purchase_price) && $product->current_stock > 0) {
                                    $totalValue += ($product->purchase_price * $product->current_stock);
                                }
                            }
                        }
                        echo number_format($totalValue, 0);
                        ?>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted mb-0">
                            <?php echo count($data['products'] ?? []); ?> Products
                        </h6>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <?php
                        $inStock = 0;
                        $lowStock = 0;
                        $outOfStock = 0;
                        $total = count($data['products'] ?? []);

                        if (!empty($data['products'])) {
                            foreach ($data['products'] as $product) {
                                if ($product->current_stock <= 0) {
                                    $outOfStock++;
                                } elseif ($product->current_stock <= $product->reorder_level) {
                                    $lowStock++;
                                } else {
                                    $inStock++;
                                }
                            }
                        }

                        $inStockPercent = $total > 0 ? ($inStock / $total) * 100 : 0;
                        $lowStockPercent = $total > 0 ? ($lowStock / $total) * 100 : 0;
                        $outStockPercent = $total > 0 ? ($outOfStock / $total) * 100 : 0;
                        ?>
                        <div class="progress-bar bg-success" style="width: <?php echo $inStockPercent; ?>%"></div>
                        <div class="progress-bar bg-warning" style="width: <?php echo $lowStockPercent; ?>%"></div>
                        <div class="progress-bar bg-danger" style="width: <?php echo $outStockPercent; ?>%"></div>
                    </div>
                    <div class="row text-center">
                        <div class="col">
                            <small class="text-success">
                                <i class="fa fa-circle" style="font-size: 8px;"></i> In stock: <?php echo $inStock; ?>
                            </small>
                        </div>
                        <div class="col">
                            <small class="text-warning">
                                <i class="fa fa-circle" style="font-size: 8px;"></i> Low stock: <?php echo $lowStock; ?>
                            </small>
                        </div>
                        <div class="col">
                            <small class="text-danger">
                                <i class="fa fa-circle" style="font-size: 8px;"></i> Out of stock: <?php echo $outOfStock; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-white border-right-0">
                        <i class="fa fa-search text-muted"></i>
                    </span>
                </div>
                <input type="text" id="search" class="form-control border-left-0" placeholder="Search products...">
            </div>
        </div>
        <div class="col-md-2">
            <select id="category_filter" class="form-control">
                <option value="">All Categories</option>
                <?php if (!empty($data['categories'])): ?>
                    <?php foreach ($data['categories'] as $category): ?>
                        <option value="<?php echo $category->category_id; ?>">
                            <?php echo $category->category_name; ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select id="status_filter" class="form-control">
                <option value="">Status</option>
                <option value="in_stock">In Stock</option>
                <option value="low_stock">Low Stock</option>
                <option value="out_of_stock">Out of Stock</option>
            </select>
        </div>
        <div class="col-md-3">
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="in_stock_only">
                <label class="form-check-label" for="in_stock_only">
                    In Stock Only
                </label>
            </div>
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-secondary" onclick="clearFilters()">
                <i class="fa fa-filter"></i> Filter
            </button>
        </div>
    </div>

    <!-- Enhanced Products Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-0 font-weight-bold">Product</th>
                            <th class="border-0 font-weight-bold">SKU</th>
                            <th class="border-0 font-weight-bold">Category</th>
                            <th class="border-0 font-weight-bold">Pricing</th>
                            <th class="border-0 font-weight-bold">Stock Status</th>
                            <th class="border-0 font-weight-bold">Stock Value</th>
                            <th class="border-0 font-weight-bold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="products-table-body">
                        <?php if (!empty($data['products'])): ?>
                            <?php foreach ($data['products'] as $product): ?>
                                <tr>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($product->image_path)): ?>
                                                <img src="<?php echo URLROOT; ?>/public/uploads/<?php echo $product->image_path; ?>"
                                                     alt="<?php echo $product->product_name; ?>" 
                                                     class="rounded mr-3"
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center mr-3"
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fa fa-box text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-1 font-weight-bold"><?php echo $product->product_name; ?></h6>
                                                <small class="text-muted">
                                                    <?php echo $product->brand_name ?? 'No Brand'; ?>
                                                    <?php if (!empty($product->supplier_code)): ?>
                                                        • <span class="badge badge-info badge-sm"><?php echo $product->supplier_code; ?></span>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="font-weight-medium"><?php echo $product->sku; ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-muted"><?php echo $product->category_name ?? 'Uncategorized'; ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="pricing-details">
                                            <?php if (isset($product->purchase_price) && isset($product->selling_price)): ?>
                                                <div><strong class="text-primary">$<?php echo number_format($product->selling_price, 2); ?></strong></div>
                                                <div><small class="text-muted">Cost: $<?php echo number_format($product->purchase_price, 2); ?></small></div>
                                                <?php 
                                                if ($product->purchase_price > 0) {
                                                    $margin = (($product->selling_price - $product->purchase_price) / $product->purchase_price) * 100;
                                                    $marginClass = $margin >= 25 ? 'text-success' : ($margin >= 15 ? 'text-warning' : 'text-danger');
                                                    echo '<div class="' . $marginClass . '"><small>Margin: ' . number_format($margin, 1) . '%</small></div>';
                                                }
                                                ?>
                                            <?php else: ?>
                                                <span class="text-muted">Pricing not set</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="stock-details">
                                            <?php
                                            $stockClass = '';
                                            $stockStatus = '';
                                            $badgeClass = '';
                                            if ($product->current_stock <= 0) {
                                                $stockStatus = 'Out of stock';
                                                $badgeClass = 'badge-danger';
                                                $stockClass = 'text-danger font-weight-bold';
                                            } elseif ($product->current_stock <= $product->reorder_level) {
                                                $stockStatus = 'Reorder needed';
                                                $badgeClass = 'badge-warning';
                                                $stockClass = 'text-warning font-weight-bold';
                                            } elseif ($product->current_stock <= $product->min_stock_level) {
                                                $stockStatus = 'Low stock';
                                                $badgeClass = 'badge-warning';
                                                $stockClass = 'text-warning';
                                            } else {
                                                $stockStatus = 'In stock';
                                                $badgeClass = 'badge-success';
                                                $stockClass = 'text-success';
                                            }
                                            ?>
                                            <div class="<?php echo $stockClass; ?> mb-1">
                                                <strong><?php echo $product->current_stock; ?></strong>
                                                <?php echo !empty($product->unit_abbreviation) ? $product->unit_abbreviation : ($product->unit_name ?? ''); ?>
                                            </div>
                                            <span class="badge <?php echo $badgeClass; ?> badge-sm">
                                                <?php echo $stockStatus; ?>
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                Min: <?php echo $product->min_stock_level; ?> • 
                                                Reorder: <?php echo $product->reorder_level; ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <?php if (isset($product->purchase_price) && $product->purchase_price > 0): ?>
                                            <div class="text-info font-weight-bold">
                                                $<?php echo number_format($product->current_stock * $product->purchase_price, 2); ?>
                                            </div>
                                            <small class="text-muted">
                                                @ $<?php echo number_format($product->purchase_price, 2); ?> each
                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" data-toggle="dropdown">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="<?php echo URLROOT; ?>/products/show/<?php echo $product->product_id; ?>">
                                                    <i class="fa fa-eye"></i> View Details
                                                </a>
                                                <a class="dropdown-item" href="<?php echo URLROOT; ?>/products/edit/<?php echo $product->product_id; ?>">
                                                    <i class="fa fa-edit"></i> Edit Product
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item text-warning" href="#" 
                                                   onclick="showStockAdjustment(<?php echo $product->product_id; ?>, '<?php echo addslashes($product->product_name); ?>', <?php echo $product->current_stock; ?>)">
                                                    <i class="fa fa-exchange-alt"></i> Adjust Stock
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <form class="d-inline"
                                                      action="<?php echo URLROOT; ?>/products/delete/<?php echo $product->product_id; ?>"
                                                      method="post" 
                                                      onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fa fa-trash"></i> Delete Product
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fa fa-box-open fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No products found</h5>
                                    <p class="text-muted">Get started by adding your first product</p>
                                    <a href="<?php echo URLROOT; ?>/products/add" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> Add Product
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="row mt-4">
        <div class="col-md-6">
            <small class="text-muted">
                Result 1-<?php echo count($data['products'] ?? []); ?> of <?php echo count($data['products'] ?? []); ?>
            </small>
        </div>
        <div class="col-md-6">
            <nav aria-label="Products pagination">
                <ul class="pagination pagination-sm justify-content-end mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">
                            <i class="fa fa-chevron-left"></i> Previous
                        </a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="#">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">12</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">
                            Next <i class="fa fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="stockAdjustmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adjust Stock</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="stockAdjustmentForm">
                    <input type="hidden" id="adjust_product_id">
                    <div class="form-group">
                        <label>Product:</label>
                        <p id="adjust_product_name" class="font-weight-bold"></p>
                    </div>
                    <div class="form-group">
                        <label>Current Stock:</label>
                        <p id="adjust_current_stock" class="text-info"></p>
                    </div>
                    <div class="form-group">
                        <label for="new_quantity">New Quantity:</label>
                        <input type="number" class="form-control" id="new_quantity" required min="0" step="1">
                    </div>
                    <div class="form-group">
                        <label for="adjustment_reason">Reason:</label>
                        <select class="form-control" id="adjustment_reason">
                            <option value="Manual Adjustment">Manual Adjustment</option>
                            <option value="Inventory Count">Inventory Count</option>
                            <option value="Damaged Goods">Damaged Goods</option>
                            <option value="Stock Correction">Stock Correction</option>
                            <option value="Return to Supplier">Return to Supplier</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="adjustment_notes">Notes:</label>
                        <textarea class="form-control" id="adjustment_notes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitStockAdjustment()">
                    <i class="fa fa-save"></i> Adjust Stock
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function searchProducts() {
        const searchTerm = document.getElementById('search').value;
        const categoryId = document.getElementById('category_filter').value;
        const statusFilter = document.getElementById('status_filter').value;
        const inStockOnly = document.getElementById('in_stock_only').checked;

        fetch('<?php echo URLROOT; ?>/products/search', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `search=${encodeURIComponent(searchTerm)}&category_id=${categoryId}&status=${statusFilter}&in_stock_only=${inStockOnly ? '1' : ''}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateProductsTable(data.products);
                }
            })
            .catch(error => console.error('Search error:', error));
    }

    function updateProductsTable(products) {
        const tbody = document.getElementById('products-table-body');
        if (products.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="fa fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No products found</h5>
                        <p class="text-muted">Try adjusting your search or filters</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        products.forEach(product => {
            const stockStatus = product.current_stock <= 0 ? 'Out of stock' :
                               product.current_stock <= product.reorder_level ? 'Reorder needed' : 
                               product.current_stock <= product.min_stock_level ? 'Low stock' : 'In stock';
            const badgeClass = product.current_stock <= 0 ? 'badge-danger' :
                              product.current_stock <= product.reorder_level ? 'badge-warning' : 
                              product.current_stock <= product.min_stock_level ? 'badge-warning' : 'badge-success';
            const stockClass = product.current_stock <= 0 ? 'text-danger font-weight-bold' :
                              product.current_stock <= product.reorder_level ? 'text-warning font-weight-bold' : 
                              product.current_stock <= product.min_stock_level ? 'text-warning' : 'text-success';

            const margin = product.purchase_price > 0 ? 
                          (((product.selling_price - product.purchase_price) / product.purchase_price) * 100) : 0;
            const marginClass = margin >= 25 ? 'text-success' : (margin >= 15 ? 'text-warning' : 'text-danger');

            html += `
                <tr>
                    <td class="align-middle">
                        <div class="d-flex align-items-center">
                            ${product.image_path ? 
                                `<img src="<?php echo URLROOT; ?>/public/uploads/${product.image_path}" class="rounded mr-3" style="width: 40px; height: 40px; object-fit: cover;">` :
                                '<div class="bg-light rounded d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;"><i class="fa fa-box text-muted"></i></div>'
                            }
                            <div>
                                <h6 class="mb-1 font-weight-bold">${product.product_name}</h6>
                                <small class="text-muted">${product.brand_name || 'No Brand'} ${product.supplier_code ? `• <span class="badge badge-info badge-sm">${product.supplier_code}</span>` : ''}</small>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle">
                        <span class="font-weight-medium">${product.sku}</span>
                    </td>
                    <td class="align-middle">
                        <span class="text-muted">${product.category_name || 'Uncategorized'}</span>
                    </td>
                    <td class="align-middle">
                        ${product.purchase_price && product.selling_price ? 
                            `<div>
                                <div><strong class="text-primary">$${parseFloat(product.selling_price).toFixed(2)}</strong></div>
                                <div><small class="text-muted">Cost: $${parseFloat(product.purchase_price).toFixed(2)}</small></div>
                                ${product.purchase_price > 0 ? `<div class="${marginClass}"><small>Margin: ${margin.toFixed(1)}%</small></div>` : ''}
                            </div>` : 
                            '<span class="text-muted">Pricing not set</span>'
                        }
                    </td>
                    <td class="align-middle">
                        <div class="stock-details">
                            <div class="${stockClass} mb-1">
                                <strong>${product.current_stock}</strong> ${product.unit_abbreviation || product.unit_name || ''}
                            </div>
                            <span class="badge ${badgeClass} badge-sm">${stockStatus}</span>
                            <br>
                            <small class="text-muted">Min: ${product.min_stock_level} • Reorder: ${product.reorder_level}</small>
                        </div>
                    </td>
                    <td class="align-middle">
                        ${product.purchase_price > 0 ? 
                            `<div class="text-info font-weight-bold">$${(product.current_stock * product.purchase_price).toFixed(2)}</div>
                             <small class="text-muted">@ $${parseFloat(product.purchase_price).toFixed(2)} each</small>` : 
                            '<span class="text-muted">-</span>'
                        }
                    </td>
                    <td class="align-middle text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary" data-toggle="dropdown">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="<?php echo URLROOT; ?>/products/show/${product.product_id}">
                                    <i class="fa fa-eye"></i> View Details
                                </a>
                                <a class="dropdown-item" href="<?php echo URLROOT; ?>/products/edit/${product.product_id}">
                                    <i class="fa fa-edit"></i> Edit Product
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-warning" href="#" onclick="showStockAdjustment(${product.product_id}, '${product.product_name.replace(/'/g, "\\'")}', ${product.current_stock})">
                                    <i class="fa fa-exchange-alt"></i> Adjust Stock
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function clearFilters() {
        document.getElementById('search').value = '';
        document.getElementById('category_filter').value = '';
        document.getElementById('status_filter').value = '';
        document.getElementById('in_stock_only').checked = false;
        searchProducts();
    }

    function showStockAdjustment(productId, productName, currentStock) {
        document.getElementById('adjust_product_id').value = productId;
        document.getElementById('adjust_product_name').textContent = productName;
        document.getElementById('adjust_current_stock').textContent = currentStock;
        document.getElementById('new_quantity').value = currentStock;
        $('#stockAdjustmentModal').modal('show');
    }

    function submitStockAdjustment() {
        const productId = document.getElementById('adjust_product_id').value;
        const newQuantity = document.getElementById('new_quantity').value;
        const reason = document.getElementById('adjustment_reason').value;
        const notes = document.getElementById('adjustment_notes').value;

        fetch(`<?php echo URLROOT; ?>/products/adjustStock/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `new_quantity=${newQuantity}&reason=${encodeURIComponent(reason)}&notes=${encodeURIComponent(notes)}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#stockAdjustmentModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error adjusting stock: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Stock adjustment error:', error);
                alert('Error adjusting stock');
            });
    }

    // Auto-search functionality
    document.getElementById('search').addEventListener('input', function () {
        if (this.value.length >= 2 || this.value.length === 0) {
            searchProducts();
        }
    });

    document.getElementById('category_filter').addEventListener('change', searchProducts);
    document.getElementById('status_filter').addEventListener('change', searchProducts);
    document.getElementById('in_stock_only').addEventListener('change', searchProducts);

    // Add custom CSS for professional table styling
    const style = document.createElement('style');
    style.textContent = `
        .table th {
            vertical-align: middle;
            border-bottom: 2px solid #dee2e6;
        }
        .table td {
            vertical-align: middle;
            border-top: 1px solid #dee2e6;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0,123,255,.075);
        }
        .badge-sm {
            font-size: 0.75em;
        }
        .font-weight-medium {
            font-weight: 500;
        }
        .card {
            border-radius: 12px;
        }
        .form-control, .input-group-text {
            border-radius: 8px;
        }
        .btn {
            border-radius: 8px;
        }
    `;
    document.head.appendChild(style);
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'footer.php'; ?>
