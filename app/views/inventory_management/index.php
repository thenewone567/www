<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid mt-0 pt-3">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-1 font-weight-bold">
                <i class="fas fa-boxes mr-2"></i>Inventory Management
            </h1>
            <small class="text-muted">Manage stock, adjustments, and inventory reports</small>
        </div>
    </div>
    <!-- Inventory Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="btn-group" role="group">
                <button class="btn btn-primary" onclick="openStockAdjustment()">
                    <i class="fas fa-edit mr-1"></i> Stock Adjustment
                </button>
                <button class="btn btn-info" onclick="viewStockMovements()">
                    <i class="fas fa-exchange-alt mr-1"></i> Stock Movements
                </button>
                <button class="btn btn-warning" onclick="generateLowStockReport()">
                    <i class="fas fa-exclamation-circle mr-1"></i> Low Stock Report
                </button>
                <button class="btn btn-success" onclick="performStockTake()">
                    <i class="fas fa-clipboard-list mr-1"></i> Stock Take
                </button>
            </div>
        </div>
    </div>
    <!-- Stock Table -->
    <div class="table-responsive">
        <table id="stockTable" class="table table-hover">
            <thead class="thead-light">
                <tr>
                    <th>SKU</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Current Stock</th>
                    <th>Reorder Level</th>
                    <th>Unit Price</th>
                    <th>Total Value</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['products'])): ?>
                    <?php foreach ($data['products'] as $product): ?>
                        <tr>
                            <td>
                                <span class="font-weight-bold">
                                    <?php echo htmlspecialchars($product->sku ?? 'N/A'); ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($product->image_path)): ?>
                                        <img src="<?php echo URLROOT . '/' . $product->image_path; ?>" class="rounded mr-2"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded mr-2 d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="font-weight-bold">
                                            <?php echo htmlspecialchars($product->product_name); ?>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($product->brand_name ?? 'No Brand'); ?>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-secondary">
                                    <?php echo htmlspecialchars($product->category_name ?? 'Uncategorized'); ?>
                                </span>
                            </td>
                            <td>
                                <span class="font-weight-bold">
                                    <?php echo number_format($product->current_stock ?? 0); ?>
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">
                                    <?php echo number_format($product->reorder_level ?? 0); ?>
                                </span>
                            </td>
                            <td>
                                $<?php echo number_format($product->selling_price ?? 0, 2); ?>
                            </td>
                            <td>
                                <span class="font-weight-bold">
                                    $<?php echo number_format(($product->selling_price ?? 0) * ($product->current_stock ?? 0), 2); ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $stock = $product->current_stock ?? 0;
                                $reorder = $product->reorder_level ?? 0;

                                if ($stock <= 0) {
                                    echo '<span class="badge badge-danger">Out of Stock</span>';
                                } elseif ($stock <= $reorder) {
                                    echo '<span class="badge badge-warning">Low Stock</span>';
                                } else {
                                    echo '<span class="badge badge-success">In Stock</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary"
                                        onclick="adjustStock(<?php echo $product->product_id; ?>)" data-toggle="tooltip"
                                        title="Adjust Stock">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-info"
                                        onclick="viewProduct(<?php echo $product->product_id; ?>)" data-toggle="tooltip"
                                        title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="<?php echo URLROOT; ?>/products/edit/<?php echo $product->product_id; ?>"
                                        class="btn btn-outline-secondary" data-toggle="tooltip" title="Edit Product">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-box-open fa-3x mb-3"></i>
                                <p>No products found</p>
                                <a href="<?php echo URLROOT; ?>/products/add" class="btn btn-primary">
                                    <i class="fas fa-plus mr-1"></i> Add Your First Product
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#stockTable').DataTable({
            "responsive": true,
            "pageLength": 25,
            "order": [[7, "asc"]],
            "columnDefs": [
                { "orderable": false, "targets": [8] }
            ]
        });
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>