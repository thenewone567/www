<?php require APPROOT . '/'views/layout/header.php'; ?>
    <div class="row">
        <div class="col-md-6">
            <h1>Warehouse Locations</h1>
        </div>
        <div class="col-md-6">
            <a href="<?php echo URLROOT; ?>/stock/addlocation" class="btn btn-primary pull-right">
                <i class="fa fa-pencil"></i> Add Location
            </a>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Rack</th>
                <th>Shelf</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach($data['locations'] as $location) : ?>
        <tr>
            <td><?php echo $location->location_id; ?></td>
            <td><?php echo $location->location_name; ?></td>
            <td><?php echo $location->rack; ?></td>
            <td><?php echo $location->shelf; ?></td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
<?php require APPROOT . '/'views/layout/footer.php'; ?>
