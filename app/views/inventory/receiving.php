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

// Check permissions for receiving
$permissions = [
    'admin'             => ['can_receive' => true],
    'warehouse_manager' => ['can_receive' => true],
    'receiving_clerk'   => ['can_receive' => true],
    'inventory_clerk'   => ['can_receive' => false],
    'viewer'            => ['can_receive' => false]
];

$userPermissions = $permissions[$systemRole] ?? $permissions['viewer'];

// Redirect if no permission
if (!$userPermissions['can_receive']) {
    header('Location: ' . URLROOT . '/inventory');
    exit();
}

// Use data passed from controller
$receivingStats = $data['receivingStats'] ?? [
    'deliveries_today'     => 0,
    'items_received_today' => 0,
    'pending_items'        => 0,
    'completed_items'      => 0
];
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-warehouse text-primary"></i> Simple Receiving
            </h1>
            <p class="text-muted mb-0">Easy 3-step receiving process</p>
        </div>
        <div>
            <a href="<?= URLROOT ?>/purchases" class="btn btn-outline-primary">
                <i class="fas fa-plus mr-2"></i>Receive New PO
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="theme-card text-center">
                <div class="card-body">
                    <div class="h2 mb-1 text-primary"><?= $receivingStats['pending_items'] ?></div>
                    <div class="text-muted">Items to Receive</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="theme-card text-center">
                <div class="card-body">
                    <div class="h2 mb-1 text-success"><?= $receivingStats['items_received_today'] ?></div>
                    <div class="text-muted">Received Today</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="theme-card text-center">
                <div class="card-body">
                    <div class="h2 mb-1 text-info"><?= $receivingStats['deliveries_today'] ?></div>
                    <div class="text-muted">Deliveries Today</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="theme-card text-center">
                <div class="card-body">
                    <div class="h2 mb-1 text-warning"><?= $receivingStats['completed_items'] ?></div>
                    <div class="text-muted">Completed Items</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step Indicator -->
    <div class="theme-card-light p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center" id="step-1">
                <div class="btn btn-primary rounded-circle mr-3"
                    style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">1
                </div>
                <div class="font-weight-bold">Select Purchase Order</div>
            </div>
            <div class="d-flex align-items-center" id="step-2">
                <div class="btn btn-outline-secondary rounded-circle mr-3"
                    style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">2
                </div>
                <div class="text-muted">Verify Products</div>
            </div>
            <div class="d-flex align-items-center" id="step-3">
                <div class="btn btn-outline-secondary rounded-circle mr-3"
                    style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">3
                </div>
                <div class="text-muted">Complete Receiving</div>
            </div>
        </div>
    </div>

    <!-- Step 1: Select PO -->
    <div id="step-1-content" class="theme-card p-4 mb-4">
        <h4 class="mb-3"><i class="fas fa-search mr-2"></i>Step 1: Select Purchase Order</h4>
        <div class="row">
            <div class="col-md-6">
                <label for="po-selector" class="form-label">Choose PO to receive:</label>
                <select id="po-selector" class="form-control form-control-lg">
                    <option value="">Select a Purchase Order...</option>
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button id="load-po-btn" class="btn btn-primary btn-lg" disabled>
                    <i class="fas fa-download mr-2"></i>Load PO Details
                </button>
            </div>
        </div>
    </div>

    <!-- Step 2: Verify Products -->
    <div id="step-2-content" class="theme-card p-4 mb-4 d-none">
        <h4 class="mb-3"><i class="fas fa-check mr-2"></i>Step 2: Verify Products</h4>
        <div id="po-info" class="alert alert-info mb-3"></div>
        <div class="table-responsive">
            <table class="table table-hover" id="products-table">
                <thead class="thead-light">
                    <tr>
                        <th>Product</th>
                        <th>Expected Qty</th>
                        <th>Received Qty</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="text-right">
            <button id="proceed-to-complete-btn" class="btn btn-success btn-lg" disabled>
                <i class="fas fa-arrow-right mr-2"></i>Proceed to Complete
            </button>
        </div>
    </div>

    <!-- Step 3: Complete -->
    <div id="step-3-content" class="theme-card p-4 mb-4 d-none">
        <h4 class="mb-3"><i class="fas fa-check-circle mr-2"></i>Step 3: Complete Receiving & Assign Locations</h4>

        <!-- Location Assignment Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-map-marker-alt mr-2"></i>Assign Storage Locations</h5>
            </div>
            <div class="card-body">
                <div id="location-assignments"></div>
            </div>
        </div>

        <!-- Receiving Details -->
        <div class="row">
            <div class="col-md-12">
                <label for="receiving-notes" class="form-label">Notes (optional):</label>
                <textarea id="receiving-notes" class="form-control" rows="3"
                    placeholder="Any notes about this receiving..."></textarea>
            </div>
        </div>
        <div class="text-right mt-3">
            <button id="complete-receiving-btn" class="btn btn-success btn-lg">
                <i class="fas fa-check-circle mr-2"></i>Complete Receiving
            </button>
        </div>
    </div>
</div>

<script>
    let currentPO = null;
    let expectedItems = [];

    // Load available POs when page loads
    document.addEventListener('DOMContentLoaded', function () {
        loadAvailablePOs();
    });

    // Load available purchase orders
    function loadAvailablePOs() {
        fetch('<?= URLROOT ?>/api/getAvailablePOs.php?status=ready_to_receive,receiving_in_progress,received,at_dock')
            .then(response => response.json())
            .then(data => {
                console.log('PO API Response:', data); // Debug log
                const selector = document.getElementById('po-selector');
                selector.innerHTML = '<option value="">Select a Purchase Order...</option>';

                if (data.success && data.data && data.data.length > 0) {
                    data.data.forEach(po => {
                        const option = document.createElement('option');
                        option.value = po.purchase_id;
                        option.textContent = `PO-${po.po_number} - ${po.supplier_name} ($${po.total_amount})`;
                        selector.appendChild(option);
                    });
                    console.log(`Loaded ${data.data.length} purchase orders`);
                } else {
                    console.log('No purchase orders available for receiving');
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No purchase orders ready for receiving';
                    option.disabled = true;
                    selector.appendChild(option);
                }
            })
            .catch(error => {
                console.error('Error loading POs:', error);
                const selector = document.getElementById('po-selector');
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Error loading purchase orders';
                option.disabled = true;
                selector.appendChild(option);
            });
    }

    // Handle PO selection
    document.getElementById('po-selector').addEventListener('change', function () {
        const loadBtn = document.getElementById('load-po-btn');
        loadBtn.disabled = !this.value;
    });

    // Load PO details
    document.getElementById('load-po-btn').addEventListener('click', function () {
        const poId = document.getElementById('po-selector').value;
        if (!poId) return;

        fetch('<?= URLROOT ?>/api/getPODetails.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ po_id: poId })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentPO = data.data.po;
                    expectedItems = data.data.items;
                    showStep2();
                } else {
                    alert('Error loading PO details: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading PO details');
            });
    });

    // Show step 2
    function showStep2() {
        // Update step indicators
        updateStepIndicator(1, 'completed');
        updateStepIndicator(2, 'active');

        // Hide step 1, show step 2
        document.getElementById('step-1-content').classList.add('d-none');
        document.getElementById('step-2-content').classList.remove('d-none');

        // Populate PO info
        const dockInfo = currentPO.dock_name ? `� ${currentPO.dock_name}` : '';
        const receivingAreaInfo = currentPO.receiving_area_name ? `📦 ${currentPO.receiving_area_name}` : '';
        const locationInfo = [dockInfo, receivingAreaInfo].filter(info => info).join(' | ');

        document.getElementById('po-info').innerHTML = `
        <div class="row">
            <div class="col-md-8">
                <strong>PO #${currentPO.po_number}</strong> - ${currentPO.supplier_name}<br>
                <small class="text-muted">Total: $${parseFloat(currentPO.total_amount).toFixed(2)} | Status: ${currentPO.status}</small>
            </div>
            <div class="col-md-4 text-right">
                <div class="text-primary font-weight-bold">${locationInfo}</div>
                <small class="text-muted">${currentPO.purchase_date}</small>
            </div>
        </div>
    `;

        // Populate products table
        const tbody = document.querySelector('#products-table tbody');
        tbody.innerHTML = '';

        expectedItems.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
            <td>
                <strong>${item.product_name}</strong><br>
                <small class="text-muted">SKU: ${item.sku || 'N/A'}</small>
            </td>
            <td>${item.quantity}</td>
            <td>
                <input type="number" class="form-control text-center font-weight-bold" 
                       style="width: 80px;" min="0" max="${item.quantity}" value="${item.quantity}"
                       data-index="${index}">
            </td>
            <td id="status-${index}">
                <span class="badge badge-warning">Pending</span>
            </td>
        `;
            tbody.appendChild(row);
        });

        // Add quantity change listeners
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('change', updateReceivingStatus);
        });

        updateReceivingStatus();
    }

    // Update receiving status
    function updateReceivingStatus() {
        let allValid = true;

        document.querySelectorAll('input[type="number"]').forEach(input => {
            const index = input.dataset.index;
            const receivedQty = parseInt(input.value) || 0;
            const expectedQty = parseInt(expectedItems[index].quantity);
            const statusCell = document.getElementById(`status-${index}`);

            if (receivedQty === expectedQty) {
                statusCell.innerHTML = '<span class="badge badge-success">Complete</span>';
            } else if (receivedQty > 0) {
                statusCell.innerHTML = '<span class="badge badge-warning">Partial</span>';
            } else {
                statusCell.innerHTML = '<span class="badge badge-secondary">Pending</span>';
                allValid = false;
            }

            expectedItems[index].received_qty = receivedQty;
        });

        document.getElementById('proceed-to-complete-btn').disabled = !allValid;
    }

    // Proceed to step 3
    document.getElementById('proceed-to-complete-btn').addEventListener('click', function () {
        // Update step indicators
        updateStepIndicator(2, 'completed');
        updateStepIndicator(3, 'active');

        // Hide step 2, show step 3
        document.getElementById('step-2-content').classList.add('d-none');
        document.getElementById('step-3-content').classList.remove('d-none');

        // Load location assignments
        loadLocationAssignments();
    });

    // Helper function to update step indicators
    function updateStepIndicator(stepNumber, status) {
        const stepElement = document.getElementById(`step-${stepNumber}`);
        const button = stepElement.querySelector('.btn');
        const text = stepElement.querySelector('div:last-child');

        if (status === 'active') {
            button.className = 'btn btn-primary rounded-circle mr-3';
            text.className = 'font-weight-bold text-primary';
        } else if (status === 'completed') {
            button.className = 'btn btn-success rounded-circle mr-3';
            text.className = 'font-weight-bold text-success';
        } else {
            button.className = 'btn btn-outline-secondary rounded-circle mr-3';
            text.className = 'text-muted';
        }
    }

    // Load available locations and create assignment interface
    function loadLocationAssignments() {
        fetch('<?= URLROOT ?>/api/getLocations.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    createLocationAssignmentInterface(data.data); // Use data.data instead of data.locations
                } else {
                    console.error('Failed to load locations');
                }
            })
            .catch(error => {
                console.error('Error loading locations:', error);
            });
    }

    // Create the location assignment interface
    function createLocationAssignmentInterface(locations) {
        const container = document.getElementById('location-assignments');
        let html = '';

        expectedItems.forEach((item, index) => {
            if (item.received_qty > 0) {
                html += `
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-4">
                            <strong>${item.product_name}</strong><br>
                            <small class="text-muted">SKU: ${item.sku}</small>
                        </div>
                        <div class="col-md-2">
                            <span class="badge badge-primary">${item.received_qty} units</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Assign to Location:</label>
                            <select class="form-control location-select" data-product-id="${item.product_id}">
                                <option value="">Select Location...</option>
                `;

                locations.forEach(location => {
                    html += `<option value="${location.location_id}">${location.location_code} - ${location.location_name}</option>`;
                });

                html += `
                            </select>
                        </div>
                    </div>
                `;
            }
        });

        container.innerHTML = html;
    }

    // Complete receiving
    document.getElementById('complete-receiving-btn').addEventListener('click', function () {
        const notes = document.getElementById('receiving-notes').value;

        // Collect location assignments
        const locationSelects = document.querySelectorAll('.location-select');
        const productLocations = {};

        locationSelects.forEach(select => {
            const productId = select.getAttribute('data-product-id');
            const locationId = select.value;
            if (locationId) {
                productLocations[productId] = locationId;
            }
        });

        // Add locations to products
        const productsWithLocations = expectedItems.map(item => ({
            ...item,
            location_id: productLocations[item.product_id] || null
        }));

        const requestData = {
            po_id: currentPO.purchase_id,
            po_number: currentPO.po_number,
            notes: notes,
            products: productsWithLocations
        };

        fetch('<?= URLROOT ?>/api/completeReceiving.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(requestData)
        })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.text(); // Get raw text first
            })
            .then(text => {
                console.log('Raw response:', text);
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        alert('Receiving completed successfully!');
                        // Reset form
                        location.reload();
                    } else {
                        alert('Error completing receiving: ' + data.message);
                    }
                } catch (parseError) {
                    console.error('JSON Parse Error:', parseError);
                    console.error('Raw response was:', text);
                    alert('Error: Invalid response format. Check console for details.');
                }
            })
            .catch(error => {
                console.error('Network Error:', error);
                alert('Network error completing receiving. Check console for details.');
            });
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>