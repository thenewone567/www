<?php
// Test script to simulate an admin AJAX POST to toggleUserStatus
require_once __DIR__ . '/../bootstrap.php';

// Simulate server environment for AJAX
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

// Simulate logged-in admin (adjust id if needed)
$_SESSION['user_id'] = 1; // assume user 1 is admin in this setup

// Prepare POST data that previously failed
$_POST['user_id'] = 3;
$_POST['source_table'] = 'users';
$_POST['status'] = 'active';

// Instantiate controller and call the endpoint
$ctrl = new AdminController();
ob_start();
$ctrl->toggleUserStatus();
$output = ob_get_clean();

// Print the JSON response and a short message
echo "\n--- Controller response ---\n";
echo $output . "\n";

// Show last 40 lines of app.log for quick inspection
$logPath = __DIR__ . '/../storage/logs/app.log';
if (file_exists($logPath)) {
    $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $tail = array_slice($lines, -40);
    echo "\n--- Last log lines ---\n";
    foreach ($tail as $l)
        echo $l . "\n";
} else {
    echo "Log file not found: $logPath\n";
}

?>