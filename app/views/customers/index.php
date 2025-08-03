<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container">
    <!-- Page Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <h1 class="mb-0">
                <i class="fas fa-users"></i>
                Customer Management
            </h1>
            <p class="text-muted mb-0">Manage customer information and purchase history</p>
        </div>
    </div>

    <!-- Action Cards Row -->
    <div class="row mb-4">
        <!-- Add Customer Section -->
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="theme-card">
                <div class="card-header bg-success-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-user-plus"></i> Add Customer</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Register new customers and set up their accounts.</p>
                    <a href="<?php echo URLROOT; ?>/customers/add" class="btn btn-success btn-lg btn-block">
                        <i class="fas fa-plus"></i> Add New Customer
                    </a>
                </div>
            </div>
        </div>

        <!-- Customer Analytics Section -->
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="theme-card">
                <div class="card-header bg-info-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Customer Reports</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">View customer analytics and purchase reports.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/reports/customers" class="btn btn-outline-info">
                            <i class="fas fa-chart-line"></i> Customer Reports
                        </a>
                        <a href="<?php echo URLROOT; ?>/customers/export" class="btn btn-info">
                            <i class="fas fa-download"></i> Export Data
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Section -->
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="theme-stats">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="theme-stat-item">
                                    <h4 class="text-primary"><?php echo count($data['customers'] ?? []); ?></h4>
                                    <small>Total Customers</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="theme-stat-item">
                                    <h4 class="text-success">--</h4>
                                    <small>Active This Month</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="theme-card">
        <div class="card-header bg-secondary-theme text-white">
            <h5 class="mb-0">
                <i class="fas fa-list"></i>
                All Customers
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="theme-table">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Contact Info</th>
                            <th>Credit Limit</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['customers'])): ?>
                            <?php foreach ($data['customers'] as $customer): ?>
                                <tr>
                                    <td><?php echo $customer->customer_id; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($customer->customer_name); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($customer->contact_info); ?></td>
                                    <td>
                                        <span class="text-success font-weight-bold">
                                            $<?php echo number_format($customer->credit_limit, 2); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="theme-action-group">
                                            <a href="<?php echo URLROOT; ?>/customers/view/<?php echo $customer->customer_id; ?>"
                                                class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo URLROOT; ?>/customers/edit/<?php echo $customer->customer_id; ?>"
                                                class="btn btn-sm btn-primary" title="Edit Customer">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form class="d-inline"
                                                action="<?php echo URLROOT; ?>/customers/delete/<?php echo $customer->customer_id; ?>"
                                                method="post"
                                                onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete Customer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-3x mb-3 text-muted"></i>
                                    <br>
                                    <strong>No customers found</strong>
                                    <br>
                                    <small>Start by adding your first customer</small>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
</div> <!-- End wrapper -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
    crossorigin="anonymous"></script>
<script src="<?php echo URLROOT; ?>/js/main.js"></script>
</body>

</html>