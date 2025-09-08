<?php
require 'bootstrap.php';

echo "=== FINAL PUTAWAY QUEUE TEST ===\n\n";

try {
    $db = new Database();

    echo "Testing final corrected putaway queue query:\n";
    $db->query("SELECT 
        i.product_id,
        i.quantity as pending_quantity,
        i.location_id,
        p.product_name,
        p.sku,
        l.location_code,
        l.location_name,
        i.updated_at
        FROM inventory i
        JOIN locations l ON i.location_id = l.location_id
        LEFT JOIN products p ON i.product_id = p.product_id
        WHERE l.location_type = 'receiving' 
        AND i.quantity > 0
        ORDER BY i.updated_at DESC
        LIMIT 10");

    if ($db->execute()) {
        $items = $db->resultSet();
        if ($items) {
            echo "SUCCESS! Found " . count($items) . " real items waiting for putaway:\n\n";
            foreach ($items as $item) {
                $receivingArea = $item->location_name ?? $item->location_code ?? 'Receiving';
                echo "✓ {$item->product_name} ({$item->sku})\n";
                echo "  Quantity: {$item->pending_quantity} units\n";
                echo "  Currently in: {$receivingArea}\n";
                echo "  Updated: {$item->updated_at}\n\n";
            }
            
            echo "Summary of receiving areas:\n";
            $areas = [];
            foreach ($items as $item) {
                $area = $item->location_name;
                if (!isset($areas[$area])) $areas[$area] = 0;
                $areas[$area] += $item->pending_quantity;
            }
            foreach ($areas as $area => $qty) {
                echo "- {$area}: {$qty} units\n";
            }
            
        } else {
            echo "No items found\n";
        }
    } else {
        echo "Query failed\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
