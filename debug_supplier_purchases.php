<?php
/**
 * Quick test to debug supplier purchase history
 */

require_once 'bootstrap.php';

echo "<h3>Debug: Supplier Purchase History Test</h3>";

// Test 1: Using the model
echo "<h4>Test 1: Using Supplier Model</h4>";
$supplierModel = new Supplier();
$purchaseHistory = $supplierModel->getSupplierPurchases(1, 10);
echo "<p>Model result count: " . count($purchaseHistory) . "</p>";

// Test 2: Using Database class directly
echo "<h4>Test 2: Using Database Class Directly</h4>";
$db = new Database();
$db->query("
    SELECT purchase_id, po_number, purchase_date, status, total_amount
    FROM purchases
    WHERE supplier_id = :supplier_id
    ORDER BY purchase_date DESC
    LIMIT :limit
");
$db->bind(':supplier_id', 1);
$db->bind(':limit', 10);
$db->execute(); // Added missing execute call
$directResult = $db->resultSet();
echo "<p>Direct Database result count: " . count($directResult) . "</p>";

if (!empty($directResult)) {
    echo "<h5>First result:</h5>";
    echo "<pre>";
    print_r($directResult[0]);
    echo "</pre>";
}

// Test 3: Simple query without parameters
echo "<h4>Test 3: Simple Query Without Parameters</h4>";
$db->query("SELECT COUNT(*) as count FROM purchases WHERE supplier_id = 1");
$db->execute();
$countResult = $db->single();
echo "<p>Simple count query result: " . ($countResult->count ?? 'Error') . "</p>";

// Test 4: Check if supplier exists
echo "<h4>Test 4: Check Supplier Exists</h4>";
$db->query("SELECT * FROM suppliers WHERE supplier_id = 1");
$db->execute();
$supplierResult = $db->single();
echo "<p>Supplier exists: " . ($supplierResult ? 'Yes - ' . $supplierResult->supplier_name : 'No') . "</p>";
?>
