<?php require APPROOT . DS . 'views/layout/header.php'; ?>
    <a href="<?php echo URLROOT; ?>/stock" class="btn btn-light"><i class="fa fa-backward"></i> Back</a>
    <div class="card card-body bg-light mt-5">
        <h2>Add Stock</h2>
        <p>Create a new stock record with this form</p>
        <form action="<?php echo URLROOT; ?>/stock/add" method="post">
            <div class="form-group">
                <label for="product_id">Product ID: <sup>*</sup></label>
                <input type="text" name="product_id" class="form-control form-control-lg <?php echo (!empty($data['product_id_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['product_id']; ?>">
                <span class="invalid-feedback"><?php echo $data['product_id_err']; ?></span>
            </div>
            <div class="form-group">
                <label for="batch_number">Batch Number:</label>
                <input type="text" name="batch_number" class="form-control form-control-lg" value="<?php echo $data['batch_number']; ?>">
            </div>
            <div class="form-group">
                <label for="expiry_date">Expiry Date:</label>
                <input type="date" name="expiry_date" class="form-control form-control-lg" value="<?php echo $data['expiry_date']; ?>">
            </div>
            <div class="form-group">
                <label for="quantity">Quantity: <sup>*</sup></label>
                <input type="number" name="quantity" class="form-control form-control-lg <?php echo (!empty($data['quantity_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['quantity']; ?>">
                <span class="invalid-feedback"><?php echo $data['quantity_err']; ?></span>
            </div>
            <div class="form-group">
                <label for="location_id">Location ID:</label>
                <input type="text" name="location_id" class="form-control form-control-lg" value="<?php echo $data['location_id']; ?>">
            </div>
            <input type="submit" class="btn btn-success" value="Submit">
        </form>
    </div>
<?php require APPROOT . DS . 'views/layout/footer.php'; ?>
