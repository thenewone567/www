<?php
require_once 'config/config.php';

$stmt = $pdo->query("SELECT * FROM products ORDER BY product_id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Inventory Management</h2>

<a href="index.php?page=products&action=new">Add New Product</a>

<table>
    <thead>
        <tr>
            <th>SKU</th>
            <th>Item</th>
            <th>Location</th>
            <th>Date Updated</th>
            <th>Quantity</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['sku']) ?></td>
                <td>
                    <?php if ($product['icon']): ?>
                        <img src="public/uploads/icons/<?= htmlspecialchars($product['icon']) ?>" alt="icon" width="20">
                    <?php endif; ?>
                    <?= htmlspecialchars($product['product_name']) ?>
                </td>
                <td><?= htmlspecialchars($product['aisle']) ?>-<?= htmlspecialchars($product['rack']) ?></td>
                <td><?= htmlspecialchars($product['last_updated']) ?></td>
                <td><?= htmlspecialchars($product['stock_quantity']) ?></td>
                <td>
                    <a href="#" class="edit-btn" data-id="<?= $product['product_id'] ?>"><img src="public/images/edit.png" alt="Edit" width="20"></a>
                    <a href="src/actions/delete_product.php?id=<?= $product['product_id'] ?>" onclick="return confirm('Are you sure?')"><img src="public/images/delete.png" alt="Delete" width="20"></a>
                    <a href="#" class="barcode-btn" data-barcode="<?= htmlspecialchars($product['sku']) ?>">Barcode</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Stock Movement Modal -->
<div id="stock-movement-modal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Edit Stock</h2>
        <form id="stock-movement-form" action="src/actions/update_stock.php" method="post">
            <input type="hidden" name="product_id" id="modal-product-id">
            <label for="quantity">New Quantity:</label>
            <input type="number" name="quantity" id="modal-quantity" required>
            <br>
            <label for="aisle">Aisle:</label>
            <input type="text" name="aisle" id="modal-aisle">
            <br>
            <label for="rack">Rack:</label>
            <input type="text" name="rack" id="modal-rack">
            <br>
            <button type="submit">Update Stock</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('stock-movement-modal');
    const closeBtn = document.querySelector('.close-btn');

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.id;
            // You would typically fetch the current product data via AJAX here
            // For now, we'll just populate the product ID
            document.getElementById('modal-product-id').value = productId;
            modal.style.display = 'block';
        });
    });

    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    window.addEventListener('click', function(e) {
        if (e.target == modal) {
            modal.style.display = 'none';
        }
    });

    const barcodeModal = document.getElementById('barcode-modal');
    const barcodeImage = document.getElementById('barcode-image');
    const closeBarcodeBtn = document.querySelector('#barcode-modal .close-btn');

    document.querySelectorAll('.barcode-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const barcodeValue = this.dataset.barcode;
            if (barcodeValue) {
                JsBarcode(barcodeImage, barcodeValue);
                barcodeModal.style.display = 'block';
            }
        });
    });

    closeBarcodeBtn.addEventListener('click', function() {
        barcodeModal.style.display = 'none';
    });
});
</script>

<!-- Barcode Modal -->
<div id="barcode-modal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Product Barcode</h2>
        <svg id="barcode-image"></svg>
    </div>
</div>
