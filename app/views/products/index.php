<?php require APPROOT . '/' . ''views/layout/header.php'; ?>
    <div class="row">
        <div class="col-md-6">
            <h1>Products</h1>
        </div>
        <div class="col-md-6">
            <a href="<?php echo URLROOT; ?>/products/add" class="btn btn-primary pull-right">
                <i class="fa fa-pencil"></i> Add Product
            </a>
        </div>
    </div>
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
    <?php foreach($data['products'] as $product) : ?>
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
                <a href="<?php echo URLROOT; ?>/products/edit/<?php echo $product->product_id; ?>" class="btn btn-dark">Edit</a>
                <form class="inline" action="<?php echo URLROOT; ?>/products/delete/<?php echo $product->product_id; ?>" method="post">
                    <input type="submit" value="Delete" class="btn btn-danger">
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
<?php require APPROOT . '/' . ''views/layout/footer.php'; ?>
