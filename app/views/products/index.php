<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/app-unified.css">

<div class="container-fluid page-top-area my-4">
    <div class="row align-items-center mb-3">
        <div class="col-md-6">
            <h1 class="h3 mb-0"><i class="fas fa-boxes mr-2"></i>Products</h1>
            <small class="text-muted">Catalog & inventory overview</small>
        </div>
        <div class="col-md-6 text-md-right mt-3 mt-md-0">
            <a href="<?php echo URLROOT; ?>/products/add" class="btn btn-success">
                <i class="fa fa-plus mr-1"></i> Add Product
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card-theme p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="mr-3 display-4 text-primary"><i class="fas fa-cubes"></i></div>
                    <div>
                        <div class="text-muted">Total Products</div>
                        <div class="h4 mb-0"><?php echo $data['pagination']['total_records'] ?? 0; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card-theme p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="mr-3 display-4 text-success"><i class="fas fa-warehouse"></i></div>
                    <div>
                        <div class="text-muted">Total Inventory</div>
                        <div class="h4 mb-0"><?php echo number_format($data['total_inventory'] ?? 0); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card-theme p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="mr-3 display-4 text-warning"><i class="fas fa-percentage"></i></div>
                    <div>
                        <div class="text-muted">Avg Margin</div>
                        <div class="h4 mb-0">
                            <?php echo isset($data['avg_margin']) && $data['avg_margin'] !== null ? number_format($data['avg_margin'], 1) . '%' : '—'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card-theme p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="mr-3 display-4 text-danger"><i class="fas fa-exclamation-triangle"></i></div>
                    <div>
                        <div class="text-muted">Need Reorder</div>
                        <div class="h4 mb-0"><?php echo $data['need_reorder'] ?? 0; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <form method="GET" action="<?php echo URLROOT; ?>/products" class="d-inline">
                <div class="search-container position-relative">
                    <input id="productSearch" name="search" class="form-control search-input"
                        placeholder="Search products..."
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="search-icon-btn">
                        <i class="fas fa-search"></i>
                    </button>
                    <?php if (!empty($_GET['search'])): ?>
                        <a href="<?php echo URLROOT; ?>/products" class="clear-search-btn" title="Clear search">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <div class="col-md-6 text-md-right">
            <div class="pagination-controls">
                <div class="d-flex align-items-center">
                    <label for="itemsPerPage" class="text-muted mr-2 mb-0" style="font-size: 0.875rem;">Show:</label>
                    <select id="itemsPerPage" class="form-control form-control-sm d-inline-block" style="width:100px;"
                        onchange="changeItemsPerPage();">
                        <option value="25" <?php echo (isset($data['pagination']['per_page']) && $data['pagination']['per_page'] == 25) ? 'selected' : ''; ?>>25</option>
                        <option value="50" <?php echo (isset($data['pagination']['per_page']) && $data['pagination']['per_page'] == 50) ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?php echo (isset($data['pagination']['per_page']) && $data['pagination']['per_page'] == 100) ? 'selected' : ''; ?>>100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Table and column toggles -->
    <div class="card-theme">
        <div class="card-body p-2 border-bottom">
            <div class="d-flex align-items-center flex-wrap">
                <h6 class="mb-0 text-primary">Products</h6>
            </div>
        </div>

        <div class="table-responsive">
            <table id="productsTable" class="table table-hover table-layout-fixed mb-0">
                <thead class="thead-light">
                    <tr>
                        <!-- select-all column removed -->
                        <th style="width:80px" title="Product Image">Image</th>
                        <th title="Product Name and Details">Product</th>
                        <th style="width:120px" title="Stock Keeping Unit (SKU)">SKU</th>
                        <th class="column-brand" style="width:120px" title="Product Brand">Brand</th>
                        <th style="width:120px" title="Product Category">Category</th>
                        <th style="width:100px" title="Current Stock Quantity">Stock</th>
                        <th class="column-margin" style="width:90px" title="Profit Margin Percentage">Margin</th>
                        <th class="column-reorder" style="width:140px" title="Reorder Level">Reorder Level</th>
                        <th class="column-unit" style="width:80px" title="Unit of Measurement">Unit</th>
                        <th style="width:90px" title="Base Purchase Price from Supplier">Purchase Price</th>
                        <th style="width:90px" title="Current Average Cost (Realistic)">Average Cost</th>
                        <th style="width:90px" title="Current Selling Price">Sale Price</th>
                        <th style="width:140px" title="Available Actions">Actions</th>
                    </tr>
                </thead>
                <tbody id="productsTableBody">
                    <?php if (!empty($data['products'])):
                        foreach ($data['products'] as $product): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($product->image_path)): ?>
                                        <img src="<?php echo URLROOT; ?>/public/uploads/<?php echo $product->image_path; ?>"
                                            style="width:48px;height:48px;object-fit:cover;border-radius:4px;">
                                    <?php else: ?>
                                        <img src="<?php echo URLROOT; ?>/storage/uploads/products/no_image.png"
                                            style="width:48px;height:48px;object-fit:cover;border-radius:4px;">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($product->product_name); ?></strong>
                                    <?php if (!empty($product->model_number)): ?>
                                        <div class="text-muted small">Model/Batch:
                                            <?php echo htmlspecialchars($product->model_number); ?>
                                        </div><?php endif; ?>
                                </td>
                                <td><code><?php echo htmlspecialchars($product->sku); ?></code></td>
                                <td class="column-brand"><?php echo htmlspecialchars($product->brand_name ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($product->category_name ?? '-'); ?></td>
                                <td>
                                    <span
                                        class="badge <?php echo ($product->current_inventory <= 5) ? 'badge-danger' : (($product->current_inventory <= 10) ? 'badge-warning' : 'badge-success'); ?>"><?php echo (int) ($product->current_inventory ?? 0); ?></span>
                                </td>
                                <td class="column-margin"><?php
                                $selling = $product->selling_price ?? 0;
                                // Use same cost logic as details view: current_average_cost if available, otherwise purchase_price
                                $cost = ($product->current_average_cost ?? 0) > 0 ? $product->current_average_cost : ($product->purchase_price ?? 0);
                                $margin = 0;
                                if ($selling > 0 && $cost > 0)
                                    $margin = (($selling - $cost) / $selling) * 100;
                                echo number_format($margin, 1) . '%';
                                ?></td>
                                <td class="column-reorder text-center">
                                    <?php echo htmlspecialchars((string) ($product->reorder_level ?? '-')); ?>
                                </td>
                                <td class="column-unit"><?php echo htmlspecialchars($product->unit_name ?? '-'); ?></td>
                                <td class="text-right">
                                    <small class="text-muted">Base:</small><br>
                                    <strong><?php echo formatCurrency($product->purchase_price ?? 0, 2); ?></strong>
                                </td>
                                <td class="text-right">
                                    <small class="text-muted">Avg:</small><br>
                                    <strong
                                        class="text-info"><?php echo formatCurrency($product->current_average_cost ?? 0, 2); ?></strong>
                                </td>
                                <td class="text-right">
                                    <small class="text-muted">Sale:</small><br>
                                    <strong
                                        class="text-success"><?php echo formatCurrency($product->selling_price ?? 0, 2); ?></strong>
                                </td>
                                <td>
                                    <div class="btn-group dropdown">
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="viewProduct(<?php echo $product->product_id; ?>)"><i
                                                class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-outline-warning"
                                            onclick="editProduct(<?php echo $product->product_id; ?>)"><i
                                                class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown"
                                            aria-expanded="false" aria-haspopup="true">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#"
                                                onclick="openAddSupplierModal(<?php echo $product->product_id; ?>, '<?php echo htmlspecialchars($product->product_name, ENT_QUOTES); ?>')"><i
                                                    class="fas fa-plus mr-1"></i>Add Supplier</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item text-warning" href="#"
                                                onclick="deactivateProduct(<?php echo $product->product_id; ?>)"><i
                                                    class="fas fa-user-slash mr-1"></i>Deactivate</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="11" class="text-center py-5">No products found — <a
                                    href="<?php echo URLROOT; ?>/products/add">Add your first product</a></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <div>
                <?php if (!empty($data['pagination'])): ?>
                    <small class="text-muted">Showing <?php echo $data['pagination']['start_record']; ?> to
                        <?php echo $data['pagination']['end_record']; ?> of
                        <?php echo number_format($data['pagination']['total_records']); ?> products</small>
                <?php endif; ?>
            </div>
            <div>
                <?php if (!empty($data['pagination']) && $data['pagination']['total_pages'] > 1): ?>
                    <nav aria-label="Product pagination">
                        <ul class="pagination pagination-sm mb-0">
                            <?php
                            $current_page = $data['pagination']['current_page'];
                            $total_pages = $data['pagination']['total_pages'];
                            $search = $data['pagination']['search'] ?? '';

                            // Previous button
                            if ($current_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="<?php echo URLROOT; ?>/products?page=<?php echo $current_page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo '&per_page=' . $data['pagination']['per_page']; ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                                </li>
                            <?php endif; ?>

                            <?php
                            // Page numbers
                            $start_page = max(1, $current_page - 2);
                            $end_page = min($total_pages, $current_page + 2);

                            // Show first page if not in range
                            if ($start_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="<?php echo URLROOT; ?>/products?page=1<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo '&per_page=' . $data['pagination']['per_page']; ?>">1</a>
                                </li>
                                <?php if ($start_page > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                    <a class="page-link"
                                        href="<?php echo URLROOT; ?>/products?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo '&per_page=' . $data['pagination']['per_page']; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php
                            // Show last page if not in range
                            if ($end_page < $total_pages): ?>
                                <?php if ($end_page < $total_pages - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="<?php echo URLROOT; ?>/products?page=<?php echo $total_pages; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo '&per_page=' . $data['pagination']['per_page']; ?>"><?php echo $total_pages; ?></a>
                                </li>
                            <?php endif; ?>

                            <?php
                            // Next button
                            if ($current_page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="<?php echo URLROOT; ?>/products?page=<?php echo $current_page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo '&per_page=' . $data['pagination']['per_page']; ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<!-- Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog" aria-labelledby="addSupplierModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSupplierModalLabel">
                    <i class="fas fa-plus mr-2"></i>Add Supplier to Product
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Product:</strong> <span id="currentProductName" class="text-primary"></span>
                </div>

                <div class="form-group">
                    <label for="supplierSearch">Search Suppliers</label>
                    <input type="text" class="form-control" id="supplierSearch"
                        placeholder="Type supplier name to search..." onkeyup="searchSuppliers(this.value)">
                </div>

                <div id="supplierSearchResults" class="mt-3">
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-search fa-2x mb-2"></i>
                        <p>Start typing to search for suppliers</p>
                    </div>
                </div>

                <div id="selectedSupplier" class="mt-3" style="display: none;">
                    <div class="alert alert-info">
                        <strong>Selected:</strong> <span id="selectedSupplierName"></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary float-right"
                            onclick="clearSelectedSupplier()">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>

                    <!-- Purchase Price Input -->
                    <div class="form-group">
                        <label for="purchasePriceProducts">Purchase Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" id="purchasePriceProducts" placeholder="0.00"
                                min="0.01" step="0.01" required>
                        </div>
                        <small class="form-text text-muted">Enter the purchase price for this product from this
                            supplier</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAddSupplier" onclick="confirmAddSupplier()"
                    disabled>
                    <i class="fas fa-plus mr-1"></i> Add Supplier
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Minimal JS to wire up existing functions already in the layout script
    document.addEventListener('DOMContentLoaded', function () {
        // Reuse existing functions from previously-loaded script in this view (if any)
        if (typeof initializeProductTable === 'function') initializeProductTable();

        // Column toggle handling: remember the default state and allow show all / reset
        try {
            const toggles = Array.from(document.querySelectorAll('.column-toggle'));
            // store default checked state for reset
            window.productsDefaultColumns = {};

            toggles.forEach(btn => {
                const col = btn.dataset.column;
                const checkbox = btn.querySelector('input[type=checkbox]');
                if (!col || !checkbox) return;

                // Save default state once on load
                window.productsDefaultColumns[col] = !!checkbox.checked;

                // When checkbox changes, show/hide the corresponding table columns
                checkbox.addEventListener('change', function () {
                    const show = !!this.checked;
                    const selector = '.column-' + col;
                    document.querySelectorAll(selector).forEach(el => {
                        el.style.display = show ? '' : 'none';
                    });
                    if (show) btn.classList.add('active'); else btn.classList.remove('active');
                });

                // Clicking the label toggles the checkbox — the change handler will update UI
                btn.addEventListener('click', function (ev) {
                    // Prevent double toggles if user clicks the checkbox itself
                    if (ev.target && ev.target.tagName === 'INPUT') return;
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change'));
                });
            });

            // Apply initial visibility based on the loaded defaults (hide any unchecked columns)
            Object.keys(window.productsDefaultColumns).forEach(col => {
                if (!window.productsDefaultColumns[col]) {
                    document.querySelectorAll('.column-' + col).forEach(el => el.style.display = 'none');
                }
            });

        } catch (err) {
            // Fail silently but log for debugging
            if (window.console) console.error('Column toggle init failed:', err);
        }
    });
</script>

<script>
    // Small helper functions referenced from the HTML that may not be loaded from other scripts
    (function () {
        const URLROOT = '<?php echo URLROOT; ?>';

        // Navigate to product view page (fallback if other scripts aren't loaded)
        window.viewProduct = function (id) {
            if (!id) return;
            window.location.href = URLROOT + '/products/view/' + id;
        };

        // Navigate to edit page
        window.editProduct = function (id) {
            if (!id) return;
            window.location.href = URLROOT + '/products/edit/' + id;
        };

        // Add Supplier Modal Functions
        let selectedSupplierId = null;
        let currentProductId = null;
        let searchTimeout = null;

        window.openAddSupplierModal = function (productId, productName) {
            currentProductId = productId;
            document.getElementById('currentProductName').textContent = productName || 'Unknown Product';
            $('#addSupplierModal').modal('show');
            document.getElementById('supplierSearch').value = '';
            clearSelectedSupplier();
            resetSearchResults();
        };

        function resetSearchResults() {
            document.getElementById('supplierSearchResults').innerHTML = `
                <div class="text-center text-muted py-3">
                    <i class="fas fa-search fa-2x mb-2"></i>
                    <p>Start typing to search for suppliers</p>
                </div>
            `;
        }

        window.searchSuppliers = function (query) {
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            if (!query || query.trim().length < 2) {
                resetSearchResults();
                return;
            }

            // Show loading
            document.getElementById('supplierSearchResults').innerHTML = `
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Searching suppliers...</p>
                </div>
            `;

            searchTimeout = setTimeout(() => {
                fetch(`${URLROOT}/api/getSuppliers.php?search=${encodeURIComponent(query.trim())}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.suppliers) {
                            displaySupplierResults(data.suppliers);
                        } else {
                            document.getElementById('supplierSearchResults').innerHTML = `
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                                    <p>No suppliers found matching "${query}"</p>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Error searching suppliers:', error);
                        document.getElementById('supplierSearchResults').innerHTML = `
                            <div class="text-center text-danger py-3">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <p>Error loading suppliers. Please try again.</p>
                            </div>
                        `;
                    });
            }, 300);
        };

        function displaySupplierResults(suppliers) {
            if (!suppliers || suppliers.length === 0) {
                resetSearchResults();
                return;
            }

            let html = '<div class="list-group">';
            suppliers.forEach(supplier => {
                html += `
                    <div class="list-group-item list-group-item-action" onclick="selectSupplier(${supplier.supplier_id}, '${escapeHtml(supplier.supplier_name)}')">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">${escapeHtml(supplier.supplier_name)}</h6>
                            <small class="text-muted">ID: ${supplier.supplier_id}</small>
                        </div>
                        <p class="mb-1 text-muted">
                            ${supplier.contact_person ? escapeHtml(supplier.contact_person) : 'No contact'} | 
                            ${supplier.phone ? escapeHtml(supplier.phone) : 'No phone'} | 
                            ${supplier.email ? escapeHtml(supplier.email) : 'No email'}
                        </p>
                        <small class="text-muted">${supplier.address ? escapeHtml(supplier.address) : 'No address'}</small>
                    </div>
                `;
            });
            html += '</div>';

            document.getElementById('supplierSearchResults').innerHTML = html;
        }

        window.selectSupplier = function (supplierId, supplierName) {
            selectedSupplierId = supplierId;
            document.getElementById('selectedSupplierName').textContent = supplierName;
            document.getElementById('selectedSupplier').style.display = 'block';
            document.getElementById('confirmAddSupplier').disabled = false;

            // Hide search results
            document.getElementById('supplierSearchResults').innerHTML = `
                <div class="text-center text-success py-3">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <p>Supplier selected! Click "Add Supplier" to confirm.</p>
                </div>
            `;
        };

        window.clearSelectedSupplier = function () {
            selectedSupplierId = null;
            document.getElementById('selectedSupplier').style.display = 'none';
            document.getElementById('confirmAddSupplier').disabled = true;
            document.getElementById('purchasePriceProducts').value = '';
            resetSearchResults();
        };

        window.confirmAddSupplier = function () {
            if (!selectedSupplierId || !currentProductId) {
                alert('Please select a supplier first.');
                return;
            }

            // Validate purchase price
            const purchasePrice = document.getElementById('purchasePriceProducts').value;
            if (!purchasePrice || parseFloat(purchasePrice) <= 0) {
                alert('Please enter a valid purchase price.');
                document.getElementById('purchasePriceProducts').focus();
                return;
            }

            const formData = new FormData();
            formData.append('product_id', currentProductId);
            formData.append('supplier_id', selectedSupplierId);
            formData.append('purchase_price', purchasePrice);

            // Disable button and show loading
            const confirmBtn = document.getElementById('confirmAddSupplier');
            const originalText = confirmBtn.innerHTML;
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Adding...';

            fetch(`${URLROOT}/products/linkSupplier`, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
                .then(response => {
                    console.log('Fetch response received:', response);
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    return response.text();
                })
                .then(text => {
                    console.log('Raw response text:', text);

                    try {
                        const data = JSON.parse(text);
                        console.log('Parsed JSON data:', data);

                        if (data.success) {
                            $('#addSupplierModal').modal('hide');
                            alert('Supplier added successfully!');
                            // Optionally reload the page to show updated data
                            location.reload();
                        } else {
                            alert(data.error || data.message || 'Failed to add supplier.');
                        }
                    } catch (parseError) {
                        console.error('JSON parsing error:', parseError);
                        console.log('Response was not valid JSON');
                        alert('Server returned invalid response.');
                    }
                })
                .catch(error => {
                    console.error('Complete error details:', error);
                    console.log('Error name:', error.name);
                    console.log('Error message:', error.message);
                    console.log('Error stack:', error.stack);
                    alert(`Network error while adding supplier: ${error.message}`);
                })
                .finally(() => {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = originalText;
                });
        };

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Deactivate product via POST to controller (confirm first)
        window.deactivateProduct = function (id) {
            if (!id) return;
            if (!confirm('Deactivate this product? It will be hidden from the catalog.')) return;

            fetch(URLROOT + '/products/deactivate/' + id, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(r => r.json())
                .then(j => {
                    if (j.success) {
                        location.reload();
                    } else {
                        alert(j.message || 'Failed to deactivate product');
                    }
                }).catch(err => {
                    console.error(err);
                    alert('Request failed');
                });
        };

        // Backwards-compatible alias for existing calls
        window.deleteProduct = function (id) { return window.deactivateProduct(id); };

        // select-all removed — no-op

        // Change items per page - simple reload with per_page param
        window.changeItemsPerPage = function () {
            const select = document.getElementById('itemsPerPage');
            if (!select) return;
            const val = select.value;
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', val);
            // reset to first page when changing page size
            url.searchParams.delete('page');
            window.location.href = url.toString();
        };

        // initializeProductTable fallback - attempt to initialize DataTable if present
        window.initializeProductTable = function () {
            try {
                if (typeof $ !== 'undefined' && $.fn && $.fn.DataTable) {
                    if (!$.fn.DataTable.isDataTable('#productsTable')) {
                        $('#productsTable').DataTable({ responsive: true, pageLength: 25 });
                    }
                }
            } catch (err) { console.warn('initializeProductTable failed', err); }
        };

        // Search functionality
        const searchInput = document.getElementById('productSearch');
        if (searchInput) {
            // Submit form on Enter key press
            searchInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const form = searchInput.closest('form');
                    if (form) {
                        form.submit();
                    }
                }
            });

            // Optional: Add debounce for auto-search (uncomment if desired)
            /*
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const form = searchInput.closest('form');
                    if (form) {
                        form.submit();
                    }
                }, 500); // Wait 500ms after user stops typing
            });
            */
        }

        // Handle dropdown z-index to prevent overlap with next row buttons
        document.addEventListener('DOMContentLoaded', function () {
            // Find all dropdown toggles in the products table
            const dropdownToggles = document.querySelectorAll('table#productsTable .dropdown-toggle');

            dropdownToggles.forEach(toggle => {
                const dropdown = toggle.closest('.dropdown');
                const tableRow = toggle.closest('tr');

                // When dropdown is shown
                toggle.addEventListener('click', function () {
                    setTimeout(() => {
                        if (dropdown.classList.contains('show')) {
                            tableRow.style.zIndex = '20';
                        }
                    }, 10);
                });

                // Listen for Bootstrap dropdown events
                $(dropdown).on('shown.bs.dropdown', function () {
                    tableRow.style.zIndex = '20';
                });

                $(dropdown).on('hidden.bs.dropdown', function () {
                    tableRow.style.zIndex = '';
                });
            });

            // Also handle clicking outside to close dropdowns
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.dropdown')) {
                    const openRows = document.querySelectorAll('table#productsTable tr[style*="z-index"]');
                    openRows.forEach(row => {
                        row.style.zIndex = '';
                    });
                }
            });
        });
    })();
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>