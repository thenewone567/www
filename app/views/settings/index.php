<?php require APPROOT . '/views/layout/header.php'; ?>
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
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
<?php require APPROOT . '/views/layout/footer.php'; ?>
