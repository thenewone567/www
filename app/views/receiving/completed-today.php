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
                        Completed Today
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h1 class="theme-page-title">
                <i class="fa-solid fa-check-circle theme-icon"></i>
                Completed Today
            </h1>
            <small class="text-muted">
                <i class="fa-solid fa-calendar"></i> <?php echo date('F j, Y'); ?>
            </small>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php flash('receive_message'); ?>

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="theme-stat-card theme-stat-success">
                <div class="theme-stat-content">
                    <div class="theme-stat-icon">
                        <i class="fa-solid fa-check-double"></i>
                    </div>
                    <div class="theme-stat-details">
                        <h3 class="theme-stat-number">
                            <?php echo isset($data['total_count']) ? $data['total_count'] : 0; ?>
                        </h3>
                        <p class="theme-stat-label">Orders Completed</p>
                        <small class="theme-stat-description">Today</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="theme-stat-card theme-stat-info">
                <div class="theme-stat-content">
                    <div class="theme-stat-icon">
                        <i class="fa-solid fa-boxes"></i>
                    </div>
                    <div class="theme-stat-details">
                        <h3 class="theme-stat-number">
                            <?php
                            $totalItems = 0;
                            if (isset($data['completed_receipts'])) {
                                foreach ($data['completed_receipts'] as $receipt) {
                                    $totalItems += $receipt->total_items ?? 0;
                                }
                            }
                            echo $totalItems;
                            ?>
                        </h3>
                        <p class="theme-stat-label">Items Received</p>
                        <small class="theme-stat-description">Today</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="theme-stat-card theme-stat-primary">
                <div class="theme-stat-content">
                    <div class="theme-stat-icon">
                        <i class="fa-solid fa-dollar-sign"></i>
                    </div>
                    <div class="theme-stat-details">
                        <h3 class="theme-stat-number">
                            ₹<?php
                            $totalValue = 0;
                            if (isset($data['completed_receipts'])) {
                                foreach ($data['completed_receipts'] as $receipt) {
                                    $totalValue += $receipt->total_amount ?? 0;
                                }
                            }
                            echo number_format($totalValue, 0);
                            ?>
                        </h3>
                        <p class="theme-stat-label">Total Value</p>
                        <small class="theme-stat-description">Today</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
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
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <label for="po_number" class="form-label">PO Number</label>
                            <input type="text" id="po_number" name="po_number" class="form-control theme-form-control"
                                placeholder="Enter PO number" value="<?php echo $_GET['po_number'] ?? ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="received_by" class="form-label">Received By</label>
                            <select id="received_by" name="received_by" class="form-select theme-form-control">
                                <option value="">All Users</option>
                                <?php if (isset($data['users'])): ?>
                                    <?php foreach ($data['users'] as $user): ?>
                                        <option value="<?php echo $user->user_id; ?>" <?php echo (isset($_GET['received_by']) && $_GET['received_by'] == $user->user_id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($user->user_name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
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
                                    <a href="<?php echo URLROOT; ?>/receiving/completed"
                                        class="btn theme-btn-outline-info">
                                        <i class="fa-solid fa-history"></i> View All Completed
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Orders Table -->
    <div class="row">
        <div class="col-12">
            <div class="theme-card">
                <div class="theme-card-header">
                    <h5 class="theme-card-title">
                        <i class="fa-solid fa-list"></i> Completed Receipts - Today
                        <?php if (isset($data['total_count'])): ?>
                            <span class="theme-badge theme-badge-success"><?php echo $data['total_count']; ?></span>
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="theme-card-body">
                    <?php if (isset($data['completed_receipts']) && !empty($data['completed_receipts'])): ?>
                        <div class="theme-table-container">
                            <table class="table theme-table">
                                <thead>
                                    <tr>
                                        <th>PO Number</th>
                                        <th>Supplier</th>
                                        <th>Items</th>
                                        <th>Total Value</th>
                                        <th>Completed Time</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['completed_receipts'] as $receipt): ?>
                                        <tr>
                                            <td>
                                                <strong class="text-success">
                                                    #<?php echo $receipt->purchase_number ?? $receipt->purchase_id; ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fa-solid fa-building text-muted me-2"></i>
                                                    <div>
                                                        <div class="fw-medium">
                                                            <?php echo htmlspecialchars($receipt->supplier_name ?? 'Unknown'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="theme-badge theme-badge-info">
                                                    <?php echo $receipt->total_items ?? 0; ?> items
                                                </span>
                                            </td>
                                            <td>
                                                <strong class="text-success">
                                                    ₹<?php echo number_format($receipt->total_amount ?? 0, 2); ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="fa-solid fa-clock"></i>
                                                    <?php echo date('g:i A', strtotime($receipt->updated_at ?? $receipt->created_at)); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="theme-badge theme-badge-success">
                                                    <i class="fa-solid fa-check"></i> Completed
                                                </span>
                                            </td>
                                            <td>
                                                <div class="theme-action-group theme-action-group-sm">
                                                    <a href="<?php echo URLROOT; ?>/receiving/details/<?php echo $receipt->purchase_id; ?>"
                                                        class="btn btn-sm theme-btn-outline-primary" title="View Details">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    <a href="<?php echo URLROOT; ?>/purchases/print/<?php echo $receipt->purchase_id; ?>"
                                                        class="btn btn-sm theme-btn-outline-secondary" title="Print PO">
                                                        <i class="fa-solid fa-print"></i>
                                                    </a>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm theme-btn-outline-secondary dropdown-toggle"
                                                            type="button" data-bs-toggle="dropdown">
                                                            <i class="fa-solid fa-ellipsis-h"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item"
                                                                    href="<?php echo URLROOT; ?>/receiving/print-receipt/<?php echo $receipt->purchase_id; ?>">
                                                                    <i class="fa-solid fa-receipt"></i> Print Receipt
                                                                </a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="<?php echo URLROOT; ?>/purchases/view/<?php echo $receipt->purchase_id; ?>">
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
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <h5 class="theme-empty-title">No Receipts Completed Today</h5>
                            <p class="theme-empty-description">
                                No purchase orders have been completed today.
                                <a href="<?php echo URLROOT; ?>/receiving/pending">Process pending receipts</a> to get
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

    function exportData() {
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('export', 'csv');
        window.location.href = currentUrl.toString();
    }
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>