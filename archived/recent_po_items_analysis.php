<?php
require_once 'config/database.php';
require_once 'app/Database.php';

echo "=== RECENT PO ITEMS ANALYSIS ===\n\n";

$db = new Database();

// Check items in the most recent POs
echo "1. Items in the 10 most recent POs:\n";
$recentPOIds = [1503, 1502, 1501, 1500, 1499, 1498, 1497, 1496, 1495, 1494];

foreach ($recentPOIds as $poId) {
    echo "\n   PO #{$poId}:\n";

    $query = "SELECT pi.product_id, pr.product_name, pi.quantity_ordered, 
                     pi.quantity_received, pi.status
              FROM purchase_items pi
              JOIN products pr ON pi.product_id = pr.product_id
              WHERE pi.purchase_id = {$poId}
              ORDER BY pr.product_name";

    $db->query($query);
    $items = $db->executeResultSet();

    if (count($items) > 0) {
        foreach ($items as $item) {
            echo "     - {$item->product_name} (ID: {$item->product_id}) | Ordered: {$item->quantity_ordered} | Received: {$item->quantity_received} | Status: {$item->status}\n";
        }
    } else {
        echo "     ❌ NO ITEMS found in this PO\n";
    }
}

// Check which of our problem products (11, 130, 140) are in recent POs
echo "\n2. Looking for our problem products in recent POs:\n";
$problemProducts = [11, 130, 140]; // Minimal Test Product, Drill, Test Product 3

foreach ($problemProducts as $productId) {
    $query = "SELECT product_name FROM products WHERE product_id = {$productId}";
    $db->query($query);
    $product = $db->executeSingle();
    $productName = $product ? $product->product_name : "Unknown Product";

    echo "\n   Product ID {$productId} ({$productName}):\n";

    $query = "SELECT pi.purchase_id, pi.quantity_ordered, pi.quantity_received, 
                     pi.status, p.purchase_date, p.status as po_status
              FROM purchase_items pi
              JOIN purchases p ON pi.purchase_id = p.purchase_id
              WHERE pi.product_id = {$productId}
              AND p.purchase_date >= '2025-09-01'
              ORDER BY pi.purchase_id DESC
              LIMIT 10";

    $db->query($query);
    $items = $db->executeResultSet();

    if (count($items) > 0) {
        foreach ($items as $item) {
            echo "     ✓ PO #{$item->purchase_id} | Ordered: {$item->quantity_ordered} | Received: {$item->quantity_received} | Item Status: {$item->status} | PO Status: {$item->po_status} | Date: {$item->purchase_date}\n";
        }
    } else {
        echo "     ❌ NOT FOUND in recent POs\n";
    }
}

// Check what products ARE in today's POs
echo "\n3. What products ARE in today's POs (Sept 1st):\n";
$query = "SELECT DISTINCT pr.product_id, pr.product_name, 
                 COUNT(pi.purchase_item_id) as po_count,
                 SUM(pi.quantity_ordered) as total_ordered,
                 SUM(pi.quantity_received) as total_received
          FROM purchase_items pi
          JOIN products pr ON pi.product_id = pr.product_id
          JOIN purchases p ON pi.purchase_id = p.purchase_id
          WHERE p.purchase_date = '2025-09-01'
          GROUP BY pr.product_id, pr.product_name
          ORDER BY total_ordered DESC
          LIMIT 20";

$db->query($query);
$todaysProducts = $db->executeResultSet();

if (count($todaysProducts) > 0) {
    echo "   Products in today's POs:\n";
    foreach ($todaysProducts as $product) {
        echo "     - {$product->product_name} (ID: {$product->product_id}) | POs: {$product->po_count} | Ordered: {$product->total_ordered} | Received: {$product->total_received}\n";
    }
} else {
    echo "   ❌ NO PRODUCTS found in today's POs\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";
?>