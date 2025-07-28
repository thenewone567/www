<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
<a href="<?php echo URLROOT; ?>/products" class="btn btn-light"><i class="fa fa-arrow-left"></i> Back</a>
<div class="card card-body bg-light mt-5">
    <h2>Edit Product</h2>
    <p>Edit the product with this form</p>
    <form action="<?php echo URLROOT; ?>/products/edit/<?php echo $data['id']; ?>" method="post">
        <div class="form-group">
            <label for="product_name">Product Name: <sup>*</sup></label>
            <input type="text" name="product_name"
                class="form-control form-control-lg <?php echo (!empty($data['product_name_err'])) ? 'is-invalid' : ''; ?>"
                value="<?php echo $data['product_name']; ?>">
            <span class="invalid-feedback"><?php echo $data['product_name_err']; ?></span>
        </div>
        <div class="form-group">
            <label for="sku">SKU: <sup>*</sup></label>
            <input type="text" name="sku"
                class="form-control form-control-lg <?php echo (!empty($data['sku_err'])) ? 'is-invalid' : ''; ?>"
                value="<?php echo $data['sku']; ?>">
            <span class="invalid-feedback"><?php echo $data['sku_err']; ?></span>
        </div>
        <div class="form-group">
            <label for="category_id">Category: <sup>*</sup></label>
            <input type="text" name="category_id" class="form-control form-control-lg"
                value="<?php echo $data['category_id']; ?>">
        </div>
        <div class="form-group">
            <label for="brand_id">Brand: <sup>*</sup></label>
            <input type="text" name="brand_id" class="form-control form-control-lg"
                value="<?php echo $data['brand_id']; ?>">
        </div>
        <div class="form-group">
            <label for="unit_id">Unit: <sup>*</sup></label>
            <input type="text" name="unit_id" class="form-control form-control-lg"
                value="<?php echo $data['unit_id']; ?>">
        </div>
        <div class="form-group">
            <label for="min_stock_level">Min Stock Level: <sup>*</sup></label>
            <input type="number" name="min_stock_level" class="form-control form-control-lg"
                value="<?php echo $data['min_stock_level']; ?>">
        </div>
        <div class="form-group">
            <label for="max_stock_level">Max Stock Level: <sup>*</sup></label>
            <input type="number" name="max_stock_level" class="form-control form-control-lg"
                value="<?php echo $data['max_stock_level']; ?>">
        </div>
        <div class="form-group">
            <label for="reorder_level">Reorder Level: <sup>*</sup></label>
            <input type="number" name="reorder_level" class="form-control form-control-lg"
                value="<?php echo $data['reorder_level']; ?>">
        </div>
        <input type="submit" class="btn btn-success" value="Submit">
    </form>
</div>
<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'footer.php'; ?>