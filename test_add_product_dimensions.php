<?php
require_once 'bootstrap.php';

// Test adding a product with dimensions
$data = [
    'product_name' => 'Test Product with Dimensions',
    'sku' => 'TEST-DIM-001',
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
    'has_expiry' => 0,
    'has_warranty' => 0
];

echo "=== Testing Product Addition with Dimensions ===\n";

// Build dimensions JSON like the controller does
$dimensionsData = [];
if (!empty($data['width']))
    $dimensionsData['width'] = $data['width'];
if (!empty($data['width_unit']))
    $dimensionsData['width_unit'] = $data['width_unit'];
if (!empty($data['height']))
    $dimensionsData['height'] = $data['height'];
if (!empty($data['height_unit']))
    $dimensionsData['height_unit'] = $data['height_unit'];
if (!empty($data['length']))
    $dimensionsData['length'] = $data['length'];
if (!empty($data['length_unit']))
    $dimensionsData['length_unit'] = $data['length_unit'];
if (!empty($data['weight']))
    $dimensionsData['weight'] = $data['weight'];
if (!empty($data['weight_unit']))
    $dimensionsData['weight_unit'] = $data['weight_unit'];

$dimensionsJson = !empty($dimensionsData) ? json_encode($dimensionsData) : null;

echo "Dimensions JSON: " . $dimensionsJson . "\n";

// Insert product manually to test
$db = new Database();
$db->query("
    INSERT INTO products (
        product_name, sku, product_type, product_status,
        weight, dimensions, has_expiry, has_warranty,
        is_active, created_at, updated_at
    ) VALUES (
        :product_name, :sku, :product_type, :product_status,
        :weight, :dimensions, :has_expiry, :has_warranty,
        :is_active, NOW(), NOW()
    )
");

$db->bind(':product_name', $data['product_name']);
$db->bind(':sku', $data['sku']);
$db->bind(':product_type', $data['product_type']);
$db->bind(':product_status', $data['product_status']);
$db->bind(':weight', $data['weight']);
$db->bind(':dimensions', $dimensionsJson);
$db->bind(':has_expiry', $data['has_expiry']);
$db->bind(':has_warranty', $data['has_warranty']);
$db->bind(':is_active', 1);

if ($db->execute()) {
    $productId = $db->lastInsertId();
    echo "Product added successfully with ID: " . $productId . "\n";

    // Now test reading it back
    $db->query('SELECT product_id, product_name, dimensions FROM products WHERE product_id = :id');
    $db->bind(':id', $productId);
    $product = $db->single();

    echo "Retrieved product:\n";
    echo "  ID: " . $product->product_id . "\n";
    echo "  Name: " . $product->product_name . "\n";
    echo "  Dimensions JSON: " . $product->dimensions . "\n";

    // Test parsing the JSON
    $parsed = json_decode($product->dimensions, true);
    if ($parsed) {
        echo "  Parsed dimensions:\n";
        foreach ($parsed as $key => $value) {
            echo "    $key: $value\n";
        }
    } else {
        echo "  ERROR: Could not parse dimensions JSON\n";
    }

} else {
    echo "ERROR: Failed to add product\n";
}
?>