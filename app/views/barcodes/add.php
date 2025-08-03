<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<a href="<?php echo URLROOT; ?>/barcodes" class="btn btn-light"><i class="fa fa-arrow-left"></i> Back</a>
<div class="card card-body bg-light mt-5">
    <h2>Add Barcode</h2>
    <p>Create a new barcode with this form</p>
    <form action="<?php echo URLROOT; ?>/barcodes/add" method="post">
        <div class="form-group">
            <label for="product_id">Product ID: <sup>*</sup></label>
            <input type="text" name="product_id"
                class="form-control form-control-lg <?php echo (!empty($data['product_id_err'])) ? 'is-invalid' : ''; ?>"
                value="<?php echo $data['product_id']; ?>">
            <span class="invalid-feedback"><?php echo $data['product_id_err']; ?></span>
        </div>
        <div class="form-group">
            <label for="barcode_value">Barcode Value: <sup>*</sup></label>
            <input type="text" name="barcode_value"
                class="form-control form-control-lg <?php echo (!empty($data['barcode_value_err'])) ? 'is-invalid' : ''; ?>"
                value="<?php echo $data['barcode_value']; ?>">
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