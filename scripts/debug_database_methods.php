<?php
require_once __DIR__ . '/../bootstrap.php';

$db = new Database();
$productId = 139;

echo "Testing Database class methods:\n";

// Test 1: Direct query
$db->query("SELECT product_id, product_name FROM products WHERE product_id = 139");
$db->execute();
$result1 = $db->single();
echo "Direct query result: " . ($result1 ? "Found: {$result1->product_name}" : "Not found") . "\n";

// Test 2: With bind
$db->query("SELECT product_id, product_name FROM products WHERE product_id = :product_id");
$db->bind(':product_id', 139);
$db->execute();
$result2 = $db->single();
echo "Bind query result: " . ($result2 ? "Found: {$result2->product_name}" : "Not found") . "\n";

// Test 3: Check if single() method has an issue
$db->query("SELECT product_id, product_name FROM products WHERE product_id = 139");
$db->execute();
$result3 = $db->resultSet();
echo "ResultSet query: " . (count($result3) > 0 ? "Found: {$result3[0]->product_name}" : "Not found") . "\n";

// Test 4: Check the actual if condition
$db->query("SELECT product_id, product_name FROM products WHERE product_id = :product_id");
$db->bind(':product_id', $productId);
$db->execute();
$product = $db->single();

echo "Product object: " . var_export($product, true) . "\n";
echo "Is product truthy: " . ($product ? 'YES' : 'NO') . "\n";
echo "Is product null: " . ($product === null ? 'YES' : 'NO') . "\n";
echo "Is product false: " . ($product === false ? 'YES' : 'NO') . "\n";
?>