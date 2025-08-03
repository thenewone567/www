<?php
// Direct test of addProduct method
require_once 'app/config.php';
require_once 'app/Database.php';
require_once 'app/models/Product.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$productModel = new Product();

$productData = [
    'product_name' => 'Debug Test Product',
    'sku' => 'DEBUG001',
    'supplier_code' => null,
    'category_id' => 1,
    'brand_id' => 1,
    'unit_id' => 1,
    'min_stock_level' => 5,
    'max_stock_level' => 100,
    'reorder_level' => 10,
    'purchase_price' => 20.00,
    'selling_price' => 29.99,
    'weight' => null,
    'dimensions' => null,
    'warranty_period' => null,
    'image_path' => null
];

echo "Testing addProduct directly...\n";

// Let's also check what happens with a simpler insert
$db = new Database();

try {
    echo "Testing simple database insert...\n";
    
    $db->query("INSERT INTO products (product_name, sku, category_id, purchase_price, selling_price) VALUES (:name, :sku, :cat, :purchase, :selling)");
    $db->bind(':name', 'Simple Test Product');
    $db->bind(':sku', 'SIMPLE001');
    $db->bind(':cat', 1);
    $db->bind(':purchase', 20.00);
    $db->bind(':selling', 29.99);
    
    echo "Executing query...\n";
    $result = $db->execute();
    echo "Execute result: " . ($result ? 'true' : 'false') . "\n";
    
    if ($result) {
        echo "✓ Simple insert worked\n";
        $id = $db->lastInsertId();
        echo "  Product ID: $id\n";
    } else {
        echo "✗ Simple insert failed\n";
    }
    
} catch (Exception $e) {
    echo "✗ Simple insert exception: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "✗ Simple insert fatal error: " . $e->getMessage() . "\n";
}

// Now test the model method
try {
    $result = $productModel->addProduct($productData);
    if ($result) {
        echo "✓ Product added successfully! Product ID: $result\n";
    } else {
        echo "✗ addProduct returned false\n";
        echo "Check error logs for details\n";
    }
} catch (Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "✗ Fatal error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
