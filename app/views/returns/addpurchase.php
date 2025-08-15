<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<a href="<?php echo URLROOT; ?>/returns" class="btn btn-light"><i class="fa fa-arrow-left"></i> Back</a>
<div class="card card-body theme-card-light mt-5">
    <h2>Add Purchase Return</h2>
    <p>Create a new purchase return with this form</p>
    <form action="<?php echo URLROOT; ?>/returns/addpurchase" method="post">
        <div class="form-group">
            <label for="purchase_id">Purchase ID: <sup>*</sup></label>
            <input type="text" name="purchase_id"
                class="form-control form-control-lg <?php echo (!empty($data['purchase_id_err'])) ? 'is-invalid' : ''; ?>"
                value="<?php echo $data['purchase_id']; ?>">
            <span class="invalid-feedback"><?php echo $data['purchase_id_err']; ?></span>
        </div>
        <div class="form-group">
            <label for="return_date">Return Date: <sup>*</sup></label>
            <input type="date" name="return_date"
                class="form-control form-control-lg <?php echo (!empty($data['return_date_err'])) ? 'is-invalid' : ''; ?>"
                value="<?php echo $data['return_date']; ?>">
            <span class="invalid-feedback"><?php echo $data['return_date_err']; ?></span>
        </div>
        <div class="form-group">
            <label for="reason">Reason:</label>
            <textarea name="reason" class="form-control form-control-lg"><?php echo $data['reason']; ?></textarea>
        </div>
        <input type="submit" class="btn btn-success" value="Submit">
    </form>
</div>

</div> <!-- End container-fluid -->
</div> <!-- End page-content-wrapper -->
</div> <!-- End wrapper -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
    crossorigin="anonymous"></script>
<script src="<?php echo URLROOT; ?>/public/js/main.js"></script>
</body>

</html>