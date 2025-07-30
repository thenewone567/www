<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>

<!-- Add Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid mt-0 pt-3">
    <!-- Header Section -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-6">
            <h1 class="mb-1 font-weight-bold">Hardware Store Dashboard</h1>
            <small class="text-muted">Real-time Analytics & Performance</small>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <div class="dropdown d-inline-block">
                <button class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-calendar"></i> Last 30 Days
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#" onclick="updateDashboard('7')">Last 7 Days</a>
                    <a class="dropdown-item active" href="#" onclick="updateDashboard('30')">Last 30 Days</a>
                    <a class="dropdown-item" href="#" onclick="updateDashboard('90')">Last 3 Months</a>
                </div>
            </div>
            <button class="btn btn-primary ml-2" onclick="refreshDashboard()">
                <i class="fa fa-sync"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="row mb-4">
        <!-- Sales Performance KPIs -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fa fa-chart-line fa-2x text-primary"></i>
                    </div>
                    <h6 class="text-muted mb-1">Total Sales</h6>
                    <h4 class="mb-1 font-weight-bold text-primary">
                        $<?php echo number_format($data['total_sales'] ?? 0, 0); ?>
                    </h4>
                    <small class="text-success">
                        <i class="fa fa-arrow-up"></i>
                        <?php echo $data['sales_growth'] ?? '0'; ?>%
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fa fa-receipt fa-2x text-success"></i>
                    </div>
                    <h6 class="text-muted mb-1">Avg Transaction</h6>
                    <h4 class="mb-1 font-weight-bold text-success">
                        $<?php echo number_format($data['avg_transaction'] ?? 0, 2); ?>
                    </h4>
                    <small class="text-info">
                        <?php echo $data['total_transactions'] ?? 0; ?> transactions
                    </small>
                </div>
            </div>
        </div>

        <!-- Inventory KPIs -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fa fa-boxes fa-2x text-info"></i>
                    </div>
                    <h6 class="text-muted mb-1">Inventory Value</h6>
                    <h4 class="mb-1 font-weight-bold text-info">
                        $<?php echo number_format($data['inventory_value'] ?? 0, 0); ?>
                    </h4>
                    <small class="text-muted">
                        <?php echo $data['total_products'] ?? 0; ?> products
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fa fa-exclamation-triangle fa-2x text-warning"></i>
                    </div>
                    <h6 class="text-muted mb-1">Low Stock Items</h6>
                    <h4 class="mb-1 font-weight-bold text-warning">
                        <?php echo $data['low_stock_count'] ?? 0; ?>
                    </h4>
                    <small class="text-danger">
                        <?php echo $data['out_of_stock_count'] ?? 0; ?> out of stock
                    </small>
                </div>
            </div>
        </div>

        <!-- Financial KPIs -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fa fa-percentage fa-2x text-success"></i>
                    </div>
                    <h6 class="text-muted mb-1">Gross Margin</h6>
                    <h4 class="mb-1 font-weight-bold text-success">
                        <?php echo number_format($data['gross_margin'] ?? 0, 1); ?>%
                    </h4>
                    <small class="text-muted">
                        Target: 25%
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fa fa-users fa-2x text-primary"></i>
                    </div>
                    <h6 class="text-muted mb-1">Customers</h6>
                    <h4 class="mb-1 font-weight-bold text-primary">
                        <?php echo $data['new_customers'] ?? 0; ?>
                    </h4>
                    <small class="text-success">
                        New this month
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics Section -->
    <div class="row mb-4">
        <!-- Sales Trend Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-bold">Sales Performance</h5>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary active"
                                onclick="changeSalesView('daily')">Daily</button>
                            <button type="button" class="btn btn-outline-secondary"
                                onclick="changeSalesView('weekly')">Weekly</button>
                            <button type="button" class="btn btn-outline-secondary"
                                onclick="changeSalesView('monthly')">Monthly</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="salesTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Status Gauges -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-0 font-weight-bold">Inventory Health</h5>
                </div>
                <div class="card-body">
                    <!-- Out of Stock Gauge -->
                    <div class="text-center mb-4">
                        <h6 class="text-muted mb-2">Out of Stock Items</h6>
                        <div style="height: 120px;">
                            <canvas id="outOfStockGauge"></canvas>
                        </div>
                        <div class="mt-2">
                            <span class="badge badge-danger px-3 py-2">
                                <?php echo $data['out_of_stock_percentage'] ?? 0; ?>% Out of Stock
                            </span>
                        </div>
                    </div>

                    <!-- Gross Margin Gauge -->
                    <div class="text-center">
                        <h6 class="text-muted mb-2">Gross Profit Margin</h6>
                        <div style="height: 120px;">
                            <canvas id="marginGauge"></canvas>
                        </div>
                        <div class="mt-2">
                            <span class="badge badge-success px-3 py-2">
                                <?php echo number_format($data['gross_margin'] ?? 0, 1); ?>% Margin
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics Section -->
    <div class="row mb-4">
        <!-- Sales by Category -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-0 font-weight-bold">Sales by Category</h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Analytics -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-0 font-weight-bold">Customer Insights</h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="customerChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Tables Section -->
    <div class="row mb-4">
        <!-- Top Selling Products -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold">Top 5 Selling Products</h5>
                    <a href="<?php echo URLROOT; ?>/reports/products" class="btn btn-sm btn-outline-primary">
                        View All <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0">Product</th>
                                    <th class="border-0 text-right">Units Sold</th>
                                    <th class="border-0 text-right">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['top_selling'])): ?>
                                    <?php foreach ($data['top_selling'] as $index => $product): ?>
                                        <tr>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <div class="badge badge-primary rounded-circle mr-3"
                                                        style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                                        <?php echo $index + 1; ?>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0"><?php echo $product->product_name; ?></h6>
                                                        <small
                                                            class="text-muted"><?php echo $product->category_name ?? 'Uncategorized'; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-right">
                                                <span class="font-weight-bold"><?php echo $product->total_quantity; ?></span>
                                            </td>
                                            <td class="align-middle text-right">
                                                <span class="font-weight-bold text-success">
                                                    $<?php echo number_format($product->total_revenue ?? 0, 0); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            <i class="fa fa-chart-bar fa-2x mb-2"></i>
                                            <p>No sales data available</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Items by Category -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold">Low Stock Alerts</h5>
                    <a href="<?php echo URLROOT; ?>/products/index?filter=low_stock"
                        class="btn btn-sm btn-outline-warning">
                        Manage Stock <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="border-0">Product</th>
                                    <th class="border-0 text-center">Status</th>
                                    <th class="border-0 text-right">Current Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['low_stock'])): ?>
                                    <?php foreach (array_slice($data['low_stock'], 0, 5) as $product): ?>
                                        <tr>
                                            <td class="align-middle">
                                                <div>
                                                    <h6 class="mb-0"><?php echo $product->product_name; ?></h6>
                                                    <small class="text-muted">SKU: <?php echo $product->sku ?? 'N/A'; ?></small>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <?php if ($product->current_stock <= 0): ?>
                                                    <span class="badge badge-danger">Out of Stock</span>
                                                <?php elseif ($product->current_stock <= $product->reorder_level): ?>
                                                    <span class="badge badge-warning">Reorder Now</span>
                                                <?php else: ?>
                                                    <span class="badge badge-info">Low Stock</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle text-right">
                                                <span
                                                    class="font-weight-bold <?php echo $product->current_stock <= 0 ? 'text-danger' : 'text-warning'; ?>">
                                                    <?php echo $product->current_stock; ?>
                                                </span>
                                                <small class="text-muted d-block">
                                                    Min: <?php echo $product->min_stock_level; ?>
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            <i class="fa fa-check-circle fa-2x mb-2 text-success"></i>
                                            <p>All products are well stocked!</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-0 font-weight-bold">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo URLROOT; ?>/sales/add" class="btn btn-outline-primary btn-block">
                                <i class="fa fa-plus mb-2"></i><br>
                                New Sale
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo URLROOT; ?>/purchases/add" class="btn btn-outline-success btn-block">
                                <i class="fa fa-shopping-cart mb-2"></i><br>
                                New Purchase
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo URLROOT; ?>/products/add" class="btn btn-outline-info btn-block">
                                <i class="fa fa-box mb-2"></i><br>
                                Add Product
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?php echo URLROOT; ?>/reports/index" class="btn btn-outline-warning btn-block">
                                <i class="fa fa-chart-bar mb-2"></i><br>
                                View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Global variables for charts
    let salesTrendChart, categoryChart, customerChart, outOfStockGauge, marginGauge;

    // Sample data - In production, this would come from PHP/AJAX
    const dashboardData = {
        sales_trend: {
            daily: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                current: [1200, 1900, 3000, 2500, 2200, 3000, 2800],
                previous: [1000, 1600, 2400, 2000, 1800, 2400, 2200]
            },
            weekly: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                current: [12000, 19000, 15000, 22000],
                previous: [10000, 16000, 13000, 18000]
            },
            monthly: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                current: [45000, 52000, 48000, 61000, 55000, 67000],
                previous: [38000, 45000, 42000, 53000, 48000, 58000]
            }
        },
        categories: {
            labels: ['Tools', 'Hardware', 'Electrical', 'Plumbing', 'Garden'],
            sales: [25000, 19000, 15000, 12000, 8000],
            colors: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1']
        },
        customers: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            new_customers: [12, 19, 15, 22],
            returning_customers: [25, 35, 28, 42]
        }
    };

    // Initialize Dashboard
    document.addEventListener('DOMContentLoaded', function () {
        initializeSalesTrendChart();
        initializeCategoryChart();
        initializeCustomerChart();
        initializeGauges();
    });

    // Sales Trend Chart
    function initializeSalesTrendChart() {
        const ctx = document.getElementById('salesTrendChart').getContext('2d');

        salesTrendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dashboardData.sales_trend.daily.labels,
                datasets: [{
                    label: 'Current Period',
                    data: dashboardData.sales_trend.daily.current,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Previous Period',
                    data: dashboardData.sales_trend.daily.previous,
                    borderColor: '#6c757d',
                    backgroundColor: 'rgba(108, 117, 125, 0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function (context) {
                                return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Time Period'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Sales ($)'
                        },
                        ticks: {
                            callback: function (value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    // Category Sales Chart
    function initializeCategoryChart() {
        const ctx = document.getElementById('categoryChart').getContext('2d');

        categoryChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: dashboardData.categories.labels,
                datasets: [{
                    data: dashboardData.categories.sales,
                    backgroundColor: dashboardData.categories.colors,
                    borderWidth: 0,
                    hoverBorderWidth: 3,
                    hoverBorderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': $' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });
    }

    // Customer Analytics Chart
    function initializeCustomerChart() {
        const ctx = document.getElementById('customerChart').getContext('2d');

        customerChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dashboardData.customers.labels,
                datasets: [{
                    label: 'New Customers',
                    data: dashboardData.customers.new_customers,
                    backgroundColor: '#28a745',
                    borderRadius: 4
                }, {
                    label: 'Returning Customers',
                    data: dashboardData.customers.returning_customers,
                    backgroundColor: '#007bff',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Time Period'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Number of Customers'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Gauge Charts
    function initializeGauges() {
        // Out of Stock Gauge
        const outOfStockCtx = document.getElementById('outOfStockGauge').getContext('2d');
        const outOfStockPercentage = <?php echo $data['out_of_stock_percentage'] ?? 5; ?>;

        outOfStockGauge = new Chart(outOfStockCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [outOfStockPercentage, 100 - outOfStockPercentage],
                    backgroundColor: ['#dc3545', '#e9ecef'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                circumference: 180,
                rotation: 270,
                cutout: '80%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            }
        });

        // Gross Margin Gauge
        const marginCtx = document.getElementById('marginGauge').getContext('2d');
        const marginPercentage = <?php echo $data['gross_margin'] ?? 22; ?>;

        marginGauge = new Chart(marginCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [marginPercentage, 100 - marginPercentage],
                    backgroundColor: ['#28a745', '#e9ecef'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                circumference: 180,
                rotation: 270,
                cutout: '80%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            }
        });
    }

    // Chart Interaction Functions
    function changeSalesView(period) {
        // Update active button
        document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');

        // Update chart data
        const newData = dashboardData.sales_trend[period];
        salesTrendChart.data.labels = newData.labels;
        salesTrendChart.data.datasets[0].data = newData.current;
        salesTrendChart.data.datasets[1].data = newData.previous;
        salesTrendChart.update();
    }

    function updateDashboard(days) {
        // In production, this would make an AJAX call to fetch new data
        console.log('Updating dashboard for last', days, 'days');

        // Update dropdown text
        const dropdown = document.querySelector('.dropdown-toggle');
        dropdown.innerHTML = `<i class="fa fa-calendar"></i> Last ${days} Days`;

        // Simulate data refresh
        refreshDashboard();
    }

    function refreshDashboard() {
        // Add loading state
        const refreshBtn = document.querySelector('button[onclick="refreshDashboard()"]');
        const originalContent = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<i class="fa fa-sync fa-spin"></i> Refreshing...';
        refreshBtn.disabled = true;

        // Simulate API call delay
        setTimeout(() => {
            // Reset button
            refreshBtn.innerHTML = originalContent;
            refreshBtn.disabled = false;

            // Show success message
            showNotification('Dashboard data refreshed successfully!', 'success');
        }, 1500);
    }

    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;

        document.body.appendChild(notification);

        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    }

    // Add custom CSS for modern dashboard styling
    const style = document.createElement('style');
    style.textContent = `
    .card {
        border-radius: 12px !important;
        transition: all 0.2s ease;
    }
    .card:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }
    .btn {
        border-radius: 8px;
    }
    .badge {
        border-radius: 20px;
        font-weight: 500;
    }
    .table th {
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
    .progress {
        border-radius: 10px;
    }
    .btn-group .btn {
        border-radius: 6px;
    }
    .btn-group .btn:not(:last-child) {
        margin-right: 2px;
    }
    .alert {
        border-radius: 8px;
        border: none;
    }
    /* Chart container styling */
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    canvas {
        max-height: 100% !important;
        max-width: 100% !important;
    }
`;
    document.head.appendChild(style);
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'footer.php'; ?>