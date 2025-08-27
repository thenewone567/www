<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/app-unified.css">


<script>
    // Open the receipt view for the PO entered in the Quick Receive field
    // accepts an optional PO argument (openQuickReceiveReceipt('PO-123'))
    function openQuickReceiveReceipt(poArg) {
        const po = (typeof poArg === 'string' && poArg.trim() !== '' ? poArg.trim() : ($('#poSearchInputMain').val() || '').trim());
        if (!po) {
            alert('Please enter a PO number to print its receipt.');
            return;
        }

        // Build base URL using PHP-provided URLROOT if available, otherwise derive from location
        let baseUrl = '';
        <?php if (defined('URLROOT') && preg_match('#^https?://#i', URLROOT)): ?>
            baseUrl = '<?php echo rtrim(URLROOT, '/'); ?>';
        <?php else: ?>
            baseUrl = window.location.protocol + '//' + window.location.host + '<?php echo isset($_SERVER['SCRIPT_NAME']) ? rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') : ''; ?>';
        <?php endif; ?>

        const url = baseUrl + '/purchases/viewReceipt/' + encodeURIComponent(po);
        const w = window.open(url, '_blank');
        try { if (w) { w.focus(); } } catch (e) { }
    }
</script>
<style>
    /* Custom CSS for Enhanced Quick Receive UI */
    .step-item {
        text-align: center;
        flex: 1;
    }

    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e9ecef;
        border: 2px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-weight: bold;
        color: #6c757d;
        transition: all 0.3s ease;
    }

    .step-item.active .step-circle {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
        transform: scale(1.1);
    }

    .step-item.completed .step-circle {
        background-color: #20c997;
        border-color: #20c997;
        color: white;
    }

    .step-line {
        height: 2px;
        background-color: #dee2e6;
        flex: 1;
        margin: 20px 10px 0;
    }

    .step-line.active {
        background-color: #28a745;
    }

    .step-label {
        color: #6c757d;
        font-weight: 500;
    }

    .step-item.active .step-label {
        color: #28a745;
        font-weight: bold;
    }

    .gap-2>* {
        margin-right: 0.5rem !important;
    }

    .input-group-lg .form-control {
        border-radius: 0.375rem;
    }

    .card:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }

    .bg-opacity-20 {
        background-color: rgba(255, 255, 255, 0.2) !important;
    }

    .border-primary:focus {
        border-color: #007bff !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    }

    .border-success:focus {
        border-color: #28a745 !important;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
    }

    .border-warning:focus {
        border-color: #ffc107 !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25) !important;
    }

    .text-white-50 {
        color: rgba(255, 255, 255, 0.5) !important;
    }

    /* Animation for search button */
    #searchButton:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    /* Loading animation */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* KPI card styles moved to unified CSS: public/css/app-unified.css */

    /* Success animation */
    .success-checkmark {
        animation: checkmark 0.6s ease-in-out;
    }

    @keyframes checkmark {
        0% {
            transform: scale(0);
        }

        50% {
            transform: scale(1.2);
        }

        100% {
            transform: scale(1);
        }
    }

    /* Fix for dropdown text visibility */
    /* Form control fixes - enhanced by global CSS */
    .form-control,
    .form-control-sm,
    select.form-control,
    select.form-control-sm {
        /* Global CSS handles the main fixes, just ensure consistency */
        box-sizing: border-box !important;
    }

    /* Ensure dropdown options are visible */
    select option {
        padding: 8px 12px;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .step-item {
            margin-bottom: 20px;
        }

        .step-line {
            display: none;
        }

        .row>.col-md-4 {
            margin-bottom: 15px;
        }
    }

    /* Workflow Styling */
    .border-left-primary {
        border-left: 4px solid #4e73df !important;
    }

    .workflow-steps .list-group-item {
        border: none;
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .workflow-steps .list-group-item:hover {
        border-left-color: #4e73df;
        background-color: #f8f9fc;
    }

    .badge {
        min-width: 25px;
        min-height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .workflow-modal .modal-content {
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
</style>

<div class="container-fluid theme-container theme-unified">
    <!-- Page Header with Primary Actions -->
    <div class="theme-header">
        <div class="row align-items-center mb-4">
            <div class="col-12 col-lg-8">
                <h1 class="mb-0">
                    <i class="fas fa-shopping-cart"></i>
                    Purchase Management
                </h1>
                <p class="description">Create purchase orders, manage suppliers, and track inventory</p>
            </div>
            <div class="col-12 col-lg-4 text-lg-right mt-3 mt-lg-0">
                <a href="<?php echo URLROOT; ?>/purchases/add" class="btn btn-success btn-lg mr-2">
                    <i class="fas fa-plus"></i> New Purchase Order
                </a>
                <a href="<?php echo URLROOT; ?>/purchases/history" class="btn btn-info btn-lg mr-2">
                    <i class="fas fa-history"></i> View History
                </a>
                <a href="<?php echo URLROOT; ?>/purchases/quick" class="btn btn-primary btn-lg mr-2">
                    <i class="fas fa-bolt"></i> Quick Order
                </a>

            </div>
        </div>
    </div>

    <!-- KPI Cards for Purchase Order Status -->
    <div class="row mb-3">
        <div class="col-3 mb-2">
            <div class="kpi-card kpi-gradient-warning shadow-sm h-100">
                <div class="kpi-body">
                    <div class="kpi-count"><?php echo $data['summary']['pending_count'] ?? 0; ?></div>
                    <div class="kpi-value small">Pending •
                        ₹<?php echo number_format($data['summary']['pending'] ?? 0, 0); ?></div>
                    <div class="kpi-small-spark" aria-hidden="true"></div>
                    <i class="fas fa-clock kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>
        <div class="col-3 mb-2">
            <div class="kpi-card kpi-gradient-info shadow-sm h-100">
                <div class="kpi-body">
                    <div class="kpi-count"><?php echo $data['summary']['sent_count'] ?? 0; ?></div>
                    <div class="kpi-value small">Sent • ₹<?php echo number_format($data['summary']['sent'] ?? 0, 0); ?>
                    </div>
                    <div class="kpi-small-spark" aria-hidden="true"></div>
                    <i class="fas fa-paper-plane kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>
        <div class="col-3 mb-2">
            <div class="kpi-card kpi-gradient-success shadow-sm h-100">
                <div class="kpi-body">
                    <div class="kpi-count"><?php echo $data['summary']['received_count'] ?? 0; ?></div>
                    <div class="kpi-value small">Received •
                        ₹<?php echo number_format($data['summary']['received'] ?? 0, 0); ?></div>
                    <div class="kpi-small-spark" aria-hidden="true"></div>
                    <i class="fas fa-check-circle kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>
        <div class="col-3 mb-2">
            <div class="kpi-card kpi-gradient-primary shadow-sm h-100">
                <div class="kpi-body">
                    <div class="kpi-count"><?php echo $data['summary']['in_transit_count'] ?? 0; ?></div>
                    <div class="kpi-value small">In Transit •
                        ₹<?php echo number_format($data['summary']['in_transit'] ?? 0, 0); ?></div>
                    <div class="kpi-small-spark" aria-hidden="true"></div>
                    <i class="fas fa-truck kpi-icon" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Receive Purchase Order Section - COMPACT UI -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white py-2">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="fas fa-shipping-fast mr-2"></i>
                                Quick Receive Purchase Order
                            </h6>
                        </div>
                        <div class="col-auto">
                            <span class="badge badge-light">
                                <i class="fas fa-clock mr-1"></i>3sec avg
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body py-3">
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Main Input Section -->
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-success text-white border-success">
                                        <i class="fas fa-search"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control border-success" id="poSearchInputMain"
                                    placeholder="Enter PO Number (e.g., PO-2024-005)" autocomplete="off">
                                <div class="input-group-append">
                                    <button class="btn btn-success" type="button" onclick="searchPOMain()"
                                        id="searchButton">
                                        <i class="fas fa-search mr-1"></i>Find & Process
                                    </button>
                                </div>
                            </div>

                            <!-- Quick Actions - Compact -->
                            <div class="mb-2">
                                <button class="btn btn-outline-secondary btn-sm mr-1" onclick="scanBarcode()">
                                    <i class="fas fa-qrcode mr-1"></i>Scan
                                </button>
                                <button class="btn btn-outline-info btn-sm mr-1" onclick="showRecentPOs()">
                                    <i class="fas fa-history mr-1"></i>Recent
                                </button>
                                <button class="btn btn-outline-warning btn-sm" onclick="bulkReceive()">
                                    <i class="fas fa-list mr-1"></i>Bulk
                                </button>
                            </div>

                            <!-- Location Assignment Panel - Compact -->
                            <div id="locationSelectionMain" class="border rounded p-2 bg-light mb-2"
                                style="display: none;">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label class="small text-muted mb-1">Dock Location</label>
                                        <select class="form-control" id="dockSelectMain"
                                            style="height: auto; min-height: 38px; line-height: 1.5;">
                                            <option value="">Select Dock...</option>
                                            <option value="17">🚛 Dock 1</option>
                                            <option value="18">🚛 Dock 2</option>
                                            <option value="19">🚛 Dock 3</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="small text-muted mb-1">Receiving Area</label>
                                        <select class="form-control" id="receivingAreaSelectMain"
                                            style="height: auto; min-height: 38px; line-height: 1.5;">
                                            <option value="">Select Area...</option>
                                            <option value="20">📦 Area 1</option>
                                            <option value="21">📦 Area 2</option>
                                            <option value="22">📦 Area 3</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="small text-muted mb-1">Notes</label>
                                        <input type="text" class="form-control" id="notesInputMain"
                                            placeholder="Optional notes" style="height: auto; min-height: 38px;">
                                    </div>
                                </div>
                            </div>

                            <!-- Results Display -->
                            <div id="searchResultsMain">
                                <div class="alert alert-light border-success mb-0 py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle text-success mr-2"></i>
                                        <small class="mb-0 text-muted">Enter a PO number above to search and receive
                                            purchase orders</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side Compact Info -->
                        <div class="col-lg-4">
                            <div class="text-center bg-light rounded p-3 h-100">
                                <i class="fas fa-truck-loading fa-2x text-success mb-2"></i>
                                <h6 class="text-dark mb-1">Lightning Fast</h6>
                                <p class="text-muted small mb-2">Receive POs in seconds</p>
                                <div class="row text-center small">
                                    <div class="col-6">
                                        <strong class="text-success">99%</strong>
                                        <br><small class="text-muted">Success</small>
                                    </div>
                                    <div class="col-6">
                                        <strong class="text-primary">3sec</strong>
                                        <br><small class="text-muted">Avg Time</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receiving Workflow Explanation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info border-left-primary">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="alert-heading mb-2">
                            <i class="fas fa-info-circle mr-2"></i>Receiving Workflow Clarification
                        </h6>
                        <p class="mb-2 small">
                            <strong>Important:</strong> We have separate processes for dock operations and receiving
                            area operations:
                        </p>
                        <div class="row small">
                            <div class="col-md-6">
                                <strong class="text-primary">Dock Operations:</strong>
                                <ul class="list-unstyled mb-0 ml-3">
                                    <li><i class="fas fa-truck text-warning"></i> PO arrives at dock</li>
                                    <li><i class="fas fa-map-marker-alt text-info"></i> Dock assignment</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <strong class="text-success">Receiving Area:</strong>
                                <ul class="list-unstyled mb-0 ml-3">
                                    <li><i class="fas fa-warehouse text-primary"></i> Products transferred to receiving
                                    </li>
                                    <li><i class="fas fa-check-circle text-success"></i> Individual product processing
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="btn-group-vertical">
                            <a href="<?php echo URLROOT; ?>/inventory/receiving" class="btn btn-primary btn-sm">
                                <i class="fas fa-warehouse mr-1"></i>Go to Receiving Area
                            </a>
                            <button class="btn btn-outline-secondary btn-sm mt-1" onclick="showWorkflowDetails()">
                                <i class="fas fa-question-circle mr-1"></i>View Full Workflow
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Purchase Orders -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="kpi-card">
                <div class="card-header bg-primary-theme text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-list"></i> Recent Purchase Orders</h6>
                    <div class="btn-group btn-group-sm">
                        <a href="<?php echo URLROOT; ?>/purchases/history" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-history mr-1"></i>View All
                        </a>
                        <button class="btn btn-outline-light btn-sm" onclick="refreshPurchaseOrders()"
                            data-toggle="tooltip" title="Refresh Purchase Orders">
                            <i class="fas fa-sync"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="activeOrdersTable">
                            <thead>
                                <tr>
                                    <th>PO Number</th>
                                    <th>Supplier</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Expected</th>
                                    <th>Tracking</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Get purchase orders data (uses 'orders' for backward compatibility)
                                $purchaseOrders = isset($data['orders']) && is_array($data['orders']) ? $data['orders'] : [];
                                if (!empty($purchaseOrders)):
                                    ?>
                                    <?php foreach (array_slice($purchaseOrders, 0, 10) as $purchaseOrder): ?>
                                        <?php if (is_object($purchaseOrder)): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($purchaseOrder->po_number ?? $purchaseOrder->purchase_id ?? 'N/A'); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($purchaseOrder->supplier_name ?? 'N/A'); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($purchaseOrder->purchase_date ?? 'now')); ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $status = strtolower($purchaseOrder->status ?? '');
                                                    $statusClass = '';
                                                    $statusDisplay = '';

                                                    switch ($status) {
                                                        case 'pending':
                                                            $statusClass = 'badge-warning';
                                                            $statusDisplay = 'Pending';
                                                            break;
                                                        case 'sent':
                                                            $statusClass = 'badge-info';
                                                            $statusDisplay = 'Sent to Supplier';
                                                            break;
                                                        case 'in_transit':
                                                            $statusClass = 'badge-primary';
                                                            $statusDisplay = 'In Transit';
                                                            break;
                                                        case 'arrived_at_dock':
                                                            $statusClass = 'badge-warning';
                                                            $statusDisplay = 'Arrived at Dock';
                                                            break;
                                                        case 'dock_assigned':
                                                            $statusClass = 'badge-info';
                                                            $statusDisplay = 'Dock Assigned';
                                                            break;
                                                        case 'ready_to_receive':
                                                            $statusClass = 'badge-primary';
                                                            $statusDisplay = 'Ready for Receiving';
                                                            break;
                                                        case 'receiving_in_progress':
                                                            $statusClass = 'badge-warning';
                                                            $statusDisplay = 'Receiving in Progress';
                                                            break;
                                                        case 'partially_received':
                                                            $statusClass = 'badge-info';
                                                            $statusDisplay = 'Partially Received';
                                                            break;
                                                        case 'received':
                                                            $statusClass = 'badge-success';
                                                            $statusDisplay = 'Fully Received';
                                                            break;
                                                        case 'completed':
                                                            $statusClass = 'badge-success';
                                                            $statusDisplay = 'Completed';
                                                            break;
                                                        case 'cancelled':
                                                            $statusClass = 'badge-danger';
                                                            $statusDisplay = 'Cancelled';
                                                            break;
                                                        default:
                                                            $statusClass = 'badge-secondary';
                                                            $statusDisplay = ucfirst(str_replace('_', ' ', $status));
                                                    }

                                                    // Add workflow stage indicator
                                                    $workflowStage = '';
                                                    if (in_array($status, ['arrived_at_dock', 'dock_assigned'])) {
                                                        $workflowStage = '<br><small class="text-muted"><i class="fas fa-truck"></i> Dock Phase</small>';
                                                    } elseif (in_array($status, ['ready_to_receive', 'receiving_in_progress', 'partially_received'])) {
                                                        $workflowStage = '<br><small class="text-primary"><i class="fas fa-warehouse"></i> Receiving Phase</small>';
                                                    } elseif (in_array($status, ['received', 'completed'])) {
                                                        $workflowStage = '<br><small class="text-success"><i class="fas fa-check-circle"></i> Complete</small>';
                                                    }
                                                    ?>
                                                    <span class="badge <?php echo $statusClass; ?>">
                                                        <?php echo $statusDisplay; ?>
                                                    </span>
                                                    <?php echo $workflowStage; ?>
                                                </td>
                                                <td><?php echo formatCurrency($purchaseOrder->total_amount ?? 0); ?></td>
                                                <td>
                                                    <?php if (!empty($purchaseOrder->expected_date) && $purchaseOrder->expected_date !== '0000-00-00'): ?>
                                                        <?php echo date('M j', strtotime($purchaseOrder->expected_date)); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Not set</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($purchaseOrder->tracking_number)): ?>
                                                        <span class="badge badge-info" data-toggle="tooltip"
                                                            title="<?php echo htmlspecialchars($purchaseOrder->tracking_number); ?>">
                                                            <i class="fas fa-truck mr-1"></i>
                                                            <?php echo htmlspecialchars(substr($purchaseOrder->tracking_number, 0, 8)); ?>...
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">
                                                            <i class="fas fa-minus"></i> No tracking
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?php echo URLROOT; ?>/purchases/details/<?php echo $purchaseOrder->purchase_id ?? 0; ?>"
                                                            class="btn btn-outline-primary btn-sm" data-toggle="tooltip"
                                                            title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <?php if (!empty($purchaseOrder->tracking_number)): ?>
                                                            <span class="btn btn-success btn-sm" data-toggle="tooltip"
                                                                title="Tracking: <?php echo htmlspecialchars($purchaseOrder->tracking_number); ?>">
                                                                <i class="fas fa-check"></i>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                            No purchase orders found
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (!empty($purchaseOrders) && count($purchaseOrders) > 10): ?>
                        <div class="card-footer bg-light text-center">
                            <small class="text-muted">Showing first 10 of <?php echo count($purchaseOrders); ?>
                                purchase orders</small>
                            <a href="<?php echo URLROOT; ?>/purchases/history" class="btn btn-outline-primary btn-sm ml-2">
                                <i class="fas fa-list"></i> View All Purchase Orders
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>



<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>

<script>
    $(document).ready(function () {
        console.log('Document ready - jQuery is working');
        console.log('jQuery version:', $.fn.jquery);

        // Initialize DataTables
        if ($('#activeOrdersTable').length > 0) {
            $('#activeOrdersTable').DataTable({
                "order": [[2, "desc"]], // Sort by Date column descending
                "pageLength": 10,
                "responsive": true,
                "columnDefs": [
                    { "orderable": false, "targets": [7] }, // Disable sorting on Actions column
                ],
                "language": {
                    "search": "Search orders:",
                    "lengthMenu": "Show _MENU_ orders per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ orders"
                }
            });
        }

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Load dock and receiving area options
        console.log('About to call loadLocationOptions');
        loadLocationOptions();
        // Keep duplicate selects in sync if both are present
        $(document).on('change', '#dockSelectMain', function () {
            if ($('#dockSelectInCard').length) {
                $('#dockSelectInCard').val($(this).val());
            }
        });
        $(document).on('change', '#dockSelectInCard', function () {
            if ($('#dockSelectMain').length) {
                $('#dockSelectMain').val($(this).val());
            }
        });
        $(document).on('change', '#receivingAreaSelectMain', function () {
            if ($('#receivingAreaSelectInCard').length) {
                $('#receivingAreaSelectInCard').val($(this).val());
            }
        });
        $(document).on('change', '#receivingAreaSelectInCard', function () {
            if ($('#receivingAreaSelectMain').length) {
                $('#receivingAreaSelectMain').val($(this).val());
            }
        });
    });    // Load dock and receiving area options
    function loadLocationOptions() {
        console.log('Starting to load location options...');
        $.ajax({
            url: '<?php echo URLROOT; ?>/api/getDockLocations.php',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                console.log('API Response:', response);
                if (response.success) {
                    const docks = response.data.docks;
                    const receivingAreas = response.data.receiving_areas;

                    console.log('Docks:', docks);
                    console.log('Receiving Areas:', receivingAreas);

                    // Check if dropdowns exist
                    console.log('Dock dropdown exists:', $('#dockSelectMain').length);
                    console.log('Receiving area dropdown exists:', $('#receivingAreaSelectMain').length);

                    // Populate dock dropdown
                    let dockOptions = '<option value="">Select Dock...</option>';
                    docks.forEach(dock => {
                        dockOptions += `<option value="${dock.location_id}">${dock.location_name}</option>`;
                    });
                    $('#dockSelectMain').html(dockOptions);
                    // Also populate in-card dock select if present
                    if ($('#dockSelectInCard').length) {
                        $('#dockSelectInCard').html(dockOptions);
                    }
                    console.log('Dock options set:', dockOptions);

                    // Populate receiving area dropdown
                    let areaOptions = '<option value="">Select Receiving Area...</option>';
                    receivingAreas.forEach(area => {
                        areaOptions += `<option value="${area.location_id}">${area.location_name}</option>`;
                    });
                    $('#receivingAreaSelectMain').html(areaOptions);
                    // Also populate in-card receiving area select if present
                    if ($('#receivingAreaSelectInCard').length) {
                        $('#receivingAreaSelectInCard').html(areaOptions);
                    }
                    console.log('Area options set:', areaOptions);
                } else {
                    console.error('API returned success: false');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error loading location options:', error);
                console.error('XHR status:', status);
                console.error('Response text:', xhr.responseText);
            }
        });
    }



    // Refresh purchase orders function
    function refreshPurchaseOrders() {
        location.reload();
    }

    // Enter key support for main search
    $('#poSearchInputMain').on('keypress', function (e) {
        if (e.which === 13) {
            searchPOMain();
        }
    });

    // Main Quick Receive functionality (on the page) - Enhanced Version
    function searchPOMain() {
        const searchTerm = $('#poSearchInputMain').val().trim();
        if (!searchTerm) {
            $('#searchResultsMain').html(`
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Please enter a PO number to search
                </div>
            `);
            return;
        }

        // Update step progression
        updateStepProgress(2);

        // Update button to show loading
        const searchButton = $('#searchButton');
        searchButton.prop('disabled', true).html('<i class="loading-spinner"></i> Searching...');

        // Show location selection panel with loading state
        $('#locationSelectionMain').show();

        $.ajax({
            url: '<?php echo URLROOT; ?>/api/searchPurchaseOrder.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                po_number: searchTerm
            }),
            success: function (response) {
                if (response.success) {
                    const data = response.data;
                    updateStepProgress(3);

                    $('#searchResultsMain').html(`
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-check-circle mr-2 success-checkmark"></i>
                                    Purchase Order Found
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5 class="text-success mb-3">${data.po_number}</h5>
                                        <p class="mb-2">
                                            <strong><i class="fas fa-building text-primary mr-1"></i>Supplier:</strong><br>
                                            <span class="text-muted">${data.supplier_name}</span>
                                        </p>
                                        <p class="mb-2">
                                            <strong><i class="fas fa-rupee-sign text-success mr-1"></i>Total Amount:</strong><br>
                                            <span class="text-muted h5">₹${parseFloat(data.total_amount || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</span>
                                        </p>
                                        <p class="mb-2">
                                            <strong><i class="fas fa-user mr-1"></i>Receiver:</strong><br>
                                            <span class="text-muted"><?php echo htmlspecialchars($_SESSION['user_full_name'] ?? ''); ?></span>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="bg-light rounded p-3">
                                            <i class="fas fa-clipboard-check fa-3x text-success mb-2"></i>
                                            <p class="text-muted mb-0">Steps: Select Dock → Start Unloading → Confirm Received</p>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label class="small text-muted mb-1">Dock Location</label>
                                        <select class="form-control" id="dockSelectInCard" style="height: auto; min-height: 38px; line-height: 1.5;">
                                            <option value="">Select Dock...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="small text-muted mb-1">Receiving Area</label>
                                        <select class="form-control" id="receivingAreaSelectInCard" style="height: auto; min-height: 38px; line-height: 1.5;">
                                            <option value="">Select Area...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="small text-muted mb-1">Notes</label>
                                        <input type="text" class="form-control" id="notesInputInCard" placeholder="Optional notes" style="height: auto; min-height: 38px;">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button class="btn btn-outline-success btn-lg mr-2" id="startUnloadingBtn" onclick="startUnloading('${searchTerm}')">
                                            <i class="fas fa-box-open mr-2"></i>Start Unloading
                                        </button>
                                        <span id="unloadingTimer" class="ml-3 text-muted" style="font-weight:bold;"></span>
                                        <button class="btn btn-success btn-lg ml-2" id="confirmReceivedBtn" disabled onclick="confirmReceived('${searchTerm}')">
                                            <i class="fas fa-check mr-2"></i>Confirm Received & Stage
                                        </button>
                                        <!-- Print handled from the receipt view; avoid duplicate print buttons -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                    // Hide the top location selector and prefer the in-card controls
                    $('#locationSelectionMain').hide();
                    $('#dockSelectMain').prop('disabled', true);
                    $('#receivingAreaSelectMain').prop('disabled', true);
                    // If in-card selects are present but empty, copy options from the main selects
                    if ($('#dockSelectInCard').length) {
                        if ($('#dockSelectInCard').children().length <= 1) {
                            $('#dockSelectInCard').html($('#dockSelectMain').html());
                        }
                        $('#dockSelectInCard').prop('disabled', false);
                    }
                    if ($('#receivingAreaSelectInCard').length) {
                        if ($('#receivingAreaSelectInCard').children().length <= 1) {
                            $('#receivingAreaSelectInCard').html($('#receivingAreaSelectMain').html());
                        }
                        $('#receivingAreaSelectInCard').prop('disabled', false);
                    }
                } else {
                    resetStepProgress();
                    $('#searchResultsMain').html(`
                        <div class="alert alert-danger">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle fa-2x text-danger mr-3"></i>
                                <div>
                                    <h6 class="mb-1">Purchase Order Not Found</h6>
                                    <p class="mb-0">${response.message}</p>
                                </div>
                            </div>
                        </div>
                    `);
                    $('#locationSelectionMain').hide();
                }
            },
            error: function (xhr, status, error) {
                console.error('Search error:', error);
                resetStepProgress();
                let errorMessage = 'Error searching for purchase order. Please try again.';
                if (xhr.status === 404) {
                    errorMessage = 'API endpoint not found. Please contact administrator.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error occurred. Please try again later.';
                }
                $('#searchResultsMain').html(`
                    <div class="alert alert-danger">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger mr-3"></i>
                            <div>
                                <h6 class="mb-1">Search Failed</h6>
                                <p class="mb-0">${errorMessage}</p>
                            </div>
                        </div>
                    </div>
                `);
                $('#locationSelectionMain').hide();
            },
            complete: function () {
                // Re-enable button
                searchButton.prop('disabled', false).html('<i class="fas fa-search mr-2"></i>Find & Process');
            }
        });
    }

    const originalMarkAsReceived = function (poNumber, dockLocationId = null, receivingAreaId = null, notes = '') {
        $('#searchResultsMain').html('<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Processing ' + poNumber + '...</div>');

        // Prepare data for the API call
        const requestData = {
            po_number: poNumber
        };

        if (dockLocationId) {
            requestData.dock_location_id = dockLocationId;
        }

        if (receivingAreaId) {
            requestData.receiving_area_id = receivingAreaId;
        }

        if (notes) {
            requestData.notes = notes;
        }

        // Make AJAX call to mark purchase order as received
        // Mark initiation flags before starting
        quickReceiveAjaxInitiated = true;
        quickReceiveAjaxActive = true;

        const jq = $.ajax({
            url: '<?php echo URLROOT; ?>/api/quickReceivePurchaseOrder.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
            success: function (response) {
                if (response.success) {
                    const data = response.data;
                    let locationInfo = '';
                    if (data.dock_location) {
                        locationInfo += `<br><strong>Dock:</strong> ${data.dock_location}`;
                    }
                    if (data.receiving_area) {
                        locationInfo += `<br><strong>Receiving Area:</strong> ${data.receiving_area}`;
                    }
                    if (data.notes) {
                        locationInfo += `<br><strong>Notes:</strong> ${data.notes}`;
                    }

                    $('#searchResultsMain').html(`
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <strong>Success!</strong><br>
                            Purchase Order <strong>${data.po_number}</strong> from <strong>${data.supplier_name}</strong> 
                            (₹${parseFloat(data.total_amount || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 })}) 
                            has been marked as received and staged at dock.${locationInfo}<br>
                            <div class="mt-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="resetMainSearch()">
                                    <i class="fas fa-plus"></i> Process Another Order
                                </button>
                                <a href="<?php echo URLROOT; ?>/inventory/receiving" class="btn btn-primary btn-sm ml-2">
                                    <i class="fas fa-truck-loading"></i> Go to Receiving
                                </a>
                                <!-- Receipt printing is available on the receipt view; not duplicated here -->
                            </div>
                        </div>
                    `);
                    $('#poSearchInputMain').val('');
                    $('#locationSelectionMain').hide();

                    // If a receipt URL was returned, show an Open Receipt button so staff can open/print it.
                    // We intentionally avoid auto-opening/auto-printing to prevent duplicate print dialogs.
                    if (data.receipt_url) {
                        console.log('Quick receive returned receipt_url:', data.receipt_url);
                        // Append button to the action area inside the success message
                        $('#searchResultsMain .alert-success .mt-2').append(`
                            <a href="${data.receipt_url}" target="_blank" class="btn btn-outline-secondary btn-sm ml-2">
                                <i class="fas fa-print mr-1"></i> Open Receipt
                            </a>
                            <button class="btn btn-outline-info btn-sm ml-2" id="emailReceiptBtn">
                                <i class="fas fa-envelope mr-1"></i> Email Receipt
                            </button>
                        `);

                        // Wire up Email Receipt button to call the controller endpoint
                        $('#emailReceiptBtn').on('click', function () {
                            $(this).prop('disabled', true).text('Sending...');
                            $.post('<?php echo URLROOT; ?>/purchases/emailReceipt/' + encodeURIComponent(data.po_number), {}, function (resp) {
                                try {
                                    var r = typeof resp === 'object' ? resp : JSON.parse(resp);
                                    if (r.success) {
                                        alert('Receipt emailed successfully');
                                    } else {
                                        alert('Failed to email receipt: ' + (r.message || 'Unknown error'));
                                    }
                                } catch (e) {
                                    alert('Unexpected response from server');
                                }
                                $('#emailReceiptBtn').prop('disabled', false).html('<i class="fas fa-envelope mr-1"></i> Email Receipt');
                            }).fail(function () {
                                alert('Failed to contact server to email receipt');
                                $('#emailReceiptBtn').prop('disabled', false).html('<i class="fas fa-envelope mr-1"></i> Email Receipt');
                            });
                        });
                    }

                    // Attempt to refresh the orders table without a full page reload.
                    // Do NOT force a location.reload() here because that interrupts opening/printing the receipt.
                    try {
                        if ($.fn.dataTable && $.fn.dataTable.isDataTable('#activeOrdersTable')) {
                            // If the table was initialized with an AJAX source, reload it
                            try {
                                $('#activeOrdersTable').DataTable().ajax.reload(null, false);
                                console.log('Active orders DataTable reloaded via AJAX');
                            } catch (e) {
                                console.warn('DataTable reload failed, falling back to manual refresh button', e);
                                $('#searchResultsMain .alert-success .mt-2').append(`
                                    <button class="btn btn-outline-secondary btn-sm ml-2" onclick="location.reload()">
                                        <i class="fas fa-sync mr-1"></i> Refresh Page
                                    </button>
                                `);
                            }
                        } else {
                            // No DataTable ajax source available; show a manual refresh control so users can refresh when ready
                            $('#searchResultsMain .alert-success .mt-2').append(`
                                <button class="btn btn-outline-secondary btn-sm ml-2" onclick="location.reload()">
                                    <i class="fas fa-sync mr-1"></i> Refresh Page
                                </button>
                            `);
                        }
                    } catch (e) {
                        console.error('Error during post-receive refresh handling:', e);
                    }
                } else {
                    $('#searchResultsMain').html(`<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ${response.message}</div>`);
                }
            },
            error: function (xhr, status, error) {
                console.error('Receive error:', error);
                $('#searchResultsMain').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error processing purchase order. Please try again.</div>');
            },
            complete: function () {
                quickReceiveAjaxActive = false;
            }
        });

        return jq;
    };

    function showLocationSelection(poNumber) {
        $('#locationSelectionMain').show();
        $('#searchResultsMain').html(`
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                Please select dock and receiving area assignments for PO: <strong>${poNumber}</strong>
                <div class="mt-3">
                    <button class="btn btn-success btn-sm" onclick="processWithLocation('${poNumber}')">
                        <i class="fas fa-check"></i> Receive & Assign
                    </button>
                    <button class="btn btn-outline-secondary btn-sm ml-2" onclick="resetMainSearch()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        `);
    }

    function processWithLocation(poNumber) {
        const dockLocationId = $('#dockSelectMain').val();
        const receivingAreaId = $('#receivingAreaSelectMain').val();
        const notes = $('#notesInputMain').val().trim();

        // Validate at least one location is selected
        if (!dockLocationId && !receivingAreaId) {
            alert('Please select at least a dock location or receiving area.');
            return;
        }

        markAsReceivedMain(poNumber, dockLocationId, receivingAreaId, notes);
    }

    function resetMainSearch() {
        $('#poSearchInputMain').val('');
        $('#locationSelectionMain').show();
        $('#dockSelectMain').val('');
        $('#receivingAreaSelectMain').val('');
        $('#dockSelectMain').prop('disabled', false);
        $('#receivingAreaSelectMain').prop('disabled', false);
        $('#notesInputMain').val('');
        $('#searchResultsMain').html('<div class="alert alert-info"><i class="fas fa-info-circle"></i> Enter a PO number above to search for available purchase orders</div>');
        resetStepProgress();
    }

    // Enhanced UI helper functions
    function processWithLocation(poNumber) {
        const dockLocation = $('#dockSelectMain').val();
        const receivingArea = $('#receivingAreaSelectMain').val();
        const notes = $('#notesInputMain').val();

        if (!dockLocation || !receivingArea) {
            $('#searchResultsMain').append(`
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle"></i> Please select both dock location and receiving area before proceeding.
                </div>
            `);
            return;
        }

        markAsReceivedMain(poNumber, dockLocation, receivingArea, notes);
    }

    // Step progression functions
    function updateStepProgress(currentStep) {
        $('.step-item').removeClass('active completed');
        $('.step-line').removeClass('active');

        for (let i = 1; i <= 4; i++) {
            const stepItem = $(`.step-item:nth-child(${i * 2 - 1})`);
            const stepLine = $(`.step-line:nth-child(${i * 2})`);

            if (i < currentStep) {
                stepItem.addClass('completed');
                stepLine.addClass('active');
            } else if (i === currentStep) {
                stepItem.addClass('active');
            }
        }
    }

    function resetStepProgress() {
        $('.step-item').removeClass('active completed');
        $('.step-line').removeClass('active');
        $('.step-item:first').addClass('active');
    }

    // New utility functions for enhanced UX
    function scanBarcode() {
        // Show modal or use camera API for barcode scanning
        $('#poSearchInputMain').val('PO-2024-005');
        $('#poSearchInputMain').focus();
        // You could integrate with a barcode scanning library here
    }

    function showRecentPOs() {
        const recentPOs = ['PO-2024-005', 'PO-2024-004', 'PO-2024-003'];
        const poList = recentPOs.map(po => `<li><a href="#" onclick="fillPO('${po}')">${po}</a></li>`).join('');

        $('#searchResultsMain').html(`
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-history mr-2"></i>Recent Purchase Orders</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        ${poList}
                    </ul>
                </div>
            </div>
        `);
    }

    function fillPO(poNumber) {
        $('#poSearchInputMain').val(poNumber);
        searchPOMain();
    }

    function bulkReceive() {
        alert('Bulk receive feature allows you to process multiple POs at once. This feature is coming soon!');
    }

    // Unloading timer and controls
    let unloadingTimerInterval = null;
    let unloadingStartTime = null;

    function startUnloading(poNumber) {
        const dock = $('#dockSelectInCard').val();
        if (!dock) {
            alert('Please select a dock before starting unloading.');
            return;
        }

        $('#startUnloadingBtn').prop('disabled', true).text('Unloading...');
        $('#confirmReceivedBtn').prop('disabled', false);

        unloadingStartTime = Date.now();
        $('#unloadingTimer').text('Elapsed: 00:00');

        unloadingTimerInterval = setInterval(function () {
            const diff = Date.now() - unloadingStartTime;
            const mins = Math.floor(diff / 60000).toString().padStart(2, '0');
            const secs = Math.floor((diff % 60000) / 1000).toString().padStart(2, '0');
            $('#unloadingTimer').text('Elapsed: ' + mins + ':' + secs);
        }, 1000);
    }

    function confirmReceived(poNumber) {
        // stop timer
        if (unloadingTimerInterval) {
            clearInterval(unloadingTimerInterval);
            unloadingTimerInterval = null;
        }

        const dock = $('#dockSelectInCard').val();
        const area = $('#receivingAreaSelectInCard').val();
        const notes = $('#notesInputInCard').val() || '';

        if (!dock && !area) {
            alert('Please select at least a dock or receiving area to stage the items.');
            return;
        }

        // Use the existing receive flow to mark the PO as received and staged
        markAsReceivedMain(poNumber, dock, area, notes);
    }

    // Flags used to detect AJAX initiation and activity for quick receive
    let quickReceiveAjaxInitiated = false;
    let quickReceiveAjaxActive = false;

    // Enhanced markAsReceivedMain function to handle step progression
    function markAsReceivedMain(poNumber, dockLocationId = null, receivingAreaId = null, notes = '') {
        // Preserve the enhanced step UI, then delegate to the original implementation which
        // contains the receipt handling/printing logic.
        updateStepProgress(4);

        $('#searchResultsMain').html(`
            <div class="card border-info">
                <div class="card-body text-center">
                    <div class="loading-spinner mb-3" style="width: 40px; height: 40px;"></div>
                    <h5>Processing Purchase Order</h5>
                    <p class="text-muted">Please wait while we receive ${poNumber}...</p>
                </div>
            </div>
        `);

        // Delegate the actual receive flow (including printing) to the original function
        // which performs the AJAX call and handles receipt_url when returned.
        return originalMarkAsReceived(poNumber, dockLocationId, receivingAreaId, notes);
    }

    // Show detailed workflow explanation
    function showWorkflowDetails() {
        const workflowModal = `
            <div class="modal fade" id="workflowModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-route mr-2"></i>Complete Receiving Workflow
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="workflow-steps">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-primary">
                                            <i class="fas fa-truck"></i> Dock Operations
                                        </h6>
                                        <div class="list-group list-group-flush">
                                            <div class="list-group-item d-flex align-items-center">
                                                <span class="badge badge-warning mr-3">1</span>
                                                <div>
                                                    <strong>PO Arrives at Dock</strong>
                                                    <br><small class="text-muted">Status: "Arrived at Dock"</small>
                                                </div>
                                            </div>
                                            <div class="list-group-item d-flex align-items-center">
                                                <span class="badge badge-info mr-3">2</span>
                                                <div>
                                                    <strong>Dock Assignment</strong>
                                                    <br><small class="text-muted">Status: "Dock Assigned"</small>
                                                </div>
                                            </div>
                                            <div class="list-group-item d-flex align-items-center">
                                                <span class="badge badge-primary mr-3">3</span>
                                                <div>
                                                    <strong>Ready for Transfer</strong>
                                                    <br><small class="text-muted">Status: "Ready to Receive"</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-success">
                                            <i class="fas fa-warehouse"></i> Receiving Area Operations
                                        </h6>
                                        <div class="list-group list-group-flush">
                                            <div class="list-group-item d-flex align-items-center">
                                                <span class="badge badge-warning mr-3">4</span>
                                                <div>
                                                    <strong>Transfer to Receiving</strong>
                                                    <br><small class="text-muted">Status: "Receiving in Progress"</small>
                                                </div>
                                            </div>
                                            <div class="list-group-item d-flex align-items-center">
                                                <span class="badge badge-info mr-3">5</span>
                                                <div>
                                                    <strong>Product Processing</strong>
                                                    <br><small class="text-muted">Individual product scanning/verification</small>
                                                </div>
                                            </div>
                                            <div class="list-group-item d-flex align-items-center">
                                                <span class="badge badge-success mr-3">6</span>
                                                <div>
                                                    <strong>Receiving Complete</strong>
                                                    <br><small class="text-muted">Status: "Received" or "Completed"</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 p-3 bg-light rounded">
                                    <h6 class="text-info">Key Differences:</h6>
                                    <ul class="mb-0 small">
                                        <li><strong>Dock Phase:</strong> Physical delivery handling - trucks, unloading, dock assignment</li>
                                        <li><strong>Receiving Phase:</strong> Product processing - scanning, verification, inventory updates</li>
                                        <li><strong>Status Tracking:</strong> Each phase has distinct statuses for clear workflow visibility</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="<?php echo URLROOT; ?>/inventory/receiving" class="btn btn-primary">
                                <i class="fas fa-warehouse mr-1"></i>Go to Receiving Area
                            </a>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if present
        $('#workflowModal').remove();

        // Add modal to body and show
        $('body').append(workflowModal);
        $('#workflowModal').modal('show');
    }
</script>