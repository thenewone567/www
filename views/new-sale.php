<?php require_once ROOT_PATH . 'views/header.php'; ?>

<div class="row">
    <div class="col-md-3">
        <?php require_once ROOT_PATH . 'views/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <h1>New Sale</h1>
        <hr>
        <form action="/sales/create" method="POST">
            <div class="form-group">
                <label for="product">Product</label>
                <input type="text" name="product" id="product" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="barcode">Barcode</label>
                <input type="text" name="barcode" id="barcode" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" name="quantity" id="quantity" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="discount">Discount</label>
                <input type="number" name="discount" id="discount" class="form-control" step="0.01">
            </div>
            <button type="submit" class="btn btn-primary">Create Sale</button>
        </form>
    </div>
</div>

<?php require_once ROOT_PATH . 'views/footer.php'; ?>
