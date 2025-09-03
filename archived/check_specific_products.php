<?php
require_once 'bootstrap.php';

echo "CHECKING SPECIFIC PRODUCTS MENTIONED BY USER\n";
echo "===========================================\n";

try {
    $db = new Database();

    // Check for the specific products mentioned: "Integration Test Hammer 2" and "Integration Test Product"
    $db->query("SELECT product_id, product_name, model_number, stock_quantity, is_active FROM products WHERE product_name LIKE '%hammer%' OR product_name LIKE '%Integration%' ORDER BY product_name");
    $products = $db->resultSet();

    echo "Found " . count($products) . " matching products:\n";
    echo "================================================\n";

    foreach ($products as $product) {
        echo "Product: " . $product->product_name . "\n";
        echo "Model/Batch: " . ($product->model_number ?? 'N/A') . "\n";
        echo "Stock: " . ($product->stock_quantity ?? 'NULL') . "\n";
        echo "Active: " . ($product->is_active ? 'Yes' : 'No') . "\n";
        echo "---\n";
    }

    if (count($products) == 0) {
        echo "No matching products found.\n";
        echo "Let's check all products to see what's available:\n\n";

        $db->query("SELECT product_name, stock_quantity FROM products ORDER BY product_name LIMIT 10");
        $allProducts = $db->resultSet();

        echo "Sample of all products:\n";
        foreach ($allProducts as $product) {
            echo "- " . $product->product_name . " (Stock: " . $product->stock_quantity . ")\n";
        }
    }

    // Also check total active products
    $db->query("SELECT COUNT(*) as total FROM products WHERE is_active = 1");
    $result = $db->single();
    $totalActive = $result ? $result->total : 0;

    echo "\nTotal active products in database: " . $totalActive . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>