<?php
// Debug script to test the cancel AJAX endpoint directly
require_once 'bootstrap.php';

// Simulate a POST request to the cancel endpoint
$_POST['purchase_id'] = '1'; // Use a valid purchase ID
$_POST['reason'] = 'Test cancellation reason';
$_SERVER['REQUEST_METHOD'] = 'POST';

// Capture any output before JSON
ob_start();

try {
    // Initialize the controller
    $purchasesController = new PurchasesController();
    
    // Call the cancel method
    $purchasesController->cancelPurchaseAjax();
    
} catch (Exception $e) {
    // Clear any output buffer
    if (ob_get_length()) {
        ob_clean();
    }
    
    // Output error as JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'PHP Error: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
} catch (Error $e) {
    // Handle fatal errors
    if (ob_get_length()) {
        ob_clean();
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'PHP Fatal Error: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}

// Get any output that was captured
$output = ob_get_clean();

// If there was unexpected output, show it
if (!empty($output)) {
    echo "\nUnexpected output captured:\n";
    echo $output;
}
?>
