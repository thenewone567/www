<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<a href="<?php echo URLROOT; ?>/stock" class="btn btn-light"><i class="fa fa-arrow-left"></i> Back</a>
<div class="card card-body bg-light mt-5">
    <h2>Move Stock</h2>
    <p>Move stock from one location to another with this form</p>
    <form action="<?php echo URLROOT; ?>/stock/move" method="post">
        <div class="form-group">
            <label for="product_id">Product ID: <sup>*</sup></label>
            <input type="text" name="product_id"
                class="form-control form-control-lg <?php echo (!empty($data['product_id_err'])) ? 'is-invalid' : ''; ?>"
                value="<?php echo $data['product_id']; ?>">
            <span class="invalid-feedback"><?php echo $data['product_id_err']; ?></span>
        </div>
        <div class="form-group">
            <label for="from_location_id">From Location ID: <sup>*</sup></label>
            <input type="text" name="from_location_id" class="form-control form-control-lg"
                value="<?php echo $data['from_location_id']; ?>">
        </div>
        <div class="form-group">
            <label for="to_location_id">To Location ID: <sup>*</sup></label>
            <input type="text" name="to_location_id" class="form-control form-control-lg"
                value="<?php echo $data['to_location_id']; ?>">
        </div>
        <div class="form-group">
            <label for="quantity">Quantity: <sup>*</sup></label>
            <input type="number" name="quantity"
                class="form-control form-control-lg <?php echo (!empty($data['quantity_err'])) ? 'is-invalid' : ''; ?>"
                value="<?php echo $data['quantity']; ?>">
            <span class="invalid-feedback"><?php echo $data['quantity_err']; ?></span>
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