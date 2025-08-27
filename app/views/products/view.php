<?php
require_once APPROOT . DS . 'app' . DS . 'helpers' . DS . 'view_helpers.php';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/app-unified.css">

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
                                <a class="dropdown-item"
                                    href="<?php echo URLROOT; ?>/purchases/add?supplier_id=<?php echo htmlspecialchars(safeProperty($data['product'] ?? null, 'primary_supplier_id', '')); ?>">
                                    <i class="fas fa-shopping-cart mr-2"></i> Purchase Order
                                </a>
                                <a class="dropdown-item"
                                    href="<?php echo URLROOT; ?>/inventory/receiving?supplier_id=<?php echo htmlspecialchars(safeProperty($data['product'] ?? null, 'primary_supplier_id', '')); ?>">
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
                                <table class="table table-sm table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <th style="width:160px">Brand</th>
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
                                            <td><?php echo isset($p->reorder_level) ? (int) $p->reorder_level : '-'; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Price</th>
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
                                        <tr>
                                            <th>Created At</th>
                                            <td><?php echo !empty($p->created_at) ? date('M d, Y', strtotime($p->created_at)) : '-'; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Updated At</th>
                                            <td><?php echo !empty($p->updated_at) ? date('M d, Y', strtotime($p->updated_at)) : (!empty($p->created_at) ? date('M d, Y', strtotime($p->created_at)) : '-'); ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Linked Suppliers -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h5 class="mb-3"><i class="fas fa-truck mr-2"></i> Linked Suppliers</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Supplier</th>
                                                <th>Contact</th>
                                                <th>Phone</th>
                                                <th>Purchase Price</th>
                                                <th>Email</th>
                                                <th>Primary</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="linkedSuppliersTbody">
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">Loading linked suppliers...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const tbody = document.getElementById('linkedSuppliersTbody');
                                const productId = '<?php echo htmlspecialchars(safeProperty($p ?? null, "product_id", "")); ?>';
                                if (!productId) {
                                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No product specified</td></tr>';
                                    return;
                                }

                                const baseUrl = (window.URLROOT && window.URLROOT.length) ? window.URLROOT : '<?php echo URLROOT; ?>';
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
                                            const primary = s.is_primary ? '<span class="badge badge-success">Yes</span>' : '';
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
                                                <td class="cell-purchase-price">${escapeHtml(s.purchase_price || '-')}</td>
                                                <td class="cell-email">${escapeHtml(email)}</td>
                                                <td class="cell-primary">${primary}</td>
                                                <td class="cell-actions">
                                                    <a href="${window.URLROOT}/suppliers/view/${sid}" class="btn btn-outline-primary btn-sm" type="button" title="View Supplier"><i class="fas fa-eye"></i></a>
                                                    <button class="btn btn-outline-warning btn-sm btn-edit-link" type="button" title="Quick Edit" onclick="toggleInlineEdit(this)"><i class="fas fa-edit"></i></button>
                                                    <button class="btn btn-outline-danger btn-sm btn-unlink" type="button" title="Unlink Supplier" onclick="confirmUnlinkInline(this)"><i class="fas fa-unlink"></i></button>
                                                </td>
                                            `;
                                            tbody.appendChild(tr);
                                        });
                                    })
                                    .catch(err => {
                                        console.error('Error fetching suppliers', err);
                                        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Error loading suppliers</td></tr>';
                                    });

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

                                const baseUrl = (window.URLROOT && window.URLROOT.length) ? window.URLROOT : '<?php echo URLROOT; ?>';
                                fetch(`${baseUrl}/suppliers/link`, {
                                    method: 'POST',
                                    body: formData,
                                    credentials: 'same-origin'
                                }).then(r => r.json()).then(resp => {
                                    if (resp && resp.success) {
                                        // update row dataset and display
                                        tr.dataset.purchasePrice = purchasePrice;
                                        const priceCell = tr.querySelector('.cell-purchase-price');
                                        const actionsCell = tr.querySelector('.cell-actions');
                                        priceCell.innerHTML = escapeHtml(purchasePrice || tr.dataset.purchasePrice || '-');
                                        actionsCell.innerHTML = actionsCell.dataset.orig || actionsCell.innerHTML;
                                        tr.classList.remove('editing');
                                    } else {
                                        alert(resp.message || resp.error || 'Failed to update link');
                                    }
                                }).catch(err => {
                                    console.error('Error updating link', err);
                                    alert('Network error while updating link');
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
                                const baseUrl = (window.URLROOT && window.URLROOT.length) ? window.URLROOT : '<?php echo URLROOT; ?>';
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
                        <?php
                    endif;
                    require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php';
                    ?>