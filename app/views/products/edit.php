<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<div class="edit-product-top container-fluid mt-0 pt-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <a href="<?php echo URLROOT; ?>/products" class="btn btn-light"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h2 class="mb-0">Edit Product</h2>
        </div>
    </div>
</div>
<div class="card card-body theme-card-light mt-3">
    <p>Edit the product with this form</p>
    <?php if (!empty($data['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $data['success']; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>
    <?php if (!empty($data['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $data['error']; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>
    <form id="editProductForm" action="<?php echo URLROOT; ?>/products/edit/<?php echo $data['id']; ?>" method="post"
        enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-3 col-6 mb-3">
                <label for="product_name">Product Name: <sup>*</sup></label>
                <input type="text" name="product_name"
                    class="form-control form-control-lg <?php echo (!empty($data['product_name_err'])) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $data['product_name']; ?>" required>
                <span class="invalid-feedback"><?php echo $data['product_name_err']; ?></span>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="sku">SKU: <sup>*</sup></label>
                <input type="text" name="sku"
                    class="form-control form-control-lg <?php echo (!empty($data['sku_err'])) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $data['sku']; ?>" required>
                <span class="invalid-feedback"><?php echo $data['sku_err']; ?></span>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="category_id">Category: <sup>*</sup></label>
                <select name="category_id"
                    class="form-control form-control-lg searchable-dropdown <?php echo (!empty($data['category_id_err'])) ? 'is-invalid' : ''; ?>"
                    required>
                    <option value="">Select Category</option>
                    <?php if (!empty($data['categories'])): ?>
                        <?php foreach ($data['categories'] as $category): ?>
                            <option value="<?php echo $category->category_id; ?>" <?php echo ($data['category_id'] == $category->category_id) ? 'selected' : ''; ?>>
                                <?php echo $category->category_name; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <span class="invalid-feedback"><?php echo $data['category_id_err']; ?></span>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="brand_id">Brand: <sup>*</sup></label>
                <select name="brand_id"
                    class="form-control form-control-lg searchable-dropdown <?php echo (!empty($data['brand_id_err'])) ? 'is-invalid' : ''; ?>"
                    required>
                    <option value="">Select Brand</option>
                    <?php if (!empty($data['brands'])): ?>
                        <?php foreach ($data['brands'] as $brand): ?>
                            <option value="<?php echo $brand->brand_id; ?>" <?php echo ($data['brand_id'] == $brand->brand_id) ? 'selected' : ''; ?>>
                                <?php echo $brand->brand_name; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <span class="invalid-feedback"><?php echo $data['brand_id_err']; ?></span>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="current_quantity">Current Inventory Quantity:</label>
                <input type="number" name="current_quantity"
                    class="form-control form-control-lg <?php echo (!empty($data['current_quantity_err'])) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $data['current_quantity'] ?? 0; ?>" min="0">
                <span class="invalid-feedback"><?php echo $data['current_quantity_err']; ?></span>
                <small class="form-text text-muted">Current Inventory level</small>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="unit_id">Unit: <sup>*</sup></label>
                <select name="unit_id"
                    class="form-control form-control-lg searchable-dropdown <?php echo (!empty($data['unit_id_err'])) ? 'is-invalid' : ''; ?>"
                    required>
                    <option value="">Select Unit</option>
                    <?php if (!empty($data['units'])): ?>
                        <?php foreach ($data['units'] as $unit): ?>
                            <option value="<?php echo $unit->unit_id; ?>" <?php echo ($data['unit_id'] == $unit->unit_id) ? 'selected' : ''; ?>>
                                <?php echo $unit->unit_name; ?>        <?php echo !empty($unit->abbreviation) ? ' (' . $unit->abbreviation . ')' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <span class="invalid-feedback"><?php echo $data['unit_id_err']; ?></span>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="min_Inventory_level">Min Inventory Level: <sup>*</sup></label>
                <input type="number" name="min_Inventory_level" class="form-control form-control-lg"
                    value="<?php echo $data['min_Inventory_level']; ?>" required>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="max_Inventory_level">Max Inventory Level: <sup>*</sup></label>
                <input type="number" name="max_Inventory_level" class="form-control form-control-lg"
                    value="<?php echo $data['max_Inventory_level']; ?>" required>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="reorder_level">Reorder Level: <sup>*</sup></label>
                <input type="number" name="reorder_level" class="form-control form-control-lg"
                    value="<?php echo $data['reorder_level']; ?>" required>
            </div>
            <div class="col-md-12 mb-3">
                <label for="image_path">Product Image:</label>
                <?php if (!empty($data['image_path'])): ?>
                    <div class="mb-2">
                        <small class="text-muted">Current image: <?php echo $data['image_path']; ?></small>
                    </div>
                <?php endif; ?>
                <input type="file" name="image_path" class="form-control-file">
                <small class="form-text text-muted">Leave empty to keep current image</small>
            </div>
        </div>

        <!-- Inventory Location Management Section -->
        <div class="row">
            <div class="col-12">
                <h4 class="mt-4 mb-3"><i class="fas fa-map-marker-alt"></i> Inventory Location Management</h4>
                
                <!-- Current Inventory Locations -->
                <?php if (!empty($data['Inventory_locations'])): ?>
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">Current Inventory Locations</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($data['Inventory_locations'] as $index => $Inventory): ?>
                                <div class="row mb-3 border rounded p-3 theme-row-striped">
                                    <div class="col-md-3">
                                        <label>Location:</label>
                                        <select name="Inventory_locations[<?php echo $Inventory->Inventory_id; ?>][location_id]" class="form-control searchable-dropdown">
                                            <option value="">Select Location</option>
                                            <?php if (!empty($data['locations'])): ?>
                                                <?php foreach ($data['locations'] as $location): ?>
                                                    <option value="<?php echo $location->location_id; ?>" 
                                                            <?php echo ($Inventory->location_id == $location->location_id) ? 'selected' : ''; ?>>
                                                        <?php echo $location->location_code; ?> 
                                                        (<?php echo $location->section . $location->aisle . $location->bin; ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Quantity:</label>
                                        <input type="number" 
                                               name="Inventory_locations[<?php echo $Inventory->Inventory_id; ?>][quantity]" 
                                               class="form-control" 
                                               value="<?php echo $Inventory->quantity; ?>" 
                                               min="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Batch Number:</label>
                                        <input type="text" class="form-control" 
                                               value="<?php echo $Inventory->batch_number; ?>" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Current Location:</label>
                                        <div class="form-control-plaintext">
                                            <?php if ($Inventory->location_code): ?>
                                                <code><?php echo $Inventory->location_code; ?></code>
                                                <small class="text-muted d-block">
                                                    <?php echo $Inventory->section . $Inventory->aisle . $Inventory->bin; ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-warning">No location assigned</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="removeInventoryLocation(this)" title="Remove this Inventory entry">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> This product has no current Inventory entries. Add Inventory below to assign a location.
                    </div>
                <?php endif; ?>

                <!-- Add New Inventory Location -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Add New Inventory Location</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="new_Inventory_location">Warehouse Location:</label>
                                <select name="new_Inventory_location" id="new_Inventory_location" class="form-control searchable-dropdown">
                                    <option value="">Select Location</option>
                                    <?php if (!empty($data['locations'])): ?>
                                        <?php foreach ($data['locations'] as $location): ?>
                                            <option value="<?php echo $location->location_id; ?>">
                                                <?php echo $location->location_code; ?> 
                                                (<?php echo $location->section . $location->aisle . $location->bin; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="new_Inventory_quantity">Quantity:</label>
                                <input type="number" name="new_Inventory_quantity" id="new_Inventory_quantity" 
                                       class="form-control" min="0" placeholder="Enter quantity">
                            </div>
                            <div class="col-md-5">
                                <label>&nbsp;</label>
                                <div class="form-control-plaintext">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Adding Inventory here will create a new Inventory entry for this location.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="submit" class="btn btn-success" value="Update Product">
    </form>
</div>
<script>
    document.getElementById('editProductForm').addEventListener('submit', function (e) {
        if (!confirm('Are you sure you want to update this product?')) {
            e.preventDefault();
        }
    });

    function removeInventoryLocation(button) {
        if (confirm('Are you sure you want to remove this Inventory location? This will delete the Inventory entry.')) {
            const row = button.closest('.row');
            row.style.display = 'none';
            
            // Find the location and quantity inputs in this row and set them to empty
            const locationSelect = row.querySelector('select[name*="[location_id]"]');
            const quantityInput = row.querySelector('input[name*="[quantity]"]');
            
            if (locationSelect) locationSelect.value = '';
            if (quantityInput) quantityInput.value = '0';
            
            // Add a hidden field to mark this Inventory for deletion
            const deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = locationSelect.name.replace('[location_id]', '[delete]');
            deleteInput.value = '1';
            row.appendChild(deleteInput);
        }
    }

    // Auto-calculate total quantity when individual Inventory quantities change
    function updateTotalQuantity() {
        const quantityInputs = document.querySelectorAll('input[name*="[quantity]"]:not([name="new_Inventory_quantity"])');
        const newQuantityInput = document.querySelector('input[name="new_Inventory_quantity"]');
        const currentQuantityInput = document.querySelector('input[name="current_quantity"]');
        
        let total = 0;
        quantityInputs.forEach(input => {
            if (input.style.display !== 'none' && input.value) {
                total += parseInt(input.value) || 0;
            }
        });
        
        if (newQuantityInput && newQuantityInput.value) {
            total += parseInt(newQuantityInput.value) || 0;
        }
        
        if (currentQuantityInput) {
            currentQuantityInput.value = total;
        }
    }

    // Add event listeners to quantity inputs
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInputs = document.querySelectorAll('input[name*="[quantity]"], input[name="new_Inventory_quantity"]');
        quantityInputs.forEach(input => {
            input.addEventListener('input', updateTotalQuantity);
        });
        
        // Auto-capitalize product name on blur
        const productNameField = document.querySelector('input[name="product_name"]');
        if (productNameField) {
            productNameField.addEventListener('blur', function () {
                if (this.value) {
                    this.value = capitalizeProductName(this.value);
                }
            });
        }

        // Auto-uppercase SKU for consistency
        const skuField = document.querySelector('input[name="sku"]');
        if (skuField) {
            skuField.addEventListener('blur', function () {
                if (this.value) {
                    this.value = this.value.toUpperCase();
                }
            });
        }

        // Auto-clean barcode (remove spaces and non-numeric characters)
        const barcodeField = document.querySelector('input[name="barcode"]');
        if (barcodeField) {
            barcodeField.addEventListener('blur', function () {
                if (this.value) {
                    this.value = formatBarcode(this.value);
                }
            });
        }

        // Auto-uppercase model number for consistency
        const modelNumberField = document.querySelector('input[name="model_number"]');
        if (modelNumberField) {
            modelNumberField.addEventListener('blur', function () {
                if (this.value) {
                    this.value = this.value.toUpperCase();
                }
            });
        }

        // Auto-uppercase supplier code for consistency
        const supplierCodeField = document.querySelector('input[name="supplier_code"]');
        if (supplierCodeField) {
            supplierCodeField.addEventListener('blur', function () {
                if (this.value) {
                    this.value = this.value.toUpperCase();
                }
            });
        }

        // Auto-format dimensions for consistency
        const dimensionsField = document.querySelector('input[name="dimensions"]');
        if (dimensionsField) {
            dimensionsField.addEventListener('blur', function () {
                if (this.value) {
                    this.value = formatDimensions(this.value);
                }
            });
        }

        // Auto-uppercase storage location for consistency
        const storageLocationField = document.querySelector('input[name="storage_location"]');
        if (storageLocationField) {
            storageLocationField.addEventListener('blur', function () {
                if (this.value) {
                    this.value = this.value.toUpperCase();
                }
            });
        }
        
        // Initial calculation
        updateTotalQuantity();
    });

    // Capitalize product name (First letter of each word)
    function capitalizeProductName(str) {
        return str.toLowerCase().replace(/\b\w/g, function(letter) {
            return letter.toUpperCase();
        });
    }

    // Capitalize first letter of each word (reusable for names)
    function capitalizeWords(str) {
        return str.toLowerCase().replace(/\b\w/g, function(letter) {
            return letter.toUpperCase();
        });
    }

    // Format dimensions consistently (15 x 10 x 5 cm → 15 X 10 X 5 CM)
    function formatDimensions(str) {
        return str.toUpperCase()
                 .replace(/\s*x\s*/gi, ' X ')  // Replace x with X and normalize spacing
                 .replace(/\s+/g, ' ')         // Remove extra spaces
                 .trim();                      // Remove leading/trailing spaces
    }

    // Format barcode (remove spaces and non-numeric characters)
    function formatBarcode(str) {
        // Remove all spaces and keep only digits
        return str.replace(/\D/g, '');
    }
</script>

            </div> <!-- End container-fluid -->
        </div> <!-- End page-content-wrapper -->
    </div> <!-- End wrapper -->

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>