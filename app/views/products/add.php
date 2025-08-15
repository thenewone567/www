<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<?php
// Defensive programming - ensure data structure is correct
$categories = isset($data['categories']) && is_array($data['categories']) ? $data['categories'] : [];
$suppliers = isset($data['suppliers']) && is_array($data['suppliers']) ? $data['suppliers'] : [];
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Add New Product</h1>
            <p class="text-muted">Create a new product - pricing and stock will be managed through supplier and receiving workflows</p>
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
    <form id="productForm" action="<?php echo URLROOT; ?>/products/add" method="post" enctype="multipart/form-data">
        <!-- Top Row - Full Width Product Information -->
        <div class="row mb-4">
            <div class="col-12">
                <!-- Product Information Card -->
                <div class="card-theme mb-4 theme-card-light">
                    <div class="card-header theme-card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle text-primary mr-2"></i>Product Information
                        </h5>
                    </div>
                    <div class="card-body py-3">
                        <!-- Basic Information Row -->
                        <div class="row mb-2">
                            <div class="col-md-2 mb-2">
                                <label for="sku" class="form-label small">SKU/UPC<span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control form-control-sm <?php echo (!empty($data['sku_err'])) ? 'is-invalid' : ''; ?>" 
                                       id="sku" 
                                       name="sku" 
                                       value="<?php echo $data['sku'] ?? ''; ?>" 
                                       placeholder="Scan your barcode here" 
                                       required>
                                <?php if (!empty($data['sku_err'])): ?>
                                    <div class="invalid-feedback"><?php echo $data['sku_err']; ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="product_name" class="form-label small">Product Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control form-control-sm <?php echo (!empty($data['product_name_err'])) ? 'is-invalid' : ''; ?>" 
                                       id="product_name" 
                                       name="product_name" 
                                       value="<?php echo $data['product_name'] ?? ''; ?>" 
                                       placeholder="Enter product name" 
                                       required>
                                <?php if (!empty($data['product_name_err'])): ?>
                                    <div class="invalid-feedback"><?php echo $data['product_name_err']; ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="category_id" class="form-label small">Category</label>
                                <div class="input-group input-group-sm">
                                    <select class="form-control <?php echo (!empty($data['category_id_err'])) ? 'is-invalid' : ''; ?>" 
                                            id="category_id" 
                                            name="category_id">
                                        <option value="">Select category (optional)...</option>
                                        <?php if (!empty($data['categories'])): ?>
                                            <?php foreach ($categories as $category): ?>
                                                <?php if (is_object($category)): ?>
                                                <option value="<?php echo $category->category_id; ?>"
                                                        <?php echo (($data['category_id'] ?? '') == $category->category_id) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category->category_name); ?>
                                                </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="input-group-append">
                                        <a href="<?php echo URLROOT; ?>/categories" class="btn btn-outline-primary btn-sm" title="Manage Categories">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php if (!empty($data['category_id_err'])): ?>
                                    <div class="invalid-feedback"><?php echo $data['category_id_err']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Secondary Information Row -->
                        <div class="row mb-2">
                            <div class="col-md-4 mb-2">
                                <label for="model_number" class="form-label small">Model/Batch Number</label>
                                <input type="text" 
                                       class="form-control form-control-sm" 
                                       id="model_number" 
                                       name="model_number" 
                                       value="<?php echo $data['model_number'] ?? ''; ?>" 
                                       placeholder="e.g., DCD771C2, A1234-X567">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="product_status" class="form-label small">Status</label>
                                <select class="form-control form-control-sm" id="product_status" name="product_status">
                                    <option value="active" <?php echo (($data['product_status'] ?? 'active') == 'active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="discontinued" <?php echo (($data['product_status'] ?? '') == 'discontinued') ? 'selected' : ''; ?>>Discontinued</option>
                                    <option value="seasonal" <?php echo (($data['product_status'] ?? '') == 'seasonal') ? 'selected' : ''; ?>>Seasonal</option>
                                    <option value="special_order" <?php echo (($data['product_status'] ?? '') == 'special_order') ? 'selected' : ''; ?>>Special Order</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="product_type" class="form-label small">Product Type</label>
                                <select class="form-control form-control-sm <?php echo (!empty($data['product_type_err'])) ? 'is-invalid' : ''; ?>" 
                                        id="product_type" 
                                        name="product_type">
                                    <option value="STANDARD" <?php echo (($data['product_type'] ?? 'STANDARD') == 'STANDARD') ? 'selected' : ''; ?>>📦 Standard</option>
                                    <option value="BULK" <?php echo (($data['product_type'] ?? '') == 'BULK') ? 'selected' : ''; ?>>📦📦 Bulk Item</option>
                                    <option value="OVERSIZED" <?php echo (($data['product_type'] ?? '') == 'OVERSIZED') ? 'selected' : ''; ?>>📏 Oversized</option>
                                    <option value="FRAGILE" <?php echo (($data['product_type'] ?? '') == 'FRAGILE') ? 'selected' : ''; ?>>🔸 Fragile</option>
                                    <option value="HAZMAT" <?php echo (($data['product_type'] ?? '') == 'HAZMAT') ? 'selected' : ''; ?>>⚠️ Hazardous Material</option>
                                </select>
                                <?php if (!empty($data['product_type_err'])): ?>
                                    <div class="invalid-feedback"><?php echo $data['product_type_err']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Dimensions Row -->
                        <div class="row mb-2">
                            <div class="col-12 mb-3">
                                <div class="d-flex align-items-center">
                                    <label class="form-label small mb-0 mr-3">Dimensions</label>
                                    <div class="unit-system-toggle">
                                        <div class="toggle-container">
                                            <span class="toggle-label" id="metric-label">Metric</span>
                                            <div class="toggle-switch" onclick="toggleUnitSystem()">
                                                <div class="toggle-slider" id="unit-slider"></div>
                                            </div>
                                            <span class="toggle-label" id="imperial-label">Imperial</span>
                                        </div>
                                        <input type="hidden" id="current-unit-system" value="metric">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-2">
                                <label for="width" class="form-label small">Width</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" 
                                           class="form-theme" 
                                           id="width" 
                                           name="width" 
                                           value="<?php echo $data['width'] ?? ''; ?>" 
                                           placeholder="Width" 
                                           step="0.01" 
                                           min="0">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="width_unit_display">cm</span>
                                    </div>
                                </div>
                                <input type="hidden" name="width_unit" id="width_unit" value="cm">
                            </div>
                            
                            <div class="col-md-3 mb-2">
                                <label for="height" class="form-label small">Height</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" 
                                           class="form-theme" 
                                           id="height" 
                                           name="height" 
                                           value="<?php echo $data['height'] ?? ''; ?>" 
                                           placeholder="Height" 
                                           step="0.01" 
                                           min="0">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="height_unit_display">cm</span>
                                    </div>
                                </div>
                                <input type="hidden" name="height_unit" id="height_unit" value="cm">
                            </div>
                            
                            <div class="col-md-3 mb-2">
                                <label for="length" class="form-label small">Length</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" 
                                           class="form-theme" 
                                           id="length" 
                                           name="length" 
                                           value="<?php echo $data['length'] ?? ''; ?>" 
                                           placeholder="Length" 
                                           step="0.01" 
                                           min="0">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="length_unit_display">cm</span>
                                    </div>
                                </div>
                                <input type="hidden" name="length_unit" id="length_unit" value="cm">
                            </div>
                            
                            <div class="col-md-3 mb-2">
                                <label for="weight" class="form-label small">Weight</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" 
                                           class="form-theme" 
                                           id="weight" 
                                           name="weight" 
                                           value="<?php echo $data['weight'] ?? ''; ?>" 
                                           placeholder="Weight" 
                                           step="0.01" 
                                           min="0">
                                    <div class="input-group-append">
                                        <select class="form-control form-control-sm" name="weight_unit" id="weight_unit">
                                            <option value="">Unit</option>
                                            <option value="g" <?php echo (($data['weight_unit'] ?? '') == 'g') ? 'selected' : ''; ?>>grams</option>
                                            <option value="kg" <?php echo (($data['weight_unit'] ?? '') == 'kg') ? 'selected' : ''; ?>>kg</option>
                                            <option value="lb" <?php echo (($data['weight_unit'] ?? '') == 'lb') ? 'selected' : ''; ?>>lbs</option>
                                            <option value="oz" <?php echo (($data['weight_unit'] ?? '') == 'oz') ? 'selected' : ''; ?>>oz</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Conversion Hint -->
                        <div class="row mb-2">
                            <div class="col-12">
                                <div id="conversion_hint" class="text-center">
                                    <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>1 cm = 0.39 inches, 1 kg = 2.2 lbs</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Product Information Row -->
                        <div class="row mb-2">
                            <div class="col-md-5 mb-2">
                                <label for="image" class="form-label small">Product Image</label>
                                <input type="file" 
                                       class="form-control-file form-control-sm" 
                                       id="image" 
                                       name="image" 
                                       accept="image/*">
                                <small class="form-text text-muted">JPG, PNG files only</small>
                                <div id="imagePreview" class="mt-2" style="display: none;">
                                    <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 80px;">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Expiry and Warranty Information -->
                        <div class="row mb-2">
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="has_expiry" 
                                           name="has_expiry" 
                                           value="1"
                                           <?php echo (!empty($data['has_expiry']) && $data['has_expiry']) ? 'checked' : ''; ?>
                                           onchange="toggleExpiryFields()">
                                    <label class="form-check-label small" for="has_expiry">
                                        <i class="fas fa-calendar-times mr-1"></i>Product has expiry date
                                    </label>
                                </div>
                                
                                <div id="expiry_months_field" style="display: none;" class="mt-2">
                                    <label for="expiry_months" class="form-label small">Shelf Life (Months)</label>
                                    <input type="number" 
                                           class="form-control form-control-sm" 
                                           id="expiry_months" 
                                           name="expiry_months" 
                                           value="<?php echo $data['expiry_months'] ?? ''; ?>" 
                                           min="1" 
                                           max="120"
                                           placeholder="1-120">
                                    <small class="form-text text-muted">Total months until expiry</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="has_warranty" 
                                           name="has_warranty" 
                                           value="1"
                                           <?php echo (!empty($data['has_warranty']) && $data['has_warranty']) ? 'checked' : ''; ?>
                                           onchange="toggleWarrantyFields()">
                                    <label class="form-check-label small" for="has_warranty">
                                        <i class="fas fa-shield-alt mr-1"></i>Product has warranty
                                    </label>
                                </div>
                                
                                <div id="warranty_months_field" style="display: none;" class="mt-2">
                                    <label for="warranty_period" class="form-label small">Warranty (Months)</label>
                                    <input type="number" 
                                           class="form-control form-control-sm" 
                                           id="warranty_period" 
                                           name="warranty_period" 
                                           value="<?php echo $data['warranty_period'] ?? ''; ?>" 
                                           min="1" 
                                           max="120"
                                           placeholder="1-120">
                                    <small class="form-text text-muted">Warranty period in months</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-2" id="expiry_example_row" style="display: none;">
                            <div class="col-md-12 mb-2" id="expiry_example">
                                <label class="form-label small">Example Expiry</label>
                                <div class="alert alert-info alert-sm py-1 px-2">
                                    <small id="expiry_calculation">-</small>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bottom Row - 2 Columns -->
        <div class="row">
            <div class="col-lg-6 d-flex">
                <!-- Tax Information Card -->
                <div class="card-theme mb-4 theme-card-light flex-fill">
                    <div class="card-header theme-card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-receipt text-warning mr-2"></i>Tax Information
                        </h5>
                    </div>
                    <div class="card-body py-3">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label for="gst_rate" class="form-label small">GST Rate <span class="text-danger">*</span></label>
                                <select class="form-control form-control-sm <?php echo (!empty($data['gst_rate_err'])) ? 'is-invalid' : ''; ?>" 
                                        id="gst_rate" 
                                        name="gst_rate" 
                                        required>
                                    <option value="">Select GST Rate</option>
                                    <option value="0" <?php echo (($data['gst_rate'] ?? '') == '0') ? 'selected' : ''; ?>>0% (Exempt)</option>
                                    <option value="5" <?php echo (($data['gst_rate'] ?? '') == '5') ? 'selected' : ''; ?>>5% GST</option>
                                    <option value="12" <?php echo (($data['gst_rate'] ?? '') == '12') ? 'selected' : ''; ?>>12% GST</option>
                                    <option value="18" <?php echo (($data['gst_rate'] ?? '') == '18') ? 'selected' : ''; ?>>18% GST</option>
                                    <option value="28" <?php echo (($data['gst_rate'] ?? '') == '28') ? 'selected' : ''; ?>>28% GST</option>
                                </select>
                                <?php if (!empty($data['gst_rate_err'])): ?>
                                    <div class="invalid-feedback"><?php echo $data['gst_rate_err']; ?></div>
                                <?php endif; ?>
                                <small class="form-text text-muted">Goods and Services Tax rate</small>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning mt-3 mb-0 py-2">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <small><strong>Important:</strong> GST rate should match the tax classification for this product category as per GST regulations.</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 d-flex">
                <!-- Inventory Card -->
                <div class="card-theme mb-4 theme-card-light flex-fill">
                    <div class="card-header theme-card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-boxes text-info mr-2"></i>Inventory Management
                        </h5>
                    </div>
                    <div class="card-body py-3">
                        <!-- Inventory Levels Row -->
                        <div class="row mb-2">
                            <div class="col-md-4 mb-2">
                                <label for="min_inventory_level" class="form-label small">Min Level</label>
                                <input type="number" 
                                       class="form-control form-control-sm" 
                                       id="min_inventory_level" 
                                       name="min_inventory_level" 
                                       value="<?php echo $data['min_inventory_level'] ?? ''; ?>" 
                                       min="0" 
                                       placeholder="0">
                                <small class="form-text text-muted">Alert threshold</small>
                            </div>
                            
                            <div class="col-md-4 mb-2">
                                <label for="reorder_level" class="form-label small">Reorder</label>
                                <input type="number" 
                                       class="form-control form-control-sm" 
                                       id="reorder_level" 
                                       name="reorder_level" 
                                       value="<?php echo $data['reorder_level'] ?? ''; ?>" 
                                       min="0" 
                                       placeholder="0">
                                <small class="form-text text-muted">When to reorder</small>
                            </div>
                            
                            <div class="col-md-4 mb-2">
                                <label for="max_inventory_level" class="form-label small">Max Level</label>
                                <input type="number" 
                                       class="form-control form-control-sm" 
                                       id="max_inventory_level" 
                                       name="max_inventory_level" 
                                       value="<?php echo $data['max_inventory_level'] ?? ''; ?>" 
                                       min="0" 
                                       placeholder="0">
                                <small class="form-text text-muted">Upper limit</small>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning mb-0 py-2">
                            <i class="fas fa-lightbulb mr-2"></i>
                            <small><strong>Tip:</strong> Set minimum level for alerts, reorder level for purchasing, and maximum level to prevent overstocking.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Full Width Section for Actions -->
        <div class="row">
            <div class="col-12">
                <!-- Action Buttons -->
                <div class="card-theme theme-card-light">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success mr-2">
                                <i class="fas fa-save mr-2"></i>Save Product
                            </button>
                            <a href="<?php echo URLROOT; ?>/products" class="btn-theme btn-danger-theme">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Custom CSS for searchable dropdowns -->
<style>
/* Modern Unit System Toggle */
.unit-system-toggle {
    margin-left: 15px;
}

.toggle-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

.toggle-label {
    font-size: 12px;
    font-weight: 500;
    color: #6c757d;
    transition: color 0.3s ease;
}

.toggle-label.active {
    color: #28a745;
    font-weight: 600;
}

.toggle-label.active.imperial {
    color: #007bff;
}

.toggle-switch {
    position: relative;
    width: 50px;
    height: 24px;
    background: #28a745;
    border-radius: 12px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    border: 1px solid #28a745;
}

.toggle-switch:hover {
    background: #218838;
}

.toggle-switch.imperial {
    background: #007bff;
    border-color: #007bff;
}

.toggle-switch.imperial:hover {
    background: #0056b3;
}

.toggle-slider {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 18px;
    height: 18px;
    background: white;
    border-radius: 50%;
    transition: transform 0.3s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.toggle-switch.imperial .toggle-slider {
    transform: translateX(26px);
}

/* Select2 Styles */
.select2-container--bootstrap4 .select2-selection {
    border-color: #ced4da;
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.375rem 0.75rem;
}

.select2-container--bootstrap4 .select2-selection--single {
    height: calc(1.5em + 0.75rem + 2px) !important;
}

.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
    padding-left: 0;
    padding-right: 20px;
    height: auto;
    margin-top: -2px;
}

.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
    height: calc(1.5em + 0.75rem);
}

.select2-container--bootstrap4.select2-container--focus .select2-selection {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.select2-container--bootstrap4 .select2-dropdown {
    border-color: #ced4da;
}

.select2-container--bootstrap4 .select2-results__option {
    padding: 0.375rem 0.75rem;
}

/* Custom styling for "Add New" options */
.select2-results__option .text-primary {
    color: #007bff !important;
}

/* Ensure proper z-index for Select2 dropdown */
.select2-container {
    z-index: 1050;
}

.select2-dropdown {
    z-index: 1051;
}

/* Input group integration */
.input-group .select2-container {
    flex: 1 1 auto;
    width: 1% !important;
}

.input-group .select2-container .select2-selection {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}
</style>

<!-- JavaScript -->
<script>
// Initialize everything when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Document ready - Initializing product form');
    
    try {
        // Check for server messages and show alerts
        checkServerMessages();
        
        // Initialize image preview
        initializeImagePreview();
        
        // Initialize form validations
        initializeValidations();
        
        console.log('Initialization completed successfully');
        
    } catch (error) {
        console.error('Error during initialization:', error);
        alert('Page initialization failed: ' + error.message + '. Please refresh the page.');
    }
});

// Check for PHP success/error messages and show JavaScript alerts
function checkServerMessages() {
    // Check for success message
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        const successText = successAlert.textContent.trim();
        if (successText) {
            alert('Submission successful! ' + successText);
            console.log('Success:', successText);
        }
    }
    
    // Check for error message  
    const errorAlert = document.querySelector('.alert-danger');
    if (errorAlert) {
        const errorText = errorAlert.textContent.trim();
        if (errorText) {
            alert('Submission failed! Details: ' + errorText);
            console.error('Error:', errorText);
        }
    }
}

function initializeImagePreview() {
    const imageInput = document.getElementById('image');
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('preview');
    
    if (imageInput && preview && previewImg) {
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
}

function initializeValidations() {
    const form = document.getElementById('productForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submission initiated...');
            alert('Initiating submission...');
            
            let isValid = true;
            
            // Basic validation for required fields (updated for our simplified form)
            const requiredFields = ['product_name', 'sku'];
            
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
                alert('Submission failed! Please fill in all required fields (Product Name and SKU)');
                return false;
            }
            
            // If validation passes, show confirmation
            console.log('Validation passed, submitting form...');
            
            return true;
        });
        
        // Remove validation styling when user types
        const inputs = form.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    }
}

function setupInventoryValidation() {
    const minLevel = document.getElementById('min_inventory_level');
    const maxLevel = document.getElementById('max_inventory_level');
    const reorderLevel = document.getElementById('reorder_level');
    
    function validateOnChange() {
        const minVal = parseFloat(minLevel.value) || 0;
        const maxVal = parseFloat(maxLevel.value) || 0;
        const reorderVal = parseFloat(reorderLevel.value) || 0;
        
        // Clear previous validation states
        [minLevel, maxLevel, reorderLevel].forEach(field => {
            field.classList.remove('is-invalid');
        });
        
        // Validate relationships
        if (maxVal > 0 && minVal > 0 && maxVal <= minVal) {
            maxLevel.classList.add('is-invalid');
            minLevel.classList.add('is-invalid');
        }
        
        if (reorderVal > 0 && minVal > 0 && reorderVal <= minVal) {
            reorderLevel.classList.add('is-invalid');
        }
        
        if (reorderVal > 0 && maxVal > 0 && reorderVal >= maxVal) {
            reorderLevel.classList.add('is-invalid');
        }
    }
    
    minLevel.addEventListener('input', validateOnChange);
    maxLevel.addEventListener('input', validateOnChange);
    reorderLevel.addEventListener('input', validateOnChange);
}

// Toggle expiry fields based on checkbox
function toggleExpiryFields() {
    const hasExpiry = document.getElementById('has_expiry').checked;
    const monthsField = document.getElementById('expiry_months_field');
    const exampleField = document.getElementById('expiry_example');
    
    if (hasExpiry) {
        monthsField.style.display = 'block';
        exampleField.style.display = 'block';
        
        // Set default value if empty
        const monthsInput = document.getElementById('expiry_months');
        
        if (!monthsInput.value) monthsInput.value = '12';
        
        calculateExpiryExample();
    } else {
        monthsField.style.display = 'none';
        exampleField.style.display = 'none';
    }
}

// Toggle warranty fields based on checkbox
function toggleWarrantyFields() {
    const hasWarranty = document.getElementById('has_warranty').checked;
    const warrantyField = document.getElementById('warranty_months_field');
    
    if (hasWarranty) {
        warrantyField.style.display = 'block';
        
        // Set default value if empty
        const warrantyInput = document.getElementById('warranty_period');
        if (!warrantyInput.value) warrantyInput.value = '12';
    } else {
        warrantyField.style.display = 'none';
    }
}

// Modern toggle for unit system
function toggleUnitSystem() {
    const currentSystem = document.getElementById('current-unit-system').value;
    const newSystem = currentSystem === 'metric' ? 'imperial' : 'metric';
    
    // Update hidden field
    document.getElementById('current-unit-system').value = newSystem;
    
    // Update toggle appearance
    const toggleSwitch = document.querySelector('.toggle-switch');
    const metricLabel = document.getElementById('metric-label');
    const imperialLabel = document.getElementById('imperial-label');
    
    if (newSystem === 'imperial') {
        toggleSwitch.classList.add('imperial');
        metricLabel.classList.remove('active');
        imperialLabel.classList.add('active', 'imperial');
    } else {
        toggleSwitch.classList.remove('imperial');
        metricLabel.classList.add('active');
        imperialLabel.classList.remove('active', 'imperial');
    }
    
    // Switch the units (excluding weight)
    switchDimensionSystem(newSystem);
}

// Initialize toggle on page load
function initializeUnitToggle() {
    // Set initial state
    const metricLabel = document.getElementById('metric-label');
    if (metricLabel) {
        metricLabel.classList.add('active');
    }
}

// Switch dimension unit system (metric/imperial) - excludes weight
function switchDimensionSystem(system) {
    const units = {
        metric: {
            dimension: 'cm'
        },
        imperial: {
            dimension: 'in'
        }
    };
    
    // Update dimension units (width, height, length) - EXCLUDING weight
    ['width', 'height', 'length'].forEach(dimension => {
        const unitDisplay = document.getElementById(dimension + '_unit_display');
        const unitHidden = document.getElementById(dimension + '_unit');
        
        if (unitDisplay && unitHidden) {
            unitDisplay.textContent = units[system].dimension;
            unitHidden.value = units[system].dimension;
        }
    });
    
    // Weight stays independent - no changes to weight unit
    
    // Update conversion hint (only for dimensions)
    const conversionHint = document.getElementById('conversion_hint');
    if (conversionHint) {
        if (system === 'imperial') {
            conversionHint.innerHTML = '<small class="text-muted"><i class="fas fa-info-circle mr-1"></i>Dimensions: 1 inch = 2.54 cm | Weight: Independent unit selection</small>';
        } else {
            conversionHint.innerHTML = '<small class="text-muted"><i class="fas fa-info-circle mr-1"></i>Dimensions: 1 cm = 0.39 inches | Weight: Independent unit selection</small>';
        }
    }
}

// Calculate and display expiry example
function calculateExpiryExample() {
    const months = parseInt(document.getElementById('expiry_months').value) || 0;
    const exampleDiv = document.getElementById('expiry_calculation');
    
    if (months === 0) {
        exampleDiv.textContent = 'Please enter shelf life';
        return;
    }
    
    const today = new Date();
    const expiryDate = new Date(today.getFullYear(), today.getMonth() + months, today.getDate());
    
    const monthsText = months === 1 ? '1 month' : `${months} months`;
    
    exampleDiv.innerHTML = `
        <strong>Shelf Life:</strong> ${monthsText}<br>
        <strong>If manufactured today:</strong><br>
        Expires: ${expiryDate.toLocaleDateString('en-IN')}
    `;
}

// Initialize expiry functionality on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set up event listeners for expiry fields
    const monthsInput = document.getElementById('expiry_months');
    
    if (monthsInput) {
        monthsInput.addEventListener('input', calculateExpiryExample);
    }
    
    // Check if has_expiry is already checked (for edit forms)
    const hasExpiryCheckbox = document.getElementById('has_expiry');
    if (hasExpiryCheckbox && hasExpiryCheckbox.checked) {
        toggleExpiryFields();
    }
    
    // Check if has_warranty is already checked (for edit forms)
    const hasWarrantyCheckbox = document.getElementById('has_warranty');
    if (hasWarrantyCheckbox && hasWarrantyCheckbox.checked) {
        toggleWarrantyFields();
    }
    
    // Initialize unit system toggle
    initializeUnitToggle();
});

</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
