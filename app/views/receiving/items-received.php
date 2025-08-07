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
                        Items Received
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h1 class="theme-page-title">
                <i class="fa-solid fa-boxes theme-icon"></i>
                Items Received This Week
            </h1>
            <small class="text-muted">
                <i class="fa-solid fa-calendar-week"></i>
                <?php echo date('M j', strtotime('monday this week')); ?> - <?php echo date('M j, Y'); ?>
            </small>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php flash('receive_message'); ?>

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="theme-stat-card theme-stat-info">
                <div class="theme-stat-content">
                    <div class="theme-stat-icon">
                        <i class="fa-solid fa-boxes"></i>
                    </div>
                    <div class="theme-stat-details">
                        <h3 class="theme-stat-number">
                            <?php echo isset($data['total_items']) ? number_format($data['total_items']) : 0; ?>
                        </h3>
                        <p class="theme-stat-label">Total Items</p>
                        <small class="theme-stat-description">Received this week</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="theme-stat-card theme-stat-success">
                <div class="theme-stat-content">
                    <div class="theme-stat-icon">
                        <i class="fa-solid fa-dollar-sign"></i>
                    </div>
                    <div class="theme-stat-details">
                        <h3 class="theme-stat-number">
                            ₹<?php echo isset($data['total_value']) ? number_format($data['total_value'], 0) : 0; ?>
                        </h3>
                        <p class="theme-stat-label">Total Value</p>
                        <small class="theme-stat-description">This week</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="theme-stat-card theme-stat-primary">
                <div class="theme-stat-content">
                    <div class="theme-stat-icon">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <div class="theme-stat-details">
                        <h3 class="theme-stat-number">
                            <?php echo isset($data['unique_orders']) ? $data['unique_orders'] : 0; ?>
                        </h3>
                        <p class="theme-stat-label">Purchase Orders</p>
                        <small class="theme-stat-description">With received items</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="theme-stat-card theme-stat-warning">
                <div class="theme-stat-content">
                    <div class="theme-stat-icon">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div class="theme-stat-details">
                        <h3 class="theme-stat-number">
                            <?php
                            $avgValue = ($data['unique_orders'] ?? 0) > 0 ?
                                ($data['total_value'] ?? 0) / $data['unique_orders'] : 0;
                            echo '₹' . number_format($avgValue, 0);
                            ?>
                        </h3>
                        <p class="theme-stat-label">Avg Order Value</p>
                        <small class="theme-stat-description">This week</small>
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
                        <i class="fa-solid fa-filter"></i> Filters & Date Range
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
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" id="date_from" name="date_from" class="form-control theme-form-control"
                                value="<?php echo $_GET['date_from'] ?? date('Y-m-d', strtotime('monday this week')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" id="date_to" name="date_to" class="form-control theme-form-control"
                                value="<?php echo $_GET['date_to'] ?? date('Y-m-d'); ?>">
                        </div>
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
                            <label for="product_filter" class="form-label">Product Search</label>
                            <input type="text" id="product_filter" name="product"
                                class="form-control theme-form-control" placeholder="Search products..."
                                value="<?php echo $_GET['product'] ?? ''; ?>">
                        </div>
                        <div class="col-12">
                            <div class="theme-action-bar">
                                <div class="theme-action-group">
                                    <button type="submit" class="btn theme-btn-primary">
                                        <i class="fa-solid fa-search"></i> Apply Filters
                                    </button>
                                    <button type="button" class="btn theme-btn-secondary" onclick="exportData()">
                                        <i class="fa-solid fa-download"></i> Export CSV
                                    </button>
                                </div>
                                <div class="theme-action-group">
                                    <button type="button" class="btn theme-btn-outline-info" onclick="setThisWeek()">
                                        <i class="fa-solid fa-calendar-week"></i> This Week
                                    </button>
                                    <button type="button" class="btn theme-btn-outline-info" onclick="setLastWeek()">
                                        <i class="fa-solid fa-calendar"></i> Last Week
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Received Table -->
    <div class="row">
        <div class="col-12">
            <div class="theme-card">
                <div class="theme-card-header">
                    <h5 class="theme-card-title">
                        <i class="fa-solid fa-list"></i> Received Items Details
                        <?php if (isset($data['total_count'])): ?>
                            <span class="theme-badge theme-badge-info"><?php echo $data['total_count']; ?> records</span>
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="theme-card-body">
                    <?php if (isset($data['received_items']) && !empty($data['received_items'])): ?>
                        <div class="theme-table-container">
                            <table class="table theme-table">
                                <thead>
                                    <tr>
                                        <th>Date Received</th>
                                        <th>PO Number</th>
                                        <th>Supplier</th>
                                        <th>Product</th>
                                        <th>Qty Ordered</th>
                                        <th>Qty Received</th>
                                        <th>Unit Price</th>
                                        <th>Total Value</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['received_items'] as $item): ?>
                                        <tr>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="fa-solid fa-calendar"></i>
                                                    <?php echo date('M j, Y', strtotime($item->received_date)); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <strong class="text-primary">
                                                    #<?php echo $item->purchase_number; ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fa-solid fa-building text-muted me-2"></i>
                                                    <span><?php echo htmlspecialchars($item->supplier_name); ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-medium">
                                                    <?php echo htmlspecialchars($item->product_name); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="theme-badge theme-badge-secondary">
                                                    <?php echo number_format($item->quantity_ordered); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="theme-badge theme-badge-success">
                                                    <?php echo number_format($item->quantity_received); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-muted">
                                                    ₹<?php echo number_format($item->unit_price, 2); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong class="text-success">
                                                    ₹<?php echo number_format($item->total_value, 2); ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = 'theme-badge-success';
                                                $statusText = ucfirst(str_replace('_', ' ', $item->status));
                                                if ($item->status === 'partially_received')
                                                    $statusClass = 'theme-badge-warning';
                                                ?>
                                                <span class="theme-badge <?php echo $statusClass; ?>">
                                                    <?php echo $statusText; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="theme-action-group theme-action-group-sm">
                                                    <a href="<?php echo URLROOT; ?>/receiving/details/<?php echo $item->purchase_id; ?>"
                                                        class="btn btn-sm theme-btn-outline-primary"
                                                        title="View Receipt Details">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    <a href="<?php echo URLROOT; ?>/purchases/view/<?php echo $item->purchase_id; ?>"
                                                        class="btn btn-sm theme-btn-outline-secondary"
                                                        title="View Purchase Order">
                                                        <i class="fa-solid fa-file-invoice"></i>
                                                    </a>
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
                                <i class="fa-solid fa-inbox"></i>
                            </div>
                            <h5 class="theme-empty-title">No Items Received</h5>
                            <p class="theme-empty-description">
                                No items have been received in the selected date range.
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
            } else if (input.name !== 'date_from' && input.name !== 'date_to') {
                input.value = '';
            }
        });

        // Reset to this week's dates
        setThisWeek();
    }

    function setThisWeek() {
        const monday = new Date();
        monday.setDate(monday.getDate() - monday.getDay() + 1);
        const today = new Date();

        document.getElementById('date_from').value = monday.toISOString().split('T')[0];
        document.getElementById('date_to').value = today.toISOString().split('T')[0];

        document.querySelector('form').submit();
    }

    function setLastWeek() {
        const lastMonday = new Date();
        lastMonday.setDate(lastMonday.getDate() - lastMonday.getDay() - 6);
        const lastSunday = new Date();
        lastSunday.setDate(lastSunday.getDate() - lastSunday.getDay());

        document.getElementById('date_from').value = lastMonday.toISOString().split('T')[0];
        document.getElementById('date_to').value = lastSunday.toISOString().split('T')[0];

        document.querySelector('form').submit();
    }

    function exportData() {
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('export', 'csv');
        window.location.href = currentUrl.toString();
    }
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>