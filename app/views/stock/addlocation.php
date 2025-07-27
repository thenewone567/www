<?php require APPROOT . '/views/layout/header.php'; ?>
    <a href="<?php echo URLROOT; ?>/stock/locations" class="btn btn-light"><i class="fa fa-backward"></i> Back</a>
    <div class="card card-body bg-light mt-5">
        <h2>Add Warehouse Location</h2>
        <p>Create a new warehouse location with this form</p>
        <form action="<?php echo URLROOT; ?>/stock/addlocation" method="post">
            <div class="form-group">
                <label for="location_name">Location Name: <sup>*</sup></label>
                <input type="text" name="location_name" class="form-control form-control-lg <?php echo (!empty($data['location_name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['location_name']; ?>">
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
<?php require APPROOT . '/views/layout/footer.php'; ?>
