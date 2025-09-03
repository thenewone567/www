<?php
require_once 'bootstrap.php';

$db = new Database();
$db->query('SELECT product_id, product_name, dimensions FROM products LIMIT 5');
$products = $db->resultSet();

echo "=== Product Dimensions Test ===\n";
foreach ($products as $product) {
    echo "ID: " . $product->product_id . " | Name: " . $product->product_name . " | Dimensions: " . ($product->dimensions ?? 'NULL') . "\n";

    if ($product->dimensions) {
        $decoded = json_decode($product->dimensions, true);
        if ($decoded) {
            echo "  Parsed: " . print_r($decoded, true) . "\n";
        } else {
            echo "  Invalid JSON: " . $product->dimensions . "\n";
        }
    }
    echo "---\n";
}
?>