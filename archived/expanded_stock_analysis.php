<?php
require_once 'bootstrap.php';

echo "=== EXPANDED STOCK & PO ANALYSIS ===\n\n";

$database = new Database();

// 1. Check current stock distribution
echo "1. Current Stock Distribution:\n";
$database->query("
    SELECT 
        CASE 
            WHEN stock_quantity = 0 THEN '0 Stock'
            WHEN stock_quantity BETWEEN 1 AND 5 THEN '1-5 Stock'
            WHEN stock_quantity BETWEEN 6 AND 20 THEN '6-20 Stock'
            ELSE '20+ Stock'
        END as stock_range,
        COUNT(*) as product_count
    FROM products 
    GROUP BY 
        CASE 
            WHEN stock_quantity = 0 THEN '0 Stock'
            WHEN stock_quantity BETWEEN 1 AND 5 THEN '1-5 Stock'
            WHEN stock_quantity BETWEEN 6 AND 20 THEN '6-20 Stock'
            ELSE '20+ Stock'
        END
    ORDER BY 
        CASE 
            WHEN stock_quantity = 0 THEN 1
            WHEN stock_quantity BETWEEN 1 AND 5 THEN 2
            WHEN stock_quantity BETWEEN 6 AND 20 THEN 3
            ELSE 4
        END
");
$database->execute();
$stockDistribution = $database->resultSet();

foreach ($stockDistribution as $dist) {
    echo "   {$dist->stock_range}: {$dist->product_count} products\n";
}
echo "\n";

// 2. Check products with low stock (1-5)
echo "2. Low Stock Products (1-5 units):\n";
$database->query("
    SELECT product_id, product_name, stock_quantity, reorder_level
    FROM products 
    WHERE stock_quantity BETWEEN 1 AND 5
    ORDER BY stock_quantity ASC, product_name
    LIMIT 10
");
$database->execute();
$lowStockProducts = $database->resultSet();

foreach ($lowStockProducts as $product) {
    echo "   ID: {$product->product_id} | {$product->product_name} | Stock: {$product->stock_quantity} | Reorder: {$product->reorder_level}\n";
}
echo "Total low stock products: " . count($lowStockProducts) . "\n\n";

// 3. Check ALL recent Purchase Orders (last 14 days)
echo "3. All Purchase Orders (last 14 days):\n";
$database->query("
    SELECT po.po_id, po.supplier_id, s.supplier_name, po.status, po.total_amount, 
           po.created_at, po.received_date, po.order_date,
           COUNT(poi.product_id) as item_count
    FROM purchase_orders po
    LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
    LEFT JOIN purchase_order_items poi ON po.po_id = poi.po_id
    WHERE po.created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
    GROUP BY po.po_id
    ORDER BY po.created_at DESC
");
$database->execute();
$allPOs = $database->resultSet();

foreach ($allPOs as $po) {
    echo "   PO #{$po->po_id} | {$po->supplier_name} | Status: {$po->status} | Items: {$po->item_count} | Amount: {$po->total_amount} | Created: {$po->created_at}\n";
    if ($po->received_date) {
        echo "     Received: {$po->received_date}\n";
    }
}
echo "Total POs (14 days): " . count($allPOs) . "\n\n";

// 4. Check recent receiving activity
echo "4. All Recent Receiving Activity (last 14 days):\n";
$database->query("
    SELECT r.receiving_id, r.po_id, r.product_id, p.product_name, 
           r.quantity_received, r.unit_cost, r.received_date, r.status,
           p.stock_quantity as current_stock
    FROM receiving r
    JOIN products p ON r.product_id = p.product_id
    WHERE r.received_date >= DATE_SUB(NOW(), INTERVAL 14 DAY)
    ORDER BY r.received_date DESC
    LIMIT 20
");
$database->execute();
$allReceivings = $database->resultSet();

foreach ($allReceivings as $recv) {
    echo "   Receiving #{$recv->receiving_id} | PO #{$recv->po_id} | {$recv->product_name} | Qty: {$recv->quantity_received} | Current Stock: {$recv->current_stock} | Date: {$recv->received_date} | Status: {$recv->status}\n";
}
echo "Total receivings (14 days): " . count($allReceivings) . "\n\n";

// 5. Check recent inventory transactions
echo "5. Recent Inventory Transactions (last 7 days):\n";
$database->query("
    SELECT it.transaction_id, it.product_id, p.product_name, it.transaction_type, 
           it.quantity_change, it.running_balance, it.created_at, it.reference_type, it.reference_id,
           p.stock_quantity as current_stock
    FROM inventory_transactions it
    JOIN products p ON it.product_id = p.product_id
    WHERE it.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ORDER BY it.created_at DESC
    LIMIT 15
");
$database->execute();
$recentTransactions = $database->resultSet();

foreach ($recentTransactions as $txn) {
    echo "   TX #{$txn->transaction_id} | {$txn->product_name} | Type: {$txn->transaction_type} | Change: {$txn->quantity_change} | Balance: {$txn->running_balance} | Current: {$txn->current_stock} | {$txn->created_at}\n";
    if ($txn->reference_type && $txn->reference_id) {
        echo "     Ref: {$txn->reference_type} #{$txn->reference_id}\n";
    }
}
echo "Total recent transactions: " . count($recentTransactions) . "\n\n";

// 6. Check for specific products that might have been at 0 stock recently
echo "6. Products that had stock changes recently (potential former 0-stock):\n";
$database->query("
    SELECT DISTINCT p.product_id, p.product_name, p.stock_quantity, 
           MAX(it.created_at) as last_transaction_date,
           SUM(CASE WHEN it.transaction_type = 'purchase_receive' THEN it.quantity_change ELSE 0 END) as total_received_recently
    FROM products p
    JOIN inventory_transactions it ON p.product_id = it.product_id
    WHERE it.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    AND it.transaction_type IN ('purchase_receive', 'stock_adjustment')
    GROUP BY p.product_id, p.product_name, p.stock_quantity
    ORDER BY last_transaction_date DESC
    LIMIT 10
");
$database->execute();
$changedStock = $database->resultSet();

foreach ($changedStock as $product) {
    echo "   ID: {$product->product_id} | {$product->product_name} | Current Stock: {$product->stock_quantity} | Recent Received: {$product->total_received_recently} | Last Change: {$product->last_transaction_date}\n";
}
echo "Total products with recent stock changes: " . count($changedStock) . "\n\n";

// 7. Check the purchase bot's recent activity
echo "7. Purchase Bot Recent Activity:\n";
$database->query("
    SELECT po.po_id, po.supplier_id, s.supplier_name, po.status, po.total_amount, 
           po.created_at, po.notes,
           COUNT(poi.product_id) as item_count
    FROM purchase_orders po
    LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
    LEFT JOIN purchase_order_items poi ON po.po_id = poi.po_id
    WHERE po.notes LIKE '%bot%' OR po.notes LIKE '%automated%' OR po.created_at >= DATE_SUB(NOW(), INTERVAL 3 DAY)
    GROUP BY po.po_id
    ORDER BY po.created_at DESC
    LIMIT 5
");
$database->execute();
$botPOs = $database->resultSet();

foreach ($botPOs as $po) {
    echo "   PO #{$po->po_id} | {$po->supplier_name} | Status: {$po->status} | Items: {$po->item_count} | Created: {$po->created_at}\n";
    echo "     Notes: {$po->notes}\n";
}
echo "Total bot/recent POs: " . count($botPOs) . "\n\n";

echo "=== ANALYSIS COMPLETE ===\n";
