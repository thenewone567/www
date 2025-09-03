<?php
require_once 'config/database.php';
require_once 'app/Database.php';

echo "=== CORRECTED PURCHASE ORDER ANALYSIS ===\n\n";

$db = new Database();

// Use correct column names based on table structure
echo "1. Recent Purchase Orders (using updated_at):\n";
$query = "SELECT purchase_id, status, purchase_date, updated_at, total_amount 
          FROM purchases 
          WHERE deleted_at IS NULL
          ORDER BY purchase_id DESC 
          LIMIT 10";

$db->query($query);
$recentPOs = $db->executeResultSet();

if (count($recentPOs) > 0) {
    foreach ($recentPOs as $po) {
        echo "   PO #{$po->purchase_id} | Status: {$po->status} | Date: {$po->purchase_date} | Updated: {$po->updated_at} | Amount: \${$po->total_amount}\n";
    }
} else {
    echo "   ❌ No active Purchase Orders found\n";
}

// Check today's POs using updated_at
echo "\n2. Today's Purchase Orders (using updated_at >= today):\n";
$today = date('Y-m-d');
$query = "SELECT purchase_id, status, purchase_date, updated_at 
          FROM purchases 
          WHERE DATE(updated_at) = '{$today}' AND deleted_at IS NULL
          ORDER BY purchase_id DESC";

$db->query($query);
$todayPOs = $db->executeResultSet();

if (count($todayPOs) > 0) {
    foreach ($todayPOs as $po) {
        echo "   PO #{$po->purchase_id} | Status: {$po->status} | Date: {$po->purchase_date} | Updated: {$po->updated_at}\n";
    }
} else {
    echo "   ❌ No Purchase Orders updated today\n";
}

// Check recent POs by purchase_date
echo "\n3. Recent Purchase Orders by purchase_date:\n";
$query = "SELECT purchase_id, status, purchase_date, updated_at 
          FROM purchases 
          WHERE purchase_date >= CURDATE() - INTERVAL 7 DAYS AND deleted_at IS NULL
          ORDER BY purchase_date DESC, purchase_id DESC
          LIMIT 10";

$db->query($query);
$recentByDate = $db->executeResultSet();

if (count($recentByDate) > 0) {
    foreach ($recentByDate as $po) {
        echo "   PO #{$po->purchase_id} | Status: {$po->status} | Date: {$po->purchase_date} | Updated: {$po->updated_at}\n";
    }
} else {
    echo "   ❌ No Purchase Orders from last 7 days\n";
}

// Check specific high PO IDs to see recent activity
echo "\n4. Latest PO IDs (1480+):\n";
$query = "SELECT purchase_id, status, purchase_date, updated_at 
          FROM purchases 
          WHERE purchase_id >= 1480 AND deleted_at IS NULL
          ORDER BY purchase_id DESC";

$db->query($query);
$highPOs = $db->executeResultSet();

foreach ($highPOs as $po) {
    echo "   PO #{$po->purchase_id} | Status: {$po->status} | Date: {$po->purchase_date} | Updated: {$po->updated_at}\n";
}

// Check the items in the highest PO ID
if (count($recentPOs) > 0) {
    $highestPO = $recentPOs[0];
    echo "\n5. Items in PO #{$highestPO->purchase_id}:\n";

    $query = "SELECT pi.product_id, pr.product_name, pi.quantity_ordered, 
                     pi.quantity_received, pi.status
              FROM purchase_items pi
              JOIN products pr ON pi.product_id = pr.product_id
              WHERE pi.purchase_id = {$highestPO->purchase_id}
              ORDER BY pr.product_name";

    $db->query($query);
    $items = $db->executeResultSet();

    if (count($items) > 0) {
        foreach ($items as $item) {
            echo "     - {$item->product_name} (ID: {$item->product_id}) | Ordered: {$item->quantity_ordered} | Received: {$item->quantity_received} | Status: {$item->status}\n";
        }
    } else {
        echo "     No items found in this PO\n";
    }
}

echo "\n=== ANALYSIS COMPLETE ===\n";
?>