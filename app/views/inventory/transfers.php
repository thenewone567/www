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

// Check permissions for transfers
$permissions = [
    'admin' => ['can_transfer' => true, 'can_approve' => true],
    'warehouse_manager' => ['can_transfer' => true, 'can_approve' => true],
    'receiving_clerk' => ['can_transfer' => false, 'can_approve' => false],
    'inventory_clerk' => ['can_transfer' => true, 'can_approve' => false],
    'viewer' => ['can_transfer' => false, 'can_approve' => false]
];

$userPermissions = $permissions[$systemRole] ?? $permissions['viewer'];

// Redirect if no permission
if (!$userPermissions['can_transfer']) {
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
                <i class="fas fa-exchange-alt mr-2"></i>Transfer Operations
                <span class="badge badge-secondary ml-2">Active</span>
            </h1>
            <small class="text-muted">
                Welcome, <?php echo htmlspecialchars($userName); ?> |
                Role: <?php echo ucwords(str_replace('_', ' ', $systemRole)); ?>
                <?php if ($userPermissions['can_approve']): ?>
                    | <span class="text-success">Approval Authority</span>
                <?php endif; ?>
            </small>
        </div>
        <div class="col-12 col-md-6 text-md-right mt-3 mt-md-0">
            <a href="<?php echo URLROOT; ?>/inventory" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Transfer Quick Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> Pending Approval</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-warning">12</h3>
                    <small class="text-muted">Transfers Waiting</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-truck"></i> In Transit</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-info">8</h3>
                    <small class="text-muted">Active Transfers</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle"></i> Completed Today</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-success">34</h3>
                    <small class="text-muted">Transfers Done</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="theme-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-percentage"></i> Success Rate</h5>
                </div>
                <div class="card-body text-center">
                    <h3 class="text-primary">98%</h3>
                    <small class="text-muted">This Week</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Operations -->
    <div class="row">
        <!-- Create Transfer -->
        <div class="col-lg-6 mb-4">
            <div class="theme-card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus"></i> Create New Transfer</h5>
                </div>
                <div class="card-body">
                    <form id="transfer-form">
                        <div class="form-group">
                            <label for="transfer-type">Transfer Type</label>
                            <select class="form-theme" id="transfer-type">
                                <option value="warehouse-to-shop">Warehouse to Shop</option>
                                <option value="shop-to-warehouse">Shop to Warehouse</option>
                                <option value="shop-to-shop">Shop to Shop</option>
                                <option value="warehouse-to-warehouse">Warehouse to Warehouse</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="from-location">From Location</label>
                            <div class="input-group">
                                <input type="text" class="form-theme" id="from-location"
                                    placeholder="Scan or enter source location">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="scanLocation('from')">
                                        <i class="fas fa-qrcode"></i> Scan
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="to-location">To Location</label>
                            <div class="input-group">
                                <input type="text" class="form-theme" id="to-location"
                                    placeholder="Scan or enter destination location">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="scanLocation('to')">
                                        <i class="fas fa-qrcode"></i> Scan
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="item-scan">Item to Transfer</label>
                            <div class="input-group">
                                <input type="text" class="form-theme" id="item-scan" placeholder="Scan item barcode">
                                <div class="input-group-append">
                                    <button class="btn-theme btn-info-theme" type="button"
                                        onclick="addItemToTransfer()">
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="transfer-quantity">Quantity</label>
                            <input type="number" class="form-theme" id="transfer-quantity" value="1" min="1">
                        </div>

                        <div class="form-group">
                            <label for="transfer-reason">Reason for Transfer</label>
                            <select class="form-theme" id="transfer-reason">
                                <option value="replenishment">Replenishment</option>
                                <option value="overstock">Overstock</option>
                                <option value="relocation">Relocation</option>
                                <option value="consolidation">Consolidation</option>
                                <option value="customer-request">Customer Request</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="transfer-notes">Notes (Optional)</label>
                            <textarea class="form-theme" id="transfer-notes" rows="2"
                                placeholder="Additional notes or instructions"></textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Transfer Items List -->
        <div class="col-lg-6 mb-4">
            <div class="theme-card">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Transfer Items</h5>
                    <button class="btn btn-light btn-sm" onclick="clearTransferItems()">
                        <i class="fas fa-trash"></i> Clear
                    </button>
                </div>
                <div class="card-body">
                    <div id="transfer-items-list">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-exchange-alt fa-3x mb-3"></i>
                            <p>No items added</p>
                            <small>Add items to create transfer</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary btn-block" onclick="createTransfer()">
                        <i class="fas fa-paper-plane"></i> Create Transfer Request
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Management Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-secondary text-white">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active text-white" data-toggle="tab" href="#pending-transfers">
                                <i class="fas fa-clock"></i> Pending Transfers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" data-toggle="tab" href="#active-transfers">
                                <i class="fas fa-truck"></i> Active Transfers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" data-toggle="tab" href="#completed-transfers">
                                <i class="fas fa-check"></i> Completed Transfers
                            </a>
                        </li>
                        <?php if ($userPermissions['can_approve']): ?>
                            <li class="nav-item">
                                <a class="nav-link text-white" data-toggle="tab" href="#approval-queue">
                                    <i class="fas fa-gavel"></i> Approval Queue
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Pending Transfers -->
                        <div class="tab-pane fade show active" id="pending-transfers">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Transfer ID</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Items</th>
                                            <th>Reason</th>
                                            <th>Created</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>TR-2025-001</td>
                                            <td>W1-A5-B2</td>
                                            <td>S1-A2-C1</td>
                                            <td>Hammer - 16oz (15 units)</td>
                                            <td>Replenishment</td>
                                            <td>2 hours ago</td>
                                            <td><span class="badge badge-warning">Pending</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-success"
                                                    onclick="executeTransfer('TR-2025-001')">
                                                    <i class="fas fa-play"></i> Execute
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>TR-2025-002</td>
                                            <td>S2-B1-A3</td>
                                            <td>W1-C8-B1</td>
                                            <td>Paint Brush 2" (25 units)</td>
                                            <td>Overstock</td>
                                            <td>1 hour ago</td>
                                            <td><span class="badge badge-warning">Pending</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-success"
                                                    onclick="executeTransfer('TR-2025-002')">
                                                    <i class="fas fa-play"></i> Execute
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Active Transfers -->
                        <div class="tab-pane fade" id="active-transfers">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Transfer ID</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Items</th>
                                            <th>Progress</th>
                                            <th>Started</th>
                                            <th>ETA</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>TR-2025-003</td>
                                            <td>W1-D2-A1</td>
                                            <td>S3-C1-B2</td>
                                            <td>Drill Bits Set (20 units)</td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar bg-info" style="width: 75%">75%</div>
                                                </div>
                                            </td>
                                            <td>30 mins ago</td>
                                            <td>10 mins</td>
                                            <td>
                                                <button class="btn btn-sm btn-info"
                                                    onclick="trackTransfer('TR-2025-003')">
                                                    <i class="fas fa-map-marker-alt"></i> Track
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Completed Transfers -->
                        <div class="tab-pane fade" id="completed-transfers">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Transfer ID</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Items</th>
                                            <th>Completed</th>
                                            <th>Duration</th>
                                            <th>Completed By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>TR-2025-004</td>
                                            <td>W2-A1-C2</td>
                                            <td>S1-B3-A1</td>
                                            <td>Safety Goggles (30 units)</td>
                                            <td>1 hour ago</td>
                                            <td>45 minutes</td>
                                            <td><?php echo htmlspecialchars($userName); ?></td>
                                        </tr>
                                        <tr>
                                            <td>TR-2025-005</td>
                                            <td>S2-C2-B1</td>
                                            <td>W1-E1-A1</td>
                                            <td>Extension Cord (8 units)</td>
                                            <td>2 hours ago</td>
                                            <td>32 minutes</td>
                                            <td><?php echo htmlspecialchars($userName); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <?php if ($userPermissions['can_approve']): ?>
                            <!-- Approval Queue -->
                            <div class="tab-pane fade" id="approval-queue">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Transfer ID</th>
                                                <th>Requested By</th>
                                                <th>From</th>
                                                <th>To</th>
                                                <th>Items</th>
                                                <th>Reason</th>
                                                <th>Requested</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>TR-2025-006</td>
                                                <td>John Smith</td>
                                                <td>W1-F1-A1</td>
                                                <td>S1-A1-A1</td>
                                                <td>Work Gloves (50 units)</td>
                                                <td>Customer Request</td>
                                                <td>30 mins ago</td>
                                                <td>
                                                    <button class="btn btn-sm btn-success mr-1"
                                                        onclick="approveTransfer('TR-2025-006')">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                    <button class="btn btn-sm btn-danger"
                                                        onclick="rejectTransfer('TR-2025-006')">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let transferItems = [];

    // Handle item scanning
    document.getElementById('item-scan').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            addItemToTransfer();
        }
    });

    function scanLocation(type) {
        const input = type === 'from' ? 'from-location' : 'to-location';
        const location = prompt(`Scan or enter ${type} location:`);
        if (location) {
            document.getElementById(input).value = location;
        }
    }

    function addItemToTransfer() {
        const barcode = document.getElementById('item-scan').value;
        const quantity = parseInt(document.getElementById('transfer-quantity').value);
        const fromLocation = document.getElementById('from-location').value;
        const toLocation = document.getElementById('to-location').value;

        if (!barcode || !fromLocation || !toLocation) {
            alert('Please fill in all required fields');
            return;
        }

        const item = {
            barcode: barcode,
            name: 'Item ' + barcode, // This would come from database lookup
            quantity: quantity,
            fromLocation: fromLocation,
            toLocation: toLocation
        };

        transferItems.push(item);
        updateTransferItemsList();

        // Clear item fields for next item
        document.getElementById('item-scan').value = '';
        document.getElementById('transfer-quantity').value = 1;
        document.getElementById('item-scan').focus();
    }

    function updateTransferItemsList() {
        const listContainer = document.getElementById('transfer-items-list');

        if (transferItems.length === 0) {
            listContainer.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="fas fa-exchange-alt fa-3x mb-3"></i>
                <p>No items added</p>
                <small>Add items to create transfer</small>
            </div>
        `;
            return;
        }

        let html = '';
        transferItems.forEach((item, index) => {
            html += `
            <div class="border-bottom pb-2 mb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${item.name}</strong>
                        <br><small class="text-muted">From: ${item.fromLocation} → To: ${item.toLocation}</small>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-secondary">Qty: ${item.quantity}</span>
                        <br><button class="btn btn-sm btn-outline-danger mt-1" onclick="removeTransferItem(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        });

        listContainer.innerHTML = html;
    }

    function removeTransferItem(index) {
        transferItems.splice(index, 1);
        updateTransferItemsList();
    }

    function clearTransferItems() {
        if (confirm('Clear all transfer items?')) {
            transferItems = [];
            updateTransferItemsList();
        }
    }

    function createTransfer() {
        if (transferItems.length === 0) {
            alert('Please add at least one item to transfer');
            return;
        }

        const transferData = {
            type: document.getElementById('transfer-type').value,
            reason: document.getElementById('transfer-reason').value,
            notes: document.getElementById('transfer-notes').value,
            items: transferItems,
            createdBy: '<?php echo htmlspecialchars($userName); ?>'
        };

        if (confirm(`Create transfer request with ${transferItems.length} item(s)?`)) {
            console.log('Creating transfer:', transferData);

            // Clear the form
            document.getElementById('transfer-form').reset();
            transferItems = [];
            updateTransferItemsList();

            alert('Transfer request created successfully!');
        }
    }

    function executeTransfer(transferId) {
        if (confirm(`Execute transfer ${transferId}?`)) {
            alert(`Transfer ${transferId} execution started!`);
        }
    }

    function trackTransfer(transferId) {
        alert(`Tracking transfer ${transferId}...`);
    }

    function approveTransfer(transferId) {
        if (confirm(`Approve transfer ${transferId}?`)) {
            alert(`Transfer ${transferId} approved!`);
        }
    }

    function rejectTransfer(transferId) {
        const reason = prompt('Reason for rejection:');
        if (reason) {
            alert(`Transfer ${transferId} rejected: ${reason}`);
        }
    }

    // Auto-focus on item scan input
    document.getElementById('item-scan').focus();
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>