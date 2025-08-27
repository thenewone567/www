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

    <!-- Sales Action Cards - unified markup -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-6 mb-3">
            <div class="kpi-card kpi-gradient-success shadow-sm h-100">
                <div class="kpi-body text-center">
                    <div class="kpi-count">Start Sale</div>
                    <div class="kpi-value small">New Sale</div>
                    <div class="d-grid gap-2 mt-2">
                        <a href="<?php echo URLROOT; ?>/sales/add" class="btn btn-light btn-sm">
                            <i class="fas fa-plus"></i> Start Sale
                        </a>
                    </div>
                    <i class="fas fa-plus-circle kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-6 mb-3">
            <div class="kpi-card kpi-gradient-primary shadow-sm h-100">
                <div class="kpi-body text-center">
                    <div class="kpi-count">All Sales</div>
                    <div class="kpi-value small">Sales History</div>
                    <div class="d-grid gap-2 mt-2">
                        <a href="<?php echo URLROOT; ?>/sales/list" class="btn btn-light btn-sm">
                            <i class="fas fa-list"></i> View
                        </a>
                    </div>
                    <i class="fas fa-list kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-6 mb-3">
            <div class="kpi-card kpi-gradient-info shadow-sm h-100">
                <div class="kpi-body text-center">
                    <div class="kpi-count">Open POS</div>
                    <div class="kpi-value small">Point of Sale</div>
                    <div class="d-grid gap-2 mt-2">
                        <a href="<?php echo URLROOT; ?>/pos" class="btn btn-light btn-sm">
                            <i class="fas fa-cash-register"></i> Open POS
                        </a>
                    </div>
                    <i class="fas fa-cash-register kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-6 mb-3">
            <div class="kpi-card kpi-gradient-warning shadow-sm h-100">
                <div class="kpi-body text-center">
                    <div class="kpi-count">Customers</div>
                    <div class="kpi-value small">Manage Customers</div>
                    <div class="d-grid gap-2 mt-2">
                        <a href="<?php echo URLROOT; ?>/customers" class="btn btn-light btn-sm">
                            <i class="fas fa-users"></i> View
                        </a>
                    </div>
                    <i class="fas fa-users kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-6 mb-3">
            <div class="kpi-card kpi-gradient-danger shadow-sm h-100">
                <div class="kpi-body text-center">
                    <div class="kpi-count">Invoices</div>
                    <div class="kpi-value small">All Invoices</div>
                    <div class="d-grid gap-2 mt-2">
                        <a href="<?php echo URLROOT; ?>/invoices" class="btn btn-light btn-sm">
                            <i class="fas fa-file-invoice"></i> View
                        </a>
                    </div>
                    <i class="fas fa-file-invoice kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-4 col-6 mb-3">
            <div class="kpi-card kpi-gradient-primary shadow-sm h-100">
                <div class="kpi-body text-center">
                    <div class="kpi-count">Reports</div>
                    <div class="kpi-value small">Sales Reports</div>
                    <div class="d-grid gap-2 mt-2">
                        <a href="<?php echo URLROOT; ?>/reports/sales" class="btn btn-light btn-sm">
                            <i class="fas fa-chart-line"></i> View
                        </a>
                    </div>
                    <i class="fas fa-chart-bar kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Sales Summary - unified KPI row -->
    <div class="row mb-4 kpi-row">
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="kpi-card kpi-gradient-success shadow-sm h-100">
                <div class="kpi-body text-center">
                    <div class="kpi-count">$--</div>
                    <div class="kpi-value small">Today's Revenue • vs yesterday</div>
                    <i class="fas fa-dollar-sign kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="kpi-card kpi-gradient-primary shadow-sm h-100">
                <div class="kpi-body text-center">
                    <div class="kpi-count">--</div>
                    <div class="kpi-value small">Transactions • today</div>
                    <i class="fas fa-receipt kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="kpi-card kpi-gradient-info shadow-sm h-100">
                <div class="kpi-body text-center">
                    <div class="kpi-count">$--</div>
                    <div class="kpi-value small">Average Sale • per transaction</div>
                    <i class="fas fa-chart-line kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="kpi-card kpi-gradient-warning shadow-sm h-100">
                <div class="kpi-body text-center">
                    <div class="kpi-count">--</div>
                    <div class="kpi-value small">Items Sold • today</div>
                    <i class="fas fa-box kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>