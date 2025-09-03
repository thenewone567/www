<?php
require_once 'bootstrap.php';

echo "Checking product-supplier relationships:\n";
echo "========================================\n\n";

$db = new Database();

// Check products table
$db->query('SELECT COUNT(*) as count FROM products WHERE selling_price > 0');
$db->execute();
$result = $db->single();
echo "Products with selling_price > 0: " . $result->count . "\n";

// Check suppliers table  
$db->query('SELECT COUNT(*) as count FROM suppliers');
$db->execute();
$result = $db->single();
echo "Total suppliers: " . $result->count . "\n";

// Check product_suppliers relationships
$db->query('SELECT COUNT(*) as count FROM product_suppliers');
$db->execute();
$result = $db->single();
echo "Product-supplier relationships: " . $result->count . "\n";

// Check if product_suppliers table has purchase_price
$db->query('DESCRIBE product_suppliers');
$db->execute();
$columns = $db->resultSet();
echo "\nproduct_suppliers table structure:\n";
foreach ($columns as $col) {
    echo "  " . $col->Field . " (" . $col->Type . ")\n";
}

// Check suppliers table structure for purchase_price
echo "\nsuppliers table structure:\n";
$db->query('DESCRIBE suppliers');
$db->execute();
$columns = $db->resultSet();
foreach ($columns as $col) {
    echo "  " . $col->Field . " (" . $col->Type . ")\n";
}

// Sample data check
echo "\nSample product data:\n";
$db->query('SELECT product_id, product_name, selling_price, purchase_price FROM products WHERE selling_price > 0 LIMIT 5');
$db->execute();
$products = $db->resultSet();
foreach ($products as $product) {
    echo "  ID: {$product->product_id}, Name: {$product->product_name}, Sell: \${$product->selling_price}, Cost: \${$product->purchase_price}\n";
}

echo "\nSample supplier data:\n";
$db->query('SELECT supplier_id, supplier_name FROM suppliers LIMIT 5');
$db->execute();
$suppliers = $db->resultSet();
foreach ($suppliers as $supplier) {
    echo "  ID: {$supplier->supplier_id}, Name: {$supplier->supplier_name}\n";
}
?>