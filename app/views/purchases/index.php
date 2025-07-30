<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>

<div class="container-fluid mt-0 pt-3">
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <h1 class="mb-0"><i class="fa-solid fa-shopping-cart"></i> Purchases Management</h1>
            <p class="text-muted mb-0">Manage supplier orders, receive stock, and track purchases</p>
        </div>
    </div>

    <div class="row">
        <!-- New Purchase Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-plus-circle"></i> New Purchase</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Create new purchase orders and receive stock from suppliers.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/purchases/add" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-plus"></i> Create Purchase Order
                        </a>
                        <a href="<?php echo URLROOT; ?>/purchases/quick" class="btn btn-outline-primary">
                            <i class="fa-solid fa-bolt"></i> Quick Purchase
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase History Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-list"></i> Purchase History</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">View and manage all purchase orders and deliveries.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/purchases/list" class="btn btn-outline-success">
                            <i class="fa-solid fa-list"></i> All Purchases
                        </a>
                        <a href="<?php echo URLROOT; ?>/purchases/pending" class="btn btn-success">
                            <i class="fa-solid fa-clock"></i> Pending Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suppliers Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-truck"></i> Suppliers</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Manage supplier information and purchase history.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/suppliers" class="btn btn-outline-info">
                            <i class="fa-solid fa-list"></i> View Suppliers
                        </a>
                        <a href="<?php echo URLROOT; ?>/suppliers/add" class="btn btn-info">
                            <i class="fa-solid fa-plus"></i> Add Supplier
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receiving Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-box-open"></i> Receiving</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Receive shipments and update inventory levels.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/purchases/receive" class="btn btn-outline-warning">
                            <i class="fa-solid fa-box-open"></i> Receive Shipment
                        </a>
                        <a href="<?php echo URLROOT; ?>/purchases/received" class="btn btn-warning">
                            <i class="fa-solid fa-check"></i> Received Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Returns Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-undo"></i> Purchase Returns</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Process returns to suppliers and credit notes.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/returns/addpurchase" class="btn btn-outline-danger">
                            <i class="fa-solid fa-plus"></i> New Return
                        </a>
                        <a href="<?php echo URLROOT; ?>/returns/purchase" class="btn btn-danger">
                            <i class="fa-solid fa-list"></i> Return History
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Reports Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-chart-bar"></i> Purchase Reports</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Analyze purchase trends and supplier performance.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/reports/purchases" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-chart-line"></i> Purchase Reports
                        </a>
                        <a href="<?php echo URLROOT; ?>/reports/suppliers" class="btn btn-secondary">
                            <i class="fa-solid fa-analytics"></i> Supplier Analysis
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
                    <h5 class="mb-0"><i class="fa-solid fa-chart-bar"></i> Purchase Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-primary mb-1">$--</h4>
                                <small class="text-muted">Monthly Purchases</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-warning mb-1">--</h4>
                                <small class="text-muted">Pending Orders</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-success mb-1">--</h4>
                                <small class="text-muted">Active Suppliers</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-info mb-1">--</h4>
                                <small class="text-muted">Items Received</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'footer.php'; ?>