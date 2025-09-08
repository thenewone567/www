<?php
require_once APPROOT . DS . 'app' . DS . 'helpers' . DS . 'view_helpers.php';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/app-unified.css">
<style>
    /* Product details column tweaks */
    .product-details .details-col-left td {
        text-align: right;
    }

    .product-details .details-col-left th {
        width: 140px;
        white-space: nowrap;
    }

    .product-details .details-col-right td.truncate {
        max-width: 160px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    @media (max-width: 767px) {
        .product-details .details-col-left td {
            text-align: left;
        }

        .product-details .details-col-left th {
            white-space: normal;
        }

        .product-details .details-col-right td.truncate {
            white-space: normal;
        }
    }
</style>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card theme-card-light shadow mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="mb-0"><i class="fas fa-eye"></i> Product Details</h3>
                    <div class="btn-group">
                        <a href="<?php echo URLROOT; ?>/products/edit/<?php echo isset($data['product']) && is_object($data['product']) ? $data['product']->product_id : ''; ?>"
                            class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit Product
                        </a>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
                                <i class="fas fa-plus"></i> Create
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?php echo URLROOT; ?>/purchases/add">
                                    <i class="fas fa-shopping-cart mr-2"></i> Purchase Order
                                </a>
                                <a class="dropdown-item" href="<?php echo URLROOT; ?>/inventory/receiving">
                                    <i class="fas fa-truck mr-2"></i> Receiving
                                </a>
                            </div>
                        </div>
                        <a href="<?php echo URLROOT; ?>/products" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($data['product']) && is_object($data['product'])):
                        $p = $data['product']; ?>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <?php if (!empty($p->image_path)): ?>
                                            <img src="<?php echo URLROOT; ?>/public/uploads/<?php echo htmlspecialchars($p->image_path); ?>"
                                                alt="<?php echo htmlspecialchars($p->product_name); ?>" class="img-fluid"
                                                style="max-height:220px; object-fit:contain">
                                        <?php else: ?>
                                            <i class="fas fa-box fa-3x text-muted"></i>
                                        <?php endif; ?>
                                        <h5 class="mt-3 text-truncate"><?php echo htmlspecialchars($p->product_name); ?>
                                        </h5>
                                        <p class="text-muted mb-0">SKU: <?php echo htmlspecialchars($p->sku ?? '-'); ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <h5 class="mb-3"><i class="fas fa-info-circle mr-2"></i> Details</h5>
                                <div class="row product-details">
                                    <div class="col-md-4 details-col-left">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <th style="width:140px">Brand</th>
                                                    <td><?php echo !empty($p->brand_name) ? htmlspecialchars($p->brand_name) : '<span class="badge badge-light">No Brand</span>'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Category</th>
                                                    <td><?php echo !empty($p->category_name) ? htmlspecialchars($p->category_name) : '-'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Unit</th>
                                                    <td><?php echo !empty($p->unit_name) ? htmlspecialchars($p->unit_name) : '-'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Current Stock</th>
                                                    <td><?php echo isset($p->current_inventory) ? (int) $p->current_inventory : '-'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Reorder Level</th>
                                                    <td><?php echo isset($p->reorder_level) ? (int) $p->reorder_level : '-'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Barcode</th>
                                                    <td><?php echo !empty($p->barcode) ? htmlspecialchars($p->barcode) : '-'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Model/Batch No.</th>
                                                    <td><?php echo !empty($p->model_number) ? htmlspecialchars($p->model_number) : '-'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Supplier Code</th>
                                                    <td><?php echo !empty($p->supplier_code) ? htmlspecialchars($p->supplier_code) : '-'; ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-4 details-col-center">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <th>Unit Qty</th>
                                                    <td><?php echo isset($p->unit_quantity) ? htmlspecialchars($p->unit_quantity) : '-'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Purchase Price</th>
                                                    <td><?php echo function_exists('formatCurrency') ? formatCurrency($p->purchase_price ?? 0, 2) : htmlspecialchars($p->purchase_price ?? '-'); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Current Avg Cost</th>
                                                    <td><?php echo function_exists('formatCurrency') ? formatCurrency($p->current_average_cost ?? 0, 2) : htmlspecialchars($p->current_average_cost ?? '-'); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Profit Margin</th>
                                                    <td>
                                                        <?php
                                                        // Use calculated profit margin from database if available
                                                        if (isset($p->calculated_profit_margin) && $p->calculated_profit_margin > 0) {
                                                            echo $p->calculated_profit_margin . '%';

                                                            // Show which cost was used for calculation
                                                            $current_avg_cost = floatval($p->current_average_cost ?? 0);
                                                            $cost_source = $current_avg_cost > 0 ? 'avg cost' : 'purchase price';
                                                            echo '<small class="text-muted ml-1">(using ' . $cost_source . ')</small>';
                                                        } else {
                                                            // Fallback to manual calculation
                                                            $selling_price = floatval($p->selling_price ?? 0);
                                                            $current_avg_cost = floatval($p->current_average_cost ?? 0);
                                                            $purchase_price = floatval($p->purchase_price ?? 0);

                                                            // Use current_average_cost if available, otherwise use purchase_price
                                                            $cost_base = $current_avg_cost > 0 ? $current_avg_cost : $purchase_price;

                                                            if ($selling_price > 0 && $cost_base > 0) {
                                                                $calculated_margin = (($selling_price - $cost_base) / $selling_price) * 100;
                                                                echo number_format($calculated_margin, 2) . '%';

                                                                // Show which cost was used for calculation
                                                                $cost_source = $current_avg_cost > 0 ? 'avg cost' : 'purchase price';
                                                                echo '<small class="text-muted ml-1">(using ' . $cost_source . ')</small>';
                                                            } else {
                                                                echo isset($p->profit_margin) ? (float) $p->profit_margin . '%' : '-';
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>GST Rate</th>
                                                    <td><?php echo isset($p->gst_rate) ? (float) $p->gst_rate . '%' : '-'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Selling Price</th>
                                                    <td><?php echo function_exists('formatCurrency') ? formatCurrency($p->selling_price ?? 0, 2) : htmlspecialchars($p->selling_price ?? '-'); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Suppliers</th>
                                                    <td><?php echo isset($p->supplier_count) ? (int) $p->supplier_count : '0'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Last Ordered</th>
                                                    <td><?php echo !empty($p->last_ordered_date) ? date('M d, Y', strtotime($p->last_ordered_date)) : '-'; ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-4 details-col-right">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <th>Weight</th>
                                                    <td><?php echo !empty($p->weight) ? htmlspecialchars($p->weight) : '-'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Dimensions</th>
                                                    <td>
                                                        <?php
                                                        if (!empty($p->dimensions)) {
                                                            $dimensions = json_decode($p->dimensions, true);
                                                            if ($dimensions && is_array($dimensions)) {
                                                                $parts = [];
                                                                if (!empty($dimensions['length'])) {
                                                                    $parts[] = 'L: ' . $dimensions['length'] . ($dimensions['length_unit'] ?? 'cm');
                                                                }
                                                                if (!empty($dimensions['width'])) {
                                                                    $parts[] = 'W: ' . $dimensions['width'] . ($dimensions['width_unit'] ?? 'cm');
                                                                }
                                                                if (!empty($dimensions['height'])) {
                                                                    $parts[] = 'H: ' . $dimensions['height'] . ($dimensions['height_unit'] ?? 'cm');
                                                                }
                                                                echo !empty($parts) ? htmlspecialchars(implode(' × ', $parts)) : '-';
                                                            } else {
                                                                echo '-';
                                                            }
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Warranty</th>
                                                    <td><?php echo !empty($p->warranty_period) ? htmlspecialchars($p->warranty_period) : '-'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Has Expiry</th>
                                                    <td><?php echo isset($p->has_expiry) ? (($p->has_expiry) ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>') : '-'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Expiry (mo/yr)</th>
                                                    <td><?php echo (isset($p->expiry_months) && $p->expiry_months) || (isset($p->expiry_years) && $p->expiry_years) ? (intval($p->expiry_months) . ' mo / ' . intval($p->expiry_years) . ' yr') : '-'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Has Warranty</th>
                                                    <td><?php echo isset($p->has_warranty) ? (($p->has_warranty) ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>') : '-'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Storage</th>
                                                    <td><?php echo !empty($p->storage_location) ? htmlspecialchars($p->storage_location) : '-'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Status</th>
                                                    <td><?php echo !empty($p->product_status) ? htmlspecialchars($p->product_status) : '-'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Active</th>
                                                    <td class="truncate">
                                                        <?php echo isset($p->is_active) ? ($p->is_active ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>') : '-'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Deleted At</th>
                                                    <td class="truncate">
                                                        <?php echo !empty($p->deleted_at) ? date('M d, Y H:i', strtotime($p->deleted_at)) : '-'; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Deleted By</th>
                                                    <td class="truncate">
                                                        <?php echo !empty($p->deleted_by) ? htmlspecialchars($p->deleted_by) : '-'; ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Linked Suppliers -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0"><i class="fas fa-truck mr-2"></i> Linked Suppliers</h5>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="openAddSupplierModal()">
                                        <i class="fas fa-plus mr-1"></i> Add Supplier
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Supplier</th>
                                                <th>Contact</th>
                                                <th>Phone</th>
                                                <th>Purchase Price</th>
                                                <th>Email</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="linkedSuppliersTbody">
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">Loading linked suppliers...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <script>
                            // global helper used by multiple inline scripts
                            function escapeHtml(str) {
                                if (str === null || str === undefined) return '';
                                return String(str).replace(/[&<>"']/g, function (s) {
                                    return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[s];
                                });
                            }

                            function loadLinkedSuppliers() {
                                const tbody = document.getElementById('linkedSuppliersTbody');
                                const productId = '<?php echo htmlspecialchars(safeProperty($p ?? null, "product_id", "")); ?>';
                                if (!productId) {
                                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No product specified</td></tr>';
                                    return;
                                }

                                const baseUrl = (window.URLROOT && window.URLROOT.length) ? window.URLROOT.replace(/\/$/, '') : '<?php echo URLROOT; ?>'.replace(/\/$/, '');
                                fetch(`${baseUrl}/products/getProductSuppliers/${productId}`)
                                    .then(async res => {
                                        const ct = res.headers.get('content-type') || '';
                                        const text = await res.text();
                                        try {
                                            const data = ct.includes('application/json') ? JSON.parse(text) : null;
                                            if (!data || !data.success) {
                                                const msg = data && data.error ? data.error : (data && data.message ? data.message : text || 'No linked suppliers');
                                                tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">${escapeHtml(String(msg).substring(0, 160))}</td></tr>`;
                                                console.error('getProductSuppliers returned error:', data, 'raw:', text);
                                                return;
                                            }
                                            return data.suppliers || [];
                                        } catch (err) {
                                            console.error('Failed to parse suppliers JSON:', err, 'raw response:', text);
                                            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">Error loading suppliers: ${escapeHtml(String(text).substring(0, 160))}</td></tr>`;
                                            return null;
                                        }
                                    })
                                    .then(suppliers => {
                                        if (!suppliers) return;
                                        if (!Array.isArray(suppliers) || suppliers.length === 0) {
                                            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No linked suppliers</td></tr>';
                                            return;
                                        }

                                        tbody.innerHTML = '';
                                        suppliers.forEach(s => {
                                            const tr = document.createElement('tr');
                                            const supplierName = s.supplier_name || '-';
                                            const contact = s.contact_person || '-';
                                            const phone = s.phone || '-';
                                            const email = s.email || '-';
                                            const sid = s.supplier_id || '';
                                            const psid = s.ps_id || '';
                                            // store link metadata on the row for inline editing
                                            tr.dataset.psId = psid;
                                            tr.dataset.productId = productId;
                                            tr.dataset.supplierNameForProduct = s.supplier_name_for_product || '';
                                            tr.dataset.purchasePrice = s.purchase_price || '';

                                            tr.innerHTML = `
                                                <td class="cell-supplier">${escapeHtml(supplierName)}</td>
                                                <td class="cell-contact">${escapeHtml(contact)}</td>
                                                <td class="cell-phone">${escapeHtml(phone)}</td>
                                                <td class="cell-purchase-price">${escapeHtml(s.purchase_price_display || s.purchase_price || '-')}</td>
                                                <td class="cell-email">${escapeHtml(email)}</td>
                                                <td class="cell-actions">
                                                    <a href="${baseUrl}/suppliers/view/${sid}" class="btn btn-outline-primary btn-sm" type="button" title="View Supplier"><i class="fas fa-eye"></i></a>
                                                    <button class="btn btn-outline-warning btn-sm btn-edit-link" type="button" title="Quick Edit" onclick="toggleInlineEdit(this)"><i class="fas fa-edit"></i></button>
                                                    <button class="btn btn-outline-danger btn-sm btn-unlink" type="button" title="Unlink Supplier" onclick="confirmUnlinkInline(this)"><i class="fas fa-unlink"></i></button>
                                                </td>
                                            `;
                                            tbody.appendChild(tr);
                                        });
                                    })
                                    .catch(err => {
                                        console.error('Error fetching suppliers', err);
                                        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Error loading suppliers</td></tr>';
                                    });
                            }

                            document.addEventListener('DOMContentLoaded', function () {
                                // Load suppliers when page loads
                                loadLinkedSuppliers();

                                function escapeHtml(str) {
                                    if (!str) return '';
                                    return String(str).replace(/[&<>"']/g, function (s) {
                                        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": "&#39;" })[s];
                                    });
                                }
                            });
                        </script>
                        <script>
                            // Inline edit and unlink handlers (no modals)
                            function toggleInlineEdit(btn) {
                                const tr = btn.closest('tr');
                                if (!tr) return;
                                // If already editing, do nothing
                                if (tr.classList.contains('editing')) return;
                                tr.classList.add('editing');
                                const psId = tr.dataset.psId || '';
                                const productId = tr.dataset.productId || '';
                                const currentPrice = tr.dataset.purchasePrice || '';

                                // target only the purchase price cell
                                const priceCell = tr.querySelector('.cell-purchase-price');
                                const actionsCell = tr.querySelector('.cell-actions');

                                // create small inline input for price
                                const priceInput = document.createElement('input');
                                priceInput.type = 'number';
                                priceInput.step = '0.01';
                                priceInput.className = 'form-control form-control-sm';
                                priceInput.value = currentPrice;

                                // store original contents
                                priceCell.dataset.orig = priceCell.innerHTML;
                                actionsCell.dataset.orig = actionsCell.innerHTML;

                                priceCell.innerHTML = '';
                                priceCell.appendChild(priceInput);

                                // replace actions with Save/Cancel
                                actionsCell.innerHTML = `
                                                    <button class="btn btn-success btn-sm" type="button" onclick="saveInlineEdit(this)">Save</button>
                                                    <button class="btn btn-secondary btn-sm" type="button" onclick="cancelInlineEdit(this)">Cancel</button>
                                                `;
                            }

                            function cancelInlineEdit(btn) {
                                const tr = btn.closest('tr');
                                if (!tr) return;
                                const priceCell = tr.querySelector('.cell-purchase-price');
                                const actionsCell = tr.querySelector('.cell-actions');
                                priceCell.innerHTML = priceCell.dataset.orig || '';
                                actionsCell.innerHTML = actionsCell.dataset.orig || '';
                                tr.classList.remove('editing');
                            }

                            function saveInlineEdit(btn) {
                                const tr = btn.closest('tr');
                                if (!tr) return;
                                const psId = tr.dataset.psId || '';
                                const productId = tr.dataset.productId || '';
                                const priceInput = tr.querySelector('.cell-purchase-price input');
                                const purchasePrice = priceInput ? priceInput.value : '';

                                const formData = new FormData();
                                formData.append('ps_id', psId);
                                formData.append('product_id', productId);
                                formData.append('purchase_price', purchasePrice);

                                const baseUrl = (window.URLROOT && window.URLROOT.length) ? window.URLROOT.replace(/\/$/, '') : '<?php echo URLROOT; ?>'.replace(/\/$/, '');
                                fetch(`${baseUrl}/suppliers/linkProduct`, {
                                    method: 'POST',
                                    body: formData,
                                    credentials: 'same-origin',
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                }).then(r => r.text()).then(text => {
                                    let resp = null;
                                    try {
                                        resp = text ? JSON.parse(text) : null;
                                    } catch (e) {
                                        console.error('Non-JSON response from linkProduct:', text);
                                        // Show the server response as an error to help debugging
                                        alert('Server response: ' + String(text).substring(0, 400));
                                        tr.classList.remove('editing');
                                        return;
                                    }

                                    if (resp && resp.success) {
                                        // update row dataset and display using server-provided formatted price if available
                                        tr.dataset.purchasePrice = resp.updated_price_raw !== undefined ? String(resp.updated_price_raw) : purchasePrice;
                                        const priceCell = tr.querySelector('.cell-purchase-price');
                                        const actionsCell = tr.querySelector('.cell-actions');
                                        const displayPrice = resp.updated_price || resp.updated_price_formatted || purchasePrice || tr.dataset.purchasePrice || '-';
                                        priceCell.innerHTML = escapeHtml(displayPrice);
                                        actionsCell.innerHTML = actionsCell.dataset.orig || actionsCell.innerHTML;
                                        tr.classList.remove('editing');
                                    } else {
                                        alert(resp && (resp.message || resp.error) ? (resp.message || resp.error) : 'Failed to update link');
                                    }
                                }).catch(err => {
                                    console.error('Error updating link', err);
                                    alert('Network error while updating link: ' + (err && err.message ? err.message : String(err)));
                                    tr.classList.remove('editing');
                                });
                            }

                            function confirmUnlinkInline(btn) {
                                const tr = btn.closest('tr');
                                if (!tr) return;
                                const psId = tr.dataset.psId || '';
                                const productId = tr.dataset.productId || '';
                                const supplierId = tr.querySelector('a[href*="/suppliers/view/"]') ? (tr.querySelector('a[href*="/suppliers/view/"]').getAttribute('href').split('/').pop()) : '';
                                if (!confirm('Remove this supplier from the product?')) return;
                                const formData = new FormData();
                                formData.append('product_id', productId);
                                formData.append('supplier_id', supplierId);
                                const baseUrl = (window.URLROOT && window.URLROOT.length) ? window.URLROOT.replace(/\/$/, '') : '<?php echo URLROOT; ?>'.replace(/\/$/, '');
                                fetch(`${baseUrl}/products/unlinkSupplier`, {
                                    method: 'POST',
                                    body: formData,
                                    credentials: 'same-origin'
                                }).then(r => r.json()).then(resp => {
                                    if (resp && resp.success) {
                                        // remove row
                                        tr.parentNode.removeChild(tr);
                                    } else {
                                        alert(resp.message || resp.error || 'Failed to unlink');
                                    }
                                }).catch(err => {
                                    console.error('Error unlinking supplier', err);
                                    alert('Network error while unlinking');
                                });
                            }
                        </script>

                        <!-- Add Supplier Modal -->
                        <div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog"
                            aria-labelledby="addSupplierModalLabel" aria-hidden="true">
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
                                        <div class="form-group">
                                            <label for="supplierSearch">Search Suppliers</label>
                                            <input type="text" class="form-control" id="supplierSearch"
                                                placeholder="Type supplier name to search..."
                                                onkeyup="searchSuppliers(this.value)">
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
                                                <label for="purchasePrice">Purchase Price <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">$</span>
                                                    </div>
                                                    <input type="number" class="form-control" id="purchasePrice"
                                                        placeholder="0.00" min="0.01" step="0.01" required>
                                                </div>
                                                <small class="form-text text-muted">Enter the purchase price for this
                                                    product from this supplier</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary" id="confirmAddSupplier"
                                            onclick="confirmAddSupplier()" disabled>
                                            <i class="fas fa-plus mr-1"></i> Add Supplier
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            let selectedSupplierId = null;
                            let searchTimeout = null;

                            function openAddSupplierModal() {
                                $('#addSupplierModal').modal('show');
                                document.getElementById('supplierSearch').value = '';
                                clearSelectedSupplier();
                                resetSearchResults();
                            }

                            function resetSearchResults() {
                                document.getElementById('supplierSearchResults').innerHTML = `
                                    <div class="text-center text-muted py-3">
                                        <i class="fas fa-search fa-2x mb-2"></i>
                                        <p>Start typing to search for suppliers</p>
                                    </div>
                                `;
                            }

                            function searchSuppliers(query) {
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
                                    const baseUrl = (window.URLROOT && window.URLROOT.length) ? window.URLROOT.replace(/\/$/, '') : '<?php echo URLROOT; ?>'.replace(/\/$/, '');

                                    fetch(`${baseUrl}/api/getSuppliers.php?search=${encodeURIComponent(query.trim())}`)
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
                            }

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

                            function selectSupplier(supplierId, supplierName) {
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
                            }

                            function clearSelectedSupplier() {
                                selectedSupplierId = null;
                                document.getElementById('selectedSupplier').style.display = 'none';
                                document.getElementById('confirmAddSupplier').disabled = true;
                                document.getElementById('purchasePrice').value = '';
                                resetSearchResults();
                            }

                            function confirmAddSupplier() {
                                if (!selectedSupplierId) {
                                    alert('Please select a supplier first.');
                                    return;
                                }

                                const productId = <?php echo isset($data['product']) && is_object($data['product']) ? $data['product']->product_id : 0; ?>;
                                if (!productId) {
                                    alert('Product ID not found.');
                                    return;
                                }

                                // Validate purchase price
                                const purchasePrice = document.getElementById('purchasePrice').value;
                                if (!purchasePrice || parseFloat(purchasePrice) <= 0) {
                                    alert('Please enter a valid purchase price.');
                                    document.getElementById('purchasePrice').focus();
                                    return;
                                }

                                const baseUrl = (window.URLROOT && window.URLROOT.length) ? window.URLROOT.replace(/\/$/, '') : '<?php echo URLROOT; ?>'.replace(/\/$/, '');
                                const formData = new FormData();
                                formData.append('product_id', productId);
                                formData.append('supplier_id', selectedSupplierId);
                                formData.append('purchase_price', purchasePrice);

                                // Disable button and show loading
                                const confirmBtn = document.getElementById('confirmAddSupplier');
                                const originalText = confirmBtn.innerHTML;
                                confirmBtn.disabled = true;
                                confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Adding...';

                                fetch(`${baseUrl}/products/linkSupplier`, {
                                    method: 'POST',
                                    body: formData,
                                    credentials: 'same-origin'
                                })
                                    .then(response => {
                                        console.log('Response status:', response.status);
                                        console.log('Response headers:', response.headers);

                                        if (!response.ok) {
                                            throw new Error(`HTTP error! status: ${response.status}`);
                                        }

                                        return response.text().then(text => {
                                            console.log('Raw response:', text);
                                            try {
                                                return JSON.parse(text);
                                            } catch (e) {
                                                console.error('JSON parse error:', e);
                                                throw new Error(`Invalid JSON response: ${text.substring(0, 100)}`);
                                            }
                                        });
                                    })
                                    .then(data => {
                                        console.log('Parsed data:', data);
                                        if (data.success) {
                                            $('#addSupplierModal').modal('hide');
                                            // Reload the suppliers list
                                            loadLinkedSuppliers();
                                            alert('Supplier added successfully!');
                                        } else {
                                            alert(data.error || data.message || 'Failed to add supplier.');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error adding supplier:', error);
                                        alert(`Network error while adding supplier: ${error.message}`);
                                    })
                                    .finally(() => {
                                        confirmBtn.disabled = false;
                                        confirmBtn.innerHTML = originalText;
                                    });
                            }

                            function escapeHtml(text) {
                                const div = document.createElement('div');
                                div.textContent = text;
                                return div.innerHTML;
                            }
                        </script>
                        <script>
                            // Set global URLROOT for AJAX calls
                            window.URLROOT = '<?php echo URLROOT; ?>';
                        </script>
                        <?php
                    endif;
                    require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php';
                    ?>