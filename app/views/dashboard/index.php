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
                <p class="description">Real-time analytics and performance insights</p>
            </div>
            <div class="col-12 col-lg-4 text-lg-right mt-3 mt-lg-0">
                <div class="btn-group">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-calendar"></i> Last 30 Days
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" onclick="updateDashboard('7')">Last 7 Days</a>
                        <a class="dropdown-item active" href="#" onclick="updateDashboard('30')">Last 30 Days</a>
                        <a class="dropdown-item" href="#" onclick="updateDashboard('90')">Last 3 Months</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Key Performance Indicators -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Total Sales</h5>
                </div>
                <div class="card-body text-center">
                    <h4 class="text-primary"><?php echo formatCurrency($data['total_sales'] ?? 0, 0); ?></h4>
                    <small class="text-success"><i class="fas fa-arrow-up"></i>
                        <?php echo $data['sales_growth'] ?? '0'; ?>%</small>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-success-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-receipt"></i> Avg Transaction</h5>
                </div>
                <div class="card-body text-center">
                    <h4 class="text-success"><?php echo formatCurrency($data['avg_transaction'] ?? 0, 2); ?></h4>
                    <small class="text-muted"><?php echo formatIndianNumber($data['total_transactions'] ?? 0, 0); ?>
                        transactions</small>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-info-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-boxes"></i> Inventory Value</h5>
                </div>
                <div class="card-body text-center">
                    <h4 class="text-info"><?php echo formatCurrency($data['inventory_value'] ?? 0, 0); ?></h4>
                    <small class="text-muted"><?php echo formatIndianNumber($data['total_products'] ?? 0, 0); ?>
                        products</small>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-warning-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Low Inventory</h5>
                </div>
                <div class="card-body text-center">
                    <h4 class="text-warning"><?php echo $data['low_Inventory_count'] ?? 0; ?></h4>
                    <small class="text-danger"><?php echo $data['out_of_Inventory_count'] ?? 0; ?> out of
                        Inventory</small>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-success-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-percentage"></i> Gross Margin</h5>
                </div>
                <div class="card-body text-center">
                    <h4 class="text-success"><?php echo number_format($data['gross_margin'] ?? 0, 1); ?>%</h4>
                    <small class="text-muted">Target: 25%</small>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> New Customers</h5>
                </div>
                <div class="card-body text-center">
                    <h4 class="text-primary"><?php echo $data['new_customers'] ?? 0; ?></h4>
                    <small class="text-muted">This month</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="row">
        <!-- Main Analytics Column -->
        <div class="col-lg-9 col-xl-9">
            <!-- Sales Trend Chart -->
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line"></i>
                            Monthly Sales Trend
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-light" onclick="updateChart('sales')">Sales</button>
                            <button class="btn btn-outline-light" onclick="updateChart('profit')">Profit</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Analytics Row -->
            <div class="row">
                <!-- Sales by Category -->
                <div class="col-lg-6 mb-3">
                    <div class="theme-card">
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
                    <div class="theme-card">
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

        <!-- Sidebar Widgets -->
        <div class="col-lg-3 col-xl-3">
            <!-- Quick Actions -->
            <div class="theme-card">
                <div class="card-header bg-warning-theme text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <a href="<?php echo URLROOT; ?>/sales/add" class="btn btn-success btn-block">
                                <i class="fas fa-plus"></i>
                                <div>New Sale</div>
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="<?php echo URLROOT; ?>/products/add" class="btn btn-primary btn-block">
                                <i class="fas fa-box"></i>
                                <div>Add Product</div>
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="<?php echo URLROOT; ?>/purchases/add" class="btn btn-info btn-block">
                                <i class="fas fa-shopping-cart"></i>
                                <div>New Purchase</div>
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="<?php echo URLROOT; ?>/customers" class="btn btn-secondary btn-block">
                                <i class="fas fa-users"></i>
                                <div>Customers</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="theme-card">
                <div class="card-header bg-info-theme text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-clock"></i>
                            Recent Activity
                        </h5>
                        <a href="#" class="btn btn-sm btn-outline-light">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle p-2 me-3"
                                style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                                <i class="fas fa-chart-line fa-sm"></i>
                            </div>
                            <div>
                                <div class="font-weight-medium">Sale Completed</div>
                                <div class="text-muted small">New sale of <?php echo formatCurrency(245.50, 2); ?></div>
                                <div class="text-muted small">2 hours ago</div>
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <div class="bg-success text-white rounded-circle p-2 me-3"
                                style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                                <i class="fas fa-box fa-sm"></i>
                            </div>
                            <div>
                                <div class="font-weight-medium">Product Added</div>
                                <div class="text-muted small">New hammer added to inventory</div>
                                <div class="text-muted small">4 hours ago</div>
                            </div>
                        </div>
                        <div class="list-group-item d-flex align-items-center">
                            <div class="bg-warning text-white rounded-circle p-2 me-3"
                                style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                                <i class="fas fa-exclamation-triangle fa-sm"></i>
                            </div>
                            <div>
                                <div class="font-weight-medium">Low Inventory Alert</div>
                                <div class="text-muted small">Screws below minimum level</div>
                                <div class="text-muted small">6 hours ago</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Summary -->
            <div class="theme-card">
                <div class="card-header bg-secondary-theme text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy"></i>
                        Performance Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="font-weight-medium">Daily Sales Target</span>
                            <span class="text-success font-weight-bold">85%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: 85%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="font-weight-medium">Inventory Turnover</span>
                            <span class="text-info font-weight-bold">Good</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: 72%"></div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="font-weight-medium">Customer Satisfaction</span>
                            <span class="text-warning font-weight-bold">92%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: 92%"></div>
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

    // Initialize Dashboard
    document.addEventListener('DOMContentLoaded', function () {
        initializeMonthlyChart();
        initializeCategoryChart();
        initializeCustomerChart();
    });

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

    // Dashboard interaction functions
    function updateDashboard(days) {
        console.log('Updating dashboard for last', days, 'days');
    }

    function refreshDashboard() {
        location.reload();
    }

    function updateChart(type) {
        console.log('Updating chart type:', type);
    }
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>