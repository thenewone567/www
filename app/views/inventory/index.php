<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<?php
// User Role & Permission System
$currentUser = $_SESSION['user'] ?? null;
$userRole = $_SESSION['user_role'] ?? 'Associate';
$roleId = $_SESSION['role_id'] ?? 4;
$userId = $_SESSION['user_id'] ?? 0;
$userName = $_SESSION['user_name'] ?? $_SESSION['display_name'] ?? 'Guest User';

// Map role IDs to system roles (primary mapping)
$roleIdMapping = [
    1 => 'admin',
    2 => 'warehouse_manager',
    3 => 'receiving_clerk',
    4 => 'inventory_clerk',
    5 => 'viewer'
];

// Map role names to system roles (fallback)
$roleMapping = [
    'admin' => 'admin',
    'Administrator' => 'admin',
    'warehouse_manager' => 'warehouse_manager',
    'Warehouse Manager' => 'warehouse_manager',
    'receiving_clerk' => 'receiving_clerk',
    'Receiving Clerk' => 'receiving_clerk',
    'inventory_clerk' => 'inventory_clerk',
    'Inventory Clerk' => 'inventory_clerk',
    'Associate' => 'viewer',
    'viewer' => 'viewer'
];

// Use role_id first, then fall back to role name
$systemRole = $roleIdMapping[$roleId] ?? $roleMapping[$userRole] ?? 'viewer';

// Define role-based permissions
$permissions = [
    'admin' => [
        'can_receive' => true,
        'can_putaway' => true,
        'can_replenish' => true,
        'can_cycle_count' => true,
        'can_transfer' => true,
        'can_view_all_locations' => true,
        'can_manage_locations' => true,
        'can_approve_transfers' => true,
        'can_override_counts' => true
    ],
    'warehouse_manager' => [
        'can_receive' => true,
        'can_putaway' => true,
        'can_replenish' => true,
        'can_cycle_count' => true,
        'can_transfer' => true,
        'can_view_all_locations' => true,
        'can_manage_locations' => false,
        'can_approve_transfers' => true,
        'can_override_counts' => false
    ],
    'receiving_clerk' => [
        'can_receive' => true,
        'can_putaway' => true,
        'can_replenish' => false,
        'can_cycle_count' => false,
        'can_transfer' => false,
        'can_view_all_locations' => false,
        'can_manage_locations' => false,
        'can_approve_transfers' => false,
        'can_override_counts' => false
    ],
    'inventory_clerk' => [
        'can_receive' => false,
        'can_putaway' => true,
        'can_replenish' => true,
        'can_cycle_count' => true,
        'can_transfer' => true,
        'can_view_all_locations' => true,
        'can_manage_locations' => false,
        'can_approve_transfers' => false,
        'can_override_counts' => false
    ],
    'viewer' => [
        'can_receive' => false,
        'can_putaway' => false,
        'can_replenish' => false,
        'can_cycle_count' => false,
        'can_transfer' => false,
        'can_view_all_locations' => true,
        'can_manage_locations' => false,
        'can_approve_transfers' => false,
        'can_override_counts' => false
    ]
];

$userPermissions = $permissions[$systemRole] ?? $permissions['viewer'];

// Get user's assigned zones/areas for location-based restrictions
$assignedZones = [];
if (!$userPermissions['can_view_all_locations']) {
    try {
        $db = new Database();
        $db->query("SELECT DISTINCT zone FROM user_location_assignments WHERE user_id = :user_id");
        $db->bind(':user_id', $userId);
        $db->execute();
        $zoneResults = $db->resultSet();
        $assignedZones = array_column($zoneResults, 'zone');
    } catch (Exception $e) {
        $assignedZones = ['W1', 'W2']; // Default zones for receiving clerks
    }
}

// Define available workflows based on user permissions
$availableWorkflows = [];
if ($userPermissions['can_receive']) {
    $availableWorkflows['receiving'] = [
        'title' => 'Receiving',
        'subtitle' => 'Process Incoming',
        'icon' => 'fas fa-dolly',
        'url' => URLROOT . '/inventory/receiving'
    ];
}
if ($userPermissions['can_putaway']) {
    $availableWorkflows['putaway'] = [
        'title' => 'Putaway',
        'subtitle' => 'Stock Items',
        'icon' => 'fas fa-boxes',
        'url' => URLROOT . '/inventory/putaway'
    ];
}
if ($userPermissions['can_replenish']) {
    $availableWorkflows['replenishment'] = [
        'title' => 'Replenishment',
        'subtitle' => 'Restock Items',
        'icon' => 'fas fa-arrow-up',
        'url' => URLROOT . '/inventory/replenishment'
    ];
}
if ($userPermissions['can_cycle_count']) {
    $availableWorkflows['cycle-counting'] = [
        'title' => 'Cycle Counting',
        'subtitle' => 'Audit Inventory',
        'icon' => 'fas fa-clipboard-check',
        'url' => URLROOT . '/inventory/cycle-counting'
    ];
}
if ($userPermissions['can_transfer']) {
    $availableWorkflows['transfers'] = [
        'title' => 'Transfers',
        'subtitle' => 'Move Items',
        'icon' => 'fas fa-exchange-alt',
        'url' => URLROOT . '/inventory/transfers'
    ];
}

// Set the first available workflow for tab navigation
$firstWorkflow = !empty($availableWorkflows) ? array_keys($availableWorkflows)[0] : null;
?>

<style>
    .workflow-action-btn {
        text-decoration: none !important;
        transition: all 0.3s ease;
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 20px 15px;
        height: auto;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .workflow-action-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        text-decoration: none !important;
    }

    .workflow-action-btn i {
        transition: all 0.3s ease;
    }

    .workflow-action-btn:hover i {
        transform: scale(1.1);
    }

    .workflow-action-btn strong {
        font-size: 1.1rem;
        margin-bottom: 5px;
    }

    .workflow-action-btn small {
        font-size: 0.85rem;
        opacity: 0.8;
    }
</style>

<div class="container-fluid theme-container">
    <!-- Dynamic Role-Based Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-6">
            <h1 class="mb-1 font-weight-bold">
                <i class="fas fa-warehouse mr-2"></i>Inventory Management
                <span
                    class="badge badge-<?php echo $systemRole === 'admin' ? 'danger' : ($systemRole === 'warehouse_manager' ? 'warning' : 'info'); ?> ml-2">
                    <?php echo ucwords(str_replace('_', ' ', $systemRole)); ?>
                </span>
            </h1>
            <small class="text-muted">
                Welcome, <?php echo htmlspecialchars($userName); ?> |
                <?php if (!empty($assignedZones)): ?>
                    Assigned Zones: <?php echo implode(', ', $assignedZones); ?>
                <?php else: ?>
                    All Warehouse Access
                <?php endif; ?>
            </small>
        </div>
    </div>

    <!-- User Activity Tracking -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-theme">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <small class="text-muted">
                                <i class="fas fa-clock mr-1"></i>
                                Last Activity: <span id="last-activity-time">Loading...</span> |
                                <i class="fas fa-user mr-1"></i>
                                Session: <?php echo date('M d, Y H:i', strtotime($_SESSION['login_time'] ?? 'now')); ?>
                            </small>
                        </div>
                        <div class="col-md-4 text-md-right">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-secondary btn-sm" onclick="refreshUserActivity()"
                                    data-toggle="tooltip" title="Refresh Activity">
                                    <i class="fas fa-sync"></i>
                                </button>
                                <button class="btn btn-outline-info btn-sm" onclick="showUserPreferences()"
                                    data-toggle="tooltip" title="User Preferences">
                                    <i class="fas fa-cog"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Workflow Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="row">
                <?php if ($userPermissions['can_receive']): ?>
                    <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
                        <a href="<?php echo URLROOT; ?>/inventory/receiving"
                            class="btn btn-success btn-lg btn-block workflow-action-btn">
                            <i class="fas fa-dolly d-block mb-2" style="font-size: 2rem;"></i>
                            <strong>Receiving</strong>
                            <small class="d-block">Process Incoming</small>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ($userPermissions['can_putaway']): ?>
                    <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
                        <a href="<?php echo URLROOT; ?>/inventory/putaway"
                            class="btn btn-info btn-lg btn-block workflow-action-btn">
                            <i class="fas fa-boxes d-block mb-2" style="font-size: 2rem;"></i>
                            <strong>Putaway</strong>
                            <small class="d-block">Stock Items</small>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ($userPermissions['can_replenish']): ?>
                    <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
                        <a href="<?php echo URLROOT; ?>/inventory/replenishment"
                            class="btn btn-warning btn-lg btn-block workflow-action-btn">
                            <i class="fas fa-arrow-up d-block mb-2" style="font-size: 2rem;"></i>
                            <strong>Replenishment</strong>
                            <small class="d-block">Restock Items</small>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ($userPermissions['can_cycle_count']): ?>
                    <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
                        <a href="<?php echo URLROOT; ?>/inventory/cycle-counting"
                            class="btn btn-primary btn-lg btn-block workflow-action-btn">
                            <i class="fas fa-clipboard-check d-block mb-2" style="font-size: 2rem;"></i>
                            <strong>Cycle Count</strong>
                            <small class="d-block">Audit Inventory</small>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ($userPermissions['can_transfer']): ?>
                    <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
                        <a href="<?php echo URLROOT; ?>/inventory/transfers"
                            class="btn btn-secondary btn-lg btn-block workflow-action-btn">
                            <i class="fas fa-exchange-alt d-block mb-2" style="font-size: 2rem;"></i>
                            <strong>Transfers</strong>
                            <small class="d-block">Move Items</small>
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Locations Button -->
                <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
                    <a href="<?php echo URLROOT; ?>/inventory/locations"
                        class="btn btn-info btn-lg btn-block workflow-action-btn">
                        <i class="fas fa-map-marker-alt d-block mb-2" style="font-size: 2rem;"></i>
                        <strong>Locations</strong>
                        <small class="d-block">Manage Warehouse</small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Role-Based Performance Indicators (KPI) -->
    <div class="row mb-4">
        <?php
        // Get role-specific statistics
        try {
            $db = new Database();

            // Base query conditions based on user access
            $locationCondition = "";
            $bindParams = [];

            if (!$userPermissions['can_view_all_locations'] && !empty($assignedZones)) {
                $placeholders = str_repeat('?,', count($assignedZones) - 1) . '?';
                $locationCondition = " AND section IN ($placeholders)";
                $bindParams = $assignedZones;
            }

            // Get location statistics based on user access
            if ($userPermissions['can_view_all_locations']) {
                // Admin/Manager view - all locations
                $db->query("SELECT COUNT(*) as count FROM locations WHERE location_type = 'bin'");
                $db->execute();
                $shopLocations = $db->single()->count ?? 0;

                $db->query("SELECT COUNT(*) as count FROM locations WHERE location_type = 'storage'");
                $db->execute();
                $warehouseLocations = $db->single()->count ?? 0;
            } else {
                // Restricted view - only assigned zones
                $db->query("SELECT COUNT(*) as count FROM locations WHERE location_type = 'storage' $locationCondition");
                foreach ($bindParams as $i => $param) {
                    $db->bind($i + 1, $param);
                }
                $db->execute();
                $warehouseLocations = $db->single()->count ?? 0;
                $shopLocations = 0; // Clerks typically don't manage shop locations
            }

            // Get role-specific metrics
            if ($systemRole === 'admin' || $systemRole === 'warehouse_manager') {
                // Admin/Manager comprehensive metrics
                $db->query("SELECT COUNT(*) as count FROM inventory_movements 
                           WHERE movement_type = 'received' 
                           AND DATE(created_at) = CURDATE()");
                $db->execute();
                $todayReceived = $db->single()->count ?? 0;

                $db->query("SELECT COUNT(*) as count FROM inventory_movements 
                           WHERE movement_type = 'putaway' 
                           AND DATE(created_at) = CURDATE()");
                $db->execute();
                $todayPutaway = $db->single()->count ?? 0;

                $db->query("SELECT COUNT(*) as count FROM inventory_movements 
                           WHERE movement_type = 'cycle_count' 
                           AND DATE(created_at) = CURDATE()");
                $db->execute();
                $todayCounts = $db->single()->count ?? 0;

                $db->query("SELECT COUNT(*) as count FROM inventory_movements 
                           WHERE movement_type = 'transfer' 
                           AND DATE(created_at) = CURDATE()");
                $db->execute();
                $todayTransfers = $db->single()->count ?? 0;

            } elseif ($userRole === 'receiving_clerk') {
                // Focus on receiving metrics
                $db->query("SELECT COUNT(*) as count FROM inventory_movements 
                           WHERE movement_type = 'received' 
                           AND created_by = :user_id 
                           AND DATE(created_at) = CURDATE()");
                $db->bind(':user_id', $userId);
                $db->execute();
                $todayReceived = $db->single()->count ?? 0;

                $db->query("SELECT COUNT(*) as count FROM inventory_movements 
                           WHERE movement_type = 'putaway' 
                           AND created_by = :user_id 
                           AND DATE(created_at) = CURDATE()");
                $db->bind(':user_id', $userId);
                $db->execute();
                $todayPutaway = $db->single()->count ?? 0;

            } elseif ($userRole === 'inventory_clerk') {
                // Focus on receiving metrics
                $db->query("SELECT COUNT(*) as count FROM inventory_movements 
                           WHERE movement_type = 'received' 
                           AND created_by = :user_id 
                           AND DATE(created_at) = CURDATE()");
                $db->bind(':user_id', $userId);
                $db->execute();
                $todayReceived = $db->single()->count ?? 0;

                $db->query("SELECT COUNT(*) as count FROM inventory_movements 
                           WHERE movement_type = 'putaway' 
                           AND created_by = :user_id 
                           AND DATE(created_at) = CURDATE()");
                $db->bind(':user_id', $userId);
                $db->execute();
                $todayPutaway = $db->single()->count ?? 0;

            } elseif ($userRole === 'inventory_clerk') {
                // Focus on inventory management
                $db->query("SELECT COUNT(*) as count FROM inventory_movements 
                           WHERE movement_type = 'cycle_count' 
                           AND created_by = :user_id 
                           AND DATE(created_at) = CURDATE()");
                $db->bind(':user_id', $userId);
                $db->execute();
                $todayCounts = $db->single()->count ?? 0;

                $db->query("SELECT COUNT(*) as count FROM inventory_movements 
                           WHERE movement_type = 'transfer' 
                           AND created_by = :user_id 
                           AND DATE(created_at) = CURDATE()");
                $db->bind(':user_id', $userId);
                $db->execute();
                $todayTransfers = $db->single()->count ?? 0;
            }

        } catch (Exception $e) {
            // Fallback values
            $shopLocations = 750;
            $warehouseLocations = 15000;
            $todayReceived = 0;
            $todayPutaway = 0;
            $todayCounts = 0;
            $todayTransfers = 0;
        }
        ?>

        <?php if ($userPermissions['can_view_all_locations']): ?>
            <!-- Admin/Manager Comprehensive KPIs -->
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="theme-card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-dolly"></i> Today's Receiving</h5>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="text-success"><?php echo number_format($todayReceived ?? 0); ?></h3>
                        <small class="text-muted">Items Received Today</small>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="theme-card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-boxes"></i> Today's Putaway</h5>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="text-info"><?php echo number_format($todayPutaway ?? 0); ?></h3>
                        <small class="text-muted">Items Put Away Today</small>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="theme-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-clipboard-check"></i> Today's Counts</h5>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="text-primary"><?php echo number_format($todayCounts ?? 0); ?></h3>
                        <small class="text-muted">Cycle Counts Completed</small>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="theme-card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="fas fa-exchange-alt"></i> Today's Transfers</h5>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="text-warning"><?php echo number_format($todayTransfers ?? 0); ?></h3>
                        <small class="text-muted">Transfers Completed</small>
                    </div>
                </div>
            </div>

            <!-- Additional Location Stats for Admin -->
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="theme-card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-store"></i> Shop Locations</h5>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="text-secondary"><?php echo number_format($shopLocations); ?></h3>
                        <small class="text-muted">S1-A1-A1 to S3-E10-A5</small>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="theme-card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-warehouse"></i> Warehouse Locations</h5>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="text-dark"><?php echo number_format($warehouseLocations); ?></h3>
                        <small class="text-muted">W1-A1-A1 to W5-J20-C5</small>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($userRole === 'receiving_clerk'): ?>
            <!-- Receiving Clerk Specific KPIs -->
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="theme-card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-dolly"></i> Today's Receiving</h5>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="text-success"><?php echo number_format($todayReceived); ?></h3>
                        <small class="text-muted">Items Received Today</small>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="theme-card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-boxes"></i> Today's Putaway</h5>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="text-info"><?php echo number_format($todayPutaway); ?></h3>
                        <small class="text-muted">Items Put Away Today</small>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="theme-card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="fas fa-clock"></i> Assigned Zones</h5>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="text-warning"><?php echo count($assignedZones); ?></h3>
                        <small class="text-muted"><?php echo implode(', ', $assignedZones); ?></small>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($userRole === 'inventory_clerk'): ?>
            <!-- Inventory Clerk Specific KPIs -->
            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="theme-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-clipboard-check"></i> Today's Counts</h5>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="text-primary"><?php echo number_format($todayCounts); ?></h3>
                        <small class="text-muted">Cycle Counts Completed</small>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                <div class="theme-card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-exchange-alt"></i> Today's Transfers</h5>
                    </div>
                    <div class="card-body text-center">
                        <h3 class="text-secondary"><?php echo number_format($todayTransfers); ?></h3>
                        <small class="text-muted">Location Transfers</small>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Performance Score (All Roles) -->
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-trophy"></i> Performance Score</h5>
                </div>
                <div class="card-body text-center">
                    <?php
                    // Calculate performance score based on role
                    $performanceScore = 0;
                    if ($userRole === 'receiving_clerk') {
                        $performanceScore = min(100, ($todayReceived + $todayPutaway) * 10);
                    } elseif ($userRole === 'inventory_clerk') {
                        $performanceScore = min(100, ($todayCounts + $todayTransfers) * 15);
                    } else {
                        $performanceScore = 95; // Default for managers/admins
                    }

                    $scoreColor = $performanceScore >= 80 ? 'success' : ($performanceScore >= 60 ? 'warning' : 'danger');
                    ?>
                    <h3 class="text-<?php echo $scoreColor; ?>"><?php echo $performanceScore; ?>%</h3>
                    <small class="text-muted">Today's Efficiency</small>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>