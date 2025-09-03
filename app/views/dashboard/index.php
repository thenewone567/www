<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container theme-unified">
    <!-- Page Header -->
    <div class="theme-header">
        <div class="row align-items-center mb-4">
            <div class="col-12 col-lg-8">
                <h1 class="mb-0">
                    <i class="fas fa-tachometer-alt"></i>
                    Hardware Store Dashboard
                </h1>
                <p class="description">
                    Real-time analytics and performance insights
                    <br><small class="text-muted">
                        <i class="fa fa-clock mr-1"></i>Period: <?php echo $data['period'] ?? 30; ?> days |
                        Loaded: <?php echo date('Y-m-d H:i:s'); ?>
                    </small>
                </p>
            </div>
            <div class="col-12 col-lg-4 text-lg-right mt-3 mt-lg-0">
                <a href="<?php echo URLROOT; ?>/sales/add" class="btn btn-success btn-lg mr-2">
                    <i class="fas fa-plus"></i> New Sale
                </a>
                <a href="<?php echo URLROOT; ?>/purchases/add" class="btn btn-primary btn-lg mr-2">
                    <i class="fas fa-shopping-cart"></i> New Purchase
                </a>

                <!-- Period Selection Dropdown -->
                <div class="btn-group">
                    <button class="btn btn-outline-secondary btn-lg dropdown-toggle" type="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-calendar mr-1"></i>
                        <span id="current-period">
                            <?php
                            $period = $data['period'] ?? 30;
                            echo $period == 7 ? 'Last 7 Days' : ($period == 90 ? 'Last 3 Months' : 'Last 30 Days');
                            ?> (<?php echo $period; ?> days)
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item <?php echo ($data['period'] ?? 30) == 7 ? 'active font-weight-bold' : ''; ?>"
                            href="<?php echo URLROOT; ?>/dashboard?period=7&t=<?php echo time(); ?>">
                            <i class="fa fa-calendar mr-2"></i>Last 7 Days
                        </a>
                        <a class="dropdown-item <?php echo ($data['period'] ?? 30) == 30 ? 'active font-weight-bold' : ''; ?>"
                            href="<?php echo URLROOT; ?>/dashboard?period=30&t=<?php echo time(); ?>">
                            <i class="fa fa-calendar mr-2"></i>Last 30 Days
                        </a>
                        <a class="dropdown-item <?php echo ($data['period'] ?? 30) == 90 ? 'active font-weight-bold' : ''; ?>"
                            href="<?php echo URLROOT; ?>/dashboard?period=90&t=<?php echo time(); ?>">
                            <i class="fa fa-calendar mr-2"></i>Last 3 Months
                        </a>
                        <div class="dropdown-divider"></div>
                        <span class="dropdown-item-text small text-muted">
                            <i class="fa fa-info-circle mr-1"></i>Current: <?php echo $data['period'] ?? 30; ?> days
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Key Performance Indicators - unified layout -->
    <div class="row mb-4 kpi-row">
        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-6 mb-3">
            <div class="kpi-card kpi-gradient-primary shadow-sm h-100">
                <div class="kpi-body">
                    <div class="kpi-count" data-metric="total_sales">
                        <?php echo formatCurrency($data['total_sales'] ?? 0, 0); ?>
                    </div>
                    <div class="kpi-value small">
                        Total Sales • <span
                            data-metric="sales_growth"><?php echo $data['sales_growth'] ?? '0'; ?>%</span> <i
                            class="fas fa-arrow-up text-success"></i>
                        <br><small class="text-muted">[<?php echo $data['period'] ?? 30; ?>d:
                            $<?php echo number_format($data['total_sales'] ?? 0, 0); ?>]</small>
                    </div>
                    <div class="kpi-small-spark" aria-hidden="true"></div>
                    <i class="fas fa-chart-line kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-6 mb-3">
            <div class="kpi-card kpi-gradient-success shadow-sm h-100">
                <div class="kpi-body">
                    <div class="kpi-count" data-metric="avg_transaction">
                        <?php echo formatCurrency($data['avg_transaction'] ?? 0, 2); ?>
                    </div>
                    <div class="kpi-value small"><span
                            data-metric="total_transactions"><?php echo formatIndianNumber($data['total_transactions'] ?? 0, 0); ?></span>
                        transactions • Avg Transaction</div>
                    <div class="kpi-small-spark" aria-hidden="true"></div>
                    <i class="fas fa-receipt kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-6 mb-3">
            <div class="kpi-card kpi-gradient-info shadow-sm h-100">
                <div class="kpi-body">
                    <div class="kpi-count" data-metric="inventory_value">
                        <?php echo formatCurrency($data['inventory_value'] ?? 0, 0); ?>
                    </div>
                    <div class="kpi-value small"><span
                            data-metric="total_products"><?php echo formatIndianNumber($data['total_products'] ?? 0, 0); ?></span>
                        products • Inventory Value</div>
                    <div class="kpi-small-spark" aria-hidden="true"></div>
                    <i class="fas fa-boxes kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-6 mb-3">
            <div class="kpi-card kpi-gradient-warning shadow-sm h-100">
                <div class="kpi-body">
                    <div class="kpi-count" data-metric="low_inventory_count">
                        <?php echo $data['low_inventory_count'] ?? 0; ?>
                    </div>
                    <div class="kpi-value small">Low Inventory • Need Restocking</div>
                    <div class="kpi-small-spark" aria-hidden="true"></div>
                    <i class="fas fa-exclamation-triangle kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-6 mb-3">
            <div class="kpi-card kpi-gradient-danger shadow-sm h-100">
                <div class="kpi-body">
                    <div class="kpi-count" data-metric="out_of_inventory_count">
                        <?php echo $data['out_of_inventory_count'] ?? 0; ?>
                    </div>
                    <div class="kpi-value small">Out of Inventory • Immediate Action</div>
                    <div class="kpi-small-spark" aria-hidden="true"></div>
                    <i class="fas fa-times-circle kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-6 mb-3">
            <div class="kpi-card kpi-gradient-success shadow-sm h-100">
                <div class="kpi-body">
                    <div class="kpi-count" data-metric="gross_margin">
                        <?php echo number_format($data['gross_margin'] ?? 0, 1); ?>%
                    </div>
                    <div class="kpi-value small">Gross Margin • Target: 25%</div>
                    <div class="kpi-small-spark" aria-hidden="true"></div>
                    <i class="fas fa-percentage kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-6 mb-3">
            <div class="kpi-card kpi-gradient-primary shadow-sm h-100">
                <div class="kpi-body">
                    <div class="kpi-count" data-metric="new_customers"><?php echo $data['new_customers'] ?? 0; ?></div>
                    <div class="kpi-value small">New Customers • This month</div>
                    <div class="kpi-small-spark" aria-hidden="true"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Analytics Row -->
    <div class="row mb-4">
        <!-- Inventory Status Chart -->
        <div class="col-lg-3 mb-3">
            <div class="card-theme theme-card-light h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie text-info mr-2"></i>Inventory Status Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="inventoryStatusChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Price Range Distribution -->
        <div class="col-lg-3 mb-3">
            <div class="card-theme theme-card-light h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar text-warning mr-2"></i>Price Range Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="priceRangeChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Category Performance -->
        <div class="col-lg-3 mb-3">
            <div class="card-theme theme-card-light h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tags text-success mr-2"></i>Category Performance
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary active"
                            onclick="toggleCategoryView('inventory')">Inventory</button>
                        <button class="btn btn-outline-primary" onclick="toggleCategoryView('value')">Value</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="categoryPerformanceChart" width="400" height="150"></canvas>
                </div>
            </div>
        </div>

        <!-- Product Activity -->
        <div class="col-lg-3 mb-3">
            <div class="card-theme h-100">
                <div
                    class="card-header bg-secondary-theme text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-history mr-2"></i>Product Activity
                    </h6>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-light btn-sm" onclick="refreshProductActivity()"
                            data-toggle="tooltip" title="Refresh Activity">
                            <i class="fas fa-sync"></i>
                        </button>
                        <button class="btn btn-outline-light btn-sm" onclick="clearProductActivity()"
                            data-toggle="tooltip" title="Clear All Activity">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                    <div id="productActivityTableBody">
                        <?php
                        // Get recent product activities from the controller
                        $productActivities = [];
                        if (!empty($data['product_activities'])) {
                            $productActivities = array_slice($data['product_activities'], 0, 5); // Show only 5 items in compact view
                        }

                        if (!empty($productActivities)):
                            foreach ($productActivities as $index => $activity):
                                $badgeClass = '';
                                $iconClass = '';

                                switch (strtoupper($activity->action)) {
                                    case 'ADD':
                                        $badgeClass = 'badge-success';
                                        $iconClass = 'fas fa-plus';
                                        break;
                                    case 'EDIT':
                                        $badgeClass = 'badge-info';
                                        $iconClass = 'fas fa-edit';
                                        break;
                                    case 'DELETE':
                                        $badgeClass = 'badge-danger';
                                        $iconClass = 'fas fa-trash';
                                        break;
                                    default:
                                        $badgeClass = 'badge-secondary';
                                        $iconClass = 'fas fa-question';
                                }

                                $formattedDate = date('M d', strtotime($activity->created_at));
                                $formattedTime = date('H:i', strtotime($activity->created_at));
                                ?>
                                <div class="d-flex align-items-center p-2 border-bottom">
                                    <span class="badge <?php echo $badgeClass; ?> mr-2">
                                        <i class="<?php echo $iconClass; ?>"></i>
                                    </span>
                                    <div class="flex-grow-1">
                                        <div class="font-weight-bold text-dark" style="font-size: 0.8rem;">
                                            <?php echo htmlspecialchars(substr($activity->product_name, 0, 20)); ?>...
                                        </div>
                                        <small class="text-muted"><?php echo $formattedDate; ?>
                                            <?php echo $formattedTime; ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="text-center p-2">
                                <a href="<?php echo URLROOT; ?>/products/activity" class="btn btn-sm btn-outline-secondary">
                                    View All Activity
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-history fa-2x mb-2"></i>
                                    <p class="mb-0" style="font-size: 0.8rem;">No recent activity</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Section - Full Width Layout -->
    <div class="row">
        <!-- Main Analytics Column - Extended Width -->
        <div class="col-lg-8 col-xl-8">
            <!-- Sales Trend Chart -->
            <div class="kpi-card mb-4">
                <div class="card-header bg-primary-theme text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line"></i>
                            Monthly Sales Trend
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button class="btn-outline-modern" onclick="updateChart('sales')">Sales</button>
                            <button class="btn-outline-modern" onclick="updateChart('profit')">Profit</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div style="height: 350px;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Analytics Row -->
            <div class="row">
                <!-- Sales by Category -->
                <div class="col-lg-6 mb-3">
                    <div class="kpi-card">
                        <div class="card-header bg-success-theme text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-pie"></i>
                                Sales by Category
                            </h5>
                        </div>
                        <div class="card-body">
                            <div style="height: 300px;">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Analytics -->
                <div class="col-lg-6 mb-3">
                    <div class="kpi-card">
                        <div class="card-header bg-info-theme text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-users"></i>
                                Customer Insights
                            </h5>
                        </div>
                        <div class="card-body">
                            <div style="height: 300px;">
                                <canvas id="customerChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Widgets - Extended Width -->
        <div class="col-lg-4 col-xl-4">
            <!-- Quick Actions -->
            <div class="kpi-card mb-4">
                <div class="card-header bg-warning-theme text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <a href="<?php echo URLROOT; ?>/sales/add" class="btn-action btn-success-theme">
                                <i class="fas fa-plus"></i>
                                <div>New Sale</div>
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="<?php echo URLROOT; ?>/products/add" class="btn-action btn-primary-theme">
                                <i class="fas fa-box"></i>
                                <div>Add Product</div>
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="<?php echo URLROOT; ?>/purchases/add" class="btn-action btn-info-theme">
                                <i class="fas fa-shopping-cart"></i>
                                <div>New Purchase</div>
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="<?php echo URLROOT; ?>/customers" class="btn-action btn-secondary-theme">
                                <i class="fas fa-users"></i>
                                <div>Customers</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="kpi-card">
                <div class="card-header bg-info-theme text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-clock"></i>
                            Recent Activity
                        </h5>
                        <a href="#" class="btn-outline-modern btn-sm">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon bg-primary">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Sale Completed</div>
                                <div class="activity-description">New sale of <?php echo formatCurrency(245.50, 2); ?>
                                </div>
                                <div class="activity-time">2 hours ago</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon bg-success">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Product Added</div>
                                <div class="activity-description">New hammer added to inventory</div>
                                <div class="activity-time">4 hours ago</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon bg-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Low Inventory Alert</div>
                                <div class="activity-description">Screws below minimum level</div>
                                <div class="activity-time">6 hours ago</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Summary -->
            <div class="kpi-card">
                <div class="card-header bg-secondary-theme text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy"></i>
                        Performance Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="progress-label">
                            <span class="progress-label-title">Daily Sales Target</span>
                            <span class="progress-label-value text-success">85%</span>
                        </div>
                        <div class="progress-modern">
                            <div class="progress-bar-modern bg-success" style="width: 85%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="progress-label">
                            <span class="progress-label-title">Inventory Turnover</span>
                            <span class="progress-label-value text-info">Good</span>
                        </div>
                        <div class="progress-modern">
                            <div class="progress-bar-modern bg-info" style="width: 72%"></div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <div class="progress-label">
                            <span class="progress-label-title">Customer Satisfaction</span>
                            <span class="progress-label-value text-warning">92%</span>
                        </div>
                        <div class="progress-modern">
                            <div class="progress-bar-modern bg-warning" style="width: 92%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Global variables for charts
    let monthlyChart, categoryChart, customerChart;

    // Real data from PHP
    const dashboardData = {
        monthly: {
            labels: <?php echo json_encode($data['monthly_labels'] ?? []); ?>,
            data: <?php echo json_encode($data['monthly_sales'] ?? []); ?>
        },
        categories: {
            labels: <?php echo json_encode($data['category_labels'] ?? []); ?>,
            data: <?php echo json_encode($data['category_sales'] ?? []); ?>,
            colors: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1']
        },
        customers: {
            labels: <?php echo json_encode($data['customer_labels'] ?? []); ?>,
            new_customers: <?php echo json_encode($data['new_customers_data'] ?? []); ?>,
            returning_customers: <?php echo json_encode($data['returning_customers_data'] ?? []); ?>
        }
    };

    // Theme color helpers used by charts; defined here so charts don't fail if theme scripts are missing
    function getThemeColors() {
        return {
            primary: '#007bff',
            success: '#28a745',
            warning: '#ffc107',
            danger: '#dc3545',
            info: '#17a2b8'
        };
    }

    // Simple chart options merger. Accepts a custom options object and returns a usable options object for Chart.js.
    function getChartOptions(customOptions) {
        const defaults = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                }
            }
        };
        // Shallow merge is sufficient for our simple use cases
        return Object.assign({}, defaults, customOptions || {});
    }

    // Initialize Dashboard
    document.addEventListener('DOMContentLoaded', function () {
        console.log('DOM loaded, initializing dashboard...');

        // Initialize charts with a slight delay to ensure Chart.js is fully loaded
        setTimeout(function () {
            initializeMonthlyChart();
            initializeCategoryChart();
            initializeCustomerChart();
            initializeDashboardAnalytics();
        }, 100);
    });

    // Setup dropdown event handlers
    function setupDropdownHandlers() {
        console.log('Setting up dropdown handlers...');
        const dropdownMenu = document.getElementById('periodDropdownMenu');
        console.log('Dropdown menu element:', dropdownMenu);

        if (dropdownMenu) {
            dropdownMenu.addEventListener('click', function (e) {
                console.log('Dropdown clicked!', e.target);
                const clickedItem = e.target;

                // Check if it's a dropdown item
                if (clickedItem.classList.contains('dropdown-item')) {
                    console.log('Valid dropdown item clicked');
                    e.preventDefault();
                    e.stopPropagation();

                    const days = parseInt(clickedItem.getAttribute('data-days'));
                    const periodText = clickedItem.getAttribute('data-period');

                    console.log('Days:', days, 'Period:', periodText);

                    if (days && periodText) {
                        // Update the dropdown button text
                        const selectedPeriodSpan = document.getElementById('selected-period');
                        if (selectedPeriodSpan) {
                            selectedPeriodSpan.textContent = periodText;
                            console.log('Updated button text to:', periodText);
                        }

                        // Update active state in dropdown
                        document.querySelectorAll('.dropdown-item').forEach(item => {
                            item.classList.remove('active');
                        });
                        clickedItem.classList.add('active');
                        console.log('Updated active state');

                        // Close the dropdown
                        if (typeof $ !== 'undefined') {
                            $('#dashboardPeriodDropdown').dropdown('hide');
                            console.log('Dropdown hidden via Bootstrap');
                        }

                        // Update dashboard
                        console.log('Calling updateDashboardData with days:', days);
                        updateDashboardData(days);
                    } else {
                        console.error('Missing days or periodText', { days, periodText });
                    }
                } else {
                    console.log('Not a dropdown item');
                }
            });
            console.log('Event listener added successfully');
        } else {
            console.error('Dropdown menu not found!');
        }
    }

    // Listen for theme changes and update charts
    window.addEventListener('themeChanged', function (e) {
        // Reinitialize charts with new theme
        if (monthlyChart) {
            monthlyChart.destroy();
            initializeMonthlyChart();
        }
        if (categoryChart) {
            categoryChart.destroy();
            initializeCategoryChart();
        }
        if (customerChart) {
            customerChart.destroy();
            initializeCustomerChart();
        }
        if (inventoryChart) {
            inventoryChart.destroy();
        }
        if (priceChart) {
            priceChart.destroy();
        }
        if (productCategoryChart) {
            productCategoryChart.destroy();
        }
        // Reset flag and reinitialize dashboard analytics
        chartsInitialized = false;
        initializeDashboardAnalytics();
    });

    // Monthly Chart
    function initializeMonthlyChart() {
        const ctx = document.getElementById('monthlyChart');
        if (!ctx) return;

        if (!dashboardData.monthly.labels.length || !dashboardData.monthly.data.length) {
            ctx.parentNode.innerHTML = '<div class="text-center text-muted py-5">No sales data available for chart.</div>';
            return;
        }

        const colors = getThemeColors();

        monthlyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dashboardData.monthly.labels,
                datasets: [{
                    label: 'Monthly Sales',
                    data: dashboardData.monthly.data,
                    borderColor: colors.primary,
                    backgroundColor: colors.primary + '20',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: getChartOptions({
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return '₹' + value.toLocaleString('en-IN');
                            }
                        }
                    }
                }
            })
        });
    }

    // Category Sales Chart
    function initializeCategoryChart() {
        const ctx = document.getElementById('categoryChart');
        if (!ctx) return;

        if (!dashboardData.categories.labels.length || !dashboardData.categories.data.length) {
            ctx.parentNode.innerHTML = '<div class="text-center text-muted py-5">No category sales data available for chart.</div>';
            return;
        }

        const colors = getThemeColors();

        categoryChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: dashboardData.categories.labels,
                datasets: [{
                    data: dashboardData.categories.data,
                    backgroundColor: [colors.primary, colors.success, colors.warning, colors.danger, colors.info],
                    borderWidth: 0
                }]
            },
            options: getChartOptions({
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '60%'
            })
        });
    }

    // Customer Analytics Chart
    function initializeCustomerChart() {
        const ctx = document.getElementById('customerChart');
        if (!ctx) return;

        const colors = getThemeColors();

        customerChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dashboardData.customers.labels,
                datasets: [{
                    label: 'New Customers',
                    data: dashboardData.customers.new_customers,
                    backgroundColor: colors.success
                }, {
                    label: 'Returning Customers',
                    data: dashboardData.customers.returning_customers,
                    backgroundColor: colors.primary
                }]
            },
            options: getChartOptions({
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            })
        });
    }

    // Dashboard data update function
    // Make server root available to page scripts
    window.URLROOT = '<?php echo URLROOT; ?>';

    function updateDashboardData(days, evt) {
        console.log('updateDashboardData called with days:', days);

        // Simple redirect to reload dashboard for selected period
        const url = window.URLROOT + '/dashboard?period=' + encodeURIComponent(days);
        console.log('Redirecting to:', url);

        // Prevent default link behavior if event provided
        if (evt && typeof evt.preventDefault === 'function') {
            evt.preventDefault();
        }

        window.location.href = url;
        return false;
    }

    function showLoadingState() {
        // Add loading spinner to KPI cards
        document.querySelectorAll('.kpi-count').forEach(element => {
            element.style.opacity = '0.5';
        });

        // Show loading indicator
        const loadingIndicator = document.createElement('div');
        loadingIndicator.id = 'dashboard-loading';
        loadingIndicator.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
        loadingIndicator.style.cssText = 'position: fixed; top: 20px; right: 20px; background: var(--primary); color: white; padding: 10px 15px; border-radius: 5px; z-index: 9999;';
        document.body.appendChild(loadingIndicator);
    }

    function hideLoadingState() {
        // Remove loading state
        document.querySelectorAll('.kpi-count').forEach(element => {
            element.style.opacity = '1';
        });

        // Remove loading indicator
        const loadingIndicator = document.getElementById('dashboard-loading');
        if (loadingIndicator) {
            loadingIndicator.remove();
        }
    }

    function updateDashboardCards(data) {
        // Update KPI cards with new data
        const updates = {
            'total_sales': data.total_sales || 0,
            'sales_growth': data.sales_growth || 0,
            'avg_transaction': data.avg_transaction || 0,
            'total_transactions': data.total_transactions || 0,
            'inventory_value': data.inventory_value || 0,
            'total_products': data.total_products || 0,
            'low_inventory_count': data.low_inventory_count || 0,
            'out_of_inventory_count': data.out_of_inventory_count || 0,
            'gross_margin': data.gross_margin || 0,
            'new_customers': data.new_customers || 0
        };

        // Update the actual DOM elements based on card structure
        Object.keys(updates).forEach(key => {
            const element = document.querySelector(`[data-metric="${key}"]`);
            if (element) {
                if (key === 'total_sales' || key === 'inventory_value') {
                    element.textContent = formatCurrency(updates[key]);
                } else if (key === 'avg_transaction') {
                    element.textContent = formatCurrency(updates[key], 2);
                } else if (key === 'total_transactions' || key === 'total_products') {
                    element.textContent = formatIndianNumber(updates[key]);
                } else if (key === 'gross_margin') {
                    element.textContent = parseFloat(updates[key]).toFixed(1) + '%';
                } else if (key === 'sales_growth') {
                    element.innerHTML = updates[key] + '% <i class="fas fa-arrow-up text-success"></i>';
                } else {
                    element.textContent = updates[key];
                }
            }
        });
    }

    function updateDashboardCharts(data) {
        // Reset initialization flag to allow re-initialization with new data
        chartsInitialized = false;
        // Update charts if they exist
        if (typeof initializeDashboardAnalytics === 'function') {
            // Re-initialize charts with new data
            initializeDashboardAnalytics();
        }
    }

    function formatCurrency(amount, decimals = 0) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(amount);
    }

    function formatIndianNumber(number, decimals = 0) {
        // Convert to Indian numbering system (lakhs, crores)
        const num = parseFloat(number);
        if (num >= 10000000) { // 1 crore
            return (num / 10000000).toFixed(decimals) + ' Cr';
        } else if (num >= 100000) { // 1 lakh
            return (num / 100000).toFixed(decimals) + ' L';
        } else if (num >= 1000) { // 1 thousand
            return (num / 1000).toFixed(decimals) + ' K';
        }
        return num.toFixed(decimals);
    }

    function refreshDashboard() {
        location.reload();
    }

    function updateChart(type) {
        console.log('Updating chart type:', type);
    }

    // Dashboard Analytics Charts
    let inventoryChart, priceChart, productCategoryChart;
    let chartsInitialized = false;

    function resetDashboardCharts() {
        // Destroy existing charts
        if (inventoryChart) {
            inventoryChart.destroy();
            inventoryChart = null;
        }
        if (priceChart) {
            priceChart.destroy();
            priceChart = null;
        }
        if (productCategoryChart) {
            productCategoryChart.destroy();
            productCategoryChart = null;
        }
        chartsInitialized = false;
    }

    function initializeDashboardAnalytics() {
        // Prevent multiple initializations
        if (chartsInitialized) {
            console.log('Charts already initialized, skipping...');
            return;
        }

        // Check if Chart.js is loaded
        if (typeof Chart === 'undefined') {
            console.error('Chart.js is not loaded!');
            // Only retry if charts haven't been initialized yet
            if (!chartsInitialized) {
                setTimeout(initializeDashboardAnalytics, 100);
            }
            return;
        }

        console.log('Initializing dashboard analytics charts...');

        // Destroy existing charts before creating new ones
        if (inventoryChart) {
            inventoryChart.destroy();
            inventoryChart = null;
        }
        if (priceChart) {
            priceChart.destroy();
            priceChart = null;
        }
        if (productCategoryChart) {
            productCategoryChart.destroy();
            productCategoryChart = null;
        }

        // Inventory Status Chart
        const inventoryCtx = document.getElementById('inventoryStatusChart');
        if (!inventoryCtx) {
            console.error('inventoryStatusChart canvas not found!');
            return;
        }

        // Ensure canvas has proper dimensions
        const inventoryParent = inventoryCtx.parentElement;
        if (inventoryParent.offsetHeight === 0) {
            inventoryParent.style.height = '300px';
        }

        const invDist = <?php echo json_encode($data['inventory_status_distribution'] ?? [0, 0, 0, 0]); ?>;
        console.log('Creating inventory chart with data:', invDist);

        try {
            inventoryChart = new Chart(inventoryCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['In Stock', 'Low Stock', 'Out of Stock', 'Reorder Level'],
                    datasets: [{
                        data: invDist,
                        backgroundColor: [
                            '#28a745',
                            '#ffc107',
                            '#dc3545',
                            '#17a2b8'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            console.log('Inventory chart created successfully');
        } catch (error) {
            console.error('Error creating inventory chart:', error);
        }

        // Price Range Chart
        const priceCtx = document.getElementById('priceRangeChart');
        if (!priceCtx) {
            console.error('priceRangeChart canvas not found!');
            return;
        }

        // Ensure canvas has proper dimensions
        const priceParent = priceCtx.parentElement;
        if (priceParent.offsetHeight === 0) {
            priceParent.style.height = '300px';
        }

        const priceDist = <?php echo json_encode($data['price_range_distribution'] ?? [0, 0, 0, 0, 0]); ?>;
        console.log('Creating price chart with data:', priceDist);

        try {
            priceChart = new Chart(priceCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['₹0-500', '₹500-2K', '₹2K-5K', '₹5K-10K', '₹10K+'],
                    datasets: [{
                        label: 'Number of Products',
                        data: priceDist,
                        backgroundColor: '#007bff',
                        borderColor: '#0056b3',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 5
                            }
                        }
                    }
                }
            });
            console.log('Price chart created successfully');
        } catch (error) {
            console.error('Error creating price chart:', error);
        }

        // Product Category Chart (horizontal) - Chart.js v3+ uses indexAxis:'y'
        const productCategoryCtx = document.getElementById('categoryPerformanceChart');
        if (!productCategoryCtx) {
            console.error('categoryPerformanceChart canvas not found!');
            return;
        }

        // Ensure canvas has proper dimensions
        const categoryParent = productCategoryCtx.parentElement;
        if (categoryParent.offsetHeight === 0) {
            categoryParent.style.height = '300px';
        }

        // For category chart we'll use server-provided sales by category data
        const categoryLabels = <?php echo json_encode(array_column($data['sales_by_category'] ?? [], 'category_name')); ?> || ['Power Tools', 'Hand Tools', 'Hardware', 'Electrical', 'Plumbing', 'Safety'];
        const categoryValues = <?php echo json_encode(array_column($data['sales_by_category'] ?? [], 'total_sales')); ?> || [45, 38, 32, 28, 22, 15];
        console.log('Creating category chart with labels:', categoryLabels, 'values:', categoryValues);

        try {
            productCategoryChart = new Chart(productCategoryCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        label: 'Sales Amount (₹)',
                        data: categoryValues,
                        backgroundColor: [
                            '#007bff',
                            '#28a745',
                            '#ffc107',
                            '#17a2b8',
                            '#6f42c1',
                            '#e83e8c'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
            console.log('Category chart created successfully');
        } catch (error) {
            console.error('Error creating category chart:', error);
        }

        // Mark charts as initialized
        chartsInitialized = true;
        console.log('All dashboard analytics charts initialized!');
    }

    function toggleCategoryView(viewType, evt) {
        // Update active button
        document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
        if (evt && evt.target) evt.target.classList.add('active');

        if (viewType === 'value') {
            productCategoryChart.data.datasets[0].label = 'Total Value (₹)';
            productCategoryChart.data.datasets[0].data = [450000, 320000, 280000, 220000, 180000, 120000];
        } else {
            productCategoryChart.data.datasets[0].label = 'Inventory Count';
            productCategoryChart.data.datasets[0].data = [45, 38, 32, 28, 22, 15];
        }
        productCategoryChart.update();
    }

    // Product Activity functions
    function refreshProductActivity() {
        // Show loading state
        const tableBody = document.getElementById('productActivityTableBody');
        tableBody.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Refreshing...</div>';

        // Simulate refresh (in real implementation, make AJAX call to get fresh data)
        setTimeout(() => {
            location.reload();
        }, 1000);
    }

    function clearProductActivity() {
        if (confirm('Are you sure you want to clear all product activity history? This action cannot be undone.')) {
            // In real implementation, make AJAX call to clear activity
            fetch(window.URLROOT + '/products/clearActivity', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({})
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tableBody = document.getElementById('productActivityTableBody');
                        tableBody.innerHTML = `
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-history fa-2x mb-2"></i>
                                    <p class="mb-0" style="font-size: 0.8rem;">No recent activity</p>
                                </div>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error clearing activity:', error);
                });
        }
    }
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>