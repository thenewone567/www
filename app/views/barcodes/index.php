<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
<div class="row">
    <div class="col-md-6">
        <h1>Barcodes</h1>
    </div>
    <div class="col-md-6">
        <a href="<?php echo URLROOT; ?>/barcodes/add" class="btn btn-primary float-right">
            <i class="fa fa-plus"></i> Add Barcode
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
        <?php if (!empty($data['barcodes'])): ?>
            <?php foreach ($data['barcodes'] as $barcode): ?>
                <tr>
                    <td><?php echo $barcode->barcode_id; ?></td>
                    <td><?php echo $barcode->product_id; ?></td>
                    <td><?php echo $barcode->barcode_value; ?></td>
                    <td><?php echo $barcode->type; ?></td>
                    <td>
                        <?php if (!empty($barcode->image)): ?>
                            <img src="data:image/png;base64,<?php echo $barcode->image; ?>" alt="barcode" />
                        <?php else: ?>
                            <span class="text-muted">No image</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center text-muted">No barcodes found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'footer.php'; ?>