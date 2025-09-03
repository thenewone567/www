<?php
require_once 'bootstrap.php';

$db = new Database();
$db->query('SELECT product_id, product_name, dimensions FROM products ORDER BY product_id DESC LIMIT 3');
$products = $db->resultSet();

echo "=== Latest Products ===\n";
foreach ($products as $product) {
    echo "ID: " . $product->product_id . " | Name: " . $product->product_name . "\n";
    echo "Dimensions: " . ($product->dimensions ?? 'NULL') . "\n";
    echo "Length: " . strlen($product->dimensions ?? '') . " characters\n";

    if ($product->dimensions) {
        $decoded = json_decode($product->dimensions, true);
        if ($decoded) {
            echo "Valid JSON - Parsed:\n";
            foreach ($decoded as $key => $value) {
                echo "  $key: $value\n";
            }
        } else {
            echo "INVALID JSON!\n";
            echo "JSON Error: " . json_last_error_msg() . "\n";
        }
    }
    echo "---\n";
}
?>