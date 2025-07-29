<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
<div class="products-top-area container-fluid mt-0 pt-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h1 class="mb-0">Products</h1>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <a href="<?php echo URLROOT; ?>/products/add" class="btn btn-primary">
                <i class="fa fa-plus"></i> Add Product
            </a>
        </div>
    </div>
</div>
<div class="table-responsive mt-3">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Unit</th>
                <th>Min Stock</th>
                <th>Max Stock</th>
                <th>Reorder Level</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['products'])): ?>
                <?php foreach ($data['products'] as $product): ?>
                    <tr>
                        <td><?php echo $product->product_id; ?></td>
                        <td><?php echo $product->product_name; ?></td>
                        <td><?php echo $product->sku; ?></td>
                        <td><?php echo $product->category_id; ?></td>
                        <td><?php echo $product->brand_id; ?></td>
                        <td><?php echo $product->unit_id; ?></td>
                        <td><?php echo $product->min_stock_level; ?></td>
                        <td><?php echo $product->max_stock_level; ?></td>
                        <td><?php echo $product->reorder_level; ?></td>
                        <td>
                            <a href="<?php echo URLROOT; ?>/products/edit/<?php echo $product->product_id; ?>"
                                class="btn btn-dark">Edit</a>
                            <form class="d-inline"
                                action="<?php echo URLROOT; ?>/products/delete/<?php echo $product->product_id; ?>"
                                method="post" style="display:inline;">
                                <input type="submit" value="Delete" class="btn btn-danger">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center text-muted">No products found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'footer.php'; ?>