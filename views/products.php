<?php require_once ROOT_PATH . 'views/header.php'; ?>

<div class="row">
    <div class="col-md-3">
        <?php require_once ROOT_PATH . 'views/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <h1>Products</h1>
        <hr>
        <form action="/products" method="GET" class="row mb-3">
            <div class="col">
                <input type="text" name="searchTerm" class="form-control" placeholder="Search for products..." value="<?php echo isset($_GET['searchTerm']) ? htmlspecialchars($_GET['searchTerm']) : ''; ?>">
            </div>
            <div class="col">
                <select name="searchType" class="form-control">
                    <option value="ProductName" <?php echo (isset($_GET['searchType']) && $_GET['searchType'] === 'ProductName') ? 'selected' : ''; ?>>Name</option>
                    <option value="SKU" <?php echo (isset($_GET['searchType']) && $_GET['searchType'] === 'SKU') ? 'selected' : ''; ?>>SKU</option>
                    <option value="Barcode" <?php echo (isset($_GET['searchType']) && $_GET['searchType'] === 'Barcode') ? 'selected' : ''; ?>>Barcode</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addProductModal">Add Product</button>
                <a href="/products/import-csv" class="btn btn-info">Import CSV</a>
                <a href="/products/export-csv" class="btn btn-success">Export CSV</a>
            </div>
        </form>
        <table class="table table-bordered" id="productsTable">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product) : ?>
                    <tr>
                        <td><img src="<?php echo $product['Photo']; ?>" alt="<?php echo $product['ProductName']; ?>" width="50"></td>
                        <td><?php echo $product['ProductName']; ?></td>
                        <td><?php echo $product['Description']; ?></td>
                        <td><?php echo $product['Price']; ?></td>
                        <td><?php echo $product['Quantity']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editProductModal" data-product='<?php echo json_encode($product); ?>'>Edit</button>
                            <a href="/products/delete?id=<?php echo $product['ProductID']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                            <a href="/products/qr-code?id=<?php echo $product['ProductID']; ?>" class="btn btn-sm btn-info">QR Code</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/products/add" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="productName">Product Name</label>
                        <input type="text" name="productName" id="productName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" name="price" id="price" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="photo">Photo</label>
                        <input type="file" name="photo" id="photo" class="form-control-file">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/products/edit" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="productID" id="editProductID">
                    <div class="form-group">
                        <label for="editProductName">Product Name</label>
                        <input type="text" name="productName" id="editProductName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="editDescription">Description</label>
                        <textarea name="description" id="editDescription" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editPrice">Price</label>
                        <input type="number" name="price" id="editPrice" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="editQuantity">Quantity</label>
                        <input type="number" name="quantity" id="editQuantity" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="editPhoto">Photo</label>
                        <input type="file" name="photo" id="editPhoto" class="form-control-file">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $('#editProductModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var product = button.data('product');
        var modal = $(this);
        modal.find('#editProductID').val(product.ProductID);
        modal.find('#editProductName').val(product.ProductName);
        modal.find('#editDescription').val(product.Description);
        modal.find('#editPrice').val(product.Price);
        modal.find('#editQuantity').val(product.Quantity);
    });

    // Barcode scanning simulation
    let barcode = '';
    let reading = false;

    document.addEventListener('keydown', e => {
        if (e.key === 'Enter') {
            if (barcode.length > 0) {
                document.querySelector('input[name="searchTerm"]').value = barcode;
                document.querySelector('select[name="searchType"]').value = 'Barcode';
                document.querySelector('form').submit();
            }
            barcode = '';
            return;
        }
        if (e.key !== 'Shift' && e.key !== 'Control' && e.key !== 'Alt') {
            barcode += e.key;
        }
    });
</script>

<?php require_once ROOT_PATH . 'views/footer.php'; ?>
