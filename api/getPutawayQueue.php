<?php
require_once '../bootstrap.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getDbh();

    if (!$db) {
        throw new Exception('Database connection failed');
    }

    // Get putaway queue data - items currently in receiving areas waiting for putaway
    // Fixed query to prevent duplicate and incorrect items
    $query = "SELECT DISTINCT
                COALESCE(p.sku, p.barcode, CONCAT('P-', p.product_id)) as item_sku,
                p.product_id,
                p.product_name,
                p.purchase_price as cost_price,
                p.selling_price,
                COALESCE(b.brand_name, 'N/A') as brand,
                pi.received_quantity as quantity_received,
                COALESCE(pi.putaway_quantity, 0) as putaway_quantity,
                (pi.received_quantity - COALESCE(pi.putaway_quantity, 0)) as available_quantity,
                pi.purchase_item_id,
                pi.purchase_id,
                COALESCE(s.supplier_name, 'Unknown Supplier') as supplier_name,
                pi.received_at as received_date,
                pu.po_number,
                recv_loc.location_name as current_location,
                recv_loc.location_id as current_location_id,
                CASE 
                    WHEN p.purchase_price > 100 THEN 'High'
                    WHEN p.purchase_price > 50 THEN 'Medium'
                    ELSE 'Low'
                END as priority_level,
                DATEDIFF(NOW(), pi.received_at) as days_waiting
              FROM purchase_items pi
              JOIN products p ON pi.product_id = p.product_id
              JOIN purchases pu ON pi.purchase_id = pu.purchase_id
              LEFT JOIN brands b ON p.brand_id = b.brand_id
              LEFT JOIN suppliers s ON pu.supplier_id = s.supplier_id
              JOIN inventory inv ON (p.product_id = inv.product_id)
              JOIN locations recv_loc ON (inv.location_id = recv_loc.location_id AND recv_loc.location_type = 'receiving')
              WHERE pi.received_quantity > COALESCE(pi.putaway_quantity, 0)
              AND inv.quantity > 0
              AND pu.status IN ('received', 'receiving_in_progress', 'completed')
              ORDER BY 
                CASE 
                    WHEN p.purchase_price > 100 THEN 1
                    WHEN p.purchase_price > 50 THEN 2
                    ELSE 3
                END ASC,
                DATEDIFF(NOW(), pi.received_at) DESC,
                pi.received_at ASC
              LIMIT 50";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $rawItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $putawayQueue = [];

    foreach ($rawItems as $item) {
        $priority = $item['priority_level'];
        $urgency = 'Normal';
        $daysSinceReceived = (int) $item['days_waiting'];

        // Determine urgency based on days waiting
        if ($daysSinceReceived > 7) {
            $urgency = 'URGENT';
        } elseif ($daysSinceReceived > 3) {
            $urgency = 'High';
        } elseif ($daysSinceReceived > 1) {
            $urgency = 'Priority';
        }

        // Create a unique identifier for tracking
        $uniqueId = $item['purchase_item_id'] . '-' . $item['product_id'];

        $putawayQueue[] = (object) [
            'id' => $uniqueId,
            'sku' => $item['item_sku'],
            'product_id' => $item['product_id'],
            'product_name' => $item['product_name'],
            'quantity' => (int) $item['available_quantity'],
            'supplier' => $item['supplier_name'],
            'location' => $item['current_location'],
            'location_id' => $item['current_location_id'],
            'priority' => $priority,
            'urgency' => $urgency,
            'cost_price' => number_format((float) $item['cost_price'], 2),
            'selling_price' => number_format((float) $item['selling_price'], 2),
            'brand' => $item['brand'],
            'days_waiting' => $daysSinceReceived,
            'po_number' => $item['po_number'],
            'purchase_item_id' => $item['purchase_item_id'],
            'received_date' => $item['received_date']
        ];
    }

    if (empty($putawayQueue)) {
        $putawayQueue = [
            (object) [
                'id' => 'empty-queue',
                'sku' => 'NO-ITEMS',
                'product_id' => 0,
                'product_name' => 'No items in putaway queue',
                'quantity' => 0,
                'supplier' => 'N/A',
                'location' => 'N/A',
                'location_id' => 0,
                'priority' => 'Low',
                'urgency' => 'Normal',
                'cost_price' => '0.00',
                'selling_price' => '0.00',
                'brand' => 'N/A',
                'days_waiting' => 0,
                'po_number' => 'N/A',
                'purchase_item_id' => 0,
                'received_date' => date('Y-m-d H:i:s')
            ]
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $putawayQueue,
        'count' => count($putawayQueue)
    ]);

} catch (Exception $e) {
    error_log("Get putaway queue error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error retrieving putaway queue: ' . $e->getMessage()
    ]);
}
?>