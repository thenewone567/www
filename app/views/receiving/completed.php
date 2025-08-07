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
                        Completed Receipts
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h1 class="theme-page-title">
                <i class="fa-solid fa-check-circle theme-icon"></i>
                Completed Receipts
            </h1>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php flash('receive_message'); ?>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="theme-stat-card theme-stat-success">
                <div class="theme-stat-content">
                    <div class="theme-stat-icon">
                        <i class="fa-solid fa-check-circle"></i>
                    </div>
                    <div class="theme-stat-details">
                        <h3 class="theme-stat-number">
                            <?php echo isset($data['completed_today']) ? $data['completed_today'] : 0; ?>
                        </h3>
                        <p class="theme-stat-label">Completed Today</p>
                        <small class="theme-stat-description">Fully received orders</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="theme-stat-card theme-stat-info">
                <div class="theme-stat-content">
                    <div class="theme-stat-icon">
                        <i class="fa-solid fa-calendar-week"></i>
                    </div>
                    <div class="theme-stat-details">
                        <h3 class="theme-stat-number">
                            <?php echo isset($data['completed_week']) ? $data['completed_week'] : 0; ?>
                        </h3>
                        <p class="theme-stat-label">This Week</p>
                        <small class="theme-stat-description">Weekly completions</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="theme-stat-card theme-stat-primary">
                <div class="theme-stat-content">
                    <div class="theme-stat-icon">
                        <i class="fa-solid fa-boxes"></i>
                    </div>
                    <div class="theme-stat-details">
                        <h3 class="theme-stat-number">
                            <?php echo isset($data['total_items_received']) ? number_format($data['total_items_received']) : 0; ?>
                        </h3>
                        <p class="theme-stat-label">Items Received</p>
                        <small class="theme-stat-description">Total items processed</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="theme-stat-card theme-stat-warning">
                <div class="theme-stat-content">
                    <div class="theme-stat-icon">
                        <i class="fa-solid fa-rupee-sign"></i>
                    </div>
                    <div class="theme-stat-details">
                        <h3 class="theme-stat-number">
                            ₹<?php echo isset($data['total_value_received']) ? number_format($data['total_value_received'], 0) : 0; ?>
                        </h3>
                        <p class="theme-stat-label">Value Received</p>
                        <small class="theme-stat-description">Total inventory value</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="theme-card">
                <div class="theme-card-header">
                    <h5 class="theme-card-title">
                        <i class="fa-solid fa-filter"></i> Search & Filters
                    </h5>
                    <div class="theme-card-actions">
                        <button class="btn btn-sm theme-btn-outline-secondary" onclick="clearFilters()">
                            <i class="fa-solid fa-times"></i> Clear
                        </button>
                    </div>
                </div>
                <div class="theme-card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-2">
                            <label for="po_search" class="form-label">PO Number</label>
                            <input type="text" id="po_search" name="po_number" class="form-control theme-form-control"
                                placeholder="Search PO..." value="<?php echo $_GET['po_number'] ?? ''; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="supplier_filter" class="form-label">Supplier</label>
                            <select id="supplier_filter" name="supplier" class="form-select theme-form-control">
                                <option value="">All Suppliers</option>
                                <?php if (isset($data['suppliers'])): ?>
                                    <?php foreach ($data['suppliers'] as $supplier): ?>
                                        <option value="<?php echo $supplier->id; ?>" <?php echo (isset($_GET['supplier']) && $_GET['supplier'] == $supplier->id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($supplier->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" id="date_from" name="date_from" class="form-control theme-form-control"
                                value="<?php echo $_GET['date_from'] ?? ''; ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" id="date_to" name="date_to" class="form-control theme-form-control"
                                value="<?php echo $_GET['date_to'] ?? ''; ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="received_by_filter" class="form-label">Received By</label>
                            <select id="received_by_filter" name="received_by" class="form-select theme-form-control">
                                <option value="">All Users</option>
                                <?php if (isset($data['users'])): ?>
                                    <?php foreach ($data['users'] as $user): ?>
                                        <option value="<?php echo $user->id; ?>" <?php echo (isset($_GET['received_by']) && $_GET['received_by'] == $user->id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($user->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn theme-btn-primary">
                                    <i class="fa-solid fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="theme-action-bar">
                                <div class="theme-action-group">
                                    <button type="button" class="btn theme-btn-secondary" onclick="exportData()">
                                        <i class="fa-solid fa-download"></i> Export CSV
                                    </button>
                                    <button type="button" class="btn theme-btn-info" onclick="printReport()">
                                        <i class="fa-solid fa-print"></i> Print Report
                                    </button>
                                </div>
                                <div class="theme-action-group">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Show</span>
                                        <select class="form-select" name="per_page" onchange="this.form.submit()">
                                            <option value="25" <?php echo (($_GET['per_page'] ?? 25) == 25) ? 'selected' : ''; ?>>25</option>
                                            <option value="50" <?php echo (($_GET['per_page'] ?? 25) == 50) ? 'selected' : ''; ?>>50</option>
                                            <option value="100" <?php echo (($_GET['per_page'] ?? 25) == 100) ? 'selected' : ''; ?>>100</option>
                                        </select>
                                        <span class="input-group-text">per page</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Receipts Table -->
    <div class="row">
        <div class="col-12">
            <div class="theme-card">
                <div class="theme-card-header">
                    <h5 class="theme-card-title">
                        <i class="fa-solid fa-list"></i> Completed Receipts
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
                                        <th>
                                            <a href="?sort=purchase_number&order=<?php echo ($_GET['order'] ?? 'asc') === 'asc' ? 'desc' : 'asc'; ?>"
                                                class="text-decoration-none">
                                                PO Number
                                                <i class="fa-solid fa-sort ms-1"></i>
                                            </a>
                                        </th>
                                        <th>
                                            <a href="?sort=supplier_name&order=<?php echo ($_GET['order'] ?? 'asc') === 'asc' ? 'desc' : 'asc'; ?>"
                                                class="text-decoration-none">
                                                Supplier
                                                <i class="fa-solid fa-sort ms-1"></i>
                                            </a>
                                        </th>
                                        <th>
                                            <a href="?sort=order_date&order=<?php echo ($_GET['order'] ?? 'asc') === 'asc' ? 'desc' : 'asc'; ?>"
                                                class="text-decoration-none">
                                                Order Date
                                                <i class="fa-solid fa-sort ms-1"></i>
                                            </a>
                                        </th>
                                        <th>
                                            <a href="?sort=received_date&order=<?php echo ($_GET['order'] ?? 'asc') === 'asc' ? 'desc' : 'asc'; ?>"
                                                class="text-decoration-none">
                                                Received Date
                                                <i class="fa-solid fa-sort ms-1"></i>
                                            </a>
                                        </th>
                                        <th>Items</th>
                                        <th>Total Value</th>
                                        <th>Received By</th>
                                        <th>Receipt Ref</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['completed_receipts'] as $receipt): ?>
                                        <tr>
                                            <td>
                                                <strong class="text-primary">
                                                    #<?php echo $receipt->purchase_number ?? $receipt->purchase_id; ?>
                                                </strong>
                                                <div>
                                                    <span class="theme-badge theme-badge-success">
                                                        <i class="fa-solid fa-check-circle"></i> Completed
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fa-solid fa-building text-muted me-2"></i>
                                                    <div>
                                                        <div class="fw-medium">
                                                            <?php echo htmlspecialchars($receipt->supplier_name ?? 'Unknown'); ?>
                                                        </div>
                                                        <?php if (isset($receipt->supplier_code)): ?>
                                                            <small class="text-muted"><?php echo $receipt->supplier_code; ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="fa-solid fa-calendar-plus me-1"></i>
                                                    <?php echo date('M j, Y', strtotime($receipt->order_date ?? $receipt->created_at)); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-success">
                                                    <i class="fa-solid fa-check me-1"></i>
                                                    <?php echo date('M j, Y g:i A', strtotime($receipt->received_date ?? $receipt->updated_at)); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="theme-item-summary">
                                                    <span class="theme-badge theme-badge-info">
                                                        <?php echo $receipt->total_items ?? 0; ?> items
                                                    </span>
                                                    <?php if (isset($receipt->item_details)): ?>
                                                        <div class="mt-1">
                                                            <small class="text-muted">
                                                                <?php echo number_format($receipt->total_quantity ?? 0, 2); ?> units
                                                                total
                                                            </small>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-success fw-medium">
                                                    ₹<?php echo number_format($receipt->total_amount ?? 0, 2); ?>
                                                </div>
                                                <?php if (isset($receipt->tax_amount) && $receipt->tax_amount > 0): ?>
                                                    <small class="text-muted">
                                                        (incl. ₹<?php echo number_format($receipt->tax_amount, 2); ?> tax)
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fa-solid fa-user text-muted me-2"></i>
                                                    <div>
                                                        <div class="fw-medium">
                                                            <?php echo htmlspecialchars($receipt->received_by_name ?? 'Unknown'); ?>
                                                        </div>
                                                        <small class="text-muted">
                                                            <?php echo date('g:i A', strtotime($receipt->received_date ?? $receipt->updated_at)); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if (isset($receipt->receipt_reference) && $receipt->receipt_reference): ?>
                                                    <code class="theme-code">
                                                                    <?php echo htmlspecialchars($receipt->receipt_reference); ?>
                                                                </code>
                                                <?php else: ?>
                                                    <small class="text-muted">No reference</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="theme-action-group theme-action-group-sm">
                                                    <a href="<?php echo URLROOT; ?>/receiving/details/<?php echo $receipt->purchase_id; ?>"
                                                        class="btn btn-sm theme-btn-outline-primary" title="View Details">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    <a href="<?php echo URLROOT; ?>/receiving/print-receipt/<?php echo $receipt->purchase_id; ?>"
                                                        class="btn btn-sm theme-btn-outline-info" title="Print Receipt"
                                                        target="_blank">
                                                        <i class="fa-solid fa-print"></i>
                                                    </a>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm theme-btn-outline-secondary dropdown-toggle"
                                                            type="button" data-bs-toggle="dropdown">
                                                            <i class="fa-solid fa-ellipsis-h"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item"
                                                                    href="<?php echo URLROOT; ?>/purchases/view/<?php echo $receipt->purchase_id; ?>">
                                                                    <i class="fa-solid fa-file-invoice"></i> View Purchase Order
                                                                </a></li>
                                                            <li><a class="dropdown-item"
                                                                    href="<?php echo URLROOT; ?>/receiving/export-receipt/<?php echo $receipt->purchase_id; ?>">
                                                                    <i class="fa-solid fa-download"></i> Export Receipt
                                                                </a></li>
                                                            <?php if (isset($receipt->notes) && $receipt->notes): ?>
                                                                <li>
                                                                    <hr class="dropdown-divider">
                                                                </li>
                                                                <li><span class="dropdown-item-text">
                                                                        <small class="text-muted">
                                                                            <i class="fa-solid fa-note-sticky"></i>
                                                                            <?php echo htmlspecialchars(substr($receipt->notes, 0, 30)); ?>
                                                                            <?php if (strlen($receipt->notes) > 30)
                                                                                echo '...'; ?>
                                                                        </small>
                                                                    </span></li>
                                                            <?php endif; ?>
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
                                                    href="?page=<?php echo $data['pagination']['current_page'] - 1; ?>&<?php echo http_build_query(array_filter($_GET, function ($key) {
                                                             return $key !== 'page'; }, ARRAY_FILTER_USE_KEY)); ?>">
                                                    <i class="fa-solid fa-chevron-left"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = max(1, $data['pagination']['current_page'] - 2); $i <= min($data['pagination']['total_pages'], $data['pagination']['current_page'] + 2); $i++): ?>
                                            <li
                                                class="page-item <?php echo ($i == $data['pagination']['current_page']) ? 'active' : ''; ?>">
                                                <a class="page-link"
                                                    href="?page=<?php echo $i; ?>&<?php echo http_build_query(array_filter($_GET, function ($key) {
                                                           return $key !== 'page'; }, ARRAY_FILTER_USE_KEY)); ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($data['pagination']['current_page'] < $data['pagination']['total_pages']): ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="?page=<?php echo $data['pagination']['current_page'] + 1; ?>&<?php echo http_build_query(array_filter($_GET, function ($key) {
                                                             return $key !== 'page'; }, ARRAY_FILTER_USE_KEY)); ?>">
                                                    <i class="fa-solid fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>

                                <div class="text-center mt-2">
                                    <small class="text-muted">
                                        Showing
                                        <?php echo (($data['pagination']['current_page'] - 1) * ($data['pagination']['per_page'] ?? 25)) + 1; ?>
                                        to
                                        <?php echo min($data['pagination']['current_page'] * ($data['pagination']['per_page'] ?? 25), $data['pagination']['total_records']); ?>
                                        of <?php echo $data['pagination']['total_records']; ?> entries
                                    </small>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="theme-empty-state">
                            <div class="theme-empty-icon">
                                <i class="fa-solid fa-inbox"></i>
                            </div>
                            <h5 class="theme-empty-title">No Completed Receipts</h5>
                            <p class="theme-empty-description">
                                No receipts have been completed yet with the current filters.
                                <br>
                                <a href="<?php echo URLROOT; ?>/receiving/pending">Process pending receipts</a> to get
                                started.
                            </p>
                            <button class="btn theme-btn-outline-secondary" onclick="clearFilters()">
                                <i class="fa-solid fa-filter"></i> Clear Filters
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function clearFilters() {
        window.location.href = '<?php echo URLROOT; ?>/receiving/completed';
    }

    function exportData() {
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('export', 'csv');
        window.location.href = currentUrl.toString();
    }

    function printReport() {
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('print', '1');
        window.open(currentUrl.toString(), '_blank');
    }

    // Auto-refresh data every 2 minutes
    setInterval(function () {
        fetch('<?php echo URLROOT; ?>/receiving/get-counts', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                // Update summary cards if needed
                if (data.completed_today !== undefined) {
                    const todayCard = document.querySelector('.theme-stat-card:first-child .theme-stat-number');
                    if (todayCard && todayCard.textContent !== data.completed_today.toString()) {
                        todayCard.textContent = data.completed_today;
                        // Add a subtle highlight effect
                        todayCard.style.animation = 'pulse 0.5s ease-in-out';
                        setTimeout(() => todayCard.style.animation = '', 500);
                    }
                }
            })
            .catch(error => console.error('Error updating data:', error));
    }, 120000);
</script>

<style>
    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }
</style>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>