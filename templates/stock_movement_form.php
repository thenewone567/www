<?php
require_once 'config/config.php';

// Fetch products for dropdown
$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>New Stock Movement</h2>

<form action="src/actions/create_stock_movement.php" method="post">
    <label for="product_id">Product:</label>
    <select name="product_id" id="product_id" required>
        <option value="">Select Product</option>
        <?php foreach ($products as $product): ?>
            <option value="<?= $product['product_id'] ?>"><?= htmlspecialchars($product['product_name']) ?></option>
        <?php endforeach; ?>
    </select>
    <br>

    <label for="from_location">From Location:</label>
    <input type="text" name="from_location" id="from_location" placeholder="e.g., Warehouse A">
    <br>

    <label for="to_location">To Location:</label>
    <input type="text" name="to_location" id="to_location" placeholder="e.g., Shop Floor">
    <br>

    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" id="quantity" value="1" min="1" required>
    <br>

    <button type="submit">Move Stock</button>
</form>
