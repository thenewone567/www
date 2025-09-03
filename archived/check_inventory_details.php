<?php
// Check inventory table structure and data
echo "INVENTORY TABLE DETAILED ANALYSIS\n";
echo "=================================\n";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=master_hardware", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check inventory table structure
    $stmt = $pdo->query("DESCRIBE inventory");
    $columns = $stmt->fetchAll();

    echo "Inventory table structure:\n";
    echo "-------------------------\n";
    foreach ($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }

    echo "\n";

    // Check total records
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM inventory");
    $result = $stmt->fetch();
    echo "Total inventory records: " . $result['count'] . "\n";

    if ($result['count'] > 0) {
        // Sample inventory records
        $stmt = $pdo->query("SELECT * FROM inventory LIMIT 5");
        $records = $stmt->fetchAll();

        echo "\nSample inventory records:\n";
        echo "------------------------\n";
        foreach ($records as $i => $record) {
            echo "Record " . ($i + 1) . ":\n";
            foreach ($record as $key => $value) {
                if (!is_numeric($key)) {
                    echo "  " . $key . ": " . ($value ?? 'NULL') . "\n";
                }
            }
            echo "\n";
        }

        // Check products with their inventory
        $stmt = $pdo->query("
            SELECT p.product_name, 
                   COALESCE(SUM(i.quantity), 0) as total_inventory,
                   p.reorder_level
            FROM products p 
            LEFT JOIN inventory i ON p.product_id = i.product_id 
            GROUP BY p.product_id, p.product_name, p.reorder_level
            HAVING total_inventory <= 0
            LIMIT 10
        ");
        $outOfStock = $stmt->fetchAll();

        echo "Products with 0 inventory (first 10):\n";
        echo "------------------------------------\n";
        foreach ($outOfStock as $product) {
            echo "- " . $product['product_name'] . " (Inventory: " . $product['total_inventory'] . ", Reorder: " . $product['reorder_level'] . ")\n";
        }

        // Count out of stock vs low stock
        $stmt = $pdo->query("
            SELECT 
                SUM(CASE WHEN COALESCE(inv.total_qty, 0) <= 0 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(CASE WHEN COALESCE(inv.total_qty, 0) > 0 AND COALESCE(inv.total_qty, 0) <= COALESCE(p.reorder_level, 10) THEN 1 ELSE 0 END) as low_stock
            FROM products p
            LEFT JOIN (
                SELECT product_id, SUM(quantity) as total_qty 
                FROM inventory 
                GROUP BY product_id
            ) inv ON p.product_id = inv.product_id
            WHERE p.is_active = 1
        ");
        $counts = $stmt->fetch();

        echo "\nInventory Counts:\n";
        echo "----------------\n";
        echo "Out of Stock: " . $counts['out_of_stock'] . "\n";
        echo "Low Stock: " . $counts['low_stock'] . "\n";

    }

} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
?>