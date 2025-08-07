<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container">
    <!-- Page Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-0">
                        <i class="fas fa-exchange-alt"></i>
                        Move Inventory
                    </h1>
                    <p class="text-muted mb-0">Transfer Inventory from one location to another</p>
                </div>
                <a href="<?php echo URLROOT; ?>/inventory/inventory_levels" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back to Inventory
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card theme-card-light">
                <div class="card-body">
                    <form action="<?php echo URLROOT; ?>/inventory/move_inventory" method="post" data-verify="inventory"
                        data-verify-redirect="<?php echo URLROOT; ?>/inventory">
                        <div class="form-group">
                            <label for="product_id">Product ID: <sup class="text-danger">*</sup></label>
                            <input type="text" name="product_id"
                                class="form-control form-control-lg <?php echo (!empty($data['product_id_err'])) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $data['product_id']; ?>" placeholder="Enter product ID">
                            <span class="invalid-feedback"><?php echo $data['product_id_err']; ?></span>
                        </div>

                        <div class="form-group">
                            <label for="from_location_id">From Location: <sup class="text-danger">*</sup></label>
                            <input type="text" name="from_location_id" class="form-control form-control-lg"
                                value="<?php echo $data['from_location_id']; ?>"
                                placeholder="Source location (e.g., A-001)">
                            <small class="form-text text-muted">Location where the Inventory is currently stored</small>
                        </div>

                        <div class="form-group">
                            <label for="to_location_id">To Location: <sup class="text-danger">*</sup></label>
                            <input type="text" name="to_location_id" class="form-control form-control-lg"
                                value="<?php echo $data['to_location_id']; ?>"
                                placeholder="Destination location (e.g., B-002)">
                            <small class="form-text text-muted">Location where the Inventory will be moved</small>
                        </div>

                        <div class="form-group">
                            <label for="quantity">Quantity: <sup class="text-danger">*</sup></label>
                            <input type="number" name="quantity"
                                class="form-control form-control-lg <?php echo (!empty($data['quantity_err'])) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $data['quantity']; ?>" placeholder="Enter quantity to move" min="1">
                            <span class="invalid-feedback"><?php echo $data['quantity_err']; ?></span>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-exchange-alt"></i> Move Inventory
                            </button>
                            <a href="<?php echo URLROOT; ?>/inventory/inventory_levels"
                                class="btn btn-secondary btn-lg ml-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>