<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container theme-unified">
    <!-- Page Header -->
    <div class="theme-header">
        <div class="row align-items-center mb-4">
            <div class="col-12 col-lg-8">
                <h1 class="mb-0">
                    <i class="fas fa-chart-line"></i>
                    Sales Management
                </h1>
                <p class="description">Process sales, manage customers, and track performance</p>
            </div>
            <div class="col-12 col-lg-4 text-lg-right mt-3 mt-lg-0">
                <a href="<?php echo URLROOT; ?>/sales/add" class="btn btn-success btn-lg mr-2">
                    <i class="fas fa-plus"></i> New Sale
                </a>
                <a href="<?php echo URLROOT; ?>/pos" class="btn btn-primary btn-lg mr-2">
                    <i class="fas fa-cash-register"></i> POS
                </a>
                <a href="<?php echo URLROOT; ?>/customers/add" class="btn btn-warning btn-lg">
                    <i class="fas fa-user-plus"></i> Add Customer
                </a>
            </div>
        </div>
    </div>

    <!-- Sales Action Cards - All 6 in One Line -->
    <div class="row mb-4">
        <!-- New Sale Section -->
        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-6 mb-3">
            <div class="kpi-card">
                <div class="card-header bg-success-theme text-white">
                    <h6 class="mb-0"><i class="fas fa-plus-circle"></i> New Sale</h6>
                </div>
                <div class="card-body text-center p-2">
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/sales/add" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Start Sale
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales History Section -->
        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-6 mb-3">
            <div class="kpi-card">
                <div class="card-header bg-primary-theme text-white">
                    <h6 class="mb-0"><i class="fas fa-list"></i> Sales History</h6>
                </div>
                <div class="card-body text-center p-2">
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/sales/list" class="btn btn-primary btn-sm">
                            <i class="fas fa-list"></i> All Sales
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Point of Sale Section -->
        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-6 mb-3">
            <div class="kpi-card">
                <div class="card-header bg-info-theme text-white">
                    <h6 class="mb-0"><i class="fas fa-cash-register"></i> POS</h6>
                </div>
                <div class="card-body text-center p-2">
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/pos" class="btn btn-info btn-sm">
                            <i class="fas fa-cash-register"></i> Open POS
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customers Section -->
        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-6 mb-3">
            <div class="kpi-card">
                <div class="card-header bg-warning-theme text-white">
                    <h6 class="mb-0"><i class="fas fa-users"></i> Customers</h6>
                </div>
                <div class="card-body text-center p-2">
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/customers" class="btn btn-warning btn-sm">
                            <i class="fas fa-list"></i> View All
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices Section -->
        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-6 mb-3">
            <div class="kpi-card">
                <div class="card-header bg-danger-theme text-white">
                    <h6 class="mb-0"><i class="fas fa-file-invoice"></i> Invoices</h6>
                </div>
                <div class="card-body text-center p-2">
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/invoices" class="btn btn-danger btn-sm">
                            <i class="fas fa-list"></i> All Invoices
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Reports Section -->
        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-6 mb-3">
            <div class="kpi-card">
                <div class="card-header bg-secondary-theme text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Reports</h6>
                </div>
                <div class="card-body text-center p-2">
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/reports/sales" class="btn btn-secondary btn-sm">
                            <i class="fas fa-chart-line"></i> Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Sales Summary - KPI Row -->
    <div class="row mb-4 kpi-row">
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="kpi-card">
                <div class="card-header bg-success-theme text-white">
                    <h6 class="mb-0"><i class="fas fa-dollar-sign"></i> Today's Revenue</h6>
                </div>
                <div class="card-body text-center p-2">
                    <h5 class="text-success mb-1">$--</h5>
                    <small class="text-muted">vs yesterday</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="kpi-card">
                <div class="card-header bg-primary-theme text-white">
                    <h6 class="mb-0"><i class="fas fa-receipt"></i> Transactions</h6>
                </div>
                <div class="card-body text-center p-2">
                    <h5 class="text-primary mb-1">--</h5>
                    <small class="text-muted">today</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="kpi-card">
                <div class="card-header bg-info-theme text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-line"></i> Average Sale</h6>
                </div>
                <div class="card-body text-center p-2">
                    <h5 class="text-info mb-1">$--</h5>
                    <small class="text-muted">per transaction</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="kpi-card">
                <div class="card-header bg-warning-theme text-white">
                    <h6 class="mb-0"><i class="fas fa-box"></i> Items Sold</h6>
                </div>
                <div class="card-body text-center p-2">
                    <h5 class="text-warning mb-1">--</h5>
                    <small class="text-muted">today</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>