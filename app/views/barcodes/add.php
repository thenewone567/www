<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<a href="<?php echo URLROOT; ?>/barcodes" class="btn btn-light"><i class="fa fa-arrow-left"></i> Back</a>
<div class="card card-body theme-card-light mt-5">
    <h2>Add Barcode</h2>
    <p>Create a new barcode with this form</p>
    <form action="<?php echo URLROOT; ?>/barcodes/add" method="post">
        <div class="form-group">
            <label for="product_id">Product ID: <sup>*</sup></label>
            <input type="text" name="product_id"
                class="form-control form-control-lg <?php echo (!empty($data['product_id_err'])) ? 'is-invalid' : ''; ?>"
                value="<?php echo $data['product_id']; ?>" placeholder="PROD001">
            <span class="invalid-feedback"><?php echo $data['product_id_err']; ?></span>
            <small class="form-text text-muted">Reference to existing product</small>
        </div>
        <div class="form-group">
            <label for="barcode_value">Barcode Value: <sup>*</sup></label>
            <input type="text" name="barcode_value"
                class="form-control form-control-lg <?php echo (!empty($data['barcode_value_err'])) ? 'is-invalid' : ''; ?>"
                value="<?php echo $data['barcode_value']; ?>" placeholder="123456789012">
            <span class="invalid-feedback"><?php echo $data['barcode_value_err']; ?></span>
            <small class="form-text text-muted">Numeric barcode for scanning</small>
        </div>
        <div class="form-group">
            <label for="type">Barcode Type: <sup>*</sup></label>
            <select name="type" class="form-control form-control-lg">
                <option value="C128">Code 128 (Recommended)</option>
                <option value="C39">Code 39</option>
                <option value="EAN13">EAN-13 (European)</option>
                <option value="UPCA">UPC-A (US/Canada)</option>
            </select>
            <small class="form-text text-muted">Choose barcode standard</small>
        </div>
        <input type="submit" class="btn btn-success" value="Submit">
    </form>
</div>

<script>
    // Auto-formatting for barcode management
    document.addEventListener('DOMContentLoaded', function () {
        // Auto-uppercase product ID for consistency
        const productIdField = document.querySelector('input[name="product_id"]');
        if (productIdField) {
            productIdField.addEventListener('blur', function () {
                if (this.value) {
                    this.value = this.value.toUpperCase();
                }
            });
        }

        // Auto-clean barcode value (remove spaces, non-numeric characters for most types)
        const barcodeValueField = document.querySelector('input[name="barcode_value"]');
        const barcodeTypeField = document.querySelector('select[name="type"]');
        if (barcodeValueField) {
            barcodeValueField.addEventListener('blur', function () {
                if (this.value) {
                    this.value = formatBarcodeValue(this.value, barcodeTypeField.value);
                }
            });
        }
    });

    // Format barcode value based on type
    function formatBarcodeValue(value, type) {
        // Remove all spaces and special characters
        let cleaned = value.replace(/\s+/g, '').replace(/[^0-9A-Z]/gi, '');

        switch (type) {
            case 'EAN13':
                // EAN-13 should be exactly 13 digits
                cleaned = cleaned.replace(/\D/g, '');
                if (cleaned.length > 13) cleaned = cleaned.substring(0, 13);
                break;
            case 'UPCA':
                // UPC-A should be exactly 12 digits
                cleaned = cleaned.replace(/\D/g, '');
                if (cleaned.length > 12) cleaned = cleaned.substring(0, 12);
                break;
            case 'C128':
            case 'C39':
                // Code 128 and Code 39 can contain alphanumeric
                cleaned = cleaned.toUpperCase();
                break;
            default:
                cleaned = cleaned.toUpperCase();
        }

        return cleaned;
    }
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