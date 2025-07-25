<?php require_once ROOT_PATH . 'views/header.php'; ?>

<div class="row">
    <div class="col-md-3">
        <?php require_once ROOT_PATH . 'views/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <h1>New Purchase</h1>
        <hr>
        <form action="/purchases/create" method="POST">
            <div class="form-group">
                <label for="supplier">Supplier</label>
                <input type="text" name="supplier" id="supplier" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="product">Product</label>
                <input type="text" name="product" id="product" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" name="quantity" id="quantity" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="cost">Cost</label>
                <input type="number" name="cost" id="cost" class="form-control" step="0.01" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Purchase</button>
        </form>
    </div>
</div>

<?php require_once ROOT_PATH . 'views/footer.php'; ?>
