<?php require APPROOT . '/'views/layout/header.php'; ?>
    <div class="row">
        <div class="col-md-6">
            <h1>Barcodes</h1>
        </div>
        <div class="col-md-6">
            <a href="<?php echo URLROOT; ?>/barcodes/add" class="btn btn-primary pull-right">
                <i class="fa fa-pencil"></i> Add Barcode
            </a>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product ID</th>
                <th>Barcode</th>
                <th>Type</th>
                <th>Image</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach($data['barcodes'] as $barcode) : ?>
        <tr>
            <td><?php echo $barcode->barcode_id; ?></td>
            <td><?php echo $barcode->product_id; ?></td>
            <td><?php echo $barcode->barcode_value; ?></td>
            <td><?php echo $barcode->type; ?></td>
            <td>
                <img src="data:image/png;base64,<?php echo $barcode->image; ?>" alt="barcode"   />
            </td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
<?php require APPROOT . '/'views/layout/footer.php'; ?>
