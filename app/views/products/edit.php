<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container theme-unified page-top-area mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-6">
            <h1 class="mb-1 font-weight-bold">
                <i class="fas fa-edit mr-2"></i>Edit Product
            </h1>
            <small class="text-muted">Update product details</small>
        </div>
        <div class="col-12 col-md-6 text-md-right mt-3 mt-md-0">
            <a href="<?php echo URLROOT; ?>/products" class="btn btn-outline-secondary btn-lg">
                <i class="fas fa-arrow-left mr-2"></i>Back to Products
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card-theme theme-card-light">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit mr-2"></i>Product Information
                </h5>
            </div>
            <div class="card-body">
                <!-- Display validation errors -->
                <?php if (!empty($data['product_name_err']) || !empty($data['sku_err']) || !empty($data['category_id_err']) || 
                          !empty($data['brand_id_err']) || !empty($data['unit_id_err']) || !empty($data['purchase_price_err']) || 
                          !empty($data['selling_price_err'])): ?>
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle mr-2"></i>Please fix the following errors:</h6>
                        <ul class="mb-0">
                            <?php if (!empty($data['product_name_err'])): ?>
                                <li><?php echo $data['product_name_err']; ?></li>
                            <?php endif; ?>
                            <?php if (!empty($data['sku_err'])): ?>
                                <li><?php echo $data['sku_err']; ?></li>
                            <?php endif; ?>
                            <?php if (!empty($data['category_id_err'])): ?>
                                <li><?php echo $data['category_id_err']; ?></li>
                            <?php endif; ?>
                            <?php if (!empty($data['brand_id_err'])): ?>
                                <li><?php echo $data['brand_id_err']; ?></li>
                            <?php endif; ?>
                            <?php if (!empty($data['unit_id_err'])): ?>
                                <li><?php echo $data['unit_id_err']; ?></li>
                            <?php endif; ?>
                            <?php if (!empty($data['purchase_price_err'])): ?>
                                <li><?php echo $data['purchase_price_err']; ?></li>
                            <?php endif; ?>
                            <?php if (!empty($data['selling_price_err'])): ?>
                                <li><?php echo $data['selling_price_err']; ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Success message -->
                <?php flash('product_message'); ?>

                <form action="<?php echo URLROOT; ?>/products/edit/<?php echo $data['product_id']; ?>" method="post" enctype="multipart/form-data">
                    <!-- Basic Information -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="product_name">Product Name *</label>
                                <input type="text" class="form-control <?php echo !empty($data['product_name_err']) ? 'is-invalid' : ''; ?>" 
                                       id="product_name" name="product_name" 
                                       value="<?php echo htmlspecialchars($data['product_name'] ?? ''); ?>" required>
                                <div class="invalid-feedback"><?php echo $data['product_name_err'] ?? ''; ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sku">SKU *</label>
                                <input type="text" class="form-control <?php echo !empty($data['sku_err']) ? 'is-invalid' : ''; ?>" 
                                       id="sku" name="sku" 
                                       value="<?php echo htmlspecialchars($data['sku'] ?? ''); ?>" required>
                                <div class="invalid-feedback"><?php echo $data['sku_err'] ?? ''; ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="supplier_code">Supplier Code</label>
                                <input type="text" class="form-control" id="supplier_code" name="supplier_code" 
                                       value="<?php echo htmlspecialchars($data['supplier_code'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="product_type">Product Type</label>
                                <select class="form-control" id="product_type" name="product_type">
                                    <option value="STANDARD" <?php echo ($data['product_type'] ?? '') == 'STANDARD' ? 'selected' : ''; ?>>Standard</option>
                                    <option value="BULK" <?php echo ($data['product_type'] ?? '') == 'BULK' ? 'selected' : ''; ?>>Bulk</option>
                                    <option value="OVERSIZED" <?php echo ($data['product_type'] ?? '') == 'OVERSIZED' ? 'selected' : ''; ?>>Oversized</option>
                                    <option value="FRAGILE" <?php echo ($data['product_type'] ?? '') == 'FRAGILE' ? 'selected' : ''; ?>>Fragile</option>
                                    <option value="HAZMAT" <?php echo ($data['product_type'] ?? '') == 'HAZMAT' ? 'selected' : ''; ?>>Hazmat</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Categories, Brands, Units -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="category_id">Category *</label>
                                <select class="form-control <?php echo !empty($data['category_id_err']) ? 'is-invalid' : ''; ?>" 
                                        id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php if (!empty($data['categories'])): ?>
                                        <?php foreach ($data['categories'] as $category): ?>
                                            <option value="<?php echo $category->category_id; ?>" 
                                                    <?php echo ($data['category_id'] ?? 0) == $category->category_id ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category->category_name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback"><?php echo $data['category_id_err'] ?? ''; ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="brand_id">Brand *</label>
                                <select class="form-control <?php echo !empty($data['brand_id_err']) ? 'is-invalid' : ''; ?>" 
                                        id="brand_id" name="brand_id" required>
                                    <option value="">Select Brand</option>
                                    <?php if (!empty($data['brands'])): ?>
                                        <?php foreach ($data['brands'] as $brand): ?>
                                            <option value="<?php echo $brand->brand_id; ?>" 
                                                    <?php echo ($data['brand_id'] ?? 0) == $brand->brand_id ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($brand->brand_name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback"><?php echo $data['brand_id_err'] ?? ''; ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="unit_id">Unit *</label>
                                <select class="form-control <?php echo !empty($data['unit_id_err']) ? 'is-invalid' : ''; ?>" 
                                        id="unit_id" name="unit_id" required>
                                    <option value="">Select Unit</option>
                                    <?php if (!empty($data['units'])): ?>
                                        <?php foreach ($data['units'] as $unit): ?>
                                            <option value="<?php echo $unit->unit_id; ?>" 
                                                    <?php echo ($data['unit_id'] ?? 0) == $unit->unit_id ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($unit->unit_name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback"><?php echo $data['unit_id_err'] ?? ''; ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="purchase_price">Purchase Price *</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₹</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control <?php echo !empty($data['purchase_price_err']) ? 'is-invalid' : ''; ?>" 
                                           id="purchase_price" name="purchase_price" 
                                           value="<?php echo $data['purchase_price'] ?? 0; ?>" required>
                                    <div class="invalid-feedback"><?php echo $data['purchase_price_err'] ?? ''; ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="selling_price">Selling Price *</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₹</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control <?php echo !empty($data['selling_price_err']) ? 'is-invalid' : ''; ?>" 
                                           id="selling_price" name="selling_price" 
                                           value="<?php echo $data['selling_price'] ?? 0; ?>" required>
                                    <div class="invalid-feedback"><?php echo $data['selling_price_err'] ?? ''; ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="profit_margin">Profit Margin (%)</label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control" 
                                       id="profit_margin" name="profit_margin" 
                                       value="<?php echo $data['profit_margin'] ?? 0; ?>" readonly>
                                <small class="form-text text-muted">Automatically calculated</small>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Levels -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="min_Inventory_level">Min Inventory Level</label>
                                <input type="number" min="0" class="form-control" id="min_Inventory_level" 
                                       name="min_Inventory_level" value="<?php echo $data['min_Inventory_level'] ?? 0; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="max_Inventory_level">Max Inventory Level</label>
                                <input type="number" min="0" class="form-control" id="max_Inventory_level" 
                                       name="max_Inventory_level" value="<?php echo $data['max_Inventory_level'] ?? 0; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="reorder_level">Reorder Level</label>
                                <input type="number" min="0" class="form-control" id="reorder_level" 
                                       name="reorder_level" value="<?php echo $data['reorder_level'] ?? 10; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="weight">Weight (kg)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="weight" 
                                       name="weight" value="<?php echo $data['weight'] ?? 0; ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dimensions">Dimensions (L x W x H)</label>
                                <input type="text" class="form-control" id="dimensions" name="dimensions" 
                                       value="<?php echo htmlspecialchars($data['dimensions'] ?? ''); ?>" 
                                       placeholder="e.g., 10cm x 5cm x 2cm">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="warranty_period">Warranty (months)</label>
                                <input type="number" min="0" class="form-control" id="warranty_period" 
                                       name="warranty_period" value="<?php echo $data['warranty_period'] ?? 0; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="expiry_months">Expiry Period (months)</label>
                                <input type="number" min="0" class="form-control" id="expiry_months" 
                                       name="expiry_months" value="<?php echo $data['expiry_months'] ?? 0; ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Product Image -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="product_image">Product Image</label>
                                <input type="file" class="form-control-file" id="product_image" name="product_image" accept="image/*">
                                <small class="form-text text-muted">Choose a new image to replace the current one</small>
                                <?php if (!empty($data['image_path'])): ?>
                                    <input type="hidden" name="current_image" value="<?php echo $data['image_path']; ?>">
                                    <div class="mt-2">
                                        <small class="text-muted">Current image:</small><br>
                                        <img src="<?php echo URLROOT; ?>/public/uploads/<?php echo $data['image_path']; ?>" 
                                             alt="Current product image" class="img-thumbnail" style="max-width: 150px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="has_expiry" name="has_expiry" 
                                           value="1" <?php echo !empty($data['has_expiry']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="has_expiry">
                                        Product has expiry date
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                            <hr>
                            <div class="form-group text-right">
                                <a href="<?php echo URLROOT; ?>/products" class="btn btn-outline-secondary btn-lg mr-2">
                                    <i class="fas fa-times mr-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save mr-2"></i>Update Product
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Calculate profit margin automatically
document.addEventListener('DOMContentLoaded', function() {
    const purchasePriceInput = document.getElementById('purchase_price');
    const sellingPriceInput = document.getElementById('selling_price');
    const profitMarginInput = document.getElementById('profit_margin');

    function calculateProfitMargin() {
        const purchasePrice = parseFloat(purchasePriceInput.value) || 0;
        const sellingPrice = parseFloat(sellingPriceInput.value) || 0;
        
        if (sellingPrice > 0 && purchasePrice > 0) {
            const margin = ((sellingPrice - purchasePrice) / sellingPrice) * 100;
            profitMarginInput.value = margin.toFixed(2);
        } else {
            profitMarginInput.value = '0.00';
        }
    }

    purchasePriceInput.addEventListener('input', calculateProfitMargin);
    sellingPriceInput.addEventListener('input', calculateProfitMargin);

    // Calculate initial margin
    calculateProfitMargin();
});
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
