<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<a href="<?php echo URLROOT; ?>/suppliers" class="btn btn-light"><i class="fa fa-arrow-left"></i> Back</a>
<div class="card card-body theme-card-light mt-5">
    <h2>Add Supplier</h2>
    <p>Create a new supplier with this form</p>
    <form action="<?php echo URLROOT; ?>/suppliers/add" method="post">
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
                value="<?php echo $data['gst_number'] ?? ''; ?>" placeholder="e.g., 22AAAAA0000A1Z5" maxlength="15">
            <span class="invalid-feedback"><?php echo $data['gst_number_err'] ?? ''; ?></span>
            <small class="form-text text-muted">15-digit GST identification number (optional)</small>
        </div>
        <div class="form-group">
            <label for="default_delivery_days">Default Delivery Time (Days):</label>
            <input type="number" name="default_delivery_days" class="form-control form-control-lg"
                value="<?php echo $data['default_delivery_days'] ?? '7'; ?>" min="1" max="365" placeholder="e.g., 7">
            <small class="form-text text-muted">Default number of days this supplier takes to deliver orders (can be
                customized per product)</small>
        </div>
        <input type="submit" class="btn btn-success" value="Submit">
    </form>
</div>

<script>
    // Live duplicate check for supplier name, email, and GST number
    function showFieldError(input, message) {
        input.classList.add('is-invalid');
        let feedback = input.parentElement.querySelector('.invalid-feedback');
        if (feedback) feedback.textContent = message;
    }
    function clearFieldError(input) {
        input.classList.remove('is-invalid');
        let feedback = input.parentElement.querySelector('.invalid-feedback');
        if (feedback) feedback.textContent = '';
    }

    function checkDuplicate(field, value, input) {
        if (!value) { clearFieldError(input); return; }
        fetch('<?php echo URLROOT; ?>/suppliers/check_duplicate', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ field, value })
        })
            .then(res => res.json())
            .then(data => {
                if (data.exists) {
                    showFieldError(input, data.message);
                } else {
                    clearFieldError(input);
                }
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const nameInput = document.querySelector('input[name="supplier_name"]');
        const emailInput = document.querySelector('input[name="email"]');
        const gstInput = document.querySelector('input[name="gst_number"]');

        if (nameInput) {
            nameInput.addEventListener('blur', function () {
                checkDuplicate('supplier_name', nameInput.value, nameInput);
            });
        }
        if (emailInput) {
            emailInput.addEventListener('blur', function () {
                checkDuplicate('email', emailInput.value, emailInput);
            });
        }
        if (gstInput) {
            gstInput.addEventListener('blur', function () {
                checkDuplicate('gst_number', gstInput.value, gstInput);
            });
        }
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>