<?php
require_once 'config/database.php';
require_once 'app/Database.php';

echo "=== TODAY'S PURCHASE ORDER ANALYSIS ===\n\n";

$db = new Database();

// Check all POs created today
echo "1. All Purchase Orders created today:\n";
$query = "SELECT p.purchase_id, p.status, p.created_at, COUNT(pi.purchase_item_id) as item_count
          FROM purchases p
          LEFT JOIN purchase_items pi ON p.purchase_id = pi.purchase_id
          WHERE DATE(p.created_at) = CURDATE()
          GROUP BY p.purchase_id, p.status, p.created_at
          ORDER BY p.created_at DESC";

$db->query($query);
$todaysPOs = $db->executeResultSet();

foreach ($todaysPOs as $po) {
    echo "   PO #{$po->purchase_id} | Status: {$po->status} | Items: {$po->item_count} | Created: {$po->created_at}\n";
}

echo "\n2. Products in today's POs:\n";
$query = "SELECT p.purchase_id, pr.product_id, pr.product_name, pi.quantity_ordered, 
                 pi.quantity_received, pi.status as item_status
          FROM purchases p
          JOIN purchase_items pi ON p.purchase_id = pi.purchase_id
          JOIN products pr ON pi.product_id = pr.product_id
          WHERE DATE(p.created_at) = CURDATE()
          ORDER BY p.purchase_id, pr.product_name";

$db->query($query);
$todaysItems = $db->executeResultSet();

$currentPO = null;
foreach ($todaysItems as $item) {
    if ($currentPO != $item->purchase_id) {
        $currentPO = $item->purchase_id;
        echo "\n   PO #{$item->purchase_id}:\n";
    }
    echo "     - {$item->product_name} (ID: {$item->product_id}) | Ordered: {$item->quantity_ordered} | Received: {$item->quantity_received} | Status: {$item->item_status}\n";
}

echo "\n3. Out-of-stock products that SHOULD have been in POs:\n";
$query = "SELECT p.product_id, p.product_name,
                 COALESCE(inv.quantity, 0) as current_stock
          FROM products p
          LEFT JOIN inventory inv ON p.product_id = inv.product_id
          WHERE p.is_active = 1 AND p.deleted_at IS NULL
          AND COALESCE(inv.quantity, 0) <= 0
          ORDER BY p.product_name";

$db->query($query);
$outOfStock = $db->executeResultSet();

echo "   Products with 0 stock:\n";
foreach ($outOfStock as $product) {
    echo "     - {$product->product_name} (ID: {$product->product_id}) | Stock: {$product->current_stock}\n";
}

echo "\n4. Check if out-of-stock products were missed in PO creation:\n";
$outOfStockIds = [];
foreach ($outOfStock as $product) {
    $outOfStockIds[] = $product->product_id;
}

$todaysProductIds = [];
foreach ($todaysItems as $item) {
    $todaysProductIds[] = $item->product_id;
}

$missedProducts = array_diff($outOfStockIds, $todaysProductIds);

if (count($missedProducts) > 0) {
    echo "   ❌ Products that are out-of-stock but NOT in any PO:\n";
    foreach ($missedProducts as $productId) {
        foreach ($outOfStock as $product) {
            if ($product->product_id == $productId) {
                echo "     - {$product->product_name} (ID: {$product->product_id})\n";
                break;
            }
        }
    }
} else {
    echo "   ✅ All out-of-stock products are included in today's POs\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";
?>