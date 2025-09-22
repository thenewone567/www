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

    // Get the input - check both JSON and form data
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }

    $action = $input['action'] ?? '';

    // Handle different actions
    if ($action === 'get_offloading_pos') {
        // Return all POs currently in off-loading status
        $purchaseModel = new Purchase();

        // Use the working approach: get all purchases and filter manually
        $allPurchases = $purchaseModel->getHistory();
        $result = [];

        foreach ($allPurchases as $purchase) {
            // Check for off-loading status
            $status = strtolower($purchase->status ?? '');
            if ($status === 'off-loading') {
                // Get full details for this PO to access dock_arrival_time
                $fullPO = $purchaseModel->getPurchaseByPONumber($purchase->order_no);
                if ($fullPO && !empty($fullPO->dock_arrival_time)) {
                    $result[] = [
                        'po_number' => $purchase->order_no,
                        'dock_arrival_time' => $fullPO->dock_arrival_time
                    ];
                }
            }
        }

        // Clean any unwanted output
        ob_end_clean();
        echo json_encode([
            'status' => 'success',
            'message' => 'Retrieved off-loading POs',
            'offloading_pos' => $result
        ]);
        exit;
    }

    // Original PO search functionality
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

    // Check if the purchase order can be received or is already in off-loading
    $status = strtolower($purchase->status ?? '');
    $canReceive = in_array($status, ['pending', 'email_received', 'in_transit', 'shipped', 'ready_to_receive', 'receiving_in_progress', 'partially_received']);
    // Check for off-loading status
    $isOffloading = in_array($status, ['off-loading', 'ready_to_receive', 'receiving_in_progress']);

    // Check for stuck off-loading
    $stuckInfo = null;
    if ($isOffloading && !empty($purchase->dock_arrival_time)) {
        $startTime = strtotime($purchase->dock_arrival_time);
        $now = time();
        $elapsedMinutes = ($now - $startTime) / 60;

        if ($elapsedMinutes > 10) {
            $stuckInfo = [
                'is_stuck' => true,
                'elapsed_minutes' => floor($elapsedMinutes),
                'dock_arrival_time' => $purchase->dock_arrival_time,
                'elapsed_formatted' => gmdate($elapsedMinutes >= 60 ? 'H:i:s' : 'i:s', $now - $startTime)
            ];
        }
    }

    // Clean any unwanted output before sending response
    ob_end_clean();

    if (!$canReceive && !$isOffloading) {
        // Format status for display (replace underscores with spaces and capitalize properly)
        $statusDisplay = ucwords(str_replace('_', ' ', $status));

        echo json_encode([
            'success' => false,
            'message' => "Purchase Order cannot be received. Current status: " . $statusDisplay,
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
        'message' => $isOffloading ? 'Purchase Order is currently off-loading' : 'Purchase Order found and ready to receive',
        'data' => [
            'purchase' => $purchase,
            'can_receive' => $canReceive,
            'is_offloading' => $isOffloading,
            'stuck_info' => $stuckInfo,
            'po_number' => $purchase->po_number ?? 'PO-' . $purchase->purchase_id,
            'supplier_name' => $purchase->supplier_name ?? 'Unknown',
            'total_amount' => $purchase->total_amount ?? 0,
            'status' => ucfirst($status),
            'dock_arrival_time' => $purchase->dock_arrival_time ?? null
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