<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Prevent any HTML output from error handlers
ini_set('display_errors', 0);
ob_start();

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }

    // Check if user is logged in
    if (!isLoggedIn()) {
        throw new Exception('User not authenticated');
    }

    // Get the input
    $input = json_decode(file_get_contents('php://input'), true);
    $poNumber = $input['po_number'] ?? '';
    $dockLocationId = $input['dock_location_id'] ?? null;
    $receivingAreaId = $input['receiving_area_id'] ?? null;
    $notes = $input['notes'] ?? '';

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
            'message' => 'Purchase Order not found'
        ]);
        exit;
    }

    // Check if the purchase order can be received
    $status = strtolower($purchase->status ?? '');
    $canReceive = in_array($status, ['pending', 'sent', 'in_transit', 'shipped']);

    if (!$canReceive) {
        // Clean any unwanted output
        ob_end_clean();
        echo json_encode([
            'success' => false,
            'message' => "Purchase Order cannot be received. Current status: " . ucfirst($status)
        ]);
        exit;
    }

    // Mark as received and staged with location assignments
    $result = $purchaseModel->markAsReceivedAndStaged(
        $purchase->purchase_id,
        $dockLocationId,
        $receivingAreaId,
        $notes
    );

    // Clean any unwanted output before sending response
    ob_end_clean();

    if ($result) {
        // Get location names for response
        $dockName = '';
        $receivingAreaName = '';

        if ($dockLocationId) {
            $db = new Database();
            $db->query("SELECT location_name FROM locations WHERE location_id = ?");
            $db->bind(1, $dockLocationId);
            $db->execute();
            $dock = $db->single();
            $dockName = $dock ? $dock->location_name : '';
        }

        if ($receivingAreaId) {
            $db = new Database();
            $db->query("SELECT location_name FROM locations WHERE location_id = ?");
            $db->bind(1, $receivingAreaId);
            $db->execute();
            $area = $db->single();
            $receivingAreaName = $area ? $area->location_name : '';
        }

        echo json_encode([
            'success' => true,
            'message' => 'Purchase Order successfully marked as received and staged at dock',
            'data' => [
                'po_number' => $purchase->po_number ?? 'PO-' . $purchase->purchase_id,
                'supplier_name' => $purchase->supplier_name ?? 'Unknown',
                'total_amount' => $purchase->total_amount ?? 0,
                'dock_location' => $dockName,
                'receiving_area' => $receivingAreaName,
                'notes' => $notes
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to mark Purchase Order as received'
        ]);
    }

} catch (Exception $e) {
    // Clean any unwanted output before sending error response
    ob_end_clean();
    error_log("Error in quickReceivePurchaseOrder.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>