<?php
// Test the updated CSV import functionality
require_once 'app/config.php';
require_once 'app/Database.php';
require_once 'app/models/Product.php';
require_once 'app/models/Category.php';

$productModel = new Product();

// Test the new simple add method
$testProduct = [
    'product_name' => 'Final Test Product',
    'sku' => 'FINAL001',
    'category_id' => 1,
    'brand_id' => 1,
    'unit_id' => 1,
    'min_stock_level' => 5,
    'max_stock_level' => 100,
    'reorder_level' => 10,
    'image_path' => null,
    'is_active' => 1
];

echo "Testing simple product add...\n";

try {
    $result = $productModel->addSimpleProduct($testProduct);
    if ($result) {
        echo "✓ Simple product add worked! Product ID: $result\n";
    } else {
        echo "✗ Simple product add failed\n";
    }
} catch (Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\nTesting CSV data parsing...\n";

// Test CSV parsing
$csvFile = 'simple_test_import.csv';
if (file_exists($csvFile)) {
    $handle = fopen($csvFile, 'r');
    $headers = fgetcsv($handle);
    echo "CSV Headers: " . implode(', ', $headers) . "\n";
    
    $rowCount = 0;
    while (($data = fgetcsv($handle)) !== FALSE) {
        $rowCount++;
        $row = array_combine($headers, $data);
        echo "Row $rowCount: {$row['product_name']} - {$row['sku']}\n";
        
        // Test data preparation
        $productData = [
            'product_name' => trim($row['product_name']),
            'sku' => trim($row['sku']),
            'category_id' => !empty($row['category_id']) ? (int)$row['category_id'] : null,
            'brand_id' => !empty($row['brand_id']) ? (int)$row['brand_id'] : null,
            'unit_id' => !empty($row['unit_id']) ? (int)$row['unit_id'] : null,
            'min_stock_level' => !empty($row['min_stock_level']) ? (int)$row['min_stock_level'] : 0,
            'max_stock_level' => !empty($row['max_stock_level']) ? (int)$row['max_stock_level'] : 0,
            'reorder_level' => !empty($row['reorder_level']) ? (int)$row['reorder_level'] : 0,
            'image_path' => null,
            'is_active' => 1
        ];
        
        // Check for existing SKU
        $existing = $productModel->getProductBySku($row['sku']);
        if ($existing) {
            echo "  ! SKU already exists (skipping)\n";
            continue;
        }
        
        // Try to add
        $result = $productModel->addSimpleProduct($productData);
        if ($result) {
            echo "  ✓ Added successfully (ID: $result)\n";
        } else {
            echo "  ✗ Failed to add\n";
        }
    }
    fclose($handle);
} else {
    echo "CSV file not found\n";
}

echo "\nTest completed!\n";
?>
