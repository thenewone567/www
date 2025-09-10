<?php
// Extract data passed from controller
extract($data);

$pageTitle = 'Customer Directory - Admin Panel';
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
?>

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css"
    href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.bootstrap4.min.css">

<div class="admin-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-0">
                    <i class="fas fa-shopping-cart"></i> Customer Directory
                </h1>
                <p class="mb-0 mt-2 opacity-75">Customer management and analytics dashboard</p>
            </div>
            <div class="col-md-4 text-md-right">
                <div class="btn-group" role="group">
                    <a href="<?php echo URLROOT; ?>/admin/users" class="btn btn-outline-secondary btn-sm mr-2">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-plus"></i> Add Customer
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo URLROOT; ?>/admin/addCustomer">
                            <i class="fas fa-user-plus"></i> New Customer
                        </a>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#importCustomersModal">
                            <i class="fas fa-file-import"></i> Import Customers
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- KPI Dashboard Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $kpis['total_customers'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $kpis['active_customers'] ?? 0; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?php echo number_format($kpis['total_revenue'] ?? 0, 2); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Average Order Value
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?php echo number_format($kpis['avg_order_value'] ?? 0, 2); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-bag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Type Distribution -->
    <div class="row mb-4">
        <div class="col-xl-6 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Type Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="customer-type-item mb-3">
                                <span class="badge badge-primary customer-type-badge">🏢 Business</span>
                                <span class="ml-2"><?php echo $kpis['business_customers'] ?? 0; ?> customers</span>
                            </div>
                            <div class="customer-type-item mb-3">
                                <span class="badge badge-success customer-type-badge">🏠 Individual</span>
                                <span class="ml-2"><?php echo $kpis['individual_customers'] ?? 0; ?> customers</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="customer-type-item mb-3">
                                <span class="badge badge-info customer-type-badge">🔧 Contractor</span>
                                <span class="ml-2"><?php echo $kpis['contractor_customers'] ?? 0; ?> customers</span>
                            </div>
                            <div class="customer-type-item mb-3">
                                <span class="badge badge-warning customer-type-badge">🏪 Retail</span>
                                <span class="ml-2"><?php echo $kpis['retail_customers'] ?? 0; ?> customers</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                </div>
                <div class="card-body">
                    <div class="customer-activity-item mb-3">
                        <small class="text-muted">Today</small>
                        <div><?php echo $kpis['new_customers_today'] ?? 0; ?> new customers registered</div>
                    </div>
                    <div class="customer-activity-item mb-3">
                        <small class="text-muted">This Week</small>
                        <div><?php echo $kpis['orders_this_week'] ?? 0; ?> orders placed</div>
                    </div>
                    <div class="customer-activity-item mb-3">
                        <small class="text-muted">This Month</small>
                        <div>$<?php echo number_format($kpis['revenue_this_month'] ?? 0, 2); ?> revenue generated</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Directory Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-users"></i> Customer Directory
                        </h6>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary active"
                                onclick="filterCustomers('all')">
                                <i class="fas fa-users"></i> All
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="filterCustomers('active')">
                                <i class="fas fa-user-check"></i> Active
                            </button>
                            <button type="button" class="btn btn-outline-warning" onclick="filterCustomers('inactive')">
                                <i class="fas fa-user-times"></i> Inactive
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="customersTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Contact</th>
                                    <th>Orders</th>
                                    <th>Total Spent</th>
                                    <th>Last Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($customers) && !empty($customers)): ?>
                                    <?php foreach ($customers as $customer): ?>
                                        <tr data-status="<?php echo $customer->is_active ? 'active' : 'inactive'; ?>">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-avatar mr-3">
                                                        <i class="fas fa-user-circle fa-2x text-muted"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold">
                                                            <?php echo htmlspecialchars($customer->customer_name ?? 'N/A'); ?>
                                                        </div>
                                                        <div class="text-muted small">ID:
                                                            <?php echo $customer->customer_id ?? 'N/A'; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $customerType = $customer->customer_type ?? 'individual';
                                                $typeConfig = [
                                                    'business' => ['🏢', 'badge-primary'],
                                                    'individual' => ['🏠', 'badge-success'],
                                                    'contractor' => ['🔧', 'badge-info'],
                                                    'retail' => ['🏪', 'badge-warning']
                                                ];
                                                $config = $typeConfig[$customerType] ?? ['👤', 'badge-secondary'];
                                                ?>
                                                <span class="badge <?php echo $config[1]; ?> customer-type-badge">
                                                    <?php echo $config[0] . ' ' . ucfirst($customerType); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div><?php echo htmlspecialchars($customer->email ?? 'N/A'); ?></div>
                                                <div class="text-muted small">
                                                    <?php echo htmlspecialchars($customer->phone ?? 'N/A'); ?>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge badge-info"><?php echo $customer->total_orders ?? 0; ?></span>
                                            </td>
                                            <td class="text-right">
                                                <strong>$<?php echo number_format($customer->total_spent ?? 0, 2); ?></strong>
                                            </td>
                                            <td>
                                                <?php if (isset($customer->last_order_date) && $customer->last_order_date): ?>
                                                    <span
                                                        class="text-muted small"><?php echo date('M d, Y', strtotime($customer->last_order_date)); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted small">No orders</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($customer->is_active ?? true): ?>
                                                    <span class="badge badge-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                                        onclick="viewCustomer(<?php echo $customer->customer_id; ?>)"
                                                        title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-success btn-sm"
                                                        onclick="editCustomer(<?php echo $customer->customer_id; ?>)"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-warning btn-sm"
                                                        onclick="toggleCustomerStatus(<?php echo $customer->customer_id; ?>, '<?php echo $customer->is_active ? 'inactive' : 'active'; ?>')"
                                                        title="<?php echo $customer->is_active ? 'Deactivate' : 'Activate'; ?>">
                                                        <i
                                                            class="fas fa-<?php echo $customer->is_active ? 'user-times' : 'user-check'; ?>"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <!-- Empty row will be handled by DataTables emptyTable message -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Contact</th>
                                    <th>Orders</th>
                                    <th>Total Spent</th>
                                    <th>Last Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .customer-type-badge {
        font-size: 0.85em;
        padding: 0.4em 0.8em;
    }

    .customer-avatar {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .customer-activity-item {
        border-left: 3px solid var(--primary);
        padding-left: 12px;
    }

    .customer-type-item {
        display: flex;
        align-items: center;
    }
</style>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.7/js/responsive.bootstrap4.min.js"></script>

<script>
    // Initialize DataTable
    $(document).ready(function () {
        // Check if DataTables is available and table exists
        if ($.fn.DataTable && $('#customersTable').length) {
            try {
                // Debug: Log table structure
                console.log('Table headers:', $('#customersTable thead th').length);
                console.log('Table data rows:', $('#customersTable tbody tr').length);

                $('#customersTable').DataTable({
                    "pageLength": 25,
                    "order": [[4, "desc"]], // Sort by total spent descending
                    "columnDefs": [
                        { "orderable": false, "targets": [7] }, // Disable sorting on Actions column (index 7)
                        { "searchable": false, "targets": [7] } // Disable search on Actions column
                    ],
                    "language": {
                        "search": "Search customers:",
                        "lengthMenu": "Show _MENU_ customers per page",
                        "info": "Showing _START_ to _END_ of _TOTAL_ customers",
                        "infoEmpty": "No customers found",
                        "infoFiltered": "(filtered from _MAX_ total customers)",
                        "emptyTable": "No customers found. Start by adding your first customer!"
                    },
                    "responsive": true,
                    "autoWidth": false,
                    "processing": true,
                    "deferRender": true
                });
            } catch (error) {
                console.error('DataTables initialization error:', error);
                // Fallback: just show the table without DataTables features
            }
        } else {
            console.log('DataTables not available or table not found');
        }
    });

    // Filter functions
    function filterCustomers(status) {
        try {
            const table = $('#customersTable').DataTable();

            // Remove active class from all buttons
            $('.btn-group .btn').removeClass('active');

            if (status === 'all') {
                table.search('').draw();
                $('button[onclick="filterCustomers(\'all\')"]').addClass('active');
            } else {
                table.column(6).search(status === 'active' ? 'Active' : 'Inactive').draw();
                $('button[onclick="filterCustomers(\'' + status + '\')"]').addClass('active');
            }
        } catch (error) {
            console.error('Filter error:', error);
        }
    }

    // Customer management functions
    function viewCustomer(customerId) {
        window.location.href = '<?php echo URLROOT; ?>/admin/viewCustomer/' + customerId;
    }

    function editCustomer(customerId) {
        window.location.href = '<?php echo URLROOT; ?>/admin/editCustomer/' + customerId;
    }

    function toggleCustomerStatus(customerId, newStatus) {
        if (confirm('Are you sure you want to ' + newStatus + ' this customer?')) {
            fetch('<?php echo URLROOT; ?>/customer/toggleCustomerStatus', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'customer_id=' + customerId + '&status=' + newStatus
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + (data.error || 'Failed to update customer status'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating customer status');
                });
        }
    }
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>