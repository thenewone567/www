<?php require APPROOT . DS . 'app' . DS . '' . ''views/layout/header.php'; ?>
    <a href="<?php echo URLROOT; ?>/returns" class="btn btn-light"><i class="fa fa-backward"></i> Back</a>
    <div class="card card-body bg-light mt-5">
        <h2>Add Sale Return</h2>
        <p>Create a new sale return with this form</p>
        <form action="<?php echo URLROOT; ?>/returns/addsale" method="post">
            <div class="form-group">
                <label for="sale_id">Sale ID: <sup>*</sup></label>
                <input type="text" name="sale_id" class="form-control form-control-lg <?php echo (!empty($data['sale_id_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['sale_id']; ?>">
                <span class="invalid-feedback"><?php echo $data['sale_id_err']; ?></span>
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
            <div class="form-group">
                <label for="refund_amount">Refund Amount: <sup>*</sup></label>
                <input type="number" step="0.01" name="refund_amount" class="form-control form-control-lg" value="<?php echo $data['refund_amount']; ?>">
            </div>
            <input type="submit" class="btn btn-success" value="Submit">
        </form>
    </div>
<?php require APPROOT . DS . 'app' . DS . '' . ''views/layout/footer.php'; ?>
