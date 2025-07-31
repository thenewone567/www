<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
<a href="<?php echo URLROOT; ?>/stocks/locations" class="btn btn-light"><i class="fa fa-arrow-left"></i> Back</a>
<div class="card card-body bg-light mt-5">
    <h2>Add Warehouse Location</h2>
    <p>Create a new warehouse location with this form</p>
    <form action="<?php echo URLROOT; ?>/stock/addlocation" method="post">
        <div class="form-group">
            <label for="location_name">Location Name: <sup>*</sup></label>
            <input type="text" name="location_name"
                class="form-control form-control-lg <?php echo (!empty($data['location_name_err'])) ? 'is-invalid' : ''; ?>"
                value="<?php echo $data['location_name']; ?>">
            <span class="invalid-feedback"><?php echo $data['location_name_err']; ?></span>
        </div>
        <div class="form-group">
            <label for="rack">Rack:</label>
            <input type="text" name="rack" class="form-control form-control-lg" value="<?php echo $data['rack']; ?>">
        </div>
        <div class="form-group">
            <label for="shelf">Shelf:</label>
            <input type="text" name="shelf" class="form-control form-control-lg" value="<?php echo $data['shelf']; ?>">
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