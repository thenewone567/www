<?php require_once ROOT_PATH . 'views/header.php'; ?>

<div class="row">
    <div class="col-md-3">
        <?php require_once ROOT_PATH . 'views/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <h1>Putaway</h1>
        <hr>
        <form action="/inventory/putaway" method="POST">
            <div class="form-group">
                <label for="product">Product</label>
                <input type="text" name="product" id="product" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" name="quantity" id="quantity" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <select name="location" id="location" class="form-control" required>
                    <!-- In a real application, these options would be populated from the database -->
                    <option value="1">Location A</option>
                    <option value="2">Location B</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Putaway</button>
        </form>
    </div>
</div>

<?php require_once ROOT_PATH . 'views/footer.php'; ?>
