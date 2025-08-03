<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<h1>Settings</h1>
<?php flash('setting_message'); ?>
<form action="<?php echo URLROOT; ?>/settings" method="post">
    <div class="form-group">
        <label for="company_name">Company Name:</label>
        <input type="text" name="company_name" class="form-control"
            value="<?php echo isset($data['company_name']) ? $data['company_name'] : ''; ?>">
    </div>
    <div class="form-group">
        <label for="company_logo">Company Logo URL:</label>
        <input type="text" name="company_logo" class="form-control"
            value="<?php echo isset($data['company_logo']) ? $data['company_logo'] : ''; ?>">
    </div>
    <div class="form-group">
        <label for="company_gst">Company GST:</label>
        <input type="text" name="company_gst" class="form-control"
            value="<?php echo isset($data['company_gst']) ? $data['company_gst'] : ''; ?>">
    </div>
    <div class="form-group">
        <label for="currency">Currency:</label>
        <input type="text" name="currency" class="form-control"
            value="<?php echo isset($data['currency']) ? $data['currency'] : ''; ?>">
    </div>
    <div class="form-group">
        <label for="company_address">Company Address:</label>
        <textarea name="company_address"
            class="form-control"><?php echo isset($data['company_address']) ? $data['company_address'] : ''; ?></textarea>
    </div>
    <div class="form-group">
        <label for="company_email">Company Email:</label>
        <input type="email" name="company_email" class="form-control"
            value="<?php echo isset($data['company_email']) ? $data['company_email'] : ''; ?>">
    </div>
    <div class="form-group">
        <label for="company_phone">Company Phone:</label>
        <input type="text" name="company_phone" class="form-control"
            value="<?php echo isset($data['company_phone']) ? $data['company_phone'] : ''; ?>">
    </div>
    <button type="submit" class="btn btn-primary">Save Settings</button>
</form>

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