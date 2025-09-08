<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include necessary files
require_once dirname(__DIR__) . '/bootstrap.php';

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        throw new Exception('Invalid JSON data');
    }

    // Get PO ID from request
    $poId = $input['po_id'] ?? '';

    if (empty($poId)) {
        throw new Exception('PO ID is required');
    }

    // Initialize database
    $db = new Database();

    // Get PO details with products (include sent and in_transit statuses)
    $db->query('
        SELECT p.purchase_id, p.po_number, p.supplier_id, p.purchase_date, 
               p.expected_date, p.status, p.total_amount, p.notes,
               p.dock_location_id, p.receiving_area_id, s.supplier_name,
               dl.location_name as dock_name, rl.location_name as receiving_area_name
        FROM purchases p
        LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
        LEFT JOIN locations dl ON p.dock_location_id = dl.location_id
        LEFT JOIN locations rl ON p.receiving_area_id = rl.location_id
        WHERE p.purchase_id = ? 
        AND (p.status IN ("ready_to_receive", "receiving_in_progress", "partially_received", "received", "at_dock", "sent", "in_transit"))
        LIMIT 1
    ');
    $db->bind(1, $poId);
    $db->execute();
    $po = $db->single();

    if (!$po) {
        throw new Exception('PO not found or not ready for receiving');
    }

    // Get products for this PO (simplified query)
    $db->query('
        SELECT pi.product_id, pi.quantity, pi.unit_price,
               pr.product_name, pr.sku
        FROM purchase_items pi
        LEFT JOIN products pr ON pi.product_id = pr.product_id
        WHERE pi.purchase_id = ?
    ');
    $db->bind(1, $po->purchase_id);
    $db->execute();
    $products = $db->resultSet();

    // Check if PO has no products
    if (empty($products)) {
        throw new Exception('This PO has no products associated with it. Please contact your administrator.');
    }

    // Format response
    $response = [
        'success' => true,
        'data' => [
            'po' => [
                'purchase_id' => $po->purchase_id,
                'po_number' => $po->po_number,
                'supplier_name' => $po->supplier_name,
                'purchase_date' => date('M j, Y', strtotime($po->purchase_date)),
                'expected_date' => $po->expected_date ? date('M j, Y', strtotime($po->expected_date)) : 'Not set',
                'status' => $po->status,
                'total_amount' => $po->total_amount,
                'notes' => $po->notes,
                'dock_location_id' => $po->dock_location_id,
                'dock_name' => $po->dock_name,
                'receiving_area_id' => $po->receiving_area_id,
                'receiving_area_name' => $po->receiving_area_name
            ],
            'items' => array_map(function ($product) {
                return [
                    'product_id' => $product->product_id,
                    'product_name' => $product->product_name,
                    'sku' => $product->sku,
                    'quantity' => (int) $product->quantity,
                    'already_received' => 0,
                    'remaining_qty' => (int) $product->quantity,
                    'unit_price' => (float) $product->unit_price
                ];
            }, $products)
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>