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
                <input type="text" name="category_id" class="form-control form-control-lg"
                    value="<?php echo $data['category_id']; ?>" required>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="brand_id">Brand: <sup>*</sup></label>
                <input type="text" name="brand_id" class="form-control form-control-lg"
                    value="<?php echo $data['brand_id']; ?>" required>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="unit_id">Unit: <sup>*</sup></label>
                <input type="text" name="unit_id" class="form-control form-control-lg"
                    value="<?php echo $data['unit_id']; ?>" required>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label for="min_stock_level">Min Stock Level: <sup>*</sup></label>
                <input type="number" name="min_stock_level" class="form-control form-control-lg"
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
<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'footer.php'; ?>