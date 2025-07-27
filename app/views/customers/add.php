<?php require APPROOT . DS . 'views/layout/header.php'; ?>
    <a href="<?php echo URLROOT; ?>/customers" class="btn btn-light"><i class="fa fa-backward"></i> Back</a>
    <div class="card card-body bg-light mt-5">
        <h2>Add Customer</h2>
        <p>Create a new customer with this form</p>
        <form action="<?php echo URLROOT; ?>/customers/add" method="post">
            <div class="form-group">
                <label for="customer_name">Customer Name: <sup>*</sup></label>
                <input type="text" name="customer_name" class="form-control form-control-lg <?php echo (!empty($data['customer_name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['customer_name']; ?>">
                <span class="invalid-feedback"><?php echo $data['customer_name_err']; ?></span>
            </div>
            <div class="form-group">
                <label for="contact_info">Contact Info:</label>
                <input type="text" name="contact_info" class="form-control form-control-lg" value="<?php echo $data['contact_info']; ?>">
            </div>
            <div class="form-group">
                <label for="credit_limit">Credit Limit:</label>
                <input type="number" step="0.01" name="credit_limit" class="form-control form-control-lg" value="<?php echo $data['credit_limit']; ?>">
            </div>
            <input type="submit" class="btn btn-success" value="Submit">
        </form>
    </div>
<?php require APPROOT . DS . 'views/layout/footer.php'; ?>
