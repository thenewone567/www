<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/app.unified.css">

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
                        <div class="h4 mb-0"><?php echo count($data['products'] ?? []); ?></div>
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
                        <?php $totalInv = 0;
                        if (!empty($data['products'])) {
                            foreach ($data['products'] as $p) {
                                $totalInv += $p->current_inventory ?? 0;
                            }
                        } ?>
                        <div class="h4 mb-0"><?php echo number_format($totalInv); ?></div>
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
                            <?php echo isset($data['avg_margin']) ? number_format($data['avg_margin'], 1) . '%' : '—'; ?>
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
                        <?php $reorderCount = 0;
                        if (!empty($data['products'])) {
                            foreach ($data['products'] as $p) {
                                $ri = $p->reorder_level ?? 10;
                                if (($p->current_inventory ?? 0) <= $ri)
                                    $reorderCount++;
                            }
                        } ?>
                        <div class="h4 mb-0"><?php echo $reorderCount; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <div class="input-group">
                <input id="productSearch" name="search" class="form-control" placeholder="Search products..."
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary"
                        onclick="document.getElementById('productSearch').dispatchEvent(new Event('input'))"><i
                            class="fas fa-search"></i></button>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-md-right">
            <div class="btn-group mr-2">
                <button class="btn btn-outline-primary" onclick="showAllColumns()"><i
                        class="fas fa-expand mr-1"></i>Show All</button>
                <button class="btn btn-outline-secondary" onclick="resetColumnView()"><i
                        class="fas fa-eye-slash mr-1"></i>Default View</button>
            </div>
            <select id="itemsPerPage" class="form-control d-inline-block" style="width:120px;"
                onchange="changeItemsPerPage();">
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    <!-- Table and column toggles -->
    <div class="card-theme">
        <div class="card-body p-2 border-bottom">
            <div class="d-flex align-items-center flex-wrap">
                <small class="text-muted mr-2">Columns:</small>
                <div class="btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-sm btn-outline-secondary mr-1 mb-1" style="cursor: default;" disabled><input
                            type="checkbox" checked disabled>Image</label>
                    <label class="btn btn-sm btn-outline-secondary mr-1 mb-1" style="cursor: default;" disabled><input
                            type="checkbox" checked disabled>Product</label>
                    <label class="btn btn-sm btn-outline-info active mr-1 mb-1 column-toggle" data-column="brand"><input
                            type="checkbox" checked>Brand</label>
                    <label class="btn btn-sm btn-outline-info active mr-1 mb-1 column-toggle"
                        data-column="supplier"><input type="checkbox" checked>Supplier</label>
                    <label class="btn btn-sm btn-outline-info active mr-1 mb-1 column-toggle" data-column="unit"><input
                            type="checkbox" checked>Unit</label>
                    <label class="btn btn-sm btn-outline-info active mr-1 mb-1 column-toggle"
                        data-column="margin"><input type="checkbox" checked>Margin</label>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="productsTable" class="table table-hover table-layout-fixed mb-0">
                <thead class="thead-light">
                    <tr>
                        <th style="width:40px"><input id="selectAll" type="checkbox" onchange="toggleSelectAll()"></th>
                        <th style="width:80px">Image</th>
                        <th>Product</th>
                        <th style="width:120px">SKU</th>
                        <th class="column-brand" style="width:120px">Brand</th>
                        <th style="width:120px">Category</th>
                        <th style="width:100px">Stock</th>
                        <th class="column-margin" style="width:90px">Margin</th>
                        <th class="column-supplier" style="width:140px">Supplier</th>
                        <th class="column-unit" style="width:80px">Unit</th>
                        <th style="width:120px">Price</th>
                        <th style="width:140px">Actions</th>
                    </tr>
                </thead>
                <tbody id="productsTableBody">
                    <?php if (!empty($data['products'])):
                        foreach ($data['products'] as $product): ?>
                            <tr>
                                <td><input class="product-checkbox" type="checkbox" value="<?php echo $product->product_id; ?>">
                                </td>
                                <td>
                                    <?php if (!empty($product->image_path)): ?>
                                        <img src="<?php echo URLROOT; ?>/public/uploads/<?php echo $product->image_path; ?>"
                                            style="width:48px;height:48px;object-fit:cover;border-radius:4px;">
                                    <?php else: ?>
                                        <img src="<?php echo URLROOT; ?>/public/images/products/default.jpg"
                                            style="width:48px;height:48px;object-fit:cover;border-radius:4px;">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($product->product_name); ?></strong>
                                    <?php if (!empty($product->model_number)): ?>
                                        <div class="text-muted small">Model: <?php echo htmlspecialchars($product->model_number); ?>
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
                                $cost = $product->primary_purchase_price ?? $product->unit_price ?? 0;
                                $margin = 0;
                                if ($selling > 0 && $cost > 0)
                                    $margin = (($selling - $cost) / $selling) * 100;
                                echo number_format($margin, 1) . '%';
                                ?></td>
                                <td class="column-supplier"><?php echo htmlspecialchars($product->supplier_name ?? '-'); ?></td>
                                <td class="column-unit"><?php echo htmlspecialchars($product->unit_name ?? '-'); ?></td>
                                <td><?php echo formatCurrency($product->selling_price ?? 0, 2); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="viewProduct(<?php echo $product->product_id; ?>)"><i
                                                class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-outline-warning"
                                            onclick="editProduct(<?php echo $product->product_id; ?>)"><i
                                                class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                            data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#"
                                                onclick="viewSuppliers(<?php echo $product->product_id; ?>)"><i
                                                    class="fas fa-users mr-1"></i>Suppliers</a>
                                            <a class="dropdown-item" href="#"
                                                onclick="linkSupplier(<?php echo $product->product_id; ?>, '<?php echo htmlspecialchars($product->product_name, ENT_QUOTES); ?>')"><i
                                                    class="fas fa-link mr-1"></i>Link Supplier</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item text-danger" href="#"
                                                onclick="deleteProduct(<?php echo $product->product_id; ?>)"><i
                                                    class="fas fa-trash mr-1"></i>Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="12" class="text-center py-5">No products found — <a
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
                        <?php echo $data['pagination']['total_records']; ?></small>
                <?php endif; ?>
            </div>
            <div>
                <?php if (!empty($data['pagination'])): ?>
                    <?php // render pagination links (existing code could be reused) ?>
                    <nav><?php echo $data['pagination']['links'] ?? ''; ?></nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<!-- Reuse the LinkSupplierModal we added earlier if present in the layout --><!-- Fallback modal here to ensure JS works -->
<div class="modal fade" id="LinkSupplierModal" tabindex="-1" role="dialog" aria-labelledby="LinkSupplierModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="LinkSupplierModalLabel">Link Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="link_product_id" value="">
                <h6 id="link_product_name"></h6>
                <div class="form-group">
                    <input type="text" id="supplier_search" class="form-control" placeholder="Search suppliers...">
                </div>
                <div id="existing_suppliers_section" style="display:none;"></div>
                <div id="suppliers_loading" style="display:none;" class="text-center py-3">
                    <div class="spinner-border text-primary" role="status"><span class="sr-only">Loading</span></div>
                </div>
                <div id="suppliers_list"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm_link_supplier" onclick="confirmLinkSupplier()"
                    disabled>Link Supplier</button>
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

            // Expose showAllColumns and resetColumnView globally for inline buttons
            window.showAllColumns = function () {
                toggles.forEach(btn => {
                    const col = btn.dataset.column;
                    const checkbox = btn.querySelector('input[type=checkbox]');
                    if (!col || !checkbox) return;
                    if (!checkbox.checked) {
                        checkbox.checked = true;
                        checkbox.dispatchEvent(new Event('change'));
                    }
                });
            };

            window.resetColumnView = function () {
                toggles.forEach(btn => {
                    const col = btn.dataset.column;
                    const checkbox = btn.querySelector('input[type=checkbox]');
                    if (!col || !checkbox) return;
                    const def = !!window.productsDefaultColumns[col];
                    if (checkbox.checked !== def) {
                        checkbox.checked = def;
                        checkbox.dispatchEvent(new Event('change'));
                    }
                });
            };

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

        // View suppliers for a product (simple redirect fallback)
        window.viewSuppliers = function (id) {
            if (!id) return;
            // If there's a dedicated suppliers view, redirect there; otherwise try modal
            if (window.location && URLROOT) {
                window.location.href = URLROOT + '/products/suppliers/' + id;
            }
        };

        // Open the Link Supplier modal and prefill product info
        window.linkSupplier = function (productId, productName) {
            try {
                document.getElementById('link_product_id').value = productId || '';
                document.getElementById('link_product_name').textContent = productName || '';
                // clear previous selections
                document.getElementById('supplier_search').value = '';
                document.getElementById('existing_suppliers_section').style.display = 'none';
                document.getElementById('suppliers_list').innerHTML = '';
                document.getElementById('confirm_link_supplier').disabled = true;
                $('#LinkSupplierModal').modal('show');
            } catch (err) {
                console.error('linkSupplier error', err);
            }
        };

        // Handler for confirm link supplier - expects an element #selected_supplier_id to be set by supplier search UI
        window.confirmLinkSupplier = function () {
            const pid = document.getElementById('link_product_id').value;
            const selected = document.getElementById('selected_supplier_id');
            const supplierId = selected ? selected.value : null;

            if (!pid || !supplierId) {
                alert('Please select a supplier to link.');
                return;
            }

            fetch(URLROOT + '/products/linkSupplier', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: pid, supplier_id: supplierId })
            })
                .then(r => r.json())
                .then(j => {
                    if (j.success) {
                        $('#LinkSupplierModal').modal('hide');
                        location.reload();
                    } else {
                        alert(j.message || 'Failed to link supplier');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('An error occurred while linking supplier');
                });
        };

        // Delete product via POST to controller (confirm first)
        window.deleteProduct = function (id) {
            if (!id) return;
            if (!confirm('Delete this product? This action cannot be undone.')) return;

            fetch(URLROOT + '/products/delete/' + id, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })
                .then(r => r.json())
                .then(j => {
                    if (j.success) {
                        // remove the row or reload
                        location.reload();
                    } else {
                        alert(j.message || 'Failed to delete product');
                    }
                }).catch(err => {
                    console.error(err);
                    alert('Request failed');
                });
        };

        // Toggle select all checkboxes on the products table
        window.toggleSelectAll = function () {
            try {
                const master = document.getElementById('selectAll');
                const checked = !!master.checked;
                document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = checked);
            } catch (err) { console.error(err); }
        };

        // Change items per page - simple reload with items param
        window.changeItemsPerPage = function () {
            const select = document.getElementById('itemsPerPage');
            if (!select) return;
            const val = select.value;
            const url = new URL(window.location.href);
            url.searchParams.set('items', val);
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
    })();
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>