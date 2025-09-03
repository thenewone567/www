<?php
require_once 'config/database.php';
require_once 'app/Database.php';

echo "=== DETAILED STOCK ANALYSIS ===\n\n";

$db = new Database();

// Check recent receiving and its inventory impact
echo "1. Recent Receiving Analysis:\n";
$query = "SELECT r.receiving_id, r.purchase_id, r.received_date, 
                 ri.product_id, ri.quantity_received, p.product_name
          FROM receiving r 
          JOIN receiving_items ri ON r.receiving_id = ri.receiving_id
          JOIN products p ON ri.product_id = p.product_id
          WHERE DATE(r.received_date) = CURDATE()
          ORDER BY r.received_date DESC
          LIMIT 20";

$db->query($query);
$recentReceiving = $db->executeResultSet();
foreach ($recentReceiving as $item) {
    echo "   Purchase #{$item->purchase_id} | {$item->product_name} | Received: {$item->quantity_received} | Date: {$item->received_date}\n";
}

echo "\n2. Inventory Status for Recently Received Items:\n";
$query = "SELECT DISTINCT ri.product_id, p.product_name,
                 COALESCE(inv.quantity, 0) as current_stock,
                 SUM(ri.quantity_received) as total_received_today
          FROM receiving_items ri
          JOIN receiving r ON ri.receiving_id = r.receiving_id
          JOIN products p ON ri.product_id = p.product_id
          LEFT JOIN inventory inv ON ri.product_id = inv.product_id
          WHERE DATE(r.received_date) = CURDATE()
          GROUP BY ri.product_id, p.product_name, inv.quantity
          ORDER BY p.product_name";

$db->query($query);
$inventoryStatus = $db->executeResultSet();
foreach ($inventoryStatus as $item) {
    $status = $item->current_stock > 0 ? "✓" : "✗";
    echo "   {$status} {$item->product_name} | Received Today: {$item->total_received_today} | Current Stock: {$item->current_stock}\n";
}

echo "\n3. Check if receiving is properly updating inventory:\n";
$query = "SELECT ri.product_id, p.product_name,
                 COUNT(ri.receiving_item_id) as receiving_count,
                 SUM(ri.quantity_received) as total_received,
                 COALESCE(inv.quantity, 0) as inventory_quantity,
                 CASE 
                   WHEN inv.quantity IS NULL THEN 'NO INVENTORY RECORD'
                   WHEN inv.quantity = 0 THEN 'ZERO STOCK'
                   ELSE 'HAS STOCK'
                 END as status
          FROM receiving_items ri
          JOIN products p ON ri.product_id = p.product_id
          LEFT JOIN inventory inv ON ri.product_id = inv.product_id
          GROUP BY ri.product_id, p.product_name, inv.quantity
          HAVING total_received > 0
          ORDER BY total_received DESC
          LIMIT 15";

$db->query($query);
$receivingAnalysis = $db->executeResultSet();
echo "   Product | Total Received | Current Stock | Status\n";
echo "   " . str_repeat("-", 80) . "\n";
foreach ($receivingAnalysis as $item) {
    echo "   {$item->product_name} | {$item->total_received} | {$item->inventory_quantity} | {$item->status}\n";
}

echo "\n4. Check recent inventory table updates:\n";
$query = "SELECT inv.product_id, p.product_name, inv.quantity,
                 inv.created_at, inv.updated_at
          FROM inventory inv
          JOIN products p ON inv.product_id = p.product_id
          WHERE inv.updated_at >= CURDATE() - INTERVAL 1 DAY
          ORDER BY inv.updated_at DESC
          LIMIT 10";

$db->query($query);
$recentUpdates = $db->executeResultSet();
if (count($recentUpdates) > 0) {
    echo "   Recent inventory updates:\n";
    foreach ($recentUpdates as $item) {
        echo "   {$item->product_name} | Qty: {$item->quantity} | Updated: {$item->updated_at}\n";
    }
} else {
    echo "   No recent inventory updates found in last 24 hours\n";
}

echo "\n5. Specific Problem Products Analysis:\n";
// Check the products that should have stock but don't
$problemProducts = [11, 130, 140]; // Minimal Test Product, Drill, Test Product 3
foreach ($problemProducts as $productId) {
    echo "   Product ID {$productId}:\n";

    // Check if received
    $query = "SELECT SUM(ri.quantity_received) as total_received
              FROM receiving_items ri
              JOIN receiving r ON ri.receiving_id = r.receiving_id
              WHERE ri.product_id = {$productId}";
    $db->query($query);
    $received = $db->executeSingle();
    $totalReceived = $received ? $received->total_received : 0;

    // Check inventory
    $query = "SELECT quantity FROM inventory WHERE product_id = {$productId}";
    $db->query($query);
    $inventory = $db->executeSingle();
    $currentStock = $inventory ? $inventory->quantity : 'NO RECORD';

    // Check product details
    $query = "SELECT product_name, is_active, deleted_at FROM products WHERE product_id = {$productId}";
    $db->query($query);
    $product = $db->executeSingle();

    if ($product) {
        echo "     Name: {$product->product_name}\n";
        echo "     Active: " . ($product->is_active ? 'Yes' : 'No') . "\n";
        echo "     Deleted: " . ($product->deleted_at ? 'Yes' : 'No') . "\n";
        echo "     Total Received: {$totalReceived}\n";
        echo "     Current Stock: {$currentStock}\n";
        echo "\n";
    }
}

echo "=== ANALYSIS COMPLETE ===\n";
?>