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
    /* Custom CSS for Enhanced Quick Off-load UI */
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

    /* Copy PO Number Button Styles */
    .copy-po-btn {
        font-size: 0.75rem;
        padding: 0.15rem 0.3rem;
        line-height: 1;
        border-radius: 0.2rem;
        transition: all 0.2s ease;
    }

    .copy-po-btn:hover {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
        transform: translateY(-1px);
    }

    .btn-xs {
        padding: 0.15rem 0.3rem;
        font-size: 0.75rem;
        line-height: 1;
        border-radius: 0.15rem;
    }

    /* Copy Notification Styles */
    .copy-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 12px 20px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 500;
        transform: translateX(400px);
        opacity: 0;
        transition: all 0.3s ease;
    }

    .copy-notification.show {
        transform: translateX(0);
        opacity: 1;
    }

    .copy-notification-error {
        background: #dc3545;
    }

    .copy-notification-info {
        background: #17a2b8;
    }

    .copy-notification i {
        font-size: 16px;
    }

    /* Paste ready indicator */
    .paste-ready {
        border-color: #28a745 !important;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
        background-color: rgba(40, 167, 69, 0.05) !important;
        animation: paste-pulse 1s ease-in-out infinite;
    }

    @keyframes paste-pulse {

        0%,
        100% {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        50% {
            border-color: #20c997;
            box-shadow: 0 0 0 0.3rem rgba(32, 201, 151, 0.35);
        }
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

    <!-- Quick Off-load Purchase Order Section - COMPACT UI -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white py-2">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="fas fa-truck-loading mr-2"></i>
                                Quick Off-load Purchase Order
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
                                    <button class="btn btn-outline-success border-success" type="button"
                                        onclick="pastePONumber()" data-toggle="tooltip"
                                        title="Paste PO Number from clipboard">
                                        <i class="fas fa-paste"></i>
                                    </button>
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
                                <button class="btn btn-outline-warning btn-sm" onclick="bulkOffload()">
                                    <i class="fas fa-list mr-1"></i>Bulk
                                </button>
                            </div>

                            <!-- Quick Search Instructions -->
                            <div class="alert alert-light border-primary mb-2 py-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle text-primary mr-2"></i>
                                    <small class="text-muted mb-0">Search for a PO number to start the 2-step
                                        off-loading process</small>
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

                        <!-- Right Side - Ongoing Off-loading POs Table -->
                        <div class="col-lg-4">
                            <div class="bg-light rounded p-3 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="text-dark mb-0">
                                        <i class="fas fa-truck-loading text-success mr-2"></i>
                                        Active Off-loading
                                    </h6>
                                    <span id="offloadingCount" class="badge badge-success">0</span>
                                </div>

                                <!-- Ongoing Off-loading Table -->
                                <div id="ongoingOffloadingContainer" style="max-height: 200px; overflow-y: auto;">
                                    <div id="noOffloadingMessage" class="text-center text-muted py-3">
                                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                        <div class="small">No active off-loading</div>
                                    </div>
                                    <div id="offloadingTable" class="d-none">
                                        <table class="table table-sm table-borderless mb-0">
                                            <thead>
                                                <tr class="text-muted" style="font-size: 0.75rem;">
                                                    <th class="border-0 pb-1">PO</th>
                                                    <th class="border-0 pb-1 text-right">Duration</th>
                                                </tr>
                                            </thead>
                                            <tbody id="offloadingTableBody">
                                                <!-- Dynamic rows will be added here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Stats row -->
                                <div class="row text-center small mt-2 pt-2 border-top">
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
                            <i class="fas fa-info-circle mr-2"></i>Off-loading Workflow: Arrived at Facility
                        </h6>
                        <p class="mb-2 small">
                            <strong>This page:</strong> Use Quick Off-load to mark POs as "arrived at facility" when
                            they reach your dock.
                        </p>
                        <div class="row small">
                            <div class="col-md-8">
                                <strong class="text-primary">Quick Off-load Process:</strong>
                                <ul class="list-unstyled mb-0 ml-3">
                                    <li><i class="fas fa-truck text-warning"></i> PO delivery arrives at facility</li>
                                    <li><i class="fas fa-map-marker-alt text-info"></i> Assign to dock location</li>
                                    <li><i class="fas fa-clipboard-check text-success"></i> Status: "arrived at
                                        facility"</li>
                                </ul>
                            </div>
                        </div>
                        <p class="mt-2 small text-muted">
                            <i class="fas fa-arrow-right mr-1"></i>Next step: Use the inventory receiving system to
                            process individual products.
                        </p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="btn-group-vertical">
                            <a href="<?php echo URLROOT; ?>/inventory/receiving" class="btn btn-primary btn-sm">
                                <i class="fas fa-warehouse mr-1"></i>Go to Inventory Receiving
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
                                $hasValidOrders = false;

                                if (!empty($purchaseOrders)):
                                    foreach (array_slice($purchaseOrders, 0, 10) as $purchaseOrder):
                                        if (is_object($purchaseOrder)):
                                            $hasValidOrders = true;
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <strong
                                                            class="mr-2"><?php echo htmlspecialchars($purchaseOrder->po_number ?? $purchaseOrder->purchase_id ?? 'N/A'); ?></strong>
                                                        <button class="btn btn-outline-secondary btn-xs copy-po-btn"
                                                            onclick="copyPONumber('<?php echo htmlspecialchars($purchaseOrder->po_number ?? $purchaseOrder->purchase_id ?? 'N/A'); ?>')"
                                                            data-toggle="tooltip" title="Copy PO Number">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
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
                                                        case 'email_received':
                                                            $statusClass = 'badge-info';
                                                            $statusDisplay = 'Email Received';
                                                            break;
                                                        case 'in_transit':
                                                            $statusClass = 'badge-primary';
                                                            $statusDisplay = 'In Transit';
                                                            break;
                                                        case 'arrived_at_dock':
                                                            $statusClass = 'badge-warning';
                                                            $statusDisplay = 'Arrived at Dock';
                                                            break;
                                                        case 'off-loading':
                                                            $statusClass = 'badge-offloading';
                                                            $statusDisplay = 'Off-loading';
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
                                                    if (in_array($status, ['arrived_at_dock', 'off-loading', 'dock_assigned'])) {
                                                        $workflowStage = '<br><small class="text-muted"><i class="fas fa-truck"></i> Off-loading Phase</small>';
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
                                                        <span class="badge badge-info badge-compact" data-toggle="tooltip"
                                                            title="<?php echo htmlspecialchars($purchaseOrder->tracking_number); ?> - Click to copy"
                                                            onclick="copyToClipboard('<?php echo htmlspecialchars($purchaseOrder->tracking_number); ?>')">
                                                            <i class="fas fa-truck mr-1"></i>
                                                            <?php echo htmlspecialchars($purchaseOrder->tracking_number); ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">
                                                            <i class="fas fa-minus mr-1"></i> No tracking
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
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        endif;
                                    endforeach;
                                endif;

                                // Don't add empty state row - let DataTables handle it
                                ?>
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

        // Function to safely initialize DataTable
        function initializeDataTable() {
            const $table = $('#activeOrdersTable');

            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable($table)) {
                console.log('Destroying existing DataTable...');
                $table.DataTable().destroy();
            }

            // Clear any DataTable classes that might interfere
            $table.removeClass('dataTable');
            $table.find('thead, tbody, tfoot').removeClass('dataTable');

            return initializeDataTableCore();
        }

        // Core DataTable initialization function
        function initializeDataTableCore() {
            const $table = $('#activeOrdersTable');

            if ($table.length === 0) {
                console.warn('Table #activeOrdersTable not found');
                return false;
            }

            if (!$.fn.DataTable) {
                console.warn('DataTable library not available');
                return false;
            }

            try {
                // Debug table structure
                const headerCols = $table.find('thead tr th').length;
                const allRows = $table.find('tbody tr');

                console.log('Table structure debug:');
                console.log('- Header columns:', headerCols);
                console.log('- Total body rows:', allRows.length);

                // Check each row for column count consistency
                let inconsistentRows = 0;

                allRows.each(function (index) {
                    const cols = $(this).find('td').length;
                    if (cols !== headerCols) {
                        console.warn(`Row ${index} has ${cols} columns, expected ${headerCols}`);
                        inconsistentRows++;
                    }
                });

                if (inconsistentRows > 0) {
                    console.error(`Found ${inconsistentRows} rows with incorrect column count`);
                    return false; // Don't initialize DataTable
                }

                // Verify we have the expected 8 columns
                if (headerCols !== 8) {
                    console.error('Column count mismatch: Expected 8 columns, found', headerCols);
                    console.log('Header columns:', $table.find('thead tr th').map(function () { return $(this).text().trim(); }).get());
                    return false;
                }

                const dataTableConfig = {
                    "order": [[2, "desc"]], // Sort by Date column descending
                    "pageLength": 10,
                    "responsive": true,
                    "columnDefs": [
                        { "orderable": false, "targets": [7] }, // Disable sorting on Actions column (8th column, index 7)
                    ],
                    "language": {
                        "search": "Search orders:",
                        "lengthMenu": "Show _MENU_ orders per page",
                        "info": "Showing _START_ to _END_ of _TOTAL_ orders",
                        "emptyTable": '<div class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2"></i><br>No purchase orders found</div>',
                        "zeroRecords": '<div class="text-center text-muted py-4"><i class="fas fa-search fa-2x mb-2"></i><br>No matching purchase orders found</div>'
                    }
                };

                $table.DataTable(dataTableConfig);
                console.log('DataTable initialized successfully with', headerCols, 'columns');
                return true;

            } catch (e) {
                console.error('Failed to initialize DataTable:', e);
                console.log('Error details:', e.message);
                return false;
            }
        }

        // Wait a moment for any dynamic content to load, then initialize
        setTimeout(function () {
            if (!initializeDataTable()) {
                console.log('DataTable initialization failed, falling back to basic table functionality');
            }

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
        }, 150); // Increased delay to ensure DOM is fully ready

        // Load dock location options
        console.log('About to call loadLocationOptions');
        loadLocationOptions();
    });    // Load dock location options
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

                    console.log('Docks:', docks);

                    // Check if dock dropdown exists
                    console.log('In-card dock dropdown exists:', $('#dockSelectInCard').length);

                    // Populate dock dropdown
                    let dockOptions = '<option value="">Select Dock...</option>';
                    docks.forEach(dock => {
                        dockOptions += `<option value="${dock.location_id}">${dock.location_name}</option>`;
                    });

                    // Populate in-card dock select
                    if ($('#dockSelectInCard').length) {
                        $('#dockSelectInCard').html(dockOptions);
                    }
                    console.log('Dock options set:', dockOptions);
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

    // Direct function to populate dock dropdown
    function populateDockDropdown() {
        console.log('populateDockDropdown called');
        $.ajax({
            url: '<?php echo URLROOT; ?>/api/getDockLocations.php',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                console.log('Dock API Response:', response);
                if (response.success && response.data && response.data.docks) {
                    const docks = response.data.docks;
                    let dockOptions = '<option value="">Select Dock...</option>';
                    docks.forEach(dock => {
                        dockOptions += `<option value="${dock.location_id}">${dock.location_name}</option>`;
                    });

                    // Find and populate any dock dropdowns
                    $('select[id^="dockSelectInCard"], select[id*="dockSelect"]').each(function () {
                        console.log('Populating dropdown:', this.id);
                        $(this).html(dockOptions);
                    });

                    console.log('Dock dropdowns populated with options:', dockOptions);
                } else {
                    console.error('No dock data received');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error loading dock options:', error);
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

    // Main Quick Off-load functionality (on the page) - Enhanced Version
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

                    // Check if this PO is stuck in off-loading
                    if (data.is_offloading && data.stuck_info && data.stuck_info.is_stuck) {
                        // Show stuck off-loading UI
                        $('#searchResultsMain').html(`
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Off-loading In Progress (Stuck): ${data.po_number}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock fa-2x mr-3"></i>
                                            <div>
                                                <h6 class="mb-1">Off-loading has been running for ${data.stuck_info.elapsed_minutes} minutes</h6>
                                                <p class="mb-0">This may be due to a browser session ending or network interruption.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h5 class="text-warning mb-3">${data.po_number}</h5>
                                            <p class="mb-2">
                                                <strong><i class="fas fa-building text-primary mr-1"></i>Supplier:</strong><br>
                                                <span class="text-muted">${data.supplier_name}</span>
                                            </p>
                                            <p class="mb-2">
                                                <strong><i class="fas fa-rupee-sign text-success mr-1"></i>Total Amount:</strong><br>
                                                <span class="text-muted h5">₹${parseFloat(data.total_amount || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</span>
                                            </p>
                                            <p class="mb-3">
                                                <strong><i class="fas fa-clock text-warning mr-1"></i>Off-loading Started:</strong><br>
                                                <span class="text-muted">${data.dock_arrival_time}</span>
                                            </p>
                                            <p class="mb-3">
                                                <strong><i class="fas fa-stopwatch text-warning mr-1"></i>Elapsed Time:</strong><br>
                                                <span class="text-muted h5">${data.stuck_info.elapsed_formatted}</span>
                                            </p>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="bg-light rounded p-3">
                                                <i class="fas fa-truck-loading fa-3x text-warning mb-2"></i>
                                                <p class="text-muted mb-0">Resume the off-loading process or complete it manually</p>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="alert alert-warning mb-2 py-2">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <i class="fas fa-play mr-2"></i>Off-loading resumed...
                                                        <strong id="timer_${data.po_number}">${data.stuck_info.elapsed_formatted}</strong>
                                                    </div>
                                                    <small class="text-muted">Status: Resuming Off-loading</small>
                                                </div>
                                            </div>
                                            <button class="btn btn-success btn-lg" onclick="completeOffloadStep2('${data.po_number}')">
                                                <i class="fas fa-check mr-2"></i>Complete Off-loading (Resume)
                                            </button>
                                            <small class="text-muted ml-3">This will mark the PO as ready for receiving.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);

                        // Start timer from the correct elapsed time
                        resumeStuckOffloading(data.po_number, data.dock_arrival_time);

                    } else {
                        // Normal case - show regular off-loading UI
                        $('#searchResultsMain').html(`
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-check-circle mr-2 success-checkmark"></i>
                                        Purchase Order Found: Ready to Off-load
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
                                            <p class="mb-3">
                                                <strong><i class="fas fa-user mr-1"></i>Ready to Off-load by:</strong><br>
                                                <span class="text-muted"><?php echo htmlspecialchars($_SESSION['user_full_name'] ?? ''); ?></span>
                                            </p>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="bg-light rounded p-3">
                                                <i class="fas fa-truck-loading fa-3x text-success mb-2"></i>
                                                <p class="text-muted mb-0">Select dock location and off-load to mark as "Off-loading"</p>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="small text-muted mb-1">Dock Location</label>
                                            <select class="form-control" id="dockSelectInCard" style="height: auto; min-height: 38px; line-height: 1.5;">
                                                <option value="">Select Dock...</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="small text-muted mb-1">Notes</label>
                                            <input type="text" class="form-control" id="notesInputInCard" placeholder="Optional off-loading notes" style="height: auto; min-height: 38px;">
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div id="offloadStep1_${data.po_number}" style="display: block;">
                                                <button class="btn btn-primary btn-lg" onclick="startOffloadStep1('${data.po_number}')">
                                                    <i class="fas fa-truck-loading mr-2"></i>Assign Dock & Start Off-loading
                                                </button>
                                            </div>
                                            <div id="offloadStep2_${data.po_number}" style="display: none;">
                                                <div class="alert alert-info mb-2 py-2">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <i class="fas fa-clock mr-2"></i>Off-loading in progress...
                                                            <strong id="timer_${data.po_number}">00:00</strong>
                                                        </div>
                                                        <small class="text-muted">Status: Off-loading</small>
                                                    </div>
                                                </div>
                                                <button class="btn btn-success btn-lg" onclick="completeOffloadStep2('${data.po_number}')">
                                                    <i class="fas fa-check mr-2"></i>Confirm Off-loading Complete
                                                </button>
                                            </div>
                                            <small class="text-muted ml-3">This will mark the PO as "Off-loading" and ready for inventory receiving.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    }

                    // Show the location selector for dock assignment (no longer hide it)
                    $('#locationSelectionMain').show();
                    // Ensure dock select is populated and enabled
                    if ($('#dockSelectInCard').length) {
                        // Reload dock options if empty
                        if ($('#dockSelectInCard').children().length <= 1) {
                            loadLocationOptions();
                        }
                        $('#dockSelectInCard').prop('disabled', false);
                    }
                    // Also ensure dock dropdown is populated immediately after creating the HTML
                    setTimeout(function () {
                        populateDockDropdown();
                    }, 100);
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

    const originalMarkAsOffloaded = function (poNumber, dockLocationId = null, notes = '') {
        $('#searchResultsMain').html('<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Processing ' + poNumber + '...</div>');

        // Prepare data for the API call
        const requestData = {
            po_number: poNumber
        };

        if (dockLocationId) {
            requestData.dock_location_id = dockLocationId;
        }

        if (notes) {
            requestData.notes = notes;
        }

        // Make AJAX call to mark purchase order as off-loaded at dock
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
                            // Since this table doesn't use AJAX, we'll add a refresh button instead
                            console.log('DataTable detected - adding refresh option');
                            $('#searchResultsMain .alert-success .mt-2').append(`
                                <button class="btn btn-outline-secondary btn-sm ml-2" onclick="location.reload()">
                                    <i class="fas fa-sync mr-1"></i> Refresh to see updated orders
                                </button>
                            `);
                        } else {
                            // No DataTable; show a manual refresh control so users can refresh when ready
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
                Please select dock location for PO: <strong>${poNumber}</strong>
                <div class="mt-3">
                    <button class="btn btn-success btn-sm" onclick="processWithLocation('${poNumber}')">
                        <i class="fas fa-truck-loading"></i> Off-load to Facility
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
        const notes = $('#notesInputMain').val().trim();

        // Validate dock location is selected
        if (!dockLocationId) {
            alert('Please select a dock location for off-loading.');
            return;
        }

        markAsOffloadedMain(poNumber, dockLocationId, notes);
    }

    // Timer tracking for off-loading process
    let offloadTimers = {};

    function startOffloadStep1(poNumber) {
        const dockLocationId = $('#dockSelectInCard').val();
        const notes = $('#notesInputInCard').val().trim();

        console.log('startOffloadStep1 called with:', {
            poNumber: poNumber,
            dockLocationId: dockLocationId,
            notes: notes
        });

        // Validate dock location is selected
        if (!dockLocationId) {
            alert('Please select a dock location for off-loading.');
            return;
        }

        // Start the off-loading process
        $.ajax({
            url: '<?php echo URLROOT; ?>/api/quickReceivePurchaseOrder.php',
            method: 'POST',
            data: {
                po_number: poNumber,
                dock_location_id: dockLocationId,
                notes: notes || '',
                status: 'off-loading'
            },
            dataType: 'json',
            beforeSend: function () {
                console.log('Sending AJAX request to API...');
            },
            success: function (response) {
                console.log('API Response:', response);
                if (response.status === 'success') {
                    // Hide step 1, show step 2
                    $(`#offloadStep1_${poNumber}`).hide();
                    $(`#offloadStep2_${poNumber}`).show();

                    // Start timer
                    startOffloadTimer(poNumber);

                    // Add to ongoing off-loading table
                    addToOffloadingTable(poNumber, new Date().toISOString());

                    // Disable dock dropdown
                    $('#dockSelectIn Card').prop('disabled', true);

                    alert('Off-loading started! Status updated to: Arrived at Facility');
                } else {
                    console.error('API Error:', response);
                    alert('Error starting off-loading: ' + (response.message || 'Unknown error'));
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', { xhr: xhr, status: status, error: error });
                console.error('Response Text:', xhr.responseText);
                alert('Network error starting off-loading. Please try again.');
            }
        });
    }

    function completeOffloadStep2(poNumber) {
        // Stop timer
        stopOffloadTimer(poNumber);

        // Remove from ongoing off-loading table
        removeFromOffloadingTable(poNumber);

        // Complete the off-loading process
        $.ajax({
            url: '<?php echo URLROOT; ?>/api/quickReceivePurchaseOrder.php',
            method: 'POST',
            data: {
                po_number: poNumber,
                status: 'ready_to_receive'
            },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    const finalTime = $(`#timer_${poNumber}`).text();
                    alert(`Off-loading completed in ${finalTime}! Status updated to: Ready to Receive Products`);

                    // Reset the search form
                    resetMainSearch();
                } else {
                    alert('Error completing off-loading: ' + (response.message || 'Unknown error'));
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Network error completing off-loading. Please try again.');
            }
        });
    }

    function startOffloadTimer(poNumber) {
        offloadTimers[poNumber] = {
            startTime: Date.now(),
            interval: setInterval(() => {
                const elapsed = Date.now() - offloadTimers[poNumber].startTime;
                const minutes = Math.floor(elapsed / 60000);
                const seconds = Math.floor((elapsed % 60000) / 1000);
                const timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                $(`#timer_${poNumber}`).text(timeString);
            }, 1000)
        };
    }

    function stopOffloadTimer(poNumber) {
        if (offloadTimers[poNumber]) {
            clearInterval(offloadTimers[poNumber].interval);
            delete offloadTimers[poNumber];
        }
    }

    // Function to detect and handle stuck off-loading POs
    function checkForStuckOffloading(poNumber, status, dockArrivalTime) {
        // Support both old 'pending_arrival' and new 'off-loading' status during transition
        if ((status === 'off-loading' || status === 'pending_arrival') && dockArrivalTime) {
            const startTime = new Date(dockArrivalTime).getTime();
            const now = Date.now();
            const elapsed = now - startTime;
            const minutes = Math.floor(elapsed / 60000);

            // If off-loading has been running for more than 10 minutes, consider it stuck
            if (minutes > 10) {
                return {
                    isStuck: true,
                    elapsedMinutes: minutes,
                    elapsedFormatted: formatElapsedTime(elapsed)
                };
            }
        }
        return { isStuck: false };
    }

    function formatElapsedTime(elapsed) {
        const hours = Math.floor(elapsed / 3600000);
        const minutes = Math.floor((elapsed % 3600000) / 60000);
        const seconds = Math.floor((elapsed % 60000) / 1000);

        if (hours > 0) {
            return `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        } else {
            return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
    }

    // Function to resume stuck off-loading with correct elapsed time
    function resumeStuckOffloading(poNumber, startTime) {
        // Show step 2 UI
        $(`#offloadStep1_${poNumber}`).hide();
        $(`#offloadStep2_${poNumber}`).show();

        // Update the display text
        $(`#offloadStep2_${poNumber} .alert`).removeClass('alert-info').addClass('alert-warning');
        $(`#offloadStep2_${poNumber} .alert small`).text('Status: Resuming Off-loading (was stuck)');

        // Add to ongoing off-loading table with original start time
        addToOffloadingTable(poNumber, startTime);

        // Start timer from the correct elapsed time
        offloadTimers[poNumber] = {
            startTime: new Date(startTime).getTime(),
            interval: setInterval(() => {
                const elapsed = Date.now() - offloadTimers[poNumber].startTime;
                const timeString = formatElapsedTime(elapsed);
                $(`#timer_${poNumber}`).text(timeString);
            }, 1000)
        };

        // Change button text to indicate resume
        $(`#offloadStep2_${poNumber} button`).html('<i class="fas fa-play mr-2"></i>Resume & Complete Off-loading');
    }

    function processWithLocationFromCard(poNumber) {
        // Legacy function - replaced by 2-step workflow
        // Keeping for backward compatibility
        startOffloadStep1(poNumber);
    }

    function resetMainSearch() {
        $('#poSearchInputMain').val('');
        $('#searchResultsMain').html('<div class="alert alert-info"><i class="fas fa-info-circle"></i> Enter a PO number above to search for available purchase orders</div>');
        resetStepProgress();

        // Clear any running timers and ongoing table
        for (const poNumber in offloadTimers) {
            stopOffloadTimer(poNumber);
            removeFromOffloadingTable(poNumber);
        }
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
        const poList = recentPOs.map(po => `
            <li class="d-flex justify-content-between align-items-center py-1">
                <a href="#" onclick="fillPO('${po}')">${po}</a>
                <button class="btn btn-outline-secondary btn-xs ml-2" 
                        onclick="copyPONumber('${po}')"
                        data-toggle="tooltip" 
                        title="Copy PO Number">
                    <i class="fas fa-copy"></i>
                </button>
            </li>
        `).join('');

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

    function copyPONumber(poNumber) {
        // Create a temporary textarea element to copy the text
        const tempElement = document.createElement('textarea');
        tempElement.value = poNumber;
        document.body.appendChild(tempElement);
        tempElement.select();
        tempElement.setSelectionRange(0, 99999); // For mobile devices

        try {
            // Copy the text
            document.execCommand('copy');

            // Show success notification
            showCopyNotification(`PO Number "${poNumber}" copied!`);
        } catch (err) {
            console.error('Failed to copy PO number:', err);
            showCopyNotification('Failed to copy PO number', 'error');
        }

        // Remove the temporary element
        document.body.removeChild(tempElement);
    }

    function showCopyNotification(message, type = 'success') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `copy-notification copy-notification-${type}`;

        let icon = 'fa-check-circle';
        if (type === 'error') icon = 'fa-exclamation-triangle';
        else if (type === 'info') icon = 'fa-info-circle';

        notification.innerHTML = `
            <i class="fas ${icon}"></i>
            <span>${message}</span>
        `;

        // Add to page
        document.body.appendChild(notification);

        // Trigger animation
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        // Remove after 3 seconds (longer for info messages)
        const duration = type === 'info' ? 4000 : 3000;
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentNode) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, duration);
    }

    // Ongoing Off-loading Table Management Functions
    let ongoingOffloadingPOs = {};

    function addToOffloadingTable(poNumber, startTime) {
        ongoingOffloadingPOs[poNumber] = {
            startTime: new Date(startTime).getTime(),
            poNumber: poNumber
        };
        updateOffloadingTable();
    }

    function removeFromOffloadingTable(poNumber) {
        delete ongoingOffloadingPOs[poNumber];
        updateOffloadingTable();
    }

    function updateOffloadingTable() {
        const tableBody = $('#offloadingTableBody');
        const table = $('#offloadingTable');
        const noMessage = $('#noOffloadingMessage');
        const countBadge = $('#offloadingCount');

        const activeCount = Object.keys(ongoingOffloadingPOs).length;
        countBadge.text(activeCount);

        if (activeCount === 0) {
            table.addClass('d-none');
            noMessage.removeClass('d-none');
            return;
        }

        table.removeClass('d-none');
        noMessage.addClass('d-none');

        // Clear existing rows
        tableBody.empty();

        // Add rows for each ongoing off-loading PO
        Object.values(ongoingOffloadingPOs).forEach(po => {
            const elapsed = Date.now() - po.startTime;
            const timeString = formatElapsedTime(elapsed);
            const isStuck = elapsed > 600000; // 10 minutes

            const row = $(`
                <tr id="offloadingRow_${po.poNumber}" class="${isStuck ? 'text-warning' : ''}">
                    <td class="py-1">
                        <a href="#" onclick="scrollToPO('${po.poNumber}')" class="text-decoration-none">
                            ${po.poNumber}
                        </a>
                        ${isStuck ? '<i class="fas fa-exclamation-triangle text-warning ml-1" title="Stuck - may need attention"></i>' : ''}
                    </td>
                    <td class="text-right py-1">
                        <span id="offloadingDuration_${po.poNumber}">${timeString}</span>
                    </td>
                </tr>
            `);
            tableBody.append(row);
        });
    }

    function scrollToPO(poNumber) {
        // Scroll to the PO in the search results if it exists
        const poElement = $(`#offloadStep1_${poNumber}, #offloadStep2_${poNumber}`).first();
        if (poElement.length > 0) {
            $('html, body').animate({
                scrollTop: poElement.offset().top - 100
            }, 500);

            // Highlight the PO briefly
            poElement.addClass('border-primary').removeClass('border-primary').addClass('border-primary');
            setTimeout(() => {
                poElement.removeClass('border-primary');
            }, 2000);
        }
    }

    // Update durations every second
    function updateOffloadingDurations() {
        Object.values(ongoingOffloadingPOs).forEach(po => {
            const elapsed = Date.now() - po.startTime;
            const timeString = formatElapsedTime(elapsed);
            $(`#offloadingDuration_${po.poNumber}`).text(timeString);

            // Update row styling if it becomes stuck
            const row = $(`#offloadingRow_${po.poNumber}`);
            if (elapsed > 600000) { // 10 minutes
                row.addClass('text-warning');
                if (!row.find('.fa-exclamation-triangle').length) {
                    row.find('td:first a').after('<i class="fas fa-exclamation-triangle text-warning ml-1" title="Stuck - may need attention"></i>');
                }
            }
        });
    }

    // Start the duration update interval
    setInterval(updateOffloadingDurations, 1000);

    async function pastePONumber() {
        try {
            // First, focus the input field
            $('#poSearchInputMain').focus();

            // Check if the Clipboard API is available and we have permissions
            if (navigator.clipboard && navigator.clipboard.readText) {
                try {
                    const clipboardText = await navigator.clipboard.readText();
                    if (clipboardText.trim()) {
                        $('#poSearchInputMain').val(clipboardText.trim());
                        showCopyNotification(`PO Number "${clipboardText.trim()}" pasted!`);
                        return;
                    } else {
                        // Clipboard is empty, try alternative method
                        tryAlternativePaste();
                    }
                } catch (clipboardError) {
                    console.log('Clipboard API failed, trying alternative:', clipboardError);
                    tryAlternativePaste();
                }
            } else {
                // Clipboard API not available, try alternative
                tryAlternativePaste();
            }
        } catch (err) {
            console.error('Failed to paste:', err);
            tryAlternativePaste();
        }
    }

    function tryAlternativePaste() {
        // Create a temporary input that we can paste into
        const tempInput = document.createElement('input');
        tempInput.style.position = 'absolute';
        tempInput.style.left = '-9999px';
        tempInput.style.opacity = '0';
        document.body.appendChild(tempInput);

        // Focus the temp input and try to paste
        tempInput.focus();
        tempInput.select();

        // Try to execute paste command
        try {
            const success = document.execCommand('paste');
            if (success && tempInput.value.trim()) {
                $('#poSearchInputMain').val(tempInput.value.trim());
                $('#poSearchInputMain').focus();
                showCopyNotification(`PO Number "${tempInput.value.trim()}" pasted!`);
                document.body.removeChild(tempInput);
                return;
            }
        } catch (e) {
            console.log('execCommand paste failed:', e);
        }

        // Clean up temp input
        document.body.removeChild(tempInput);

        // Final fallback - show helpful message and focus input
        $('#poSearchInputMain').focus();
        showCopyNotification('Click here and press Ctrl+V to paste', 'info');

        // Add a temporary visual indicator
        $('#poSearchInputMain').addClass('paste-ready');
        setTimeout(() => {
            $('#poSearchInputMain').removeClass('paste-ready');
        }, 3000);
    }

    function bulkOffload() {
        alert('Bulk off-load feature allows you to process multiple POs at once. This feature is coming soon!');
    }

    // Flags used to detect AJAX initiation and activity for quick receive
    let quickReceiveAjaxInitiated = false;
    let quickReceiveAjaxActive = false;

    // Enhanced markAsOffloadedMain function to handle step progression
    function markAsOffloadedMain(poNumber, dockLocationId = null, notes = '') {
        // Preserve the enhanced step UI, then delegate to the original implementation which
        // contains the receipt handling/printing logic.
        updateStepProgress(4);

        $('#searchResultsMain').html(`
            <div class="card border-info">
                <div class="card-body text-center">
                    <div class="loading-spinner mb-3" style="width: 40px; height: 40px;"></div>
                    <h5>Processing Purchase Order</h5>
           <p class="text-muted">Please wait while we off-load ${poNumber}...</p>
                </div>
            </div>
        `);

        // Delegate the actual off-load flow to the original function
        // which performs the AJAX call and handles receipt_url when returned.
        return originalMarkAsOffloaded(poNumber, dockLocationId, notes);
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
                                            <i class="fas fa-warehouse"></i> Inventory Receiving Operations
                                        </h6>
                                        <div class="list-group list-group-flush">
                                            <div class="list-group-item d-flex align-items-center">
                                                <span class="badge badge-warning mr-3">4</span>
                                                <div>
                                                    <strong>Process Into Inventory</strong>
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
                                <i class="fas fa-warehouse mr-1"></i>Go to Inventory Receiving
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

    // Copy tracking number to clipboard
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function () {
            // Show temporary success feedback
            const temp = $('<div class="alert alert-success alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">')
                .html('<i class="fas fa-check mr-2"></i>Tracking number copied to clipboard!')
                .append('<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>');

            $('body').append(temp);

            // Auto-dismiss after 3 seconds
            setTimeout(function () {
                temp.alert('close');
            }, 3000);
        }).catch(function (err) {
            console.error('Failed to copy: ', err);
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);

            alert('Tracking number copied: ' + text);
        });
    }

    // Load existing off-loading POs when page loads
    function loadExistingOffloadingPOs() {
        $.ajax({
            url: '<?php echo URLROOT; ?>/api/searchPurchaseOrder.php',
            method: 'POST',
            data: {
                action: 'get_offloading_pos'
            },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success' && response.offloading_pos) {
                    response.offloading_pos.forEach(function (po) {
                        addToOffloadingTable(po.po_number, po.dock_arrival_time);
                    });
                    console.log('Loaded existing off-loading POs:', response.offloading_pos.length);
                }
            },
            error: function (xhr, status, error) {
                console.log('Could not load existing off-loading POs:', error);
            }
        });
    }

    // Load existing off-loading POs when document is ready
    $(document).ready(function () {
        loadExistingOffloadingPOs();
    });
</script>