<?php
require_once 'bootstrap.php';

echo "DATABASE TABLES ANALYSIS\n";
echo "========================\n";

try {
    $db = new Database();

    // Check what tables exist
    $db->query("SHOW TABLES");
    $tables = $db->resultSet();

    echo "Available tables:\n";
    foreach ($tables as $table) {
        $tableName = array_values((array) $table)[0];
        echo "- " . $tableName . "\n";
    }

    echo "\n";

    // Check if inventory table exists
    $inventoryExists = false;
    foreach ($tables as $table) {
        $tableName = array_values((array) $table)[0];
        if ($tableName === 'inventory') {
            $inventoryExists = true;
            break;
        }
    }

    if ($inventoryExists) {
        echo "✅ Inventory table EXISTS\n";

        // Check structure
        $db->query("DESCRIBE inventory");
        $structure = $db->resultSet();

        echo "Inventory table structure:\n";
        foreach ($structure as $col) {
            echo "- " . $col->Field . " (" . $col->Type . ")\n";
        }

        // Check records
        $db->query("SELECT COUNT(*) as total FROM inventory");
        $result = $db->single();
        echo "\nTotal inventory records: " . ($result ? $result->total : 'ERROR') . "\n";

    } else {
        echo "❌ Inventory table DOES NOT EXIST!\n";
        echo "This is the root cause of the discrepancy!\n\n";

        echo "SOLUTION NEEDED:\n";
        echo "1. Dashboard model expects 'inventory' table\n";
        echo "2. Product list uses 'products.stock_quantity' field\n";
        echo "3. We need to align these two data sources\n";
    }

    // Check products table for the mentioned items
    echo "\nChecking products table for test items:\n";
    $db->query("SELECT product_id, product_name, stock_quantity, is_active FROM products WHERE product_name LIKE '%hammer%' OR product_name LIKE '%Integration%' OR product_name LIKE '%Test%'");
    $testProducts = $db->resultSet();

    foreach ($testProducts as $product) {
        echo "- " . $product->product_name . " (Stock: " . $product->stock_quantity . ", Active: " . $product->is_active . ")\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>