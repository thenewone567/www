<?php require APPROOT . DS . 'app' . DS . views/layout/header.php'; ?>
    <a href="<?php echo URLROOT; ?>/suppliers" class="btn btn-light"><i class="fa fa-backward"></i> Back</a>
    <div class="card card-body bg-light mt-5">
        <h2>Edit Supplier</h2>
        <p>Edit the supplier with this form</p>
        <form action="<?php echo URLROOT; ?>/suppliers/edit/<?php echo $data['id']; ?>" method="post">
            <div class="form-group">
                <label for="supplier_name">Supplier Name: <sup>*</sup></label>
                <input type="text" name="supplier_name" class="form-control form-control-lg <?php echo (!empty($data['supplier_name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['supplier_name']; ?>">
                <span class="invalid-feedback"><?php echo $data['supplier_name_err']; ?></span>
            </div>
            <div class="form-group">
                <label for="contact_info">Contact Info:</label>
                <input type="text" name="contact_info" class="form-control form-control-lg" value="<?php echo $data['contact_info']; ?>">
            </div>
            <div class="form-group">
                <label for="gst_info">GST Info:</label>
                <input type="text" name="gst_info" class="form-control form-control-lg" value="<?php echo $data['gst_info']; ?>">
            </div>
            <div class="form-group">
                <label for="due_amount">Due Amount:</label>
                <input type="number" step="0.01" name="due_amount" class="form-control form-control-lg" value="<?php echo $data['due_amount']; ?>">
            </div>
            <input type="submit" class="btn btn-success" value="Submit">
        </form>
    </div>
<?php require APPROOT . DS . 'app' . DS . views/layout/footer.php'; ?>
