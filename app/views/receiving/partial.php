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
                    <li class="breadcrumb-item active" aria-current="page">
                        Partial Receipts
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h1 class="theme-page-title">
                <i class="fa-solid fa-clock-rotate-left theme-icon"></i>
                Partial Receipts
            </h1>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php flash('receive_message'); ?>

    <!-- Filters and Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="theme-card">
                <div class="theme-card-header">
                    <h5 class="theme-card-title">
                        <i class="fa-solid fa-filter"></i> Filters & Actions
                    </h5>
                    <div class="theme-card-actions">
                        <button class="btn btn-sm theme-btn-outline-secondary" onclick="clearFilters()">
                            <i class="fa-solid fa-times"></i> Clear
                        </button>
                    </div>
                </div>
                <div class="theme-card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="supplier_filter" class="form-label">Supplier</label>
                            <select id="supplier_filter" name="supplier" class="form-select theme-form-control">
                                <option value="">All Suppliers</option>
                                <?php if (isset($data['suppliers'])): ?>
                                    <?php foreach ($data['suppliers'] as $supplier): ?>
                                        <option value="<?php echo $supplier->supplier_id; ?>" <?php echo (isset($_GET['supplier']) && $_GET['supplier'] == $supplier->supplier_id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($supplier->supplier_name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" id="date_from" name="date_from" class="form-control theme-form-control"
                                value="<?php echo $_GET['date_from'] ?? ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" id="date_to" name="date_to" class="form-control theme-form-control"
                                value="<?php echo $_GET['date_to'] ?? ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="po_number" class="form-label">PO Number</label>
                            <input type="text" id="po_number" name="po_number" class="form-control theme-form-control"
                                placeholder="Enter PO number" value="<?php echo $_GET['po_number'] ?? ''; ?>">
                        </div>
                        <div class="col-12">
                            <div class="theme-action-bar">
                                <div class="theme-action-group">
                                    <button type="submit" class="btn theme-btn-primary">
                                        <i class="fa-solid fa-search"></i> Apply Filters
                                    </button>
                                    <button type="button" class="btn theme-btn-secondary" onclick="exportData()">
                                        <i class="fa-solid fa-download"></i> Export
                                    </button>
                                </div>
                                <div class="theme-action-group">
                                    <button type="button" class="btn theme-btn-warning" onclick="bulkComplete()">
                                        <i class="fa-solid fa-check-double"></i> Complete Selected
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Partial Orders Table -->
    <div class="row">
        <div class="col-12">
            <div class="theme-card">
                <div class="theme-card-header">
                    <h5 class="theme-card-title">
                        <i class="fa-solid fa-list"></i> Partially Received Purchase Orders
                        <?php if (isset($data['total_count'])): ?>
                            <span class="theme-badge theme-badge-warning"><?php echo $data['total_count']; ?></span>
                        <?php endif; ?>
                    </h5>
                    <div class="theme-card-actions">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            <label class="form-check-label" for="selectAll">
                                Select All
                            </label>
                        </div>
                    </div>
                </div>
                <div class="theme-card-body">
                    <?php if (isset($data['partial_orders']) && !empty($data['partial_orders'])): ?>
                        <div class="theme-table-container">
                            <table class="table theme-table">
                                <thead>
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="masterSelect" onchange="toggleSelectAll()">
                                        </th>
                                        <th>PO Number</th>
                                        <th>Supplier</th>
                                        <th>Order Date</th>
                                        <th>Last Updated</th>
                                        <th>Items</th>
                                        <th>Received</th>
                                        <th>Progress</th>
                                        <th>Total Value</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['partial_orders'] as $order): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="order-select"
                                                    value="<?php echo $order->purchase_id; ?>">
                                            </td>
                                            <td>
                                                <strong class="text-warning">
                                                    #<?php echo $order->purchase_number ?? $order->purchase_id; ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fa-solid fa-building text-muted me-2"></i>
                                                    <div>
                                                        <div class="fw-medium">
                                                            <?php echo htmlspecialchars($order->supplier_name ?? 'Unknown'); ?>
                                                        </div>
                                                        <?php if (isset($order->supplier_code)): ?>
                                                            <small class="text-muted"><?php echo $order->supplier_code; ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('M j, Y', strtotime($order->created_at)); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('M j, Y', strtotime($order->updated_at ?? $order->created_at)); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="theme-badge theme-badge-info">
                                                    <?php echo $order->total_items ?? 0; ?> items
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $receivedItems = $order->received_items ?? 0;
                                                $totalItems = $order->total_items ?? 1;
                                                $receivedPercentage = ($receivedItems / $totalItems) * 100;
                                                ?>
                                                <span class="theme-badge theme-badge-warning">
                                                    <?php echo $receivedItems; ?> / <?php echo $totalItems; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-warning" role="progressbar"
                                                        style="width: <?php echo $receivedPercentage; ?>%"
                                                        aria-valuenow="<?php echo $receivedPercentage; ?>" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                        <?php echo round($receivedPercentage); ?>%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong class="text-success">
                                                    ₹<?php echo number_format($order->total_amount ?? 0, 2); ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <span class="theme-badge theme-badge-warning">
                                                    Partially Received
                                                </span>
                                            </td>
                                            <td>
                                                <div class="theme-action-group theme-action-group-sm">
                                                    <a href="<?php echo URLROOT; ?>/receiving/process/<?php echo $order->purchase_id; ?>"
                                                        class="btn btn-sm theme-btn-warning" title="Continue Receiving">
                                                        <i class="fa-solid fa-dolly"></i>
                                                    </a>
                                                    <a href="<?php echo URLROOT; ?>/receiving/details/<?php echo $order->purchase_id; ?>"
                                                        class="btn btn-sm theme-btn-outline-primary" title="View Details">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    <button class="btn btn-sm theme-btn-outline-success"
                                                        onclick="markComplete(<?php echo $order->purchase_id; ?>)"
                                                        title="Mark Complete">
                                                        <i class="fa-solid fa-check"></i>
                                                    </button>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm theme-btn-outline-secondary dropdown-toggle"
                                                            type="button" data-bs-toggle="dropdown">
                                                            <i class="fa-solid fa-ellipsis-h"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item"
                                                                    href="<?php echo URLROOT; ?>/purchases/print/<?php echo $order->purchase_id; ?>">
                                                                    <i class="fa-solid fa-print"></i> Print PO
                                                                </a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="<?php echo URLROOT; ?>/receiving/print-receipt/<?php echo $order->purchase_id; ?>">
                                                                    <i class="fa-solid fa-receipt"></i> Print Receipt
                                                                </a></li>
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                    href="<?php echo URLROOT; ?>/purchases/view/<?php echo $order->purchase_id; ?>">
                                                                    <i class="fa-solid fa-info-circle"></i> View Purchase Order
                                                                </a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if (isset($data['pagination']) && $data['pagination']['total_pages'] > 1): ?>
                            <div class="theme-pagination mt-4">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($data['pagination']['current_page'] > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="?page=<?php echo $data['pagination']['current_page'] - 1; ?>">
                                                    <i class="fa-solid fa-chevron-left"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= $data['pagination']['total_pages']; $i++): ?>
                                            <li
                                                class="page-item <?php echo ($i == $data['pagination']['current_page']) ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($data['pagination']['current_page'] < $data['pagination']['total_pages']): ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="?page=<?php echo $data['pagination']['current_page'] + 1; ?>">
                                                    <i class="fa-solid fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="theme-empty-state">
                            <div class="theme-empty-icon">
                                <i class="fa-solid fa-check-circle"></i>
                            </div>
                            <h5 class="theme-empty-title">No Partial Receipts</h5>
                            <p class="theme-empty-description">
                                Great! No purchase orders are partially received.
                                <a href="<?php echo URLROOT; ?>/receiving/pending">Check pending receipts</a> to get
                                started.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleSelectAll() {
        const masterSelect = document.getElementById('masterSelect');
        const orderSelects = document.querySelectorAll('.order-select');

        orderSelects.forEach(select => {
            select.checked = masterSelect.checked;
        });
    }

    function clearFilters() {
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input, select');
        inputs.forEach(input => {
            if (input.type === 'checkbox' || input.type === 'radio') {
                input.checked = false;
            } else {
                input.value = '';
            }
        });
        form.submit();
    }

    function bulkComplete() {
        const selectedOrders = Array.from(document.querySelectorAll('.order-select:checked')).map(cb => cb.value);

        if (selectedOrders.length === 0) {
            alert('Please select at least one order to complete.');
            return;
        }

        if (confirm(`Are you sure you want to mark ${selectedOrders.length} order(s) as completed?`)) {
            // Send bulk complete request
            fetch(`<?php echo URLROOT; ?>/receiving/bulk-complete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ orders: selectedOrders })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error completing orders: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while completing orders.');
                });
        }
    }

    function markComplete(orderId) {
        if (confirm('Are you sure you want to mark this order as completed?')) {
            // Send complete request
            fetch(`<?php echo URLROOT; ?>/receiving/mark-complete/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error marking order as complete: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while marking the order as complete.');
                });
        }
    }

    function exportData() {
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('export', 'csv');
        window.location.href = currentUrl.toString();
    }

    // Auto-update order counts every 30 seconds
    setInterval(function () {
        fetch(`<?php echo URLROOT; ?>/receiving/getCounts`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                // Update counts in the UI if needed
                if (data.partial_count !== undefined) {
                    const badge = document.querySelector('.theme-card-title .theme-badge');
                    if (badge) {
                        badge.textContent = data.partial_count;
                    }
                }
            })
            .catch(error => console.error('Error updating counts:', error));
    }, 30000);
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>