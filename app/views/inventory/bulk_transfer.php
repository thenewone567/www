<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container theme-unified mt-0 pt-3">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <a href="<?php echo URLROOT; ?>/inventory" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Inventory
            </a>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h2 class="mb-0"><i class="fa-solid fa-truck-moving"></i> <?php echo $data['title']; ?></h2>
        </div>
    </div>

    <!-- Flash Message -->
    <?php flash('inventory_message'); ?>

    <!-- Instructions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-info-theme text-white">
                    <h6 class="mb-0"><i class="fa-solid fa-info-circle"></i> Bulk Location Transfer</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p class="mb-0">
                                <strong>Transfer items from bulk receiving locations to their final storage
                                    locations.</strong>
                                Items in bulk locations (B-001, B-002, etc.) should be moved to regular warehouse
                                locations for easy access during sales.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-right">
                            <small class="text-muted">
                                <i class="fa-solid fa-warehouse"></i>
                                <?php echo count($data['bulk_items']); ?> items in bulk locations
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Transfer Form -->
    <div class="row">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-warning-theme text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa-solid fa-boxes"></i> Items in Bulk Locations</h5>
                    <small class="text-white"><?php echo count($data['bulk_items']); ?> items</small>
                </div>
                <div class="card-body">
                    <?php if (empty($data['bulk_items'])): ?>
                        <div class="alert alert-success text-center">
                            <i class="fa-solid fa-check-circle fa-3x mb-3 text-success"></i>
                            <h5>No Items in Bulk Locations</h5>
                            <p>All items have been transferred to their final locations. Great job!</p>
                            <a href="<?php echo URLROOT; ?>/inventory" class="btn btn-primary">
                                <i class="fa-solid fa-box"></i> View Inventory
                            </a>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="<?php echo URLROOT; ?>/inventory/process_bulk_transfer">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>SKU</th>
                                            <th>Current Location</th>
                                            <th>Available Qty</th>
                                            <th>Transfer Qty</th>
                                            <th>Destination Location</th>
                                            <th>Batch</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['bulk_items'] as $item): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($item->product_name ?? 'Unknown Product'); ?></strong>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-secondary"><?php echo htmlspecialchars($item->sku ?? 'N/A'); ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-warning">
                                                        <?php echo htmlspecialchars($item->location_name); ?>
                                                    </span>
                                                    <small class="text-muted d-block">
                                                        <?php echo htmlspecialchars($item->rack ?? ''); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info"><?php echo $item->quantity; ?></span>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm" style="width: 100px;">
                                                        <input type="number"
                                                            name="transfers[<?php echo $item->Inventory_id; ?>][quantity]"
                                                            class="form-control" value="<?php echo $item->quantity; ?>" min="0"
                                                            max="<?php echo $item->quantity; ?>" step="1">
                                                    </div>
                                                </td>
                                                <td>
                                                    <select name="transfers[<?php echo $item->Inventory_id; ?>][to_location]"
                                                        class="form-control form-control-sm">
                                                        <option value="">Select location...</option>
                                                        <?php foreach ($data['regular_locations'] as $location): ?>
                                                            <option value="<?php echo $location->location_id; ?>">
                                                                <?php echo htmlspecialchars($location->location_name); ?>
                                                                (<?php echo htmlspecialchars($location->rack); ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($item->batch_number ?? 'N/A'); ?>
                                                    </small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <button type="button" class="btn btn-outline-secondary"
                                                onclick="selectAllItems()">
                                                <i class="fa-solid fa-check-double"></i> Select All Items
                                            </button>
                                            <button type="button" class="btn btn-outline-warning"
                                                onclick="clearSelections()">
                                                <i class="fa-solid fa-times"></i> Clear Selections
                                            </button>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-secondary mr-2"
                                                onclick="window.history.back()">
                                                <i class="fa-solid fa-times"></i> Cancel
                                            </button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fa-solid fa-truck-moving"></i> Transfer Items
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white">
                    <h6 class="mb-0"><i class="fa-solid fa-question-circle"></i> Transfer Guidelines</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6><i class="fa-solid fa-1"></i> Review Items</h6>
                            <p class="small text-muted">Check all items currently in bulk receiving locations.</p>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fa-solid fa-2"></i> Set Quantities</h6>
                            <p class="small text-muted">Enter the quantity to transfer (can be partial amounts).</p>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="fa-solid fa-3"></i> Choose Locations</h6>
                            <p class="small text-muted">Select the final storage location for each item.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function selectAllItems() {
        const locationSelects = document.querySelectorAll('select[name*="[to_location]"]');
        const quantityInputs = document.querySelectorAll('input[name*="[quantity]"]');

        // Set first available location for all items
        locationSelects.forEach(select => {
            if (select.options.length > 1) {
                select.selectedIndex = 1; // Select first non-empty option
            }
        });
    }

    function clearSelections() {
        const locationSelects = document.querySelectorAll('select[name*="[to_location]"]');
        const quantityInputs = document.querySelectorAll('input[name*="[quantity]"]');

        locationSelects.forEach(select => {
            select.selectedIndex = 0; // Reset to "Select location..."
        });

        quantityInputs.forEach(input => {
            input.value = 0; // Set quantity to 0
        });
    }
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>