<?php
$pageTitle = 'Analytics Dashboard';
require_once '../app/views/layout/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><i class="fas fa-chart-line"></i> Analytics Dashboard</h1>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary" id="refreshData">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-calendar"></i> Period
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#" data-period="7">Last 7 days</a>
                        <a class="dropdown-item" href="#" data-period="30">Last 30 days</a>
                        <a class="dropdown-item" href="#" data-period="90">Last 3 months</a>
                        <a class="dropdown-item" href="#" data-period="365">Last year</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Sales (<?= date('M Y') ?>)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSales">
                                $<?= number_format($data['analytics']['total_sales'] ?? 0, 2) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Profit Margin
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="profitMargin">
                                <?= number_format($data['analytics']['profit_margin'] ?? 0, 1) ?>%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeCustomers">
                                <?= $data['analytics']['active_customers'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Low Stock Items
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="lowStockItems">
                                <?= $data['analytics']['low_stock_count'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sales Trend</h6>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Products</h6>
                </div>
                <div class="card-body">
                    <canvas id="topProductsChart" width="100%" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Customers</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Total Sales</th>
                                    <th>Orders</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['topCustomers']) && !empty($data['topCustomers'])): ?>
                                    <?php foreach ($data['topCustomers'] as $customer): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($customer->customer_name) ?></td>
                                            <td>$<?= number_format($customer->total_sales, 2) ?></td>
                                            <td><?= $customer->order_count ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No data available</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Supplier Performance</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%">
                            <thead>
                                <tr>
                                    <th>Supplier</th>
                                    <th>Rating</th>
                                    <th>On-time %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($data['supplierPerformance']) && !empty($data['supplierPerformance'])): ?>
                                    <?php foreach ($data['supplierPerformance'] as $supplier): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($supplier->supplier_name) ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="mr-2"><?= number_format($supplier->rating, 1) ?></span>
                                                    <div class="progress flex-grow-1" style="height: 20px;">
                                                        <div class="progress-bar bg-success"
                                                            style="width: <?= ($supplier->rating / 5) * 100 ?>%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= number_format($supplier->on_time_percentage, 1) ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No data available</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Alerts -->
    <?php if (isset($data['lowStockItems']) && !empty($data['lowStockItems'])): ?>
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-danger">Low Stock Alerts</h6>
                        <a href="<?= URLROOT ?>/inventory" class="btn btn-sm btn-outline-danger">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Current Stock</th>
                                        <th>Minimum Stock</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($data['lowStockItems'], 0, 5) as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item->product_name) ?></td>
                                            <td>
                                                <span class="badge badge-danger"><?= $item->current_stock ?></span>
                                            </td>
                                            <td><?= $item->minimum_stock ?></td>
                                            <td>
                                                <a href="<?= URLROOT ?>/purchases/add?product_id=<?= $item->product_id ?>"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-plus"></i> Order
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function () {
        // Sales Trend Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($data['salesTrend']['labels'] ?? []) ?>,
                datasets: [{
                    label: 'Sales',
                    data: <?= json_encode($data['salesTrend']['data'] ?? []) ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Top Products Chart
        const productsCtx = document.getElementById('topProductsChart').getContext('2d');
        const productsChart = new Chart(productsCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($data['topProducts']['labels'] ?? []) ?>,
                datasets: [{
                    data: <?= json_encode($data['topProducts']['data'] ?? []) ?>,
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Refresh data
        $('#refreshData').click(function () {
            location.reload();
        });

        // Period filter
        $('.dropdown-item[data-period]').click(function (e) {
            e.preventDefault();
            const period = $(this).data('period');
            window.location.href = '<?= URLROOT ?>/reports/analytics?period=' + period;
        });
    });
</script>


            </div> <!-- End container-fluid -->
        </div> <!-- End page-content-wrapper -->
    </div> <!-- End wrapper -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
    <script src="<?php echo URLROOT; ?>/js/main.js"></script>
</body>
</html>