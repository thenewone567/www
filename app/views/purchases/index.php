<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container theme-unified mt-0 pt-3">
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <h1 class="mb-0"><i class="fa-solid fa-shopping-cart"></i> Purchases Management</h1>
            <p class="text-muted mb-0">Manage supplier orders, receive Inventory, and track purchases</p>
        </div>
    </div>

    <!-- Purchase Summary Row (moved to top) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-chart-bar"></i> Purchase Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 theme-card-light rounded">
                                <h4 class="text-primary mb-1">
                                    $<?php echo isset($data['monthly_purchases']) ? $data['monthly_purchases'] : '0.00'; ?>
                                </h4>
                                <small class="text-muted">Monthly Purchases</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 theme-card-light rounded">
                                <h4 class="text-warning mb-1">
                                    <?php echo isset($data['pending_orders']) ? $data['pending_orders'] : '0'; ?>
                                </h4>
                                <small class="text-muted">Pending Orders</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 theme-card-light rounded">
                                <h4 class="text-success mb-1">
                                    <?php echo isset($data['active_suppliers']) ? $data['active_suppliers'] : '0'; ?>
                                </h4>
                                <small class="text-muted">Active Suppliers</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 theme-card-light rounded">
                                <h4 class="text-info mb-1">
                                    <?php echo isset($data['items_received']) ? $data['items_received'] : '0'; ?>
                                </h4>
                                <small class="text-muted">Items Received</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Purchase Summary Row -->

    <div class="row">
        <!-- New Purchase Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="theme-card h-100">
                <div class="card-header bg-primary-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-plus-circle"></i> New Purchase</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Create new purchase orders and receive Inventory from suppliers.</p>
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
            <div class="theme-card h-100">
                <div class="card-header bg-success-theme text-white">
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
            <div class="theme-card h-100">
                <div class="card-header bg-info-theme text-white">
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
            <div class="theme-card h-100">
                <div class="card-header bg-warning-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-box-open"></i> Receiving</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Receive shipments and update inventory levels.</p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/receiving/pending" class="btn btn-outline-warning">
                            <i class="fa-solid fa-box-open"></i> Receive Shipment
                        </a>
                        <a href="<?php echo URLROOT; ?>/receiving/completed" class="btn btn-warning">
                            <i class="fa-solid fa-check"></i> Received Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Returns Section -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="theme-card h-100">
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
            <div class="theme-card h-100">
                <div class="card-header bg-secondary-theme text-white">
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

    <!-- All Products Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-info-theme text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa-solid fa-boxes"></i> All Products</h5>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" class="form-control form-control-sm mr-2" style="width: 200px;"
                            placeholder="Search products...">
                        <select class="form-control form-control-sm mr-2" style="width: 140px;">
                            <option value="">All Status</option>
                            <option value="low">Low Inventory</option>
                            <option value="expired">Expired</option>
                            <option value="expiring">Expiring Soon</option>
                        </select>
                        <button class="btn btn-sm btn-outline-light"><i class="fa fa-filter"></i> Filter</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="theme-table">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Inventory</th>
                                    <th>Expiry</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Example rows, replace with dynamic data -->
                                <tr>
                                    <td>Hammer</td>
                                    <td>HAM123</td>
                                    <td><span class="badge badge-warning">3</span></td>
                                    <td>--</td>
                                    <td><span class="badge badge-danger">Low Inventory</span></td>
                                    <td><button class="btn btn-sm btn-outline-primary">View</button></td>
                                </tr>
                                <tr>
                                    <td>Wood Glue</td>
                                    <td>GLU456</td>
                                    <td>15</td>
                                    <td>2025-08-10</td>
                                    <td><span class="badge badge-info">Expiring Soon</span></td>
                                    <td><button class="btn btn-sm btn-outline-primary">View</button></td>
                                </tr>
                                <tr>
                                    <td>Paint Thinner</td>
                                    <td>PNT789</td>
                                    <td>0</td>
                                    <td>2024-12-01</td>
                                    <td><span class="badge badge-danger">Expired</span></td>
                                    <td><button class="btn btn-sm btn-outline-primary">View</button></td>
                                </tr>
                                <tr>
                                    <td>Screwdriver Set</td>
                                    <td>SCR321</td>
                                    <td>42</td>
                                    <td>--</td>
                                    <td><span class="badge badge-success">In Inventory</span></td>
                                    <td><button class="btn btn-sm btn-outline-primary">View</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div> <!-- End theme-table -->
                </div>
            </div>
        </div>
    </div>
    <!-- End All Products Section -->
</div>


</div> <!-- End container-fluid -->
</div> <!-- End page-content-wrapper -->
</div> <!-- End wrapper -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
    crossorigin="anonymous"></script>
<script src="<?php echo URLROOT; ?>/public/js/currency-formatter.js"></script>

<script>
    $(document).ready(function () {
        // Filter and search functionality
        let currentFilter = 'all';

        // Quick filter buttons
        $('.quick-filter').click(function () {
            $('.quick-filter').removeClass('active');
            $(this).addClass('active');
            currentFilter = $(this).data('filter');
            applyFilters();
        });

        // Search input
        $('#product-search').on('input', function () {
            applyFilters();
        });

        // Filter dropdowns
        $('#status-filter, #category-filter, #sort-filter').change(function () {
            applyFilters();
        });

        function applyFilters() {
            const searchTerm = $('#product-search').val().toLowerCase();
            const statusFilter = $('#status-filter').val();
            const categoryFilter = $('#category-filter').val();
            const sortFilter = $('#sort-filter').val();

            let visibleRows = 0;

            // Filter rows
            $('.product-row').each(function () {
                let showRow = true;
                const $row = $(this);
                const productName = $row.find('strong').first().text().toLowerCase();
                const sku = $row.find('code').text().toLowerCase();
                const productStatus = $row.data('status');
                const productCategory = $row.data('category');

                // Search filter
                if (searchTerm && !productName.includes(searchTerm) && !sku.includes(searchTerm)) {
                    showRow = false;
                }

                // Status filter
                if (statusFilter) {
                    if (statusFilter === 'low' && productStatus !== 'low-Inventory') showRow = false;
                    if (statusFilter === 'out' && productStatus !== 'out-of-Inventory') showRow = false;
                    if (statusFilter === 'expired' && productStatus !== 'expired') showRow = false;
                    if (statusFilter === 'expiring' && productStatus !== 'expiring') showRow = false;
                    if (statusFilter === 'good' && productStatus !== 'good-Inventory') showRow = false;
                }

                // Category filter
                if (categoryFilter && productCategory !== categoryFilter) {
                    showRow = false;
                }

                // Quick filter
                if (currentFilter !== 'all') {
                    if (currentFilter === 'out-of-Inventory' && productStatus !== 'out-of-Inventory') showRow = false;
                    if (currentFilter === 'low-Inventory' && productStatus !== 'low-Inventory') showRow = false;
                    if (currentFilter === 'expiring' && productStatus !== 'expiring') showRow = false;
                    if (currentFilter === 'expensive') {
                        const price = parseFloat($row.find('.text-success').text().replace('₹', '').replace(',', ''));
                        if (price < 1000) showRow = false;
                    }
                    if (currentFilter === 'recent') {
                        const addedDate = $row.data('added');
                        const isRecent = addedDate === '2025-08-01' || addedDate === '2025-08-02';
                        if (!isRecent) showRow = false;
                    }
                }

                if (showRow) {
                    $row.show();
                    visibleRows++;
                } else {
                    $row.hide();
                }
            });

            // Sort visible rows
            if (sortFilter && visibleRows > 0) {
                sortTable(sortFilter);
            }

            // Update row count
            updateRowCount(visibleRows);
        }

        function sortTable(sortBy) {
            const tbody = $('#products-table tbody');
            const rows = tbody.find('.product-row:visible').detach();

            rows.sort(function (a, b) {
                switch (sortBy) {
                    case 'name':
                        return $(a).find('strong').first().text().localeCompare($(b).find('strong').first().text());
                    case 'Inventory-asc':
                        return parseInt($(a).find('.badge').first().text()) - parseInt($(b).find('.badge').first().text());
                    case 'Inventory-desc':
                        return parseInt($(b).find('.badge').first().text()) - parseInt($(a).find('.badge').first().text());
                    case 'recent':
                        return new Date($(b).data('added')) - new Date($(a).data('added'));
                    case 'price-asc':
                        const priceA = parseFloat($(a).find('.text-success').text().replace('₹', '').replace(',', ''));
                        const priceB = parseFloat($(b).find('.text-success').text().replace('₹', '').replace(',', ''));
                        return priceA - priceB;
                    case 'price-desc':
                        const priceA2 = parseFloat($(a).find('.text-success').text().replace('₹', '').replace(',', ''));
                        const priceB2 = parseFloat($(b).find('.text-success').text().replace('₹', '').replace(',', ''));
                        return priceB2 - priceA2;
                    default:
                        return 0;
                }
            });

            tbody.append(rows);
        }

        function updateRowCount(count) {
            // Update the "All Products" button count
            $('.quick-filter[data-filter="all"]').html('<i class="fas fa-list"></i> All Products (' + count + ')');
        }

        // Clear filters function
        window.clearFilters = function () {
            $('#product-search').val('');
            $('#status-filter').val('');
            $('#category-filter').val('');
            $('#sort-filter').val('name');
            $('.quick-filter').removeClass('active');
            $('.quick-filter[data-filter="all"]').addClass('active');
            currentFilter = 'all';
            applyFilters();
        };

        // Apply filters function (make it global)
        window.applyFilters = applyFilters;

        // Tooltip initialization
        $('[title]').tooltip();

        // Row hover effect
        $('.product-row').hover(
            function () { $(this).addClass('table-active'); },
            function () { $(this).removeClass('table-active'); }
        );
    });
</script>

<style>
    .quick-filter {
        transition: all 0.3s ease;
    }

    .quick-filter.active {
        background-color: #007bff !important;
        color: white !important;
        border-color: #007bff !important;
    }

    .product-row {
        transition: all 0.2s ease;
    }

    .product-row:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .badge-sm {
        font-size: 0.6rem;
        padding: 0.2rem 0.4rem;
    }

    .btn-outline-purple {
        color: #6f42c1;
        border-color: #6f42c1;
    }

    .btn-outline-purple:hover {
        background-color: #6f42c1;
        border-color: #6f42c1;
        color: white;
    }
</style>

<script src="<?php echo URLROOT; ?>/js/main.js"></script>
</body>

</html>