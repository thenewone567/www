<?php
require_once 'bootstrap.php';

echo "=== INVENTORY TRACKING ANALYSIS ===\n\n";

$database = new Database();

// 1. Check inventory table structure and content
echo "1. Inventory Table Structure:\n";
$database->query("DESCRIBE inventory");
if ($database->execute()) {
    $stmt = $database->getStatement();
    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
        echo "   {$row->Field} | {$row->Type}\n";
    }
} else {
    echo "   Inventory table does not exist\n";
}

// 2. Check inventory table content
echo "\n2. Inventory Table Content (sample):\n";
$database->query("SELECT COUNT(*) as count FROM inventory");
$database->execute();
$invCount = $database->single();
echo "   Total inventory records: " . ($invCount ? $invCount->count : 0) . "\n";

if ($invCount && $invCount->count > 0) {
    $database->query("
        SELECT i.inventory_id, i.product_id, p.product_name, i.quantity, i.location_id
        FROM inventory i
        JOIN products p ON i.product_id = p.product_id
        ORDER BY i.quantity DESC
        LIMIT 10
    ");
    if ($database->execute()) {
        $stmt = $database->getStatement();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            echo "   Inv #{$row->inventory_id} | {$row->product_name} | Qty: {$row->quantity} | Location: {$row->location_id}\n";
        }
    }
}

// 3. Check inventory_transactions table structure and content
echo "\n3. Inventory Transactions Table:\n";
$database->query("SELECT COUNT(*) as count FROM inventory_transactions");
$database->execute();
$txnCount = $database->single();
echo "   Total transaction records: " . ($txnCount ? $txnCount->count : 0) . "\n";

if ($txnCount && $txnCount->count > 0) {
    $database->query("
        SELECT it.transaction_id, it.product_id, p.product_name, 
               it.transaction_type, it.quantity_change, it.quantity_after, it.created_at
        FROM inventory_transactions it
        JOIN products p ON it.product_id = p.product_id
        ORDER BY it.created_at DESC
        LIMIT 10
    ");
    if ($database->execute()) {
        $stmt = $database->getStatement();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            echo "   TX #{$row->transaction_id} | {$row->product_name} | Type: {$row->transaction_type} | Change: {$row->quantity_change} | After: {$row->quantity_after} | {$row->created_at}\n";
        }
    }
}

// 4. Compare stock calculations between both methods
echo "\n4. Stock Calculation Comparison:\n";
$database->query("
    SELECT 
        p.product_id,
        p.product_name,
        COALESCE(SUM(i.quantity), 0) as inventory_table_stock,
        COALESCE(MAX(it.quantity_after), 0) as transactions_table_stock
    FROM products p
    LEFT JOIN inventory i ON p.product_id = i.product_id
    LEFT JOIN inventory_transactions it ON p.product_id = it.product_id
    GROUP BY p.product_id, p.product_name
    HAVING inventory_table_stock > 0 OR transactions_table_stock > 0
    ORDER BY inventory_table_stock DESC
    LIMIT 10
");

if ($database->execute()) {
    $stmt = $database->getStatement();
    echo "   Product | Inventory Table | Transactions Table\n";
    echo "   -------|-----------------|------------------\n";
    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
        echo "   {$row->product_name} | {$row->inventory_table_stock} | {$row->transactions_table_stock}\n";
    }
}

// 5. Check which method the dashboard uses
echo "\n5. Finding Dashboard Stock Calculation:\n";
$dashboardFiles = [
    'app/views/dashboard.php',
    'app/controllers/DashboardController.php',
    'app/models/Dashboard.php'
];

foreach ($dashboardFiles as $file) {
    $fullPath = "c:/wamp64/www/$file";
    if (file_exists($fullPath)) {
        echo "   Checking $file...\n";
        $content = file_get_contents($fullPath);
        if (strpos($content, 'inventory_transactions') !== false) {
            echo "     ✓ Uses inventory_transactions table\n";
        }
        if (strpos($content, 'inventory') !== false && strpos($content, 'inventory_transactions') === false) {
            echo "     ✓ Uses inventory table\n";
        }
    }
}

echo "\n=== ANALYSIS COMPLETE ===\n";
