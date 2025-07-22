<?php
require_once 'config/config.php';

// Fetch all products
$stmt = $pdo->query("SELECT p.*, c.category_name FROM products p LEFT JOIN product_categories c ON p.category_id = c.category_id ORDER BY p.product_id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Product Management</h2>

<a href="index.php?page=products&action=new">Add New Product</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>SKU</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['product_id']) ?></td>
                <td><?= htmlspecialchars($product['product_name']) ?></td>
                <td><?= htmlspecialchars($product['category_name']) ?></td>
                <td><?= htmlspecialchars($product['sku']) ?></td>
                <td><?= htmlspecialchars($product['stock_quantity']) ?></td>
                <td>
                    <a href="index.php?page=products&action=edit&id=<?= $product['product_id'] ?>">Edit</a>
                    <a href="src/actions/delete_product.php?id=<?= $product['product_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
