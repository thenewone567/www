<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>

<div class="container-fluid mt-0 pt-3">
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <h1 class="mb-0"><i class="fa-solid fa-chart-line"></i> Sales Management</h1>
            <p class="text-muted mb-0">Process sales, manage customers, and track performance</p>
        </div>
    </div>

    <div class="row">
        <!-- New Sale Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-plus-circle"></i> New Sale</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Create a new sales transaction and process customer orders.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/sales/add" class="btn btn-success btn-lg">
                            <i class="fa-solid fa-plus"></i> Start New Sale
                        </a>
                        <a href="<?php echo URLROOT; ?>/sales/quick" class="btn btn-outline-success">
                            <i class="fa-solid fa-bolt"></i> Quick Sale
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales History Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-list"></i> Sales History</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">View and manage all sales transactions and invoices.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/sales/list" class="btn btn-outline-primary">
                            <i class="fa-solid fa-list"></i> All Sales
                        </a>
                        <a href="<?php echo URLROOT; ?>/sales/today" class="btn btn-primary">
                            <i class="fa-solid fa-calendar-day"></i> Today's Sales
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customers Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-users"></i> Customers</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Manage customer information and purchase history.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/customers" class="btn btn-outline-info">
                            <i class="fa-solid fa-list"></i> View Customers
                        </a>
                        <a href="<?php echo URLROOT; ?>/customers/add" class="btn btn-info">
                            <i class="fa-solid fa-user-plus"></i> Add Customer
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-file-invoice"></i> Invoices</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Generate and manage sales invoices and receipts.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/invoices" class="btn btn-outline-warning">
                            <i class="fa-solid fa-list"></i> All Invoices
                        </a>
                        <a href="<?php echo URLROOT; ?>/invoices/pending" class="btn btn-warning">
                            <i class="fa-solid fa-clock"></i> Pending Invoices
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Point of Sale Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-cash-register"></i> Point of Sale</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Quick checkout interface for retail sales.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/pos" class="btn btn-dark btn-lg">
                            <i class="fa-solid fa-cash-register"></i> Open POS
                        </a>
                        <a href="<?php echo URLROOT; ?>/pos/settings" class="btn btn-outline-dark">
                            <i class="fa-solid fa-cog"></i> POS Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Reports Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-chart-bar"></i> Sales Reports</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Analyze sales performance and generate reports.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/reports/sales" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-chart-line"></i> Sales Reports
                        </a>
                        <a href="<?php echo URLROOT; ?>/reports/analytics" class="btn btn-secondary">
                            <i class="fa-solid fa-analytics"></i> Sales Analytics
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-chart-bar"></i> Today's Sales Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-success mb-1">$--</h4>
                                <small class="text-muted">Today's Revenue</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-primary mb-1">--</h4>
                                <small class="text-muted">Transactions</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-info mb-1">$--</h4>
                                <small class="text-muted">Average Sale</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-warning mb-1">--</h4>
                                <small class="text-muted">Items Sold</small>
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
    <script src="<?php echo URLROOT; ?>/js/main.js"></script>
</body>
</html>