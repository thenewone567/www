<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Add New Product</h1>
            <p class="text-muted">Create a new product in your inventory</p>
        </div>
        <a href="<?php echo URLROOT; ?>/products" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Products
        </a>
    </div>

    <!-- Alert Messages -->
    <?php if (!empty($data['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i><?php echo $data['success']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i><?php echo $data['error']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Main Form -->
    <form id="productForm" action="<?php echo URLROOT; ?>/products/add" method="post" enctype="multipart/form-data" data-verify="product" data-verify-redirect="<?php echo URLROOT; ?>/products">
        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Basic Information Card -->
                <div class="card mb-4 theme-card-light">
                    <div class="card-header theme-card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle text-primary mr-2"></i>Basic Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="product_name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?php echo (!empty($data['product_name_err'])) ? 'is-invalid' : ''; ?>" 
                                       id="product_name" 
                                       name="product_name" 
                                       value="<?php echo $data['product_name'] ?? ''; ?>" 
                                       placeholder="Enter product name" 
                                       required>
                                <?php if (!empty($data['product_name_err'])): ?>
                                    <div class="invalid-feedback"><?php echo $data['product_name_err']; ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?php echo (!empty($data['sku_err'])) ? 'is-invalid' : ''; ?>" 
                                       id="sku" 
                                       name="sku" 
                                       value="<?php echo $data['sku'] ?? ''; ?>" 
                                       placeholder="Product SKU" 
                                       required>
                                <?php if (!empty($data['sku_err'])): ?>
                                    <div class="invalid-feedback"><?php echo $data['sku_err']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="barcode" class="form-label">Barcode/UPC</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="barcode" 
                                       name="barcode" 
                                       value="<?php echo $data['barcode'] ?? ''; ?>" 
                                       placeholder="123456789012">
                                <small class="form-text text-muted">For barcode scanning</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="model_number" class="form-label">Model Number</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="model_number" 
                                       name="model_number" 
                                       value="<?php echo $data['model_number'] ?? ''; ?>" 
                                       placeholder="DCD771C2">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category & Supplier Card -->
                <div class="card mb-4 theme-card-light">
                    <div class="card-header theme-card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tags text-success mr-2"></i>Classification & Supplier
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-control dropdown-select <?php echo (!empty($data['category_id_err'])) ? 'is-invalid' : ''; ?>" 
                                        id="category_id" 
                                        name="category_id" 
                                        required>
                                    <option value="">Choose category...</option>
                                    <?php if (!empty($data['categories'])): ?>
                                        <?php foreach ($data['categories'] as $category): ?>
                                            <option value="<?php echo $category->category_id; ?>" 
                                                    <?php echo (($data['category_id'] ?? '') == $category->category_id) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category->category_name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <option value="add_new" class="text-primary">➕ Add New Category</option>
                                </select>
                                <?php if (!empty($data['category_id_err'])): ?>
                                    <div class="invalid-feedback"><?php echo $data['category_id_err']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select class="form-control dropdown-select <?php echo (!empty($data['supplier_id_err'])) ? 'is-invalid' : ''; ?>" 
                                        id="supplier_id" 
                                        name="supplier_id" 
                                        required>
                                    <option value="">Choose supplier...</option>
                                    <?php if (!empty($data['suppliers'])): ?>
                                        <?php foreach ($data['suppliers'] as $supplier): ?>
                                            <option value="<?php echo $supplier->supplier_id; ?>" 
                                                    <?php echo (($data['supplier_id'] ?? '') == $supplier->supplier_id) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($supplier->supplier_name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <option value="add_new" class="text-primary">➕ Add New Supplier</option>
                                </select>
                                <?php if (!empty($data['supplier_id_err'])): ?>
                                    <div class="invalid-feedback"><?php echo $data['supplier_id_err']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="brand_id" class="form-label">Brand</label>
                                <select class="form-control dropdown-select" 
                                        id="brand_id" 
                                        name="brand_id">
                                    <option value="">Choose brand...</option>
                                    <?php if (!empty($data['brands'])): ?>
                                        <?php foreach ($data['brands'] as $brand): ?>
                                            <option value="<?php echo $brand->brand_id; ?>" 
                                                    <?php echo (($data['brand_id'] ?? '') == $brand->brand_id) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($brand->brand_name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <option value="add_new" class="text-primary">➕ Add New Brand</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="supplier_code" class="form-label">Supplier Code</label>
                                <input type="text" 
                                       class="form-control bg-light" 
                                       id="supplier_code" 
                                       name="supplier_code" 
                                       value="<?php echo $data['supplier_code'] ?? ''; ?>" 
                                       placeholder="Auto-filled from supplier" 
                                       readonly>
                                <small class="form-text text-muted">Automatically filled when supplier is selected</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="unit_info" class="form-label">Unit Information</label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control" 
                                           id="unit_quantity" 
                                           name="unit_quantity" 
                                           value="<?php echo $data['unit_quantity'] ?? ''; ?>" 
                                           placeholder="Quantity" 
                                           step="0.001" 
                                           min="0">
                                    <div class="input-group-append">
                                        <select class="form-control" name="unit_id" id="unit_id">
                                            <option value="">Unit</option>
                                            <option value="1" <?php echo (($data['unit_id'] ?? '') == '1') ? 'selected' : ''; ?>>Pieces</option>
                                            <option value="2" <?php echo (($data['unit_id'] ?? '') == '2') ? 'selected' : ''; ?>>Grams</option>
                                            <option value="3" <?php echo (($data['unit_id'] ?? '') == '3') ? 'selected' : ''; ?>>Kilograms</option>
                                            <option value="4" <?php echo (($data['unit_id'] ?? '') == '4') ? 'selected' : ''; ?>>Liters</option>
                                            <option value="5" <?php echo (($data['unit_id'] ?? '') == '5') ? 'selected' : ''; ?>>Milliliters</option>
                                            <option value="6" <?php echo (($data['unit_id'] ?? '') == '6') ? 'selected' : ''; ?>>Meters</option>
                                            <option value="7" <?php echo (($data['unit_id'] ?? '') == '7') ? 'selected' : ''; ?>>Centimeters</option>
                                            <option value="8" <?php echo (($data['unit_id'] ?? '') == '8') ? 'selected' : ''; ?>>Boxes</option>
                                            <option value="9" <?php echo (($data['unit_id'] ?? '') == '9') ? 'selected' : ''; ?>>Packets</option>
                                            <option value="10" <?php echo (($data['unit_id'] ?? '') == '10') ? 'selected' : ''; ?>>Sets</option>
                                        </select>
                                    </div>
                                </div>
                                <small class="form-text text-muted">e.g., 250 grams, 5 pieces</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing Card -->
                <div class="card mb-4 theme-card-light">
                    <div class="card-header theme-card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-rupee-sign text-warning mr-2"></i>Pricing Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="purchase_price" class="form-label">Purchase Price (₹)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₹</span>
                                    </div>
                                    <input type="number" 
                                           class="form-control" 
                                           id="purchase_price" 
                                           name="purchase_price" 
                                           value="<?php echo $data['purchase_price'] ?? ''; ?>" 
                                           placeholder="0.00" 
                                           step="0.01" 
                                           min="0">
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label for="selling_price" class="form-label">Selling Price (₹)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₹</span>
                                    </div>
                                    <input type="number" 
                                           class="form-control" 
                                           id="selling_price" 
                                           name="selling_price" 
                                           value="<?php echo $data['selling_price'] ?? ''; ?>" 
                                           placeholder="0.00" 
                                           step="0.01" 
                                           min="0">
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label for="profit_margin" class="form-label">Profit Margin (%)</label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control bg-light" 
                                           id="profit_margin" 
                                           name="profit_margin" 
                                           value="<?php echo $data['profit_margin'] ?? ''; ?>" 
                                           readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Auto-calculated</small>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label for="gst_rate" class="form-label">GST Rate</label>
                                <select class="form-control" id="gst_rate" name="gst_rate">
                                    <option value="">Select GST</option>
                                    <option value="0" <?php echo (($data['gst_rate'] ?? '') == '0') ? 'selected' : ''; ?>>0% (Exempt)</option>
                                    <option value="5" <?php echo (($data['gst_rate'] ?? '') == '5') ? 'selected' : ''; ?>>5% GST</option>
                                    <option value="12" <?php echo (($data['gst_rate'] ?? '') == '12') ? 'selected' : ''; ?>>12% GST</option>
                                    <option value="18" <?php echo (($data['gst_rate'] ?? '') == '18') ? 'selected' : ''; ?>>18% GST</option>
                                    <option value="28" <?php echo (($data['gst_rate'] ?? '') == '28') ? 'selected' : ''; ?>>28% GST</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Quick Markup Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label">Quick Markup:</label>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary" onclick="applyMarkup(15)">15%</button>
                                    <button type="button" class="btn btn-outline-primary" onclick="applyMarkup(25)">25%</button>
                                    <button type="button" class="btn btn-outline-primary" onclick="applyMarkup(30)">30%</button>
                                    <button type="button" class="btn btn-outline-primary" onclick="applyMarkup(50)">50%</button>
                                    <button type="button" class="btn btn-outline-primary" onclick="applyMarkup(100)">100%</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Inventory Card -->
                <div class="card mb-4 theme-card-light">
                    <div class="card-header theme-card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-boxes text-info mr-2"></i>Inventory
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="initial_quantity" class="form-label">Initial Stock</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="initial_quantity" 
                                   name="initial_quantity" 
                                   value="<?php echo $data['initial_quantity'] ?? '0'; ?>" 
                                   min="0" 
                                   placeholder="0">
                        </div>
                        
                        <div class="mb-3">
                            <label for="min_Inventory_level" class="form-label">Minimum Level</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="min_Inventory_level" 
                                   name="min_Inventory_level" 
                                   value="<?php echo $data['min_Inventory_level'] ?? ''; ?>" 
                                   min="0" 
                                   placeholder="0">
                            <small class="form-text text-muted">Alert when stock is below this level</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="reorder_level" class="form-label">Reorder Level</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="reorder_level" 
                                   name="reorder_level" 
                                   value="<?php echo $data['reorder_level'] ?? ''; ?>" 
                                   min="0" 
                                   placeholder="0">
                        </div>
                        
                        <div class="mb-3">
                            <label for="storage_location" class="form-label">Storage Location</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="storage_location" 
                                   name="storage_location" 
                                   value="<?php echo $data['storage_location'] ?? ''; ?>" 
                                   placeholder="A1-B2-C3">
                        </div>
                    </div>
                </div>

                <!-- Product Details Card -->
                <div class="card mb-4 theme-card-light">
                    <div class="card-header theme-card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog text-secondary mr-2"></i>Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="dimensions" class="form-label">Dimensions</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="dimensions" 
                                   name="dimensions" 
                                   value="<?php echo $data['dimensions'] ?? ''; ?>" 
                                   placeholder="L x W x H (cm)">
                        </div>
                        
                        <div class="mb-3">
                            <label for="warranty_period" class="form-label">Warranty (months)</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="warranty_period" 
                                   name="warranty_period" 
                                   value="<?php echo $data['warranty_period'] ?? ''; ?>" 
                                   min="0" 
                                   placeholder="0">
                        </div>
                        
                        <div class="mb-3">
                            <label for="product_status" class="form-label">Status</label>
                            <select class="form-control" id="product_status" name="product_status">
                                <option value="active" <?php echo (($data['product_status'] ?? 'active') == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="discontinued" <?php echo (($data['product_status'] ?? '') == 'discontinued') ? 'selected' : ''; ?>>Discontinued</option>
                                <option value="seasonal" <?php echo (($data['product_status'] ?? '') == 'seasonal') ? 'selected' : ''; ?>>Seasonal</option>
                                <option value="special_order" <?php echo (($data['product_status'] ?? '') == 'special_order') ? 'selected' : ''; ?>>Special Order</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" 
                                   class="form-control-file" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*">
                            <small class="form-text text-muted">JPG, PNG files only</small>
                            <div id="imagePreview" class="mt-2" style="display: none;">
                                <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 150px;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card theme-card-light">
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            <button type="submit" class="btn btn-success btn-lg mb-2">
                                <i class="fas fa-save mr-2"></i>Save Product
                            </button>
                            <a href="<?php echo URLROOT; ?>/products" class="btn btn-outline-secondary">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dropdown functionality
    initializeDropdowns();
    
    // Initialize pricing calculations
    initializePricing();
    
    // Initialize image preview
    initializeImagePreview();
    
    // Initialize form validations
    initializeValidations();
});

// Simple dropdown initialization without complex libraries
function initializeDropdowns() {
    const dropdowns = document.querySelectorAll('.dropdown-select');
    
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('change', function() {
            if (this.value === 'add_new') {
                handleAddNew(this);
                this.value = ''; // Reset selection
            }
            
            // Handle supplier code update
            if (this.id === 'supplier_id') {
                updateSupplierCode(this.value);
            }
        });
    });
}

function handleAddNew(selectElement) {
    const type = selectElement.id.replace('_id', '');
    
    // Simple prompt for now - can be enhanced with modals later
    const name = prompt(`Enter new ${type} name:`);
    if (name && name.trim()) {
        // Add option to dropdown
        const option = document.createElement('option');
        option.value = 'temp_' + Date.now(); // Temporary ID
        option.text = name.trim();
        option.selected = true;
        
        // Insert before "Add New" option
        const addNewOption = selectElement.querySelector('option[value="add_new"]');
        selectElement.insertBefore(option, addNewOption);
        
        console.log(`Adding new ${type}: ${name}`);
    }
}

function initializePricing() {
    const purchasePrice = document.getElementById('purchase_price');
    const sellingPrice = document.getElementById('selling_price');
    const profitMargin = document.getElementById('profit_margin');
    
    function calculateMargin() {
        const purchase = parseFloat(purchasePrice.value) || 0;
        const selling = parseFloat(sellingPrice.value) || 0;
        
        if (purchase > 0 && selling > 0) {
            const margin = ((selling - purchase) / purchase * 100);
            profitMargin.value = margin.toFixed(2);
            
            // Color code the margin
            if (margin >= 30) {
                profitMargin.style.color = 'green';
            } else if (margin >= 15) {
                profitMargin.style.color = 'orange';
            } else {
                profitMargin.style.color = 'red';
            }
        } else {
            profitMargin.value = '';
            profitMargin.style.color = '';
        }
    }
    
    purchasePrice.addEventListener('input', calculateMargin);
    sellingPrice.addEventListener('input', calculateMargin);
    
    // Initial calculation
    calculateMargin();
}

function applyMarkup(percentage) {
    const purchasePrice = parseFloat(document.getElementById('purchase_price').value) || 0;
    
    if (purchasePrice > 0) {
        const sellingPrice = purchasePrice * (1 + percentage / 100);
        document.getElementById('selling_price').value = sellingPrice.toFixed(2);
        
        // Trigger calculation
        document.getElementById('selling_price').dispatchEvent(new Event('input'));
    } else {
        alert('Please enter a purchase price first');
    }
}

function updateSupplierCode(supplierId) {
    const supplierCodeField = document.getElementById('supplier_code');
    
    if (!supplierId || supplierId === 'add_new') {
        supplierCodeField.value = '';
        supplierCodeField.placeholder = 'Auto-filled from supplier';
        return;
    }
    
    // Show loading state
    supplierCodeField.placeholder = 'Loading...';
    
    // Simulate API call
    setTimeout(() => {
        supplierCodeField.value = 'SUP' + supplierId.padStart(4, '0');
        supplierCodeField.placeholder = 'Supplier code loaded';
    }, 500);
}

function initializeImagePreview() {
    const imageInput = document.getElementById('image');
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('preview');
    
    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });
}

function initializeValidations() {
    const form = document.getElementById('productForm');
    
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Basic validation
        const requiredFields = ['product_name', 'sku', 'category_id', 'supplier_id'];
        
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                const value = field.value.trim();
                
                if (!value) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields');
        }
    });
    
    // Remove validation styling when user types
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
}
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
