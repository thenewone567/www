<?php
header('Content-Type: application/json');

require_once '../bootstrap.php';

try {
    $db = new Database();

    // Get status parameter from URL
    $statusParam = $_GET['status'] ?? 'ready_to_receive';
    $statuses = explode(',', $statusParam);

    // Create placeholders for prepared statement
    $placeholders = str_repeat('?,', count($statuses) - 1) . '?';

    // Build query to include dock-received POs that need detailed receiving and have products
    $query = "SELECT p.purchase_id, p.po_number, p.status, p.total_amount, p.dock_location_id, 
                     s.supplier_name, l.location_name as dock_name
              FROM purchases p 
              LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id 
              LEFT JOIN locations l ON p.dock_location_id = l.location_id
              WHERE p.status IN ($placeholders)
              AND EXISTS (SELECT 1 FROM purchase_items pi WHERE pi.purchase_id = p.purchase_id)";

    // For 'received' status, only include those that were received at dock (have dock_location_id)
    if (in_array('received', $statuses)) {
        $query .= " AND (p.status != 'received' OR p.dock_location_id IS NOT NULL)";
    }

    $query .= " ORDER BY p.updated_at DESC LIMIT 20";

    $db->query($query);

    // Bind the status values
    foreach ($statuses as $index => $status) {
        $db->bind($index + 1, trim($status));
    }

    $db->execute();
    $purchases = $db->resultSet();

    $result = [];
    if ($purchases) {
        foreach ($purchases as $purchase) {
            $result[] = [
                'purchase_id'      => $purchase->purchase_id,
                'po_number'        => $purchase->po_number,
                'status'           => $purchase->status,
                'total_amount'     => $purchase->total_amount,
                'supplier_name'    => $purchase->supplier_name ?? 'Unknown',
                'dock_location_id' => $purchase->dock_location_id,
                'dock_name'        => $purchase->dock_name
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'data'    => $result,
        'count'   => count($result)
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>