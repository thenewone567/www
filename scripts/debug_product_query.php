<?php
require_once __DIR__ . '/../bootstrap.php';

$db = new Database();
$productId = 139;

echo "Debugging product ID 139:\n";

// Test basic query
$db->query("SELECT COUNT(*) as count FROM products");
$db->execute();
$count = $db->single();
echo "Total products in database: {$count->count}\n";

// Test specific product
$db->query("SELECT product_id, product_name FROM products WHERE product_id = :product_id");
$db->bind(':product_id', $productId);
$db->execute();
$product = $db->single();

if ($product) {
    echo "Found product: ID={$product->product_id}, Name={$product->product_name}\n";
} else {
    echo "Product 139 not found. Let's check what products exist:\n";

    $db->query("SELECT product_id, product_name FROM products WHERE product_id BETWEEN 135 AND 145");
    $db->execute();
    $products = $db->resultSet();

    foreach ($products as $p) {
        echo "ID: {$p->product_id}, Name: {$p->product_name}\n";
    }
}
?>