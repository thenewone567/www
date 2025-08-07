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
                        Reports
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h1 class="theme-page-title">
                <i class="fa-solid fa-chart-bar theme-icon"></i>
                Receiving Reports
            </h1>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php flash('receive_message'); ?>

    <!-- Report Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="theme-card">
                <div class="theme-card-header">
                    <h5 class="theme-card-title">
                        <i class="fa-solid fa-filter"></i> Report Filters
                    </h5>
                </div>
                <div class="theme-card-body">
                    <form method="GET" id="reportFilters">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="report_type" class="form-label">Report Type</label>
                                <select id="report_type" name="report_type" class="form-select theme-form-control">
                                    <option value="summary" <?php echo (($_GET['report_type'] ?? 'summary') === 'summary') ? 'selected' : ''; ?>>
                                        Summary Report
                                    </option>
                                    <option value="detailed" <?php echo (($_GET['report_type'] ?? '') === 'detailed') ? 'selected' : ''; ?>>
                                        Detailed Report
                                    </option>
                                    <option value="performance" <?php echo (($_GET['report_type'] ?? '') === 'performance') ? 'selected' : ''; ?>>
                                        Performance Report
                                    </option>
                                    <option value="supplier" <?php echo (($_GET['report_type'] ?? '') === 'supplier') ? 'selected' : ''; ?>>
                                        Supplier Report
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" id="date_from" name="date_from" class="form-control theme-form-control"
                                       value="<?php echo $_GET['date_from'] ?? date('Y-m-01'); ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" id="date_to" name="date_to" class="form-control theme-form-control"
                                       value="<?php echo $_GET['date_to'] ?? date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="supplier_filter" class="form-label">Supplier</label>
                                <select id="supplier_filter" name="supplier" class="form-select theme-form-control">
                                    <option value="">All Suppliers</option>
                                    <?php if (isset($data['suppliers'])): ?>
                                        <?php foreach ($data['suppliers'] as $supplier): ?>
                                            <option value="<?php echo $supplier->id; ?>" 
                                                    <?php echo (isset($_GET['supplier']) && $_GET['supplier'] == $supplier->id) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($supplier->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn theme-btn-primary">
                                        <i class="fa-solid fa-chart-line"></i> Generate
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="theme-action-bar">
                                    <div class="theme-action-group">
                                        <button type="button" class="btn theme-btn-success" onclick="exportReport('pdf')">
                                            <i class="fa-solid fa-file-pdf"></i> Export PDF
                                        </button>
                                        <button type="button" class="btn theme-btn-info" onclick="exportReport('excel')">
                                            <i class="fa-solid fa-file-excel"></i> Export Excel
                                        </button>
                                        <button type="button" class="btn theme-btn-secondary" onclick="printReport()">
                                            <i class="fa-solid fa-print"></i> Print
                                        </button>
                                    </div>
                                    <div class="theme-action-group">
                                        <button type="button" class="btn theme-btn-outline-secondary" onclick="clearFilters()">
                                            <i class="fa-solid fa-refresh"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="theme-stat-card theme-stat-primary">
                <div class="theme-stat-content">
                    <div class="theme-stat-icon">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div class="theme-stat-details">
                        <h3 class="theme-stat-number">
                            <?php echo isset($data['stats']['total_receipts']) ? $data['stats']['total_receipts'] : 0; ?>
                        </h3>
                        <p class="theme-stat-label">Total Receipts</p>
                        <small class="theme-stat-description">In selected period</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="theme-stat-card theme-stat-success">
                <div class="theme-stat-content">
                    <div class="theme-stat-icon">
                        <i class="fa-solid fa-rupee-sign"></i>
                    </div>
                    <div class="theme-stat-details">
                        <h3 class="theme-stat-number">
                            ₹<?php echo isset($data['stats']['total_value']) ? number_format($data['stats']['total_value'], 0) : 0; ?>
                        </h3>
                        <p class="theme-stat-label">Total Value</p>
                        <small class="theme-stat-description">Inventory received</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="theme-stat-card theme-stat-warning">
                <div class="theme-stat-content">
                    <div class="theme-stat-icon">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div class="theme-stat-details">
                        <h3 class="theme-stat-number">
                            <?php echo isset($data['stats']['avg_processing_time']) ? $data['stats']['avg_processing_time'] : 0; ?>
                        </h3>
                        <p class="theme-stat-label">Avg Processing</p>
                        <small class="theme-stat-description">Hours per receipt</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="theme-stat-card theme-stat-info">
                <div class="theme-stat-content">
                    <div class="theme-stat-icon">
                        <i class="fa-solid fa-percentage"></i>
                    </div>
                    <div class="theme-stat-details">
                        <h3 class="theme-stat-number">
                            <?php echo isset($data['stats']['completion_rate']) ? number_format($data['stats']['completion_rate'], 1) : 0; ?>%
                        </h3>
                        <p class="theme-stat-label">Completion Rate</p>
                        <small class="theme-stat-description">On-time receipts</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Content -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Main Report -->
            <div class="theme-card">
                <div class="theme-card-header">
                    <h5 class="theme-card-title">
                        <i class="fa-solid fa-chart-line"></i> 
                        <?php 
                        $reportType = $_GET['report_type'] ?? 'summary';
                        echo ucfirst($reportType) . ' Report';
                        ?>
                    </h5>
                    <div class="theme-card-actions">
                        <small class="text-muted">
                            <?php echo date('M j, Y', strtotime($_GET['date_from'] ?? date('Y-m-01'))); ?> - 
                            <?php echo date('M j, Y', strtotime($_GET['date_to'] ?? date('Y-m-d'))); ?>
                        </small>
                    </div>
                </div>
                <div class="theme-card-body">
                    <?php if (isset($data['report_data']) && !empty($data['report_data'])): ?>
                        <?php if ($reportType === 'summary'): ?>
                            <!-- Summary Report -->
                            <div class="row">
                                <div class="col-md-6">
                                    <canvas id="receiptChart" width="400" height="200"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <div class="theme-summary-list">
                                        <div class="theme-summary-item">
                                            <span class="theme-summary-label">Total Purchase Orders</span>
                                            <span class="theme-summary-value"><?php echo $data['report_data']['total_pos'] ?? 0; ?></span>
                                        </div>
                                        <div class="theme-summary-item">
                                            <span class="theme-summary-label">Completed Receipts</span>
                                            <span class="theme-summary-value text-success"><?php echo $data['report_data']['completed'] ?? 0; ?></span>
                                        </div>
                                        <div class="theme-summary-item">
                                            <span class="theme-summary-label">Partial Receipts</span>
                                            <span class="theme-summary-value text-warning"><?php echo $data['report_data']['partial'] ?? 0; ?></span>
                                        </div>
                                        <div class="theme-summary-item">
                                            <span class="theme-summary-label">Pending Receipts</span>
                                            <span class="theme-summary-value text-danger"><?php echo $data['report_data']['pending'] ?? 0; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                        <?php elseif ($reportType === 'detailed'): ?>
                            <!-- Detailed Report Table -->
                            <div class="theme-table-container">
                                <table class="table theme-table">
                                    <thead>
                                        <tr>
                                            <th>PO Number</th>
                                            <th>Supplier</th>
                                            <th>Order Date</th>
                                            <th>Received Date</th>
                                            <th>Items</th>
                                            <th>Value</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['report_data'] as $item): ?>
                                            <tr>
                                                <td><strong>#<?php echo $item->purchase_number ?? $item->purchase_id; ?></strong></td>
                                                <td><?php echo htmlspecialchars($item->supplier_name ?? 'Unknown'); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($item->order_date)); ?></td>
                                                <td>
                                                    <?php if ($item->received_date): ?>
                                                        <?php echo date('M j, Y', strtotime($item->received_date)); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not received</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $item->total_items ?? 0; ?></td>
                                                <td>₹<?php echo number_format($item->total_value ?? 0, 2); ?></td>
                                                <td>
                                                    <?php
                                                    $statusClass = 'theme-badge-secondary';
                                                    if ($item->status === 'received') $statusClass = 'theme-badge-success';
                                                    elseif ($item->status === 'partially_received') $statusClass = 'theme-badge-warning';
                                                    ?>
                                                    <span class="theme-badge <?php echo $statusClass; ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $item->status)); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        
                        <?php elseif ($reportType === 'performance'): ?>
                            <!-- Performance Report -->
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <canvas id="performanceChart" width="800" height="400"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h6>Top Performing Metrics</h6>
                                    <div class="theme-metric-list">
                                        <div class="theme-metric-item">
                                            <span class="theme-metric-label">Fastest Processing</span>
                                            <span class="theme-metric-value text-success">
                                                <?php echo $data['report_data']['fastest_processing'] ?? 'N/A'; ?> hours
                                            </span>
                                        </div>
                                        <div class="theme-metric-item">
                                            <span class="theme-metric-label">Most Active User</span>
                                            <span class="theme-metric-value">
                                                <?php echo $data['report_data']['most_active_user'] ?? 'N/A'; ?>
                                            </span>
                                        </div>
                                        <div class="theme-metric-item">
                                            <span class="theme-metric-label">Peak Receiving Hour</span>
                                            <span class="theme-metric-value">
                                                <?php echo $data['report_data']['peak_hour'] ?? 'N/A'; ?>:00
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Areas for Improvement</h6>
                                    <div class="theme-improvement-list">
                                        <div class="theme-improvement-item">
                                            <i class="fa-solid fa-triangle-exclamation text-warning"></i>
                                            <span>Average processing time: <?php echo $data['report_data']['avg_processing'] ?? 'N/A'; ?> hours</span>
                                        </div>
                                        <div class="theme-improvement-item">
                                            <i class="fa-solid fa-clock text-info"></i>
                                            <span>Pending receipts older than 24h: <?php echo $data['report_data']['overdue_count'] ?? 0; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                        <?php elseif ($reportType === 'supplier'): ?>
                            <!-- Supplier Report -->
                            <div class="theme-table-container">
                                <table class="table theme-table">
                                    <thead>
                                        <tr>
                                            <th>Supplier</th>
                                            <th>Total Orders</th>
                                            <th>Completed</th>
                                            <th>Pending</th>
                                            <th>Total Value</th>
                                            <th>Avg Processing Time</th>
                                            <th>Performance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['report_data'] as $supplier): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($supplier->supplier_name); ?></strong>
                                                </td>
                                                <td><?php echo $supplier->total_orders ?? 0; ?></td>
                                                <td>
                                                    <span class="theme-badge theme-badge-success">
                                                        <?php echo $supplier->completed_orders ?? 0; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="theme-badge theme-badge-warning">
                                                        <?php echo $supplier->pending_orders ?? 0; ?>
                                                    </span>
                                                </td>
                                                <td>₹<?php echo number_format($supplier->total_value ?? 0, 2); ?></td>
                                                <td><?php echo number_format($supplier->avg_processing_time ?? 0, 1); ?>h</td>
                                                <td>
                                                    <?php 
                                                    $performance = $supplier->performance_score ?? 0;
                                                    $performanceClass = 'text-danger';
                                                    if ($performance >= 80) $performanceClass = 'text-success';
                                                    elseif ($performance >= 60) $performanceClass = 'text-warning';
                                                    ?>
                                                    <span class="<?php echo $performanceClass; ?>">
                                                        <?php echo number_format($performance, 1); ?>%
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    
                    <?php else: ?>
                        <div class="theme-empty-state">
                            <div class="theme-empty-icon">
                                <i class="fa-solid fa-chart-line"></i>
                            </div>
                            <h5 class="theme-empty-title">No Data Available</h5>
                            <p class="theme-empty-description">
                                No receiving data found for the selected period and filters.
                                Try adjusting your date range or removing filters.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Quick Insights -->
            <div class="theme-card mb-4">
                <div class="theme-card-header">
                    <h5 class="theme-card-title">
                        <i class="fa-solid fa-lightbulb"></i> Quick Insights
                    </h5>
                </div>
                <div class="theme-card-body">
                    <div class="theme-insight-list">
                        <div class="theme-insight-item">
                            <i class="fa-solid fa-trend-up text-success"></i>
                            <div>
                                <div class="fw-medium">Processing Efficiency</div>
                                <small class="text-muted">
                                    <?php echo isset($data['insights']['efficiency_trend']) ? $data['insights']['efficiency_trend'] : 'Improving by 15% this month'; ?>
                                </small>
                            </div>
                        </div>
                        <div class="theme-insight-item">
                            <i class="fa-solid fa-users text-info"></i>
                            <div>
                                <div class="fw-medium">Top Performer</div>
                                <small class="text-muted">
                                    <?php echo isset($data['insights']['top_performer']) ? $data['insights']['top_performer'] : 'John Doe processed 45 receipts'; ?>
                                </small>
                            </div>
                        </div>
                        <div class="theme-insight-item">
                            <i class="fa-solid fa-clock text-warning"></i>
                            <div>
                                <div class="fw-medium">Peak Hours</div>
                                <small class="text-muted">
                                    <?php echo isset($data['insights']['peak_hours']) ? $data['insights']['peak_hours'] : 'Most activity between 10 AM - 12 PM'; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recommendations -->
            <div class="theme-card">
                <div class="theme-card-header">
                    <h5 class="theme-card-title">
                        <i class="fa-solid fa-bullseye"></i> Recommendations
                    </h5>
                </div>
                <div class="theme-card-body">
                    <div class="theme-recommendation-list">
                        <div class="theme-recommendation-item">
                            <div class="theme-recommendation-priority high"></div>
                            <div>
                                <div class="fw-medium">Reduce Processing Time</div>
                                <small class="text-muted">Consider implementing barcode scanning to speed up receiving process.</small>
                            </div>
                        </div>
                        <div class="theme-recommendation-item">
                            <div class="theme-recommendation-priority medium"></div>
                            <div>
                                <div class="fw-medium">Supplier Communication</div>
                                <small class="text-muted">Set up automated notifications for overdue deliveries.</small>
                            </div>
                        </div>
                        <div class="theme-recommendation-item">
                            <div class="theme-recommendation-priority low"></div>
                            <div>
                                <div class="fw-medium">Staff Training</div>
                                <small class="text-muted">Provide training on efficient receiving procedures.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function clearFilters() {
    window.location.href = '<?php echo URLROOT; ?>/receiving/reports';
}

function exportReport(format) {
    const form = document.getElementById('reportFilters');
    const formData = new FormData(form);
    formData.append('export', format);
    
    const params = new URLSearchParams(formData).toString();
    window.open(`<?php echo URLROOT; ?>/receiving/export-report?${params}`, '_blank');
}

function printReport() {
    window.print();
}

// Initialize charts based on report type
document.addEventListener('DOMContentLoaded', function() {
    const reportType = '<?php echo $_GET['report_type'] ?? 'summary'; ?>';
    
    if (reportType === 'summary' && document.getElementById('receiptChart')) {
        const ctx = document.getElementById('receiptChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Partial', 'Pending'],
                datasets: [{
                    data: [
                        <?php echo $data['report_data']['completed'] ?? 0; ?>,
                        <?php echo $data['report_data']['partial'] ?? 0; ?>,
                        <?php echo $data['report_data']['pending'] ?? 0; ?>
                    ],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    if (reportType === 'performance' && document.getElementById('performanceChart')) {
        const ctx = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Processing Time (Hours)',
                    data: [8, 6, 5, 4],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
