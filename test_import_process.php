<?php
// Test CSV Import Function
require_once 'app/config.php';
require_once 'app/Database.php';
require_once 'app/models/Product.php';
require_once 'app/models/Category.php';

// Simulate import functionality
$productModel = new Product();
$categoryModel = new Category();

// Test CSV content
$csvData = [
    ['product_name', 'sku', 'category_id', 'purchase_price', 'selling_price'],
    ['Import Test Product 1', 'IMPORT001', '1', '20.00', '29.99'],
    ['Import Test Product 2', 'IMPORT002', '2', '35.50', '49.99']
];

echo "Testing CSV Import Functionality...\n\n";

// Process each data row
for ($i = 1; $i < count($csvData); $i++) {
    $headers = $csvData[0];
    $data = $csvData[$i];
    $row = array_combine($headers, $data);
    
    echo "Processing row " . ($i) . ":\n";
    echo "  Product: {$row['product_name']}\n";
    echo "  SKU: {$row['sku']}\n";
    
    // Check if category exists
    $category = $categoryModel->getCategoryById($row['category_id']);
    if ($category) {
        echo "  ✓ Category found: {$category->category_name}\n";
    } else {
        echo "  ✗ Category not found for ID: {$row['category_id']}\n";
        // Let's see what categories are available
        $categories = $categoryModel->getCategories();
        echo "  Available category IDs: ";
        foreach ($categories as $cat) {
            echo $cat->category_id . " ";
        }
        echo "\n";
        continue;
    }
    
    // Check if SKU already exists
    $existingProduct = $productModel->getProductBySku($row['sku']);
    if ($existingProduct) {
        echo "  ! SKU already exists (would be skipped in real import)\n";
    } else {
        echo "  ✓ SKU is unique\n";
    }
    
    // Prepare product data
    $productData = [
        'product_name' => $row['product_name'],
        'sku' => $row['sku'],
        'supplier_code' => null,
        'category_id' => (int)$row['category_id'],
        'brand_id' => 1, // Default brand
        'unit_id' => 1, // Default unit
        'min_stock_level' => 5,
        'max_stock_level' => 100,
        'reorder_level' => 10,
        'purchase_price' => (float)$row['purchase_price'],
        'selling_price' => (float)$row['selling_price'],
        'weight' => null,
        'dimensions' => null,
        'warranty_period' => null,
        'image_path' => null,
        'is_active' => 1
    ];
    
    // Try to add product
    try {
        $success = $productModel->addProduct($productData);
        if ($success) {
            echo "  ✓ Product added successfully\n";
        } else {
            echo "  ✗ Failed to add product (returned false)\n";
        }
    } catch (Exception $e) {
        echo "  ✗ Error adding product: " . $e->getMessage() . "\n";
    } catch (Error $e) {
        echo "  ✗ Fatal error adding product: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "Import test completed!\n";
?>
