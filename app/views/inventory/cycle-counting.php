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

// Check permissions for cycle counting
$permissions = [
    'admin' => ['can_cycle_count' => true],
    'warehouse_manager' => ['can_cycle_count' => true],
    'receiving_clerk' => ['can_cycle_count' => false],
    'inventory_clerk' => ['can_cycle_count' => true],
    'viewer' => ['can_cycle_count' => false]
];

$userPermissions = $permissions[$systemRole] ?? $permissions['viewer'];

// Redirect if no permission
if (!$userPermissions['can_cycle_count']) {
    header('Location: ' . URLROOT . '/inventory');
    exit();
}

// Get real cycle counting statistics from database
$cycleCountStats = [
    'scheduled_counts' => 0,
    'completed_today' => 0,
    'discrepancies' => 0,
    'accuracy_rate' => 0
];

$recentCounts = [];
$discrepancyItems = [];

try {
    require_once APPROOT . DS . 'app' . DS . 'Database.php';
    $db = new Database();

    // Get scheduled counts this week
    $db->query("SELECT COUNT(*) as count FROM cycle_counts WHERE status = 'scheduled' AND DATE(scheduled_date) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)");
    $result = $db->single();
    $cycleCountStats['scheduled_counts'] = $result->count ?? 0;

    // Get completed counts today
    $db->query("SELECT COUNT(*) as count FROM cycle_counts WHERE status = 'completed' AND DATE(completed_at) = CURDATE()");
    $result = $db->single();
    $cycleCountStats['completed_today'] = $result->count ?? 0;

    // Get discrepancies (where counted_quantity != expected_quantity)
    $db->query("SELECT COUNT(*) as count FROM cycle_counts WHERE status = 'completed' AND counted_quantity != expected_quantity AND DATE(completed_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
    $result = $db->single();
    $cycleCountStats['discrepancies'] = $result->count ?? 0;

    // Calculate accuracy rate
    $db->query("SELECT 
                    COUNT(*) as total_counts,
                    SUM(CASE WHEN counted_quantity = expected_quantity THEN 1 ELSE 0 END) as accurate_counts
                FROM cycle_counts 
                WHERE status = 'completed' 
                AND DATE(completed_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    $result = $db->single();
    if ($result && $result->total_counts > 0) {
        $cycleCountStats['accuracy_rate'] = round(($result->accurate_counts / $result->total_counts) * 100, 1);
    }

    // Get recent counts
    $db->query("SELECT cc.*, p.name as product_name, l.name as location_name, u.display_name as counter_name 
                FROM cycle_counts cc 
                LEFT JOIN products p ON cc.product_id = p.id 
                LEFT JOIN locations l ON cc.location_id = l.id 
                LEFT JOIN users u ON cc.counted_by = u.id 
                WHERE DATE(cc.completed_at) = CURDATE() 
                ORDER BY cc.completed_at DESC 
                LIMIT 10");
    $recentCounts = $db->resultSet() ?? [];

    // Get discrepancy items
    $db->query("SELECT cc.*, p.name as product_name, l.name as location_name 
                FROM cycle_counts cc 
                LEFT JOIN products p ON cc.product_id = p.id 
                LEFT JOIN locations l ON cc.location_id = l.id 
                WHERE cc.status = 'completed' 
                AND cc.counted_quantity != cc.expected_quantity 
                AND DATE(cc.completed_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                ORDER BY cc.completed_at DESC 
                LIMIT 5");
    $discrepancyItems = $db->resultSet() ?? [];

} catch (Exception $e) {
    error_log("Cycle count stats error: " . $e->getMessage());
    // Keep default values if database query fails
}
?>

<!-- Theme System Styles -->
<div class="container-fluid theme-container">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-6">
            <h1 class="mb-1 font-weight-bold">
                <i class="fas fa-clipboard-check mr-2"></i>Cycle Counting Operations
                <span class="badge badge-primary ml-2">Active</span>
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

    <!-- Cycle Count Quick Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Scheduled Counts</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-primary"><?php echo $cycleCountStats['scheduled_counts']; ?></h3>
                    <small class="text-muted">Due This Week</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle"></i> Completed Today</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-success"><?php echo $cycleCountStats['completed_today']; ?></h3>
                    <small class="text-muted">Counts Finished</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Discrepancies</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-warning"><?php echo $cycleCountStats['discrepancies']; ?></h3>
                    <small class="text-muted">Need Investigation</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-percentage"></i> Accuracy Rate</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-info"><?php echo $cycleCountStats['accuracy_rate']; ?>%</h3>
                    <small class="text-muted">Last 30 Days</small>
                </div>
            </div>
        </div>
    </div>
    <div class="theme-card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-percentage"></i> Accuracy Rate</h5>
        </div>
        <div class="card-body text-center">
            <h3 class="text-info">96%</h3>
            <small class="text-muted">This Month</small>
        </div>
    </div>
</div>
</div>

<!-- Cycle Count Operations -->
<div class="row">
    <!-- Count Scanner -->
    <div class="col-lg-6 mb-4">
        <div class="theme-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-qrcode"></i> Cycle Count Scanner</h5>
            </div>
            <div class="card-body">
                <form id="cycle-count-form">
                    <div class="form-group">
                        <label for="location-scan">Scan Location to Count</label>
                        <div class="input-group">
                            <input type="text" class="form-control scanner-input" id="location-scan"
                                placeholder="Scan location barcode" autofocus>
                            <div class="input-group-append">
                                <button class="btn-theme btn-info-theme" type="button" onclick="loadLocation()">
                                    <i class="fas fa-search"></i> Load
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="location-items" class="d-none">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-map-marker-alt"></i> Location Information</h6>
                            <div id="location-info"></div>
                        </div>

                        <div class="form-group">
                            <label for="expected-items">Expected Items in Location</label>
                            <div id="expected-items" class="border p-3 bg-light">
                                <!-- Items will be loaded here -->
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="item-scan">Scan Items Found</label>
                            <div class="input-group">
                                <input type="text" class="form-control scanner-input" id="item-scan"
                                    placeholder="Scan item barcode">
                                <div class="input-group-append">
                                    <button class="btn-theme btn-success-theme" type="button" onclick="countItem()">
                                        <i class="fas fa-plus"></i> Count
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="physical-count">Physical Count</label>
                            <input type="number" class="form-theme" id="physical-count" value="1" min="0">
                        </div>
                    </div>
                </form>

                <div id="count-actions" class="d-none mt-3">
                    <button class="btn btn-primary mr-2" onclick="submitCount()">
                        <i class="fas fa-check"></i> Submit Count
                    </button>
                    <button class="btn-theme btn-secondary" onclick="resetCount()">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Count Progress -->
    <div class="col-lg-6 mb-4">
        <div class="theme-card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-list-check"></i> Current Count Progress</h5>
            </div>
            <div class="card-body">
                <div id="count-progress">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                        <p>No active count</p>
                        <small>Scan a location to start counting</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scheduled Counts & Discrepancies -->
<div class="row">
    <!-- Scheduled Counts -->
    <div class="col-lg-8 mb-4">
        <div class="theme-card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-calendar"></i> Scheduled Cycle Counts</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Location</th>
                                <th>Zone</th>
                                <th>Items</th>
                                <th>Due Date</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>W1-A5-B2</td>
                                <td>Zone A</td>
                                <td>12</td>
                                <td>Today</td>
                                <td><span class="badge badge-danger">High</span></td>
                                <td><span class="badge badge-warning">Pending</span></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="startLocationCount('W1-A5-B2')">
                                        <i class="fas fa-play"></i> Start
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>W1-B3-A1</td>
                                <td>Zone B</td>
                                <td>8</td>
                                <td>Today</td>
                                <td><span class="badge badge-warning">Medium</span></td>
                                <td><span class="badge badge-warning">Pending</span></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="startLocationCount('W1-B3-A1')">
                                        <i class="fas fa-play"></i> Start
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>W2-C1-A3</td>
                                <td>Zone C</td>
                                <td>15</td>
                                <td>Tomorrow</td>
                                <td><span class="badge badge-info">Low</span></td>
                                <td><span class="badge badge-secondary">Scheduled</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="startLocationCount('W2-C1-A3')">
                                        <i class="fas fa-calendar"></i> Schedule
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>W1-D2-B1</td>
                                <td>Zone D</td>
                                <td>9</td>
                                <td>Yesterday</td>
                                <td><span class="badge badge-danger">Overdue</span></td>
                                <td><span class="badge badge-danger">Overdue</span></td>
                                <td>
                                    <button class="btn btn-sm btn-danger" onclick="startLocationCount('W1-D2-B1')">
                                        <i class="fas fa-exclamation"></i> Urgent
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Discrepancies -->
    <div class="col-lg-4 mb-4">
        <div class="theme-card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Count Discrepancies</h5>
            </div>
            <div class="card-body">
                <div class="discrepancy-item border-bottom pb-2 mb-2">
                    <strong>Hammer - 16oz</strong>
                    <br><small class="text-muted">Location: W1-A5-B2</small>
                    <div class="mt-1">
                        <span class="badge badge-info">System: 25</span>
                        <span class="badge badge-warning">Counted: 23</span>
                    </div>
                    <button class="btn btn-sm btn-outline-warning mt-1" onclick="investigateDiscrepancy('HAM001')">
                        <i class="fas fa-search"></i> Investigate
                    </button>
                </div>

                <div class="discrepancy-item border-bottom pb-2 mb-2">
                    <strong>Screwdriver Set</strong>
                    <br><small class="text-muted">Location: W1-B3-A1</small>
                    <div class="mt-1">
                        <span class="badge badge-info">System: 15</span>
                        <span class="badge badge-warning">Counted: 18</span>
                    </div>
                    <button class="btn btn-sm btn-outline-warning mt-1" onclick="investigateDiscrepancy('SCR015')">
                        <i class="fas fa-search"></i> Investigate
                    </button>
                </div>

                <div class="discrepancy-item border-bottom pb-2 mb-2">
                    <strong>Safety Goggles</strong>
                    <br><small class="text-muted">Location: W2-A1-C2</small>
                    <div class="mt-1">
                        <span class="badge badge-info">System: 30</span>
                        <span class="badge badge-danger">Counted: 0</span>
                    </div>
                    <button class="btn btn-sm btn-outline-danger mt-1" onclick="investigateDiscrepancy('SAF105')">
                        <i class="fas fa-exclamation"></i> Critical
                    </button>
                </div>

                <div class="mt-3">
                    <button class="btn btn-outline-primary btn-sm btn-block" onclick="generateDiscrepancyReport()">
                        <i class="fas fa-file-alt"></i> Generate Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    let currentCount = {
        location: null,
        expectedItems: [],
        countedItems: []
    };

    // Handle location scanning
    document.getElementById('location-scan').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            loadLocation();
        }
    });

    // Handle item scanning
    document.getElementById('item-scan').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            countItem();
        }
    });

    function startLocationCount(location) {
        document.getElementById('location-scan').value = location;
        loadLocation();
    }

    function loadLocation() {
        const location = document.getElementById('location-scan').value;

        if (!location) {
            alert('Please scan or enter a location');
            return;
        }

        // Simulate location lookup
        const expectedItems = [
            { sku: 'HAM001', name: 'Hammer - 16oz', systemCount: 25 },
            { sku: 'SCR015', name: 'Screwdriver Set', systemCount: 15 },
            { sku: 'PNT201', name: 'Paint Brush 2"', systemCount: 50 }
        ];

        currentCount = {
            location: location,
            expectedItems: expectedItems,
            countedItems: []
        };

        // Show location details
        document.getElementById('location-info').innerHTML = `
        <strong>Location:</strong> ${location}<br>
        <strong>Zone:</strong> ${location.split('-')[1] || 'A'}<br>
        <strong>Expected Items:</strong> ${expectedItems.length}
    `;

        // Show expected items
        let expectedHtml = '';
        expectedItems.forEach(item => {
            expectedHtml += `
            <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                <div>
                    <strong>${item.name}</strong><br>
                    <small class="text-muted">SKU: ${item.sku}</small>
                </div>
                <span class="badge badge-info">Expected: ${item.systemCount}</span>
            </div>
        `;
        });
        document.getElementById('expected-items').innerHTML = expectedHtml;

        document.getElementById('location-items').classList.remove('d-none');
        document.getElementById('count-actions').classList.remove('d-none');
        document.getElementById('item-scan').focus();

        updateCountProgress();
    }

    function countItem() {
        const barcode = document.getElementById('item-scan').value;
        const physicalCount = parseInt(document.getElementById('physical-count').value);

        if (!barcode) {
            alert('Please scan an item');
            return;
        }

        // Find the item in expected items
        const expectedItem = currentCount.expectedItems.find(item =>
            item.sku === barcode || item.name.toLowerCase().includes(barcode.toLowerCase())
        );

        const countedItem = {
            barcode: barcode,
            name: expectedItem ? expectedItem.name : 'Unknown Item',
            sku: expectedItem ? expectedItem.sku : barcode,
            physicalCount: physicalCount,
            systemCount: expectedItem ? expectedItem.systemCount : 0,
            variance: expectedItem ? physicalCount - expectedItem.systemCount : physicalCount
        };

        // Check if item already counted
        const existingIndex = currentCount.countedItems.findIndex(item => item.sku === countedItem.sku);
        if (existingIndex >= 0) {
            currentCount.countedItems[existingIndex] = countedItem;
        } else {
            currentCount.countedItems.push(countedItem);
        }

        // Clear form for next item
        document.getElementById('item-scan').value = '';
        document.getElementById('physical-count').value = 1;
        document.getElementById('item-scan').focus();

        updateCountProgress();
    }

    function updateCountProgress() {
        const progressContainer = document.getElementById('count-progress');

        if (!currentCount.location) {
            progressContainer.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                <p>No active count</p>
                <small>Scan a location to start counting</small>
            </div>
        `;
            return;
        }

        let html = `
        <h6><i class="fas fa-map-marker-alt"></i> Counting: ${currentCount.location}</h6>
        <div class="progress mb-3">
            <div class="progress-bar" style="width: ${(currentCount.countedItems.length / Math.max(currentCount.expectedItems.length, 1)) * 100}%"></div>
        </div>
    `;

        if (currentCount.countedItems.length > 0) {
            html += '<div class="counted-items">';
            currentCount.countedItems.forEach(item => {
                const varianceClass = item.variance === 0 ? 'success' :
                    item.variance > 0 ? 'info' : 'warning';

                html += `
                <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                    <div>
                        <strong>${item.name}</strong><br>
                        <small class="text-muted">${item.sku}</small>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-${varianceClass}">Counted: ${item.physicalCount}</span>
                        ${item.variance !== 0 ? `<br><small class="text-${varianceClass}">Variance: ${item.variance > 0 ? '+' : ''}${item.variance}</small>` : ''}
                    </div>
                </div>
            `;
            });
            html += '</div>';
        } else {
            html += '<p class="text-muted">No items counted yet</p>';
        }

        progressContainer.innerHTML = html;
    }

    function submitCount() {
        if (!currentCount.location || currentCount.countedItems.length === 0) {
            alert('Please count at least one item');
            return;
        }

        if (confirm(`Submit count for location ${currentCount.location}?`)) {
            console.log('Submitting count:', currentCount);

            // Reset the count
            resetCount();
            alert('Count submitted successfully!');
        }
    }

    function resetCount() {
        currentCount = {
            location: null,
            expectedItems: [],
            countedItems: []
        };

        document.getElementById('cycle-count-form').reset();
        document.getElementById('location-items').classList.add('d-none');
        document.getElementById('count-actions').classList.add('d-none');
        document.getElementById('location-scan').focus();

        updateCountProgress();
    }

    function investigateDiscrepancy(sku) {
        alert(`Investigating discrepancy for item: ${sku}`);
    }

    function generateDiscrepancyReport() {
        alert('Generating discrepancy report...');
    }

    // Auto-focus on scanner input
    document.getElementById('location-scan').focus();
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>