<?php
// Test that the cancel AJAX endpoint now returns proper JSON
require_once 'bootstrap.php';

// Start a session to simulate being logged in
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'test_user';

// Set up AJAX request simulation
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
$_POST['purchase_id'] = '999'; // Use non-existent ID to test error handling
$_POST['reason'] = 'Test cancellation reason';

echo "Testing cancel AJAX with non-existent purchase ID...\n";

// Capture output
ob_start();

try {
    $controller = new PurchasesController();
    $controller->cancelPurchaseAjax();
} catch (Exception $e) {
    echo "Exception caught: " . $e->getMessage() . "\n";
}

$output = ob_get_clean();

echo "Response:\n";
echo $output . "\n";

// Verify it's valid JSON
$json = json_decode($output, true);
if ($json === null) {
    echo "ERROR: Response is not valid JSON!\n";
    echo "JSON error: " . json_last_error_msg() . "\n";
} else {
    echo "SUCCESS: Valid JSON response received!\n";
    echo "Response data: " . print_r($json, true) . "\n";
}
?>
