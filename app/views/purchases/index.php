<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/app-unified.css">

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
                <a href="<?php echo URLROOT; ?>/purchases/quick" class="btn btn-primary btn-lg mr-2">
                    <i class="fas fa-bolt"></i> Quick Order
                </a>

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
                                                    switch ($status) {
                                                        case 'pending':
                                                            $statusClass = 'badge-warning';
                                                            break;
                                                        case 'sent':
                                                            $statusClass = 'badge-info';
                                                            break;
                                                        case 'in_transit':
                                                            $statusClass = 'badge-primary';
                                                            break;
                                                        case 'received':
                                                            $statusClass = 'badge-success';
                                                            break;
                                                        case 'cancelled':
                                                            $statusClass = 'badge-danger';
                                                            break;
                                                        default:
                                                            $statusClass = 'badge-secondary';
                                                    }
                                                    ?>
                                                    <span class="badge <?php echo $statusClass; ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $status)); ?>
                                                    </span>
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
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?php echo URLROOT; ?>/purchases/details/<?php echo $purchaseOrder->purchase_id ?? 0; ?>"
                                                            class="btn btn-outline-primary btn-sm" data-toggle="tooltip"
                                                            title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <?php if ($status === 'pending'): ?>
                                                            <a href="<?php echo URLROOT; ?>/purchases/edit/<?php echo $purchaseOrder->purchase_id ?? 0; ?>"
                                                                class="btn btn-outline-secondary btn-sm" data-toggle="tooltip"
                                                                title="Edit Purchase Order">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
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
                    { "orderable": false, "targets": [6] }, // Disable sorting on Actions column
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
                    console.log('Dock options set:', dockOptions);

                    // Populate receiving area dropdown
                    let areaOptions = '<option value="">Select Receiving Area...</option>';
                    receivingAreas.forEach(area => {
                        areaOptions += `<option value="${area.location_id}">${area.location_name}</option>`;
                    });
                    $('#receivingAreaSelectMain').html(areaOptions);
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
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <p class="mb-2">
                                                    <strong><i class="fas fa-building text-primary mr-1"></i>Supplier:</strong><br>
                                                    <span class="text-muted">${data.supplier_name}</span>
                                                </p>
                                            </div>
                                            <div class="col-sm-6">
                                                <p class="mb-2">
                                                    <strong><i class="fas fa-rupee-sign text-success mr-1"></i>Total Amount:</strong><br>
                                                    <span class="text-muted h5">₹${parseFloat(data.total_amount || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</span>
                                                </p>
                                            </div>
                                        </div>
                                        <p class="mb-0">
                                            <strong><i class="fas fa-info-circle text-info mr-1"></i>Status:</strong>
                                            <span class="badge badge-${data.status === 'Pending' ? 'warning' : 'info'}">${data.status}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="bg-light rounded p-3">
                                            <i class="fas fa-clipboard-check fa-3x text-success mb-2"></i>
                                            <p class="text-muted mb-0">Ready to assign dock location and receive</p>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-success btn-lg mr-2" onclick="processWithLocation('${searchTerm}')">
                                            <i class="fas fa-warehouse mr-2"></i>Assign Location & Receive
                                        </button>
                                        <button class="btn btn-outline-success btn-lg" onclick="markAsReceivedMain('${searchTerm}', null, null, '')">
                                            <i class="fas fa-fast-forward mr-2"></i>Quick Receive (Skip Location)
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
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

    function markAsReceivedMain(poNumber, dockLocationId = null, receivingAreaId = null, notes = '') {
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
        $.ajax({
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
                            </div>
                        </div>
                    `);
                    $('#poSearchInputMain').val('');
                    $('#locationSelectionMain').hide();

                    // Optionally refresh the orders table
                    setTimeout(function () {
                        location.reload();
                    }, 3000);
                } else {
                    $('#searchResultsMain').html(`<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ${response.message}</div>`);
                }
            },
            error: function (xhr, status, error) {
                console.error('Receive error:', error);
                $('#searchResultsMain').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error processing purchase order. Please try again.</div>');
            }
        });
    }

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
        $('#locationSelectionMain').hide();
        $('#dockSelectMain').val('');
        $('#receivingAreaSelectMain').val('');
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

    // Enhanced markAsReceivedMain function to handle step progression
    const originalMarkAsReceived = markAsReceivedMain;
    function markAsReceivedMain(poNumber, dockLocationId = null, receivingAreaId = null, notes = '') {
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

        // Call the original function with enhanced success handling
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

        $.ajax({
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
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-check-circle mr-2 success-checkmark"></i>
                                    Successfully Received!
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h5 class="text-success mb-3">${data.po_number}</h5>
                                        <p class="mb-2">
                                            <i class="fas fa-check-circle text-success mr-2"></i>
                                            Purchase order has been successfully received and staged.
                                            ${locationInfo}
                                        </p>
                                        <p class="text-muted mb-0">
                                            <small><i class="fas fa-clock mr-1"></i>Received at: ${new Date().toLocaleString()}</small>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="bg-light rounded p-3">
                                            <i class="fas fa-check-circle fa-4x text-success mb-2"></i>
                                            <p class="text-muted mb-0">Complete!</p>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-primary mr-2" onclick="resetMainSearch()">
                                            <i class="fas fa-plus mr-2"></i>Process Another PO
                                        </button>
                                        <button class="btn btn-outline-info" onclick="window.location.reload()">
                                            <i class="fas fa-refresh mr-2"></i>Refresh Page
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                } else {
                    resetStepProgress();
                    $('#searchResultsMain').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> ${response.message}
                        </div>
                    `);
                }
            },
            error: function (xhr, status, error) {
                resetStepProgress();
                console.error('Receive error:', error);
                $('#searchResultsMain').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> Error processing purchase order. Please try again.
                    </div>
                `);
            }
        });
    }
</script>