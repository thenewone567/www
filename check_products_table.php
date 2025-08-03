<?php
require_once 'app/config.php';
require_once 'app/Database.php';

$db = new Database();

echo "Checking products table structure...\n";

try {
    $db->query("DESCRIBE products");
    $db->execute();
    $columns = $db->resultSet();
    
    echo "Products table columns:\n";
    foreach ($columns as $column) {
        echo "  {$column->Field} - {$column->Type} - NULL: {$column->Null} - Default: {$column->Default}\n";
    }
    
    echo "\nTesting with only required fields...\n";
    
    $db->query("INSERT INTO products (product_name, sku) VALUES (:name, :sku)");
    $db->bind(':name', 'Minimal Test Product');
    $db->bind(':sku', 'MINIMAL001');
    
    if ($db->execute()) {
        echo "✓ Minimal insert worked\n";
        $id = $db->lastInsertId();
        echo "  Product ID: $id\n";
    } else {
        echo "✗ Even minimal insert failed\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
