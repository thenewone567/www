<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container">
    <!-- Page Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <h1 class="mb-0">
                <i class="fas fa-boxes"></i>
                Inventory Levels
            </h1>
            <p class="text-muted mb-0">Monitor inventory levels, movements, and warehouse locations</p>
        </div>
    </div>

    <!-- Inventory Levels Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card theme-card-light">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-inventory"></i> Current Inventory Levels</h4>
                    <a href="<?php echo URLROOT; ?>/inventory/add_inventory" class="btn btn-success">
                        <i class="fa fa-plus"></i> Add Inventory
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Inventory ID</th>
                                    <th>Product ID</th>
                                    <th>Batch Number</th>
                                    <th>Expiry Date</th>
                                    <th>Quantity</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['inventory'])): ?>
                                    <?php foreach ($data['inventory'] as $inventory): ?>
                                        <tr>
                                            <td><span class="badge badge-primary"><?php echo $inventory->Inventory_id; ?></span>
                                            </td>
                                            <td><strong><?php echo $inventory->product_id; ?></strong></td>
                                            <td><?php echo $inventory->batch_number ?: '<span class="text-muted">N/A</span>'; ?>
                                            </td>
                                            <td><?php echo $inventory->expiry_date ?: '<span class="text-muted">N/A</span>'; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-info"><?php echo $inventory->quantity; ?></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-warning"><?php echo $inventory->location_id; ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                            No inventory items found
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

    <!-- Inventory Movements Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card theme-card-light">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-exchange-alt"></i> Recent Inventory Movements</h4>
                    <a href="<?php echo URLROOT; ?>/inventory/move_inventory" class="btn btn-primary">
                        <i class="fa fa-exchange-alt"></i> Move Inventory
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Movement ID</th>
                                    <th>Product ID</th>
                                    <th>From Location</th>
                                    <th>To Location</th>
                                    <th>Quantity</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['movements'])): ?>
                                    <?php foreach ($data['movements'] as $movement): ?>
                                        <tr>
                                            <td><span class="badge badge-warning"><?php echo $movement->movement_id; ?></span>
                                            </td>
                                            <td><strong><?php echo $movement->product_id; ?></strong></td>
                                            <td><span
                                                    class="badge badge-secondary"><?php echo $movement->from_location_id; ?></span>
                                            </td>
                                            <td><span
                                                    class="badge badge-success"><?php echo $movement->to_location_id; ?></span>
                                            </td>
                                            <td><span class="badge badge-info"><?php echo $movement->quantity; ?></span></td>
                                            <td><small
                                                    class="text-muted"><?php echo date('M j, Y', strtotime($movement->movement_date)); ?></small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-exchange-alt fa-2x mb-2"></i><br>
                                            No inventory movements found
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

    <!-- Warehouse Locations Section -->
    <div class="row">
        <div class="col-12">
            <div class="card theme-card-light">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-map-marker-alt"></i> Warehouse Locations</h4>
                    <div>
                        <a href="<?php echo URLROOT; ?>/locations/location_barcodes" class="btn btn-warning mr-2">
                            <i class="fa fa-qrcode"></i> Location Barcodes
                        </a>
                        <a href="<?php echo URLROOT; ?>/locations/addlocation" class="btn btn-success">
                            <i class="fa fa-plus"></i> Add Location
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Location ID</th>
                                    <th>Name</th>
                                    <th>Rack</th>
                                    <th>Shelf</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['locations'])): ?>
                                    <?php foreach ($data['locations'] as $location): ?>
                                        <tr>
                                            <td><span class="badge badge-primary"><?php echo $location->location_id; ?></span>
                                            </td>
                                            <td><strong><?php echo $location->location_name; ?></strong></td>
                                            <td><?php echo $location->rack ?: '<span class="text-muted">N/A</span>'; ?></td>
                                            <td><?php echo $location->shelf ?: '<span class="text-muted">N/A</span>'; ?></td>
                                            <td><span class="badge badge-success">Available</span></td>
                                            <td>
                                                <a href="<?php echo URLROOT; ?>/locations/location_barcodes"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-barcode"></i> Barcode
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-map-marker-alt fa-2x mb-2"></i><br>
                                            No warehouse locations found
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

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>