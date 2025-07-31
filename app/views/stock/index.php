<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
<div class="row">
    <div class="col-md-6">
        <h1>Stock Levels</h1>
    </div>
    <div class="col-md-6">
        <a href="<?php echo URLROOT; ?>/stock/add" class="btn btn-primary float-right">
            <i class="fa fa-plus"></i> Add Stock
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
        <?php if (!empty($data['stock'])): ?>
            <?php foreach ($data['stock'] as $stock): ?>
                <tr>
                    <td><?php echo $stock->stock_id; ?></td>
                    <td><?php echo $stock->product_id; ?></td>
                    <td><?php echo $stock->batch_number; ?></td>
                    <td><?php echo $stock->expiry_date; ?></td>
                    <td><?php echo $stock->quantity; ?></td>
                    <td><?php echo $stock->location_id; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center text-muted">No stock found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="row mt-5">
    <div class="col-md-6">
        <h1>Stock Movements</h1>
    </div>
    <div class="col-md-6">
        <a href="<?php echo URLROOT; ?>/stock/move" class="btn btn-primary float-right">
            <i class="fa fa-plus"></i> Move Stock
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
        <?php if (!empty($data['movements'])): ?>
            <?php foreach ($data['movements'] as $movement): ?>
                <tr>
                    <td><?php echo $movement->movement_id; ?></td>
                    <td><?php echo $movement->product_id; ?></td>
                    <td><?php echo $movement->from_location_id; ?></td>
                    <td><?php echo $movement->to_location_id; ?></td>
                    <td><?php echo $movement->quantity; ?></td>
                    <td><?php echo $movement->movement_date; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center text-muted">No stock movements found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="row mt-5">
    <div class="col-md-6">
        <h1>Warehouse Locations</h1>
    </div>
    <div class="col-md-6">
        <a href="<?php echo URLROOT; ?>/stock/addlocation" class="btn btn-primary float-right">
            <i class="fa fa-plus"></i> Add Location
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
        <?php if (!empty($data['locations'])): ?>
            <?php foreach ($data['locations'] as $location): ?>
                <tr>
                    <td><?php echo $location->location_id; ?></td>
                    <td><?php echo $location->location_name; ?></td>
                    <td><?php echo $location->rack; ?></td>
                    <td><?php echo $location->shelf; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center text-muted">No warehouse locations found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

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