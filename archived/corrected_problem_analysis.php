<?php
require_once 'config/database.php';
require_once 'app/Database.php';

echo "=== CORRECTED PROBLEM PRODUCTS ANALYSIS ===\n\n";

$db = new Database();

$problemProducts = [11, 130, 140]; // Minimal Test Product, Drill, Test Product 3

echo "1. CORRECTED: Problem products in recent purchase items:\n";
foreach ($problemProducts as $productId) {
    $query = "SELECT pr.product_name FROM products pr WHERE pr.product_id = {$productId}";
    $db->query($query);
    $product = $db->executeSingle();
    $productName = $product ? $product->product_name : "Unknown";

    echo "\n   Product ID {$productId} ({$productName}):\n";

    $query = "SELECT pi.purchase_item_id, pi.purchase_id, pi.quantity, pi.unit_price,
                     p.purchase_date, p.status, p.po_number
              FROM purchase_items pi
              JOIN purchases p ON pi.purchase_id = p.purchase_id
              WHERE pi.product_id = {$productId}
              AND p.purchase_date >= '2025-09-01'
              ORDER BY pi.purchase_item_id DESC
              LIMIT 5";

    $db->query($query);
    $items = $db->executeResultSet();

    if (count($items) > 0) {
        foreach ($items as $item) {
            echo "     ✅ PO #{$item->purchase_id} ({$item->po_number}) | Ordered: {$item->quantity} | Price: ₹{$item->unit_price} | Status: {$item->status} | Date: {$item->purchase_date}\n";
        }
    } else {
        echo "     ❌ NOT FOUND in recent purchase items\n";
    }
}

echo "\n2. Why are these products still showing 0 stock if they're in POs?\n";

// Check if the POs have been received
foreach ($problemProducts as $productId) {
    $query = "SELECT pr.product_name FROM products pr WHERE pr.product_id = {$productId}";
    $db->query($query);
    $product = $db->executeSingle();
    $productName = $product ? $product->product_name : "Unknown";

    echo "\n   Product ID {$productId} ({$productName}):\n";

    // Get the most recent PO item
    $query = "SELECT pi.purchase_id, pi.quantity, pi.received_quantity,
                     p.status, p.po_number, r.receiving_id
              FROM purchase_items pi
              JOIN purchases p ON pi.purchase_id = p.purchase_id
              LEFT JOIN receiving r ON p.purchase_id = r.purchase_id
              WHERE pi.product_id = {$productId}
              ORDER BY pi.purchase_item_id DESC
              LIMIT 1";

    $db->query($query);
    $item = $db->executeSingle();

    if ($item) {
        echo "     📦 Latest PO: #{$item->purchase_id} ({$item->po_number})\n";
        echo "     📊 Ordered: {$item->quantity} | Received: " . ($item->received_quantity ?? 0) . "\n";
        echo "     📋 PO Status: {$item->status}\n";
        echo "     🏭 Receiving ID: " . ($item->receiving_id ?? 'None') . "\n";

        // Check if it's been received but not processed
        if ($item->status == 'received' && ($item->received_quantity ?? 0) == 0) {
            echo "     ⚠️  ISSUE: PO marked as 'received' but received_quantity is 0\n";
        }

        if (!$item->receiving_id && $item->status == 'received') {
            echo "     ⚠️  ISSUE: PO marked as 'received' but no receiving record exists\n";
        }

        // Check inventory for this product
        $query = "SELECT quantity FROM inventory WHERE product_id = {$productId}";
        $db->query($query);
        $inventory = $db->executeSingle();
        $currentStock = $inventory ? $inventory->quantity : 0;

        echo "     📦 Current Inventory: {$currentStock}\n";

        if ($item->status == 'received' && $currentStock == 0) {
            echo "     🚨 CRITICAL: PO received but inventory not updated!\n";
        }
    }
}

echo "\n3. Summary of the real issue:\n";
$query = "SELECT COUNT(*) as received_pos,
                 SUM(CASE WHEN pi.received_quantity = 0 THEN 1 ELSE 0 END) as zero_received
          FROM purchase_items pi
          JOIN purchases p ON pi.purchase_id = p.purchase_id
          WHERE pi.product_id IN (11, 130, 140)
          AND p.status = 'received'";

$db->query($query);
$summary = $db->executeSingle();

echo "   📊 Problem products in 'received' POs: {$summary->received_pos}\n";
echo "   ⚠️  Of those, with received_quantity = 0: {$summary->zero_received}\n";

echo "\n=== ANALYSIS COMPLETE ===\n";
?>