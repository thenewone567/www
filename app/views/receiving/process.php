<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container theme-unified">
    <!-- Header Section -->
    <div class="row align-items-center theme-header mb-4">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 m-0">
                    <li class="breadcrumb-item">
                        <a href="<?php echo URLROOT; ?>/dashboard" class="text-decoration-none">
                            <i class="fa-solid fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?php echo URLROOT; ?>/receiving" class="text-decoration-none">
                            <i class="fa-solid fa-dolly"></i> Receiving
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?php echo URLROOT; ?>/receiving/pending" class="text-decoration-none">
                            Pending
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Process Receipt
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h1 class="theme-page-title">
                <i class="fa-solid fa-dolly theme-icon"></i>
                Process Receipt
            </h1>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php flash('receive_message'); ?>

    <?php if (isset($data['purchase']) && $data['purchase']): ?>
    <!-- Purchase Order Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="theme-card">
                <div class="theme-card-header">
                    <h5 class="theme-card-title">
                        <i class="fa-solid fa-file-invoice"></i> Purchase Order Information
                    </h5>
                    <div class="theme-card-actions">
                        <span class="theme-badge theme-badge-primary">
                            PO #<?php echo $data['purchase']->purchase_number ?? $data['purchase']->id; ?>
                        </span>
                    </div>
                </div>
                <div class="theme-card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="theme-info-group">
                                <label class="theme-info-label">Supplier</label>
                                <div class="theme-info-value">
                                    <i class="fa-solid fa-building text-muted me-2"></i>
                                    <?php echo htmlspecialchars($data['purchase']->supplier_name ?? 'Unknown'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="theme-info-group">
                                <label class="theme-info-label">Order Date</label>
                                <div class="theme-info-value">
                                    <i class="fa-solid fa-calendar text-muted me-2"></i>
                                    <?php echo date('M j, Y', strtotime($data['purchase']->created_at)); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="theme-info-group">
                                <label class="theme-info-label">Expected Date</label>
                                <div class="theme-info-value">
                                    <i class="fa-solid fa-clock text-muted me-2"></i>
                                    <?php if (isset($data['purchase']->expected_date) && $data['purchase']->expected_date): ?>
                                        <?php echo date('M j, Y', strtotime($data['purchase']->expected_date)); ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not specified</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="theme-info-group">
                                <label class="theme-info-label">Total Value</label>
                                <div class="theme-info-value">
                                    <i class="fa-solid fa-rupee-sign text-success me-2"></i>
                                    <strong class="text-success">
                                        ₹<?php echo number_format($data['purchase']->total_amount ?? 0, 2); ?>
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receiving Form -->
    <form id="receivingForm" method="POST" action="<?php echo URLROOT; ?>/receiving/save">
        <input type="hidden" name="purchase_id" value="<?php echo $data['purchase']->id; ?>">
        
        <!-- Receipt Information -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="theme-card">
                    <div class="theme-card-header">
                        <h5 class="theme-card-title">
                            <i class="fa-solid fa-truck"></i> Receipt Information
                        </h5>
                    </div>
                    <div class="theme-card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="receipt_date" class="form-label">Receipt Date <span class="text-danger">*</span></label>
                                    <input type="date" id="receipt_date" name="receipt_date" class="form-control theme-form-control"
                                           value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="receipt_reference" class="form-label">Receipt Reference</label>
                                    <input type="text" id="receipt_reference" name="receipt_reference" 
                                           class="form-control theme-form-control" placeholder="Invoice/Bill number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="delivery_note" class="form-label">Delivery Note</label>
                                    <input type="text" id="delivery_note" name="delivery_note" 
                                           class="form-control theme-form-control" placeholder="Delivery note number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="received_by" class="form-label">Received By</label>
                                    <input type="text" id="received_by" name="received_by" 
                                           class="form-control theme-form-control" 
                                           value="<?php echo $_SESSION['user_name'] ?? ''; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Receipt Notes</label>
                                    <textarea id="notes" name="notes" class="form-control theme-form-control" 
                                              rows="3" placeholder="Any notes about the receipt..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="col-lg-4">
                <div class="theme-card">
                    <div class="theme-card-header">
                        <h5 class="theme-card-title">
                            <i class="fa-solid fa-bolt"></i> Quick Actions
                        </h5>
                    </div>
                    <div class="theme-card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn theme-btn-success" onclick="receiveAll()">
                                <i class="fa-solid fa-check-double"></i> Receive All Items
                            </button>
                            <button type="button" class="btn theme-btn-warning" onclick="scanBarcode()">
                                <i class="fa-solid fa-qrcode"></i> Scan Barcode
                            </button>
                            <button type="button" class="btn theme-btn-info" onclick="printLabels()">
                                <i class="fa-solid fa-print"></i> Print Labels
                            </button>
                            <hr class="theme-divider">
                            <div class="theme-status-summary">
                                <div class="theme-status-item">
                                    <span class="theme-status-label">Total Items:</span>
                                    <span class="theme-status-value" id="totalItems">0</span>
                                </div>
                                <div class="theme-status-item">
                                    <span class="theme-status-label">Received:</span>
                                    <span class="theme-status-value theme-text-success" id="receivedItems">0</span>
                                </div>
                                <div class="theme-status-item">
                                    <span class="theme-status-label">Pending:</span>
                                    <span class="theme-status-value theme-text-warning" id="pendingItems">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items to Receive -->
        <div class="row">
            <div class="col-12">
                <div class="theme-card">
                    <div class="theme-card-header">
                        <h5 class="theme-card-title">
                            <i class="fa-solid fa-boxes"></i> Items to Receive
                        </h5>
                        <div class="theme-card-actions">
                            <div class="theme-search-box">
                                <input type="text" id="itemSearch" class="form-control form-control-sm" 
                                       placeholder="Search items..." onkeyup="filterItems()">
                                <i class="fa-solid fa-search theme-search-icon"></i>
                            </div>
                        </div>
                    </div>
                    <div class="theme-card-body">
                        <?php if (isset($data['purchase_items']) && !empty($data['purchase_items'])): ?>
                            <div class="theme-table-container">
                                <table class="table theme-table" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th width="40">
                                                <input type="checkbox" id="selectAllItems" onchange="toggleSelectAllItems()">
                                            </th>
                                            <th>Product</th>
                                            <th>SKU</th>
                                            <th>Ordered Qty</th>
                                            <th>Received Qty</th>
                                            <th>Remaining</th>
                                            <th>Receive Now</th>
                                            <th>Unit Price</th>
                                            <th>Location</th>
                                            <th>Condition</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['purchase_items'] as $index => $item): ?>
                                            <tr class="item-row" data-product-name="<?php echo strtolower($item->product_name ?? ''); ?>">
                                                <td>
                                                    <input type="checkbox" class="item-select" name="items[<?php echo $index; ?>][selected]" value="1">
                                                    <input type="hidden" name="items[<?php echo $index; ?>][purchase_item_id]" value="<?php echo $item->id; ?>">
                                                    <input type="hidden" name="items[<?php echo $index; ?>][product_id]" value="<?php echo $item->product_id; ?>">
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if (isset($item->product_image) && $item->product_image): ?>
                                                            <img src="<?php echo URLROOT; ?>/uploads/products/<?php echo $item->product_image; ?>" 
                                                                 class="theme-product-thumb me-2" alt="Product">
                                                        <?php else: ?>
                                                            <div class="theme-product-thumb theme-product-thumb-placeholder me-2">
                                                                <i class="fa-solid fa-box"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <div class="fw-medium">
                                                                <?php echo htmlspecialchars($item->product_name ?? 'Unknown Product'); ?>
                                                            </div>
                                                            <?php if (isset($item->product_description)): ?>
                                                                <small class="text-muted">
                                                                    <?php echo htmlspecialchars(substr($item->product_description, 0, 50)); ?>
                                                                    <?php if (strlen($item->product_description) > 50) echo '...'; ?>
                                                                </small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <code class="theme-code">
                                                        <?php echo htmlspecialchars($item->sku ?? 'N/A'); ?>
                                                    </code>
                                                </td>
                                                <td>
                                                    <span class="theme-badge theme-badge-info">
                                                        <?php echo number_format($item->quantity ?? 0, 2); ?>
                                                        <small><?php echo $item->unit ?? ''; ?></small>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="theme-badge theme-badge-success">
                                                        <?php echo number_format($item->received_quantity ?? 0, 2); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php $remaining = ($item->quantity ?? 0) - ($item->received_quantity ?? 0); ?>
                                                    <span class="theme-badge <?php echo $remaining > 0 ? 'theme-badge-warning' : 'theme-badge-success'; ?>">
                                                        <?php echo number_format($remaining, 2); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" name="items[<?php echo $index; ?>][receive_quantity]" 
                                                               class="form-control theme-form-control receive-quantity" 
                                                               value="<?php echo $remaining; ?>" 
                                                               max="<?php echo $remaining; ?>" 
                                                               min="0" step="0.01"
                                                               onchange="updateReceiveTotals()">
                                                        <span class="input-group-text"><?php echo $item->unit ?? ''; ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-success fw-medium">
                                                        ₹<?php echo number_format($item->unit_price ?? 0, 2); ?>
                                                    </div>
                                                    <small class="text-muted">per <?php echo $item->unit ?? 'unit'; ?></small>
                                                </td>
                                                <td>
                                                    <select name="items[<?php echo $index; ?>][location_id]" class="form-select form-select-sm theme-form-control">
                                                        <option value="">Select Location</option>
                                                        <?php if (isset($data['locations'])): ?>
                                                            <?php foreach ($data['locations'] as $location): ?>
                                                                <option value="<?php echo $location->id; ?>">
                                                                    <?php echo htmlspecialchars($location->name); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="items[<?php echo $index; ?>][condition]" class="form-select form-select-sm theme-form-control">
                                                        <option value="good">Good</option>
                                                        <option value="damaged">Damaged</option>
                                                        <option value="expired">Expired</option>
                                                        <option value="defective">Defective</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="theme-empty-state">
                                <div class="theme-empty-icon">
                                    <i class="fa-solid fa-box-open"></i>
                                </div>
                                <h5 class="theme-empty-title">No Items Found</h5>
                                <p class="theme-empty-description">
                                    This purchase order doesn't have any items to receive.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="theme-action-bar theme-action-bar-sticky">
                    <div class="theme-action-group">
                        <button type="submit" name="action" value="partial" class="btn theme-btn-warning">
                            <i class="fa-solid fa-clock"></i> Save as Partial Receipt
                        </button>
                        <button type="submit" name="action" value="complete" class="btn theme-btn-success">
                            <i class="fa-solid fa-check-circle"></i> Complete Receipt
                        </button>
                    </div>
                    <div class="theme-action-group">
                        <a href="<?php echo URLROOT; ?>/receiving/pending" class="btn theme-btn-secondary">
                            <i class="fa-solid fa-times"></i> Cancel
                        </a>
                        <button type="button" class="btn theme-btn-outline-primary" onclick="previewReceipt()">
                            <i class="fa-solid fa-eye"></i> Preview
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php else: ?>
        <!-- Purchase Order Not Found -->
        <div class="row">
            <div class="col-12">
                <div class="theme-card">
                    <div class="theme-card-body">
                        <div class="theme-empty-state">
                            <div class="theme-empty-icon">
                                <i class="fa-solid fa-exclamation-triangle text-warning"></i>
                            </div>
                            <h5 class="theme-empty-title">Purchase Order Not Found</h5>
                            <p class="theme-empty-description">
                                The requested purchase order could not be found or may have been deleted.
                            </p>
                            <a href="<?php echo URLROOT; ?>/receiving/pending" class="btn theme-btn-primary">
                                <i class="fa-solid fa-arrow-left"></i> Back to Pending Receipts
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    updateReceiveTotals();
});

function updateReceiveTotals() {
    const totalItems = document.querySelectorAll('.item-row').length;
    let receivedItems = 0;
    
    document.querySelectorAll('.receive-quantity').forEach(input => {
        if (parseFloat(input.value) > 0) {
            receivedItems++;
        }
    });
    
    const pendingItems = totalItems - receivedItems;
    
    document.getElementById('totalItems').textContent = totalItems;
    document.getElementById('receivedItems').textContent = receivedItems;
    document.getElementById('pendingItems').textContent = pendingItems;
}

function toggleSelectAllItems() {
    const masterSelect = document.getElementById('selectAllItems');
    const itemSelects = document.querySelectorAll('.item-select');
    
    itemSelects.forEach(select => {
        select.checked = masterSelect.checked;
    });
}

function receiveAll() {
    if (confirm('Are you sure you want to mark all remaining items as received?')) {
        document.querySelectorAll('.receive-quantity').forEach(input => {
            const maxValue = parseFloat(input.getAttribute('max'));
            if (maxValue > 0) {
                input.value = maxValue;
            }
        });
        
        document.querySelectorAll('.item-select').forEach(checkbox => {
            checkbox.checked = true;
        });
        
        updateReceiveTotals();
    }
}

function filterItems() {
    const searchTerm = document.getElementById('itemSearch').value.toLowerCase();
    const rows = document.querySelectorAll('.item-row');
    
    rows.forEach(row => {
        const productName = row.getAttribute('data-product-name');
        if (productName.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function scanBarcode() {
    // Placeholder for barcode scanning functionality
    alert('Barcode scanning feature will be implemented soon.');
}

function printLabels() {
    const selectedItems = Array.from(document.querySelectorAll('.item-select:checked'));
    if (selectedItems.length === 0) {
        alert('Please select items to print labels for.');
        return;
    }
    
    // Open print labels page
    const itemIds = selectedItems.map(cb => cb.closest('tr').querySelector('[name*="product_id"]').value);
    const params = itemIds.map(id => `items[]=${id}`).join('&');
    window.open(`<?php echo URLROOT; ?>/receiving/print-labels?${params}`, '_blank');
}

function previewReceipt() {
    // Show preview modal or open preview page
    const form = document.getElementById('receivingForm');
    const formData = new FormData(form);
    formData.append('preview', '1');
    
    // Open preview in new tab
    const form2 = document.createElement('form');
    form2.method = 'POST';
    form2.action = '<?php echo URLROOT; ?>/receiving/preview';
    form2.target = '_blank';
    
    for (let [key, value] of formData.entries()) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form2.appendChild(input);
    }
    
    document.body.appendChild(form2);
    form2.submit();
    document.body.removeChild(form2);
}

// Form validation
document.getElementById('receivingForm').addEventListener('submit', function(e) {
    const receiveQuantities = document.querySelectorAll('.receive-quantity');
    let hasItems = false;
    
    receiveQuantities.forEach(input => {
        if (parseFloat(input.value) > 0) {
            hasItems = true;
        }
    });
    
    if (!hasItems) {
        e.preventDefault();
        alert('Please specify quantities to receive for at least one item.');
        return false;
    }
    
    const action = e.submitter.value;
    if (action === 'complete') {
        const remainingItems = Array.from(receiveQuantities).some(input => {
            const max = parseFloat(input.getAttribute('max'));
            const current = parseFloat(input.value);
            return max > current;
        });
        
        if (remainingItems) {
            if (!confirm('Some items will not be fully received. Are you sure you want to mark this as complete?')) {
                e.preventDefault();
                return false;
            }
        }
    }
});
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
