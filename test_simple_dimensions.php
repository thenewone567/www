<?php
require_once 'bootstrap.php';

echo "=== Simple Product Dimension Test ===\n";

// Simple JSON test
$dimensionsData = [
    'width' => 10.5,
    'width_unit' => 'cm',
    'height' => 5.0,
    'height_unit' => 'cm',
    'length' => 15.2,
    'length_unit' => 'cm'
];

$dimensionsJson = json_encode($dimensionsData);
echo "Test JSON: " . $dimensionsJson . "\n";

// Test parsing back
$parsed = json_decode($dimensionsJson, true);
if ($parsed) {
    echo "Parsed successfully:\n";
    foreach ($parsed as $key => $value) {
        echo "  $key: $value\n";
    }
} else {
    echo "ERROR: Could not parse JSON\n";
}

// Now let's add via the product model directly
$productModel = new Product();

$data = [
    'product_name' => 'Test Dimension Product',
    'sku' => 'TEST-DIM-002',
    'product_type' => 'STANDARD',
    'product_status' => 'active',
    'width' => 10.5,
    'width_unit' => 'cm',
    'height' => 5.0,
    'height_unit' => 'cm',
    'length' => 15.2,
    'length_unit' => 'cm',
    'weight' => 0.5,
    'weight_unit' => 'kg',
    'has_expiry' => false,
    'has_warranty' => false
];

echo "\nAdding product via model...\n";
$result = $productModel->addProduct($data);

if ($result) {
    echo "Product added with ID: $result\n";

    // Get the product back
    $product = $productModel->getProductById($result);
    if ($product) {
        echo "Retrieved product dimensions: " . ($product->dimensions ?? 'NULL') . "\n";

        if ($product->dimensions) {
            $parsed = json_decode($product->dimensions, true);
            if ($parsed) {
                echo "Parsed dimensions:\n";
                foreach ($parsed as $key => $value) {
                    echo "  $key: $value\n";
                }
            }
        }
    }
} else {
    echo "Failed to add product\n";
}
?>