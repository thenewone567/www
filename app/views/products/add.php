<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
<div class="add-product-top container-fluid mt-0 pt-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <a href="<?php echo URLROOT; ?>/products" class="btn btn-light"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h2 class="mb-0">Add Product</h2>
        </div>
    </div>
</div>
<div class="card card-body bg-light mt-3">
    <p>Create a new product with this form</p>
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
    <form id="addProductForm" action="<?php echo URLROOT; ?>/products/add" method="post" enctype="multipart/form-data">
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
                    class="form-control form-control-lg <?php echo (!empty($data['category_id_err'])) ? 'is-invalid' : ''; ?>"
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
                    class="form-control form-control-lg <?php echo (!empty($data['brand_id_err'])) ? 'is-invalid' : ''; ?>"
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
                <label for="initial_quantity">Initial Quantity: <sup>*</sup></label>
                <input type="number" name="initial_quantity"
                    class="form-control form-control-lg <?php echo (!empty($data['initial_quantity_err'])) ? 'is-invalid' : ''; ?>"
                    value="<?php echo $data['initial_quantity']; ?>" min="0" required>
                <span class="invalid-feedback"><?php echo $data['initial_quantity_err']; ?></span>
                <small class="form-text text-muted">Starting stock quantity</small>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="unit_id">Unit: <sup>*</sup></label>
                <select name="unit_id"
                    class="form-control form-control-lg <?php echo (!empty($data['unit_id_err'])) ? 'is-invalid' : ''; ?>"
                    required>
                    <option value="">Select Unit</option>
                    <?php if (!empty($data['units'])): ?>
                        <?php foreach ($data['units'] as $unit): ?>
                            <option value="<?php echo $unit->unit_id; ?>" <?php echo ($data['unit_id'] == $unit->unit_id) ? 'selected' : ''; ?>>
                                <?php echo $unit->unit_name; ?>
                                <?php echo !empty($unit->abbreviation) ? ' (' . $unit->abbreviation . ')' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <span class="invalid-feedback"><?php echo $data['unit_id_err']; ?></span>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="min_stock_level">Min Stock Level: <sup>*</sup></label>
                <input type="number" name="min_stock_level" class="form-control form-control-lg"
                    value="<?php echo $data['min_stock_level']; ?>" min="0" required>
                <small class="form-text text-muted">Alert when stock falls below this level</small>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="max_stock_level">Max Stock Level:</label>
                <input type="number" name="max_stock_level" class="form-control form-control-lg"
                    value="<?php echo $data['max_stock_level']; ?>" min="0">
                <small class="form-text text-muted">Maximum stock capacity</small>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="reorder_level">Reorder Level:</label>
                <input type="number" name="reorder_level" class="form-control form-control-lg"
                    value="<?php echo $data['reorder_level']; ?>" min="0">
                <small class="form-text text-muted">Reorder when stock reaches this level</small>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="supplier_code">Supplier Code:</label>
                <input type="text" name="supplier_code" class="form-control form-control-lg"
                    value="<?php echo $data['supplier_code']; ?>" placeholder="SUP-001">
                <small class="form-text text-muted">Supplier's product code</small>
            </div>
        </div>

        <!-- Pricing Section -->
        <div class="row">
            <div class="col-12">
                <h5 class="mb-3 text-primary"><i class="fa-solid fa-dollar-sign"></i> Pricing Information</h5>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="purchase_price">Purchase Price: <sup>*</sup></label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" name="purchase_price" step="0.01" min="0"
                        class="form-control form-control-lg <?php echo (!empty($data['purchase_price_err'])) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $data['purchase_price']; ?>" required onchange="calculateMargin()">
                    <span class="invalid-feedback"><?php echo $data['purchase_price_err']; ?></span>
                </div>
                <small class="form-text text-muted">Cost price from supplier</small>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="selling_price">Selling Price: <sup>*</sup></label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" name="selling_price" step="0.01" min="0"
                        class="form-control form-control-lg <?php echo (!empty($data['selling_price_err'])) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $data['selling_price']; ?>" required onchange="calculateMargin()">
                    <span class="invalid-feedback"><?php echo $data['selling_price_err']; ?></span>
                </div>
                <small class="form-text text-muted">Retail price to customers</small>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="profit_margin">Profit Margin (%):</label>
                <div class="input-group">
                    <input type="number" name="profit_margin" step="0.01" min="0" class="form-control form-control-lg"
                        id="profitMarginField" value="<?php echo $data['profit_margin']; ?>" readonly>
                    <span class="input-group-text">%</span>
                </div>
                <small class="form-text text-muted">Automatically calculated</small>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="markup_preset">Quick Markup:</label>
                <select class="form-control form-control-lg" id="markupPreset" onchange="applyMarkup()">
                    <option value="">Select Markup</option>
                    <option value="15">15% Markup</option>
                    <option value="25">25% Markup</option>
                    <option value="30">30% Markup</option>
                    <option value="50">50% Markup</option>
                    <option value="100">100% Markup</option>
                </select>
                <small class="form-text text-muted">Auto-calculate selling price</small>
            </div>
        </div>

        <!-- Physical Details Section -->
        <div class="row">
            <div class="col-12">
                <h5 class="mb-3 text-info"><i class="fa-solid fa-cube"></i> Physical Details</h5>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="weight">Weight (kg):</label>
                <input type="number" name="weight" step="0.001" min="0" class="form-control form-control-lg"
                    value="<?php echo $data['weight']; ?>" placeholder="0.000">
                <small class="form-text text-muted">Product weight in kilograms</small>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="dimensions">Dimensions:</label>
                <input type="text" name="dimensions" class="form-control form-control-lg"
                    value="<?php echo $data['dimensions']; ?>" placeholder="L x W x H (cm)">
                <small class="form-text text-muted">Length x Width x Height</small>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="warranty_period">Warranty (months):</label>
                <input type="number" name="warranty_period" min="0" class="form-control form-control-lg"
                    value="<?php echo $data['warranty_period']; ?>" placeholder="0">
                <small class="form-text text-muted">Warranty period in months</small>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="image">Product Image:</label>
                <input type="file" name="image" class="form-control form-control-lg" accept="image/*" id="productImage"
                    onchange="previewImage()">
                <small class="form-text text-muted">Upload product photo (JPG, PNG)</small>
                <div id="imagePreview" class="mt-2" style="display: none;">
                    <img id="preview" src="" alt="Preview" class="img-thumbnail"
                        style="max-width: 150px; max-height: 150px;">
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <a href="<?php echo URLROOT; ?>/products" class="btn btn-secondary btn-lg">
                        <i class="fa-solid fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fa-solid fa-save"></i> Add Product
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function calculateMargin() {
        const purchasePrice = parseFloat(document.querySelector('input[name="purchase_price"]').value) || 0;
        const sellingPrice = parseFloat(document.querySelector('input[name="selling_price"]').value) || 0;

        if (purchasePrice > 0 && sellingPrice > 0) {
            const margin = ((sellingPrice - purchasePrice) / purchasePrice * 100);
            document.getElementById('profitMarginField').value = margin.toFixed(2);

            // Color code the margin
            const marginField = document.getElementById('profitMarginField');
            if (margin >= 30) {
                marginField.style.color = 'green';
            } else if (margin >= 15) {
                marginField.style.color = 'orange';
            } else {
                marginField.style.color = 'red';
            }
        }
    }

    function applyMarkup() {
        const markup = parseFloat(document.getElementById('markupPreset').value);
        const purchasePrice = parseFloat(document.querySelector('input[name="purchase_price"]').value) || 0;

        if (markup && purchasePrice > 0) {
            const sellingPrice = purchasePrice * (1 + markup / 100);
            document.querySelector('input[name="selling_price"]').value = sellingPrice.toFixed(2);
            calculateMargin();
        }
    }

    function previewImage() {
        const file = document.getElementById('productImage').files[0];
        const preview = document.getElementById('imagePreview');
        const img = document.getElementById('preview');

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                img.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    }

    // Initialize calculations on page load
    document.addEventListener('DOMContentLoaded', function () {
        calculateMargin();
    });
</script>
value="<?php echo $data['min_stock_level']; ?>" required>
</div>
<div class="col-md-3 col-6 mb-3">
    <label for="max_stock_level">Max Stock Level: <sup>*</sup></label>
    <input type="number" name="max_stock_level" class="form-control form-control-lg"
        value="<?php echo $data['max_stock_level']; ?>" required>
</div>
<div class="col-md-3 col-6 mb-3">
    <label for="reorder_level">Reorder Level: <sup>*</sup></label>
    <input type="number" name="reorder_level" class="form-control form-control-lg"
        value="<?php echo $data['reorder_level']; ?>" required>
</div>
<div class="col-md-12 mb-3">
    <label for="image_path">Product Image:</label>
    <input type="file" name="image_path" class="form-control-file">
</div>
</div>
<input type="submit" class="btn btn-success" value="Submit">
</form>
</div>
<script>
    document.getElementById('addProductForm').addEventListener('submit', function (e) {
        if (!confirm('Are you sure you want to save this product?')) {
            e.preventDefault();
        }
    });
</script>

            </div> <!-- End container-fluid -->
        </div> <!-- End page-content-wrapper -->
    </div> <!-- End wrapper -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
    <script src="<?php echo URLROOT; ?>/js/main.js"></script>
</body>
</html>