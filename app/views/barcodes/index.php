<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fa-solid fa-barcode text-primary mr-2"></i>
            Barcode Management
        </h1>
        <div>
            <a href="<?php echo URLROOT; ?>/barcodes/print" class="btn btn-success mr-2">
                <i class="fa-solid fa-print mr-1"></i> Print Barcodes
            </a>
            <a href="<?php echo URLROOT; ?>/barcodes/add" class="btn btn-primary">
                <i class="fa-solid fa-plus mr-1"></i> Add Barcode
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="kpi-card text-center">
                <i class="fa-solid fa-barcode fa-2x text-primary mb-2"></i>
                <h4 class="mb-1"><?php echo count($data['barcodes']); ?></h4>
                <small class="text-muted">Total Barcodes</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card text-center">
                <i class="fa-solid fa-box fa-2x text-success mb-2"></i>
                <h4 class="mb-1">
                    <?php echo count(array_filter($data['barcodes'], function ($b) {
                        return !empty($b->product_id);
                    })); ?>
                </h4>
                <small class="text-muted">Product Barcodes</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card text-center">
                <i class="fa-solid fa-print fa-2x text-info mb-2"></i>
                <h4 class="mb-1">0</h4>
                <small class="text-muted">Printed Today</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card text-center">
                <i class="fa-solid fa-qrcode fa-2x text-warning mb-2"></i>
                <h4 class="mb-1">0</h4>
                <small class="text-muted">QR Codes</small>
            </div>
        </div>
    </div>

    <!-- Barcodes Table -->
    <div class="kpi-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fa-solid fa-list mr-2"></i>
                All Barcodes
            </h5>
            <div class="input-group" style="width: 300px;">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fa-solid fa-search"></i>
                    </span>
                </div>
                <input type="text" class="form-control" placeholder="Search barcodes...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product ID</th>
                        <th>Barcode Value</th>
                        <th>Type</th>
                        <th>Preview</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['barcodes'])): ?>
                        <?php foreach ($data['barcodes'] as $barcode): ?>
                            <tr>
                                <td><?php echo $barcode->barcode_id; ?></td>
                                <td>
                                    <?php if (!empty($barcode->product_id)): ?>
                                        <span class="badge badge-primary"><?php echo $barcode->product_id; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <code><?php echo $barcode->barcode_value; ?></code>
                                </td>
                                <td>
                                    <span class="badge badge-secondary"><?php echo $barcode->type; ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($barcode->image)): ?>
                                        <img src="data:image/png;base64,<?php echo $barcode->image; ?>" alt="barcode"
                                            style="max-height: 40px;" />
                                    <?php else: ?>
                                        <span class="text-muted">No preview</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary"
                                            onclick="printSingleBarcode('<?php echo $barcode->barcode_value; ?>')"
                                            title="Print">
                                            <i class="fa-solid fa-print"></i>
                                        </button>
                                        <button class="btn btn-outline-info"
                                            onclick="viewBarcode('<?php echo $barcode->barcode_id; ?>')" title="View Details">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger"
                                            onclick="deleteBarcode('<?php echo $barcode->barcode_id; ?>')" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fa-solid fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <h5 class="text-muted">No barcodes found</h5>
                                <p class="text-muted">Create your first barcode to get started</p>
                                <a href="<?php echo URLROOT; ?>/barcodes/add" class="btn btn-primary">
                                    <i class="fa-solid fa-plus mr-1"></i> Add Barcode
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function printSingleBarcode(barcodeValue) {
        const printUrl = `${window.URLROOT}/barcodes/print?type=single&value=${encodeURIComponent(barcodeValue)}`;
        window.open(printUrl, '_blank', 'width=600,height=400');
    }

    function viewBarcode(barcodeId) {
        // Implementation for viewing barcode details
        window.location.href = `${window.URLROOT}/barcodes/view/${barcodeId}`;
    }

    function deleteBarcode(barcodeId) {
        if (confirm('Are you sure you want to delete this barcode?')) {
            // Implementation for deleting barcode
            // You can add AJAX call here
            window.location.href = `${window.URLROOT}/barcodes/delete/${barcodeId}`;
        }
    }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
    crossorigin="anonymous"></script>
<script src="<?php echo URLROOT; ?>/js/main.js"></script>
</body>

</html>