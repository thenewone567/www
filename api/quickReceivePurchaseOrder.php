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

    // Get the input - handle both JSON and form data
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $poNumber = $input['po_number'] ?? '';
    $dockLocationId = $input['dock_location_id'] ?? null;
    $notes = $input['notes'] ?? '';
    $requestedStatus = $input['status'] ?? 'off-loading'; // Default to off-loading (valid ENUM)

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
            'status' => 'error',
            'message' => 'Purchase Order not found'
        ]);
        exit;
    }

    // Check if the purchase order can be processed based on requested status
    $status = strtolower($purchase->status ?? '');

    if ($requestedStatus === 'off-loading') {
        // Step 1: Check if PO can be off-loaded (start off-loading)
        $canOffload = in_array($status, ['pending', 'email_received', 'in_transit']);

        if (!$canOffload) {
            ob_end_clean();

            // Provide specific error message based on current status
            $errorMessage = "Purchase Order cannot start off-loading.";
            if (empty($status)) {
                $errorMessage .= " Status is blank/empty. Please set PO to 'pending' status first.";
            } elseif ($status === 'off-loading') {
                $errorMessage .= " Off-loading already in progress. Use 'Ready to Receive' button to complete.";
            } elseif ($status === 'ready_to_receive') {
                $errorMessage .= " Off-loading already completed. Use the receiving interface instead.";
            } else {
                $errorMessage .= " Current status '" . ucfirst($status) . "' does not allow off-loading.";
            }

            echo json_encode([
                'status' => 'error',
                'message' => $errorMessage,
                'current_status' => $status,
                'required_statuses' => ['pending', 'email_received', 'in_transit']
            ]);
            exit;
        }

        // Mark as arrived at facility (off-loading started)
        $result = $purchaseModel->markAsArrivedAtFacility(
            $purchase->purchase_id,
            $dockLocationId,
            $notes
        );

        $successMessage = 'Off-loading started! PO status changed to "Off-loading".';

    } elseif ($requestedStatus === 'ready_to_receive') {
        // Step 2: Check if PO is currently being off-loaded
        $canComplete = in_array($status, ['off-loading']);

        if (!$canComplete) {
            ob_end_clean();

            // Provide specific error message based on current status
            $errorMessage = "Purchase Order cannot be completed.";
            if (empty($status)) {
                $errorMessage .= " Status is blank/empty. Please start the off-loading process first.";
            } elseif ($status === 'ready_to_receive') {
                $errorMessage .= " Off-loading is already completed. Use the receiving interface instead.";
            } elseif (in_array($status, ['pending', 'email_received', 'in_transit'])) {
                $errorMessage .= " Please start off-loading first (current status: " . ucfirst($status) . ").";
            } else {
                $errorMessage .= " Current status '" . ucfirst($status) . "' does not allow completion.";
            }

            echo json_encode([
                'status' => 'error',
                'message' => $errorMessage,
                'current_status' => $status,
                'required_status' => 'off-loading'
            ]);
            exit;
        }

        // Mark as ready to receive (off-loading completed)
        $result = $purchaseModel->markReadyToReceive($purchase->purchase_id);

        $successMessage = 'Off-loading completed! PO is ready to receive products.';

    } else {
        // Legacy mode - mark as arrived at facility
        $canOffload = in_array($status, ['pending', 'email_received', 'in_transit', 'shipped']);

        if (!$canOffload) {
            ob_end_clean();
            echo json_encode([
                'success' => false,
                'message' => "Purchase Order cannot be off-loaded. Current status: " . ucfirst($status)
            ]);
            exit;
        }

        $result = $purchaseModel->markAsArrivedAtFacility(
            $purchase->purchase_id,
            $dockLocationId,
            $notes
        );

        $successMessage = 'Purchase Order successfully off-loaded and marked as arrived at facility';
    }

    // Clean any unwanted output before sending response
    ob_end_clean();

    if ($result) {
        // Get location names for response
        $dockName = '';

        if ($dockLocationId) {
            $db = new Database();
            $db->query("SELECT location_name FROM locations WHERE location_id = ?");
            $db->bind(1, $dockLocationId);
            $db->execute();
            $dock = $db->single();
            $dockName = $dock ? $dock->location_name : '';
        }

        // Provide a fully-qualified URL to view/print the generated receiving receipt. Uses PO number as identifier.
        $poIdentifier = urlencode($purchase->po_number ?? 'PO-' . $purchase->purchase_id);

        // If URLROOT is defined and already absolute, use it. otherwise build from server vars.
        $baseUrl = '';
        if (defined('URLROOT') && preg_match('#^https?://#i', URLROOT)) {
            $baseUrl = rtrim(URLROOT, '/');
        } else {
            // Determine scheme
            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
            $scheme = $isHttps ? 'https' : 'http';

            // Host
            $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');

            // Determine base path: prefer a relative URLROOT if set, else infer from script location
            $basePath = '';
            if (defined('URLROOT') && !empty(URLROOT) && strpos(URLROOT, '/') === 0) {
                $basePath = rtrim(URLROOT, '/');
            } else {
                $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/';
                $basePath = rtrim(dirname($scriptName), '/');
            }

            $baseUrl = $scheme . '://' . $host . $basePath;
        }

        $receiptUrl = $baseUrl . '/purchases/viewReceipt/' . $poIdentifier;

        echo json_encode([
            'status' => 'success',
            'message' => $successMessage,
            'data' => [
                'po_number' => $purchase->po_number ?? 'PO-' . $purchase->purchase_id,
                'supplier_name' => $purchase->supplier_name ?? 'Unknown',
                'total_amount' => $purchase->total_amount ?? 0,
                'dock_location' => $dockName,
                'notes' => $notes,
                'receipt_url' => $receiptUrl,
                'current_status' => $requestedStatus
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to process Purchase Order'
        ]);
    }

} catch (Exception $e) {
    // Clean any unwanted output before sending error response
    ob_end_clean();
    error_log("Error in quickReceivePurchaseOrder.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>