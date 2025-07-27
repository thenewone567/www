<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
    <h1>Settings</h1>
    <?php flash('setting_message'); ?>
    <form action="<?php echo URLROOT; ?>/settings" method="post">
        <div class="form-group">
            <label for="company_name">Company Name:</label>
            <input type="text" name="company_name" class="form-control" value="<?php echo $data['company_name']; ?>">
        </div>
        <div class="form-group">
            <label for="company_logo">Company Logo URL:</label>
            <input type="text" name="company_logo" class="form-control" value="<?php echo $data['company_logo']; ?>">
        </div>
        <div class="form-group">
            <label for="company_gst">Company GST:</label>
            <input type="text" name="company_gst" class="form-control" value="<?php echo $data['company_gst']; ?>">
        </div>
        <div class="form-group">
            <label for="currency">Currency:</label>
            <input type="text" name="currency" class="form-control" value="<?php echo $data['currency']; ?>">
        </div>
        <div class="form-group">
            <label for="company_address">Company Address:</label>
            <textarea name="company_address" class="form-control"><?php echo $data['company_address']; ?></textarea>
        </div>
        <div class="form-group">
            <label for="company_email">Company Email:</label>
            <input type="email" name="company_email" class="form-control" value="<?php echo $data['company_email']; ?>">
        </div>
        <div class="form-group">
            <label for="company_phone">Company Phone:</label>
            <input type="text" name="company_phone" class="form-control" value="<?php echo $data['company_phone']; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'footer.php'; ?>
