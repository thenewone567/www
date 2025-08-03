<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<a href="<?php echo URLROOT; ?>/customers" class="btn btn-light"><i class="fa fa-arrow-left"></i> Back</a>
<div class="card card-body bg-light mt-5">
    <h2>Edit Customer</h2>
    <p>Edit the customer with this form</p>
    <form action="<?php echo URLROOT; ?>/customers/edit/<?php echo $data['id']; ?>" method="post">
        <div class="form-group">
            <label for="customer_name">Customer Name: <sup>*</sup></label>
            <input type="text" name="customer_name"
                class="form-control form-control-lg <?php echo (!empty($data['customer_name_err'])) ? 'is-invalid' : ''; ?>"
                value="<?php echo $data['customer_name']; ?>">
            <span class="invalid-feedback"><?php echo $data['customer_name_err']; ?></span>
        </div>
        <div class="form-group">
            <label for="contact_info">Contact Info:</label>
            <input type="text" name="contact_info" class="form-control form-control-lg"
                value="<?php echo $data['contact_info']; ?>">
        </div>
        <div class="form-group">
            <label for="credit_limit">Credit Limit:</label>
            <input type="number" step="0.01" name="credit_limit" class="form-control form-control-lg"
                value="<?php echo $data['credit_limit']; ?>">
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
<script src="<?php echo URLROOT; ?>/js/main.js"></script>
</body>

</html>