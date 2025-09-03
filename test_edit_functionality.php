<?php
require_once 'bootstrap.php';

echo "=== Testing Product Edit Functionality ===\n";

// Test if we can get the product first
$productModel = new Product();
$product = $productModel->getProductById(147);

if ($product) {
    echo "✅ Product found: " . $product->product_name . "\n";
    echo "Current data:\n";
    echo "  SKU: " . $product->sku . "\n";
    echo "  Category ID: " . ($product->category_id ?? 'NULL') . "\n";
    echo "  Product Type: " . ($product->product_type ?? 'NULL') . "\n";
} else {
    echo "❌ Product 147 not found\n";

    // Let's find any product to test with
    $db = new Database();
    $db->query('SELECT product_id, product_name FROM products LIMIT 1');
    $testProduct = $db->single();

    if ($testProduct) {
        echo "Found test product: ID " . $testProduct->product_id . " - " . $testProduct->product_name . "\n";
    } else {
        echo "No products found in database\n";
    }
}

// Test the updateProduct method directly
if ($product) {
    echo "\n=== Testing updateProduct method directly ===\n";

    $testData = [
        'product_name' => $product->product_name . ' (Test Update)',
        'sku' => $product->sku,
        'supplier_code' => $product->supplier_code ?? '',
        'category_id' => $product->category_id ?? 1,
        'brand_id' => null, // Set to null instead of 0
        'unit_id' => null, // Set to null instead of 1
        'product_type' => 'STANDARD',
        'has_expiry' => 0,
        'expiry_months' => 0,
        'min_Inventory_level' => 0,
        'max_Inventory_level' => 0,
        'reorder_level' => 0,
        'purchase_price' => $product->purchase_price ?? 0,
        'selling_price' => $product->selling_price ?? 0,
        'profit_margin' => 0,
        'weight' => 0,
        'dimensions' => null,
        'warranty_period' => 0,
        'image_path' => $product->image_path ?? ''
    ];

    echo "Attempting update with data:\n";
    echo "  Product Name: " . $testData['product_name'] . "\n";
    echo "  Category ID: " . $testData['category_id'] . "\n";

    $result = $productModel->updateProduct(147, $testData);

    if ($result) {
        echo "✅ Direct update successful\n";

        // Verify the update
        $updatedProduct = $productModel->getProductById(147);
        if ($updatedProduct && $updatedProduct->product_name == $testData['product_name']) {
            echo "✅ Update verified - name changed to: " . $updatedProduct->product_name . "\n";
        } else {
            echo "❌ Update not verified\n";
        }
    } else {
        echo "❌ Direct update failed\n";
    }
}
?>