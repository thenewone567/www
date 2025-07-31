<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Inventory Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Inventory</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Summary Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $summary->total_products ?? 0 ?></h3>
                            <p>Total Products</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= number_format($summary->total_stock_quantity ?? 0) ?></h3>
                            <p>Total Stock Quantity</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-cubes"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>$<?= number_format($summary->total_stock_value ?? 0, 2) ?></h3>
                            <p>Total Stock Value</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $summary->low_stock_items ?? 0 ?></h3>
                            <p>Low Stock Items</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="btn-group" role="group">
                        <a href="/inventory/adjustments" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Stock Adjustments
                        </a>
                        <a href="/inventory/movements" class="btn btn-info">
                            <i class="fas fa-exchange-alt"></i> Stock Movements
                        </a>
                        <a href="/inventory/lowstock" class="btn btn-warning">
                            <i class="fas fa-exclamation-circle"></i> Low Stock Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Current Stock Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Current Stock Levels</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="stockTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Warehouse</th>
                                    <th>Location</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total Value</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($stock)): ?>
                                    <?php foreach ($stock as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item->product_code) ?></td>
                                            <td><?= htmlspecialchars($item->product_name) ?></td>
                                            <td><?= htmlspecialchars($item->category_name ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($item->warehouse_name ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($item->location_name ?? 'N/A') ?></td>
                                            <td>
                                                <span
                                                    class="badge <?= $item->quantity <= 10 ? 'badge-danger' : 'badge-success' ?>">
                                                    <?= number_format($item->quantity) ?>
                                                </span>
                                            </td>
                                            <td>$<?= number_format($item->unit_price, 2) ?></td>
                                            <td>$<?= number_format($item->quantity * $item->unit_price, 2) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary"
                                                    onclick="adjustStock(<?= $item->product_id ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No stock records found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function () {
        $('#stockTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "order": [[0, "asc"]]
        });
    });

    function adjustStock(productId) {
        // Redirect to stock adjustment page
        window.location.href = '/inventory/adjustments?product_id=' + productId;
    }
</script>


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