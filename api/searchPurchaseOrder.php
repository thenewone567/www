<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Prevent any HTML output from error handlers
ini_set('display_errors', 0);
ob_start();

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }

    // Get the input
    $input = json_decode(file_get_contents('php://input'), true);
    $poNumber = $input['po_number'] ?? '';

    if (empty($poNumber)) {
        throw new Exception('PO number is required');
    }

    // Initialize models
    $purchaseModel = new Purchase();

    // Search for the purchase order
    $purchase = $purchaseModel->getPurchaseByPONumber($poNumber);

    if (!$purchase) {
        // Clean any unwanted output
        ob_end_clean();
        echo json_encode([
            'success' => false,
            'message' => 'Purchase Order not found',
            'data' => null
        ]);
        exit;
    }

    // Check if the purchase order can be received
    $status = strtolower($purchase->status ?? '');
    $canReceive = in_array($status, ['pending', 'sent', 'in_transit', 'shipped']);

    // Clean any unwanted output before sending response
    ob_end_clean();

    if (!$canReceive) {
        echo json_encode([
            'success' => false,
            'message' => "Purchase Order cannot be received. Current status: " . ucfirst($status),
            'data' => [
                'purchase' => $purchase,
                'can_receive' => false
            ]
        ]);
        exit;
    }

    // Return purchase order details
    echo json_encode([
        'success' => true,
        'message' => 'Purchase Order found and ready to receive',
        'data' => [
            'purchase' => $purchase,
            'can_receive' => true,
            'po_number' => $purchase->po_number ?? 'PO-' . $purchase->purchase_id,
            'supplier_name' => $purchase->supplier_name ?? 'Unknown',
            'total_amount' => $purchase->total_amount ?? 0,
            'status' => ucfirst($status)
        ]
    ]);

} catch (Exception $e) {
    // Clean any unwanted output before sending error response
    ob_end_clean();
    error_log("Error in searchPurchaseOrder.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'data' => null
    ]);
}
?>