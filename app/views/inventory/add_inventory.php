<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container">
    <!-- Page Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-0">
                        <i class="fas fa-plus-circle"></i>
                        Add Inventory
                    </h1>
                    <p class="text-muted mb-0">Add new inventory items to inventory</p>
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
                    <form action="<?php echo URLROOT; ?>/inventory/add_inventory" method="post">
                        <div class="form-group">
                            <label for="product_id">Product ID: <sup class="text-danger">*</sup></label>
                            <input type="text" name="product_id"
                                class="form-control form-control-lg <?php echo (!empty($data['product_id_err'])) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $data['product_id']; ?>" placeholder="Enter product ID">
                            <span class="invalid-feedback"><?php echo $data['product_id_err']; ?></span>
                        </div>

                        <div class="form-group">
                            <label for="batch_number">Batch Number:</label>
                            <input type="text" name="batch_number" class="form-control form-control-lg"
                                value="<?php echo $data['batch_number']; ?>"
                                placeholder="Enter batch number (optional)">
                        </div>

                        <div class="form-group">
                            <label for="expiry_date">Expiry Date:</label>
                            <input type="date" name="expiry_date" class="form-control form-control-lg"
                                value="<?php echo $data['expiry_date']; ?>">
                        </div>

                        <div class="form-group">
                            <label for="quantity">Quantity: <sup class="text-danger">*</sup></label>
                            <input type="number" name="quantity"
                                class="form-control form-control-lg <?php echo (!empty($data['quantity_err'])) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $data['quantity']; ?>" placeholder="Enter quantity" min="1">
                            <span class="invalid-feedback"><?php echo $data['quantity_err']; ?></span>
                        </div>

                        <div class="form-group">
                            <label for="location_id">Location ID:</label>
                            <input type="text" name="location_id" class="form-control form-control-lg"
                                value="<?php echo $data['location_id']; ?>"
                                placeholder="Enter location ID (e.g., A-001)">
                            <small class="form-text text-muted">Leave empty for default location</small>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-plus"></i> Add Inventory
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