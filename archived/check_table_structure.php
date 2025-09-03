<?php
// Check actual products table structure
echo "PRODUCTS TABLE STRUCTURE ANALYSIS\n";
echo "=================================\n";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=master_hardware", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Database connected successfully\n\n";

    // Check table structure
    $stmt = $pdo->query("DESCRIBE products");
    $columns = $stmt->fetchAll();

    echo "Products table columns:\n";
    echo "----------------------\n";
    foreach ($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")" . ($col['Null'] == 'YES' ? ' NULL' : ' NOT NULL') . "\n";
    }

    echo "\n";

    // Look for inventory-related columns
    $inventoryColumns = [];
    foreach ($columns as $col) {
        if (
            stripos($col['Field'], 'stock') !== false ||
            stripos($col['Field'], 'quantity') !== false ||
            stripos($col['Field'], 'inventory') !== false
        ) {
            $inventoryColumns[] = $col['Field'];
        }
    }

    if (count($inventoryColumns) > 0) {
        echo "Inventory-related columns found:\n";
        foreach ($inventoryColumns as $col) {
            echo "✅ " . $col . "\n";
        }
    } else {
        echo "❌ No inventory-related columns found!\n";
    }

    echo "\n";

    // Sample products with all columns
    $stmt = $pdo->query("SELECT * FROM products LIMIT 2");
    $products = $stmt->fetchAll();

    echo "Sample product data:\n";
    echo "-------------------\n";
    foreach ($products as $i => $product) {
        echo "Product " . ($i + 1) . ":\n";
        foreach ($product as $key => $value) {
            if (!is_numeric($key)) { // Skip numeric indices
                echo "  " . $key . ": " . ($value ?? 'NULL') . "\n";
            }
        }
        echo "\n";
    }

} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
?>