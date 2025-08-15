<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container theme-unified mt-0 pt-3">
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <h1 class="mb-0"><i class="fa-solid fa-chart-bar"></i> Reports & Analytics</h1>
            <p class="text-muted mb-0">Generate insights and analyze business performance</p>
        </div>
    </div>

    <div class="row">
        <!-- Sales Reports Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="theme-card h-100">
                <div class="card-header bg-success-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-chart-line"></i> Sales Reports</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Analyze sales performance and customer trends.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/reports/sales" class="btn btn-success btn-lg">
                            <i class="fa-solid fa-chart-line"></i> Sales Analytics
                        </a>
                        <a href="<?php echo URLROOT; ?>/reports/salereturns" class="btn btn-outline-success">
                            <i class="fa-solid fa-undo"></i> Sale Returns
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Reports Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="theme-card h-100">
                <div class="card-header bg-primary-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-shopping-cart"></i> Purchase Reports</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Track purchasing trends and supplier performance.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/reports/purchases" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-shopping-cart"></i> Purchase Analytics
                        </a>
                        <a href="<?php echo URLROOT; ?>/reports/purchasereturns" class="btn btn-outline-primary">
                            <i class="fa-solid fa-undo"></i> Purchase Returns
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Reports Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="theme-card h-100">
                <div class="card-header bg-warning-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-boxes"></i> Inventory Reports</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Monitor Inventory levels and inventory movement.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/reports/inventory" class="btn btn-warning btn-lg">
                            <i class="fa-solid fa-boxes"></i> Inventory Reports
                        </a>
                        <a href="<?php echo URLROOT; ?>/reports/Inventoryalerts" class="btn btn-outline-warning">
                            <i class="fa-solid fa-exclamation-triangle"></i> Inventory Alerts
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Reports Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="theme-card h-100">
                <div class="card-header bg-info-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-dollar-sign"></i> Financial Reports</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">View profit/loss statements and financial summaries.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/reports/financial" class="btn btn-info btn-lg">
                            <i class="fa-solid fa-dollar-sign"></i> P&L Report
                        </a>
                        <a href="<?php echo URLROOT; ?>/reports/cashflow" class="btn btn-outline-info">
                            <i class="fa-solid fa-money-bill-wave"></i> Cash Flow
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Reports Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="theme-card h-100">
                <div class="card-header bg-secondary-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-users"></i> Customer Reports</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Analyze customer behavior and purchase patterns.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/reports/customers" class="btn btn-secondary btn-lg">
                            <i class="fa-solid fa-users"></i> Customer Analytics
                        </a>
                        <a href="<?php echo URLROOT; ?>/reports/loyalty" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-star"></i> Loyalty Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Reports Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="theme-card h-100">
                <div class="card-header bg-dark-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-cog"></i> Custom Reports</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Create and schedule custom reports and exports.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/reports/custom" class="btn btn-dark btn-lg">
                            <i class="fa-solid fa-cog"></i> Report Builder
                        </a>
                        <a href="<?php echo URLROOT; ?>/reports/exports" class="btn btn-outline-dark">
                            <i class="fa-solid fa-download"></i> Export Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Dashboard -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-tachometer-alt"></i> Quick Dashboard</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2 col-6 mb-3">
                            <div class="p-3 theme-card-light rounded">
                                <h4 class="text-success mb-1">$--</h4>
                                <small class="text-muted">Today's Sales</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="p-3 theme-card-light rounded">
                                <h4 class="text-primary mb-1">$--</h4>
                                <small class="text-muted">Monthly Revenue</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="p-3 theme-card-light rounded">
                                <h4 class="text-warning mb-1">--</h4>
                                <small class="text-muted">Low Inventory Items</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="p-3 theme-card-light rounded">
                                <h4 class="text-info mb-1">--</h4>
                                <small class="text-muted">Total Products</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="p-3 theme-card-light rounded">
                                <h4 class="text-secondary mb-1">--</h4>
                                <small class="text-muted">Active Customers</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="p-3 theme-card-light rounded">
                                <h4 class="text-dark mb-1">--</h4>
                                <small class="text-muted">Pending Orders</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


</div> <!-- End container-fluid -->
</div> <!-- End page-content-wrapper -->
</div> <!-- End wrapper -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
    crossorigin="anonymous"></script>
<script src="<?php echo URLROOT; ?>/public/js/main.js"></script>
</body>

</html>