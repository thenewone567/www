<?php
require_once 'bootstrap.php';

echo "=== DASHBOARD QUERY VERIFICATION ===\n\n";

$database = new Database();

// 1. Run the exact out-of-inventory query from Dashboard model
echo "1. Out of Inventory Count (Dashboard query):\n";
$database->query("
    SELECT COUNT(p.product_id) as total 
    FROM products p
    LEFT JOIN (
        SELECT product_id, SUM(quantity) as total_qty 
        FROM inventory 
        GROUP BY product_id
    ) inv ON p.product_id = inv.product_id
    WHERE p.is_active = 1
    AND p.deleted_at IS NULL
    AND COALESCE(inv.total_qty, 0) <= 0
");
$database->execute();
$outOfStock = $database->single();
echo "   Out of stock products: " . ($outOfStock ? $outOfStock->total : 'ERROR') . "\n";

// 2. Check products with their inventory status
echo "\n2. Product Inventory Status (detailed):\n";
$database->query("
    SELECT 
        p.product_id,
        p.product_name,
        p.is_active,
        p.deleted_at,
        COALESCE(inv.total_qty, 0) as inventory_quantity
    FROM products p
    LEFT JOIN (
        SELECT product_id, SUM(quantity) as total_qty 
        FROM inventory 
        GROUP BY product_id
    ) inv ON p.product_id = inv.product_id
    WHERE p.is_active = 1
    AND p.deleted_at IS NULL
    ORDER BY inventory_quantity ASC
    LIMIT 20
");

if ($database->execute()) {
    $stmt = $database->getStatement();
    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
        $status = $row->inventory_quantity <= 0 ? "OUT OF STOCK" : "IN STOCK";
        echo "   ID: {$row->product_id} | {$row->product_name} | Qty: {$row->inventory_quantity} | {$status}\n";
    }
} else {
    echo "   Query failed: " . $database->getLastError() . "\n";
}

// 3. Check products that have no inventory records at all
echo "\n3. Products with NO inventory records:\n";
$database->query("
    SELECT 
        p.product_id,
        p.product_name,
        COUNT(i.inventory_id) as inv_records
    FROM products p
    LEFT JOIN inventory i ON p.product_id = i.product_id
    WHERE p.is_active = 1
    AND p.deleted_at IS NULL
    GROUP BY p.product_id, p.product_name
    HAVING inv_records = 0
    LIMIT 10
");

if ($database->execute()) {
    $stmt = $database->getStatement();
    $noInvCount = 0;
    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
        $noInvCount++;
        echo "   ID: {$row->product_id} | {$row->product_name} | No inventory records\n";
    }
    echo "Total products with no inventory records: $noInvCount\n";
} else {
    echo "   Query failed: " . $database->getLastError() . "\n";
}

// 4. Check active products count
echo "\n4. Active Products Summary:\n";
$database->query("
    SELECT 
        COUNT(*) as total_products,
        COUNT(CASE WHEN p.is_active = 1 AND p.deleted_at IS NULL THEN 1 END) as active_products
    FROM products p
");
$database->execute();
$productStats = $database->single();
echo "   Total products: " . ($productStats ? $productStats->total_products : 'ERROR') . "\n";
echo "   Active products: " . ($productStats ? $productStats->active_products : 'ERROR') . "\n";

echo "\n=== VERIFICATION COMPLETE ===\n";
