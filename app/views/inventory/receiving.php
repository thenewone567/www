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

// Check permissions for receiving
$permissions = [
    'admin' => ['can_receive' => true],
    'warehouse_manager' => ['can_receive' => true],
    'receiving_clerk' => ['can_receive' => true],
    'inventory_clerk' => ['can_receive' => false],
    'viewer' => ['can_receive' => false]
];

$userPermissions = $permissions[$systemRole] ?? $permissions['viewer'];

// Redirect if no permission
if (!$userPermissions['can_receive']) {
    header('Location: ' . URLROOT . '/inventory');
    exit();
}

// Use data passed from controller
$receivingStats = $data['receivingStats'] ?? [
    'deliveries_today' => 0,
    'items_received_today' => 0,
    'pending_items' => 0,
    'completed_items' => 0
];

$recentActivity = $data['recentActivity'] ?? [];
?>

<!-- Enhanced Receiving Styles -->
<style>
    .received-items-container {
        min-height: 300px;
        max-height: 400px;
        overflow-y: auto;
    }

    .received-items-container .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 300px;
    }

    .scanner-input {
        font-family: 'Courier New', monospace;
        font-weight: bold;
    }

    .scanner-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        border-color: #28a745;
    }

    .item-row {
        transition: all 0.2s ease;
    }

    .item-row:hover {
        background-color: var(--bg-secondary);
    }

    .progress {
        background-color: var(--bg-tertiary);
    }

    .progress-bar {
        transition: width 0.3s ease;
    }

    .badge-complete {
        background-color: #28a745;
    }

    .badge-partial {
        background-color: #ffc107;
    }

    .badge-over {
        background-color: #dc3545;
    }

    .badge-unknown {
        background-color: #6c757d;
    }

    .quick-stats {
        font-size: 0.875rem;
    }

    .quick-stats .stat-value {
        font-size: 1.2rem;
        font-weight: bold;
    }

    .po-details-panel {
        border-left: 4px solid #007bff;
    }

    .condition-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 5px;
    }

    .condition-good .condition-indicator {
        background-color: #28a745;
    }

    .condition-damaged .condition-indicator {
        background-color: #ffc107;
    }

    .condition-defective .condition-indicator {
        background-color: #dc3545;
    }

    .item-preview {
        color: var(--text-muted);
        font-style: italic;
    }

    .receiving-actions .btn {
        margin: 2px;
    }

    .status-indicator {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }

        100% {
            opacity: 1;
        }
    }

    .table-sm td {
        padding: 0.5rem 0.25rem;
        vertical-align: middle;
    }

    .btn-group-sm .btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.8rem;
    }

    .modal-body .table-responsive {
        max-height: 400px;
        overflow-y: auto;
    }

    .summary-stats {
        background: var(--bg-secondary);
        border-radius: 0.25rem;
        padding: 0.5rem;
    }

    .summary-stats .stat-item {
        text-align: center;
        padding: 0.25rem;
    }

    .summary-stats .stat-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .summary-stats .stat-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .dropdown-menu {
        border: 1px solid var(--card-border);
        background-color: var(--card-bg);
    }

    .dropdown-item {
        color: var(--text-primary);
    }

    .dropdown-item:hover {
        background-color: var(--bg-secondary);
        color: var(--text-primary);
    }

    @media (max-width: 768px) {
        .received-items-container {
            max-height: 250px;
        }

        .btn-group-sm .btn {
            padding: 0.15rem 0.3rem;
            font-size: 0.75rem;
        }

        .table-sm td {
            padding: 0.3rem 0.15rem;
            font-size: 0.85rem;
        }
    }
</style>

<div class="container-fluid theme-container">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-6">
            <h1 class="mb-1 font-weight-bold">
                <i class="fas fa-dolly mr-2"></i>Receiving Operations
                <span class="badge badge-success ml-2">Active</span>
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

    <!-- Receiving Quick Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-truck"></i> Today's Deliveries</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-success"><?php echo $receivingStats['deliveries_today']; ?></h3>
                    <small class="text-muted">Deliveries Expected</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-dolly"></i> Items Received</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-info"><?php echo $receivingStats['items_received_today']; ?></h3>
                    <small class="text-muted">Items Today</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> Pending</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-warning"><?php echo $receivingStats['pending_items']; ?></h3>
                    <small class="text-muted">Items to Process</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle"></i> Completed</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-primary"><?php echo $receivingStats['completed_items']; ?></h3>
                    <small class="text-muted">Items Processed</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Receiving Operations -->
    <div class="row">
        <!-- Enhanced Scanner Section -->
        <div class="col-lg-6 mb-4">
            <div class="theme-card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-qrcode"></i> Receive Items</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-light" onclick="toggleExpectedItems()" data-toggle="tooltip"
                            title="View Expected Items">
                            <i class="fas fa-list-check"></i>
                        </button>
                        <button class="btn btn-outline-light" onclick="showReceivingHistory()" data-toggle="tooltip"
                            title="Receiving History">
                            <i class="fas fa-history"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- PO Selection with Enhanced Features -->
                    <div class="form-group">
                        <label for="po-number">Purchase Order Number</label>
                        <div class="input-group">
                            <input type="text" class="form-theme" id="po-number" placeholder="Enter or scan PO Number">
                            <div class="input-group-append">
                                <button class="btn-theme btn-primary-theme" onclick="loadPODetails()"
                                    data-toggle="tooltip" title="Load PO Details">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#" onclick="showPOBrowser()">
                                        <i class="fas fa-list mr-2"></i>Browse POs
                                    </a>
                                    <a class="dropdown-item" href="#" onclick="scanPOBarcode()">
                                        <i class="fas fa-qrcode mr-2"></i>Scan PO Barcode
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" onclick="createQuickReceiving()">
                                        <i class="fas fa-plus mr-2"></i>Quick Receiving
                                    </a>
                                </div>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            <span id="po-status-indicator"></span>
                        </small>
                    </div>

                    <!-- Enhanced PO Details Panel with Expected Items Form -->
                    <div id="po-details-panel" class="d-none">
                        <!-- PO Summary -->
                        <div class="alert alert-info mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <strong>Supplier:</strong> <span id="po-supplier">-</span><br>
                                    <strong>Expected Date:</strong> <span id="po-expected-date">-</span>
                                </div>
                                <div class="col-6">
                                    <strong>Total Items:</strong> <span id="po-total-items">-</span><br>
                                    <strong>Status:</strong> <span id="po-status-badge">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Expected Items Form -->
                        <div class="card-theme mb-3">
                            <div
                                class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-list-check"></i> Expected Items
                                </h6>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-light" onclick="fillAllExpected()"
                                        title="Fill All Expected Quantities">
                                        <i class="fas fa-fill"></i> Fill All
                                    </button>
                                    <button type="button" class="btn btn-outline-light" onclick="clearAllReceived()"
                                        title="Clear All Received Quantities">
                                        <i class="fas fa-eraser"></i> Clear All
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div id="expected-items-form" class="table-responsive">
                                    <table class="table table-sm mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="30%">Product</th>
                                                <th width="10%">SKU</th>
                                                <th width="10%">Expected</th>
                                                <th width="15%">Received Qty</th>
                                                <th width="15%">Condition</th>
                                                <th width="15%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="expected-items-tbody">
                                            <!-- Expected items will be populated here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-8">
                                        <button type="button" class="btn-theme btn-success-theme"
                                            onclick="processAllReceived()">
                                            <i class="fas fa-check"></i> Process All Received Items
                                        </button>
                                        <button type="button" class="btn btn-outline-info"
                                            onclick="saveReceivingDraft()">
                                            <i class="fas fa-save"></i> Save Draft
                                        </button>
                                    </div>
                                    <div class="col-4 text-right">
                                        <small class="text-muted">
                                            Progress: <span id="receiving-progress-text">0/0</span>
                                        </small>
                                        <div class="progress mt-1" style="height: 4px;">
                                            <div id="receiving-progress-bar" class="progress-bar bg-success"
                                                style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form id="receiving-form">
                        <!-- Enhanced Item Scanning -->
                        <div class="form-group">
                            <label for="item-scan">Item Identification</label>
                            <div class="input-group">
                                <input type="text" class="form-control scanner-input" id="item-scan"
                                    placeholder="Scan barcode, enter SKU, or search by name" autofocus>
                                <div class="input-group-append">
                                    <button class="btn-theme btn-success-theme" type="submit" data-toggle="tooltip"
                                        title="Add Item">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button class="btn btn-outline-primary" type="button" onclick="searchProducts()"
                                        data-toggle="tooltip" title="Search Products">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                <span id="item-preview"></span>
                            </small>
                        </div>

                        <!-- Enhanced Quantity and Condition -->
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="quantity">Quantity Received</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-outline-secondary" type="button"
                                                onclick="adjustQuantity(-1)">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                        <input type="number" class="form-control text-center" id="quantity" value="1"
                                            min="1">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button"
                                                onclick="adjustQuantity(1)">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">
                                        Expected: <span id="expected-quantity">-</span>
                                    </small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="condition">Item Condition</label>
                                    <select class="form-theme" id="condition">
                                        <option value="good">✓ Good Condition</option>
                                        <option value="damaged">⚠ Damaged</option>
                                        <option value="defective">✗ Defective</option>
                                        <option value="partial">📦 Partial Shipment</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Dock Location Selection -->
                        <div class="form-group">
                            <label for="dock_location">Dock Location *</label>
                            <select class="form-theme" id="dock_location" required>
                                <option value="">Select Dock/Receiving Location</option>
                                <!-- Dock locations will be loaded dynamically -->
                            </select>
                            <small class="form-text text-muted">
                                Select where the items are being received
                            </small>
                        </div>

                        <!-- Additional Options -->
                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea class="form-theme" id="notes" rows="2"
                                placeholder="Any notes about this item..."></textarea>
                        </div>

                        <!-- Quick Actions -->
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <button type="submit" class="btn-theme btn-success-theme">
                                <i class="fas fa-plus"></i> Add Item
                            </button>
                            <button type="button" class="btn-theme btn-warning-theme" onclick="markItemDamaged()">
                                <i class="fas fa-exclamation-triangle"></i> Mark Damaged
                            </button>
                            <button type="button" class="btn-theme btn-info-theme" onclick="setPartialReceive()">
                                <i class="fas fa-boxes"></i> Partial Receive
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Quick Stats Footer -->
                <div class="card-footer bg-light">
                    <div class="row text-center">
                        <div class="col-4">
                            <small class="text-muted">Items Scanned</small><br>
                            <strong id="items-scanned-count">0</strong>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Total Qty</small><br>
                            <strong id="total-quantity-count">0</strong>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Issues</small><br>
                            <strong id="issues-count" class="text-warning">0</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Received Items List -->
        <div class="col-lg-6 mb-4">
            <div class="theme-card">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Received Items</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-light" onclick="showExpectedVsReceived()" data-toggle="tooltip"
                            title="Compare Expected vs Received">
                            <i class="fas fa-balance-scale"></i>
                        </button>
                        <button class="btn btn-outline-light" onclick="exportReceivingList()" data-toggle="tooltip"
                            title="Export List">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="btn btn-light" onclick="clearReceivingList()">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Received Items Table -->
                    <div id="received-items-list" class="received-items-container">
                        <div class="empty-state text-center text-muted py-4">
                            <i class="fas fa-boxes fa-3x mb-3"></i>
                            <p class="mb-1">No items received yet</p>
                            <small>Scan items to start receiving</small>
                        </div>

                        <!-- Items will be populated here -->
                        <div id="received-items-table" class="d-none">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="25%">Item</th>
                                            <th width="10%">SKU</th>
                                            <th width="10%">Qty</th>
                                            <th width="15%">Expected</th>
                                            <th width="15%">Condition</th>
                                            <th width="10%">Status</th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="received-items-tbody">
                                        <!-- Items will be added here via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Footer with Multiple Actions -->
                <div class="card-footer">
                    <!-- Progress Bar -->
                    <div class="progress mb-3" style="height: 6px;">
                        <div id="receiving-progress" class="progress-bar bg-success" role="progressbar"
                            style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row">
                        <div class="col-6">
                            <button class="btn btn-success btn-block" onclick="processReceiving()" id="process-btn"
                                disabled>
                                <i class="fas fa-check"></i> Process All Items
                            </button>
                        </div>
                        <div class="col-6">
                            <div class="btn-group btn-group-sm w-100">
                                <button class="btn btn-outline-primary" onclick="saveAsDraft()" data-toggle="tooltip"
                                    title="Save as Draft">
                                    <i class="fas fa-save"></i>
                                </button>
                                <button class="btn btn-outline-warning" onclick="flagDiscrepancies()"
                                    data-toggle="tooltip" title="Flag Discrepancies">
                                    <i class="fas fa-flag"></i>
                                </button>
                                <button class="btn btn-outline-info" onclick="printReceivingSlip()"
                                    data-toggle="tooltip" title="Print Slip">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Stats -->
                    <div class="mt-3 pt-2 border-top">
                        <div class="row text-center small">
                            <div class="col-3">
                                <span class="text-muted">Items:</span><br>
                                <strong id="summary-items">0</strong>
                            </div>
                            <div class="col-3">
                                <span class="text-muted">Expected:</span><br>
                                <strong id="summary-expected">0</strong>
                            </div>
                            <div class="col-3">
                                <span class="text-muted">Received:</span><br>
                                <strong id="summary-received">0</strong>
                            </div>
                            <div class="col-3">
                                <span class="text-muted">Issues:</span><br>
                                <strong id="summary-issues" class="text-warning">0</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Recent Receiving Activity</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>PO Number</th>
                                    <th>Supplier</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Received By</th>
                                </tr>
                            </thead>
                            <tbody id="recent-activity">
                                <?php if (!empty($recentActivity)): ?>
                                    <?php foreach ($recentActivity as $activity): ?>
                                        <tr>
                                            <td><?php echo $activity->received_at ? date('g:i A', strtotime($activity->received_at)) : 'Pending'; ?>
                                            </td>
                                            <td><strong><?php echo htmlspecialchars($activity->po_number ?? 'N/A'); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($activity->supplier_name ?? 'Unknown Supplier'); ?>
                                            </td>
                                            <td>$<?php echo number_format($activity->total_amount ?? 0, 2); ?></td>
                                            <td>
                                                <?php
                                                $statusClass = match ($activity->status) {
                                                    'received' => 'success',
                                                    'completed' => 'success',
                                                    'ready_to_receive' => 'info',
                                                    'receiving_in_progress' => 'warning',
                                                    'partially_received' => 'warning',
                                                    default => 'secondary'
                                                };
                                                ?>
                                                <span
                                                    class="badge badge-<?php echo $statusClass; ?>"><?php echo ucwords(str_replace('_', ' ', $activity->status)); ?></span>
                                            </td>
                                            <td><?php echo htmlspecialchars($activity->receiver_name ?? 'System'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            <i class="fas fa-info-circle mr-2"></i>No receiving activity today
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let receivedItems = [];
    let currentPO = null;
    let expectedItems = [];
    let receivingSession = {
        startTime: null,
        itemsScanned: 0,
        totalQuantity: 0,
        issuesCount: 0
    };

    // Initialize tooltips
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
        receivingSession.startTime = new Date();
    });

    // Enhanced form submission handling
    document.getElementById('receiving-form').addEventListener('submit', function (e) {
        e.preventDefault();
        addReceivedItem();
    });

    // Auto-submit when scanning
    document.getElementById('item-scan').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addReceivedItem();
        }
    });

    // Real-time item preview
    document.getElementById('item-scan').addEventListener('input', function (e) {
        const value = e.target.value;
        if (value.length > 3) {
            previewItem(value);
        } else {
            document.getElementById('item-preview').innerHTML = '';
        }
    });

    // PO number change handler
    document.getElementById('po-number').addEventListener('change', function (e) {
        if (e.target.value) {
            loadPODetails();
        }
    });

    // Enhanced add received item function
    function addReceivedItem() {
        const poNumber = document.getElementById('po-number').value;
        const barcode = document.getElementById('item-scan').value;
        const quantity = parseInt(document.getElementById('quantity').value);
        const condition = document.getElementById('condition').value;
        const dockLocation = document.getElementById('dock_location').value;
        const notes = document.getElementById('notes').value;

        if (!barcode) {
            showAlert('Please scan or enter a barcode', 'warning');
            return;
        }

        if (!poNumber) {
            showAlert('Please enter a PO number first', 'warning');
            return;
        }

        if (!dockLocation) {
            showAlert('Please select a dock/receiving location', 'warning');
            return;
        }

        // Check if item already exists in received list
        const existingIndex = receivedItems.findIndex(item => item.barcode === barcode);

        if (existingIndex !== -1) {
            // Update existing item
            receivedItems[existingIndex].quantity += quantity;
            receivedItems[existingIndex].notes = notes;
            receivedItems[existingIndex].dockLocation = dockLocation;
            showAlert('Item quantity updated', 'success');
        } else {
            // Add new item
            const item = {
                id: generateItemId(),
                barcode: barcode,
                name: 'Item ' + barcode, // This would come from database lookup
                sku: barcode,
                quantity: quantity,
                expectedQuantity: getExpectedQuantity(barcode),
                condition: condition,
                dockLocation: dockLocation,
                poNumber: poNumber,
                notes: notes,
                timestamp: new Date().toLocaleTimeString(),
                status: getItemStatus(quantity, getExpectedQuantity(barcode))
            };

            receivedItems.push(item);
            receivingSession.itemsScanned++;
        }

        receivingSession.totalQuantity += quantity;
        if (condition !== 'good') {
            receivingSession.issuesCount++;
        }

        updateReceivedItemsList();
        updateStats();
        clearScanForm();
        focusScanInput();
    }

    // Load PO details and expected items
    function loadPODetails() {
        const poNumber = document.getElementById('po-number').value;
        if (!poNumber) return;

        document.getElementById('po-status-indicator').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading PO details...';

        // Real AJAX call to get PO details
        fetch(`inventory/getPODetails?po_number=${encodeURIComponent(poNumber)}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.po) {
                    currentPO = {
                        poNumber: poNumber,
                        supplier: data.po.supplier_name,
                        expectedDate: data.po.expected_date || data.po.order_date,
                        totalItems: data.po.total_items,
                        status: data.po.status
                    };

                    expectedItems = data.items.map(item => ({
                        id: item.purchase_item_id,
                        sku: item.sku,
                        name: item.product_name,
                        expectedQty: parseInt(item.expected_quantity),
                        receivedQty: 0,
                        condition: 'good'
                    }));

                    displayPODetails();
                    populateExpectedItemsForm();
                    document.getElementById('po-status-indicator').innerHTML = '<i class="fas fa-check text-success"></i> PO loaded successfully';
                } else {
                    document.getElementById('po-status-indicator').innerHTML = '<i class="fas fa-exclamation-triangle text-warning"></i> PO not found';
                    showAlert(data.message || 'Purchase Order not found', 'warning');
                }
            })
            .catch(error => {
                console.error('Error loading PO details:', error);
                document.getElementById('po-status-indicator').innerHTML = '<i class="fas fa-times text-danger"></i> Error loading PO';
                showAlert('Error loading PO details. Please try again.', 'danger');
            });
    }

    // Display PO details panel
    function displayPODetails() {
        document.getElementById('po-supplier').textContent = currentPO.supplier;
        document.getElementById('po-expected-date').textContent = currentPO.expectedDate;
        document.getElementById('po-total-items').textContent = currentPO.totalItems;
        document.getElementById('po-status-badge').innerHTML = `<span class="badge badge-info">${currentPO.status.replace('_', ' ')}</span>`;
        document.getElementById('po-details-panel').classList.remove('d-none');
    }

    // Populate the expected items form with received data
    function populateExpectedItemsForm() {
        const tbody = document.getElementById('expected-items-tbody');
        let html = '';

        expectedItems.forEach((item, index) => {
            html += `
                <tr data-item-id="${item.id}" data-index="${index}">
                    <td>${index + 1}</td>
                    <td>
                        <strong>${item.name}</strong>
                        <div class="small text-muted">ID: ${item.id}</div>
                    </td>
                    <td><code>${item.sku}</code></td>
                    <td class="text-center">
                        <span class="badge badge-secondary">${item.expectedQty}</span>
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="number" 
                                   class="form-control received-qty-input" 
                                   value="${item.receivedQty}" 
                                   min="0" 
                                   max="${item.expectedQty}"
                                   data-index="${index}"
                                   onchange="updateReceivedQuantity(${index}, this.value)"
                                   placeholder="0">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                        onclick="fillExpectedQuantity(${index})" 
                                        title="Fill Expected Quantity">
                                    <i class="fas fa-fill"></i>
                                </button>
                            </div>
                        </div>
                    </td>
                    <td>
                        <select class="form-control form-control-sm condition-select" 
                                data-index="${index}" 
                                onchange="updateItemCondition(${index}, this.value)">
                            <option value="good" ${item.condition === 'good' ? 'selected' : ''}>Good</option>
                            <option value="damaged" ${item.condition === 'damaged' ? 'selected' : ''}>Damaged</option>
                            <option value="defective" ${item.condition === 'defective' ? 'selected' : ''}>Defective</option>
                        </select>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-success btn-sm" 
                                    onclick="markItemAsReceived(${index})" 
                                    title="Mark as Received">
                                <i class="fas fa-check"></i>
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm" 
                                    onclick="reportIssue(${index})" 
                                    title="Report Issue">
                                <i class="fas fa-exclamation-triangle"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
        });

        tbody.innerHTML = html;
        updateReceivingProgress();
    }

    // Update received quantity for an item
    function updateReceivedQuantity(index, quantity) {
        quantity = parseInt(quantity) || 0;
        expectedItems[index].receivedQty = Math.min(quantity, expectedItems[index].expectedQty);
        updateReceivingProgress();

        // Update the input value in case it was clamped
        const input = document.querySelector(`input[data-index="${index}"]`);
        if (input) {
            input.value = expectedItems[index].receivedQty;
        }
    }

    // Update item condition
    function updateItemCondition(index, condition) {
        expectedItems[index].condition = condition;
    }

    // Fill expected quantity for a specific item
    function fillExpectedQuantity(index) {
        expectedItems[index].receivedQty = expectedItems[index].expectedQty;
        const input = document.querySelector(`input[data-index="${index}"]`);
        if (input) {
            input.value = expectedItems[index].receivedQty;
        }
        updateReceivingProgress();
    }

    // Fill all items with expected quantities
    function fillAllExpected() {
        expectedItems.forEach((item, index) => {
            item.receivedQty = item.expectedQty;
            const input = document.querySelector(`input[data-index="${index}"]`);
            if (input) {
                input.value = item.receivedQty;
            }
        });
        updateReceivingProgress();
        showAlert('All quantities filled with expected amounts', 'success');
    }

    // Clear all received quantities
    function clearAllReceived() {
        expectedItems.forEach((item, index) => {
            item.receivedQty = 0;
            const input = document.querySelector(`input[data-index="${index}"]`);
            if (input) {
                input.value = 0;
            }
        });
        updateReceivingProgress();
        showAlert('All received quantities cleared', 'info');
    }

    // Update receiving progress indicator
    function updateReceivingProgress() {
        const totalItems = expectedItems.length;
        const completedItems = expectedItems.filter(item => item.receivedQty > 0).length;
        const percentage = totalItems > 0 ? Math.round((completedItems / totalItems) * 100) : 0;

        document.getElementById('receiving-progress-text').textContent = `${completedItems}/${totalItems}`;
        document.getElementById('receiving-progress-bar').style.width = `${percentage}%`;
    }

    // Mark individual item as received
    function markItemAsReceived(index) {
        const item = expectedItems[index];
        if (item.receivedQty === 0) {
            showAlert('Please enter a received quantity first', 'warning');
            return;
        }

        // Process this individual item
        processReceivedItem(item)
            .then(() => {
                showAlert(`${item.name} marked as received (${item.receivedQty} units)`, 'success');
                // Optionally remove from list or mark as processed
                const row = document.querySelector(`tr[data-index="${index}"]`);
                if (row) {
                    row.classList.add('table-success');
                    row.style.opacity = '0.7';
                }
            })
            .catch(error => {
                showAlert('Error processing item: ' + error.message, 'danger');
            });
    }

    // Process all received items
    function processAllReceived() {
        const itemsToProcess = expectedItems.filter(item => item.receivedQty > 0);

        if (itemsToProcess.length === 0) {
            showAlert('No items have received quantities to process', 'warning');
            return;
        }

        if (confirm(`Process ${itemsToProcess.length} received items?`)) {
            // Before starting submission
            console.log("Initiating submission...");
            alert("Initiating submission...");

            Promise.all(itemsToProcess.map(item => processReceivedItem(item)))
                .then(() => {
                    // Upon successful completion
                    console.log("Submission successful!", `${itemsToProcess.length} items processed`);
                    alert(`Submission successful! ${itemsToProcess.length} items have been processed and added to inventory.`);

                    showAlert(`Successfully processed ${itemsToProcess.length} items`, 'success');
                    loadReceivingStats(); // Refresh stats
                    // Optionally clear the form or redirect
                })
                .catch(error => {
                    // For any errors
                    console.error("Submission failed! Details:", error.message);
                    alert(`Submission failed! Details: ${error.message}`);

                    showAlert('Error processing items: ' + error.message, 'danger');
                });
        }
    }

    // Process a single received item (API call)
    function processReceivedItem(item) {
        // Before starting submission
        console.log("Initiating submission...");
        alert("Initiating submission...");

        return fetch('inventory/receiveItem', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                purchase_item_id: item.id,
                received_quantity: item.receivedQty,
                condition: item.condition,
                dock_location: item.dockLocation,
                po_number: currentPO.poNumber
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.status !== 'success') {
                    throw new Error(data.message || 'Failed to process item');
                }

                // Upon successful completion
                console.log("Submission successful!", data);
                alert(`Submission successful! ${item.name} (${item.receivedQty} units) has been received.`);

                return data;
            })
            .catch(error => {
                // For any errors
                console.error("Submission failed! Details:", error.message);
                alert(`Submission failed! Details: ${error.message}`);
                throw error;
            });
    }

    // Report issue with an item
    function reportIssue(index) {
        const item = expectedItems[index];
        const issue = prompt(`Report issue with ${item.name}:`);
        if (issue) {
            // Here you could send the issue to a backend endpoint
            showAlert(`Issue reported for ${item.name}: ${issue}`, 'info');
        }
    }

    // Save receiving draft
    function saveReceivingDraft() {
        const draftData = {
            po_number: currentPO.poNumber,
            items: expectedItems.map(item => ({
                id: item.id,
                received_qty: item.receivedQty,
                condition: item.condition
            })),
            timestamp: new Date().toISOString()
        };

        localStorage.setItem(`receiving_draft_${currentPO.poNumber}`, JSON.stringify(draftData));
        showAlert('Receiving draft saved', 'success');
    }

    // Show expected items modal
    function showExpectedItems() {
        if (expectedItems.length === 0) {
            showAlert('No expected items loaded. Please load a PO first.', 'info');
            return;
        }

        let html = `
            <div class="modal fade" id="expectedItemsModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Expected Items - ${currentPO.poNumber}</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>SKU</th>
                                            <th>Item Name</th>
                                            <th>Expected</th>
                                            <th>Received</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

        expectedItems.forEach(item => {
            const status = getItemStatus(item.receivedQty, item.expectedQty);
            const statusClass = status === 'complete' ? 'success' : status === 'partial' ? 'warning' : 'secondary';

            html += `
                <tr>
                    <td>${item.sku}</td>
                    <td>${item.name}</td>
                    <td>${item.expectedQty}</td>
                    <td>${item.receivedQty}</td>
                    <td><span class="badge badge-${statusClass}">${status}</span></td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="quickReceiveItem('${item.sku}', ${item.expectedQty})">
                            <i class="fas fa-plus"></i> Receive
                        </button>
                    </td>
                </tr>`;
        });

        html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-theme btn-success-theme" onclick="startBulkReceiving()">
                                <i class="fas fa-boxes"></i> Bulk Receive All
                            </button>
                            <button type="button" class="btn-theme btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>`;

        // Remove existing modal if any
        $('#expectedItemsModal').remove();
        $('body').append(html);
        $('#expectedItemsModal').modal('show');
    }

    // Quick receive an expected item
    function quickReceiveItem(sku, quantity) {
        document.getElementById('item-scan').value = sku;
        document.getElementById('quantity').value = quantity;
        addReceivedItem();
        $('#expectedItemsModal').modal('hide');
    }

    // Enhanced update received items list
    function updateReceivedItemsList() {
        const container = document.getElementById('received-items-list');
        const emptyState = container.querySelector('.empty-state');
        const tableContainer = document.getElementById('received-items-table');
        const tbody = document.getElementById('received-items-tbody');

        if (receivedItems.length === 0) {
            emptyState.classList.remove('d-none');
            tableContainer.classList.add('d-none');
            document.getElementById('process-btn').disabled = true;
            return;
        }

        emptyState.classList.add('d-none');
        tableContainer.classList.remove('d-none');
        document.getElementById('process-btn').disabled = false;

        tbody.innerHTML = '';
        receivedItems.forEach((item, index) => {
            const row = createItemRow(item, index + 1);
            tbody.appendChild(row);
        });

        updateReceivingProgress();
    }

    // Create item row for the table
    function createItemRow(item, index) {
        const row = document.createElement('tr');
        const statusClass = item.status === 'complete' ? 'success' : item.status === 'partial' ? 'warning' : item.status === 'over' ? 'danger' : 'secondary';
        const conditionIcon = item.condition === 'good' ? 'check' : item.condition === 'damaged' ? 'exclamation-triangle' : 'times';
        const conditionClass = item.condition === 'good' ? 'success' : item.condition === 'damaged' ? 'warning' : 'danger';

        row.innerHTML = `
            <td>${index}</td>
            <td>
                <div class="font-weight-bold">${item.name}</div>
                ${item.notes ? `<small class="text-muted">${item.notes}</small>` : ''}
            </td>
            <td><code>${item.sku}</code></td>
            <td><strong>${item.quantity}</strong></td>
            <td>
                <span class="text-muted">${item.expectedQuantity || '-'}</span>
                ${item.expectedQuantity ? `<br><small class="text-${statusClass}">${item.status}</small>` : ''}
            </td>
            <td>
                <i class="fas fa-${conditionIcon} text-${conditionClass}"></i>
                ${item.condition}
            </td>
            <td><span class="badge badge-${statusClass}">${item.status}</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary btn-sm" onclick="editItem(${index - 1})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm" onclick="removeItem(${index - 1})" title="Remove">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;

        return row;
    }

    // Utility functions
    function generateItemId() {
        return Date.now() + Math.random().toString(36).substr(2, 9);
    }

    function getExpectedQuantity(sku) {
        const expected = expectedItems.find(item => item.sku === sku);
        return expected ? expected.expectedQty : null;
    }

    function getItemStatus(received, expected) {
        if (!expected) return 'unknown';
        if (received === expected) return 'complete';
        if (received < expected) return 'partial';
        if (received > expected) return 'over';
        return 'pending';
    }

    function updateStats() {
        document.getElementById('items-scanned-count').textContent = receivingSession.itemsScanned;
        document.getElementById('total-quantity-count').textContent = receivingSession.totalQuantity;
        document.getElementById('issues-count').textContent = receivingSession.issuesCount;

        // Update summary stats
        document.getElementById('summary-items').textContent = receivedItems.length;
        document.getElementById('summary-received').textContent = receivingSession.totalQuantity;
        document.getElementById('summary-issues').textContent = receivingSession.issuesCount;

        const expectedTotal = expectedItems.reduce((sum, item) => sum + item.expectedQty, 0);
        document.getElementById('summary-expected').textContent = expectedTotal || '-';
    }

    function updateReceivingProgress() {
        if (expectedItems.length === 0) return;

        const totalExpected = expectedItems.reduce((sum, item) => sum + item.expectedQty, 0);
        const totalReceived = receivingSession.totalQuantity;
        const progress = Math.min((totalReceived / totalExpected) * 100, 100);

        document.getElementById('receiving-progress').style.width = progress + '%';
        document.getElementById('receiving-progress').setAttribute('aria-valuenow', progress);
    }

    function clearScanForm() {
        document.getElementById('item-scan').value = '';
        document.getElementById('quantity').value = '1';
        document.getElementById('condition').value = 'good';
        document.getElementById('notes').value = '';
        document.getElementById('item-preview').innerHTML = '';
    }

    function focusScanInput() {
        document.getElementById('item-scan').focus();
    }

    function showAlert(message, type = 'info') {
        // Simple alert for now - could be enhanced with toast notifications
        const alertClass = type === 'success' ? 'alert-success' : type === 'warning' ? 'alert-warning' : type === 'danger' ? 'alert-danger' : 'alert-info';
        console.log(`${type.toUpperCase()}: ${message}`);
    }

    // Quantity adjustment buttons
    function adjustQuantity(delta) {
        const input = document.getElementById('quantity');
        const newValue = Math.max(1, parseInt(input.value) + delta);
        input.value = newValue;
    }

    // Enhanced action functions
    function markItemDamaged() {
        document.getElementById('condition').value = 'damaged';
        addReceivedItem();
    }

    function setPartialReceive() {
        const currentQty = parseInt(document.getElementById('quantity').value);
        const partialQty = Math.ceil(currentQty / 2);
        document.getElementById('quantity').value = partialQty;
        document.getElementById('condition').value = 'partial';
        addReceivedItem();
    }

    function previewItem(barcode) {
        // Mock preview - replace with actual lookup
        document.getElementById('item-preview').innerHTML = `<i class="fas fa-search text-muted"></i> Searching for "${barcode}"...`;

        setTimeout(() => {
            document.getElementById('item-preview').innerHTML = `<i class="fas fa-box text-primary"></i> Found: Sample Item (SKU: ${barcode})`;
        }, 500);
    }

    function searchProducts() {
        const searchTerm = document.getElementById('item-scan').value;
        showAlert(`Searching for products matching: "${searchTerm}"`, 'info');
        // Implement product search modal
    }

    // Additional interactive functions
    function toggleExpectedItems() {
        showExpectedItems();
    }

    function showReceivingHistory() {
        showAlert('Opening receiving history...', 'info');
        // Implement receiving history modal
    }

    function startBulkReceiving() {
        if (expectedItems.length === 0) {
            showAlert('No expected items loaded', 'warning');
            return;
        }

        expectedItems.forEach(item => {
            if (item.receivedQty < item.expectedQty) {
                quickReceiveItem(item.sku, item.expectedQty - item.receivedQty);
            }
        });

        showAlert('Bulk receiving completed for all expected items', 'success');
    }

    function showExpectedVsReceived() {
        showAlert('Opening expected vs received comparison...', 'info');
        // Implement comparison modal
    }

    function exportReceivingList() {
        showAlert('Exporting receiving list...', 'info');
        // Implement export functionality
    }

    function saveAsDraft() {
        showAlert('Saving receiving session as draft...', 'info');
        // Implement draft save
    }

    function flagDiscrepancies() {
        const discrepancies = receivedItems.filter(item => item.status === 'over' || item.status === 'partial');
        showAlert(`Found ${discrepancies.length} discrepancies`, discrepancies.length > 0 ? 'warning' : 'success');
    }

    function printReceivingSlip() {
        showAlert('Generating receiving slip...', 'info');
        // Implement print functionality
    }

    function editItem(index) {
        const item = receivedItems[index];
        // Populate form with item data for editing
        document.getElementById('item-scan').value = item.barcode;
        document.getElementById('quantity').value = item.quantity;
        document.getElementById('condition').value = item.condition;
        document.getElementById('notes').value = item.notes || '';

        // Remove item from list so it can be re-added with new values
        removeItem(index);
    }

    function removeItem(index) {
        const item = receivedItems[index];
        receivingSession.totalQuantity -= item.quantity;
        if (item.condition !== 'good') {
            receivingSession.issuesCount--;
        }
        receivingSession.itemsScanned--;

        receivedItems.splice(index, 1);
        updateReceivedItemsList();
        updateStats();
    }

    function clearReceivingList() {
        if (receivedItems.length === 0) return;

        if (confirm('Are you sure you want to clear all received items?')) {
            receivedItems = [];
            receivingSession = {
                startTime: new Date(),
                itemsScanned: 0,
                totalQuantity: 0,
                issuesCount: 0
            };
            updateReceivedItemsList();
            updateStats();
            showAlert('Receiving list cleared', 'info');
        }
    }

    function processReceiving() {
        if (receivedItems.length === 0) {
            showAlert('No items to process', 'warning');
            return;
        }

        const hasIssues = receivedItems.some(item => item.condition !== 'good' || item.status === 'over');

        if (hasIssues && !confirm('Some items have issues. Do you want to continue processing?')) {
            return;
        }

        showAlert('Processing all received items...', 'info');

        // Mock processing - replace with actual submission
        setTimeout(() => {
            showAlert('All items processed successfully!', 'success');
            // Reset the form
            clearReceivingList();
            document.getElementById('po-number').value = '';
            document.getElementById('po-details-panel').classList.add('d-none');
            currentPO = null;
            expectedItems = [];
        }, 2000);
    }

    // Clear form for next item
    document.getElementById('item-scan').value = '';
    document.getElementById('quantity').value = 1;
    document.getElementById('item-scan').focus();
    }

    function updateReceivedItemsList() {
        const listContainer = document.getElementById('received-items-list');

        if (receivedItems.length === 0) {
            listContainer.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="fas fa-boxes fa-3x mb-3"></i>
                <p>No items received yet</p>
                <small>Scan items to start receiving</small>
            </div>
        `;
            return;
        }

        let html = '';
        receivedItems.forEach((item, index) => {
            const conditionClass = item.condition === 'good' ? 'success' :
                item.condition === 'damaged' ? 'warning' : 'danger';

            html += `
            <div class="border-bottom pb-2 mb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${item.name}</strong>
                        <br><small class="text-muted">Barcode: ${item.barcode}</small>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-${conditionClass}">${item.condition}</span>
                        <br><small>Qty: ${item.quantity}</small>
                    </div>
                </div>
            </div>
        `;
        });

        listContainer.innerHTML = html;
    }

    function clearReceivingList() {
        if (confirm('Clear all received items?')) {
            receivedItems = [];
            updateReceivedItemsList();
        }
    }

    function processReceiving() {
        if (receivedItems.length === 0) {
            alert('No items to process');
            return;
        }

        if (confirm(`Process ${receivedItems.length} received items?`)) {
            // Here you would send data to backend
            console.log('Processing received items:', receivedItems);

            // Clear the list
            receivedItems = [];
            updateReceivedItemsList();

            alert('Items processed successfully!');
        }
    }

    // Auto-focus on scanner input
    document.getElementById('item-scan').focus();

    // Load dock locations when page loads
    loadDockLocations();

    // Function to load dock and receiving locations
    function loadDockLocations() {
        fetch('inventory/getDockLocations')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('dock_location');
                select.innerHTML = '<option value="">Select Dock/Receiving Location</option>';

                if (data.success && data.locations) {
                    data.locations.forEach(location => {
                        const option = document.createElement('option');
                        option.value = location.location_code;
                        option.textContent = `${location.location_code} - ${location.location_name}`;
                        select.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading dock locations:', error);
            });
    }
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>