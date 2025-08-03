<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
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