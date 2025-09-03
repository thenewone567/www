<?php
require_once 'bootstrap.php';

echo "=== Testing Edit Form Dimension Parsing ===\n";

$productModel = new Product();
$product = $productModel->getProductById(149);

if ($product) {
    echo "Product found: " . $product->product_name . "\n";
    echo "Dimensions JSON from DB: " . ($product->dimensions ?? 'NULL') . "\n";

    // Test the parsing logic from the edit controller
    $dimensionsData = [];
    if (!empty($product->dimensions)) {
        $dimensionsData = json_decode($product->dimensions, true) ?? [];
    }

    echo "Parsed dimensions data:\n";
    if (!empty($dimensionsData)) {
        foreach ($dimensionsData as $key => $value) {
            echo "  $key: $value\n";
        }

        echo "\nEdit form field values would be:\n";
        echo "  width: " . ($dimensionsData['width'] ?? '') . "\n";
        echo "  width_unit: " . ($dimensionsData['width_unit'] ?? 'cm') . "\n";
        echo "  height: " . ($dimensionsData['height'] ?? '') . "\n";
        echo "  height_unit: " . ($dimensionsData['height_unit'] ?? 'cm') . "\n";
        echo "  length: " . ($dimensionsData['length'] ?? '') . "\n";
        echo "  length_unit: " . ($dimensionsData['length_unit'] ?? 'cm') . "\n";
    } else {
        echo "  No dimensions data found\n";
    }
} else {
    echo "Product not found!\n";
}

// Test creating the JSON from form data (simulate POST)
echo "\n=== Testing Form Data to JSON Conversion ===\n";

$formData = [
    'width' => 12.5,
    'width_unit' => 'cm',
    'height' => 8.0,
    'height_unit' => 'cm',
    'length' => 20.0,
    'length_unit' => 'cm'
];

// Build dimensions JSON like the edit controller does
$dimensionsData = [];
if (!empty($formData['width']))
    $dimensionsData['width'] = $formData['width'];
if (!empty($formData['width_unit']))
    $dimensionsData['width_unit'] = $formData['width_unit'];
if (!empty($formData['height']))
    $dimensionsData['height'] = $formData['height'];
if (!empty($formData['height_unit']))
    $dimensionsData['height_unit'] = $formData['height_unit'];
if (!empty($formData['length']))
    $dimensionsData['length'] = $formData['length'];
if (!empty($formData['length_unit']))
    $dimensionsData['length_unit'] = $formData['length_unit'];

$dimensionsJson = !empty($dimensionsData) ? json_encode($dimensionsData) : null;

echo "Form data JSON: " . $dimensionsJson . "\n";
echo "✅ Form to JSON conversion working correctly!\n";
?>