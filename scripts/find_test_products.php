<?php
require_once __DIR__ . '/../bootstrap.php';

$db = new Database();

echo "Finding products with multiple suppliers:\n";

$db->query("
    SELECT p.product_id, p.product_name, COUNT(ps.supplier_id) as supplier_count
    FROM products p
    JOIN product_suppliers ps ON p.product_id = ps.product_id
    WHERE ps.is_active = 1
    GROUP BY p.product_id, p.product_name
    HAVING supplier_count >= 2
    ORDER BY supplier_count DESC
    LIMIT 5
");
$db->execute();
$products = $db->resultSet();

foreach ($products as $product) {
    echo "ID: {$product->product_id}, Name: {$product->product_name}, Suppliers: {$product->supplier_count}\n";
}
?>