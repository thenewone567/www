<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<a href="<?php echo URLROOT; ?>/suppliers" class="btn btn-light btn-sm mb-2"><i class="fa fa-arrow-left"></i> Back</a>

<div class="supplier-form-compact">
    <div class="card card-body theme-card-light supplier-form mt-3">
        <h2>Add Supplier</h2>
        <p>Create a new supplier with this form</p>

        <form action="<?php echo URLROOT; ?>/suppliers/add" method="post">
            <div class="row">
                <!-- Left column: main inputs -->
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="supplier_name">Supplier Name <sup>*</sup></label>
                        <input type="text" name="supplier_name"
                            class="form-control form-control-sm <?php echo (!empty($data['supplier_name_err'])) ? 'is-invalid' : ''; ?>"
                            value="<?php echo $data['supplier_name']; ?>">
                        <span class="invalid-feedback"><?php echo $data['supplier_name_err']; ?></span>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="contact_person">Contact Person</label>
                            <input type="text" name="contact_person" class="form-control form-control-sm"
                                value="<?php echo $data['contact_person'] ?? ''; ?>" placeholder="John Doe">
                        </div>
                        <div class="form-group col-6">
                            <label for="phone">Phone</label>
                            <input type="tel" name="phone" class="form-control form-control-sm"
                                value="<?php echo $data['phone'] ?? ''; ?>" placeholder="+91 98765 43210">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="email">Email</label>
                            <input type="email" name="email"
                                class="form-control form-control-sm <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $data['email'] ?? ''; ?>" placeholder="supplier@company.com">
                            <span class="invalid-feedback"><?php echo $data['email_err'] ?? ''; ?></span>
                        </div>
                        <div class="form-group col-6">
                            <label for="gst_number">GST Number</label>
                            <input type="text" name="gst_number"
                                class="form-control form-control-sm <?php echo (!empty($data['gst_number_err'])) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $data['gst_number'] ?? ''; ?>" placeholder="22AAAAA0000A1Z5"
                                maxlength="15">
                            <span class="invalid-feedback"><?php echo $data['gst_number_err'] ?? ''; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Right column: address & actions -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea name="address" class="form-control form-control-sm" rows="5"
                            placeholder="Business address"><?php echo $data['address'] ?? ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="default_delivery_days">Default Delivery Time (Days)</label>
                        <input type="number" name="default_delivery_days" class="form-control form-control-sm"
                            value="<?php echo $data['default_delivery_days'] ?? '7'; ?>" min="1" max="365"
                            placeholder="7">
                        <small class="form-text text-muted">Default days to deliver</small>
                    </div>

                    <div class="form-group mt-2">
                        <input type="submit" class="btn btn-success btn-sm btn-block" value="Save Supplier">
                    </div>
                </div>
            </div>
        </form>
    </div>
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