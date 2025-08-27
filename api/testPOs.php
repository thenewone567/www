<?php
header('Content-Type: application/json');

require_once '../bootstrap.php';

try {
    $db = new Database();
    
    // Simple query
    $db->query("SELECT p.purchase_id, p.po_number, p.status, p.total_amount, s.supplier_name 
                FROM purchases p 
                LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id 
                WHERE p.status = 'ready_to_receive' 
                LIMIT 5");
    $db->execute();
    $purchases = $db->resultSet();
    
    $result = [];
    if ($purchases) {
        foreach ($purchases as $purchase) {
            $result[] = [
                'purchase_id' => $purchase->purchase_id,
                'po_number' => $purchase->po_number,
                'status' => $purchase->status,
                'total_amount' => $purchase->total_amount,
                'supplier_name' => $purchase->supplier_name ?? 'Unknown'
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $result,
        'count' => count($result)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
