<?php
require_once 'config/config.php';

// Fetch products and suppliers for dropdowns
$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
$suppliers = $pdo->query("SELECT * FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>New Purchase</h2>

<form action="src/actions/create_purchase.php" method="post" enctype="multipart/form-data">
    <label for="supplier_id">Supplier:</label>
    <select name="supplier_id" id="supplier_id" required>
        <option value="">Select Supplier</option>
        <?php foreach ($suppliers as $supplier): ?>
            <option value="<?= $supplier['supplier_id'] ?>"><?= htmlspecialchars($supplier['supplier_name']) ?></option>
        <?php endforeach; ?>
    </select>
    <br>

    <div id="purchase-items">
        <div class="purchase-item">
            <select name="product_id[]" class="product-select">
                <option value="">Select Product</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['product_id'] ?>"><?= htmlspecialchars($product['product_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="quantity[]" value="1" min="1">
            <input type="number" name="unit_price[]" step="0.01" placeholder="Unit Price">
        </div>
    </div>

    <button type="button" id="add-item">Add Another Item</button>
    <hr>

    <label for="invoice_attachment">Invoice Attachment (PDF/JPG):</label>
    <input type="file" name="invoice_attachment" id="invoice_attachment">
    <br>

    <button type="submit">Create Purchase</button>
</form>

<script>
document.getElementById('add-item').addEventListener('click', function() {
    const item = document.querySelector('.purchase-item');
    const clone = item.cloneNode(true);
    document.getElementById('purchase-items').appendChild(clone);
});
</script>
