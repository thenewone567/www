<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
<div class="row">
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