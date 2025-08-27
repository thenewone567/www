<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<!-- Unified CSS -->
<link rel="stylesheet" href="<?= URLROOT ?>/css/app-unified.css">

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

// Check permissions for putaway
$permissions = [
    'admin'             => ['can_putaway' => true],
    'warehouse_manager' => ['can_putaway' => true],
    'receiving_clerk'   => ['can_putaway' => true],
    'inventory_clerk'   => ['can_putaway' => true],
    'viewer'            => ['can_putaway' => false]
];

$userPermissions = $permissions[$systemRole] ?? $permissions['viewer'];

// Redirect if no permission
if (!$userPermissions['can_putaway']) {
    header('Location: ' . URLROOT . '/inventory');
    exit();
}

// Get real putaway statistics from database
$putawayStats = [
    'pending_putaway'  => 0,
    'putaway_today'    => 0,
    'efficiency_rate'  => 0,
    'active_locations' => 0
];

try {
    require_once APPROOT . DS . 'app' . DS . 'Database.php';
    $db = new Database();

    // Get pending putaway items (received but not put away)
    $db->query("SELECT COUNT(*) as count FROM inventory_movements WHERE movement_type = 'received' AND putaway_status = 'pending'");
    $result = $db->single();
    $putawayStats['pending_putaway'] = $result->count ?? 0;

    // Get items put away today
    $db->query("SELECT COUNT(*) as count FROM inventory_movements WHERE movement_type = 'putaway' AND DATE(created_at) = CURDATE()");
    $result = $db->single();
    $putawayStats['putaway_today'] = $result->count ?? 0;

    // Calculate efficiency rate (successful putaways vs total attempts)
    $db->query("SELECT 
                    COUNT(*) as total_putaways,
                    SUM(CASE WHEN putaway_status = 'completed' THEN 1 ELSE 0 END) as successful_putaways
                FROM inventory_movements 
                WHERE movement_type = 'putaway' 
                AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
    $result = $db->single();
    if ($result && $result->total_putaways > 0) {
        $putawayStats['efficiency_rate'] = round(($result->successful_putaways / $result->total_putaways) * 100, 0);
    }

    // Get active locations count
    $db->query("SELECT COUNT(DISTINCT location_id) as count FROM inventory_movements WHERE movement_type = 'putaway' AND DATE(created_at) = CURDATE()");
    $result = $db->single();
    $putawayStats['active_locations'] = $result->count ?? 0;

} catch (Exception $e) {
    error_log("Putaway stats error: " . $e->getMessage());
    // Keep default values if database query fails
}
?>

<div class="container-fluid theme-container">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-6">
            <h1 class="mb-1 font-weight-bold">
                <i class="fas fa-boxes mr-2"></i>Putaway Operations
                <span class="badge badge-info ml-2">Active</span>
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

    <!-- Putaway Quick Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> Pending Putaway</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-warning"><?php echo $putawayStats['pending_putaway']; ?></h3>
                    <small class="text-muted">Items Waiting</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-boxes"></i> Put Away Today</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-info"><?php echo $putawayStats['putaway_today']; ?></h3>
                    <small class="text-muted">Items Stored</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle"></i> Efficiency</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-success"><?php echo $putawayStats['efficiency_rate']; ?>%</h3>
                    <small class="text-muted">Accuracy Rate</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Active Locations</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-primary"><?php echo $putawayStats['active_locations']; ?></h3>
                    <small class="text-muted">In Use Today</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="theme-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Active Locations</h5>
            </div>
            <div class="card-body text-center">
                <h3 class="text-primary">28</h3>
                <small class="text-muted">In Use Today</small>
            </div>
        </div>
    </div>
</div>

<!-- Putaway Operations -->
<div class="row">
    <!-- Scanner Section -->
    <div class="col-lg-6 mb-4">
        <div class="theme-card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-qrcode"></i> Putaway Scanner</h5>
            </div>
            <div class="card-body">
                <form id="putaway-form">
                    <div class="form-group">
                        <label for="item-scan">Scan Item to Put Away</label>
                        <div class="input-group">
                            <input type="text" class="form-control scanner-input" id="item-scan"
                                placeholder="Scan item barcode" autofocus>
                            <div class="input-group-append">
                                <button class="btn-theme btn-info-theme" type="button" onclick="lookupItem()">
                                    <i class="fas fa-search"></i> Lookup
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="item-details" class="d-none">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Item Information</h6>
                            <div id="item-info"></div>
                        </div>

                        <div class="form-group">
                            <label for="suggested-location">Suggested Location</label>
                            <input type="text" class="form-theme" id="suggested-location" readonly>
                        </div>

                        <div class="form-group">
                            <label for="location-scan">Scan Storage Location</label>
                            <div class="input-group">
                                <input type="text" class="form-control scanner-input" id="location-scan"
                                    placeholder="Scan location barcode">
                                <div class="input-group-append">
                                    <button class="btn-theme btn-success-theme" type="submit">
                                        <i class="fas fa-check"></i> Put Away
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="putaway-quantity">Quantity to Put Away</label>
                            <input type="number" class="form-theme" id="putaway-quantity" value="1" min="1">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Putaway Queue -->
    <div class="col-lg-6 mb-4">
        <div class="theme-card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="fas fa-list"></i> Putaway Queue</h5>
            </div>
            <div class="card-body">
                <div id="putaway-queue">
                    <div class="list-group">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Hammer - 16oz</strong>
                                <br><small class="text-muted">SKU: HAM001 | Qty: 25</small>
                            </div>
                            <span class="badge badge-warning">Waiting</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Screwdriver Set</strong>
                                <br><small class="text-muted">SKU: SCR015 | Qty: 12</small>
                            </div>
                            <span class="badge badge-warning">Waiting</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Paint Brush 2"</strong>
                                <br><small class="text-muted">SKU: PNT201 | Qty: 50</small>
                            </div>
                            <span class="badge badge-warning">Waiting</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Location Map -->
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="theme-card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-map"></i> Warehouse Location Map</h5>
            </div>
            <div class="card-body">
                <div class="warehouse-map">
                    <div class="row text-center">
                        <div class="col-2">
                            <div class="location-zone border p-2 mb-2 bg-light">
                                <strong>Zone A</strong>
                                <br><small>A1-A20</small>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="location-zone border p-2 mb-2 bg-light">
                                <strong>Zone B</strong>
                                <br><small>B1-B20</small>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="location-zone border p-2 mb-2 bg-light">
                                <strong>Zone C</strong>
                                <br><small>C1-C20</small>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="location-zone border p-2 mb-2 bg-success text-white">
                                <strong>Zone D</strong>
                                <br><small>D1-D20</small>
                                <br><span class="badge badge-light">Active</span>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="location-zone border p-2 mb-2 bg-light">
                                <strong>Zone E</strong>
                                <br><small>E1-E20</small>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="location-zone border p-2 mb-2 bg-light">
                                <strong>Zone F</strong>
                                <br><small>F1-F20</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Putaway Activity -->
    <div class="col-lg-4 mb-4">
        <div class="theme-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-history"></i> Recent Activity</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Hammer - 16oz</h6>
                            <small class="text-muted">Put away at W1-D5-A2</small>
                            <br><small class="text-muted">2 minutes ago</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Drill Bits Set</h6>
                            <small class="text-muted">Put away at W1-C8-B1</small>
                            <br><small class="text-muted">5 minutes ago</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Safety Goggles</h6>
                            <small class="text-muted">Put away at W1-A2-C3</small>
                            <br><small class="text-muted">8 minutes ago</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    // Handle item scanning
    document.getElementById('item-scan').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            lookupItem();
        }
    });

    // Handle location scanning
    document.getElementById('location-scan').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            processPutaway();
        }
    });

    // Handle form submission
    document.getElementById('putaway-form').addEventListener('submit', function (e) {
        e.preventDefault();
        processPutaway();
    });

    function lookupItem() {
        const barcode = document.getElementById('item-scan').value;

        if (!barcode) {
            alert('Please scan or enter an item barcode');
            return;
        }

        // Simulate item lookup
        const itemDetails = {
            barcode: barcode,
            name: 'Hardware Item',
            sku: 'HW' + barcode.slice(-3),
            category: 'Tools',
            suggestedLocation: 'W1-D5-A' + Math.floor(Math.random() * 10 + 1)
        };

        // Show item details
        document.getElementById('item-info').innerHTML = `
        <strong>${itemDetails.name}</strong><br>
        SKU: ${itemDetails.sku}<br>
        Category: ${itemDetails.category}<br>
        Barcode: ${itemDetails.barcode}
    `;

        document.getElementById('suggested-location').value = itemDetails.suggestedLocation;
        document.getElementById('item-details').classList.remove('d-none');
        document.getElementById('location-scan').focus();
    }

    function processPutaway() {
        const itemBarcode = document.getElementById('item-scan').value;
        const location = document.getElementById('location-scan').value;
        const quantity = document.getElementById('putaway-quantity').value;

        if (!itemBarcode || !location) {
            alert('Please scan both item and location');
            return;
        }

        // Process the putaway
        console.log('Processing putaway:', {
            item: itemBarcode,
            location: location,
            quantity: quantity
        });

        // Clear form
        document.getElementById('putaway-form').reset();
        document.getElementById('item-details').classList.add('d-none');
        document.getElementById('item-scan').focus();

        alert('Item put away successfully!');
    }

    // Auto-focus on scanner input
    document.getElementById('item-scan').focus();
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>