<?php
require_once 'config/config.php';

$product = [
    'product_id' => '',
    'product_name' => '',
    'category_id' => '',
    'sku' => '',
    'stock_quantity' => '',
    'min_stock_alert' => '',
    'max_stock_alert' => '',
    'warehouse_location' => '',
    'batch_number' => '',
    'expiry_date' => '',
    'reorder_level' => '',
    'supplier_id' => '',
    'product_image' => ''
];
$is_edit = false;

if (isset($_GET['id'])) {
    $is_edit = true;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch categories and suppliers for dropdowns
$categories = $pdo->query("SELECT * FROM product_categories")->fetchAll(PDO::FETCH_ASSOC);
$suppliers = $pdo->query("SELECT * FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2><?= $is_edit ? 'Edit Product' : 'Add New Product' ?></h2>

<form action="src/actions/save_product.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">

    <label for="product_name">Product Name:</label>
    <input type="text" name="product_name" id="product_name" value="<?= htmlspecialchars($product['product_name']) ?>" required>
    <br>

    <label for="category_id">Category:</label>
    <select name="category_id" id="category_id">
        <option value="">Select Category</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= $category['category_id'] ?>" <?= $product['category_id'] == $category['category_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($category['category_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br>

    <label for="sku">SKU:</label>
    <input type="text" name="sku" id="sku" value="<?= htmlspecialchars($product['sku']) ?>">
    <br>

    <label for="stock_quantity">Stock Quantity:</label>
    <input type="number" name="stock_quantity" id="stock_quantity" value="<?= htmlspecialchars($product['stock_quantity']) ?>">
    <br>

    <label for="aisle">Aisle:</label>
    <input type="text" name="aisle" id="aisle" value="<?= htmlspecialchars($product['aisle'] ?? '') ?>">
    <br>

    <label for="rack">Rack:</label>
    <input type="text" name="rack" id="rack" value="<?= htmlspecialchars($product['rack'] ?? '') ?>">
    <br>

    <label for="product_image">Product Image:</label>
    <input type="file" name="product_image" id="product_image">
    <br>

    <label for="icon">Icon:</label>
    <input type="file" name="icon" id="icon">
    <br>

    <button type="submit"><?= $is_edit ? 'Update Product' : 'Save Product' ?></button>
</form>
