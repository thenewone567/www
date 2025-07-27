<?php require APPROOT . '/' . ''views/layout/header.php'; ?>
    <a href="<?php echo URLROOT; ?>/stock" class="btn btn-light"><i class="fa fa-backward"></i> Back</a>
    <div class="card card-body bg-light mt-5">
        <h2>Move Stock</h2>
        <p>Move stock from one location to another with this form</p>
        <form action="<?php echo URLROOT; ?>/stock/move" method="post">
            <div class="form-group">
                <label for="product_id">Product ID: <sup>*</sup></label>
                <input type="text" name="product_id" class="form-control form-control-lg <?php echo (!empty($data['product_id_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['product_id']; ?>">
                <span class="invalid-feedback"><?php echo $data['product_id_err']; ?></span>
            </div>
            <div class="form-group">
                <label for="from_location_id">From Location ID: <sup>*</sup></label>
                <input type="text" name="from_location_id" class="form-control form-control-lg" value="<?php echo $data['from_location_id']; ?>">
            </div>
            <div class="form-group">
                <label for="to_location_id">To Location ID: <sup>*</sup></label>
                <input type="text" name="to_location_id" class="form-control form-control-lg" value="<?php echo $data['to_location_id']; ?>">
            </div>
            <div class="form-group">
                <label for="quantity">Quantity: <sup>*</sup></label>
                <input type="number" name="quantity" class="form-control form-control-lg <?php echo (!empty($data['quantity_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['quantity']; ?>">
                <span class="invalid-feedback"><?php echo $data['quantity_err']; ?></span>
            </div>
            <input type="submit" class="btn btn-success" value="Submit">
        </form>
    </div>
<?php require APPROOT . '/' . ''views/layout/footer.php'; ?>
