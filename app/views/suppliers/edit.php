<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<a href="<?php echo URLROOT; ?>/suppliers" class="btn btn-light"><i class="fa fa-arrow-left"></i> Back</a>
<div class="card card-body theme-card-light mt-5">
    <h2>Edit Supplier</h2>
    <p>Edit the supplier with this form</p>
    <form action="<?php echo URLROOT; ?>/suppliers/edit/<?php echo $data['id']; ?>" method="post">
        <div class="form-group">
            <label for="supplier_name">Supplier Name: <sup>*</sup></label>
            <input type="text" name="supplier_name"
                class="form-control form-control-lg <?php echo (!empty($data['supplier_name_err'])) ? 'is-invalid' : ''; ?>"
                value="<?php echo $data['supplier_name']; ?>">
            <span class="invalid-feedback"><?php echo $data['supplier_name_err']; ?></span>
        </div>
        <div class="form-group">
            <label for="contact_person">Contact Person Name:</label>
            <input type="text" name="contact_person" class="form-control form-control-lg"
                value="<?php echo $data['contact_person'] ?? ''; ?>" placeholder="e.g., John Doe">
        </div>
        <div class="form-group">
            <label for="phone">Phone Number:</label>
            <input type="tel" name="phone" class="form-control form-control-lg"
                value="<?php echo $data['phone'] ?? ''; ?>" placeholder="e.g., +91 98765 43210">
        </div>
        <div class="form-group">
            <label for="email">Email Address:</label>
            <input type="email" name="email"
                class="form-control form-control-lg <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>"
                value="<?php echo $data['email'] ?? ''; ?>" placeholder="e.g., supplier@company.com">
            <span class="invalid-feedback"><?php echo $data['email_err'] ?? ''; ?></span>
        </div>
        <div class="form-group">
            <label for="address">Address:</label>
            <textarea name="address" class="form-control form-control-lg" rows="3"
                placeholder="Complete business address"><?php echo $data['address'] ?? ''; ?></textarea>
        </div>
        <div class="form-group">
            <label for="gst_number">GST Number:</label>
            <input type="text" name="gst_number"
                class="form-control form-control-lg <?php echo (!empty($data['gst_number_err'])) ? 'is-invalid' : ''; ?>"
                value="<?php echo $data['supplier']->gst_number ?? ''; ?>" placeholder="e.g., 22AAAAA0000A1Z5"
                maxlength="15">
            <span class="invalid-feedback"><?php echo $data['gst_number_err'] ?? ''; ?></span>
            <small class="form-text text-muted">15-digit GST identification number (optional)</small>
        </div>
        <input type="submit" class="btn btn-success" value="Submit">
    </form>
</div>

<script>
    // Auto-capitalize supplier name and contact person name
    document.addEventListener('DOMContentLoaded', function () {
        // Auto-capitalize supplier name
        const supplierNameField = document.querySelector('input[name="supplier_name"]');
        if (supplierNameField) {
            supplierNameField.addEventListener('blur', function () {
                if (this.value) {
                    this.value = capitalizeWords(this.value);
                }
            });
        }

        // Auto-capitalize contact person name
        const contactPersonField = document.querySelector('input[name="contact_person"]');
        if (contactPersonField) {
            contactPersonField.addEventListener('blur', function () {
                if (this.value) {
                    this.value = capitalizeWords(this.value);
                }
            });
        }

        // Auto-uppercase GST number for consistency
        const gstNumberField = document.querySelector('input[name="gst_number"]');
        if (gstNumberField) {
            gstNumberField.addEventListener('blur', function () {
                if (this.value) {
                    this.value = this.value.toUpperCase();
                }
            });
        }
    });

    // Capitalize first letter of each word
    function capitalizeWords(str) {
        return str.toLowerCase().replace(/\b\w/g, function (letter) {
            return letter.toUpperCase();
        });
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