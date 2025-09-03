<?php
require_once 'bootstrap.php';

echo "INVENTORY TABLE ANALYSIS\n";
echo "========================\n";

try {
    $db = new Database();

    // Check inventory table structure and data
    $db->query("DESCRIBE inventory");
    $structure = $db->resultSet();

    echo "Inventory table structure:\n";
    foreach ($structure as $col) {
        echo "- " . $col->Field . " (" . $col->Type . ")\n";
    }

    echo "\n";

    // Check total records
    $db->query("SELECT COUNT(*) as total FROM inventory");
    $total = $db->single()->total;
    echo "Total inventory records: " . $total . "\n\n";

    if ($total > 0) {
        // Sample data
        $db->query("SELECT * FROM inventory LIMIT 10");
        $samples = $db->resultSet();

        echo "Sample inventory records:\n";
        foreach ($samples as $record) {
            echo "- Product ID: " . $record->product_id . ", Quantity: " . $record->quantity . "\n";
        }

        echo "\n";

        // Check products that exist in products table but not in inventory
        $db->query("
            SELECT p.product_id, p.product_name, p.stock_quantity
            FROM products p
            LEFT JOIN inventory i ON p.product_id = i.product_id
            WHERE i.product_id IS NULL
            LIMIT 10
        ");
        $missingInInventory = $db->resultSet();

        echo "Products in 'products' table but NOT in 'inventory' table:\n";
        foreach ($missingInInventory as $product) {
            echo "- " . $product->product_name . " (ID: " . $product->product_id . ", Stock: " . $product->stock_quantity . ")\n";
        }
    } else {
        echo "No inventory records found!\n";
        echo "This explains the discrepancy!\n\n";

        // Check if products have data in products table
        $db->query("SELECT COUNT(*) as total FROM products WHERE is_active = 1");
        $activeProducts = $db->single()->total;
        echo "Active products in products table: " . $activeProducts . "\n";

        // Check specific products mentioned by user
        $db->query("SELECT product_id, product_name, stock_quantity, is_active FROM products WHERE product_name LIKE '%hammer%' OR product_name LIKE '%Integration%'");
        $testProducts = $db->resultSet();

        echo "\nTest products mentioned by user:\n";
        foreach ($testProducts as $product) {
            echo "- " . $product->product_name . " (ID: " . $product->product_id . ", Stock: " . $product->stock_quantity . ", Active: " . $product->is_active . ")\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>