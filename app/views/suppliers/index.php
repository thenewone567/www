<?php
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';

// Helper function to build pagination URLs with all current parameters
function buildPaginationUrl($page, $perPage = null)
{
    $params = [];

    // Add page parameter
    $params['page'] = $page;

    // Preserve current parameters
    if (!empty($_GET['search']))
        $params['search'] = $_GET['search'];
    if (!empty($_GET['status']))
        $params['status'] = $_GET['status'];
    if (!empty($_GET['tier']))
        $params['tier'] = $_GET['tier'];
    if (!empty($_GET['sort']))
        $params['sort'] = $_GET['sort'];
    if (!empty($_GET['order']))
        $params['order'] = $_GET['order'];

    // Use provided per_page or current per_page
    if ($perPage) {
        $params['per_page'] = $perPage;
    } elseif (!empty($_GET['per_page'])) {
        $params['per_page'] = $_GET['per_page'];
    }

    return '?' . http_build_query($params);
}
?>

<div class="container-fluid mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-6">
            <h1 class="mb-1 font-weight-bold">
                <i class="fas fa-users mr-2"></i>Suppliers Dashboard
            </h1>
            <small class="text-muted">Complete supplier management and performance tracking</small>
        </div>
        <div class="col-12 col-md-6 text-md-right mt-3 mt-md-0">
            <a href="<?php echo URLROOT; ?>/suppliers/add" class="btn btn-success btn-lg mr-2">
                <i class="fa fa-plus"></i> Add Supplier
            </a>
            <a href="<?php echo URLROOT; ?>/suppliers/link" class="btn btn-primary btn-lg mr-2">
                <i class="fas fa-link"></i> Link Supplier
            </a>
            <a href="<?php echo URLROOT; ?>/suppliers/competitionReport" class="btn btn-warning btn-lg">
                <i class="fas fa-chart-line"></i> Competition Report
            </a>
        </div>
    </div>
</div>

<div class="container-fluid mt-0 pt-3">
    <!-- Enhanced KPI Summary Cards (unified .kpi-card markup) -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 mb-3">
            <div class="kpi-card kpi-gradient-primary shadow-sm h-100">
                <div class="kpi-body">
                    <div class="kpi-count">
                        <?php echo isset($data['total_suppliers']) ? $data['total_suppliers'] : '0'; ?>
                    </div>
                    <div class="kpi-value small">Registered •
                        <?php echo isset($data['total_suppliers']) ? $data['total_suppliers'] : '0'; ?>
                    </div>
                    <div class="kpi-small-spark" aria-hidden="true"></div>
                    <i class="fas fa-users kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 mb-3">
            <div class="kpi-card kpi-gradient-success shadow-sm h-100">
                <div class="kpi-body">
                    <div class="kpi-count">
                        <?php echo isset($data['active_suppliers']) ? $data['active_suppliers'] : '0'; ?>
                    </div>
                    <div class="kpi-value small">Active •
                        <?php echo isset($data['active_suppliers']) ? $data['active_suppliers'] : '0'; ?>
                    </div>
                    <div class="kpi-small-spark" aria-hidden="true"></div>
                    <i class="fas fa-check-circle kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 mb-3">
            <div class="kpi-card kpi-gradient-info shadow-sm h-100">
                <div class="kpi-body">
                    <div class="kpi-count">
                        <?php echo isset($data['avg_delivery_days']) ? number_format($data['avg_delivery_days'], 1) : '0'; ?>
                    </div>
                    <div class="kpi-value small">Avg Delivery • Days</div>
                    <div class="kpi-small-spark" aria-hidden="true"></div>
                    <i class="fas fa-truck kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 mb-3">
            <div class="kpi-card kpi-gradient-warning shadow-sm h-100">
                <div class="kpi-body">
                    <div class="kpi-count">
                        <?php echo isset($data['avg_on_time_rate']) ? number_format($data['avg_on_time_rate'], 1) : '0'; ?>%
                    </div>
                    <div class="kpi-value small">On-Time Rate • Average</div>
                    <div class="kpi-small-spark" aria-hidden="true"></div>
                    <i class="fas fa-clock kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 mb-3">
            <div class="kpi-card kpi-gradient-success shadow-sm h-100 special">
                <div class="kpi-body">
                    <div class="kpi-count">
                        <?php echo isset($data['gold_tier_suppliers']) ? $data['gold_tier_suppliers'] : '0'; ?>
                    </div>
                    <div class="kpi-value small">Gold Tier • Suppliers</div>
                    <div class="kpi-small-spark" aria-hidden="true"></div>
                    <i class="fas fa-crown kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 mb-3">
            <div class="kpi-card kpi-gradient-primary shadow-sm h-100 secondary special">
                <div class="kpi-body">
                    <div class="kpi-count">
                        ₹<?php echo isset($data['total_order_value']) ? number_format($data['total_order_value'] / 100000, 1) : '0'; ?>L
                    </div>
                    <div class="kpi-value small">Total Value • Order Value</div>
                    <div class="kpi-small-spark" aria-hidden="true"></div>
                    <i class="fas fa-chart-line kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div> <!-- end of KPI row -->

    <!-- Quick Stats Cards (unified look) -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="kpi-card kpi-gradient-primary shadow-sm h-100">
                <div class="kpi-body text-center">
                    <div class="kpi-count">
                        ₹<?php echo isset($data['total_order_value']) ? number_format($data['total_order_value'] / 100000, 1) : '2.45'; ?>L
                    </div>
                    <div class="kpi-value small">Total Supplier Value</div>
                    <i class="fas fa-handshake kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="kpi-card kpi-gradient-success shadow-sm h-100">
                <div class="kpi-body text-center">
                    <div class="kpi-count">
                        <?php echo isset($data['avg_on_time_rate']) ? number_format($data['avg_on_time_rate'], 1) : '85.2'; ?>%
                    </div>
                    <div class="kpi-value small">Average On-Time Rate</div>
                    <i class="fas fa-percentage kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="kpi-card kpi-gradient-warning shadow-sm h-100">
                <div class="kpi-body text-center">
                    <div class="kpi-count">
                        <?php echo isset($data['poor_performers_count']) ? $data['poor_performers_count'] : '3'; ?>
                    </div>
                    <div class="kpi-value small">Suppliers Need Attention</div>
                    <i class="fas fa-exclamation-triangle kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="kpi-card kpi-gradient-info shadow-sm h-100">
                <div class="kpi-body text-center">
                    <div class="kpi-count">
                        <?php echo isset($data['active_suppliers']) ? $data['active_suppliers'] : '8'; ?>
                    </div>
                    <div class="kpi-value small">Active Partnerships</div>
                    <i class="fas fa-sync-alt kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performers Section -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-3">
            <div class="kpi-card kpi-gradient-success shadow h-100">
                <div class="kpi-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item bg-transparent border-0 px-3 py-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <i class="fas fa-trophy fa-lg mr-2"></i>
                                    <strong>Top Performers</strong>
                                </div>
                                <small class="text-white-50">Top 3</small>
                            </div>
                        </div>
                        <?php if (!empty($data['top_performers'])): ?>
                            <?php foreach (array_slice($data['top_performers'], 0, 3) as $index => $supplier): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($supplier->supplier_name); ?></strong>
                                        <br><small
                                            class="text-muted"><?php echo number_format($supplier->on_time_delivery_rate ?? 0, 1); ?>%
                                            on-time</small>
                                    </div>
                                    <span
                                        class="badge badge-<?php echo $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'primary'); ?> badge-pill">
                                        <?php echo $index + 1; ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="list-group-item text-center text-muted">
                                No performance data available
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="kpi-card kpi-gradient-warning shadow h-100">
                <div class="kpi-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item bg-transparent border-0 px-3 py-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <i class="fas fa-exclamation-triangle fa-lg mr-2"></i>
                                    <strong>Needs Attention</strong>
                                </div>
                                <small class="text-white-50">Action Required</small>
                            </div>
                        </div>
                        <?php if (!empty($data['poor_performers'])): ?>
                            <?php foreach (array_slice($data['poor_performers'], 0, 3) as $supplier): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($supplier->supplier_name); ?></strong>
                                        <br><small
                                            class="text-muted"><?php echo number_format($supplier->on_time_delivery_rate ?? 0, 1); ?>%
                                            on-time</small>
                                    </div>
                                    <span class="badge badge-danger badge-pill">
                                        <i class="fas fa-arrow-down"></i>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="list-group-item text-center text-muted">
                                All suppliers performing well
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="kpi-card kpi-gradient-info shadow h-100">
                <div class="kpi-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item bg-transparent border-0 px-3 py-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <i class="fas fa-clock fa-lg mr-2"></i>
                                    <strong>Recent Deliveries</strong>
                                </div>
                                <small class="text-white-50">Latest</small>
                            </div>
                        </div>
                        <?php if (!empty($data['recent_deliveries'])): ?>
                            <?php foreach (array_slice($data['recent_deliveries'], 0, 3) as $delivery): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <strong><?php echo htmlspecialchars($delivery->supplier_name); ?></strong>
                                        <span
                                            class="badge badge-<?php echo $delivery->days_early_late > 0 ? 'danger' : ($delivery->days_early_late < 0 ? 'success' : 'primary'); ?>">
                                            <?php
                                            if ($delivery->days_early_late > 0) {
                                                echo '+' . $delivery->days_early_late . ' days';
                                            } elseif ($delivery->days_early_late < 0) {
                                                echo abs($delivery->days_early_late) . ' days early';
                                            } else {
                                                echo 'On time';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <small
                                        class="text-muted"><?php echo date('M d, Y', strtotime($delivery->actual_delivery_date)); ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="list-group-item text-center text-muted">
                                No recent deliveries
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Suppliers Management Table -->
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="kpi-card">
                <div class="kpi-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            <i class="fas fa-list fa-lg text-muted"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Supplier Management</h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <form method="GET" action="<?php echo URLROOT; ?>/suppliers" class="mr-3" id="searchForm">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" class="form-control" id="supplierSearch" name="search"
                                    placeholder="Search suppliers..."
                                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                <!-- Preserve all current GET parameters -->
                                <?php if (isset($_GET['per_page'])): ?>
                                    <input type="hidden" name="per_page" value="<?php echo (int) $_GET['per_page']; ?>">
                                <?php endif; ?>
                                <?php if (isset($_GET['status'])): ?>
                                    <input type="hidden" name="status"
                                        value="<?php echo htmlspecialchars($_GET['status']); ?>">
                                <?php endif; ?>
                                <?php if (isset($_GET['tier'])): ?>
                                    <input type="hidden" name="tier" value="<?php echo htmlspecialchars($_GET['tier']); ?>">
                                <?php endif; ?>
                                <?php if (isset($_GET['sort'])): ?>
                                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($_GET['sort']); ?>">
                                <?php endif; ?>
                                <?php if (isset($_GET['order'])): ?>
                                    <input type="hidden" name="order"
                                        value="<?php echo htmlspecialchars($_GET['order']); ?>">
                                <?php endif; ?>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                                        <a href="<?php echo URLROOT; ?>/suppliers" class="btn btn-outline-danger"
                                            title="Clear search">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                        <div class="d-flex align-items-center mr-2">
                            <label class="mb-0 mr-2 text-muted" style="font-size: 0.875rem;">Show:</label>
                            <select class="form-control form-control-sm" id="itemsPerPage"
                                onchange="changeItemsPerPage()" style="width: auto;">
                                <option value="25" <?php echo (!isset($_GET['per_page']) || $_GET['per_page'] == 25) ? 'selected' : ''; ?>>25</option>
                                <option value="50" <?php echo (isset($_GET['per_page']) && $_GET['per_page'] == 50) ? 'selected' : ''; ?>>50</option>
                                <option value="100" <?php echo (isset($_GET['per_page']) && $_GET['per_page'] == 100) ? 'selected' : ''; ?>>100</option>
                                <option value="500" <?php echo (isset($_GET['per_page']) && $_GET['per_page'] == 500) ? 'selected' : ''; ?>>500</option>
                            </select>
                        </div>
                        <div class="btn-group btn-group-sm mr-2">
                            <button class="btn btn-outline-secondary" onclick="exportSuppliers('csv')">
                                <i class="fas fa-file-csv mr-1"></i>CSV
                            </button>
                            <button class="btn btn-outline-secondary" onclick="exportSuppliers('excel')">
                                <i class="fas fa-file-excel mr-1"></i>Excel
                            </button>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button"
                                data-toggle="dropdown">
                                <i class="fas fa-filter mr-1"></i>Filter
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" onclick="filterSuppliers('all')">All Suppliers</a>
                                <a class="dropdown-item" href="#" onclick="filterSuppliers('active')">Active Only</a>
                                <a class="dropdown-item" href="#" onclick="filterSuppliers('inactive')">Inactive
                                    Only</a>
                                <a class="dropdown-item" href="#" onclick="filterSuppliers('gold_tier')">Gold Tier</a>
                                <a class="dropdown-item" href="#" onclick="filterSuppliers('poor_performance')">Poor
                                    Performance</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="suppliersTable">
                            <thead class="thead-light">
                                <tr>
                                    <th width="3%">
                                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                    </th>
                                    <th width="8%" id="sort-supplier_id" class="sortable" data-sort="supplier_id">
                                        ID
                                        <?php if (isset($data['current_sort']) && $data['current_sort'] === 'supplier_id'): ?>
                                            <i
                                                class="fas fa-sort-<?php echo $data['current_order'] === 'DESC' ? 'down' : 'up'; ?>"></i>
                                        <?php else: ?>
                                            <i class="fas fa-sort text-muted"></i>
                                        <?php endif; ?>
                                    </th>
                                    <th width="20%" id="sort-supplier_name" class="sortable" data-sort="supplier_name">
                                        Supplier Details
                                        <?php if (isset($data['current_sort']) && $data['current_sort'] === 'supplier_name'): ?>
                                            <i
                                                class="fas fa-sort-<?php echo $data['current_order'] === 'DESC' ? 'down' : 'up'; ?>"></i>
                                        <?php else: ?>
                                            <i class="fas fa-sort text-muted"></i>
                                        <?php endif; ?>
                                    </th>
                                    <th width="15%" id="sort-contact_person" class="sortable"
                                        data-sort="contact_person">
                                        Contact Info
                                        <?php if (isset($data['current_sort']) && $data['current_sort'] === 'contact_person'): ?>
                                            <i
                                                class="fas fa-sort-<?php echo $data['current_order'] === 'DESC' ? 'down' : 'up'; ?>"></i>
                                        <?php else: ?>
                                            <i class="fas fa-sort text-muted"></i>
                                        <?php endif; ?>
                                    </th>
                                    <th width="10%" id="sort-reliability_score" class="sortable"
                                        data-sort="reliability_score">
                                        Performance
                                        <?php if (isset($data['current_sort']) && $data['current_sort'] === 'reliability_score'): ?>
                                            <i
                                                class="fas fa-sort-<?php echo $data['current_order'] === 'DESC' ? 'down' : 'up'; ?>"></i>
                                        <?php else: ?>
                                            <i class="fas fa-sort text-muted"></i>
                                        <?php endif; ?>
                                    </th>
                                    <th width="12%" id="sort-average_delivery_days" class="sortable"
                                        data-sort="average_delivery_days">
                                        Delivery
                                        <?php if (isset($data['current_sort']) && $data['current_sort'] === 'average_delivery_days'): ?>
                                            <i
                                                class="fas fa-sort-<?php echo $data['current_order'] === 'DESC' ? 'down' : 'up'; ?>"></i>
                                        <?php else: ?>
                                            <i class="fas fa-sort text-muted"></i>
                                        <?php endif; ?>
                                    </th>
                                    <th width="8%" id="sort-supplier_tier" class="sortable" data-sort="supplier_tier">
                                        Tier
                                        <?php if (isset($data['current_sort']) && $data['current_sort'] === 'supplier_tier'): ?>
                                            <i
                                                class="fas fa-sort-<?php echo $data['current_order'] === 'DESC' ? 'down' : 'up'; ?>"></i>
                                        <?php else: ?>
                                            <i class="fas fa-sort text-muted"></i>
                                        <?php endif; ?>
                                    </th>
                                    <th width="10%" id="sort-status" class="sortable" data-sort="status">
                                        Status
                                        <?php if (isset($data['current_sort']) && $data['current_sort'] === 'status'): ?>
                                            <i
                                                class="fas fa-sort-<?php echo $data['current_order'] === 'DESC' ? 'down' : 'up'; ?>"></i>
                                        <?php else: ?>
                                            <i class="fas fa-sort text-muted"></i>
                                        <?php endif; ?>
                                    </th>
                                    <th width="12%" id="sort-added_by" class="sortable" data-sort="added_by">
                                        Added By
                                        <?php if (isset($data['current_sort']) && $data['current_sort'] === 'added_by'): ?>
                                            <i
                                                class="fas fa-sort-<?php echo $data['current_order'] === 'DESC' ? 'down' : 'up'; ?>"></i>
                                        <?php else: ?>
                                            <i class="fas fa-sort text-muted"></i>
                                        <?php endif; ?>
                                    </th>
                                    <th width="12%" id="sort-created_at" class="sortable" data-sort="created_at">
                                        Added At
                                        <?php if (isset($data['current_sort']) && $data['current_sort'] === 'created_at'): ?>
                                            <i
                                                class="fas fa-sort-<?php echo $data['current_order'] === 'DESC' ? 'down' : 'up'; ?>"></i>
                                        <?php else: ?>
                                            <i class="fas fa-sort text-muted"></i>
                                        <?php endif; ?>
                                    </th>
                                    <th width="14%">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="suppliersTableBody">
                                <?php if (!empty($data['suppliers'])): ?>
                                    <?php foreach ($data['suppliers'] as $supplier): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="supplier-checkbox"
                                                    value="<?php echo $supplier->supplier_id; ?>">
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-secondary">#<?php echo $supplier->supplier_id; ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($supplier->supplier_name); ?></strong>
                                                        <br><small class="text-muted">
                                                            <i class="fas fa-envelope mr-1"></i>
                                                            <?php echo htmlspecialchars($supplier->email ?? 'No email'); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <i class="fas fa-user text-muted mr-1"></i>
                                                    <?php echo htmlspecialchars($supplier->contact_person ?? '-'); ?>
                                                    <br>
                                                    <i class="fas fa-phone text-muted mr-1"></i>
                                                    <small><?php echo htmlspecialchars($supplier->phone ?? '-'); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $performance_score = $supplier->delivery_performance_score ?? 0;
                                                $score_class = $performance_score >= 90 ? 'success' : ($performance_score >= 70 ? 'warning' : 'danger');
                                                ?>
                                                <div class="text-center">
                                                    <div class="circular-progress circular-progress-<?php echo $score_class; ?>"
                                                        style="width: 40px; height: 40px; display: inline-block;">
                                                        <span class="score-text"
                                                            style="font-size: 12px;"><?php echo number_format($performance_score, 0); ?></span>
                                                    </div>
                                                    <br><small class="text-muted">Score</small>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $avg_delivery = $supplier->avg_delivery_days ?? 0;
                                                $delivery_class = $avg_delivery <= 3 ? 'success' : ($avg_delivery <= 7 ? 'warning' : 'danger');
                                                $on_time_rate = 0;
                                                if (isset($supplier->total_completed_orders) && $supplier->total_completed_orders > 0) {
                                                    $on_time_rate = (($supplier->on_time_deliveries_count ?? 0) / $supplier->total_completed_orders) * 100;
                                                }
                                                $rate_class = $on_time_rate >= 90 ? 'success' : ($on_time_rate >= 70 ? 'warning' : 'danger');
                                                ?>
                                                <span class="badge badge-<?php echo $delivery_class; ?>">
                                                    <i class="fas fa-truck mr-1"></i>
                                                    <?php echo number_format($avg_delivery, 1); ?> days
                                                </span>
                                                <br>
                                                <small class="text-<?php echo $rate_class; ?>">
                                                    <?php echo number_format($on_time_rate, 1); ?>% on-time
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                $tier = 'Bronze';
                                                $tier_class = 'secondary';
                                                $tier_icon = 'medal';

                                                if ($performance_score >= 95) {
                                                    $tier = 'Gold';
                                                    $tier_class = 'warning';
                                                    $tier_icon = 'crown';
                                                } elseif ($performance_score >= 80) {
                                                    $tier = 'Silver';
                                                    $tier_class = 'light';
                                                    $tier_icon = 'medal';
                                                }
                                                ?>
                                                <span class="badge badge-<?php echo $tier_class; ?> p-2">
                                                    <i class="fas fa-<?php echo $tier_icon; ?> mr-1"></i>
                                                    <?php echo $tier; ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                $status = $supplier->status ?? 'active';
                                                $status_class = $status === 'active' ? 'success' : ($status === 'pending' ? 'warning' : 'danger');
                                                ?>
                                                <span class="badge badge-<?php echo $status_class; ?>">
                                                    <i
                                                        class="fas fa-<?php echo $status === 'active' ? 'check-circle' : ($status === 'pending' ? 'clock' : 'times-circle'); ?> mr-1"></i>
                                                    <?php echo ucfirst($status); ?>
                                                </span>
                                            </td>
                                            <td class="text-middle">
                                                <?php echo htmlspecialchars($supplier->added_by_username ?? '-'); ?>
                                            </td>
                                            <td class="text-middle">
                                                <?php echo !empty($supplier->created_at) ? date('M d, Y H:i', strtotime($supplier->created_at)) : '-'; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary"
                                                        onclick="viewSupplier(<?php echo $supplier->supplier_id; ?>)"
                                                        title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-warning"
                                                        onclick="editSupplier(<?php echo $supplier->supplier_id; ?>)"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <div class="dropdown">
                                                        <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="#"
                                                                onclick="viewSupplierOrders(<?php echo $supplier->supplier_id; ?>)">
                                                                <i class="fas fa-shopping-cart mr-2"></i>Orders
                                                            </a>
                                                            <a class="dropdown-item" href="#"
                                                                data-supplier-id="<?php echo $supplier->supplier_id; ?>"
                                                                data-supplier-name="<?php echo htmlspecialchars($supplier->supplier_name, ENT_QUOTES); ?>"
                                                                onclick="linkProducts(this.getAttribute('data-supplier-id'), this.getAttribute('data-supplier-name'))">
                                                                <i class="fas fa-link mr-2"></i>Link Products
                                                            </a>
                                                            <a class="dropdown-item" href="#"
                                                                onclick="toggleSupplierStatus(<?php echo $supplier->supplier_id; ?>, '<?php echo $status; ?>')">
                                                                <i
                                                                    class="fas fa-toggle-<?php echo $status === 'active' ? 'off' : 'on'; ?> mr-2"></i>
                                                                <?php echo $status === 'active' ? 'Deactivate' : 'Activate'; ?>
                                                            </a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item text-danger" href="#"
                                                                onclick="deleteSupplier(<?php echo $supplier->supplier_id; ?>)">
                                                                <i class="fas fa-archive mr-2"></i>Archive
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-users fa-3x mb-3 text-muted"></i>
                                            <h5>No suppliers found</h5>
                                            <p>Get started by adding your first supplier.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Controls -->
                    <div class="card-footer">
                        <div class="row align-items-center">
                            <div class="col-md-6 offset-md-3">
                                <?php if (isset($data['pagination']) && $data['pagination']['total_pages'] > 1): ?>
                                    <nav aria-label="Suppliers pagination">
                                        <ul class="pagination pagination-sm justify-content-center mb-0">
                                            <!-- Previous Button -->
                                            <?php if ($data['pagination']['current_page'] > 1): ?>
                                                <li class="page-item">
                                                    <a class="page-link"
                                                        href="<?php echo buildPaginationUrl($data['pagination']['current_page'] - 1, $data['pagination']['per_page']); ?>">
                                                        <i class="fas fa-chevron-left"></i>
                                                    </a>
                                                </li>
                                            <?php else: ?>
                                                <li class="page-item disabled">
                                                    <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                                                </li>
                                            <?php endif; ?>

                                            <!-- Page Numbers -->
                                            <?php
                                            $start_page = max(1, $data['pagination']['current_page'] - 2);
                                            $end_page = min($data['pagination']['total_pages'], $data['pagination']['current_page'] + 2);

                                            // Show first page if not in range
                                            if ($start_page > 1): ?>
                                                <li class="page-item">
                                                    <a class="page-link"
                                                        href="<?php echo buildPaginationUrl(1, $data['pagination']['per_page']); ?>">1</a>
                                                </li>
                                                <?php if ($start_page > 2): ?>
                                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                                <?php endif;
                                            endif;

                                            // Show page numbers in range
                                            for ($i = $start_page; $i <= $end_page; $i++): ?>
                                                <li
                                                    class="page-item <?php echo ($i == $data['pagination']['current_page']) ? 'active' : ''; ?>">
                                                    <a class="page-link"
                                                        href="<?php echo buildPaginationUrl($i, $data['pagination']['per_page']); ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php endfor;

                                            // Show last page if not in range
                                            if ($end_page < $data['pagination']['total_pages']):
                                                if ($end_page < $data['pagination']['total_pages'] - 1): ?>
                                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                                <?php endif; ?>
                                                <li class="page-item">
                                                    <a class="page-link"
                                                        href="<?php echo buildPaginationUrl($data['pagination']['total_pages'], $data['pagination']['per_page']); ?>"><?php echo $data['pagination']['total_pages']; ?></a>
                                                </li>
                                            <?php endif; ?>

                                            <!-- Next Button -->
                                            <?php if ($data['pagination']['current_page'] < $data['pagination']['total_pages']): ?>
                                                <li class="page-item">
                                                    <a class="page-link"
                                                        href="<?php echo buildPaginationUrl($data['pagination']['current_page'] + 1, $data['pagination']['per_page']); ?>">
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
                            <div class="col-md-3">
                                <?php if (isset($data['pagination'])): ?>
                                    <small class="text-muted text-right d-block">
                                        Showing <?php echo $data['pagination']['start_record']; ?> to
                                        <?php echo $data['pagination']['end_record']; ?>
                                        of <?php echo $data['pagination']['total_records']; ?> entries
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="card-footer bg-light" id="bulkActions" style="display: none;">
                        <div class="d-flex align-items-center">
                            <span class="mr-3" id="selectedCount">0 items selected</span>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" onclick="bulkEditSuppliers()">
                                    <i class="fas fa-edit mr-1"></i>Bulk Edit
                                </button>
                                <button type="button" class="btn btn-outline-success" onclick="bulkActivateSuppliers()">
                                    <i class="fas fa-check mr-1"></i>Activate
                                </button>
                                <button type="button" class="btn btn-outline-warning"
                                    onclick="bulkDeactivateSuppliers()">
                                    <i class="fas fa-pause mr-1"></i>Deactivate
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="bulkDeleteSuppliers()">
                                    <i class="fas fa-archive mr-1"></i>Archive
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pass PHP data to JavaScript
        window.URLROOT = '<?php echo URLROOT; ?>';
        window.suppliersData = <?php echo json_encode($data['suppliers'] ?? []); ?>;

        // Debug: Log the data being passed
        console.log('PHP Suppliers Data:', window.suppliersData);
        console.log('Data length:', window.suppliersData ? window.suppliersData.length : 'No data');

        document.addEventListener('DOMContentLoaded', function () {
            // Charts removed - no initialization needed
        });

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.supplier-checkbox');

            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });

            updateBulkActions();
        }

        function updateBulkActions() {
            const checkboxes = document.querySelectorAll('.supplier-checkbox:checked');
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');

            if (checkboxes.length > 0) {
                bulkActions.style.display = 'block';
                selectedCount.textContent = `${checkboxes.length} item${checkboxes.length > 1 ? 's' : ''} selected`;
            } else {
                bulkActions.style.display = 'none';
            }
        }

        // Supplier Action Functions
        function toggleSupplierStatus(supplierId, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            const action = newStatus === 'active' ? 'activate' : 'deactivate';

            if (confirm(`Are you sure you want to ${action} this supplier?`)) {
                const formData = new FormData();
                formData.append('supplier_id', supplierId);
                formData.append('status', newStatus);

                fetch(`${window.URLROOT}/suppliers/updateStatus`, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(`Supplier ${action}d successfully`, 'success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showNotification(data.error || `Failed to ${action} supplier`, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred', 'error');
                    });
            }
        }

        function deleteSupplier(supplierId) {
            if (confirm('Archive this supplier?\n\nThis will hide the supplier from active lists but preserve all records and history.\n\nYou can restore this supplier later if needed.\n\nProceed to archive?')) {
                // Use fetch for AJAX request instead of redirect
                fetch(`${window.URLROOT}/suppliers/delete/${supplierId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Supplier archived successfully!');
                            location.reload();
                        } else {
                            alert('Error archiving supplier: ' + (data.error || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error archiving supplier');
                    });
            }
        }

        // New action functions to match Products Management style
        function viewSupplier(supplierId) {
            window.location.href = `${window.URLROOT}/suppliers/view/${supplierId}`;
        }

        function editSupplier(supplierId) {
            window.location.href = `${window.URLROOT}/suppliers/edit/${supplierId}`;
        }

        function viewSupplierPerformance(supplierId) {
            window.location.href = `${window.URLROOT}/suppliers/performance/${supplierId}`;
        }

        function viewSupplierOrders(supplierId) {
            window.location.href = `${window.URLROOT}/suppliers/orders/${supplierId}`;
        }

        // Filter Functions - Enhanced with server-side filtering
        function filterSuppliers(filterType) {
            const currentUrl = new URL(window.location.href);

            // Set filter parameters
            if (filterType === 'all') {
                currentUrl.searchParams.delete('status');
                currentUrl.searchParams.delete('tier');
            } else if (filterType === 'active' || filterType === 'inactive') {
                currentUrl.searchParams.set('status', filterType);
                currentUrl.searchParams.delete('tier');
            } else {
                currentUrl.searchParams.set('tier', filterType);
                currentUrl.searchParams.delete('status');
            }

            currentUrl.searchParams.set('page', '1'); // Reset to first page
            window.location.href = currentUrl.toString();
        }

        // Pagination Functions
        function changeItemsPerPage() {
            const perPage = document.getElementById('itemsPerPage').value;
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('per_page', perPage);
            currentUrl.searchParams.set('page', '1'); // Reset to first page
            window.location.href = currentUrl.toString();
        }

        // Export Functions
        function exportSuppliers(format) {
            const selectedSuppliers = Array.from(document.querySelectorAll('.supplier-checkbox:checked')).map(cb => cb.value);
            const exportType = selectedSuppliers.length > 0 ? 'selected' : 'all';

            const params = new URLSearchParams({
                format: format,
                type: exportType,
                suppliers: selectedSuppliers.join(',')
            });

            window.location.href = `${window.URLROOT}/suppliers/export?${params}`;
        }

        // Bulk Operations
        function bulkEditSuppliers() {
            const selectedSuppliers = Array.from(document.querySelectorAll('.supplier-checkbox:checked')).map(cb => cb.value);
            if (selectedSuppliers.length === 0) {
                alert('Please select suppliers to edit');
                return;
            }
            window.location.href = `${window.URLROOT}/suppliers/bulkEdit?suppliers=${selectedSuppliers.join(',')}`;
        }

        function bulkActivateSuppliers() {
            const selectedSuppliers = Array.from(document.querySelectorAll('.supplier-checkbox:checked')).map(cb => cb.value);
            if (selectedSuppliers.length === 0) {
                alert('Please select suppliers to activate');
                return;
            }

            if (confirm(`Activate ${selectedSuppliers.length} selected suppliers?`)) {
                const formData = new FormData();
                formData.append('suppliers', selectedSuppliers.join(','));
                formData.append('action', 'activate');

                fetch(`${window.URLROOT}/suppliers/bulkUpdateStatus`, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('Suppliers activated successfully', 'success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showNotification(data.error || 'Failed to activate suppliers', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred while activating suppliers', 'error');
                    });
            }
        }

        function bulkDeactivateSuppliers() {
            const selectedSuppliers = Array.from(document.querySelectorAll('.supplier-checkbox:checked')).map(cb => cb.value);
            if (selectedSuppliers.length === 0) {
                alert('Please select suppliers to deactivate');
                return;
            }

            if (confirm(`Deactivate ${selectedSuppliers.length} selected suppliers?`)) {
                const formData = new FormData();
                formData.append('suppliers', selectedSuppliers.join(','));
                formData.append('action', 'deactivate');

                fetch(`${window.URLROOT}/suppliers/bulkUpdateStatus`, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('Suppliers deactivated successfully', 'success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showNotification(data.error || 'Failed to deactivate suppliers', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred while deactivating suppliers', 'error');
                    });
            }
        }

        function bulkDeleteSuppliers() {
            const selectedSuppliers = Array.from(document.querySelectorAll('.supplier-checkbox:checked')).map(cb => cb.value);
            if (selectedSuppliers.length === 0) {
                alert('Please select suppliers to archive');
                return;
            }

            if (confirm(`Archive ${selectedSuppliers.length} selected suppliers?\n\nThis will hide the suppliers from active lists but preserve all records and history.\n\nYou can restore these suppliers later if needed.\n\nProceed to archive?`)) {
                // Use AJAX instead of redirect for proper handling
                const formData = new FormData();
                selectedSuppliers.forEach(id => formData.append('supplier_ids[]', id));

                fetch(`${window.URLROOT}/suppliers/bulkDelete`, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(`${selectedSuppliers.length} suppliers archived successfully!`);
                            location.reload();
                        } else {
                            alert('Error archiving suppliers: ' + (data.error || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error archiving suppliers');
                    });
            }
        }

        function showNotification(message, type = 'info') {
            // Simple notification function
            const alertClass = type === 'success' ? 'alert-success' :
                type === 'error' ? 'alert-danger' : 'alert-info';

            const notification = document.createElement('div');
            notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;

            document.body.appendChild(notification);

            // Auto-remove after 3 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
        }

        // Initialize tooltips if jQuery + Bootstrap are available
        (function () {
            if (window.jQuery && typeof window.jQuery === 'function' && window.jQuery.fn && window.jQuery.fn.tooltip) {
                window.jQuery(function () {
                    window.jQuery('[data-toggle="tooltip"]').tooltip();
                });
            }
        })();

        // Column Sorting functionality
        function sortTable(column) {
            console.log('Sorting by column:', column); // Debug output
            const currentUrl = new URL(window.location.href);
            const currentSort = currentUrl.searchParams.get('sort');
            const currentOrder = currentUrl.searchParams.get('order');

            // Determine new sort order
            let newOrder = 'ASC';
            if (currentSort === column && currentOrder === 'ASC') {
                newOrder = 'DESC';
            }

            // Update URL parameters
            currentUrl.searchParams.set('sort', column);
            currentUrl.searchParams.set('order', newOrder);
            currentUrl.searchParams.set('page', '1'); // Reset to first page

            console.log('New URL:', currentUrl.toString()); // Debug output

            // Navigate to new URL
            window.location.href = currentUrl.toString();
        }

        // Initialize sorting event listeners (vanilla JS - works regardless of jQuery load timing)
        (function () {
            // Apply pointer cursor to any sortable headers and keep it updated for dynamic changes
            function applyPointer() {
                document.querySelectorAll('.sortable').forEach(function (el) {
                    el.style.cursor = 'pointer';
                });
            }

            applyPointer();

            // Observe DOM changes and re-apply pointer style when new sortable elements are inserted
            try {
                const observer = new MutationObserver(function () {
                    applyPointer();
                });
                observer.observe(document.body, { childList: true, subtree: true });
            } catch (e) {
                // MutationObserver may not be available in very old browsers; that's fine
            }

            // Delegated click handler: walks up the DOM to find a `.sortable` ancestor
            document.addEventListener('click', function (e) {
                var el = e.target;
                while (el && el !== document) {
                    if (el.classList && el.classList.contains('sortable')) {
                        e.preventDefault();
                        var sortColumn = (el.dataset && el.dataset.sort) ? el.dataset.sort : el.getAttribute('data-sort');
                        if (sortColumn) {
                            sortTable(sortColumn);
                        }
                        break;
                    }
                    el = el.parentNode;
                }
        });
        })();
    </script>

    <!-- Link Products Modal -->
    <div class="modal fade" id="LinkProductsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content kpi-card">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-link mr-2"></i>Link Products to Supplier
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="link_supplier_id" name="supplier_id">
                    <div class="form-group mb-3">
                        <label><strong>Supplier:</strong></label>
                        <div id="link_supplier_name" class="form-control-plaintext font-weight-bold text-primary">
                            <!-- Supplier name will be loaded here -->
                        </div>
                    </div>

                    <!-- Existing Products Display -->
                    <div id="existing_products_section" class="mb-4" style="display: none;">
                        <h6 class="mb-3"><i class="fas fa-boxes mr-2"></i>Currently Linked Products</h6>
                        <div id="existing_products_list" class="border rounded p-2" style="background-color: #f8f9fa;">
                            <!-- Existing products will be loaded here -->
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label>Search Products:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" id="product_search" class="form-control"
                                placeholder="Search by product name, SKU, or barcode..." autocomplete="off">
                        </div>
                        <small class="form-text text-muted">Search will update results automatically as you type</small>
                    </div>
                    <div id="products_loading" class="text-center py-3" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Loading products...
                    </div>
                    <div id="products_list" class="products-list">
                        <!-- Products will be loaded here -->
                    </div>

                    <!-- Product Details Form -->
                    <div id="product_details_form" class="mt-4 p-3 border rounded bg-light" style="display: none;">
                        <h6 class="mb-3"><i class="fas fa-info-circle mr-2"></i>Supplier Pricing Details</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supplier_purchase_price">Purchase Price *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">₹</span>
                                        </div>
                                        <input type="number" id="supplier_purchase_price" class="form-control"
                                            placeholder="0.00" step="0.01" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supplier_product_sku">Supplier SKU</label>
                                    <input type="text" id="supplier_product_sku" class="form-control"
                                        placeholder="Supplier's product code">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supplier_lead_time_days">Lead Time (Days)</label>
                                    <input type="number" id="supplier_lead_time_days" class="form-control"
                                        placeholder="7" value="7" min="1" max="365">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supplier_min_order_quantity">Minimum Order Quantity</label>
                                    <input type="number" id="supplier_min_order_quantity" class="form-control"
                                        placeholder="1" value="1" min="1">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supplier_product_rating">Quality Rating</label>
                                    <select id="supplier_product_rating" class="form-control">
                                        <option value="5">★★★★★ Excellent</option>
                                        <option value="4" selected>★★★★☆ Good</option>
                                        <option value="3">★★★☆☆ Average</option>
                                        <option value="2">★★☆☆☆ Below Average</option>
                                        <option value="1">★☆☆☆☆ Poor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supplier_product_notes">Notes</label>
                                    <textarea id="supplier_product_notes" class="form-control" rows="2"
                                        placeholder="Additional notes about this product from this supplier..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirm_link_product"
                        onclick="confirmLinkProduct()" disabled>
                        <i class="fas fa-link mr-1"></i>Link Product
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Link Products functionality
        let selectedProductId = null;

        function linkProducts(supplierId, supplierName) {
            // Set supplier details
            document.getElementById('link_supplier_id').value = supplierId;

            // Handle supplier name with better error handling
            const supplierNameElement = document.getElementById('link_supplier_name');
            if (supplierNameElement) {
                const displayName = supplierName && supplierName.trim() ? supplierName.trim() : 'Unknown Supplier';
                supplierNameElement.textContent = displayName;
            }

            // Reset modal state
            document.getElementById('product_search').value = '';
            document.getElementById('confirm_link_product').disabled = true;
            selectedProductId = null;

            // Reset existing products display
            document.getElementById('existing_products_section').style.display = 'none';
            document.getElementById('existing_products_list').innerHTML = '';

            // Reset product details form
            document.getElementById('product_details_form').style.display = 'none';
            document.getElementById('supplier_purchase_price').value = '';
            document.getElementById('supplier_product_sku').value = '';
            document.getElementById('supplier_lead_time_days').value = '7';
            document.getElementById('supplier_min_order_quantity').value = '1';
            document.getElementById('supplier_product_notes').value = '';
            document.getElementById('supplier_product_rating').value = '4';

            // Set up search functionality for this modal instance
            setupProductSearch();

            // Add validation event listeners
            document.getElementById('supplier_purchase_price').addEventListener('input', validateProductForm);

            // Load existing products first
            loadExistingProducts(supplierId);

            // Show modal and load products
            var linkModal = document.getElementById('LinkProductsModal');
            if (linkModal) {
                // Use Bootstrap modal if available via jQuery, else use simple show
                if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
                    window.jQuery('#LinkProductsModal').modal('show');
                    window.jQuery('#LinkProductsModal').on('shown.bs.modal', function () {
                        var ps = document.getElementById('product_search'); if (ps) ps.focus();
                    });
                } else {
                    linkModal.style.display = 'block';
                    // basic focus after a short delay
                    setTimeout(function () { var ps = document.getElementById('product_search'); if (ps) ps.focus(); }, 200);
                }
            }

            loadProductsForLinking();
        }

        // Separate function to set up product search
        function setupProductSearch() {
            const productSearchInput = document.getElementById('product_search');
            if (productSearchInput) {
                // Remove any existing event listeners
                productSearchInput.removeEventListener('input', productSearchHandler);

                // Add the event listener
                productSearchInput.addEventListener('input', productSearchHandler);
            }
        }

        // Load existing products for the supplier
        function loadExistingProducts(supplierId) {
            console.log('Loading existing products for supplier:', supplierId);

            fetch(`${window.URLROOT}/suppliers/getSupplierProducts/${supplierId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Existing products data:', data);
                    displayExistingProducts(data.products || []);
                })
                .catch(error => {
                    console.error('Error loading existing products:', error);
                    // Don't show an error message, just continue without existing products display
                });
        }

        function displayExistingProducts(products) {
            const existingSection = document.getElementById('existing_products_section');
            const existingList = document.getElementById('existing_products_list');

            if (!products || products.length === 0) {
                existingSection.style.display = 'none';
                return;
            }

            let html = '';

            products.forEach(product => {
                const price = product.purchase_price ? parseFloat(product.purchase_price).toFixed(2) : 'Not set';
                const leadTime = product.lead_time_days || 'Not set';
                const minOrderQty = product.min_order_quantity || 'Not set';
                const rating = product.supplier_rating || 'Not rated';

                html += `
                <div class="existing-product-item mb-2 p-2 border rounded border-secondary">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <strong>${product.product_name}</strong>
                            <br><small class="text-muted">SKU: ${product.sku || 'N/A'}</small>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">Price:</small><br>
                            <span class="font-weight-bold">₹${price}</span>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">Lead Time:</small><br>
                            <span>${leadTime} days</span>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">Min Order:</small><br>
                            <span>${minOrderQty}</span>
                        </div>
                        <div class="col-md-1">
                            <small class="text-muted">Rating:</small><br>
                            <span>${rating}/5</span>
                        </div>
                        <div class="col-md-1 text-right">
                            <button class="btn btn-sm btn-outline-danger" 
                                onclick="unlinkProduct(${product.product_id})" 
                                title="Remove Link">
                                <i class="fas fa-unlink"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            });

            if (products.length > 0) {
                html = `
                <div class="mb-2">
                    <small class="text-muted">This supplier currently has ${products.length} product${products.length > 1 ? 's' : ''} linked:</small>
                </div>
                ${html}
            `;
            }

            existingList.innerHTML = html;
            existingSection.style.display = 'block';
        }

        // Search handler function
        let productSearchTimeout;
        function productSearchHandler() {
            clearTimeout(productSearchTimeout);
            productSearchTimeout = setTimeout(() => {
                const searchTerm = this.value.trim();
                console.log('Searching for products with term:', searchTerm); // Debug log
                loadProductsForLinking(searchTerm);
            }, 300);
        }

        function loadProductsForLinking(searchTerm = '') {
            console.log('loadProductsForLinking called with searchTerm:', searchTerm); // Debug log

            const loadingDiv = document.getElementById('products_loading');
            const listDiv = document.getElementById('products_list');

            if (!loadingDiv || !listDiv) {
                console.error('Required DOM elements not found');
                return;
            }

            loadingDiv.style.display = 'block';
            listDiv.innerHTML = '';

            const url = searchTerm
                ? `${window.URLROOT}/api/getProducts.php?search=${encodeURIComponent(searchTerm)}`
                : `${window.URLROOT}/api/getProducts.php`;

            console.log('Fetching products from URL:', url); // Debug log

            fetch(url)
                .then(response => {
                    console.log('Response status:', response.status); // Debug log
                    return response.json();
                })
                .then(data => {
                    console.log('Products data received:', data); // Debug log
                    loadingDiv.style.display = 'none';

                    if (data.success && data.products && data.products.length > 0) {
                        let html = '<div class="table-responsive"><table class="table table-hover"><tbody>';

                        data.products.forEach(product => {
                            html += `
                            <tr class="product-row" data-product-id="${product.product_id}">
                                <td style="width: 40px;">
                                    <input type="radio" name="selected_product" value="${product.product_id}" 
                                        onchange="selectProduct(${product.product_id}, '${product.product_name.replace(/'/g, "\\'")}')">
                                </td>
                                <td>
                                    <strong>${product.product_name}</strong>
                                    <br><small class="text-muted">SKU: ${product.sku || 'N/A'}</small>
                                </td>
                                <td>
                                    <span class="badge badge-primary">₹${product.selling_price || '0.00'}</span>
                                    <br><small class="text-muted">Current Price</small>
                                </td>
                                <td>
                                    <span class="badge badge-info">${product.current_inventory || 0}</span>
                                    <br><small class="text-muted">In Stock</small>
                                </td>
                                <td>
                                    <small class="text-muted">${product.category_name || 'Uncategorized'}</small>
                                </td>
                            </tr>
                        `;
                        });

                        html += '</tbody></table></div>';
                        listDiv.innerHTML = html;
                    } else {
                        const message = searchTerm
                            ? `No products found matching "${searchTerm}".`
                            : 'No products found.';
                        listDiv.innerHTML = `<div class="alert alert-info"><i class="fas fa-info-circle mr-2"></i>${message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error fetching products:', error); // Debug log
                    loadingDiv.style.display = 'none';
                    listDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle mr-2"></i>Error loading products. Please try again.</div>';
                });
        }

        function selectProduct(productId, productName) {
            // Clear previous selection
            document.querySelectorAll('.product-row').forEach(row => {
                row.classList.remove('table-active');
            });

            // Mark new selection
            const selectedRow = document.querySelector(`[data-product-id="${productId}"]`);
            if (selectedRow) {
                selectedRow.classList.add('table-active');
                selectedRow.querySelector('input[type="radio"]').checked = true;
            }

            // Show product details form
            const detailsForm = document.getElementById('product_details_form');
            detailsForm.style.display = 'block';

            // Focus on purchase price field
            setTimeout(() => {
                document.getElementById('supplier_purchase_price').focus();
            }, 100);

            // Enable confirm button (but require purchase price validation)
            selectedProductId = productId;
            validateProductForm();
        }

        function validateProductForm() {
            const purchasePrice = document.getElementById('supplier_purchase_price').value;
            const confirmButton = document.getElementById('confirm_link_product');

            // Enable button only if product is selected and purchase price is provided
            if (selectedProductId && purchasePrice && parseFloat(purchasePrice) > 0) {
                confirmButton.disabled = false;
            } else {
                confirmButton.disabled = true;
            }
        }

        function confirmLinkProduct() {
            if (!selectedProductId) {
                showNotification('Please select a product', 'error');
                return;
            }

            // Submission guard and validation
            console.log('Initiating submission...');
            const purchasePrice = document.getElementById('supplier_purchase_price').value;
            if (!purchasePrice || isNaN(purchasePrice) || parseFloat(purchasePrice) <= 0) {
                showNotification('Please enter a valid purchase price', 'error');
                document.getElementById('supplier_purchase_price').focus();
                return;
            }

            const supplierId = document.getElementById('link_supplier_id').value;
            console.log('Linking product:', selectedProductId, 'to supplier:', supplierId);

            const formData = new FormData();
            formData.append('product_id', selectedProductId);
            formData.append('supplier_id', supplierId);
            formData.append('purchase_price', purchasePrice);
            formData.append('supplier_sku', document.getElementById('supplier_product_sku').value || '');
            formData.append('lead_time_days', document.getElementById('supplier_lead_time_days').value || '7');
            formData.append('min_order_quantity', document.getElementById('supplier_min_order_quantity').value || '1');
            formData.append('supplier_notes', document.getElementById('supplier_product_notes').value || '');
            formData.append('supplier_rating', document.getElementById('supplier_product_rating').value || '4');

            fetch(`${window.URLROOT}/index.php?url=suppliers/linkProduct`, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    console.log('Link product response data:', data);
                    if (data.success) {
                        alert('Submission successful!');
                        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.modal) {
                            window.jQuery('#LinkProductsModal').modal('hide');
                        } else {
                            var m = document.getElementById('LinkProductsModal'); if (m) m.style.display = 'none';
                        }
                        showNotification('Product linked successfully', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        alert('Submission failed! Details: ' + (data.error || 'unknown'));
                        showNotification(data.error || 'Failed to link product', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error linking product:', error);
                    alert('Submission failed! Details: ' + error.message);
                    showNotification('An error occurred while linking product', 'error');
                });
        }

        function unlinkProduct(productId) {
            const supplierId = document.getElementById('link_supplier_id').value;

            if (confirm('Are you sure you want to remove this product link? This action cannot be undone.')) {
                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('supplier_id', supplierId);

                fetch(`${window.URLROOT}/index.php?url=suppliers/unlinkProduct`, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadExistingProducts(supplierId);
                            showNotification('Product unlinked successfully', 'success');
                        } else {
                            showNotification(data.error || 'Failed to unlink product', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error unlinking product:', error);
                        showNotification('An error occurred while unlinking product', 'error');
                    });
            }
        }

        function showNotification(message, type = 'info') {
            // Simple notification function
            const alertClass = type === 'success' ? 'alert-success' :
                type === 'error' ? 'alert-danger' : 'alert-info';

            const notification = document.createElement('div');
            notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;

            document.body.appendChild(notification);

            // Auto-remove after 3 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
        }, 3000);
        }
    </script>

    <?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>