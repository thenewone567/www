<?php
require_once 'bootstrap.php';

echo "=== RECEIVING WORKFLOW ANALYSIS ===\n\n";

$database = new Database();

// 1. Check a specific recent purchase and its receiving status
echo "1. Recent Purchase Detail Analysis:\n";
$database->query("
    SELECT 
        p.purchase_id,
        p.status as purchase_status,
        pi.product_id,
        pr.product_name,
        pi.quantity,
        pi.received_quantity,
        pi.received_at,
        r.receiving_id,
        r.status as receiving_status,
        r.received_date
    FROM purchases p
    JOIN purchase_items pi ON p.purchase_id = pi.purchase_id
    JOIN products pr ON pi.product_id = pr.product_id
    LEFT JOIN receiving r ON p.purchase_id = r.purchase_id
    WHERE p.purchase_id = 1355
");

if ($database->execute()) {
    $stmt = $database->getStatement();
    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
        echo "   Purchase #{$row->purchase_id} | {$row->product_name} | Ordered: {$row->quantity} | Received: {$row->received_quantity} | Purchase Status: {$row->purchase_status}\n";
        echo "   Receiving #{$row->receiving_id} | Status: {$row->receiving_status} | Date: {$row->received_date}\n";
        echo "   Item Received At: {$row->received_at}\n";
    }
} else {
    echo "   Query failed: " . $database->getLastError() . "\n";
}

// 2. Check if inventory transactions exist for this product
echo "\n2. Inventory Transaction Check for Recent Products:\n";
$database->query("
    SELECT 
        it.transaction_id,
        it.product_id,
        p.product_name,
        it.transaction_type,
        it.quantity_change,
        it.quantity_after,
        it.created_at,
        it.reference_type,
        it.reference_id
    FROM inventory_transactions it
    JOIN products p ON it.product_id = p.product_id
    WHERE it.product_id IN (SELECT DISTINCT product_id FROM purchase_items WHERE purchase_id >= 1350)
    ORDER BY it.created_at DESC
    LIMIT 10
");

if ($database->execute()) {
    $stmt = $database->getStatement();
    $count = 0;
    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
        $count++;
        echo "   TX #{$row->transaction_id} | {$row->product_name} | Type: {$row->transaction_type} | Change: {$row->quantity_change} | After: {$row->quantity_after} | {$row->created_at}\n";
        if ($row->reference_type && $row->reference_id) {
            echo "     Ref: {$row->reference_type} #{$row->reference_id}\n";
        }
    }
    echo "Recent inventory transactions: $count\n";
} else {
    echo "   Query failed: " . $database->getLastError() . "\n";
}

// 3. Check current stock calculation for products that should have received stock
echo "\n3. Current Stock Status for Recently Received Products:\n";
$database->query("
    SELECT 
        pr.product_id,
        pr.product_name,
        SUM(pi.received_quantity) as total_received_recently,
        COALESCE(MAX(it.quantity_after), 0) as current_calculated_stock
    FROM products pr
    JOIN purchase_items pi ON pr.product_id = pi.product_id
    JOIN purchases p ON pi.purchase_id = p.purchase_id
    LEFT JOIN inventory_transactions it ON pr.product_id = it.product_id
    WHERE p.purchase_id >= 1350
    GROUP BY pr.product_id, pr.product_name
    ORDER BY total_received_recently DESC
    LIMIT 10
");

if ($database->execute()) {
    $stmt = $database->getStatement();
    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
        echo "   {$row->product_name} | Recently Received: {$row->total_received_recently} | Current Stock: {$row->current_calculated_stock}\n";
    }
} else {
    echo "   Query failed: " . $database->getLastError() . "\n";
}

// 4. Check the receiving process - look at the receiving table structure
echo "\n4. Receiving Process Analysis:\n";
$database->query("
    SELECT 
        r.receiving_id,
        r.purchase_id,
        r.status,
        r.received_date,
        p.status as purchase_status,
        COUNT(pi.purchase_item_id) as item_count
    FROM receiving r
    JOIN purchases p ON r.purchase_id = p.purchase_id
    LEFT JOIN purchase_items pi ON p.purchase_id = pi.purchase_id
    WHERE r.received_date = CURDATE()
    GROUP BY r.receiving_id, r.purchase_id, r.status, r.received_date, p.status
    ORDER BY r.receiving_id DESC
    LIMIT 5
");

if ($database->execute()) {
    $stmt = $database->getStatement();
    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
        echo "   Receiving #{$row->receiving_id} | Purchase #{$row->purchase_id} | Items: {$row->item_count} | Status: {$row->status} | Purchase Status: {$row->purchase_status}\n";
    }
} else {
    echo "   Query failed: " . $database->getLastError() . "\n";
}

// 5. Check if there's a receiving_items table that might have the detail
echo "\n5. Checking for receiving_items table:\n";
$database->query("SHOW TABLES LIKE 'receiving_items'");
if ($database->execute() && $database->rowCount() > 0) {
    echo "   receiving_items table EXISTS\n";

    // Check its structure
    $database->query("DESCRIBE receiving_items");
    if ($database->execute()) {
        $stmt = $database->getStatement();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            echo "   {$row->Field} | {$row->Type}\n";
        }
    }

    // Check recent data
    $database->query("SELECT COUNT(*) as count FROM receiving_items WHERE created_at >= CURDATE()");
    $database->execute();
    $todayItems = $database->single();
    echo "   receiving_items records today: " . ($todayItems ? $todayItems->count : 0) . "\n";

} else {
    echo "   receiving_items table does NOT exist\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";
