<?php require APPROOT . '/'views/layout/header.php'; ?>
    <a href="<?php echo URLROOT; ?>/barcodes" class="btn btn-light"><i class="fa fa-backward"></i> Back</a>
    <div class="card card-body bg-light mt-5">
        <h2>Add Barcode</h2>
        <p>Create a new barcode with this form</p>
        <form action="<?php echo URLROOT; ?>/barcodes/add" method="post">
            <div class="form-group">
                <label for="product_id">Product ID: <sup>*</sup></label>
                <input type="text" name="product_id" class="form-control form-control-lg <?php echo (!empty($data['product_id_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['product_id']; ?>">
                <span class="invalid-feedback"><?php echo $data['product_id_err']; ?></span>
            </div>
            <div class="form-group">
                <label for="barcode_value">Barcode Value: <sup>*</sup></label>
                <input type="text" name="barcode_value" class="form-control form-control-lg <?php echo (!empty($data['barcode_value_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['barcode_value']; ?>">
                <span class="invalid-feedback"><?php echo $data['barcode_value_err']; ?></span>
            </div>
            <div class="form-group">
                <label for="type">Type: <sup>*</sup></label>
                <select name="type" class="form-control form-control-lg">
                    <option value="C128">Code 128</option>
                    <option value="C39">Code 39</option>
                    <option value="EAN13">EAN-13</option>
                    <option value="UPCA">UPC-A</option>
                </select>
            </div>
            <input type="submit" class="btn btn-success" value="Submit">
        </form>
    </div>
<?php require APPROOT . '/'views/layout/footer.php'; ?>
