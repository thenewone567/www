<?php
/**
 * Test supplier products query
 */

require_once 'bootstrap.php';

echo "<h3>Debug: Supplier Products Test</h3>";

// Test 1: Using the Product model
echo "<h4>Test 1: Using Product Model</h4>";
$productModel = new Product();
$supplierProducts = $productModel->getProductsBySupplier(1);
echo "<p>Model result count: " . count($supplierProducts) . "</p>";

// Test 2: Using Database class directly with simple query
echo "<h4>Test 2: Simple Database Query</h4>";
$db = new Database();
$db->query("
    SELECT p.product_id, p.product_name, p.sku, ps.purchase_price, ps.supplier_id
    FROM products p
    JOIN product_suppliers ps ON p.product_id = ps.product_id 
    WHERE ps.supplier_id = :supplier_id AND ps.is_active = 1
    LIMIT 10
");
$db->bind(':supplier_id', 1);
$db->execute();
$directResult = $db->resultSet();
echo "<p>Direct Database result count: " . count($directResult) . "</p>";

if (!empty($directResult)) {
    echo "<h5>First result:</h5>";
    echo "<pre>";
    print_r($directResult[0]);
    echo "</pre>";
}

// Test 3: Check if supplier exists and has active links
echo "<h4>Test 3: Check Active Links</h4>";
$db->query("SELECT COUNT(*) as count FROM product_suppliers WHERE supplier_id = 1 AND is_active = 1");
$db->execute();
$countResult = $db->single();
echo "<p>Active links for supplier 1: " . ($countResult->count ?? 'Error') . "</p>";
?>
