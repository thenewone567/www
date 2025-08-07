<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<a href="<?php echo URLROOT; ?>/suppliers" class="btn btn-light"><i class="fa fa-arrow-left"></i> Back</a>
<div class="card card-body theme-card-light mt-5">
    <h2>Add Supplier</h2>
    <p>Create a new supplier with this form</p>
    <form action="<?php echo URLROOT; ?>/suppliers/add" method="post" data-verify="supplier"
        data-verify-redirect="<?php echo URLROOT; ?>/suppliers">
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
        <input type="submit" class="btn btn-success" value="Submit">
    </form>
</div>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>