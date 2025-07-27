<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
    <a href="<?php echo URLROOT; ?>/returns" class="btn btn-light"><i class="fa fa-backward"></i> Back</a>
    <div class="card card-body bg-light mt-5">
        <h2>Add Purchase Return</h2>
        <p>Create a new purchase return with this form</p>
        <form action="<?php echo URLROOT; ?>/returns/addpurchase" method="post">
            <div class="form-group">
                <label for="purchase_id">Purchase ID: <sup>*</sup></label>
                <input type="text" name="purchase_id" class="form-control form-control-lg <?php echo (!empty($data['purchase_id_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['purchase_id']; ?>">
                <span class="invalid-feedback"><?php echo $data['purchase_id_err']; ?></span>
            </div>
            <div class="form-group">
                <label for="return_date">Return Date: <sup>*</sup></label>
                <input type="date" name="return_date" class="form-control form-control-lg <?php echo (!empty($data['return_date_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['return_date']; ?>">
                <span class="invalid-feedback"><?php echo $data['return_date_err']; ?></span>
            </div>
            <div class="form-group">
                <label for="reason">Reason:</label>
                <textarea name="reason" class="form-control form-control-lg"><?php echo $data['reason']; ?></textarea>
            </div>
            <input type="submit" class="btn btn-success" value="Submit">
        </form>
    </div>
<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'footer.php'; ?>
