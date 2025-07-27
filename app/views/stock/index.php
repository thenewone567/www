<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
    <div class="row">
        <div class="col-md-6">
            <h1>Stock Levels</h1>
        </div>
        <div class="col-md-6">
            <a href="<?php echo URLROOT; ?>/stock/add" class="btn btn-primary pull-right">
                <i class="fa fa-pencil"></i> Add Stock
            </a>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product ID</th>
                <th>Batch Number</th>
                <th>Expiry Date</th>
                <th>Quantity</th>
                <th>Location ID</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach($data['stock'] as $stock) : ?>
        <tr>
            <td><?php echo $stock->stock_id; ?></td>
            <td><?php echo $stock->product_id; ?></td>
            <td><?php echo $stock->batch_number; ?></td>
            <td><?php echo $stock->expiry_date; ?></td>
            <td><?php echo $stock->quantity; ?></td>
            <td><?php echo $stock->location_id; ?></td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>

    <div class="row mt-5">
        <div class="col-md-6">
            <h1>Stock Movements</h1>
        </div>
        <div class="col-md-6">
            <a href="<?php echo URLROOT; ?>/stock/move" class="btn btn-primary pull-right">
                <i class="fa fa-pencil"></i> Move Stock
            </a>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product ID</th>
                <th>From</th>
                <th>To</th>
                <th>Quantity</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach($data['movements'] as $movement) : ?>
        <tr>
            <td><?php echo $movement->movement_id; ?></td>
            <td><?php echo $movement->product_id; ?></td>
            <td><?php echo $movement->from_location_id; ?></td>
            <td><?php echo $movement->to_location_id; ?></td>
            <td><?php echo $movement->quantity; ?></td>
            <td><?php echo $movement->movement_date; ?></td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>

    <div class="row mt-5">
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
<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'footer.php'; ?>
