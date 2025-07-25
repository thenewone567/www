<?php require_once ROOT_PATH . 'views/header.php'; ?>

<div class="row">
    <div class="col-md-3">
        <?php require_once ROOT_PATH . 'views/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <h1>Import Products from CSV</h1>
        <hr>
        <form action="/products/import-csv" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="csvFile">CSV File</label>
                <input type="file" name="csvFile" id="csvFile" class="form-control-file" required>
            </div>
            <button type="submit" class="btn btn-primary">Import</button>
        </form>
    </div>
</div>

<?php require_once ROOT_PATH . 'views/footer.php'; ?>
