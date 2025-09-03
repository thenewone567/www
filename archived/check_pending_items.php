<?php
require_once 'bootstrap.php';

$db = new Database();

// Check pending purchase items
$db->query("
    SELECT COUNT(*) as count 
    FROM purchase_items pi 
    JOIN purchases p ON pi.purchase_id = p.purchase_id 
    WHERE p.status IN ('pending', 'sent', 'in_transit', 'ready_to_receive') 
    AND pi.quantity > COALESCE(pi.received_quantity, 0)
");
$db->execute();
$result = $db->single();

echo "Pending purchase items available for receiving: " . $result->count . "\n";

// Show some sample pending items
$db->query("
    SELECT pi.purchase_item_id, pi.quantity, pi.received_quantity, p.po_number, pr.product_name, p.status
    FROM purchase_items pi 
    JOIN purchases p ON pi.purchase_id = p.purchase_id 
    JOIN products pr ON pi.product_id = pr.product_id
    WHERE p.status IN ('pending', 'sent', 'in_transit', 'ready_to_receive') 
    AND pi.quantity > COALESCE(pi.received_quantity, 0)
    LIMIT 5
");
$db->execute();
$items = $db->resultSet();

echo "\nSample pending items:\n";
foreach ($items as $item) {
    $pending = $item->quantity - ($item->received_quantity ?? 0);
    echo "- {$item->product_name} (PO: {$item->po_number}) - Need to receive: {$pending} units\n";
}
?>