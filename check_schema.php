<?php
require_once 'bootstrap.php';

echo "=== Checking Database Schema ===\n";

$db = new Database();
$db->query('DESCRIBE products');
$columns = $db->resultSet();

foreach ($columns as $column) {
    if ($column->Field == 'dimensions') {
        echo "Dimensions column found:\n";
        echo "  Type: " . $column->Type . "\n";
        echo "  Null: " . $column->Null . "\n";
        echo "  Default: " . ($column->Default ?? 'NULL') . "\n";
        echo "  Key: " . ($column->Key ?? 'None') . "\n";
        echo "  Extra: " . ($column->Extra ?? 'None') . "\n";
        break;
    }
}

// Check if there are any products with problematic dimensions
echo "\n=== Checking for problematic dimensions ===\n";
$db->query("SELECT product_id, product_name, dimensions, LENGTH(dimensions) as dim_length FROM products WHERE dimensions IS NOT NULL AND dimensions != '' ORDER BY product_id DESC LIMIT 5");
$products = $db->resultSet();

if (empty($products)) {
    echo "No products with dimensions found.\n";
} else {
    foreach ($products as $product) {
        echo "ID: " . $product->product_id . " | " . $product->product_name . "\n";
        echo "  Length: " . $product->dim_length . " chars\n";
        echo "  Content: " . $product->dimensions . "\n";

        $decoded = json_decode($product->dimensions, true);
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            echo "  ❌ INVALID JSON: " . json_last_error_msg() . "\n";
        } else {
            echo "  ✅ Valid JSON\n";
        }
        echo "---\n";
    }
}
?>