<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<?php
// User Role & Permission System
$userRole = $_SESSION['user_role'] ?? 'Associate';
$roleId = $_SESSION['role_id'] ?? 4;
$userId = $_SESSION['user_id'] ?? 0;
$userName = $_SESSION['user_name'] ?? $_SESSION['display_name'] ?? 'Guest User';

// Role mapping
$roleIdMapping = [
    1 => 'admin',
    2 => 'warehouse_manager',
    3 => 'receiving_clerk',
    4 => 'inventory_clerk',
    5 => 'viewer'
];

$systemRole = $roleIdMapping[$roleId] ?? 'viewer';

// Check permissions for replenishment
$permissions = [
    'admin' => ['can_replenish' => true],
    'warehouse_manager' => ['can_replenish' => true],
    'receiving_clerk' => ['can_replenish' => false],
    'inventory_clerk' => ['can_replenish' => true],
    'viewer' => ['can_replenish' => false]
];

$userPermissions = $permissions[$systemRole] ?? $permissions['viewer'];

// Redirect if no permission
if (!$userPermissions['can_replenish']) {
    header('Location: ' . URLROOT . '/inventory');
    exit();
}
?>

<!-- Theme System Styles -->
<div class="container-fluid theme-container">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-6">
            <h1 class="mb-1 font-weight-bold">
                <i class="fas fa-arrow-up mr-2"></i>Replenishment Operations
                <span class="badge badge-warning ml-2">Active</span>
            </h1>
            <small class="text-muted">
                Welcome, <?php echo htmlspecialchars($userName); ?> |
                Role: <?php echo ucwords(str_replace('_', ' ', $systemRole)); ?>
            </small>
        </div>
        <div class="col-12 col-md-6 text-md-right mt-3 mt-md-0">
            <a href="<?php echo URLROOT; ?>/inventory" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Replenishment Quick Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Low Stock</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-danger">23</h3>
                    <small class="text-muted">Items Need Replenishment</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-arrow-up"></i> Replenished Today</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-warning">156</h3>
                    <small class="text-muted">Items Restocked</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> Pending Tasks</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-info">8</h3>
                    <small class="text-muted">Replenishment Tasks</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle"></i> Completion Rate</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-success">87%</h3>
                    <small class="text-muted">Today's Performance</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Replenishment Operations -->
    <div class="row">
        <!-- Low Stock Items -->
        <div class="col-lg-6 mb-4">
            <div class="theme-card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Priority Items - Low Stock</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Hammer - 16oz</div>
                                <small class="text-muted">Shop Location: S1-A5-B2 | Current: 2 units</small>
                            </div>
                            <span class="badge bg-danger rounded-pill">Critical</span>
                            <button class="btn btn-sm btn-warning ml-2" onclick="startReplenishment('HAM001')">
                                <i class="fas fa-arrow-up"></i> Replenish
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Screwdriver Set</div>
                                <small class="text-muted">Shop Location: S1-B3-A1 | Current: 1 unit</small>
                            </div>
                            <span class="badge bg-danger rounded-pill">Critical</span>
                            <button class="btn btn-sm btn-warning ml-2" onclick="startReplenishment('SCR015')">
                                <i class="fas fa-arrow-up"></i> Replenish
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Paint Brush 2"</div>
                                <small class="text-muted">Shop Location: S2-C1-A3 | Current: 5 units</small>
                            </div>
                            <span class="badge bg-warning rounded-pill">Low</span>
                            <button class="btn btn-sm btn-warning ml-2" onclick="startReplenishment('PNT201')">
                                <i class="fas fa-arrow-up"></i> Replenish
                            </button>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Safety Goggles</div>
                                <small class="text-muted">Shop Location: S3-A1-B2 | Current: 3 units</small>
                            </div>
                            <span class="badge bg-warning rounded-pill">Low</span>
                            <button class="btn btn-sm btn-warning ml-2" onclick="startReplenishment('SAF105')">
                                <i class="fas fa-arrow-up"></i> Replenish
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Replenishment Scanner -->
        <div class="col-lg-6 mb-4">
            <div class="theme-card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-qrcode"></i> Replenishment Scanner</h5>
                </div>
                <div class="card-body">
                    <form id="replenishment-form">
                        <div class="form-group">
                            <label for="shop-location-scan">Scan Shop Location</label>
                            <div class="input-group">
                                <input type="text" class="form-control scanner-input" id="shop-location-scan"
                                    placeholder="Scan shop location barcode" autofocus>
                                <div class="input-group-append">
                                    <button class="btn-theme btn-info-theme" type="button" onclick="checkLocation()">
                                        <i class="fas fa-search"></i> Check
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="location-details" class="d-none">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-map-marker-alt"></i> Location Information</h6>
                                <div id="location-info"></div>
                            </div>

                            <div class="form-group">
                                <label for="current-stock">Current Stock Level</label>
                                <input type="number" class="form-theme" id="current-stock" readonly>
                            </div>

                            <div class="form-group">
                                <label for="warehouse-location">Warehouse Source Location</label>
                                <input type="text" class="form-theme" id="warehouse-location" readonly>
                            </div>

                            <div class="form-group">
                                <label for="replenish-quantity">Quantity to Replenish</label>
                                <input type="number" class="form-theme" id="replenish-quantity" min="1">
                            </div>

                            <div class="form-group">
                                <label for="warehouse-scan">Confirm Warehouse Location</label>
                                <div class="input-group">
                                    <input type="text" class="form-control scanner-input" id="warehouse-scan"
                                        placeholder="Scan warehouse location">
                                    <div class="input-group-append">
                                        <button class="btn-theme btn-success-theme" type="submit">
                                            <i class="fas fa-check"></i> Complete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Replenishment Progress -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="theme-card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Today's Replenishment Progress</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Item</th>
                                    <th>Shop Location</th>
                                    <th>Quantity</th>
                                    <th>From Warehouse</th>
                                    <th>Status</th>
                                    <th>By</th>
                                </tr>
                            </thead>
                            <tbody id="replenishment-progress">
                                <tr>
                                    <td>3:15 PM</td>
                                    <td>Drill Bits Set</td>
                                    <td>S1-B2-A3</td>
                                    <td>15</td>
                                    <td>W1-C5-B2</td>
                                    <td><span class="badge badge-success">Completed</span></td>
                                    <td><?php echo htmlspecialchars($userName); ?></td>
                                </tr>
                                <tr>
                                    <td>2:58 PM</td>
                                    <td>Work Gloves</td>
                                    <td>S2-A1-C1</td>
                                    <td>25</td>
                                    <td>W1-A8-A1</td>
                                    <td><span class="badge badge-success">Completed</span></td>
                                    <td><?php echo htmlspecialchars($userName); ?></td>
                                </tr>
                                <tr>
                                    <td>2:45 PM</td>
                                    <td>Extension Cord</td>
                                    <td>S3-C2-B1</td>
                                    <td>8</td>
                                    <td>W2-D1-C3</td>
                                    <td><span class="badge badge-warning">In Progress</span></td>
                                    <td><?php echo htmlspecialchars($userName); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Level Alerts -->
        <div class="col-lg-4 mb-4">
            <div class="theme-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bell"></i> Stock Alerts</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <strong>Critical Alert</strong>
                        <br>5 items below minimum stock level
                    </div>
                    <div class="alert alert-warning">
                        <strong>Low Stock Warning</strong>
                        <br>12 items approaching minimum
                    </div>
                    <div class="alert alert-info">
                        <strong>Scheduled Replenishment</strong>
                        <br>6 items due for routine restock
                    </div>

                    <div class="mt-3">
                        <h6>Quick Actions</h6>
                        <button class="btn btn-outline-primary btn-sm btn-block mb-2"
                            onclick="generateReplenishmentReport()">
                            <i class="fas fa-file-alt"></i> Generate Report
                        </button>
                        <button class="btn btn-outline-secondary btn-sm btn-block" onclick="exportLowStock()">
                            <i class="fas fa-download"></i> Export Low Stock
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle location scanning
    document.getElementById('shop-location-scan').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            checkLocation();
        }
    });

    // Handle warehouse scanning
    document.getElementById('warehouse-scan').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            completeReplenishment();
        }
    });

    // Handle form submission
    document.getElementById('replenishment-form').addEventListener('submit', function (e) {
        e.preventDefault();
        completeReplenishment();
    });

    function startReplenishment(sku) {
        // Auto-fill the form with item data
        const mockData = {
            'HAM001': { location: 'S1-A5-B2', stock: 2, warehouse: 'W1-D5-A3', suggested: 20 },
            'SCR015': { location: 'S1-B3-A1', stock: 1, warehouse: 'W1-C8-B1', suggested: 15 },
            'PNT201': { location: 'S2-C1-A3', stock: 5, warehouse: 'W2-A2-C2', suggested: 30 },
            'SAF105': { location: 'S3-A1-B2', stock: 3, warehouse: 'W1-E1-A1', suggested: 25 }
        };

        const data = mockData[sku];
        if (data) {
            document.getElementById('shop-location-scan').value = data.location;
            checkLocation();
        }
    }

    function checkLocation() {
        const location = document.getElementById('shop-location-scan').value;

        if (!location) {
            alert('Please scan or enter a shop location');
            return;
        }

        // Simulate location lookup
        const locationData = {
            location: location,
            item: 'Hardware Item',
            currentStock: Math.floor(Math.random() * 10) + 1,
            warehouseLocation: 'W1-' + String.fromCharCode(65 + Math.floor(Math.random() * 5)) +
                Math.floor(Math.random() * 10 + 1) + '-A' + Math.floor(Math.random() * 5 + 1),
            suggestedQuantity: Math.floor(Math.random() * 20) + 10
        };

        // Show location details
        document.getElementById('location-info').innerHTML = `
        <strong>Location:</strong> ${locationData.location}<br>
        <strong>Current Item:</strong> ${locationData.item}<br>
        <strong>Stock Level:</strong> ${locationData.currentStock} units
    `;

        document.getElementById('current-stock').value = locationData.currentStock;
        document.getElementById('warehouse-location').value = locationData.warehouseLocation;
        document.getElementById('replenish-quantity').value = locationData.suggestedQuantity;
        document.getElementById('location-details').classList.remove('d-none');
        document.getElementById('warehouse-scan').focus();
    }

    function completeReplenishment() {
        const shopLocation = document.getElementById('shop-location-scan').value;
        const warehouseLocation = document.getElementById('warehouse-scan').value;
        const quantity = document.getElementById('replenish-quantity').value;

        if (!shopLocation || !warehouseLocation || !quantity) {
            alert('Please complete all required fields');
            return;
        }

        // Process the replenishment
        console.log('Processing replenishment:', {
            shopLocation: shopLocation,
            warehouseLocation: warehouseLocation,
            quantity: quantity
        });

        // Clear form
        document.getElementById('replenishment-form').reset();
        document.getElementById('location-details').classList.add('d-none');
        document.getElementById('shop-location-scan').focus();

        alert('Replenishment completed successfully!');
    }

    function generateReplenishmentReport() {
        alert('Generating replenishment report...');
    }

    function exportLowStock() {
        alert('Exporting low stock items...');
    }

    // Auto-focus on scanner input
    document.getElementById('shop-location-scan').focus();
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>